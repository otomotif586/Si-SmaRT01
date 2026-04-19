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