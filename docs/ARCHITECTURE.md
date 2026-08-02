# Arsitektur Aplikasi - Bayu CCTV

Dokumen ini ditujukan untuk developer yang akan memelihara atau mengembangkan proyek ini lebih lanjut.

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan arsitektur **MVC-like** (Model-View-Controller) yang dibangun dari nol tanpa framework. Tidak ada Composer, tidak ada dependency eksternal — semuanya adalah kode PHP murni.

```
┌─────────────┐     ┌──────────────────┐     ┌────────────────┐
│   Browser   │────▶│  public/index.php │────▶│ bootstrap/     │
│   Request   │     │  (Entry Point)    │     │ app.php        │
└─────────────┘     └──────────────────┘     └────────┬───────┘
                                                       │
                    ┌──────────────────────────────────▼───────┐
                    │              Router::dispatch()           │
                    │         (cocokkan method + URI)           │
                    └──────────────────────────────────┬────────┘
                                                       │
                    ┌──────────────────────────────────▼───────┐
                     │              Middleware Chain               │
│   AuthMiddleware                         │
│   → CsrfMiddleware (validasi POST)       │
                    └──────────────────────────────────┬────────┘
                                                       │
               ┌───────────────────────────────────────▼───────┐
               │                  Controller                     │
               │        panggil Model, siapkan data             │
               └──────────┬──────────────────────────────────────┘
                          │                    │
              ┌───────────▼──────┐  ┌──────────▼────────┐
              │      Model       │  │       View         │
              │  (query PDO)     │  │  (output buffer)   │
              └───────────┬──────┘  └──────────┬─────────┘
                          │                    │
              ┌───────────▼──────┐  ┌──────────▼─────────┐
              │    Database      │  │      Layout         │
              │  (PDO Singleton) │  │  (wrap $content)   │
              └──────────────────┘  └────────────┬────────┘
                                                  │
                                    ┌─────────────▼──────┐
                                    │  HTTP Response      │
                                    │  (HTML ke browser)  │
                                    └────────────────────┘
```

---

## Request Lifecycle

Berikut alur lengkap sebuah request dari browser hingga response:

1. **Browser** mengirim HTTP request ke domain/server
2. **Apache/Nginx** menerima request; `.htaccess` atau konfigurasi server mengarahkan semua request ke `public/index.php`
3. **`public/index.php`** memanggil `bootstrap/app.php`:
   - Mendefinisikan konstanta `BASE_PATH`, `VIEWS_PATH`, `PUBLIC_PATH`
   - Memuat variabel dari file `.env` ke `$_ENV`
   - Mendaftarkan fungsi helper global (`env()`, `e()`, `asset()`, `url()`, `redirect()`, `setting()`, `format_rupiah()`)
   - Menjalankan `bootstrap/autoload.php` (mendaftarkan PSR-4 autoloader manual)
   - Memuat `config/app.php` dan men-set timezone
   - Memulai sesi PHP
   - Mengirim security headers (`X-Frame-Options`, `X-Content-Type-Options`, dll.)
   - Menginisialisasi koneksi database via `Database::getInstance()`
4. **`public/index.php`** membuat instance `Router`, memuat `routes/web.php` dan `routes/admin.php`
5. **`Router::dispatch()`** menerima `$_SERVER['REQUEST_METHOD']` dan `$_SERVER['REQUEST_URI']`, mencocokkan dengan daftar route yang terdaftar
6. **Middleware** dijalankan di dalam constructor controller (admin) dalam urutan berikut: `AuthMiddleware::handle()` (cek login) → `IdleTimeoutMiddleware::handle()` (cek aktivitas) → `RbacMiddleware::handle()` (cek role) → `CsrfMiddleware::handle()` (cek token POST)
7. **Controller method** dipanggil dengan parameter URL yang sudah di-extract (misalnya `{id}`)
8. **Model** digunakan oleh controller untuk query database via PDO
9. **`View::render()`** menjalankan template view dengan output buffering, menangkap hasilnya ke `$content`, lalu menyertakan file layout yang membungkus `$content`
10. **Response HTML** dikirim ke browser

---

## Routing

### Cara Kerja Router

`App\Helpers\Router` adalah router HTTP sederhana yang mendukung method `GET` dan `POST`. Route didaftarkan dengan method `get()` atau `post()`.

```php
// Contoh pendaftaran route
$router->get('/admin/sliders/{id}/edit', 'Admin\SliderController@edit');
$router->post('/admin/sliders/{id}', 'Admin\SliderController@update');
```

**Pencocokan parameter dinamis** dilakukan dengan regex `\{(\w+)\}`. Segmen URL yang cocok dikumpulkan ke array `$params` dan diteruskan sebagai argumen ke method controller:

```
Route:  /admin/sliders/{id}/edit
URI:    /admin/sliders/42/edit
Hasil:  ['id' => '42']
```

Router kemudian memanggil controller dengan `call_user_func_array([$controller, $method], $params)`.

**Perilaku 404:** Jika tidak ada route yang cocok, router mengirim HTTP 404 dan menampilkan pesan sederhana.

### File Route

| File | Isi |
|---|---|
| `routes/web.php` | Route halaman publik (saat ini hanya `GET /`) |
| `routes/admin.php` | Seluruh route admin panel (`/admin/*`) |

### Format Handler

```
'NamaController@namaMethod'
'Admin\NamaController@namaMethod'   // untuk controller di subfolder Admin/
```

Router otomatis meng-prefix namespace `App\Controllers\`, sehingga `Admin\SliderController` menjadi `App\Controllers\Admin\SliderController`.

---

## Controllers

### Konvensi

- Namespace: `App\Controllers` (publik) atau `App\Controllers\Admin` (admin)
- Lokasi file: `app/Controllers/` atau `app/Controllers/Admin/`
- Nama file mengikuti nama class: `SliderController.php`
- Setiap method controller bertanggung jawab atas satu aksi (index, create, store, edit, update, destroy, reorder)

### Admin Controllers

Semua admin controller memanggil middleware di constructor:

**Middleware yang tersedia (urutan eksekusi per request):**

| Middleware | File | Fungsi |
|---|---|---|
| `AuthMiddleware` | `app/Middleware/AuthMiddleware.php` | Memastikan pengguna sudah login. Cek `$_SESSION['user_id']`, jika tidak ada redirect ke `/admin/login` |
| `CsrfMiddleware` | `app/Middleware/CsrfMiddleware.php` | Verifikasi token CSRF pada POST request. Token dirotasi hanya untuk full page POST (bukan AJAX). Fallback validasi ke token sebelumnya untuk kompatibilitas AJAX |

### Pola CRUD Standar

| Method | Route | Aksi |
|---|---|---|
| `index()` | `GET /admin/resource` | Tampilkan daftar semua data |
| `create()` | `GET /admin/resource/create` | Tampilkan form tambah |
| `store()` | `POST /admin/resource` | Simpan data baru |
| `edit($id)` | `GET /admin/resource/{id}/edit` | Tampilkan form edit |
| `update($id)` | `POST /admin/resource/{id}` | Perbarui data |
| `destroy($id)` | `POST /admin/resource/{id}/delete` | Hapus data |
| `reorder()` | `POST /admin/resource/reorder` | Simpan urutan baru |

| Method | Route | Aksi |
|---|---|---|
| `index()` | `GET /admin/about-features` | Tampilkan daftar semua fitur Tentang Kami |
| `create()` | `GET /admin/about-features/create` | Tampilkan form tambah fitur Tentang Kami |
| `store()` | `POST /admin/about-features` | Simpan fitur Tentang Kami baru |
| `edit($id)` | `GET /admin/about-features/{id}/edit` | Tampilkan form edit fitur Tentang Kami |
| `update($id)` | `POST /admin/about-features/{id}` | Perbarui fitur Tentang Kami |
| `destroy($id)` | `POST /admin/about-features/{id}/delete` | Hapus fitur Tentang Kami |
| `reorder()` | `POST /admin/about-features/reorder` | Simpan urutan baru fitur Tentang Kami |

| Method | Route | Aksi |
|---|---|---|
| `index()` | `GET /admin/faqs` | Tampilkan daftar semua FAQ |
| `create()` | `GET /admin/faqs/create` | Tampilkan form tambah FAQ |
| `store()` | `POST /admin/faqs` | Simpan FAQ baru |
| `edit($id)` | `GET /admin/faqs/{id}/edit` | Tampilkan form edit FAQ |
| `update($id)` | `POST /admin/faqs/{id}` | Perbarui FAQ |
| `destroy($id)` | `POST /admin/faqs/{id}/delete` | Hapus FAQ |
| `reorder()` | `POST /admin/faqs/reorder` | Simpan urutan baru FAQ |

> Metode DELETE dan PATCH tidak digunakan karena keterbatasan form HTML. Semua aksi modifikasi data menggunakan POST.

---

## Models

### BaseModel

`App\Models\BaseModel` adalah class abstrak yang menyediakan operasi CRUD standar. Setiap model concrete hanya perlu mendefinisikan `$table` dan jika diperlukan `$primaryKey`.

```php
abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
```

**Method yang tersedia dari BaseModel:**

| Method | Deskripsi |
|---|---|
| `getAll(string $orderBy)` | Ambil semua baris, diurutkan |
| `getActive(string $orderBy)` | Ambil baris dengan `is_active = 1` |
| `find(int $id)` | Ambil satu baris berdasarkan primary key |
| `create(array $data)` | Insert baris baru, otomatis isi `created_at` dan `updated_at`, kembalikan ID |
| `update(int $id, array $data)` | Update baris, otomatis perbarui `updated_at` |
| `delete(int $id)` | Hapus baris berdasarkan primary key |
| `count()` | Hitung total baris |
| `updateSortOrder(array $ids)` | Update kolom `sort_order` berdasarkan array ID yang diurutkan |

### Membuat Model Baru

```php
<?php

namespace App\Models;

class ArticleModel extends BaseModel
{
    protected string $table = 'articles';

    // Tambahkan method khusus di sini
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
```

---

## Views & Layouts

### View::render()

`App\Helpers\View::render()` bekerja dengan dua tahap menggunakan output buffering:

```php
public static function render(string $template, array $data = [], string $layout = 'main'): void
{
    extract($data);          // jadikan array key sebagai variabel lokal

    ob_start();
    include $templatePath;   // jalankan template, tangkap output
    $content = ob_get_clean();

    include $layoutPath;     // layout meng-echo $content di posisi yang tepat
}
```

**Memanggil dari controller:**

```php
View::render('home/index', compact('sliders', 'packages'), 'main');
// Template: resources/views/home/index.php
// Layout:   resources/views/layouts/main.php
```

**Nama template menggunakan titik sebagai separator:**

```
'home/index'       -> resources/views/home/index.php
'admin/sliders/edit' -> resources/views/admin/sliders/edit.php
```

### Sistem Layout

Layout (`resources/views/layouts/main.php` dan `admin.php`) menerima variabel `$content` yang sudah berisi HTML hasil render template. Layout meng-echo `$content` di tempat yang tepat:

```php
<main>
    <?= $content ?>
</main>
```

### Partials

Komponen yang digunakan berulang (navbar, footer, WhatsApp widget) dirender dengan `View::partial()`:

```php
View::partial('nav');
View::partial('footer');
View::partial('whatsapp-widget');
```

File partial disimpan di `resources/views/partials/`.

---

## Helpers

| Helper | Namespace | Fungsi Utama |
|---|---|---|
| `Auth` | `App\Helpers\Auth` | `attempt()` login dengan rate limiting (atomic SQLite transaction), `check()` cek sesi, `user()` ambil data user sesi, `logout()` hapus sesi, `hasRole()` cek role user, `requireRole()` abort jika role tidak cocok |
| `Csrf` | `App\Helpers\Csrf` | `token()` buat/ambil token, `field()` hasilkan input hidden HTML, `verify()` validasi token POST dengan fallback token sebelumnya untuk AJAX |
| `Flash` | `App\Helpers\Flash` | `set(type, message)` simpan pesan ke sesi, `get()` ambil dan hapus pesan, `has()` cek keberadaan |
| `Router` | `App\Helpers\Router` | `get()`, `post()` daftarkan route, `dispatch()` cocokkan dan jalankan handler |
| `Upload` | `App\Helpers\Upload` | `handle(file, directory)` validasi berlapis + simpan file, `delete(path)` hapus file dengan path traversal protection, `sanitizePath()` filter path berbahaya, `generateThumbnails()` buat 4 ukuran thumbnail via GD |
| `SecurityLogger` | `App\Helpers\SecurityLogger` | `logLogin()` login gagal/berhasil, `logUpload()` upload attempt, `logAccess()` akses tidak sah, `logCsrf()` CSRF violation, `logPathTraversal()` path traversal attempt, `logRole()` role violation |
| `View` | `App\Helpers\View` | `render(template, data, layout)` render view dengan layout, `partial(name, data)` render partial |

### Helper Global (bootstrap/app.php)

Fungsi-fungsi berikut tersedia secara global di seluruh aplikasi:

| Fungsi | Kegunaan |
|---|---|
| `env(key, default)` | Ambil nilai dari `.env` / environment variable |
| `e(value)` | HTML escape (`htmlspecialchars`) untuk output di view |
| `asset(path)` | Buat URL ke file di folder `public/assets/` |
| `upload_url(path)` | Buat URL ke file di folder `public/uploads/` |
| `url(path)` | Buat URL absolut berdasarkan `APP_URL` |
| `redirect(path)` | Redirect dan `exit` |
| `setting(key, default)` | Ambil nilai dari tabel `settings` (di-cache per request) |
| `format_rupiah(amount)` | Format integer ke string mata uang Rupiah |

---

## Database Abstraction

### Database Singleton

`App\Models\Database` menggunakan pattern Singleton untuk memastikan hanya ada satu koneksi PDO per request:

```php
$db = Database::getInstance(); // koneksi dibuat di panggilan pertama
$db = Database::getInstance(); // mengembalikan instance yang sama
```

### Pembuatan DSN per Driver

`Database::getInstance()` membaca `config/database.php` dan membangun DSN sesuai driver:

```
sqlite  -> sqlite:/path/to/storage/db/app.db
mysql   -> mysql:host=HOST;port=PORT;dbname=NAME;charset=utf8mb4
pgsql   -> pgsql:host=HOST;port=PORT;dbname=NAME
```

### PDO Attributes

Tiga attribute penting di-set setelah koneksi berhasil:

```php
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION   // error melempar exception
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC          // fetch selalu asosiatif
PDO::ATTR_EMULATE_PREPARES   => false                     // gunakan prepared statement native
```

### Beralih Database

Cukup ubah `DB_DRIVER` di `.env`. Tidak ada perubahan kode yang diperlukan. Pastikan ekstensi PDO yang sesuai terinstal di PHP.

---

## Migration System

### Cara Kerja

Runner migrasi (`database/migrate.php`) menggunakan tabel `migrations` sebagai tracker:

1. Baca semua file di `database/migrations/` secara terurut (alfabetis)
2. Query tabel `migrations` untuk mendapatkan daftar migrasi yang sudah dijalankan
3. Untuk setiap file yang belum ada di tabel `migrations`: jalankan `$migration->up($db)` lalu catat namanya
4. Jika flag `--fresh`: jalankan `$migration->down($db)` pada semua migrasi secara terbalik dahulu, bersihkan tabel `migrations`, lalu jalankan ulang

### Struktur File Migrasi

Setiap file migrasi mengembalikan anonymous class dengan method `up()` dan `down()`:

```php
<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS articles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                body TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS articles");
    }
};
```

### Konvensi Penamaan Migrasi

Format: `NNN_deskripsi_singkat.php` di mana `NNN` adalah nomor urut tiga digit:

```
008_create_articles.php
009_add_thumbnail_to_articles.php
```

Urutan numerik penting karena runner mengurutkan file secara alfabetis.

---

### Alur Autentikasi

```
1. User POST /admin/login (email + password + _csrf token)
2. CsrfMiddleware::handle() -> verifikasi token CSRF
3. Auth::attempt(email, password):
   a. Cek status akun: jika is_active = 0, tolak login
   b. Cek rate limit dengan SQLite EXCLUSIVE transaction:
      - Query tabel auth_attempts untuk IP + username dalam 15 menit terakhir
      - Jika count >= 5: return 'locked' (tanpa increment)
   c. Query UserModel::findByEmail()
   d. password_verify() cocokkan password dengan hash bcrypt (cost 12)
   e. Jika gagal:
      - INSERT ke auth_attempts (ip_address, username, created_at)
      - COMMIT transaction
      - return false
   f. Jika berhasil:
      -> Hapus semua auth_attempts untuk IP + username ini
      -> session_regenerate_id(true)  // cegah session fixation
      -> simpan user_id, user_name, user_email, user_role, user_id_original ke $_SESSION
      -> Set last_activity timestamp untuk idle timeout
4. redirect('/admin')
```

### Alur CSRF Protection

```
1. Saat render form: Csrf::field() hasilkan <input type="hidden" name="_csrf" value="TOKEN">
   -> Token dibuat dengan bin2hex(random_bytes(32)) dan disimpan di $_SESSION['csrf_token']
   -> Token juga disalin ke $_SESSION['csrf_token_prev'] untuk fallback AJAX
2. Saat form di-submit (POST):
   -> CsrfMiddleware::handle() memanggil Csrf::verify()
   -> Csrf::verify() bandingkan $_POST['_csrf'] dengan:
      a. $_SESSION['csrf_token'] (token saat ini) menggunakan hash_equals() -> jika cocok: lanjut
      b. Jika a gagal: bandingkan dengan $_SESSION['csrf_token_prev'] (token sebelumnya) -> jika cocok: lanjut
      c. Jika keduanya gagal: HTTP 403 dan die()
   -> Jika matched dengan token saat ini (a) DAN request bukan AJAX: rotasi token
      (generate baru, simpan yang lama ke csrf_token_prev)
   -> Jika matched dengan token lama (b): lanjutkan tanpa rotasi (untuk AJAX)
```

`hash_equals()` digunakan sebagai ganti `===` untuk mencegah timing attack.
Token hanya dirotasi pada POST request halaman penuh (bukan fetch/XHR) untuk menjaga kompatibilitas dengan AJAX request yang dikirim bersamaan.


### Rate Limiting Login

Implementasi berbasis database table `auth_attempts` dengan SQLite EXCLUSIVE transaction untuk atomic increment:

- Struktur tabel: `id INTEGER PRIMARY KEY`, `ip_address TEXT`, `username TEXT`, `created_at TEXT`
- Saat login gagal: `INSERT INTO auth_attempts (ip_address, username, created_at) VALUES (?, ?, datetime('now'))`
- Sebelum login: `SELECT COUNT(*) FROM auth_attempts WHERE (ip_address = ? OR username = ?) AND created_at > datetime('now', '-15 minutes')`
- Jika count >= 5: akun terkunci, `Auth::attempt()` langsung return `'locked'`
- Seluruh operasi (count + insert) dibungkus dalam `BEGIN EXCLUSIVE TRANSACTION` / `COMMIT` untuk mencegah race condition pada concurrent request
- Login berhasil: `DELETE FROM auth_attempts WHERE ip_address = ? OR username = ?`
- Tracking per IP dan per username (jika attacker ganti IP, username tetap terblokir; jika ganti username, IP tetap terblokir)

### Upload Security

`App\Helpers\Upload::handle()` melakukan validasi berlapis (10 lapis):

1. Cek `$file['error'] === UPLOAD_ERR_OK` dengan switch untuk semua kode error
2. Validasi file benar-benar hasil upload (`is_uploaded_file()`) — bukan file lokal yang diinjeksi
3. Buka file dengan `finfo_open(FILEINFO_MIME_TYPE)` — baca magic bytes file, bukan ekstensi
4. Cocokkan MIME type dengan whitelist: `image/jpeg`, `image/png`, `image/webp`, `image/gif`
5. **MIME-Extension Consistency Check** — Validasi bahwa ekstensi file asli cocok dengan MIME type hasil `finfo`
6. Cek ukuran file tidak melebihi `config/storage.php` `max_size` (default 5MB) dan minimum 1 byte
7. **Banned Extension Check** — Tolak ekstensi berbahaya: `.php`, `.pht`, `.phtml`, `.php4`, `.php5`, `.phar`, `.htaccess`, `.htpasswd`, `.shtml`, `.inc`
8. **Image Content Integrity** — Untuk file gambar, panggil `getimagesize()` untuk memverifikasi file benar-benar gambar yang valid
9. **Image Dimension Limit** — Tolak upload jika dimensi > 4000x4000 piksel (`Upload::MAX_IMAGE_DIMENSION`)
10. Generate nama file baru: `uniqid() . '_' . time() . '.' . $ext`

**Validasi path di Upload::handle() dan Upload::delete():**
- `sanitizePath()` memvalidasi path input terhadap: null bytes, directory traversal, absolute paths, karakter tidak aman
- `realpath()` + `strpos()` memastikan path final di dalam direktori uploads

**Thumbnail Generation:**
- Jika GD atau Imagick tersedia, upload gambar otomatis menghasilkan 4 ukuran thumbnail: 400, 800, 1200, 1600 px
- Thumbnail disimpan sebagai `filename_{width}.ext` di direktori yang sama
- Format output mengikuti format asli (JPEG quality 85, PNG compression 9, WebP quality 80)
- Transparency dipertahankan untuk PNG dan GIF

## Known Limitations

### Admin Panel UI
- Tidak mendukung real-time updates (harus refresh halaman).
- RBAC sudah diimplementasikan, namun tampilan UI belum sepenuhnya adaptif terhadap role yang berbeda.

---

## Dark / Light Mode

### Implementasi Teknis

Tema menggunakan atribut `data-bs-theme` milik Bootstrap 5.3 pada elemen `<html>`:

```html
<!-- Bootstrap membaca atribut ini untuk menerapkan tema -->
<html lang="id" data-bs-theme="light" id="html-root">
```

**Mencegah Flash of Wrong Theme (FOUT):**

Skrip inline kecil ditempatkan di `<head>` sebelum CSS apapun dimuat. Skrip ini berjalan sinkron sehingga tema diterapkan sebelum browser mulai merender konten:

```html
<script>
    (function(){
        var t = localStorage.getItem('cctv_theme') || 'light';
        document.getElementById('html-root').setAttribute('data-bs-theme', t);
    })();
</script>
```

**Toggle di navbar** (dikelola `public/assets/js/app.js`):

```javascript
// Pseudocode alur toggle
currentTheme = html.getAttribute('data-bs-theme')
newTheme = currentTheme === 'dark' ? 'light' : 'dark'
html.setAttribute('data-bs-theme', newTheme)
localStorage.setItem('cctv_theme', newTheme)
// update ikon toggle
```

**CSS custom properties** di `public/assets/css/app.css` dapat menggunakan selector `[data-bs-theme="dark"]` untuk styling kustom di luar scope Bootstrap:

```css
[data-bs-theme="dark"] .custom-card {
    background-color: #1e1e2e;
}
```

---

## Menambah Fitur Baru (Panduan Langkah demi Langkah)

Contoh kasus: menambah modul **Artikel / Blog**.

### Langkah 1: Buat Migrasi

Buat `database/migrations/008_create_articles.php`:

```php
<?php
return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS articles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                body TEXT,
                image TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }
    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS articles");
    }
};
```

Jalankan: `php database/migrate.php`

### Langkah 2: Buat Model

Buat `app/Models/ArticleModel.php`:

```php
<?php
namespace App\Models;

class ArticleModel extends BaseModel
{
    protected string $table = 'articles';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
```

### Langkah 3: Buat Controller Admin

Buat `app/Controllers/Admin/ArticleController.php`:

```php
<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Models\ArticleModel;
use App\Helpers\View;
use App\Helpers\Flash;

class ArticleController
{
    private ArticleModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();
        $this->model = new ArticleModel();
    }

    public function index(): void
    {
        $articles = $this->model->getAll('created_at DESC');
        View::render('admin/articles/index', compact('articles'), 'admin');
    }

    public function store(): void
    {
        // validasi, simpan, redirect
        $id = $this->model->create([
            'title' => $_POST['title'],
            'slug'  => $_POST['slug'],
            'body'  => $_POST['body'],
        ]);
        Flash::set('success', 'Artikel berhasil ditambahkan.');
        redirect('/admin/articles');
    }

    // ... create(), edit(), update(), destroy()
}
```

### Langkah 4: Buat View

Buat folder `resources/views/admin/articles/` dan file-file view:
- `index.php` — tabel daftar artikel
- `create.php` — form tambah artikel
- `edit.php` — form edit artikel

Setiap form harus menyertakan CSRF field:

```html
<form method="POST" action="<?= url('admin/articles') ?>">
    <?= \App\Helpers\Csrf::field() ?>
    <!-- field lainnya -->
    <button type="submit">Simpan</button>
</form>
```

### Langkah 5: Daftarkan Route

Tambahkan di `routes/admin.php`:

```php
$router->get('/admin/articles', 'Admin\ArticleController@index');
$router->get('/admin/articles/create', 'Admin\ArticleController@create');
$router->post('/admin/articles', 'Admin\ArticleController@store');
$router->get('/admin/articles/{id}/edit', 'Admin\ArticleController@edit');
$router->post('/admin/articles/{id}', 'Admin\ArticleController@update');
$router->post('/admin/articles/{id}/delete', 'Admin\ArticleController@destroy');
```

### Langkah 6 (Opsional): Tampilkan di Halaman Publik

Tambahkan route di `routes/web.php`:

```php
$router->get('/artikel', 'ArticleController@index');
$router->get('/artikel/{slug}', 'ArticleController@show');
```

Buat `app/Controllers/ArticleController.php` (tanpa middleware) dan view yang sesuai.

---

## Konvensi Kode

### Penamaan Class & File

| Tipe | Konvensi | Contoh |
|---|---|---|
| Controller | PascalCase + sufiks `Controller` | `SliderController` |
| Model | PascalCase + sufiks `Model` | `SliderModel` |
| Helper | PascalCase | `Upload`, `Flash` |
| Middleware | PascalCase + sufiks `Middleware` | `AuthMiddleware` |
| View template | kebab-case | `create.php`, `edit.php` |
| View folder | kebab-case | `admin/sliders/` |

### Penamaan Method Controller

| Aksi | Method |
|---|---|
| Tampilkan daftar | `index()` |
| Form tambah | `create()` |
| Simpan baru | `store()` |
| Form edit | `edit($id)` |
| Simpan perubahan | `update($id)` |
| Hapus data | `destroy($id)` |
| Simpan urutan | `reorder()` |

### Penamaan Tabel Database

- Menggunakan `snake_case`, plural: `sliders`, `pricing_packages`, `pricing_features`
- Kolom: `snake_case`, singular
- Kolom standar yang ada di hampir semua tabel: `id`, `is_active`, `sort_order`, `created_at`, `updated_at`

### Namespace PHP

```
App\Controllers\       -> app/Controllers/
App\Controllers\Admin\ -> app/Controllers/Admin/
App\Models\            -> app/Models/
App\Helpers\           -> app/Helpers/
App\Middleware\        -> app/Middleware/
```

Autoloader terdapat di `bootstrap/autoload.php` menggunakan `spl_autoload_register`.

---

## API Endpoints (Daftar Semua Route)

### Halaman Publik

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/` | `HomeController@index` | Halaman utama company profile |

### Admin — Autentikasi

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/login` | `Admin\AuthController@showLogin` | Tampilkan form login |
| POST | `/admin/login` | `Admin\AuthController@login` | Proses login |
| POST | `/admin/logout` | `Admin\AuthController@logout` | Logout & destroy session |

### Admin — Dashboard

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin` | `Admin\DashboardController@index` | Ringkasan statistik konten |

### Admin — Slider

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/sliders` | `Admin\SliderController@index` | Daftar slider |
| GET | `/admin/sliders/create` | `Admin\SliderController@create` | Form tambah slider |
| POST | `/admin/sliders` | `Admin\SliderController@store` | Simpan slider baru |
| GET | `/admin/sliders/{id}/edit` | `Admin\SliderController@edit` | Form edit slider |
| POST | `/admin/sliders/{id}` | `Admin\SliderController@update` | Update slider |
| POST | `/admin/sliders/{id}/delete` | `Admin\SliderController@destroy` | Hapus slider |
| POST | `/admin/sliders/reorder` | `Admin\SliderController@reorder` | Simpan urutan |

### Admin — Galeri

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/gallery` | `Admin\GalleryController@index` | Daftar foto galeri |
| GET | `/admin/gallery/create` | `Admin\GalleryController@create` | Form tambah foto |
| POST | `/admin/gallery` | `Admin\GalleryController@store` | Simpan foto baru |
| GET | `/admin/gallery/{id}/edit` | `Admin\GalleryController@edit` | Form edit foto |
| POST | `/admin/gallery/{id}` | `Admin\GalleryController@update` | Update foto |
| POST | `/admin/gallery/{id}/delete` | `Admin\GalleryController@destroy` | Hapus foto |
| POST | `/admin/gallery/reorder` | `Admin\GalleryController@reorder` | Simpan urutan |

### Admin — Paket Harga

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/pricing` | `Admin\PricingController@index` | Daftar paket harga |
| GET | `/admin/pricing/create` | `Admin\PricingController@create` | Form tambah paket |
| POST | `/admin/pricing` | `Admin\PricingController@store` | Simpan paket baru |
| GET | `/admin/pricing/{id}/edit` | `Admin\PricingController@edit` | Form edit paket |
| POST | `/admin/pricing/{id}` | `Admin\PricingController@update` | Update paket |
| POST | `/admin/pricing/{id}/delete` | `Admin\PricingController@destroy` | Hapus paket |
| POST | `/admin/pricing/reorder` | `Admin\PricingController@reorder` | Simpan urutan |

### Admin — Testimoni

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/testimonials` | `Admin\TestimonialController@index` | Daftar testimoni |
| GET | `/admin/testimonials/create` | `Admin\TestimonialController@create` | Form tambah testimoni |
| POST | `/admin/testimonials` | `Admin\TestimonialController@store` | Simpan testimoni baru |
| GET | `/admin/testimonials/{id}/edit` | `Admin\TestimonialController@edit` | Form edit testimoni |
| POST | `/admin/testimonials/{id}` | `Admin\TestimonialController@update` | Update testimoni |
| POST | `/admin/testimonials/{id}/delete` | `Admin\TestimonialController@destroy` | Hapus testimoni |
| POST | `/admin/testimonials/reorder` | `Admin\TestimonialController@reorder` | Simpan urutan |

### Admin — Client / Mitra

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/clients` | `Admin\ClientController@index` | Daftar logo klien |
| GET | `/admin/clients/create` | `Admin\ClientController@create` | Form tambah klien |
| POST | `/admin/clients` | `Admin\ClientController@store` | Simpan klien baru |
| GET | `/admin/clients/{id}/edit` | `Admin\ClientController@edit` | Form edit klien |
| POST | `/admin/clients/{id}` | `Admin\ClientController@update` | Update klien |
| POST | `/admin/clients/{id}/delete` | `Admin\ClientController@destroy` | Hapus klien |
| POST | `/admin/clients/reorder` | `Admin\ClientController@reorder` | Simpan urutan |

### Admin — Brand Logo

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/brand` | `Admin\BrandController@index` | Daftar brand |
| GET | `/admin/brand/create` | `Admin\BrandController@create` | Form tambah brand |
| POST | `/admin/brand` | `Admin\BrandController@store` | Simpan brand baru (upload logo) |
| GET | `/admin/brand/{id}/edit` | `Admin\BrandController@edit` | Form edit brand |
| POST | `/admin/brand/{id}` | `Admin\BrandController@update` | Update brand (upload logo baru) |
| POST | `/admin/brand/{id}/delete` | `Admin\BrandController@destroy` | Hapus brand + file logo |
| POST | `/admin/brand/reorder` | `Admin\BrandController@reorder` | Simpan urutan |

### Admin — Keunggulan (Advantages)

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/advantages` | `Admin\AdvantageController@index` | Daftar keunggulan |
| GET | `/admin/advantages/create` | `Admin\AdvantageController@create` | Form tambah keunggulan |
| POST | `/admin/advantages` | `Admin\AdvantageController@store` | Simpan keunggulan baru |
| GET | `/admin/advantages/{id}/edit` | `Admin\AdvantageController@edit` | Form edit keunggulan |
| POST | `/admin/advantages/{id}` | `Admin\AdvantageController@update` | Update keunggulan |
| POST | `/admin/advantages/{id}/delete` | `Admin\AdvantageController@destroy` | Hapus keunggulan |
| POST | `/admin/advantages/reorder` | `Admin\AdvantageController@reorder` | Simpan urutan |

### Admin — Pengaturan

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| GET | `/admin/settings` | `Admin\SettingsController@index` | Tampilkan form pengaturan |
| POST | `/admin/settings` | `Admin\SettingsController@update` | Simpan perubahan pengaturan |
