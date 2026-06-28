<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(): View
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $demoAccounts = [
            ['role' => 'Admin', 'email' => 'admin@webwa.test', 'password' => 'password', 'scope' => 'Akses penuh: kelola user, semua device, semua log, panel WhatsApp /whatsapp'],
            ['role' => 'User', 'email' => 'user@webwa.test', 'password' => 'password', 'scope' => 'Dashboard pribadi: device, API key, kirim pesan, log'],
            ['role' => 'Demo', 'email' => 'demo@webwa.test', 'password' => 'password', 'scope' => 'Akun demo dengan device & log contoh'],
        ];

        $endpoints = [
            [
                'method' => 'POST',
                'path' => '/api/v1/messages/text',
                'title' => 'Kirim Pesan Teks',
                'body' => '{
  "device": "device-id-atau-nama",
  "to": "628123456789",
  "message": "Halo dari WebWA!"
}',
                'curl' => 'curl -X POST '.$baseUrl.'/api/v1/messages/text \\
  -H "Authorization: Bearer wwa_xxx.yyy" \\
  -H "Content-Type: application/json" \\
  -d \'{"device":"1","to":"628123456789","message":"Halo!"}\'',
            ],
            [
                'method' => 'POST',
                'path' => '/api/v1/messages/media',
                'title' => 'Kirim Media (gambar/video/dokumen)',
                'body' => '{
  "device": "1",
  "to": "628123456789",
  "type": "image",
  "url": "https://contoh.com/foto.jpg",
  "caption": "Foto produk"
}',
                'curl' => 'curl -X POST '.$baseUrl.'/api/v1/messages/media \\
  -H "Authorization: Bearer wwa_xxx.yyy" \\
  -d \'{"device":"1","to":"628123456789","type":"image","url":"https://contoh.com/foto.jpg"}\'',
            ],
            [
                'method' => 'GET',
                'path' => '/api/v1/devices',
                'title' => 'Daftar Device',
                'body' => null,
                'curl' => 'curl '.$baseUrl.'/api/v1/devices -H "Authorization: Bearer wwa_xxx.yyy"',
            ],
            [
                'method' => 'GET',
                'path' => '/api/v1/devices/{device}/status',
                'title' => 'Cek Status Device',
                'body' => null,
                'curl' => 'curl '.$baseUrl.'/api/v1/devices/1/status -H "Authorization: Bearer wwa_xxx.yyy"',
            ],
            [
                'method' => 'GET',
                'path' => '/api/v1/devices/{device}/qr',
                'title' => 'Ambil QR Code Pairing',
                'body' => null,
                'curl' => 'curl '.$baseUrl.'/api/v1/devices/1/qr -H "Authorization: Bearer wwa_xxx.yyy"',
            ],
        ];

        $tutorial = [
            ['phase' => 'Fase 1 · Registrasi & Login', 'steps' => [
                'Buka halaman /register dan buat akun baru (nama, email, password).',
                'Login di /login. Anda diarahkan ke dashboard pribadi.',
            ]],
            ['phase' => 'Fase 2 · Tambah & Hubungkan Device', 'steps' => [
                'Masuk menu Device → "Tambah Device". Pilih backend Web (nomor pribadi) atau Cloud (Meta resmi).',
                'Klik "Hubungkan" pada device Web — sebuah QR code akan muncul.',
                'Buka WhatsApp di HP → Perangkat Tertaut → Tautkan Perangkat → pindai QR.',
                'Status device berubah menjadi "Terhubung" otomatis.',
            ]],
            ['phase' => 'Fase 3 · Buat API Key', 'steps' => [
                'Masuk menu API Key → "Buat API Key". Beri nama (mis. "Server Produksi").',
                'Salin key yang muncul SEKALI ini. Simpan aman — tidak akan ditampilkan ulang.',
            ]],
            ['phase' => 'Fase 4 · Kirim Pesan Pertama', 'steps' => [
                'Coba lewat menu "Kirim Pesan": pilih device, masukkan nomor tujuan + isi pesan.',
                'Atau lewat REST API: POST /api/v1/messages/text dengan header Authorization: Bearer <key>.',
                'Cek hasil pengiriman di menu "Log Pesan".',
            ]],
            ['phase' => 'Fase 5 · Integrasi & Otomasi', 'steps' => [
                'Set webhook_url pada device untuk menerima pesan masuk (inbound).',
                'Gunakan kuota & rate-limit per paket untuk blast / notifikasi transaksional.',
                'Untuk skala besar pakai backend Cloud API (Meta) — tanpa risiko ban.',
            ]],
        ];

        return view('docs.index', compact('demoAccounts', 'endpoints', 'tutorial', 'baseUrl'));
    }
}
