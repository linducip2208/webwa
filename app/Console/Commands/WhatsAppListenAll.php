<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\WhatsAppDaemonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Keep-alive supervisor untuk listener WhatsApp Web.
 *
 * Setiap device yang terhubung (status di Device::READY_STATES, backend `web`)
 * butuh satu daemon `whatsapp:web:listen {session}` agar pesan masuk + auto-reply
 * berjalan. Command ini men-spawn daemon yang belum hidup, melewati yang sudah
 * jalan, dan (dengan --prune) menghentikan daemon untuk device yang sudah putus.
 *
 * Dijalankan tiap menit via scheduler (routes/console.php). Bisa di-ON/OFF dari
 * Admin Settings — saat OFF, --prune akan menghentikan semua listener.
 */
class WhatsAppListenAll extends Command
{
    protected $signature = 'whatsapp:listen:all
        {--prune : Hentikan listener untuk device yang sudah tidak terhubung}
        {--reconnect-delay=2 : Diteruskan ke whatsapp:web:listen}';

    protected $description = 'Jaga daemon whatsapp:web:listen tetap hidup untuk setiap device yang terhubung.';

    public function __construct(protected WhatsAppDaemonService $daemon)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        File::ensureDirectoryExists($this->daemon->pidDir());

        if (! $this->daemon->isEnabled()) {
            $this->warn('Daemon auto-reply dinonaktifkan di Admin Settings.');

            if ($this->option('prune')) {
                $this->prune([]); // tidak ada device aktif → hentikan semua listener
            }

            return self::SUCCESS;
        }

        $devices = Device::query()
            ->whereIn('status', Device::READY_STATES)
            ->get()
            ->reject(fn (Device $d) => $d->isCloud() || blank($d->session_name));

        $activeSafe = [];

        foreach ($devices as $device) {
            $session = $device->session_name;
            $activeSafe[$this->daemon->safeName($session)] = true;

            if ($this->daemon->listenerAlive($session)) {
                $this->line("  ✓ {$session} sudah listening");

                continue;
            }

            $pid = $this->spawn($session);

            if ($pid) {
                $this->daemon->writePid($session, $pid);
                $this->info("  ▶ {$session} dijalankan (pid {$pid})");
            } else {
                $this->warn("  ✗ {$session} gagal dijalankan");
            }
        }

        if ($this->option('prune')) {
            $this->prune($activeSafe);
        }

        $this->line('Selesai. Device terhubung: '.$devices->count());

        return self::SUCCESS;
    }

    protected function prune(array $activeSafe): void
    {
        foreach (glob($this->daemon->pidDir().DIRECTORY_SEPARATOR.'*.pid') ?: [] as $file) {
            $name = basename($file, '.pid');

            if (isset($activeSafe[$name])) {
                continue;
            }

            $pid = (int) trim((string) @file_get_contents($file));

            if ($pid > 0 && $this->daemon->pidAlive($pid)) {
                $this->kill($pid);
                $this->warn("  ■ listener orphan dihentikan (pid {$pid}, {$name})");
            }

            @unlink($file);
        }
    }

    protected function spawn(string $session): ?int
    {
        $artisan = base_path('artisan');
        $delay = (int) $this->option('reconnect-delay');

        if (PHP_OS_FAMILY === 'Windows') {
            return $this->spawnWindows($artisan, $session, $delay);
        }

        return $this->spawnUnix($artisan, $session, $delay);
    }

    protected function spawnWindows(string $artisan, string $session, int $delay): ?int
    {
        // Win32_Process.Create = proses fully-detached (tanpa inherited handle/console).
        // PHP exec() langsung kembali walau listener tetap hidup. (Start-Process
        // -RedirectStandardOutput membuat daemon menahan pipe pemanggil → exec() dan
        // scheduler menggantung selamanya.) Artisan dipanggil relatif via CurrentDirectory
        // agar path berspasi ("project laravel") tidak terpecah.
        $cmdLine = '"'.PHP_BINARY.'" artisan whatsapp:web:listen '.$session.' --reconnect-delay='.$delay;

        $ps = '$r = Invoke-CimMethod -ClassName Win32_Process -MethodName Create -Arguments @{'
            ."CommandLine = '".str_replace("'", "''", $cmdLine)."'; "
            ."CurrentDirectory = '".str_replace("'", "''", base_path())."'"
            ."}\r\n".'$r.ProcessId';

        $tmp = storage_path('app/wa-spawn-'.$this->daemon->safeName($session).'.ps1');
        @file_put_contents($tmp, $ps);

        $out = [];
        @exec('powershell -NoProfile -ExecutionPolicy Bypass -File '.escapeshellarg($tmp), $out);
        @unlink($tmp);

        $pid = (int) trim(implode('', $out));

        return $pid > 0 ? $pid : null;
    }

    protected function spawnUnix(string $artisan, string $session, int $delay): ?int
    {
        $cmd = sprintf(
            'nohup %s %s whatsapp:web:listen %s --reconnect-delay=%d > %s 2>&1 & echo $!',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($artisan),
            escapeshellarg($session),
            $delay,
            escapeshellarg($this->logFile($session)),
        );

        $out = [];
        @exec('/bin/sh -c '.escapeshellarg($cmd), $out);

        $pid = (int) trim(implode('', $out));

        return $pid > 0 ? $pid : null;
    }

    protected function kill(int $pid): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            @exec('taskkill /PID '.$pid.' /T /F 2>NUL');

            return;
        }

        if (function_exists('posix_kill')) {
            @posix_kill($pid, SIGTERM);

            return;
        }

        @exec('kill '.$pid.' 2>/dev/null');
    }

    protected function logFile(string $session): string
    {
        return storage_path('logs/whatsapp-listen-'.$this->daemon->safeName($session).'.log');
    }
}
