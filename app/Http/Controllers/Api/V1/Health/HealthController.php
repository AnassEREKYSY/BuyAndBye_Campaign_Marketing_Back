<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Health;

use App\Http\Controllers\Controller;
use Src\Application\Health\UseCases\GetHealthStatusUseCase;
use Src\Application\Shared\DTOs\ApiResponse;

class HealthController extends Controller
{
    public function index(GetHealthStatusUseCase $useCase)
    {
        $status = $useCase->execute();

        return response()->json(new ApiResponse(true, 'Health check', $status));
    }
}
