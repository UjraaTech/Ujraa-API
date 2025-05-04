<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCredit;
use App\Models\CreditTransaction;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function calculateProposalCost(Job $job): int
    {
        $budget = $job->budget;
        
        // Rule: ≤$200 → 2 cr, $201-1000 → 4 cr, >$1000 → 6 cr
        if ($budget <= 200) {
            return 2;
        } elseif ($budget <= 1000) {
            return 4;
        }
        return 6;
    }

    public function hasEnoughCredits(User $user, int $required): bool
    {
        return $user->credits->balance >= $required;
    }

    public function deductCredits(User $user, int $amount, string $reason, array $metadata = []): bool
    {
        return DB::transaction(function () use ($user, $amount, $reason, $metadata) {
            $credits = $user->credits;
            
            if ($credits->balance < $amount) {
                return false;
            }

            $credits->balance -= $amount;
            $credits->save();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => 'proposal_cost',
                'description' => $reason,
                'metadata' => $metadata
            ]);

            return true;
        });
    }

    public function addCredits(User $user, int $amount, string $type, string $reason): void
    {
        DB::transaction(function () use ($user, $amount, $type, $reason) {
            $credits = $user->credits;
            $credits->balance += $amount;
            $credits->save();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $reason
            ]);
        });
    }
}