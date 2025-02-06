<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;
use App\Http\Resources\BookingResource;
use App\Http\Resources\SeatResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\BookingService;
use App\Exceptions\SeatNotAvailableException;
use App\Http\Requests\BookingRequest;

class BookingController extends Controller
{
    protected $bookingService;

    /**
     * BookingController constructor.
     *
     * @param BookingService $bookingService
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Get list of available seats for a specific trip and station range
     *
     * @param BookingRequest $request
     * @return JsonResponse
     */
    public function availableSeats(Request $request): JsonResponse
    {
        $trip = Trip::findOrFail($request->trip_id);
        
        $seats = $this->bookingService->getAvailableSeats(
            $trip,
            $request->from_station_id,
            $request->to_station_id
        );

        return response()->json([
            'status' => 'success',
            'data' => SeatResource::collection($seats)
        ]);
    }

    /**
     * Create a new booking
     *
     * @param BookingRequest $request
     * @return JsonResponse
     */
    public function store(BookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->createBooking([
            'user_id' => auth()->id(),
            'trip_id' => $request->trip_id,
            'from_station_id' => $request->from_station_id,
            'to_station_id' => $request->to_station_id,
            'seat_id' => $request->seat_id,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'data' => new BookingResource($booking)
        ], 201);
    }

    /**
     * Get user's bookings
     *
     * @return JsonResponse
     */
    public function myBookings(): JsonResponse
    {
        $bookings = auth()->user()->bookings()->with([
            'trip',
            'seat',
            'fromStation',
            'toStation'
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => BookingResource::collection($bookings)
        ]);
    }

    /**
     * Get specific booking details
     *
     * @param Booking $booking
     * @return JsonResponse
     */
    public function show(Booking $booking): JsonResponse
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Cancel a booking
     *
     * @param Booking $booking
     * @return JsonResponse
     */
    public function cancel(Booking $booking): JsonResponse
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking is already cancelled'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking cancelled successfully',
            'data' => new BookingResource($booking)
        ]);
    }
}
