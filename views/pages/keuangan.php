<!-- Page: Keuangan Jurnal Kas RT -->
<div id="page-keuangan" class="page-content hidden page-section">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h3 class="section-title">Buku Kas Utama</h3>
            <p class="text-secondary" style="font-size: 0.875rem; margin-top: 4px;">Pencatatan arus kas masuk dan keluar tingkat pusat (RT).</p>
        </div>
        <div class="header-actions">
            <button class="button-primary button-sm" style="border-radius: 12px; padding: 10px 16px;" onclick="openFormKeuangan()"><i data-lucide="plus" style="margin-right: 6px; width: 18px; height: 18px;"></i> Catat Transaksi</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-wrapper">
        <div class="summary-card-modern" style="flex: 1;">
            <div class="summary-icon-wrapper bg-blue-light text-blue"><i data-lucide="wallet"></i></div>
            <p class="card-label m-0">Saldo Kas Tersedia</p>
            <h3 id="keuangan-saldo" class="card-value text-color m-0">Rp 0</h3>
        </div>
        <div class="summary-card-modern" style="flex: 1;">
            <div class="summary-icon-wrapper bg-emerald-light text-emerald"><i data-lucide="arrow-down-left"></i></div>
            <p class="card-label m-0">Total Pemasukan</p>
            <h3 id="keuangan-pemasukan" class="card-value text-emerald m-0">Rp 0</h3>
        </div>
        <div class="summary-card-modern" style="flex: 1;">
            <div class="summary-icon-wrapper bg-red-light text-red"><i data-lucide="arrow-up-right"></i></div>
            <p class="card-label m-0">Total Pengeluaran</p>
            <h3 id="keuangan-pengeluaran" class="card-value text-red m-0">Rp 0</h3>
        </div>
    </div>

    <!-- Multi-Filter Canggih -->
    <div class="glass-card" style="padding: 20px; margin-bottom: 24px; border-radius: 20px; display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div class="input-with-icon" style="flex: 1; min-width: 200px;">
                <i data-lucide="search"></i>
                <input type="text" id="keuangan-search" class="input-field" placeholder="Cari keterangan..." style="padding-left: 40px; border-radius: 12px;" oninput="filterKeuangan()">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="card-label" style="margin-bottom: 4px; display: block;">Jenis Transaksi</label>
                <select id="keuangan-jenis" class="input-field select-custom" style="border-radius: 12px;" onchange="filterKeuangan()">
                    <option value="">Semua Transaksi</option>
                    <option value="Masuk">Pemasukan (Masuk)</option>
                    <option value="Keluar">Pengeluaran (Keluar)</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 250px; display: flex; flex-direction: column;">
                <label class="card-label" style="margin-bottom: 4px; display: block;">Periode Bulan</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="prevMonthKeuangan()" title="Bulan Sebelumnya"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
                    <select id="filter-bulan-keuangan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; flex: 1; min-width: 80px; border-radius: 12px;" onchange="filterKeuangan()"></select>
                    <select id="filter-tahun-keuangan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px;flex: 1; min-width: 80px; border-radius: 12px;" onchange="filterKeuangan()"></select>
                    <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="nextMonthKeuangan()" title="Bulan Selanjutnya"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- List Data Dinamis -->
    <div id="keuangan-list-container" style="display: flex; flex-direction: column; gap: 12px;"></div>
    
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
                <input type="file" id="form-keuangan-lampiran" class="input-field file-input-modern" style="width: 100%;">
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