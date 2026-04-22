<!-- Page: Ruang Warga -->
<div id="page-ruang-warga" class="page-content hidden page-section">
    <!-- Header Greeting -->
    <div class="rw-greeting-header">
        <div>
            <p class="rw-greeting-eyebrow">Selamat Datang</p>
            <h1 class="rw-greeting-name" id="rw-greeting-name">Halo, Warga</h1>
        </div>
        <div class="rw-profile-avatar" id="rw-profile-avatar">
            <span><i data-lucide="user-circle"></i></span>
        </div>
    </div>

    <!-- Primary Gradient Card -->
    <div class="rw-primary-card glass-card">
        <div class="rw-primary-bg"></div>
        <div class="rw-primary-content">
            <p class="rw-primary-label">Nama Lengkap</p>
            <h2 class="rw-primary-name" id="rw-primary-name">-</h2>
            
            <div class="rw-primary-info">
                <div class="rw-primary-item">
                    <span class="rw-primary-small">Blok & Rumah</span>
                    <span class="rw-primary-value" id="rw-primary-blok">-</span>
                </div>
                <div class="rw-primary-divider"></div>
                <div class="rw-primary-item">
                    <span class="rw-primary-small">Status</span>
                    <span class="rw-primary-value" id="rw-primary-status">-</span>
                </div>
            </div>

            <div class="rw-primary-stats">
                <div class="rw-stat-mini">
                    <span class="rw-stat-mini-label">Lunas</span>
                    <span class="rw-stat-mini-value" id="rw-primary-lunas">0</span>
                    <span class="rw-stat-mini-unit">bulan</span>
                </div>
                <div class="rw-stat-mini">
                    <span class="rw-stat-mini-label">Tunggakan</span>
                    <span class="rw-stat-mini-value" id="rw-primary-tunggakan">0</span>
                    <span class="rw-stat-mini-unit">bulan</span>
                </div>
            </div>

            <div class="rw-primary-progress-wrap">
                <div class="rw-primary-progress-head">
                    <span id="rw-primary-level" class="rw-tier-badge">Level 1</span>
                    <strong id="rw-primary-progress-text">0%</strong>
                </div>
                <div class="rw-primary-progress-track">
                    <span class="rw-primary-progress-fill" id="rw-primary-progress-fill" style="width: 0%;"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Grid -->
    <div class="rw-action-grid">
        <button class="rw-action-btn rw-action-bayar" data-rw-tab="history" type="button">
            <span class="rw-action-icon"><i data-lucide="credit-card"></i></span>
            <span class="rw-action-label">Bayar Iuran</span>
        </button>
        <button class="rw-action-btn rw-action-history" data-rw-tab="history" type="button">
            <span class="rw-action-icon"><i data-lucide="receipt-text"></i></span>
            <span class="rw-action-label">Cek History</span>
        </button>
        <button class="rw-action-btn rw-action-pengaduan" data-rw-tab="pengaduan" type="button">
            <span class="rw-action-icon"><i data-lucide="alert-circle"></i></span>
            <span class="rw-action-label">Ajukan Aduan</span>
        </button>
        <button class="rw-action-btn rw-action-info" data-rw-tab="informasi" type="button">
            <span class="rw-action-icon"><i data-lucide="bell-ring"></i></span>
            <span class="rw-action-label">Lihat Info</span>
        </button>
    </div>

    <div class="rw-summary-grid">
        <div class="glass-card rw-stat">
            <p>Data Warga Terdaftar</p>
            <h3 id="rw-stat-global-warga">0</h3>
        </div>
        <div class="glass-card rw-stat">
            <p>Total Blok</p>
            <h3 id="rw-stat-global-blok">0</h3>
        </div>
        <div class="glass-card rw-stat">
            <p>Iuran Lunas Saya</p>
            <h3 id="rw-stat-lunas">0 Bulan</h3>
        </div>
        <div class="glass-card rw-stat">
            <p>Tunggakan Saya</p>
            <h3 id="rw-stat-tunggakan">0 Bulan</h3>
        </div>
    </div>

    <div class="rw-tab-nav glass-card">
        <button class="rw-tab-btn rw-tab-profil active" data-rw-tab="profil" type="button">
            <span class="rw-tab-icon-wrap"><i data-lucide="user-round"></i></span>
            <span class="rw-tab-label">Data Diri</span>
        </button>
        <button class="rw-tab-btn rw-tab-history" data-rw-tab="history" type="button">
            <span class="rw-tab-icon-wrap"><i data-lucide="receipt-text"></i></span>
            <span class="rw-tab-label">History</span>
        </button>
        <button class="rw-tab-btn rw-tab-pengaduan" data-rw-tab="pengaduan" type="button">
            <span class="rw-tab-icon-wrap"><i data-lucide="message-square-warning"></i></span>
            <span class="rw-tab-label">Pengaduan</span>
        </button>
        <button class="rw-tab-btn rw-tab-informasi" data-rw-tab="informasi" type="button">
            <span class="rw-tab-icon-wrap"><i data-lucide="bell-ring"></i></span>
            <span class="rw-tab-label">Informasi</span>
        </button>
        <button class="rw-tab-btn rw-tab-pasar" data-rw-tab="pasar" type="button">
            <span class="rw-tab-icon-wrap"><i data-lucide="store"></i></span>
            <span class="rw-tab-label">Pasar</span>
        </button>
    </div>

    <div id="rw-tab-profil" class="rw-tab-panel">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Data Diri</h3>
                <p class="text-secondary">Kelola data profil Anda melalui panel form seperti aplikasi mobile.</p>
                <button type="button" id="rw-open-profile-form" class="rw-sheet-trigger">
                    <span class="rw-sheet-trigger-icon"><i data-lucide="user-cog"></i></span>
                    <span class="rw-sheet-trigger-main">
                        <strong>Edit Data Diri</strong>
                        <small>Ubah nama, username, dan password</small>
                    </span>
                    <span class="rw-sheet-trigger-trail"><i data-lucide="chevron-right"></i></span>
                </button>
            </div>

            <div class="glass-card rw-card">
                <h3>Data Warga Terhubung</h3>
                <p class="text-secondary">Data warga ini dipakai untuk membaca histori pembayaran Anda.</p>
                <div id="rw-linked-warga" class="rw-linked-box">
                    <p class="text-secondary">Memuat data...</p>
                </div>
                <p class="rw-note">Jika data belum terhubung, samakan nama lengkap akun dengan data warga atau hubungi admin RT.</p>
            </div>
        </div>
    </div>

    <div id="rw-tab-history" class="rw-tab-panel hidden">
        <div class="glass-card rw-card">
            <h3>History Pembayaran</h3>
            <p class="text-secondary">Riwayat iuran berdasarkan data warga yang terhubung ke akun Anda.</p>
            <div id="rw-history-list" class="rw-list-wrap" style="margin-top: 16px;">
                <p class="text-secondary">Memuat data...</p>
            </div>
        </div>
    </div>

    <div id="rw-tab-pengaduan" class="rw-tab-panel hidden">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Kirim Pengaduan</h3>
                <p class="text-secondary">Buat atau edit pengaduan melalui bottom sheet agar lebih nyaman di HP.</p>
                <button type="button" id="rw-open-pengaduan-form" class="rw-sheet-trigger">
                    <span class="rw-sheet-trigger-icon"><i data-lucide="message-square-plus"></i></span>
                    <span class="rw-sheet-trigger-main">
                        <strong>Tulis Pengaduan Baru</strong>
                        <small>Masukkan judul dan isi pengaduan</small>
                    </span>
                    <span class="rw-sheet-trigger-trail"><i data-lucide="chevron-right"></i></span>
                </button>
            </div>

            <div class="glass-card rw-card">
                <h3>Daftar Pengaduan Saya</h3>
                <div id="rw-pengaduan-list" class="rw-list-wrap">
                    <p class="text-secondary">Belum ada pengaduan.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="rw-tab-informasi" class="rw-tab-panel hidden">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Update Informasi Warga</h3>
                <p class="text-secondary">Tulis update melalui bottom sheet dengan pengalaman seperti aplikasi Android.</p>
                <button type="button" id="rw-open-update-form" class="rw-sheet-trigger">
                    <span class="rw-sheet-trigger-icon"><i data-lucide="square-pen"></i></span>
                    <span class="rw-sheet-trigger-main">
                        <strong>Buat Update Baru</strong>
                        <small>Bagikan info terbaru untuk warga</small>
                    </span>
                    <span class="rw-sheet-trigger-trail"><i data-lucide="chevron-right"></i></span>
                </button>
            </div>

            <div class="glass-card rw-card">
                <h3>Update Saya</h3>
                <div id="rw-update-list" class="rw-list-wrap">
                    <p class="text-secondary">Belum ada update informasi.</p>
                </div>
                <h4 class="rw-subheading">Informasi Terbaru Portal</h4>
                <div id="rw-info-feed" class="rw-list-wrap"></div>
            </div>
        </div>
    </div>

    <div id="rw-tab-pasar" class="rw-tab-panel hidden">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Jadi Penjual Pasar Warga</h3>
                <p class="text-secondary">Buka akun penjual untuk memasarkan produk/jasa Anda di Pasar Warga.</p>
                <div class="rw-market-actions">
                    <a href="login_penjual.php" target="_blank" rel="noopener" class="rw-btn rw-btn-primary">Daftar / Login Penjual</a>
                    <a href="ruang_penjual.php" target="_blank" rel="noopener" class="rw-btn rw-btn-ghost">Buka Ruang Penjual</a>
                </div>
            </div>

            <div class="glass-card rw-card">
                <h3>Belanja di Pasar Warga</h3>
                <p class="text-secondary">Dukung UMKM sekitar dan pantau katalog produk terbaru warga RT.</p>
                <div class="rw-market-actions">
                    <a href="pasar.php" target="_blank" rel="noopener" class="rw-btn rw-btn-primary">Buka Pasar Warga</a>
                    <a href="portal.php" target="_blank" rel="noopener" class="rw-btn rw-btn-ghost">Kembali ke Portal</a>
                </div>
            </div>
        </div>
    </div>

    <nav class="rw-bottom-nav" aria-label="Navigasi Ruang Warga">
        <button class="rw-bottom-nav-btn rw-tab-btn rw-tab-profil active" data-rw-tab="profil" type="button">
            <i data-lucide="house"></i>
            <span>Beranda</span>
        </button>
        <button class="rw-bottom-nav-btn rw-tab-btn rw-tab-history" data-rw-tab="history" type="button">
            <i data-lucide="scroll-text"></i>
            <span>Iuran</span>
        </button>
        <button class="rw-bottom-nav-btn rw-tab-btn rw-tab-pengaduan" data-rw-tab="pengaduan" type="button">
            <i data-lucide="triangle-alert"></i>
            <span>Aduan</span>
        </button>
        <button class="rw-bottom-nav-btn rw-tab-btn rw-tab-informasi" data-rw-tab="informasi" type="button">
            <i data-lucide="newspaper"></i>
            <span>Info</span>
        </button>
        <button class="rw-bottom-nav-btn rw-tab-btn rw-tab-pasar" data-rw-tab="pasar" type="button">
            <i data-lucide="store"></i>
            <span>Pasar</span>
        </button>
    </nav>

    <div id="rw-info-popup" class="rw-popup hidden" aria-hidden="true">
        <div class="rw-popup-sheet">
            <div class="rw-popup-handle"></div>
            <div class="rw-popup-header">
                <h3 id="rw-popup-title">Detail</h3>
                <button type="button" id="rw-popup-close" class="rw-popup-close" aria-label="Tutup detail">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div id="rw-popup-content" class="rw-popup-content"></div>
        </div>
    </div>

    <div id="rw-profile-modal" class="rw-form-modal hidden" aria-hidden="true">
        <div class="rw-form-sheet">
            <div class="rw-form-handle"></div>
            <div class="rw-form-header">
                <h3>Edit Data Diri</h3>
                <button type="button" class="rw-form-close" data-rw-close-modal="rw-profile-modal" aria-label="Tutup form data diri">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form id="rw-profile-form" class="rw-form-grid rw-sheet-form">
                <div>
                    <label>Nama Lengkap</label>
                    <input type="text" id="rw-nama" class="input-field" required>
                </div>
                <div>
                    <label>Username</label>
                    <input type="text" id="rw-username" class="input-field" required>
                </div>
                <div>
                    <label>Role</label>
                    <input type="text" id="rw-role" class="input-field" readonly>
                </div>
                <div>
                    <label>Password Baru</label>
                    <input type="password" id="rw-password" class="input-field" placeholder="Kosongkan jika tidak ingin ganti">
                </div>
                <div class="rw-form-actions">
                    <button type="submit" class="button-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rw-pengaduan-modal" class="rw-form-modal hidden" aria-hidden="true">
        <div class="rw-form-sheet">
            <div class="rw-form-handle"></div>
            <div class="rw-form-header">
                <h3>Kirim Pengaduan</h3>
                <button type="button" class="rw-form-close" data-rw-close-modal="rw-pengaduan-modal" aria-label="Tutup form pengaduan">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form id="rw-pengaduan-form" class="rw-form-grid rw-sheet-form">
                <input type="hidden" id="rw-pengaduan-id">
                <div>
                    <label>Judul Pengaduan</label>
                    <input type="text" id="rw-pengaduan-judul" class="input-field" required>
                </div>
                <div>
                    <label>Isi Pengaduan</label>
                    <textarea id="rw-pengaduan-isi" class="input-field" rows="5" required></textarea>
                </div>
                <div class="rw-form-actions">
                    <button type="submit" class="button-primary">Simpan Pengaduan</button>
                    <button type="button" id="rw-pengaduan-reset" class="button-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rw-update-modal" class="rw-form-modal hidden" aria-hidden="true">
        <div class="rw-form-sheet">
            <div class="rw-form-handle"></div>
            <div class="rw-form-header">
                <h3>Update Informasi</h3>
                <button type="button" class="rw-form-close" data-rw-close-modal="rw-update-modal" aria-label="Tutup form update informasi">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form id="rw-update-form" class="rw-form-grid rw-sheet-form">
                <input type="hidden" id="rw-update-id">
                <div>
                    <label>Judul Informasi</label>
                    <input type="text" id="rw-update-judul" class="input-field" required>
                </div>
                <div>
                    <label>Isi Informasi</label>
                    <textarea id="rw-update-isi" class="input-field" rows="5" required></textarea>
                </div>
                <div class="rw-form-actions">
                    <button type="submit" class="button-primary">Simpan Update</button>
                    <button type="button" id="rw-update-reset" class="button-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
