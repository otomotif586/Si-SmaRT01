    <!-- SEKSI BERITA & WARTA -->
    <section id="berita" class="py-16 md:py-24 lg:py-32 bg-white/40 backdrop-blur-sm border-b border-white/50 relative overflow-hidden">
        <div class="container mx-auto px-6 md:px-12">
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-12 md:mb-16 gap-6 reveal">
                <div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight">Warta & <br><span class="text-emerald-600">Berita Warga.</span></h2>
                    <p class="text-emerald-900/40 mt-4 font-medium italic">Informasi terkini seputar kegiatan dan pengumuman desa.</p>
                </div>
                <div class="block">
                    <button class="px-8 py-4 rounded-full bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">Lihat Semua Berita</button>
                </div>
            </div>

            <div id="berita-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10 reveal">
                <?php if(!empty($blogs)): ?>
                    <?php foreach($blogs as $b): 
                        $judul = $b['judul'] ?? 'Berita Warga';
                        $konten = $b['konten'] ?? '';
                        $thumb = !empty($b['thumbnail']) ? $b['thumbnail'] : 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=800';
                        $video_url = $b['video_url'] ?? '';
                        $youtube_url = $b['youtube_url'] ?? '';
                        $created_at = $b['created_at'] ?? null;
                        $tanggal_label = $created_at ? date('d M Y', strtotime($created_at)) : '-';
                        $is_dark = (crc32($judul) % 2 == 0);
                    ?>
                    <div class="flex justify-center berita-item paginate-item">
                        <div class="card <?= $is_dark ? 'dark' : '' ?>">
                            <div class="card-media-wrapper">
                                <?php if($video_url): ?>
                                    <video src="<?= htmlspecialchars(smart_asset($video_url), ENT_QUOTES, 'UTF-8') ?>" autoplay muted loop class="absolute inset-0 w-full h-full object-cover"></video>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars(smart_asset($thumb), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($judul) ?>" class="w-full h-full object-cover">
                                <?php endif; ?>
                                
                                <div class="card-title-overlay">
                                    <h2><?= htmlspecialchars($judul) ?></h2>
                                </div>

                                <div class="absolute top-6 left-6 z-30">
                                    <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[9px] font-bold text-white uppercase tracking-widest border border-white/30">Berita</span>
                                </div>
                            </div>

                            <section>
                                <p class="line-clamp-3"><?= strip_tags($konten) ?></p>
                                <div class="mt-4 flex items-center justify-between gap-4 pr-2 sm:pr-3">
                                    <span class="tag"><?= htmlspecialchars($tanggal_label) ?></span>
                                    <button
                                        type="button"
                                        class="shrink-0 js-open-blog-modal"
                                        data-title="<?= htmlspecialchars($judul, ENT_QUOTES) ?>"
                                        data-content="<?= htmlspecialchars(str_replace(["\r", "\n"], '', $konten), ENT_QUOTES) ?>"
                                        data-date="<?= htmlspecialchars($tanggal_label, ENT_QUOTES) ?>"
                                        data-thumb="<?= htmlspecialchars(smart_asset($thumb), ENT_QUOTES) ?>"
                                        data-video="<?= htmlspecialchars(smart_asset($video_url), ENT_QUOTES) ?>"
                                        data-youtube="<?= htmlspecialchars($youtube_url, ENT_QUOTES) ?>"
                                    >Baca</button>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-20 text-emerald-900/30 font-medium italic">Belum ada berita yang diterbitkan.</div>
                <?php endif; ?>
            </div>
            <div id="berita-pagination" class="portal-pagination hidden reveal" aria-label="Pagination Warta dan Berita Warga"></div>
        </div>
    </section>
