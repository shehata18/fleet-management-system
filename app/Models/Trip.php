<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = ['name', 'bus_id'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function tripStations()
    {
        return $this->hasMany(TripStation::class)->orderBy('sequence');
    }
    public function stations()
    {
        return $this->belongsToMany(Station::class, 'trip_stations')
            ->withPivot('sequence')
            ->orderBy('trip_stations.sequence');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
