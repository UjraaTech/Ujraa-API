<?php

namespace App\Services;

use App\Models\User;
use App\Models\EscrowTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    protected $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }

    public function processPayment(EscrowTransaction $transaction): bool
    {
        if ($transaction->status !== 'held') {
            return false;
        }

        return DB::transaction(function () use ($transaction) {
            // Calculate the net amount after platform fee
            $netAmount = $transaction->amount - $transaction->platform_fee;
            
            // Here you would integrate with your payment gateway
            // to transfer the funds to the freelancer
            
            // For example:
            // $paymentGateway->transfer([
            //     'amount' => $netAmount,
            //     'recipient' => $transaction->freelancer->payment_id
            // ]);

            return $this->escrowService->releasePayment($transaction);
        });
    }

    public function requestPayout(User $user, float $amount): bool
    {
        // Here you would implement the payout request logic
        // This would typically create a payout request that requires admin approval
        
        return true;
    }
}