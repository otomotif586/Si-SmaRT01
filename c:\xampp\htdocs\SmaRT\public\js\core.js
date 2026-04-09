// Initialize Lucide Icons
lucide.createIcons();

/**
 * Applies the specified theme to the document and updates the theme toggle icon.
 * @param {string} theme - The theme to apply ('light-theme' or 'dark-theme').
 */
function applyTheme(theme) {
    document.documentElement.classList.remove('light-theme', 'dark-theme');
    document.documentElement.classList.add(theme);
    localStorage.setItem('theme', theme);

    const themeToggleButton = document.getElementById('theme-toggle');
    if (themeToggleButton) {
        const iconElement = themeToggleButton.querySelector('i');
        if (iconElement) {
            if (theme === 'dark-theme') {
                iconElement.setAttribute('data-lucide', 'moon');
            } else {
                iconElement.setAttribute('data-lucide', 'sun');
            }
            lucide.createIcons(); // Re-render the icon
        }
    }
}

/**
 * Toggles between 'light-theme' and 'dark-theme'.
 */
function toggleTheme() {
    // Default to light if no theme is saved, or if the saved theme is invalid
    const currentTheme = localStorage.getItem('theme') || 'light-theme'; 
    const newTheme = currentTheme === 'dark-theme' ? 'light-theme' : 'dark-theme';
    applyTheme(newTheme);
}

// Apply theme on initial load
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // Check user's system preference if no theme is saved
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark-theme');
        } else {
            applyTheme('light-theme');
        }
    }

    // Attach event listener for theme toggle button
    const themeToggleButton = document.getElementById('theme-toggle');
    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', toggleTheme);
    }
}); 

/**
 * Toggles the collapsed state of the sidebar (for desktop).
 */
function toggleDesktopSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
        if (mainContent) {
            mainContent.classList.toggle('sidebar-collapsed');
        }
    }
}

// --- Sidebar Toggling ---
/**
 * Toggles the active state of the sidebar (for mobile).
 */
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active'); // 'active' class for mobile visibility
    }
}

// --- Sidebar Submenu Logic ---
function toggleSubmenu(submenuId) {
    // Tutup submenu lain yang sedang terbuka (mencegah menu popup bertumpuk di HP)
    document.querySelectorAll('.submenu-items.active').forEach(menu => {
        if (menu.id !== submenuId) {
            menu.classList.add('hidden');
            menu.classList.remove('active');
            const otherBtn = document.getElementById(menu.id.replace('submenu-', 'nav-group-'));
            if (otherBtn) otherBtn.classList.remove('open');
        }
    });

    const submenu = document.getElementById(submenuId);
    const toggleBtn = document.getElementById(submenuId.replace('submenu-', 'nav-group-'));
    if (submenu.classList.contains('hidden')) {
        submenu.classList.remove('hidden');
        submenu.classList.add('active');
        if (toggleBtn) toggleBtn.classList.add('open');
    } else {
        submenu.classList.add('hidden');
        submenu.classList.remove('active');
        if (toggleBtn) toggleBtn.classList.remove('open');
    }
}

// Attach event listeners
document.addEventListener('DOMContentLoaded', () => {
    // Desktop sidebar toggle button (inside sidebar)
    const desktopSidebarToggleButton = document.getElementById('desktop-sidebar-toggle');
    if (desktopSidebarToggleButton) {
        desktopSidebarToggleButton.addEventListener('click', toggleDesktopSidebar);
    }

    // Mobile sidebar toggle button (inside header)
    const mobileSidebarToggleButton = document.getElementById('mobile-sidebar-toggle');
    if (mobileSidebarToggleButton) {
        mobileSidebarToggleButton.addEventListener('click', toggleMobileSidebar);
    }

    // Modal sidebar toggle
    const modalSidebarToggle = document.getElementById('modal-sidebar-toggle');
    if (modalSidebarToggle) {
        modalSidebarToggle.addEventListener('click', toggleModalSidebar);
    }

    // Mengembalikan pengguna ke halaman terakhir setelah reload
    const activePage = localStorage.getItem('activePage') || 'dashboard';
    setTimeout(() => showPage(activePage), 50); // Sedikit delay agar DOM siap
    loadAllBloks();
});

function showPage(pageId) {
    // Simpan halaman yang sedang dibuka ke Local Storage
    localStorage.setItem('activePage', pageId);

    // Hide all pages
    document.querySelectorAll('.page-content').forEach(p => p.classList.add('hidden'));
    // Remove active state from nav
    document.querySelectorAll('nav button').forEach(b => b.classList.remove('active-tab'));
    
    // Show requested page
    const targetPage = document.getElementById('page-' + pageId);
    targetPage.classList.remove('hidden');
    
    // Trigger fade-in animation
    targetPage.classList.remove('page-enter');
    void targetPage.offsetWidth; // Trigger DOM reflow to restart animation
    targetPage.classList.add('page-enter');

    // Set active state to nav
    const activeNav = document.getElementById('nav-' + pageId);
    if (activeNav) {
        activeNav.classList.add('active-tab');
        
        // Buka submenu secara otomatis jika item yang diklik ada di dalam submenu
        const parentSubmenu = activeNav.closest('.submenu-items');
        if (parentSubmenu) {
            const toggleBtn = document.getElementById(parentSubmenu.id.replace('submenu-', 'nav-group-'));
            
            if (window.innerWidth >= 768) {
                // Di Desktop: Biarkan submenu tetap terbuka (Accordion style)
                parentSubmenu.classList.remove('hidden');
                parentSubmenu.classList.add('active');
                if (toggleBtn) {
                    toggleBtn.classList.add('active-tab');
                    toggleBtn.classList.add('open');
                }
            } else {
                // Di Mobile: Sembunyikan otomatis popup menu setelah item dipilih
                parentSubmenu.classList.add('hidden');
                parentSubmenu.classList.remove('active');
                if (toggleBtn) {
                    toggleBtn.classList.add('active-tab');
                    toggleBtn.classList.remove('open');
                }
            }
        }
    }

    // Update Header
    const titles = {
        'dashboard': ['Beranda', 'Ringkasan info lingkungan.'],
        'global-warga': ['Data Warga', 'Direktori penghuni.'],
        'laporan-iuran-blok': ['Iuran Blok', 'Rekap per blok.'],
        'warga': ['Workspace', 'Kelola blok & warga.'],
        'keuangan': ['Buku Kas', 'Arus kas utama.'],
        'detail-keuangan': ['Detail Kas', 'Rincian per kategori.'],
        'pos-keuangan': ['Pos Anggaran', 'Alokasi & pengeluaran.'],
        'pembukuan': ['Pembukuan', 'Neraca & arus kas.'],
        'keamanan': ['Keamanan', 'Laporan & darurat.'],
        'info': ['Informasi', 'Dokumen & kontak.'],
        'rekonsiliasi': ['Audit Iuran', 'Cek kedisiplinan.'],
        'laporan-iuran-warga': ['Tunggakan', 'Status bayar warga.']
    };

    document.getElementById('page-title').innerText = titles[pageId][0];
    document.getElementById('page-subtitle').innerText = titles[pageId][1];

    // Re-render icons for dynamic content if any
    lucide.createIcons();

    if (pageId === 'dashboard') {
        if (typeof initDashboard === 'function') initDashboard();
    } else if (pageId === 'global-warga') {
        loadGlobalWarga();
    } else if (pageId === 'laporan-iuran-blok') {
        initLaporanIuranBlok();
    } else if (pageId === 'rekonsiliasi') {
        initRekonsiliasi();
    } else if (pageId === 'laporan-iuran-warga') {
        initLaporanIuranWarga();
    } else if (pageId === 'keuangan') {
        initKeuanganGlobal();
    } else if (pageId === 'detail-keuangan') {
        if (typeof initDetailKeuangan === 'function') initDetailKeuangan();
    } else if (pageId === 'pos-keuangan') {
        if (typeof initPosKeuangan === 'function') initPosKeuangan();
    } else if (pageId === 'pembukuan') {
        if (typeof initPembukuan === 'function') initPembukuan();
    }
    
    // Scroll to top
    window.scrollTo(0,0);
}

// Fungsi pembantu untuk mendapatkan format YYYY-MM-DD sesuai zona waktu komputer Anda
function getLocalDateString() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Handle Mobile Interaction (vibration etc)
document.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('touchstart', function() {
        if ('vibrate' in navigator) {
            navigator.vibrate(5);
        }
    });
});

window.showToast = function(title, icon = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    } else {
        alert(title);
    }
};

// --- DASHBOARD MAIN RENDERER ---
function initDashboard() {
    const dashPage = document.getElementById('page-dashboard');
    if (!dashPage) return;
    
    if (dashPage.querySelector('.dashboard-rendered')) return; // Mencegah render ganda

    dashPage.innerHTML = `
        <div class="dashboard-rendered">
            <div class="grid-container" style="margin-bottom: 24px;">
                <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--accent-color);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Saldo Kas</p>
                            <h2 class="text-color" style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.03em;">Rp 24.5M</h2>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;"><i data-lucide="wallet" style="width: 24px; height: 24px;"></i></div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;"><span class="badge bg-emerald-light text-emerald" style="padding: 2px 6px;">+4.2%</span> bln lalu</p>
                </div>
                
                <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #3b82f6;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Warga</p>
                            <h2 class="text-color" style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.03em;">1,240 <span style="font-size: 1rem; color: var(--text-secondary-color); font-weight: 600;">Jiwa</span></h2>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center;"><i data-lucide="users" style="width: 24px; height: 24px;"></i></div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;"><span class="badge bg-blue-light text-blue" style="padding: 2px 6px;">32 Blok Aktif</span></p>
                </div>

                <div class="glass-card" style="padding: 24px; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ef4444;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p class="text-secondary" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Aduan Aktif</p>
                            <h2 class="text-color" style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.03em;">5 <span style="font-size: 1rem; color: var(--text-secondary-color); font-weight: 600;">Aduan</span></h2>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center;"><i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i></div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.8rem; margin: 16px 0 0 0;"><span class="badge bg-red-light text-red" style="padding: 2px 6px;">Perlu Tindakan</span></p>
                </div>
            </div>

            <div class="grid-container-2-col" style="margin-bottom: 24px;">
                <div class="glass-card" style="padding: 28px; border-radius: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.02em;">Grafik Kas</h4>
                        <button class="button-secondary button-sm" style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Tahun Ini <i data-lucide="chevron-down" style="width: 14px; height: 14px; margin-left: 4px; display: inline-block;"></i></button>
                    </div>
                    <div style="height: 280px; width: 100%;">
                        <canvas id="mainDashLineChart"></canvas>
                    </div>
                </div>
                <div class="glass-card" style="padding: 28px; border-radius: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.02em;">Demografi Warga</h4>
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; color: var(--text-secondary-color);"><i data-lucide="pie-chart" style="width: 16px; height: 16px;"></i></div>
                    </div>
                    <div style="height: 280px; width: 100%;">
                        <canvas id="mainDashDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    `;

    lucide.createIcons();

    setTimeout(() => {
        const rootStyles = getComputedStyle(document.documentElement);
        const textColor = rootStyles.getPropertyValue('--text-color').trim() || '#64748b';
        const gridColor = rootStyles.getPropertyValue('--border-color').trim() || '#e2e8f0';

        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderMainCharts;
            document.head.appendChild(script);
        } else {
            renderMainCharts();
        }

        function renderMainCharts() {
            const ctxL = document.getElementById('mainDashLineChart');
            if (ctxL) {
                new Chart(ctxL.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Pemasukan Kas (Juta)',
                            data: [18, 22, 19, 25, 21, 28, 30],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3, tension: 0.4, fill: true,
                            pointBackgroundColor: '#fff', pointBorderColor: '#10b981',
                            pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { 
                            x: { ticks: { color: textColor, font: { family: 'Inter' } }, grid: { display: false } }, 
                            y: { ticks: { color: textColor, font: { family: 'Inter' } }, grid: { color: gridColor, drawBorder: false, borderDash: [5, 5] } } 
                        },
                        interaction: { intersect: false, mode: 'index' },
                    }
                });
            }

            const ctxD = document.getElementById('mainDashDoughnutChart');
            if (ctxD) {
                new Chart(ctxD.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Keluarga Tetap', 'Kontrak/Sewa', 'Lainnya'],
                        datasets: [{
                            data: [70, 20, 10],
                            backgroundColor: ['#3b82f6', '#ec4899', '#f59e0b'],
                            borderWidth: 0, hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 20, font: { family: 'Inter', size: 12, weight: '500' }, usePointStyle: true, pointStyle: 'circle' } } },
                        cutout: '75%', layout: { padding: 10 }
                    }
                });
            }
        }
    }, 200);
}
