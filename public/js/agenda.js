// --- Agenda & Laporan ---
window.currentAgendaData = [];
window.currentLaporanData = [];
window.currentAgendaPage = 1;
const agendaItemsPerPage = 15;
window.currentLaporanPage = 1;
const laporanItemsPerPage = 15;

function initAgendaLaporan() {
    // Reset ke tab pertama
    const firstSubTab = document.querySelector('.sub-nav-tab');
    if (firstSubTab) switchSubTab(firstSubTab, 'sub-tab-agenda');

    loadAgendaData();
    loadLaporanData();
}

function loadAgendaData() {
    const container = document.getElementById('agenda-list-container');
    if(container) container.innerHTML = '<p class="text-center text-secondary py-4">Memuat agenda...</p>';
    window.currentAgendaPage = 1;
    
    fetch(`api/get_agenda.php?blok_id=${window.currentBlokId}`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            window.currentAgendaData = res.data;
            filterAgendaList();
        } else {
            if(container) container.innerHTML = `<p class="text-red text-center py-4">${res.message}</p>`;
        }
    });
}

function filterAgendaList() {
    if (!window.currentAgendaData) return;
    
    const q = document.getElementById('search-agenda-input').value.toLowerCase();
    const fStatus = document.getElementById('filter-status-agenda').value;

    const filtered = window.currentAgendaData.filter(a => {
        const matchQ = (a.judul && a.judul.toLowerCase().includes(q)) || 
                       (a.keterangan && a.keterangan.toLowerCase().includes(q));
        const matchS = fStatus === '' || a.status === fStatus;
        return matchQ && matchS;
    });

    // Update Kartu Ringkasan Agenda
    document.getElementById('sum-agenda-total').innerText = filtered.length;
    document.getElementById('sum-agenda-selesai').innerText = filtered.filter(a => a.status === 'Selesai').length + ' Selesai';

    // Pagination Agenda
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / agendaItemsPerPage);
    if (window.currentAgendaPage > totalPages && totalPages > 0) window.currentAgendaPage = totalPages;
    if (window.currentAgendaPage < 1) window.currentAgendaPage = 1;

    const startIndex = (window.currentAgendaPage - 1) * agendaItemsPerPage;
    const endIndex = Math.min(startIndex + agendaItemsPerPage, totalItems);
    const paginatedItems = filtered.slice(startIndex, endIndex);

    const paginationContainer = document.getElementById('agenda-pagination');
    if (totalItems > 0) {
        paginationContainer.style.display = 'flex';
        document.getElementById('agenda-page-info').innerText = `Menampilkan ${startIndex + 1}-${endIndex} dari ${totalItems}`;
    } else {
        paginationContainer.style.display = 'none';
    }

    renderAgendaList(paginatedItems);
}

function prevPageAgenda() {
    if (window.currentAgendaPage > 1) { window.currentAgendaPage--; filterAgendaList(); }
}

function nextPageAgenda() {
    window.currentAgendaPage++; filterAgendaList();
}

function loadLaporanData() {
    const container = document.getElementById('laporan-list-container');
    if(container) container.innerHTML = '<p class="text-center text-secondary py-4">Memuat laporan...</p>';
    window.currentLaporanPage = 1;
    
    fetch(`api/get_laporan.php?blok_id=${window.currentBlokId}`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            window.currentLaporanData = res.data;
            filterLaporanList();
        } else {
            if(container) container.innerHTML = `<p class="text-red text-center py-4">${res.message}</p>`;
        }
    });
}

function filterLaporanList() {
    if (!window.currentLaporanData) return;
    
    const q = document.getElementById('search-laporan-input').value.toLowerCase();
    const fStatus = document.getElementById('filter-status-laporan').value;

    const filtered = window.currentLaporanData.filter(l => {
        const matchQ = (l.judul_laporan && l.judul_laporan.toLowerCase().includes(q)) || 
                       (l.keterangan && l.keterangan.toLowerCase().includes(q));
        const matchS = fStatus === '' || l.status === fStatus;
        return matchQ && matchS;
    });

    // Update Kartu Ringkasan Laporan
    document.getElementById('sum-laporan-total').innerText = filtered.length;
    document.getElementById('sum-laporan-selesai').innerText = filtered.filter(l => l.status === 'Selesai').length + ' Selesai';

    // Pagination Laporan
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / laporanItemsPerPage);
    if (window.currentLaporanPage > totalPages && totalPages > 0) window.currentLaporanPage = totalPages;
    if (window.currentLaporanPage < 1) window.currentLaporanPage = 1;

    const startIndex = (window.currentLaporanPage - 1) * laporanItemsPerPage;
    const endIndex = Math.min(startIndex + laporanItemsPerPage, totalItems);
    const paginatedItems = filtered.slice(startIndex, endIndex);

    const paginationContainer = document.getElementById('laporan-pagination');
    if (totalItems > 0) {
        paginationContainer.style.display = 'flex';
        document.getElementById('laporan-page-info').innerText = `Menampilkan ${startIndex + 1}-${endIndex} dari ${totalItems}`;
    } else {
        paginationContainer.style.display = 'none';
    }

    renderLaporanList(paginatedItems);
}

function prevPageLaporan() {
    if (window.currentLaporanPage > 1) { window.currentLaporanPage--; filterLaporanList(); }
}

function nextPageLaporan() {
    window.currentLaporanPage++; filterLaporanList();
}

function switchSubTab(element, tabId) {
    document.querySelectorAll('.sub-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sub-nav-tab').forEach(el => el.classList.remove('active'));

    document.getElementById(tabId).classList.remove('hidden');
    element.classList.add('active');
}

function renderAgendaList(agendas) {
    const container = document.getElementById('agenda-list-container');
    if(agendas.length === 0) {
        container.innerHTML = '<div class="glass-card" style="padding: 32px; text-align: center;"><i data-lucide="calendar" style="width: 48px; height: 48px; margin: 0 auto 16px auto; color: var(--text-secondary-color);"></i><p>Belum ada agenda kegiatan.</p></div>';
        lucide.createIcons();
        return;
    }

    let html = '<div style="display: flex; flex-direction: column; gap: 16px;">';
    agendas.forEach(a => {
        const isSelesai = a.status === 'Selesai';
        const statusClass = isSelesai ? 'bg-emerald-light text-emerald' : (a.status === 'Dibatalkan' ? 'bg-red-light text-red' : 'bg-blue-light text-blue');
        const tgl = new Date(a.tanggal_kegiatan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

        let galleryHtml = '';
        if (isSelesai && a.gallery && a.gallery.length > 0) {
            galleryHtml += '<div class="agenda-gallery">';
            const galData = encodeURIComponent(JSON.stringify(a.gallery));
            a.gallery.forEach(img => {
                const isVideo = img.match(/\.(mp4|webm|ogg)$/i) != null;
                if (isVideo) {
                    galleryHtml += `<div class="gallery-item" style="position: relative; overflow: hidden; cursor: pointer; border-radius: 12px;" onclick="openGsapGallery(this)" data-gallery="${galData}">
                        <video src="${img}" style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;" muted></video>
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); pointer-events: none;"><i data-lucide="play-circle" style="color: white; width: 24px; height: 24px;"></i></div>
                    </div>`;
                } else {
                    galleryHtml += `<img src="${img}" class="gallery-item" data-gallery="${galData}" onclick="openGsapGallery(this)">`;
                }
            });
            galleryHtml += '</div>';
        }

        let lampiranHtml = '';
        if (a.lampiran && a.lampiran.length > 0) {
            lampiranHtml += '<div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px;">';
            a.lampiran.forEach(doc => {
                lampiranHtml += `<a href="${doc.file_path}" target="_blank" class="document-item" style="padding: 6px 12px; background: var(--hover-bg); border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; width: fit-content; text-decoration: none; border: 1px solid var(--border-color);"><i data-lucide="paperclip" style="width: 14px; height: 14px; color: var(--text-secondary-color);"></i><span style="font-size: 0.75rem; color: var(--text-color);">${doc.file_name}</span></a>`;
            });
            lampiranHtml += '</div>';
        }

        html += `
            <div class="agenda-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color);">${a.judul}</h4>
                        <p class="text-secondary" style="font-size: 0.8rem; margin-top: 4px;">${tgl}</p>
                    </div>
                    <span class="badge ${statusClass}">${a.status}</span>
                </div>
                <p class="text-secondary" style="font-size: 0.875rem; margin: 12px 0; white-space: pre-wrap;">${a.keterangan}</p>
                ${lampiranHtml}
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed var(--border-color); padding-top: 12px; margin-top: 8px;">
                    <span style="font-size: 0.8rem; color: var(--text-secondary-color);">Estimasi Biaya: <b class="text-color">Rp ${parseInt(a.biaya_estimasi).toLocaleString('id-ID')}</b></span>
                    <div class="warga-action-group">
                        <button onclick="editAgenda(${a.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: var(--text-secondary-color); border-color: transparent; background: var(--hover-bg); box-shadow: none;" title="Edit"><i data-lucide="edit-2" style="width: 16px; height: 16px;"></i></button>
                        <button onclick="hapusAgenda(${a.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: #ef4444; border-color: transparent; background: rgba(239, 68, 68, 0.1); box-shadow: none;" title="Hapus"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                    </div>
                </div>
                ${galleryHtml}
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons();
}

function renderLaporanList(laporans) {
    const container = document.getElementById('laporan-list-container');
    if(laporans.length === 0) {
        container.innerHTML = '<div class="glass-card" style="padding: 32px; text-align: center;"><i data-lucide="flag" style="width: 48px; height: 48px; margin: 0 auto 16px auto; color: var(--text-secondary-color);"></i><p>Belum ada laporan masalah.</p></div>';
        lucide.createIcons();
        return;
    }

    let html = '<div style="display: flex; flex-direction: column; gap: 16px;">';
    laporans.forEach(l => {
        let statusClass = 'bg-blue-light text-blue';
        if (l.status === 'Diproses') statusClass = 'bg-orange-light text-orange';
        else if (l.status === 'Selesai') statusClass = 'bg-emerald-light text-emerald';

        let lampiranHtml = '';
        if (l.lampiran && l.lampiran.length > 0) {
            lampiranHtml += '<div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px;">';
            l.lampiran.forEach(doc => {
                const isImage = doc.file_name.match(/\.(jpeg|jpg|gif|png)$/i) != null;
                if (isImage) {
                    lampiranHtml += `<a href="${doc.file_path}" target="_blank" style="display: inline-block; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;"><img src="${doc.file_path}" style="height: 60px; width: 60px; object-fit: cover;"></a>`;
                } else {
                    lampiranHtml += `<a href="${doc.file_path}" target="_blank" class="document-item" style="padding: 6px 12px; background: var(--hover-bg); border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; width: fit-content; text-decoration: none; border: 1px solid var(--border-color);"><i data-lucide="paperclip" style="width: 14px; height: 14px; color: var(--text-secondary-color);"></i><span style="font-size: 0.75rem; color: var(--text-color);">${doc.file_name}</span></a>`;
                }
            });
            lampiranHtml += '</div>';
        }
        
        let tglSelesaiHtml = '';
        if (l.status === 'Selesai' && l.tanggal_selesai) {
            tglSelesaiHtml = `<p class="text-emerald" style="font-size: 0.8rem; margin-top: 4px; font-weight: 600;"><i data-lucide="check-circle" style="width: 12px; height: 12px; display: inline;"></i> Selesai pada: ${new Date(l.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>`;
        }

        html += `
            <div class="laporan-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color);">${l.judul_laporan}</h4>
                    <span class="badge ${statusClass}">${l.status}</span>
                </div>
                <p class="text-secondary" style="font-size: 0.8rem; margin-top: 4px;">Dilaporkan pada: ${new Date(l.tanggal_laporan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                ${tglSelesaiHtml}
                <p class="text-secondary" style="font-size: 0.875rem; margin: 12px 0; white-space: pre-wrap;">${l.keterangan}</p>
                ${lampiranHtml}
                <div style="display: flex; justify-content: flex-end; align-items: center; border-top: 1px dashed var(--border-color); padding-top: 12px; margin-top: 8px;">
                    <div class="warga-action-group">
                        <button onclick="editLaporan(${l.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: var(--text-secondary-color); border-color: transparent; background: var(--hover-bg); box-shadow: none;" title="Edit"><i data-lucide="edit-2" style="width: 16px; height: 16px;"></i></button>
                        <button onclick="hapusLaporan(${l.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: #ef4444; border-color: transparent; background: rgba(239, 68, 68, 0.1); box-shadow: none;" title="Hapus"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons();
}

function openFormAgenda() {
    // Cek tab mana yang sedang aktif
    const isAgenda = document.getElementById('sub-tab-agenda').classList.contains('hidden') === false;
    if (isAgenda) {
        openFormAgendaDrawer();
    } else {
        openFormLaporanDrawer();
    }
}

// Fungsi pembantu format datetime-local
function formatDateTimeLocal(dtStr) {
    if(!dtStr) return '';
    let dt = dtStr.replace(' ', 'T');
    if(dt.length > 16) dt = dt.substring(0, 16);
    return dt;
}

// Drawer Agenda
function openFormAgendaDrawer() {
    document.getElementById('agenda_id').value = 0;
    document.getElementById('agenda_judul').value = '';
    document.getElementById('agenda_tanggal').value = '';
    document.getElementById('agenda_biaya').value = '';
    document.getElementById('agenda_keterangan').value = '';
    document.getElementById('agenda_status').value = 'Direncanakan';
    document.getElementById('agenda_gallery_files').value = '';
    document.getElementById('agenda_lampiran_files').value = '';
    document.getElementById('agenda_existing_gallery').innerHTML = '';
    document.getElementById('agenda_gallery_preview').innerHTML = '';
    document.getElementById('agenda_existing_lampiran').innerHTML = '';
    toggleAgendaGallery('Direncanakan');
    
    document.getElementById('drawer-agenda-title').innerText = 'Tambah Agenda';
    
    const drawer = document.getElementById('drawer-agenda');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
}

function closeFormAgendaDrawer() {
    const drawer = document.getElementById('drawer-agenda');
    drawer.classList.remove('drawer-active');
    setTimeout(() => drawer.classList.add('hidden'), 400);
}

function previewAgendaGallery(input) {
    const preview = document.getElementById('agenda_gallery_preview');
    preview.innerHTML = '';
    if(input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const isVideo = file.type.startsWith('video/');
                const el = isVideo 
                    ? `<video src="${e.target.result}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" muted></video>`
                    : `<img src="${e.target.result}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">`;
                preview.innerHTML += `<div style="position: relative; display: inline-block; animation: pageFadeIn 0.3s forwards;">${el}</div>`;
            }
            reader.readAsDataURL(file);
        });
    }
}

function toggleAgendaGallery(status) {
    const sec = document.getElementById('agenda_gallery_section');
    if (status === 'Selesai') sec.classList.remove('hidden');
    else sec.classList.add('hidden');
}

function editAgenda(id) {
    const a = window.currentAgendaData.find(x => x.id == id);
    if(!a) return;
    
    document.getElementById('agenda_id').value = a.id;
    document.getElementById('agenda_judul').value = a.judul;
    document.getElementById('agenda_tanggal').value = formatDateTimeLocal(a.tanggal_kegiatan);
    document.getElementById('agenda_biaya').value = a.biaya_estimasi;
    document.getElementById('agenda_keterangan').value = a.keterangan;
    document.getElementById('agenda_status').value = a.status;
    document.getElementById('agenda_gallery_files').value = '';
    document.getElementById('agenda_lampiran_files').value = '';
    document.getElementById('agenda_gallery_preview').innerHTML = '';
    
    let galHtml = '';
    if(a.gallery) {
        a.gallery.forEach(img => {
            const isVideo = img.match(/\.(mp4|webm|ogg)$/i) != null;
            const mediaEl = isVideo ? `<video src="${img}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" muted></video>` : `<img src="${img}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">`;
            galHtml += `<div style="position: relative; display: inline-block;">
                ${mediaEl}
                <button type="button" onclick="hapusFotoAgenda(${a.id}, '${img}', this)" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; display:flex; align-items:center; justify-content:center; font-size:10px;"><i data-lucide="x" style="width: 12px; height: 12px;"></i></button>
            </div>`;
        });
    }
    document.getElementById('agenda_existing_gallery').innerHTML = galHtml;

    let lampHtml = '';
    if(a.lampiran) {
        a.lampiran.forEach(doc => {
            lampHtml += `<div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; background: var(--hover-bg); border-radius: 8px; border: 1px solid var(--border-color);"><span style="font-size: 0.75rem; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%;">${doc.file_name}</span><button type="button" onclick="hapusLampiranAgenda(${a.id}, '${doc.file_path}', this)" style="color: #ef4444; border: none; background: transparent; cursor: pointer; padding: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button></div>`;
        });
    }
    document.getElementById('agenda_existing_lampiran').innerHTML = lampHtml;

    lucide.createIcons();
    
    toggleAgendaGallery(a.status);
    document.getElementById('drawer-agenda-title').innerText = 'Edit Agenda';
    
    const drawer = document.getElementById('drawer-agenda');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
}

function simpanAgenda() {
    const btn = document.querySelector('#drawer-agenda .button-primary');
    const origText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    
    const fd = new FormData();
    fd.append('id', document.getElementById('agenda_id').value);
    fd.append('blok_id', window.currentBlokId);
    fd.append('judul', document.getElementById('agenda_judul').value);
    fd.append('tanggal_kegiatan', document.getElementById('agenda_tanggal').value.replace('T', ' ') + ':00');
    fd.append('biaya_estimasi', document.getElementById('agenda_biaya').value);
    fd.append('keterangan', document.getElementById('agenda_keterangan').value);
    fd.append('status', document.getElementById('agenda_status').value);
    
    const files = document.getElementById('agenda_gallery_files').files;
    for(let i=0; i<files.length; i++) {
        fd.append('gallery[]', files[i]);
    }

    const lampiranFiles = document.getElementById('agenda_lampiran_files').files;
    for(let i=0; i<lampiranFiles.length; i++) {
        fd.append('lampiran[]', lampiranFiles[i]);
    }
    
    fetch('api/simpan_agenda.php', { method: 'POST', body: fd })
    .then(r=>r.json())
    .then(res => {
        if(res.status === 'success') {
            closeFormAgendaDrawer();
            loadAgendaData();
        } else {
            alert('Gagal: ' + res.message);
        }
        btn.innerHTML = origText;
    }).catch(e => { alert('Error: ' + e.message); btn.innerHTML = origText; });
}

function hapusAgenda(id) {
    if(confirm('Hapus agenda ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/hapus_agenda.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status === 'success') loadAgendaData();
        });
    }
}

function hapusFotoAgenda(agenda_id, file_path, btnEl) {
    if(confirm('Hapus foto ini?')) {
        btnEl.innerHTML = '...';
        const fd = new FormData(); fd.append('agenda_id', agenda_id); fd.append('file_path', file_path);
        fetch('api/hapus_foto_agenda.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status === 'success') {
                btnEl.parentElement.remove();
                const a = window.currentAgendaData.find(x => x.id == agenda_id);
                if(a && a.gallery) {
                    a.gallery = a.gallery.filter(g => g !== file_path);
                }
                loadAgendaData();
            }
        });
    }
}

function hapusLampiranAgenda(agenda_id, file_path, btnEl) {
    if(confirm('Hapus lampiran ini?')) {
        btnEl.innerHTML = '...';
        const fd = new FormData(); fd.append('agenda_id', agenda_id); fd.append('file_path', file_path);
        fetch('api/hapus_lampiran_agenda.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status === 'success') {
                btnEl.parentElement.remove();
                loadAgendaData();
            } else {
                alert("Gagal menghapus: " + res.message);
                btnEl.innerHTML = '<i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>';
                lucide.createIcons();
            }
        });
    }
}

function toggleLaporanSelesai(status) {
    const sec = document.getElementById('laporan_tanggal_selesai_section');
    if (status === 'Selesai') sec.classList.remove('hidden');
    else sec.classList.add('hidden');
}

// Drawer Laporan
function openFormLaporanDrawer() {
    document.getElementById('laporan_id').value = 0;
    document.getElementById('laporan_judul').value = '';
    document.getElementById('laporan_tanggal').value = getLocalDateString() + 'T' + new Date().toTimeString().slice(0,5);
    document.getElementById('laporan_keterangan').value = '';
    document.getElementById('laporan_status').value = 'Baru';
    document.getElementById('laporan_tanggal_selesai').value = '';
    document.getElementById('laporan_lampiran_files').value = '';
    document.getElementById('laporan_existing_lampiran').innerHTML = '';
    toggleLaporanSelesai('Baru');
    
    document.getElementById('drawer-laporan-title').innerText = 'Buat Laporan';
    
    const drawer = document.getElementById('drawer-laporan');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
}

function closeFormLaporanDrawer() {
    const drawer = document.getElementById('drawer-laporan');
    drawer.classList.remove('drawer-active');
    setTimeout(() => drawer.classList.add('hidden'), 400);
}

function editLaporan(id) {
    const l = window.currentLaporanData.find(x => x.id == id);
    if(!l) return;
    
    document.getElementById('laporan_id').value = l.id;
    document.getElementById('laporan_judul').value = l.judul_laporan;
    document.getElementById('laporan_tanggal').value = formatDateTimeLocal(l.tanggal_laporan);
    document.getElementById('laporan_keterangan').value = l.keterangan;
    document.getElementById('laporan_status').value = l.status;
    document.getElementById('laporan_tanggal_selesai').value = formatDateTimeLocal(l.tanggal_selesai);
    document.getElementById('laporan_lampiran_files').value = '';

    let lampHtml = '';
    if(l.lampiran) {
        l.lampiran.forEach(doc => {
            lampHtml += `<div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; background: var(--hover-bg); border-radius: 8px; border: 1px solid var(--border-color);"><span style="font-size: 0.75rem; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%;">${doc.file_name}</span><button type="button" onclick="hapusLampiranLaporan(${l.id}, '${doc.file_path}', this)" style="color: #ef4444; border: none; background: transparent; cursor: pointer; padding: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button></div>`;
        });
    }
    document.getElementById('laporan_existing_lampiran').innerHTML = lampHtml;
    lucide.createIcons();

    toggleLaporanSelesai(l.status);
    
    document.getElementById('drawer-laporan-title').innerText = 'Edit Laporan';
    
    const drawer = document.getElementById('drawer-laporan');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
}

function simpanLaporan() {
    const btn = document.querySelector('#drawer-laporan .button-primary');
    const origText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    
    const fd = new FormData();
    fd.append('id', document.getElementById('laporan_id').value);
    fd.append('blok_id', window.currentBlokId);
    fd.append('judul', document.getElementById('laporan_judul').value);
    fd.append('tanggal_laporan', document.getElementById('laporan_tanggal').value.replace('T', ' ') + ':00');
    fd.append('keterangan', document.getElementById('laporan_keterangan').value);
    fd.append('status', document.getElementById('laporan_status').value);
    if (document.getElementById('laporan_status').value === 'Selesai' && document.getElementById('laporan_tanggal_selesai').value) {
        fd.append('tanggal_selesai', document.getElementById('laporan_tanggal_selesai').value.replace('T', ' ') + ':00');
    }
    const lampiranFiles = document.getElementById('laporan_lampiran_files').files;
    for(let i=0; i<lampiranFiles.length; i++) {
        fd.append('lampiran[]', lampiranFiles[i]);
    }
    
    fetch('api/simpan_laporan.php', { method: 'POST', body: fd })
    .then(r=>r.json())
    .then(res => {
        if(res.status === 'success') {
            closeFormLaporanDrawer();
            loadLaporanData();
        } else {
            alert('Gagal: ' + res.message);
        }
        btn.innerHTML = origText;
    }).catch(e => { alert('Error: ' + e.message); btn.innerHTML = origText; });
}

function hapusLaporan(id) {
    if(confirm('Hapus laporan ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/hapus_laporan.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status === 'success') loadLaporanData();
        });
    }
}

function hapusLampiranLaporan(laporan_id, file_path, btnEl) {
    if(confirm('Hapus lampiran ini?')) {
        btnEl.innerHTML = '...';
        const fd = new FormData(); fd.append('laporan_id', laporan_id); fd.append('file_path', file_path);
        fetch('api/hapus_lampiran_laporan.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status === 'success') {
                btnEl.parentElement.remove();
                loadLaporanData();
            } else {
                alert("Gagal menghapus: " + res.message);
                btnEl.innerHTML = '<i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>';
                lucide.createIcons();
            }
        });
    }
}
