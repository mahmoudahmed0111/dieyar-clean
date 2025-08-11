<?php

namespace App\Http\Controllers\API;

trait ResponseTrait
{
    public function apiResponse($data = null, $message = null, $statusCode = 200)
    {
        $array = [
            'data'    => $data,
            'message' => $message,
            'status' => $statusCode,
        ];

        return response()->json($array, $statusCode);
    }
}
