<!-- Page: Global Warga -->
<div id="page-global-warga" class="page-content hidden page-section">
    <!-- Header & SUMMARY Global Warga -->
    <div class="gw-header-stack">
        <div class="glass-card gw-top-shell">
            <p class="text-secondary page-filter-desc">Direktori seluruh warga RT.</p>
            <button class="button-primary button-sm gw-add-btn" onclick="openFormWarga(true)"><i data-lucide="user-plus" class="gw-icon-16-mr6"></i> <span class="hide-text-mobile">Tambah Warga</span></button>
        </div>

        <!-- Deluxe 3-Card Summary -->
        <div class="summary-3-grid">
            <div class="glass-card-deluxe stagger-item stagger-delay-1">
                <div class="card-icon-deluxe gw-icon-blue">
                    <i data-lucide="users"></i>
                </div>
                <p class="card-label">Total Warga</p>
                <h3 id="sum-global-warga" class="card-value text-color card-value-xl">0</h3>
                <div class="card-sub-info">Seluruh penduduk RT</div>
            </div>
            
            <div class="glass-card-deluxe stagger-item stagger-delay-2">
                <div class="card-icon-deluxe gw-icon-purple">
                    <i data-lucide="layout-grid"></i>
                </div>
                <p class="card-label">Penyebaran Blok</p>
                <h3 id="sum-global-blok" class="card-value text-color card-value-xl">0 Blok</h3>
                <div class="card-sub-info">Wilayah jangkauan</div>
            </div>

            <div class="glass-card-deluxe stagger-item stagger-delay-3">
                <div class="card-icon-deluxe gw-icon-emerald">
                    <i data-lucide="user-check"></i>
                </div>
                <p class="card-label">Kependudukan</p>
                <h3 id="sum-global-status-main" class="card-value text-emerald card-value-xl">0</h3>
                <div class="card-sub-info" id="sum-global-status-sub">Memuat status...</div>
                <div class="card-trend-badge up gw-hidden" id="sum-global-status-badge">
                    <span id="sum-global-status-percent">0%</span> Tetap
                </div>
            </div>
        </div>
    </div>
        
    <!-- Pencarian & Filter Global -->
    <div class="gw-filter-row">
        <div class="input-with-icon gw-search-wrap">
            <i data-lucide="search" class="gw-icon-18"></i>
            <input type="text" id="search-global-warga-input" placeholder="Cari nama atau NIK..." class="input-field gw-search-input" oninput="filterGlobalWargaList()">
        </div>
        <select id="filter-blok-global" class="input-field select-custom gw-filter-select" onchange="filterGlobalWargaList()">
            <option value="">Semua Blok</option>
        </select>
        <select id="filter-pernikahan-global" class="input-field select-custom gw-filter-select" onchange="filterGlobalWargaList()">
            <option value="">Status Nikah</option>
            <option value="Lajang">Lajang</option>
            <option value="Menikah">Menikah</option>
            <option value="Pisah">Pisah</option>
        </select>
        <select id="filter-status-global" class="input-field select-custom gw-filter-select" onchange="filterGlobalWargaList()">
            <option value="">Semua Status</option>
            <option value="Tetap">Tetap</option>
            <option value="Kontrak">Kontrak</option>
        </select>
    </div>
    
    <div class="list-container" id="global-warga-list-container"></div>
    
    <div id="global-warga-pagination" class="gw-pagination">
        <span id="global-warga-page-info" class="text-secondary gw-page-info">Menampilkan 0-0 dari 0</span>
        <div class="gw-page-actions"><button class="button-secondary button-sm gw-page-btn" onclick="prevPageGlobalWarga()">Sebelumnya</button><button class="button-secondary button-sm gw-page-btn" onclick="nextPageGlobalWarga()">Selanjutnya</button></div>
    </div>
</div>

<style>
.gw-header-stack { display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px; }
.gw-top-shell {
    padding: 16px 20px;
    border-radius: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.gw-add-btn { padding: 10px 16px; border-radius: 10px; font-size: 0.8125rem; }
.gw-icon-16-mr6 { margin-right: 6px; width: 16px; height: 16px; }

.gw-icon-blue { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
.gw-icon-purple { color: #a855f7; background: rgba(168, 85, 247, 0.1); }
.gw-icon-emerald { color: #10b981; background: rgba(16, 185, 129, 0.1); }
.gw-hidden { display: none; }

.gw-filter-row { display: flex; gap: 10px; width: 100%; flex-wrap: wrap; margin-bottom: 12px; }
.gw-search-wrap { flex: 2; min-width: 200px; }
.gw-icon-18 { width: 18px; height: 18px; }
.gw-search-input { padding: 8px 12px 8px 40px; font-size: 0.8125rem; border-radius: 10px; }
.gw-filter-select { flex: 1; font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px; }

.gw-pagination {
    display: none;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--border-color);
}
.gw-page-info { font-size: 0.875rem; }
.gw-page-actions { display: flex; gap: 8px; }
.gw-page-btn { padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; }
</style>