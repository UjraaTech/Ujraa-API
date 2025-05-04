<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CreditService;

class CheckCredits
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    public function handle(Request $request, Closure $next, int $requiredCredits)
    {
        $user = $request->user();
        
        if (!$this->creditService->hasEnoughCredits($user, $requiredCredits)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient credits',
                'required' => $requiredCredits,
                'current' => $user->credits->balance
            ], 402);
        }

        return $next($request);
    }
}