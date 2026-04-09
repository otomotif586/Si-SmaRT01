// --- GLOBAL WARGA (DIRECTORY) LOGIC ---
window.allBloks = [];
window.globalWargaData = [];
window.globalWargaPage = 1;
const globalWargaItemsPerPage = 15;

function loadAllBloks() {
    fetch('api/get_bloks.php').then(r=>r.json()).then(res => {
        window.allBloks = res.data || [];
        let options = '<option value="">-- Pilih Blok --</option>';
        let filterOptions = '<option value="">Semua Blok</option>';
        window.allBloks.forEach(b => {
            options += `<option value="${b.id}">${b.nama_blok}</option>`;
            filterOptions += `<option value="${b.id}">${b.nama_blok}</option>`;
        });
        const formSelect = document.getElementById('warga_blok_id');
        if(formSelect) formSelect.innerHTML = options;
        const filterSelect = document.getElementById('filter-blok-global');
        if(filterSelect) filterSelect.innerHTML = filterOptions;
    });
}

function loadGlobalWarga() {
    const container = document.getElementById('global-warga-list-container');
    if(!container) return;
    container.innerHTML = '<p class="text-center text-secondary py-4">Memuat direktori warga...</p>';
    window.globalWargaPage = 1;
    
    fetch(`api/get_global_warga.php`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            window.globalWargaData = res.data; filterGlobalWargaList();
        } else container.innerHTML = `<p class="text-red text-center py-4">${res.message}</p>`;
    });
}

function filterGlobalWargaList() {
    if (!window.globalWargaData) return;
    const q = document.getElementById('search-global-warga-input').value.toLowerCase();
    const fB = document.getElementById('filter-blok-global').value;
    const fP = document.getElementById('filter-pernikahan-global').value;
    const fS = document.getElementById('filter-status-global').value;

    const filtered = window.globalWargaData.filter(w => {
        const matchQ = (w.nama_lengkap && w.nama_lengkap.toLowerCase().includes(q)) || (w.nik && w.nik.toLowerCase().includes(q)) || (w.nik_kepala && w.nik_kepala.toLowerCase().includes(q));
        const matchB = fB === '' || w.blok_id == fB;
        const matchP = fP === '' || w.status_pernikahan === fP;
        const matchS = fS === '' || w.status_kependudukan === fS;
        return matchQ && matchB && matchP && matchS;
    });

    // Extract Unique Blocks
    const uniqueBloks = [...new Set(filtered.map(item => item.blok_id))];
    
    document.getElementById('sum-global-warga').innerText = filtered.length;
    document.getElementById('sum-global-blok').innerText = uniqueBloks.length + ' Blok';
    document.getElementById('sum-global-tetap').innerText = filtered.filter(w => w.status_kependudukan === 'Tetap').length;
    document.getElementById('sum-global-kontrak').innerText = filtered.filter(w => w.status_kependudukan === 'Kontrak').length;

    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / globalWargaItemsPerPage);
    if (window.globalWargaPage > totalPages && totalPages > 0) window.globalWargaPage = totalPages;
    if (window.globalWargaPage < 1) window.globalWargaPage = 1;

    const startIndex = (window.globalWargaPage - 1) * globalWargaItemsPerPage;
    const paginated = filtered.slice(startIndex, Math.min(startIndex + globalWargaItemsPerPage, totalItems));

    const pageContainer = document.getElementById('global-warga-pagination');
    if (totalItems > 0) { pageContainer.style.display = 'flex'; document.getElementById('global-warga-page-info').innerText = `Menampilkan ${startIndex + 1}-${Math.min(startIndex + globalWargaItemsPerPage, totalItems)} dari ${totalItems}`; } 
    else { pageContainer.style.display = 'none'; }

    renderGlobalWargaList(paginated);
}

function renderGlobalWargaList(data) {
    const container = document.getElementById('global-warga-list-container');
    if (data.length === 0) { container.innerHTML = '<p class="text-secondary text-center py-4">Belum ada data warga ditemukan.</p>'; return; }

    let html = '<div class="warga-grid">';
    data.forEach(w => {
        let sK = w.status_kependudukan || '-';
        let statusClass = sK === 'Kontrak' ? 'bg-orange-light text-orange' : (sK === 'Weekend' ? 'bg-purple-light text-purple' : 'bg-emerald-light text-emerald');
        let waLink = w.no_wa ? `<a href="https://wa.me/${w.no_wa.replace(/\D/g, '').startsWith('0') ? '62'+w.no_wa.replace(/\D/g, '').substring(1) : w.no_wa.replace(/\D/g, '')}" target="_blank" class="button-secondary" style="border-radius: 12px; padding: 8px 16px; color: #25D366; border-color: transparent; background: rgba(37, 211, 102, 0.1); font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: none;" title="Chat WA"><i data-lucide="message-circle" style="width: 16px; height: 16px;"></i> Chat WA</a>` : '';
        let bDetail = `<button onclick="showDetailWarga(${w.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: var(--text-color); border-color: transparent; background: rgba(128,128,128,0.1); box-shadow: none;" title="Detail Lengkap Warga"><i data-lucide="eye" style="width: 18px; height: 18px;"></i></button>`;
        let bE = `<button onclick="editWarga(${w.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: var(--accent-color); border-color: transparent; background: color-mix(in srgb, var(--accent-color) 10%, transparent); box-shadow: none;" title="Edit Data"><i data-lucide="edit-2" style="width: 18px; height: 18px;"></i></button>`;
        let bD = `<button onclick="hapusWarga(${w.id}, '${w.nama_lengkap}')" class="button-secondary" style="border-radius: 12px; padding: 8px; color: #ef4444; border-color: transparent; background: rgba(239, 68, 68, 0.1); box-shadow: none;" title="Hapus"><i data-lucide="trash-2" style="width: 18px; height: 18px;"></i></button>`;

        html += `<div class="warga-card glass-card" style="padding: 24px;"><div class="warga-card-header"><div class="avatar bg-blue-light text-blue" style="width: 48px; height: 48px; font-size: 1.2rem;">${w.nama_lengkap.charAt(0)}</div><div style="flex: 1; overflow: hidden;"><h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">${w.nama_lengkap}</h4><div style="display:flex; gap:6px; margin-top:6px; flex-wrap:wrap;"><span class="badge bg-secondary-light text-secondary" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(128,128,128,0.1);"><i data-lucide="map" style="width: 12px; height: 12px;"></i> ${w.nama_blok || 'Blok ?'}</span><span class="badge ${statusClass}" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;"><i data-lucide="${sK === 'Tetap' ? 'user-check' : 'user'}" style="width: 12px; height: 12px;"></i> ${sK}</span></div></div></div><div class="warga-card-body"><div class="warga-detail-item"><i data-lucide="map-pin"></i> <span>No. Rumah: ${w.nomor_rumah || '-'}</span></div><div class="warga-detail-item"><i data-lucide="credit-card"></i> <span>NIK: ${w.nik_kepala || w.nik || '-'}</span></div><div class="warga-detail-item"><i data-lucide="user-check"></i> <span>Pernikahan: ${w.status_pernikahan || 'Lajang'}</span></div></div><div class="warga-card-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; border-top: 1px dashed var(--border-color); padding-top: 16px;"><div>${waLink}</div><div class="warga-action-group">${bDetail}${bE}${bD}</div></div></div>`;
    });
    html += '</div>'; container.innerHTML = html; lucide.createIcons();
}

function prevPageGlobalWarga() { if (window.globalWargaPage > 1) { window.globalWargaPage--; filterGlobalWargaList(); } }
function nextPageGlobalWarga() { window.globalWargaPage++; filterGlobalWargaList(); }
