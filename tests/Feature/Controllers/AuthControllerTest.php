<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/register', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'user',
                         'token'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        // Verify initial credits
        $this->assertDatabaseHas('user_credits', [
            'user_id' => $response->json('data.user.id'),
            'balance' => 20
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'user',
                         'token'
                     ]
                 ]);
    }
}