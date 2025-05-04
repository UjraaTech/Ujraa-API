<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="API Endpoints for admin management"
 * )
 */
class AdminController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     summary="Admin login",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // ... existing code ...
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/users",
     *     summary="List all users",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="role", type="string"),
     *                     @OA\Property(property="status", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function users(): JsonResponse
    {
        // ... existing code ...
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/admin/users/{user}/status",
     *     summary="Update user status",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"active", "disabled"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function updateStatus(User $user, Request $request): JsonResponse
    {
        // ... existing code ...
    }
}