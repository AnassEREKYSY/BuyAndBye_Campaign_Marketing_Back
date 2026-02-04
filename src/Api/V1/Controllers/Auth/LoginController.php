<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\LoginRequest;
use Src\Api\V1Resources\Auth\AuthResource;
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
