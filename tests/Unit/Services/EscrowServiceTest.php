<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EscrowService;
use App\Models\User;
use App\Models\Job;
use App\Models\EscrowTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EscrowServiceTest extends TestCase
{
    use RefreshDatabase;

    private EscrowService $escrowService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escrowService = new EscrowService();
    }

    public function test_calculates_platform_fee_correctly()
    {
        $client = User::factory()->create();
        $freelancer = User::factory()->create();

        // First collaboration should have 5% fee
        $fee = $this->escrowService->calculatePlatformFee($client, $freelancer, 1000);
        $this->assertEquals(50, $fee);

        // Create a previous transaction
        EscrowTransaction::factory()->create([
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id
        ]);

        // Subsequent collaboration should have 10% fee
        $fee = $this->escrowService->calculatePlatformFee($client, $freelancer, 1000);
        $this->assertEquals(100, $fee);
    }

    public function test_holds_payment_successfully()
    {
        $job = Job::factory()->create();
        $amount = 1000;

        $transaction = $this->escrowService->holdPayment($job, $amount);

        $this->assertInstanceOf(EscrowTransaction::class, $transaction);
        $this->assertEquals('held', $transaction->status);
        $this->assertEquals($amount, $transaction->amount);
    }
}