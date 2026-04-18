    <!-- LAPORAN TERBARU SECTION -->
    <section id="laporan_terbaru" class="py-16 md:py-24 lg:py-32 bg-emerald-50/30 backdrop-blur-sm border-b border-emerald-100/50 relative overflow-hidden" data-parallax-section data-parallax-speed="0.02">
        <!-- Dekorasi Background Ambient -->
        <div class="absolute -left-20 top-20 w-72 h-72 bg-emerald-400/10 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute right-0 bottom-0 w-96 h-96 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="container mx-auto px-6 md:px-12 relative z-10">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-12 md:mb-16 gap-6 reveal">
                <div>
                    <!-- Indikator Live Pinging -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-red-50 border border-red-100 mb-4 shadow-sm hover:bg-red-100 transition-colors">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                        </span>
                        <span class="text-[10px] font-black tracking-[0.3em] text-red-600 uppercase">Live Update</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight">Pantau <br><span class="text-emerald-600">Informasi.</span></h2>
                    <p class="text-emerald-900/50 mt-4 font-medium max-w-xl text-sm md:text-base leading-relaxed">Transparansi Pelaporan Informasi terkini Di RT 001, Untuk meningkatkan kepedulian lingkungan kususnya RT 001 .</p>
                </div>
                <div class="block">
                    <a href="app.php" class="px-8 py-4 rounded-full bg-white text-emerald-700 font-bold hover:bg-emerald-50 hover:scale-105 active:scale-95 border border-emerald-100 transition-all shadow-lg shadow-emerald-900/5 inline-flex items-center gap-3">
                        <span>Lapor Sekarang</span>
                        <i class="fas fa-arrow-right text-sm opacity-60"></i>
                    </a>
                </div>
            </div>

            <!-- Grid Laporan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 reveal">
                <?php if(!empty($laporan_terbaru)): ?>
                <?php foreach($laporan_terbaru as $idx => $lap): 
                    // Variabel Deteksi Otomatis (Support untuk kolom database yang berbeda-beda)
                    $status = strtolower($lap['status'] ?? 'selesai');
                    $kategori = $lap['kategori'] ?? 'Info Warga';
                    $judul = $lap['judul'] ?? 'Laporan Keamanan';
                    $desc = $lap['deskripsi'] ?? $lap['keterangan'] ?? 'Tidak ada deskripsi mendetail.';
                    $tanggal = isset($lap['waktu_kejadian']) ? date('d M Y, H:i', strtotime($lap['waktu_kejadian'])) : date('d M Y');
                    $pelapor = $lap['pelapor'] ?? $lap['nama_pelapor'] ?? 'Petugas / Warga';
                    $lampiranPath = trim((string)($lap['lampiran_path'] ?? ''));
                    $lampiranName = trim((string)($lap['lampiran_name'] ?? ''));
                    $lampiranExt = strtolower(pathinfo($lampiranPath !== '' ? $lampiranPath : $lampiranName, PATHINFO_EXTENSION));
                    $isImageLampiran = in_array($lampiranExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                    $isVideoLampiran = in_array($lampiranExt, ['mp4', 'webm', 'ogg', 'mov'], true);
                    $displayLampiranName = $lampiranName !== '' ? $lampiranName : basename($lampiranPath);
                    
                    // Kecerdasan Buatan Sederhana untuk Mewarnai Status Otomatis
                    if(strpos($status, 'proses') !== false || strpos($status, 'tindak') !== false) {
                        $statusColor = 'text-amber-600'; $statusBg = 'bg-amber-50'; $statusBorder = 'border-amber-200';
                        $icon = 'fa-sync-alt fa-spin'; $glowColor = 'group-hover:shadow-amber-500/20 hover:border-amber-300';
                    } elseif(strpos($status, 'tunggu') !== false || strpos($status, 'pending') !== false) {
                        $statusColor = 'text-slate-600'; $statusBg = 'bg-slate-50'; $statusBorder = 'border-slate-200';
                        $icon = 'fa-clock'; $glowColor = 'group-hover:shadow-slate-500/20 hover:border-slate-300';
                    } else { // Jika Selesai / Aman
                        $statusColor = 'text-emerald-600'; $statusBg = 'bg-emerald-50'; $statusBorder = 'border-emerald-200';
                        $icon = 'fa-check-circle'; $glowColor = 'group-hover:shadow-emerald-500/20 hover:border-emerald-300';
                    }
                ?>
                <div class="glass bg-white/80 p-8 rounded-[2.5rem] card-glow <?= $glowColor ?> group relative transition-all duration-500 flex flex-col h-full overflow-hidden border border-white" style="transition-delay: <?= $idx * 100 ?>ms">
                    
                    <!-- Latar Aksen Lingkaran saat Di-hover -->
                    <div class="absolute -right-10 -top-10 w-40 h-40 <?= $statusBg ?> rounded-full blur-3xl opacity-60 pointer-events-none transition-all duration-700 group-hover:scale-150"></div>

                    <!-- Header Laporan -->
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest <?= $statusBg ?> <?= $statusColor ?> <?= $statusBorder ?> border inline-flex items-center gap-2 w-max shadow-sm">
                            <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars(strtoupper($status)) ?>
                        </span>
                        <span class="text-[10px] font-bold text-emerald-900/50 bg-white/80 px-3 py-1.5 rounded-xl border border-emerald-50 shadow-sm backdrop-blur-sm whitespace-nowrap">
                            <?= $tanggal ?>
                        </span>
                    </div>
                    
                    <!-- Isi Utama Laporan -->
                    <div class="mb-8 flex-1 relative z-10">
                        <h4 class="text-xl font-extrabold text-emerald-950 mb-3 leading-tight group-hover:<?= $statusColor ?> transition-colors line-clamp-2">
                            <?= htmlspecialchars($judul) ?>
                        </h4>
                        <p class="text-[0.9rem] font-medium text-emerald-900/60 leading-relaxed line-clamp-3">
                            <?= htmlspecialchars($desc) ?>
                        </p>

                        <?php if ($lampiranPath !== ''): ?>
                            <div class="mt-5 rounded-2xl border border-emerald-100 bg-white/80 p-3 backdrop-blur-sm">
                                <?php if ($isImageLampiran): ?>
                                    <a href="<?= htmlspecialchars($lampiranPath) ?>" target="_blank" rel="noopener" class="block">
                                        <img src="<?= htmlspecialchars($lampiranPath) ?>" alt="Lampiran Aduan" class="w-full h-40 object-cover rounded-xl border border-emerald-100">
                                    </a>
                                <?php elseif ($isVideoLampiran): ?>
                                    <video controls preload="metadata" class="w-full h-44 rounded-xl border border-emerald-100 bg-black/10" playsinline>
                                        <source src="<?= htmlspecialchars($lampiranPath) ?>">
                                        Browser tidak mendukung pemutaran video.
                                    </video>
                                <?php else: ?>
                                    <div class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/60 px-3 py-3 text-sm font-semibold text-emerald-800">
                                        <i class="fas fa-file-arrow-down mr-2"></i><?= htmlspecialchars($displayLampiranName !== '' ? $displayLampiranName : 'Lampiran Aduan') ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-3 flex items-center justify-end gap-2">
                                    <a href="<?= htmlspecialchars($lampiranPath) ?>" download class="px-3 py-1.5 rounded-lg border border-emerald-200 bg-white text-emerald-700 text-xs font-bold tracking-wide hover:bg-emerald-50 transition-colors">
                                        Download File
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer Pengirim & Kategori -->
                    <div class="pt-5 border-t border-emerald-900/10 flex justify-between items-center mt-auto relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-inner group-hover:scale-110 transition-transform">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] uppercase font-black text-emerald-900/30 tracking-widest">Dilaporkan Oleh</span>
                                <span class="text-xs font-bold text-emerald-950 truncate max-w-[120px]"><?= htmlspecialchars($pelapor) ?></span>
                            </div>
                        </div>
                        <div class="text-[9px] uppercase font-bold tracking-widest text-emerald-700 bg-white border border-emerald-100 px-3 py-1.5 rounded-lg shadow-sm">
                            <?= htmlspecialchars($kategori) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16 bg-white/50 rounded-[2rem] border border-emerald-100 border-dashed">
                        <p class="text-emerald-900/40 font-medium">Belum ada log kejadian atau informasi terbaru saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>