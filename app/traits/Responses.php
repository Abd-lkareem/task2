<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait Responses {


    public function apiSuccess($data = null, $message = "", $code = 200)
    {
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    public function apiError($message = "", $errors = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}