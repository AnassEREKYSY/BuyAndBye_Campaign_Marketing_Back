<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Users\ActivateUserUseCase;
use App\Application\UseCases\Users\DeleteUserUseCase;
use App\Application\UseCases\Users\GetUserUseCase;
use App\Application\UseCases\Users\ListUsersUseCase;
use App\Application\UseCases\Users\RestoreUserUseCase;
use App\Application\UseCases\Users\SuspendUserUseCase;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Admin - Users", description="Admin user management endpoints")
 */
class AdminUserController extends Controller
{
    private function authorizeAdmin(): void
    {
        Gate::authorize('admin-only');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/users",
     *     tags={"Admin - Users"},
     *     summary="List users (paginated)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="role", in="query", required=false, @OA\Schema(type="string", enum={"brand","influencer","admin"})),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"pending_verification","active","suspended","banned","deleted"})),
     *     @OA\Response(response=200, description="Users list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(ListUsersUseCase $useCase): AnonymousResourceCollection
    {
        $this->authorizeAdmin();

        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);
        $role = request()->query('role');
        $status = request()->query('status');

        $users = $useCase->execute($page, $size, $role, $status);

        return UserResource::collection($users);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Get a user by id",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="User returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id, GetUserUseCase $useCase): UserResource
    {
        $this->authorizeAdmin();

        return new UserResource($useCase->execute($id));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Update a user (admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="display_name", type="string", example="New Name"),
     *             @OA\Property(property="role", type="string", enum={"brand","influencer","admin"}, example="brand"),
     *             @OA\Property(property="status", type="string", enum={"pending_verification","active","suspended","banned","deleted"}, example="active")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(string $id, UpdateUserRequest $request, UserRepositoryInterface $users): UserResource
    {
        $this->authorizeAdmin();

        $user = $users->findById($id);
        if (! $user) {
            abort(404, 'User not found.');
        }

        $data = array_filter([
            'display_name' => $request->input('display_name'),
            'role' => $request->input('role'),
            'status' => $request->input('status'),
        ], fn ($v) => $v !== null);

        if (! empty($data)) {
            $users->update($user, $data);
        }

        return new UserResource($user->fresh(['brandProfile', 'influencerProfile']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/users/{id}/suspend",
     *     tags={"Admin - Users"},
     *     summary="Suspend a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="User suspended"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function suspend(string $id, SuspendUserUseCase $useCase): JsonResponse
    {
        $this->authorizeAdmin();

        $useCase->execute($id);

        return response()->json(['success' => true]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/users/{id}/activate",
     *     tags={"Admin - Users"},
     *     summary="Activate a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="User activated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function activate(string $id, ActivateUserUseCase $useCase): JsonResponse
    {
        $this->authorizeAdmin();

        $useCase->execute($id);

        return response()->json(['success' => true]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/users/{id}",
     *     tags={"Admin - Users"},
     *     summary="Delete a user (soft delete)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="User deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function delete(string $id, DeleteUserUseCase $useCase): JsonResponse
    {
        $this->authorizeAdmin();

        $useCase->execute($id);

        return response()->json(['success' => true]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/users/{id}/restore",
     *     tags={"Admin - Users"},
     *     summary="Restore a soft-deleted user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="User restored"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function restore(string $id, RestoreUserUseCase $useCase): JsonResponse
    {
        $this->authorizeAdmin();

        $useCase->execute($id);

        return response()->json(['success' => true]);
    }
}