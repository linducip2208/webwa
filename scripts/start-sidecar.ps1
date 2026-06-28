# Start WebWA whatsapp-web.js sidecar di Windows.
# (php artisan whatsapp:sidecar:start tidak jalan di Windows karena pakai nohup/posix —
#  pakai script ini sebagai gantinya.)
#
# Jalankan:  powershell -ExecutionPolicy Bypass -File scripts\start-sidecar.ps1

$ErrorActionPreference = 'Continue'
$root = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $root '.env'

function Get-EnvVal($key, $default) {
    if (Test-Path $envFile) {
        $line = Select-String -Path $envFile -Pattern "^$key=" -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($line) { return ($line.Line -replace "^$key=", '').Trim().Trim('"') }
    }
    return $default
}

$env:PORT           = Get-EnvVal 'WHATSAPP_WEB_PORT'  '3000'
$env:HOST           = Get-EnvVal 'WHATSAPP_WEB_HOST'  '127.0.0.1'
$env:SIDECAR_TOKEN  = Get-EnvVal 'WHATSAPP_WEB_TOKEN' 'change-me-shared-secret'
$env:SESSION_DIR    = Join-Path $root 'storage\app\whatsapp-sidecar\sessions'
$env:SIDECAR_PID_FILE = Join-Path $root 'storage\app\whatsapp-sidecar\sidecar.pid'

New-Item -ItemType Directory -Force -Path $env:SESSION_DIR | Out-Null

$host.UI.RawUI.WindowTitle = "sidecar webwa"
Write-Host "WebWA sidecar -> http://$($env:HOST):$($env:PORT)   (token: $($env:SIDECAR_TOKEN))" -ForegroundColor Green
Write-Host "Session dir: $($env:SESSION_DIR)"
Write-Host "Tekan Ctrl+C untuk berhenti.`n"

Set-Location (Join-Path $root 'whatsapp-sidecar')
node index.js
