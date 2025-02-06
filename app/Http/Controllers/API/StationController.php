<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StationResource;

use App\Models\Station;
use Illuminate\Http\JsonResponse;

class StationController extends Controller
{
    /**
     * Get list of all stations
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $stations = Station::all();
        
        return response()->json([
            'status' => 'success',
            'data' => StationResource::collection($stations)
        ]);
    }

    /**
     * Get specific station details
     *
     * @param Station $station
     * @return JsonResponse
     */
    public function show(Station $station): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new StationResource($station)
        ]);
    }
}
