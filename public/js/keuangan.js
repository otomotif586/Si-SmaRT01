// --- Logika Kas Blok & Iuran ---
window.currentIuranData = [];
window.currentMasterIuran = [];

function initKeuanganBlok() {
    const selectBulan = document.getElementById('filter-bulan-iuran');
    if (selectBulan.options.length === 0) {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const now = new Date();
        
        // Default: Bulan Sebelumnya
        let defaultMonth = now.getMonth() - 1;
        let defaultYear = now.getFullYear();
        if (defaultMonth < 0) { defaultMonth = 11; defaultYear -= 1; }

        for (let i = 0; i < 12; i++) {
            const opt = document.createElement('option');
            opt.value = `${i}-${defaultYear}`;
            opt.text = `${months[i]} ${defaultYear}`;
            if (i === defaultMonth) opt.selected = true;
            selectBulan.appendChild(opt);
        }
    }
    
    loadDataIuran();
}

function prevMonthIuran() {
    const select = document.getElementById('filter-bulan-iuran');
    if (select.selectedIndex > 0) {
        select.selectedIndex--;
        loadDataIuran(); // Minta database memuat data bulan sebelumnya
    }
}

function nextMonthIuran() {
    const select = document.getElementById('filter-bulan-iuran');
    if (select.selectedIndex < select.options.length - 1) {
        select.selectedIndex++;
        loadDataIuran(); // Minta database memuat data bulan selanjutnya
    }
}

window.currentIuranPage = 1;
const iuranItemsPerPage = 15;

function loadDataIuran() {
    const selectBulan = document.getElementById('filter-bulan-iuran').value;
    if (!selectBulan) return;
    
    const [bulan, tahun] = selectBulan.split('-');
    const container = document.getElementById('modal-iuran-list-container');
    container.innerHTML = '<p class="text-secondary text-center py-4">Memuat data iuran...</p>';
    window.currentIuranPage = 1; // Reset halaman ke 1 saat ganti bulan

    fetch(`api/get_iuran.php?blok_id=${window.currentBlokId}&bulan=${bulan}&tahun=${tahun}`)
        .then(async r => {
            const text = await r.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Server Response Error:", text);
                throw new Error("Respon Server bukan JSON. Pastikan tabel pembayaran_iuran ada, dan file api/get_iuran.php valid.");
            }
        })
        .then(data => {
            if(data.error) {
                container.innerHTML = `<p class="text-red text-center py-4">${data.message}</p>`;
                return;
            }
            window.currentIuranData = data.data;
            window.currentMasterIuran = data.master || [];
            filterIuranList();
        })
        .catch(e => {
            container.innerHTML = `<p class="text-red text-center py-4" style="font-size: 0.875rem;"><b>Error:</b> ${e.message}</p>`;
        });
}

function filterIuranList() {
    const q = document.getElementById('search-iuran-input').value.toLowerCase();
    const fStatus = document.getElementById('filter-status-iuran').value;
    
    let totalLunas = 0;
    let totalMenunggak = 0;
    let countLunas = 0;
    let countMenunggak = 0;

    const filtered = window.currentIuranData.filter(d => {
        const matchQ = d.nama_lengkap.toLowerCase().includes(q) || d.nomor_rumah.toLowerCase().includes(q);
        const matchS = fStatus === '' || d.status === fStatus;
        return matchQ && matchS;
    });

    // Akumulasi data ke Summary Cards
    filtered.forEach(d => {
        const tagihanInt = parseInt(d.total_tagihan) || 0;
        if (d.status === 'LUNAS') {
            totalLunas += tagihanInt;
            countLunas++;
        } else {
            totalMenunggak += tagihanInt;
            countMenunggak++;
        }
    });

    document.getElementById('summary-lunas').innerText = 'Rp ' + totalLunas.toLocaleString('id-ID');
    document.getElementById('summary-menunggak').innerText = 'Rp ' + totalMenunggak.toLocaleString('id-ID');
    document.getElementById('summary-count-lunas').innerHTML = `<i data-lucide="users" style="width: 14px; height: 14px;"></i> ${countLunas} Warga`;
    document.getElementById('summary-count-menunggak').innerHTML = `<i data-lucide="users" style="width: 14px; height: 14px;"></i> ${countMenunggak} Warga`;

    // Logika Paginasi (Membagi data per 15 baris)
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / iuranItemsPerPage);
    if (window.currentIuranPage > totalPages && totalPages > 0) window.currentIuranPage = totalPages;
    if (window.currentIuranPage < 1) window.currentIuranPage = 1;

    const startIndex = (window.currentIuranPage - 1) * iuranItemsPerPage;
    const endIndex = Math.min(startIndex + iuranItemsPerPage, totalItems);
    const paginatedItems = filtered.slice(startIndex, endIndex);

    const paginationContainer = document.getElementById('iuran-pagination');
    if (totalItems > 0) {
        paginationContainer.style.display = 'flex';
        document.getElementById('iuran-page-info').innerText = `Menampilkan ${startIndex + 1}-${endIndex} dari ${totalItems}`;
    } else {
        paginationContainer.style.display = 'none';
    }

    const container = document.getElementById('modal-iuran-list-container');
    if (paginatedItems.length === 0) {
        container.innerHTML = '<p class="text-secondary text-center py-4">Tidak ada data tagihan.</p>';
        lucide.createIcons();
        return;
    }

    let html = `
        <div class="report-header-row" style="padding: 12px 20px; background: var(--secondary-bg); border-radius: 12px; margin-bottom: 8px; font-weight: 700; font-size: 0.8rem; color: var(--text-secondary-color); display: grid; grid-template-columns: 40px 1fr 1fr; align-items: center;">
            <div style="display:flex; align-items:center;"><input type="checkbox" id="check-all-iuran-ws" onchange="toggleSelectAllIuranWorkspace(this)" style="width:18px; height:18px; cursor:pointer;" /></div>
            <div>Warga & Tagihan</div>
            <div style="text-align: right;">Status & Aksi</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
    `;
    paginatedItems.forEach(d => {
        const isLunas = d.status === 'LUNAS';
        const statusClass = isLunas ? 'bg-emerald-light text-emerald' : 'bg-red-light text-red';
        const warningBorder = isLunas ? '' : 'border-left: 4px solid #ef4444; background: rgba(239, 68, 68, 0.05);';
        
        // Format tombol WA untuk Tagihan
        let waLink = '';
        if (d.no_wa) {
            let cleanWa = d.no_wa.replace(/\D/g, ''); 
            if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
            waLink = `<a href="https://wa.me/${cleanWa}" target="_blank" class="button-secondary" style="border-radius: 50%; padding: 8px; color: #25D366; border-color: transparent; background: rgba(37, 211, 102, 0.1); box-shadow: none;" title="Chat WhatsApp" onclick="event.stopPropagation();"><i data-lucide="message-circle" style="width: 18px; height: 18px;"></i></a>`;
        }

        // Lencana Status Setoran (Di Bendahara vs Disetor RT)
        let statusSetorBadge = '';
        if (isLunas) {
            if (d.tgl_setor) {
                statusSetorBadge = `<span class="badge bg-purple-light text-purple" style="font-size: 0.7rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="check-check" style="width: 14px; height: 14px;"></i> Disetor RT</span>`;
            } else {
                statusSetorBadge = `<span class="badge bg-orange-light text-orange" style="font-size: 0.7rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="wallet" style="width: 14px; height: 14px;"></i> Di Bendahara</span>`;
            }
        }

        // Checkbox untuk Tandai Sebagian (Multiple Select)
        const checkboxHtml = !isLunas 
            ? `<input type="checkbox" class="iuran-checkbox" value="${d.id}" style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--accent-color);" onclick="event.stopPropagation();">` 
            : `<div style="width: 20px;"></div>`;

        const actionBtn = isLunas 
            ? `<div class="text-secondary" style="font-size: 0.75rem; text-align:right; display: flex; flex-direction: column; align-items: flex-end;"><span style="display: flex; align-items: center; gap: 4px;"><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> ${d.tgl_bayar}</span><span>Via: <b class="text-color">${d.metode_pembayaran || 'Cash'}</b></span></div>`
            : `<button class="button-primary button-sm" style="padding: 8px 16px; font-size: 0.75rem; border-radius: 12px; display: flex; align-items: center; gap: 6px;" onclick="bayarIuran(${d.id})"><i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Tandai Dibayar</button>`;

        // Tombol CRUD Detail, Edit, Hapus untuk Iuran
        const isValidated = d.tanggal_validasi_rt !== null;
        const editBtn = isValidated 
            ? `<button class="button-secondary" style="border-radius: 50%; padding: 8px; color: var(--text-secondary-color); opacity: 0.5; cursor: not-allowed;" title="Sudah validasi RT (Tidak bisa diedit)"><i data-lucide="edit-2" style="width: 16px; height: 16px;"></i></button>`
            : `<button onclick="editIuran(${d.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: var(--text-secondary-color); border-color: transparent; background: var(--hover-bg); box-shadow: none;" title="Edit Tagihan"><i data-lucide="edit-2" style="width: 16px; height: 16px;"></i></button>`;
        
        const hapusBtn = isValidated
            ? `<button class="button-secondary" style="border-radius: 50%; padding: 8px; color: #ef4444; opacity: 0.5; cursor: not-allowed;" title="Sudah validasi RT (Tidak bisa dihapus)"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>`
            : `<button onclick="hapusIuran(${d.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: #ef4444; border-color: transparent; background: rgba(239, 68, 68, 0.1); box-shadow: none;" title="Hapus Riwayat"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>`;

        const crudBtns = `
            <button onclick="detailIuran(${d.id})" class="button-secondary" style="border-radius: 50%; padding: 8px; color: var(--accent-color); border-color: transparent; background: color-mix(in srgb, var(--accent-color) 10%, transparent); box-shadow: none;" title="Detail Tagihan"><i data-lucide="file-text" style="width: 16px; height: 16px;"></i></button>
            ${editBtn}
            ${hapusBtn}
        `;

        // Rincian Komponen Tagihan dihapus sesuai permintaan
        let breakdownHtml = '';

        html += `
            <div class="glass-card" style="padding: 20px; display: flex; flex-direction: column; gap: 16px; ${warningBorder}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                    
                    <!-- Info Warga -->
                    <div class="list-item-left" style="flex: 1; min-width: 240px; display: flex; align-items: center; gap: 16px;">
                        ${checkboxHtml}
                        <div class="avatar bg-emerald-light text-emerald" style="width: 48px; height: 48px; font-size: 1.2rem;">${d.nama_lengkap.charAt(0)}</div>
                        <div class="list-item-content">
                            <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color); font-weight: 700;">${d.nama_lengkap}</h4>
                            <p class="list-item-subtitle" style="font-size: 0.85rem; margin-top: 4px;">Blok No: <b>${d.nomor_rumah}</b></p>
                            <p class="list-item-subtitle" style="font-size: 0.85rem; margin-top: 2px;">Tagihan: <b class="text-color">Rp ${parseInt(d.total_tagihan).toLocaleString('id-ID')}</b></p>
                        </div>
                    </div>
                    
                    <!-- Aksi & Status -->
                    <div class="list-item-right" style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px; flex: 1; min-width: 240px;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; width: 100%;">
                            <span class="badge ${statusClass}" style="font-size: 0.7rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="${isLunas ? 'check-circle' : 'alert-circle'}" style="width: 14px; height: 14px;"></i> ${isLunas ? 'LUNAS' : 'BELUM BAYAR'}</span>
                            ${statusSetorBadge}
                        </div>
                        <div style="display: flex; justify-content: flex-end; width: 100%;">
                            ${actionBtn}
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin-top: 4px;">
                            ${waLink}
                            ${waLink ? '<div style="width: 1px; height: 24px; background-color: var(--border-color); margin: 0 4px;"></div>' : ''}
                            ${crudBtns}
                        </div>
                    </div>
                </div>
                ${breakdownHtml}
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    lucide.createIcons();

    // Page Numbers
    const pageNumbers = document.getElementById('iuran-page-numbers');
    if (pageNumbers) {
        pageNumbers.innerHTML = '';
        const maxVisible = 5;
        let startPage = Math.max(1, window.currentIuranPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className = i === window.currentIuranPage ? 'button-primary button-sm' : 'button-secondary button-sm';
            btn.style.padding = '8px 12px';
            btn.style.borderRadius = '8px';
            btn.onclick = () => { window.currentIuranPage = i; filterIuranList(); };
            pageNumbers.appendChild(btn);
        }
    }
}

function prevPageIuran() {
    if (window.currentIuranPage > 1) {
        window.currentIuranPage--;
        filterIuranList();
    }
}

function nextPageIuran() {
    const q = document.getElementById('search-iuran-input').value.toLowerCase();
    const fStatus = document.getElementById('filter-status-iuran').value;
    const filtered = window.currentIuranData.filter(d => (d.nama_lengkap.toLowerCase().includes(q) || d.nomor_rumah.toLowerCase().includes(q)) && (fStatus === '' || d.status === fStatus));
    
    const totalPages = Math.ceil(filtered.length / iuranItemsPerPage);
    if (window.currentIuranPage < totalPages) {
        window.currentIuranPage++;
        filterIuranList();
    }
}

window.toggleSelectAllIuranWorkspace = function(master) {
    const checkboxes = document.querySelectorAll('#modal-iuran-list-container .iuran-checkbox');
    checkboxes.forEach(cb => {
        if (!cb.disabled) cb.checked = master.checked;
    });
}



function bayarIuran(wargaId) {
    document.getElementById('bayar-iuran-id').value = wargaId;
    
    // Gunakan fungsi lokal agar tanggal form tidak blank/error
    document.getElementById('bayar-tanggal').value = getLocalDateString();
    
    document.getElementById('modal-bayar-iuran').classList.remove('hidden');
}

function submitBayarIuran(btn) {
    const id = document.getElementById('bayar-iuran-id').value;
    const metode = document.getElementById('bayar-metode').value;
    const tanggal = document.getElementById('bayar-tanggal').value;
    
    btn.innerHTML = "Memproses...";
    const fd = new FormData(); fd.append('id', id); fd.append('metode', metode); fd.append('tanggal', tanggal);
    
    fetch('api/bayar_iuran.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
        if(res.status === 'success') { document.getElementById('modal-bayar-iuran').classList.add('hidden'); loadDataIuran(); }
        else alert("Gagal: " + res.message);
        btn.innerHTML = `<i data-lucide="check-circle" style="margin-right: 8px;"></i> Konfirmasi Pembayaran`;
        lucide.createIcons();
    });
}

window.bayarSemuaIuran = function() {
    Swal.fire({
        title: 'Tandai Semua Lunas?',
        text: "Semua tagihan yang belum bayar di bulan ini akan ditandai LUNAS.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Ya, Bayar Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const selectBulan = document.getElementById('filter-bulan-iuran').value;
            const [bulan, tahun] = selectBulan.split('-');
            const fd = new FormData(); fd.append('blok_id', window.currentBlokId); fd.append('bulan', bulan); fd.append('tahun', tahun);
            
            showToast("Memproses pembayaran semua...", 'info');
            fetch('api/bayar_semua_iuran.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
                if(res.status === 'success') { 
                    showToast(`${res.updated} warga berhasil ditandai LUNAS.`); 
                    loadDataIuran(); 
                } else {
                    showToast("Gagal: " + res.message, 'error');
                }
            });
        }
    });
}


window.bayarTerpilihIuran = function() {
    const checkboxes = document.querySelectorAll('#modal-iuran-list-container .iuran-checkbox:checked');
    if (checkboxes.length === 0) {
        showToast("Pilih minimal satu tagihan untuk ditandai lunas.", 'warning');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: `Tandai ${checkboxes.length} tagihan terpilih menjadi LUNAS?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Bayar Sekarang',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = Array.from(checkboxes).map(cb => cb.value);
            const fd = new FormData(); 
            fd.append('ids', JSON.stringify(ids));
            
            showToast("Memproses pembayaran massal...", 'info');
            
            fetch('api/bayar_sebagian_iuran.php', {method: 'POST', body: fd})
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') { 
                    showToast(`${res.updated} tagihan berhasil ditandai LUNAS.`);
                    loadDataIuran(); 
                } else {
                    showToast("Gagal: " + res.message, 'error');
                }
            }).catch(e => {
                showToast("Terjadi kesalahan koneksi.", 'error');
                console.error(e);
            });
        }
    });
}


function setorKeRT() {
    document.getElementById('setor-tanggal').value = getLocalDateString();
    document.getElementById('modal-setor-rt').classList.remove('hidden');
}

function submitSetorRT(btn) {
    const selectBulan = document.getElementById('filter-bulan-iuran').value;
    const [bulan, tahun] = selectBulan.split('-');
    const tanggal = document.getElementById('setor-tanggal').value;

    btn.innerHTML = "Memproses...";
    const fd = new FormData();
    fd.append('blok_id', window.currentBlokId);
    fd.append('bulan', bulan);
    fd.append('tahun', tahun);
    fd.append('tanggal', tanggal);

    fetch('api/setor_kas_rt.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
        if(res.status === 'success') {
            document.getElementById('modal-setor-rt').classList.add('hidden');
            alert(`Berhasil menyetorkan kas blok ke RT Pusat!`);
            loadDataIuran();
        } else alert("Gagal: " + res.message);
        btn.innerHTML = `<i data-lucide="send" style="margin-right: 8px;"></i> Konfirmasi Setoran`;
        lucide.createIcons();
    }).catch(e => { alert("Kesalahan koneksi."); btn.innerHTML = `<i data-lucide="send" style="margin-right: 8px;"></i> Konfirmasi Setoran`; lucide.createIcons(); });
}

function detailIuran(id) {
    const w = window.currentIuranData.find(x => x.id == id);
    const tagihanUtama = parseInt(w.total_tagihan);
    document.getElementById('detail-iuran-total').innerText = 'Rp ' + tagihanUtama.toLocaleString('id-ID');
    
    document.getElementById('detail-iuran-list').innerHTML = `<p class="text-secondary text-center" style="font-size: 0.875rem; padding: 10px 0;">Pilih tombol "Detail" untuk rincian selengkapnya.</p>`;
    document.getElementById('modal-detail-iuran').classList.remove('hidden');
}
function toggleEditIuranDates(status) {
    const container = document.getElementById('edit-iuran-dates-container');
    container.style.display = (status === 'LUNAS') ? 'block' : 'none';
}
function editIuran(id) {
    const w = window.currentIuranData.find(x => x.id == id);
    document.getElementById('edit-iuran-id').value = id;
    document.getElementById('edit-iuran-nominal').value = w.total_tagihan;
    document.getElementById('edit-iuran-status').value = w.status;
    document.getElementById('edit-iuran-metode').value = w.metode_pembayaran || 'Cash';
    
    if (w.tanggal_bayar) document.getElementById('edit-iuran-tgl-bayar').value = w.tanggal_bayar.split(' ')[0];
    else document.getElementById('edit-iuran-tgl-bayar').value = getLocalDateString();
    
    if (w.tanggal_setor) document.getElementById('edit-iuran-tgl-setor').value = w.tanggal_setor.split(' ')[0];
    else document.getElementById('edit-iuran-tgl-setor').value = '';

    toggleEditIuranDates(w.status);
    document.getElementById('modal-edit-iuran').classList.remove('hidden');
}
function submitEditIuran(btn) {
    const origText = btn.innerHTML;
    btn.innerHTML = "Memproses...";
    
    const fd = new FormData();
    fd.append('id', document.getElementById('edit-iuran-id').value);
    fd.append('tagihan', document.getElementById('edit-iuran-nominal').value);
    fd.append('status', document.getElementById('edit-iuran-status').value);
    fd.append('metode', document.getElementById('edit-iuran-metode').value);
    fd.append('tgl_bayar', document.getElementById('edit-iuran-tgl-bayar').value);
    fd.append('tgl_setor', document.getElementById('edit-iuran-tgl-setor').value);
    fetch('api/edit_iuran.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
        if(res.status==='success') { document.getElementById('modal-edit-iuran').classList.add('hidden'); loadDataIuran(); }
        else { alert("Gagal: " + (res.message || "Error saat update")); }
        btn.innerHTML = origText;
    });
}
function hapusIuran(id) {
    if(confirm("PERINGATAN:\nYakin ingin menghapus riwayat tagihan ini secara permanen?")) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/hapus_iuran.php', {method: 'POST', body: fd}).then(r=>r.json()).then(res=>{
            if(res.status==='success') loadDataIuran();
        });
    }
}

// --- Master Iuran Drawer ---
function openMasterIuran() {
    const drawer = document.getElementById('drawer-master-iuran');
    drawer.classList.remove('hidden');
    setTimeout(() => drawer.classList.add('drawer-active'), 50);
    
    // Mengambil data master iuran secara mandiri agar bisa langsung diakses dari dashboard ringkasan
    fetch(`api/get_master_iuran.php?blok_id=${window.currentBlokId}`)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            window.currentMasterIuran = res.data;
        }
        renderMasterIuranList();
    }).catch(() => renderMasterIuranList());
}
function closeMasterIuran() {
    const drawer = document.getElementById('drawer-master-iuran');
    drawer.classList.remove('drawer-active');
    setTimeout(() => drawer.classList.add('hidden'), 400);
}
function renderMasterIuranList() {
    const container = document.getElementById('master-iuran-list');
    const komponen = window.currentMasterIuran;
    
    let total = 0; let html = '';
    komponen.forEach((k, idx) => {
        const nom = parseInt(k.nominal);
        total += nom;
        html += `
            <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                <input type="text" name="master_nama[]" class="input-field flex-grow" value="${k.nama_komponen}" placeholder="Nama Komponen" oninput="updateMasterTotal()">
                <input type="number" name="master_nominal[]" class="input-field" value="${nom}" style="width: 140px;" placeholder="Nominal (Rp)" oninput="updateMasterTotal()">
                <button type="button" class="button-secondary" style="border-radius: 50%; color: #ef4444; padding: 10px;" onclick="this.parentElement.remove(); updateMasterTotal();"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>
            </div>`;
    });
    container.innerHTML = html;
    document.getElementById('total-master-iuran').innerText = 'Rp ' + total.toLocaleString('id-ID');
    lucide.createIcons();
}
function updateMasterTotal() {
    let t = 0;
    document.querySelectorAll('input[name="master_nominal[]"]').forEach(el => { t += (parseInt(el.value) || 0); });
    document.getElementById('total-master-iuran').innerText = 'Rp ' + t.toLocaleString('id-ID');
}
function addMasterIuranField() {
    const div = document.createElement('div');
    div.style.cssText = 'display: flex; gap: 12px; align-items: center; margin-bottom: 12px; animation: pageFadeIn 0.3s forwards;';
    div.innerHTML = `<input type="text" name="master_nama[]" class="input-field flex-grow" placeholder="Cth: Uang Agustusan" oninput="updateMasterTotal()"><input type="number" name="master_nominal[]" class="input-field" style="width: 140px;" placeholder="Nominal (Rp)" oninput="updateMasterTotal()"><button type="button" class="button-secondary" style="border-radius: 50%; color: #ef4444; padding: 10px;" onclick="this.parentElement.remove(); updateMasterTotal();"><i data-lucide="trash-2" style="width: 16px; height: 16px;"></i></button>`;
    document.getElementById('master-iuran-list').appendChild(div);
    lucide.createIcons();
}
function simpanMasterIuran() { 
    const btn = document.querySelector('#drawer-master-iuran .button-primary');
    const orig = btn.innerHTML; btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';

    const fd = new FormData();
    fd.append('blok_id', window.currentBlokId); // Tambahkan ID Blok
    document.querySelectorAll('input[name="master_nama[]"]').forEach(el => fd.append('komponen[]', el.value));
    document.querySelectorAll('input[name="master_nominal[]"]').forEach(el => fd.append('nominal[]', el.value));
    
    fetch('api/simpan_master_iuran.php', { method: 'POST', body: fd })
    .then(async r => {
        const text = await r.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Server Error:", text);
            throw new Error("Respon Server bukan JSON. Cek console log atau pastikan file api/simpan_master_iuran.php ada.");
        }
    })
    .then(res => {
        if (res.status === 'success') { closeMasterIuran(); loadDataIuran(); } 
        else alert('Gagal: ' + res.message);
        btn.innerHTML = orig;
    }).catch(e => { alert('Error Server:\n' + e.message); btn.innerHTML = orig; lucide.createIcons(); });
}

// --- Rekonsiliasi & Audit Kas ---
function openRekonsiliasi() {
    document.getElementById('modal-rekonsiliasi').classList.remove('hidden');
    loadRekonsiliasi();
}

function loadRekonsiliasi() {
    const container = document.getElementById('rekonsiliasi-list');
    container.innerHTML = '<p class="text-center text-secondary py-4">Menghitung dan memuat data rekonsiliasi...</p>';
    document.getElementById('rekon-total-warga').innerText = 'Memuat...';

    fetch(`api/get_rekonsiliasi.php?blok_id=${window.currentBlokId}`)
    .then(r => r.json())
    .then(res => {
        if (res.status !== 'success') { container.innerHTML = `<p class="text-red text-center">${res.message}</p>`; return; }

        document.getElementById('rekon-bulan').value = res.periode_bulan;
        document.getElementById('rekon-tahun').value = res.periode_tahun;

        if (res.data.length === 0) {
            container.innerHTML = '<div class="glass-card" style="padding: 32px; text-align: center; border-color: var(--accent-color);"><i data-lucide="check-circle" style="color: var(--accent-color); width: 48px; height: 48px; margin: 0 auto 16px auto;"></i><p class="font-bold text-color" style="font-size: 1.2rem;">Luar Biasa!</p><p class="text-secondary" style="font-size: 0.875rem;">Semua warga di blok ini telah melunasi iurannya secara penuh.</p></div>';
            document.getElementById('rekon-total-warga').innerText = '0 Warga';
            document.getElementById('rekon-total-warga').className = 'badge bg-emerald-light text-emerald';
            lucide.createIcons(); return;
        }

        document.getElementById('rekon-total-warga').innerText = `${res.data.length} Warga Menunggak`;
        document.getElementById('rekon-total-warga').className = 'badge bg-red-light text-red';

        let html = '';
        res.data.forEach(w => {
            let waLink = '';
            if (w.no_wa) {
                let cleanWa = w.no_wa.replace(/\D/g, ''); if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
                const msg = encodeURIComponent(`Halo Bp/Ibu ${w.nama_lengkap},\nMenginformasikan dari sistem Kas Blok bahwa terdapat *tunggakan iuran wajib sebanyak ${w.tunggakan_bulan} Bulan* (Estimasi: Rp ${w.estimasi_hutang.toLocaleString('id-ID')}).\nMohon konfirmasi pembayarannya ya. Terima kasih.`);
                waLink = `<a href="https://wa.me/${cleanWa}?text=${msg}" target="_blank" class="button-secondary" style="border-radius: 50%; padding: 8px; color: #25D366; border-color: transparent; background: rgba(37, 211, 102, 0.1); box-shadow: none;" title="Kirim Tagihan WA"><i data-lucide="message-circle" style="width: 16px; height: 16px;"></i></a>`;
            }

            html += `<div class="glass-card" style="padding: 16px; border-left: 4px solid #ef4444; display: flex; justify-content: space-between; align-items: center;"><div><p class="font-bold text-color" style="margin: 0; font-size: 1rem;">${w.nama_lengkap}</p><p class="text-secondary" style="font-size: 0.75rem; margin-top: 4px;">Blok No: ${w.nomor_rumah}</p></div><div style="display: flex; align-items: center; gap: 16px;"><div style="text-align: right;"><p class="text-red font-bold" style="margin: 0; font-size: 0.9rem;">${w.tunggakan_bulan} Bulan</p><p class="text-secondary" style="font-size: 0.7rem; margin-top: 2px;">~ Rp ${w.estimasi_hutang.toLocaleString('id-ID')}</p></div>${waLink}</div></div>`;
        });
        container.innerHTML = html;
        lucide.createIcons();
    });
}

function simpanPeriodeRekon(btn) {
    const orig = btn.innerHTML; btn.innerHTML = '<i data-lucide="loader"></i>'; lucide.createIcons();

    const fd = new FormData();
    fd.append('blok_id', window.currentBlokId);
    fd.append('bulan', document.getElementById('rekon-bulan').value);
    fd.append('tahun', document.getElementById('rekon-tahun').value);

    fetch('api/simpan_periode_blok.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        btn.innerHTML = orig; // Kembalikan bentuk tombol
        lucide.createIcons();
        if(res.status === 'success') loadRekonsiliasi();
        else { alert('Gagal menyimpan periode: ' + res.message); }
    })
    .catch(e => {
        btn.innerHTML = orig; // Kembalikan bentuk tombol jika error
        lucide.createIcons();
        alert('Terjadi kesalahan koneksi jaringan.');
    });
}
