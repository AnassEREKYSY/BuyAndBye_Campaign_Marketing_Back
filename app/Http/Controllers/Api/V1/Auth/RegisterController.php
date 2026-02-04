<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthResource;
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
