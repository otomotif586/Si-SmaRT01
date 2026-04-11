    <!-- STRUKTUR ORGANISASI -->
    <?php if(!empty($pengurus)): ?>
    <section id="organisasi" class="py-16 md:py-24 lg:py-32 bg-emerald-50/40 border-b border-emerald-100/50 relative">
        <div class="container mx-auto px-6 md:px-12 text-center mb-12 md:mb-20 reveal">
            <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4">Pengurus Lingkungan</h2>
            <h3 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 tracking-tight">Struktur Organisasi</h3>
        </div>

        <div class="container mx-auto px-6 md:px-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-10">
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
