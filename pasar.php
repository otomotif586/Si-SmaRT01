<?php
require_once 'config/database.php';
require_once 'config/asset_url.php';
smart_send_html_no_cache_headers();

// Auto-create database table jika belum ada (Tanpa repot buka phpMyAdmin)
$pdo->exec("CREATE TABLE IF NOT EXISTS `pasar_produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text,
  `harga` decimal(15,2) NOT NULL DEFAULT 0,
  `foto` text,
  `penjual_nama` varchar(100) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `kategori` varchar(50) DEFAULT 'Umum',
  `status` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS `pasar_profil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(255) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `alamat` text,
  `deskripsi` text,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS `pasar_slider` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) DEFAULT NULL,
    `subtitle` text,
    `badge_text` varchar(100) DEFAULT 'Promo',
    `badge_icon` varchar(50) DEFAULT 'fa-fire',
    `theme_color` varchar(50) DEFAULT 'emerald',
    `urutan` int(11) NOT NULL DEFAULT 1,
    `image` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$ensureColumn = function (string $table, string $column, string $definition) use ($pdo): void {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
};

// Sinkronkan kolom inti agar query CMS slider tidak gagal walau skema lama.
$ensureColumn('pasar_slider', 'title', "varchar(255) DEFAULT NULL");
$ensureColumn('pasar_slider', 'subtitle', "text");
$ensureColumn('pasar_slider', 'badge_text', "varchar(100) DEFAULT 'Promo'");
$ensureColumn('pasar_slider', 'badge_icon', "varchar(50) DEFAULT 'fa-fire'");
$ensureColumn('pasar_slider', 'theme_color', "varchar(50) DEFAULT 'emerald'");
$ensureColumn('pasar_slider', 'urutan', "int(11) NOT NULL DEFAULT 1");
$ensureColumn('pasar_slider', 'image', "text");

// Ambil data produk
$search = $_GET['q'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$queryBase = " FROM pasar_produk WHERE 1=1";
$params = [];
if($search) {
    $queryBase .= " AND (nama_produk LIKE ? OR deskripsi LIKE ? OR penjual_nama LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if($kategori && $kategori !== 'Terlaris') {
    $queryBase .= " AND kategori = ?";
    $params[] = $kategori;
}

// Hitung total produk
$stmtTotal = $pdo->prepare("SELECT COUNT(*)" . $queryBase);
$stmtTotal->execute($params);
$totalProduk = $stmtTotal->fetchColumn();
$totalPages = ceil($totalProduk / $limit);

// Ambil data produk dengan limit & offset
$query = "SELECT *" . $queryBase;
if($kategori === 'Terlaris') {
    $query .= " ORDER BY klik_beli DESC, created_at DESC";
} else {
    $query .= " ORDER BY status DESC, created_at DESC";
}
$query .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data Slider Promosi dari Database
$sliders = [];
try {
    $stmtSlider = $pdo->query("SELECT * FROM pasar_slider ORDER BY urutan ASC");
    $sliders = $stmtSlider->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Fallback agar halaman pasar tetap berjalan walaupun skema slider belum sinkron.
    $sliders = [];
}

// Ambil setting untuk info RT
$stmtSet = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
$settings = $stmtSet->fetchAll(PDO::FETCH_KEY_PAIR);
$web_nama = $settings['web_nama'] ?? 'Portal Warga';
$bg_overlay = $settings['web_hero_image_1'] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1600';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM & Pasar Warga - <?= htmlspecialchars($web_nama) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/mobile-ux.css', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>">
    <!-- Swiper CSS untuk Slider Promosi -->
    <style>
        /* Mencegah FOUC (Flash of Unstyled Content) */
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.96) 0%, rgba(241, 245, 249, 0.88) 100%);
        }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Efek Latar Belakang Semi Transparan Menyesuaikan Tema Portal */
        .background-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image: url('<?= htmlspecialchars(smart_asset($bg_overlay), ENT_QUOTES, 'UTF-8') ?>');
            background-size: cover; background-position: center center; background-repeat: no-repeat;
            filter: grayscale(18%) blur(4px); opacity: 0.11;
        }

        /* Custom Swiper Pagination */
        .swiper-pagination-bullet-active { background-color: #10b981 !important; }

        /* Premium Product Card CSS */
        .card-baru {
            --bg: #fff;
            --title-color: #fff;
            --title-color-hover: #0f172a;
            --text-color: #64748b;
            --button-color: #10b981;
            --button-color-hover: #059669;
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.96));
            border-radius: 1.9rem;
            padding: 0.55rem;
            width: 100%;
            height: 384px;
            overflow: clip;
            position: relative;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 16px 32px -26px rgba(15, 23, 42, 0.28);
            border: 1px solid rgba(148, 163, 184, 0.14);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .card-baru:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 40px -24px rgba(16, 185, 129, 0.25);
            border-color: rgba(16, 185, 129, 0.18);
        }
        @media (max-width: 640px) {
            .card-baru { height: 310px; border-radius: 1.45rem; }
            .card-baru > section h2 { font-size: 1rem !important; }
            .card-baru > section > div button { padding: 0.6rem 1rem 0.6rem 2rem !important; font-size: 0.8rem !important; width: auto !important; }
        }
        @media (max-width: 390px) {
            .card-baru { height: 296px; border-radius: 1.15rem; padding: 0.42rem; }
            .card-baru > .img-container { border-radius: 0.95rem; }
            .card-baru > section { margin: 0.65rem; }
            .card-baru > section h2 { font-size: 0.9rem !important; margin-block-end: 0.5rem; }
            .card-baru > section p { font-size: 0.72rem; }
            .card-baru > section > div .tag { font-size: 0.85rem; }
            .btn-beli-modern { font-size: 0.72rem; padding: 0.45rem 0.85rem; gap: 6px; }
            main.container { padding-left: 10px !important; padding-right: 10px !important; }
            .card-text-skeleton { left: 0.65rem; right: 0.65rem; bottom: 0.65rem; }
        }
        .card-baru::before {
            content: ""; position: absolute; width: calc(100% - 1rem); height: 40%;
            bottom: 0.5rem; left: 0.5rem;
            mask: linear-gradient(#0000, #000f 80%); -webkit-mask: linear-gradient(#0000, #000f 80%);
            backdrop-filter: blur(1rem); -webkit-backdrop-filter: blur(1rem);
            border-radius: 0 0 1.5rem 1.5rem; translate: 0 0; transition: translate 0.3s ease; z-index: 1;
        }
        .card-baru > .img-container {
            max-width: 100%; aspect-ratio: 4 / 5; border-radius: 1.45rem; display: block;
            transition: aspect-ratio 0.3s ease, transform 0.3s ease; width: 100%; height: auto; overflow: hidden; position: relative;
        }

        .card-baru .img-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background: #e2e8f0;
            z-index: 1;
        }

        .card-baru .img-container::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 2;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(226, 232, 240, 0), rgba(255, 255, 255, 0.8), rgba(226, 232, 240, 0));
            animation: pasarImgShimmer 1.2s infinite;
        }

        .card-baru .img-container.images-ready::before,
        .card-baru .img-container.images-ready::after {
            display: none;
        }

        .card-text-skeleton {
            position: absolute;
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
            z-index: 12;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: opacity 0.25s ease;
            pointer-events: none;
        }

        .card-text-skeleton .line {
            height: 9px;
            border-radius: 999px;
        }

        .card-baru.card-ready .card-text-skeleton {
            opacity: 0;
        }

        @keyframes pasarImgShimmer {
            100% { transform: translateX(100%); }
        }
        .card-baru > section {
            margin: 0.95rem; height: calc(40% - 1rem); display: flex; flex-direction: column;
            position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
        }
        .card-baru > section h2 {
            margin: 0; margin-block-end: 0.9rem; font-size: 1.2rem; font-weight: 800; opacity: 0;
            translate: 0 -200%; color: var(--title-color);
            transition: color 0.5s ease, margin-block-end 0.3s ease, opacity 0.8s ease, translate 0.3s ease;
            text-shadow: 0 2px 4px rgba(0,0,0,0.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .card-baru > section p {
            font-size: 0.85rem; line-height: 1.3; color: var(--text-color); opacity: 0; margin: 0;
            translate: 0 100%; transition: margin-block-end 0.3s ease, opacity 0.8s ease 0.1s, translate 0.3s ease 0.1s;
            display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .card-baru > section > div {
            flex: 1; align-items: flex-end; display: flex; justify-content: space-between; opacity: 0;
            transition: translate 0.3s ease 0.2s, opacity 0.8s ease 0.2s;
        }
        .card-baru > section > div .tag { color: var(--title-color-hover); font-weight: 900; font-size: 1.05rem; }
        
        /* Modern Elegant Buy Button */
        .btn-beli-modern {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.62rem 1.15rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff !important; font-size: 0.85rem; font-weight: 800; border-radius: 99px;
            text-decoration: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            translate: 0.5rem;
        }
        .btn-beli-modern:hover {
            transform: translateY(-2px) scale(1.03); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .btn-beli-modern.clicked {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); transform: scale(0.95);
        }

        .card-baru:hover::before, .card-baru:focus-within::before { translate: 0 100%; }
        .card-baru:hover > .img-container, .card-baru:focus-within > .img-container { aspect-ratio: 1 / 1; height: 50%; }
        .card-baru:hover > section h2, .card-baru:focus-within > section h2,
        .card-baru:hover > section p, .card-baru:focus-within > section p { translate: 0 0; margin-block-end: 0.5rem; opacity: 1; }
        .card-baru:hover > section h2, .card-baru:focus-within > section h2 { color: var(--title-color-hover); text-shadow: none; }
        .card-baru:hover > section > div, .card-baru:focus-within > section > div { translate: 0 0; opacity: 1; }

        /* Swiper Product Custom */
        .productSwiper { width: 100%; height: 100%; border-radius: 1rem; }
        .productSwiper img { width: 100%; height: 100%; object-fit: cover; border-radius: 1rem; display: block; transition: transform 0.4s ease; }
        .productSwiper .swiper-pagination-fraction {
            bottom: unset !important; top: 15px !important; left: 50% !important; transform: translateX(-50%);
            width: auto !important; background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1); color: white; font-size: 9px; font-weight: 900;
            padding: 4px 12px; border-radius: 20px; z-index: 20;
        }
        /* Glassmorphism Badge */
        .glass-badge {
            background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; color: #fff;
            font-size: 10px; font-weight: 800; padding: 4px 10px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* --- STACKING CARDS CSS FOR PASAR --- */
        .stack-area-pasar {
            margin-top: 2rem;
            margin-bottom: 4rem;
            position: relative;
        }

        .card-stack-pasar {
            min-height: 290px;
            position: sticky;
            top: 15vh;
            background: linear-gradient(145deg, rgba(255,255,255,0.92), rgba(240,253,244,0.92));
            border: 1px solid rgba(16, 185, 129, 0.12);
            border-radius: 30px;
            padding: 24px;
            margin-bottom: 6.5vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            overflow: hidden;
            backdrop-filter: blur(18px);
            box-shadow: 0 16px 40px -28px rgba(15, 23, 42, 0.24);
            transform-origin: center top;
            transition: transform 0.3s ease-out, filter 0.3s ease-out;
            will-change: transform;
            z-index: 5;
        }

        .card-stack-pasar:nth-child(1) { background: linear-gradient(145deg, rgba(236, 253, 245, 0.95), rgba(255, 255, 255, 0.95)); }
        .card-stack-pasar:nth-child(2) { background: linear-gradient(145deg, rgba(236, 254, 255, 0.95), rgba(255, 255, 255, 0.95)); }
        .card-stack-pasar:nth-child(3) { background: linear-gradient(145deg, rgba(255, 251, 235, 0.95), rgba(255, 255, 255, 0.95)); }

        .card-stack-pasar h2 {
            font-size: clamp(1.45rem, 2vw, 2.15rem);
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 10px 0;
            letter-spacing: -0.04em;
        }

        .card-stack-content-pasar { flex: 1.45; padding-right: 10px; }
        .card-stack-pasar p { font-size: 0.95rem; color: #64748b; line-height: 1.65; margin: 0; }
        .card-stack-pasar .badge-pasar { 
            display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 9px; 
            font-weight: 800; color: #fff; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 2px;
            box-shadow: 0 12px 20px -14px rgba(15, 23, 42, 0.35);
        }
        
        .card-stack-img-pasar {
            flex: 1; height: 100%; border-radius: 24px; overflow: hidden; position: relative;
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: 0 18px 30px -24px rgba(15, 23, 42, 0.2);
        }
        .card-stack-img-pasar img { width: 100%; height: 100%; object-fit: cover; opacity: 0.95; transition: transform 0.6s ease; }
        .card-stack-pasar:hover .card-stack-img-pasar img { transform: scale(1.05); opacity: 1; }

        @media (max-width: 768px) {
            .card-stack-pasar { flex-direction: column; height: auto; min-height: 0; top: 18vh; padding: 18px; border-radius: 24px; }
            .card-stack-content-pasar { padding-right: 0; margin-bottom: 16px; flex: 0; }
            .card-stack-pasar h2 { font-size: 1.45rem; }
            .card-stack-pasar p { font-size: 0.9rem; }
            .card-stack-img-pasar { height: 190px; width: 100%; }
        }
    </style>
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/theme-glass.css', smart_asset_version()), ENT_QUOTES, 'UTF-8') ?>">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000); // Fallback
    </script>
</head>
<body class="pb-24 selection:bg-emerald-200">
    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Menyiapkan halaman...</h3>
            <p class="smart-loading-subtitle">Mengambil produk terbaru warga</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>

    <div class="background-overlay"></div>
    
    <!-- Sticky Top Navigation & Filter -->
    <div class="sticky top-0 z-50">
        <header class="bg-white/95 backdrop-blur-xl border-b border-slate-100">
            <div class="container mx-auto px-4 py-3 md:py-4">
                <div class="flex items-center gap-3 mb-3 md:mb-4">
                    <a href="index.php" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="flex-1">
                        <h1 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight leading-tight">Pasar Warga</h1>
                        <p class="text-[10px] md:text-xs uppercase tracking-widest text-emerald-600 font-bold">Dari Warga, Untuk Warga</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="login_penjual.php" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-[10px] md:text-sm hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-200 border border-emerald-500">
                            <i class="fa-solid fa-shop"></i> <span class="block">Ruang Penjual</span>
                        </a>
                        <a href="ruang_warga.php" class="w-10 h-10 md:w-auto md:px-4 md:py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs md:text-sm hover:bg-slate-200 transition-colors flex items-center justify-center md:gap-2 border border-slate-200">
                            <i class="fas fa-user-circle text-lg md:text-base"></i> <span class="hidden md:inline">Masuk</span>
                        </a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="relative">
                    <form action="pasar.php" method="GET">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari makanan, jasa, barang..." class="w-full bg-slate-100 text-slate-800 rounded-2xl py-3 pl-11 pr-4 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all text-sm border border-transparent focus:bg-white focus:border-emerald-200 shadow-inner">
                    </form>
                </div>
            </div>
        </header>

        <!-- Horizontal Categories -->
        <div class="bg-white/95 backdrop-blur-xl shadow-sm border-b border-slate-100">
            <div class="container mx-auto px-4 py-3 md:py-4">
                <div class="flex overflow-x-auto gap-2 hide-scroll pb-1 snap-x">
                    <a href="pasar.php" class="snap-start whitespace-nowrap px-5 py-2 rounded-full text-xs font-bold transition-all <?= $kategori == '' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' ?>">Semua</a>
                    <a href="pasar.php?kategori=Terlaris" class="snap-start whitespace-nowrap px-5 py-2 rounded-full text-xs font-bold transition-all <?= $kategori == 'Terlaris' ? 'bg-orange-500 text-white shadow-md shadow-orange-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' ?>">🔥 Terlaris</a>
                    <a href="pasar.php?kategori=Makanan" class="snap-start whitespace-nowrap px-5 py-2 rounded-full text-xs font-bold transition-all <?= $kategori == 'Makanan' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' ?>">🍲 Makanan</a>
                    <a href="pasar.php?kategori=Jasa" class="snap-start whitespace-nowrap px-5 py-2 rounded-full text-xs font-bold transition-all <?= $kategori == 'Jasa' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' ?>">🔧 Jasa</a>
                    <a href="pasar.php?kategori=Barang" class="snap-start whitespace-nowrap px-5 py-2 rounded-full text-xs font-bold transition-all <?= $kategori == 'Barang' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' ?>">📦 Barang</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <main class="container mx-auto px-4 pt-6 md:pt-8">

        <!-- Header Media Promosi Stacking Cards -->
        <div class="stack-area-pasar">
            <?php if(!empty($sliders)): ?>
                <?php foreach($sliders as $sl): ?>
                <div class="card-stack-pasar js-passed-card">
                    <div class="card-stack-content-pasar">
                        <span class="badge-pasar bg-<?= htmlspecialchars($sl['theme_color']) ?>-600 shadow-lg shadow-<?= htmlspecialchars($sl['theme_color']) ?>-900/40">
                            <i class="fas <?= htmlspecialchars($sl['badge_icon']) ?> mr-1"></i> <?= htmlspecialchars($sl['badge_text']) ?>
                        </span>
                        <h2><?= htmlspecialchars($sl['title']) ?></h2>
                        <p><?= htmlspecialchars($sl['subtitle']) ?></p>
                    </div>
                    <div class="card-stack-img-pasar">
                        <img src="<?= htmlspecialchars($sl['image']) ?>" alt="<?= htmlspecialchars($sl['title']) ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Dummy Stack 1 -->
                <div class="card-stack-pasar js-passed-card">
                    <div class="card-stack-content-pasar">
                        <span class="badge-pasar bg-emerald-600">Promo Spesial</span>
                        <h2>Diskon UMKM Warga</h2>
                        <p>Dukung usaha tetangga, nikmati potongan harga khusus hari ini.</p>
                    </div>
                    <div class="card-stack-img-pasar">
                       <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=1200" alt="Promo">
                    </div>
                </div>
                <!-- Dummy Stack 2 -->
                <div class="card-stack-pasar js-passed-card">
                    <div class="card-stack-content-pasar">
                        <span class="badge-pasar bg-orange-600">Buka Toko</span>
                        <h2>Jualan Makin Laris</h2>
                        <p>Daftarkan usaha atau jasa Anda melalui aplikasi SmaRT sekarang juga.</p>
                    </div>
                    <div class="card-stack-img-pasar">
                       <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?q=80&w=1200" alt="Toko">
                    </div>
                </div>
                <!-- Dummy Stack 3 -->
                <div class="card-stack-pasar js-passed-card">
                    <div class="card-stack-content-pasar">
                        <span class="badge-pasar bg-blue-600">Belanja Mudah</span>
                        <h2>Penuhi Kebutuhan Harian</h2>
                        <p>Cari sayur, lauk, hingga jasa servis cukup dari rumah saja.</p>
                    </div>
                    <div class="card-stack-img-pasar">
                       <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1200" alt="Belanja">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if(empty($produk)): ?>
            <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-slate-200 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl">
                    <i class="fas fa-store-slash"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Belum ada produk</h3>
                <p class="text-slate-500 text-sm mt-2 font-medium">Coba cari kata kunci lain atau cek kembali nanti.</p>
                <div class="mt-8 max-w-md mx-auto space-y-3 px-6" aria-hidden="true">
                    <div class="h-3 rounded-full smart-skeleton"></div>
                    <div class="h-3 rounded-full smart-skeleton" style="width: 80%; margin-left: auto; margin-right: auto;"></div>
                    <div class="h-3 rounded-full smart-skeleton" style="width: 65%; margin-left: auto; margin-right: auto;"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6">
                <?php foreach($produk as $p): 
                    // Format & Bersihkan nomor WA
                    $wa = preg_replace('/[^0-9]/', '', $p['no_wa']);
                    if(str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);
                    
                    $msg = urlencode("Halo {$p['penjual_nama']}, saya tertarik dengan *{$p['nama_produk']}* (Rp " . number_format($p['harga'],0,',','.') . ") yang ada di Portal Warga. Apakah masih tersedia?");
                    
                    // Decode Foto JSON
                    $photos = [];
                    try { $photos = json_decode($p['foto'], true) ?: []; } catch(Exception $e) { if($p['foto']) $photos = [$p['foto']]; }
                    $foto = !empty($photos) ? $photos[0] : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
                ?>
                <!-- Product Card Premium -->
                <div class="card-baru group">
                    <div class="img-container relative h-full">
                        <div class="swiper productSwiper">
                            <div class="swiper-wrapper">
                                <?php if(empty($photos)): ?>
                                    <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80" class="js-card-photo" loading="lazy" decoding="async"></div>
                                <?php else: ?>
                                    <?php foreach($photos as $ft): ?>
                                        <div class="swiper-slide"><img src="<?= htmlspecialchars($ft) ?>" class="js-card-photo" loading="lazy" decoding="async"></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if(count($photos) > 1): ?>
                                <div class="swiper-pagination"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Gradient Overlay untuk memperjelas teks -->
                        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-transparent to-slate-900/90 z-[5] pointer-events-none transition-opacity duration-400 group-hover:opacity-0"></div>

                        <!-- Top Information (Toko & Badge) - Tetap tampil saat hover agar bisa diklik -->
                        <div class="absolute top-4 left-4 right-4 z-[10] flex justify-between items-start">
                            <div class="flex flex-col gap-2">
                                <a href="toko.php?penjual=<?= urlencode($p['penjual_nama']) ?>" class="flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-md rounded-full border border-white/30 text-white hover:bg-emerald-500 hover:border-emerald-400 transition-all shadow-sm w-max" title="Kunjungi Toko">
                                    <i class="fas fa-store text-[10px]"></i>
                                    <span class="text-[10px] font-bold tracking-wide truncate max-w-[120px]"><?= htmlspecialchars($p['penjual_nama']) ?></span>
                                </a>
                                <?php if($p['status'] == 'Habis'): ?>
                                    <span class="bg-red-500/90 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-md w-max">Stok Habis</span>
                                <?php endif; ?>
                            </div>
                            <span class="glass-badge shadow-sm">
                                <?= htmlspecialchars($p['kategori']) ?>
                            </span>
                        </div>

                        <!-- Bottom Information (Nama Produk & Harga) - Hilang saat hover -->
                        <div class="absolute bottom-5 left-5 right-5 z-[10] transition-all duration-400 group-hover:translate-y-4 group-hover:opacity-0 pointer-events-none">
                            <h2 class="text-lg md:text-xl font-black text-white leading-tight mb-1 drop-shadow-md line-clamp-2"><?= htmlspecialchars($p['nama_produk']) ?></h2>
                            <span class="text-emerald-400 font-black text-lg drop-shadow-md">Rp <?= number_format($p['harga'],0,',','.') ?></span>
                        </div>
                    </div>

                    <div class="card-text-skeleton" aria-hidden="true">
                        <div class="line smart-skeleton" style="width: 74%;"></div>
                        <div class="line smart-skeleton" style="width: 48%;"></div>
                    </div>

                    <!-- Bagian Detail yang Menggeser dari Bawah Saat Hover -->
                    <section>
                        <h2><?= htmlspecialchars($p['nama_produk']) ?></h2>
                        <p class="line-clamp-3"><?= htmlspecialchars($p['deskripsi'] ?: 'Tidak ada deskripsi produk.') ?></p>
                        
                        <div>
                            <div class="flex flex-col gap-0.5 overflow-hidden">
                                <span class="tag whitespace-nowrap">Rp <?= number_format($p['harga'],0,',','.') ?></span>
                                <span class="text-[0.65rem] font-bold text-slate-500 truncate mt-1"><i class="fas fa-store text-emerald-500 mr-1"></i> <?= htmlspecialchars($p['penjual_nama']) ?></span>
                            </div>
                            
                            <a href="https://wa.me/<?= $wa ?>?text=<?= $msg ?>" target="_blank" class="btn-beli-modern" onclick="trackAlgoritmaBeli(<?= $p['id'] ?>); this.classList.add('clicked'); setTimeout(() => this.classList.remove('clicked'), 2000)">
                                <i class="fab fa-whatsapp text-lg"></i> Beli
                            </a>
                        </div>
                    </section>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div class="mt-12 flex justify-center items-center gap-2 pb-10">
                <?php if ($page > 1): ?>
                    <a href="pasar.php?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori) ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                <?php endif; ?>

                <?php 
                $startRange = max(1, $page - 2);
                $endRange = min($totalPages, $page + 2);
                if ($startRange > 1) echo '<span class="text-slate-400 px-2 font-bold">...</span>';
                for ($i = $startRange; $i <= $endRange; $i++): ?>
                    <a href="pasar.php?page=<?= $i ?>&q=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori) ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black transition-all <?= $i == $page ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; 
                if ($endRange < $totalPages) echo '<span class="text-slate-400 px-2 font-bold">...</span>';
                ?>

                <?php if ($page < $totalPages): ?>
                    <a href="pasar.php?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori) ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        let pageLoadingTick;
        function showPageLoading(title = 'Memuat...') {
            const overlay = document.getElementById('smartLoadingOverlay');
            const fill = document.getElementById('smartLoadingFill');
            const pct = document.getElementById('smartLoadingPct');
            const heading = document.getElementById('smartLoadingTitle');
            if (!overlay || !fill || !pct) return;
            if (heading) heading.textContent = title;
            let value = 8;
            fill.style.width = value + '%';
            pct.textContent = String(value);
            overlay.classList.add('active');
            clearInterval(pageLoadingTick);
            pageLoadingTick = setInterval(() => {
                value = Math.min(90, value + Math.max(1, Math.round((100 - value) / 12)));
                fill.style.width = value + '%';
                pct.textContent = String(value);
            }, 150);
        }

        function hidePageLoading() {
            clearInterval(pageLoadingTick);
            const overlay = document.getElementById('smartLoadingOverlay');
            if (overlay) overlay.classList.remove('active');
        }

        function initCardImageSkeletons() {
            document.querySelectorAll('.card-baru .img-container').forEach((container) => {
                const images = container.querySelectorAll('img.js-card-photo');
                if (!images.length) {
                    container.classList.add('images-ready');
                    return;
                }

                let loaded = 0;
                const markLoaded = () => {
                    loaded += 1;
                    if (loaded >= images.length) {
                        container.classList.add('images-ready');
                        const card = container.closest('.card-baru');
                        if (card) card.classList.add('card-ready');
                    }
                };

                images.forEach((img) => {
                    if (img.complete) {
                        markLoaded();
                    } else {
                        img.addEventListener('load', markLoaded, { once: true });
                        img.addEventListener('error', markLoaded, { once: true });
                    }
                });
            });
        }

        // Show first-load progress for better perceived performance on mobile.
        showPageLoading('Memuat Pasar Warga...');
        window.addEventListener('load', () => setTimeout(hidePageLoading, 350));

        // Inisialisasi Slider Promo
        const swiper = new Swiper('.promoSwiper', {
            loop: true,
            autoplay: { delay: 4500, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
        });

        // Inisialisasi Multi Carousel Produk
        const productSwiper = new Swiper('.productSwiper', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                type: 'fraction',
            },
        });
        initCardImageSkeletons();

        // Stacking Cards Scroll Listener for Pasar
        const passedCards = document.querySelectorAll('.js-passed-card');
        window.addEventListener('scroll', () => {
            const viewportHeight = window.innerHeight;
            const isMobile = window.innerWidth <= 768;
            const stickyTopOffset = isMobile ? viewportHeight * 0.18 : viewportHeight * 0.15;

            passedCards.forEach((card, index) => {
                const nextCard = passedCards[index + 1];
                if (nextCard) {
                    const nextRect = nextCard.getBoundingClientRect();
                    const distance = nextRect.top - stickyTopOffset;

                    if (distance < viewportHeight && distance > 0) {
                        const maxShrink = isMobile ? 0.95 : 0.90;
                        const factor = (1 - maxShrink) / viewportHeight;
                        const scale = 1 - ((viewportHeight - distance) * factor);
                        const finalScale = Math.max(maxShrink, Math.min(1, scale));
                        const brightness = Math.max(0.6, Math.min(1, scale));
                        card.style.transform = `scale(${finalScale})`;
                        card.style.filter = `brightness(${brightness})`;
                    } else if (distance <= 0) {
                        const maxShrink = isMobile ? 0.95 : 0.90;
                        card.style.transform = `scale(${maxShrink})`;
                        card.style.filter = `brightness(0.6)`;
                    } else {
                        card.style.transform = `scale(1)`;
                        card.style.filter = `brightness(1)`;
                    }
                }
            });
        });

        // Fungsi Algoritma Perekam Klik (Hit API Tanpa Reload)
        function trackAlgoritmaBeli(id) {
            const fd = new FormData(); fd.append('id', id);
            fetch('views/pages/track_beli.php', { method: 'POST', body: fd }).catch(e => console.log('Tracking failed', e));
        }

        // Display loading when navigating via filter/category/search/pagination on the same page.
        document.querySelectorAll('a[href^="pasar.php"]').forEach(link => {
            link.addEventListener('click', () => showPageLoading('Memperbarui daftar produk...'));
        });
        const searchForm = document.querySelector('form[action="pasar.php"]');
        if (searchForm) {
            searchForm.addEventListener('submit', () => showPageLoading('Mencari produk...'));
        }
    </script>
</body>
</html>

