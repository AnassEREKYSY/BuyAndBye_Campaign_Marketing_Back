<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\GoogleLoginRequest as GoogleLoginDTO;
use Src\Application\Auth\UseCases\LoginWithGoogleUseCase;

class LoginWithGoogleController extends Controller
{
    public function __invoke(GoogleLoginRequest $request, LoginWithGoogleUseCase $useCase): AuthResource
    {
        $dto = new GoogleLoginDTO(
            idToken: $request->validated('idToken'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
