<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Proposal\StoreProposalRequest;
use App\Models\Proposal;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Proposals",
 *     description="API Endpoints for proposal management"
 * )
 */
class ProposalController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/jobs/{job}/proposals",
     *     summary="Submit a proposal for a job",
     *     tags={"Proposals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="job",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "delivery_days", "cover_letter"},
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="delivery_days", type="integer"),
     *             @OA\Property(property="cover_letter", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Proposal submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="amount", type="number"),
     *                 @OA\Property(property="delivery_days", type="integer"),
     *                 @OA\Property(property="cover_letter", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="credits_used", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not enough credits or not authorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(Job $job, StoreProposalRequest $request)
    {
        $user = auth()->user();
        $job = $request->job;

        // Check proposal limit
        if ($job->proposal_count >= 50) {
            return response()->json([
                'status' => 'error',
                'message' => 'This job has reached its maximum proposal limit'
            ], 422);
        }

        // Calculate and deduct credits
        $requiredCredits = $this->creditService->calculateProposalCost($job);
        
        if (!$this->creditService->deductCredits($user, $requiredCredits, 'Proposal submission')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient credits'
            ], 402);
        }

        $proposal = $job->proposals()->create([
            'freelancer_id' => $user->id,
            'amount' => $request->amount,
            'delivery_days' => $request->delivery_days,
            'cover_letter' => $request->cover_letter,
            'credits_used' => $requiredCredits
        ]);

        // Increment proposal count
        $job->increment('proposal_count');

        return response()->json([
            'status' => 'success',
            'data' => $proposal->load('job', 'freelancer')
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/proposals/{proposal}/accept",
     *     summary="Accept a proposal",
     *     tags={"Proposals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="proposal",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proposal accepted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to accept this proposal"
     *     )
     * )
     */
    public function accept(Proposal $proposal)
    {
        $this->authorize('withdraw', $proposal);

        if ($proposal->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending proposals can be withdrawn'
            ], 422);
        }

        $proposal->update(['status' => 'withdrawn']);

        return response()->json([
            'status' => 'success',
            'message' => 'Proposal withdrawn successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/proposals/{proposal}/reject",
     *     summary="Reject a proposal",
     *     tags={"Proposals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="proposal",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proposal rejected successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to reject this proposal"
     *     )
     * )
     */
    public function reject(Proposal $proposal)
    {
        $this->authorize('withdraw', $proposal);

        if ($proposal->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending proposals can be withdrawn'
            ], 422);
        }

        $proposal->update(['status' => 'withdrawn']);

        return response()->json([
            'status' => 'success',
            'message' => 'Proposal withdrawn successfully'
        ]);
    }
}