<?php

namespace App\Services;

/**
 * Sumber data wilayah administratif Indonesia untuk kebutuhan checkout.
 *
 * Data disimpan sebagai JSON lokal agar halaman checkout tetap cepat dan
 * tidak bergantung pada layanan eksternal. Format ID mengikuti kode
 * Kemendagri tanpa titik (provinsi -> kab/kota -> kecamatan).
 */
class RegionService
{
    private ?array $provinceData = null;

    private ?array $regencyData = null;

    private ?array $districtData = null;

    /**
     * Daftar provinsi, dapat difilter dengan kata kunci.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function provinces(string $search = ''): array
    {
        return $this->filter($this->provincesData(), $search);
    }

    /**
     * Daftar kabupaten/kota dalam satu provinsi.
     *
     * @return array<int, array{id: string, province_id: string, name: string}>
     */
    public function regencies(?string $provinceId, string $search = ''): array
    {
        if (blank($provinceId)) {
            return [];
        }

        $items = array_values(array_filter(
            $this->regenciesData(),
            fn (array $regency) => $regency['province_id'] === (string) $provinceId,
        ));

        return $this->filter($items, $search);
    }

    /**
     * Daftar kecamatan dalam satu kabupaten/kota.
     *
     * @return array<int, array{id: string, regency_id: string, name: string}>
     */
    public function districts(?string $regencyId, string $search = ''): array
    {
        if (blank($regencyId)) {
            return [];
        }

        $items = array_values(array_filter(
            $this->districtsData(),
            fn (array $district) => $district['regency_id'] === (string) $regencyId,
        ));

        return $this->filter($items, $search);
    }

    public function findProvince(?string $id): ?array
    {
        return $this->find($this->provincesData(), $id);
    }

    public function findRegency(?string $id): ?array
    {
        return $this->find($this->regenciesData(), $id);
    }

    public function findDistrict(?string $id): ?array
    {
        return $this->find($this->districtsData(), $id);
    }

    /**
     * Reverse geocode koordinat menggunakan OpenStreetMap Nominatim.
     * Mengembalikan array [province, regency, district] yang cocok dengan
     * data lokal, atau null jika tidak ditemukan.
     *
     * @return array{province?: array{id: string, name: string}, regency?: array{id: string, name: string}, district?: array{id: string, name: string}}|null
     */
    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
            'lat' => $latitude,
            'lon' => $longitude,
            'format' => 'json',
            'addressdetails' => 1,
            'accept-language' => 'id',
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: instanweb/1.0 (reverse-geocode;UMKM Indonesia)',
                    'Accept: application/json',
                ],
                'timeout' => 5,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (! is_array($data) || ! isset($data['address'])) {
            return null;
        }

        $address = $data['address'];

        // provinsi ↔ state / region / city (untuk DKI Jakarta dll)
        $state = $address['state'] ?? null;
        $cityVal = $address['city'] ?? null;
        $province = null;

        foreach (array_filter([$state, $cityVal]) as $label) {
            foreach ($this->provincesData() as $p) {
                if (stripos($p['name'], (string) $label) !== false
                    || stripos((string) $label, $p['name']) !== false) {
                    $province = $p;
                    break 2;
                }
            }
        }

        // kabupaten/kota ↔ county / city / municipality
        $county = $address['county'] ?? null;
        $city = $address['city'] ?? null;
        $municipality = $address['municipality'] ?? null;
        $regency = null;

        foreach (array_filter([$county, $city, $municipality]) as $label) {
            foreach ($this->regenciesData() as $r) {
                if (stripos($r['name'], (string) $label) !== false
                    || stripos((string) $label, $r['name']) !== false) {
                    $regency = $r;
                    break 2;
                }
            }
        }

        // kecamatan ↔ village / town / suburb / hamlet / district / neighbourhood
        $village = $address['village'] ?? null;
        $town = $address['town'] ?? null;
        $suburb = $address['suburb'] ?? null;
        $hamlet = $address['hamlet'] ?? null;
        $districtLabel = $address['district'] ?? null;
        $neighbourhood = $address['neighbourhood'] ?? null;
        $district = null;

        $districtLabels = array_filter([$suburb, $districtLabel, $village, $town, $neighbourhood, $hamlet]);
        $districtCandidates = $regency
            ? array_values(array_filter(
                $this->districtsData(),
                fn (array $d) => $d['regency_id'] === $regency['id'],
            ))
            : $this->districtsData();

        foreach ($districtLabels as $label) {
            foreach ($districtCandidates as $d) {
                if (stripos($d['name'], (string) $label) !== false) {
                    $district = $d;
                    break 2;
                }
            }
        }

        $result = [];

        if ($province !== null) {
            $result['province'] = $province;
        }

        if ($regency !== null) {
            $result['regency'] = $regency;
        }

        if ($district !== null) {
            $result['district'] = $district;
        }

        return empty($result) ? null : $result;
    }

    /**
     * @param  array<int, array{id: string, name: string}>  $items
     * @return array<int, array{id: string, name: string}>
     */
    private function filter(array $items, string $search): array
    {
        $search = trim($search);

        if ($search === '') {
            return $items;
        }

        return array_values(array_filter(
            $items,
            fn (array $item) => stripos($item['name'], $search) !== false,
        ));
    }

    private function find(array $items, ?string $id): ?array
    {
        if (blank($id)) {
            return null;
        }

        foreach ($items as $item) {
            if ($item['id'] === (string) $id) {
                return $item;
            }
        }

        return null;
    }

    private function provincesData(): array
    {
        return $this->provinceData ??= $this->load('provinces.json');
    }

    private function regenciesData(): array
    {
        return $this->regencyData ??= $this->load('regencies.json');
    }

    private function districtsData(): array
    {
        return $this->districtData ??= $this->load('districts.json');
    }

    private function load(string $file): array
    {
        $path = resource_path('data/regions/' . $file);

        if (! is_file($path)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }
}
