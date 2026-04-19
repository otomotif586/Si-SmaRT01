<!-- Page: Rekonsiliasi & Audit Iuran (Annual Summary) -->
<div id="page-rekonsiliasi" class="page-content hidden page-section">
    
    <div class="glass-card" style="padding: 16px 20px; margin-bottom: 12px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <p class="text-secondary" style="font-size: 0.8125rem; margin: 0;">Monitoring kedisiplinan pembayaran warga per tahun buku.</p>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary-color);">Tahun:</label>
                    <input type="number" id="filter-tahun-rekonsiliasi" class="input-field" style="font-size: 0.8125rem; padding: 8px 12px; width: 85px; border-radius: 10px;" value="<?= date('Y') ?>" onchange="loadGlobalRekonsiliasi()">
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary-color);">Blok:</label>
                    <select id="filter-blok-rekonsiliasi" class="input-field select-custom" style="font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px;" onchange="loadGlobalRekonsiliasi()">
                        <option value="all">Semua Blok</option>
                    </select>
                </div>
            </div>
            <button class="button-secondary button-sm" style="padding: 8px 14px; border-radius: 10px; font-size: 0.8125rem;" onclick="exportRekonsiliasiCSV()"><i data-lucide="download" style="margin-right: 6px; width: 16px; height: 16px;"></i> Export</button>
        </div>
    </div>

    <!-- Reconciliation Summary Cards -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
            <div class="card-icon-deluxe icon-tone-info">
                <i data-lucide="users"></i>
            </div>
            <p class="card-label">Warga Audit</p>
            <h3 id="rekon-stat-warga" class="card-value text-color">0</h3>
            <div class="card-sub-info">Total KK terdaftar</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
            <div class="card-icon-deluxe icon-tone-success">
                <i data-lucide="check-circle"></i>
            </div>
            <p class="card-label">Lunas 100%</p>
            <h3 id="rekon-stat-lunas" class="card-value text-emerald">0</h3>
            <div class="card-sub-info">Pembayaran setahun penuh</div>
        </div>

        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
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
