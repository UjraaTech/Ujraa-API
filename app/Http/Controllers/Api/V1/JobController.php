<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Models\Job;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Jobs",
 *     description="API Endpoints for job management"
 * )
 */
class JobController extends BaseController
{
    public function __construct()
    {
        $this->middleware('role:client')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/jobs",
     *     summary="Get list of jobs",
     *     tags={"Jobs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter jobs by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"draft", "open", "in_progress", "completed", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of jobs",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="budget", type="number"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="proposal_count", type="integer")
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
    public function index()
    {
        $jobs = Job::with('client')
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(request('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $jobs->items(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/jobs",
     *     summary="Create a new job",
     *     tags={"Jobs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","budget"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="budget", type="number"),
     *             @OA\Property(property="deadline", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job created successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Only clients can create jobs"
     *     )
     * )
     */
    public function store(StoreJobRequest $request)
    {
        $job = auth()->user()->clientJobs()->create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $job
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/jobs/{job}",
     *     summary="Get job details",
     *     tags={"Jobs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="job",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job details retrieved successfully"
     *     )
     * )
     */
    public function show(Job $job)
    {
        return response()->json([
            'status' => 'success',
            'data' => $job->load(['client', 'proposals'])
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/jobs/{job}/proposals",
     *     summary="Get job proposals",
     *     tags={"Jobs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="job",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of proposals for the job"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized to view proposals"
     *     )
     * )
     */
    public function proposals(Job $job)
    {
        $this->authorize('view-proposals', $job);

        $proposals = $job->proposals()
            ->with('freelancer')
            ->paginate(request('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $proposals->items(),
            'meta' => [
                'current_page' => $proposals->currentPage(),
                'last_page' => $proposals->lastPage(),
                'per_page' => $proposals->perPage(),
                'total' => $proposals->total()
            ]
        ]);
    }
}