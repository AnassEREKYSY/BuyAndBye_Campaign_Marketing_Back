<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Health;

use App\Http\Controllers\Controller;
use Src\Application\Health\UseCases\GetConfigUseCase;
use Src\Application\Shared\DTOs\ApiResponse;

class ConfigController extends Controller
{
    public function index(GetConfigUseCase $useCase)
    {
        $environment = app()->environment();
        $config = $useCase->execute($environment);

        return response()->json(new ApiResponse(true, 'Config retrieved', $config));
    }
}
