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

## [1.2.0] - 2026-07-13

### Ditambahkan
- **Upload Gambar via URL** — Setiap modul (Slider, Gallery, Brand, Client, Testimonial) kini mendukung upload gambar melalui URL internet sebagai alternatif upload file
- `Upload::handleFromUrl()` — Method baru di `Upload.php` untuk mendownload gambar dari URL, dengan validasi keamanan berlapis (MIME type via finfo, dimensi max 4000px, ukuran max 5MB, getimagesize(), path traversal protection, filename acak, thumbnail generation)
- **Field input URL** — Setiap halaman create/edit admin panel kini memiliki input URL gambar di samping upload file (image_url, logo_url, screenshot_url)

### Diubah
- **Fresh data reset** — Semua data database dan file upload dihapus sepenuhnya; migrasi dijalankan ulang dengan data awal (default values, akun admin, contoh slider, paket harga, testimoni)

### Detail Teknis
- `Upload::handleFromUrl()` menggunakan `file_get_contents()` dengan stream context timeout 15s, user-agent 'BayuCCTV/1.0', follow redirects
- Temp file di `sys_get_temp_dir()` di-unlink pada setiap jalur error sebelum return
- Copy + unlink untuk cross-filesystem safety (bukan move_uploaded_file yang khusus upload form)
- Prioritas: URL lebih diutamakan daripada file upload jika keduanya diisi
- Semua 5 controller (Slider, Gallery, Brand, Client, Testimonial) diubah di store() dan update()
- 10 view files diubah (create + edit untuk masing-masing modul)

## [1.3.0] - 2026-07-13

### Ditambahkan
- **Admin Panel Full-Width** — `.admin-content` kini full-width tanpa batas `max-width: 1200px`; padding responsif menggunakan `clamp()` menyesuaikan lebar layar
- **Responsive Breakpoints Komprehensif** — 6 breakpoint: 576px (HP kecil), 768px (tablet), 991px (desktop kecil), 1400px (layar besar), 1920px (ultrawide)
- **Tabel Scroll Horizontal di Mobile** — Semua tabel admin bisa discroll horizontal di layar <768px
- **Sidebar Overlay Mobile** — Sidebar toggle menggunakan overlay di mobile, support class `.show` dan `.open`

### Diperbaiki
- **UI Admin terlalu kecil di layar besar** — Dulu terkekang `max-width: 1200px`, sekarang memenuhi layar
- **FOR UPDATE SQLite incompatible** — Diganti dengan `BEGIN IMMEDIATE` untuk atomic rate limiting
- **base_url() skip IP address** — Sekarang mendeteksi IP address di HTTP_HOST, bukan cuma domain name
- **CSRF token mismatch di Docker** — Session file tidak writable karena entrypoint tidak chown `storage/sessions/`
- **Sidebar toggle tidak konsisten** — CSS pakai class `.show`, JS pakai `.open`; sekarang keduanya didukung

### Detail Teknis
- CSS: `max-width: 1200px` → `max-width: 100%` + `clamp(1rem, 3vw, 3rem)` padding
- Breakpoint 576px: stat cards stack 1 per row, quick actions full-width, tombol full-width
- Breakpoint 768px-991px: sidebar overlay, tabel scroll, admin cards overflow handling
- Breakpoint 1400px-1920px: padding dan font lebih besar untuk layar lebar
- Auth.php: `FOR UPDATE` dihapus, `$db->exec('BEGIN IMMEDIATE')` untuk SQLite
- bootstrap/app.php: `base_url()` tambah `filter_var($hostPart, FILTER_VALIDATE_IP)`
- Dockerfile: Entrypoint chown `storage/sessions/` di runtime untuk volume mount


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