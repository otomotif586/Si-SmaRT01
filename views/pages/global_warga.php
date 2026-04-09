<!-- Page: Global Warga -->
<div id="page-global-warga" class="page-content hidden page-section">
    <!-- Header & SUMMARY Global Warga -->
    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px;">
        <div class="glass-card" style="padding: 16px 20px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <p class="text-secondary" style="font-size: 0.8125rem; margin: 0;">Direktori seluruh warga RT.</p>
            <button class="button-primary button-sm" style="padding: 10px 16px; border-radius: 10px; font-size: 0.8125rem;" onclick="openFormWarga(true)"><i data-lucide="user-plus" style="margin-right: 6px; width: 16px; height: 16px;"></i> <span class="hide-text-mobile">Tambah Warga</span></button>
        </div>

        <!-- Deluxe 3-Card Summary -->
        <div class="summary-3-grid">
            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
                <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                    <i data-lucide="users"></i>
                </div>
                <p class="card-label">Total Warga</p>
                <h3 id="sum-global-warga" class="card-value text-color" style="font-size: 1.8rem;">0</h3>
                <div class="card-sub-info">Seluruh penduduk RT</div>
            </div>
            
            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
                <div class="card-icon-deluxe" style="color: #a855f7; background: rgba(168, 85, 247, 0.1);">
                    <i data-lucide="layout-grid"></i>
                </div>
                <p class="card-label">Penyebaran Blok</p>
                <h3 id="sum-global-blok" class="card-value text-color" style="font-size: 1.8rem;">0 Blok</h3>
                <div class="card-sub-info">Wilayah jangkauan</div>
            </div>

            <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
                <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i data-lucide="user-check"></i>
                </div>
                <p class="card-label">Kependudukan</p>
                <h3 id="sum-global-status-main" class="card-value text-emerald" style="font-size: 1.8rem;">0</h3>
                <div class="card-sub-info" id="sum-global-status-sub">Memuat status...</div>
                <div class="card-trend-badge up" id="sum-global-status-badge" style="display:none;">
                    <span id="sum-global-status-percent">0%</span> Tetap
                </div>
            </div>
        </div>
    </div>
        
    <!-- Pencarian & Filter Global -->
    <div style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap; margin-bottom: 12px;">
        <div class="input-with-icon" style="flex: 2; min-width: 200px;">
            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
            <input type="text" id="search-global-warga-input" placeholder="Cari nama atau NIK..." class="input-field" style="padding: 8px 12px 8px 40px; font-size: 0.8125rem; border-radius: 10px;" oninput="filterGlobalWargaList()">
        </div>
        <select id="filter-blok-global" class="input-field select-custom" style="flex: 1; font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px;" onchange="filterGlobalWargaList()">
            <option value="">Semua Blok</option>
        </select>
        <select id="filter-pernikahan-global" class="input-field select-custom" style="flex: 1; font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px;" onchange="filterGlobalWargaList()">
            <option value="">Status Nikah</option>
            <option value="Lajang">Lajang</option>
            <option value="Menikah">Menikah</option>
            <option value="Pisah">Pisah</option>
        </select>
        <select id="filter-status-global" class="input-field select-custom" style="flex: 1; font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px;" onchange="filterGlobalWargaList()">
            <option value="">Semua Status</option>
            <option value="Tetap">Tetap</option>
            <option value="Kontrak">Kontrak</option>
        </select>
    </div>
    
    <div class="list-container" id="global-warga-list-container"></div>
    
    <div id="global-warga-pagination" style="display: none; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
        <span id="global-warga-page-info" class="text-secondary" style="font-size: 0.875rem;">Menampilkan 0-0 dari 0</span>
        <div style="display: flex; gap: 8px;"><button class="button-secondary button-sm" style="padding: 6px 12px; border-radius: 8px; font-size: 0.8rem;" onclick="prevPageGlobalWarga()">Sebelumnya</button><button class="button-secondary button-sm" style="padding: 6px 12px; border-radius: 8px; font-size: 0.8rem;" onclick="nextPageGlobalWarga()">Selanjutnya</button></div>
    </div>
</div>