<?php
// Load Database
require_once 'config/database.php';

// Ambil Data Pengaturan Web CMS
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
    $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $settingsData = [];
    $pdo->exec("CREATE TABLE IF NOT EXISTS `web_settings` (
      `setting_key` varchar(50) NOT NULL,
      `setting_value` text,
      PRIMARY KEY (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

$web_nama = $settingsData['web_nama'] ?? 'Pesona Kahuripan';
$web_title = $settingsData['web_title'] ?? 'Pesona Kahuripan - Hunian Asri & Modern';
$web_hero_title = $settingsData['web_hero_title'] ?? "Kampung Impian <br> <span class='text-gradient'>Kini Jadi Nyata.</span>";
$web_visi = $settingsData['web_visi'] ?? 'Nikmati harmoni pemandangan bukit, sawah, dan suasana religius pesantren. Kawasan mandiri di mana memiliki rumah asri bukan lagi sekadar angan.';
$web_hero_image = $settingsData['web_hero_image'] ?? 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&q=80&w=2000';
$web_logo = $settingsData['web_logo'] ?? '';
$web_favicon = $settingsData['web_favicon'] ?? '';

// Data Transparansi Keuangan
$web_transparansi_judul = $settingsData['web_transparansi_judul'] ?? 'Transparansi Keuangan Warga';
$web_transparansi_deskripsi = $settingsData['web_transparansi_deskripsi'] ?? 'Kami berkomitmen untuk selalu terbuka dalam pengelolaan dana iuran. Laporan kas dapat diakses dan diunduh di bawah ini.';
$web_transparansi_file = $settingsData['web_transparansi_file'] ?? '';

// Ambil Data Menu Dinamis
try {
    $stmtMenu = $pdo->query("SELECT * FROM web_menus WHERE status='Aktif' ORDER BY urutan ASC");
    $menus = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $menus = [];
    $pdo->exec("CREATE TABLE IF NOT EXISTS `web_menus` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nama_menu` varchar(100) NOT NULL,
      `url` varchar(255) NOT NULL,
      `urutan` int(11) DEFAULT 0,
      `status` enum('Aktif','Draft') DEFAULT 'Aktif',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Ambil Data Artikel/Blog Publik
try {
    $stmtBlog = $pdo->query("SELECT * FROM web_blogs WHERE status='Publish' ORDER BY created_at DESC LIMIT 3");
    $blogs = $stmtBlog->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $blogs = [];
    $pdo->exec("CREATE TABLE IF NOT EXISTS `web_blogs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `judul` varchar(255) NOT NULL,
      `konten` longtext,
      `status` enum('Publish','Draft') DEFAULT 'Publish',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Ambil Data Struktur Organisasi
try {
    $stmtPengurus = $pdo->query("SELECT * FROM web_pengurus ORDER BY urutan ASC, id ASC");
    $pengurus = $stmtPengurus->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pengurus = [];
    // Silently create table if visitor arrives before backend initializes it
    $pdo->exec("CREATE TABLE IF NOT EXISTS `web_pengurus` (`id` int(11) NOT NULL AUTO_INCREMENT,`nama` varchar(100) NOT NULL,`jabatan` varchar(100) NOT NULL,`foto` varchar(255) DEFAULT NULL,`urutan` int(11) DEFAULT 1,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
// Grouping per level/tingkat
$struktur = [];
foreach($pengurus as $p) {
    $struktur[$p['urutan']][] = $p;
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($web_title) ?> - Portal Warga & Organisasi RT</title>
    <?php if($web_favicon): ?>
    <link rel="icon" href="<?= $web_favicon ?>" type="image/x-icon">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Menggunakan Inter untuk UI dan Plus Jakarta Sans untuk Heading -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream-bg: #fdfaf3;
            --emerald-primary: #059669;
            --text-main: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream-bg);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Tekstur Grain Halus */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.02;
            pointer-events: none;
            z-index: 50;
        }

        /* Glassmorphism Refined */
        .glass {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 10px 40px -15px rgba(5, 150, 105, 0.08);
        }

        .glass-nav {
            background: rgba(253, 250, 243, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(5, 150, 105, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animasi Blobs */
        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
            animation: morph 20s ease-in-out infinite;
        }

        @keyframes morph {
            0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; transform: translate(0, 0) rotate(0deg); }
            34% { border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%; transform: translate(10%, 5%) rotate(90deg); }
            67% { border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%; transform: translate(-5%, 10%) rotate(180deg); }
        }

        .float { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .card-glow:hover {
            box-shadow: 0 30px 60px -15px rgba(5, 150, 105, 0.15);
            border-color: rgba(5, 150, 105, 0.3);
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-8px);
        }

        #mobile-menu-overlay {
            transition: transform 0.6s cubic-bezier(0.85, 0, 0.15, 1);
            transform: translateX(100%);
        }

        #mobile-menu-overlay.open {
            transform: translateX(0);
        }
    </style>
</head>
<body>

    <!-- Latar Belakang Gambar Alam Semi-Transparan -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=2000" class="absolute inset-0 w-full h-full object-cover opacity-[0.06] mix-blend-multiply" alt="Latar Belakang">
        <div class="absolute inset-0 bg-[#fdfaf3]/60 backdrop-blur-[1px]"></div>
    </div>

    <!-- Animasi Elemen Latar Belakang -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="blob top-[-15%] left-[-10%]"></div>
        <div class="blob bottom-[-10%] right-[-10%]" style="animation-delay: -5s; background: radial-gradient(circle, rgba(252, 211, 77, 0.1) 0%, rgba(255, 255, 255, 0) 70%);"></div>
        
        <!-- Elemen Daun Halus -->
        <svg class="absolute top-60 right-20 opacity-10 float" style="width: 120px; animation-duration: 10s;" viewBox="0 0 24 24" fill="#059669">
            <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z" />
        </svg>
    </div>

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-[100] transition-all duration-700 py-6">
        <div class="container mx-auto px-6 md:px-12 flex justify-between items-center">
            <div class="flex items-center space-x-4 group cursor-pointer">
                <?php if($web_logo): ?>
                    <img src="<?= $web_logo ?>" class="w-12 h-12 object-contain rounded-2xl shadow-xl shadow-emerald-200 group-hover:rotate-6 transition-transform bg-white" alt="Logo">
                <?php else: ?>
                    <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-200 group-hover:rotate-6 transition-transform">
                        <i class="fas fa-users-rectangle text-white text-xl"></i>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col text-left">
                    <span class="text-xl font-extrabold tracking-tight uppercase leading-none text-emerald-950"><?= htmlspecialchars($web_nama) ?></span>
                    <span class="text-[9px] tracking-[0.4em] uppercase opacity-40 font-bold mt-1">Sistem Informasi Warga</span>
                </div>
            </div>
            
            <!-- Integrasi Menu CMS Dinamis -->
            <div class="hidden lg:flex items-center space-x-12 text-[10px] font-bold tracking-[0.2em] uppercase text-emerald-900/60">
                <?php if(empty($menus)): ?>
                    <a href="#kawasan" class="hover:text-emerald-600 transition-all">Kawasan</a>
                    <a href="#visimisi" class="hover:text-emerald-600 transition-all">Visi Misi</a>
                    <a href="#organisasi" class="hover:text-emerald-600 transition-all">Organisasi</a>
                    <a href="#layanan" class="hover:text-emerald-600 transition-all">Layanan</a>
                    <a href="#wisata" class="hover:text-emerald-600 transition-all">Wisata</a>
                <?php else: ?>
                    <?php foreach($menus as $m): ?>
                        <a href="<?= htmlspecialchars($m['url']) ?>" class="hover:text-emerald-600 transition-all"><?= htmlspecialchars($m['nama_menu']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- TOMBOL MASUK SISTEM SI-SMART -->
                <a href="app.php" class="px-8 py-3.5 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 hover:shadow-2xl hover:shadow-emerald-100 transition-all shadow-lg font-bold">
                    PORTAL WARGA
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button id="menu-btn" class="lg:hidden w-12 h-12 flex items-center justify-center glass rounded-2xl text-emerald-700 shadow-sm transition-transform active:scale-90">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>
    </nav>

    <div id="mobile-menu-overlay" class="fixed inset-0 bg-[#fdfaf3] z-[150] hidden flex flex-col items-center justify-center space-y-12 text-3xl font-black uppercase tracking-widest text-emerald-950">
        <button id="close-btn" class="absolute top-8 right-8 w-14 h-14 glass rounded-3xl text-emerald-600 flex items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
        <?php if(empty($menus)): ?>
            <a href="#kawasan" class="mobile-link">Kawasan</a>
            <a href="#visimisi" class="mobile-link">Visi Misi</a>
            <a href="#organisasi" class="mobile-link">Organisasi</a>
            <a href="#layanan" class="mobile-link">Layanan</a>
            <a href="#wisata" class="mobile-link">Wisata</a>
        <?php else: ?>
            <?php foreach($menus as $m): ?>
                <a href="<?= htmlspecialchars($m['url']) ?>" class="mobile-link"><?= htmlspecialchars($m['nama_menu']) ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="app.php" class="mt-4 px-12 py-6 bg-emerald-600 text-white rounded-[2.5rem] shadow-2xl text-xl font-bold">Akses Warga</a>
    </div>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-32 lg:pt-20 overflow-hidden">
        <div class="container mx-auto px-6 md:px-12 relative z-10 text-left">
            <div class="grid lg:grid-cols-2 items-center gap-16 lg:gap-24">
                <div class="space-y-10">
                    <div class="inline-flex items-center space-x-3 px-5 py-3 rounded-full bg-emerald-600/5 border border-emerald-600/10 text-emerald-700 text-[10px] font-bold tracking-[0.2em] uppercase glass">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>RT 001 Go-Digital</span>
                    </div>
                    
                    <h1 class="text-6xl md:text-7xl lg:text-[5.5rem] font-extrabold leading-[1] text-emerald-950 tracking-tight">
                        <?= $web_hero_title ?>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-emerald-900/50 leading-relaxed max-w-xl font-medium">
                        <?= nl2br(htmlspecialchars($web_visi)) ?>
                    </p>
                    
                    <div class="flex flex-wrap gap-6 pt-4">
                        <a href="#organisasi" class="px-12 py-6 bg-emerald-600 text-white font-bold rounded-[2rem] flex items-center space-x-4 hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-100">
                            <span>PENGURUS RT</span>
                            <i class="fas fa-arrow-right text-xs opacity-50"></i>
                        </a>
                        <a href="https://www.google.com/maps/place/Bimbel+Become/@-6.4617173,106.9727219,3a,73.9y,173.83h,88.65t/data=!3m7!1e1!3m5!1s0-jsU8IuF6zRD2dAo4a4pQ!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.3543519995511133%26panoid%3D0-jsU8IuF6zRD2dAo4a4pQ%26yaw%3D173.82917027237332!7i16384!8i8192!4m6!3m5!1s0x2e69bf52d8d30d3b:0x94ee6f0e357a0db5!8m2!3d-6.4600501!4d106.9744559!16s%2Fg%2F11y2dtt465?entry=ttu&g_ep=EgoyMDI2MDQwNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="px-12 py-6 glass text-emerald-900 font-bold rounded-[2rem] hover:bg-white transition-all flex items-center space-x-4 cursor-pointer">
                            <i class="fas fa-play-circle text-emerald-600 text-xl"></i>
                            <span>TUR KAWASAN</span>
                        </a>
                    </div>

                </div>
                
                <!-- Hero Image Decor -->
                <div class="relative mt-12 lg:mt-0 float w-full">
                    <div class="absolute -inset-10 bg-emerald-500/10 blur-[120px] rounded-full animate-pulse"></div>
                    <img 
                        src="<?= $web_hero_image ?>" 
                        alt="View Pesona" 
                        class="relative z-10 rounded-[3rem] lg:rounded-[5rem] border-[12px] lg:border-[16px] border-white/60 shadow-2xl object-cover h-[400px] lg:h-[650px] w-full"
                        loading="lazy"
                        onerror="this.src='https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&q=80&w=1200'"
                    >
                    <div class="absolute -bottom-6 left-4 right-4 lg:right-auto lg:-bottom-10 lg:-left-10 glass p-6 lg:p-10 rounded-[2.5rem] lg:rounded-[3.5rem] z-20 shadow-2xl max-w-[320px] mx-auto lg:mx-0">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-emerald-950">
                                <i class="fas fa-quote-left text-xs"></i>
                            </div>
                            <span class="font-bold text-sm text-emerald-900">Suasana Warga</span>
                        </div>
                        <p class="text-sm text-emerald-900/60 leading-relaxed font-medium italic">"View bukit & sawah di sini luar biasa, ekonomi warganya juga hidup sekali."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="kawasan" class="py-32 relative">
        <div class="container mx-auto px-6 md:px-12 text-center mb-20">
            <h2 class="text-4xl font-extrabold text-emerald-950 tracking-tight reveal">Kawasan Mandiri Terpadu</h2>
        </div>
        <div class="container mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="glass p-12 rounded-[4rem] card-glow reveal">
                    <div class="w-20 h-20 bg-emerald-600/10 rounded-3xl flex items-center justify-center text-emerald-600 mb-8 text-4xl shadow-inner border border-emerald-100">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-emerald-950">Bukit & Sawah</h3>
                    <p class="text-emerald-900/50 text-sm leading-relaxed font-medium">Pemandangan alam murni di kanan-kiri yang menyejukkan mata setiap hari.</p>
                </div>
                <div class="glass p-12 rounded-[4rem] card-glow reveal bg-emerald-600 shadow-2xl shadow-emerald-200">
                    <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center text-white mb-8 text-4xl border border-white/10 shadow-inner">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Ekonomi Warga</h3>
                    <p class="text-white/60 text-sm leading-relaxed font-medium">Warga produktif yang aktif berwirausaha secara lengkap di lingkungan RT.</p>
                </div>
                <div class="glass p-12 rounded-[4rem] card-glow reveal">
                    <div class="w-20 h-20 bg-emerald-600/10 rounded-3xl flex items-center justify-center text-emerald-600 mb-8 text-4xl shadow-inner border border-emerald-100">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-emerald-950">Go Digital</h3>
                    <p class="text-emerald-900/50 text-sm leading-relaxed font-medium">Sistem administrasi warga yang modern, transparan, dan mudah diakses kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi Section -->
    <section id="visimisi" class="py-32 bg-emerald-600/5">
        <div class="container mx-auto px-6 md:px-12">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="reveal">
                    <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4 text-left">Arah & Tujuan</h2>
                    <h3 class="text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight mb-8 text-left">Visi & Misi <br> <span class="text-emerald-500">Kawasan Kita.</span></h3>
                    <div class="glass p-10 rounded-[3rem] border-l-8 border-emerald-600 text-left card-glow">
                        <h4 class="text-xl font-extrabold text-emerald-900 mb-4 tracking-tight">VISI KAMI</h4>
                        <p class="text-emerald-950/70 font-medium leading-relaxed italic">
                            "<?= htmlspecialchars($web_visi) ?>"
                        </p>
                    </div>
                </div>
                <div class="space-y-6 reveal text-left">
                    <h4 class="text-xl font-extrabold text-emerald-900 tracking-tight mb-6 uppercase">MISI KAMI</h4>
                    
                    <?php if(!empty(trim($settingsData['web_misi'] ?? ''))): ?>
                        <!-- Dinamis Misi dari CMS -->
                        <div class="glass p-8 rounded-[2.5rem] card-glow">
                            <div class="text-sm text-emerald-900/70 font-medium leading-relaxed space-y-3">
                                <?= nl2br(htmlspecialchars($settingsData['web_misi'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Default Fallback Design -->
                        <div class="space-y-4 text-left">
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Pelestarian Alam</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Menjaga keasrian view bukit dan kebersihan lingkungan sawah.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Ekonomi Mandiri</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Mendukung dan memfasilitasi wirausaha warga agar kawasan semakin maju.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Kerukunan Warga</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Membangun silaturahmi yang erat dan suasana pesantren yang religius.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-laptop-code"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Layanan Digital</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Memberikan pelayanan administrasi warga yang cepat dan transparan.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Penting -->
    <section class="py-32 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="glass p-12 md:p-16 rounded-[4rem] relative overflow-hidden bg-gradient-to-br from-white/60 to-emerald-50/40 text-left reveal">
                <div class="absolute top-0 right-0 p-12 opacity-5 hidden lg:block">
                    <i class="fas fa-circle-info text-[10rem] text-emerald-900"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-3xl font-extrabold text-emerald-950 mb-10 tracking-tight">Informasi Penting Warga</h3>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
                        <div class="space-y-3">
                            <span class="text-emerald-600 font-black text-[10px] tracking-widest uppercase">Keamanan</span>
                            <p class="font-bold text-emerald-950">Lapor Tamu 1x24 Jam</p>
                            <p class="text-xs text-emerald-900/40 leading-relaxed font-medium">Wajib bagi tamu yang menginap demi keamanan bersama.</p>
                        </div>
                        <div class="space-y-3">
                            <span class="text-emerald-600 font-black text-[10px] tracking-widest uppercase">Kebersihan</span>
                            <p class="font-bold text-emerald-950">Jadwal Sampah</p>
                            <p class="text-xs text-emerald-900/40 leading-relaxed font-medium">Pengangkutan dilakukan setiap hari Selasa dan Jumat pagi.</p>
                        </div>
                        <div class="space-y-3">
                            <span class="text-emerald-600 font-black text-[10px] tracking-widest uppercase">Iuran</span>
                            <p class="font-bold text-emerald-950">Batas Pembayaran</p>
                            <p class="text-xs text-emerald-900/40 leading-relaxed font-medium">Setiap tanggal 10 tiap bulannya melalui bendahara RT.</p>
                        </div>
                        <div class="space-y-3">
                            <span class="text-emerald-600 font-black text-[10px] tracking-widest uppercase">Kontak</span>
                            <p class="font-bold text-emerald-950">Hotline Darurat</p>
                            <p class="text-xs text-emerald-900/40 leading-relaxed font-medium">Hubungi Satgas Keamanan di: 0812-3456-7890 (24 Jam).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STRUKTUR ORGANISASI -->
    <?php if(!empty($pengurus)): ?>
    <section id="organisasi" class="py-32 bg-white/40 relative">
        <div class="container mx-auto px-6 md:px-12 text-center mb-20 reveal">
            <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4">Pengurus Lingkungan</h2>
            <h3 class="text-5xl font-extrabold text-emerald-950 tracking-tight">Struktur Organisasi</h3>
        </div>

        <div class="container mx-auto px-6 md:px-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php foreach($pengurus as $index => $a): ?>
            <div class="glass p-10 rounded-[4rem] card-glow reveal flex flex-col items-center text-center group transition-all duration-700" style="transition-delay: <?= ($index % 4) * 0.1 ?>s;">
                <div class="w-32 h-32 rounded-[2.5rem] bg-emerald-50 border-2 border-emerald-100 flex items-center justify-center mb-8 group-hover:bg-emerald-600 transition-all shadow-inner overflow-hidden">
                    <?php if($a['foto']): ?>
                        <img src="<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['nama']) ?>" class="w-full h-full object-cover">
                    <?php else: 
                        $icon = 'fa-user-tie'; // Default
                        $jab_lower = strtolower($a['jabatan']);
                        if(strpos($jab_lower, 'sekretaris') !== false) $icon = 'fa-file-signature';
                        elseif(strpos($jab_lower, 'bendahara') !== false) $icon = 'fa-coins';
                        elseif(strpos($jab_lower, 'keamanan') !== false || strpos($jab_lower, 'satgas') !== false) $icon = 'fa-user-shield';
                    ?>
                        <i class="fas <?= $icon ?> text-5xl text-emerald-600 group-hover:text-white transition-colors"></i>
                    <?php endif; ?>
                </div>
                <h4 class="text-xl font-bold text-emerald-900"><?= htmlspecialchars($a['jabatan']) ?></h4>
                <span class="text-emerald-600 text-xs font-bold mt-2"><?= htmlspecialchars($a['nama']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Layanan Digital Section & WARTA -->
    <section id="layanan" class="py-32 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row gap-20 items-center">
                <div class="flex-1 space-y-12 reveal text-left">
                    <h2 class="text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight">Layanan Warga <br><span class="text-emerald-600">Digital RT.</span></h2>
                    <p class="text-lg text-emerald-900/50 font-medium">Pengurusan surat pengantar, pembayaran iuran, hingga lapor keluhan kini lebih transparan dan cepat lewat portal mandiri.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="glass p-8 rounded-3xl hover:bg-white transition-all cursor-pointer group shadow-sm card-glow">
                            <i class="fas fa-envelope-open-text text-emerald-600 text-2xl mb-4 block group-hover:scale-110 transition-transform"></i>
                            <h5 class="font-bold text-sm text-emerald-950 tracking-tight uppercase">Surat RT</h5>
                        </div>
                        <div class="glass p-8 rounded-3xl hover:bg-white transition-all cursor-pointer group shadow-sm card-glow">
                            <i class="fas fa-receipt text-emerald-600 text-2xl mb-4 block group-hover:scale-110 transition-transform"></i>
                            <h5 class="font-bold text-sm text-emerald-950 tracking-tight uppercase">Iuran</h5>
                        </div>
                        <div class="glass p-8 rounded-3xl hover:bg-white transition-all cursor-pointer group shadow-sm card-glow">
                            <i class="fas fa-bullhorn text-emerald-600 text-2xl mb-4 block group-hover:scale-110 transition-transform"></i>
                            <h5 class="font-bold text-sm text-emerald-950 tracking-tight uppercase">Laporan</h5>
                        </div>
                        <div class="glass p-8 rounded-3xl hover:bg-white transition-all cursor-pointer group shadow-sm card-glow">
                            <i class="fas fa-circle-info text-emerald-600 text-2xl mb-4 block group-hover:scale-110 transition-transform"></i>
                            <h5 class="font-bold text-sm text-emerald-950 tracking-tight uppercase">Warta</h5>
                        </div>
                    </div>
                </div>

                <div class="flex-1 w-full reveal">
                    <div class="glass p-12 rounded-[4.5rem] bg-white/50 shadow-2xl relative overflow-hidden text-left">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-10 -mt-10"></div>
                        <h4 class="text-2xl font-extrabold text-emerald-950 mb-10 border-b-4 border-emerald-100 pb-4 inline-block tracking-tight">Warta Terbaru</h4>
                        <div class="space-y-10">
                            <?php if(!empty($blogs)): ?>
                                <?php foreach($blogs as $index => $b): 
                                    $tgl = date('d', strtotime($b['created_at']));
                                    $bln = date('M', strtotime($b['created_at']));
                                    $bgClass = $index % 2 == 0 ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-600';
                                ?>
                                <div class="flex gap-8 group cursor-pointer items-center">
                                    <div class="w-20 h-20 <?= $bgClass ?> rounded-[1.5rem] flex-shrink-0 flex flex-col items-center justify-center font-bold group-hover:rotate-3 transition-transform shadow-lg shadow-emerald-100">
                                        <span class="text-2xl leading-none tracking-tight"><?= $tgl ?></span>
                                        <span class="text-[9px] uppercase tracking-widest mt-1"><?= $bln ?></span>
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-lg text-emerald-950 group-hover:text-emerald-600 transition-colors tracking-tight line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h6>
                                        <p class="text-sm text-emerald-900/50 mt-1 font-medium italic leading-relaxed line-clamp-2"><?= strip_tags($b['konten']) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-emerald-900/50 italic">Belum ada pengumuman terbaru.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR TRANSPARANSI KEUANGAN PUBLIK -->
    <section id="transparansi" class="py-32 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="glass p-12 md:p-20 rounded-[4rem] card-glow reveal relative overflow-hidden bg-white/60">
                <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                    <i class="fas fa-chart-pie text-9xl text-emerald-900"></i>
                </div>
                <div class="relative z-10 max-w-3xl">
                    <div class="inline-flex items-center space-x-3 px-5 py-3 rounded-full bg-emerald-600/10 border border-emerald-600/20 text-emerald-700 text-[10px] font-bold tracking-[0.2em] uppercase mb-8">
                        <i class="fas fa-shield-alt"></i><span>Akuntabel & Terbuka</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-emerald-950 mb-6 tracking-tight"><?= htmlspecialchars($web_transparansi_judul) ?></h2>
                    <p class="text-emerald-900/60 text-lg leading-relaxed mb-10 font-medium"><?= nl2br(htmlspecialchars($web_transparansi_deskripsi)) ?></p>
                    <?php if($web_transparansi_file): ?>
                    <a href="<?= htmlspecialchars($web_transparansi_file) ?>" target="_blank" class="inline-flex items-center space-x-4 px-10 py-5 bg-emerald-600 text-white font-bold rounded-[2rem] hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-100">
                        <i class="fas fa-file-pdf text-xl"></i><span>Lihat Dokumen Laporan</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Destinasi Wisata -->
    <section id="wisata" class="py-32">
        <div class="container mx-auto px-6 md:px-12 text-center reveal mb-20">
            <h2 class="text-4xl font-extrabold text-emerald-950 tracking-tight">Destinasi Wisata Alam Sekitar</h2>
            <p class="text-emerald-900/40 mt-4 font-medium italic underline decoration-emerald-200 decoration-4 underline-offset-4 tracking-tight">Rekreasi alam yang menyejukkan jiwa.</p>
        </div>
        <div class="container mx-auto px-6 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="group relative h-[500px] overflow-hidden rounded-[4rem] reveal shadow-2xl">
                <img src="https://images.unsplash.com/photo-1751945142122-08edb4107d3e?q=80&w=816&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-1000 opacity-60" alt="Mata Air Sodong" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/10 to-transparent"></div>
                <div class="absolute bottom-12 left-12 text-white text-left">
                    <span class="text-[10px] font-bold tracking-[0.4em] uppercase opacity-70 mb-2 block">Ekologi</span>
                    <h4 class="text-4xl font-extrabold tracking-tight">Mata Air Sodong</h4>
                    <p class="text-white/70 mt-3 text-sm max-w-sm font-medium leading-relaxed">Mata air jernih pegunungan yang melegenda dekat kawasan Pesona.</p>
                </div>
            </div>
            <div class="group relative h-[500px] overflow-hidden rounded-[4rem] reveal shadow-2xl">
                <img src="https://plus.unsplash.com/premium_photo-1669058431888-8c792a5a762d?q=80&w=871&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-1000 opacity-60" alt="Goa Lalay Pool" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/10 to-transparent"></div>
                <div class="absolute bottom-12 left-12 text-white text-left">
                    <span class="text-[10px] font-bold tracking-[0.4em] uppercase opacity-70 mb-2 block">Rekreasi</span>
                    <h4 class="text-4xl font-extrabold tracking-tight">Goa Lalay Pool</h4>
                    <p class="text-white/70 mt-3 text-sm max-w-sm font-medium leading-relaxed">Kolam renang alam unik untuk liburan keluarga di akhir pekan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-32 bg-emerald-950 text-white rounded-t-[5rem] mt-20 relative overflow-hidden">
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="w-20 h-20 bg-white/10 rounded-[2.5rem] flex items-center justify-center mx-auto mb-10 backdrop-blur-xl border border-white/10 shadow-lg">
                <i class="fas fa-heart text-emerald-400 text-3xl animate-pulse"></i>
            </div>
            
            <h2 class="text-5xl md:text-6xl font-extrabold tracking-tighter mb-6 uppercase" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                We Love You <br> <?= htmlspecialchars($web_nama) ?>
            </h2>
            <p class="text-emerald-400 text-lg md:text-xl font-bold tracking-tight mb-16 italic">
                "Menyambung silaturahmi untuk tetap bersinergi"
            </p>
            
            <div class="grid grid-cols-3 gap-8 max-w-4xl mx-auto mb-24 opacity-40">
                <div class="flex flex-col items-center space-y-4">
                    <i class="fas fa-leaf text-2xl"></i>
                    <span class="text-[9px] font-black tracking-[0.5em] uppercase">Alam</span>
                </div>
                <div class="flex flex-col items-center space-y-4">
                    <i class="fas fa-people-group text-2xl"></i>
                    <span class="text-[9px] font-black tracking-[0.5em] uppercase">Warga</span>
                </div>
                <div class="flex flex-col items-center space-y-4">
                    <i class="fas fa-hand-holding-heart text-2xl"></i>
                    <span class="text-[9px] font-black tracking-[0.5em] uppercase">Sinergi</span>
                </div>
            </div>
            
            <p class="text-white/20 text-[9px] tracking-[0.8em] font-black uppercase">
                &copy; 2026 Portal Warga RT 001 • Pesona Kahuripan Development
            </p>
        </div>
    </footer>

    <script>
        // Navbar Scroll Logic
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 80) {
                nav.classList.add('glass-nav', 'py-4', 'shadow-2xl');
                nav.classList.remove('py-8');
            } else {
                nav.classList.remove('glass-nav', 'py-4', 'shadow-2xl');
                nav.classList.add('py-8');
            }
        });

        // Mobile Menu Logic
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const overlay = document.getElementById('mobile-menu-overlay');
        const links = document.querySelectorAll('.mobile-link');

        menuBtn.addEventListener('click', () => {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('open'), 10);
            document.body.style.overflow = 'hidden';
        });

        const closeMenu = () => {
            overlay.classList.remove('open');
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 600);
        };

        closeBtn.addEventListener('click', closeMenu);
        links.forEach(link => link.addEventListener('click', closeMenu));

        // Smooth Reveal Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>