<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Notifications\ListNotificationsUseCase;
use App\Application\UseCases\Notifications\MarkAllNotificationsReadUseCase;
use App\Application\UseCases\Notifications\MarkNotificationReadUseCase;
use App\Application\UseCases\Notifications\UnreadCountUseCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Notifications", description="User notifications")
 */
class NotificationController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/notifications",
     *     tags={"Notifications"},
     *     summary="List my notifications",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="unread", in="query", required=false, @OA\Schema(type="boolean", example=false)),
     *     @OA\Response(response=200, description="Notifications list"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(ListNotificationsUseCase $useCase): JsonResponse
    {
        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);
        $unread = filter_var(request()->query('unread', false), FILTER_VALIDATE_BOOLEAN);

        $items = $useCase->execute($this->user(), $page, $size, $unread);

        return UserNotificationResource::collection($items)->response();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/notifications/unread-count",
     *     tags={"Notifications"},
     *     summary="Get unread notifications count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Unread count"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function unreadCount(UnreadCountUseCase $useCase): JsonResponse
    {
        $count = $useCase->execute($this->user());

        return response()->json(['data' => ['count' => $count]]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notifications/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark a notification as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Marked read"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function markRead(string $id, MarkNotificationReadUseCase $useCase): JsonResponse
    {
        $n = $useCase->execute($this->user(), $id);

        return (new UserNotificationResource($n))->response();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notifications/read-all",
     *     tags={"Notifications"},
     *     summary="Mark all notifications as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="All marked read"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function markAllRead(MarkAllNotificationsReadUseCase $useCase): JsonResponse
    {
        $count = $useCase->execute($this->user());

        return response()->json(['data' => ['marked' => $count]]);
    }
}