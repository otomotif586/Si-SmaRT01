    <!-- Informasi Penting Section -->
    <section id="info_penting" class="py-32 bg-emerald-600/5 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="text-center mb-20 reveal">
                <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4">Layanan Cepat</h2>
                <h3 class="text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight">
                    <?= htmlspecialchars($settingsData['web_info_penting_judul'] ?? 'Informasi Penting Warga') ?>
                </h3>
                <p class="text-emerald-900/50 mt-6 font-medium max-w-2xl mx-auto">
                    <?= htmlspecialchars($settingsData['web_info_penting_deskripsi'] ?? 'Pintasan informasi mendasar untuk kebutuhan harian Anda.') ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php for($i=1; $i<=4; $i++): 
                    $icon = $settingsData["web_info_item_{$i}_icon"] ?? 'fa-info-circle';
                    $title = $settingsData["web_info_item_{$i}_title"] ?? 'Informasi';
                    $desc = $settingsData["web_info_item_{$i}_desc"] ?? 'Deskripsi informasi penting.';
                ?>
                <div class="glass p-10 rounded-[3.5rem] card-glow reveal flex flex-col items-center text-center group transition-all duration-500 hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-3xl bg-white shadow-xl flex items-center justify-center text-emerald-600 mb-8 text-3xl transition-transform group-hover:scale-110">
                        <i class="fas <?= htmlspecialchars($icon) ?>"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-emerald-950 mb-4"><?= htmlspecialchars($title) ?></h4>
                    <p class="text-emerald-900/40 text-sm leading-relaxed font-medium"><?= htmlspecialchars($desc) ?></p>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
