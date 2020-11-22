<?php declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait Response
{
    public function jsonResponse(bool $success, int $code, ?string $errorMessage = null, array $data = []): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => $success,
                'code' => $code,
                'data' => $data,
                'errorMessage' => $errorMessage,
            ]
        );
    }
}
