<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\UseCases\GetProfileStatusUseCase;

class GetProfileStatusController extends Controller
{
    public function __invoke(GetProfileStatusUseCase $useCase): JsonResponse
    {
        $statusResponse = $useCase->execute();

        return response()->json([
            'status' => $statusResponse->status,
            'isProfileComplete' => $statusResponse->isProfileComplete,
        ]);
    }
}
