    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-[100] transition-all duration-700 py-5">
        <div class="container mx-auto px-4 md:px-8 xl:px-12">
            <div class="startup-nav-shell">
                <a href="index.php" class="startup-brand group" aria-label="Kembali ke beranda portal">
                    <?php if($web_logo): ?>
                        <img src="<?= $web_logo ?>" class="w-10 h-10 object-contain rounded-2xl shadow-lg shadow-emerald-100 group-hover:rotate-3 transition-transform bg-white" alt="Logo">
                    <?php else: ?>
                        <div class="w-10 h-10 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-100 group-hover:rotate-3 transition-transform">
                            <i class="fas fa-users-rectangle text-white text-base"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col text-left">
                        <span class="text-[15px] md:text-base font-extrabold tracking-tight leading-none text-emerald-950"><?= htmlspecialchars($web_nama) ?></span>
                        <span class="text-[9px] tracking-[0.22em] uppercase opacity-45 font-semibold mt-1">Portal Warga Digital</span>
                    </div>
                </a>

                <div class="hidden lg:flex startup-nav-links">
                    <?php if(empty($menus)): ?>
                        <a href="#kawasan" class="nav-link">Kawasan</a>
                        <a href="#info_penting" class="nav-link">Info</a>
                        <a href="#organisasi" class="nav-link">Organisasi</a>
                        <a href="#visimisi" class="nav-link">Visi Misi</a>
                        <a href="#berita" class="nav-link">Berita</a>
                        <a href="#layanan" class="nav-link">Layanan</a>
                        <a href="#wisata" class="nav-link">Wisata</a>
                    <?php else: ?>
                        <?php foreach($menus as $m): ?>
                            <a href="<?= htmlspecialchars($m['url']) ?>" class="nav-link"><?= htmlspecialchars($m['nama_menu']) ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="hidden lg:flex items-center gap-2">
                    <a href="pasar.php" class="startup-ghost-btn">Pasar Warga</a>
                    <a href="app.php" class="startup-primary-btn">Masuk Dashboard</a>
                </div>

                <div class="lg:hidden flex items-center gap-2">
                    <a href="app.php" class="startup-mobile-quick">Masuk</a>
                    <button id="menu-btn" class="startup-menu-btn" aria-label="Buka menu navigasi">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
