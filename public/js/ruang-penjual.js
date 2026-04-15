let allProducts = [];
let storeProfile = window.storeProfile || {};
let selectedPhotos = [];
let existingPhotos = [];
let pageLoadingTick;
let filterTick;
let lastFilterAt = 0;
let lastFilterQuery = '';
let currentPage = 1;
const itemsPerPage = 25;

function getAdaptiveFilterDelay(query) {
    const now = Date.now();
    const gap = now - lastFilterAt;
    lastFilterAt = now;

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
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => document.getElementById('profLogoPreview').src = ev.target.result;
        reader.readAsDataURL(file);
    }
}

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
        if (res.status === 'success') {
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
    if (data.length === 0) {
        container.innerHTML = `<div class="col-span-full text-center py-20 text-slate-400 font-medium bg-white rounded-[2rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center gap-3"><i class="fas fa-box-open text-4xl text-slate-200"></i> Belum ada dagangan.</div>`;
        return;
    }

    container.innerHTML = data.map((p) => {
        let photos = [];
        try { photos = JSON.parse(p.foto); } catch (e) { if (p.foto) photos = [p.foto]; }
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
    document.getElementById('stat-aktif').innerText = currentData.filter((p) => p.status === 'Tersedia').length;
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
        const filtered = allProducts.filter((p) => p.nama_produk.toLowerCase().includes(q) || p.kategori.toLowerCase().includes(q));
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
    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = () => {
            selectedPhotos.push(file);
            renderPreviews();
        };
        reader.readAsDataURL(file);
    });
    e.target.value = '';
}

function renderPreviews() {
    const container = document.getElementById('imagePreviewContainer');
    const addButton = container.querySelector('label');

    container.querySelectorAll('.preview-item').forEach((el) => el.remove());

    existingPhotos.forEach((url, index) => {
        const div = document.createElement('div');
        div.className = 'preview-item w-24 h-24 rounded-3xl relative border border-slate-100 overflow-hidden';
        div.innerHTML = `
            <img src="${url}" class="w-full h-full object-cover">
            <button onclick="removeExistingPhoto(${index})" class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500/80 backdrop-blur-md text-white rounded-full flex items-center justify-center text-[10px]"><i class="fas fa-times"></i></button>
        `;
        container.insertBefore(div, addButton);
    });

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
    const p = allProducts.find((x) => x.id == id);
    if (!p) return;
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

    try { existingPhotos = JSON.parse(p.foto) || []; } catch (e) { existingPhotos = p.foto ? [p.foto] : []; }
    renderPreviews();
}

async function saveProduct() {
    const btn = document.getElementById('btnSave');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
    btn.disabled = true;
    showPageLoading('Menyimpan dagangan...');

    try {
        let newPhotoUrls = [];
        if (selectedPhotos.length > 0) {
            const fd = new FormData();
            selectedPhotos.forEach((file) => fd.append('fotos[]', file));
            const upResp = await fetch('views/pages/upload_produk.php', { method: 'POST', body: fd });
            const upRes = await upResp.json();
            if (upRes.status === 'success') newPhotoUrls = upRes.data;
        }

        const finalPhotos = [...existingPhotos, ...newPhotoUrls];
        document.getElementById('prodFotoHidden').value = JSON.stringify(finalPhotos);

        const form = document.getElementById('productForm');
        const fdSave = new FormData(form);
        const saveResp = await fetch('views/pages/save_produk.php', { method: 'POST', body: fdSave });
        const saveRes = await saveResp.json();

        if (saveRes.status === 'success') {
            showPageToast(saveRes.message || 'Dagangan berhasil disimpan.');
            closeModal();
            loadProducts(currentPage);
        } else {
            showPageToast(saveRes.message || 'Gagal menyimpan data.', 'error');
        }
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
        text: 'Data tidak bisa dikembalikan!',
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
            if (res.status === 'success') {
                loadProducts(currentPage);
                showPageToast('Dagangan berhasil dihapus.');
            }
        } catch (e) {
            showPageToast('Gagal menghapus data.', 'error');
        } finally {
            hidePageLoading();
        }
    }
}

async function saveProfile() {
    const fd = new FormData(document.getElementById('profileForm'));
    showPageLoading('Menyimpan profil toko...');
    try {
        const resp = await fetch('views/pages/update_toko.php', { method: 'POST', body: fd });
        const res = await resp.json();
        if (res.status === 'success') {
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
