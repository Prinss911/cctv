# Bayu CCTV - Website Company Profile

Website company profile profesional untuk jasa pemasangan CCTV, dibangun dengan PHP murni tanpa framework. Dilengkapi CMS admin panel untuk mengelola konten secara dinamis.

---

## Fitur

- **Halaman Utama Dinamis** — Hero slider, paket harga, galeri, testimoni, dan logo klien semuanya dikelola lewat admin panel
- **Dark / Light Mode** — Toggle tema yang tersimpan di `localStorage`, tidak ada flash saat halaman dimuat ulang
- **Responsive Design** — Tampilan optimal di semua ukuran layar menggunakan Bootstrap 5.3
- **CMS Admin Panel** — Kelola semua konten tanpa menyentuh kode: slider, paket harga, galeri, testimoni, klien, dan pengaturan situs
- **Drag-and-drop Reorder** — Urutkan item di setiap modul dengan SortableJS
- **WhatsApp Integration** — Tombol WhatsApp mengambang dengan pesan greeting yang dapat dikonfigurasi; setiap paket harga memiliki pesan WhatsApp tersendiri
- **SQLite Database** — Zero-konfigurasi, file database disimpan di `storage/db/app.db`; mendukung migrasi ke MySQL atau PostgreSQL
- **Upload Gambar** — Validasi MIME type, batas ukuran 5MB, nama file diacak untuk keamanan
- **SEO Ready** — Meta title, meta description, Open Graph image, dan favicon semua dapat dikonfigurasi dari admin panel
- **Keamanan Bawaan** — CSRF protection, bcrypt password hashing, rate limiting login, prepared statements, security headers
- **Sistem Migrasi** — Skrip migrasi dengan dukungan `--fresh` dan `--seed`

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Bahasa | PHP 8.0+ |
| CSS Framework | Bootstrap 5.3.3 |
| Database | SQLite (via PDO), mendukung MySQL & PostgreSQL |
| Icon | Font Awesome 6.5.1 |
| Drag-and-drop | SortableJS |
| Tema | Bootstrap `data-bs-theme` + `localStorage` |
| Web Server (dev) | PHP Built-in Server |
| Web Server (prod) | Apache (mod_rewrite) / Nginx |

---

## Persyaratan Sistem

- PHP 8.0 atau lebih baru
- Ekstensi PHP: `pdo`, `pdo_sqlite`, `fileinfo`, `mbstring`
- Apache dengan `mod_rewrite` diaktifkan (atau Nginx dengan konfigurasi setara)
- Ekstensi `sqlite3` (sudah termasuk di PHP secara default)
- Untuk MySQL: ekstensi `pdo_mysql`
- Untuk PostgreSQL: ekstensi `pdo_pgsql`

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/bayu-cctv.git
cd bayu-cctv
```

### 2. Salin File Konfigurasi

```bash
cp .env.example .env
```

### 3. Konfigurasi File `.env`

Buka `.env` dan sesuaikan nilainya:

```env
APP_NAME="Bayu CCTV"
APP_URL=http://localhost:8000
APP_ENV=development
APP_DEBUG=true

DB_DRIVER=sqlite
# Untuk MySQL, ganti nilai di atas menjadi:
# DB_DRIVER=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_NAME=cctv_db
# DB_USER=root
# DB_PASS=password_anda
```

### 4. Jalankan Migrasi dan Seeder

```bash
php database/migrate.php --seed
```

Perintah ini akan membuat semua tabel dan mengisi data awal (termasuk akun admin, contoh slider, paket harga, dan testimoni).

### 5. Jalankan Development Server

```bash
php -S localhost:8000 -t public
```

### 6. Buka di Browser

- **Halaman Utama:** [http://localhost:8000](http://localhost:8000)
- **Admin Panel:** [http://localhost:8000/admin](http://localhost:8000/admin)

---

## Login Admin

| Field | Nilai Default |
|---|---|
| Email | `admin@bayucctv.com` |
| Password | `admin123` |

> **PENTING:** Segera ganti password default setelah pertama kali login, terutama di lingkungan production. Gunakan menu Pengaturan di admin panel untuk menggantinya.

---

## Struktur Folder

```
bayu-cctv/
|
|-- app/                        # Kode inti aplikasi
|   |-- Controllers/            # Controller halaman
|   |   |-- Admin/              # Controller khusus admin panel
|   |   |   |-- AuthController.php
|   |   |   |-- DashboardController.php
|   |   |   |-- SliderController.php
|   |   |   |-- GalleryController.php
|   |   |   |-- PricingController.php
|   |   |   |-- TestimonialController.php
|   |   |   |-- ClientController.php
|   |   |   `-- SettingsController.php
|   |   `-- HomeController.php  # Controller halaman publik
|   |-- Helpers/                # Class helper / utility
|   |   |-- Auth.php            # Autentikasi & rate limiting
|   |   |-- Csrf.php            # Token CSRF
|   |   |-- Flash.php           # Pesan flash satu-kali
|   |   |-- Router.php          # HTTP router
|   |   |-- Upload.php          # Upload & validasi file
|   |   `-- View.php            # Render template & layout
|   |-- Middleware/
|   |   |-- AuthMiddleware.php  # Cek sesi login
|   |   `-- CsrfMiddleware.php  # Verifikasi token CSRF
|   `-- Models/
|       |-- Database.php        # Singleton koneksi PDO
|       |-- BaseModel.php       # CRUD abstrak
|       |-- UserModel.php
|       |-- SettingModel.php
|       |-- SliderModel.php
|       |-- PricingModel.php
|       |-- GalleryModel.php
|       |-- TestimonialModel.php
|       `-- ClientModel.php
|
|-- bootstrap/
|   |-- app.php                 # Inisialisasi aplikasi, helper global, security headers
|   `-- autoload.php            # PSR-4 autoloader manual
|
|-- config/
|   |-- app.php                 # Konfigurasi nama app, URL, timezone
|   |-- database.php            # Konfigurasi driver & kredensial database
|   `-- storage.php             # Konfigurasi upload (path, tipe, ukuran)
|
|-- database/
|   |-- migrate.php             # Skrip runner migrasi (CLI)
|   |-- migrations/             # File migrasi per tabel
|   |   |-- 001_create_users.php
|   |   |-- 002_create_settings.php
|   |   |-- 003_create_sliders.php
|   |   |-- 004_create_pricing.php
|   |   |-- 005_create_gallery.php
|   |   |-- 006_create_testimonials.php
|   |   `-- 007_create_clients.php
|   `-- seeds/
|       `-- DatabaseSeeder.php  # Data awal untuk development
|
|-- docs/
|   `-- ARCHITECTURE.md         # Dokumentasi teknis arsitektur
|
|-- public/                     # Document root web server
|   |-- index.php               # Entry point semua request
|   |-- .htaccess               # Rewrite rules Apache
|   |-- assets/
|   |   |-- css/
|   |   |   |-- app.css         # CSS halaman publik
|   |   |   `-- admin.css       # CSS admin panel
|   |   `-- js/
|   |       |-- app.js          # JS halaman publik (dark mode toggle, dll)
|   |       `-- admin.js        # JS admin panel (SortableJS, konfirmasi hapus)
|   `-- uploads/                # File gambar yang diupload
|       |-- sliders/
|       |-- gallery/
|       |-- testimonials/
|       |-- clients/
|       `-- logo/
|
|-- resources/
|   `-- views/
|       |-- layouts/
|       |   |-- main.php        # Layout halaman publik
|       |   `-- admin.php       # Layout admin panel
|       |-- partials/
|       |   |-- nav.php         # Navigasi + dark mode toggle
|       |   |-- footer.php      # Footer halaman publik
|       |   `-- whatsapp-widget.php  # Tombol WhatsApp mengambang
|       |-- home/
|       |   `-- index.php       # Halaman utama
|       `-- admin/              # View per modul admin
|           |-- login.php
|           |-- dashboard.php
|           |-- sliders/
|           |-- gallery/
|           |-- pricing/
|           |-- testimonials/
|           |-- clients/
|           `-- settings/
|
|-- routes/
|   |-- web.php                 # Rute halaman publik
|   `-- admin.php               # Rute admin panel
|
|-- storage/
|   |-- db/
|   |   `-- app.db              # File database SQLite
|   |-- cache/                  # (Reservasi untuk cache di masa depan)
|   `-- logs/                   # (Reservasi untuk log di masa depan)
|
|-- .env                        # Konfigurasi environment (tidak di-commit)
|-- .env.example                # Template konfigurasi environment
`-- .gitignore
```

---

## Panduan Admin Panel

Akses admin panel di `/admin`. Semua modul memiliki operasi CRUD lengkap (tambah, edit, hapus) dan drag-and-drop untuk mengatur urutan tampil.

### Slider (Hero Banner)

Kelola gambar dan teks di bagian hero halaman utama. Setiap slider memiliki:
- Judul dan subjudul
- Gambar (upload)
- Teks dan URL tombol CTA
- Toggle aktif/nonaktif

### Paket Harga

Kelola paket-paket CCTV yang ditampilkan di halaman utama. Fitur per paket:
- Nama paket, jumlah kamera, harga normal, harga coret
- Deskripsi singkat
- Daftar fitur (dinamis, bisa tambah/hapus)
- Pesan WhatsApp otomatis saat tombol diklik
- Toggle "featured" untuk paket yang ingin ditonjolkan

### Galeri

Upload foto-foto hasil pemasangan CCTV. Setiap item memiliki caption dan dapat dinonaktifkan tanpa dihapus.

### Testimoni

Kelola ulasan pelanggan. Setiap testimoni memiliki nama pelanggan, isi ulasan, dan rating bintang (1-5).

### Client / Mitra

Tampilkan logo-logo brand atau klien korporat. Setiap item memiliki nama, logo, dan opsional link ke website.

### Pengaturan

Konfigurasi seluruh aspek situs dalam satu halaman, dikelompokkan ke dalam tiga tab:
- **Umum:** Nama situs, tagline, logo, favicon, teks about, statistik (tahun pengalaman, total klien, total kota), footer copyright
- **Kontak:** Nomor WhatsApp, nomor telepon, email, alamat, Google Maps embed, pesan greeting WhatsApp
- **SEO:** Meta title, meta description, Open Graph image

---

## Dark Mode

Dark mode menggunakan mekanisme Bootstrap 5.3 (`data-bs-theme="dark"` pada elemen `<html>`). Preferensi pengguna disimpan di `localStorage` dengan key `cctv_theme`.

Untuk mencegah "flash of wrong theme" saat halaman dimuat, terdapat skrip inline kecil di bagian `<head>` yang membaca `localStorage` dan langsung menerapkan tema sebelum browser merender konten:

```html
<script>
    (function(){
        var t = localStorage.getItem('cctv_theme') || 'light';
        document.getElementById('html-root').setAttribute('data-bs-theme', t);
    })();
</script>
```

Toggle dark/light mode tersedia di navbar dan dikelola oleh `public/assets/js/app.js`.

---

## Database

### Cara Kerja

Database diakses melalui `App\Models\Database` — sebuah singleton PDO. Koneksi hanya dibuat satu kali per request. Seluruh query menggunakan prepared statements untuk mencegah SQL injection.

Untuk SQLite, dua PRAGMA diaktifkan otomatis:
- `journal_mode = WAL` — meningkatkan performa concurrent reads
- `foreign_keys = ON` — menegakkan integritas relasi

### Perintah Migrasi

```bash
# Jalankan migrasi yang belum dijalankan
php database/migrate.php

# Reset semua tabel lalu jalankan ulang dari awal
php database/migrate.php --fresh

# Jalankan migrasi lalu isi data awal
php database/migrate.php --seed

# Reset total + migrasi ulang + data awal
php database/migrate.php --fresh --seed
```

### Beralih dari SQLite ke MySQL

1. Buat database MySQL:
   ```sql
   CREATE DATABASE cctv_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Edit file `.env`:
   ```env
   DB_DRIVER=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=cctv_db
   DB_USER=root
   DB_PASS=password_anda
   ```

3. Jalankan migrasi:
   ```bash
   php database/migrate.php --seed
   ```

### Beralih ke PostgreSQL

1. Buat database:
   ```sql
   CREATE DATABASE cctv_db;
   ```

2. Edit `.env`:
   ```env
   DB_DRIVER=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_NAME=cctv_db
   DB_USER=postgres
   DB_PASS=password_anda
   ```

3. Jalankan migrasi:
   ```bash
   php database/migrate.php --seed
   ```

> **Catatan:** File migrasi menggunakan sintaks SQL yang kompatibel dengan SQLite. Saat migrasi ke MySQL/PostgreSQL, periksa tipe data di folder `database/migrations/` dan sesuaikan jika diperlukan (misalnya `INTEGER PRIMARY KEY AUTOINCREMENT` menjadi `INT AUTO_INCREMENT PRIMARY KEY` di MySQL).

---

## Deployment

### Shared Hosting (cPanel)

1. Upload seluruh file ke server (misalnya ke `/home/username/bayu-cctv/`)
2. Di cPanel, arahkan document root domain ke folder `public/`:
   - Masuk ke **Domains** > **Domains** > Edit
   - Set **Document Root** ke `bayu-cctv/public`
3. Set permission file:
   ```bash
   chmod -R 755 public/uploads/
   chmod -R 755 storage/
   chmod 644 .env
   ```
4. Edit `.env` untuk konfigurasi production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.com
   ```
5. Jalankan migrasi via SSH atau buat skrip PHP sementara
6. Pastikan folder `storage/` tidak dapat diakses dari browser (sudah ada `storage/.htaccess`)

### VPS / Server Mandiri

1. Clone repository:
   ```bash
   git clone https://github.com/username/bayu-cctv.git /var/www/bayu-cctv
   ```

2. Konfigurasi virtual host Nginx atau Apache untuk mengarah ke `/var/www/bayu-cctv/public`

   **Contoh konfigurasi Nginx:**
   ```nginx
   server {
       listen 80;
       server_name domain-anda.com;
       root /var/www/bayu-cctv/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

3. Set ownership dan permission:
   ```bash
   chown -R www-data:www-data /var/www/bayu-cctv
   chmod -R 755 /var/www/bayu-cctv/public/uploads
   chmod -R 755 /var/www/bayu-cctv/storage
   ```

4. Salin dan konfigurasi `.env`:
   ```bash
   cp .env.example .env
   nano .env
   ```

5. Jalankan migrasi:
   ```bash
   php database/migrate.php --seed
   ```

---

## Keamanan

Fitur keamanan yang sudah dibangun ke dalam aplikasi:

| Fitur | Implementasi |
|---|---|
| CSRF Protection | Token acak 64-char hex per sesi, dirotasi setelah setiap POST request |
| Password Hashing | `password_hash()` dengan algoritma bcrypt, cost factor 12 |
| Rate Limiting | Akun terkunci 15 menit setelah 5 kali gagal login |
| SQL Injection | Seluruh query menggunakan PDO prepared statements |
| Upload Validation | Validasi MIME type via `finfo` (bukan ekstensi), batas ukuran 5MB |
| Filename Sanitization | Nama file diacak dengan `uniqid() + time()`, ekstensi diambil dari MIME type |
| Security Headers | `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy` dikirim di setiap response |
| XSS Output Escaping | Fungsi `e()` (`htmlspecialchars`) digunakan di seluruh view |
| Session Security | `session_regenerate_id(true)` dipanggil saat login berhasil |
| Storage Protection | `storage/.htaccess` memblokir akses langsung ke file di folder storage |

---

## Customization

### Menambah Seksi Baru di Halaman Utama

1. Tambahkan data di `app/Controllers/HomeController.php` (ambil dari model jika perlu)
2. Tambahkan HTML seksi di `resources/views/home/index.php`
3. Tambahkan CSS di `public/assets/css/app.css`

### Mengubah Warna Tema

Edit variabel CSS di `public/assets/css/app.css`. Warna utama mengikuti variabel Bootstrap sehingga perubahan otomatis mendukung dark mode.

### Menambah Modul Admin Baru

Ikuti langkah-langkah berikut (lihat `docs/ARCHITECTURE.md` untuk panduan lengkap):

1. Buat file migrasi di `database/migrations/`
2. Buat model di `app/Models/`
3. Buat controller di `app/Controllers/Admin/`
4. Buat view di `resources/views/admin/nama-modul/`
5. Daftarkan route di `routes/admin.php`

### Mengubah Nomor WhatsApp

Masuk ke **Admin Panel** > **Pengaturan** > tab **Kontak** > ubah field **Nomor WhatsApp**. Format: kode negara + nomor tanpa tanda `+` atau spasi (contoh: `6281234567890`).

---

## Lisensi

MIT License. Bebas digunakan, dimodifikasi, dan didistribusikan untuk keperluan pribadi maupun komersial.
