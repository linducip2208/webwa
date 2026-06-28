<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\MessageLog;
use App\Models\User;
use App\Services\GatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Membuktikan pipeline auto-reply jalan: pesan masuk (MessageReceived) yang cocok
 * dengan rule aktif → AutoReplyListener → GatewayService::sendText.
 *
 * GatewayService di-mock supaya tidak benar-benar mengirim WhatsApp, jadi tes ini
 * deterministik & tanpa efek samping (tidak perlu sidecar / device beneran).
 */
class AutoReplyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Device, 2: \App\Models\AutoReply}
     */
    protected function makeSetup(array $ruleOverrides = []): array
    {
        $user = User::factory()->create(['is_active' => true, 'monthly_quota' => 100]);

        $device = $user->devices()->create([
            'name' => 'CS Utama',
            'backend' => 'web',
            'status' => 'ready',
            'session_name' => 'test_sess',
            'last_connected_at' => now(),
        ]);

        $rule = $user->autoReplies()->create(array_merge([
            'name' => 'Sapaan',
            'match_type' => 'contains',
            'keyword' => 'halo',
            'reply_text' => 'Hai, ada yang bisa kami bantu?',
            'case_sensitive' => false,
            'skip_groups' => false,
            'is_active' => true,
            'priority' => 0,
        ], $ruleOverrides));

        return [$user, $device, $rule];
    }

    protected function fireInbound(string $body, array $messageOverrides = []): void
    {
        event(new MessageReceived('test_sess', [
            'message' => array_merge([
                'from' => '6281234567890@c.us',
                'body' => $body,
                'fromMe' => false,
                'type' => 'chat',
            ], $messageOverrides),
        ]));
    }

    public function test_auto_reply_fires_on_matching_message(): void
    {
        [, $device, $rule] = $this->makeSetup();

        $this->mock(GatewayService::class, function (MockInterface $m) use ($device) {
            $m->shouldReceive('sendText')->once()
                ->withArgs(function (Device $d, string $to, string $body, array $ctx) use ($device) {
                    return $d->id === $device->id
                        && $to === '6281234567890@c.us'
                        && $body === 'Hai, ada yang bisa kami bantu?'
                        && ($ctx['source'] ?? null) === 'auto_reply';
                })
                ->andReturn(new MessageLog());
        });

        $this->fireInbound('halo kak, mau tanya');

        $this->assertSame(1, $rule->fresh()->triggered_count);
        $this->assertNotNull($rule->fresh()->last_triggered_at);
    }

    public function test_no_reply_when_keyword_does_not_match(): void
    {
        $this->makeSetup(['keyword' => 'promo']);

        $this->mock(GatewayService::class, fn (MockInterface $m) => $m->shouldNotReceive('sendText'));

        $this->fireInbound('halo kak');
    }

    public function test_no_reply_for_own_outgoing_message(): void
    {
        $this->makeSetup();

        $this->mock(GatewayService::class, fn (MockInterface $m) => $m->shouldNotReceive('sendText'));

        $this->fireInbound('halo', ['fromMe' => true]);
    }

    public function test_no_reply_when_quota_exhausted(): void
    {
        [$user] = $this->makeSetup();
        $user->update(['monthly_quota' => 0]);

        $this->mock(GatewayService::class, fn (MockInterface $m) => $m->shouldNotReceive('sendText'));

        $this->fireInbound('halo');
    }
}
