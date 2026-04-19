<!-- Page: Informasi & CMS Website -->
<div id="page-info" class="page-content hidden page-section">
    
    <!-- Modern Header for Page -->
    <div class="page-header-premium mb-8">
        <h2 id="page-title" class="text-3xl font-bold text-slate-900 font-space">CMS Website</h2>
        <p id="page-subtitle" class="text-slate-500 font-medium">Kelola konten dan informasi publik website Si-SmaRT</p>
    </div>

    <style>
        .info-layout-wrapper {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 32px;
            align-items: start;
        }
        .info-sidebar-tabs {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: sticky;
            top: 24px;
        }
        .info-tab-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: 16px;
            background: transparent;
            color: #64748b;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            text-align: left;
            cursor: pointer;
        }
        .info-tab-btn:hover {
            background: rgba(16, 185, 129, 0.05);
            color: #10b981;
        }
        .info-tab-btn.active {
            background: #fff;
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .info-tab-btn i {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 1024px) {
            .info-layout-wrapper {
                grid-template-columns: 1fr;
            }
            .info-sidebar-tabs {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 12px;
                position: static;
                border-bottom: 1px solid #e2e8f0;
                margin-bottom: 24px;
                white-space: nowrap;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .info-sidebar-tabs::-webkit-scrollbar { display: none; }
            .info-tab-btn {
                padding: 10px 20px;
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="info-layout-wrapper">
        <!-- Vertical Tabs Sidebar -->
        <div class="info-sidebar-tabs">
            <button class="info-tab-btn active" onclick="switchInfoTab('info-umum', this)">
                <i data-lucide="settings"></i> <span>Pengaturan Umum</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-menu', this)">
                <i data-lucide="menu"></i> <span>Menu Frontend</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-blog', this)">
                <i data-lucide="newspaper"></i> <span>Blog & Artikel</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-slider', this)">
                <i data-lucide="layers"></i> <span>Slider Hero</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-wisata', this)">
                <i data-lucide="map-pin"></i> <span>Wisata Sekitar</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-transparansi', this)">
                <i data-lucide="pie-chart"></i> <span>Laporan Keuangan</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-struktur', this)">
                <i data-lucide="users"></i> <span>Struktur Organisasi</span>
            </button>
            <button class="info-tab-btn" onclick="switchInfoTab('info-penting', this)">
                <i data-lucide="alert-circle"></i> <span>Info Penting</span>
            </button>
        </div>

        <div class="info-main-content">
            <!-- Isi Tab Akan Dimuat di Sini (Konten di bawah tetap ada) -->

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
    
    <!-- Tab Content: Slider Hero (Parallax Slides) -->
    <div id="info-slider" class="info-tab-content hidden">
        <div class="glass-card card-section" style="margin-bottom: 24px;">
            <div class="section-header" style="margin-bottom: 20px;">
                <div>
                    <h4 class="section-title"><i data-lucide="layers" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Kelola 3 Slide Parallax Slider</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Sesuaikan gambar dan teks untuk ketiga slide di halaman depan.</p>
                </div>
            </div>

            <div class="grid-container-3-col" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <?php if(!isset($pdo)) require_once 'config/database.php'; ?>
<!-- Quill Rich Text Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                <?php for($i=1; $i<=3; $i++): ?>
                <div class="glass-card p-4" style="background: var(--bg-color-soft); border-radius: 20px;">
                    <h5 class="font-bold mb-3">Slide <?= $i ?></h5>
                    <div class="form-group mb-3">
                        <label class="card-label" style="display:flex; justify-content:space-between;">Gambar <span id="preview_web_slider_<?= $i ?>_image"></span></label>
                        <input type="file" id="web_slider_<?= $i ?>_image_file" accept="image/*" class="input-field file-input-modern mt-1">
                    </div>
                    <div class="form-group mb-3">
                        <label class="card-label">Judul (Title)</label>
                        <input type="text" id="web_slider_<?= $i ?>_title" class="input-field mt-1" placeholder="Cth: Ekologi Desa">
                    </div>
                    <div class="form-group mb-3">
                        <label class="card-label">Sub-Judul (Subtitle)</label>
                        <input type="text" id="web_slider_<?= $i ?>_subtitle" class="input-field mt-1" placeholder="Cth: Keasrian Alam">
                    </div>
                    <div class="form-group">
                        <label class="card-label">Deskripsi Singkat</label>
                        <textarea id="web_slider_<?= $i ?>_description" class="input-field mt-1" style="min-height: 80px;"></textarea>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button class="button-primary" style="padding: 14px 32px;" onclick="saveWebSettings()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Pengaturan Slider</button>
            </div>
        </div>
    </div>

    <!-- Tab Content: Wisata Sekitar -->
    <div id="info-wisata" class="info-tab-content hidden">
        <div class="glass-card card-section" style="margin-bottom: 24px;">
            <div class="section-header" style="margin-bottom: 20px;">
                <div>
                    <h4 class="section-title"><i data-lucide="map-pin" style="display:inline; width:20px; margin-right:8px;" class="text-accent"></i> Kelola 2 Destinasi Wisata Sekitar</h4>
                    <p class="text-secondary" style="font-size: 0.8rem;">Sesuaikan gambar dan info wisata alam di sekitar kawasan.</p>
                </div>
            </div>

            <div class="grid-container-2-col" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <?php for($i=1; $i<=2; $i++): ?>
                <div class="glass-card p-4" style="background: var(--bg-color-soft); border-radius: 20px;">
                    <h5 class="font-bold mb-3">Wisata <?= $i ?></h5>
                    <div class="form-group mb-3">
                        <label class="card-label" style="display:flex; justify-content:space-between;">Gambar <span id="preview_web_wisata_<?= $i ?>_image"></span></label>
                        <input type="file" id="web_wisata_<?= $i ?>_image_file" accept="image/*" class="input-field file-input-modern mt-1">
                    </div>
                    <div class="form-group mb-3">
                        <label class="card-label">Nama Tempat</label>
                        <input type="text" id="web_wisata_<?= $i ?>_title" class="input-field mt-1" placeholder="Cth: Mata Air Sodong">
                    </div>
                    <div class="form-group mb-3">
                        <label class="card-label">Kategori</label>
                        <input type="text" id="web_wisata_<?= $i ?>_category" class="input-field mt-1" placeholder="Cth: Ekologi">
                    </div>
                    <div class="form-group">
                        <label class="card-label">Deskripsi</label>
                        <textarea id="web_wisata_<?= $i ?>_description" class="input-field mt-1" style="min-height: 80px;"></textarea>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button class="button-primary" style="padding: 14px 32px;" onclick="saveWebSettings()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Pengaturan Wisata</button>
            </div>
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

    <!-- Tab Content: Informasi Penting Warga -->
    <div id="info-penting" class="info-tab-content hidden">
        <div class="glass-card card-section mb-6">
            <div class="section-header mb-8">
                <div>
                    <h4 class="section-title text-2xl"><i data-lucide="alert-circle" class="text-emerald-500 mr-2"></i> Kelola Informasi Penting</h4>
                    <p class="text-slate-500 font-medium">Atur nomor darurat dan informasi krusial yang muncul di halaman depan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="form-group">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 block">Judul Bagian</label>
                    <input type="text" id="web_info_penting_judul" class="input-field py-4" placeholder="Cth: Informasi Penting Warga">
                </div>
                <div class="form-group">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 block">Deskripsi Bagian</label>
                    <input type="text" id="web_info_penting_deskripsi" class="input-field py-4" placeholder="Cth: Pintasan informasi mendasar...">
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <?php for($i=1; $i<=4; $i++): ?>
                <div class="p-8 rounded-[2rem] bg-slate-50 border border-slate-100 relative group transition-all hover:shadow-xl hover:shadow-slate-200">
                    <div class="absolute top-6 right-8 text-4xl font-black text-slate-100 group-hover:text-emerald-50/50 transition-colors"><?= $i ?></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-emerald-600">
                                <i class="fas fa-hashtag text-xl"></i>
                            </div>
                            <h5 class="text-lg font-bold text-slate-800">Kartu Informasi <?= $i ?></h5>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 block">Ikon FontAwesome</label>
                                <div class="relative">
                                    <i class="fas fa-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                    <input type="text" id="web_info_item_<?= $i ?>_icon" class="input-field pl-12 py-3 text-sm" placeholder="fa-phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 block">Judul Kartu</label>
                                <input type="text" id="web_info_item_<?= $i ?>_title" class="input-field py-3 text-sm" placeholder="Nama Layanan">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 block">Isi Informasi (Deskripsi)</label>
                            <textarea id="web_info_item_<?= $i ?>_desc" class="input-field min-h-[100px] py-4 text-sm resize-none" placeholder="Masukkan nomor telepon atau info singkat..."></textarea>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div class="flex justify-end mt-12 pt-8 border-t border-slate-100">
                <button class="px-10 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-xl shadow-emerald-200 transition-all flex items-center gap-3" onclick="saveWebSettings()">
                    <i data-lucide="save"></i> SIMPAN INFORMASI PENTING
                </button>
            </div>
        </div>
    </div>
</div> <!-- info-main-content -->
</div> <!-- info-layout-wrapper -->

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

        <div class="grid-container-2-col" style="gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label class="card-label" style="display:flex; justify-content:space-between;">Thumbnail Photo <span id="preview_blog_thumbnail"></span></label>
                <div class="upload-premium-container mt-1" style="border-radius:12px; padding:0;">
                    <input type="file" id="cms-blog-thumbnail-file" accept="image/*" class="upload-premium-input">
                    <div class="upload-premium-label" style="padding:8px;"><i data-lucide="image" style="width:16px;"></i> <span style="font-size:0.7rem;">Unggah Sampul</span></div>
                </div>
            </div>
            <div class="form-group">
                <label class="card-label" style="display:flex; justify-content:space-between;">Video MP4 (Opsional) <span id="preview_blog_video"></span></label>
                <div class="upload-premium-container mt-1" style="border-radius:12px; padding:0;">
                    <input type="file" id="cms-blog-video-file" accept="video/mp4" class="upload-premium-input">
                    <div class="upload-premium-label" style="padding:8px;"><i data-lucide="video" style="width:16px;"></i> <span style="font-size:0.7rem;">Unggah Video</span></div>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Link YouTube (Integrasi)</label>
            <input type="text" id="cms-blog-youtube" class="input-field" style="margin-top: 8px;" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Konten Artikel</label>
            <div id="cms-blog-editor" style="height: 250px; background: white; border-radius: 0 0 16px 16px;"></div>
            <input type="hidden" id="cms-blog-konten">
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
    #page-info .grid-container-2-col,
    #page-info .grid-container-3-col {
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

    #cms-users-body td::before { font-weight: 600; color: var(--text-secondary-color); text-align: left; flex-shrink: 0; }
    #cms-users-body td:nth-child(1)::before { content: "Nama Lengkap"; }
    #cms-users-body td:nth-child(2)::before { content: "Username"; }
    #cms-users-body td:nth-child(3)::before { content: "Role"; }

    /* Penyesuaian Modal (Popup) di Layar Kecil */
    #modal-cms-menu .glass-card, 
    #modal-cms-blog .glass-card,
    #modal-cms-user .glass-card {
        padding: 24px 20px !important;
        max-height: 90dvh;
        overflow-y: auto;
    }
}

/* PREMIUM CARD STYLES */
.premium-card {
    --bg: #fff;
    --title-color: #fff;
    --title-color-hover: #000;
    --text-color: #666;
    --button-color: #eee;
    --button-color-hover: #ddd;
    background: var(--bg);
    border-radius: 2rem;
    padding: 0.5rem;
    width: 100%;
    max-width: 20rem;
    height: 30rem;
    overflow: clip;
    position: relative;
    font-family: var(--font-sans);
    transition: transform 0.3s ease;
}

.premium-card.dark {
    --bg: #222;
    --title-color: #fff;
    --title-color-hover: #fff;
    --text-color: #ccc;
    --button-color: #555;
    --button-color-hover: #444;
}

.premium-card::before {
    content: "";
    position: absolute;
    width: calc(100% - 1rem);
    height: 30%;
    bottom: 0.5rem;
    left: 0.5rem;
    mask: linear-gradient(#0000, #000f 80%);
    backdrop-filter: blur(1rem);
    border-radius: 0 0 1.5rem 1.5rem;
    translate: 0 0;
    transition: translate 0.25s;
    z-index: 1;
}

.premium-card > img, .premium-card > video {
    max-width: 100%;
    aspect-ratio: 2 / 3;
    object-fit: cover;
    object-position: 50% 5%;
    border-radius: 1.5rem;
    display: block;
    transition: aspect-ratio 0.25s, object-position 0.5s;
    width: 100%;
    height: 100%;
}

.premium-card > section {
    margin: 1rem;
    height: calc(33.3333% - 1rem);
    display: flex;
    flex-direction: column;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 2;
}

.premium-card h2 {
    margin: 0;
    margin-block-end: 1rem;
    font-size: 1.5rem;
    opacity: 0;
    translate: 0 -200%;
    color: var(--title-color);
    transition: color 0.5s, margin-block-end 0.25s, opacity 1s, translate 0.25s;
}

.premium-card p {
    font-size: 0.95rem;
    line-height: 1.3;
    color: var(--text-color);
    opacity: 0;
    margin: 0;
    translate: 0 100%;
    transition: margin-block-end 0.25s, opacity 1s 0.2s, translate 0.25s 0.2s;
}

.premium-card > section > div {
    flex: 1;
    align-items: flex-end;
    display: flex;
    justify-content: space-between;
    opacity: 0;
    transition: translate 0.25s 0.2s, opacity 1s;
}

.premium-card .tag {
    align-self: center;
    color: var(--title-color-hover);
    font-weight: bold;
    font-size: 0.7rem;
}

.premium-card button {
    border: 1px solid #0000;
    border-radius: 1.25rem 1.25rem 1.5rem 1.25rem;
    font-size: 1rem;
    padding: 1rem 1.5rem 1rem 2.75rem;
    translate: 1rem;
    background: var(--button-color);
    transition: background 0.33s, color 0.33s;
    outline-offset: 2px;
    position: relative;
    color: var(--title-color-hover);
    width: 8.5rem;
    text-align: right;
    cursor: pointer;
}

.premium-card button::before, .premium-card button::after {
    content: "";
    background: currentcolor;
    position: absolute;
    border-radius: 1rem;
    transition: all 0.25s ease-out;
}

.premium-card button::before {
    width: 0.85rem;
    height: 0.1rem;
    top: 50%;
    left: 1.33rem;
}

.premium-card button::after {
    width: 0.85rem;
    height: 0.1rem;
    top: 50%;
    left: 1.33rem;
    rotate: 90deg;
}

.premium-card:hover, .premium-card:focus-within {
    &::before { translate: 0 100%; }
    > img, > video { aspect-ratio: 1 / 1; object-position: 50% 10%; height: 60%; }
    > section {
        h2, p { translate: 0 0; margin-block-end: 0.5rem; opacity: 1; }
        h2 { color: var(--title-color-hover); }
        > div { translate: 0 0; opacity: 1; transition: translate 0.25s 0.25s, opacity 0.5s 0.25s; }
    }
}
</style>
</div> <!-- Akhir dari #page-info -->
