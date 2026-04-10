<!-- Page: Keamanan -->
<div id="page-keamanan" class="page-content hidden page-section">
    
    <!-- Sub Navigation Tabs -->
    <div class="sub-nav-tabs" style="margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 8px;">
        <button class="sub-nav-tab active" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchKeamananTab('km-ringkasan', this)">
            <i data-lucide="layout-dashboard"></i> Ringkasan
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchKeamananTab('km-jadwal', this)">
            <i data-lucide="calendar"></i> Jadwal
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchKeamananTab('km-master', this)">
            <i data-lucide="users"></i> Master Satpam
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchKeamananTab('km-laporan', this)">
            <i data-lucide="clipboard-list"></i> Laporan
        </button>
        <button class="sub-nav-tab" style="flex: 1 1 auto; justify-content: center; white-space: nowrap;" onclick="switchKeamananTab('km-izin', this)">
            <i data-lucide="user-minus"></i> Izin/Cuti
        </button>
    </div>

    <!-- Tab Content: Ringkasan -->
    <div id="km-ringkasan" class="km-tab-content active-tab">
        <div class="summary-3-grid" style="margin-bottom: 24px;">
            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
                <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                    <i data-lucide="user-check"></i>
                </div>
                <p class="card-label">Satpam Bertugas</p>
                <h3 id="km-current-guard" class="card-value text-color" style="font-size: 1.2rem;">3 Personel</h3>
                <div class="card-sub-info">Shift Pagi (08:00 - 20:00)</div>
            </div>
            
            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
                <div class="card-icon-deluxe" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">
                    <i data-lucide="alert-circle"></i>
                </div>
                <p class="card-label">Laporan Baru</p>
                <h3 id="km-unread-reports" class="card-value text-orange" style="font-size: 1.5rem;">2</h3>
                <div class="card-sub-info">Butuh review hari ini</div>
            </div>

            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
                <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i data-lucide="shield"></i>
                </div>
                <p class="card-label">Status Lingkungan</p>
                <h3 class="card-value text-emerald" style="font-size: 1.5rem;">Aman</h3>
                <div class="card-sub-info">Patroli aktif berkelanjutan</div>
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
            <p class="panic-description" style="margin-top: 24px; font-size: 0.85rem; opacity: 0.8; max-width: 400px; margin-left: auto; margin-right: auto;">
                Satu sentuhan untuk mengirim sinyal darurat ke daftar kontak prioritas & seluruh tim keamanan.
            </p>
        </div>

        <div class="glass-card card-section" style="margin-top: 32px;">
            <div class="section-header" style="margin-bottom: 20px;">
                <h4 class="section-title" style="font-size: 1rem;">Aktifitas Terbaru</h4>
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
                <table class="modern-table" style="width: 100%;">
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
        <div class="section-header" style="margin-top: 10px;">
            <h4 class="section-title">Personel Keamanan</h4>
            <button class="button-primary" onclick="addSatpam()">
                <i data-lucide="plus"></i> Tambah Personel
            </button>
        </div>
        <div id="km-guard-list" class="grid-container" style="margin-top: 20px;">
            <!-- Guard cards will be loaded here -->
        </div>
    </div>

    <!-- Tab Content: Laporan Keamanan -->
    <div id="km-laporan" class="km-tab-content hidden">
        <div class="glass-card card-section">
            <div class="section-header">
                <h4 class="section-title">Log Kejadian & Patroli</h4>
                <button class="button-primary" onclick="addIncident()">
                    <i data-lucide="plus"></i> Laporan Baru
                </button>
            </div>
            <div class="table-responsive">
                <table class="modern-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Petugas</th>
                            <th>Kejadian/Tamu</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="km-incident-body">
                        <!-- Incidents will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Izin & Cuti -->
    <div id="km-izin" class="km-tab-content hidden">
        <div class="grid-container-2-col">
            <div class="glass-card card-section">
                <div class="section-header" style="margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center;">
                    <h4 class="section-title" style="margin: 0;">Pengajuan Izin/Cuti</h4>
                    <button class="button-primary button-sm" onclick="addIzin()"><i data-lucide="plus"></i> Ajukan Izin</button>
                </div>
                <div class="report-list" id="km-leave-requests">
                    <!-- Requests will be loaded here -->
                </div>
            </div>
            <div class="glass-card card-section">
                <h4 class="section-title" style="margin-bottom: 20px;">Statistik Absensi</h4>
                <div id="km-attendance-stats">
                    <!-- Stats will be loaded here -->
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Emergency Broadcast Modal -->
<div id="modal-panic-broadcast" class="modal-overlay hidden">
    <div class="modal-content glass-card-deluxe" style="max-width: 450px; text-align: center; padding: 40px;">
        <div class="panic-button" style="width: 100px; height: 100px; margin: 0 auto 24px; cursor: default;">
            <i data-lucide="send"></i>
        </div>
        <h3 class="section-title" style="font-size: 1.5rem; margin-bottom: 12px;">Sinyal Darurat!</h3>
        <p class="text-secondary" style="margin-bottom: 32px;">Pilih jalur pengiriman pesan darurat ke seluruh tim & warga:</p>
        
        <div class="grid-container" id="panic-recipient-list">
            <!-- Dynamic recipient contact buttons -->
        </div>

        <div style="margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 24px;">
            <button class="button-secondary w-full" onclick="closeModal('modal-panic-broadcast')">Batalkan Sinyal</button>
        </div>
    </div>
</div>

<!-- Panic Settings Modal -->
<div id="modal-panic-settings" class="modal-overlay hidden">
    <div class="modal-content glass-card-deluxe" style="max-width: 500px;">
        <div class="section-header" style="margin-bottom: 24px;">
            <h3 class="section-title">Kontak Darurat</h3>
            <button class="button-link" onclick="closeModal('modal-panic-settings')"><i data-lucide="x"></i></button>
        </div>
        <p class="text-secondary" style="font-size: 0.85rem; margin-bottom: 20px;">Nomor di bawah ini akan dihubungi saat Panic Button ditekan.</p>
        
        <div id="panic-numbers-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            <!-- Inputs will be generated here -->
        </div>

        <button class="button-secondary w-full" style="margin-bottom: 12px;" onclick="addPanicNumber()">
            <i data-lucide="plus-circle"></i> Tambah Nomor Baru
        </button>
        
        <div class="flex gap-3">
            <button class="button-primary flex-1" onclick="savePanicSettings()">Simpan Pengaturan</button>
        </div>
    </div>
</div>

<!-- MODAL CRUD KEAMANAN -->

<!-- 1. Modal Master Satpam -->
<div id="modal-satpam" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeKmModal('modal-satpam')"><i data-lucide="x"></i></button>
        <h2 id="modal-satpam-title" class="section-title" style="margin-bottom: 8px;">Tambah Personel</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Kelola data master petugas keamanan.</p>
        
        <input type="hidden" id="km-satpam-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Nama Lengkap</label>
            <input type="text" id="km-satpam-nama" class="input-field" style="margin-top: 8px;">
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Nomor HP / WA</label>
            <input type="text" id="km-satpam-nohp" class="input-field" style="margin-top: 8px;">
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label">Status</label>
            <select id="km-satpam-status" class="input-field select-custom" style="margin-top: 8px;">
                <option value="Aktif">Aktif Bertugas</option>
                <option value="Nonaktif">Nonaktif / Berhenti</option>
            </select>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveSatpam()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Data</button>
    </div>
</div>

<!-- 2. Modal Jadwal Shift -->
<div id="modal-jadwal" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeKmModal('modal-jadwal')"><i data-lucide="x"></i></button>
        <h2 class="section-title" style="margin-bottom: 8px;">Atur Jadwal Shift</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Penugasan personel untuk patroli lingkungan.</p>
        
        <input type="hidden" id="km-jadwal-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Personel Keamanan</label>
            <select id="km-jadwal-satpam" class="input-field select-custom" style="margin-top: 8px;"></select>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Tanggal Bertugas</label>
            <input type="date" id="km-jadwal-tanggal" class="input-field" style="margin-top: 8px; padding-left: 20px;">
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label">Waktu Shift</label>
            <select id="km-jadwal-shift" class="input-field select-custom" style="margin-top: 8px;">
                <option value="Pagi">Pagi (08:00 - 20:00)</option>
                <option value="Malam">Malam (20:00 - 08:00)</option>
            </select>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveJadwal()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Jadwal</button>
    </div>
</div>

<!-- 3. Modal Laporan (Incident) -->
<div id="modal-lap-keamanan" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeKmModal('modal-lap-keamanan')"><i data-lucide="x"></i></button>
        <h2 id="modal-lap-title" class="section-title" style="margin-bottom: 8px;">Laporan Baru</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Catat kejadian darurat atau tamu di lingkungan RT.</p>
        
        <input type="hidden" id="km-lap-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Judul Kejadian / Tamu</label>
            <input type="text" id="km-lap-judul" class="input-field" style="margin-top: 8px;">
        </div>
        <div class="grid-container-2-col" style="gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label class="card-label">Waktu</label>
                <input type="datetime-local" id="km-lap-waktu" class="input-field" style="margin-top: 8px; padding-left: 20px;">
            </div>
            <div class="form-group">
                <label class="card-label">Lokasi / Blok</label>
                <input type="text" id="km-lap-lokasi" class="input-field" style="margin-top: 8px;" placeholder="Cth: Gerbang Depan">
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Deskripsi Lengkap</label>
            <textarea id="km-lap-deskripsi" class="input-field" style="margin-top: 8px; min-height: 80px; padding: 12px 20px; border-radius: 16px; resize: vertical;"></textarea>
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label">Status Penanganan</label>
            <select id="km-lap-status" class="input-field select-custom" style="margin-top: 8px;">
                <option value="Baru">Baru / Menunggu</option>
                <option value="Diproses">Sedang Ditangani</option>
                <option value="Selesai">Selesai / Aman</option>
            </select>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveLaporanKeamanan()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Laporan</button>
    </div>
</div>

<!-- Modal Detail Laporan Keamanan -->
<div id="modal-detail-lap-keamanan" class="modal-overlay hidden" style="z-index: 10025 !important;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeKmModal('modal-detail-lap-keamanan')"><i data-lucide="x"></i></button>
        <h3 class="section-title" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="file-text" class="text-blue"></i> Detail Kejadian</h3>
        <div id="km-detail-lap-content" class="hide-scrollbar" style="overflow-y: auto; max-height: 60vh;"></div>
    </div>
</div>

<!-- 4. Modal Pengajuan Izin / Cuti -->
<div id="modal-izin" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="closeKmModal('modal-izin')"><i data-lucide="x"></i></button>
        <h2 id="modal-izin-title" class="section-title" style="margin-bottom: 8px;">Formulir Izin</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Pengajuan ketidakhadiran (Cuti/Izin/Sakit).</p>
        
        <input type="hidden" id="km-izin-id" value="0">
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Pilih Personel</label>
            <select id="km-izin-satpam" class="input-field select-custom" style="margin-top: 8px;"></select>
        </div>
        <div class="grid-container-2-col" style="gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label class="card-label">Mulai Tanggal</label>
                <input type="date" id="km-izin-mulai" class="input-field" style="margin-top: 8px; padding-left: 16px;">
            </div>
            <div class="form-group">
                <label class="card-label">Sampai Tanggal</label>
                <input type="date" id="km-izin-selesai" class="input-field" style="margin-top: 8px; padding-left: 16px;">
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="card-label">Jenis Pengajuan</label>
            <select id="km-izin-jenis" class="input-field select-custom" style="margin-top: 8px;">
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin Pribadi</option>
                <option value="Cuti">Cuti Tahunan</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="card-label">Keterangan / Alasan</label>
            <textarea id="km-izin-ket" class="input-field" style="margin-top: 8px; min-height: 80px; padding: 12px 20px; border-radius: 16px; resize: vertical;"></textarea>
        </div>
        <div class="form-group hidden" id="km-izin-status-group" style="margin-bottom: 24px;">
            <label class="card-label">Status Persetujuan</label>
            <select id="km-izin-status" class="input-field select-custom" style="margin-top: 8px;">
                <option value="Pending">Menunggu (Pending)</option>
                <option value="Disetujui">Disetujui</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>
        <button class="button-primary" style="width: 100%; justify-content: center;" onclick="saveIzin()"><i data-lucide="send" style="margin-right: 8px;"></i> Kirim Pengajuan</button>
    </div>
</div>

<style>
.km-tab-content {
    animation: fadeIn 0.4s ease;
}
.hidden { display: none !important; }
.active-tab { display: block !important; }

/* Custom Badge colors for reports */
.badge-status-waiting { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.badge-status-resolved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.badge-status-critical { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>