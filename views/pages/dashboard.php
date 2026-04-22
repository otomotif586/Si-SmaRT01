<!-- Page: Dashboard (Professional Startup Style) -->
<div id="page-dashboard" class="page-content page-section stagger-ready">
    
    <!-- Premium Informative Summary Section -->
    <div class="summary-3-grid">
        <!-- Card 1: Saldo Kas Utama -->
        <div class="glass-card-deluxe stagger-item stagger-delay-1">
            <div class="card-icon-deluxe icon-tone-success">
                <i data-lucide="wallet"></i>
            </div>
            <p class="card-label">Saldo Kas Utama</p>
            <h3 class="card-value text-emerald animated-counter card-value-xl" id="dash-saldo">Rp 0</h3>
            <div class="inline-wrap-12">
                <div class="card-trend-badge up">
                    <i data-lucide="trending-up" class="icon-12"></i>
                    <span id="dash-saldo-trend">0%</span>
                </div>
                <span class="card-sub-info">Pertumbuhan bulan ini</span>
            </div>
        </div>

        <!-- Card 2: Partisipasi Iuran -->
        <div class="glass-card-deluxe stagger-item stagger-delay-2">
            <div class="card-icon-deluxe icon-tone-info">
                <i data-lucide="shield-check"></i>
            </div>
            <p class="card-label">Partisipasi Iuran</p>
            <div class="inline-baseline-8">
                <h3 class="card-value card-value-xl" id="dash-iuran-percent">0%</h3>
                <span class="text-secondary text-[0.8rem]" id="dash-iuran-detail">0/0 Warga</span>
            </div>
            <div class="progress-bar h-2 mt-4 bg-white/5">
                <div id="dash-iuran-progress" class="progress-fill progress-fill-info-success w-0"></div>
            </div>
            <div class="card-sub-info">
                <i data-lucide="clock" class="icon-12"></i>
                <span>Update otomatis per 24 jam</span>
            </div>
        </div>

        <!-- Card 3: Laporan Aktif -->
        <div class="glass-card-deluxe stagger-item stagger-delay-3">
            <div class="card-icon-deluxe icon-tone-warning">
                <i data-lucide="bell"></i>
            </div>
            <p class="card-label">Laporan & Keluhan</p>
            <h3 class="card-value text-warning card-value-xl" id="dash-laporan-count">0</h3>
            <div class="card-trend-badge warning" id="dash-laporan-status">
                <i data-lucide="alert-circle" class="icon-12"></i>
                <span>Butuh Tindak Lanjut</span>
            </div>
            <p class="card-sub-info mt-12">
                Cek detail di menu Keamanan
            </p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid-container-2-col">
        <!-- Line Chart: Tren Pemasukan -->
        <div class="glass-card card-section stagger-item stagger-delay-4">
            <div class="section-header">
                <h4 class="section-title">Tren Pemasukan (6 Bln)</h4>
                <div class="chart-legend">
                    <span class="legend-item"><span class="dot bg-emerald"></span> Iuran</span>
                </div>
            </div>
            <div class="chart-container chart-h-250">
                <canvas id="iuranTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Demografi -->
        <div class="glass-card card-section stagger-item stagger-delay-5">
            <div class="section-header">
                <h4 class="section-title">Status Kependudukan</h4>
            </div>
            <div class="chart-container chart-h-250">
                <canvas id="demografiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Info & Activity Section -->
    <div class="grid-container-2-col">
         <!-- Pengumuman -->
        <div class="glass-card card-section stagger-item stagger-delay-6">
            <div class="section-header">
                <h4 class="section-title">Pengumuman Terbaru</h4>
                <button class="button-link" onclick="showPage('info')">Lihat Semua</button>
            </div>
            <div class="announcement-list" id="dash-announcements">
                <div class="announcement-item">
                    <div class="announcement-icon bg-emerald-light">
                        <i data-lucide="megaphone" class="text-emerald"></i>
                    </div>
                    <div class="announcement-content">
                        <h5 class="announcement-title">Selamat Datang di Si-SmaRT 01</h5>
                        <p class="announcement-text">Sistem informasi RT modern untuk lingkungan yang lebih transparan dan nyaman.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agenda -->
        <div class="glass-card card-section stagger-item stagger-delay-7">
            <div class="section-header">
                <h4 class="section-title">Agenda Terdekat</h4>
                <button class="button-link">Kalender</button>
            </div>
            <div id="dash-agenda-empty" class="text-center py-8 opacity-50">
                <i data-lucide="calendar" class="mx-auto mb-2 opacity-20" size="48"></i>
                <p id="dash-agenda-text">Memuat agenda...</p>
            </div>
        </div>
    </div>
</div>
