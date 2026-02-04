<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CheckSellerOrAdminAuthorizationController extends Controller
{
    public function __invoke(): JsonResponse
    {
        Gate::authorize('seller-or-admin');

        return response()->json(['authorized' => true]);
    }
}
