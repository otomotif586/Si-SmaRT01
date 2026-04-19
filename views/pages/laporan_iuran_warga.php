<!-- Page: Laporan Iuran Warga (Detailed History with Relationship Lines) -->
<div id="page-laporan-iuran-warga" class="page-content hidden page-section">
    
    <!-- Summary Cards -->
    <!-- Deluxe Summary Section -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item stagger-delay-1">
            <div class="card-icon-deluxe icon-tone-info">
                <i data-lucide="users"></i>
            </div>
            <p class="card-label">Total Warga</p>
            <h3 id="laporan-warga-total" class="card-value text-color card-value-lg">0</h3>
            <div class="card-sub-info">Pemilih data aktif</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item stagger-delay-2">
            <div class="card-icon-deluxe icon-tone-success">
                <i data-lucide="check-circle"></i>
            </div>
            <p class="card-label">Lunas (12 Bln)</p>
            <h3 id="laporan-warga-lunas" class="card-value text-emerald card-value-lg">0</h3>
            <div class="card-sub-info">Pembayaran sempurna</div>
        </div>

        <div class="glass-card-deluxe stagger-item stagger-delay-3">
            <div class="card-icon-deluxe icon-tone-danger">
                <i data-lucide="alert-circle"></i>
            </div>
            <p class="card-label">Ada Tunggakan</p>
            <h3 id="laporan-warga-menunggak" class="card-value text-red card-value-lg">0</h3>
            <div class="card-sub-info">Perlu ditagih</div>
        </div>
    </div>

    <!-- Filter Glass Card -->
    <div class="glass-card page-filter-card gap-16">
        <p class="text-secondary page-filter-desc">Visualisasi pelunasan iuran warga per tahun buku.</p>
        <div class="header-actions page-filter-actions gap-12">
            <div class="page-filter-group page-filter-group--spacious">
                <div class="page-filter-group">
                    <label class="compact-label">Tahun:</label>
                    <input type="number" id="filter-tahun-laporan-warga" class="input-field compact-control w-85" value="<?= date('Y') ?>" onchange="loadLaporanIuranWarga()">
                </div>
                <div class="page-filter-group">
                    <label class="compact-label">Blok:</label>
                    <select id="filter-blok-laporan-warga" class="input-field select-custom compact-control min-w-[120px]" onchange="loadLaporanIuranWarga()">
                        <option value="all">Semua Blok</option>
                    </select>
                </div>
            </div>
            <button class="button-secondary button-sm compact-btn" onclick="exportLaporanWargaCSV()"><i data-lucide="download" class="icon-16 mr-[6px]"></i> Export</button>
        </div>
    </div>

    <!-- Legend -->
    <div class="glass-card legend-wrap-end">
        <div class="legend-inline legend-inline-xs"><span class="rekon-dot rekon-dot-lunas dot-mini"></span> Lunas</div>
        <div class="legend-inline legend-inline-xs"><span class="line-mini line-mini-accent"></span> Relasi Tunggakan</div>
        <div class="legend-inline legend-inline-xs"><span class="line-mini line-mini-info"></span> Bayar Lebih Awal</div>
        <div class="legend-inline legend-inline-xs"><span class="rekon-dot rekon-dot-menunggak dot-mini"></span> Belum Bayar</div>
        <div class="legend-inline legend-inline-xs"><span class="rekon-dot rekon-dot-sebelum dot-mini"></span> Di Luar Periode</div>
    </div>

    <!-- Main Table Container with SVG Overlay -->
    <div class="glass-card table-panel pos-relative">
        <div class="table-panel-head">
            <h4 class="table-panel-title">Mapping Pelunasan Iuran</h4>
            <div class="input-with-icon search-shell-250">
                <i data-lucide="search" class="icon-18"></i>
                <input type="text" id="search-laporan-warga" class="input-field input-field-compact" placeholder="Cari Warga..." oninput="filterLaporanWarga()">
            </div>
        </div>

        <div class="table-responsive table-scroll-shell">
            <!-- Continer for SVG + Table to handle scroll together -->
            <div id="laporan-warga-scroll-wrapper" class="scroll-relasi-wrapper">
                <!-- SVG Layer -->
                <svg id="svg-relations" class="svg-relasi-overlay">
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--accent-color)" opacity="0.6" />
                        </marker>
                        <marker id="arrowhead-advance" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--status-info)" opacity="0.6" />
                        </marker>
                    </defs>
                </svg>

                <table id="laporan-warga-table" class="modern-table rekon-table ws-relasi-table">
                    <thead>
                        <tr>
                            <th class="ws-col-warga">Nama Warga</th>
                            <th class="ws-col-blok">NO/Blok</th>
                            <th class="text-center ws-col-status">Status</th>
                            <th class="text-center ws-col-month">Jan</th>
                            <th class="text-center ws-col-month">Feb</th>
                            <th class="text-center ws-col-month">Mar</th>
                            <th class="text-center ws-col-month">Apr</th>
                            <th class="text-center ws-col-month">Mei</th>
                            <th class="text-center ws-col-month">Jun</th>
                            <th class="text-center ws-col-month">Jul</th>
                            <th class="text-center ws-col-month">Agu</th>
                            <th class="text-center ws-col-month">Sep</th>
                            <th class="text-center ws-col-month">Okt</th>
                            <th class="text-center ws-col-month">Nov</th>
                            <th class="text-center ws-col-month">Des</th>
                        </tr>
                    </thead>
                    <tbody id="laporan-warga-table-body">
                        <!-- Diisi dinamis -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Controls -->
        <div id="laporan-warga-pagination" class="glass-card pagination-card-shell">
            <div id="laporan-warga-page-info" class="text-secondary pagination-bar-info">Menampilkan 1-20 dari 100 data</div>
            <div class="pagination-buttons pagination-actions">
                <button onclick="prevLaporanWargaPage()" class="button-secondary button-sm compact-btn"><i data-lucide="chevron-left" class="icon-18"></i></button>
                <button onclick="nextLaporanWargaPage()" class="button-secondary button-sm compact-btn"><i data-lucide="chevron-right" class="icon-18"></i></button>
            </div>
        </div>

        <div id="laporan-warga-empty" class="hidden empty-state">
            <i data-lucide="file-x" class="icon-48 icon-muted-soft mb-16"></i>
            <p class="text-secondary">Tidak ada data ditemukan untuk kriteria ini.</p>
        </div>
    </div>

</div>

<style>
#page-laporan-iuran-warga .rekon-table th, 
#page-laporan-iuran-warga .rekon-table td {
    padding: 16px 10px !important;
    font-size: 0.8125rem;
}

#page-laporan-iuran-warga .rekon-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 auto;
    display: block;
    position: relative;
    z-index: 15;
    box-shadow: 0 0 0 2px var(--secondary-bg);
}

.rekon-dot-lunas { background-color: #10b981; }
.rekon-dot-menunggak { background-color: #ef4444; }
.rekon-dot-sebelum { background-color: var(--status-info); box-shadow: 0 0 10px color-mix(in srgb, var(--status-info) 40%, transparent); }
.rekon-dot-empty { background-color: var(--border-color); opacity: 0.3; }

.relation-line {
    fill: none;
    stroke: var(--accent-color);
    stroke-width: 2.2;
    stroke-linecap: round;
    opacity: 0.4;
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: drawFlow 1.5s ease-out forwards;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes drawFlow {
    to { stroke-dashoffset: 0; }
}

/* Garis Biru untuk Pembayaran Dimuka (Advance) */
.relation-line-advance {
    fill: none;
    stroke: var(--status-info);
    stroke-width: 2.2;
    stroke-linecap: round;
    opacity: 0.4;
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: drawFlow 1.5s ease-out forwards;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

tr:hover .relation-line-advance {
    opacity: 0.9;
    stroke-width: 3.5;
}

tr:hover .relation-line {
    opacity: 0.9;
    stroke-width: 3.5;
}

#page-laporan-iuran-warga .text-center { text-align: center; }
</style>
