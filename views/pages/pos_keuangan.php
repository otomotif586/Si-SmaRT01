<!-- Page: Pos Keuangan -->
<div id="page-pos-keuangan" class="page-content hidden page-section">
    <div id="pos-cards-container" class="summary-3-grid-responsive mb-24">
        <p class="text-secondary text-center py-4 grid-col-full">Memuat data anggaran...</p>
    </div>

    <div class="glass-card" style="padding: 16px 20px; margin-bottom: 12px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <p class="text-secondary page-filter-desc">Kelola alokasi dana dan pengeluaran per komponen.</p>
        <div class="header-actions" style="display: flex; gap: 8px; align-items: center;">
            <button class="button-secondary button-sm" style="padding: 8px; border-radius: 10px;" onclick="prevMonthPos()"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></button>
            <div style="display: flex; gap: 6px;">
                <select id="filter-bulan-pos" class="input-field select-custom" style="font-size: 0.8125rem; padding: 8px 12px; min-width: 120px; border-radius: 10px;" onchange="loadPosKeuangan()"></select>
                <select id="filter-tahun-pos" class="input-field select-custom" style="font-size: 0.8125rem; padding: 8px 12px; min-width: 100px; border-radius: 10px;" onchange="loadPosKeuangan()"></select>
            </div>
            <button class="button-secondary button-sm" style="padding: 8px; border-radius: 10px;" onclick="nextMonthPos()"><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></button>
        </div>
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
<div id="modal-pengeluaran-pos" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-pengeluaran-pos').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Catat Pengeluaran</h2>
        <p class="text-secondary modal-desc">Gunakan dana dari pos <b id="pos-nama-label" class="text-color"></b>.</p>
        
        <input type="hidden" id="pos-input-nama">
        <div style="margin-bottom: 16px;">
            <label class="card-label">Nominal Pengeluaran (Rp)</label>
            <input type="number" id="pos-input-nominal" class="input-field" placeholder="0" style="margin-top: 8px; padding-left: 20px; font-size: 1.2rem; font-weight: bold; color: var(--text-color);">
        </div>
        <div style="margin-bottom: 16px;">
            <label class="card-label">Tanggal Pengeluaran</label>
            <input type="date" id="pos-input-tanggal" class="input-field mt-8 input-left-20">
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