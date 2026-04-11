    <!-- Visi & Misi Section -->
    <section id="visimisi" class="py-32 bg-emerald-600/5">
        <div class="container mx-auto px-6 md:px-12">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="reveal">
                    <h2 class="text-[10px] font-black tracking-[0.5em] text-emerald-600 uppercase mb-4 text-left">Arah & Tujuan</h2>
                    <h3 class="text-5xl font-extrabold text-emerald-950 tracking-tight leading-tight mb-8 text-left">Visi & Misi <br> <span class="text-emerald-500">Kawasan Kita.</span></h3>
                    <div class="glass p-10 rounded-[3rem] border-l-8 border-emerald-600 text-left card-glow">
                        <h4 class="text-xl font-extrabold text-emerald-900 mb-4 tracking-tight">VISI KAMI</h4>
                        <p class="text-emerald-950/70 font-medium leading-relaxed italic">
                            "<?= htmlspecialchars($web_visi) ?>"
                        </p>
                    </div>
                </div>
                <div class="space-y-6 reveal text-left">
                    <h4 class="text-xl font-extrabold text-emerald-900 tracking-tight mb-6 uppercase">MISI KAMI</h4>
                    
                    <?php if(!empty(trim($settingsData['web_misi'] ?? ''))): ?>
                        <!-- Dinamis Misi dari CMS -->
                        <div class="glass p-8 rounded-[2.5rem] card-glow">
                            <div class="text-sm text-emerald-900/70 font-medium leading-relaxed space-y-3">
                                <?= nl2br(htmlspecialchars($settingsData['web_misi'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Default Fallback Design -->
                        <div class="space-y-4 text-left">
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Pelestarian Alam</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Menjaga keasrian view bukit dan kebersihan lingkungan sawah.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Ekonomi Mandiri</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Mendukung dan memfasilitasi wirausaha warga agar kawasan semakin maju.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Kerukunan Warga</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Membangun silaturahmi yang erat dan suasana pesantren yang religius.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start glass p-6 rounded-3xl hover:bg-white transition-all card-glow">
                                <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fas fa-laptop-code"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-emerald-950 text-sm tracking-tight">Layanan Digital</h5>
                                    <p class="text-xs text-emerald-900/50 mt-1 font-medium">Memberikan pelayanan administrasi warga yang cepat dan transparan.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
