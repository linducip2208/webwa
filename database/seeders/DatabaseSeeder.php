<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Device;
use App\Models\MessageLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@webwa.test'], [
            'name' => 'Admin WebWA',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'plan' => 'enterprise',
            'device_limit' => 999,
            'monthly_quota' => 9999999,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'user@webwa.test'], [
            'name' => 'Budi Santoso',
            'company' => 'PT Maju Jaya',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan' => 'growth',
            'device_limit' => 5,
            'monthly_quota' => 50000,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $demo = User::updateOrCreate(['email' => 'demo@webwa.test'], [
            'name' => 'Demo Toko Online',
            'company' => 'TokoKu',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan' => 'growth',
            'device_limit' => 5,
            'monthly_quota' => 50000,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->seedDemoDevices($demo);
        $this->seedBlog($admin);
    }

    protected function seedDemoDevices(User $demo): void
    {
        $waDevice = Device::updateOrCreate(
            ['session_name' => 'u'.$demo->id.'_demo_main'],
            [
                'user_id' => $demo->id,
                'name' => 'CS Utama',
                'backend' => 'web',
                'status' => 'ready',
                'phone' => '6281234567890',
                'push_name' => 'TokoKu CS',
                'last_connected_at' => now()->subHours(2),
            ]
        );

        Device::updateOrCreate(
            ['session_name' => 'u'.$demo->id.'_demo_cloud'],
            [
                'user_id' => $demo->id,
                'name' => 'Notifikasi Cloud',
                'backend' => 'cloud',
                'status' => 'disconnected',
            ]
        );

        if ($demo->apiKeys()->count() === 0) {
            ApiKey::generate($demo, 'Server Produksi');
        }

        if ($demo->messageLogs()->count() === 0) {
            $statuses = ['sent', 'delivered', 'read', 'sent', 'failed', 'read'];
            $samples = [
                ['text', 'Pesanan #INV-2041 telah dikonfirmasi. Terima kasih!'],
                ['text', 'Kode OTP Anda: 884213. Berlaku 5 menit.'],
                ['text', 'Paket Anda sedang dalam pengiriman 🚚'],
                ['image', 'Bukti pembayaran diterima ✅'],
                ['text', 'Promo spesial hari ini: diskon 20% untuk semua produk!'],
                ['text', 'Pengingat: tagihan jatuh tempo besok.'],
                ['document', 'Invoice terlampir.'],
                ['text', 'Terima kasih telah berbelanja di TokoKu 🙏'],
            ];

            for ($i = 0; $i < 28; $i++) {
                $s = $samples[$i % count($samples)];
                MessageLog::create([
                    'user_id' => $demo->id,
                    'device_id' => $waDevice->id,
                    'direction' => 'outbound',
                    'backend' => 'web',
                    'to_number' => '62812'.rand(10000000, 99999999),
                    'type' => $s[0],
                    'body' => $s[1],
                    'status' => $statuses[$i % count($statuses)],
                    'source' => ['api', 'dashboard', 'api', 'bulk'][$i % 4],
                    'wa_message_id' => 'wamid.'.Str::random(20),
                    'created_at' => now()->subDays(rand(0, 6))->subMinutes(rand(0, 1440)),
                ]);
            }
        }
    }

    protected function seedBlog(User $author): void
    {
        if (BlogPost::count() > 0) {
            return;
        }

        $catTips = BlogCategory::create(['name' => 'Tips & Tutorial', 'slug' => 'tips-tutorial', 'description' => 'Panduan praktis otomasi WhatsApp.']);
        $catApi = BlogCategory::create(['name' => 'API & Integrasi', 'slug' => 'api-integrasi', 'description' => 'Integrasi WhatsApp gateway ke sistem Anda.']);
        $catBisnis = BlogCategory::create(['name' => 'Bisnis', 'slug' => 'bisnis', 'description' => 'Strategi WhatsApp untuk pertumbuhan bisnis.']);

        $posts = [
            ['Cara Mengirim Notifikasi WhatsApp Otomatis dengan REST API', $catApi->id, 'Pelajari cara mengirim notifikasi WhatsApp otomatis dari aplikasi Anda menggunakan REST API WebWA hanya dengan beberapa baris kode.'],
            ['Panduan Lengkap Pairing WhatsApp via QR Code', $catTips->id, 'Langkah demi langkah menghubungkan nomor WhatsApp Anda ke gateway menggunakan pemindaian QR code.'],
            ['Web vs Cloud API: Mana Backend WhatsApp yang Tepat?', $catApi->id, 'Bandingkan kelebihan dan kekurangan backend whatsapp-web.js dan Cloud API Meta untuk kebutuhan bisnis Anda.'],
            ['5 Cara Meningkatkan Konversi Toko Online dengan WhatsApp', $catBisnis->id, 'Strategi memanfaatkan notifikasi WhatsApp untuk mengurangi keranjang terbengkalai dan meningkatkan repeat order.'],
            ['Mengirim OTP via WhatsApp: Lebih Murah & Cepat dari SMS', $catTips->id, 'Mengapa OTP via WhatsApp menjadi pilihan favorit, dan cara mengimplementasikannya dengan WebWA.'],
            ['Membangun Webhook untuk Membalas Pesan WhatsApp Otomatis', $catApi->id, 'Tutorial membuat auto-reply WhatsApp menggunakan fitur webhook WebWA.'],
            ['Tips Menghindari Pemblokiran Nomor WhatsApp Saat Blast', $catBisnis->id, 'Praktik terbaik agar nomor WhatsApp Anda tetap aman saat mengirim pesan dalam jumlah besar.'],
            ['Integrasi WhatsApp Gateway dengan Laravel dalam 10 Menit', $catApi->id, 'Contoh kode lengkap mengintegrasikan WebWA ke aplikasi Laravel Anda.'],
        ];

        foreach ($posts as $i => [$title, $catId, $excerpt]) {
            BlogPost::create([
                'category_id' => $catId,
                'author_id' => $author->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $excerpt,
                'content' => $this->articleBody($title, $excerpt),
                'meta_description' => $excerpt,
                'is_published' => true,
                'published_at' => now()->subDays(($i + 1) * 3),
                'views' => rand(120, 3400),
            ]);
        }
    }

    protected function articleBody(string $title, string $excerpt): string
    {
        return <<<HTML
<p>{$excerpt}</p>
<p>WhatsApp telah menjadi kanal komunikasi utama bagi jutaan bisnis di Indonesia. Dengan tingkat keterbacaan pesan yang jauh lebih tinggi dibanding email maupun SMS, mengotomatiskan pengiriman WhatsApp adalah langkah strategis untuk meningkatkan engagement pelanggan.</p>
<h2>Mengapa otomasi WhatsApp penting?</h2>
<p>Mengirim pesan secara manual memakan waktu, rawan kesalahan, dan tidak terukur. Dengan gateway seperti <strong>WebWA</strong>, Anda dapat mengirim ribuan pesan melalui satu endpoint REST API, lengkap dengan log status pengiriman.</p>
<h2>Langkah implementasi</h2>
<p>Mulai dengan mendaftar akun, menghubungkan device WhatsApp melalui QR atau Cloud API, lalu membuat API key. Setelah itu, integrasikan endpoint pengiriman ke sistem Anda. Seluruh proses dapat selesai dalam hitungan menit.</p>
<h2>Kesimpulan</h2>
<p>Otomasi WhatsApp bukan lagi kemewahan, melainkan kebutuhan. WebWA menyediakan fondasi yang andal, fleksibel, dan mudah diintegrasikan — cocok untuk bisnis skala kecil hingga enterprise. Coba gratis hari ini dan rasakan perbedaannya.</p>
HTML;
    }
}
