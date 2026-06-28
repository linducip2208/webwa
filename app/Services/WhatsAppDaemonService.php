<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kstmostofa\LaravelWhatsApp\Facades\WhatsApp;
use Throwable;

/**
 * Otak orkestrasi daemon auto-reply WhatsApp Web.
 *
 * Menyatukan: toggle on/off (disimpan di Setting), heartbeat scheduler, cek
 * kesehatan sidecar, dan pelacakan PID listener (selaras dengan command
 * whatsapp:listen:all). Dipakai oleh command scheduler dan halaman Admin Settings.
 */
class WhatsAppDaemonService
{
    public const KEY_ENABLED = 'whatsapp.daemon_enabled';

    public const KEY_HEARTBEAT = 'whatsapp.scheduler_last_run';

    public function isEnabled(): bool
    {
        return (bool) Setting::get(self::KEY_ENABLED, true);
    }

    public function setEnabled(bool $on): void
    {
        Setting::set(self::KEY_ENABLED, $on ? '1' : '0');
    }

    public function recordHeartbeat(): void
    {
        Setting::set(self::KEY_HEARTBEAT, now()->toIso8601String());
    }

    public function lastRun(): ?Carbon
    {
        $value = Setting::get(self::KEY_HEARTBEAT);

        return $value ? Carbon::parse($value) : null;
    }

    /** Scheduler dianggap sehat bila heartbeat terakhir < 2 menit lalu. */
    public function schedulerHealthy(): bool
    {
        $last = $this->lastRun();

        return $last !== null && $last->greaterThan(now()->subMinutes(2));
    }

    public function sidecarUp(): bool
    {
        try {
            return WhatsApp::web('_healthcheck')->client()->ping();
        } catch (Throwable) {
            return false;
        }
    }

    // ------------------------------------------------------------------
    // Pelacakan PID listener (konvensi sama dgn WhatsAppListenAll)
    // ------------------------------------------------------------------

    public function pidDir(): string
    {
        return storage_path('app/whatsapp-listeners');
    }

    public function safeName(string $session): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '_', $session);
    }

    public function pidFile(string $session): string
    {
        return $this->pidDir().DIRECTORY_SEPARATOR.$this->safeName($session).'.pid';
    }

    public function readPid(string $session): ?int
    {
        $file = $this->pidFile($session);

        if (! is_file($file)) {
            return null;
        }

        $pid = (int) trim((string) @file_get_contents($file));

        return $pid > 0 ? $pid : null;
    }

    public function writePid(string $session, int $pid): void
    {
        @file_put_contents($this->pidFile($session), (string) $pid);
    }

    public function pidAlive(int $pid): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $out = [];
            @exec('tasklist /FI "PID eq '.$pid.'" /FO CSV /NH 2>NUL', $out);

            foreach ($out as $line) {
                if (str_contains($line, '"'.$pid.'"')) {
                    return true;
                }
            }

            return false;
        }

        if (function_exists('posix_kill')) {
            return @posix_kill($pid, 0);
        }

        return file_exists("/proc/{$pid}");
    }

    public function listenerAlive(string $session): bool
    {
        $pid = $this->readPid($session);

        return $pid !== null && $this->pidAlive($pid);
    }

    /**
     * Status per device Web (non-cloud) untuk panel admin.
     *
     * @return Collection<int, array{device: Device, listener: bool}>
     */
    public function deviceStatuses(): Collection
    {
        return Device::query()->latest()->get()
            ->reject(fn (Device $d) => $d->isCloud() || blank($d->session_name))
            ->map(fn (Device $d) => [
                'device' => $d,
                'listener' => $this->listenerAlive($d->session_name),
            ])
            ->values();
    }
}
