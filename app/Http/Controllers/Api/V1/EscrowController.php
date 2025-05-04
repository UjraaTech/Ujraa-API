<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Escrow\HoldPaymentRequest;
use App\Models\EscrowTransaction;
use App\Services\EscrowService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Escrow",
 *     description="API Endpoints for escrow payment management"
 * )
 */
class EscrowController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/escrow/deposit",
     *     summary="Make escrow deposit for a job",
     *     tags={"Escrow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id", "amount"},
     *             @OA\Property(property="job_id", type="integer"),
     *             @OA\Property(property="amount", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deposit successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="transaction_id", type="integer"),
     *                 @OA\Property(property="amount", type="number"),
     *                 @OA\Property(property="status", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to make deposit"
     *     )
     * )
     */
    public function deposit(DepositRequest $request)
    {
        $transaction = $this->escrowService->holdPayment(
            $request->job,
            $request->amount
        );

        return response()->json([
            'status' => 'success',
            'data' => $transaction->load(['job', 'client', 'freelancer'])
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/escrow/release/{transaction}",
     *     summary="Release escrow payment to freelancer",
     *     tags={"Escrow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="transaction",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment released successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to release payment"
     *     )
     * )
     */
    public function release(EscrowTransaction $transaction)
    {
        $this->authorize('release', $transaction);

        if ($this->escrowService->releasePayment($transaction)) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحرير المبلغ بنجاح'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'فشل في تحرير المبلغ'
        ], 422);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/escrow/transactions",
     *     summary="Get escrow transaction history",
     *     tags={"Escrow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of escrow transactions",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="job_id", type="integer"),
     *                     @OA\Property(property="amount", type="number"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function transactions()
    {
        $this->authorize('release', $transaction);

        if ($this->escrowService->releasePayment($transaction)) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحرير المبلغ بنجاح'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'فشل في تحرير المبلغ'
        ], 422);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/escrow/{transaction}/dispute",
     *     summary="Create a dispute for an escrow transaction",
     *     tags={"Escrow"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="transaction",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dispute created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="transaction", type="object"),
     *                 @OA\Property(property="support_ticket", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to create dispute"
     *     )
     * )
     */
    public function dispute(EscrowTransaction $transaction): JsonResponse
    {
        $this->authorize('dispute', $transaction);

        $transaction->update(['status' => 'disputed']);

        // إنشاء تذكرة دعم تلقائياً
        $supportTicket = $transaction->job->supportTickets()->create([
            'user_id' => auth()->id(),
            'title' => 'نزاع على معاملة مالية',
            'description' => "نزاع على المعاملة رقم {$transaction->id}",
            'status' => 'open',
            'last_activity_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع النزاع بنجاح',
            'data' => [
                'transaction' => $transaction,
                'support_ticket' => $supportTicket
            ]
        ]);
    }
}