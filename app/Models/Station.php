<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = ['name'];

    public function tripStations()
    {
        return $this->hasMany(TripStation::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_stations')->withPivot('sequence')->orderBy('trip_stations.sequence');
    }
}
