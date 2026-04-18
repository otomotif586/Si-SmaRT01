window.initKeamanan = function() {
    if(typeof lucide !== 'undefined') lucide.createIcons();
    loadKeamananRingkasan();
}

window.kmIncidentPage = 1;
window.kmIncidentPerPage = 15;
window.kmIncidentTotalPages = 1;

window.switchKeamananTab = function(tabId, btnElement) {
    document.querySelectorAll('.km-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sub-nav-tab').forEach(el => el.classList.remove('active'));
    
    document.getElementById(tabId).classList.remove('hidden');
    if (btnElement) btnElement.classList.add('active');
    
    if (tabId === 'km-ringkasan') loadKeamananRingkasan();
    else if (tabId === 'km-master') loadMasterSatpam();
    else if (tabId === 'km-jadwal') loadJadwalSatpam();
    else if (tabId === 'km-laporan') loadLaporanKeamanan();
    else if (tabId === 'km-izin') loadIzinSatpam();
}

window.closeKmModal = function(id) {
    document.getElementById(id).classList.add('hidden');
}

// =========================================
// 0. LOAD RINGKASAN & PANIC BUTTON
// =========================================
window.loadKeamananRingkasan = function() {
    const guardEl = document.getElementById('km-current-guard');
    const reportEl = document.getElementById('km-unread-reports');
    const activityEl = document.getElementById('km-recent-activity');
    
    if (guardEl) guardEl.innerText = 'Memuat...';
    if (reportEl) reportEl.innerText = '-';
    if (activityEl) activityEl.innerHTML = '<p class="text-center text-secondary py-4">Memuat aktivitas...</p>';

    fetch('api/keamanan/get_ringkasan.php')
        .then(r => r.ok ? r.json() : {status:'error'})
        .then(res => {
            if (res.status === 'success') {
                if (guardEl) guardEl.innerText = (res.data.satpam_aktif || 0) + ' Personel';
                if (reportEl) reportEl.innerText = res.data.laporan_baru || 0;
                
                if (activityEl) {
                    let html = '';
                    if (res.data.aktifitas && res.data.aktifitas.length > 0) {
                        res.data.aktifitas.forEach(a => {
                            html += `<div style="padding: 12px; border-bottom: 1px dashed var(--border-color);"><h5 style="margin: 0 0 4px 0; color: var(--text-color);">${a.judul}</h5><p class="text-secondary" style="margin: 0; font-size: 0.8rem;"><i data-lucide="clock" style="width: 12px; height: 12px; display: inline;"></i> ${a.waktu}</p></div>`;
                        });
                    } else {
                        html = '<p class="text-center text-secondary py-4">Belum ada aktivitas terbaru.</p>';
                    }
                    activityEl.innerHTML = html;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            } else throw new Error('API Not Ready');
        }).catch(() => {
            // Fallback UI jika Endpoint API get_ringkasan.php belum dibuat
            if (guardEl) guardEl.innerText = '0 Personel';
            if (reportEl) reportEl.innerText = '0';
            if (activityEl) activityEl.innerHTML = '<p class="text-center text-secondary py-4">Belum ada data / API Ringkasan belum tersedia.</p>';
        });
}

// Load Kontak Darurat dari Local Storage
let panicContacts = JSON.parse(localStorage.getItem('panic_contacts')) || [
    { nama: 'Ketua RT', nomor: '081234567890' },
    { nama: 'Pos Satpam', nomor: '089876543210' }
];

window.openPanicSettings = function() {
    window.renderPanicNumbers();
    document.getElementById('modal-panic-settings').classList.remove('hidden');
}

window.renderPanicNumbers = function() {
    const container = document.getElementById('panic-numbers-container');
    container.innerHTML = '';
    
    if (panicContacts.length === 0) {
        container.innerHTML = '<p class="text-secondary text-center" style="font-size: 0.85rem;">Belum ada kontak darurat.</p>';
    }

    panicContacts.forEach((contact, index) => {
        container.innerHTML += `
            <div class="panic-contact-item" style="display: flex; gap: 8px; align-items: center;">
                <input type="text" class="input-field contact-nama" placeholder="Nama (Cth: Satpam)" value="${contact.nama}" style="flex: 1;">
                <input type="text" class="input-field contact-nomor" placeholder="No. WA (Cth: 0812...)" value="${contact.nomor}" style="flex: 1;">
                <button class="button-secondary" style="color: #ef4444; padding: 10px; border-radius: 12px; flex-shrink: 0;" onclick="removePanicNumber(${index})">
                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        `;
    });
    if(typeof lucide !== 'undefined') lucide.createIcons();
}

window.addPanicNumber = function() {
    panicContacts.push({ nama: '', nomor: '' });
    window.renderPanicNumbers();
}

window.removePanicNumber = function(index) {
    panicContacts.splice(index, 1);
    window.renderPanicNumbers();
}

window.savePanicSettings = function() {
    const items = document.querySelectorAll('.panic-contact-item');
    let newContacts = [];
    items.forEach(item => {
        const nama = item.querySelector('.contact-nama').value.trim();
        const nomor = item.querySelector('.contact-nomor').value.trim();
        if (nama || nomor) {
            newContacts.push({ nama, nomor });
        }
    });
    panicContacts = newContacts;
    localStorage.setItem('panic_contacts', JSON.stringify(panicContacts));
    closeKmModal('modal-panic-settings');
    if (typeof showToast === 'function') showToast("Kontak darurat berhasil disimpan.");
}

window.triggerPanic = function() {
    const listContainer = document.getElementById('panic-recipient-list');
    listContainer.innerHTML = '';

    if (panicContacts.length === 0) {
        listContainer.innerHTML = '<p class="text-secondary text-center col-span-full" style="width: 100%;">Belum ada kontak darurat yang diatur. Silakan atur di pengaturan.</p>';
    } else {
        panicContacts.forEach(contact => {
            let cleanWa = contact.nomor.replace(/\D/g, ''); 
            if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
            
            // Pre-filled WA message Darurat
            const msg = encodeURIComponent(`*🚨 SINYAL DARURAT (PANIC BUTTON) 🚨*\n\nHarap segera merespon atau menuju lokasi. Terdapat indikasi keadaan darurat yang dilaporkan melalui Sistem Keamanan.`);
            const waLink = `https://wa.me/${cleanWa}?text=${msg}`;

            listContainer.innerHTML += `
                <a href="${waLink}" target="_blank" class="glass-card" style="padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; transition: transform 0.2s; border-color: var(--border-color); background: rgba(128,128,128,0.05);" onmouseover="this.style.borderColor='var(--accent-color)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(37, 211, 102, 0.1); color: #25D366; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="phone-call" style="width: 24px; height: 24px;"></i>
                    </div>
                    <span style="font-weight: 700; color: var(--text-color); text-align: center;">${contact.nama}</span>
                    <span style="font-size: 0.75rem; color: var(--text-secondary-color);">${contact.nomor}</span>
                </a>
            `;
        });
    }
    
    if(typeof lucide !== 'undefined') lucide.createIcons();
    document.getElementById('modal-panic-broadcast').classList.remove('hidden');
}

// Helper untuk mengisi opsi Personel
window.populateSatpamSelect = function(selectId, selectedId = null) {
    const sel = document.getElementById(selectId);
    fetch('api/keamanan/get_satpam.php')
        .then(r => r.ok ? r.json() : {status:'error'})
        .then(res => {
            sel.innerHTML = '<option value="">-- Pilih Personel --</option>';
            if(res.status === 'success') {
                res.data.forEach(s => {
                    if(s.status === 'Aktif') {
                        const opt = document.createElement('option');
                        opt.value = s.id; opt.text = s.nama;
                        sel.appendChild(opt);
                    }
                });
                if(selectedId) sel.value = selectedId;
            }
        }).catch(e => { console.log('Silakan buat endpoint API get_satpam.php'); });
}

// =========================================
// 1. CRUD MASTER SATPAM
// =========================================
window.loadMasterSatpam = function() {
    const container = document.getElementById('km-guard-list');
    container.innerHTML = '<p class="text-secondary col-span-full text-center py-5">Memuat data personel...</p>';
    
    fetch('api/keamanan/get_satpam.php')
        .then(r => r.ok ? r.json() : {status:'error', message:'API Endpoint (get_satpam.php) Belum Tersedia.'})
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(s => {
                    const statusClass = s.status === 'Aktif' ? 'bg-emerald-light text-emerald' : 'bg-red-light text-red';
                    html += `
                    <div class="glass-card" style="padding: 20px; border-radius: 20px; display: flex; flex-direction: column; gap: 16px; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="report-avatar bg-blue-light text-blue" style="width: 48px; height: 48px; font-size: 1.2rem;">${s.nama.charAt(0)}</div>
                            <span class="badge ${statusClass}">${s.status}</span>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; color: var(--text-color);">${s.nama}</h3>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary-color);"><i data-lucide="phone" style="width: 14px; height: 14px; display: inline;"></i> ${s.no_hp || '-'}</p>
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: auto; border-top: 1px dashed var(--border-color); padding-top: 16px;">
                            <button class="button-secondary flex-1" style="padding: 8px; border-radius: 10px;" onclick="editSatpam(${s.id}, '${s.nama}', '${s.no_hp}', '${s.status}')"><i data-lucide="edit" style="width:16px; height:16px; margin-right:4px;"></i> Edit</button>
                            <button class="button-secondary" style="padding: 8px; border-radius: 10px; color: #ef4444;" onclick="deleteSatpam(${s.id})"><i data-lucide="trash-2" style="width:16px; height:16px;"></i></button>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                container.innerHTML = `<div class="col-span-full text-center py-8"><i data-lucide="users" style="width:48px; height:48px; color:var(--text-secondary-color); opacity:0.3; margin: 0 auto 16px auto;"></i><p class="text-secondary">${res.message || 'Belum ada data personel.'}</p></div>`;
            }
        }).catch(() => container.innerHTML = '<p class="text-red col-span-full text-center py-5">Gagal terhubung ke API Keamanan.</p>');
}

window.addSatpam = function() {
    document.getElementById('km-satpam-id').value = '0';
    document.getElementById('km-satpam-nama').value = '';
    document.getElementById('km-satpam-nohp').value = '';
    document.getElementById('km-satpam-status').value = 'Aktif';
    document.getElementById('modal-satpam-title').innerText = 'Tambah Personel';
    document.getElementById('modal-satpam').classList.remove('hidden');
}

window.editSatpam = function(id, nama, nohp, status) {
    document.getElementById('km-satpam-id').value = id;
    document.getElementById('km-satpam-nama').value = nama;
    document.getElementById('km-satpam-nohp').value = nohp;
    document.getElementById('km-satpam-status').value = status;
    document.getElementById('modal-satpam-title').innerText = 'Edit Personel';
    document.getElementById('modal-satpam').classList.remove('hidden');
}

window.saveSatpam = function() {
    const fd = new FormData();
    fd.append('id', document.getElementById('km-satpam-id').value);
    fd.append('nama', document.getElementById('km-satpam-nama').value);
    fd.append('no_hp', document.getElementById('km-satpam-nohp').value);
    fd.append('status', document.getElementById('km-satpam-status').value);

    showLoading('Menyimpan...');
    fetch('api/keamanan/simpan_satpam.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') { showToast(res.message); closeKmModal('modal-satpam'); loadMasterSatpam(); } 
            else showToast(res.message, 'error');
        }).catch(e => { showToast("Sistem backend API belum ditambahkan.", "info"); closeKmModal('modal-satpam'); });
}

window.deleteSatpam = function(id) {
    if(confirm('Hapus personel ini secara permanen?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/keamanan/hapus_satpam.php', { method: 'POST', body: fd })
            .then(r=>r.json()).then(res=>{
                if(res.status==='success') loadMasterSatpam();
                else showToast(res.message, 'error');
            });
    }
}

// =========================================
// 2. CRUD JADWAL SHIFT
// =========================================
window.loadJadwalSatpam = function() {
    const tbody = document.getElementById('km-schedule-body');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5">Memuat jadwal...</td></tr>';
    
    fetch('api/keamanan/get_jadwal.php')
        .then(r => r.ok ? r.json() : {status:'error', message:'API Endpoint Belum Tersedia.'})
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(j => {
                    html += `
                    <tr>
                        <td style="font-weight: 600;">${j.tanggal}</td>
                        <td>${j.shift === 'Pagi' ? j.nama_satpam : '-'}</td>
                        <td>${j.shift === 'Malam' ? j.nama_satpam : '-'}</td>
                        <td class="text-right">
                            <button class="button-secondary button-sm" style="color: #ef4444;" onclick="deleteJadwal(${j.id})"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-secondary">${res.message || 'Belum ada data jadwal shift.'}</td></tr>`;
            }
        });
}

window.addJadwal = function() {
    document.getElementById('km-jadwal-id').value = '0';
    document.getElementById('km-jadwal-tanggal').value = '';
    populateSatpamSelect('km-jadwal-satpam');
    document.getElementById('modal-jadwal').classList.remove('hidden');
}

window.saveJadwal = function() {
    const fd = new FormData();
    fd.append('id', document.getElementById('km-jadwal-id').value);
    fd.append('satpam_id', document.getElementById('km-jadwal-satpam').value);
    fd.append('tanggal', document.getElementById('km-jadwal-tanggal').value);
    fd.append('shift', document.getElementById('km-jadwal-shift').value);
    showLoading('Menyimpan...');
    fetch('api/keamanan/simpan_jadwal.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{
        if(res.status==='success') { showToast(res.message); closeKmModal('modal-jadwal'); loadJadwalSatpam(); } 
        else showToast(res.message, 'error'); 
    }).catch(e => showToast("API Belum Tersedia", "info"));
}

window.deleteJadwal = function(id) {
    if(confirm('Hapus jadwal ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/keamanan/hapus_jadwal.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{ if(res.status==='success') loadJadwalSatpam(); });
    }
}

// =========================================
// 3. CRUD LAPORAN KEAMANAN (INCIDENT)
// =========================================
window.loadLaporanKeamanan = function() {
    const tbody = document.getElementById('km-incident-body');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5">Memuat aduan dan informasi...</td></tr>';

    const paginationWrap = document.getElementById('km-incident-pagination');
    const paginationInfo = document.getElementById('km-incident-pagination-info');
    const prevBtn = document.getElementById('km-incident-prev');
    const nextBtn = document.getElementById('km-incident-next');

    fetch(`api/keamanan/get_laporan.php?page=${window.kmIncidentPage}&per_page=${window.kmIncidentPerPage}`)
        .then(r => r.ok ? r.json() : {status:'error', message:'API Endpoint Belum Tersedia.'})
        .then(res => {
            const pg = res.pagination || { page: 1, per_page: window.kmIncidentPerPage, total: 0, total_pages: 1 };
            window.kmIncidentPage = Number(pg.page || 1);
            window.kmIncidentTotalPages = Math.max(1, Number(pg.total_pages || 1));

            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(l => {
                    let statusClass = 'bg-secondary-light text-secondary';
                    if(l.status === 'Baru') statusClass = 'bg-red-light text-red';
                    if(l.status === 'Diproses') statusClass = 'bg-orange-light text-orange';
                    if(l.status === 'Selesai') statusClass = 'bg-emerald-light text-emerald';
                    const portalApproved = Number(l.approved_portal || 0) === 1;
                    const portalBadgeClass = portalApproved ? 'bg-emerald-light text-emerald' : 'bg-orange-light text-orange';
                    const portalBadgeText = portalApproved ? 'Disetujui' : 'Menunggu';
                    const nextApprove = portalApproved ? '0' : '1';
                    const approveBtnLabel = portalApproved ? 'Unpublish' : 'Approve';
                    
                    html += `
                    <tr>
                        <td data-label="Waktu" style="font-size:0.85rem;">${l.waktu_kejadian}</td>
                        <td data-label="Pelapor" style="font-weight:600;">${l.pelapor || 'Sistem'}</td>
                        <td data-label="Judul">${l.judul}</td>
                        <td data-label="Lokasi">${l.lokasi || '-'}</td>
                        <td data-label="Status"><span class="badge ${statusClass}" style="font-size:0.7rem;">${l.status}</span></td>
                        <td data-label="Portal"><span class="badge ${portalBadgeClass}" style="font-size:0.7rem;">${portalBadgeText}</span></td>
                        <td data-label="Aksi" class="text-right" style="display:flex; justify-content:flex-end; gap:8px;">
                            <button class="button-secondary button-sm" onclick="setPortalApproval(${l.id}, ${nextApprove})">${approveBtnLabel}</button>
                            <button class="button-secondary button-sm" onclick="viewDetailLaporan(${l.id}, '${encodeURIComponent(JSON.stringify(l))}')"><i data-lucide="eye" style="width:14px; height:14px;"></i></button>
                            <button class="button-secondary button-sm" onclick="editLaporan(${l.id}, '${l.judul}', '${l.waktu_kejadian}', '${l.lokasi}', '${l.status}', '${encodeURIComponent(l.deskripsi)}')"><i data-lucide="edit" style="width:14px; height:14px;"></i></button>
                            <button class="button-secondary button-sm" style="color: #ef4444;" onclick="deleteLaporan(${l.id})"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-secondary">${res.message || 'Belum ada aduan/informasi terbaru.'}</td></tr>`;
            }

            if (paginationWrap && paginationInfo && prevBtn && nextBtn) {
                const total = Number(pg.total || 0);
                const perPage = Number(pg.per_page || window.kmIncidentPerPage);
                const start = total === 0 ? 0 : ((window.kmIncidentPage - 1) * perPage) + 1;
                const end = Math.min(total, window.kmIncidentPage * perPage);
                paginationInfo.textContent = total > 0
                    ? `Menampilkan ${start}-${end} dari ${total} data`
                    : 'Belum ada data';
                paginationWrap.style.display = 'flex';
                prevBtn.disabled = window.kmIncidentPage <= 1;
                nextBtn.disabled = window.kmIncidentPage >= window.kmIncidentTotalPages;
            }
        });
}

window.changeKmIncidentPage = function(step) {
    const nextPage = window.kmIncidentPage + Number(step || 0);
    if (nextPage < 1 || nextPage > window.kmIncidentTotalPages) {
        return;
    }
    window.kmIncidentPage = nextPage;
    loadLaporanKeamanan();
}

window.setPortalApproval = function(id, approved) {
    const fd = new FormData();
    fd.append('id', String(id));
    fd.append('approved_portal', String(approved));
    fetch('api/keamanan/simpan_laporan.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                if (typeof showToast === 'function') showToast(res.message || 'Status portal diperbarui.');
                loadLaporanKeamanan();
                loadKeamananRingkasan();
            } else if (typeof showToast === 'function') {
                showToast(res.message || 'Gagal memperbarui status portal.', 'error');
            }
        })
        .catch(() => {
            if (typeof showToast === 'function') showToast('Gagal terhubung ke API persetujuan portal.', 'error');
        });
}

window.addIncident = function() {
    document.getElementById('km-lap-id').value = '0';
    document.getElementById('km-lap-judul').value = '';
    
    // Auto-fill form waktu kejadian dengan waktu saat ini sesuai zona waktu perangkat
    const now = new Date();
    const localISO = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
    document.getElementById('km-lap-waktu').value = localISO;
    
    document.getElementById('km-lap-lokasi').value = '';
    document.getElementById('km-lap-deskripsi').value = '';
    document.getElementById('km-lap-file').value = '';
    document.getElementById('km-lap-status').value = 'Baru';
    document.getElementById('modal-lap-title').innerText = 'Info/Aduan Baru';
    document.getElementById('modal-lap-keamanan').classList.remove('hidden');
}

window.editLaporan = function(id, judul, waktu, lokasi, status, descEncoded) {
    document.getElementById('km-lap-id').value = id;
    document.getElementById('km-lap-judul').value = judul;
    document.getElementById('km-lap-waktu').value = waktu;
    document.getElementById('km-lap-lokasi').value = lokasi;
    document.getElementById('km-lap-status').value = status;
    document.getElementById('km-lap-deskripsi').value = decodeURIComponent(descEncoded);
    document.getElementById('km-lap-file').value = '';
    document.getElementById('modal-lap-title').innerText = 'Edit Info/Aduan';
    document.getElementById('modal-lap-keamanan').classList.remove('hidden');
}

window.viewDetailLaporan = function(id, dataStr) {
    const l = JSON.parse(decodeURIComponent(dataStr));
    let statusClass = 'bg-secondary-light text-secondary';
    if(l.status === 'Baru') statusClass = 'bg-red-light text-red';
    if(l.status === 'Diproses') statusClass = 'bg-orange-light text-orange';
    if(l.status === 'Selesai') statusClass = 'bg-emerald-light text-emerald';

    const content = `
        <div style="margin-bottom: 16px;">
            <span class="badge ${statusClass}" style="margin-bottom: 12px; display: inline-block;">Status: ${l.status}</span>
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 8px 0; color: var(--text-color);">${l.judul}</h2>
            <div style="display: flex; gap: 16px; font-size: 0.85rem; color: var(--text-secondary-color); margin-bottom: 16px;">
                <span style="display: flex; align-items: center; gap: 4px;"><i data-lucide="clock" style="width:14px; height:14px;"></i> Waktu: ${l.waktu_kejadian}</span>
                <span style="display: flex; align-items: center; gap: 4px;"><i data-lucide="map-pin" style="width:14px; height:14px;"></i> Lokasi: ${l.lokasi || '-'}</span>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-secondary-color);">Dilaporkan Oleh: <strong>${l.pelapor || 'Sistem'}</strong></p>
            <p style="font-size: 0.85rem; color: var(--text-secondary-color);">Portal: <strong>${Number(l.approved_portal || 0) === 1 ? 'Disetujui Tayang' : 'Belum Disetujui'}</strong></p>
        </div>
        <div style="background: var(--hover-bg); padding: 16px; border-radius: 16px; border: 1px solid var(--border-color); margin-bottom: 24px;">
            <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0 0 8px 0;">Deskripsi Kejadian:</h4>
            <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: var(--text-color);">${l.deskripsi || 'Tidak ada deskripsi rinci.'}</p>
        </div>
        ${l.lampiran_path ? `<div style="margin-bottom: 20px;"><a href="${l.lampiran_path}" target="_blank" class="button-secondary">Lihat Lampiran: ${l.lampiran_name || 'File Aduan'}</a></div>` : ''}
        <div style="display: flex; justify-content: flex-end;">
            <button class="button-secondary" onclick="closeKmModal('modal-detail-lap-keamanan')">Tutup Jendela</button>
        </div>
    `;
    document.getElementById('km-detail-lap-content').innerHTML = content;
    if(typeof lucide !== 'undefined') lucide.createIcons();
    document.getElementById('modal-detail-lap-keamanan').classList.remove('hidden');
}

window.saveLaporanKeamanan = function() {
    const judul = document.getElementById('km-lap-judul').value.trim();
    const waktu = document.getElementById('km-lap-waktu').value;
    
    if (!judul || !waktu) {
        if(typeof showToast === 'function') showToast("Judul dan Waktu kejadian wajib diisi!", "warning");
        return;
    }

    const fd = new FormData();
    fd.append('id', document.getElementById('km-lap-id').value);
    fd.append('judul', judul);
    fd.append('waktu_kejadian', waktu.replace('T', ' '));
    fd.append('lokasi', document.getElementById('km-lap-lokasi').value);
    fd.append('deskripsi', document.getElementById('km-lap-deskripsi').value);
    fd.append('status', document.getElementById('km-lap-status').value);
    fd.append('pelapor', 'Pengurus');
    fd.append('kategori', 'Info Pengurus');
    fd.append('sumber_input', 'Pengurus');
    const lampiranFile = document.getElementById('km-lap-file').files[0];
    if (lampiranFile) {
        fd.append('lampiran_file', lampiranFile);
    }
    showLoading('Menyimpan...');
    fetch('api/keamanan/simpan_laporan.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{
        if(res.status==='success') { showToast(res.message); closeKmModal('modal-lap-keamanan'); loadLaporanKeamanan(); loadKeamananRingkasan(); } 
        else showToast(res.message, 'error'); 
    }).catch(e => showToast("API Belum Tersedia", "info"));
}

window.deleteLaporan = function(id) {
    if(confirm('Hapus laporan ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/keamanan/hapus_laporan.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{ if(res.status==='success') { loadLaporanKeamanan(); loadKeamananRingkasan(); } });
    }
}

// =========================================
// 4. CRUD IZIN & CUTI
// =========================================
window.loadIzinSatpam = function() {
    const container = document.getElementById('km-leave-requests');
    container.innerHTML = '<p class="text-secondary text-center py-5">Memuat pengajuan...</p>';
    
    fetch('api/keamanan/get_izin.php')
        .then(r => r.ok ? r.json() : {status:'error', message:'API Endpoint Belum Tersedia.'})
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(i => {
                    let statusClass = i.status === 'Pending' ? 'bg-orange-light text-orange' : (i.status === 'Disetujui' ? 'bg-emerald-light text-emerald' : 'bg-red-light text-red');
                    html += `
                    <div class="list-item" style="padding: 16px; border: 1px solid var(--border-color); border-radius: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: var(--secondary-bg);">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <div class="report-avatar bg-blue-light text-blue" style="width: 40px; height: 40px; flex-shrink: 0;">${(i.nama_satpam || 'U').charAt(0)}</div>
                            <div>
                                <h4 style="margin: 0 0 4px 0; font-size: 1rem;">${i.nama_satpam} <span class="badge bg-secondary-light text-secondary" style="font-size: 0.65rem; margin-left: 8px;">${i.jenis}</span></h4>
                                <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary-color);"><i data-lucide="calendar" style="width:12px;height:12px;display:inline;"></i> ${i.tanggal_mulai} s/d ${i.tanggal_selesai}</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <span class="badge ${statusClass}">${i.status}</span>
                            <button class="button-secondary button-sm" onclick="editIzin(${i.id}, ${i.satpam_id}, '${i.tanggal_mulai}', '${i.tanggal_selesai}', '${i.jenis}', '${i.status}', '${encodeURIComponent(i.keterangan)}')"><i data-lucide="edit" style="width:14px; height:14px;"></i></button>
                            <button class="button-secondary button-sm" style="color: #ef4444;" onclick="deleteIzin(${i.id})"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                container.innerHTML = `<p class="text-secondary text-center py-5">${res.message || 'Belum ada pengajuan izin.'}</p>`;
            }
        });
}

window.addIzin = function() {
    document.getElementById('km-izin-id').value = '0';
    document.getElementById('km-izin-mulai').value = '';
    document.getElementById('km-izin-selesai').value = '';
    document.getElementById('km-izin-ket').value = '';
    document.getElementById('km-izin-jenis').value = 'Sakit';
    document.getElementById('km-izin-status-group').classList.add('hidden'); // Sembunyikan set status jika pengajuan baru
    populateSatpamSelect('km-izin-satpam');
    document.getElementById('modal-izin-title').innerText = 'Formulir Izin Baru';
    document.getElementById('modal-izin').classList.remove('hidden');
}

window.editIzin = function(id, satpam_id, mulai, selesai, jenis, status, ketEncoded) {
    document.getElementById('km-izin-id').value = id;
    populateSatpamSelect('km-izin-satpam', satpam_id);
    document.getElementById('km-izin-mulai').value = mulai;
    document.getElementById('km-izin-selesai').value = selesai;
    document.getElementById('km-izin-jenis').value = jenis;
    document.getElementById('km-izin-status').value = status;
    document.getElementById('km-izin-ket').value = decodeURIComponent(ketEncoded);
    document.getElementById('km-izin-status-group').classList.remove('hidden'); // Tampilkan status approve jika edit (oleh admin)
    document.getElementById('modal-izin-title').innerText = 'Kelola Izin / Cuti';
    document.getElementById('modal-izin').classList.remove('hidden');
}

window.saveIzin = function() {
    const fd = new FormData();
    fd.append('id', document.getElementById('km-izin-id').value);
    fd.append('satpam_id', document.getElementById('km-izin-satpam').value);
    fd.append('tanggal_mulai', document.getElementById('km-izin-mulai').value);
    fd.append('tanggal_selesai', document.getElementById('km-izin-selesai').value);
    fd.append('jenis', document.getElementById('km-izin-jenis').value);
    fd.append('keterangan', document.getElementById('km-izin-ket').value);
    fd.append('status', document.getElementById('km-izin-status').value || 'Pending');
    showLoading('Menyimpan...');
    fetch('api/keamanan/simpan_izin.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{
        if(res.status==='success') { showToast(res.message); closeKmModal('modal-izin'); loadIzinSatpam(); } else showToast(res.message, 'error'); 
    }).catch(e => showToast("API Belum Tersedia", "info"));
}

window.deleteIzin = function(id) {
    if(confirm('Hapus pengajuan izin ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/keamanan/hapus_izin.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{ if(res.status==='success') loadIzinSatpam(); });
    }
}