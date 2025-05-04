<?php

namespace App\Services;

use App\Models\User;
use App\Models\Job;
use App\Models\EscrowTransaction;
use Illuminate\Support\Facades\DB;

class EscrowService
{
    public function calculatePlatformFee(User $client, User $freelancer, float $amount): float
    {
        // Check if this is their first collaboration
        $previousTransactions = EscrowTransaction::where(function ($query) use ($client, $freelancer) {
            $query->where('client_id', $client->id)
                  ->where('freelancer_id', $freelancer->id);
        })->orWhere(function ($query) use ($client, $freelancer) {
            $query->where('client_id', $freelancer->id)
                  ->where('freelancer_id', $client->id);
        })->exists();

        // First time collaboration gets 5% fee, otherwise 10%
        $feePercentage = $previousTransactions ? 0.10 : 0.05;
        
        return round($amount * $feePercentage, 2);
    }

    public function holdPayment(Job $job, float $amount): EscrowTransaction
    {
        return DB::transaction(function () use ($job, $amount) {
            $platformFee = $this->calculatePlatformFee(
                $job->client,
                $job->proposals->where('status', 'accepted')->first()->freelancer,
                $amount
            );

            return EscrowTransaction::create([
                'job_id' => $job->id,
                'client_id' => $job->client_id,
                'freelancer_id' => $job->proposals->where('status', 'accepted')->first()->freelancer_id,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'is_first_collaboration' => ($platformFee / $amount) < 0.10,
                'status' => 'held'
            ]);
        });
    }

    public function releasePayment(EscrowTransaction $transaction): bool
    {
        if ($transaction->status !== 'held') {
            return false;
        }

        return DB::transaction(function () use ($transaction) {
            $transaction->status = 'released';
            $transaction->save();
            
            // Here you would trigger the actual payment process
            // This could involve your payment gateway integration
            
            return true;
        });
    }
}