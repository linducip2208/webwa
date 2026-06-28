<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\WhatsAppDaemonService;
use Illuminate\Console\Command;
use Kstmostofa\LaravelWhatsApp\Exceptions\SidecarException;

/**
 * Watchdog sesi WhatsApp Web.
 *
 * Browser headless (Chromium) yang dijalankan whatsapp-web.js di dalam sidecar
 * adalah koneksi WhatsApp yang sebenarnya. Kalau browser/sesi itu putus, sidecar
 * cuma menandai status `disconnected` dan diam — pesan masuk berhenti, auto-reply
 * mati. Command ini melakukan otomatis apa yang biasanya dilakukan manual saat
 * user membuka halaman device (DeviceController@state / @connect): cek status
 * live ke sidecar dan boot ulang sesi yang putus (LocalAuth → reconnect tanpa
 * scan QR ulang).
 *
 * Dijalankan tiap menit via scheduler (routes/console.php), tepat sebelum
 * whatsapp:listen:all yang memasang konsumen SSE-nya.
 *
 * Catatan: command ini TIDAK bisa menghidupkan proses sidecar (Node) yang mati —
 * itu tugas Supervisor/systemd. Ia hanya membangkitkan ulang sesi di dalam
 * sidecar yang sudah berjalan.
 */
class WhatsAppDevicesEnsure extends Command
{
    protected $signature = 'whatsapp:devices:ensure
        {--dry-run : Tampilkan tindakan tanpa benar-benar mem-boot ulang sesi}';

    protected $description = 'Pantau & boot ulang sesi WhatsApp Web yang putus agar auto-reply tetap hidup.';

    protected const TRANSIENT = ['initializing', 'authenticated', 'connecting'];

    protected const NEEDS_HUMAN = ['qr', 'auth_failure'];

    public function __construct(protected WhatsAppDaemonService $daemon)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Heartbeat ditulis lebih dulu, terlepas dari toggle, supaya Admin Settings
        // bisa mendeteksi apakah scheduler (cron/schedule:work) benar-benar jalan.
        $this->daemon->recordHeartbeat();

        if (! $this->daemon->isEnabled()) {
            $this->warn('Daemon auto-reply dinonaktifkan di Admin Settings — watchdog dilewati.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        $devices = Device::query()
            ->whereNotNull('last_connected_at')
            ->get()
            ->reject(fn (Device $d) => $d->isCloud() || blank($d->session_name));

        if ($devices->isEmpty()) {
            $this->line('Tidak ada device Web yang pernah terhubung. Lewati.');

            return self::SUCCESS;
        }

        if (! $devices->first()->webSession()->client()->ping()) {
            $this->error('Sidecar tidak terjangkau. Jalankan: php artisan whatsapp:sidecar:start '
                .'(atau pastikan Supervisor/systemd menghidupkannya).');

            return self::FAILURE;
        }

        $rebooted = 0;

        foreach ($devices as $device) {
            $session = $device->webSession();
            $id = $session->id();

            try {
                $status = $session->state()['status'] ?? 'disconnected';
            } catch (SidecarException) {
                $status = 'missing';
            }

            if (in_array($status, Device::READY_STATES, true)) {
                $this->syncReady($device);
                $this->line("  ✓ {$id} ready");

                continue;
            }

            if (in_array($status, self::NEEDS_HUMAN, true)) {
                $device->update(['status' => $status]);
                $this->warn("  ⚠ {$id} butuh tindakan manual ({$status}) — scan QR / re-auth");

                continue;
            }

            if (in_array($status, self::TRANSIENT, true)) {
                $device->update(['status' => $status]);
                $this->line("  … {$id} {$status} (menunggu)");

                continue;
            }

            // disconnected / error / missing → boot ulang sesi.
            if ($dryRun) {
                $this->comment("  ▶ {$id} akan di-boot ulang (status: {$status}) [dry-run]");

                continue;
            }

            try {
                $resp = $session->start();
                $newStatus = $resp['status'] ?? 'connecting';
                $device->update(['status' => $newStatus]);
                $this->info("  ▶ {$id} di-boot ulang (status: {$status} → {$newStatus})");
                $rebooted++;
            } catch (SidecarException $e) {
                $this->warn("  ✗ {$id} gagal di-boot ulang: {$e->getMessage()}");
            }
        }

        $this->line("Selesai. Device dicek: {$devices->count()}, di-boot ulang: {$rebooted}.");

        return self::SUCCESS;
    }

    /**
     * Sinkronkan metadata device saat sesi sudah ready — mirror dari
     * DeviceController@state agar dashboard tetap fresh tanpa harus dibuka.
     */
    protected function syncReady(Device $device): void
    {
        $update = ['status' => 'ready', 'last_connected_at' => now()];

        try {
            $info = $device->webSession()->info();
            $update['phone'] = $info['wid']['user'] ?? $info['me']['user'] ?? $device->phone;
            $update['push_name'] = $info['pushname'] ?? $info['pushName'] ?? $device->push_name;
        } catch (SidecarException) {
            // info belum siap — biarkan nilai lama
        }

        $device->update($update);
    }
}
