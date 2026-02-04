<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\LoginRequest as LoginDTO;
use Src\Application\Auth\UseCases\LoginUserUseCase;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserUseCase $useCase): AuthResource
    {
        $dto = new LoginDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
