    <!-- Footer -->
    <footer class="py-12 bg-emerald-950 text-white rounded-t-[3rem] mt-20 relative overflow-hidden">
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="flex flex-col items-center space-y-4 mb-8">
                <div class="flex items-center space-x-2 text-[10px] font-bold tracking-[0.3em] uppercase text-emerald-400 opacity-80">
                    <i class="fas fa-heart text-[8px] animate-pulse"></i>
                    <span>We Love You</span>
                </div>
                <h3 class="text-xl md:text-2xl font-black tracking-[0.1em] uppercase text-white">
                    <?= htmlspecialchars($web_nama) ?>
                </h3>
            </div>

            <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 mb-8 opacity-40 group">
                <div class="flex items-center space-x-2 hover:opacity-100 transition-opacity cursor-default">
                    <i class="fas fa-leaf text-xs"></i>
                    <span class="text-[8px] font-bold tracking-widest uppercase">Pengurus RT</span>
                </div>
                <div class="flex items-center space-x-2 hover:opacity-100 transition-opacity cursor-default">
                    <i class="fas fa-people-group text-xs"></i>
                    <span class="text-[8px] font-bold tracking-widest uppercase">Warga</span>
                </div>
                <div class="flex items-center space-x-2 hover:opacity-100 transition-opacity cursor-default">
                    <i class="fas fa-hand-holding-heart text-xs"></i>
                    <span class="text-[8px] font-bold tracking-widest uppercase">Sinergi</span>
                </div>
            </div>
            
            <div class="w-full h-px bg-white/5 mb-8"></div>
            
            <p class="text-white/20 text-[8px] tracking-[0.4em] font-bold uppercase transition-all hover:text-white/30">
                &copy; 2026 Portal Warga RT 001 • Pesona Kahuripan Development
            </p>
        </div>
    </footer>
