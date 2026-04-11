<?php
require_once 'config/database.php';

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
$query_blogs = "SELECT * FROM web_blogs ORDER BY created_at DESC LIMIT 6";
$blogs = mysqli_query($conn, $query_blogs);

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
        'image' => $settingsData['web_hero_image_1'] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1600',
        'title' => $settingsData['web_hero_slide_1_title'] ?? 'Panorama Alam',
        'subtitle' => $settingsData['web_hero_slide_1_subtitle'] ?? 'Kawasan Hijau & Asri',
        'description' => $settingsData['web_hero_slide_1_desc'] ?? 'Lingkungan yang terjaga kelestariannya untuk kenyamanan bersama.'
    ],
    2 => [
        'image' => $settingsData['web_hero_image_2'] ?? 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=1600',
        'title' => $settingsData['web_hero_slide_2_title'] ?? 'Sinergi Warga',
        'subtitle' => $settingsData['web_hero_slide_2_subtitle'] ?? 'Gotong Royong Modern',
        'description' => $settingsData['web_hero_slide_2_desc'] ?? 'Membangun kebersamaan melalui kolaborasi digital yang transparan.'
    ],
    3 => [
        'image' => $settingsData['web_hero_image_3'] ?? 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1600',
        'title' => $settingsData['web_hero_slide_3_title'] ?? 'Layanan Cepat',
        'subtitle' => $settingsData['web_hero_slide_3_subtitle'] ?? 'Akses Kapan Saja',
        'description' => $settingsData['web_hero_slide_3_desc'] ?? 'Memudahkan urusan warga dengan sistem informasi terpadu.'
    ]
];

// Wisata Data
$wisata = [
    1 => [
        'image' => $settingsData['web_wisata_1_image'] ?? 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200',
        'title' => $settingsData['web_wisata_1_title'] ?? 'Danau Biru Pesona',
        'category' => $settingsData['web_wisata_1_category'] ?? 'WISATA ALAM',
        'description' => $settingsData['web_wisata_1_desc'] ?? 'Nikmati ketenangan air danau yang jernih dengan latar bukit asri.'
    ],
    2 => [
        'image' => $settingsData['web_wisata_2_image'] ?? 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?q=80&w=1200',
        'title' => $settingsData['web_wisata_2_title'] ?? 'Lembah Hijau RT01',
        'category' => $settingsData['web_wisata_2_category'] ?? 'WISATA KELUARGA',
        'description' => $settingsData['web_wisata_2_desc'] ?? 'Hamparan sawah dan taman bermain untuk keceriaan keluarga.'
    ]
];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($web_nama) ?> | Portal Informasi Warga</title>
    
    <!-- Favicon -->
    <?php if($web_favicon): ?><link rel="icon" href="<?= $web_favicon ?>"><?php endif; ?>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Portal CSS -->
    <link rel="stylesheet" href="public/css/portal.css">
</head>
<body class="selection:bg-emerald-100 selection:text-emerald-900">

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
    include 'views/portal/wisata.php';
    include 'views/portal/footer.php';
    include 'views/portal/modals.php';
    ?>

    <!-- Custom Portal JavaScript -->
    <script src="public/js/portal.js"></script>

</body>
</html>