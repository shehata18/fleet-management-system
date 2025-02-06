<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Booking;
use App\Models\TripStation;
use App\Exceptions\SeatNotAvailableException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Get available seats for a trip between two stations
     *
     * @param Trip $trip
     * @param int $fromStationId
     * @param int $toStationId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getAvailableSeats(Trip $trip, int $fromStationId, int $toStationId)
    {
        // Validate station sequence
        $this->validateStationSequence($trip, $fromStationId, $toStationId);

        // Get all seats for the bus
        $seats = $trip->bus->seats;

        // Get bookings that overlap with the requested journey
        $overlappingBookings = $this->getOverlappingBookings($trip->id, $fromStationId, $toStationId);

        // Filter out booked seats
        $bookedSeatIds = $overlappingBookings->pluck('seat_id')->unique();

        return $seats->whereNotIn('id', $bookedSeatIds);
    }

    /**
     * Create a new booking
     *
     * @param array $data
     * @return Booking
     * @throws SeatNotAvailableException|\Exception
     */
    public function createBooking(array $data)
    {
        // Change from findOrFail to first() to ensure single model
        $trip = Trip::where('id', $data['trip_id'])->first();
        if (!$trip) {
            throw new \Exception('Trip not found');
        }

        // Validate station sequence
        $this->validateStationSequence($trip, $data['from_station_id'], $data['to_station_id']);

        // Check if seat is available
        $availableSeats = $this->getAvailableSeats($trip, $data['from_station_id'], $data['to_station_id']);

        if (!$availableSeats->contains('id', $data['seat_id'])) {
            throw new SeatNotAvailableException('The selected seat is not available for this journey.');
        }

        // Create booking
        return DB::transaction(function () use ($data) {
            return Booking::create([
                'user_id' => auth()->id(),
                'trip_id' => $data['trip_id'],
                'seat_id' => $data['seat_id'],
                'from_station_id' => $data['from_station_id'],
                'to_station_id' => $data['to_station_id'],
                'status' => 'confirmed',
            ]);
        });
    }

    /**
     * Validate that stations are in correct sequence for the trip
     *
     * @param Trip $trip
     * @param int $fromStationId
     * @param int $toStationId
     * @throws \Exception
     */
    private function validateStationSequence(Trip $trip, int $fromStationId, int $toStationId)
    {
        $tripStations = $trip->stations()->orderBy('trip_stations.sequence')->get();

        $fromStationSequence = $tripStations->firstWhere('id', $fromStationId)?->pivot->sequence;
        $toStationSequence = $tripStations->firstWhere('id', $toStationId)?->pivot->sequence;

        if (!$fromStationSequence || !$toStationSequence) {
            throw new \Exception('Invalid stations for this trip.');
        }

        if ($fromStationSequence >= $toStationSequence) {
            throw new \Exception('Invalid station sequence.');
        }
    }

    /**
     * Get bookings that overlap with the requested journey
     *
     * @param int $tripId
     * @param int $fromStationId
     * @param int $toStationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getOverlappingBookings(int $tripId, int $fromStationId, int $toStationId)
    {
        return Booking::where('trip_id', $tripId)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($fromStationId, $toStationId) {
                // Get bookings where:
                // 1. Booking's from_station is between our journey's from and to stations
                // 2. Booking's to_station is between our journey's from and to stations
                // 3. Booking spans our entire journey
                $query
                    ->whereHas('fromStation', function ($q) use ($fromStationId, $toStationId) {
                        $q->whereHas('tripStations', function ($q) use ($fromStationId, $toStationId) {
                            $q->whereBetween('sequence', function ($q) use ($fromStationId, $toStationId) {
                                $q->select('sequence')
                                    ->from('trip_stations')
                                    ->whereIn('station_id', [$fromStationId, $toStationId]);
                            });
                        });
                    })
                    ->orWhereHas('toStation', function ($q) use ($fromStationId, $toStationId) {
                        $q->whereHas('tripStations', function ($q) use ($fromStationId, $toStationId) {
                            $q->whereBetween('sequence', function ($q) use ($fromStationId, $toStationId) {
                                $q->select('sequence')
                                    ->from('trip_stations')
                                    ->whereIn('station_id', [$fromStationId, $toStationId]);
                            });
                        });
                    })
                    ->orWhere(function ($q) use ($fromStationId, $toStationId) {
                        $q->whereHas('fromStation', function ($q) use ($fromStationId) {
                            $q->whereHas('tripStations', function ($q) use ($fromStationId) {
                                $q->where('sequence', '<', function ($q) use ($fromStationId) {
                                    $q->select('sequence')->from('trip_stations')->where('station_id', $fromStationId);
                                });
                            });
                        })->whereHas('toStation', function ($q) use ($toStationId) {
                            $q->whereHas('tripStations', function ($q) use ($toStationId) {
                                $q->where('sequence', '>', function ($q) use ($toStationId) {
                                    $q->select('sequence')->from('trip_stations')->where('station_id', $toStationId);
                                });
                            });
                        });
                    });
            })
            ->get();
    }
}
