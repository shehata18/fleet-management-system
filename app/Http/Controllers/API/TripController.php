<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Http\Resources\TripResource;
use Illuminate\Http\JsonResponse;

class TripController extends Controller
{
    /**
     * Get list of all trips with their stations
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $trips = Trip::with(['bus', 'stations'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => TripResource::collection($trips)
        ]);
    }

    /**
     * Get specific trip details
     *
     * @param Trip $trip
     * @return JsonResponse
     */
    public function show(Trip $trip): JsonResponse
    {
        $trip->load(['stations', 'bus', 'bus.seats']);
        
        return response()->json([
            'status' => 'success',
            'data' => new TripResource($trip)
        ]);
    }
}