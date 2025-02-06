<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

class TripResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bus' => [
                'id' => $this->bus->id,
                'number' => $this->bus->number,
                'seats_count' => $this->bus->seats->count(),
            ],
            'stations' => $this->stations->map(function ($station) {
                return [
                    'id' => $station->id,
                    'name' => $station->name,
                    'sequence' => $station->pivot->sequence,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

class SeatResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'seat_number' => $this->seat_number,
            'bus' => [
                'id' => $this->bus->id,
                'number' => $this->bus->number,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'trip' => [
                'id' => $this->trip->id,
                'name' => $this->trip->name,
            ],
            'seat' => [
                'id' => $this->seat->id,
                'seat_number' => $this->seat->seat_number,
            ],
            'from_station' => [
                'id' => $this->fromStation->id,
                'name' => $this->fromStation->name,
            ],
            'to_station' => [
                'id' => $this->toStation->id,
                'name' => $this->toStation->name,
            ],
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}