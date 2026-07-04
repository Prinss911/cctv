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
- **Brand Logo CRUD** — Kelola logo brand/klien korporat dengan upload file, tampil di tab pricing halaman utama
- **RBAC Authorization** — Tiga level akses: admin, editor, viewer, dicek via middleware di setiap route admin
- **Security Event Logging** — Semua event keamanan tercatat di `storage/logs/security.log` dengan detail terstruktur
- **Dynamic Base URL** — Otomatis deteksi protokol, host, dan port, support proxy/CDN (Cloudflare, nginx)
- **Docker Support** — Production-ready Docker image dengan PHP 8.2 + Apache + SQLite3, entrypoint auto-migration
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
| Container Runtime | Docker & Docker Compose |
| Web Server (dev) | PHP Built-in Server / Docker |
| Web Server (prod) | Apache (mod_rewrite) / Nginx / Docker |

---

## Persyaratan Sistem

- PHP 8.0 atau lebih baru
- Ekstensi PHP: `pdo`, `pdo_sqlite`, `fileinfo`, `mbstring`
- Apache dengan `mod_rewrite` diaktifkan (atau Nginx dengan konfigurasi setara)
- Ekstensi `sqlite3` (sudah termasuk di PHP secara default)
- Untuk MySQL: ekstensi `pdo_mysql`
- Untuk PostgreSQL: ekstensi `pdo_pgsql`
- **Docker:** Docker Engine 20+ & Docker Compose (untuk containerized deployment)

---

## Instalasi

### Opsi A — Docker (Direkomendasikan)

Jalankan aplikasi dalam container tanpa perlu setup PHP/SQLite di host:

```bash
# 1. Clone repository
git clone https://github.com/Prinss911/cctv.git
cd cctv

# 2. Salin file konfigurasi
cp .env.example .env

# 3. Build dan jalankan container
docker compose up -d --build

# 4. Buka di browser
# Halaman Utama: http://localhost:8081
# Admin Panel:  http://localhost:8081/admin
```

Container akan secara otomatis:
- Menjalankan migrasi database saat pertama kali start (`php database/migrate.php --seed`)
- Mount folder `storage/` dan `public/uploads/` untuk persistensi data
- Menggunakan konfigurasi dari file `.env` (read-only mount)

Untuk menghentikan container:
```bash
docker compose down
```

### Opsi B — Manual (PHP Native)

### 1. Clone Repository

```bash
git clone https://github.com/Prinss911/cctv.git
cd cctv
```

### 2. Salin File Konfigurasi

```bash
cp .env.example .env
```

### 3. Konfigurasi File `.env`

Buka `.env` dan sesuaikan nilainya:

```env
APP_NAME="Bayu CCTV"
APP_URL=http://localhost:8081
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
php -S 0.0.0.0:8081 -t public
```

> **Catatan:** Port 8081 digunakan agar kompatibel dengan Docker dan menghindari konflik. Untuk akses dari perangkat lain di jaringan yang sama, gunakan IP lokal host (contoh: `http://192.168.x.x:8081`).

### 6. Buka di Browser

- **Halaman Utama:** [http://localhost:8081](http://localhost:8081)
- **Admin Panel:** [http://localhost:8081/admin](http://localhost:8081/admin)

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
|   |   |   |-- BrandController.php     # CRUD logo brand/klien
|   |   |   |-- DashboardController.php
|   |   |   |-- GalleryController.php
|   |   |   |-- PricingController.php
|   |   |   |-- SettingsController.php
|   |   |   |-- SliderController.php
|   |   |   |-- TestimonialController.php
|   |   |   `-- ClientController.php
|   |   `-- HomeController.php  # Controller halaman publik
|   |-- Helpers/                # Class helper / utility
|   |   |-- Auth.php            # Autentikasi & rate limiting
|   |   |-- Csrf.php            # Token CSRF
|   |   |-- Flash.php           # Pesan flash satu-kali
|   |   |-- Router.php          # HTTP router
|   |   |-- Upload.php          # Upload & validasi file
|   |   `-- View.php            # Render template & layout
|   |-- Middleware/
|   |   |-- AuthMiddleware.php      # Cek sesi login
|   |   |-- CsrfMiddleware.php      # Verifikasi token CSRF
|   |   |-- IdleTimeoutMiddleware.php # Auto-logout 30 menit inaktif
|   |   `-- RbacMiddleware.php       # Kontrol akses berbasis role
|   `-- Models/
|       |-- Database.php        # Singleton koneksi PDO
|       |-- BaseModel.php       # CRUD abstrak
|       |-- UserModel.php
|       |-- SettingModel.php
|       |-- SliderModel.php
|       |-- PricingModel.php
|       |-- PricingBrandModel.php  # Brand logo untuk tab pricing
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
|   |   |-- 007_create_clients.php
|   |   `-- 008_create_pricing_brands.php
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
|           |-- brand/             # CRUD logo brand/klien
|           |   |-- create.php
|           |   |-- edit.php
|           |   `-- index.php
|           |-- dashboard.php
|           |-- gallery/
|           |-- login.php
|           |-- pricing/
|           |-- settings/
|           |-- sliders/
|           `-- testimonials/
|
|-- routes/
|   |-- web.php                 # Rute halaman publik
|   `-- admin.php               # Rute admin panel
|
|-- storage/
|   |-- db/
|   |   `-- app.db              # File database SQLite
|   |-- cache/                  # (Reservasi untuk cache di masa depan)
|   `-- logs/                   # Security event log & error log
|
|-- .dockerignore               # File yang diabaikan Docker build
|-- .env                        # Konfigurasi environment (tidak di-commit)
|-- .env.example                # Template konfigurasi environment
|-- .gitignore
|-- Dockerfile                  # Production Docker image (PHP 8.2 + Apache)
`-- docker-compose.yml          # Container orchestration
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

### Brand / Client Logo

Kelola logo brand/klien korporat yang tampil di tab navigasi bagian pricing halaman utama. Modul terpisah dari "Client / Mitra" (yang tampil di bagian logo klien footer).
- Nama brand dan file logo (upload)
- Logo disimpan di `public/uploads/logo/` dengan format JPEG/PNG/WebP/GIF, max 5MB
- Tampil sebagai tab filter di bagian pricing halaman utama
- Setiap brand bisa dikaitkan ke paket harga tertentu

### Client / Mitra

Tampilkan logo-logo klien yang muncul di bagian "Klien Kami" halaman utama (footer/client section). Setiap item memiliki nama, logo, dan opsional link ke website.

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

### Docker Deployment

Deploy dengan Docker di VPS/server mana pun yang memiliki Docker Engine:

```bash
# Clone repository
git clone https://github.com/Prinss911/cctv.git /opt/bayu-cctv
cd /opt/bayu-cctv

# Salin dan sesuaikan konfigurasi
cp .env.example .env
# Edit .env: set APP_URL, APP_ENV=production, APP_DEBUG=false

# Build dan jalankan
docker compose up -d --build
```

Aplikasi akan berjalan di port **8081**. Untuk menggunakan port 80, edit `docker-compose.yml`:
```yaml
ports:
  - "80:80"   # ganti 8081:80 jadi 80:80
```

Atau gunakan reverse proxy (Nginx) di host:
```nginx
server {
    listen 80;
    server_name domain-anda.com;

    location / {
        proxy_pass http://127.0.0.1:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Data persisten (database SQLite + uploads) tersimpan di `./storage/` dan `./public/uploads/` — aman selama volume mount tidak dihapus.

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
   git clone https://github.com/Prinss911/cctv.git /var/www/bayu-cctv
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
| Banned Extension Check | Upload menolak file dengan ekstensi berbahaya (`.php`, `.pht`, `.phtml`, `.php4`, `.php5`, `.phar`, `.htaccess`, `.htpasswd`, `.shtml`, `.inc`) |
| CSRF Protection | Token acak 64-char hex per sesi, dirotasi setelah POST halaman penuh; AJAX request dilindungi via fallback validasi token sebelumnya |
| Filename Sanitization | Nama file diacak dengan `uniqid() + time()`, ekstensi diambil dari MIME type |
| Idle Timeout | Session otomatis logout setelah 30 menit tidak ada aktivitas, dengan flash message notifikasi |
| Image Content Integrity | Validasi gambar via `getimagesize()` selain MIME type check; dimensi maksimal 4000x4000 piksel |
| Password Hashing | `password_hash()` dengan algoritma bcrypt, cost factor 12 |
| Path Traversal Protection | `Upload::sanitizePath()` memfilter null bytes, `../`, `./`, absolute paths; hanya alphanumeric/underscore/hyphen/dot diizinkan; path final diverifikasi via realpath() |
| Rate Limiting | 5 gagal login → lock 15 menit; counter disimpan di tabel `auth_attempts` dengan SQLite `EXCLUSIVE` transaction untuk atomic increment; tracking per IP dan per username |
| RBAC Authorization | Tiga level role: `admin` (penuh), `editor` (CRUD konten), `viewer` (read-only). Dicek via `RbacMiddleware` di setiap route admin |
| Security Event Logging | Semua event keamanan dicatat ke `storage/logs/security.log`: login gagal/berhasil, upload, akses tidak sah, CSRF violation, path traversal, role violation |
| Security Headers | `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`, `Content-Security-Policy` dikirim di setiap response |
| Session Security | `session_regenerate_id(true)` dipanggil saat login berhasil; session fixation prevention |
| SQL Injection | Seluruh query menggunakan PDO prepared statements |
| Storage Protection | `storage/.htaccess` memblokir akses langsung ke file di folder storage |
| Thumbnail Generation | Upload gambar otomatis menghasilkan thumbnail 400/800/1200/1600 px via GD library |
| Upload Validation | Validasi MIME type via `finfo` (bukan ekstensi), batas ukuran 5MB, MIME-extension consistency check |
| XSS Output Escaping | Fungsi `e()` (`htmlspecialchars`) digunakan di seluruh view; JSON-LD di-escape dengan flags JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_HEX_QUOT |

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
