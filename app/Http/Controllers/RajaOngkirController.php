<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RajaOngkirController extends Controller
{
    /**
     * Get all provinces
     */
    public function getProvinces()
    {
        try {
            $response = app('rajaongkir')->provinces();
        } catch (\Throwable $exception) {
            Log::error('Exception while fetching provinces from RajaOngkir', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'Tidak dapat terhubung ke layanan RajaOngkir.'
            ], 503);
        }

        if ($response->successful()) {
            $payload = $response->json();

            return response()->json($this->formatProvinceResponse($payload));
        }

        Log::error('Failed to fetch provinces from RajaOngkir', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $message = data_get($response->json(), 'rajaongkir.status.description')
            ?? data_get($response->json(), 'message')
            ?? 'Failed to fetch provinces';

        return response()->json(['error' => $message], $response->status() ?: 500);
    }

    /**
     * Get cities by province
     */
    public function getCities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province_id' => 'required',
            'keyword' => 'nullable|string',
            'province_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $params = [
            'province_id' => $request->province_id,
        ];

        if ($request->filled('keyword')) {
            $params['keyword'] = $request->keyword;
            $params['search'] = $request->keyword;
            $params['q'] = $request->keyword;
        }

        try {
            $response = app('rajaongkir')->cities($params);
        } catch (\Throwable $exception) {
            Log::error('Exception while fetching cities from RajaOngkir', [
                'keyword' => $request->keyword,
                'province_id' => $request->province_id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'Tidak dapat terhubung ke layanan RajaOngkir.'
            ], 503);
        }

        if ($response->successful()) {
            $payload = $response->json();

            return response()->json($this->formatCityResponse($payload, [
                'province' => $request->input('province_name'),
                'province_id' => $request->province_id,
            ]));
        }

        Log::error('Failed to fetch cities from RajaOngkir', [
            'keyword' => $request->keyword,
            'province_id' => $request->province_id,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $message = data_get($response->json(), 'rajaongkir.status.description')
            ?? data_get($response->json(), 'message')
            ?? 'Failed to fetch cities';

        return response()->json(['error' => $message], $response->status() ?: 500);
    }

    /**
     * Get districts by city
     */
    public function getDistricts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required',
            'city_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $params = [
            'city_id' => $request->city_id,
        ];

        try {
            $response = app('rajaongkir')->districts($params);
        } catch (\Throwable $exception) {
            Log::error('Exception while fetching districts from RajaOngkir', [
                'city_id' => $request->city_id,
                'city_name' => $request->city_name,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'Tidak dapat terhubung ke layanan RajaOngkir.'
            ], 503);
        }

        if ($response->successful()) {
            $payload = $response->json();

            return response()->json($this->formatDistrictResponse($payload, [
                'city' => $request->input('city_name'),
                'city_id' => $request->city_id,
            ]));
        }

        Log::error('Failed to fetch districts from RajaOngkir', [
            'city_id' => $request->city_id,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $message = data_get($response->json(), 'rajaongkir.status.description')
            ?? data_get($response->json(), 'message')
            ?? 'Failed to fetch districts';

        return response()->json(['error' => $message], $response->status() ?: 500);
    }

    /**
     * Calculate shipping cost
     */
    public function getShippingCost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin' => 'required',
            'destination' => 'required',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string',
            'price' => 'nullable',
            'origin_type' => 'nullable|string|in:city,district',
            'destination_type' => 'nullable|string|in:city,district',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $payload = [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ];

        if ($request->filled('price')) {
            $payload['price'] = $request->price;
        }

        if ($request->filled('origin_type')) {
            $payload['origin_type'] = $request->origin_type;
        }

        if ($request->filled('destination_type')) {
            $payload['destination_type'] = $request->destination_type;
        }

        try {
            $response = app('rajaongkir')->cost($payload);
        } catch (\Throwable $exception) {
            Log::error('Exception while requesting RajaOngkir shipping cost', [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'Tidak dapat terhubung ke layanan RajaOngkir.'
            ], 503);
        }

        if ($response->successful()) {
            return response()->json($response->json());
        }

        Log::error('Failed to calculate RajaOngkir shipping cost', [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $message = data_get($response->json(), 'rajaongkir.status.description')
            ?? data_get($response->json(), 'message')
            ?? 'Failed to calculate shipping cost';

        return response()->json(['error' => $message], $response->status() ?: 500);
    }

    /**
     * Format province response into legacy structure expected by the frontend.
     */
    protected function formatProvinceResponse(array $payload): array
    {
        $results = $this->extractItems($payload, [
            'rajaongkir.results',
            'data.items',
            'data.data',
            'data',
            'results',
        ]);

        if (empty($results)) {
            return $payload;
        }

        $normalized = array_values(array_filter(array_map(function ($item) {
            $id = data_get($item, 'province_id')
                ?? data_get($item, 'province_code')
                ?? data_get($item, 'code')
                ?? data_get($item, 'id');

            $name = data_get($item, 'province')
                ?? data_get($item, 'province_name')
                ?? data_get($item, 'name')
                ?? data_get($item, 'label');

            if (!$id || !$name) {
                return null;
            }

            return [
                'province_id' => (string) $id,
                'province' => $name,
            ];
        }, $results)));

        return [
            'rajaongkir' => [
                'results' => $normalized
            ]
        ];
    }

    /**
     * Format city response into legacy structure expected by the frontend.
     */
    protected function formatCityResponse(array $payload, array $context = []): array
    {
        $results = $this->extractItems($payload, [
            'rajaongkir.results',
            'data.items',
            'data.data',
            'data',
            'results',
        ]);

        if (empty($results)) {
            return $payload;
        }

        $normalized = array_values(array_filter(array_map(function ($item) use ($context) {
            $id = data_get($item, 'city_id')
                ?? data_get($item, 'city_code')
                ?? data_get($item, 'code')
                ?? data_get($item, 'id');

            $name = data_get($item, 'city_name')
                ?? data_get($item, 'name')
                ?? data_get($item, 'label');

            $type = data_get($item, 'type')
                ?? data_get($item, 'city_type')
                ?? data_get($item, 'type_name')
                ?? '';

            $province = data_get($item, 'province')
                ?? data_get($item, 'province_name')
                ?? data_get($context, 'province')
                ?? null;

            $provinceId = data_get($item, 'province_id')
                ?? data_get($item, 'province_code')
                ?? data_get($context, 'province_id')
                ?? null;

            if (!$id || !$name) {
                return null;
            }

            return [
                'city_id' => (string) $id,
                'city_name' => $name,
                'type' => $type,
                'province' => $province,
                'province_id' => $provinceId
            ];
        }, $results)));

        return [
            'rajaongkir' => [
                'results' => $normalized
            ]
        ];
    }

    /**
     * Format district response into legacy structure expected by the frontend.
     */
    protected function formatDistrictResponse(array $payload, array $context = []): array
    {
        $results = $this->extractItems($payload, [
            'rajaongkir.results',
            'data.items',
            'data.data',
            'data',
            'results',
        ]);

        if (empty($results)) {
            return $payload;
        }

        $normalized = array_values(array_filter(array_map(function ($item) use ($context) {
            $id = data_get($item, 'subdistrict_id')
                ?? data_get($item, 'district_id')
                ?? data_get($item, 'id');

            $name = data_get($item, 'subdistrict_name')
                ?? data_get($item, 'district_name')
                ?? data_get($item, 'name')
                ?? data_get($item, 'label');

            if (!$id || !$name) {
                return null;
            }

            $city = data_get($item, 'city')
                ?? data_get($item, 'city_name')
                ?? data_get($context, 'city')
                ?? null;

            $cityId = data_get($item, 'city_id')
                ?? data_get($context, 'city_id')
                ?? null;

            $province = data_get($item, 'province')
                ?? data_get($item, 'province_name')
                ?? null;

            $provinceId = data_get($item, 'province_id') ?? null;

            return [
                'subdistrict_id' => (string) $id,
                'subdistrict_name' => $name,
                'city' => $city,
                'city_id' => $cityId,
                'province' => $province,
                'province_id' => $provinceId,
            ];
        }, $results)));

        return [
            'rajaongkir' => [
                'results' => $normalized
            ]
        ];
    }

    /**
     * Extract items from payload using multiple possible paths.
     */
    protected function extractItems(array $payload, array $paths): array
    {
        foreach ($paths as $path) {
            $items = data_get($payload, $path);
            if ($items !== null) {
                return (array) $items;
            }
        }
        return [];
    }

    /**
     * Get tracking information (waybill)
     */
    public function getWaybill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'waybill' => 'required|string',
            'courier' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $payload = [
            'waybill' => $request->waybill,
            'courier' => $request->courier,
        ];

        try {
            $response = app('rajaongkir')->waybill($payload);
        } catch (\Throwable $exception) {
            Log::error('Exception while requesting RajaOngkir waybill', [
                'waybill' => $request->waybill,
                'courier' => $request->courier,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'Tidak dapat terhubung ke layanan RajaOngkir.'
            ], 503);
        }

        if ($response->successful()) {
            return response()->json($response->json());
        }

        Log::error('Failed to get RajaOngkir waybill', [
            'waybill' => $request->waybill,
            'courier' => $request->courier,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $message = data_get($response->json(), 'rajaongkir.status.description')
            ?? data_get($response->json(), 'message')
            ?? 'Failed to get waybill information';

        return response()->json(['error' => $message], $response->status() ?: 500);
    }

    /**
     * Get all available couriers
     */
    public function getCouriers()
    {
        $couriers = [
            ['code' => 'jne', 'name' => 'Jalur Nugraha Ekakurir (JNE)'],
            ['code' => 'sicepat', 'name' => 'SiCepat Express'],
            ['code' => 'ide', 'name' => 'ID Express'],
            ['code' => 'sap', 'name' => 'Satria Antaran Prima'],
            ['code' => 'jnt', 'name' => 'J&T Express'],
            ['code' => 'ninja', 'name' => 'Ninja Xpress'],
            ['code' => 'tiki', 'name' => 'TIKI'],
            ['code' => 'lion', 'name' => 'Lion Parcel'],
            ['code' => 'anteraja', 'name' => 'AnterAja'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'ncs', 'name' => 'Nusantara Card Semesta (NCS)'],
            ['code' => 'rex', 'name' => 'Royal Express Indonesia (REX)'],
            ['code' => 'rpx', 'name' => 'RPX Holding'],
            ['code' => 'sentral', 'name' => 'Sentral Cargo'],
            ['code' => 'star', 'name' => 'Star Cargo'],
            ['code' => 'wahana', 'name' => 'Wahana Prestasi Logistik'],
            ['code' => 'dse', 'name' => '21 Express (DSE)'],
        ];

        return response()->json([
            'rajaongkir' => [
                'results' => $couriers
            ]
        ]);
    }

}
