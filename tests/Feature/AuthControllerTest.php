<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobSeeker;
use App\Models\Company;
use App\Models\Employer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Registration Tests ─────────────────────────────────────

    /** @test */
    public function job_seeker_can_register_and_receive_token()
    {
        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'job_seeker',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
            'date_of_birth' => '1995-06-15',
            'gender' => 'male',
            'employment_status' => 'currently_employed',
            'highest_education' => "Bachelor's Degree",
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'user_type'],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'user' => ['email' => 'john@example.com', 'user_type' => 'job_seeker'],
            ]);

        // Assert user was created in database
        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'user_type' => 'job_seeker']);
        $this->assertDatabaseHas('job_seekers', ['first_name' => 'John', 'last_name' => 'Doe']);

        // Assert token is a valid Sanctum token in the database
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertCount(1, $user->tokens);
        $this->assertEquals('api-token', $user->tokens->first()->name);
    }

    /** @test */
    public function employer_can_register_and_receive_token()
    {
        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'employer',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Tech Corp',
            'industry' => 'Technology',
            'company_size' => '50-100',
            'website' => 'https://techcorp.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'user_type'],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'user' => ['email' => 'jane@company.com', 'user_type' => 'employer'],
            ]);

        // Assert user, company, and employer were created
        $this->assertDatabaseHas('users', ['email' => 'jane@company.com', 'user_type' => 'employer']);
        $this->assertDatabaseHas('companies', ['name' => 'Tech Corp']);

        // Assert token exists
        $user = User::where('email', 'jane@company.com')->first();
        $this->assertNotNull($user);
        $this->assertCount(1, $user->tokens);
    }

    /** @test */
    public function registration_validates_required_fields()
    {
        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'job_seeker',
            'email' => 'invalid', // not a valid email
            'password' => 'short', // too short and no confirmation
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    }

    /** @test */
    public function registration_prevents_duplicate_email()
    {
        // Create a user first
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'job_seeker',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '1995-06-15',
            'gender' => 'male',
            'employment_status' => 'currently_employed',
            'highest_education' => "Bachelor's Degree",
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_requires_job_seeker_specific_fields()
    {
        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'job_seeker',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // Missing date_of_birth, gender, employment_status, highest_education
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_of_birth', 'gender', 'employment_status', 'highest_education']);
    }

    /** @test */
    public function registration_requires_employer_specific_fields()
    {
        $response = $this->postJson('/api/auth/register', [
            'user_type' => 'employer',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // Missing company_name, industry, company_size
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_name', 'industry', 'company_size']);
    }

    // ── Login Tests ────────────────────────────────────────────

    /** @test */
    public function user_can_login_and_receive_token()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'job_seeker',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'user_type'],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'message' => 'Login successful',
                'user' => ['email' => 'test@example.com'],
            ]);

        // Assert old tokens were deleted and a new one was created
        $user->refresh();
        $this->assertCount(1, $user->tokens);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /** @test */
    public function login_fails_with_non_existent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /** @test */
    public function login_validates_required_fields()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function login_returns_new_token_each_time_and_revokes_old_ones()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // First login
        $response1 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $firstToken = $response1->json('token');

        // Second login
        $response2 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $secondToken = $response2->json('token');

        // Tokens should be different
        $this->assertNotEquals($firstToken, $secondToken);

        // Old token should no longer work
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $firstToken,
            'Accept' => 'application/json',
        ])->getJson('/api/auth/me');

        $meResponse->assertStatus(401);
    }

    // ── Me (Get Authenticated User) Tests ──────────────────────

    /** @test */
    public function authenticated_user_can_access_me_with_token()
    {
        $user = User::factory()
            ->has(JobSeeker::factory(), 'jobSeeker')
            ->create(['user_type' => 'job_seeker']);

        // Act as this user with Sanctum
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                    'job_seeker',
                ],
            ])
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_type' => 'job_seeker',
                ],
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_me()
    {
        $response = $this->getJson('/api/auth/me', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function me_returns_employer_profile_for_employer_user()
    {
        $company = Company::factory()->create(['name' => 'Test Corp']);
        $employer = Employer::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create([
            'user_type' => 'employer',
            'employer_id' => $employer->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                    'employer',
                ],
            ]);
    }

    // ── Logout Tests ───────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_logout_and_token_is_revoked()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout successful']);

        // Token should be revoked (deleted from database)
        $this->assertCount(0, $user->tokens);

        // Old token should no longer work
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/auth/me');

        $meResponse->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout', [], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function logout_with_session_auth_works()
    {
        $user = User::factory()->create();

        // Log in via session (simulating web auth)
        $this->actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout successful']);
    }

    // ── Full Auth Flow Test ────────────────────────────────────

    /** @test */
    public function full_auth_flow_works_end_to_end()
    {
        // 1. Register
        $registerResponse = $this->postJson('/api/auth/register', [
            'user_type' => 'job_seeker',
            'first_name' => 'Full',
            'last_name' => 'Flow',
            'email' => 'full.flow@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '1995-06-15',
            'gender' => 'male',
            'employment_status' => 'currently_employed',
            'highest_education' => "Bachelor's Degree",
        ]);

        $registerResponse->assertStatus(201);
        $token = $registerResponse->json('token');
        $this->assertNotNull($token);

        // 2. Access /me with the token
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'full.flow@example.com');

        // 3. Logout
        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200);

        // 4. Token should no longer work
        $meAfterLogout = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/auth/me');

        $meAfterLogout->assertStatus(401);
    }
}
