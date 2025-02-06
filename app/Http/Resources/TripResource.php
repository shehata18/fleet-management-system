<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
