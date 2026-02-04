<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use Src\Api\V1\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CheckSellerAuthorizationController extends Controller
{
    public function __invoke(): JsonResponse
    {
        Gate::authorize('seller-only');

        return response()->json(['authorized' => true]);
    }
}
