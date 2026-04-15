    <div id="mobile-menu-overlay" class="fixed inset-0 z-[150] hidden">
        <div id="mobile-menu-panel" class="mobile-menu-panel">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <?php if($web_logo): ?>
                        <img src="<?= $web_logo ?>" class="w-10 h-10 object-contain rounded-xl bg-white p-1.5 border border-emerald-100" alt="Logo">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center"><i class="fas fa-users-rectangle"></i></div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm font-extrabold text-emerald-950 leading-tight"><?= htmlspecialchars($web_nama) ?></p>
                        <p class="text-[10px] uppercase tracking-[0.16em] text-emerald-900/45 font-semibold">Navigation</p>
                    </div>
                </div>
                <button id="close-btn" class="w-10 h-10 rounded-xl bg-white border border-emerald-100 text-emerald-700 flex items-center justify-center" aria-label="Tutup menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mobile-menu-links">
                <?php if(empty($menus)): ?>
                    <a href="#kawasan" class="mobile-link">Kawasan</a>
                    <a href="#info_penting" class="mobile-link">Info Penting</a>
                    <a href="#visimisi" class="mobile-link">Visi Misi</a>
                    <a href="#berita" class="mobile-link">Berita Warga</a>
                    <a href="#organisasi" class="mobile-link">Organisasi</a>
                    <a href="#layanan" class="mobile-link">Layanan</a>
                    <a href="#wisata" class="mobile-link">Wisata</a>
                <?php else: ?>
                    <?php foreach($menus as $m): ?>
                        <a href="<?= htmlspecialchars($m['url']) ?>" class="mobile-link"><?= htmlspecialchars($m['nama_menu']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mobile-menu-actions">
                <a href="pasar.php" class="startup-ghost-btn w-full text-center">Pasar Warga</a>
                <a href="app.php" class="startup-primary-btn w-full text-center">Masuk Dashboard</a>
            </div>
        </div>
    </div>
