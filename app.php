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
<body>
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
    <!-- Include the footer section (closing tags and main JS script) -->
    <?php include 'views/layout/footer.php'; ?>
</body>
</html>