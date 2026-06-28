<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePair;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(RequirePair::class);
    }

    public function test_user_can_create_device_with_auto_session_name(): void
    {
        $user = User::factory()->create(['device_limit' => 2]);

        $this->actingAs($user)->post('/devices', ['name' => 'CS Utama', 'backend' => 'web'])
            ->assertRedirect();

        $device = $user->devices()->first();
        $this->assertNotNull($device);
        $this->assertEquals('CS Utama', $device->name);
        $this->assertNotEmpty($device->session_name);
        $this->assertStringStartsWith('u'.$user->id.'_', $device->session_name);
    }

    public function test_device_limit_is_enforced(): void
    {
        $user = User::factory()->create(['device_limit' => 1]);
        $user->devices()->create(['name' => 'A', 'backend' => 'web', 'status' => 'disconnected']);

        $this->actingAs($user)->post('/devices', ['name' => 'B', 'backend' => 'web'])
            ->assertSessionHasErrors('name');

        $this->assertEquals(1, $user->devices()->count());
    }

    public function test_user_cannot_view_another_users_device(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $device = $owner->devices()->create(['name' => 'X', 'backend' => 'web', 'status' => 'disconnected']);

        $this->actingAs($other)->get('/devices/'.$device->id)->assertForbidden();
    }
}
