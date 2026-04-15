<!-- Page: Ruang Warga -->
<div id="page-ruang-warga" class="page-content hidden page-section">
    <div class="glass-card rw-hero">
        <div>
            <p class="rw-eyebrow">Portal Personal Warga</p>
            <h2 class="rw-title">Ruang Warga</h2>
            <p class="rw-subtitle">Kelola data diri, pantau histori pembayaran, kirim pengaduan, update informasi, dan akses cepat ke Pasar Warga.</p>
        </div>
        <div class="rw-cta-wrap">
            <a href="pasar.php" class="rw-btn rw-btn-ghost" target="_blank" rel="noopener">Lihat Pasar Warga</a>
            <a href="login_penjual.php" class="rw-btn rw-btn-primary" target="_blank" rel="noopener">Masuk sebagai Penjual</a>
        </div>
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
        <button class="rw-tab-btn active" data-rw-tab="profil">Data Diri</button>
        <button class="rw-tab-btn" data-rw-tab="history">History Pembayaran</button>
        <button class="rw-tab-btn" data-rw-tab="pengaduan">Pengaduan</button>
        <button class="rw-tab-btn" data-rw-tab="informasi">Update Informasi</button>
        <button class="rw-tab-btn" data-rw-tab="pasar">Link Pasar Warga</button>
    </div>

    <div id="rw-tab-profil" class="rw-tab-panel">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Data Diri (CRUD)</h3>
                <p class="text-secondary">Perubahan username dan password akan langsung mempengaruhi proses login Anda.</p>
                <form id="rw-profile-form" class="rw-form-grid">
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
            <div class="table-container" style="margin-top: 16px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Bulan</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody id="rw-history-body">
                        <tr><td colspan="5" class="text-center text-secondary">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="rw-tab-pengaduan" class="rw-tab-panel hidden">
        <div class="rw-grid-2">
            <div class="glass-card rw-card">
                <h3>Kirim Pengaduan</h3>
                <form id="rw-pengaduan-form" class="rw-form-grid">
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
                <form id="rw-update-form" class="rw-form-grid">
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
</div>
