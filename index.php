<?php
// Load Database
require_once 'config/database.php';

// Ambil Data Pengaturan Web CMS
$stmt = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
$settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

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
$stmtMenu = $pdo->query("SELECT * FROM web_menus WHERE status='Aktif' ORDER BY urutan ASC");
$menus = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);

// Ambil Data Artikel/Blog Publik
$stmtBlog = $pdo->query("SELECT * FROM web_blogs WHERE status='Publish' ORDER BY created_at DESC LIMIT 3");
$blogs = $stmtBlog->fetchAll(PDO::FETCH_ASSOC);

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
    <title><?= htmlspecialchars($web_title) ?></title>
    <?php if($web_favicon): ?>
    <link rel="icon" href="<?= $web_favicon ?>" type="image/x-icon">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #041a14;
            color: #ecfdf5;
            overflow-x: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-nav {
            background: rgba(4, 26, 20, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .text-gradient {
            background: linear-gradient(to right, #6ee7b7, #d1fae5, #99f6e4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes slow-zoom {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .animate-slow-zoom {
            animation: slow-zoom 30s ease-in-out infinite;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .card-glow:hover {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-500 py-6">
        <div class="container mx-auto px-6 md:px-12 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <?php if($web_logo): ?>
                    <img src="<?= $web_logo ?>" class="w-10 h-10 object-contain rounded-xl bg-white/10 p-1 shadow-lg" alt="Logo">
                <?php else: ?>
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-mountain text-white"></i>
                    </div>
                <?php endif; ?>
                <span class="text-xl font-bold tracking-tighter uppercase"><?= htmlspecialchars($web_nama) ?></span>
            </div>
            
            <!-- Integrasi Menu CMS Dinamis -->
            <div class="hidden md:flex items-center space-x-8 text-[11px] font-bold tracking-[0.2em] uppercase opacity-90">
                <?php if(empty($menus)): ?>
                    <a href="#kawasan" class="hover:text-emerald-400 transition-colors">Kawasan</a>
                    <a href="#fasilitas" class="hover:text-emerald-400 transition-colors">Fasilitas</a>
                    <a href="#wisata" class="hover:text-emerald-400 transition-colors">Wisata</a>
                <?php else: ?>
                    <?php foreach($menus as $m): ?>
                        <a href="<?= htmlspecialchars($m['url']) ?>" class="hover:text-emerald-400 transition-colors"><?= htmlspecialchars($m['nama_menu']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- TOMBOL MASUK SISTEM SI-SMART -->
                <a href="app.php" class="px-6 py-2.5 bg-emerald-500 text-emerald-950 rounded-full hover:bg-emerald-400 transition-all duration-300 font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Portal SmaRT
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img 
                src="<?= $web_hero_image ?>" 
                alt="Hero Cover" 
                class="w-full h-full object-cover opacity-20 animate-slow-zoom"
                onerror="this.src='https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&q=80&w=2000'"
            >
            <div class="absolute inset-0 bg-gradient-to-b from-[#041a14] via-transparent to-[#041a14]"></div>
        </div>

        <div class="container mx-auto px-6 md:px-12 relative z-10">
            <div class="max-w-4xl">
                <div class="inline-flex items-center space-x-3 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold tracking-[0.2em] uppercase mb-8">
                    <i class="fas fa-bolt"></i>
                    <span>Hunian Subsidi Berkualitas Pemerintah</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold leading-[1.1] mb-8">
                    <?= $web_hero_title ?> <!-- Output langsung agar format HTML gradient bisa terbaca -->
                </h1>
                
                <p class="text-lg md:text-xl text-emerald-100/60 leading-relaxed mb-10 max-w-2xl font-light">
                    <?= nl2br(htmlspecialchars($web_visi)) ?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5">
                    <!-- TOMBOL MASUK SISTEM SI-SMART UTAMA -->
                    <a href="app.php" class="px-10 py-5 bg-emerald-500 text-emerald-950 font-bold rounded-2xl flex items-center justify-center space-x-3 hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20">
                        <span>Masuk Sistem Si-SmaRT</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="https://www.google.com/maps/place/Bimbel+Become/@-6.4617173,106.9727219,3a,73.9y,173.83h,88.65t/data=!3m7!1e1!3m5!1s0-jsU8IuF6zRD2dAo4a4pQ!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.3543519995511133%26panoid%3D0-jsU8IuF6zRD2dAo4a4pQ%26yaw%3D173.82917027237332!7i16384!8i8192!4m6!3m5!1s0x2e69bf52d8d30d3b:0x94ee6f0e357a0db5!8m2!3d-6.4600501!4d106.9744559!16s%2Fg%2F11y2dtt465?entry=ttu&g_ep=EgoyMDI2MDQwNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="px-10 py-5 glass font-bold rounded-2xl hover:bg-white/10 transition-all flex items-center justify-center space-x-3 text-center">
                        <span>Tur Virtual 360°</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Kawasan, Fasilitas, dan Wisata tetap utuh seperti desain asli -->
    <section id="kawasan" class="py-24 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass p-8 rounded-[2rem] card-glow reveal">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 mb-6 text-2xl"><i class="fas fa-mountain"></i></div>
                    <h3 class="text-2xl font-bold mb-3">View Bukit</h3>
                    <p class="text-emerald-100/40 text-sm leading-relaxed">Pemandangan hijau yang membentang luas di sisi kanan dan kiri kawasan perumahan.</p>
                </div>
                <div class="glass p-8 rounded-[2rem] card-glow reveal border-emerald-500/30 bg-emerald-500/5">
                    <div class="w-14 h-14 bg-emerald-500/30 rounded-2xl flex items-center justify-center text-emerald-300 mb-6 text-2xl"><i class="fas fa-mosque"></i></div>
                    <h3 class="text-2xl font-bold mb-3">Religius</h3>
                    <p class="text-emerald-100/40 text-sm leading-relaxed">Suasana tenang dan damai dengan lingkungan yang dekat dengan area pesantren.</p>
                </div>
                <div class="glass p-8 rounded-[2rem] card-glow reveal">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 mb-6 text-2xl"><i class="fas fa-store"></i></div>
                    <h3 class="text-2xl font-bold mb-3">Wirausaha</h3>
                    <p class="text-emerald-100/40 text-sm leading-relaxed">Warga yang berboyong usaha mandiri membuat kawasan ini menjadi lengkap dan hidup.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- STRUKTUR ORGANISASI -->
    <?php if(!empty($struktur)): ?>
    <section id="struktur" class="py-24 relative bg-[#03100c]">
        <div class="container mx-auto px-6 md:px-12">
            <div class="text-center mb-20 reveal">
                <div class="inline-flex items-center space-x-3 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold tracking-[0.2em] uppercase mb-4">
                    <i class="fas fa-users"></i><span>Susunan Pengurus</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Struktur Organisasi</h2>
                <p class="text-emerald-100/60 text-lg max-w-2xl mx-auto font-light">Jajaran pengurus yang melayani dan mengayomi warga dengan sepenuh hati demi lingkungan yang lebih baik.</p>
            </div>
            
            <div class="flex flex-col items-center gap-12 relative">
                <!-- Garis Vertikal Tengah (Tulang Punggung Tree) -->
                <div class="absolute left-1/2 top-10 bottom-10 w-[2px] bg-gradient-to-b from-emerald-500/50 via-emerald-500/20 to-transparent -translate-x-1/2 z-0 hidden md:block"></div>
                
                <?php foreach($struktur as $tingkat => $anggota): ?>
                <div class="flex flex-wrap justify-center gap-6 relative z-10 w-full reveal">
                    <?php foreach($anggota as $a): ?>
                    <div class="glass p-8 rounded-[2rem] card-glow flex flex-col items-center text-center w-full sm:w-[280px] bg-[#041a14]/90 backdrop-blur-xl border-emerald-500/20 hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-32 h-32 rounded-full overflow-hidden mb-6 border-4 border-emerald-500/30 p-1 bg-emerald-500/10 shadow-[0_0_30px_rgba(16,185,129,0.2)]">
                            <?php if($a['foto']): ?>
                                <img src="<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['nama']) ?>" class="w-full h-full object-cover rounded-full">
                            <?php else: ?>
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-4xl font-bold">
                                    <?= strtoupper(substr($a['nama'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="text-xl font-bold mb-2 text-white"><?= htmlspecialchars($a['nama']) ?></h4>
                        <p class="text-emerald-400 text-sm font-bold tracking-widest uppercase bg-emerald-500/10 px-4 py-1.5 rounded-full inline-block"><?= htmlspecialchars($a['jabatan']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FITUR BARU: TRANSPARANSI KEUANGAN PUBLIK -->
    <section id="transparansi" class="py-24 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="glass p-10 md:p-16 rounded-[3rem] card-glow reveal relative overflow-hidden border-emerald-500/30 bg-emerald-500/5">
                <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                    <i class="fas fa-chart-pie text-9xl text-emerald-500"></i>
                </div>
                <div class="relative z-10 max-w-3xl">
                    <div class="inline-flex items-center space-x-3 px-4 py-2 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold tracking-[0.2em] uppercase mb-6">
                        <i class="fas fa-shield-alt"></i><span>Akuntabel & Terbuka</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6"><?= htmlspecialchars($web_transparansi_judul) ?></h2>
                    <p class="text-emerald-100/70 text-lg leading-relaxed mb-10"><?= nl2br(htmlspecialchars($web_transparansi_deskripsi)) ?></p>
                    <?php if($web_transparansi_file): ?>
                    <a href="<?= htmlspecialchars($web_transparansi_file) ?>" target="_blank" class="inline-flex items-center space-x-3 px-8 py-4 bg-emerald-500 text-emerald-950 font-bold rounded-xl hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-file-pdf"></i><span>Lihat Dokumen Laporan</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- INTEGRASI BLOG & PENGUMUMAN CMS -->
    <?php if(!empty($blogs)): ?>
    <section id="berita" class="py-24 bg-white/5 relative">
        <div class="container mx-auto px-6 md:px-12">
            <h2 class="text-4xl font-bold mb-16 text-center">Kabar & Pengumuman Terbaru</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach($blogs as $b): ?>
                <div class="glass p-8 rounded-[2rem] card-glow reveal flex flex-col">
                    <div class="text-emerald-400 text-xs tracking-widest font-bold uppercase mb-4"><i class="fas fa-calendar-alt mr-2"></i> <?= date('d M Y', strtotime($b['created_at'])) ?></div>
                    <h3 class="text-xl font-bold mb-4 line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h3>
                    <p class="text-emerald-100/60 text-sm leading-relaxed mb-6 line-clamp-3 flex-grow">
                        <?= strip_tags($b['konten']) ?>
                    </p>
                    <a href="#" class="text-emerald-400 font-bold hover:text-emerald-300 transition-colors text-sm">Baca Selengkapnya &rarr;</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="py-20 border-t border-white/5 text-center bg-[#031510]">
        <div class="container mx-auto px-6">
            <i class="fas fa-heart text-emerald-500 text-4xl mb-6"></i>
            <h2 class="text-3xl font-bold italic mb-12 uppercase tracking-widest">We Love You <?= htmlspecialchars($web_nama) ?></h2>
            <p class="text-emerald-100/30 text-sm tracking-widest uppercase mb-2">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($web_nama) ?>
            </p>
            <p class="text-emerald-100/10 text-[10px] tracking-widest uppercase">
                Diberdayakan oleh Si-SmaRT
            </p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) { nav.classList.add('glass-nav', 'py-4'); nav.classList.remove('py-6'); } 
            else { nav.classList.remove('glass-nav', 'py-4'); nav.classList.add('py-6'); }
        });

        // Scroll Reveal Animation
        function reveal() {
            document.querySelectorAll(".reveal").forEach(el => {
                if (el.getBoundingClientRect().top < window.innerHeight - 100) el.classList.add("active");
            });
        }
        window.addEventListener("scroll", reveal); reveal();
    </script>
</body>
</html>