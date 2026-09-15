<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'device_name' => 'flutter_pixel',
        ];

        $response = $this->postJson(route('api.v1.auth.register'), $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'created_at'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);
    }

    public function test_user_registration_requires_valid_data(): void
    {
        $response = $this->postJson(route('api.v1.auth.register'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('Secret123!'),
            'is_active' => true,
        ]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'johndoe@example.com',
            'password' => 'Secret123!',
            'device_name' => 'flutter_phone',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('Secret123!'),
            'is_active' => true,
        ]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'johndoe@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'deactivated@example.com',
            'password' => Hash::make('Secret123!'),
            'is_active' => false,
        ]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'deactivated@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Your account has been deactivated.',
            ]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('api.v1.auth.me'));

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson(route('api.v1.auth.me'));

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'bio' => 'Original Bio',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson(route('api.v1.auth.profile'), [
                'name' => 'Updated Name',
                'bio' => 'Updated Bio text for Flutter user',
                'job_title' => 'Mobile Developer',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully',
                'user' => [
                    'name' => 'Updated Name',
                    'bio' => 'Updated Bio text for Flutter user',
                    'job_title' => 'Mobile Developer',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'bio' => 'Updated Bio text for Flutter user',
        ]);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword1!'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson(route('api.v1.auth.password'), [
                'current_password' => 'CurrentPassword1!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password updated successfully',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token');

        $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson(route('api.v1.auth.logout'));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_authenticated_user_can_delete_account(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson(route('api.v1.auth.account'), [
                'password' => 'Password123!',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Account deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
