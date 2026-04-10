<!-- Page: Informasi & CMS Website -->
<div id="page-info" class="page-content hidden page-section">
    
    <!-- Sub Navigation Tabs -->
    <div class="sub-nav-tabs" style="margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 8px;">
        <button class="sub-nav-tab active" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchInfoTab('info-umum', this)">
            <i data-lucide="settings"></i> Pengaturan Umum
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchInfoTab('info-menu', this)">
            <i data-lucide="menu"></i> Menu Frontend
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchInfoTab('info-blog', this)">
            <i data-lucide="newspaper"></i> Blog & Artikel
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchInfoTab('info-transparansi', this)">
            <i data-lucide="pie-chart"></i> Laporan Keuangan
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchInfoTab('info-struktur', this)">
            <i data-lucide="users"></i> Struktur Organisasi
        </button>
    </div>

    <!-- Tab Content: Pengaturan Umum (Visi, Misi, Alamat) -->
    <div id="info-umum" class="info-tab-content active-tab">
        <div class="grid-container-2-col" style="margin-bottom: 16px;">
            <!-- SEO & Branding -->
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;"><i data-lucide="image" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> SEO & Branding</h4>
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Judul Tab / Meta Title (SEO)</label>
                    <input type="text" id="web_title" class="input-field" style="margin-top: 8px;" placeholder="Cth: SmaRT 01 - Perumahan Nyaman & Aman">
                </div>
                <div class="grid-container-2-col" style="gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="card-label" style="display: flex; justify-content: space-between;">Logo Utama <span id="preview_web_logo"></span></label>
                        <div class="upload-premium-container mt-2" style="border-radius: 12px; padding: 0;">
                            <input type="file" id="web_logo_file" accept="image/*" class="upload-premium-input">
                            <div class="upload-premium-label" style="padding: 12px;">
                                <i data-lucide="upload" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                <span class="text-color" style="font-size: 0.75rem;">Unggah Logo</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="card-label" style="display: flex; justify-content: space-between;">Favicon <span id="preview_web_favicon"></span></label>
                        <div class="upload-premium-container mt-2" style="border-radius: 12px; padding: 0;">
                            <input type="file" id="web_favicon_file" accept="image/png, image/x-icon, image/jpeg" class="upload-premium-input">
                            <div class="upload-premium-label" style="padding: 12px;">
                                <i data-lucide="upload" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                <span class="text-color" style="font-size: 0.75rem;">Unggah Favicon</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profil & Kontak -->
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;"><i data-lucide="building" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Profil & Kontak Publik</h4>
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Nama Website / Perumahan</label>
                    <input type="text" id="web_nama" class="input-field" style="margin-top: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Email Publik</label>
                    <input type="text" id="web_email" class="input-field" style="margin-top: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Nomor Telepon / WA</label>
                    <input type="text" id="web_telepon" class="input-field" style="margin-top: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="card-label">Alamat Lengkap</label>
                    <textarea id="web_alamat" class="input-field" style="margin-top: 8px; min-height: 80px; padding: 12px 20px; border-radius: 16px; resize: vertical;"></textarea>
                </div>
            </div>
        </div>

        <div class="grid-container-2-col" style="margin-bottom: 16px;">
            <!-- Tampilan Utama (Hero & Media) -->
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;"><i data-lucide="layout" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Tampilan Utama (Hero & Slider)</h4>
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Judul Utama (Hero Header)</label>
                    <input type="text" id="web_hero_title" class="input-field" style="margin-top: 8px;" placeholder="Cth: Selamat Datang di Lingkungan SmaRT 01">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label" style="display: flex; justify-content: space-between;">Gambar Latar (Hero Banner) <span id="preview_web_hero_image"></span></label>
                    <input type="file" id="web_hero_image_file" accept="image/*" class="input-field file-input-modern" style="margin-top: 8px; width: 100%;">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label" style="display: flex; justify-content: space-between;">Gambar Banner Slider <span id="preview_web_slider_images"></span></label>
                    <input type="file" id="web_slider_images_files" accept="image/*" multiple class="input-field file-input-modern" style="margin-top: 8px; width: 100%;" title="Pilih beberapa gambar sekaligus">
                    <small class="text-secondary" style="margin-top: 4px;">Pilih 1 atau lebih gambar untuk ditambahkan ke *carousel slider*.</small>
                </div>
                <div class="form-group" style="margin-bottom: 16px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
                    <label class="card-label">Integrasi Galeri Warga (Infinity Gallery)</label>
                    <select id="web_use_gallery" class="input-field select-custom" style="margin-top: 8px;">
                        <option value="Ya">Ya, Tarik & Tampilkan Data Galeri Sistem</option>
                        <option value="Tidak">Sembunyikan Galeri di Halaman Publik</option>
                    </select>
                    <small class="text-secondary" style="margin-top: 6px; display: block;">Jika <b>Ya</b>, galeri kegiatan yang diunggah di Workspace akan muncul secara dinamis di Landing Page.</small>
                </div>
            </div>
            
            <!-- Visi & Misi -->
            <div class="glass-card card-section">
                <div class="section-header" style="margin-bottom: 20px;">
                    <h4 class="section-title"><i data-lucide="target" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Visi & Misi Lingkungan</h4>
                </div>
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="card-label">Visi Utama</label>
                    <textarea id="web_visi" class="input-field" style="margin-top: 8px; min-height: 100px; padding: 12px 20px; border-radius: 16px; resize: vertical;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="card-label">Misi (Daftar Poin)</label>
                    <textarea id="web_misi" class="input-field" style="margin-top: 8px; min-height: 150px; padding: 12px 20px; border-radius: 16px; resize: vertical;"></textarea>
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
            <button class="button-primary" style="padding: 14px 32px;" onclick="saveWebSettings()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Pengaturan Umum</button>
        </div>
    </div>

    <!-- Tab Content: Menu Frontend -->
    <div id="info-menu" class="info-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <div>
                    <h4 class="section-title">Navigasi Landing Page</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Atur urutan dan link menu pada halaman depan website publik.</p>
                </div>
                <button class="button-primary button-sm" onclick="addMenu()"><i data-lucide="plus"></i> Tambah Menu</button>
            </div>
            <div class="table-responsive">
                <table class="modern-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width:60px;">Urutan</th>
                            <th>Nama Menu</th>
                            <th>URL / Link</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cms-menu-body">
                        <!-- Diisi via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Blog & Artikel -->
    <div id="info-blog" class="info-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <div>
                    <h4 class="section-title">Kelola Konten & Berita</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Buat pengumuman atau artikel publik untuk Landing Page.</p>
                </div>
                <button class="button-primary button-sm" onclick="addBlog()"><i data-lucide="edit-3"></i> Tulis Artikel</button>
            </div>
            <div class="grid-container-2-col" id="cms-blog-list" style="margin-top: 16px;">
                <!-- Diisi via JS -->
            </div>
        </div>
    </div>

    <!-- Tab Content: Transparansi Keuangan (Baru) -->
    <div id="info-transparansi" class="info-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header" style="margin-bottom: 24px;">
                <div>
                    <h4 class="section-title">Transparansi Keuangan Publik</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Tampilkan informasi atau laporan saldo kas kepada warga di Landing Page depan.</p>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="card-label">Judul Bagian Transparansi</label>
                <input type="text" id="web_transparansi_judul" class="input-field" style="margin-top: 8px;" placeholder="Cth: Transparansi Keuangan Warga">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="card-label">Deskripsi / Teks Pesan Laporan</label>
                <textarea id="web_transparansi_deskripsi" class="input-field" style="margin-top: 8px; min-height: 120px; padding: 12px 20px; border-radius: 16px; resize: vertical;" placeholder="Jelaskan ringkasan keuangan kas atau pesan pengantar..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="card-label" style="display: flex; justify-content: space-between;">Unggah Dokumen Laporan (PDF/Gambar) <span id="preview_web_transparansi_file"></span></label>
                <div class="upload-premium-container mt-2" style="border-radius: 12px; padding: 0;">
                    <input type="file" id="web_transparansi_file_input" accept=".pdf,image/*" class="upload-premium-input">
                    <div class="upload-premium-label" style="padding: 16px;">
                        <i data-lucide="upload" class="text-secondary" style="width: 24px; height: 24px; margin-bottom: 8px;"></i>
                        <span class="text-color font-bold" style="font-size: 0.85rem;">Pilih File Laporan Bulanan</span>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;"><button class="button-primary" style="padding: 14px 32px;" onclick="saveWebSettings()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Laporan Publik</button></div>
        </div>
    </div>

    <!-- Tab Content: Struktur Organisasi (Baru) -->
    <div id="info-struktur" class="info-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <div>
                    <h4 class="section-title">Struktur Organisasi / Pengurus</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Buat bagan susunan pengurus untuk ditampilkan di Landing Page publik. Angka tingkat yang sama akan sejajar.</p>
                </div>
                <button class="button-primary button-sm" onclick="addPengurus()"><i data-lucide="user-plus"></i> Tambah Pengurus</button>
            </div>
            <div class="table-responsive">
                <table class="modern-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width:80px;" class="text-center">Tingkat</th>
                            <th style="width:60px;">Foto</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cms-pengurus-body">
                        <!-- Diisi via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL MENU CMS -->
<div id="modal-cms-menu" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeInfoModal('modal-cms-menu')"><i data-lucide="x"></i></button>
        <h2 id="modal-menu-title" class="section-title" style="margin-bottom: 8px;">Tambah Menu</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Atur navigasi publik.</p>
        
        <input type="hidden" id="cms-menu-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Nama Menu</label>
            <input type="text" id="cms-menu-nama" class="input-field" style="margin-top: 8px;" placeholder="Cth: Tentang Kami">
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">URL Target</label>
            <input type="text" id="cms-menu-url" class="input-field" style="margin-top: 8px;" placeholder="Cth: #about atau /halaman-baru">
        </div>
        <div class="grid-container-2-col" style="gap: 12px; margin-bottom: 24px;">
            <div class="form-group">
                <label class="card-label">Nomor Urut</label>
                <input type="number" id="cms-menu-urutan" class="input-field" style="margin-top: 8px;" value="1">
            </div>
            <div class="form-group">
                <label class="card-label">Status</label>
                <select id="cms-menu-status" class="input-field select-custom" style="margin-top: 8px;">
                    <option value="Aktif">Tampil (Aktif)</option>
                    <option value="Draft">Sembunyi (Draft)</option>
                </select>
            </div>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveCmsMenu()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Menu</button>
    </div>
</div>

<!-- MODAL ARTIKEL BLOG -->
<div id="modal-cms-blog" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 700px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeInfoModal('modal-cms-blog')"><i data-lucide="x"></i></button>
        <h2 id="modal-blog-title" class="section-title" style="margin-bottom: 8px;">Tulis Artikel</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Sebarkan berita atau pengumuman ke publik.</p>
        
        <input type="hidden" id="cms-blog-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Judul Artikel</label>
            <input type="text" id="cms-blog-judul" class="input-field" style="margin-top: 8px; font-weight: 600; font-size: 1.1rem;">
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Konten / Isi Berita</label>
            <textarea id="cms-blog-konten" class="input-field" style="margin-top: 8px; min-height: 250px; padding: 16px 20px; border-radius: 16px; resize: vertical; line-height: 1.6;"></textarea>
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label">Status Tayang</label>
            <select id="cms-blog-status" class="input-field select-custom" style="margin-top: 8px; max-width: 200px;">
                <option value="Publish">Publish Sekarang</option>
                <option value="Draft">Simpan Draft</option>
            </select>
        </div>
        <div style="display: flex; justify-content: flex-end;">
            <button class="button-primary" style="padding: 12px 32px;" onclick="saveCmsBlog()"><i data-lucide="send" style="margin-right: 8px;"></i> Simpan & Terbitkan</button>
        </div>
    </div>
</div>

<!-- MODAL PENGURUS STRUKTUR -->
<div id="modal-cms-pengurus" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeInfoModal('modal-cms-pengurus')"><i data-lucide="x"></i></button>
        <h2 id="modal-pengurus-title" class="section-title" style="margin-bottom: 8px;">Tambah Pengurus</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Atur posisi anggota dalam struktur organisasi.</p>
        
        <input type="hidden" id="cms-pengurus-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Nama Lengkap</label>
            <input type="text" id="cms-pengurus-nama" class="input-field" style="margin-top: 8px;" placeholder="Cth: Budi Santoso">
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Jabatan</label>
            <input type="text" id="cms-pengurus-jabatan" class="input-field" style="margin-top: 8px;" placeholder="Cth: Ketua RT 01">
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label" style="display:flex; justify-content:space-between;">Tingkat (Urutan) <span class="text-emerald" style="font-size:0.7rem;"><i data-lucide="info" style="display:inline;width:12px;height:12px;"></i> 1 = Paling Atas</span></label>
            <input type="number" id="cms-pengurus-urutan" class="input-field" style="margin-top: 8px;" value="1" title="Pengurus dengan urutan angka yang sama akan disejajarkan dalam satu baris">
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label" style="display: flex; justify-content: space-between;">Foto Profil <span id="preview_pengurus_foto"></span></label>
            <div class="upload-premium-container mt-2" style="border-radius: 12px; padding: 0;">
                <input type="file" id="cms-pengurus-foto" accept="image/*" class="upload-premium-input">
                <div class="upload-premium-label" style="padding: 12px;"><i data-lucide="upload" class="text-secondary" style="width: 20px; height: 20px;"></i><span class="text-color" style="font-size: 0.75rem;">Unggah Foto</span></div>
            </div>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveCmsPengurus()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Anggota</button>
    </div>
</div>

<style>
.info-tab-content {
    animation: fadeIn 0.4s ease;
}
.text-accent { color: var(--accent-color); }

@media (max-width: 767px) {
    .info-tab-content { padding-bottom: 60px; } /* Ruang scroll ekstra di mobile */
    
    /* Memaksa Grid menjadi 1 Kolom (Stack) di Layar HP */
    #page-info .grid-container-2-col {
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
    }

    /* Mengubah Tabel Menu CMS Menjadi Card View yang Responsif */
    #info-menu .table-responsive { border: none !important; padding: 0 !important; }
    #info-menu .modern-table thead { display: none !important; }
    #cms-menu-body { display: block !important; width: 100% !important; }
    
    #cms-menu-body tr {
        display: flex; flex-direction: column;
        background: var(--card-bg);
        margin-bottom: 16px; border-radius: 16px; padding: 16px;
        box-shadow: 0 4px 12px var(--shadow-color);
        border: 1px solid var(--card-border);
    }
    #cms-menu-body td {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 8px 0 !important; text-align: right !important;
        border-bottom: 1px dashed var(--border-color);
        white-space: normal !important; word-wrap: break-word !important; gap: 12px;
    }
    #cms-menu-body td:last-child { border-bottom: none; justify-content: flex-end; }
    
    /* Injeksi Label Header ke samping data */
    #cms-menu-body td::before { font-weight: 600; color: var(--text-secondary-color); text-align: left; flex-shrink: 0; }
    #cms-menu-body td:nth-child(1)::before { content: "Urutan"; }
    #cms-menu-body td:nth-child(2)::before { content: "Nama Menu"; }
    #cms-menu-body td:nth-child(3)::before { content: "URL / Link Target"; }
    #cms-menu-body td:nth-child(4)::before { content: "Status"; }

    #cms-pengurus-body td::before { font-weight: 600; color: var(--text-secondary-color); text-align: left; flex-shrink: 0; }
    #cms-pengurus-body td:nth-child(1)::before { content: "Tingkat"; }
    #cms-pengurus-body td:nth-child(2)::before { content: "Foto"; }
    #cms-pengurus-body td:nth-child(3)::before { content: "Nama Lengkap"; }
    #cms-pengurus-body td:nth-child(4)::before { content: "Jabatan"; }

    /* Penyesuaian Modal (Popup) di Layar Kecil */
    #modal-cms-menu .glass-card, 
    #modal-cms-blog .glass-card {
        padding: 24px 20px !important;
        max-height: 90dvh;
        overflow-y: auto;
    }
}
</style>