<?php

namespace App\Http\Controllers;

use App\Services\RegionService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(private readonly RegionService $regions)
    {
    }

    public function provinces(Request $request)
    {
        return response()->json(
            $this->regions->provinces((string) $request->query('search', '')),
        );
    }

    public function regencies(Request $request)
    {
        return response()->json(
            $this->regions->regencies(
                $request->query('province_id'),
                (string) $request->query('search', ''),
            ),
        );
    }

    public function districts(Request $request)
    {
        return response()->json(
            $this->regions->districts(
                $request->query('city_id'),
                (string) $request->query('search', ''),
            ),
        );
    }

    /**
     * Reverse geocode koordinat menjadi ID & label provinsi/kota/kecamatan lokal.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function geocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $result = $this->regions->reverseGeocode(
            (float) $request->latitude,
            (float) $request->longitude,
        );

        $payload = [
            'province_id' => $result['province']['id'] ?? null,
            'province_name' => $result['province']['name'] ?? null,
            'city_id' => $result['regency']['id'] ?? null,
            'city_name' => $result['regency']['name'] ?? null,
            'district_id' => $result['district']['id'] ?? null,
            'district_name' => $result['district']['name'] ?? null,
            'matched' => $result !== null,
        ];

        return response()->json($payload);
    }
}
