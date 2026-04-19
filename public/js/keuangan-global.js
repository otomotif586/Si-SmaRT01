// --- GLOBAL KEUANGAN (JURNAL KAS RT) LOGIC ---
window.keuanganData = [];
window.filteredKeuanganData = [];
window.keuanganPage = 1;
const keuanganItemsPerPage = 15;

function smartAssetUrl(path) {
    if (!path) return '';
    if (/^(?:https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) return path;
    const basePath = (window.__SMART_ASSET_BASE_PATH__ || '').replace(/\/$/, '');
    const cleanedPath = String(path).replace(/^\/+/, '');
    if (!basePath) return cleanedPath;
    return `${basePath}/${cleanedPath}`;
}

function initKeuanganGlobal() {
    const selBulan = document.getElementById('filter-bulan-keuangan');
    const selTahun = document.getElementById('filter-tahun-keuangan');
    
    if (selBulan && selBulan.options.length === 0) {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        months.forEach((m, i) => {
            const opt = document.createElement('option');
            opt.value = i;
            opt.text = m;
            selBulan.appendChild(opt);
        });

        const now = new Date();
        const currentYear = now.getFullYear();
        for (let y = currentYear; y >= currentYear - 3; y--) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.text = y;
            selTahun.appendChild(opt);
        }

        selBulan.value = now.getMonth();
        selTahun.value = currentYear;
    }

    loadKeuangan();
}

function loadKeuangan() {
    const container = document.getElementById('keuangan-list-container');
    if (!container) return;
    container.innerHTML = '<p class="text-center text-secondary py-5"><i data-lucide="loader"></i> Memuat data keuangan...</p>';
    lucide.createIcons();
    
    fetch('api/get_keuangan.php').then(r => r.json())
    .then(res => {
        if(res.status === 'success') { window.keuanganData = res.data || []; filterKeuangan(); } 
        else { container.innerHTML = `<p class="text-red text-center py-5">${res.message}</p>`; }
    }).catch(e => {
        // Fallback jika API belum dibuat, Tampilkan Form Kosong
        window.keuanganData = [];
        filterKeuangan();
        container.innerHTML = `<p class="text-secondary text-center py-5">Tabel Jurnal Keuangan belum memiliki data. Silakan catat transaksi baru.</p>`;
    });
}

function filterKeuangan() {
    const q = document.getElementById('keuangan-search').value.toLowerCase();
    const j = document.getElementById('keuangan-jenis').value;
    const bulan = parseInt(document.getElementById('filter-bulan-keuangan').value);
    const tahun = parseInt(document.getElementById('filter-tahun-keuangan').value);

    let saldo = 0; let pemasukan = 0; let pengeluaran = 0;

    // Kalkulasi Saldo Global (Berdasarkan semua transaksi kas yang ada)
    window.keuanganData.forEach(t => {
        if (t.jenis === 'Masuk') saldo += parseFloat(t.nominal); else saldo -= parseFloat(t.nominal);
    });

    window.filteredKeuanganData = window.keuanganData.filter(t => {
        let matchQ = t.keterangan.toLowerCase().includes(q);
        let matchJ = j === '' || t.jenis === j;
        
        const tDate = new Date(t.tanggal);
        let matchDate = !isNaN(tDate) && (tDate.getMonth() === bulan && tDate.getFullYear() === tahun);

        if (matchQ && matchJ && matchDate) {
            if (t.jenis === 'Masuk') pemasukan += parseFloat(t.nominal); else pengeluaran += parseFloat(t.nominal);
            return true;
        }
        return false;
    });

    // Sorting data yang tampil (Terbaru paling atas)
    window.filteredKeuanganData.sort((a,b) => new Date(b.tanggal) - new Date(a.tanggal) || b.id - a.id);

    document.getElementById('keuangan-saldo').innerText = 'Rp ' + parseInt(saldo).toLocaleString('id-ID');
    document.getElementById('keuangan-pemasukan').innerText = 'Rp ' + parseInt(pemasukan).toLocaleString('id-ID');
    document.getElementById('keuangan-pengeluaran').innerText = 'Rp ' + parseInt(pengeluaran).toLocaleString('id-ID');

    window.keuanganPage = 1;
    renderKeuangan();
}

function renderKeuangan() {
    const container = document.getElementById('keuangan-list-container');
    const pagination = document.getElementById('keuangan-pagination');
    const pageInfo = document.getElementById('keuangan-page-info');
    const total = window.filteredKeuanganData.length;
    
    if (total === 0) {
        container.innerHTML = '<div class="glass-card" style="padding: 40px; text-align: center;"><i data-lucide="file-x" style="width: 48px; height: 48px; color: var(--text-secondary-color); opacity: 0.5; margin-bottom: 16px;"></i><p class="text-secondary">Tidak ada transaksi ditemukan.</p></div>';
        pagination.style.display = 'none'; lucide.createIcons(); return;
    }

    const totalPages = Math.ceil(total / keuanganItemsPerPage);
    const startIdx = (window.keuanganPage - 1) * keuanganItemsPerPage;
    const endIdx = Math.min(startIdx + keuanganItemsPerPage, total);
    const paginatedData = window.filteredKeuanganData.slice(startIdx, endIdx);

    pagination.style.display = total > keuanganItemsPerPage ? 'flex' : 'none';
    if(pageInfo) pageInfo.innerText = `Menampilkan ${startIdx + 1} - ${endIdx} dari ${total} data`;

    let html = '';
    paginatedData.forEach(t => {
        const isMasuk = t.jenis === 'Masuk';
        const colorClass = isMasuk ? 'text-emerald' : 'text-red';
        const bgClass = isMasuk ? 'bg-emerald-light' : 'bg-red-light';
        const icon = isMasuk ? 'arrow-down-left' : 'arrow-up-right';
        const sign = isMasuk ? '+' : '-';
        const nominalStr = 'Rp ' + parseInt(t.nominal).toLocaleString('id-ID');
        const tgl = new Date(t.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        const colorHex = isMasuk ? '#10b981' : '#ef4444';
        
        const docNum = t.doc_number || '-';
        let postingTime = '-';
        if (t.created_at) {
            const d = new Date(t.created_at);
            postingTime = d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
        }

        const attachIcon = t.lampiran ? `<span style="display: flex; align-items: center; gap: 4px; color: var(--accent-color);" title="Ada Lampiran Bukti"><i data-lucide="paperclip" style="width: 12px; height: 12px;"></i> Lampiran</span>` : '';
        const detailBtn = `<button onclick="detailKeuangan(${t.id})" class="button-secondary button-sm" style="border-radius: 8px; padding: 6px 12px; font-size: 0.75rem;" title="Lihat Detail Transaksi"><i data-lucide="eye" style="width: 14px; height: 14px; margin-right: 4px;"></i> Detail</button>`;

        html += `
            <div class="transaction-item-modern glass-card" style="border-left: 4px solid ${colorHex}; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; transition: transform 0.3s, box-shadow 0.3s;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 280px;">
                    <div class="${bgClass} ${colorClass}" style="width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="${icon}" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h4 style="margin: 0; font-size: 1rem; color: var(--text-color); font-weight: 600; line-height: 1.4;">${t.keterangan}</h4>
                        <div style="display: flex; gap: 12px; margin-top: 6px; font-size: 0.75rem; color: var(--text-secondary-color); flex-wrap: wrap;">
                            <span style="display: flex; align-items: center; gap: 4px;" title="Nomor Dokumen Jurnal"><i data-lucide="hash" style="width: 12px; height: 12px;"></i> ${docNum}</span>
                            <span style="display: flex; align-items: center; gap: 4px;" title="Tanggal Kejadian (Kwitansi)"><i data-lucide="calendar" style="width: 12px; height: 12px;"></i> ${tgl}</span>
                            <span style="display: flex; align-items: center; gap: 4px;" title="Waktu Diposting ke Sistem"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> Posting: ${postingTime}</span>
                            ${attachIcon}
                        </div>
                    </div>
                </div>
                <div class="transaction-actions" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div style="text-align: right; min-width: 120px;">
                        <h3 class="${colorClass}" style="margin: 0; font-size: 1.1rem; font-weight: 700;">${sign} ${nominalStr}</h3>
                    </div>
                    ${detailBtn}
                    <div style="display: flex; gap: 6px;">
                        ${t.source_type === 'iuran_warga' ? `<button onclick="reclassKeuangan(${t.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: #f59e0b; border: none; background: rgba(245, 158, 11, 0.1);" title="Reclass (Kembalikan ke Iuran)"><i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i></button>` : ''}
                        <button onclick="editKeuangan(${t.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: var(--text-color); border: none; background: var(--hover-bg);"><i data-lucide="edit-2" style="width: 16px; height: 16px;"></i></button>
                        <button onclick="hapusKeuangan(${t.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: #ef4444; border: none; background: rgba(239, 68, 68, 0.1);"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                    </div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html; lucide.createIcons();

    // Render Page Numbers
    const pageNumbers = document.getElementById('keuangan-page-numbers');
    if (pageNumbers) {
        pageNumbers.innerHTML = '';
        const maxVisible = 5;
        let startPage = Math.max(1, window.keuanganPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className = i === window.keuanganPage ? 'button-primary button-sm' : 'button-secondary button-sm';
            btn.style.padding = '8px 12px';
            btn.style.borderRadius = '8px';
            btn.onclick = () => { window.keuanganPage = i; renderKeuangan(); };
            pageNumbers.appendChild(btn);
        }
    }
}

function prevKeuanganPage() { if (window.keuanganPage > 1) { window.keuanganPage--; renderKeuangan(); } }
function nextKeuanganPage() { const max = Math.ceil(window.filteredKeuanganData.length / keuanganItemsPerPage); if (window.keuanganPage < max) { window.keuanganPage++; renderKeuangan(); } }

function prevMonthKeuangan() {
    const selBulan = document.getElementById('filter-bulan-keuangan');
    const selTahun = document.getElementById('filter-tahun-keuangan');
    if (selBulan.selectedIndex > 0) { selBulan.selectedIndex--; filterKeuangan(); } 
    else if (selTahun.selectedIndex < selTahun.options.length - 1) { selBulan.selectedIndex = 11; selTahun.selectedIndex++; filterKeuangan(); }
}

function nextMonthKeuangan() {
    const selBulan = document.getElementById('filter-bulan-keuangan');
    const selTahun = document.getElementById('filter-tahun-keuangan');
    if (selBulan.selectedIndex < 11) { selBulan.selectedIndex++; filterKeuangan(); } 
    else if (selTahun.selectedIndex > 0) { selBulan.selectedIndex = 0; selTahun.selectedIndex--; filterKeuangan(); }
}
function updateFormKeuanganStatus() {
    const jenis = document.querySelector('input[name="form_keuangan_jenis"]:checked').value;
    const badge = document.getElementById('form-keuangan-status-badge');
    const drawerBody = document.querySelector('#drawer-keuangan .drawer-body');
    
    if (badge) {
        badge.classList.remove('badge-pop');
        void badge.offsetWidth; // Memaksa browser me-render ulang animasi CSS
        
        if (jenis === 'Masuk') {
            badge.className = 'badge bg-emerald-light text-emerald badge-pop';
            badge.innerHTML = '<i data-lucide="arrow-down-left" style="width: 12px; height: 12px; display: inline;"></i> Pemasukan';
            if (drawerBody) drawerBody.style.backgroundColor = 'color-mix(in srgb, var(--accent-color) 8%, transparent)';
        } else {
            badge.className = 'badge bg-red-light text-red badge-pop';
            badge.innerHTML = '<i data-lucide="arrow-up-right" style="width: 12px; height: 12px; display: inline;"></i> Pengeluaran';
            if (drawerBody) drawerBody.style.backgroundColor = 'rgba(239, 68, 68, 0.08)';
        }
        lucide.createIcons();
    }
}

function openFormKeuangan() {
    document.getElementById('form-keuangan-id').value = 0; document.getElementById('form-keuangan-nominal').value = '';
    document.getElementById('form-keuangan-tanggal').value = getLocalDateString(); document.getElementById('form-keuangan-keterangan').value = '';
    document.getElementById('form-keuangan-lampiran').value = ''; document.getElementById('form-keuangan-lampiran-preview').innerHTML = '';
    document.querySelector('input[name="form_keuangan_jenis"][value="Masuk"]').checked = true;
    document.getElementById('drawer-keuangan-title').innerText = 'Catat Transaksi';
    updateFormKeuanganStatus();
    const drawer = document.getElementById('drawer-keuangan'); drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
}

function closeFormKeuangan() {
    const drawer = document.getElementById('drawer-keuangan'); drawer.classList.remove('drawer-active');
    setTimeout(() => drawer.classList.add('hidden'), 400);
}

function editKeuangan(id) {
    const t = window.keuanganData.find(x => x.id == id); if (!t) return;
    document.getElementById('form-keuangan-id').value = t.id; document.getElementById('form-keuangan-nominal').value = t.nominal;
    document.getElementById('form-keuangan-tanggal').value = t.tanggal; document.getElementById('form-keuangan-keterangan').value = t.keterangan;
    document.querySelector(`input[name="form_keuangan_jenis"][value="${t.jenis}"]`).checked = true;
    document.getElementById('form-keuangan-lampiran').value = '';
    document.getElementById('form-keuangan-lampiran-preview').innerHTML = t.lampiran ? `<div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--hover-bg); border-radius: 8px; border: 1px solid var(--border-color);"><span style="font-size: 0.75rem; color: var(--text-color);"><i data-lucide="paperclip" style="width: 14px; height: 14px; margin-right: 4px; display: inline;"></i> Tersimpan</span><a href="${smartAssetUrl(t.lampiran)}" target="_blank" style="font-size: 0.75rem; color: var(--accent-color);">Lihat</a></div>` : '';
    document.getElementById('drawer-keuangan-title').innerText = 'Edit Transaksi';
    updateFormKeuanganStatus();
    const drawer = document.getElementById('drawer-keuangan'); drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50); lucide.createIcons();
}

function simpanKeuangan() {
    const btn = document.querySelector('#drawer-keuangan .button-primary'); const origText = btn.innerHTML;
    const nominal = document.getElementById('form-keuangan-nominal').value; const tanggal = document.getElementById('form-keuangan-tanggal').value;
    const keterangan = document.getElementById('form-keuangan-keterangan').value;
    
    if (!nominal || !tanggal || !keterangan) { alert("Mohon lengkapi Nominal, Tanggal, dan Keterangan."); return; }
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    const fd = new FormData();
    fd.append('id', document.getElementById('form-keuangan-id').value); fd.append('jenis', document.querySelector('input[name="form_keuangan_jenis"]:checked').value);
    fd.append('nominal', nominal); fd.append('tanggal', tanggal); fd.append('keterangan', keterangan);
    const fileInput = document.getElementById('form-keuangan-lampiran'); if (fileInput.files.length > 0) fd.append('lampiran', fileInput.files[0]);
    
    fetch('api/simpan_keuangan.php', { method: 'POST', body: fd }).then(r => r.json())
    .then(res => { if (res.status === 'success') { closeFormKeuangan(); loadKeuangan(); } else alert('Gagal: ' + res.message); btn.innerHTML = origText; })
    .catch(e => { alert('Error koneksi.'); btn.innerHTML = origText; });
}

function hapusKeuangan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/hapus_keuangan.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') loadKeuangan(); else alert('Gagal: ' + res.message); });
    }
}

function lihatBuktiKeuangan(url) {
    const isImage = url.match(/\.(jpeg|jpg|gif|png)$/i) != null;
    const content = document.getElementById('bukti-keuangan-content');
    if (isImage) {
        content.innerHTML = `<img src="${smartAssetUrl(url)}" style="max-width: 100%; max-height: 70vh; border-radius: 12px; display: block; margin: 0 auto;">`;
    } else {
        content.innerHTML = `<div style="padding: 40px; background: var(--hover-bg); border-radius: 12px; margin-bottom: 16px;"><i data-lucide="file-text" style="width: 48px; height: 48px; color: var(--text-secondary-color); margin: 0 auto 16px auto; display: block;"></i><p>Dokumen PDF/Word. Silakan unduh atau lihat di tab baru.</p></div><a href="${smartAssetUrl(url)}" target="_blank" class="button-primary" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; border-radius: 12px;"><i data-lucide="external-link" style="margin-right: 8px;"></i> Buka di Tab Baru</a>`;
    }
    document.getElementById('modal-bukti-keuangan').classList.remove('hidden');
    lucide.createIcons();
}

function detailKeuangan(id) {
    const t = window.keuanganData.find(x => x.id == id);
    if (!t) return;
    
    const isMasuk = t.jenis === 'Masuk';
    const colorClass = isMasuk ? 'text-emerald' : 'text-red';
    const sign = isMasuk ? '+' : '-';
    const nominalStr = 'Rp ' + parseInt(t.nominal).toLocaleString('id-ID');
    const tgl = new Date(t.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    
    let postingTime = '-';
    if (t.created_at) {
        const d = new Date(t.created_at);
        postingTime = d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    }

    let lampiranHtml = '';
    if (t.lampiran) {
        const isImage = t.lampiran.match(/\.(jpeg|jpg|gif|png)$/i) != null;
        if (isImage) {
            lampiranHtml = `<div style="margin-top: 16px;"><p class="card-label">Lampiran Bukti</p><img src="${smartAssetUrl(t.lampiran)}" style="max-width: 100%; border-radius: 12px; border: 1px solid var(--border-color); margin-top: 8px;"></div>`;
        } else {
            lampiranHtml = `<div style="margin-top: 16px;"><p class="card-label">Lampiran Bukti</p><a href="${smartAssetUrl(t.lampiran)}" target="_blank" class="button-secondary" style="display: inline-flex; width: 100%; justify-content: center; border-radius: 12px; margin-top: 8px;"><i data-lucide="external-link" style="margin-right: 8px;"></i> Buka Dokumen Lampiran</a></div>`;
        }
    } else {
        lampiranHtml = `<div style="margin-top: 16px; padding: 16px; background: var(--hover-bg); border-radius: 12px; text-align: center; border: 1px dashed var(--border-color);"><p class="text-secondary" style="margin: 0; font-size: 0.85rem;">Tidak ada lampiran dokumen</p></div>`;
    }

    const html = `
        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; color: var(--text-color);">
            <div style="text-align: center; padding: 20px; background: rgba(128,128,128,0.05); border-radius: 16px; margin-bottom: 8px; border: 1px dashed var(--border-color);">
                <p class="text-secondary" style="margin: 0 0 4px 0; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Nominal ${t.jenis}</p>
                <h2 class="${colorClass}" style="margin: 0; font-size: 2rem; font-weight: 800;">${sign} ${nominalStr}</h2>
            </div>
            
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span class="text-secondary">Status Transaksi</span>
                <span class="font-bold ${colorClass}"><i data-lucide="${isMasuk ? 'arrow-down-left' : 'arrow-up-right'}" style="width:14px; height:14px; display:inline;"></i> ${t.jenis}</span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span class="text-secondary">Nomor Dokumen</span>
                <span class="font-bold">${t.doc_number || '-'}</span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span class="text-secondary">Tanggal Kejadian</span>
                <span class="font-bold">${tgl}</span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span class="text-secondary">Waktu Posting Sistem</span>
                <span class="font-bold">${postingTime}</span>
            </div>
            
            <div style="margin-top: 8px;">
                <p class="card-label">Keterangan Transaksi</p>
                <p style="margin: 8px 0 0 0; line-height: 1.6; background: var(--input-bg); padding: 16px; border-radius: 12px; border: 1px solid var(--input-border); white-space: pre-wrap;">${t.keterangan}</p>
            </div>
            
            ${lampiranHtml}
        </div>
    `;
    
    document.getElementById('detail-keuangan-content').innerHTML = html;
    document.getElementById('modal-detail-keuangan').classList.remove('hidden');
    lucide.createIcons();
}

function reclassKeuangan(id) {
    Swal.fire({
        title: 'Reclass Transaksi?',
        text: "Ini akan menghapus catatan jurnal ini dan mengembalikan status iuran terkait menjadi 'Tervalidasi' (siap untuk diposting ulang).",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Reclass Sekarang',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.querySelector(`button[onclick="reclassKeuangan(${id})"]`);
            if(btn) btn.disabled = true;
            
            showToast('Memproses reclass...', 'info');
            
            const fd = new FormData();
            fd.append('id', id);

            fetch('api/reclass_jurnal_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(res.message);
                    loadKeuangan();
                    // Juga refresh laporan iuran blok jika fungsinya ada (untuk sinkronisasi antar halaman)
                    if (typeof loadLaporanIuranBlok === 'function') {
                        loadLaporanIuranBlok();
                    }
                } else {
                    showToast('Gagal: ' + res.message, 'error');
                    if(btn) btn.disabled = false;
                }
            })
            .catch(e => {
                showToast('Terjadi kesalahan koneksi.', 'error');
                if(btn) btn.disabled = false;
            });
        }
    });
}
