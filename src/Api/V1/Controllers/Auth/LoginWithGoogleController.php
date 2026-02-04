<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\GoogleLoginRequest;
use Src\Api\V1\Resources\Auth\AuthResource;
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
