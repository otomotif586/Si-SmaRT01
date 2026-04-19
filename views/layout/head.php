<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi RT Modern</title>
    <meta name="theme-color" id="theme-meta" content="#f8fafc">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Modular CSS (split by function) -->
    <?php $v = '1.0.1'; // Hapus time() agar browser bisa meng-cache CSS dengan baik ?>
    <link rel="stylesheet" href="public/css/animations.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/core.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/layout.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/components.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/workspace.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/warga.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/ruang-warga.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/agenda.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/gallery.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/rekonsiliasi.css?v=<?= $v ?>">
    <link rel="stylesheet" href="public/css/mobile-ux.css?v=<?= $v ?>">

    <!-- FOUC Prevention untuk Tailwind CDN -->
    <style>
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
    </style>
    <link rel="stylesheet" href="public/css/theme-glass.css?v=20260417">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000); // Fallback
    </script>
</head>