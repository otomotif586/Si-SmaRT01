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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10 reveal">
                <?php if(!empty($blogs)): ?>
                    <?php foreach($blogs as $b): 
                        $thumb = $b['thumbnail'] ?: 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=800';
                        $is_dark = (crc32($b['judul']) % 2 == 0);
                    ?>
                    <div class="flex justify-center">
                        <div class="card <?= $is_dark ? 'dark' : '' ?>">
                            <div class="card-media-wrapper">
                                <?php if($b['video_url']): ?>
                                    <video src="<?= $b['video_url'] ?>" autoplay muted loop class="absolute inset-0 w-full h-full object-cover"></video>
                                <?php else: ?>
                                    <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($b['judul']) ?>" class="w-full h-full object-cover">
                                <?php endif; ?>
                                
                                <div class="card-title-overlay">
                                    <h2><?= htmlspecialchars($b['judul']) ?></h2>
                                </div>

                                <div class="absolute top-6 left-6 z-30">
                                    <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[9px] font-bold text-white uppercase tracking-widest border border-white/30">Berita</span>
                                </div>
                            </div>

                            <section>
                                <p class="line-clamp-3"><?= strip_tags($b['konten']) ?></p>
                                <div>
                                    <span class="tag"><?= date('d M Y', strtotime($b['created_at'])) ?></span>
                                    <button onclick="openBlogModal('<?= htmlspecialchars(addslashes($b['judul'])) ?>', '<?= htmlspecialchars(addslashes(str_replace(["\r", "\n"], '', $b['konten']))) ?>', '<?= date('d M Y', strtotime($b['created_at'])) ?>', '<?= $thumb ?>', '<?= $b['video_url'] ?>', '<?= $b['youtube_url'] ?>')">Baca</button>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-20 text-emerald-900/30 font-medium italic">Belum ada berita yang diterbitkan.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
