<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\ProfileResource;
use Src\Application\Users\UseCases\GetUserProfileUseCase;

class GetUserProfileController extends Controller
{
    public function __invoke(GetUserProfileUseCase $useCase): ProfileResource
    {
        $profileResponse = $useCase->execute();

        return new ProfileResource($profileResponse);
    }
}
