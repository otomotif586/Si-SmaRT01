<!-- Page: Rekonsiliasi & Audit Iuran (Annual Summary) -->
<div id="page-rekonsiliasi" class="page-content hidden page-section">
    
    <div class="section-header">
        <div>
            <h3 class="section-title">Audit & Rekonsiliasi Iuran</h3>
            <p class="section-subtitle">Monitoring kedisiplinan pembayaran warga per tahun buku.</p>
        </div>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <label for="filter-tahun-rekonsiliasi" class="text-secondary" style="font-size: 0.8125rem; font-weight: 600;">Tahun Buku:</label>
                <input type="number" id="filter-tahun-rekonsiliasi" class="input-field" style="font-size: 0.875rem; padding: 10px; width: 100px; border-radius: 12px;" value="<?= date('Y') ?>" onchange="loadGlobalRekonsiliasi()">
                
                <label for="filter-blok-rekonsiliasi" class="text-secondary" style="font-size: 0.8125rem; font-weight: 600; margin-left: 8px;">Filter Blok:</label>
                <select id="filter-blok-rekonsiliasi" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; width: auto; min-width: 130px; border-radius: 12px;" onchange="loadGlobalRekonsiliasi()">
                    <option value="all">Semua Blok</option>
                </select>
            </div>
            <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px;" onclick="exportRekonsiliasiCSV()"><i data-lucide="download" style="margin-right: 6px; width: 18px; height: 18px;"></i> Export Data</button>
        </div>
    </div>

    <!-- Reconciliation Summary Cards -->
    <div class="summary-wrapper" style="margin-bottom: 32px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid var(--accent-color);">
            <div class="summary-icon-wrapper bg-blue-light text-blue"><i data-lucide="users"></i></div>
            <p class="card-label m-0">Total Warga</p>
            <h3 id="rekon-stat-warga" class="card-value text-color m-0">0</h3>
        </div>
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid #10b981;">
            <div class="summary-icon-wrapper bg-emerald-light text-emerald"><i data-lucide="check-circle-2"></i></div>
            <p class="card-label m-0">Lunas 100% (1th)</p>
            <h3 id="rekon-stat-lunas" class="card-value text-emerald m-0">0</h3>
        </div>
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid #ef4444;">
            <div class="summary-icon-wrapper bg-red-light text-red"><i data-lucide="alert-triangle"></i></div>
            <p class="card-label m-0">Warga Menunggak</p>
            <h3 id="rekon-stat-menunggak" class="card-value text-red m-0">0</h3>
        </div>
        <div class="summary-card-modern glass-card" style="border-bottom: 4px solid #f59e0b;">
            <div class="summary-icon-wrapper bg-orange-light text-orange"><i data-lucide="help-circle"></i></div>
            <p class="card-label m-0">Estimasi Piutang</p>
            <h3 id="rekon-stat-piutang" class="card-value text-orange m-0">Rp 0</h3>
        </div>
    </div>

    <!-- Legend -->
    <div class="glass-card" style="padding: 12px 24px; margin-bottom: 16px; border-radius: 12px; display: flex; gap: 20px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span style="width:10px; height:10px; border-radius:50%; background:#10b981;"></span> Lunas</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span style="width:10px; height:10px; border-radius:50%; background:#ef4444;"></span> Menunggak</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span style="width:10px; height:10px; border-radius:50%; background:var(--border-color);"></span> Belum Berjalan</div>
    </div>

    <!-- Main Reconciliation Table -->
    <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h4 style="font-size: 1.1rem; margin: 0; font-weight: 700;">Daftar Status Iuran Per Bulan</h4>
            <div class="input-with-icon" style="max-width: 250px; width: 100%;">
                <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                <input type="text" id="search-rekonsiliasi" class="input-field" placeholder="Cari Warga..." oninput="filterRekonsiliasi()" style="padding: 10px 16px 10px 40px; font-size: 0.8125rem; border-radius: 10px;">
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto;">
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
