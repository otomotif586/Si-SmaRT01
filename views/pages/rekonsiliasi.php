<!-- Page: Rekonsiliasi & Audit Iuran (Annual Summary) -->
<div id="page-rekonsiliasi" class="page-content hidden page-section">
    
    <div class="glass-card page-filter-card gap-16">
        <p class="text-secondary page-filter-desc">Monitoring kedisiplinan pembayaran warga per tahun buku.</p>
        <div class="header-actions page-filter-actions gap-12">
            <div class="page-filter-group page-filter-group--spacious">
                <div class="page-filter-group">
                    <label class="compact-label">Tahun:</label>
                    <input type="number" id="filter-tahun-rekonsiliasi" class="input-field compact-control w-85" value="<?= date('Y') ?>" onchange="loadGlobalRekonsiliasi()">
                </div>
                <div class="page-filter-group">
                    <label class="compact-label">Blok:</label>
                    <select id="filter-blok-rekonsiliasi" class="input-field select-custom compact-control minw-120" onchange="loadGlobalRekonsiliasi()">
                        <option value="all">Semua Blok</option>
                    </select>
                </div>
            </div>
            <button class="button-secondary button-sm compact-btn" onclick="exportRekonsiliasiCSV()"><i data-lucide="download" class="icon-16 icon-mr-6"></i> Export</button>
        </div>
    </div>

    <!-- Reconciliation Summary Cards -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item stagger-delay-1">
            <div class="card-icon-deluxe icon-tone-info">
                <i data-lucide="users"></i>
            </div>
            <p class="card-label">Warga Audit</p>
            <h3 id="rekon-stat-warga" class="card-value text-color">0</h3>
            <div class="card-sub-info">Total KK terdaftar</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item stagger-delay-2">
            <div class="card-icon-deluxe icon-tone-success">
                <i data-lucide="check-circle"></i>
            </div>
            <p class="card-label">Lunas 100%</p>
            <h3 id="rekon-stat-lunas" class="card-value text-emerald">0</h3>
            <div class="card-sub-info">Pembayaran setahun penuh</div>
        </div>

        <div class="glass-card-deluxe stagger-item stagger-delay-3">
            <div class="card-icon-deluxe icon-tone-warning">
                <i data-lucide="help-circle"></i>
            </div>
            <p class="card-label">Potensi Piutang</p>
            <h3 id="rekon-stat-piutang" class="card-value text-orange">Rp 0</h3>
            <div id="rekon-stat-menunggak-info" class="card-sub-info">0 Warga belum lunas</div>
        </div>
    </div>
    <!-- Hidden element to maintain JS compatibility for menunggak count if needed -->
    <div id="rekon-stat-menunggak" class="hidden">0</div>

    <!-- Legend -->
    <div class="glass-card legend-wrap-end mb-16">
        <div class="legend-inline"><span class="legend-dot legend-dot--success"></span> Lunas</div>
        <div class="legend-inline"><span class="legend-dot legend-dot--danger"></span> Menunggak</div>
        <div class="legend-inline"><span class="legend-dot legend-dot--muted"></span> Belum Berjalan</div>
    </div>

    <!-- Main Reconciliation Table -->
    <div class="glass-card table-panel">
        <div class="table-panel-head">
            <h4 class="table-panel-title">Daftar Status Iuran Per Bulan</h4>
            <div class="input-with-icon search-shell-250">
                <i data-lucide="search" class="icon-18"></i>
                <input type="text" id="search-rekonsiliasi" class="input-field input-field-compact" placeholder="Cari Warga..." oninput="filterRekonsiliasi()">
            </div>
        </div>

        <div class="table-responsive table-scroll-shell">
            <table class="modern-table rekon-table">
                <thead>
                    <tr>
                        <th style="width: 230px;">Nama Warga</th>
                        <th style="width: 140px;">Blok/No</th>
                        <th class="text-center">Jan</th>
                        <th class="text-center">Feb</th>
                        <th class="text-center">Mar</th>
                        <th class="text-center">Apr</th>
                        <th class="text-center">Mei</th>
                        <th class="text-center">Jun</th>
                        <th class="text-center">Jul</th>
                        <th class="text-center">Agu</th>
                        <th class="text-center">Sep</th>
                        <th class="text-center">Okt</th>
                        <th class="text-center">Nov</th>
                        <th class="text-center">Des</th>
                        <th style="width: 150px;" class="text-right">Tunggakan</th>
                    </tr>
                </thead>
                <tbody id="rekonsiliasi-table-body">
                    <!-- Diisi dinamis -->
                </tbody>
            </table>
        </div>
        
        <div id="rekonsiliasi-empty" class="hidden" style="text-align: center; padding: 40px 20px;">
            <p class="text-secondary">Tidak ada data ditemukan.</p>
        </div>
    </div>

</div>

<style>
.rekon-table th, .rekon-table td {
    padding: 14px 16px !important;
}

.month-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 auto;
    display: block;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.dot-lunas { background-color: #10b981; }
.dot-menunggak { background-color: #ef4444; }
.dot-empty { background-color: var(--border-color); opacity: 0.3; }

.modern-table.rekon-table tr:hover {
    background-color: var(--hover-bg);
}

.text-center { text-align: center; }
.text-right { text-align: right; }
</style>
