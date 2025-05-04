<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserCredit;
use App\Services\CreditService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 */
class AuthController extends BaseController
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name","email","phone","password","password_confirmation"},
     *             @OA\Property(property="full_name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string"),
     *             @OA\Property(property="language", type="string", enum={"ar", "en"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'language' => $request->language ?? 'ar'
            ]);

            // Create initial profile
            $user->profile()->create([]);

            // Add initial 20 credits
            UserCredit::create([
                'user_id' => $user->id,
                'balance' => 20
            ]);

            return $user;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token
        ], [], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error('البريد الإلكتروني غير مسجل', 'EMAIL_NOT_FOUND', [], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('كلمة المرور غير صحيحة', 'INVALID_PASSWORD', [], 401);
        }

        if ($user->status === 'disabled') {
            return $this->error('الحساب معطل', 'ACCOUNT_DISABLED', [], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     )
     * )
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'Successfully logged out'
        ]);
    }
}