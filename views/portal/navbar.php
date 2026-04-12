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
            <div class="hidden lg:flex items-center space-x-6 xl:space-x-12">
                <?php if(empty($menus)): ?>
                    <a href="#kawasan" class="nav-link">Kawasan</a>
                    <a href="#info_penting" class="nav-link">Info Penting</a>
                    <a href="#organisasi" class="nav-link">Organisasi</a>
                    <a href="#visimisi" class="nav-link">Visi Misi</a>
                    <a href="#berita" class="nav-link">Berita Warga</a>
                    <a href="#layanan" class="nav-link">Layanan</a>
                    <a href="#wisata" class="nav-link">Wisata</a>
                <?php else: ?>
                    <?php foreach($menus as $m): ?>
                        <a href="<?= htmlspecialchars($m['url']) ?>" class="nav-link"><?= htmlspecialchars($m['nama_menu']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- TOMBOL MASUK SISTEM SI-SMART -->
                <a href="app.php" class="px-6 py-3 xl:px-8 xl:py-3.5 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 hover:shadow-2xl hover:shadow-emerald-100 transition-all shadow-lg font-bold whitespace-nowrap text-sm xl:text-base">
                    SI - SMART
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button id="menu-btn" class="lg:hidden w-12 h-12 flex items-center justify-center glass rounded-2xl text-emerald-700 shadow-sm transition-transform active:scale-90">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>
    </nav>
