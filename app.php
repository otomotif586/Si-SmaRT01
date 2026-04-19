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
    <div id="app-debug-panel" style="position:fixed;left:12px;top:12px;z-index:2147483647;width:min(380px,calc(100vw - 24px));max-height:45vh;overflow:auto;padding:12px 14px;border-radius:16px;background:rgba(15,23,42,0.94);color:#e2e8f0;font:12px/1.5 monospace;box-shadow:0 18px 50px rgba(0,0,0,0.35);border:1px solid rgba(255,255,255,0.14);backdrop-filter:blur(14px);">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px;">
            <strong style="font-size:13px;color:#f8fafc;">Si-SmaRT Debug</strong>
            <span id="app-debug-status" style="padding:2px 8px;border-radius:999px;background:rgba(245,158,11,0.18);color:#fde68a;">static</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr auto;gap:6px 10px;align-items:center;">
            <span>boot</span><span id="app-debug-boot">static</span>
            <span>scripts</span><span id="app-debug-scripts">waiting</span>
            <span>page</span><span id="app-debug-page">-</span>
            <span>bootstrap</span><span id="app-debug-bootstrap">-</span>
            <span>api</span><span id="app-debug-api">-</span>
            <span>errors</span><span id="app-debug-errors">0</span>
        </div>
        <div id="app-debug-log" style="margin-top:10px;color:#93c5fd;white-space:pre-wrap;"></div>
    </div>
    <script>
        (function () {
            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.innerText = value;
            };
            setValue('app-debug-boot', 'inline');
            setValue('app-debug-scripts', 'inline-start');
            setValue('app-debug-bootstrap', 'inline');
            setValue('app-debug-status', 'inline');
            window.__appInlineBoot = true;
        })();
    </script>
    <?php
    // Include the sidebar navigation
    include 'views/layout/sidebar.php';
    ?>
    <div class="sidebar-overlay"></div>
    <script>
        window.currentUserRole = <?= json_encode($_SESSION['role'] ?? 'warga') ?>;
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