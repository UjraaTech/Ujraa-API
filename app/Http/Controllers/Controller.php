<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Ujraa API Documentation",
 *         description="API documentation for Ujraa platform"
 *     ),
 *     @OA\Server(
 *         url="/api/v1",
 *         description="API Server"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
