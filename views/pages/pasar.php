<!-- Halaman Kelola UMKM/Pasar -->
<div id="page-pasar" class="page-content hidden page-section stagger-ready">
    <script>
        function smartAssetUrl(path) {
            if (!path) return '';
            if (/^(?:https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) return path;
            const basePath = (window.__SMART_ASSET_BASE_PATH__ || '').replace(/\/$/, '');
            const cleanedPath = String(path).replace(/^\/+/, '');
            if (!basePath) return cleanedPath;
            return `${basePath}/${cleanedPath}`;
        }
    </script>
    <!-- Modern Header for Page -->
    <div class="page-header-premium mb-8 stagger-item stagger-delay-1">
        <h2 class="text-3xl font-bold text-slate-900 font-space">Pasar Warga</h2>
        <p class="text-slate-500 font-medium">Kelola Produk UMKM dan Promosi Pasar</p>
    </div>

    <div class="sub-nav-tabs mb-8 stagger-item" style="display: flex; gap: 8px; animation-delay: 0.2s">
        <button id="btn-tab-produk" class="sub-nav-tab active" onclick="switchPasarTab('tab-produk-warga', this)">
            <i data-lucide="shopping-bag" class="mr-2"></i> Produk Warga
        </button>
        <button id="btn-tab-slider" class="sub-nav-tab" onclick="switchPasarTab('tab-slider-pasar', this)">
            <i data-lucide="image" class="mr-2"></i> Slider Promosi
        </button>
        <button id="btn-tab-penjual" class="sub-nav-tab" onclick="switchPasarTab('tab-penjual-pasar', this)">
            <i data-lucide="store" class="mr-2"></i> Penjual UMKM
        </button>
    </div>

    <!-- TAB: PRODUK WARGA -->
    <div id="tab-produk-warga" class="pasar-tab-content active">
        <div class="glass-card card-section mb-8 stagger-item stagger-delay-3">
            <div class="section-header">
                <div>
                    <h4 class="section-title text-2xl font-bold text-slate-800">Daftar Produk UMKM</h4>
                    <p class="text-slate-500 text-sm">Produk inovatif dari warga untuk warga.</p>
                </div>
            </div>

            <div class="table-responsive mt-8">
                <table class="modern-table w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-4 px-6 text-left">Foto</th>
                            <th class="py-4 px-6 text-left">Nama Produk</th>
                            <th class="py-4 px-6 text-left">Penjual</th>
                            <th class="py-4 px-6 text-left">Harga</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pasar-produk-body">
                        <!-- Diisi via AJAX -->
                        <tr><td colspan="5" class="text-center py-12">Memuat data produk...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: SLIDER PASAR -->
    <div id="tab-slider-pasar" class="pasar-tab-content hidden">
        <div class="glass-card card-section stagger-item stagger-delay-3">
            <div class="section-header">
                <div>
                    <h4 class="section-title text-2xl font-bold text-slate-800">Slider Promosi Pasar</h4>
                    <p class="text-slate-500 text-sm">Slide gambar yang tampil di halaman depan portal pasar.</p>
                </div>
                <button class="button-primary px-8" onclick="openSliderModal()">
                    <i data-lucide="image-plus" class="mr-2"></i> Tambah Slide
                </button>
            </div>
            
            <div id="pasar-slider-container" class="grid-container-3-col mt-8" style="gap: 20px;">
                <!-- Diisi via AJAX -->
                <div class="text-center py-12 col-span-full">Memuat slider...</div>
            </div>
        </div>
    </div>

    <!-- TAB: PENJUAL PASAR -->
    <div id="tab-penjual-pasar" class="pasar-tab-content hidden">
        <div class="glass-card card-section mb-8 stagger-item stagger-delay-3">
            <div class="section-header">
                <div>
                    <h4 class="section-title text-2xl font-bold text-slate-800">Daftar Penjual (UMKM)</h4>
                    <p class="text-slate-500 text-sm">Kelola akun akses dan profil toko warga.</p>
                </div>
                <button class="button-primary px-8" onclick="openPenjualModal()">
                    <i data-lucide="user-plus" class="mr-2"></i> Tambah Penjual
                </button>
            </div>
            <div class="table-responsive mt-8">
                <table class="modern-table w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-4 px-6 text-left">Toko & Pemilik</th>
                            <th class="py-4 px-6 text-left">Kontak (WA)</th>
                            <th class="py-4 px-6 text-left">Akses Login</th>
                            <th class="py-4 px-6 text-left">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pasar-penjual-body">
                        <tr><td colspan="5" class="text-center py-12">Memuat data penjual...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL FORM PRODUK -->
    <div id="pasar-modal" class="fixed inset-0 w-full h-full hidden items-center justify-center p-4 sm:p-6 overlay-z10025">
        <div class="absolute inset-0 bg-slate-800/30 backdrop-blur-sm transition-opacity" onclick="closePasarModal()"></div>
        <div class="glass-card relative w-full max-w-lg m-auto flex flex-col overflow-hidden shadow-2xl" style="border-radius: 2.5rem; max-height: 90vh; background: #ffffff; border: 1px solid rgba(255,255,255,0.9);">
            <button class="absolute top-6 right-6 w-12 h-12 rounded-[1.25rem] bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all z-10" onclick="closePasarModal()">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
            <div class="hide-scrollbar" style="padding: 40px; overflow-y: auto;">
                <div class="mb-8 pr-12">
                    <h2 id="modal-pasar-title" class="text-3xl font-black text-slate-800 tracking-tight mb-2">Form Produk</h2>
                    <p class="text-slate-500 font-medium text-sm">Informasi produk UMKM warga.</p>
                </div>
                
                <input type="hidden" id="pasar-id" value="0">
                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Produk</label>
                        <input type="text" id="pasar-nama" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="Contoh: Sambal Bawang Home Made">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Toko / Penjual</label>
                        <input type="text" id="pasar-penjual" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="Contoh: Dapur Bu RT">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Harga (Rp)</label>
                            <input type="number" id="pasar-harga" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="15000">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">WhatsApp</label>
                            <input type="text" id="pasar-wa" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="08123456789">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Foto Produk</label>
                        <input type="file" id="pasar-foto" accept="image/*" class="w-full bg-slate-50 border border-slate-100 text-slate-500 rounded-[1.5rem] py-3 px-4 font-medium focus:outline-none transition-all text-sm shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                </div>
                
                <button class="w-full mt-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-[1.5rem] shadow-xl shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-2" onclick="savePasar()">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> SIMPAN PRODUK
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL PRODUK -->
    <div id="modal-detail-produk" class="fixed inset-0 w-full h-full hidden items-center justify-center p-4 sm:p-6 overlay-z10025">
        <div class="absolute inset-0 bg-slate-800/30 backdrop-blur-sm transition-opacity" onclick="closeDetailPasar()"></div>
        <div class="glass-card relative w-full max-w-lg m-auto flex flex-col overflow-hidden shadow-2xl" style="border-radius: 2.5rem; max-height: 90vh; background: #ffffff; border: 1px solid rgba(255,255,255,0.9);">
            <button class="absolute top-6 right-6 w-12 h-12 rounded-[1.25rem] bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all z-10" onclick="closeDetailPasar()">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
            <div class="hide-scrollbar" style="padding: 40px; overflow-y: auto;">
                <div class="mb-6 pr-12">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Detail Produk</h2>
                    <p class="text-slate-500 font-medium text-sm">Informasi lengkap dagangan UMKM warga.</p>
                </div>
                
                <div class="flex flex-col gap-6">
                    <div class="w-full h-48 bg-slate-100 rounded-[1.5rem] overflow-hidden border border-slate-200">
                        <img id="detail-foto" src="" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Produk</p>
                        <p id="detail-nama" class="text-lg font-bold text-slate-800">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kategori & Harga</p>
                            <p id="detail-kategori" class="text-sm font-bold text-slate-800 mb-1">-</p>
                            <p id="detail-harga" class="text-sm font-bold text-emerald-600">-</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toko & Status</p>
                            <p id="detail-penjual" class="text-sm font-bold text-slate-800 mb-1">-</p>
                            <p id="detail-status" class="text-sm font-bold text-slate-800">-</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Deskripsi</p>
                        <p id="detail-deskripsi" class="text-sm text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100 leading-relaxed">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FORM SLIDER -->
    <div id="pasar-slider-modal" class="fixed inset-0 w-full h-full hidden items-center justify-center p-4 sm:p-6 overlay-z10025">
        <div class="absolute inset-0 bg-slate-800/30 backdrop-blur-sm transition-opacity" onclick="closeSliderModal()"></div>
        <div class="glass-card relative w-full max-w-md m-auto flex flex-col overflow-hidden shadow-2xl" style="border-radius: 2.5rem; background: #ffffff; padding: 40px; border: 1px solid rgba(255,255,255,0.9);">
            <button class="absolute top-6 right-6 w-12 h-12 rounded-[1.25rem] bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all z-10" onclick="closeSliderModal()">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
            <div class="pr-12 mb-8">
                <h2 class="text-2xl font-black text-slate-800 mb-1">Tambah Slider</h2>
                <p class="text-slate-500 text-sm font-medium">Unggah gambar promosi pasar.</p>
            </div>
            
            <div class="form-group mb-6">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Pilih Gambar (Desktop 1920x600 disarankan)</label>
                <input type="file" id="slider-foto" accept="image/*" class="w-full bg-slate-50 border border-slate-100 text-slate-500 rounded-[1.5rem] py-3 px-4 font-medium focus:outline-none transition-all text-sm shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer">
            </div>
            <button class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-[1.5rem] shadow-xl shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-2" onclick="saveSlider()">
                <i data-lucide="upload" class="w-5 h-5"></i> UNGGAH SLIDER
            </button>
        </div>
    </div>

    <!-- MODAL FORM PENJUAL -->
    <div id="modal-pasar-penjual" class="fixed inset-0 w-full h-full hidden items-center justify-center p-4 sm:p-6 overlay-z10025">
        <div class="absolute inset-0 bg-slate-800/30 backdrop-blur-sm transition-opacity" onclick="closePenjualModal()"></div>
        <div class="glass-card relative w-full max-w-md m-auto flex flex-col overflow-hidden shadow-2xl" style="border-radius: 2.5rem; max-height: 90vh; background: #ffffff; border: 1px solid rgba(255,255,255,0.9);">
            <div style="padding: 32px 32px 24px; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <h2 id="modal-penjual-title" class="text-2xl font-black text-slate-800">Tambah Penjual</h2>
                    <p class="text-slate-500 font-medium text-sm mt-1">Buat akun akses dan profil toko UMKM.</p>
                </div>
                <button class="absolute top-8 right-8 w-10 h-10 rounded-[1rem] bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all z-10" onclick="closePenjualModal()"><i data-lucide="x" style="width: 20px; height: 20px;"></i></button>
            </div>
            
            <div class="hide-scrollbar" style="padding: 24px 32px; overflow-y: auto;">
                <input type="hidden" id="pasar-penjual-id" value="0">
                <h4 class="text-xs font-black uppercase tracking-widest text-emerald-600 mb-4 bg-emerald-50 inline-block px-4 py-1.5 rounded-full">Profil Toko</h4>
                
                <div class="space-y-5 mb-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Foto / Logo Toko</label>
                        <input type="file" id="pasar-penjual-logo" accept="image/*" class="w-full bg-slate-50 border border-slate-100 text-slate-500 rounded-[1.5rem] py-3 px-4 font-medium focus:outline-none transition-all text-sm shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Toko / Usaha</label>
                        <input type="text" id="pasar-penjual-toko" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="Cth: Dapur Bu RT">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Pemilik</label>
                        <input type="text" id="pasar-penjual-pemilik" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="Nama Lengkap Pemilik">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nomor WhatsApp</label>
                        <input type="text" id="pasar-penjual-wa" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="0812...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Alamat / Blok</label>
                        <textarea id="pasar-penjual-alamat" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner min-h-[100px] resize-none" placeholder="Alamat rumah/toko"></textarea>
                    </div>
                </div>
                
                <h4 class="text-xs font-black uppercase tracking-widest text-blue-600 mb-4 bg-blue-50 inline-block px-4 py-1.5 rounded-full">Akses Login Aplikasi</h4>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Username</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">@</span>
                            <input type="text" id="pasar-penjual-username" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 pl-12 pr-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="username_toko">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Password <span class="text-red-400 font-normal lowercase ml-1" style="font-size: 0.65rem;">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" id="pasar-penjual-password" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Status Toko</label>
                        <select id="pasar-penjual-status" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="Aktif">Aktif Buka</option>
                            <option value="Nonaktif">Tutup Sementara (Nonaktif)</option>
                            <option value="Pending">Menunggu Approval (Pending)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div style="padding: 24px 32px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; background: #fff; border-radius: 0 0 2.5rem 2.5rem; flex-shrink: 0;">
                <button type="button" class="px-6 py-4 rounded-[1.5rem] font-bold text-slate-500 bg-slate-50 hover:bg-slate-200 transition-all" onclick="closePenjualModal()">Batal</button>
                <button type="button" class="flex-grow px-6 py-4 rounded-[1.5rem] font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2" onclick="savePasarPenjual()"><i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Penjual</button>
            </div>
        </div>
    </div>
</div>

<script>
    function initPasarPage() {
        loadPasarProduk();
        loadPasarSliders();
        loadPasarPenjual();
    }

    function switchPasarTab(tabId, btn) {
        document.querySelectorAll('.pasar-tab-content').forEach(t => t.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
        
        document.querySelectorAll('.sub-nav-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        if(tabId === 'tab-produk-warga') loadPasarProduk();
        if(tabId === 'tab-slider-pasar') loadPasarSliders();
        if(tabId === 'tab-penjual-pasar') loadPasarPenjual();
    }

    function loadPasarProduk() {
        const tbody = document.getElementById('pasar-produk-body');
        if(!tbody) return;
        fetch('api/get_produk_pasar.php').then(r=>r.json()).then(res => {
            if(res.status === 'success') {
                let html = '';
                res.data.forEach(p => {
                    // Tangani format foto JSON (dukungan Multi-Image)
                    let mainPhoto = 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
                    try {
                        const photos = JSON.parse(p.foto);
                        if (Array.isArray(photos) && photos.length > 0) mainPhoto = smartAssetUrl(photos[0]);
                    } catch(e) {
                        if (p.foto) mainPhoto = smartAssetUrl(p.foto);
                    }

                    html += `
                        <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0">
                            <td class="py-4 px-6"><img src="${mainPhoto}" class="w-12 h-12 rounded-xl object-cover shadow-sm"></td>
                            <td class="py-4 px-6 font-bold text-slate-800">${p.nama_produk}</td>
                            <td class="py-4 px-6 text-slate-500">${p.penjual_nama || '-'}</td>
                            <td class="py-4 px-6 font-medium text-emerald-600">Rp ${Number(p.harga).toLocaleString()}</td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <button onclick='detailPasar(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="p-3 text-emerald-600 hover:bg-emerald-50 rounded-2xl transition-all mr-2" title="Detail"><i data-lucide="eye" class="w-5 h-5"></i></button>
                                <button onclick='editPasar(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="p-3 text-blue-600 hover:bg-blue-50 rounded-2xl transition-all mr-2"><i data-lucide="edit-3" class="w-5 h-5"></i></button>
                                <button onclick="deletePasar(${p.id})" class="p-3 text-red-600 hover:bg-red-50 rounded-2xl transition-all"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html || '<tr><td colspan="5" class="text-center py-12 text-slate-400">Belum ada produk terdaftar</td></tr>';
                if(typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    function loadPasarSliders() {
        const container = document.getElementById('pasar-slider-container');
        if(!container) return;
        fetch('views/pages/get_sliders.php').then(r=>r.json()).then(res => {
            if(res.status === 'success') {
                let html = '';
                res.data.forEach(s => {
                    html += `
                        <div class="relative group rounded-3xl overflow-hidden shadow-lg aspect-video bg-slate-200">
                            <img src="${smartAssetUrl(s.image)}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                <button onclick="deleteSlider(${s.id})" class="p-4 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-red-500 transition-all"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>`;
                });
                container.innerHTML = html || '<div class="text-center py-12 col-span-full text-slate-400">Belum ada slider terpasang</div>';
                if(typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // Modal & Save Functions similar to before but more robust
    function openPasarModal() { 
        document.getElementById('pasar-id').value = 0; 
        document.getElementById('pasar-nama').value = ''; 
        document.getElementById('pasar-penjual').value = ''; 
        document.getElementById('pasar-harga').value = ''; 
        document.getElementById('pasar-wa').value = ''; 
        document.getElementById('modal-pasar-title').innerText = 'Tambah Produk';
        const modal = document.getElementById('pasar-modal');
        document.body.appendChild(modal);
        modal.classList.remove('hidden'); 
        modal.classList.add('flex');
    }

    function closePasarModal() {
        const modal = document.getElementById('pasar-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function editPasar(p) {
        document.getElementById('pasar-id').value = p.id;
        document.getElementById('pasar-nama').value = p.nama_produk;
        document.getElementById('pasar-penjual').value = p.penjual_nama || p.penjual;
        document.getElementById('pasar-harga').value = p.harga;
        document.getElementById('pasar-wa').value = p.no_wa || p.wa_penjual;
        document.getElementById('modal-pasar-title').innerText = 'Edit Produk';
        const modal = document.getElementById('pasar-modal');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function detailPasar(p) {
        document.getElementById('detail-nama').innerText = p.nama_produk;
        document.getElementById('detail-kategori').innerText = p.kategori || '-';
        document.getElementById('detail-harga').innerText = 'Rp ' + Number(p.harga).toLocaleString();
        document.getElementById('detail-penjual').innerText = p.penjual_nama || '-';
        document.getElementById('detail-status').innerText = p.status || '-';
        document.getElementById('detail-deskripsi').innerText = p.deskripsi || 'Tidak ada deskripsi';
        
        let mainPhoto = 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80';
        try {
            const photos = JSON.parse(p.foto);
                        if (Array.isArray(photos) && photos.length > 0) mainPhoto = smartAssetUrl(photos[0]);
        } catch(e) {
                        if (p.foto) mainPhoto = smartAssetUrl(p.foto);
        }
        document.getElementById('detail-foto').src = mainPhoto;

        const modal = document.getElementById('modal-detail-produk');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailPasar() {
        const modal = document.getElementById('modal-detail-produk');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function savePasar() {
        const file = document.getElementById('pasar-foto').files[0];
        let fotoData = '';
        
        if (file) {
            const fdUpload = new FormData();
            fdUpload.append('fotos[]', file);
            showLoading('Mengunggah Foto...');
            try {
                const upResp = await fetch('views/pages/upload_produk.php', { method: 'POST', body: fdUpload });
                const upRes = await upResp.json();
                if(upRes.status === 'success' && upRes.data.length > 0) {
                    fotoData = JSON.stringify([upRes.data[0]]);
                }
            } catch(e) {
                showToast('Gagal mengunggah foto', 'error');
                return;
            }
        }

        const fd = new FormData();
        fd.append('id', document.getElementById('pasar-id').value);
        fd.append('nama_produk', document.getElementById('pasar-nama').value);
        fd.append('penjual_nama', document.getElementById('pasar-penjual').value);
        fd.append('harga', document.getElementById('pasar-harga').value);
        fd.append('no_wa', document.getElementById('pasar-wa').value);
        if (fotoData) {
            fd.append('foto', fotoData);
        }
        
        showLoading('Menyimpan...');
        fetch('views/pages/save_produk.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res => {
            if(res.status==='success'){
                showToast('Produk Berhasil Disimpan');
                closePasarModal();
                loadPasarProduk();
            }
        });
    }

    function deletePasar(id) {
        Swal.fire({ title: 'Hapus Produk?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus' })
            .then(res => { if(res.isConfirmed) {
                const fd = new FormData(); fd.append('id', id);
                fetch('views/pages/delete_produk.php', {method:'POST', body:fd}).then(r=>r.json()).then(res => {
                    if(res.status==='success'){ showToast('Produk Terhapus'); loadPasarProduk(); }
                });
            }});
    }

    // === LOGIK PENJUAL (UMKM) ===
    function loadPasarPenjual() {
        const tbody = document.getElementById('pasar-penjual-body');
        if(!tbody) return;
        fetch('views/pages/get_penjual.php').then(r=>r.json()).then(res => {
            if(res.status === 'success') {
                let html = '';
                res.data.forEach(p => {
                    const badge = p.status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 
                                  (p.status === 'Pending' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700');
                    const initialName = encodeURIComponent(p.nama_toko);
                    const logoSrc = p.logo ? smartAssetUrl(p.logo) : `https://ui-avatars.com/api/?name=${initialName}&background=10b981&color=fff`;
                    let approveBtn = p.status === 'Pending' ? `<button onclick="approvePasarPenjual(${p.id})" class="p-3 text-emerald-600 hover:bg-emerald-50 rounded-2xl transition-all mr-2" title="Setujui Akun UMKM"><i data-lucide="check-circle" class="w-5 h-5"></i></button>` : '';
                    html += `
                        <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4">
                                    <img src="${logoSrc}" class="w-10 h-10 rounded-full object-cover shadow-sm">
                                    <div>
                                        <div class="font-bold text-slate-800">${p.nama_toko}</div>
                                        <div class="text-xs text-slate-500">${p.nama_pemilik}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600">${p.no_wa}</td>
                            <td class="py-4 px-6 text-slate-600 font-medium">@${p.username}</td>
                            <td class="py-4 px-6"><span class="px-2 py-1 rounded-md text-[10px] font-black uppercase ${badge}">${p.status}</span></td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                ${approveBtn}
                                <button onclick='editPasarPenjual(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="p-3 text-blue-600 hover:bg-blue-50 rounded-2xl transition-all mr-2" title="Edit Akun"><i data-lucide="edit-3" class="w-5 h-5"></i></button>
                                <button onclick="deletePasarPenjual(${p.id})" class="p-3 text-red-600 hover:bg-red-50 rounded-2xl transition-all" title="Hapus Akun"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html || '<tr><td colspan="5" class="text-center py-12 text-slate-400">Belum ada data penjual/UMKM didaftarkan.</td></tr>';
                if(typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    function closePenjualModal() {
        const modal = document.getElementById('modal-pasar-penjual');
        modal.classList.remove('drawer-active');
        setTimeout(() => modal.classList.add('hidden'), 400);
    }

    function openPenjualModal() {
        document.getElementById('pasar-penjual-id').value = 0;
        document.getElementById('pasar-penjual-logo').value = '';
        document.getElementById('pasar-penjual-toko').value = '';
        document.getElementById('pasar-penjual-pemilik').value = '';
        document.getElementById('pasar-penjual-wa').value = '';
        document.getElementById('pasar-penjual-alamat').value = '';
        document.getElementById('pasar-penjual-username').value = '';
        document.getElementById('pasar-penjual-password').value = '';
        document.getElementById('modal-penjual-title').innerText = 'Tambah Penjual UMKM';
        const modal = document.getElementById('modal-pasar-penjual');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('drawer-active'), 10);
    }

    function editPasarPenjual(p) {
        document.getElementById('pasar-penjual-id').value = p.id;
        document.getElementById('pasar-penjual-logo').value = '';
        document.getElementById('pasar-penjual-toko').value = p.nama_toko;
        document.getElementById('pasar-penjual-pemilik').value = p.nama_pemilik;
        document.getElementById('pasar-penjual-wa').value = p.no_wa;
        document.getElementById('pasar-penjual-alamat').value = p.alamat;
        document.getElementById('pasar-penjual-username').value = p.username;
        document.getElementById('pasar-penjual-password').value = '';
        document.getElementById('pasar-penjual-status').value = p.status;
        document.getElementById('modal-penjual-title').innerText = 'Edit Penjual UMKM';
        const modal = document.getElementById('modal-pasar-penjual');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('drawer-active'), 10);
    }

    function savePasarPenjual() {
        const fd = new FormData();
        fd.append('id', document.getElementById('pasar-penjual-id').value);
        fd.append('nama_toko', document.getElementById('pasar-penjual-toko').value);
        fd.append('nama_pemilik', document.getElementById('pasar-penjual-pemilik').value);
        fd.append('no_wa', document.getElementById('pasar-penjual-wa').value);
        fd.append('alamat', document.getElementById('pasar-penjual-alamat').value);
        fd.append('username', document.getElementById('pasar-penjual-username').value);
        fd.append('password', document.getElementById('pasar-penjual-password').value);
        fd.append('status', document.getElementById('pasar-penjual-status').value);
        
        const logoFile = document.getElementById('pasar-penjual-logo').files[0];
        if (logoFile) fd.append('logo', logoFile);

        showLoading('Menyimpan...');
        fetch('views/pages/save_penjual.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res => {
            if(res.status === 'success') {
                showToast('Akun Penjual Tersimpan');
                closePenjualModal();
                loadPasarPenjual();
            } else { showToast(res.message, 'error'); }
        });
    }

    function deletePasarPenjual(id) {
        Swal.fire({ title: 'Hapus Penjual?', text: 'Akun ini akan dihapus secara permanen.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus' })
            .then(res => { if(res.isConfirmed) {
                const fd = new FormData(); fd.append('id', id);
                fetch('views/pages/delete_penjual.php', {method:'POST', body:fd}).then(r=>r.json()).then(res => {
                    if(res.status === 'success'){ showToast('Terhapus'); loadPasarPenjual(); }
                });
            }});
    }

    function approvePasarPenjual(id) {
        Swal.fire({ title: 'Setujui Toko?', text: 'Akun akan aktif dan warga bisa mulai berjualan.', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Setujui', confirmButtonColor: '#10b981' })
            .then(res => { if(res.isConfirmed) {
                const fd = new FormData(); fd.append('id', id);
                fetch('views/pages/approve_penjual.php', {method:'POST', body:fd}).then(r=>r.json()).then(res => {
                    if(res.status === 'success'){ showToast('Toko Disetujui'); loadPasarPenjual(); }
                });
            }});
    }

    function openSliderModal() { 
        const modal = document.getElementById('pasar-slider-modal');
        document.body.appendChild(modal);
        modal.classList.remove('hidden'); 
        modal.classList.add('flex');
    }

    function closeSliderModal() {
        const modal = document.getElementById('pasar-slider-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function saveSlider() {
        const file = document.getElementById('slider-foto').files[0];
        if(!file) return showToast('Pilih file gambar', 'error');
        const fd = new FormData(); fd.append('foto', file);
        showLoading('Mengunggah...');
        fetch('views/pages/save_slider.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res => {
            if(res.status==='success'){ 
                showToast('Slider Ditambahkan'); 
                closeSliderModal();
                loadPasarSliders(); 
            }
        });
    }

    function deleteSlider(id) {
        const fd = new FormData(); fd.append('id', id);
        fetch('views/pages/delete_slider.php', {method:'POST', body:fd}).then(r=>r.json()).then(res => {
            if(res.status==='success'){ showToast('Slider Dihapus'); loadPasarSliders(); }
        });
    }
</script>

<style>
    .sub-nav-tab {
        display: flex; align-items: center; padding: 12px 24px; border-radius: 16px;
        background: transparent; color: #64748b; font-weight: 600; border: 1px solid transparent; transition: all 0.3s;
    }
    .sub-nav-tab:hover { background: rgba(16, 185, 129, 0.05); color: #10b981; }
    .sub-nav-tab.active { background: white; color: #10b981; border-color: rgba(16, 185, 129, 0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
    .pasar-tab-content.hidden { display: none; }
</style>