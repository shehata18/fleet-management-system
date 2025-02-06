<?php

namespace App\Exceptions;

use Exception;

class SeatNotAvailableException extends Exception
{
    public function render($request)    
    {
        return response()->json([
            'message' => 'The selected seat is not available.'
        ], 422);
    }
}


