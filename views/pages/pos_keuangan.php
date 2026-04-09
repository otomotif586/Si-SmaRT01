<!-- Page: Pos Keuangan -->
<div id="page-pos-keuangan" class="page-content hidden page-section">
    <div class="section-header">
        <div>
            <h3 class="section-title">Pos Anggaran</h3>
            <p class="text-secondary" style="font-size: 0.875rem; margin-top: 4px;">Kelola alokasi dana dan catat pengeluaran per komponen (Sampah, Keamanan, dll).</p>
        </div>
        <div class="header-actions" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="prevMonthPos()"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
            <select id="filter-bulan-pos" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; min-width: 120px; border-radius: 12px;" onchange="loadPosKeuangan()"></select>
            <select id="filter-tahun-pos" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; min-width: 100px; border-radius: 12px;" onchange="loadPosKeuangan()"></select>
            <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="nextMonthPos()"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
        </div>
    </div>

    <div id="pos-cards-container" class="grid-container" style="margin-bottom: 24px; gap: 16px;">
        <p class="text-secondary text-center py-4" style="grid-column: 1 / -1;">Memuat data anggaran...</p>
    </div>

    <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); background: rgba(255,255,255,0.02);">
            <h4 style="font-size: 1.1rem; margin: 0; font-weight: 700;">Riwayat Pengeluaran Pos</h4>
        </div>
        <div id="pos-history-container" style="display: flex; flex-direction: column;">
            <!-- Diisi oleh JS -->
        </div>
    </div>
</div>

<!-- Modal Catat Pengeluaran Pos -->
<div id="modal-pengeluaran-pos" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 32px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="document.getElementById('modal-pengeluaran-pos').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title" style="margin-bottom: 8px;">Catat Pengeluaran</h2>
        <p class="text-secondary" style="font-size: 0.875rem; margin-bottom: 24px;">Gunakan dana dari pos <b id="pos-nama-label" class="text-color"></b>.</p>
        
        <input type="hidden" id="pos-input-nama">
        <div style="margin-bottom: 16px;">
            <label class="card-label">Nominal Pengeluaran (Rp)</label>
            <input type="number" id="pos-input-nominal" class="input-field" placeholder="0" style="margin-top: 8px; padding-left: 20px; font-size: 1.2rem; font-weight: bold; color: var(--text-color);">
        </div>
        <div style="margin-bottom: 16px;">
            <label class="card-label">Tanggal Pengeluaran</label>
            <input type="date" id="pos-input-tanggal" class="input-field" style="margin-top: 8px; padding-left: 20px;">
        </div>
        <div style="margin-bottom: 32px;">
            <label class="card-label">Keterangan Penggunaan</label>
            <textarea id="pos-input-keterangan" class="input-field" style="margin-top: 8px; padding: 12px 20px; min-height: 80px; border-radius: 16px;" placeholder="Cth: Gaji Satpam Bulan Ini"></textarea>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="button-secondary flex-grow" onclick="document.getElementById('modal-pengeluaran-pos').classList.add('hidden')">Batal</button>
            <button class="button-primary flex-grow" onclick="submitPengeluaranPos(this)"><i data-lucide="check-circle" style="margin-right: 8px;"></i> Simpan</button>
        </div>
    </div>
</div>