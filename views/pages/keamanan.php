<!-- Page: Info -->
<div id="page-keamanan" class="page-content hidden page-section">
    
    <!-- Sub Navigation Tabs -->
    <div class="sub-nav-tabs km-tabs-row">
        <button class="sub-nav-tab sub-nav-tab-fill active" onclick="switchKeamananTab('km-ringkasan', this)">
            <i data-lucide="layout-dashboard"></i> Ringkasan
        </button>
        <button class="sub-nav-tab sub-nav-tab-fill" onclick="switchKeamananTab('km-jadwal', this)">
            <i data-lucide="calendar"></i> Jadwal
        </button>
        <button class="sub-nav-tab sub-nav-tab-fill" onclick="switchKeamananTab('km-master', this)">
            <i data-lucide="users"></i> Master Satpam
        </button>
        <button class="sub-nav-tab sub-nav-tab-fill" onclick="switchKeamananTab('km-laporan', this)">
            <i data-lucide="clipboard-list"></i> Laporan
        </button>
        <button class="sub-nav-tab sub-nav-tab-fill" onclick="switchKeamananTab('km-izin', this)">
            <i data-lucide="user-minus"></i> Izin/Cuti
        </button>
    </div>

    <!-- Tab Content: Ringkasan -->
    <div id="km-ringkasan" class="km-tab-content active-tab">
        <div class="summary-3-grid mb-24">
            <div class="glass-card-deluxe stagger-item stagger-delay-1">
                <div class="card-icon-deluxe icon-deluxe-blue">
                    <i data-lucide="user-check"></i>
                </div>
                <p class="card-label">Petugas Aktif</p>
                <h3 id="km-current-guard" class="card-value text-color text-12rem">3 Personel</h3>
                <div class="card-sub-info">Personel siap layanan warga</div>
            </div>
            
            <div class="glass-card-deluxe stagger-item stagger-delay-2">
                <div class="card-icon-deluxe icon-deluxe-amber">
                    <i data-lucide="alert-circle"></i>
                </div>
                <p class="card-label">Aduan Baru</p>
                <h3 id="km-unread-reports" class="card-value text-orange card-value-lg">2</h3>
                <div class="card-sub-info">Butuh review hari ini</div>
            </div>

            <div class="glass-card-deluxe stagger-item stagger-delay-3">
                <div class="card-icon-deluxe icon-deluxe-emerald">
                    <i data-lucide="shield"></i>
                </div>
                <p class="card-label">Status Info</p>
                <h3 class="card-value text-emerald card-value-lg">Aman</h3>
                <div class="card-sub-info">Informasi warga terpantau</div>
            </div>
        </div>

        <div class="panic-button-container">
            <div class="panic-button-wrapper">
                <button class="panic-button" onclick="triggerPanic()">
                    <i data-lucide="bell-ring"></i>
                    <span>PANIC BUTTON</span>
                </button>
                <div class="panic-badge">AKTIF</div>
            </div>
            <p class="panic-description panic-description-soft">
                Satu sentuhan untuk mengirim sinyal darurat ke daftar kontak prioritas & seluruh tim keamanan.
            </p>
            <button class="button-secondary button-sm panic-settings-btn" onclick="openPanicSettings()">
                <i data-lucide="settings" class="icon-14"></i> Pengaturan Kontak Darurat
            </button>
        </div>

        <div class="glass-card card-section mt-32">
            <div class="section-header mb-20">
                <h4 class="section-title section-title-sm">Aktifitas Terbaru</h4>
                <button class="button-link" onclick="switchKeamananTab('km-laporan', document.querySelectorAll('.sub-nav-tab')[3])">Lihat Semua</button>
            </div>
            <div id="km-recent-activity" class="report-list">
                <!-- Activities will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Tab Content: Jadwal -->
    <div id="km-jadwal" class="km-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <h4 class="section-title">Jadwal Shift Satpam</h4>
                <button class="button-primary button-sm" onclick="addJadwal()"><i data-lucide="plus"></i> Tambah Jadwal</button>
            </div>
            <div class="table-responsive">
                <table class="modern-table w-full">
                    <thead>
                        <tr>
                            <th>Hari/Tgl</th>
                            <th>Shift Pagi (08:00-20:00)</th>
                            <th>Shift Malam (20:00-08:00)</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="km-schedule-body">
                        <!-- Schedule rows will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Master Satpam -->
    <div id="km-master" class="km-tab-content hidden">
        <div class="section-header mt-10">
            <h4 class="section-title">Personel Keamanan</h4>
            <button class="button-primary" onclick="addSatpam()">
                <i data-lucide="plus"></i> Tambah Personel
            </button>
        </div>
        <div id="km-guard-list" class="grid-container mt-20">
            <!-- Guard cards will be loaded here -->
        </div>
    </div>

    <!-- Tab Content: Laporan Info -->
    <div id="km-laporan" class="km-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <h4 class="section-title">Aduan Warga & Informasi Pengurus</h4>
                <button class="button-primary" onclick="addIncident()">
                    <i data-lucide="plus"></i> Laporan Baru
                </button>
            </div>
            <div class="table-responsive">
                <table class="modern-table km-incident-table w-full">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Dilaporkan Oleh</th>
                            <th>Kejadian/Tamu</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Portal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="km-incident-body">
                        <!-- Incidents will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div id="km-incident-pagination" class="km-incident-pagination">
                <span id="km-incident-pagination-info" class="text-secondary text-[0.8rem]">Menampilkan 0 data</span>
                <div class="flex-gap-8">
                    <button type="button" id="km-incident-prev" class="button-secondary button-sm" onclick="changeKmIncidentPage(-1)">Sebelumnya</button>
                    <button type="button" id="km-incident-next" class="button-secondary button-sm" onclick="changeKmIncidentPage(1)">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Izin & Cuti -->
    <div id="km-izin" class="km-tab-content hidden">
        <div class="grid-container-2-col">
            <div class="glass-card card-section">
                <div class="section-header section-header-mb20-between">
                    <h4 class="section-title m-0">Pengajuan Izin/Cuti</h4>
                    <button class="button-primary button-sm" onclick="addIzin()"><i data-lucide="plus"></i> Ajukan Izin</button>
                </div>
                <div class="report-list" id="km-leave-requests">
                    <!-- Requests will be loaded here -->
                </div>
            </div>
            <div class="glass-card card-section">
                <h4 class="section-title mb-20">Statistik Absensi</h4>
                <div id="km-attendance-stats">
                    <!-- Stats will be loaded here -->
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Emergency Broadcast Modal -->
<div id="modal-panic-broadcast" class="modal-overlay hidden overlay-z10020">
    <div class="modal-content glass-card-deluxe panic-modal-shell">
        <div class="panic-button panic-button-static">
            <i data-lucide="send"></i>
        </div>
        <h3 class="section-title text-15rem mb-12">Sinyal Darurat!</h3>
        <p class="text-secondary mb-32">Pilih jalur pengiriman pesan darurat ke seluruh tim & warga:</p>
        
        <div class="grid-container" id="panic-recipient-list">
            <!-- Dynamic recipient contact buttons -->
        </div>

        <div class="top-divider mt-32 pt-24">
            <button class="button-secondary w-full justify-center" onclick="closeKmModal('modal-panic-broadcast')">Batalkan Sinyal</button>
        </div>
    </div>
</div>

<!-- Panic Settings Modal -->
<div id="modal-panic-settings" class="modal-overlay hidden overlay-z10020">
    <div class="modal-content glass-card-deluxe modal-max-500">
        <div class="section-header mb-24">
            <h3 class="section-title">Kontak Darurat</h3>
            <button class="button-link" onclick="closeKmModal('modal-panic-settings')"><i data-lucide="x"></i></button>
        </div>
        <p class="text-secondary text-085 mb-20">Nomor di bawah ini akan dihubungi saat Panic Button ditekan.</p>
        
        <div id="panic-numbers-container" class="stack-gap12 mb-24">
            <!-- Inputs will be generated here -->
        </div>

        <button class="button-secondary w-full mb-12 justify-center" onclick="addPanicNumber()">
            <i data-lucide="plus-circle"></i> Tambah Nomor Baru
        </button>
        
        <div class="flex gap-3">
            <button class="button-primary flex-1 justify-center" onclick="savePanicSettings()"><i data-lucide="save" class="mr-[6px]"></i> Simpan Pengaturan</button>
        </div>
    </div>
</div>

<!-- MODAL CRUD KEAMANAN -->

<!-- 1. Modal Master Satpam -->
<div id="modal-satpam" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="closeKmModal('modal-satpam')"><i data-lucide="x"></i></button>
        <h2 id="modal-satpam-title" class="section-title mb-8">Tambah Personel</h2>
        <p class="text-secondary modal-desc">Kelola data master petugas keamanan.</p>
        
        <input type="hidden" id="km-satpam-id" value="0">
        <div class="form-group mb-16">
            <label class="card-label">Nama Lengkap</label>
            <input type="text" id="km-satpam-nama" class="input-field mt-8">
        </div>
        <div class="form-group mb-16">
            <label class="card-label">Nomor HP / WA</label>
            <input type="text" id="km-satpam-nohp" class="input-field mt-8">
        </div>
        <div class="form-group mb-24">
            <label class="card-label">Status</label>
            <select id="km-satpam-status" class="input-field select-custom mt-8">
                <option value="Aktif">Aktif Bertugas</option>
                <option value="Nonaktif">Nonaktif / Berhenti</option>
            </select>
        </div>
        <button class="button-primary button-full-center" onclick="saveSatpam()"><i data-lucide="save" class="mr-2"></i> Simpan Data</button>
    </div>
</div>

<!-- 2. Modal Jadwal Shift -->
<div id="modal-jadwal" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="closeKmModal('modal-jadwal')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Atur Jadwal Shift</h2>
        <p class="text-secondary modal-desc">Penugasan personel untuk patroli lingkungan.</p>
        
        <input type="hidden" id="km-jadwal-id" value="0">
        <div class="form-group mb-16">
            <label class="card-label">Personel Keamanan</label>
            <select id="km-jadwal-satpam" class="input-field select-custom mt-8"></select>
        </div>
        <div class="form-group mb-16">
            <label class="card-label">Tanggal Bertugas</label>
            <input type="date" id="km-jadwal-tanggal" class="input-field mt-8 input-left-20">
        </div>
        <div class="form-group mb-24">
            <label class="card-label">Waktu Shift</label>
            <select id="km-jadwal-shift" class="input-field select-custom mt-8">
                <option value="Pagi">Pagi (08:00 - 20:00)</option>
                <option value="Malam">Malam (20:00 - 08:00)</option>
            </select>
        </div>
        <button class="button-primary button-full-center" onclick="saveJadwal()"><i data-lucide="save" class="mr-2"></i> Simpan Jadwal</button>
    </div>
</div>

<!-- 3. Modal Laporan (Incident) -->
<div id="modal-lap-keamanan" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-std-shell">
        <button class="modal-close-btn modal-close-top-right" onclick="closeKmModal('modal-lap-keamanan')"><i data-lucide="x"></i></button>
        <h2 id="modal-lap-title" class="section-title mb-8">Laporan Baru</h2>
        <p class="text-secondary modal-desc">Buat informasi/aduaan sebagai Pengurus untuk ditindaklanjuti dan ditampilkan di portal.</p>
        
        <input type="hidden" id="km-lap-id" value="0">
        <div class="form-group mb-16">
            <label class="card-label">Judul Kejadian / Tamu</label>
            <input type="text" id="km-lap-judul" class="input-field mt-8">
        </div>
        <div class="grid-container-2-col gap16-mb16">
            <div class="form-group">
                <label class="card-label">Waktu</label>
                <input type="datetime-local" id="km-lap-waktu" class="input-field mt-8 input-left-20">
            </div>
            <div class="form-group">
                <label class="card-label">Lokasi / Blok</label>
                <input type="text" id="km-lap-lokasi" class="input-field mt-8" placeholder="Cth: Gerbang Depan">
            </div>
        </div>
        <div class="form-group mb-16">
            <label class="card-label">Deskripsi Lengkap</label>
            <textarea id="km-lap-deskripsi" class="input-field textarea-premium minh-80"></textarea>
        </div>
        <div class="form-group mb-16">
            <label class="card-label">Lampiran File (Opsional)</label>
            <input type="file" id="km-lap-file" class="input-field file-upload-compact" accept="image/*,video/*,.pdf,.doc,.docx,.xlsx,.xls,.zip,.rar">
            <small class="text-secondary file-upload-note">Dukung gambar, video, dan dokumen. Maksimal 8MB.</small>
        </div>
        <div class="form-group mb-24">
            <label class="card-label">Status Penanganan</label>
            <select id="km-lap-status" class="input-field select-custom mt-8">
                <option value="Baru">Baru / Menunggu</option>
                <option value="Diproses">Sedang Ditangani</option>
                <option value="Selesai">Selesai / Aman</option>
            </select>
        </div>
        <button class="button-primary button-full-center" onclick="saveLaporanKeamanan()"><i data-lucide="save" class="mr-2"></i> Simpan Laporan</button>
    </div>
</div>

<!-- Modal Detail Laporan Keamanan -->
<div id="modal-detail-lap-keamanan" class="modal-overlay hidden z-10025-force">
    <div class="glass-card modal-std-shell">
        <button class="modal-close-btn modal-close-top-right" onclick="closeKmModal('modal-detail-lap-keamanan')"><i data-lucide="x"></i></button>
        <h3 class="section-title title-row"><i data-lucide="file-text" class="text-blue"></i> Detail Kejadian</h3>
        <div id="km-detail-lap-content" class="hide-scrollbar scroll-max-60vh"></div>
    </div>
</div>

<!-- 4. Modal Pengajuan Izin / Cuti -->
<div id="modal-izin" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="closeKmModal('modal-izin')"><i data-lucide="x"></i></button>
        <h2 id="modal-izin-title" class="section-title mb-8">Formulir Izin</h2>
        <p class="text-secondary modal-desc">Pengajuan ketidakhadiran (Cuti/Izin/Sakit).</p>
        
        <input type="hidden" id="km-izin-id" value="0">
        <div class="form-group mb-16">
            <label class="card-label">Pilih Personel</label>
            <select id="km-izin-satpam" class="input-field select-custom mt-8"></select>
        </div>
        <div class="grid-container-2-col gap16-mb16">
            <div class="form-group">
                <label class="card-label">Mulai Tanggal</label>
                <input type="date" id="km-izin-mulai" class="input-field date-input-compact">
            </div>
            <div class="form-group">
                <label class="card-label">Sampai Tanggal</label>
                <input type="date" id="km-izin-selesai" class="input-field date-input-compact">
            </div>
        </div>
        <div class="form-group mb-16">
            <label class="card-label">Jenis Pengajuan</label>
            <select id="km-izin-jenis" class="input-field select-custom mt-8">
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin Pribadi</option>
                <option value="Cuti">Cuti Tahunan</option>
            </select>
        </div>
        <div class="form-group mb-24">
            <label class="card-label">Keterangan / Alasan</label>
            <textarea id="km-izin-ket" class="input-field textarea-premium minh-80"></textarea>
        </div>
        <div class="form-group hidden mb-24" id="km-izin-status-group">
            <label class="card-label">Status Persetujuan</label>
            <select id="km-izin-status" class="input-field select-custom mt-8">
                <option value="Pending">Menunggu (Pending)</option>
                <option value="Disetujui">Disetujui</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>
        <button class="button-primary button-full-center" onclick="saveIzin()"><i data-lucide="send" class="mr-2"></i> Kirim Pengajuan</button>
    </div>
</div>

<style>
.km-tab-content {
    animation: fadeIn 0.4s ease;
}
.km-tabs-row { margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 8px; }
#page-keamanan .sub-nav-tab-fill { flex: 1 1 auto; justify-content: center; white-space: nowrap; }
.icon-deluxe-blue { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
.icon-deluxe-amber { color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
.icon-deluxe-emerald { color: #10b981; background: rgba(16, 185, 129, 0.1); }
.text-12rem { font-size: 1.2rem; }
.section-title-sm { font-size: 1rem; }
.text-15rem { font-size: 1.5rem; }
.text-085 { font-size: 0.85rem; }
.m-0 { margin: 0; }
.mt-10 { margin-top: 10px; }
.mt-20 { margin-top: 20px; }
.mt-32 { margin-top: 32px; }
.mb-12 { margin-bottom: 12px; }
.mb-20 { margin-bottom: 20px; }
.mb-24 { margin-bottom: 24px; }
.icon-14 { width: 14px; height: 14px; }
.mr-[6px] { margin-right: 6px; }
.panic-description-soft { margin-top: 24px; font-size: 0.85rem; opacity: 0.8; max-width: 400px; margin-left: auto; margin-right: auto; }
.panic-settings-btn { margin: 16px auto 0; display: flex; align-items: center; gap: 6px; border-radius: 12px; }
.km-incident-pagination {
    display: none;
    margin-top: 14px;
    border-top: 1px dashed var(--border-color);
    padding-top: 12px;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
#page-keamanan .section-header-mb20-between { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
.panic-modal-shell { max-width: 450px; text-align: center; padding: 40px; }
.panic-button-static { width: 100px; height: 100px; margin: 0 auto 24px; cursor: default; }
.top-divider { border-top: 1px solid var(--border-color); }
.pt-24 { padding-top: 24px; }
.justify-center { justify-content: center; }
.modal-max-500 { max-width: 500px; }
.stack-gap12 { display: flex; flex-direction: column; gap: 12px; }
.gap16-mb16 { gap: 16px; margin-bottom: 16px; }
.modal-std-shell { width: 100%; max-width: 500px; padding: 32px; position: relative; }
.textarea-premium { margin-top: 8px; padding: 12px 20px; border-radius: 16px; resize: vertical; }
.minh-80 { min-height: 80px; }
.file-upload-compact { margin-top: 8px; padding: 10px 14px; }
.file-upload-note { display: block; margin-top: 6px; font-size: 0.78rem; }
.z-10025-force { z-index: 10025 !important; }
.title-row { margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.scroll-max-60vh { overflow-y: auto; max-height: 60vh; }
.date-input-compact { margin-top: 8px; padding-left: 16px; }
#page-keamanan .hidden { display: none !important; }
#page-keamanan .active-tab { display: block !important; }

.km-incident-table td,
.km-incident-table th {
    vertical-align: middle;
}

#page-keamanan .sub-nav-tabs {
    overflow-x: auto;
    scrollbar-width: thin;
    padding-bottom: 2px;
}

#page-keamanan .sub-nav-tabs .sub-nav-tab {
    min-height: 42px;
}

@media (max-width: 860px) {
    #page-keamanan .section-header {
        gap: 10px;
        align-items: flex-start;
        flex-direction: column;
    }

    #page-keamanan .section-header .button-primary,
    #page-keamanan .section-header .button-secondary,
    #page-keamanan .section-header .button-link {
        width: 100%;
        justify-content: center;
    }

    .km-incident-table thead {
        display: none;
    }

    .km-incident-table,
    .km-incident-table tbody,
    .km-incident-table tr,
    .km-incident-table td {
        display: block;
        width: 100%;
    }

    .km-incident-table tr {
        background: color-mix(in srgb, var(--secondary-bg) 92%, transparent);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        margin-bottom: 12px;
        padding: 10px;
    }

    .km-incident-table td {
        border: 0 !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 6px 4px !important;
        font-size: 0.82rem;
    }

    .km-incident-table td::before {
        content: attr(data-label);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
        color: var(--text-secondary-color);
        flex: 0 0 96px;
    }

    .km-incident-table td.text-right {
        justify-content: flex-end !important;
        flex-wrap: wrap;
        gap: 6px !important;
    }

    .km-incident-table td.text-right::before {
        margin-right: auto;
    }

    #page-keamanan .modal-content,
    #page-keamanan #modal-lap-keuangan .glass-card,
    #page-keamanan #modal-jadwal .glass-card,
    #page-keamanan #modal-satpam .glass-card,
    #page-keamanan #modal-izin .glass-card {
        width: min(100%, 96vw) !important;
        max-height: 92vh;
        overflow-y: auto;
        border-radius: 18px;
        padding: 20px !important;
    }
}

/* Custom Badge colors for reports */
.badge-status-waiting { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.badge-status-resolved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.badge-status-critical { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>