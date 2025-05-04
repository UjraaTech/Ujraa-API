<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreSupportTicketRequest;
use App\Http\Requests\Support\ReplySupportTicketRequest;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Support",
 *     description="API Endpoints for support ticket management"
 * )
 */
class SupportController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/support/tickets",
     *     summary="Get list of support tickets",
     *     tags={"Support"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of support tickets",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="last_activity_at", type="string", format="date-time"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate();
        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/support/tickets",
     *     summary="Create a new support ticket",
     *     tags={"Support"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreSupportTicketRequest $request): JsonResponse
    {
        $ticket = auth()->user()->supportTickets()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'open',
            'last_activity_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $ticket
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/support/tickets/{ticket}",
     *     summary="Get support ticket details",
     *     tags={"Support"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Support ticket details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(
     *                     property="messages",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="message", type="string"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(SupportTicket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);
        return response()->json([
            'status' => 'success',
            'data' => $ticket->load('messages.user')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/support/tickets/{ticket}/messages",
     *     summary="Add message to support ticket",
     *     tags={"Support"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function addMessage(SupportTicket $ticket, ReplySupportTicketRequest $request): JsonResponse
    {
        $this->authorize('reply', $ticket);
        
        $message = $ticket->messages()->create([
            'message' => $request->message,
            'user_id' => auth()->id()
        ]);

        $ticket->update(['last_activity_at' => now()]);

        return response()->json([
            'status' => 'success',
            'data' => $message->load('user')
        ], 201);
    }
}