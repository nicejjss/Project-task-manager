<?php

namespace App\Custom\Traits;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    protected function success(mixed $data = '', string $message = 'success', int $status = ResponseAlias::HTTP_OK): JsonResponse
    {
        return $this->returnJson($data, $message, $status);
    }

    protected function failed(mixed $data = '', string $message = 'request failed', int $status = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->returnJson($data, $message, $status);
    }

    private function returnJson($data, $message, $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ],
            $status);
    }
}
