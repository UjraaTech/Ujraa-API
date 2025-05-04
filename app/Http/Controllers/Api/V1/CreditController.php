<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Credit\PurchaseRequest;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Credits",
 *     description="API Endpoints for credit management"
 * )
 */
class CreditController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/credits/balance",
     *     summary="Get user's credit balance",
     *     tags={"Credits"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current credit balance",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="balance", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function balance()
    {
        $user = auth()->user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $user->credits->balance,
                'last_updated' => $user->credits->updated_at
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/credits/transactions",
     *     summary="Get credit transaction history",
     *     tags={"Credits"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of credit transactions",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="type", type="string"),
     *                     @OA\Property(property="amount", type="integer"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function transactions()
    {
        $transactions = auth()->user()
            ->creditTransactions()
            ->latest()
            ->paginate(request('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/credits/purchase",
     *     summary="Purchase credits",
     *     tags={"Credits"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="integer", description="Amount of credits to purchase")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Credits purchased successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function purchase(PurchaseRequest $request): JsonResponse
    {
        // Process payment and add credits
        $this->creditService->addCredits(
            auth()->user(),
            $request->amount,
            'purchase',
            'Credit package purchase'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Credits purchased successfully'
        ]);
    }
}