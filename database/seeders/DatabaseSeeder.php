<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Station;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\Seat;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);


        // Create stations
        $stations = [
            'Cairo',
            'Giza',
            'AlFayyum',
            'AlMinya',
            'Asyut'
        ];

        foreach ($stations as $stationName) {
            Station::create(['name' => $stationName]);
        }

        // Create buses
        $bus = Bus::create(['number' => 'BUS-001']);

        // Create seats for the bus
        for ($i = 1; $i <= 12; $i++) {
            Seat::create([
                'bus_id' => $bus->id,
                'seat_number' => 'SEAT-' . str_pad($i, 2, '0', STR_PAD_LEFT)
            ]);
        }

        // Create a trip
        $trip = Trip::create([
            'name' => 'Cairo-Asyut Trip',
            'bus_id' => $bus->id
        ]);

        // Add stations to trip
        $trip->stations()->attach([
            1 => ['sequence' => 1], // Cairo
            3 => ['sequence' => 2], // AlFayyum
            4 => ['sequence' => 3], // AlMinya
            5 => ['sequence' => 4], // Asyut
        ]);
    }

}