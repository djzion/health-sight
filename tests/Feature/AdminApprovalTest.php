<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminApprovalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register_with_status_pending()
    {
        $response = $this->post('/register', [
            'full_name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '23456789012',
            'district_id' => 1,
            'lga_id' => 1,
            'phc_id' => 1,
            'role_id' => 2,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/home'); // Assuming successful registration redirects to home
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_approve_a_user()
    {
        $user = User::factory()->create(['status' => 'pending']);

        $response = $this->patch("/users/{$user->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_reject_a_user()
    {
        $user = User::factory()->create(['status' => 'pending']);

        $response = $this->patch("/users/{$user->id}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'rejected',
        ]);
    }

    /** @test */
    public function a_pending_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'pending@example.com',
            'password' => Hash::make('password123'),
            'status' => 'pending',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login'); // Assuming failed login redirects back to login
        $response->assertSessionHasErrors(['Your account is pending approval.']);
    }

    /** @test */
    public function an_approved_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'approved@example.com',
            'password' => Hash::make('password123'),
            'status' => 'approved',
        ]);

        $response = $this->post('/login', [
            'email' => 'approved@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home'); // Assuming successful login redirects to home
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_rejected_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'rejected@example.com',
            'password' => Hash::make('password123'),
            'status' => 'rejected',
        ]);

        $response = $this->post('/login', [
            'email' => 'rejected@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['Your account has been rejected.']);
    }
}
