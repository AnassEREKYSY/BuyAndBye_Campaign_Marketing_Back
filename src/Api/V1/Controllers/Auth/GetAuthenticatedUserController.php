<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use Src\Api\V1\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use Src\Application\Auth\UseCases\GetAuthenticatedUserUseCase;

class GetAuthenticatedUserController extends Controller
{
    public function __invoke(GetAuthenticatedUserUseCase $useCase): UserResource
    {
        $userResponse = $useCase->execute();

        return new UserResource($userResponse);
    }
}
