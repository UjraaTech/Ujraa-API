<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    UserController,
    JobController,
    ProposalController,
    CreditController,
    EscrowController,
    SupportController
};

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/reset-request', [AuthController::class, 'requestPasswordReset']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User routes
        Route::get('user/profile', [UserController::class, 'profile']);
        Route::put('user/profile', [UserController::class, 'updateProfile']);
        Route::post('user/switch-role', [UserController::class, 'switchRole']);
        Route::post('user/verify-identity', [UserController::class, 'verifyIdentity']);

        // Job routes
        Route::apiResource('jobs', JobController::class);
        Route::get('jobs/{job}/proposals', [JobController::class, 'proposals']);

        // Proposal routes
        Route::apiResource('proposals', ProposalController::class)
            ->except(['edit', 'create']);
        Route::post('proposals/{proposal}/withdraw', [ProposalController::class, 'withdraw']);

        // Credit routes
        Route::get('credits/balance', [CreditController::class, 'balance']);
        Route::get('credits/history', [CreditController::class, 'history']);
        Route::post('credits/purchase', [CreditController::class, 'purchase']);

        // Escrow routes
        Route::post('escrow/hold', [EscrowController::class, 'hold']);
        Route::post('escrow/release/{transaction}', [EscrowController::class, 'release']);
        Route::post('escrow/dispute/{transaction}', [EscrowController::class, 'dispute']);

        // Support routes
        Route::apiResource('support-tickets', SupportController::class);
        Route::post('support-tickets/{ticket}/reply', [SupportController::class, 'reply']);

        // Auth routes (authenticated)
        Route::post('logout', [AuthController::class, 'logout']);
    });
});