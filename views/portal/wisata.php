    <!-- Destinasi Wisata -->
    <section id="wisata" class="py-16 md:py-24 lg:py-32 bg-white/30 backdrop-blur-sm" data-parallax-section data-parallax-speed="0.028">
        <div class="container mx-auto px-6 md:px-12 text-center reveal mb-12 md:mb-20">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 tracking-tight">Destinasi Wisata Alam Sekitar</h2>
            <p class="text-emerald-900/40 mt-4 font-medium italic underline decoration-emerald-200 decoration-4 underline-offset-4 tracking-tight">Rekreasi alam yang menyejukkan jiwa.</p>
        </div>
        <div id="wisata-grid" class="container mx-auto px-6 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
            <?php for($i=1; $i<=2; $i++): ?>
            <div class="group relative h-[400px] md:h-[500px] overflow-hidden rounded-[3rem] md:rounded-[4rem] reveal shadow-2xl wisata-item paginate-item parallax-media-shell" data-parallax-media-speed="0.15">
                <img src="<?= $wisata[$i]['image'] ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-1000 opacity-60" alt="<?= htmlspecialchars($wisata[$i]['title']) ?>" loading="lazy" data-parallax-media-speed="0.2">
                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/10 to-transparent"></div>
                <div class="absolute bottom-12 left-12 text-white text-left">
                    <span class="text-[10px] font-bold tracking-[0.4em] uppercase opacity-70 mb-2 block"><?= htmlspecialchars($wisata[$i]['category']) ?></span>
                    <h4 class="text-2xl md:text-4xl font-extrabold tracking-tight"><?= htmlspecialchars($wisata[$i]['title']) ?></h4>
                    <p class="text-white/70 mt-3 text-sm max-w-sm font-medium leading-relaxed"><?= htmlspecialchars($wisata[$i]['description']) ?></p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <div id="wisata-pagination" class="portal-pagination hidden reveal" aria-label="Pagination Destinasi Wisata"></div>
    </section>
