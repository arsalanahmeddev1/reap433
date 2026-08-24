<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $status,
            'message' => $message,
            'data' => $data === null ? (object) [] : $data,
        ], $status);
    }

    protected function error(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $status,
            'message' => $message,
            'data' => $errors,
        ], $status);
    }
}
