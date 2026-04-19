# Deployment Cache Runbook (cPanel)

Panduan ini untuk mencegah kasus tampilan lama (CSS/JS/HTML stale) setelah upload ke server.

## 1) Ringkasan Mekanisme yang Sudah Aktif

- Asset CSS/JS memakai versi terpusat dari `smart_asset_version()` di `config/asset_url.php`.
- HTML utama mengirim header anti-cache via `smart_send_html_no_cache_headers()`.
- Override versi deploy bisa lewat environment variable: `SMART_ASSET_VERSION`.

Default fallback saat env belum diset:

```php
return '20260418r2';
```

## 2) SOP Saat Mau Deploy

1. Siapkan nilai versi baru yang unik (contoh: `20260418r3` atau `2026-04-18.3`).
2. Set `SMART_ASSET_VERSION` di environment hosting (disarankan).
3. Jika environment variable belum tersedia, update fallback di `config/asset_url.php`.
4. Upload file aplikasi ke server (cPanel File Manager/FTP/Git pull).
5. Purge cache edge/proxy/CDN (jika ada Cloudflare, LiteSpeed, Nginx cache, plugin cache).
6. Verifikasi dari browser private/incognito.

## 3) Cara Bump Versi

### Opsi A (Disarankan): Environment Variable

Set pada environment web server/PHP-FPM:

- `SMART_ASSET_VERSION=20260418r3`

Kelebihan: tidak perlu edit kode setiap deploy.

### Opsi B: Ubah Fallback di Kode

Edit `config/asset_url.php` pada fungsi `smart_asset_version()`:

```php
return '20260418r3';
```

## 4) Checklist Verifikasi Cepat

- Buka halaman: `index.php`, `login.php`, `ruang_warga.php`, `ruang_penjual.php`, `pasar.php`, `toko.php`.
- Cek DevTools > Network:
  - URL CSS/JS punya query `?v=...` versi baru.
  - Dokumen HTML utama tidak di-serve sebagai halaman cache lama.
- Lakukan hard refresh:
  - Windows/Linux: `Ctrl + F5`
  - macOS: `Cmd + Shift + R`

## 5) Jika Masih Tampil Versi Lama

1. Purge ulang cache CDN/proxy/server cache.
2. Cek nilai efektif env `SMART_ASSET_VERSION` di runtime PHP.
3. Pastikan request mengarah ke dokumen root yang benar (tidak ke salinan folder lama).
4. Uji dengan browser incognito + perangkat lain.
5. Validasi bahwa file CSS/JS terbaru benar-benar ter-upload (timestamp/ukuran berubah).

## 6) Catatan Operasional

- Gunakan format versi monoton naik (mis. `YYYYMMDDrN`).
- Bump versi **setiap** perubahan frontend (CSS/JS) yang user-facing.
- Untuk release besar, dokumentasikan versi aktif dan waktu deploy di log internal tim.
