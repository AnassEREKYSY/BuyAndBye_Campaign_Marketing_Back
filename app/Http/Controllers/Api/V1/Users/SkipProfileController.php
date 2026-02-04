<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\UseCases\SkipProfileUseCase;

class SkipProfileController extends Controller
{
    public function __invoke(SkipProfileUseCase $useCase): JsonResponse
    {
        $useCase->execute();

        return response()->json(['message' => 'Profile skipped successfully']);
    }
}
