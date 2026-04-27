# 🚀 Panduan Instalasi DigiRepo

Selamat datang di **DigiRepo**, Sistem Repositori Digital Modern. Aplikasi ini telah dilengkapi dengan **Web Installer** untuk memudahkan proses instalasi tanpa perlu menyentuh baris kode atau terminal.

---

## 📋 Persyaratan Sistem
Sebelum memulai, pastikan perangkat Anda memenuhi syarat berikut:
- **PHP** >= 8.2
- **MySQL / MariaDB** (Tersedia di XAMPP, Laragon, atau aaPanel)
- **Ekstensi PHP**: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`.

---

## 💻 Instalasi di Windows (XAMPP)

1.  **Salin Folder**: 
    Ekstrak dan letakkan folder `digirepo_laravel` ke dalam direktori `C:\xampp\htdocs\`.
2.  **Jalankan XAMPP**: 
    Aktifkan modul **Apache** dan **MySQL** pada XAMPP Control Panel.
3.  **Akses Browser**: 
    Buka browser Anda dan akses: `http://localhost/digirepo_laravel/`
4.  **Ikuti Wizard**: 
    Sistem akan otomatis menampilkan halaman instalasi. Isi detail database (biasanya user: `root` dan password kosong) serta buat akun Admin Anda.
5.  **Selesai**: 
    Setelah proses selesai, Anda akan diarahkan ke halaman login.

---

## 🐧 Instalasi di Linux (aaPanel / VPS)

1.  **Unggah File**: 
    Unggah folder project ke direktori web Anda (misal: `/www/wwwroot/repo.anda.com`).
2.  **Set Izin Folder (Permissions)**:
    Pastikan folder `storage` dan `bootstrap/cache` memiliki izin tulis (writeable).
    ```bash
    chmod -R 775 storage bootstrap/cache
    chown -R www:www .
    ```
3.  **Konfigurasi Nginx**:
    Pastikan *Document Root* Anda mengarah ke folder utama project. Karena sistem sudah memiliki `index.php` di root, Anda tidak harus mengarahkannya ke `/public`.
4.  **Akses Domain**:
    Buka domain Anda di browser, dan ikuti langkah **Web Installer** yang muncul.

---

## 🛠️ Troubleshooting (Masalah Umum)

### 1. Database Gagal Terhubung
- Pastikan MySQL dalam keadaan menyala.
- Jika Anda menggunakan password di database, pastikan password yang diinput di Wizard sudah benar.
- Jika sistem tidak bisa membuat database otomatis, silakan buat database kosong secara manual di phpMyAdmin dengan nama `digirepo_laravel` lalu ulangi proses install.

### 2. Muncul Error 500
- Pastikan versi PHP Anda minimal 8.2.
- Cek log error di `storage/logs/laravel.log` untuk detail masalah.

### 3. Folder Vendor Hilang
- Pastikan saat menyalin/mengunggah file, folder `vendor` ikut terbawa. Jika Anda mengunduh dari Git tanpa folder vendor, Anda harus menjalankan `composer install` terlebih dahulu.

---

## 🔒 Keamanan Setelah Install
Setelah instalasi berhasil, sistem akan membuat file pengunci di `storage/installed`. Selama file ini ada, halaman instalasi tidak akan bisa diakses kembali oleh siapapun untuk mencegah perusakan data.

---
**DigiRepo** - *Digital Literacy Ecosystem*
