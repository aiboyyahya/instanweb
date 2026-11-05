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
