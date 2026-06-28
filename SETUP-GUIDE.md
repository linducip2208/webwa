# WebWA — Setup Guide (Server Baru)

Panduan install WebWA WhatsApp Gateway di server Linux fresh.

## Requirements

| Software | Version |
|----------|---------|
| PHP | 8.3+ |
| MySQL | 8.0+ |
| Node.js | v18+ |
| Web Server | Apache/Nginx |
| Supervisor | latest |
| Git | latest |
| Composer | 2.x |

## 1. Clone Project

```bash
cd /www/wwwroot
git clone https://github.com/linducip2208/webwa.git webwa.whitelabel.co.id
cd webwa.whitelabel.co.id
```

## 2. Set Default PHP ke 8.3

```bash
# Cek PHP yang tersedia
ls /usr/bin/php* | grep php8

# Symlink ke PHP 8.3
ln -sf /www/server/php/83/bin/php /usr/bin/php
ln -sf /www/server/php/83/bin/phpize /usr/bin/phpize
ln -sf /www/server/php/83/bin/php-fpm /usr/bin/php-fpm

# Verifikasi
php -v   # harus PHP 8.3.x
```

## 3. Install Dependency & Setup

```bash
# Composer
composer install

# Node (build asset frontend)
npm install
npm run build

# Generate APP_KEY
cp .env.example .env
php artisan key:generate
```

## 4. Edit .env

```bash
nano .env
```

Pastikan isi yang ini:
```
APP_NAME=WebWA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webwa
DB_USERNAME=webwa
DB_PASSWORD=password-anda

LICENSE_DEV_BYPASS=true
LICENSE_SERVER_URL=https://whitelabel.co.id

WHATSAPP_WEB_ENABLED=true
WHATSAPP_WEB_HOST=127.0.0.1
WHATSAPP_WEB_PORT=3000
WHATSAPP_WEB_TOKEN=<random-32-char-string>
```

## 5. Setup Database

```bash
# Buat database di MySQL
mysql -u root -p -e "CREATE DATABASE webwa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'webwa'@'127.0.0.1' IDENTIFIED BY 'password-anda';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON webwa.* TO 'webwa'@'127.0.0.1'; FLUSH PRIVILEGES;"

# Migrasi + seed data demo
php artisan migrate --seed
```

## 6. Set DocumentRoot Web Server

### Apache (aapanel)

Edit `/www/server/panel/vhost/apache/domain-anda.conf`:

```apache
DocumentRoot "/www/wwwroot/webwa.whitelabel.co.id/public"

<Directory "/www/wwwroot/webwa.whitelabel.co.id/public">
    SetOutputFilter DEFLATE
    Options FollowSymLinks
    AllowOverride All
    Require all granted
    DirectoryIndex index.php index.html
</Directory>
```

Restart Apache:
```bash
service httpd reload
```

### Nginx (aapanel)

Edit `/www/server/panel/vhost/nginx/domain-anda.conf`:

```nginx
root /www/wwwroot/webwa.whitelabel.co.id/public;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Restart Nginx:
```bash
service nginx reload
```

## 7. Install & Start WhatsApp Sidecar

```bash
# Install dependency Node sidecar + download Chromium
php artisan whatsapp:sidecar:install

# Buat folder sessions
mkdir -p vendor/kstmostofa/laravel-whatsapp/sidecar/sessions
chown -R www:www vendor/kstmostofa/laravel-whatsapp/sidecar/sessions
```

## 8. Setup Supervisor (sidecar daemon)

Buat file `/etc/supervisor/conf.d/webwa-sidecar.conf`:

```ini
[program:webwa-sidecar]
process_name=%(program_name)s_%(process_num)02d
command=node /www/wwwroot/webwa.whitelabel.co.id/vendor/kstmostofa/laravel-whatsapp/sidecar/index.js
directory=/www/wwwroot/webwa.whitelabel.co.id/vendor/kstmostofa/laravel-whatsapp/sidecar
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www
numprocs=1
redirect_stderr=true
stdout_logfile=/www/wwwroot/webwa.whitelabel.co.id/storage/logs/supervisor-sidecar.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=3
stopwaitsecs=10
environment=PORT="3000",HOST="127.0.0.1",SIDECAR_TOKEN="<token-dari-.env>",APP_ENV="production"
```

Reload Supervisor:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start webwa-sidecar:*
```

## 9. Setup Cron (Scheduler Laravel)

```bash
crontab -e
```

Tambah baris:
```
* * * * * /usr/bin/php /www/wwwroot/webwa.whitelabel.co.id/artisan schedule:run >> /dev/null 2>&1
```

## 10. Verifikasi

```bash
# Cek sidecar
php artisan whatsapp:sidecar:status
# → Installed: yes, Reachable: yes

# Cek supervisor
supervisorctl status webwa-sidecar:*

# Buka browser
curl -sI https://domain-anda.com
# → HTTP/1.1 200 OK
```

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@webwa.test | password |
| User | user@webwa.test | password |
| Demo | demo@webwa.test | password |

## Troubleshooting

**403 Forbidden** → DocumentRoot web server belum ke `/public`  
**500 Error** → APP_KEY belum di-generate, jalankan `php artisan key:generate`  
**Sidecar Offline** → Supervisor belum dikonfigurasi atau sessions folder permission salah  
**Tidak bisa login** → Database belum di-seed, jalankan `php artisan db:seed`  
**PHP version error** → Default PHP bukan 8.3, symlink ke `/usr/bin/php83`
