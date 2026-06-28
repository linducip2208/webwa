<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewayApiTest extends TestCase
{
    use RefreshDatabase;

    protected function keyFor(User $user): string
    {
        [, $plain] = ApiKey::generate($user, 'Test');

        return $plain;
    }

    public function test_me_requires_api_key(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_me_rejects_invalid_key(): void
    {
        $this->withHeaders(['Authorization' => 'Bearer wwa_salah.totallywrong'])
            ->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_me_with_valid_key(): void
    {
        $user = User::factory()->create();
        $key = $this->keyFor($user);

        $this->withHeaders(['Authorization' => 'Bearer '.$key])
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_devices_list_with_key(): void
    {
        $user = User::factory()->create();
        $user->devices()->create(['name' => 'A', 'backend' => 'web', 'status' => 'disconnected']);
        $key = $this->keyFor($user);

        $this->withHeaders(['Authorization' => 'Bearer '.$key])
            ->getJson('/api/v1/devices')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_send_text_validation_error(): void
    {
        $user = User::factory()->create();
        $key = $this->keyFor($user);

        $this->withHeaders(['Authorization' => 'Bearer '.$key])
            ->postJson('/api/v1/messages/text', [])
            ->assertStatus(422);
    }

    public function test_send_text_device_not_found(): void
    {
        $user = User::factory()->create();
        $key = $this->keyFor($user);

        $this->withHeaders(['Authorization' => 'Bearer '.$key])
            ->postJson('/api/v1/messages/text', ['device' => '99999', 'to' => '628123', 'message' => 'hi'])
            ->assertStatus(404);
    }

    public function test_quota_exhausted_returns_429(): void
    {
        $user = User::factory()->create(['monthly_quota' => 0]);
        $device = $user->devices()->create(['name' => 'A', 'backend' => 'web', 'status' => 'disconnected']);
        $key = $this->keyFor($user);

        $this->withHeaders(['Authorization' => 'Bearer '.$key])
            ->postJson('/api/v1/messages/text', ['device' => (string) $device->id, 'to' => '628123', 'message' => 'hi'])
            ->assertStatus(429);
    }
}
