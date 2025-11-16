<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{


    #[Test]
    public function test_register_requires_all_required_fields()
    {
        // dd( $this->postJson('/api/register', [])->assertStatus(422));
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJson([
            'name' => ['Name is required.'],
            'email' => ['Email is required.'],
            'password' => ['Password is required.'],
            'password_confirmation' => ['Confirm password is required.'],
        ]);
    }

    #[Test]
    public function test_user_can_register_successfully()
    {

        $role = Role::where('name', 'user')->first();
        $email = 'siddhesh' . rand(1, 1000) . '@example.com';
        $payload = [
            'name' => 'Siddhesh',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',

        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'siddhesh@example.com']);
    }

    #[Test]
    public function test_login_requires_email_and_password()
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJson([
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]);
    }

    #[Test]
    public function test_user_can_login_with_valid_credentials()
    {
        $role = Role::where('name', 'user')->first();
        $email = 'demo' . rand(1, 1000) . '@example.com';
        $user = User::factory()->create([
            'role_id' => $role->id,
            'email' => $email,
            'password' => Hash::make('mypassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'mypassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    #[Test]
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function test_authenticated_user_can_logout()
    {
        $role = Role::where('name', 'user')->first();

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Sanctum::actingAs($user);

        $res = $this->postJson('/api/logout');

        $res->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);
    }

    #[Test]
    public function test_unauthenticated_user_cannot_logout()
    {
        $this->postJson('/api/logout')
            ->assertStatus(500)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
