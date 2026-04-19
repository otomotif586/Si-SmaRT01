<!-- Page: Laporan Iuran Blok (Deposited to Central RT) -->
<div id="page-laporan-iuran-blok" class="page-content hidden page-section">
    
    <!-- Summary Overview (Deluxe Adaptive Style) -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item stagger-delay-1 card-border-bottom-accent">
            <div class="card-icon-deluxe icon-tone-info card-icon-mb-sm">
                <i data-lucide="layers"></i>
            </div>
            <p class="card-label">Blok Menyetor</p>
            <h3 id="report-stat-blok" class="card-value text-color m-0 card-value-xl">0 Blok</h3>
            <div class="card-sub-info">Partisipasi kolektif blok</div>
        </div>
        <div class="glass-card-deluxe stagger-item stagger-delay-2 card-border-bottom-success">
            <div class="card-icon-deluxe icon-tone-success card-icon-mb-sm">
                <i data-lucide="banknote"></i>
            </div>
            <p class="card-label">Total Dana Pusat</p>
            <h3 id="report-stat-total" class="card-value text-emerald m-0 card-value-xl">Rp 0</h3>
            <div class="card-sub-info">Akumulasi iuran tervalidasi</div>
        </div>
        <div class="glass-card-deluxe stagger-item stagger-delay-3 card-border-bottom-warning">
            <div class="card-icon-deluxe icon-tone-warning card-icon-mb-sm">
                <i data-lucide="users"></i>
            </div>
            <p class="card-label">Warga Berkontribusi</p>
            <h3 id="report-stat-warga" class="card-value text-color m-0 card-value-xl">0 KK</h3>
            <div class="card-sub-info">Kepatuhan pembayaran warga</div>
        </div>
    </div>

    <div class="glass-card page-filter-card page-filter-card--stack">
        <div class="page-filter-row">
            <div class="page-filter-group page-filter-group--spacious">
                <label class="compact-label">Filter:</label>
                <select id="filter-blok-laporan" class="input-field select-custom compact-control min-w-[120px]" onchange="filterLaporanBlok()">
                    <option value="all">Semua Blok</option>
                </select>
            </div>
            <button class="button-secondary button-sm compact-btn" onclick="exportLaporanBlokCSV()"><i data-lucide="download" class="icon-16 mr-[6px]"></i> Export</button>
        </div>
        <div class="action-buttons-scroll hide-scrollbar page-filter-row page-filter-divider toolbar-scroll-row">
            <button class="button-primary button-sm compact-btn btn-accent btn-no-shrink" onclick="bulkValidateIuranRT()"><i data-lucide="check-check" class="icon-16 mr-[6px]"></i> Validasi Semua</button>
            <button class="button-primary button-sm compact-btn btn-accent-soft btn-no-shrink" onclick="validateSelectedIuran()"><i data-lucide="check" class="icon-16 mr-[6px]"></i> Validasi Terpilih</button>
            <button class="button-primary button-sm compact-btn btn-info btn-no-shrink" onclick="bulkPostIuranRT()"><i data-lucide="upload-cloud" class="icon-16 mr-[6px]"></i> Posting Semua</button>
            <button class="button-primary button-sm compact-btn btn-info-soft btn-no-shrink" onclick="postSelectedIuran()"><i data-lucide="upload-cloud" class="icon-16 mr-[6px]"></i> Posting Terpilih</button>
            <button class="button-secondary button-sm compact-btn btn-warning-outline btn-no-shrink" onclick="bulkUnlockIuranRT()"><i data-lucide="rotate-ccw" class="icon-16 mr-[6px]"></i> Tarik Semua</button>
            <button class="button-secondary button-sm compact-btn btn-warning-outline btn-no-shrink" onclick="unlockSelectedIuran()"><i data-lucide="rotate-ccw" class="icon-16 mr-[6px]"></i> Tarik Terpilih</button>
        </div>
    </div>

    <!-- Main List / Table -->
    <div class="glass-card table-panel">
        <div class="table-panel-head table-head-stack">
            
            <!-- Navigator Bulan Dipindah ke Sini -->
            <div class="page-filter-group filter-row-wrap">
                <button class="button-secondary button-sm compact-btn-icon" onclick="prevMonthLaporanBlok()" title="Bulan Sebelumnya"><i data-lucide="chevron-left" class="icon-16"></i></button>
                <select id="filter-bulan-laporan" class="input-field select-custom compact-control select-auto-120" onchange="loadLaporanIuranBlok()">
                    <!-- Diisi dinamis -->
                </select>
                <select id="filter-tahun-laporan" class="input-field select-custom compact-control select-auto-100" onchange="loadLaporanIuranBlok()">
                    <!-- Diisi dinamis -->
                </select>
                <button class="button-secondary button-sm compact-btn-icon" onclick="nextMonthLaporanBlok()" title="Bulan Selanjutnya"><i data-lucide="chevron-right" class="icon-16"></i></button>
            </div>

            <div class="toolbar-space-between">
                <h4 class="section-title table-panel-title">Rincian Setoran Per Blok</h4>
                <div class="input-with-icon search-shell-280">
                    <i data-lucide="search" class="icon-18"></i>
                    <input type="text" id="search-laporan-blok" class="input-field input-field-compact text-sm rounded-12" placeholder="Cari Blok atau Warga..." oninput="filterLaporanBlok()">
                </div>
            </div>
            
            <!-- Modern Segmented Tabs -->
            <div class="tab-pill-wrapper mt-8">
                <div class="tab-pill-container">
                    <button class="tab-pill-btn active-tab-pending" id="tab-laporan-belum" onclick="switchLaporanIuranTab('belum_posting', this)">
                        <i data-lucide="clock" class="icon-14 mr-[6px]"></i> Belum Diposting
                    </button>
                    <button class="tab-pill-btn" id="tab-laporan-sudah" onclick="switchLaporanIuranTab('sudah_posting', this)">
                        <i data-lucide="check-circle" class="icon-14 mr-[6px]"></i> Sudah Diposting
                    </button>
                </div>
            </div>
        </div>

        <div id="laporan-blok-container" class="hide-scrollbar min-h-300">
            <p class="text-secondary text-center py-5">Memuat data setoran...</p>
        </div>

        <!-- Pagination Controls -->
        <div id="laporan-blok-pagination" class="pagination-wrapper pagination-shell">
            <p id="laporan-blok-pagination-info" class="text-secondary pagination-bar-info">Menampilkan 0 dari 0 data</p>
            <div class="pagination-actions">
                <button class="button-secondary button-sm compact-btn" id="btn-prev-laporan-page" onclick="changeLaporanPage(-1)"><i data-lucide="chevron-left" class="icon-16"></i> Prev</button>
                <div id="laporan-page-numbers" class="row-gap-4"></div>
                <button class="button-secondary button-sm compact-btn" id="btn-next-laporan-page" onclick="changeLaporanPage(1)">Next <i data-lucide="chevron-right" class="icon-16"></i></button>
            </div>
        </div>
    </div>

    <div id="laporan-blok-empty" class="hidden empty-state">
        <div class="empty-state-icon-wrap">
            <i data-lucide="info" class="icon-40 icon-muted"></i>
        </div>
        <h3 class="text-color">Belum Ada Setoran</h3>
        <p class="text-secondary">Tidak ada data setoran dari warga manapun untuk periode yang dipilih.</p>
    </div>

</div>

<style>
/* Modern Tab Pill Styles */
#page-laporan-iuran-blok .tab-pill-container {
    display: inline-flex;
    background: rgba(0, 0, 0, 0.04);
    padding: 4px;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    gap: 4px;
}

#page-laporan-iuran-blok .tab-pill-btn {
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

#page-laporan-iuran-blok .tab-pill-btn:hover {
    background: rgba(0, 0, 0, 0.02);
    color: var(--text-color);
}

/* Active State Colors */
#page-laporan-iuran-blok .tab-pill-btn.active-tab-pending {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.05);
}

#page-laporan-iuran-blok .tab-pill-btn.active-tab-posted {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #10b981 !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.05);
}

.dark-theme #page-laporan-iuran-blok .tab-pill-container {
    background: rgba(255, 255, 255, 0.04);
}

.dark-theme #page-laporan-iuran-blok .tab-pill-btn.active-tab-pending {
    background: rgba(239, 68, 68, 0.15) !important;
}

.dark-theme #page-laporan-iuran-blok .tab-pill-btn.active-tab-posted {
    background: rgba(16, 185, 129, 0.15) !important;
}

/* Modern Report Styles */
#page-laporan-iuran-blok .report-row {
    display: grid;
    grid-template-columns: 40px 1fr 140px 110px 110px 110px 180px;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.3s ease;
    cursor: default;
}

#page-laporan-iuran-blok .report-row:hover {
    background: var(--hover-bg);
}

#page-laporan-iuran-blok .report-row:last-child {
    border-bottom: none;
}

#page-laporan-iuran-blok .report-header-row {
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

#page-laporan-iuran-blok .report-warga-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

#page-laporan-iuran-blok .report-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

#page-laporan-iuran-blok .report-amount {
    font-family: 'DM Mono', monospace;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-color);
}

@media (max-width: 992px) {
    #page-laporan-iuran-blok .report-header-row { display: none; }
    #page-laporan-iuran-blok .report-row { 
        grid-template-columns: 40px 1fr;
        gap: 12px;
        padding: 20px;
    }
    #page-laporan-iuran-blok .report-warga-info { grid-column: 2; }
    #page-laporan-iuran-blok .report-blok-no { grid-column: 2; font-size: 1rem !important; }
    #page-laporan-iuran-blok .report-amount { font-size: 1rem; text-align: left !important; grid-column: 2; margin-top: 4px; }
    #page-laporan-iuran-blok .report-date { text-align: left !important; grid-column: 2; }
    #page-laporan-iuran-blok .report-action-btns { grid-column: 1 / -1; justify-content: flex-start !important; margin-top: 8px; }
}

@media (max-width: 576px) {
    #page-laporan-iuran-blok .report-row { 
        grid-template-columns: 30px 1fr;
    }
}
</style>
