<?php
session_start();
// Jika belum login, lempar ke login_penjual.php
if (!isset($_SESSION['penjual_id'])) {
    header("Location: login_penjual.php");
    exit();
}
require_once 'config/database.php';

// Ambil setting untuk info RT
$stmtSet = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
$settings = $stmtSet->fetchAll(PDO::FETCH_KEY_PAIR);
$web_nama = $settings['web_nama'] ?? 'Portal Warga';

// Ambil data penjual
$stmtPenjual = $pdo->prepare("SELECT * FROM pasar_penjual WHERE id = ?");
$stmtPenjual->execute([$_SESSION['penjual_id']]);
$penjualData = $stmtPenjual->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Ruang Penjual - <?= htmlspecialchars($web_nama) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/mobile-ux.css">
    <style>
        /* Mencegah FOUC */
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; -webkit-tap-highlight-color: transparent; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }

        .product-thumb-shell {
            position: relative;
            overflow: hidden;
            background: #e2e8f0;
        }

        .product-thumb-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(226, 232, 240, 0), rgba(255, 255, 255, 0.85), rgba(226, 232, 240, 0));
            animation: productThumbShimmer 1.2s infinite;
        }

        .product-thumb-shell.loaded::after {
            display: none;
        }

        .product-text-shell {
            position: relative;
            min-height: 64px;
        }

        .product-text-skeleton {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            gap: 7px;
            justify-content: center;
            transition: opacity 0.25s ease;
        }

        .product-text-skeleton .line {
            height: 9px;
            border-radius: 999px;
        }

        .product-text-shell.loaded .product-text-skeleton {
            opacity: 0;
            pointer-events: none;
        }

        .search-pending {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #059669;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.22);
            border-radius: 999px;
            padding: 3px 8px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .search-pending.active {
            opacity: 1;
        }

        @keyframes productThumbShimmer {
            100% { transform: translateX(100%); }
        }

        @media (max-width: 768px) {
            body { padding-bottom: 96px; }
            .container { padding-left: 14px !important; padding-right: 14px !important; }
            #productModal > div,
            #profileModal > div {
                max-height: 96vh !important;
                border-radius: 1.5rem !important;
            }
            #productModal .p-8,
            #profileModal .p-8 {
                padding: 1rem !important;
            }
            #productForm input,
            #productForm select,
            #productForm textarea,
            #profileForm input {
                min-height: 48px;
                font-size: 16px;
            }
        }

        @media (max-width: 390px) {
            .container { padding-left: 10px !important; padding-right: 10px !important; }
            main.container { padding-top: 14px !important; }
            .mb-8.p-6 { padding: 1rem !important; border-radius: 1.5rem !important; }
            .mb-8.p-6 h2 { font-size: 1.1rem !important; }
            .bg-white.p-4.rounded-3xl { padding: 0.7rem !important; border-radius: 1rem !important; gap: 0.55rem !important; }
            #searchInput { min-height: 42px; font-size: 0.82rem; padding-top: 0.6rem !important; padding-bottom: 0.6rem !important; }
            #productContainer { gap: 0.55rem !important; }
            #productContainer > div { border-radius: 1.1rem !important; padding: 0.6rem !important; gap: 0.6rem !important; }
            #productContainer .w-20.h-20 { width: 4rem !important; height: 4rem !important; border-radius: 0.8rem !important; }
            #productContainer h4 { font-size: 0.77rem !important; }
            #productContainer p { margin-bottom: 0.3rem !important; }
            #productContainer .text-xs { font-size: 0.65rem !important; }
            #productContainer button.w-9.h-9 { width: 2rem !important; height: 2rem !important; border-radius: 0.7rem !important; }
            .search-pending { padding: 2px 6px; font-size: 9px; }
            .search-pending span { display: none; }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000);
    </script>
</head>
<body class="bg-slate-50 min-h-screen pb-24">
    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Memuat data...</h3>
            <p class="smart-loading-subtitle">Mohon tunggu sebentar</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>
    
    <!-- Top Nav -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="pasar.php" class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-emerald-50 hover:text-emerald-500 transition-all border border-slate-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-800 leading-tight">Ruang Penjual</h1>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold"><?= htmlspecialchars($_SESSION['penjual_nama_toko'] ?? 'Toko Saya') ?></p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openProfileModal()" class="w-10 h-10 bg-white shadow-sm border border-slate-100 text-slate-600 rounded-2xl flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-all">
                    <i class="fas fa-store"></i>
                </button>
                <a href="logout_penjual.php" class="w-10 h-10 bg-red-50 border border-red-100 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <!-- Welcoming Section -->
        <div class="mb-8 p-6 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[2.5rem] text-white shadow-2xl shadow-emerald-200 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-black mb-1">Halo, <?= htmlspecialchars($penjualData['nama_pemilik'] ?? 'Penjual') ?>! 👋</h2>
                <p class="text-emerald-100 text-sm font-medium opacity-90">Kelola dagangan Anda dengan mudah dari genggaman.</p>
                <div class="mt-6 flex gap-4">
                    <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 flex-1">
                        <p class="text-[9px] uppercase font-bold tracking-widest text-emerald-200 mb-1">Total Produk</p>
                        <h3 id="stat-total" class="text-xl font-black">0</h3>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 flex-1">
                        <p class="text-[9px] uppercase font-bold tracking-widest text-emerald-200 mb-1">Status Aktif</p>
                        <h3 id="stat-aktif" class="text-xl font-black">0</h3>
                    </div>
                </div>
            </div>
            <i class="fas fa-shop text-white/5 absolute -right-4 -bottom-4 text-9xl"></i>
        </div>

        <!-- Action Bar -->
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 mb-8 flex items-center gap-4">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Cari dagangan..." class="w-full pl-11 pr-24 py-3 bg-slate-50 border-none rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none">
                <div id="searchPending" class="search-pending" aria-live="polite" aria-hidden="true">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Mencari...</span>
                </div>
            </div>
            <button onclick="openModal()" class="w-12 h-12 bg-emerald-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all shrink-0">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <!-- Product Table Responsive -->
        <div id="productContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <!-- Data will be loaded here -->
        </div>

        <!-- Pagination Controls -->
        <div id="paginationContainer" class="mt-8 flex justify-center items-center gap-2"></div>
    </main>

    <!-- Modal Form -->
    <div id="productModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md transition-all duration-300 overflow-hidden">
        <div class="bg-white w-full max-w-lg m-auto rounded-[3rem] shadow-2xl flex flex-col max-h-[92vh] sm:max-h-[85vh] animate-in slide-in-from-bottom duration-500">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 id="modalTitle" class="text-xl font-black text-slate-800">Tambah Dagangan</h3>
                    <p class="text-xs text-slate-400 font-medium">Isi detail produk/jasa Anda</p>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-red-50 hover:text-red-500 transition-all text-slate-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto no-scrollbar p-8 pt-6 space-y-6">
                <!-- Image Upload Multi -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Foto Dagangan (Bisa > 1)</label>
                    <div id="imagePreviewContainer" class="flex flex-wrap justify-center sm:justify-start gap-4 mb-4">
                        <!-- Previews will go here -->
                        <label class="w-24 h-24 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-500 transition-all cursor-pointer bg-slate-50 relative group">
                            <input type="file" id="photoInput" multiple accept="image/*" capture="environment" class="hidden" onchange="handlePhotoSelect(event)">
                            <i class="fas fa-camera text-xl mb-1 group-hover:animate-float"></i>
                            <span class="text-[9px] font-bold">Ambil Foto</span>
                        </label>
                    </div>
                    <p class="text-[9px] text-slate-400 ml-1">💡 Tips: Foto yang cerah membuat barang lebih cepat laku.</p>
                </div>

                <form id="productForm" class="space-y-5">
                    <input type="hidden" name="id" id="prodId">
                    <input type="hidden" name="foto" id="prodFotoHidden"> <!-- Will store JSON array of paths -->
                    
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Dagangan</label>
                        <input type="text" name="nama_produk" id="prodNama" required placeholder="Contoh: Pempek Palembang Asli" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold text-slate-700">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Harga (Rp)</label>
                            <input type="number" name="harga" id="prodHarga" required placeholder="0" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Kategori</label>
                            <select name="kategori" id="prodKategori" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold text-slate-700 appearance-none">
                                <option value="Makanan">🍲 Makanan</option>
                                <option value="Jasa">🔧 Jasa / Service</option>
                                <option value="Elektronik">🔌 Elektronik</option>
                                <option value="Fashion">👔 Fashion</option>
                                <option value="Lainnya">✨ Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-5 bg-emerald-50/50 rounded-3xl border border-emerald-100/50">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-3 ml-1">Info Kontak (Otomatis dari Profil)</label>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-emerald-100">
                                <i class="fas fa-store text-emerald-500 text-xs"></i>
                                <input type="text" name="penjual_nama" id="prodPenjual" readonly class="bg-transparent border-none text-xs font-bold text-slate-700 w-full outline-none" placeholder="Lengkapi Profil Toko">
                            </div>
                            <div class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-emerald-100">
                                <i class="fab fa-whatsapp text-emerald-500 text-xs"></i>
                                <input type="text" name="no_wa" id="prodWA" readonly class="bg-transparent border-none text-xs font-bold text-slate-700 w-full outline-none" placeholder="62812...">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Keterangan Produk</label>
                        <textarea name="deskripsi" id="prodDesc" rows="3" placeholder="Ceritakan kelebihan dagangan Anda..." class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-medium text-slate-700 resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-2">
                        <span class="text-xs font-bold text-slate-600">Terbitkan Dagangan?</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="prodStatusCheck" class="sr-only peer" checked onchange="document.getElementById('prodStatus').value = this.checked ? 'Tersedia' : 'Habis'">
                            <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none ring-0 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                        <input type="hidden" name="status" id="prodStatus" value="Tersedia">
                    </div>
                </form>
            </div>

            <div class="p-8 bg-slate-50/50 flex gap-4">
                <button onclick="closeModal()" class="flex-1 px-6 py-4 bg-white border border-slate-200 text-slate-500 font-bold rounded-[1.5rem] hover:bg-slate-100 transition-all outline-none">Batal</button>
                <button onclick="saveProduct()" id="btnSave" class="flex-[2] px-6 py-4 bg-emerald-600 text-white font-bold rounded-[1.5rem] hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> <span>Simpan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
        <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-emerald-50/30">
                <div>
                    <h3 class="text-xl font-black text-slate-800">Profil Toko</h3>
                    <p class="text-xs text-slate-400 font-medium">Bantu pembeli mengenali Anda</p>
                </div>
                <button onclick="closeProfileModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="profileForm" class="p-8 space-y-5">
                <div class="flex items-center gap-4 mb-2">
                    <img id="profLogoPreview" src="https://ui-avatars.com/api/?name=Toko&background=10b981&color=fff" class="w-16 h-16 rounded-full object-cover shadow-md border border-slate-100 shrink-0">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Logo / Foto Toko</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-bold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer" onchange="previewProfileLogo(event)">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Toko / Brand</label>
                    <input type="text" name="nama_toko" id="profNama" required placeholder="Contoh: Toko Berkah" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">WhatsApp Utama</label>
                    <input type="text" name="no_wa" id="profWA" required placeholder="62812..." class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Alamat Singkat</label>
                    <input type="text" name="alamat" id="profAlamat" placeholder="Blok C No. 5" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none text-sm font-semibold">
                </div>
            </form>
            <div class="p-8 bg-slate-50/50 flex gap-4">
                <button onclick="saveProfile()" class="flex-1 px-6 py-4 bg-emerald-600 text-white font-bold rounded-[1.5rem] hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all outline-none">Simpan Profil</button>
            </div>
        </div>
    </div>

    <script>
        let allProducts = [];
        let storeProfile = <?= json_encode($penjualData) ?>;
        let selectedPhotos = []; // Array of Blobs/Files
        let existingPhotos = []; // Array of URLs
        let pageLoadingTick;
        let filterTick;
        let lastFilterAt = 0;
        let lastFilterQuery = '';

        function getAdaptiveFilterDelay(query) {
            const now = Date.now();
            const gap = now - lastFilterAt;
            lastFilterAt = now;

            // Delay lebih panjang saat user mengetik cepat agar render tidak terlalu sering.
            let delay = gap < 110 ? 250 : (gap < 220 ? 180 : 110);

            const isLowEnd = ((navigator.hardwareConcurrency || 4) <= 4) || ((navigator.deviceMemory || 4) <= 4);
            if (isLowEnd) delay += 70;

            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) delay += 40;
            if (query.length <= 2) delay += 20;

            return Math.max(90, Math.min(360, delay));
        }

        function showPageLoading(title = 'Memuat data...') {
            const overlay = document.getElementById('smartLoadingOverlay');
            const fill = document.getElementById('smartLoadingFill');
            const pct = document.getElementById('smartLoadingPct');
            const heading = document.getElementById('smartLoadingTitle');
            if (!overlay || !fill || !pct) return;
            if (heading) heading.textContent = title;
            let value = 8;
            fill.style.width = value + '%';
            pct.textContent = String(value);
            overlay.classList.add('active');
            clearInterval(pageLoadingTick);
            pageLoadingTick = setInterval(() => {
                value = Math.min(92, value + Math.max(1, Math.round((100 - value) / 10)));
                fill.style.width = value + '%';
                pct.textContent = String(value);
            }, 140);
        }

        function hidePageLoading() {
            clearInterval(pageLoadingTick);
            const overlay = document.getElementById('smartLoadingOverlay');
            if (overlay) overlay.classList.remove('active');
        }

        function showPageToast(title, icon = 'success') {
            if (typeof Swal === 'undefined') return;
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            Swal.fire({
                toast: true,
                position: isMobile ? 'bottom' : 'top',
                icon,
                title,
                showConfirmButton: false,
                timer: 2200,
                timerProgressBar: true,
                customClass: { popup: 'smart-toast' }
            });
        }

        function previewProfileLogo(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = (ev) => document.getElementById('profLogoPreview').src = ev.target.result;
                reader.readAsDataURL(file);
            }
        }

        let currentPage = 1;
        const itemsPerPage = 25;

        async function init() {
            await loadProducts();
            document.getElementById('profNama').value = storeProfile.nama_toko || '';
            document.getElementById('profWA').value = storeProfile.no_wa || '';
            document.getElementById('profAlamat').value = storeProfile.alamat || '';
            
            const initialName = encodeURIComponent(storeProfile.nama_toko || 'Toko');
            document.getElementById('profLogoPreview').src = storeProfile.logo ? storeProfile.logo : `https://ui-avatars.com/api/?name=${initialName}&background=10b981&color=fff`;
        }

        async function loadProducts(page = 1) {
            currentPage = page;
            const offset = (page - 1) * itemsPerPage;
            renderSkeletonCards(6);
            showPageLoading('Memuat dagangan...');
            try {
                const resp = await fetch(`api/get_produk_pasar.php?limit=${itemsPerPage}&offset=${offset}&penjual_nama=${encodeURIComponent(storeProfile.nama_toko)}`);
                const res = await resp.json();
                if(res.status === 'success') {
                    allProducts = res.data;
                    renderCards(allProducts);
                    updateStats(res.total, allProducts);
                    renderPagination(res.total);
                }
            } catch (e) {
                console.error(e);
                showPageToast('Gagal memuat data dagangan.', 'error');
            } finally {
                hidePageLoading();
            }
        }

        function renderSkeletonCards(count = 6) {
            const container = document.getElementById('productContainer');
            if (!container) return;
            container.innerHTML = Array.from({ length: count }).map(() => `
                <div class="bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4 animate-pulse">
                    <div class="w-20 h-20 rounded-2xl smart-skeleton"></div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="h-3 w-2/3 rounded-lg smart-skeleton"></div>
                        <div class="h-2.5 w-1/3 rounded-lg smart-skeleton"></div>
                        <div class="h-2.5 w-1/2 rounded-lg smart-skeleton"></div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="w-9 h-9 rounded-xl smart-skeleton"></div>
                        <div class="w-9 h-9 rounded-xl smart-skeleton"></div>
                    </div>
                </div>
            `).join('');
        }

        function renderEmptyFilterState(query = '') {
            const container = document.getElementById('productContainer');
            if (!container) return;
            const safeQuery = (query || '').replace(/[<>]/g, '');
            container.innerHTML = `
                <div class="col-span-full text-center py-16 text-slate-400 font-medium bg-white rounded-[2rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3">
                    <div class="w-24 h-24 rounded-full smart-skeleton flex items-center justify-center">
                        <i class="fas fa-search text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-sm text-slate-600 font-extrabold">Tidak ada hasil untuk \"${safeQuery}\"</p>
                    <p class="text-xs text-slate-400 font-semibold">Coba kata kunci lain, nama produk, atau kategori.</p>
                </div>
            `;
        }

        function setSearchPending(isPending) {
            const el = document.getElementById('searchPending');
            if (!el) return;
            el.classList.toggle('active', !!isPending);
            el.setAttribute('aria-hidden', isPending ? 'false' : 'true');
        }

        function bindProductImageSkeletons() {
            document.querySelectorAll('.js-product-photo').forEach((img) => {
                const shell = img.closest('.product-thumb-shell');
                const card = img.closest('.group');
                const textShell = card ? card.querySelector('.js-product-text-shell') : null;
                if (!shell) return;
                if (img.complete) {
                    shell.classList.add('loaded');
                    if (textShell) textShell.classList.add('loaded');
                    return;
                }
                img.addEventListener('load', () => {
                    shell.classList.add('loaded');
                    if (textShell) textShell.classList.add('loaded');
                }, { once: true });
                img.addEventListener('error', () => {
                    shell.classList.add('loaded');
                    if (textShell) textShell.classList.add('loaded');
                }, { once: true });
            });
        }

        function renderPagination(total) {
            const container = document.getElementById('paginationContainer');
            const totalPages = Math.ceil(total / itemsPerPage);
            
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            if (currentPage > 1) {
                html += `<button onclick="loadProducts(${currentPage - 1})" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-all"><i class="fas fa-chevron-left text-xs"></i></button>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button onclick="loadProducts(${i})" class="w-10 h-10 rounded-xl text-xs font-black transition-all ${i === currentPage ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="text-slate-300">...</span>`;
                }
            }

            if (currentPage < totalPages) {
                html += `<button onclick="loadProducts(${currentPage + 1})" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-all"><i class="fas fa-chevron-right text-xs"></i></button>`;
            }

            container.innerHTML = html;
        }

        function renderCards(data) {
            const container = document.getElementById('productContainer');
            if(data.length === 0) {
                container.innerHTML = `<div class="col-span-full text-center py-20 text-slate-400 font-medium bg-white rounded-[2rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3"><i class="fas fa-box-open text-4xl text-slate-200"></i> Belum ada dagangan.</div>`;
                return;
            }

            container.innerHTML = data.map(p => {
                let photos = [];
                try { photos = JSON.parse(p.foto); } catch(e) { if(p.foto) photos = [p.foto]; }
                const mainPhoto = photos.length > 0 ? photos[0] : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=200';

                return `
                <div class="bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4 group hover:shadow-xl hover:shadow-emerald-500/5 transition-all">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-100 shrink-0 product-thumb-shell">
                        <img src="${mainPhoto}" class="w-full h-full object-cover object-center js-product-photo" loading="lazy" decoding="async">
                    </div>
                    <div class="flex-1 min-w-0 product-text-shell js-product-text-shell">
                        <div class="product-text-skeleton">
                            <div class="line smart-skeleton" style="width: 72%;"></div>
                            <div class="line smart-skeleton" style="width: 38%;"></div>
                            <div class="line smart-skeleton" style="width: 56%;"></div>
                        </div>
                        <h4 class="font-extrabold text-slate-800 text-sm mb-0.5 truncate">${p.nama_produk}</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">${p.kategori}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-emerald-600">Rp ${new Intl.NumberFormat('id-ID').format(p.harga)}</span>
                            <span class="px-2 py-0.5 rounded-lg text-[8px] font-black tracking-widest uppercase ${p.status === 'Tersedia' ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500'}">${p.status}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button onclick="editProduct(${p.id})" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-500 transition-all border border-slate-100">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button onclick="deleteProduct(${p.id})" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all border border-slate-100">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>`;
            }).join('');

            bindProductImageSkeletons();
        }

        function updateStats(total, currentData) {
            document.getElementById('stat-total').innerText = total;
            // For simplicity, we'll just show "Aktif" for the current page or we can hit another API. 
            // But usually total is enough for the overview.
            document.getElementById('stat-aktif').innerText = currentData.filter(p => p.status === 'Tersedia').length;
        }

        function filterProducts() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            clearTimeout(filterTick);

            if (q === lastFilterQuery) return;
            lastFilterQuery = q;

            if (!q.trim()) {
                setSearchPending(false);
                renderCards(allProducts);
                return;
            }

            const delay = getAdaptiveFilterDelay(q);
            setSearchPending(true);
            if (delay >= 130) renderSkeletonCards(3);

            filterTick = setTimeout(() => {
                const filtered = allProducts.filter(p => p.nama_produk.toLowerCase().includes(q) || p.kategori.toLowerCase().includes(q));
                if (filtered.length === 0) {
                    renderEmptyFilterState(q);
                } else {
                    renderCards(filtered);
                }
                setSearchPending(false);
            }, delay);
        }

        function handlePhotoSelect(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    selectedPhotos.push(file);
                    renderPreviews();
                };
                reader.readAsDataURL(file);
            });
            e.target.value = ''; // Reset input
        }

        function renderPreviews() {
            const container = document.getElementById('imagePreviewContainer');
            const addButton = container.querySelector('label');
            
            // Clear existing previews but keep add button
            container.querySelectorAll('.preview-item').forEach(el => el.remove());

            // Preview existing photos (from server)
            existingPhotos.forEach((url, index) => {
                const div = document.createElement('div');
                div.className = 'preview-item w-24 h-24 rounded-3xl relative border border-slate-100 overflow-hidden';
                div.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover">
                    <button onclick="removeExistingPhoto(${index})" class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500/80 backdrop-blur-md text-white rounded-full flex items-center justify-center text-[10px]"><i class="fas fa-times"></i></button>
                `;
                container.insertBefore(div, addButton);
            });

            // Preview new selected photos
            selectedPhotos.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'preview-item w-24 h-24 rounded-3xl relative border border-slate-100 overflow-hidden';
                const reader = new FileReader();
                reader.onload = (e) => {
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button onclick="removeSelectedPhoto(${index})" class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500/80 backdrop-blur-md text-white rounded-full flex items-center justify-center text-[10px]"><i class="fas fa-times"></i></button>
                    `;
                };
                reader.readAsDataURL(file);
                container.insertBefore(div, addButton);
            });
        }

        function removeSelectedPhoto(i) { selectedPhotos.splice(i, 1); renderPreviews(); }
        function removeExistingPhoto(i) { existingPhotos.splice(i, 1); renderPreviews(); }

        function openModal() {
            document.getElementById('productForm').reset();
            document.getElementById('prodId').value = '';
            document.getElementById('prodPenjual').value = storeProfile.nama_toko || '';
            document.getElementById('prodWA').value = storeProfile.no_wa || '';
            document.getElementById('modalTitle').innerText = 'Tambah Dagangan';
            document.getElementById('productModal').classList.replace('hidden', 'flex');
            selectedPhotos = [];
            existingPhotos = [];
            renderPreviews();
        }

        function closeModal() {
            document.getElementById('productModal').classList.replace('flex', 'hidden');
        }

        function editProduct(id) {
            const p = allProducts.find(x => x.id == id);
            if(!p) return;
            openModal();
            document.getElementById('prodId').value = p.id;
            document.getElementById('prodNama').value = p.nama_produk;
            document.getElementById('prodHarga').value = Number(p.harga);
            document.getElementById('prodKategori').value = p.kategori;
            document.getElementById('prodPenjual').value = p.penjual_nama;
            document.getElementById('prodWA').value = p.no_wa;
            document.getElementById('prodDesc').value = p.deskripsi;
            document.getElementById('prodStatus').value = p.status;
            document.getElementById('prodStatusCheck').checked = (p.status === 'Tersedia');
            document.getElementById('modalTitle').innerText = 'Edit Dagangan';
            
            try { existingPhotos = JSON.parse(p.foto) || []; } catch(e) { existingPhotos = p.foto ? [p.foto] : []; }
            renderPreviews();
        }

        async function saveProduct() {
            const btn = document.getElementById('btnSave');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
            btn.disabled = true;
            showPageLoading('Menyimpan dagangan...');

            try {
                // 1. Upload new photos if any
                let newPhotoUrls = [];
                if (selectedPhotos.length > 0) {
                    const fd = new FormData();
                    selectedPhotos.forEach(file => fd.append('fotos[]', file));
                    const upResp = await fetch('views/pages/upload_produk.php', { method: 'POST', body: fd });
                    const upRes = await upResp.json();
                    if(upRes.status === 'success') newPhotoUrls = upRes.data;
                }

                // 2. Combine with existing photos
                const finalPhotos = [...existingPhotos, ...newPhotoUrls];
                document.getElementById('prodFotoHidden').value = JSON.stringify(finalPhotos);

                // 3. Save Product Data
                const form = document.getElementById('productForm');
                const fdSave = new FormData(form);
                const saveResp = await fetch('views/pages/save_produk.php', { method: 'POST', body: fdSave });
                const saveRes = await saveResp.json();

                if(saveRes.status === 'success') {
                    showPageToast(saveRes.message || 'Dagangan berhasil disimpan.');
                    closeModal();
                    loadProducts(currentPage);
                } else { showPageToast(saveRes.message || 'Gagal menyimpan data.', 'error'); }
            } catch (e) { 
                console.error(e);
                showPageToast('Terjadi kesalahan sistem.', 'error');
            } finally {
                hidePageLoading();
                btn.innerHTML = '<i class="fas fa-check"></i> <span>Simpan</span>';
                btn.disabled = false;
            }
        }

        async function deleteProduct(id) {
            const result = await Swal.fire({
                title: 'Hapus Dagangan?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                borderRadius: '2rem',
                customClass: { popup: 'smart-modal' }
            });
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('id', id);
                showPageLoading('Menghapus dagangan...');
                try {
                    const resp = await fetch('views/pages/delete_produk.php', { method: 'POST', body: fd });
                    const res = await resp.json();
                    if(res.status === 'success') {
                        loadProducts(currentPage);
                        showPageToast('Dagangan berhasil dihapus.');
                    }
                } catch (e) { showPageToast('Gagal menghapus data.', 'error'); }
                finally { hidePageLoading(); }
            }
        }

        async function saveProfile() {
            const fd = new FormData(document.getElementById('profileForm'));
            showPageLoading('Menyimpan profil toko...');
            try {
                const resp = await fetch('views/pages/update_toko.php', { method: 'POST', body: fd });
                const res = await resp.json();
                if(res.status === 'success') {
                    showPageToast(res.message || 'Profil berhasil disimpan.');
                    closeProfileModal();
                    window.location.reload();
                }
            } catch (e) {
                showPageToast('Gagal menyimpan profil.', 'error');
            } finally {
                hidePageLoading();
            }
        }

        function openProfileModal() { document.getElementById('profileModal').classList.replace('hidden', 'flex'); }
        function closeProfileModal() { document.getElementById('profileModal').classList.replace('flex', 'hidden'); }

        window.onload = init;
    </script>
</body>
</html>
