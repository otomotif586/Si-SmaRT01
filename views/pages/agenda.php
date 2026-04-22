<!-- Page: Agenda & Laporan Warga -->
<div id="page-agenda" class="page-content hidden page-section">
    
    <!-- Header Section -->
    <div class="page-header-premium mb-8">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 font-space">Agenda & Pengumuman</h2>
                <p class="text-slate-500 font-medium">Kelola kegiatan lingkungan dan pantau laporan masalah dari warga.</p>
            </div>
            <button class="button-primary" onclick="openFormAgenda()" style="padding: 12px 24px;">
                <i data-lucide="plus-circle" style="margin-right: 8px;"></i> Buat Baru
            </button>
        </div>
    </div>

    <!-- Sub-Navigation for Agenda vs Laporan -->
    <div class="sub-nav-tabs" style="margin-bottom: 24px; display: flex; gap: 12px;">
        <button class="sub-nav-tab active" onclick="switchSubTab(this, 'sub-tab-agenda')">
            <i data-lucide="calendar"></i> Agenda Kegiatan
        </button>
        <button class="sub-nav-tab" onclick="switchSubTab(this, 'sub-tab-laporan')">
            <i data-lucide="flag"></i> Laporan Masalah
        </button>
    </div>

    <!-- Tab Content: Agenda -->
    <div id="sub-tab-agenda" class="sub-tab-content">
        <!-- Summary Stats Cards -->
        <div class="summary-3-grid" style="margin-bottom: 24px;">
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                    <i data-lucide="calendar-days"></i>
                </div>
                <p class="card-label">Total Agenda</p>
                <h3 class="card-value" id="sum-agenda-total">0</h3>
            </div>
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i data-lucide="check-check"></i>
                </div>
                <p class="card-label">Status Selesai</p>
                <h3 class="card-value text-emerald" id="sum-agenda-selesai">0 Selesai</h3>
            </div>
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">
                    <i data-lucide="clock"></i>
                </div>
                <p class="card-label">Mendatang</p>
                <h3 class="card-value text-orange" id="sum-agenda-upcoming">0 Aktif</h3>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="glass-card" style="padding: 16px 20px; margin-bottom: 24px; display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div class="input-with-icon" style="flex: 1; min-width: 250px;">
                <i data-lucide="search"></i>
                <input type="text" id="search-agenda-input" class="input-field" placeholder="Cari judul atau keterangan agenda..." oninput="filterAgendaList()">
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <label class="text-xs font-bold text-slate-400 uppercase">Status:</label>
                <select id="filter-status-agenda" class="input-field select-custom" style="width: 160px;" onchange="filterAgendaList()">
                    <option value="">Semua Status</option>
                    <option value="Direncanakan">Direncanakan</option>
                    <option value="Berjalan">Berjalan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- Agenda List Container -->
        <div id="agenda-list-container">
            <!-- Loaded via AJAX -->
        </div>

        <!-- Pagination -->
        <div id="agenda-pagination" class="flex justify-between items-center mt-8 py-4 border-t border-slate-100" style="display: none;">
            <span id="agenda-page-info" class="text-secondary text-sm">Menampilkan 1-10 dari 10</span>
            <div class="flex gap-2">
                <button onclick="prevPageAgenda()" class="button-secondary button-sm">Sebelumnya</button>
                <button onclick="nextPageAgenda()" class="button-secondary button-sm">Berikutnya</button>
            </div>
        </div>
    </div>

    <!-- Tab Content: Laporan -->
    <div id="sub-tab-laporan" class="sub-tab-content hidden">
        <!-- Summary Stats Cards -->
        <div class="summary-3-grid" style="margin-bottom: 24px;">
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #ef4444; background: rgba(239, 68, 68, 0.1);">
                    <i data-lucide="alert-triangle"></i>
                </div>
                <p class="card-label">Total Laporan</p>
                <h3 class="card-value" id="sum-laporan-total">0</h3>
            </div>
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i data-lucide="shield-check"></i>
                </div>
                <p class="card-label">Tuntas</p>
                <h3 class="card-value text-emerald" id="sum-laporan-selesai">0 Selesai</h3>
            </div>
            <div class="glass-card-deluxe">
                <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                    <i data-lucide="info"></i>
                </div>
                <p class="card-label">Sedang Proses</p>
                <h3 class="card-value text-blue" id="sum-laporan-proses">0 Diproses</h3>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="glass-card" style="padding: 16px 20px; margin-bottom: 24px; display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div class="input-with-icon" style="flex: 1; min-width: 250px;">
                <i data-lucide="search"></i>
                <input type="text" id="search-laporan-input" class="input-field" placeholder="Cari laporan..." oninput="filterLaporanList()">
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <label class="text-xs font-bold text-slate-400 uppercase">Status:</label>
                <select id="filter-status-laporan" class="input-field select-custom" style="width: 160px;" onchange="filterLaporanList()">
                    <option value="">Semua Status</option>
                    <option value="Baru">Baru</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
        </div>

        <!-- Laporan List Container -->
        <div id="laporan-list-container">
            <!-- Loaded via AJAX -->
        </div>

        <!-- Pagination -->
        <div id="laporan-pagination" class="flex justify-between items-center mt-8 py-4 border-t border-slate-100" style="display: none;">
            <span id="laporan-page-info" class="text-secondary text-sm">Menampilkan 1-10 dari 10</span>
            <div class="flex gap-2">
                <button onclick="prevPageLaporan()" class="button-secondary button-sm">Sebelumnya</button>
                <button onclick="nextPageLaporan()" class="button-secondary button-sm">Berikutnya</button>
            </div>
        </div>
    </div>

</div>

<!-- DRAWER FORM AGENDA -->
<div id="drawer-agenda" class="rw-drawer hidden">
    <div class="rw-drawer-overlay" onclick="closeFormAgendaDrawer()"></div>
    <div class="rw-drawer-content glass-card">
        <div class="rw-drawer-header">
            <h3 id="drawer-agenda-title">Tambah Agenda</h3>
            <button onclick="closeFormAgendaDrawer()" class="rw-drawer-close"><i data-lucide="x"></i></button>
        </div>
        <div class="rw-drawer-body">
            <form id="form-agenda" onsubmit="event.preventDefault(); simpanAgenda();">
                <input type="hidden" id="agenda_id" value="0">
                <div class="form-group mb-4">
                    <label class="card-label">Judul Agenda</label>
                    <input type="text" id="agenda_judul" class="input-field mt-2" required placeholder="Cth: Rapat RT Bulanan">
                </div>
                <div class="grid-container-2-col gap-4 mb-4">
                    <div class="form-group">
                        <label class="card-label">Waktu Kegiatan</label>
                        <input type="datetime-local" id="agenda_tanggal" class="input-field mt-2" required>
                    </div>
                    <div class="form-group">
                        <label class="card-label">Estimasi Biaya (Rp)</label>
                        <input type="number" id="agenda_biaya" class="input-field mt-2" placeholder="0">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label class="card-label">Keterangan / Deskripsi</label>
                    <textarea id="agenda_keterangan" class="input-field mt-2" style="min-height: 120px; padding-top: 12px;" required placeholder="Jelaskan detail agenda..."></textarea>
                </div>
                <div class="form-group mb-6">
                    <label class="card-label">Status Agenda</label>
                    <select id="agenda_status" class="input-field mt-2" onchange="toggleAgendaGallery(this.value)">
                        <option value="Direncanakan">Direncanakan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <!-- Gallery Section (Hanya muncul jika Selesai) -->
                <div id="agenda_gallery_section" class="hidden mb-6">
                    <label class="card-label mb-2 block">Dokumentasi (Foto/Video)</label>
                    <div class="flex flex-wrap gap-2 mb-2" id="agenda_existing_gallery"></div>
                    <div class="upload-premium-container">
                        <input type="file" id="agenda_gallery_files" multiple accept="image/*,video/*" class="upload-premium-input" onchange="previewAgendaGallery(this)">
                        <div class="upload-premium-label">
                            <i data-lucide="image-plus"></i>
                            <span>Tambah Foto/Video Selesai</span>
                        </div>
                    </div>
                    <div id="agenda_gallery_preview" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <!-- Lampiran Section -->
                <div class="form-group mb-8">
                    <label class="card-label mb-2 block">Lampiran Dokumen (PDF/Doc)</label>
                    <div id="agenda_existing_lampiran" class="flex flex-col gap-2 mb-2"></div>
                    <input type="file" id="agenda_lampiran_files" multiple class="input-field" style="padding: 10px;">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeFormAgendaDrawer()" class="button-secondary flex-1">Batal</button>
                    <button type="submit" class="button-primary flex-1">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DRAWER FORM LAPORAN -->
<div id="drawer-laporan" class="rw-drawer hidden">
    <div class="rw-drawer-overlay" onclick="closeFormLaporanDrawer()"></div>
    <div class="rw-drawer-content glass-card">
        <div class="rw-drawer-header">
            <h3 id="drawer-laporan-title">Edit Laporan</h3>
            <button onclick="closeFormLaporanDrawer()" class="rw-drawer-close"><i data-lucide="x"></i></button>
        </div>
        <div class="rw-drawer-body">
            <form id="form-laporan" onsubmit="event.preventDefault(); simpanLaporan();">
                <input type="hidden" id="laporan_id" value="0">
                <div class="form-group mb-4">
                    <label class="card-label">Judul Laporan</label>
                    <input type="text" id="laporan_judul" class="input-field mt-2" required>
                </div>
                <div class="form-group mb-4">
                    <label class="card-label">Tanggal Laporan</label>
                    <input type="datetime-local" id="laporan_tanggal" class="input-field mt-2" required>
                </div>
                <div class="form-group mb-4">
                    <label class="card-label">Deskripsi / Kronologi</label>
                    <textarea id="laporan_keterangan" class="input-field mt-2" style="min-height: 120px; padding-top: 12px;" required></textarea>
                </div>
                <div class="grid-container-2-col gap-4 mb-6">
                    <div class="form-group">
                        <label class="card-label">Status</label>
                        <select id="laporan_status" class="input-field mt-2" onchange="toggleLaporanSelesai(this.value)">
                            <option value="Baru">Baru</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div id="laporan_tanggal_selesai_section" class="form-group hidden">
                        <label class="card-label">Waktu Selesai</label>
                        <input type="datetime-local" id="laporan_tanggal_selesai" class="input-field mt-2">
                    </div>
                </div>

                <div class="form-group mb-8">
                    <label class="card-label mb-2 block">Lampiran Bukti</label>
                    <div id="laporan_existing_lampiran" class="flex flex-wrap gap-2 mb-2"></div>
                    <input type="file" id="laporan_lampiran_files" multiple class="input-field" style="padding: 10px;">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeFormLaporanDrawer()" class="button-secondary flex-1">Batal</button>
                    <button type="submit" class="button-primary flex-1">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom Styles for Agenda Page */
.agenda-card, .laporan-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 24px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px var(--shadow-color);
}
.agenda-card:hover, .laporan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px var(--shadow-color);
    border-color: var(--accent-color-soft);
}
.agenda-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 12px;
    margin-top: 16px;
}
.gallery-item {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 12px;
    cursor: pointer;
}
.sub-tab-content {
    animation: fadeIn 0.4s ease;
}

/* Drawer UI */
.rw-drawer {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    justify-content: flex-end;
    visibility: hidden;
    transition: visibility 0.4s;
}
.rw-drawer.drawer-active { visibility: visible; }
.rw-drawer-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(4px);
    opacity: 0;
    transition: opacity 0.4s;
}
.rw-drawer.drawer-active .rw-drawer-overlay { opacity: 1; }
.rw-drawer-content {
    position: relative;
    width: 100%;
    max-width: 500px;
    height: 100%;
    background: var(--bg-color);
    box-shadow: -10px 0 30px rgba(0,0,0,0.1);
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}
.rw-drawer.drawer-active .rw-drawer-content { transform: translateX(0); }
.rw-drawer-header {
    padding: 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.rw-drawer-header h3 { font-size: 1.25rem; font-weight: 700; margin: 0; }
.rw-drawer-body { padding: 24px; overflow-y: auto; flex: 1; }

.bg-emerald-light { background: rgba(16, 185, 129, 0.1); }
.bg-blue-light { background: rgba(59, 130, 246, 0.1); }
.bg-orange-light { background: rgba(245, 158, 11, 0.1); }
.bg-red-light { background: rgba(239, 68, 68, 0.1); }
</style>
