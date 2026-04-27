# Panduan Deployment & Optimasi Digirepo (aaPanel + Nginx)

Dokumen ini berisi panduan teknis khusus untuk mendeploy aplikasi **Digirepo** di server dengan **aaPanel**, menggunakan domain `repo.pustakauinsi.my.id`, port `2126`, dan **PHP 8.3**.

---

## 1. Persiapan Server di aaPanel

1.  **Instal PHP 8.3**: Pastikan PHP 8.3 sudah terinstal melalui App Store aaPanel.
2.  **Extensions PHP**: Pasang ekstensi berikut di menu PHP 8.3:
    *   `fileinfo` (Wajib untuk validasi PDF)
    *   `opcache` (Untuk kecepatan)
    *   `gd` / `imagemagick` (Untuk pengolahan gambar)
    *   `intl`
3.  **Hapus Disabled Functions**: Di menu PHP 8.3 -> **Disabled functions**, hapus fungsi berikut agar Laravel & QPDF bisa berjalan:
    *   `putenv`, `exec`, `proc_open`, `shell_exec`.

---

## 2. Konfigurasi Nginx (Site Config)

Masuk ke menu **Website** -> **Config**, lalu gunakan pengaturan berikut:

```nginx
server {
    listen 2126;
    server_name repo.pustakauinsi.my.id;
    index index.php index.html index.htm;
    root /www/wwwroot/repo.pustakauinsi.my.id/public;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Penanganan PHP 8.3 aaPanel
    location ~ [^/]\.php(/|$) {
        try_files $uri =404;
        fastcgi_pass unix:/tmp/php-cgi-83.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
        fastcgi_read_timeout 300;
    }

    # Caching Aset Statis
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|woff|woff2|ttf|svg)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, no-transform";
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    access_log /www/wwwlogs/repo.pustakauinsi.my.id.log;
    error_log /www/wwwlogs/repo.pustakauinsi.my.id.error.log;
}
```

---

## 3. Konfigurasi `.env` Produksi

Pastikan file `.env` di server sudah disesuaikan:

```env
APP_NAME="DigiRepo UINSI"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://repo.pustakauinsi.my.id:2126

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_anda
DB_USERNAME=user_db_anda
DB_PASSWORD="password_db_anda"

FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

---

## 4. Langkah Optimasi (Terminal)

Jalankan perintah ini di folder root proyek melalui terminal:

1.  **Instalasi Dependensi**:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
2.  **Caching Laravel**:
    ```bash
    php artisan optimize
    php artisan view:cache
    ```
3.  **Storage Link**:
    ```bash
    php artisan storage:link
    ```
4.  **Migrasi Database**:
    ```bash
    php artisan migrate --force
    ```

---

## 5. Background Jobs (Queue & Schedule)

Agar pengiriman sertifikat dan jadwal otomatis berjalan:

1.  **Cron Job aaPanel**:
    Tambahkan cron job tipe `Shell Script` setiap menit:
    ```bash
    php /www/wwwroot/repo.pustakauinsi.my.id/artisan schedule:run >> /dev/null 2>&1
    ```
2.  **Supervisor (Queue Worker)**:
    Instal **Supervisor Manager** dari App Store aaPanel, lalu tambahkan program:
    *   Command: `php /www/wwwroot/repo.pustakauinsi.my.id/artisan queue:work --sleep=3 --tries=3`
    *   User: `www`

---

## 6. QPDF (Linux)

Untuk pengolahan PDF di server Linux, jalankan perintah ini di terminal server:
```bash
sudo apt-get update
sudo apt-get install qpdf -y
```
Pastikan path binary di aplikasi merujuk ke `/usr/bin/qpdf` (lokasi standar Linux).
