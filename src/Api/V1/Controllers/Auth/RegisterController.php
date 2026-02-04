<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\RegisterRequest;
use Src\Api\V1\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\RegisterRequest as RegisterDTO;
use Src\Application\Auth\UseCases\RegisterUserUseCase;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUserUseCase $useCase): AuthResource
    {
        $dto = new RegisterDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
            displayName: $request->validated('display_name'),
            photoUrl: $request->validated('photo_url'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
