# WebWA — WhatsApp Gateway SaaS

WebWA adalah **gateway WhatsApp multi-user** berbasis Laravel yang dibangun di atas package
[`kstmostofa/laravel-whatsapp`](https://github.com/kstmostofa/laravel-whatsapp) (dual-backend:
**Meta Cloud API** + **whatsapp-web.js sidecar**).

Setiap user dapat menghubungkan banyak device WhatsApp (scan QR atau Cloud API), membuat API key,
dan mengirim pesan via **REST API** atau dashboard. Lengkap dengan log pengiriman, panel admin,
landing marketing, blog, dokumentasi API, dan halaman pSEO.

---

## Stack

- Laravel 13 + PHP 8.3 + MySQL 8
- Blade + Livewire 4 + Alpine.js + Tailwind (CDN)
- Package: `kstmostofa/laravel-whatsapp` (Cloud API + Node sidecar)
- Auth: session (web) + API key (REST)

---

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# set DB_* di .env (default: webwa @ 127.0.0.1, root, no password)
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8787
```

Buka `http://127.0.0.1:8787`.

### Akun Demo

| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@webwa.test  | password |
| User  | user@webwa.test   | password |
| Demo  | demo@webwa.test   | password |

---

## Mengaktifkan pengiriman WhatsApp (Web sidecar)

Pengiriman via nomor pribadi membutuhkan Node sidecar (`whatsapp-web.js`). Tanpa sidecar,
aplikasi tetap berjalan — pengiriman hanya ditandai `failed` (graceful), QR menampilkan status offline.

```bash
# sekali saja — clone whatsapp-web.js, npm ci, download Chromium (~600MB)
php artisan whatsapp:sidecar:install

# jalankan sidecar (background)
php artisan whatsapp:sidecar:start

# (produksi) jembatan event sidecar -> Laravel, jalankan di Supervisor
php artisan whatsapp:web:listen main
```

> **Windows:** command `whatsapp:sidecar:install` / `:start` memakai `which`/`nohup`/`posix`
> (Unix-only) sehingga **tidak jalan di Windows**. Gunakan cara manual:
>
> ```powershell
> # 1. install dependency Node sidecar (sekali saja)
> cd whatsapp-sidecar
> npm install --omit=dev
> npx puppeteer browsers install chrome
> cd ..
>
> # 2. jalankan sidecar (biarkan window ini terbuka)
> powershell -ExecutionPolicy Bypass -File scripts\start-sidecar.ps1
> ```
>
> Sidecar listen di `http://127.0.0.1:3000`. Token-nya dibaca dari `WHATSAPP_WEB_TOKEN` di `.env`.

Lalu di dashboard: **Device → Tambah Device (backend Web) → Hubungkan → scan QR**.

### Cloud API (Meta resmi)

Isi di `.env`:

```
WHATSAPP_ACCESS_TOKEN=EAAG...
WHATSAPP_PHONE_NUMBER_ID=...
WHATSAPP_BUSINESS_ACCOUNT_ID=...
WHATSAPP_APP_SECRET=...
WHATSAPP_VERIFY_TOKEN=...
```

Webhook Meta: `POST {APP_URL}/webhooks/whatsapp`.

---

## REST API

Autentikasi: `Authorization: Bearer <api_key>` (atau `X-Api-Key`). Buat key di menu **API Key**.

```bash
# Kirim teks
curl -X POST {APP_URL}/api/v1/messages/text \
  -H "Authorization: Bearer wwa_xxx.yyy" \
  -H "Content-Type: application/json" \
  -d '{"device":"1","to":"628123456789","message":"Halo!"}'

# Kirim media
curl -X POST {APP_URL}/api/v1/messages/media \
  -H "Authorization: Bearer wwa_xxx.yyy" \
  -d '{"device":"1","to":"628123456789","type":"image","url":"https://x/y.jpg","caption":"Hi"}'

# Info akun / device / status / QR
curl {APP_URL}/api/v1/me            -H "Authorization: Bearer wwa_xxx.yyy"
curl {APP_URL}/api/v1/devices       -H "Authorization: Bearer wwa_xxx.yyy"
curl {APP_URL}/api/v1/devices/1/status -H "Authorization: Bearer wwa_xxx.yyy"
curl {APP_URL}/api/v1/devices/1/qr     -H "Authorization: Bearer wwa_xxx.yyy"
```

Dokumentasi lengkap: `{APP_URL}/docs`.

---

## Struktur

| Area              | Path                                   |
|-------------------|----------------------------------------|
| Landing marketing | `/`                                    |
| Harga             | `/harga`                               |
| Dokumentasi API   | `/docs`                                |
| Blog              | `/blog`                                |
| pSEO              | `/whatsapp-gateway-{kota}`, `/whatsapp-gateway-untuk-{industri}`, `/best-whatsapp-gateway`, `/alternatif-{x}`, `/bandingkan/{a}-vs-{b}` |
| Dashboard user    | `/dashboard`, `/devices`, `/send`, `/api-keys`, `/logs` |
| Admin             | `/admin`, `/admin/users`, `/admin/devices`, `/admin/logs` |
| Panel WhatsApp    | `/whatsapp` (UI bawaan package, admin-only) |
| Sitemap / robots  | `/sitemap.xml`, `/robots.txt`          |

---

## SEO

- `sitemap.xml` digenerate dinamis (static + blog + pSEO).
- Submit ke Google Search Console & Bing Webmaster Tools.
- pSEO + blog memberi konten segar untuk organic traffic.

### IndexNow (Bing, Yandex, Seznam, Naver)

- Key file: `public/indexnow-key.txt`.
- Submit manual: `php artisan seo:indexnow --all` (semua) / `--new` (yang baru) / `--url=...`.
- Scheduler harian 02:45 (`--new`). Aktifkan dengan `php artisan schedule:work` atau cron.
- Blog post yang dipublish otomatis di-ping ke IndexNow.

## Screenshot marketing

Screenshot asli aplikasi (dipakai di landing & `/docs`) di-generate dengan Playwright:

```bash
npm install --save-dev playwright   # sekali saja
npx playwright install chromium
php artisan serve --host=127.0.0.1 --port=8787   # window terpisah
node scripts/screenshot.cjs          # desktop -> public/marketing/screens/
node scripts/screenshot-mobile.cjs   # mobile  -> public/marketing/screens-mobile/
```

## License v3 (whitelabel.co.id pairing)

Source code dilindungi pairing kit License v3 (RSA + AES-256-GCM). Saat di-deploy ke
domain produksi, app redirect ke `/__pair` sampai buyer memasukkan activation key dari
marketplace. Halaman marketing/SEO publik (`/`, `/docs`, `/blog`, pSEO, `/sitemap.xml`)
tetap bisa diakses tanpa pairing.

- `.env`: `LICENSE_DEV_BYPASS=true` (local), `false` (production) + `APP_ENV=production`.
- Di local (`127.0.0.1`/`.test`) pairing otomatis di-bypass.

---

## Catatan keamanan produksi

- Set `WHATSAPP_WEB_TOKEN` (shared secret PHP ↔ sidecar) yang kuat.
- Set `WHATSAPP_APP_SECRET` untuk verifikasi HMAC webhook Cloud.
- Jalankan `whatsapp:web:listen` di Supervisor/systemd.
- `/whatsapp` sudah dilindungi middleware `['web','auth','admin']`.
