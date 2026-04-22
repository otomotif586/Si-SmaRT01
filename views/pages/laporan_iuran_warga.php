<!-- Page: Laporan Iuran Warga (Detailed History with Relationship Lines) -->
<div id="page-laporan-iuran-warga" class="page-content hidden page-section">
    
    <!-- Summary Cards -->
    <!-- Deluxe Summary Section -->
    <div class="summary-3-grid">
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
            <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                <i data-lucide="users"></i>
            </div>
            <p class="card-label">Total Warga</p>
            <h3 id="laporan-warga-total" class="card-value text-color" style="font-size: 1.5rem;">0</h3>
            <div class="card-sub-info">Pemilih data aktif</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
            <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                <i data-lucide="check-circle"></i>
            </div>
            <p class="card-label">Lunas (12 Bln)</p>
            <h3 id="laporan-warga-lunas" class="card-value text-emerald" style="font-size: 1.5rem;">0</h3>
            <div class="card-sub-info">Pembayaran sempurna</div>
        </div>

        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
            <div class="card-icon-deluxe" style="color: #ef4444; background: rgba(239, 68, 68, 0.1);">
                <i data-lucide="alert-circle"></i>
            </div>
            <p class="card-label">Ada Tunggakan</p>
            <h3 id="laporan-warga-menunggak" class="card-value text-red" style="font-size: 1.5rem;">0</h3>
            <div class="card-sub-info">Perlu ditagih</div>
        </div>
    </div>

    <!-- Filter Glass Card -->
    <div class="glass-card" style="padding: 16px 20px; margin-bottom: 12px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <p class="text-secondary" style="font-size: 0.8125rem; margin: 0;">Visualisasi pelunasan iuran warga per tahun buku.</p>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary-color);">Tahun:</label>
                    <input type="number" id="filter-tahun-laporan-warga" class="input-field" style="font-size: 0.8125rem; padding: 8px; width: 85px; border-radius: 10px;" value="<?= date('Y') ?>" onchange="loadLaporanIuranWarga()">
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary-color);">Blok:</label>
                    <select id="filter-blok-laporan-warga" class="input-field select-custom" style="font-size: 0.8125rem; padding: 8px; min-width: 120px; border-radius: 10px;" onchange="loadLaporanIuranWarga()">
                        <option value="all">Semua Blok</option>
                    </select>
                </div>
            </div>
            <button class="button-secondary button-sm" style="padding: 8px 14px; border-radius: 10px; font-size: 0.8125rem;" onclick="exportLaporanWargaCSV()"><i data-lucide="download" style="margin-right: 6px; width: 16px; height: 16px;"></i> Export</button>
        </div>
    </div>

    <!-- Legend -->
    <div class="glass-card" style="padding: 12px 20px; margin-bottom: 12px; border-radius: 12px; display: flex; gap: 20px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
        <div style="display:flex; align-items:center; gap:8px; font-size:0.7rem;"><span class="rekon-dot rekon-dot-lunas" style="width:8px; height:8px; margin:0;"></span> Lunas</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.7rem;"><span style="width:16px; height:2px; background:var(--accent-color); border-radius: 1px;"></span> Relasi Tunggakan</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.7rem;"><span style="width:16px; height:2px; background:#3b82f6; border-radius: 1px;"></span> Bayar Lebih Awal</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.7rem;"><span class="rekon-dot rekon-dot-menunggak" style="width:8px; height:8px; margin:0;"></span> Belum Bayar</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.7rem;"><span class="rekon-dot rekon-dot-sebelum" style="width:8px; height:8px; margin:0;"></span> Di Luar Periode</div>
    </div>

    <!-- Main Table Container with SVG Overlay -->
    <div class="glass-card" style="padding: 0; border-radius: 20px; position: relative; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h4 style="font-size: 1.1rem; margin: 0; font-weight: 700;">Mapping Pelunasan Iuran</h4>
            <div class="input-with-icon" style="max-width: 250px; width: 100%;">
                <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                <input type="text" id="search-laporan-warga" class="input-field" placeholder="Cari Warga..." oninput="filterLaporanWarga()" style="padding: 10px 16px 10px 40px; font-size: 0.8125rem; border-radius: 10px;">
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto; position: relative; -webkit-overflow-scrolling: touch;">
            <!-- Continer for SVG + Table to handle scroll together -->
            <div id="laporan-warga-scroll-wrapper" style="position: relative; min-width: 1100px; padding-bottom: 40px;">
                <!-- SVG Layer -->
                <svg id="svg-relations" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--accent-color)" opacity="0.6" />
                        </marker>
                        <marker id="arrowhead-advance" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#3b82f6" opacity="0.6" />
                        </marker>
                    </defs>
                </svg>

                <table id="laporan-warga-table" class="modern-table rekon-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 180px; min-width: 180px; position: sticky; left: 0; z-index: 20; background: var(--secondary-bg);">Nama Warga</th>
                            <th style="width: 100px; min-width: 100px;">NO/Blok</th>
                            <th class="text-center" style="width: 100px; min-width: 100px;">Status</th>
                            <th class="text-center" style="width: 60px;">Jan</th>
                            <th class="text-center" style="width: 60px;">Feb</th>
                            <th class="text-center" style="width: 60px;">Mar</th>
                            <th class="text-center" style="width: 60px;">Apr</th>
                            <th class="text-center" style="width: 60px;">Mei</th>
                            <th class="text-center" style="width: 60px;">Jun</th>
                            <th class="text-center" style="width: 60px;">Jul</th>
                            <th class="text-center" style="width: 60px;">Agu</th>
                            <th class="text-center" style="width: 60px;">Sep</th>
                            <th class="text-center" style="width: 60px;">Okt</th>
                            <th class="text-center" style="width: 60px;">Nov</th>
                            <th class="text-center" style="width: 60px;">Des</th>
                        </tr>
                    </thead>
                    <tbody id="laporan-warga-table-body">
                        <!-- Diisi dinamis -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Controls -->
        <div id="laporan-warga-pagination" class="glass-card" style="margin-top: 16px; padding: 12px 24px; border-radius: 12px; display: none; align-items: center; justify-content: space-between; gap: 16px;">
            <div id="laporan-warga-page-info" class="text-secondary" style="font-size: 0.8125rem;">Menampilkan 1-20 dari 100 data</div>
            <div class="pagination-buttons" style="display: flex; gap: 8px;">
                <button onclick="prevLaporanWargaPage()" class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;"><i data-lucide="chevron-left" style="width: 18px; height: 18px;"></i></button>
                <button onclick="nextLaporanWargaPage()" class="button-secondary button-sm" style="padding: 8px 12px; border-radius: 8px;"><i data-lucide="chevron-right" style="width: 18px; height: 18px;"></i></button>
            </div>
        </div>

        <div id="laporan-warga-empty" class="hidden" style="text-align: center; padding: 60px 20px;">
            <i data-lucide="file-x" style="width: 48px; height: 48px; color: var(--text-secondary-color); opacity: 0.3; margin-bottom: 16px;"></i>
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
.rekon-dot-sebelum { background-color: #3b82f6; box-shadow: 0 0 10px rgba(59, 130, 246, 0.4); }
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
    stroke: #3b82f6;
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

.text-center { text-align: center; }
</style>
