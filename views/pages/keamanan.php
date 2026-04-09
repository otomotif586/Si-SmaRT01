<!-- Page: Keamanan -->
<div id="page-keamanan" class="page-content hidden page-section">
    
    <!-- Premium Security Summary -->
    <div class="summary-3-grid" style="margin-bottom: 24px;">
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.1s">
            <div class="card-icon-deluxe" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);">
                <i data-lucide="user-plus"></i>
            </div>
            <p class="card-label">Tamu Hari Ini</p>
            <h3 id="keamanan-tamu-count" class="card-value text-color" style="font-size: 1.5rem;">0</h3>
            <div class="card-sub-info">Kunjungan terdaftar</div>
        </div>
        
        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.2s">
            <div class="card-icon-deluxe" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">
                <i data-lucide="alert-triangle"></i>
            </div>
            <p class="card-label">Laporan Aktif</p>
            <h3 id="keamanan-laporan-count" class="card-value text-orange" style="font-size: 1.5rem;">0</h3>
            <div class="card-sub-info">Butuh tindak lanjut</div>
        </div>

        <div class="glass-card-deluxe stagger-item" style="animation-delay: 0.3s">
            <div class="card-icon-deluxe" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                <i data-lucide="shield-check"></i>
            </div>
            <p class="card-label">Status Lingkungan</p>
            <h3 class="card-value text-emerald" style="font-size: 1.5rem;">Aman</h3>
            <div class="card-sub-info">Sistem pengawasan aktif</div>
        </div>
    </div>

    <div class="panic-button-container text-center" style="padding: 20px 0;">
        <div class="panic-button-wrapper" style="margin-bottom: 12px;"> <!-- Wrapper for positioning badge -->
            <button class="panic-button" style="width: 140px; height: 140px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;"> <!-- Custom panic button styling -->
                <i data-lucide="bell-ring" style="width: 48px; height: 48px;"></i>
                <span style="font-size: 0.75rem; font-weight: 800;">PANIC BUTTON</span>
            </button>
            <div class="panic-badge" style="top: 10px; right: 10px;">DARURAT</div> <!-- Custom panic badge -->
        </div>
        <p class="panic-description" style="max-width: 300px; margin: 0 auto; font-size: 0.8125rem; opacity: 0.8;">Tekan jika ada kejadian mendesak. Sinyal akan dikirim ke seluruh warga & tim keamanan.</p>
    </div>

    <div class="glass-card card-section text-left" style="margin-top: 20px; border-radius: 20px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 1rem; margin: 0; font-weight: 700;">Laporan Tamu Terakhir</h4>
            <button class="button-link text-xs">Semua</button>
        </div>
        <div class="report-list" style="padding: 4px 0;"> <!-- Custom report list -->
            <div class="report-item border-left-emerald" style="padding: 12px 20px;">
                <p class="report-title" style="font-weight: 700; margin: 0; font-size: 0.9rem;">Kurir J&T - Paket A-12</p>
                <p class="report-meta" style="font-size: 0.75rem; color: var(--text-secondary-color); margin-top: 4px;">Pukul 14:20 • Pos Satpam Utama</p>
            </div>
            <div class="report-item border-left-secondary" style="padding: 12px 20px;">
                <p class="report-title" style="font-weight: 700; margin: 0; font-size: 0.9rem;">Tamu Bp. Budi (Keluarga)</p>
                <p class="report-meta" style="font-size: 0.75rem; color: var(--text-secondary-color); margin-top: 4px;">Pukul 10:15 • Blok B-05</p>
            </div>
        </div>
        <div style="padding: 16px 20px;">
            <button class="button-primary w-full" style="border-radius: 12px; padding: 12px;"><i data-lucide="plus" class="mr-2" size="16"></i> Lapor Tamu / Kejadian</button>
        </div>
    </div>
</div>