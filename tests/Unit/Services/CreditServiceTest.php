<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CreditService;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    private CreditService $creditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditService = new CreditService();
    }

    public function test_calculates_proposal_cost_correctly()
    {
        $lowBudgetJob = Job::factory()->create(['budget' => 200]);
        $mediumBudgetJob = Job::factory()->create(['budget' => 1000]);
        $highBudgetJob = Job::factory()->create(['budget' => 2000]);

        $this->assertEquals(2, $this->creditService->calculateProposalCost($lowBudgetJob));
        $this->assertEquals(4, $this->creditService->calculateProposalCost($mediumBudgetJob));
        $this->assertEquals(6, $this->creditService->calculateProposalCost($highBudgetJob));
    }

    public function test_checks_credit_balance_correctly()
    {
        $user = User::factory()->create();
        UserCredit::factory()->create([
            'user_id' => $user->id,
            'balance' => 10
        ]);

        $this->assertTrue($this->creditService->hasEnoughCredits($user, 5));
        $this->assertFalse($this->creditService->hasEnoughCredits($user, 15));
    }

    public function test_deducts_credits_successfully()
    {
        $user = User::factory()->create();
        UserCredit::factory()->create([
            'user_id' => $user->id,
            'balance' => 20
        ]);

        $result = $this->creditService->deductCredits($user, 5, 'Test deduction');

        $this->assertTrue($result);
        $this->assertEquals(15, $user->fresh()->credits->balance);
    }
}