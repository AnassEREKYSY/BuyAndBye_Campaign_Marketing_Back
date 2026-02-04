<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Gate;
use Src\Application\Auth\DTOs\GoogleLoginRequest as GoogleLoginDTO;
use Src\Application\Auth\DTOs\LoginRequest as LoginDTO;
use Src\Application\Auth\DTOs\RegisterRequest as RegisterDTO;
use Src\Application\Auth\UseCases\GetAuthenticatedUserUseCase;
use Src\Application\Auth\UseCases\LoginUserUseCase;
use Src\Application\Auth\UseCases\LoginWithGoogleUseCase;
use Src\Application\Auth\UseCases\RegisterUserUseCase;
use Src\Application\Shared\DTOs\ApiResponse;
use Src\Infrastructure\Services\Base64ImageConverter;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterUserUseCase $useCase,
        Base64ImageConverter $converter
    ) {
        $photoUrl = $request->file('photo') ? $converter->toDataUri($request->file('photo')) : null;
        $dto = new RegisterDTO($request->email, $request->password, $request->display_name, $photoUrl);
        $result = $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Registered', $result));
    }

    public function login(LoginRequest $request, LoginUserUseCase $useCase)
    {
        $dto = new LoginDTO($request->email, $request->password);
        $result = $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Logged in', $result));
    }

    public function google(GoogleLoginRequest $request, LoginWithGoogleUseCase $useCase)
    {
        $dto = new GoogleLoginDTO($request->idToken);
        $result = $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Logged in with Google', $result));
    }

    public function me(GetAuthenticatedUserUseCase $useCase)
    {
        $result = $useCase->execute();

        return response()->json(new ApiResponse(true, 'Authenticated user', $result));
    }

    public function buyerOnly()
    {
        Gate::authorize('buyer-only');

        return response()->json(new ApiResponse(true, 'Buyer authorized'));
    }

    public function sellerOnly()
    {
        Gate::authorize('seller-only');

        return response()->json(new ApiResponse(true, 'Seller authorized'));
    }

    public function adminOnly()
    {
        Gate::authorize('admin-only');

        return response()->json(new ApiResponse(true, 'Admin authorized'));
    }

    public function sellerOrAdmin()
    {
        Gate::authorize('seller-or-admin');

        return response()->json(new ApiResponse(true, 'Seller or Admin authorized'));
    }
}
