<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Buy & Bye API",
 *     version="1.0.0",
 *     description="REST API for Buy & Bye marketplace"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
