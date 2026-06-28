<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePair;
use App\Models\User;
use App\Services\WhatsAppDaemonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(RequirePair::class);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/settings')
            ->assertOk()
            ->assertSee('Config Deploy')
            ->assertSee('aaPanel')
            ->assertSee('webwa-sidecar');
    }

    public function test_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/settings')->assertForbidden();
    }

    public function test_admin_can_toggle_daemon_off_and_on(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $svc = app(WhatsAppDaemonService::class);

        $this->actingAs($admin)->put('/admin/settings', ['enabled' => '0'])->assertRedirect();
        $this->assertFalse($svc->isEnabled());

        $this->actingAs($admin)->put('/admin/settings', ['enabled' => '1'])->assertRedirect();
        $this->assertTrue($svc->isEnabled());
    }
}
