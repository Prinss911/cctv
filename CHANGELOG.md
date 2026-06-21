# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/id/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/lang/id/).

## [Unreleased]

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