// Initialize Lucide Icons safely (CDN may be delayed/blocked on some browsers)
function safeCreateIcons(root) {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons(root ? { root: root } : undefined);
        return true;
    }
    if (typeof window.__ensureLucide === 'function') {
        window.__ensureLucide();
    }
    return false;
}

safeCreateIcons();

function safeStorageGet(key, fallbackValue = null) {
    try {
        const value = localStorage.getItem(key);
        return value === null ? fallbackValue : value;
    } catch (e) {
        return fallbackValue;
    }
}

function safeStorageSet(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch (e) {
        // Ignore storage errors so navigation/theme still works.
    }
}

function safeRun(label, fn) {
    try {
        if (typeof fn === 'function') fn();
    } catch (e) {
        console.error('[Si-SmaRT] Module init failed:', label, e);
    }
}

function bindSidebarNavFallback() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    sidebar.querySelectorAll('.sidebar-nav button[id^="nav-"]').forEach((btn) => {
        if (btn.dataset.fallbackBound === '1') return;
        btn.dataset.fallbackBound = '1';

        btn.addEventListener('click', (event) => {
            // If inline handler already worked and prevented default flow, keep this no-op.
            if (event.defaultPrevented) return;

            const id = btn.id || '';
            if (id.startsWith('nav-group-')) {
                const submenuId = 'submenu-' + id.replace('nav-group-', '');
                const submenu = document.getElementById(submenuId);
                if (submenu) {
                    toggleSubmenu(submenuId);
                }
                return;
            }

            const pageId = id.replace(/^nav-/, '');
            if (pageId) {
                showPage(pageId);
            }
        });
    });
}

const nativeFetch = window.fetch ? window.fetch.bind(window) : null;
if (nativeFetch) {
    window.fetch = function (input, init = {}) {
        const requestInit = Object.assign({
            credentials: 'same-origin',
            cache: 'no-store'
        }, init || {});
        return nativeFetch(input, requestInit);
    };
}

/**
 * Applies the specified theme to the document and updates the theme toggle icon.
 * @param {string} theme - The theme to apply ('light-theme' or 'dark-theme').
 */
function applyTheme(theme) {
    document.documentElement.classList.remove('light-theme', 'dark-theme');
    document.documentElement.classList.add(theme);
    safeStorageSet('theme', theme);

    const themeToggleButton = document.getElementById('theme-toggle');
    if (themeToggleButton) {
        const iconElement = themeToggleButton.querySelector('i');
        if (iconElement) {
            if (theme === 'dark-theme') {
                iconElement.setAttribute('data-lucide', 'moon');
            } else {
                iconElement.setAttribute('data-lucide', 'sun');
            }
            safeCreateIcons(); // Re-render the icon
        }
    }

    // Update Meta Theme Color for Mobile Status Bar
    const metaTheme = document.getElementById('theme-meta');
    if (metaTheme) {
        metaTheme.setAttribute('content', theme === 'dark-theme' ? '#0f172a' : '#f8fafc');
    }
}

/**
 * Toggles between 'light-theme' and 'dark-theme'.
 */
function toggleTheme() {
    // Default to light if no theme is saved, or if the saved theme is invalid
    const currentTheme = safeStorageGet('theme', 'light-theme') || 'light-theme';
    const newTheme = currentTheme === 'dark-theme' ? 'light-theme' : 'dark-theme';
    applyTheme(newTheme);
}

let coreBooted = false;

function bootCore() {
    if (coreBooted) return;
    coreBooted = true;

    const savedTheme = safeStorageGet('theme');
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

    // Retry icon render when fallback script arrives later than initial parse.
    setTimeout(() => safeCreateIcons(), 250);
    setTimeout(() => safeCreateIcons(), 1200);
}

bootCore();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootCore, { once: true });
}

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

function bootCoreNavigation() {
    if (coreBooted === false) bootCore();

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

    // Fallback binding for browsers/extensions that block inline onclick handlers.
    bindSidebarNavFallback();

    // Mengembalikan pengguna ke halaman terakhir setelah reload
    const preferredDefaultPage = document.getElementById('page-dashboard') ? 'dashboard' : 'ruang-warga';
    const storedPage = safeStorageGet('activePage', preferredDefaultPage) || preferredDefaultPage;
    const activePage = document.getElementById('page-' + storedPage) ? storedPage : preferredDefaultPage;
    safeStorageSet('activePage', activePage);
    setTimeout(() => showPage(activePage), 50); // Sedikit delay agar DOM siap
    safeRun('loadAllBloks', () => {
        if (typeof window.loadAllBloks === 'function') window.loadAllBloks();
    });
}

bootCoreNavigation();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootCoreNavigation, { once: true });
}

function showPage(pageId) {
    // Simpan halaman yang sedang dibuka ke Local Storage
    safeStorageSet('activePage', pageId);

    // Di Mobile: Tutup sidebar otomatis saat berpindah halaman
    if (window.innerWidth < 1024) {
        closeMobileSidebar();
    }

    // Hide all pages
    document.querySelectorAll('.page-content').forEach(p => {
        p.classList.add('hidden');
        p.style.display = 'none';
    });

    // Remove active state from nav
    document.querySelectorAll('#sidebar .sidebar-nav button').forEach(b => b.classList.remove('active-tab'));

    // Show requested page
    let resolvedPageId = pageId;
    let targetPage = document.getElementById('page-' + resolvedPageId);
    if (!targetPage) {
        resolvedPageId = document.getElementById('page-dashboard') ? 'dashboard' : '';
        targetPage = resolvedPageId ? document.getElementById('page-' + resolvedPageId) : null;
    }
    if (!targetPage) {
        targetPage = document.querySelector('.page-content');
        if (!targetPage) return;
        resolvedPageId = targetPage.id.replace(/^page-/, '');
    }

    targetPage.classList.remove('hidden');
    targetPage.style.display = 'block';
    if (resolvedPageId !== pageId) {
        safeStorageSet('activePage', resolvedPageId);
    }

    // Trigger fade-in animation
    targetPage.classList.remove('page-enter', 'stagger-ready');
    void targetPage.offsetWidth;
    targetPage.classList.add('page-enter', 'stagger-ready');

    // Set active state to nav
    const activeNav = document.getElementById('nav-' + resolvedPageId);
    if (activeNav) {
        activeNav.classList.add('active-tab');

        // Handle Submenus (Buka parent submenu jika item di dalamnya terpilih)
        const parentSubmenu = activeNav.closest('.submenu-items');
        if (parentSubmenu) {
            const toggleBtn = document.getElementById(parentSubmenu.id.replace('submenu-', 'nav-group-'));

            if (window.innerWidth >= 1024) {
                // Di PC: Pastikan submenu terbuka
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
        'ruang-warga': ['Ruang Warga', 'Profil, iuran, pengaduan, dan update informasi'],
        'laporan-iuran-blok': ['Iuran Blok', 'Laporan iuran per blok'],
        'warga': ['Workspace', 'Kelola data & workspace'],
        'keuangan': ['Buku Kas', 'Transparansi kas RT'],
        'detail-keuangan': ['Detail', 'Rincian pendapatan'],
        'pos-keuangan': ['Pos Anggaran', 'Kelola pengeluaran'],
        'pembukuan': ['Pembukuan', 'Neraca & trial balance'],
        'keamanan': ['Info', 'Manajemen aduan dan informasi pengurus'],
        'info': ['CMS Website', 'Kelola konten website publik'],
        'pasar': ['Menu Penjual', 'Kelola produk & etalase toko warga'],
        'rekonsiliasi': ['Rekonsiliasi', 'Audit iuran tahunan'],
        'laporan-iuran-warga': ['Tunggakan', 'Visualisasi iuran'],
        'users': ['Master User', 'Manajemen akun & akses']
    };

    if (titles[resolvedPageId]) {
        if (document.getElementById('page-title')) document.getElementById('page-title').innerText = titles[resolvedPageId][0];
        if (document.getElementById('page-subtitle')) document.getElementById('page-subtitle').innerText = titles[resolvedPageId][1];
    }

    // Re-render icons for dynamic content if any
    safeCreateIcons();

    if (resolvedPageId === 'global-warga') {
        safeRun('loadGlobalWarga', () => {
            if (typeof window.loadGlobalWarga === 'function') window.loadGlobalWarga();
        });
    } else if (resolvedPageId === 'ruang-warga') {
        safeRun('initRuangWarga', () => {
            if (typeof window.initRuangWarga === 'function') window.initRuangWarga();
        });
    } else if (resolvedPageId === 'laporan-iuran-blok') {
        safeRun('initLaporanIuranBlok', () => {
            if (typeof window.initLaporanIuranBlok === 'function') window.initLaporanIuranBlok();
        });
    } else if (resolvedPageId === 'rekonsiliasi') {
        safeRun('initRekonsiliasi', () => {
            if (typeof window.initRekonsiliasi === 'function') window.initRekonsiliasi();
        });
    } else if (resolvedPageId === 'laporan-iuran-warga') {
        safeRun('initLaporanIuranWarga', () => {
            if (typeof window.initLaporanIuranWarga === 'function') window.initLaporanIuranWarga();
        });
    } else if (resolvedPageId === 'keuangan') {
        safeRun('initKeuanganGlobal', () => {
            if (typeof window.initKeuanganGlobal === 'function') window.initKeuanganGlobal();
        });
    } else if (resolvedPageId === 'detail-keuangan') {
        safeRun('initDetailKeuangan', () => {
            if (typeof window.initDetailKeuangan === 'function') window.initDetailKeuangan();
        });
    } else if (resolvedPageId === 'pos-keuangan') {
        safeRun('initPosKeuangan', () => {
            if (typeof window.initPosKeuangan === 'function') window.initPosKeuangan();
        });
    } else if (resolvedPageId === 'pembukuan') {
        safeRun('initPembukuan', () => {
            if (typeof window.initPembukuan === 'function') window.initPembukuan();
        });
    } else if (resolvedPageId === 'keamanan') {
        safeRun('initKeamanan', () => {
            if (typeof window.initKeamanan === 'function') window.initKeamanan();
        });
    } else if (resolvedPageId === 'info') {
        safeRun('initInfo', () => {
            if (typeof window.initInfo === 'function') window.initInfo();
        });
    } else if (resolvedPageId === 'users') {
        safeRun('loadCmsUsers', () => {
            if (typeof window.loadCmsUsers === 'function') window.loadCmsUsers();
        });
    } else if (resolvedPageId === 'pasar') {
        safeRun('initPasarPage', () => {
            if (typeof window.initPasarPage === 'function') window.initPasarPage();
        });
        safeRun('initPasar', () => {
            if (typeof window.initPasar === 'function') window.initPasar();
        });
    }

    // Scroll to top
    window.scrollTo(0, 0);
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
document.addEventListener('click', function (e) {
    const target = e.target.closest('button, .ripple');
    if (!target) return;

    // 1. Ripple Effect
    const ripple = document.createElement('span');
    ripple.classList.add('ripple-effect');
    target.appendChild(ripple);

    const rect = target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${e.clientY - rect.top - size / 2}px`;

    setTimeout(() => ripple.remove(), 600);

    // 2. Mobile Vibrate
    if ('vibrate' in navigator) {
        navigator.vibrate(5);
    }
});

window.showToast = function (title, icon = 'success') {
    if (typeof Swal !== 'undefined') {
        const isMobile = window.matchMedia('(max-width: 768px)').matches;
        Swal.fire({
            toast: true,
            position: isMobile ? 'bottom' : 'top',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: 'var(--secondary-bg)',
            color: 'var(--text-color)',
            iconColor: icon === 'success' ? '#10b981' : undefined,
            customClass: {
                popup: 'smart-toast'
            },
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    } else {
        alert(title);
    }
};

let loadingProgressTimer;

window.showLoading = function (title = 'Memuat...') {
    if (typeof Swal !== 'undefined') {
        let progress = 8;
        return Swal.fire({
            title: title,
            html: `
                <div style="margin-top: 10px; text-align: left;">
                    <p style="margin:0 0 8px; color: var(--text-secondary-color); font-size: 12px; font-weight: 600;">Mohon tunggu, data sedang diproses</p>
                    <div style="width:100%;height:8px;background:color-mix(in srgb, var(--border-color) 85%, transparent);border-radius:999px;overflow:hidden;">
                        <div id="smart-loading-fill" style="height:100%;width:${progress}%;background:linear-gradient(90deg,#10b981,#059669);border-radius:999px;transition:width .25s ease;"></div>
                    </div>
                    <div style="margin-top:6px; text-align:right; font-size:11px; color: var(--text-secondary-color); font-weight:700;"><span id="smart-loading-pct">${progress}</span>%</div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: 'var(--secondary-bg)',
            color: 'var(--text-color)',
            customClass: {
                popup: 'smart-modal'
            },
            didOpen: () => {
                const fill = document.getElementById('smart-loading-fill');
                const pct = document.getElementById('smart-loading-pct');
                clearInterval(loadingProgressTimer);
                loadingProgressTimer = setInterval(() => {
                    progress = Math.min(92, progress + Math.max(1, Math.round((100 - progress) / 11)));
                    if (fill) fill.style.width = `${progress}%`;
                    if (pct) pct.textContent = String(progress);
                }, 160);
            },
            willClose: () => {
                clearInterval(loadingProgressTimer);
            }
        });
    }
};

window.hideLoading = function () {
    if (typeof Swal !== 'undefined') {
        clearInterval(loadingProgressTimer);
        Swal.close();
    }
};
