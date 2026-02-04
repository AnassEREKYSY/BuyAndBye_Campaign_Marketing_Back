<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
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
