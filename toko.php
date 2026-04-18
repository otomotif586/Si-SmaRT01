<?php
require_once 'config/database.php';

$penjual = $_GET['penjual'] ?? '';
if (!$penjual) {
    header('Location: pasar.php');
    exit;
}

$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil profil toko jika ada
$stmtProf = $pdo->prepare("SELECT * FROM pasar_profil WHERE nama_toko = ?");
$stmtProf->execute([$penjual]);
$profile = $stmtProf->fetch(PDO::FETCH_ASSOC);

// Hitung total produk penjual ini
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM pasar_produk WHERE penjual_nama = ? AND status = 'Tersedia'");
$stmtTotal->execute([$penjual]);
$totalProduk = $stmtTotal->fetchColumn();
$totalPages = ceil($totalProduk / $limit);

// Ambil dagangan dari penjual ini dengan limit & offset
$stmtProd = $pdo->prepare("SELECT * FROM pasar_produk WHERE penjual_nama = ? AND status = 'Tersedia' ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmtProd->execute([$penjual]);
$products = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// Ambil setting web
$stmtSet = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
$settings = $stmtSet->fetchAll(PDO::FETCH_KEY_PAIR);
$web_nama = $settings['web_nama'] ?? 'Portal Warga';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog <?= htmlspecialchars($penjual) ?> - <?= htmlspecialchars($web_nama) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        /* Mencegah FOUC */
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .product-card { border-radius: 2.5rem; overflow: hidden; height: 100%; transition: all 0.4s ease; border: 1px solid #f1f5f9; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(16, 185, 129, 0.1); }
        
        .productSwiper { width: 100%; height: 100%; }
        .productSwiper img { width: 100%; height: 100%; object-fit: cover; border-radius: 2.5rem 2.5rem 0 0; }
        
        /* Glassmorphism Badge */
        .glass-badge {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .productSwiper .swiper-pagination-fraction {
            bottom: unset !important;
            top: 15px !important;
            left: 50% !important;
            transform: translateX(-50%);
            width: auto !important;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            font-size: 9px;
            font-weight: 900;
            padding: 4px 12px;
            border-radius: 20px;
            z-index: 20;
        }
    </style>
    <link rel="stylesheet" href="public/css/theme-glass.css?v=20260417">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000);
    </script>
</head>
<body class="bg-gray-50 pb-20">
    
    <!-- Hero Header -->
    <div class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="pasar.php" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-black text-gray-800">Katalog Toko</h1>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[3rem] text-white shadow-2xl shadow-emerald-100">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center border border-white/20">
                        <i class="fas fa-store text-4xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black mb-1"><?= htmlspecialchars($penjual) ?></h2>
                        <div class="flex flex-wrap gap-3 mt-2 text-emerald-100 text-xs font-bold uppercase tracking-widest">
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-full"><i class="fas fa-box"></i> <?= count($products) ?> Produk</span>
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-full"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($profile['alamat'] ?? 'Warga RT') ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $profile['no_wa'] ?? $products[0]['no_wa'] ?? '') ?>" target="_blank" class="px-8 py-4 bg-white text-emerald-600 rounded-[1.5rem] font-black text-sm hover:scale-105 transition-all flex items-center gap-3">
                        <i class="fab fa-whatsapp text-lg"></i> Hubungi Toko
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-10">
        <?php if (empty($products)): ?>
            <div class="text-center py-32 bg-white rounded-[3rem] border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-store-slash text-3xl text-gray-200"></i>
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-2">Toko Belum Ada Stok</h3>
                <p class="text-gray-400">Silakan kembali lagi nanti untuk melihat update terbaru.</p>
                <a href="pasar.php" class="inline-block mt-8 text-emerald-600 font-bold hover:underline">Kembali ke Pasar</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-8">
                <?php foreach ($products as $p): 
                    $photos = [];
                    try { $photos = json_decode($p['foto'], true) ?: []; } catch(Exception $e) { if($p['foto']) $photos = [$p['foto']]; }
                    $mainPhoto = !empty($photos) ? $photos[0] : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400';
                ?>
                    <div class="product-card group bg-white relative overflow-hidden flex flex-col h-full">
                        <!-- Image Container -->
                        <div class="aspect-square relative flex-shrink-0">
                            <div class="swiper productSwiper">
                                <div class="swiper-wrapper">
                                    <?php if(empty($photos)): ?>
                                        <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400"></div>
                                    <?php else: ?>
                                        <?php foreach($photos as $ft): ?>
                                            <div class="swiper-slide"><img src="<?= htmlspecialchars($ft) ?>"></div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if(count($photos) > 1): ?>
                                    <div class="swiper-pagination"></div>
                                <?php endif; ?>
                            </div>
                            <div class="absolute top-4 left-4 z-20">
                                <span class="px-4 py-1.5 rounded-full bg-white/90 backdrop-blur-md text-[9px] font-black text-emerald-600 uppercase tracking-widest shadow-sm">
                                    <?= htmlspecialchars($p['kategori']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-sm md:text-md font-extrabold text-gray-800 line-clamp-2 mb-2"><?= htmlspecialchars($p['nama_produk']) ?></h3>
                            <div class="mt-auto">
                                <div class="text-emerald-600 font-black text-lg mb-4">
                                    Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                </div>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $p['no_wa']) ?>?text=Halo%20saya%20tertarik%20dengan%20<?= urlencode($p['nama_produk']) ?>" 
                                   target="_blank" 
                                   class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-[1.25rem] flex items-center justify-center gap-2 text-sm font-extrabold shadow-xl shadow-emerald-200 hover:shadow-emerald-300 hover:from-emerald-600 hover:to-teal-700 hover:-translate-y-1 transition-all duration-300 border border-emerald-400/30">
                                    <i class="fab fa-whatsapp text-lg"></i> Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div class="mt-16 flex justify-center items-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="toko.php?penjual=<?= urlencode($penjual) ?>&page=<?= $page - 1 ?>" class="w-12 h-12 rounded-[1.2rem] bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                <?php endif; ?>

                <?php 
                for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="toko.php?penjual=<?= urlencode($penjual) ?>&page=<?= $i ?>" class="w-12 h-12 rounded-[1.2rem] flex items-center justify-center text-sm font-black transition-all <?= $i == $page ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-100' : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="toko.php?penjual=<?= urlencode($penjual) ?>&page=<?= $page + 1 ?>" class="w-12 h-12 rounded-[1.2rem] bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Inisialisasi Multi Carousel Produk
        const productSwiper = new Swiper('.productSwiper', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                type: 'fraction',
            },
        });
    </script>
</body>
</html>
