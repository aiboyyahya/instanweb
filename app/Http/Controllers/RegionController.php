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
}
