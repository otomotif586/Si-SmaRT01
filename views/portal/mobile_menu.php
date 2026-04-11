    <div id="mobile-menu-overlay" class="fixed inset-0 bg-[#fdfaf3] z-[150] hidden flex flex-col items-center justify-center space-y-12 text-3xl font-black uppercase tracking-widest text-emerald-950">
        <button id="close-btn" class="absolute top-8 right-8 w-14 h-14 glass rounded-3xl text-emerald-600 flex items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
        <?php if($web_logo): ?>
            <img src="<?= $web_logo ?>" class="w-20 h-20 object-contain rounded-3xl shadow-2xl mb-4 bg-white p-2" alt="Logo">
        <?php endif; ?>
        <?php if(empty($menus)): ?>
            <a href="#kawasan" class="mobile-link text-2xl font-bold">Kawasan</a>
            <a href="#info_penting" class="mobile-link text-2xl font-bold">Info Penting</a>
            <a href="#visimisi" class="mobile-link text-2xl font-bold">Visi Misi</a>
            <a href="#berita" class="mobile-link text-2xl font-bold">Berita Warga</a>
            <a href="#organisasi" class="mobile-link text-2xl font-bold">Organisasi</a>
            <a href="#layanan" class="mobile-link text-2xl font-bold">Layanan</a>
            <a href="#wisata" class="mobile-link text-2xl font-bold">Wisata</a>
        <?php else: ?>
            <?php foreach($menus as $m): ?>
                <a href="<?= htmlspecialchars($m['url']) ?>" class="mobile-link text-2xl font-bold"><?= htmlspecialchars($m['nama_menu']) ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="app.php" class="mt-4 px-12 py-6 bg-emerald-600 text-white rounded-[2.5rem] shadow-2xl text-xl font-bold">Akses Warga</a>
    </div>
