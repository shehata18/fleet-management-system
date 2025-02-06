<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'from_station_id' => 'required|exists:stations,id',
            'to_station_id' => 'required|exists:stations,id|different:from_station_id',
            'seat_id' => 'required|exists:seats,id'
        ];
    }

    public function messages()
    {
        return [
            'to_station_id.different' => 'The destination station must be different from the starting station.',
        ];
    }
}