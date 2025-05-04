<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\SwitchRoleRequest;
use App\Http\Requests\User\VerifyIdentityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="API Endpoints for user profile management"
 * )
 */
class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     summary="Get user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="avatar_url", type="string"),
     *                 @OA\Property(property="role", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function profile(): JsonResponse
    {
        $user = auth()->user()->load(['profile', 'roles', 'skills', 'portfolios']);
        
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile",
     *     summary="Update user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        $user->update($request->only(['full_name', 'language']));
        
        $user->profile()->update($request->except(['full_name', 'language']));

        return response()->json([
            'status' => 'success',
            'data' => $user->load('profile')
        ]);
    }

    public function switchRole(SwitchRoleRequest $request): JsonResponse
    {
        $user = auth()->user();
        $newRole = $request->role;

        // Store role history
        $user->roles()->create([
            'role' => $newRole,
            'assigned_at' => now()
        ]);

        // Update current role
        $user->update(['role' => $newRole]);

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles')
        ]);
    }

    public function verifyIdentity(VerifyIdentityRequest $request): JsonResponse
    {
        $user = auth()->user();
        
        if ($request->hasFile('identity_document')) {
            $path = $request->file('identity_document')->store('identity_documents', 'public');
            
            // Store document path and mark for admin verification
            $user->update([
                'identity_document_path' => $path,
                'identity_verified' => false // Will be verified by admin
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Identity document uploaded successfully. Pending verification.'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/profile/avatar",
     *     summary="Upload profile avatar",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="avatar_url", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا وجدت
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar_path' => $path]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'avatar_url' => Storage::url($user->avatar_path)
            ]
        ]);
    }
}