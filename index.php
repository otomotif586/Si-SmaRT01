<?php
require_once 'config/database.php';
require_once 'config/asset_url.php';
smart_send_html_no_cache_headers();

// Ambil data pengaturan web
$query = "SELECT * FROM web_settings";
$result = mysqli_query($conn, $query);
$settingsData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $settingsData[$row['setting_key']] = $row['setting_value'];
}
$web_nama = $settingsData['web_nama'] ?? 'Si-SmaRT';
$web_visi = $settingsData['web_visi'] ?? 'Visi Belum Diatur';
$web_logo = $settingsData['web_logo'] ?? '';
$web_favicon = $settingsData['web_favicon'] ?? '';

// Transparansi
$web_transparansi_judul = $settingsData['web_transparansi_judul'] ?? 'Transparansi Keuangan';
$web_transparansi_deskripsi = $settingsData['web_transparansi_deskripsi'] ?? 'Laporan keuangan lingkungan.';
$web_transparansi_file = $settingsData['web_transparansi_file'] ?? '';

// Ambil data blog
$query_blogs = "SELECT * FROM web_blogs ORDER BY created_at DESC LIMIT 18";
$blogs = mysqli_query($conn, $query_blogs);

// Ambil data Laporan Keamanan / Terbaru (Fallback jika tabel belum sesuai)
$laporan_terbaru = [];
try {
    // Mencoba mengambil data riil dari tabel keamanan (hanya yang sudah disetujui portal)
    $query_laporan = "SELECT * FROM laporan_keamanan WHERE approved_portal = 1 ORDER BY waktu_kejadian DESC LIMIT 3";
    $laporan_result = @mysqli_query($conn, $query_laporan);

    // Fallback untuk skema lama yang belum memiliki kolom approval
    if (!$laporan_result) {
        $query_laporan = "SELECT * FROM laporan_keamanan ORDER BY waktu_kejadian DESC LIMIT 3";
        $laporan_result = @mysqli_query($conn, $query_laporan);
    }
    
    if($laporan_result && mysqli_num_rows($laporan_result) > 0) {
        while ($row = mysqli_fetch_assoc($laporan_result)) {
            $laporan_terbaru[] = $row;
        }
    }
} catch (Exception $e) {}

// Ambil data pengurus
$query_pengurus = "SELECT * FROM web_pengurus ORDER BY id ASC";
$pengurus_result = mysqli_query($conn, $query_pengurus);
$pengurus = [];
while ($row = mysqli_fetch_assoc($pengurus_result)) {
    $pengurus[] = $row;
}

// Menus
$query_menus = "SELECT * FROM web_menus ORDER BY urutan ASC";
$menus_result = mysqli_query($conn, $query_menus);
$menus = [];
while ($row = mysqli_fetch_assoc($menus_result)) {
    $menus[] = $row;
}

// Slider Data (Local Fallback)
$slides = [
    1 => [
        'image' => $settingsData['web_slider_1_image'] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=68&w=900&auto=format&fit=crop',
        'title' => $settingsData['web_slider_1_title'] ?? 'Panorama Alam',
        'subtitle' => $settingsData['web_slider_1_subtitle'] ?? 'Kawasan Hijau & Asri',
        'description' => $settingsData['web_slider_1_description'] ?? 'Lingkungan yang terjaga kelestariannya untuk kenyamanan bersama.'
    ],
    2 => [
        'image' => $settingsData['web_slider_2_image'] ?? 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=68&w=900&auto=format&fit=crop',
        'title' => $settingsData['web_slider_2_title'] ?? 'Sinergi Warga',
        'subtitle' => $settingsData['web_slider_2_subtitle'] ?? 'Gotong Royong Modern',
        'description' => $settingsData['web_slider_2_description'] ?? 'Membangun kebersamaan melalui kolaborasi digital yang transparan.'
    ],
    3 => [
        'image' => $settingsData['web_slider_3_image'] ?? 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=68&w=900&auto=format&fit=crop',
        'title' => $settingsData['web_slider_3_title'] ?? 'Layanan Cepat',
        'subtitle' => $settingsData['web_slider_3_subtitle'] ?? 'Akses Kapan Saja',
        'description' => $settingsData['web_slider_3_description'] ?? 'Memudahkan urusan warga dengan sistem informasi terpadu.'
    ]
];

// Wisata Data
$wisata = [
    1 => [
        'image' => $settingsData['web_wisata_1_image'] ?? 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200',
        'title' => $settingsData['web_wisata_1_title'] ?? 'Danau Biru Pesona',
        'category' => $settingsData['web_wisata_1_category'] ?? 'WISATA ALAM',
        'description' => $settingsData['web_wisata_1_description'] ?? 'Nikmati ketenangan air danau yang jernih dengan latar bukit asri.'
    ],
    2 => [
        'image' => $settingsData['web_wisata_2_image'] ?? 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?q=80&w=1200',
        'title' => $settingsData['web_wisata_2_title'] ?? 'Lembah Hijau RT01',
        'category' => $settingsData['web_wisata_2_category'] ?? 'WISATA KELUARGA',
        'description' => $settingsData['web_wisata_2_description'] ?? 'Hamparan sawah dan taman bermain untuk keceriaan keluarga.'
    ]
];
?>
<?php
$background_image_url = $settingsData['web_hero_image_1'] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=68&w=900&auto=format&fit=crop';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($web_nama) ?> | Portal Informasi Warga</title>
    
    <!-- Favicon -->
    <?php if($web_favicon): ?><link rel="icon" href="<?= htmlspecialchars(smart_asset($web_favicon), ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Portal CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/portal.css', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>">
    <style>
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('<?= htmlspecialchars(smart_asset($background_image_url), ENT_QUOTES, 'UTF-8') ?>');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            filter: grayscale(10%) blur(2px); /* Efek samar */
            opacity: 0.1; /* Efek semi transparan */
        }
        
        /* Mencegah FOUC (Flash of Unstyled Content) akibat Tailwind CDN */
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
    </style>
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/theme-glass.css', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000); // Fallback
    </script>
</head>
<body class="selection:bg-emerald-100 selection:text-emerald-900">
    <div class="background-overlay"></div>
    <?php 
    // Load Portal Sections
    include 'views/portal/navbar.php';
    include 'views/portal/mobile_menu.php';
    include 'views/portal/hero.php';
    include 'views/portal/stats.php';
    include 'views/portal/info_penting.php';
    include 'views/portal/visimisi.php';
    include 'views/portal/organisasi.php';
    include 'views/portal/berita.php';
    include 'views/portal/transparansi.php';
    include 'views/portal/laporan_terbaru.php';
    include 'views/portal/wisata.php';
    include 'views/portal/footer.php';
    include 'views/portal/modals.php';
    ?>

    <!-- Custom Portal JavaScript -->
    <script src="<?= htmlspecialchars(smart_asset('public/js/portal.js', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>"></script>

</body>
</html>