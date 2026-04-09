<!-- Page: Keuangan Jurnal Kas RT -->
<div id="page-keuangan" class="page-content hidden page-section">
    <!-- Premium Summary Section -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
            <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                <i data-lucide="wallet"></i>
            </div>
            <p class="card-label">Saldo Kas Tersedia</p>
            <h3 id="keuangan-saldo" class="card-value text-color" style="font-size: 1.5rem;">Rp 0</h3>
            <div class="card-sub-info">Total dana saat ini</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
            <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                <i data-lucide="arrow-down-left"></i>
            </div>
            <p class="card-label">Total Pemasukan</p>
            <h3 id="keuangan-pemasukan" class="card-value text-emerald" style="font-size: 1.5rem;">Rp 0</h3>
            <div class="card-sub-info">Arus kas masuk</div>
        </div>

        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
            <div class="card-icon-deluxe" style="color: #ef4444; background: rgba(239, 68, 68, 0.1);">
                <i data-lucide="arrow-up-right"></i>
            </div>
            <p class="card-label">Total Pengeluaran</p>
            <h3 id="keuangan-pengeluaran" class="card-value text-red" style="font-size: 1.5rem;">Rp 0</h3>
            <div class="card-sub-info">Arus kas keluar</div>
        </div>
    </div>

    <!-- Multi-Filter Canggih -->
    <div class="glass-card" style="padding: 20px; margin-bottom: 12px; border-radius: 20px; display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <p class="text-secondary" style="font-size: 0.8125rem; margin: 0;">Pencatatan arus kas masuk dan keluar tingkat pusat (RT).</p>
            <button class="button-primary button-sm" style="border-radius: 12px; padding: 10px 16px; font-size: 0.8125rem;" onclick="openFormKeuangan()"><i data-lucide="plus" style="margin-right: 6px; width: 16px; height: 16px;"></i> Catat Transaksi</button>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; border-top: 1px dashed var(--border-color); padding-top: 16px;">
            <div class="input-with-icon" style="flex: 2; min-width: 180px;">
                <i data-lucide="search"></i>
                <input type="text" id="keuangan-search" class="input-field" placeholder="Cari keterangan..." style="padding-left: 40px; border-radius: 12px;" oninput="filterKeuangan()">
            </div>
            <div style="flex: 1; min-width: 130px;">
                <select id="keuangan-jenis" class="input-field select-custom" style="border-radius: 12px;" onchange="filterKeuangan()">
                    <option value="">Semua Jenis</option>
                    <option value="Masuk">Masuk</option>
                    <option value="Keluar">Keluar</option>
                </select>
            </div>
            <div style="flex: 1.5; min-width: 220px; display: flex; gap: 6px; align-items: center;">
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="prevMonthKeuangan()"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
                <select id="filter-bulan-keuangan" class="input-field select-custom" style="font-size: 0.8125rem; padding: 10px; flex: 1; border-radius: 12px;" onchange="filterKeuangan()"></select>
                <select id="filter-tahun-keuangan" class="input-field select-custom" style="font-size: 0.8125rem; padding: 10px;flex: 1; border-radius: 12px;" onchange="filterKeuangan()"></select>
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="nextMonthKeuangan()"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
            </div>
        </div>
    </div>

    <div id="keuangan-list-container" style="display: flex; flex-direction: column; gap: 10px;"></div>
    
    <div id="keuangan-pagination" style="display: none; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
        <span id="keuangan-page-info" class="text-secondary" style="font-size: 0.875rem;">Menampilkan 0 data</span>
        <div style="display: flex; gap: 8px;">
            <button class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;" onclick="prevKeuanganPage()"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
            <div id="keuangan-page-numbers" style="display: flex; gap: 4px;"></div>
            <button class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;" onclick="nextKeuanganPage()"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
        </div>
    </div>
</div>

<!-- Laci Form Modal Transaksi Keuangan -->
<div id="drawer-keuangan" class="modal-overlay hidden" style="z-index: 10010 !important; align-items: flex-end; justify-content: flex-end; padding: 0;">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 id="drawer-keuangan-title" class="ws-title" style="display: inline-block;">Catat Transaksi</h2>
                <span id="form-keuangan-status-badge" class="badge bg-emerald-light text-emerald" style="vertical-align: top; margin-left: 8px;"><i data-lucide="arrow-down-left" style="width: 12px; height: 12px; display: inline;"></i> Pemasukan</span>
                <p class="text-secondary" style="font-size: 0.875rem; margin-top: 4px;">Bukukan pemasukan atau pengeluaran kas RT.</p>
            </div>
            <button class="modal-close-btn" onclick="closeFormKeuangan()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar">
            <input type="hidden" id="form-keuangan-id" value="0">
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="card-label font-bold text-color">Tipe Pembukuan</label>
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="form_keuangan_jenis" value="Masuk" class="hidden peer" onchange="updateFormKeuanganStatus()" checked>
                        <div class="glass-card peer-checked:border-emerald transaction-type-card" style="padding: 12px; border-radius: 16px; border: 2px solid transparent;">
                            <i data-lucide="arrow-down-left" class="text-emerald" style="margin: 0 auto 8px auto;"></i>
                            <div class="font-bold text-emerald text-center" style="font-size: 0.9rem;">Uang Masuk</div>
                        </div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="form_keuangan_jenis" value="Keluar" class="hidden peer" onchange="updateFormKeuanganStatus()">
                        <div class="glass-card peer-checked:border-red transaction-type-card" style="padding: 12px; border-radius: 16px; border: 2px solid transparent;">
                            <i data-lucide="arrow-up-right" class="text-red" style="margin: 0 auto 8px auto;"></i>
                            <div class="font-bold text-red text-center" style="font-size: 0.9rem;">Uang Keluar</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="card-label">Nominal (Rp)</label>
                <input type="number" id="form-keuangan-nominal" class="input-field" placeholder="0" style="font-size: 1.5rem; font-weight: 700; color: var(--text-color); padding-left: 20px;">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="card-label">Tanggal Transaksi</label>
                <input type="date" id="form-keuangan-tanggal" class="input-field" style="padding-left: 20px;">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="card-label">Keterangan / Rincian</label>
                <textarea id="form-keuangan-keterangan" class="input-field" style="padding: 16px; min-height: 100px; border-radius: 16px; resize: vertical;" placeholder="Cth: Pembelian alat kebersihan / Donasi Bp. Andi..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 16px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
                <label class="card-label">Lampiran Bukti (Nota/Kwitansi/Foto)</label>
                <div class="upload-premium-container">
                    <input type="file" id="form-keuangan-lampiran" class="upload-premium-input">
                    <div class="upload-premium-label">
                        <i data-lucide="upload-cloud" class="text-accent mb-2" style="width: 32px; height: 32px;"></i>
                        <span class="text-color font-bold">Tekan atau Seret File</span>
                        <span class="text-secondary text-xs mt-1">PNG, JPG, PDF (Maks. 5MB)</span>
                    </div>
                </div>
                <div id="form-keuangan-lampiran-preview" style="margin-top: 8px;"></div>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeFormKeuangan()">Batal</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanKeuangan()"><i data-lucide="save" style="margin-right: 8px;"></i> Simpan Transaksi</button>
        </div>
    </div>
</div>

<!-- Modal Pratilik Bukti Lampiran Full -->
<div id="modal-bukti-keuangan" class="modal-overlay hidden" style="z-index: 10020 !important;">
    <div class="glass-card" style="width: 100%; max-width: 600px; padding: 24px; position: relative;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px;" onclick="document.getElementById('modal-bukti-keuangan').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h3 class="section-title" style="margin-bottom: 16px;">Bukti Lampiran</h3>
        <div id="bukti-keuangan-content" style="text-align: center;"></div>
    </div>
</div>

<!-- Modal Detail Transaksi Lengkap -->
<div id="modal-detail-keuangan" class="modal-overlay hidden" style="z-index: 10025 !important;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 24px; position: relative; max-height: 90vh; display: flex; flex-direction: column;">
        <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px; z-index: 10;" onclick="document.getElementById('modal-detail-keuangan').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h3 class="section-title" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="file-text" class="text-emerald"></i> Detail Transaksi</h3>
        <div id="detail-keuangan-content" class="hide-scrollbar" style="overflow-y: auto; flex: 1; padding-right: 8px;"></div>
    </div>
</div>