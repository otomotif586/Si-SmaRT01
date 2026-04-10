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
    </div>

    <!-- Tab Content: Pengaturan Umum (Visi, Misi, Alamat) -->
    <div id="info-umum" class="info-tab-content active-tab">
        <div class="grid-container-2-col">
            <!-- Profil & Kontak -->
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;"><i data-lucide="building" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Profil & Kontak Website</h4>
                
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
            
            <!-- Visi & Misi -->
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;"><i data-lucide="target" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Visi & Misi Lingkungan</h4>
                
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

<style>
.info-tab-content {
    animation: fadeIn 0.4s ease;
}
.text-accent { color: var(--accent-color); }

@media (max-width: 767px) {
    .info-tab-content { padding-bottom: 40px; } /* Ruang scroll ekstra di mobile */
}
</style>