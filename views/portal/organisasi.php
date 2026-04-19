    <!-- STRUKTUR ORGANISASI -->
    <?php if(!empty($pengurus)): ?>
    <?php 
    // Klasifikasi Data Pengurus Berdasarkan Level / Urutan secara Dinamis
    $levels = [];
    foreach($pengurus as $p) {
        $lvl = (int)$p['urutan'];
        // Kelompokkan pengurus ke dalam array berdasarkan levelnya
        if (!isset($levels[$lvl])) {
            $levels[$lvl] = [];
        }
        $levels[$lvl][] = $p;
    }
    ksort($levels); // Urutkan dari level paling atas (angka terkecil) ke bawah

    // Fungsi Reusable untuk menggambar Kartu Pengurus
    if (!function_exists('renderOrgCard')) {
        function renderOrgCard($a, $isTop = false, $delay = 0) {
            $icon = 'fa-user-tie';
            $jab_lower = strtolower($a['jabatan']);
            if(strpos($jab_lower, 'sekretaris') !== false) $icon = 'fa-file-signature';
            elseif(strpos($jab_lower, 'bendahara') !== false) $icon = 'fa-coins';
            elseif(strpos($jab_lower, 'keamanan') !== false || strpos($jab_lower, 'satgas') !== false) $icon = 'fa-user-shield';
            elseif(strpos($jab_lower, 'ketua') !== false) $icon = 'fa-crown';

            // Variabel Penyesuaian Desain antar Level
            $cardClass = $isTop ? 'p-8 md:p-10 max-w-[320px] rounded-[3rem] border-emerald-400 border-2 shadow-2xl shadow-emerald-900/20' : 'p-6 md:p-8 max-w-[260px] rounded-[2.5rem] border-emerald-100 border';
            $imgSize = $isTop ? 'w-32 h-32 md:w-40 md:h-40' : 'w-24 h-24 md:w-28 md:h-28';
            $titleSize = $isTop ? 'text-2xl' : 'text-lg';
            $badgeClass = $isTop ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-emerald-500/30' : 'bg-emerald-100 text-emerald-800 shadow-emerald-100/50';
            ?>
            <div class="glass card-glow org-card reveal flex flex-col items-center text-center group transition-all duration-700 w-full relative bg-white/70 <?= $cardClass ?>">
                
                <!-- Badge Jabatan Mengambang (Startup Style) -->
                <div class="org-role-badge absolute -top-4 left-1/2 -translate-x-1/2 px-5 py-1.5 rounded-full text-[10px] md:text-xs font-black shadow-lg <?= $badgeClass ?> z-20">
                    <?= htmlspecialchars($a['jabatan']) ?>
                </div>

                <!-- Foto/Icon Container -->
                <div class="<?= $imgSize ?> rounded-full bg-white border-4 border-white shadow-xl flex items-center justify-center mb-6 group-hover:scale-105 group-hover:shadow-2xl transition-all duration-500 overflow-hidden relative z-10 ring-4 ring-emerald-50">
                    <?php if($a['foto']): ?>
                        <img src="<?= htmlspecialchars(smart_asset($a['foto']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($a['nama']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-600 transition-colors duration-500">
                            <i class="fas <?= $icon ?> text-4xl <?= $isTop ? 'md:text-5xl' : '' ?> text-emerald-600 group-hover:text-white transition-colors duration-500"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="<?= $titleSize ?> font-extrabold text-emerald-950 tracking-tight leading-tight mb-1"><?= htmlspecialchars($a['nama']) ?></h4>
                
                <!-- Ornamen Garis Khusus Top Level -->
                <?php if($isTop): ?>
                    <p class="text-xs text-emerald-900/50 font-bold uppercase tracking-[0.3em] mt-3 border-t border-emerald-200/50 pt-4 w-full">Top Leader</p>
                <?php endif; ?>
            </div>
            <?php
        }
    }
    ?>

    <section id="organisasi" class="py-16 md:py-24 lg:py-32 bg-emerald-50/40 border-b border-emerald-100/50 relative overflow-x-hidden overflow-y-visible">
        <!-- Glow Background Layer -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-full bg-gradient-to-b from-emerald-200/40 to-transparent pointer-events-none blur-3xl z-0"></div>

        <div class="container mx-auto px-6 md:px-12 text-center mb-16 md:mb-24 reveal relative z-10">
            <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4">Pengurus Lingkungan</h2>
            <h3 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 tracking-tight">Struktur Organisasi</h3>
            <p class="text-emerald-900/50 mt-6 font-medium max-w-2xl mx-auto">Susunan tim penggerak yang berdedikasi untuk kemajuan dan kenyamanan warga kita.</p>
        </div>

        <div class="container mx-auto px-4 md:px-12 relative z-10 flex flex-col items-center">
            
            <?php 
            $level_keys = array_keys($levels);
            $min_level = !empty($level_keys) ? min($level_keys) : 0;
            $total_levels = count($level_keys);
            $current_index = 0;
            
            foreach($levels as $lvl_num => $members): 
                $isTop = ($lvl_num === $min_level); // Level tertinggi mendapat styling khusus
            ?>
                <!-- RENDER BARIS UNTUK LEVEL <?= $lvl_num ?> -->
                <div class="relative flex justify-center w-full <?= $isTop ? 'z-20' : 'z-10' ?>">
                    <div class="flex flex-wrap justify-center gap-6 md:gap-8 lg:gap-12 w-full">
                        <?php foreach($members as $idx => $m) renderOrgCard($m, $isTop, 0.1 + ($current_index * 0.1) + ($idx * 0.1)); ?>
                    </div>
                </div>

                <!-- GARIS KONEKTOR ANTAR LEVEL -->
                <?php if($current_index < $total_levels - 1): ?>
                    <div class="flex flex-col items-center w-full h-12 md:h-16 reveal relative z-10">
                        <div class="w-1.5 h-full bg-gradient-to-b from-emerald-400 to-emerald-100 rounded-full opacity-60"></div>
                    </div>
                <?php endif; ?>
            <?php 
                $current_index++;
            endforeach; 
            ?>

        </div>
    </section>
    <?php endif; ?>
