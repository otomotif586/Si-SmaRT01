<!-- Page: Keamanan -->
<div id="page-keamanan" class="page-content hidden page-section">
    
    <!-- Header with Tabs -->
    <div class="section-header" style="margin-bottom: 20px;">
        <div>
            <h2 class="section-title">Manajemen Keamanan</h2>
            <p class="text-secondary" style="font-size: 0.8rem;">Pusat kendali pengawasan & keselamatan lingkungan</p>
        </div>
        <div class="header-actions">
            <button class="button-secondary" onclick="openPanicSettings()" title="Pengaturan Panic Button">
                <i data-lucide="settings"></i>
                <span class="hide-text-mobile">Pengaturan</span>
            </button>
        </div>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="sub-nav-tabs" style="margin-bottom: 24px; overflow-x: auto;">
        <button class="sub-nav-tab active" onclick="switchKeamananTab('km-ringkasan', this)">
            <i data-lucide="layout-dashboard"></i> Ringkasan
        </button>
        <button class="sub-nav-tab" onclick="switchKeamananTab('km-jadwal', this)">
            <i data-lucide="calendar"></i> Jadwal
        </button>
        <button class="sub-nav-tab" onclick="switchKeamananTab('km-master', this)">
            <i data-lucide="users"></i> Master Satpam
        </button>
        <button class="sub-nav-tab" onclick="switchKeamananTab('km-laporan', this)">
            <i data-lucide="clipboard-list"></i> Laporan
        </button>
        <button class="sub-nav-tab" onclick="switchKeamananTab('km-izin', this)">
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
                <div class="flex gap-2">
                    <button class="button-secondary button-sm" onclick="prevSchedule()"><i data-lucide="chevron-left"></i></button>
                    <button class="button-secondary button-sm" onclick="nextSchedule()"><i data-lucide="chevron-right"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="modern-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Hari/Tgl</th>
                            <th>Shift Pagi (08:00-20:00)</th>
                            <th>Shift Malam (20:00-08:00)</th>
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
                <h4 class="section-title" style="margin-bottom: 20px;">Pengajuan Izin/Cuti</h4>
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