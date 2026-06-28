<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppDaemonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function __construct(protected WhatsAppDaemonService $daemon)
    {
    }

    public function index(): View
    {
        return view('admin.settings', [
            'enabled' => $this->daemon->isEnabled(),
            'schedulerHealthy' => $this->daemon->schedulerHealthy(),
            'lastRun' => $this->daemon->lastRun(),
            'sidecarUp' => $this->daemon->sidecarUp(),
            'persistIncoming' => (bool) config('laravel-whatsapp.persist.incoming_messages'),
            'webEnabled' => (bool) config('laravel-whatsapp.web.enabled'),
            'devices' => $this->daemon->deviceStatuses(),
            'deploy' => $this->deployConfigs(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->daemon->setEnabled($request->boolean('enabled'));

        return back()->with('status', 'Pengaturan daemon disimpan: auto-reply '
            .($request->boolean('enabled') ? 'AKTIF' : 'NONAKTIF').'.');
    }

    public function runEnsure(): RedirectResponse
    {
        Artisan::call('whatsapp:devices:ensure');

        return back()->with('status', 'Watchdog dijalankan. '.Str::limit(trim(Artisan::output()), 300));
    }

    public function runListen(): RedirectResponse
    {
        Artisan::call('whatsapp:listen:all', ['--prune' => true]);

        return back()->with('status', 'Sinkron listener dijalankan. '.Str::limit(trim(Artisan::output()), 300));
    }

    /**
     * Generate config deploy siap copy-paste, dengan path nyata dari instalasi ini.
     *
     * @return array<string, string>
     */
    protected function deployConfigs(): array
    {
        $php = PHP_BINARY;
        $base = base_path();
        $node = config('laravel-whatsapp.web.sidecar.node_binary', 'node');
        $sidecarPath = config('laravel-whatsapp.web.sidecar.path');
        $host = config('laravel-whatsapp.web.host', '127.0.0.1');
        $port = config('laravel-whatsapp.web.port', 3000);
        $token = config('laravel-whatsapp.web.token') ?: 'GANTI-DENGAN-TOKEN-KUAT';
        $sessionDir = config('laravel-whatsapp.web.sidecar.session_dir');

        $supervisor = implode("\n", [
            '; /etc/supervisor/conf.d/webwa.conf',
            '',
            '[program:webwa-sidecar]',
            "command={$node} {$sidecarPath}/index.js",
            "directory={$sidecarPath}",
            'autostart=true',
            'autorestart=true',
            'stopsignal=TERM',
            'stopwaitsecs=15',
            'user=www-data',
            "environment=PORT=\"{$port}\",HOST=\"{$host}\",SIDECAR_TOKEN=\"{$token}\",SESSION_DIR=\"{$sessionDir}\"",
            "stdout_logfile={$base}/storage/logs/sidecar.out.log",
            "stderr_logfile={$base}/storage/logs/sidecar.err.log",
            '',
            '[program:webwa-scheduler]',
            "command={$php} {$base}/artisan schedule:work",
            "directory={$base}",
            'autostart=true',
            'autorestart=true',
            'user=www-data',
            "stdout_logfile={$base}/storage/logs/scheduler.out.log",
            "stderr_logfile={$base}/storage/logs/scheduler.err.log",
        ]);

        $apply = implode("\n", [
            'sudo nano /etc/supervisor/conf.d/webwa.conf   # tempel config di atas',
            'sudo supervisorctl reread',
            'sudo supervisorctl update',
            'sudo supervisorctl status',
        ]);

        $cron = implode("\n", [
            '# Alternatif scheduler tanpa Supervisor (pilih salah satu).',
            '# Jalankan: crontab -e  lalu tempel baris berikut:',
            "* * * * * cd {$base} && {$php} artisan schedule:run >> /dev/null 2>&1",
        ]);

        $env = implode("\n", [
            'APP_ENV=production',
            'APP_DEBUG=false',
            'LICENSE_DEV_BYPASS=false',
            '',
            'WHATSAPP_WEB_ENABLED=true',
            "WHATSAPP_WEB_HOST={$host}",
            "WHATSAPP_WEB_PORT={$port}",
            "WHATSAPP_WEB_TOKEN={$token}",
            'WHATSAPP_PERSIST_INCOMING=true',
        ]);

        // ---- aaPanel (path konvensi aaPanel) ----
        $normalizedBase = str_replace('\\', '/', $base);
        $onAaPanel = str_starts_with($normalizedBase, '/www/wwwroot');

        $aaBase = $onAaPanel ? $normalizedBase : '/www/wwwroot/<domain>';
        $aaSidecar = $onAaPanel ? str_replace('\\', '/', $sidecarPath) : $aaBase.'/vendor/kstmostofa/laravel-whatsapp/sidecar';
        $aaSession = $onAaPanel ? str_replace('\\', '/', $sessionDir) : $aaBase.'/storage/app/whatsapp-sidecar/sessions';
        $aaPhp = '/www/server/php/'.PHP_MAJOR_VERSION.PHP_MINOR_VERSION.'/bin/php';
        $aaNode = '/www/server/nodejs/v20.x.x/bin/node';

        $aaSidecarCmd = "env PORT={$port} HOST={$host} SIDECAR_TOKEN={$token} SESSION_DIR={$aaSession} {$aaNode} index.js";
        $aaSchedulerCmd = "{$aaPhp} {$aaBase}/artisan schedule:work";
        $aaCron = "cd {$aaBase} && {$aaPhp} artisan schedule:run >/dev/null 2>&1";

        $aaPredeps = implode("\n", [
            '# 1) Library Chromium (pilih sesuai OS server) — Terminal aaPanel / SSH:',
            '# Ubuntu/Debian:',
            'apt install -y libnss3 libatk-bridge2.0-0 libgbm1 libasound2 libxkbcommon0 libxcomposite1 libxdamage1 libxrandr2 libpango-1.0-0 libcairo2 libcups2',
            '# CentOS/AlmaLinux:',
            'yum install -y nss atk at-spi2-atk gtk3 libdrm libgbm libXcomposite libXdamage libXrandr alsa-lib pango cups-libs',
            '',
            '# 2) Install Chromium + dependency Node sidecar (sekali saja):',
            "cd {$aaBase} && {$aaPhp} artisan whatsapp:sidecar:install",
            '',
            '# 3) Permission storage (sidecar nulis session/pid/log):',
            "chown -R www:www {$aaBase}/storage",
        ]);

        $aaDisableFn = 'exec, shell_exec, proc_open, popen, putenv, posix_kill, pcntl_signal, pcntl_alarm';

        $aapanel = [
            'on' => $onAaPanel,
            'sidecar_dir' => $aaSidecar,
            'sidecar_cmd' => $aaSidecarCmd,
            'scheduler_dir' => $aaBase,
            'scheduler_cmd' => $aaSchedulerCmd,
            'cron' => $aaCron,
            'predeps' => $aaPredeps,
            'disable_fn' => $aaDisableFn,
            'env' => $env,
        ];

        return [
            'linux' => compact('supervisor', 'apply', 'cron', 'env'),
            'aapanel' => $aapanel,
        ];
    }
}
