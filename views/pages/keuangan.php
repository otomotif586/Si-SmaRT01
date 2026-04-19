<!-- Page: Keuangan Jurnal Kas RT -->
<div id="page-keuangan" class="page-content hidden page-section">
    <!-- Premium Summary Section -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item stagger-delay-1">
            <div class="card-icon-deluxe icon-tone-info">
                <i data-lucide="wallet"></i>
            </div>
            <p class="card-label">Saldo Kas Tersedia</p>
            <h3 id="keuangan-saldo" class="card-value text-color card-value-lg">Rp 0</h3>
            <div class="card-sub-info">Total dana saat ini</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item stagger-delay-2">
            <div class="card-icon-deluxe icon-tone-success">
                <i data-lucide="arrow-down-left"></i>
            </div>
            <p class="card-label">Total Pemasukan</p>
            <h3 id="keuangan-pemasukan" class="card-value text-emerald card-value-lg">Rp 0</h3>
            <div class="card-sub-info">Arus kas masuk</div>
        </div>

        <div class="glass-card-deluxe stagger-item stagger-delay-3">
            <div class="card-icon-deluxe icon-tone-danger">
                <i data-lucide="arrow-up-right"></i>
            </div>
            <p class="card-label">Total Pengeluaran</p>
            <h3 id="keuangan-pengeluaran" class="card-value text-red card-value-lg">Rp 0</h3>
            <div class="card-sub-info">Arus kas keluar</div>
        </div>
    </div>

    <!-- Multi-Filter Canggih -->
    <div class="glass-card page-filter-card page-filter-card--stack">
        <div class="page-filter-row">
            <p class="text-secondary page-filter-desc">Pencatatan arus kas masuk dan keluar tingkat pusat (RT).</p>
            <button class="button-primary button-sm compact-btn" onclick="openFormKeuangan()"><i data-lucide="plus" class="icon-16 icon-mr-6"></i> Catat Transaksi</button>
        </div>
        
        <div class="page-filter-row page-filter-divider items-end">
            <div class="input-with-icon input-grow-wide minw-180">
                <i data-lucide="search"></i>
                <input type="text" id="keuangan-search" class="input-field input-pl-40" placeholder="Cari keterangan..." oninput="filterKeuangan()">
            </div>
            <div class="flex-1 minw-130">
                <select id="keuangan-jenis" class="input-field select-custom compact-control" onchange="filterKeuangan()">
                    <option value="">Semua Jenis</option>
                    <option value="Masuk">Masuk</option>
                    <option value="Keluar">Keluar</option>
                </select>
            </div>
            <div class="inline-wrap-8 minw-220">
                <button class="button-secondary button-sm compact-btn-icon" onclick="prevMonthKeuangan()"><i data-lucide="chevron-left" class="icon-16"></i></button>
                <select id="filter-bulan-keuangan" class="input-field select-custom compact-control flex-1" onchange="filterKeuangan()"></select>
                <select id="filter-tahun-keuangan" class="input-field select-custom compact-control flex-1" onchange="filterKeuangan()"></select>
                <button class="button-secondary button-sm compact-btn-icon" onclick="nextMonthKeuangan()"><i data-lucide="chevron-right" class="icon-16"></i></button>
            </div>
        </div>
    </div>

    <div id="keuangan-list-container" class="flex-col-gap-10"></div>
    
    <div id="keuangan-pagination" class="pagination-bar">
        <span id="keuangan-page-info" class="text-secondary pagination-bar-info">Menampilkan 0 data</span>
        <div class="pagination-actions">
            <button class="button-secondary button-sm compact-btn" onclick="prevKeuanganPage()"><i data-lucide="chevron-left" class="icon-16"></i></button>
            <div id="keuangan-page-numbers" class="row-gap-4"></div>
            <button class="button-secondary button-sm compact-btn" onclick="nextKeuanganPage()"><i data-lucide="chevron-right" class="icon-16"></i></button>
        </div>
    </div>
</div>

<!-- Laci Form Modal Transaksi Keuangan -->
<div id="drawer-keuangan" class="modal-overlay hidden overlay-z10010-drawer">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 id="drawer-keuangan-title" class="ws-title d-inline-block">Catat Transaksi</h2>
                <span id="form-keuangan-status-badge" class="badge bg-emerald-light text-emerald badge-top-offset"><i data-lucide="arrow-down-left" class="icon-inline icon-12"></i> Pemasukan</span>
                <p class="text-secondary drawer-subtitle">Bukukan pemasukan atau pengeluaran kas RT.</p>
            </div>
            <button class="modal-close-btn" onclick="closeFormKeuangan()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar">
            <input type="hidden" id="form-keuangan-id" value="0">
            <div class="form-group mb-24">
                <label class="card-label font-bold text-color">Tipe Pembukuan</label>
                <div class="inline-wrap-12 mt-8">
                    <label class="option-tile-label">
                        <input type="radio" name="form_keuangan_jenis" value="Masuk" class="hidden peer" onchange="updateFormKeuanganStatus()" checked>
                        <div class="glass-card peer-checked:border-emerald transaction-type-card transaction-type-card-shell">
                            <i data-lucide="arrow-down-left" class="text-emerald icon-center-mb-8"></i>
                            <div class="font-bold text-emerald text-center text-size-09">Uang Masuk</div>
                        </div>
                    </label>
                    <label class="option-tile-label">
                        <input type="radio" name="form_keuangan_jenis" value="Keluar" class="hidden peer" onchange="updateFormKeuanganStatus()">
                        <div class="glass-card peer-checked:border-red transaction-type-card transaction-type-card-shell">
                            <i data-lucide="arrow-up-right" class="text-red icon-center-mb-8"></i>
                            <div class="font-bold text-red text-center text-size-09">Uang Keluar</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="form-group mb-16">
                <label class="card-label">Nominal (Rp)</label>
                <input type="number" id="form-keuangan-nominal" class="input-field input-amount-emphasis" placeholder="0">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Tanggal Transaksi</label>
                <input type="date" id="form-keuangan-tanggal" class="input-field input-left-20">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Keterangan / Rincian</label>
                <textarea id="form-keuangan-keterangan" class="input-field textarea-compact textarea-100" placeholder="Cth: Pembelian alat kebersihan / Donasi Bp. Andi..."></textarea>
            </div>
            <div class="form-group section-top-divider">
                <label class="card-label">Lampiran Bukti (Nota/Kwitansi/Foto)</label>
                <div class="upload-premium-container">
                    <input type="file" id="form-keuangan-lampiran" class="upload-premium-input">
                    <div class="upload-premium-label">
                        <i data-lucide="upload-cloud" class="text-accent mb-2 icon-32"></i>
                        <span class="text-color font-bold">Tekan atau Seret File</span>
                        <span class="text-secondary text-xs mt-1">PNG, JPG, PDF (Maks. 5MB)</span>
                    </div>
                </div>
                <div id="form-keuangan-lampiran-preview" class="mt-8"></div>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeFormKeuangan()">Batal</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanKeuangan()"><i data-lucide="save" class="icon-mr-8"></i> Simpan Transaksi</button>
        </div>
    </div>
</div>

<!-- Modal Pratilik Bukti Lampiran Full -->
<div id="modal-bukti-keuangan" class="modal-overlay hidden overlay-z10020">
    <div class="glass-card modal-shell modal-shell-lg">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-bukti-keuangan').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h3 class="section-title mb-16">Bukti Lampiran</h3>
        <div id="bukti-keuangan-content" class="text-center"></div>
    </div>
</div>

<!-- Modal Detail Transaksi Lengkap -->
<div id="modal-detail-keuangan" class="modal-overlay hidden overlay-z10025">
    <div class="glass-card modal-shell modal-shell-md modal-shell-scroll">
        <button class="modal-close-btn modal-close-top-right z-10" onclick="document.getElementById('modal-detail-keuangan').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h3 class="section-title mb-16 inline-wrap-8"><i data-lucide="file-text" class="text-emerald"></i> Detail Transaksi</h3>
        <div id="detail-keuangan-content" class="hide-scrollbar scroll-pane-pr8"></div>
    </div>
</div>