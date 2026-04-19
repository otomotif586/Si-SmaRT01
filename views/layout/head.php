<?php require_once __DIR__ . '/../../config/asset_url.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi RT Modern</title>
    <meta name="theme-color" id="theme-meta" content="#f8fafc">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest" onerror="window.__lucideLoadFallback && window.__lucideLoadFallback()"></script>
    <script>
        window.__lucideLoadFallback = function () {
            if (window.lucide || document.getElementById('lucide-fallback-script')) return;
            const s = document.createElement('script');
            s.id = 'lucide-fallback-script';
            s.src = 'https://cdn.jsdelivr.net/npm/lucide@latest';
            s.defer = true;
            document.head.appendChild(s);
        };
        window.__ensureLucide = function () {
            if (!window.lucide) {
                window.__lucideLoadFallback();
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" onerror="window.__chartLoadFallback && window.__chartLoadFallback()"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" onerror="window.__swalLoadFallback && window.__swalLoadFallback()"></script>
    <script>
        window.__chartLoadFallback = function () {
            if (window.Chart || document.getElementById('chart-fallback-script')) return;
            const s = document.createElement('script');
            s.id = 'chart-fallback-script';
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';
            s.defer = true;
            document.head.appendChild(s);
        };
        window.__swalLoadFallback = function () {
            if (window.Swal || document.getElementById('swal-fallback-script')) return;
            const s = document.createElement('script');
            s.id = 'swal-fallback-script';
            s.src = 'https://unpkg.com/sweetalert2@11/dist/sweetalert2.all.min.js';
            s.defer = true;
            document.head.appendChild(s);
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Modular CSS (split by function) -->
    <?php $v = smart_asset_version(); // Sumber versi asset terpusat ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/animations.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/core.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/layout.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/components.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/workspace.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/warga.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/ruang-warga.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/agenda.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/gallery.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/rekonsiliasi.css', $v), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/mobile-ux.css', $v), ENT_QUOTES, 'UTF-8') ?>">

    <!-- FOUC Prevention untuk Tailwind CDN -->
    <style>
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
    </style>
    <script>
        window.__SMART_ASSET_BASE_PATH__ = <?= json_encode(smart_base_path(), JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/theme-glass.css', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000); // Fallback
    </script>
</head>