<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CheckAdminAuthorizationController extends Controller
{
    public function __invoke(): JsonResponse
    {
        Gate::authorize('admin-only');

        return response()->json(['authorized' => true]);
    }
}
