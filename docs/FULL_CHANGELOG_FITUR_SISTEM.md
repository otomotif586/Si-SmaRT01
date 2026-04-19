# Full Changelog & Fitur Sistem Si-SmaRT

Dokumen ini merangkum **fitur sistem** dan **changelog implementasi** berdasarkan snapshot kode saat ini.

- Repo: `Si-SmaRT01`
- Cabang: `main`
- Tanggal snapshot: `2026-04-18`

> Catatan: proyek ini belum menunjukkan riwayat release tag formal di dokumen. Karena itu, bagian changelog disusun dari perubahan kode yang sudah diterapkan dan status sistem saat ini.

---

## 1) Ringkasan Sistem

Si-SmaRT adalah platform RT terpadu dengan domain utama:

1. **Portal Publik** (informasi warga, berita, agenda, transparansi, laporan terbaru)
2. **Aplikasi Internal Pengurus/Admin** (dashboard, data warga, iuran, keuangan, keamanan, CMS)
3. **Ruang Warga** (akun warga, profil lengkap, aduan, update warga)
4. **Pasar Warga + Ruang Penjual** (manajemen produk/jasa warga)

Entry point utama:

- Publik: `index.php`
- Aplikasi internal: `app.php`
- Warga: `ruang_warga.php`
- Penjual: `ruang_penjual.php`
- Pasar publik/internal: `pasar.php`, `toko.php`

---

## 2) Changelog Utama (Status Terkini)

### [2026-04-18] Cache Hardening & UX Stabilization

#### Added
- Fungsi versi asset terpusat `smart_asset_version()` di `config/asset_url.php`.
- Fungsi anti-cache HTML `smart_send_html_no_cache_headers()` di `config/asset_url.php`.
- Runbook deploy cache: `docs/DEPLOYMENT_CACHE_RUNBOOK.md`.

#### Changed
- Standardisasi cache-busting query `?v=` pada CSS/JS penting via `smart_asset(..., smart_asset_version())`.
- Entry page utama kini mengirim header anti-cache HTML (`index.php`, `blog-detail.php`, `login.php`, `login_penjual.php`, `pasar.php`, `toko.php`, `ruang_penjual.php`, `ruang_warga.php`, `app.php`).
- Penyelarasan tampilan Ruang Warga agar kontras stabil setelah deploy dan tidak terpengaruh style umum.

#### Fixed
- Kasus tampilan stale setelah upload cPanel (asset lama masih ter-cache).
- Potensi inkonsistensi versi asset akibat hardcoded version yang tersebar.
- Risiko HTML lama tetap tersaji dari cache browser/proxy.

---

## 3) Changelog Fungsional Modul (Akumulatif)

### A. Ruang Warga
- Alur login/registrasi berbasis NIK 16 digit.
- Normalisasi data NIK, nomor WA, dan nomor rumah (2 digit, zero-padding) di bootstrap modul warga.
- Otomatis sinkron akun warga ke entitas `warga` (link by NIK, WA, nama+nomor rumah).
- Otomatis provisioning akun penjual (`pasar_penjual`) dari akun warga saat login/daftar.
- Pembaruan profil warga lengkap (pasangan, anak, kendaraan, orang lain, dokumen, avatar).
- Aduan warga dengan lampiran (validasi ukuran/ekstensi) dan status approval portal.
- Feed informasi penting (berita + agenda) untuk warga.

### B. Portal Publik
- Section modular: navbar, mobile menu, hero, statistik, info penting, visi-misi, organisasi, berita, transparansi, laporan terbaru, wisata, footer.
- Laporan keamanan di portal menggunakan mekanisme approval (`approved_portal`) dengan fallback skema lama.
- Pengaturan portal dinamis dari `web_settings` (logo, favicon, slider, wisata, dsb).

### C. Aplikasi Internal (Pengurus/Admin)
- Dashboard ringkasan dan modul manajemen warga/iuran/keuangan/keamanan/informasi/users/pasar.
- Halaman multi-modul terstruktur di `views/pages/*` dan `views/layout/*`.

### D. Pasar & Ruang Penjual
- Login penjual terpisah.
- Pengelolaan produk/jasa (CRUD), upload foto, pencarian, status ketersediaan.
- Sinkron identitas penjual dari data warga terkait.

### E. Keuangan, Iuran, Rekonsiliasi
- Endpoint pembayaran iuran parsial/penuh/semua.
- Validasi/unlock iuran RT (single dan bulk).
- Jurnal RT dan pos, setor kas RT, reclass jurnal.
- Laporan iuran per blok/warga, pembukuan, rekonsiliasi global.

### F. Keamanan
- Modul laporan keamanan, jadwal, satpam, dan perizinan (subfolder `api/keamanan`).
- Integrasi aduan warga ke laporan keamanan.

### G. CMS Portal
- CRUD settings, menu, blog, pengurus.
- Digunakan untuk mengatur konten publik tanpa ubah kode inti.

### H. Data Operasional
- Import/export data warga.
- Export rekonsiliasi.
- Template download warga.

---

## 4) Katalog Fitur Sistem (Per Domain)

## 4.1 Autentikasi & Akses
- Login admin/pengurus: `login.php`
- Login penjual: `login_penjual.php`
- Login/registrasi warga: `ruang_warga.php` + `views/ruang_warga/bootstrap.php`
- Logout terpisah per domain: `logout.php`, `logout_penjual.php`, `logout_ruang_warga.php`
- Session role routing ke area masing-masing.

## 4.2 Data Warga
- CRUD warga (`tambah_warga`, `edit_warga`, `hapus_warga`, `get_warga`, detail/full detail).
- Manajemen blok (`get_bloks`, `tambah_blok`, `edit_blok`, `hapus_blok`, `simpan_periode_blok`).
- Import warga (`import_warga.php`) dan export warga (`export_warga.php`).

## 4.3 Iuran RT
- Master iuran (`get_master_iuran`, `simpan_master_iuran`, `edit_iuran`, `hapus_iuran`).
- Pembayaran iuran (`bayar_iuran`, `bayar_sebagian_iuran`, `bayar_semua_iuran`).
- Validasi iuran (`validate_iuran_rt`, `validate_selected_rt`, `bulk_validate_iuran_rt`).
- Unlock iuran (`unlock_iuran_rt`, `bulk_unlock_iuran_rt`).
- Laporan iuran warga/blok (`get_laporan_iuran_warga`, `get_laporan_iuran_blok`).

## 4.4 Keuangan & Pembukuan
- Keuangan umum (`get_keuangan`, `simpan_keuangan`, `hapus_keuangan`, `get_detail_keuangan`).
- Pos keuangan (`get_pos_keuangan`, `simpan_pengeluaran_pos`, `post_jurnal_pos`).
- Jurnal RT (`post_jurnal_rt`, `bulk_post_jurnal_rt`, `reclass_jurnal_rt`, `setor_kas_rt`).
- Pembukuan & rekonsiliasi (`get_pembukuan`, `get_rekonsiliasi`, `get_global_rekonsiliasi`, `export_rekonsiliasi`).

## 4.5 Keamanan Lingkungan
- Laporan keamanan (`get_laporan`, `simpan_laporan`, `hapus_laporan`, `hapus_lampiran_laporan`).
- Aduan warga ke keamanan (`ruang_warga_save_pengaduan`, `ruang_warga_delete_pengaduan`).
- Modul keamanan detail (`api/keamanan`):
  - Satpam: `get_satpam`, `simpan_satpam`, `hapus_satpam`
  - Jadwal: `get_jadwal`, `simpan_jadwal`, `hapus_jadwal`
  - Izin: `get_izin`, `simpan_izin`, `hapus_izin`
  - Ringkasan/laporan: `get_ringkasan`, `get_laporan`, `simpan_laporan`, `hapus_laporan`

## 4.6 Agenda & Informasi
- Agenda kegiatan (`get_agenda`, `simpan_agenda`, `hapus_agenda`, `hapus_foto_agenda`, `hapus_lampiran_agenda`).
- Feed informasi penting di Ruang Warga dari berita + agenda.

## 4.7 CMS Web Publik
- Settings web (`cms_get_settings`, `cms_save_settings`).
- Menu web (`cms_get_menus`, `cms_save_menu`, `cms_delete_menu`).
- Blog/berita (`cms_get_blogs`, `cms_save_blog`, `cms_delete_blog`).
- Pengurus (`cms_get_pengurus`, `cms_save_pengurus`, `cms_delete_pengurus`).

## 4.8 Pasar Warga
- Katalog produk pasar (`get_produk_pasar`).
- Manajemen produk penjual dari `ruang_penjual.php` dan endpoint pendukung `views/pages/*` terkait pasar.
- Integrasi profil toko, kontak, status produk, dan upload foto.

## 4.9 Transparansi & Pelaporan
- Transparansi file/section di portal (`index.php` + settings).
- Dashboard summary (`get_dashboard_summary`).
- Laporan global warga (`get_global_warga`).

## 4.10 Upload Dokumen/Media
- Upload dokumen warga (`public/uploads`).
- Upload avatar warga (`public/uploads/avatars`).
- Upload lampiran aduan keamanan (`public/uploads/lampiran_aduan`).

---

## 5) Arsitektur Teknis Singkat

- Backend: PHP procedural + endpoint API file-per-action.
- DB access: campuran PDO dan mysqli (sesuai modul lama/baru).
- Frontend: server-rendered HTML + Tailwind CDN + CSS custom di `public/css` + JS custom di `public/js`.
- Asset helper: `smart_asset()`, `smart_asset_version()`, `smart_public_fs_path()`.
- Header helper: `smart_send_html_no_cache_headers()`.

---

## 6) Status Operasional & Catatan

### Kekuatan saat ini
- Cakupan fitur operasional RT cukup lengkap (warga, iuran, keuangan, keamanan, pasar).
- CMS publik sudah tersedia untuk pengelolaan konten.
- Cache deploy lebih terkendali dengan versioning terpusat + HTML no-cache headers.

### Catatan peningkatan lanjutan (opsional)
1. Konsolidasi akses DB agar seragam (PDO/mysqli).
2. Standarisasi response API JSON dan error envelope lintas endpoint.
3. Tambah release tagging (`CHANGELOG.md` berbasis versi) untuk jejak historis yang lebih presisi.
4. Tambah endpoint debug terbatas admin untuk cek versi asset aktif saat runtime.

---

## 7) Rekomendasi Format Rilis Ke Depan

Gunakan format versi semantik internal, contoh:

- `v2026.04.18-r3`
- `v2026.05.02-r1`

Dan setiap rilis mencatat minimal:

- Added
- Changed
- Fixed
- Breaking (jika ada)

Sehingga dokumen changelog bisa diturunkan otomatis ke ringkasan release deploy.
