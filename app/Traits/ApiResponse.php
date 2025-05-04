<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($data, $meta = [], $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => $meta
        ], $status);
    }

    public function error($message, $code = 'SERVER_ERROR', $errors = [], $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'errors' => $errors
        ], $status);
    }
}