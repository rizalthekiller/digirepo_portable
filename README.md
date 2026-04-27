# DigiRepo - Digital Repository System

DigiRepo adalah Sistem Repositori Digital Perpustakaan Modern berbasis Laravel yang dirancang untuk mengelola karya ilmiah (Skripsi, Thesis, Disertasi, dll) secara efisien dengan fitur watermarking PDF otomatis, sistem sertifikat dinamis, dan pencarian cepat.

## 🚀 Fitur Utama
- **Penelusuran Publik**: Pencarian karya ilmiah dengan filter Fakultas, Tahun, dan Tipe.
- **Watermarking Otomatis**: Penambahan watermark pada file PDF saat diunduh.
- **Sistem Sertifikat**: Pembuatan sertifikat otomatis dengan QR Code validasi.
- **Queue Management**: Pengolahan background job untuk pengiriman email dan PDF.
- **UI Premium**: Desain modern menggunakan Inter & Outfit fonts dengan efek Glassmorphism.

---

## 🛠 Panduan Instalasi (Development)

### 1. Persiapan Awal
Pastikan Anda sudah menginstal PHP >= 8.1, Composer, dan MySQL.

```bash
# Clone repository
git clone https://github.com/rizalthekiller/digirepo_laravel.git
cd digirepo_laravel

# Install dependensi
composer install
npm install && npm run build
```

### 2. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database serta email Anda.

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database & Storage
```bash
# Jalankan migrasi dan seeder
php artisan migrate --seed

# Hubungkan folder storage
php artisan storage:link
```

---

## 🌐 Konfigurasi Produksi (aaPanel / Nginx)

### 1. Nginx Configuration
Gunakan konfigurasi berikut pada server block Anda untuk mendukung Laravel dan pemrosesan PDF yang stabil.

```nginx
server {
    listen 80;
    server_name repo.pustakauinsi.my.id; # Sesuaikan dengan domain Anda
    root /www/wwwroot/digirepo_laravel/public;
    index index.php index.html;

    # Optimasi Upload File Besar
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-81.sock; # Sesuaikan versi PHP
        fastcgi_index index.php;
        include fastcgi.conf;
        
        # Optimasi Timeout untuk Proses PDF
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    location ~ /\.(not allowed|git|env) {
        deny all;
    }
}
```

### 2. Supervisor Configuration (Queue)
Penting untuk menjalankan worker agar pengiriman email dan pembuatan sertifikat berjalan di background. Buat file `/etc/supervisor/conf.d/digirepo-worker.conf`:

```ini
[program:digirepo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /www/wwwroot/digirepo_laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/digirepo_laravel/storage/logs/worker.log
stopwaitsecs=3600
```

Setelah itu, jalankan perintah:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start digirepo-worker:*
```

---

## 📂 Struktur Direktori Penting
- `app/Services/WatermarkGenerator.php`: Logika utama pembuatan sertifikat & watermark.
- `resources/views/admin/certificates/print.blade.php`: Template master sertifikat (Source of Truth).
- `app/Http/Controllers/ThesisController.php`: Manajemen streaming PDF & keamanan dokumen.

---

## 📝 Catatan Penting
- **DPI PDF**: Sistem ini menggunakan DPI 96 untuk menjaga konsistensi ukuran 1:1 antara tampilan browser dan hasil PDF.
- **Queue Restart**: Setiap kali Anda melakukan update kode di server, pastikan menjalankan `php artisan queue:restart` agar perubahan terbaca oleh Supervisor.

---
Dikembangkan oleh **Antigravity AI** untuk **DigiRepo Project**.
