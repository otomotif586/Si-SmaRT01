<?php
require_once 'config/database.php';

// Auto-create database table jika belum ada (Tanpa repot buka phpMyAdmin)
$pdo->exec("CREATE TABLE IF NOT EXISTS `pasar_produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text,
  `harga` decimal(15,2) NOT NULL DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `penjual_nama` varchar(100) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `kategori` varchar(50) DEFAULT 'Umum',
  `status` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Ambil data produk
$search = $_GET['q'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$query = "SELECT * FROM pasar_produk WHERE 1=1";
$params = [];
if($search) {
    $query .= " AND nama_produk LIKE ?";
    $params[] = "%$search%";
}
if($kategori && $kategori !== 'Terlaris') {
    $query .= " AND kategori = ?";
    $params[] = $kategori;
}

// Algoritma Terlaris: Urutkan berdasarkan jumlah klik beli terbanyak
if($kategori === 'Terlaris') {
    $query .= " ORDER BY klik_beli DESC, created_at DESC";
} else {
    $query .= " ORDER BY status DESC, created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data Slider Promosi dari Database
$stmtSlider = $pdo->query("SELECT * FROM pasar_slider ORDER BY urutan ASC");
$sliders = $stmtSlider->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper CSS untuk Slider Promosi -->
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: rgba(248, 250, 252, 0.85); }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Efek Latar Belakang Semi Transparan Menyesuaikan Tema Portal */
        .background-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image: url('<?= htmlspecialchars($bg_overlay) ?>');
            background-size: cover; background-position: center center; background-repeat: no-repeat;
            filter: grayscale(20%) blur(3px); opacity: 0.15;
        }

        /* Custom Swiper Pagination */
        .swiper-pagination-bullet-active { background-color: #10b981 !important; }

        /* Desain Kartu Produk Sesuai Permintaan */
        .product-card {
            --bg: #fff;
            --title-color: #fff;
            --title-color-hover: #1e293b;
            --text-color: #64748b;
            --button-color: #10b981;
            --button-color-hover: #059669;
            background: var(--bg);
            border-radius: 1.5rem;
            padding: 0.5rem;
            width: 100%;
            height: 320px; /* Tinggi dinamis responsif */
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.4s ease;
            border: 1px solid #f1f5f9;
        }

        @media (min-width: 768px) {
            .product-card { height: 380px; }
        }

        /* Glassmorphism bottom overlay */
        .product-card::before {
            content: "";
            position: absolute;
            width: calc(100% - 1rem);
            height: 45%;
            bottom: 0.5rem;
            left: 0.5rem;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            backdrop-filter: blur(2px);
            border-radius: 0 0 1rem 1rem;
            transition: transform 0.4s ease, opacity 0.4s ease;
            z-index: 1;
        }

        .product-card > img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 50% 50%;
            border-radius: 1rem;
            display: block;
            transition: height 0.4s ease, border-radius 0.4s ease, object-position 0.4s ease;
        }

        /* Teks Nama & Harga di Dalam Foto Utama */
        .product-card .img-overlay {
            position: absolute;
            bottom: 1.5rem;
            left: 1.25rem;
            right: 1.25rem;
            z-index: 2;
            transition: transform 0.4s ease, opacity 0.4s ease;
            pointer-events: none;
        }

        .product-card .img-overlay h2 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--title-color);
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .img-overlay .price {
            display: inline-block;
            margin-top: 0.25rem;
            font-size: 1.15rem;
            font-weight: 900;
            color: #34d399; /* Emerald 400 */
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }

        /* Bagian Informasi yang Muncul Saat Hover */
        .product-card > section {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 45%;
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            z-index: 3;
            opacity: 0;
            transform: translateY(20px);
            transition: transform 0.4s ease, opacity 0.4s ease;
            pointer-events: none;
        }

        .product-card > section p {
            font-size: 0.8rem;
            line-height: 1.4;
            color: var(--text-color);
            margin: 0 0 auto 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card > section .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 0.5rem;
        }

        .product-card .tag {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--button-color-hover);
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Tombol Beli (+ Plus to Checkmark Morphing) */
        .product-card .btn-buy {
            border: none;
            border-radius: 1.25rem 1.25rem 1.5rem 1.25rem;
            font-size: 0.8rem;
            font-weight: bold;
            padding: 0.6rem 1.2rem 0.6rem 2rem;
            background: var(--button-color);
            color: #fff;
            cursor: pointer;
            position: relative;
            transition: all 0.33s ease;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
            text-decoration: none;
            pointer-events: auto;
        }

        .product-card .btn-buy::before,
        .product-card .btn-buy::after {
            content: "";
            background: #fff;
            position: absolute;
            border-radius: 1rem;
            transition: all 0.25s ease-out;
        }

        .product-card .btn-buy::before { width: 0.75rem; height: 0.1rem; top: 50%; left: 0.9rem; }
        .product-card .btn-buy::after { width: 0.75rem; height: 0.1rem; top: 50%; left: 0.9rem; rotate: 90deg; }

        .product-card .btn-buy:hover { background: var(--button-color-hover); transform: scale(1.05); }
        
        .product-card .btn-buy.added { background: var(--button-color-hover); }
        .product-card .btn-buy.added::before { width: 0.4rem; top: 50%; left: 0.8rem; rotate: 45deg; translate: 0 2px; }
        .product-card .btn-buy.added::after { width: 0.7rem; top: 52%; left: 1.0rem; rotate: -45deg; translate: 0 -1px; }

        /* HOVER DYNAMICS */
        .product-card:hover {
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.15);
            border-color: #10b981;
        }
        .product-card:hover::before { transform: translateY(100%); opacity: 0; }
        .product-card:hover > img {
            height: 55%;
            object-position: 50% 20%;
            border-radius: 1rem 1rem 0 0;
        }
        .product-card:hover .img-overlay { transform: translateY(-20px); opacity: 0; }
        .product-card:hover > section { transform: translateY(0); opacity: 1; pointer-events: auto; }
    </style>
</head>
<body class="pb-24 selection:bg-emerald-200">
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
                    <a href="app.php" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-bold text-xs md:text-sm hover:bg-emerald-200 transition-colors flex items-center gap-2 shadow-sm">
                        <i class="fas fa-sign-in-alt"></i> <span class="hidden md:inline">Masuk (Login)</span>
                    </a>
                    <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-600 border border-emerald-100">
                        <i class="fas fa-shopping-bag"></i>
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

        <!-- Header Media Promosi Slider -->
        <div class="swiper promoSwiper mb-8 rounded-[1.5rem] overflow-hidden shadow-sm border border-slate-100">
            <div class="swiper-wrapper">
                <?php if(!empty($sliders)): ?>
                <?php foreach($sliders as $sl): ?>
                <div class="swiper-slide relative aspect-[2/1] md:aspect-[4/1] bg-slate-200 group cursor-pointer">
                    <img src="<?= htmlspecialchars($sl['image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-r from-<?= htmlspecialchars($sl['theme_color']) ?>-950/90 to-transparent flex items-center p-6 md:p-16">
                        <div class="text-white max-w-lg">
                            <span class="px-3 py-1.5 bg-<?= htmlspecialchars($sl['theme_color']) ?>-500 rounded-full text-[9px] font-bold uppercase tracking-widest mb-3 inline-block shadow-lg"><i class="fas <?= htmlspecialchars($sl['badge_icon']) ?> mr-1"></i> <?= htmlspecialchars($sl['badge_text']) ?></span>
                            <h2 class="text-2xl md:text-4xl font-extrabold mb-2 leading-tight"><?= htmlspecialchars($sl['title']) ?></h2>
                            <p class="text-sm md:text-base opacity-80 font-medium"><?= htmlspecialchars($sl['subtitle']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <!-- Dummy Slide 1 -->
                <div class="swiper-slide relative aspect-[2/1] md:aspect-[4/1] bg-slate-200 group cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=1200" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 to-transparent flex items-center p-6 md:p-16">
                        <div class="text-white max-w-lg">
                            <span class="px-3 py-1.5 bg-emerald-500 rounded-full text-[9px] font-bold uppercase tracking-widest mb-3 inline-block shadow-lg"><i class="fas fa-fire mr-1"></i> Promo Spesial</span>
                            <h2 class="text-2xl md:text-4xl font-extrabold mb-2 leading-tight">Diskon UMKM Warga</h2>
                            <p class="text-sm md:text-base opacity-80 font-medium">Dukung usaha tetangga, nikmati potongan harga khusus hari ini.</p>
                        </div>
                    </div>
                </div>
                <!-- Dummy Slide 2 -->
                <div class="swiper-slide relative aspect-[2/1] md:aspect-[4/1] bg-slate-200 group cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?q=80&w=1200" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-950/90 to-transparent flex items-center p-6 md:p-16">
                        <div class="text-white max-w-lg">
                            <span class="px-3 py-1.5 bg-orange-500 rounded-full text-[9px] font-bold uppercase tracking-widest mb-3 inline-block shadow-lg"><i class="fas fa-store mr-1"></i> Buka Toko</span>
                            <h2 class="text-2xl md:text-4xl font-extrabold mb-2 leading-tight">Jualan Makin Laris</h2>
                            <p class="text-sm md:text-base opacity-80 font-medium">Daftarkan usaha atau jasa Anda melalui aplikasi SmaRT sekarang juga.</p>
                        </div>
                    </div>
                </div>
                <!-- Dummy Slide 3 -->
                <div class="swiper-slide relative aspect-[2/1] md:aspect-[4/1] bg-slate-200 group cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1200" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-950/90 to-transparent flex items-center p-6 md:p-16">
                        <div class="text-white max-w-lg">
                            <span class="px-3 py-1.5 bg-blue-500 rounded-full text-[9px] font-bold uppercase tracking-widest mb-3 inline-block shadow-lg"><i class="fas fa-shopping-basket mr-1"></i> Belanja Mudah</span>
                            <h2 class="text-2xl md:text-4xl font-extrabold mb-2 leading-tight">Penuhi Kebutuhan Harian</h2>
                            <p class="text-sm md:text-base opacity-80 font-medium">Cari sayur, lauk, hingga jasa servis cukup dari rumah saja via Pasar Warga.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <?php if(empty($produk)): ?>
            <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-slate-200 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl">
                    <i class="fas fa-store-slash"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Belum ada produk</h3>
                <p class="text-slate-500 text-sm mt-2 font-medium">Coba cari kata kunci lain atau cek kembali nanti.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
                <?php foreach($produk as $p): 
                    // Format & Bersihkan nomor WA
                    $wa = preg_replace('/[^0-9]/', '', $p['no_wa']);
                    if(str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);
                    
                    $msg = urlencode("Halo {$p['penjual_nama']}, saya tertarik dengan *{$p['nama_produk']}* (Rp " . number_format($p['harga'],0,',','.') . ") yang ada di Portal Warga. Apakah masih tersedia?");
                    $foto = $p['foto'] ?: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
                ?>
                <!-- Product Card Premium -->
                <div class="product-card">
                    <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                    
                    <!-- Badge Status Tambahan Jika Habis -->
                    <?php if($p['status'] == 'Habis'): ?>
                        <div class="absolute top-4 left-4 z-[10] bg-red-500 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-md">Stok Habis</div>
                    <?php endif; ?>

                    <!-- Overlay Teks dalam Foto Utama -->
                    <div class="img-overlay">
                        <h2><?= htmlspecialchars($p['nama_produk']) ?></h2>
                        <span class="price">Rp <?= number_format($p['harga'],0,',','.') ?></span>
                    </div>

                    <!-- Bagian Detail yang Menggeser dari Bawah Saat Hover -->
                    <section>
                        <p><?= htmlspecialchars($p['deskripsi'] ?: 'Tidak ada deskripsi produk.') ?></p>
                        
                        <div class="card-actions">
                            <div class="flex flex-col gap-2">
                                <div><span class="tag"><i class="fas fa-tags text-[9px]"></i> <?= htmlspecialchars($p['kategori']) ?></span></div>
                                <span class="text-[0.7rem] font-bold text-slate-500 truncate max-w-[100px]"><i class="fas fa-store text-emerald-500 mr-1"></i> <?= htmlspecialchars($p['penjual_nama']) ?></span>
                            </div>
                            
                            <a href="https://wa.me/<?= $wa ?>?text=<?= $msg ?>" target="_blank" class="btn-buy" onclick="trackAlgoritmaBeli(<?= $p['id'] ?>); this.classList.add('added'); setTimeout(() => this.classList.remove('added'), 2000)">
                                Beli
                            </a>
                        </div>
                    </section>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Inisialisasi Slider Promo
        const swiper = new Swiper('.promoSwiper', {
            loop: true,
            autoplay: { delay: 4500, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
        });

        // Fungsi Algoritma Perekam Klik (Hit API Tanpa Reload)
        function trackAlgoritmaBeli(id) {
            const fd = new FormData(); fd.append('id', id);
            fetch('views/pages/track_beli.php', { method: 'POST', body: fd }).catch(e => console.log('Tracking failed', e));
        }
    </script>
</body>
</html>