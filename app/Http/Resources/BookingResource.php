<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
