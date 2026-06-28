<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePair;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(RequirePair::class);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'budi@test.com', 'role' => 'user']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'a@test.com',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $this->post('/login', ['email' => 'a@test.com', 'password' => 'secret123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'b@test.com', 'password' => Hash::make('secret123')]);

        $this->from('/login')
            ->post('/login', ['email' => 'b@test.com', 'password' => 'salah'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
