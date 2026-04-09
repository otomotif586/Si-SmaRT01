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
    const overlay = document.querySelector('.sidebar-overlay');
    if (sidebar) {
        sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    }
}

/**
 * Force close mobile sidebar.
 */
function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (sidebar) {
        sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
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

    // Sidebar overlay click-to-close
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeMobileSidebar);
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

    // Di Mobile: Tutup sidebar otomatis saat berpindah halaman
    if (window.innerWidth < 1024) {
        closeMobileSidebar();
    }

    // Hide all pages
    document.querySelectorAll('.page-content').forEach(p => p.classList.add('hidden'));
    // Remove active state from nav
    document.querySelectorAll('nav button').forEach(b => b.classList.remove('active-tab'));
    
    // Show requested page
    const targetPage = document.getElementById('page-' + pageId);
    targetPage.classList.remove('hidden');
    
    // Trigger fade-in animation
    targetPage.classList.remove('page-enter', 'stagger-ready');
    void targetPage.offsetWidth; // Trigger DOM reflow to restart animation
    targetPage.classList.add('page-enter', 'stagger-ready');

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
        'dashboard': ['Beranda', 'Ringkasan data warga'],
        'global-warga': ['Data Warga', 'Direktori seluruh warga'],
        'laporan-iuran-blok': ['Iuran Blok', 'Laporan iuran per blok'],
        'warga': ['Workspace', 'Kelola data & workspace'],
        'keuangan': ['Buku Kas', 'Transparansi kas RT'],
        'detail-keuangan': ['Detail', 'Rincian pendapatan'],
        'pos-keuangan': ['Pos Anggaran', 'Kelola pengeluaran'],
        'pembukuan': ['Pembukuan', 'Neraca & trial balance'],
        'keamanan': ['Keamanan', 'Laporan & bantuan'],
        'info': ['Informasi', 'Dokumen & kontak'],
        'rekonsiliasi': ['Rekonsiliasi', 'Audit iuran tahunan'],
        'laporan-iuran-warga': ['Tunggakan', 'Visualisasi iuran']
    };

    document.getElementById('page-title').innerText = titles[pageId][0];
    document.getElementById('page-subtitle').innerText = titles[pageId][1];

    // Re-render icons for dynamic content if any
    lucide.createIcons();

    if (pageId === 'global-warga') {
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

// Handle Interactive Elements
document.addEventListener('click', function(e) {
    const target = e.target.closest('button, .ripple');
    if (!target) return;

    // 1. Ripple Effect
    const ripple = document.createElement('span');
    ripple.classList.add('ripple-effect');
    target.appendChild(ripple);

    const rect = target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${e.clientX - rect.left - size/2}px`;
    ripple.style.top = `${e.clientY - rect.top - size/2}px`;

    setTimeout(() => ripple.remove(), 600);

    // 2. Mobile Vibrate
    if ('vibrate' in navigator) {
        navigator.vibrate(5);
    }
});

window.showToast = function(title, icon = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: 'var(--secondary-bg)',
            color: 'var(--text-color)',
            iconColor: icon === 'success' ? '#10b981' : undefined,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    } else {
        alert(title);
    }
};

window.showLoading = function(title = 'Memuat...') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: title,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: 'var(--secondary-bg)',
            color: 'var(--text-color)',
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
};
