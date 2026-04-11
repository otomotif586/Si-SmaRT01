    <!-- Hero Section -->
    <section class="relative min-h-[100svh] flex items-center pt-32 lg:pt-20 overflow-hidden border-b border-white/50 bg-transparent">
        <div class="container mx-auto px-6 md:px-12 relative z-10 text-left">
            <div class="grid lg:grid-cols-2 items-center gap-12 lg:gap-24 pb-16 lg:pb-0">
                <div class="space-y-10">
                    <div class="inline-flex items-center space-x-3 px-5 py-3 mt-4 md:mt-0 rounded-full bg-emerald-600/5 border border-emerald-600/10 text-emerald-700 text-[10px] font-bold tracking-[0.2em] uppercase glass">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>RT 001 Go-Digital</span>
                    </div>
                    
                    <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] font-extrabold leading-[1.1] tracking-tight">
                        <?php 
                        // Ambil judul bersih (hanya simpan <br> jika ada)
                        $raw_title = $settingsData['web_hero_title'] ?? "Kampung Impian <br> Kini Jadi Nyata.";
                        $clean_title = strip_tags($raw_title, '<br>');
                        
                        if(strpos($clean_title, '<br>') !== false) {
                            // Jika ada baris baru, bagi otomatis
                            $parts = explode('<br>', $clean_title);
                            echo '<span class="text-emerald-950">' . trim($parts[0]) . '</span>';
                            if(count($parts) > 1) {
                                echo '<br><span class="text-emerald-500">' . trim(strip_tags($parts[1])) . '</span>';
                            }
                        } else {
                            // Jika tidak ada <br>, bagi dua berdasarkan kata
                            $words = explode(' ', trim($clean_title));
                            $half = ceil(count($words) / 2);
                            $top = implode(' ', array_slice($words, 0, $half));
                            $bottom = implode(' ', array_slice($words, $half));
                            
                            echo '<span class="text-emerald-950">' . $top . '</span>';
                            if($bottom) echo '<br><span class="text-emerald-500">' . $bottom . '</span>';
                        }
                        ?>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-emerald-900/50 leading-relaxed max-w-xl font-medium">
                        <?= nl2br(htmlspecialchars($web_visi)) ?>
                    </p>
                    
                    <div class="flex flex-wrap gap-6 pt-4">
                        <a href="#organisasi" class="px-12 py-6 bg-emerald-600 text-white font-bold rounded-[2rem] flex items-center space-x-4 hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-100">
                            <span>PENGURUS RT</span>
                            <i class="fas fa-arrow-right text-xs opacity-50"></i>
                        </a>
                        <a href="https://www.google.com/maps/place/Bimbel+Become/@-6.4617173,106.9727219,3a,73.9y,173.83h,88.65t/data=!3m7!1e1!3m5!1s0-jsU8IuF6zRD2dAo4a4pQ!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.3543519995511133%26panoid%3D0-jsU8IuF6zRD2dAo4a4pQ%26yaw%3D173.82917027237332!7i16384!8i8192!4m6!3m5!1s0x2e69bf52d8d30d3b:0x94ee6f0e357a0db5!8m2!3d-6.4600501!4d106.9744559!16s%2Fg%2F11y2dtt465?entry=ttu&g_ep=EgoyMDI2MDQwNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="px-12 py-6 glass text-emerald-900 font-bold rounded-[2rem] hover:bg-white transition-all flex items-center space-x-4 cursor-pointer">
                            <i class="fas fa-play-circle text-emerald-600 text-xl"></i>
                            <span>TUR KAWASAN</span>
                        </a>
                    </div>

                </div>
                
                <!-- Hero Parallax Slider -->
                <div class="relative mt-12 lg:mt-0 w-full">
                    <div class="absolute -inset-10 bg-emerald-500/10 blur-[120px] rounded-full animate-pulse"></div>
                    
                    <div class="slider relative w-full h-[450px] lg:h-[600px] rounded-[3rem] lg:rounded-[5rem] overflow-hidden shadow-2xl bg-emerald-950/20">
                        <button class="slider--btn slider--btn__prev">
                            <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                        </button>
                        
                        <div class="slides__wrapper">
                            <div class="slides">
                                <!-- Slide 1 -->
                                <div class="slide" data-current>
                                    <div class="slide__inner">
                                        <div class="slide--image__wrapper">
                                            <img class="slide--image" src="<?= $slides[1]['image'] ?>" />
                                        </div>
                                    </div>
                                    <div class="slide__bg" style="--bg: url('<?= $slides[1]['image'] ?>')" data-current></div>
                                </div>
                                <!-- Slide 2 -->
                                <div class="slide" data-next>
                                    <div class="slide__inner">
                                        <div class="slide--image__wrapper">
                                            <img class="slide--image" src="<?= $slides[2]['image'] ?>" />
                                        </div>
                                    </div>
                                    <div class="slide__bg" style="--bg: url('<?= $slides[2]['image'] ?>')" data-next></div>
                                </div>
                                <!-- Slide 3 -->
                                <div class="slide" data-previous>
                                    <div class="slide__inner">
                                        <div class="slide--image__wrapper">
                                            <img class="slide--image" src="<?= $slides[3]['image'] ?>" />
                                        </div>
                                    </div>
                                    <div class="slide__bg" style="--bg: url('<?= $slides[3]['image'] ?>')" data-previous></div>
                                </div>
                            </div>
                            
                            <div class="slides--infos">
                                <!-- Info 1 -->
                                <div class="slide-info" data-current>
                                    <div class="slide-info__inner">
                                        <div class="slide-info--text__wrapper">
                                            <div class="slide-info--text" data-title><span><?= $slides[1]['title'] ?></span></div>
                                            <div class="slide-info--text" data-subtitle><span><?= $slides[1]['subtitle'] ?></span></div>
                                            <div class="slide-info--text" data-description><span><?= $slides[1]['description'] ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Info 2 -->
                                <div class="slide-info" data-next>
                                    <div class="slide-info__inner">
                                        <div class="slide-info--text__wrapper">
                                            <div class="slide-info--text" data-title><span><?= $slides[2]['title'] ?></span></div>
                                            <div class="slide-info--text" data-subtitle><span><?= $slides[2]['subtitle'] ?></span></div>
                                            <div class="slide-info--text" data-description><span><?= $slides[2]['description'] ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Info 3 -->
                                <div class="slide-info" data-previous>
                                    <div class="slide-info__inner">
                                        <div class="slide-info--text__wrapper">
                                            <div class="slide-info--text" data-title><span><?= $slides[3]['title'] ?></span></div>
                                            <div class="slide-info--text" data-subtitle><span><?= $slides[3]['subtitle'] ?></span></div>
                                            <div class="slide-info--text" data-description><span><?= $slides[3]['description'] ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button class="slider--btn slider--btn__next">
                            <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </button>
                    </div>

                    <div class="absolute -bottom-6 left-4 right-4 lg:right-auto lg:-bottom-10 lg:-left-10 glass p-6 lg:p-10 rounded-[2.5rem] lg:rounded-[3.5rem] z-[120] shadow-2xl max-w-[320px] mx-auto lg:mx-0">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-emerald-950">
                                <i class="fas fa-quote-left text-xs"></i>
                            </div>
                            <span class="font-bold text-sm text-emerald-900">Suasana Warga</span>
                        </div>
                        <p class="text-sm text-emerald-900/60 leading-relaxed font-medium italic">"View bukit & sawah di sini luar biasa, ekonomi warganya juga hidup sekali."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
