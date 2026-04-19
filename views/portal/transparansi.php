    <!-- FITUR TRANSPARANSI KEUANGAN PUBLIK -->
    <section id="transparansi" class="py-16 md:py-24 lg:py-32 bg-emerald-900/5 border-b border-emerald-900/10 relative">
        <div class="container mx-auto px-6 md:px-12">
            <div class="glass p-8 md:p-12 lg:p-20 rounded-[3rem] lg:rounded-[4rem] card-glow reveal relative overflow-hidden bg-white/60">
                <div class="absolute top-0 right-0 p-8 md:p-12 opacity-5 pointer-events-none hidden md:block">
                    <i class="fas fa-chart-pie text-7xl md:text-9xl text-emerald-900"></i>
                </div>
                <div class="relative z-10 max-w-3xl">
                    <div class="inline-flex items-center space-x-3 px-5 py-3 rounded-full bg-emerald-600/10 border border-emerald-600/20 text-emerald-700 text-[10px] font-bold tracking-[0.2em] uppercase mb-8">
                        <i class="fas fa-shield-alt"></i><span>Akuntabel & Terbuka</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 mb-6 tracking-tight"><?= htmlspecialchars($web_transparansi_judul) ?></h2>
                    <p class="text-emerald-900/60 text-lg leading-relaxed mb-10 font-medium"><?= nl2br(htmlspecialchars($web_transparansi_deskripsi)) ?></p>
                    <?php if($web_transparansi_file): ?>
                    <a href="<?= htmlspecialchars(smart_asset($web_transparansi_file), ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="inline-flex items-center space-x-4 px-8 md:px-10 py-4 md:py-5 bg-emerald-600 text-white font-bold rounded-[2rem] hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-100 text-sm md:text-base">
                        <i class="fas fa-file-pdf text-xl"></i><span>Lihat Dokumen Laporan</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
