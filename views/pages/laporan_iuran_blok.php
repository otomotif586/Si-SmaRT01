<!-- Page: Laporan Iuran Blok (Deposited to Central RT) -->
<div id="page-laporan-iuran-blok" class="page-content hidden page-section">
    
    <div class="section-header">
        <div>
            <h3 class="section-title">Laporan Iuran Blok</h3>
        </div>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; width: 100%;">
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <select id="filter-blok-laporan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; width: auto; min-width: 130px; border-radius: 12px;" onchange="filterLaporanBlok()">
                    <option value="all">Semua Blok</option>
                </select>
            </div>
            
            <div class="action-buttons-scroll hide-scrollbar" style="display: flex; gap: 12px; align-items: center; overflow-x: auto; max-width: 100%; padding: 8px; margin: -8px;">
                <button class="button-primary button-sm" style="padding: 10px 16px; border-radius: 12px; background-color: var(--accent-color); flex-shrink: 0;" onclick="bulkValidateIuranRT()"><i data-lucide="check-check" style="margin-right: 6px; width: 18px; height: 18px;"></i> Validasi Semua</button>
                <button class="button-primary button-sm" style="padding: 10px 16px; border-radius: 12px; background-color: var(--accent-color); opacity: 0.8; flex-shrink: 0;" onclick="validateSelectedIuran()"><i data-lucide="check" style="margin-right: 6px; width: 18px; height: 18px;"></i> Validasi Terpilih</button>
                <button class="button-primary button-sm" style="padding: 10px 16px; border-radius: 12px; background-color: #3b82f6; flex-shrink: 0;" onclick="bulkPostIuranRT()"><i data-lucide="upload-cloud" style="margin-right: 6px; width: 18px; height: 18px;"></i> Posting Semua</button>
                <button class="button-primary button-sm" style="padding: 10px 16px; border-radius: 12px; background-color: #3b82f6; opacity: 0.8; flex-shrink: 0;" onclick="postSelectedIuran()"><i data-lucide="upload-cloud" style="margin-right: 6px; width: 18px; height: 18px;"></i> Posting Terpilih</button>
                <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px; color: #f59e0b; border-color: rgba(245,158,11,0.3); flex-shrink: 0;" onclick="bulkUnlockIuranRT()"><i data-lucide="rotate-ccw" style="margin-right: 6px; width: 18px; height: 18px;"></i> Tarik Semua</button>
                <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px; color: #f59e0b; border-color: rgba(245,158,11,0.3); flex-shrink: 0;" onclick="unlockSelectedIuran()"><i data-lucide="rotate-ccw" style="margin-right: 6px; width: 18px; height: 18px;"></i> Tarik Terpilih</button>
                <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px; flex-shrink: 0;" onclick="exportLaporanBlokCSV()"><i data-lucide="download" style="margin-right: 6px; width: 18px; height: 18px;"></i> Export</button>
            </div>
        </div>
    </div>

    <!-- Summary Overview (SSO Startup Style) -->
    <div class="summary-wrapper" style="margin-bottom: 32px;">
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid var(--accent-color);">
            <div class="summary-icon-wrapper bg-blue-light text-blue"><i data-lucide="layers"></i></div>
            <p class="card-label m-0" style="margin:0;">Blok Menyetor</p>
            <h3 id="report-stat-blok" class="card-value text-color m-0" style="margin:0;">0 Blok</h3>
        </div>
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid #10b981;">
            <div class="summary-icon-wrapper bg-emerald-light text-emerald"><i data-lucide="banknote"></i></div>
            <p class="card-label m-0" style="margin:0;">Total Dana Pusat</p>
            <h3 id="report-stat-total" class="card-value text-emerald m-0" style="margin:0;">Rp 0</h3>
        </div>
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid #f59e0b;">
            <div class="summary-icon-wrapper bg-orange-light text-orange"><i data-lucide="users"></i></div>
            <p class="card-label m-0" style="margin:0;">Warga Berkontribusi</p>
            <h3 id="report-stat-warga" class="card-value text-color m-0" style="margin:0;">0 KK</h3>
        </div>
    </div>

    <!-- Main List / Table -->
    <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 16px; background: rgba(255,255,255,0.02);">
            
            <!-- Navigator Bulan Dipindah ke Sini -->
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="prevMonthLaporanBlok()" title="Bulan Sebelumnya"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
                <select id="filter-bulan-laporan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; width: auto; min-width: 120px; border-radius: 12px;" onchange="loadLaporanIuranBlok()">
                    <!-- Diisi dinamis -->
                </select>
                <select id="filter-tahun-laporan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; width: auto; min-width: 100px; border-radius: 12px;" onchange="loadLaporanIuranBlok()">
                    <!-- Diisi dinamis -->
                </select>
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="nextMonthLaporanBlok()" title="Bulan Selanjutnya"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <h4 class="section-title" style="font-size: 1.1rem; margin: 0;">Rincian Setoran Per Blok</h4>
                <div class="input-with-icon" style="max-width: 280px; width: 100%;">
                    <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                    <input type="text" id="search-laporan-blok" class="input-field" placeholder="Cari Blok atau Warga..." oninput="filterLaporanBlok()" style="padding: 10px 16px 10px 40px; font-size: 0.875rem; border-radius: 12px;">
                </div>
            </div>
            
            <!-- Modern Segmented Tabs -->
            <div class="tab-pill-wrapper" style="margin-top: 8px;">
                <div class="tab-pill-container">
                    <button class="tab-pill-btn active-tab-pending" id="tab-laporan-belum" onclick="switchLaporanIuranTab('belum_posting', this)">
                        <i data-lucide="clock" style="width: 14px; height: 14px; margin-right: 6px;"></i> Belum Diposting
                    </button>
                    <button class="tab-pill-btn" id="tab-laporan-sudah" onclick="switchLaporanIuranTab('sudah_posting', this)">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px; margin-right: 6px;"></i> Sudah Diposting
                    </button>
                </div>
            </div>
        </div>

        <div id="laporan-blok-container" class="hide-scrollbar" style="min-height: 300px;">
            <p class="text-secondary text-center py-5">Memuat data setoran...</p>
        </div>

        <!-- Pagination Controls -->
        <div id="laporan-blok-pagination" class="pagination-wrapper" style="padding: 16px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.01);">
            <p id="laporan-blok-pagination-info" class="text-secondary" style="font-size: 0.8125rem;">Menampilkan 0 dari 0 data</p>
            <div style="display: flex; gap: 8px;">
                <button class="button-secondary button-sm" id="btn-prev-laporan-page" onclick="changeLaporanPage(-1)" style="padding: 8px 12px; border-radius: 8px;"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i> Prev</button>
                <div id="laporan-page-numbers" style="display: flex; gap: 4px;"></div>
                <button class="button-secondary button-sm" id="btn-next-laporan-page" onclick="changeLaporanPage(1)" style="padding: 8px 12px; border-radius: 8px;">Next <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
            </div>
        </div>
    </div>

    <div id="laporan-blok-empty" class="hidden" style="text-align: center; padding: 60px 20px;">
        <div style="width: 80px; height: 80px; background: var(--hover-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto;">
            <i data-lucide="info" style="width: 40px; height: 40px; color: var(--text-secondary-color);"></i>
        </div>
        <h3 class="text-color">Belum Ada Setoran</h3>
        <p class="text-secondary">Tidak ada data setoran dari warga manapun untuk periode yang dipilih.</p>
    </div>

</div>

<style>
/* Modern Tab Pill Styles */
.tab-pill-container {
    display: inline-flex;
    background: rgba(0, 0, 0, 0.04);
    padding: 4px;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    gap: 4px;
}

.tab-pill-btn {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary-color);
    border: none;
    background: transparent;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tab-pill-btn:hover {
    background: rgba(0, 0, 0, 0.02);
    color: var(--text-color);
}

/* Active State Colors */
.tab-pill-btn.active-tab-pending {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.05);
}

.tab-pill-btn.active-tab-posted {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #10b981 !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.05);
}

.dark-theme .tab-pill-container {
    background: rgba(255, 255, 255, 0.04);
}

.dark-theme .tab-pill-btn.active-tab-pending {
    background: rgba(239, 68, 68, 0.15) !important;
}

.dark-theme .tab-pill-btn.active-tab-posted {
    background: rgba(16, 185, 129, 0.15) !important;
}

/* Modern Report Styles */
.report-row {
    display: grid;
    grid-template-columns: 40px 1fr 140px 110px 110px 110px 180px;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.3s ease;
    cursor: default;
}

.report-row:hover {
    background: var(--hover-bg);
}

.report-row:last-child {
    border-bottom: none;
}

.report-header-row {
    display: grid;
    grid-template-columns: 40px 1fr 140px 110px 110px 110px 180px;
    padding: 16px 24px;
    background: var(--secondary-bg);
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary-color);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.report-warga-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.report-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.report-amount {
    font-family: 'DM Mono', monospace;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-color);
}

@media (max-width: 992px) {
    .report-header-row { display: none; }
    .report-row { 
        grid-template-columns: 40px 1fr;
        gap: 12px;
        padding: 20px;
    }
    .report-warga-info { grid-column: 2; }
    .report-blok-no { grid-column: 2; font-size: 1rem !important; }
    .report-amount { font-size: 1rem; text-align: left !important; grid-column: 2; margin-top: 4px; }
    .report-date { text-align: left !important; grid-column: 2; }
    .report-action-btns { grid-column: 1 / -1; justify-content: flex-start !important; margin-top: 8px; }
}

@media (max-width: 576px) {
    .report-row { 
        grid-template-columns: 30px 1fr;
    }
}
</style>
