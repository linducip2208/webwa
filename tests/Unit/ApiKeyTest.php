<?php

namespace Tests\Unit;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_produces_prefixed_hashed_key(): void
    {
        $user = User::factory()->create();
        [$model, $plain] = ApiKey::generate($user, 'My Key');

        $this->assertStringStartsWith('wwa_', $plain);
        $this->assertStringContainsString('.', $plain);
        $this->assertEquals(hash('sha256', $plain), $model->key_hash);
        $this->assertTrue(ApiKey::findByPlain($plain)->is($model));
    }

    public function test_inactive_key_is_not_found(): void
    {
        $user = User::factory()->create();
        [$model, $plain] = ApiKey::generate($user, 'K');
        $model->update(['is_active' => false]);

        $this->assertNull(ApiKey::findByPlain($plain));
    }

    public function test_expired_key_is_not_found(): void
    {
        $user = User::factory()->create();
        [$model, $plain] = ApiKey::generate($user, 'K');
        $model->update(['expires_at' => now()->subMinute()]);

        $this->assertNull(ApiKey::findByPlain($plain));
    }
}
