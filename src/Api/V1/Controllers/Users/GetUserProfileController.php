<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Users\ProfileResource;
use Src\Application\Users\UseCases\GetUserProfileUseCase;

class GetUserProfileController extends Controller
{
    public function __invoke(GetUserProfileUseCase $useCase): ProfileResource
    {
        $profileResponse = $useCase->execute();

        return new ProfileResource($profileResponse);
    }
}
