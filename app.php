<?php
// app.php - Main entry point untuk aplikasi Si-SmaRT
session_start();

// Jika belum login, lempar ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Load Database Connection
require_once 'config/database.php';
require_once 'config/asset_url.php';
smart_send_html_no_cache_headers();

// Include the head section (meta, title, CSS, JS libraries)
include 'views/layout/head.php';
?>
<!DOCTYPE html>
<html lang="id">
<body class="smart-installer-app sx-font-body sx-installer-shell">
    <div class="smart-installer-navbar">
        <div class="si-nav-inner">
            <a href="#" class="si-logo">
                <span class="si-logo-badge"><i data-lucide="sparkles"></i></span>
                <span>Si SmaRT</span>
            </a>
            <div class="si-nav-links">
                <a href="#main-content">Overview</a>
                <a href="#">Features</a>
                <a href="#">Changelog v2.0</a>
            </div>
            <a href="index.php" class="si-download-btn" data-si-install>Download Now</a>
        </div>
    </div>
    <?php
    // Include the sidebar navigation
    include 'views/layout/sidebar.php';
    ?>
    <div class="sidebar-overlay"></div>
    <script>
        window.currentUserRole = <?= json_encode($_SESSION['role'] ?? 'warga') ?>;

        // Fallback globals for inline onclick handlers in sidebar.
        // These are used only if core.js has not been loaded yet.
        if (typeof window.toggleSubmenu !== 'function') {
            window.toggleSubmenu = function (submenuId) {
                document.querySelectorAll('.submenu-items.active').forEach(function (menu) {
                    if (menu.id !== submenuId) {
                        menu.classList.add('hidden');
                        menu.classList.remove('active');
                        var otherBtn = document.getElementById(menu.id.replace('submenu-', 'nav-group-'));
                        if (otherBtn) otherBtn.classList.remove('open');
                    }
                });

                var submenu = document.getElementById(submenuId);
                if (!submenu) return;

                var toggleBtn = document.getElementById(submenuId.replace('submenu-', 'nav-group-'));
                var isHidden = submenu.classList.contains('hidden');

                if (isHidden) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('active');
                    if (toggleBtn) toggleBtn.classList.add('open');
                } else {
                    submenu.classList.add('hidden');
                    submenu.classList.remove('active');
                    if (toggleBtn) toggleBtn.classList.remove('open');
                }
            };
        }

        if (typeof window.showPage !== 'function') {
            window.showPage = function (pageId) {
                try {
                    localStorage.setItem('activePage', pageId);
                } catch (e) {
                    // Ignore storage issues in fallback mode.
                }

                document.querySelectorAll('.page-content').forEach(function (p) {
                    p.classList.add('hidden');
                    p.style.display = 'none';
                });

                document.querySelectorAll('#sidebar .sidebar-nav button').forEach(function (b) {
                    b.classList.remove('active-tab');
                });

                var targetPage = document.getElementById('page-' + pageId);
                if (!targetPage) return;

                targetPage.classList.remove('hidden');
                targetPage.style.display = 'block';

                var activeNav = document.getElementById('nav-' + pageId);
                if (activeNav) activeNav.classList.add('active-tab');
                window.scrollTo(0, 0);
            };
        }
    </script>
    <main id="main-content">
        <?php
        // Include the main content header
        include 'views/layout/header.php';

        // Installer-style hero layer (visual only, no feature impact)
        ?>
        <section class="smart-installer-hero">
            <div class="si-panel si-main-card">
                <div class="si-app-meta">
                    <div class="si-app-icon"><i data-lucide="layout-dashboard"></i></div>
                    <div>
                        <h1 class="si-app-title">Si SmaRT</h1>
                        <p class="si-app-publisher">Go Digital</p>
                    </div>
                </div>
                <div class="si-badges">
                    <span class="si-badge">4.9 ★ Rating</span>
                    <span class="si-badge">12K+ Download</span>
                    <span class="si-badge">Secure Installer</span>
                </div>
                <button class="si-cta" type="button" data-si-install>Install / Get Started</button>
                <div class="si-install-track"><span class="si-install-fill"></span></div>
                <div class="si-steps">
                    <div class="si-step is-active">Downloading</div>
                    <div class="si-step">Verifying</div>
                    <div class="si-step">Installing</div>
                    <div class="si-step">Ready</div>
                </div>
            </div>
            <div class="si-panel si-right-stack">
                <div class="si-phone-shot">Split Hero Mockup</div>
                <div class="si-phone-shot">Mobile Screenshot</div>
                <div class="si-phone-shot">Permission Preview</div>
            </div>
        </section>

        <section class="si-sections">
            <div class="si-grid-3">
                <article class="si-card"><h4>Feature Grid</h4><p>Dashboard modular, laporan real-time, dan manajemen warga dalam satu panel.</p></article>
                <article class="si-card"><h4>System Requirements</h4><p>Berjalan optimal di browser modern, Android, iOS, serta desktop responsif.</p></article>
                <article class="si-card"><h4>Installer Flow</h4><p>Experience ala app installer: progres, steps, dan CTA tetap konsisten di mobile/PC.</p></article>
            </div>
            <article class="si-card">
                <h4>Compatible With</h4>
                <div class="si-compat">
                    <span class="si-chip">Windows</span>
                    <span class="si-chip">macOS</span>
                    <span class="si-chip">Linux</span>
                    <span class="si-chip">Chrome</span>
                    <span class="si-chip">Safari</span>
                </div>
            </article>
            <article class="si-card">
                <h4>What's New In v2.0</h4>
                <ul class="si-release-list">
                    <li>Modern startup visual layer untuk mode mobile.</li>
                    <li>Installer progress animation dengan step transitions.</li>
                    <li>Sticky floating install bar dan quick download actions.</li>
                </ul>
            </article>
            <footer class="si-footer">
                <div>
                    <a href="#">Docs</a>
                    <a href="#">Privacy</a>
                    <a href="#">Support</a>
                </div>
                <span>Made with ❤</span>
            </footer>
        </section>

        <?php

        // Determine which page content to load
        include 'views/pages/dashboard.php';
        include 'views/pages/global_warga.php';
        include 'views/pages/laporan_iuran_blok.php';
        include 'views/pages/laporan_iuran_warga.php';
        include 'views/pages/rekonsiliasi.php';
        include 'views/pages/warga.php';
        include 'views/pages/keuangan.php';
        include 'views/pages/detail_keuangan.php';
        include 'views/pages/pos_keuangan.php';
        include 'views/pages/pembukuan.php';
        include 'views/pages/keamanan.php';
        include 'views/pages/info.php';
        include 'views/pages/users.php';
        include 'views/pages/pasar.php';
        ?>
    </main>
    <div class="si-mobile-install-bar">
        <p>Si SmaRT v2.0 siap diinstal</p>
        <button type="button" data-si-install>Download</button>
    </div>
    <!-- Include the footer section (closing tags and main JS script) -->
    <?php include 'views/layout/footer.php'; ?>
</body>
</html>