<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripStation extends Model
{
    protected $fillable = ['trip_id', 'station_id', 'sequence'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
