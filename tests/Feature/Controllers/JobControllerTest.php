<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $freelancer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = User::factory()->create(['role' => 'client']);
        $this->freelancer = User::factory()->create(['role' => 'freelancer']);
    }

    public function test_client_can_create_job()
    {
        $this->actingAs($this->client);

        $response = $this->postJson('/api/v1/jobs', [
            'title' => 'Test Job',
            'description' => 'Test job description',
            'budget' => 500
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'id',
                         'title',
                         'description',
                         'budget',
                         'status',
                         'proposal_count'
                     ]
                 ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Test Job',
            'client_id' => $this->client->id
        ]);
    }

    public function test_freelancer_cannot_create_job()
    {
        $this->actingAs($this->freelancer);

        $response = $this->postJson('/api/v1/jobs', [
            'title' => 'Test Job',
            'description' => 'Test job description',
            'budget' => 500
        ]);

        $response->assertStatus(403);
    }

    public function test_can_view_job_list()
    {
        Job::factory()->count(5)->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->freelancer)
                        ->getJson('/api/v1/jobs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'description',
                             'budget',
                             'status',
                             'proposal_count'
                         ]
                     ],
                     'meta' => [
                         'current_page',
                         'last_page',
                         'per_page',
                         'total'
                     ]
                 ]);
    }

    public function test_job_proposal_limit()
    {
        $job = Job::factory()->create([
            'client_id' => $this->client->id,
            'proposal_count' => 50
        ]);

        $response = $this->actingAs($this->freelancer)
                        ->postJson('/api/v1/proposals', [
                            'job_id' => $job->id,
                            'amount' => 500,
                            'delivery_days' => 5,
                            'cover_letter' => 'Test proposal'
                        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'This job has reached its maximum proposal limit'
                 ]);
    }
}