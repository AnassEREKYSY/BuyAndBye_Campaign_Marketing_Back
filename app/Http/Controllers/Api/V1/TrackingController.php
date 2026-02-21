<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Tracking\RedirectTrackingLinkUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Tracking", description="Tracking links and click logging")
 */
class TrackingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/t/{code}",
     *     tags={"Tracking"},
     *     summary="Redirect tracking link and log click event",
     *     @OA\Parameter(name="code", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=302, description="Redirect to destination"),
     *     @OA\Response(response=404, description="Tracking link not found")
     * )
     */
    public function redirect(string $code, Request $request, RedirectTrackingLinkUseCase $useCase): RedirectResponse
    {
        $destination = $useCase->execute(
            code: $code,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            referrer: $request->headers->get('referer')
        );

        return redirect()->away($destination);
    }
}