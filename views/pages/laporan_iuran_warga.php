<!-- Page: Laporan Iuran Warga (Detailed History with Relationship Lines) -->
<div id="page-laporan-iuran-warga" class="page-content hidden page-section">
    
    <div class="section-header">
        <div>
            <h3 class="section-title">Laporan & Relasi Iuran Warga</h3>
            <p class="section-subtitle">Visualisasi hubungan antara bulan tagihan dan bulan pembayaran riil.</p>
        </div>
        <div class="header-actions" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <label for="filter-tahun-laporan-warga" class="text-secondary" style="font-size: 0.8125rem; font-weight: 600;">Tahun Buku:</label>
                <input type="number" id="filter-tahun-laporan-warga" class="input-field" style="font-size: 0.875rem; padding: 10px; width: 100px; border-radius: 12px;" value="<?= date('Y') ?>" onchange="loadLaporanIuranWarga()">
                
                <label for="filter-blok-laporan-warga" class="text-secondary" style="font-size: 0.8125rem; font-weight: 600; margin-left: 8px;">Filter Blok:</label>
                <select id="filter-blok-laporan-warga" class="input-field select-custom" style="font-size: 0.875rem; padding: 10px; width: auto; min-width: 130px; border-radius: 12px;" onchange="loadLaporanIuranWarga()">
                    <option value="all">Semua Blok</option>
                </select>
            </div>
            <button class="button-secondary button-sm" style="padding: 10px 16px; border-radius: 12px;" onclick="exportLaporanWargaCSV()"><i data-lucide="download" style="margin-right: 6px; width: 18px; height: 18px;"></i> Export Data</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid-container" style="margin-bottom: 24px;">
        <div class="stat-card glass-card" style="display: flex; align-items: center; gap: 16px; padding: 20px;">
            <div class="stat-icon bg-blue-light text-blue" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.1);"><i data-lucide="users"></i></div>
            <div class="stat-info">
                <p class="stat-label" style="font-size: 0.75rem; color: var(--text-secondary-color); margin: 0;">Total Warga</p>
                <h3 class="stat-value" id="laporan-warga-total" style="font-size: 1.25rem; margin: 4px 0 0 0; font-weight: 700;">0</h3>
            </div>
        </div>
        <div class="stat-card glass-card" style="display: flex; align-items: center; gap: 16px; padding: 20px;">
            <div class="stat-icon bg-emerald-light text-emerald" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.1);"><i data-lucide="check-circle"></i></div>
            <div class="stat-info">
                <p class="stat-label" style="font-size: 0.75rem; color: var(--text-secondary-color); margin: 0;">Lunas 12 Bulan</p>
                <h3 class="stat-value" id="laporan-warga-lunas" style="font-size: 1.25rem; margin: 4px 0 0 0; font-weight: 700;">0</h3>
            </div>
        </div>
        <div class="stat-card glass-card" style="display: flex; align-items: center; gap: 16px; padding: 20px;">
            <div class="stat-icon bg-red-light text-red" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(239, 68, 68, 0.1);"><i data-lucide="alert-circle"></i></div>
            <div class="stat-info">
                <p class="stat-label" style="font-size: 0.75rem; color: var(--text-secondary-color); margin: 0;">Ada Tunggakan</p>
                <h3 class="stat-value" id="laporan-warga-menunggak" style="font-size: 1.25rem; margin: 4px 0 0 0; font-weight: 700;">0</h3>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="glass-card" style="padding: 16px 24px; margin-bottom: 24px; border-radius: 12px; display: flex; gap: 24px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span class="rekon-dot rekon-dot-lunas" style="width:10px; height:10px; margin:0;"></span> Lunas (Tepat Waktu)</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span style="width:20px; height:2px; background:var(--accent-color); border-radius: 2px;"></span> Garis Relasi Bayar Tunggakan</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span class="rekon-dot rekon-dot-menunggak" style="width:10px; height:10px; margin:0;"></span> Belum Dibayar</div>
        <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem;"><span class="rekon-dot rekon-dot-sebelum" style="width:10px; height:10px; margin:0;"></span> Belum Masuk Tahun Buku</div>
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
            <div id="laporan-warga-scroll-wrapper" style="position: relative; min-width: 1100px;">
                <!-- SVG Layer -->
                <svg id="svg-relations" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--accent-color)" opacity="0.6" />
                        </marker>
                    </defs>
                </svg>

                <table id="laporan-warga-table" class="modern-table rekon-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 180px; min-width: 180px; position: sticky; left: 0; z-index: 20; background: var(--secondary-bg);">Nama Warga</th>
                            <th style="width: 100px; min-width: 100px;">NO/Blok</th>
                            <th class="text-center" style="width: 100px; min-width: 100px;">Status</th>
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

tr:hover .relation-line {
    opacity: 0.9;
    stroke-width: 3.5;
}

.text-center { text-align: center; }
</style>
