<?php

namespace App\Services;

/**
 * Menghitung jarak dan ongkos kirim berdasarkan koordinat peta.
 *
 * Jarak memakai rumus Haversine (garis lurus) sehingga tidak memerlukan
 * layanan pihak ketiga. Tarif dasar dan tarif per kilometer berbeda untuk
 * setiap kurir dan dapat diatur melalui config/shipping.php.
 */
class ShippingCostService
{
    private const EARTH_RADIUS_KM = 6371.0;

    /**
     * Hitung jarak antara dua koordinat dalam kilometer.
     */
    public function distanceKm(?float $originLat, ?float $originLng, ?float $destinationLat, ?float $destinationLng): ?float
    {
        if (! $this->hasCoordinate($originLat, $originLng) || ! $this->hasCoordinate($destinationLat, $destinationLng)) {
            return null;
        }

        $latDelta = deg2rad($destinationLat - $originLat);
        $lngDelta = deg2rad($destinationLng - $originLng);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($originLat)) * cos(deg2rad($destinationLat)) * sin($lngDelta / 2) ** 2;

        $a = min(1.0, max(0.0, $a));

        return round(self::EARTH_RADIUS_KM * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    /**
     * Hitung jarak dan biaya pengiriman untuk kurir tertentu.
     *
     * @return array{distance_km: float|null, shipping_cost: int}
     */
    public function estimate(
        ?float $originLat,
        ?float $originLng,
        ?float $destinationLat,
        ?float $destinationLng,
        ?string $courier = null,
    ): array {
        $distance = $this->distanceKm($originLat, $originLng, $destinationLat, $destinationLng);

        if ($distance === null) {
            return [
                'distance_km' => null,
                'shipping_cost' => 0,
            ];
        }

        return [
            'distance_km' => $distance,
            'shipping_cost' => $this->costForDistance($distance, $courier),
        ];
    }

    /**
     * Biaya berdasarkan jarak dan tarif kurir. Jarak dibulatkan ke atas ke
     * kilometer penuh.
     */
    public function costForDistance(float $distanceKm, ?string $courier = null): int
    {
        $minimumKm = max(0.0, (float) config('shipping.minimum_km', 1));
        $billableKm = max($minimumKm, $distanceKm);

        if (config('shipping.round_up', true)) {
            $billableKm = (float) ceil($billableKm);
        }

        $rates = $this->ratesForCourier($courier);

        return $rates['base_fare'] + ((int) $billableKm * $rates['per_km']);
    }

    /**
     * Daftar kurir yang didukung beserta label tampilannya.
     *
     * @return array<string, string>
     */
    public function courierOptions(): array
    {
        $options = [];

        foreach ($this->couriers() as $key => $rate) {
            $options[$key] = (string) ($rate['label'] ?? $key);
        }

        return $options;
    }

    /**
     * Key kurir yang valid, dipakai untuk validasi input checkout.
     *
     * @return list<string>
     */
    public function supportedCouriers(): array
    {
        return array_keys($this->couriers());
    }

    /**
     * Normalisasi nama kurir ke key konfigurasi. Nama yang tidak dikenal akan
     * dikembalikan apa adanya agar data lama tetap dapat ditampilkan.
     */
    public function canonicalCourier(?string $courier): string
    {
        $matched = $this->matchCourierKey($courier);

        if ($matched !== null) {
            return $matched;
        }

        $default = $this->matchCourierKey((string) config('shipping.default_courier'));

        if ($default !== null) {
            return $default;
        }

        $couriers = $this->couriers();

        if (! empty($couriers)) {
            return (string) array_key_first($couriers);
        }

        return trim((string) $courier);
    }

    /**
     * Label tampilan kurir.
     */
    public function courierLabel(?string $courier): string
    {
        $key = $this->matchCourierKey($courier)
            ?? $this->matchCourierKey((string) config('shipping.default_courier'));

        if ($key === null) {
            return trim((string) $courier);
        }

        return (string) ($this->couriers()[$key]['label'] ?? $key);
    }

    /**
     * Nama layanan kurir yang dicatat pada transaksi.
     */
    public function courierService(?string $courier): string
    {
        $key = $this->matchCourierKey($courier)
            ?? $this->matchCourierKey((string) config('shipping.default_courier'));

        if ($key === null) {
            return 'Reguler / Manual';
        }

        return (string) ($this->couriers()[$key]['service'] ?? 'Reguler / Manual');
    }

    /**
     * @return array<string, array{label?: string, service?: string, base_fare?: int, per_km?: int}>
     */
    private function couriers(): array
    {
        $couriers = config('shipping.couriers', []);

        return is_array($couriers) ? $couriers : [];
    }

    /**
     * @return array{base_fare: int, per_km: int}
     */
    private function ratesForCourier(?string $courier): array
    {
        $fallback = [
            'base_fare' => max(0, (int) config('shipping.base_fare', 5000)),
            'per_km' => max(0, (int) config('shipping.per_km', 2500)),
        ];

        $key = $this->matchCourierKey($courier)
            ?? $this->matchCourierKey((string) config('shipping.default_courier'));

        if ($key === null) {
            return $fallback;
        }

        $rate = $this->couriers()[$key] ?? [];

        return [
            'base_fare' => max(0, (int) ($rate['base_fare'] ?? $fallback['base_fare'])),
            'per_km' => max(0, (int) ($rate['per_km'] ?? $fallback['per_km'])),
        ];
    }

    private function matchCourierKey(?string $courier): ?string
    {
        $needle = $this->normalizeCourierName($courier);

        if ($needle === '') {
            return null;
        }

        foreach ($this->couriers() as $key => $rate) {
            $candidates = [$key, $rate['label'] ?? null];

            foreach ($candidates as $candidate) {
                if ($candidate !== null && $this->normalizeCourierName((string) $candidate) === $needle) {
                    return (string) $key;
                }
            }
        }

        return null;
    }

    private function normalizeCourierName(?string $value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function hasCoordinate(?float $latitude, ?float $longitude): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        return $latitude >= -90 && $latitude <= 90
            && $longitude >= -180 && $longitude <= 180;
    }
}
