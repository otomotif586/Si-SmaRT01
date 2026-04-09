// --- Detail Warga Full (Data Diri & Tunggakan) ---
window.showDetailWarga = function(id) {
    // Buat elemen modal secara dinamis jika belum ada di halaman ini
    let modalFull = document.getElementById('modal-detail-warga-full');
    if (!modalFull) {
        modalFull = document.createElement('div');
        modalFull.id = 'modal-detail-warga-full';
        modalFull.className = 'modal-overlay hidden';
        modalFull.style.cssText = 'z-index: 10020 !important; align-items: center; justify-content: center; padding: 16px;';
        modalFull.innerHTML = `
            <div class="glass-card hide-scrollbar" style="width: 100%; max-width: 850px; max-height: 90dvh; overflow-y: auto; padding: 32px; position: relative; border-radius: 24px;">
                <button class="modal-close-btn" style="position: absolute; top: 16px; right: 16px; z-index: 10;" onclick="closeDetailWarga()"><i data-lucide="x"></i></button>
                <div id="modal-detail-warga-content"></div>
            </div>
        `;
        document.body.appendChild(modalFull);
    }

    document.getElementById('modal-detail-warga-content').innerHTML = '<p class="text-center py-5 text-secondary"><i data-lucide="loader"></i> Memuat detail warga...</p>';
    modalFull.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; 
    lucide.createIcons();
    
    fetch(`api/get_warga_full_detail.php?id=${id}`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            window.renderWargaFullDetail(res);
        } else {
            document.getElementById('modal-detail-warga-content').innerHTML = `<p class="text-red text-center py-5">${res.message}</p>`;
        }
    })
    .catch(e => {
        document.getElementById('modal-detail-warga-content').innerHTML = `<p class="text-red text-center py-5">Terjadi kesalahan koneksi jaringan.</p>`;
    });
};

window.renderWargaFullDetail = function(res) {
    const w = res.data;
    const tunggakan = res.tunggakan;
    const iuran = res.iuran_tahun_ini;
    const pasangan = res.pasangan;
    const anak = res.anak || [];
    const orangLain = res.orang_lain || [];
    const kendaraan = res.kendaraan || [];
    const dokumen = res.dokumen || [];
    const tahunBuku = new Date().getFullYear();
    
    // Gabungkan data tanggungan untuk di-loop
    let tanggungan = [];
    let keluargaHtml = '';
    if (pasangan) tanggungan.push({ nama: pasangan.nama_lengkap, nik: pasangan.nik, icon: 'heart', iconColor: '#ec4899', badgeText: 'Pasangan', badgeClass: 'bg-pink-light text-pink', bgIcon: 'rgba(236, 72, 153, 0.1)' });
    anak.forEach(a => tanggungan.push({ nama: a.nama_lengkap, nik: a.nik, icon: 'user', iconColor: '#3b82f6', badgeText: 'Anak', badgeClass: 'bg-blue-light text-blue', bgIcon: 'rgba(59, 130, 246, 0.1)' }));
    orangLain.forEach(o => tanggungan.push({ nama: o.nama_lengkap, nik: null, icon: 'users', iconColor: 'var(--text-secondary-color)', badgeText: `${o.status_hubungan} (${o.umur} Thn)`, badgeClass: 'bg-secondary-light text-secondary', bgIcon: 'rgba(128,128,128,0.1)' }));

    if (tanggungan.length > 0) {
        tanggungan.forEach((t) => {
            const nikHtml = t.nik ? `<span style="font-size: 0.75rem; color: var(--text-secondary-color); margin-top: 4px; display: block;"><i data-lucide="credit-card" style="width:12px; height:12px; display:inline; margin-right:4px;"></i>NIK: ${t.nik}</span>` : '';
            
            keluargaHtml += `
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(128,128,128,0.03); padding: 12px 16px; border-radius: 16px; border: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: ${t.bgIcon}; color: ${t.iconColor}; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="${t.icon}" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <span class="font-bold text-color" style="font-size: 0.95rem; display: block;">${t.nama}</span>
                            ${nikHtml}
                        </div>
                    </div>
                    <span class="badge ${t.badgeClass}" style="font-size: 0.7rem;">${t.badgeText}</span>
                </div>
            `;
        });
    } else {
        keluargaHtml = `<div style="padding: 20px; text-align: center; background: rgba(128,128,128,0.03); border-radius: 16px; border: 1px dashed var(--border-color);"><i data-lucide="users" style="color: var(--text-secondary-color); opacity: 0.5; width: 32px; height: 32px; margin-bottom: 8px;"></i><p class="text-secondary" style="font-size: 0.85rem; margin: 0;">Tidak ada data tanggungan keluarga</p></div>`;
    }

    let asetHtml = '';
    if (kendaraan.length > 0) {
        kendaraan.forEach(k => {
            asetHtml += `<div style="display: flex; align-items: center; gap: 12px; background: rgba(128,128,128,0.03); padding: 12px 16px; border-radius: 16px; border: 1px solid var(--border-color); flex: 1; min-width: 150px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; color: var(--text-secondary-color);">
                    <i data-lucide="key" style="width:18px; height:18px;"></i>
                </div>
                <div>
                    <span style="font-size: 0.9rem; font-weight: 700; color: var(--text-color); display: block;">${k.nopol}</span>
                    <span class="text-secondary" style="font-size:0.75rem;">${k.jenis_kendaraan}</span>
                </div>
            </div>`;
        });
    } else {
        asetHtml = `<div style="width: 100%; padding: 20px; text-align: center; background: rgba(128,128,128,0.03); border-radius: 16px; border: 1px dashed var(--border-color);"><p class="text-secondary" style="font-size: 0.85rem; margin: 0;">Tidak ada data kendaraan</p></div>`;
    }

    let dokHtml = '';
    if (dokumen.length > 0) {
        dokumen.forEach(d => {
            let fname = d.file_path.split('/').pop();
            dokHtml += `<a href="${d.file_path}" target="_blank" class="document-item" style="padding: 12px 16px; background: rgba(128,128,128,0.03); border-radius: 16px; border: 1px solid var(--border-color); text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i data-lucide="file-text" style="width:18px; height:18px;"></i>
                </div>
                <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-color); word-break: break-all; white-space: normal; flex: 1; min-width: 0;">${fname}</span>
            </a>`;
        });
    } else {
        dokHtml = `<div style="padding: 20px; text-align: center; background: rgba(128,128,128,0.03); border-radius: 16px; border: 1px dashed var(--border-color);"><p class="text-secondary" style="font-size: 0.85rem; margin: 0;">Belum ada lampiran dokumen</p></div>`;
    }

    let html = `
        <div class="profile-header glass-card" style="display: flex; flex-wrap: wrap; gap: 24px; align-items: center; padding: 32px; border-radius: 24px; margin-bottom: 24px; background: linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 15%, transparent), transparent); border: 1px solid var(--border-color);">
            <div class="avatar bg-emerald text-white" style="width: 80px; height: 80px; font-size: 2.5rem; box-shadow: 0 10px 25px -5px color-mix(in srgb, var(--accent-color) 40%, transparent);">${w.nama_lengkap.charAt(0)}</div>
            <div style="flex: 1; min-width: 250px;">
                <h2 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 800; color: var(--text-color);">${w.nama_lengkap}</h2>
                <p class="text-secondary" style="margin: 0 0 16px 0; font-size: 0.95rem; font-weight: 500;"><i data-lucide="map-pin" style="width:16px; height:16px; display:inline; margin-bottom:-2px;"></i> Blok ${w.nama_blok} - No. ${w.nomor_rumah || '-'}</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <span class="badge bg-blue-light text-blue" style="padding: 6px 14px; font-size: 0.8rem; border-radius: 10px;"><i data-lucide="credit-card" style="width: 14px; height: 14px;"></i> NIK: ${w.nik_kepala || w.nik || '-'}</span>
                    <span class="badge ${w.status_kependudukan === 'Kontrak' ? 'bg-orange-light text-orange' : (w.status_kependudukan === 'Weekend' ? 'bg-purple-light text-purple' : 'bg-emerald-light text-emerald')}" style="padding: 6px 14px; font-size: 0.8rem; border-radius: 10px;"><i data-lucide="user" style="width: 14px; height: 14px;"></i> ${w.status_kependudukan || '-'}</span>
                    <span class="badge bg-secondary-light text-secondary" style="padding: 6px 14px; font-size: 0.8rem; border-radius: 10px;"><i data-lucide="phone" style="width: 14px; height: 14px;"></i> ${w.no_wa || '-'}</span>
                </div>
            </div>
        </div>

        <div class="grid-container-2-col" style="gap: 20px; margin-bottom: 24px;">
            <!-- KOLOM KIRI -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="glass-card" style="padding: 24px; border-radius: 24px;">
                    <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="user-circle" style="color:var(--accent-color);"></i> Profil Pribadi</h4>
                    <div style="display: flex; flex-direction: column; gap: 16px; font-size: 0.95rem;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                            <span class="text-secondary">Tempat, Tgl Lahir</span>
                            <span class="font-bold text-color">${w.tempat_lahir || '-'}, ${w.tanggal_lahir || '-'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                            <span class="text-secondary">Status Perkawinan</span>
                            <span class="font-bold text-color">${w.status_pernikahan || '-'}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card" style="padding: 24px; border-radius: 24px;">
                    <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="users" style="color:var(--accent-color);"></i> Anggota Keluarga & Penghuni</h4>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        ${keluargaHtml}
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="glass-card" style="padding: 24px; border-radius: 24px; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: ${tunggakan.bulan_tunggak > 0 ? '#ef4444' : '#10b981'};"></div>
                    <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="wallet" style="color:var(--text-secondary-color);"></i> Status Iuran Wajib</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(128,128,128,0.03); padding: 16px; border-radius: 16px; border: 1px solid var(--border-color);">
                            <span class="text-secondary" style="font-size: 0.9rem; font-weight: 500;">Bulan Menunggak</span>
                            <span class="badge ${tunggakan.bulan_tunggak > 0 ? 'bg-red-light text-red' : 'bg-emerald-light text-emerald'}" style="font-size: 0.85rem; padding: 6px 12px;">${tunggakan.bulan_tunggak} Bulan</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(128,128,128,0.03); padding: 16px; border-radius: 16px; border: 1px solid var(--border-color);">
                            <span class="text-secondary" style="font-size: 0.9rem; font-weight: 500;">Total Piutang</span>
                            <span class="font-bold ${tunggakan.total_tunggakan > 0 ? 'text-red' : 'text-emerald'}" style="font-size: 1.2rem;">Rp ${parseInt(tunggakan.total_tunggakan || 0).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card" style="padding: 24px; border-radius: 24px;">
                    <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="car" style="color:var(--text-secondary-color);"></i> Aset Kendaraan</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        ${asetHtml}
                    </div>
                </div>

                <div class="glass-card" style="padding: 24px; border-radius: 24px;">
                    <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="folder" style="color:var(--text-secondary-color);"></i> Dokumen Pendukung</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        ${dokHtml}
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card" style="padding: 24px; border-radius: 24px;">
            <h4 class="text-color" style="font-size: 1.1rem; margin: 0 0 20px 0; font-weight: 800; display:flex; align-items:center; gap:8px;"><i data-lucide="history" style="color:var(--text-secondary-color);"></i> Riwayat Pembayaran (Tahun ${tahunBuku})</h4>
            <div class="table-responsive" style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;">
                <table class="modern-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead style="background: var(--secondary-bg);">
                    <tr>
                        <th style="padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-weight: 600;">Bulan</th>
                        <th style="padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-weight: 600;">Tagihan</th>
                        <th style="padding: 16px; text-align: center; border-bottom: 1px solid var(--border-color); font-weight: 600;">Status</th>
                        <th style="padding: 16px; text-align: right; border-bottom: 1px solid var(--border-color); font-weight: 600;">Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
    `;

    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let iuranMap = {};
    iuran.forEach(i => { iuranMap[i.bulan] = i; });
    const currentMonth = new Date().getMonth();

    for (let m = 0; m < 12; m++) {
        let statusHtml = '-'; let tagihan = '-'; let tglBayar = '-'; let bgRow = '';

        if (iuranMap[m]) {
            const data = iuranMap[m];
            tagihan = `Rp ${parseInt(data.total_tagihan).toLocaleString('id-ID')}`;
            if (data.status === 'LUNAS') {
                statusHtml = `<span class="badge bg-emerald-light text-emerald" style="font-size: 0.75rem; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;"><i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Lunas</span>`;
                tglBayar = data.tanggal_bayar ? data.tanggal_bayar.split(' ')[0] : '-';
            } else {
                statusHtml = `<span class="badge bg-red-light text-red" style="font-size: 0.75rem; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;"><i data-lucide="x-circle" style="width: 12px; height: 12px;"></i> Menunggak</span>`;
                if (m <= currentMonth) bgRow = 'background: rgba(239, 68, 68, 0.02);';
            }
        } else {
            if (m <= currentMonth) statusHtml = `<span class="badge bg-red-light text-red" style="font-size: 0.75rem; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;"><i data-lucide="x-circle" style="width: 12px; height: 12px;"></i> Belum Ada Data</span>`;
            else statusHtml = `<span class="badge bg-secondary-light text-secondary" style="font-size: 0.75rem; padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">Belum Ditagihkan</span>`;
        }

        html += `<tr style="border-bottom: 1px solid var(--border-color); ${bgRow} transition: background 0.3s;"><td style="padding: 16px;">${months[m]}</td><td style="padding: 16px; font-weight: 700;">${tagihan}</td><td style="padding: 16px; text-align: center;">${statusHtml}</td><td style="padding: 16px; text-align: right; color: var(--text-secondary-color);">${tglBayar}</td></tr>`;
    }

    html += `</tbody></table></div></div>`;
    document.getElementById('modal-detail-warga-content').innerHTML = html;
    lucide.createIcons();
};

window.closeDetailWarga = function() {
    document.getElementById('modal-detail-warga-full').classList.add('hidden');
    // Only restore overflow if workspace modal is not active
    const wsModal = document.getElementById('workspace-modal');
    if (!wsModal || wsModal.classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
};

// Global variable menyimpan array data
window.currentWargaData = [];
window.currentWargaPage = 1;
const wargaItemsPerPage = 15;

function filterWargaList() {
    if (!window.currentWargaData) return;
    
    const q = document.getElementById('search-warga-input').value.toLowerCase();
    const fPernikahan = document.getElementById('filter-pernikahan').value;
    const fStatus = document.getElementById('filter-status').value;

    const filtered = window.currentWargaData.filter(w => {
        const matchQ = (w.nama_lengkap && w.nama_lengkap.toLowerCase().includes(q)) || 
                       (w.nik && w.nik.toLowerCase().includes(q)) ||
                       (w.nik_kepala && w.nik_kepala.toLowerCase().includes(q)) ||
                       (w.nomor_rumah && w.nomor_rumah.toLowerCase().includes(q));
        
        const matchP = fPernikahan === '' || w.status_pernikahan === fPernikahan;
        const matchS = fStatus === '' || w.status_kependudukan === fStatus;

        return matchQ && matchP && matchS;
    });
    
    // Update Kartu Ringkasan Data Warga
    document.getElementById('sum-warga-total').innerText = filtered.length;
    document.getElementById('sum-warga-tetap').innerText = filtered.filter(w => w.status_kependudukan === 'Tetap').length;
    document.getElementById('sum-warga-kontrak').innerText = filtered.filter(w => w.status_kependudukan === 'Kontrak').length;

    // Paginasi Data Warga
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / wargaItemsPerPage);
    if (window.currentWargaPage > totalPages && totalPages > 0) window.currentWargaPage = totalPages;
    if (window.currentWargaPage < 1) window.currentWargaPage = 1;

    const startIndex = (window.currentWargaPage - 1) * wargaItemsPerPage;
    const endIndex = Math.min(startIndex + wargaItemsPerPage, totalItems);
    const paginatedItems = filtered.slice(startIndex, endIndex);

    const paginationContainer = document.getElementById('warga-pagination');
    if (totalItems > 0) {
        paginationContainer.style.display = 'flex';
        document.getElementById('warga-page-info').innerText = `Menampilkan ${startIndex + 1}-${endIndex} dari ${totalItems}`;
    } else {
        paginationContainer.style.display = 'none';
    }

    renderWargaList(paginatedItems);
}

function renderWargaList(data, blockName = '') {
    const container = document.getElementById('modal-warga-list-container');
    if (!blockName) blockName = document.getElementById('modal-block-title').innerText;
    
    if (data.length === 0) {
        container.innerHTML = '<p class="text-secondary text-center py-4">Belum ada data warga ditemukan.</p>';
        return;
    }

    let html = '<div class="warga-grid">';
    data.forEach(w => {
        
        // Menyesuaikan Badge dengan Status Kependudukan Warga
        let statusKependudukan = w.status_kependudukan || '-';
        let statusClass = 'bg-emerald-light text-emerald'; // Tetap
        if (statusKependudukan === 'Kontrak') statusClass = 'bg-orange-light text-orange';
        else if (statusKependudukan === 'Weekend') statusClass = 'bg-purple-light text-purple';
        
        let waLink = '';
        if (w.no_wa) {
            let cleanWa = w.no_wa.replace(/\D/g, ''); 
            if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
            waLink = `<a href="https://wa.me/${cleanWa}" target="_blank" class="button-secondary" style="border-radius: 12px; padding: 8px 16px; color: #25D366; border-color: transparent; background: rgba(37, 211, 102, 0.1); font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: none;" title="Chat WhatsApp" onclick="event.stopPropagation();"><i data-lucide="message-circle" style="width: 16px; height: 16px;"></i> Chat WA</a>`;
        }

        let btnEdit = `<button onclick="event.stopPropagation(); editWarga(${w.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: var(--accent-color); border-color: transparent; background: color-mix(in srgb, var(--accent-color) 10%, transparent); box-shadow: none;" title="Edit Data Warga"><i data-lucide="edit-2" style="width: 18px; height: 18px;"></i></button>`;
        let btnDelete = `<button onclick="event.stopPropagation(); hapusWarga(${w.id}, '${w.nama_lengkap}')" class="button-secondary" style="border-radius: 12px; padding: 8px; color: #ef4444; border-color: transparent; background: rgba(239, 68, 68, 0.1); box-shadow: none;" title="Hapus Data"><i data-lucide="trash-2" style="width: 18px; height: 18px;"></i></button>`;
        let btnDetail = `<button onclick="event.stopPropagation(); showDetailWarga(${w.id})" class="button-secondary" style="border-radius: 12px; padding: 8px; color: var(--text-color); border-color: transparent; background: rgba(128,128,128, 0.1); box-shadow: none;" title="Detail Lengkap Warga"><i data-lucide="eye" style="width: 18px; height: 18px;"></i></button>`;

        html += `
            <div class="warga-card glass-card" style="padding: 24px;">
                <div class="warga-card-header">
                    <div class="avatar bg-emerald-light text-emerald" style="width: 48px; height: 48px; font-size: 1.2rem;">${w.nama_lengkap.charAt(0)}</div>
                    <div style="flex: 1; overflow: hidden;">
                        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">${w.nama_lengkap}</h4>
                        <span class="badge ${statusClass}" style="margin-top: 6px; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;"><i data-lucide="${w.status_kependudukan === 'Tetap' ? 'user-check' : 'user'}" style="width: 12px; height: 12px;"></i> Status: ${statusKependudukan}</span>
                    </div>
                </div>
                <div class="warga-card-body">
                    <div class="warga-detail-item"><i data-lucide="map-pin"></i> <span>Blok ${blockName} - No. ${w.nomor_rumah || '-'}</span></div>
                    <div class="warga-detail-item"><i data-lucide="credit-card"></i> <span>NIK: ${w.nik_kepala || w.nik || '-'}</span></div>
                    <div class="warga-detail-item"><i data-lucide="user-check"></i> <span>Pernikahan: ${w.status_pernikahan || 'Lajang'}</span></div>
                    <div class="warga-detail-item"><i data-lucide="calendar"></i> <span>${w.tempat_lahir || '-'}, ${w.tanggal_lahir || '-'}</span></div>
                </div>
                <div class="warga-card-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; border-top: 1px dashed var(--border-color); padding-top: 16px;">
                    <div>${waLink}</div>
                    <div class="warga-action-group">
                        ${btnDetail}
                        ${btnEdit}
                        ${btnDelete}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons();
}

function prevPageWarga() {
    if (window.currentWargaPage > 1) { window.currentWargaPage--; filterWargaList(); }
}

function nextPageWarga() {
    window.currentWargaPage++;
    filterWargaList();
}

// Variable untuk menyimpan status edit Warga
window.currentWargaId = 0;

// Fungsi untuk memuat ulang daftar warga tanpa merefresh halaman
function refreshWargaList() {
    if (window.currentBlokId === 0) return;
    const wargaListContainer = document.getElementById('modal-warga-list-container');
    wargaListContainer.innerHTML = '<p class="text-secondary text-center py-4">Memuat data warga...</p>';
    
    fetch(`api/get_warga.php?blok_id=${window.currentBlokId}`)
        .then(response => response.json())
        .then(data => {
            window.currentWargaData = data; 
            filterWargaList();
        });
}

// --- Data Warga Blok (Drawer Form Logic) ---
function openFormWarga(isGlobal = false) {
    window.currentWargaId = 0; // Reset ke mode Tambah
    document.getElementById('form-tambah-warga').reset(); // Kosongkan form
    document.querySelector('#drawer-warga .ws-title').innerText = 'Tambah Data Warga';
    
    const blokSelect = document.getElementById('warga_blok_id');
    if (isGlobal) {
        blokSelect.disabled = false; blokSelect.value = '';
    } else {
        blokSelect.value = window.currentBlokId; blokSelect.disabled = true; // Kunci input jika dari workspace
    }
    
    const drawer = document.getElementById('drawer-warga');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50); // Memicu slide animasi
}

function closeFormWarga() {
    const drawer = document.getElementById('drawer-warga');
    drawer.classList.remove('drawer-active');
    setTimeout(() => drawer.classList.add('hidden'), 400); // Tunggu animasi selesai
}

function togglePasangan(status) {
    const section = document.getElementById('section-pasangan');
    if (status === 'Menikah') {
        section.classList.remove('hidden');
    } else {
        section.classList.add('hidden');
    }
}

function generateAnakFields(jumlah) {
    const container = document.getElementById('container-anak');
    container.innerHTML = '';
    const num = parseInt(jumlah);
    
    if (num > 0) {
        container.classList.remove('hidden');
        for (let i = 1; i <= num; i++) {
            container.innerHTML += `
                <div class="anak-item" style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
                    <p class="card-label" style="margin-bottom: 12px; color: var(--accent-color);">Data Anak Ke-${i}</p>
                    <div class="form-grid">
                        <div class="form-group"><input type="text" inputmode="numeric" class="input-field anak-nik" placeholder="NIK Anak (Opsional)"></div>
                        <div class="form-group"><input type="text" class="input-field anak-nama" placeholder="Nama Lengkap Anak"></div>
                        <div class="form-group"><input type="text" class="input-field anak-tempat" placeholder="Tempat Lahir"></div>
                        <div class="form-group"><input type="date" class="input-field anak-tgl" style="padding-left: 20px;"></div>
                    </div>
                </div>`;
        }
    } else {
        container.classList.add('hidden');
    }
}

function generateOrangLainFields(jumlah) {
    const container = document.getElementById('container-oranglain');
    container.innerHTML = '';
    const num = parseInt(jumlah);
    
    if (num > 0) {
        container.classList.remove('hidden');
        for (let i = 1; i <= num; i++) {
            container.innerHTML += `
                <div class="orang-item" style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
                    <p class="card-label" style="margin-bottom: 12px; color: var(--accent-color);">Data Orang Ke-${i}</p>
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;"><input type="text" class="input-field orang-nama" placeholder="Nama Lengkap"></div>
                        <div class="form-group"><input type="text" inputmode="numeric" class="input-field orang-umur" placeholder="Umur (Tahun)"></div>
                        <div class="form-group">
                            <select class="input-field select-custom orang-hubungan">
                                <option value="Keluarga">Keluarga (Paman, Bibi, dll)</option>
                                <option value="ART">Asisten Rumah Tangga (ART)</option>
                                <option value="Supir">Supir / Pekerja</option>
                                <option value="Teman/Lainnya">Teman / Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>`;
        }
    } else {
        container.classList.add('hidden');
    }
}

function addKendaraanField() {
    const div = document.createElement('div');
    div.className = 'kendaraan-item';
    div.style.cssText = 'display: flex; gap: 8px; margin-bottom: 8px; animation: pageFadeIn 0.3s forwards;';
    div.innerHTML = `<input type="text" class="input-field kendaraan-nopol" placeholder="Nopol (cth: B 1234 CD)" style="flex: 1;"><select class="input-field select-custom kendaraan-jenis" style="width: 120px;"><option value="Motor">Motor</option><option value="Mobil">Mobil</option></select><button type="button" class="button-secondary" style="border-radius: 9999px; color: #ef4444;" onclick="this.parentElement.remove()"><i data-lucide="trash-2" style="width: 18px; height: 18px;"></i></button>`;
    document.getElementById('container-kendaraan').appendChild(div);
    lucide.createIcons();
}

function addDokumenField() {
    const div = document.createElement('div');
    div.className = 'dokumen-item';
    div.style.cssText = 'display: flex; gap: 8px; margin-bottom: 8px; animation: pageFadeIn 0.3s forwards;';
    div.innerHTML = `<input type="file" class="input-field file-input-modern dokumen-file" style="flex: 1;"><button type="button" class="button-secondary" style="border-radius: 9999px; color: #ef4444;" onclick="this.parentElement.remove()"><i data-lucide="trash-2" style="width: 18px; height: 18px;"></i></button>`;
    document.getElementById('container-dokumen').appendChild(div);
    lucide.createIcons();
}

function simpanDataWarga() {
    const btn = document.querySelector('.drawer-footer .button-primary');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    
    const elNoKk = document.getElementById('warga_nokk');
    const elNik = document.getElementById('warga_nik_kepala');
    const elWa = document.getElementById('warga_nowa');
    const elBlokId = document.getElementById('warga_blok_id');

    // Pengecekan Aman (Defensive Check) agar Javascript tidak crash
    if (!elNoKk || !elNik || !elWa || !elBlokId) {
        alert("Error Sistem: Elemen Form tidak lengkap. Pastikan file UI form warga sudah menggunakan versi yang terbaru.");
        btn.innerHTML = originalText;
        return;
    }
    
    const valNoKk = elNoKk.value;
    const valNik = elNik.value;
    const valWa = elWa.value;
    const selectedBlokId = elBlokId.value;

    if (!selectedBlokId) {
        alert('Silakan pilih Domisili Blok warga terlebih dahulu!');
        btn.innerHTML = originalText;
        lucide.createIcons();
        return;
    }
    
    // Validasi: Harus berupa angka (boleh kosong)
    const isNumber = (val) => /^\d*$/.test(val);
    if (!isNumber(valNoKk) || !isNumber(valNik) || !isNumber(valWa)) {
        alert('Validasi Gagal: No KK, NIK, dan No WhatsApp harus berupa angka!');
        btn.innerHTML = originalText;
        lucide.createIcons();
        return;
    }

    const formData = new FormData();
    if (window.currentWargaId > 0) {
        formData.append('id', window.currentWargaId);
    }
    formData.append('blok_id', selectedBlokId);
    formData.append('nomor_rumah', document.getElementById('warga_norumah').value);
    formData.append('nik', valNoKk); // Asumsi KK disimpan di kolom nik
    formData.append('nik_kepala', valNik);
    formData.append('nama_lengkap', document.getElementById('warga_kepala').value);
    formData.append('no_wa', valWa);
    formData.append('tempat_lahir', document.getElementById('warga_tempatlahir').value);
    formData.append('tanggal_lahir', document.getElementById('warga_tgllahir').value);
    formData.append('status_pernikahan', document.getElementById('warga_pernikahan').value);
    formData.append('status_kependudukan', document.getElementById('warga_status').value);

    // --- KUMPULKAN DATA DINAMIS ---
    // 1. Pasangan
    if (document.getElementById('warga_pernikahan').value === 'Menikah') {
        const pNik = document.getElementById('pasangan_nik');
        if(pNik) formData.append('pasangan_nik', pNik.value);
        formData.append('pasangan_nama', document.getElementById('pasangan_nama').value);
        formData.append('pasangan_tempat', document.getElementById('pasangan_tempat').value);
        formData.append('pasangan_tgl', document.getElementById('pasangan_tgl').value);
    }

    // 2. Anak
    document.querySelectorAll('.anak-item').forEach((el, index) => {
        if (el.querySelector('.anak-nama').value) {
            formData.append(`anak[${index}][nik]`, el.querySelector('.anak-nik').value);
            formData.append(`anak[${index}][nama]`, el.querySelector('.anak-nama').value);
            formData.append(`anak[${index}][tempat]`, el.querySelector('.anak-tempat').value);
            formData.append(`anak[${index}][tgl]`, el.querySelector('.anak-tgl').value);
        }
    });

    // 3. Kendaraan
    document.querySelectorAll('.kendaraan-item').forEach((el, index) => {
        if (el.querySelector('.kendaraan-nopol').value) {
            formData.append(`kendaraan[${index}][nopol]`, el.querySelector('.kendaraan-nopol').value);
            formData.append(`kendaraan[${index}][jenis]`, el.querySelector('.kendaraan-jenis').value);
        }
    });

    // 4. Penghuni Lain (Orang Lain)
    document.querySelectorAll('.orang-item').forEach((el, index) => {
        if (el.querySelector('.orang-nama').value) {
            formData.append(`orang_lain[${index}][nama]`, el.querySelector('.orang-nama').value);
            formData.append(`orang_lain[${index}][umur]`, el.querySelector('.orang-umur').value);
            formData.append(`orang_lain[${index}][hubungan]`, el.querySelector('.orang-hubungan').value);
        }
    });

    // 5. Dokumen Uploads
    document.querySelectorAll('.dokumen-file').forEach((el) => {
        if (el.files.length > 0) {
            formData.append('dokumen[]', el.files[0]);
        }
    });

    const apiEndpoint = window.currentWargaId > 0 ? 'api/edit_warga.php' : 'api/tambah_warga.php';

    fetch(apiEndpoint, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            if (localStorage.getItem('activePage') === 'global-warga') loadGlobalWarga();
            else refreshWargaList(); 
            closeFormWarga(); // Tutup laci form
            btn.innerHTML = originalText;
        } else {
            alert('Gagal: ' + data.message);
            btn.innerHTML = originalText;
        }
    })
    .catch(e => { alert('Kesalahan koneksi'); btn.innerHTML = originalText; });
}

function hapusWarga(id, nama) {
    if (confirm(`Hapus data warga ${nama}?`)) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/hapus_warga.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if(res.status === 'success') {
                if (localStorage.getItem('activePage') === 'global-warga') loadGlobalWarga();
                else refreshWargaList();
            }
            else alert('Gagal: ' + res.message);
        });
    }
}

function editWarga(id) {
    window.currentWargaId = id;
    document.querySelector('#drawer-warga .ws-title').innerText = 'Edit Data Warga';

    fetch(`api/get_warga_full_detail.php?id=${id}`)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const data = res.data;
            const blokSelect = document.getElementById('warga_blok_id');
            blokSelect.value = data.blok_id || '';
            blokSelect.disabled = (localStorage.getItem('activePage') !== 'global-warga');

            document.getElementById('warga_norumah').value = data.nomor_rumah || '';
            document.getElementById('warga_nokk').value = data.nik || '';
            document.getElementById('warga_nik_kepala').value = data.nik_kepala || '';
            document.getElementById('warga_kepala').value = data.nama_lengkap || '';
            document.getElementById('warga_nowa').value = data.no_wa || '';
            document.getElementById('warga_tempatlahir').value = data.tempat_lahir || '';
            document.getElementById('warga_tgllahir').value = data.tanggal_lahir || '';
            document.getElementById('warga_pernikahan').value = data.status_pernikahan || 'Lajang';
            document.getElementById('warga_status').value = data.status_kependudukan || 'Tetap';

            togglePasangan(data.status_pernikahan);

            // Populate Pasangan
            if (data.status_pernikahan === 'Menikah' && res.pasangan) {
                const pNik = document.getElementById('pasangan_nik');
                if (pNik) pNik.value = res.pasangan.nik || '';
                document.getElementById('pasangan_nama').value = res.pasangan.nama_lengkap || '';
                document.getElementById('pasangan_tempat').value = res.pasangan.tempat_lahir || '';
                document.getElementById('pasangan_tgl').value = res.pasangan.tanggal_lahir || '';
            } else {
                const pNik = document.getElementById('pasangan_nik');
                if (pNik) pNik.value = '';
                document.getElementById('pasangan_nama').value = '';
                document.getElementById('pasangan_tempat').value = '';
                document.getElementById('pasangan_tgl').value = '';
            }

            // Populate Anak
            const anakSelect = document.getElementById('warga_jumlah_anak');
            anakSelect.value = res.anak ? res.anak.length : 0;
            generateAnakFields(anakSelect.value);
            if (res.anak) {
                const anakItems = document.querySelectorAll('.anak-item');
                res.anak.forEach((a, i) => {
                    if (anakItems[i]) {
                        anakItems[i].querySelector('.anak-nik').value = a.nik || '';
                        anakItems[i].querySelector('.anak-nama').value = a.nama_lengkap || '';
                        anakItems[i].querySelector('.anak-tempat').value = a.tempat_lahir || '';
                        anakItems[i].querySelector('.anak-tgl').value = a.tanggal_lahir || '';
                    }
                });
            }

            // Populate Orang Lain
            const orangSel = document.getElementById('warga_jumlah_orang');
            if (orangSel) {
                orangSel.value = res.orang_lain ? res.orang_lain.length : 0;
                generateOrangLainFields(orangSel.value);
                if (res.orang_lain) {
                    const orangItems = document.querySelectorAll('.orang-item');
                    res.orang_lain.forEach((o, i) => {
                        if (orangItems[i]) {
                            orangItems[i].querySelector('.orang-nama').value = o.nama_lengkap || '';
                            orangItems[i].querySelector('.orang-umur').value = o.umur || '';
                            orangItems[i].querySelector('.orang-hubungan').value = o.status_hubungan || 'Keluarga';
                        }
                    });
                }
            }

            // Populate Kendaraan
            const kendaraanContainer = document.getElementById('container-kendaraan');
            kendaraanContainer.innerHTML = '';
            if (res.kendaraan) {
                res.kendaraan.forEach(k => {
                    addKendaraanField();
                    const kItems = kendaraanContainer.querySelectorAll('.kendaraan-item');
                    const lastItem = kItems[kItems.length - 1];
                    lastItem.querySelector('.kendaraan-nopol').value = k.nopol || '';
                    lastItem.querySelector('.kendaraan-jenis').value = k.jenis_kendaraan || 'Motor';
                });
            }

            // Reset container Dokumen input
            const docContainer = document.getElementById('container-dokumen');
            docContainer.innerHTML = '<input type="file" class="input-field file-input-modern dokumen-file">';

            const drawer = document.getElementById('drawer-warga');
            drawer.classList.remove('hidden');
            setTimeout(() => drawer.classList.add('drawer-active'), 50);
        } else {
            alert('Gagal mengambil data warga: ' + res.message);
        }
    })
    .catch(e => alert('Gagal mengambil data warga.'));
}

function downloadTemplateWarga() {
    window.location.href = 'api/download_template_warga.php';
}

function exportWargaCSV() {
    if (window.currentBlokId === 0) return;
    window.location.href = `api/export_warga.php?blok_id=${window.currentBlokId}`;
}

function importWargaCSV(input) {
    if (input.files.length === 0 || window.currentBlokId === 0) return;
    
    const fd = new FormData();
    fd.append('file', input.files[0]);
    fd.append('blok_id', window.currentBlokId);
    
    fetch('api/import_warga.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            alert(`Import Berhasil! ${res.imported} data warga ditambahkan.`);
            refreshWargaList();
        } else {
            alert('Gagal Import: ' + res.message);
        }
    }).catch(e => alert('Terjadi kesalahan saat mengunggah file.'));
}
