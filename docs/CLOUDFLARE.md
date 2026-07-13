# Cloudflare Configuration — Bayu CCTV

Dokumen ini berisi rekomendasi konfigurasi Cloudflare untuk mengamankan situs `cctv.miasmagoe.my.id` dari DDoS, scraping, dan serangan umum.

> **Status:** Domain sudah terproxy (orange cloud) di Cloudflare.  
> **Server:** ZimaOS NAS `192.168.7.10` — Docker port `8081`.

---

## 1. SSL/TLS — Full (Strict)

Di **SSL/TLS** > **Overview**:

| Setting | Value |
|---|---|
| SSL/TLS encryption mode | **Full (Strict)** |
| Always Use HTTPS | **ON** |
| Automatic HTTPS Rewrites | **ON** |
| Minimum TLS Version | **1.2** |

Situs sudah kirim header `Strict-Transport-Security: max-age=31536000; includeSubDomains`, jadi pastikan HTTPS end-to-end aman.

---

## 2. WAF — Security Level

Di **Security** > **Settings**:

| Setting | Value | Keterangan |
|---|---|---|
| Security Level | **Medium** | Blokir IP dengan threat score > 14 |
| Challenge Passage | **30 min** | Jangan terlalu sering challenge |
| Browser Integrity Check | **ON** | Blokir header HTTP palsu (bot) |

**Medium** sudah cukup — tidak terlalu agresif ke pengunjung normal, tapi tetap proteksi dari IP mencurigakan.

---

## 3. Bot Fight Mode

Di **Security** > **Bots**:

| Setting | Value |
|---|---|
| Bot Fight Mode | **ON** |

Ini gratis di semua plan Cloudflare. Akan:

- Blokir **verified bots** (scraper, crawler jahat)
- Berikan **JS challenge** ke bot mencurigakan
- Tidak ganggu Googlebot / Bingbot

> Efek: scraping otomatis kena challenge. Skrip sederhana (curl, wget, Python requests) tidak bisa akses halaman publik.

---

## 4. Rate Limiting Rules

Di **Security** > **WAF** > **Rate limiting rules** (free tier: 10 rules).

### 4.1 Rate Limit — Login Page

Sudah ada rate limiting di level aplikasi (5 gagal → lock 15 menit), tapi tambahkan di Cloudflare sebagai lapisan ekstra:

```
Field: URI Path
Operator: equals
Value: /admin/login

Field: IP Source Address

When: 10 requests dalam 60 seconds
Then: Block for 300 seconds
```

Kode JSON untuk API:
```json
{
  "description": "Rate limit login page",
  "expression": "(ip.src ne 192.168.0.0/16) and http.request.uri.path eq \"/admin/login\"",
  "action": "block",
  "ratelimit": {
    "characteristics": ["ip.src"],
    "period": 60,
    "requests_per_period": 10,
    "mitigation_timeout": 300
  }
}
```

> **Catatan:** Kecualikan IP lokal (192.168.x.x) agar akses dari LAN tetap lancar.

### 4.2 Rate Limit — All Requests

Proteksi dasar DDoS — batasi request per IP:

```
Field: URI Path
Operator: starts with
Value: /

Field: IP Source Address

When: 200 requests dalam 60 seconds
Then: JS Challenge for 60 seconds
```

Ini akan memberikan challenge Cloudflare ke IP yang mengirim request berlebihan (scraper, bot, DDoS ringan), tanpa memblokir total.

---

## 5. WAF Custom Rules

Di **Security** > **WAF** > **Custom rules** (tersedia di plan Pro+, core ruleset gratis).

Jika punya akses **Core Ruleset** (OWASP), aktifkan dengan **paranoia level 1** — cukup protektif tanpa false positive tinggi.

Untuk free tier, tidak ada custom rules, tapi cukup dengan Security Level + Browser Integrity Check + Bot Fight Mode.

---

## 6. Caching

Di **Speed** > **Optimization**:

| Setting | Value | Keterangan |
|---|---|---|
| Auto Minify | **JavaScript, CSS, HTML** | Kecilkan ukuran file statis |
| Brotli | **ON** | Kompresi lebih baik dari gzip |
| Always Online | **OFF** | Tidak perlu untuk situs dinamis |

Jangan aktifkan **Rocket Loader** — bisa merusak JavaScript interaktif (SortableJS, admin panel).

---

## 7. Security Headers (Already Done)

Header yang sudah dikirim dari `bootstrap/app.php` — dicek via Cloudflare:

| Header | Status |
|---|---|
| `Strict-Transport-Security` | ✅ `max-age=31536000; includeSubDomains` |
| `X-Frame-Options` | ✅ `SAMEORIGIN` |
| `X-Content-Type-Options` | ✅ `nosniff` |
| `X-XSS-Protection` | ✅ `1; mode=block` |
| `Referrer-Policy` | ✅ `strict-origin-when-cross-origin` |
| `Content-Security-Policy` | ✅ Restrictive |
| `Permissions-Policy` | ✅ Minimal |
| `X-Powered-By` | ✅ Removed |

Tidak perlu kirim ulang header yang sama dari Cloudflare (bisa redundan atau konflik). Cukup andalkan dari server.

---

## 8. Firewall Rules (Opsional)

Jika ingin blokir akses langsung ke IP server (bukan via Cloudflare):

Di **Security** > **WAF** > **Firewall rules**:

```
Expression: (not cf.client.bot) and http.request.uri contains "/admin"
Action: Managed Challenge
```

Ini memberikan challenge ke semua akses `/admin` yang bukan dari bot — lapisan keamanan tambahan.

---

## 9. Quick Checklist

Prioritas (kerjakan urut):

| # | Setting | Halaman | Efek |
|---|---|---|---|
| 1 | **SSL/TLS: Full (Strict)** | SSL/TLS > Overview | HTTPS aman |
| 2 | **Always Use HTTPS** | SSL/TLS > Overview | Paksa HTTPS |
| 3 | **Security Level: Medium** | Security > Settings | Blokir IP threat tinggi |
| 4 | **Browser Integrity Check: ON** | Security > Settings | Filter header palsu |
| 5 | **Bot Fight Mode: ON** | Security > Bots | Blokir scraper/bot |
| 6 | **Rate Limit — Login** | Security > WAF > Rate limiting | Proteksi brute force |
| 7 | **Rate Limit — General (200/min)** | Security > WAF > Rate limiting | Proteksi DDoS ringan |
| 8 | **Auto Minify (JS, CSS, HTML)** | Speed > Optimization | Loading lebih cepat |

---

## 10. Testing

Setelah konfigurasi, verifikasi:

```bash
# Cek header response
curl -sI https://cctv.miasmagoe.my.id | grep -i "cf-ray\|x-frame\|strict-transport"

# Simulasi rate limit (login page)
for i in {1..15}; do
    curl -s -o /dev/null -w "%{http_code} " https://cctv.miasmagoe.my.id/admin/login
done

# Cek apakah bot diblokir
curl -s -A "BadBot/1.0" https://cctv.miasmagoe.my.id/
# Harusnya return 403 atau JS challenge
```

---

**Referensi:**
- [Cloudflare DDoS Protection](https://www.cloudflare.com/ddos/)
- [Cloudflare Rate Limiting](https://developers.cloudflare.com/waf/rate-limiting-rules/)
- [Cloudflare Bot Management](https://developers.cloudflare.com/bots/)
