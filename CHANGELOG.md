# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/id/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/lang/id/).

## [1.1.1] - 2026-07-03

### Diperbaiki
- **Dynamic base_url** — Helper `asset()`, `upload_url()`, `url()` sekarang otomatis mendeteksi protokol, host, dan port dari request (`$_SERVER['REQUEST_SCHEME']`, `HTTP_HOST`, `SERVER_PORT`), support proxy/CDN di belakang reverse proxy (Cloudflare, nginx, dll)
- **IPv4 binding** — PHP development server bind ke `0.0.0.0:8081` agar bisa diakses dari container/network eksternal

### Ditambahkan
- **Dockerfile** — Production-ready Docker image berbasis `php:8.2-apache` dengan Apache mod_rewrite, SQLite3, entrypoint untuk first-run auto-migration
- **docker-compose.yml** — Service definition port `8081:80`, volume mount untuk persistent storage dan uploads, environment production default
- **libsqlite3-dev** — Dependency SQLite untuk environment Docker

### Detail Teknis
- Dockerfile: multi-stage entrypoint script, auto-detect first start & run `migrate.php --seed`
- docker-compose: bridge network, restart policy `unless-stopped`, bind mount `.env` read-only
- Dynamic URL: support `HTTP_X_FORWARDED_HOST`, `HTTPS`, `SERVER_PORT` auto-detection

## [Unreleased]

### Ditambahkan
- **Sistem Otorisasi RBAC** — Implementasi kontrol akses berbasis peran dengan 3 level (admin, editor, viewer), `RbacMiddleware`, dan helper `Auth::hasRole()`
- **Idle Timeout Middleware** — Auto-logout otomatis setelah 30 menit tidak ada aktivitas, konfigurasi `IDLE_TIMEOUT=1800` di `config/app.php`, lengkap dengan notifikasi flash message
- **Security Event Logger** — `App\Helpers\SecurityLogger` untuk mencatat aktivitas keamanan ke `storage/logs/security.log`, mencakup event login, upload, akses ilegal, CSRF, path traversal, dan perubahan role
- **Proteksi Path Traversal** — `Upload::sanitizePath()` untuk memfilter null bytes, `../`, `./`, dan absolute paths, serta membatasi karakter hanya `[\w\-\.]`
- **Pengecekan Ekstensi Terlarang** — Menolak upload file dengan ekstensi berbahaya seperti `.php`, `.pht`, `.phtml`, `.phar`, `.htaccess`, dll.
- **Integritas Konten Gambar** — Verifikasi menggunakan `getimagesize()` untuk memastikan file benar-benar gambar melampaui pengecekan MIME type
- **Batas Dimensi Gambar** — Pembatasan maksimum dimensi gambar 4000x4000 px
- **Generasi Thumbnail** — Auto-generasi thumbnail dengan ukuran 400, 800, 1200, dan 1600 px menggunakan GD library
- **Konsistensi MIME-Ekstensi** — Validasi agar ekstensi file yang diupload sesuai dengan tipe MIME yang terdeteksi
- **Metode BaseModel::delete()** — Penambahan metode hapus standar pada base model
- **Route Logout Admin** — Penambahan route logout admin pada `GET /admin/logout`

### Diperbaiki
- **Race Condition Rate Limiting** — Migrasi penyimpanan dari `$_SESSION` ke tabel `auth_attempts` dengan `EXCLUSIVE` SQLite transaction lock
- **Rotasi Token CSRF untuk AJAX** — Token hanya dirotasi pada POST halaman penuh, dengan validasi fallback ke token sebelumnya untuk request XHR
- **Type Error Flash::success()** — Perbaikan pada controller yang memanggil metode `Flash::success()` yang sebelumnya tidak terdefinisi
- **Kompatibilitas PHP 8.0** — Penambahan polyfill untuk `str_starts_with`

### Diubah
- **Penyimpanan Rate Limiting** — Dari berbasis sesi menjadi tabel database `auth_attempts` dengan pelacakan IP dan username
- **Kebijakan Rotasi Token CSRF** — Dari rotasi setiap POST menjadi rotasi hanya pada POST halaman penuh
- **Validasi Path Upload::delete()** — Penambahan pengecekan `sanitizePath()` sebelum proses penghapusan file

### Detail Teknis
- **SecurityLogger**: Implementasi metode logging terstruktur untuk audit trail keamanan
- **RBAC Flow**: Integrasi middleware untuk pengecekan role sebelum akses controller
- **Idle Timeout**: Implementasi timestamp aktivitas terakhir di sesi dan pengecekan middleware
- **Upload Validation**: Layer validasi berlapis (MIME -> Extension -> Content -> Dimensions -> Path Sanitization)

### Ditambahkan
- **Upload Logo Brand CRUD** — Dukungan upload file penuh untuk logo brand di admin panel
- `BrandController::store()` — Menangani upload file via `Upload::handle()` dengan validasi (5MB, JPEG/PNG/WebP/GIF)
- `BrandController::update()` — Menangani upload logo baru + menghapus logo lama via `Upload::delete()`
- `BrandController::destroy()` — Menghapus file logo sebelum brand dihapus
- `resources/views/admin/brand/create.php` — Input file dengan tipe accept, form enctype multipart/form-data
- `resources/views/admin/brand/edit.php` — Preview logo saat ini + input file untuk penggantian
- **User admin kedua** — `admin2@bayucctv.com` / `admin` ditambahkan via seeder

### Diperbaiki
- **Konektivitas Cloudflare tunnel** — Memperbaiki binding PHP server ke IPv4 (`0.0.0.0:8000`) bukan IPv6-only (`::1:8000`), agar cloudflared bisa konek dengan benar
- **Generasi URL Asset/Upload untuk tunnel** — Update helper di `bootstrap/app.php` (`asset()`, `upload_url()`, `url()`) mendeteksi `HTTP_X_FORWARDED_HOST` untuk generate URL HTTPS yang benar di belakang Cloudflare tunnel
- **Perluasan layout gambar Hero/Slider** — Menambahkan batasan CSS max-height:
  - `#heroCarousel`, `.carousel-inner` — `max-height: 90vh` / `max-height: 900px` (desktop), `700px` (mobile)
  - `.hero-slide` — `max-height: 90vh` / `900px` desktop, `700px` mobile, `object-fit: cover`
- **Overflow logo header (navbar)** — `.nav-brand img` dibatasi `max-height: 36px`, `width: auto`, `object-fit: contain`
- **Overflow logo footer** — `.site-footer img[height="30"]`, `.footer-brand img` dibatasi `max-height: 30px`
- **Overflow logo tab brand** — `.brand-tabs .nav-link img` dibatasi `max-height: 24px`, `max-width: 80px`, `object-fit: contain`
- **URL tampil logo brand** — Memperbaiki daftar brand admin (`index.php`) dan tab brand halaman utama (`index.php`) menggunakan path `/uploads/` yang benar (sebelumnya salah pakai `/uploads/logo/`)

### Diubah
- **Path storage logo brand** — Logo sekarang disimpan sebagai `logo/filename.ext` di database, ditampilkan via `url('/uploads/' . $brand['logo'])`
- **Command PHP development server** — Diubah dari `php -S localhost:8000 -t public` ke `php -S 0.0.0.0:8000 -t public` untuk binding IPv4 yang benar

### Detail Teknis
- Validasi upload: max 5MB, tipe MIME yang diizinkan (image/jpeg, image/png, image/webp, image/gif) via `finfo` di `Upload::handle()`
- Penamaan file: `uniqid()_timestamp.ext` untuk keamanan
- Lokasi storage: `public/uploads/logo/`
- URL tunnel: Acak setiap restart (Quick Tunnel), contoh: `https://trademarks-coordinated-pet-superior.trycloudflare.com`


## [1.0.0] - 2024-XX-XX
### Ditambahkan
- Rilis awal: Website company profile Bayu CCTV
- Halaman utama dinamis (hero slider, paket harga, galeri, testimoni, logo klien)
- Mode Dark/Light dengan persistensi localStorage
- Admin panel dengan CRUD untuk semua modul konten
- Drag-and-drop reordering (SortableJS)
- Integrasi WhatsApp
- Database SQLite dengan sistem migrasi
- Upload gambar dengan validasi MIME
- Konfigurasi SEO
- Fitur keamanan (CSRF, bcrypt, rate limiting, security headers)