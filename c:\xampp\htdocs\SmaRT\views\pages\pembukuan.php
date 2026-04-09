<!-- Page: Laporan Pembukuan (Trial Balance) -->
<div id="page-pembukuan" class="page-content hidden page-section">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h3 class="section-title">Pembukuan Akhir</h3>
            <p class="text-secondary" style="font-size: 0.875rem; margin-top: 4px;">Ringkasan neraca, arus kas, dan saldo.</p>
        </div>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 8px; align-items: center;">
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="prevMonthPembukuan()"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
                <select id="filter-bulan-pembukuan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; min-width: 100px; border-radius: 12px;" onchange="loadPembukuan()"></select>
                <select id="filter-tahun-pembukuan" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; min-width: 100px; border-radius: 12px;" onchange="loadPembukuan()"></select>
                <button class="button-secondary button-sm" style="padding: 10px; border-radius: 12px;" onclick="nextMonthPembukuan()"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
            </div>
            <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px;" onclick="exportPembukuanCSV()"><i data-lucide="download" style="margin-right: 6px; width: 18px; height: 18px;"></i> Export</button>
        </div>
    </div>

    <!-- SaaS Style Summary Dashboard -->
    <div class="grid-container" style="margin-bottom: 24px;">
        <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--accent-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Debit (Masuk)</p>
                    <h2 id="pb-debit" class="text-emerald" style="font-size: 2rem; font-weight: 800; margin: 0;">Rp 0</h2>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;"><i data-lucide="trending-up" style="width: 24px; height: 24px;"></i></div>
            </div>
            <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;"><span class="badge bg-emerald-light text-emerald" style="padding: 2px 6px;">+ Pemasukan</span> periode ini</p>
        </div>
        
        <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Kredit (Keluar)</p>
                    <h2 id="pb-kredit" class="text-red" style="font-size: 2rem; font-weight: 800; margin: 0;">Rp 0</h2>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center;"><i data-lucide="trending-down" style="width: 24px; height: 24px;"></i></div>
            </div>
            <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;"><span class="badge bg-red-light text-red" style="padding: 2px 6px;">- Pengeluaran</span> periode ini</p>
        </div>

        <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; background: linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 15%, transparent), transparent); border: 1px solid var(--accent-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Saldo Akhir</p>
                    <h2 id="pb-saldo" class="text-color" style="font-size: 2rem; font-weight: 800; margin: 0;">Rp 0</h2>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: var(--accent-color); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px -5px var(--accent-color);"><i data-lucide="wallet" style="width: 24px; height: 24px;"></i></div>
            </div>
            <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;">Total kas saat ini</p>
        </div>
    </div>

    <!-- Tab Navigasi -->
    <div class="sub-nav-container hide-on-print" style="margin-bottom: 24px; border-bottom: 1px solid var(--border-color);">
        <button class="sub-nav-tab active" onclick="switchPbTab('pb-tab-ledger', this)">
            <i data-lucide="list"></i> Buku Besar
        </button>
        <button class="sub-nav-tab" onclick="switchPbTab('pb-tab-laporan', this)">
            <i data-lucide="briefcase"></i> Pos Anggaran
        </button>
    </div>

    <!-- Tab 1: General Ledger (Rincian Transaksi Baris) -->
    <div id="pb-tab-ledger" class="pb-tab-content">
        <div class="glass-card hide-card-border-print" style="padding: 0; overflow: hidden; border-radius: 20px; margin-bottom: 24px;">
            <div class="hide-on-print" style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); background: rgba(255,255,255,0.02); display: flex; justify-content: space-between; align-items: center;">
                <h4 style="font-size: 1.1rem; margin: 0; font-weight: 700; display: flex; align-items: center; gap: 8px;"><i data-lucide="list" class="text-blue"></i> Buku Besar</h4>
            </div>
            
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="modern-table rekon-table" style="width: 100%; border-collapse: collapse; white-space: nowrap;">
                <thead style="background: var(--secondary-bg);">
                    <tr>
                        <th style="padding: 16px 24px; width: 120px;">Tanggal</th>
                        <th style="padding: 16px 24px;">Keterangan</th>
                        <th style="padding: 16px 24px;">Pos</th>
                        <th class="text-right" style="padding: 16px 24px; width: 130px;">Masuk</th>
                        <th class="text-right" style="padding: 16px 24px; width: 130px;">Keluar</th>
                        <th class="text-right" style="padding: 16px 24px; width: 140px; color: var(--text-color);">Saldo</th>
                    </tr>
                </thead>
                <tbody id="pb-ledger-body">
                    <!-- Rows generated by JS -->
                </tbody>
                <tfoot id="pb-ledger-foot" style="background: var(--secondary-bg); font-weight: bold;">
                    <!-- Footer generated by JS -->
                </tfoot>
            </table>
        </div>
        <div id="pb-pagination" style="display: none; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px dashed var(--border-color); background: rgba(255,255,255,0.01);">
            <span id="pb-page-info" class="text-secondary" style="font-size: 0.8125rem;">Menampilkan 0 data</span>
            <div style="display: flex; gap: 8px;">
                <button class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;" onclick="prevPbPage()"><i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i></button>
                <div id="pb-page-numbers" style="display: flex; gap: 4px;"></div>
                <button class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;" onclick="nextPbPage()"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button>
            </div>
        </div>
        </div> <!-- Penutup glass-card -->
    </div> <!-- Penutup Tab 1 -->

    <!-- Tab 2: Laporan Kas Warga (Sisa Detail Pos) -->
    <div id="pb-tab-laporan" class="pb-tab-content hidden">
        <div class="glass-card hide-card-border-print" style="padding: 32px; border-radius: 20px; margin-bottom: 24px; background: var(--secondary-bg);">
            
            <!-- Kop Surat / Judul Laporan -->
            <div style="text-align: center; margin-bottom: 32px;">
                <div class="hide-on-print" style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: color-mix(in srgb, var(--accent-color) 10%, transparent); color: var(--accent-color); border-radius: 20px; margin-bottom: 16px;">
                    <i data-lucide="briefcase" style="width: 32px; height: 32px;"></i>
                </div>
                <h2 style="margin: 0 0 8px 0; font-size: 1.8rem; font-weight: 800; color: var(--text-color);">Pos Anggaran</h2>
                <p class="text-secondary" style="margin: 0; font-size: 0.95rem;">Rincian mutasi dan sisa dana tiap pos</p>
                <p class="text-color font-bold" style="margin: 8px 0 0 0; font-size: 1rem;" id="laporan-periode-text">Periode: Semua Bulan</p>
            </div>
            
            <div id="laporan-pos-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 32px;">
                <!-- Cards generated by JS -->
            </div>

            <div style="padding: 24px; border-radius: 16px; background: linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 10%, transparent), transparent); border: 1px solid var(--accent-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 4px 0; color: var(--text-secondary-color); font-weight: 600; text-transform: uppercase; font-size: 0.8rem;">Total Saldo Pos</p>
                    <h2 id="laporan-total-sisa" style="margin: 0; font-size: 2rem; font-weight: 800; color: var(--text-color);">Rp 0</h2>
                </div>
                <button class="button-primary hide-on-print" onclick="window.print()" style="border-radius: 12px;"><i data-lucide="printer" style="width: 18px; height: 18px; margin-right: 8px;"></i> Cetak PDF Laporan</button>
            </div>

            <!-- History Transaksi di Bawah Laporan -->
            <div style="margin-top: 40px; padding-top: 32px; border-top: 2px dashed var(--border-color);">
                <h4 style="font-size: 1.2rem; margin: 0 0 16px 0; font-weight: 800; color: var(--text-color);">Riwayat Transaksi</h4>
                <div class="table-responsive" style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;">
                    <table class="modern-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead style="background: var(--secondary-bg); border-bottom: 2px solid var(--border-color);">
                            <tr>
                                <th style="padding: 12px 20px;">Tanggal</th>
                                <th style="padding: 12px 20px;">Pos</th>
                                <th style="padding: 12px 20px;">Keterangan</th>
                                <th style="padding: 12px 20px; text-align: right;">Nominal</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-pos-history-body">
                            <!-- Diisi oleh JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<style>
/* Gaya Spesifik Ketika Pengguna Mencetak ke PDF / Kertas */
@media print {
    body { background: #fff !important; color: #000 !important; }
    #sidebar, #main-header, .header-actions, .hide-on-print, .grid-container, .section-header { display: none !important; }
    #main-content { margin-left: 0 !important; padding: 0 !important; }
    .hide-card-border-print { border: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important; margin: 0 !important;}
    .text-color, .text-secondary { color: #000 !important; }
    /* Paksa grid tetap berbentuk kotak saat di-print */
    #laporan-pos-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .pos-laporan-card { border: 1px solid #ccc !important; background: #fff !important; page-break-inside: avoid; }
    .text-emerald { color: #059669 !important; }
    .text-red { color: #dc2626 !important; }
}
</style>
</div>