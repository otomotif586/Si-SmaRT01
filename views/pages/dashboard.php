<!-- Page: Dashboard (Professional Startup Style) -->
<div id="page-dashboard" class="page-content page-section stagger-ready">
    
    <!-- Premium Informative Summary Section -->
    <div class="summary-3-grid">
        <!-- Card 1: Saldo Kas Utama -->
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
            <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                <i data-lucide="wallet"></i>
            </div>
            <p class="card-label">Saldo Kas Utama</p>
            <h3 class="card-value text-emerald animated-counter" id="dash-saldo" style="font-size: 1.8rem;">Rp 0</h3>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <div class="card-trend-badge up">
                    <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i>
                    <span id="dash-saldo-trend">0%</span>
                </div>
                <span class="card-sub-info">Pertumbuhan bulan ini</span>
            </div>
        </div>

        <!-- Card 2: Partisipasi Iuran -->
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
            <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                <i data-lucide="shield-check"></i>
            </div>
            <p class="card-label">Partisipasi Iuran</p>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <h3 class="card-value" id="dash-iuran-percent" style="font-size: 1.8rem;">0%</h3>
                <span class="text-secondary" style="font-size: 0.8rem;" id="dash-iuran-detail">0/0 Warga</span>
            </div>
            <div class="progress-bar" style="height: 8px; margin-top: 16px; background: rgba(255,255,255,0.05);">
                <div id="dash-iuran-progress" class="progress-fill" style="width: 0%; background: linear-gradient(90deg, #3b82f6, #10b981);"></div>
            </div>
            <div class="card-sub-info">
                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                <span>Update otomatis per 24 jam</span>
            </div>
        </div>

        <!-- Card 3: Laporan Aktif -->
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
            <div class="card-icon-deluxe" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">
                <i data-lucide="bell"></i>
            </div>
            <p class="card-label">Laporan & Keluhan</p>
            <h3 class="card-value" id="dash-laporan-count" style="font-size: 1.8rem; color: #f59e0b;">0</h3>
            <div class="card-trend-badge down" id="dash-laporan-status" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                <span>Butuh Tindak Lanjut</span>
            </div>
            <p class="card-sub-info" style="margin-top: 12px;">
                Cek detail di menu Keamanan
            </p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid-container-2-col">
        <!-- Line Chart: Tren Pemasukan -->
        <div class="glass-card card-section stagger-item" style="animation-delay: 0.4s">
            <div class="section-header">
                <h4 class="section-title">Tren Pemasukan (6 Bln)</h4>
                <div class="chart-legend">
                    <span class="legend-item"><span class="dot bg-emerald"></span> Iuran</span>
                </div>
            </div>
            <div class="chart-container" style="height: 250px;">
                <canvas id="iuranTrendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Demografi -->
        <div class="glass-card card-section stagger-item" style="animation-delay: 0.5s">
            <div class="section-header">
                <h4 class="section-title">Status Kependudukan</h4>
            </div>
            <div class="chart-container" style="height: 250px;">
                <canvas id="demografiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Info & Activity Section -->
    <div class="grid-container-2-col">
         <!-- Pengumuman -->
        <div class="glass-card card-section stagger-item" style="animation-delay: 0.6s">
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
        <div class="glass-card card-section stagger-item" style="animation-delay: 0.7s">
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