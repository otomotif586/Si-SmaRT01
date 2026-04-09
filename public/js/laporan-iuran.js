// // // --- Laporan Iuran Blok (Pusat) LOGIC ---
window.currentLaporanBlokData = [];
window.filteredLaporanBlokData = [];
window.laporanBlokCurrentPage = 1;
window.laporanBlokRowsPerPage = 15;
window.laporanBlokSortCol = 'nama_blok';
window.laporanBlokSortDir = 'asc';
window.laporanBlokActiveTab = 'belum_posting';

function initLaporanIuranBlok() {
    const selBulan = document.getElementById('filter-bulan-laporan');
    const selTahun = document.getElementById('filter-tahun-laporan');
    
    if (selBulan.options.length === 0) {
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

        let defaultMonth = now.getMonth() - 1;
        let defaultYear = currentYear;
        if (defaultMonth < 0) { defaultMonth = 11; defaultYear -= 1; }
        
        selBulan.value = defaultMonth;
        selTahun.value = defaultYear;
    }
    loadLaporanIuranBlok();
}

function validateIuranRT(id) {
    const fd = new FormData();
    fd.append('iuran_id', id);
    
    fetch('api/validate_iuran_rt.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            showToast(res.message);
            loadLaporanIuranBlok(); // Refresh laporan pusat
        } else {
            showToast(res.message, 'error');
        }
    }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
}

function bulkValidateIuranRT() {
    const bulan = document.getElementById('filter-bulan-laporan').value;
    const tahun = document.getElementById('filter-tahun-laporan').value;
    const blokId = document.getElementById('filter-blok-laporan').value;

    Swal.fire({
        title: 'Validasi Semua?',
        text: "Validasi SEMUA iuran untuk periode ini? Ini akan memverifikasi seluruh setoran yang sudah masuk.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--accent-color)',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Validasi Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('bulan', bulan);
            fd.append('tahun', tahun);
            fd.append('blok_id', blokId);

            showToast("Memproses validasi...", 'info');
            fetch('api/bulk_validate_iuran_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function unlockIuranRT(id) {
    Swal.fire({
        title: 'Tarik Validasi?',
        text: "Ini akan memungkinkan Bendahara Blok untuk mengedit data ini kembali.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tarik!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('iuran_id', id);

            showToast("Memproses data...", 'info');
            fetch('api/unlock_iuran_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function bulkUnlockIuranRT() {
    const bulan = document.getElementById('filter-bulan-laporan').value;
    const tahun = document.getElementById('filter-tahun-laporan').value;
    const blokId = document.getElementById('filter-blok-laporan').value;

    Swal.fire({
        title: 'Tarik Semua Validasi?',
        text: "Tarik SEMUA validasi untuk periode ini? Seluruh iuran pada bulan ini akan dapat diedit kembali oleh tiap blok.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tarik Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('bulan', bulan);
            fd.append('tahun', tahun);
            fd.append('blok_id', blokId);

            showToast("Memproses data...", 'info');
            fetch('api/bulk_unlock_iuran_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function toggleSelectAllIuran(checkbox) {
    const items = document.querySelectorAll('.check-iuran-item');
    items.forEach(item => {
        if (!item.disabled) item.checked = checkbox.checked;
    });
}

function getSelectedIuranIds() {
    const checked = document.querySelectorAll('.check-iuran-item:checked');
    return Array.from(checked).map(c => c.value);
}

function validateSelectedIuran() {
    const ids = getSelectedIuranIds();
    if(ids.length === 0) {
        showToast("Pilih minimal satu data untuk divalidasi.", 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Validasi Terpilih?',
        text: `Validasi ${ids.length} rincian setoran terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--accent-color)',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Validasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('ids', JSON.stringify(ids));

            showToast("Memproses validasi terpilih...", 'info');
            fetch('api/validate_selected_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function unlockSelectedIuran() {
    const ids = getSelectedIuranIds();
    if(ids.length === 0) {
        showToast("Pilih minimal satu data untuk ditarik validasinya.", 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Tarik Validasi Terpilih?',
        text: `Tarik validasi ${ids.length} rincian setoran terpilih? Data akan kembali bisa diedit oleh Bendahara Blok.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tarik Validasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('ids', JSON.stringify(ids));

            showToast("Memproses tarik validasi...", 'info');
            fetch('api/bulk_unlock_iuran_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function postSelectedIuran() {
    const ids = getSelectedIuranIds();
    if(ids.length === 0) {
        showToast("Pilih minimal satu data valid untuk diposting.", 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Posting Terpilih?',
        text: `Posting ${ids.length} rincian setoran ke Jurnal Keuangan?\nData yang sudah di-posting bersifat permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Posting!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('ids', JSON.stringify(ids));

            showToast("Memproses posting...", 'info');
            fetch('api/post_jurnal_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function bulkPostIuranRT() {
    const bulan = document.getElementById('filter-bulan-laporan').value;
    const tahun = document.getElementById('filter-tahun-laporan').value;
    const blokId = document.getElementById('filter-blok-laporan').value;

    Swal.fire({
        title: 'Posting Semua?',
        text: "Posting SEMUA setoran yang sudah divalidasi ke Jurnal Keuangan? Perhatian: Data yang sudah di-posting bersifat permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Posting Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('bulan', bulan);
            fd.append('tahun', tahun);
            fd.append('blok_id', blokId);

            showToast("Memproses posting semua...", 'info');
            fetch('api/bulk_post_jurnal_rt.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    showToast(res.message);
                    loadLaporanIuranBlok();
                } else showToast(res.message, 'error');
            }).catch(e => showToast("Terjadi kesalahan koneksi.", 'error'));
        }
    });
}

function prevMonthLaporanBlok() {
    const selBulan = document.getElementById('filter-bulan-laporan');
    const selTahun = document.getElementById('filter-tahun-laporan');
    if (selBulan.selectedIndex > 0) {
        selBulan.selectedIndex--;
        loadLaporanIuranBlok();
    } else if (selTahun.selectedIndex < selTahun.options.length - 1) {
        selBulan.selectedIndex = 11;
        selTahun.selectedIndex++; // Karena tahun diurutkan menurun (descending)
        loadLaporanIuranBlok();
    }
}

function nextMonthLaporanBlok() {
    const selBulan = document.getElementById('filter-bulan-laporan');
    const selTahun = document.getElementById('filter-tahun-laporan');
    if (selBulan.selectedIndex < 11) {
        selBulan.selectedIndex++;
        loadLaporanIuranBlok();
    } else if (selTahun.selectedIndex > 0) {
        selBulan.selectedIndex = 0;
        selTahun.selectedIndex--; // Karena tahun diurutkan menurun (descending)
        loadLaporanIuranBlok();
    }
}

function loadLaporanIuranBlok() {
    const bulan = document.getElementById('filter-bulan-laporan').value;
    const tahun = document.getElementById('filter-tahun-laporan').value;
    
    const container = document.getElementById('laporan-blok-container');
    container.innerHTML = '<p class="text-secondary text-center py-5">Memuat data setoran...</p>';

    fetch(`api/get_laporan_iuran_blok.php?bulan=${bulan}&tahun=${tahun}`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            window.currentLaporanBlokData = res.data;
            window.filteredLaporanBlokData = [...res.data];
            window.laporanBlokCurrentPage = 1;

            const sum = res.summary;
            document.getElementById('report-stat-blok').innerText = (sum.global_total_blok_setor || 0) + ' Blok';
            document.getElementById('report-stat-total').innerText = 'Rp ' + (parseInt(sum.global_total_setoran || 0)).toLocaleString('id-ID');
            document.getElementById('report-stat-warga').innerText = (sum.global_total_warga_bayar || 0) + ' KK';
            
            // Fill Block Filter Dropdown
            const blockSelect = document.getElementById('filter-blok-laporan');
            const currentVal = blockSelect.value;
            blockSelect.innerHTML = '<option value="all">Semua Blok</option>';
            res.blocks.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.text = b.nama_blok;
                blockSelect.appendChild(opt);
            });
            blockSelect.value = currentVal || 'all';

            // Fix: Respect current tab filter after loading
            filterLaporanBlok();
        } else {
            container.innerHTML = `<p class="text-red text-center py-5">${res.message}</p>`;
        }
    });
}

function sortLaporanBy(col) {
    if (window.laporanBlokSortCol === col) {
        window.laporanBlokSortDir = window.laporanBlokSortDir === 'asc' ? 'desc' : 'asc';
    } else {
        window.laporanBlokSortCol = col;
        window.laporanBlokSortDir = 'asc';
    }
    renderLaporanIuranBlok();
}

function renderLaporanIuranBlok() {
    const container = document.getElementById('laporan-blok-container');
    const emptyMsg = document.getElementById('laporan-blok-empty');
    const pagination = document.getElementById('laporan-blok-pagination');
    
    let data = [...window.filteredLaporanBlokData];

    if (data.length === 0) {
        container.innerHTML = '';
        emptyMsg.classList.remove('hidden');
        pagination.classList.add('hidden');
        return;
    }
    
    emptyMsg.classList.add('hidden');
    pagination.classList.remove('hidden');

    // Applied Sorting
    data.sort((a, b) => {
        let valA = a[window.laporanBlokSortCol];
        let valB = b[window.laporanBlokSortCol];
        
        if (window.laporanBlokSortCol === 'total_tagihan') {
            valA = parseFloat(valA);
            valB = parseFloat(valB);
        } else {
            valA = (valA || '').toString().toLowerCase();
            valB = (valB || '').toString().toLowerCase();
        }

        if (valA < valB) return window.laporanBlokSortDir === 'asc' ? -1 : 1;
        if (valA > valB) return window.laporanBlokSortDir === 'asc' ? 1 : -1;
        return 0;
    });

    // Pagination Logic
    const totalRows = data.length;
    const totalPages = Math.ceil(totalRows / window.laporanBlokRowsPerPage);
    if (window.laporanBlokCurrentPage > totalPages) window.laporanBlokCurrentPage = totalPages || 1;
    
    const start = (window.laporanBlokCurrentPage - 1) * window.laporanBlokRowsPerPage;
    const end = start + window.laporanBlokRowsPerPage;
    const paginatedData = data.slice(start, end);

    // Update Pagination Info
    document.getElementById('laporan-blok-pagination-info').innerText = `Menampilkan ${start + 1} - ${Math.min(end, totalRows)} dari ${totalRows} data`;
    document.getElementById('btn-prev-laporan-page').disabled = window.laporanBlokCurrentPage === 1;
    document.getElementById('btn-next-laporan-page').disabled = window.laporanBlokCurrentPage === totalPages;

    const getIcon = (col) => {
        if (window.laporanBlokSortCol !== col) return '<i data-lucide="chevrons-up-down" style="width:12px; height:12px; opacity:0.3; margin-left:4px;"></i>';
        return window.laporanBlokSortDir === 'asc' 
            ? '<i data-lucide="chevron-up" style="width:12px; height:12px; margin-left:4px;"></i>' 
            : '<i data-lucide="chevron-down" style="width:12px; height:12px; margin-left:4px;"></i>';
    };

    let html = `
        <div class="report-header-row">
            <div style="display:flex; align-items:center;">${window.laporanBlokActiveTab === 'sudah_posting' ? '<i data-lucide="lock" style="width:14px; height:14px; margin-left:2px; opacity:0.5;"></i>' : '<input type="checkbox" id="check-all-iuran" onchange="toggleSelectAllIuran(this)" style="width:16px; height:16px; cursor:pointer;" />'}</div>
            <div onclick="sortLaporanBy('nama_lengkap')" style="cursor:pointer; display:flex; align-items:center;">Nama Warga ${getIcon('nama_lengkap')}</div>
            <div onclick="sortLaporanBy('nama_blok')" style="cursor:pointer; display:flex; align-items:center;">Blok - No ${getIcon('nama_blok')}</div>
            <div onclick="sortLaporanBy('total_tagihan')" style="cursor:pointer; display:flex; align-items:center; justify-content:flex-end;">Nominal ${getIcon('total_tagihan')}</div>
            <div onclick="sortLaporanBy('tanggal_bayar')" style="cursor:pointer; display:flex; align-items:center; justify-content:flex-end;">Tgl Bayar ${getIcon('tanggal_bayar')}</div>
            <div onclick="sortLaporanBy('tanggal_setor')" style="cursor:pointer; display:flex; align-items:center; justify-content:flex-end;">Tgl Setor ${getIcon('tanggal_setor')}</div>
            <div style="text-align:right;">Aksi & Status</div>
        </div>
    `;

    paginatedData.forEach(d => {
        const initial = d.nama_lengkap.charAt(0).toUpperCase();
        const tglBayar = d.tanggal_bayar ? d.tanggal_bayar.split(' ')[0] : '-';
        const tglSetor = d.tanggal_setor ? d.tanggal_setor.split(' ')[0] : '-';
        
        let waBtn = '';
        if (d.no_wa) {
            let cleanWa = d.no_wa.replace(/\D/g, ''); 
            if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
            waBtn = `<a href="https://wa.me/${cleanWa}" target="_blank" class="button-secondary" style="border-radius:50%; padding:6px; color:#10b981; background:rgba(16,185,129,0.1); border:none;" title="WhatsApp Warga"><i data-lucide="message-circle" style="width:14px; height:14px;"></i></a>`;
        }

        const isValidated = d.tanggal_validasi_rt !== null;
        const isPosted = d.tanggal_posting !== null;
        
        // Define Checkbox - disabled if posted
        let checkboxHtml = '';
        if (isPosted) {
            checkboxHtml = `<div style="display:flex; align-items:center; opacity:0.3;" title="Sudah diposting"><i data-lucide="lock" style="width:14px; height:14px;"></i></div>`;
        } else {
            checkboxHtml = `<div style="display:flex; align-items:center;"><input type="checkbox" class="check-iuran-item" value="${d.iuran_id}" style="width:16px; height:16px; cursor:pointer;" /></div>`;
        }

        let actionItem = '';
        if (isPosted) {
            actionItem = `
                <div style="display:flex; align-items:center; gap:8px; justify-content:flex-end;">
                    <span class="badge bg-blue-light text-blue" style="font-size:0.65rem; padding:4px 8px;"><i data-lucide="archive" style="width:12px; height:12px;"></i> Posted</span>
                </div>
            `;
        } else if (isValidated) {
            actionItem = `
                <div style="display:flex; align-items:center; gap:8px; justify-content:flex-end;">
                    <span class="badge bg-emerald-light text-emerald" style="font-size:0.65rem; padding:4px 8px;"><i data-lucide="check-check" style="width:12px; height:12px;"></i> Valid</span>
                    <button onclick="unlockIuranRT(${d.iuran_id})" class="button-secondary" style="border-radius:50%; padding:6px; color:#f59e0b; background:rgba(245,158,11,0.1); border:none;" title="Tarik Validasi (Buka Kunci)"><i data-lucide="rotate-ccw" style="width:14px; height:14px;"></i></button>
                </div>
            `;
        } else {
            actionItem = `<button class="button-primary button-sm" style="font-size:0.65rem; padding:6px 12px; border-radius:8px;" onclick="validateIuranRT(${d.iuran_id})"><i data-lucide="check" style="width:14px; height:14px; margin-right:4px;"></i> Cek RT</button>`;
        }

        html += `
            <div class="report-row">
                ${checkboxHtml}
                <div class="report-warga-info">
                    <div class="report-avatar bg-blue-light text-blue">${initial}</div>
                    <div style="overflow:hidden;">
                        <h4 style="margin:0; font-size:0.95rem; color:var(--text-color);">${d.nama_lengkap}</h4>
                        <p class="text-secondary" style="font-size:0.7rem; margin-top:2px;">Metode: ${d.metode_pembayaran}</p>
                    </div>
                </div>
                <div class="report-blok-no text-color" style="font-weight:600; font-size:0.875rem;">${d.nama_blok} - ${d.nomor_rumah || '-'}</div>
                <div style="text-align:right;" class="report-amount text-emerald">Rp ${parseInt(d.total_tagihan).toLocaleString('id-ID')}</div>
                <div style="text-align:right; font-size:0.8rem; color:var(--text-secondary-color);" class="report-date">${tglBayar}</div>
                <div style="text-align:right; font-size:0.8rem; color:var(--accent-color); font-weight:600;" class="report-date">${tglSetor}</div>
                <div class="report-action-btns" style="display:flex; justify-content:flex-end; align-items:center; gap:12px;">
                    ${waBtn}
                    ${actionItem}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    
    // Page Numbers
    const pageNumbers = document.getElementById('laporan-page-numbers');
    pageNumbers.innerHTML = '';
    const maxVisible = 5;
    let startPage = Math.max(1, window.laporanBlokCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);

    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.innerText = i;
        btn.className = i === window.laporanBlokCurrentPage ? 'button-primary button-sm' : 'button-secondary button-sm';
        btn.style.padding = '8px 12px';
        btn.style.borderRadius = '8px';
        btn.onclick = () => { window.laporanBlokCurrentPage = i; renderLaporanIuranBlok(); };
        pageNumbers.appendChild(btn);
    }

    lucide.createIcons();
}

function changeLaporanPage(dir) {
    window.laporanBlokCurrentPage += dir;
    renderLaporanIuranBlok();
}

function filterLaporanBlok() {
    const q = document.getElementById('search-laporan-blok').value.toLowerCase();
    const blockId = document.getElementById('filter-blok-laporan').value;
    
    window.filteredLaporanBlokData = window.currentLaporanBlokData.filter(d => {
        const matchSearch = (d.nama_lengkap && d.nama_lengkap.toLowerCase().includes(q)) || 
                          (d.nama_blok && d.nama_blok.toLowerCase().includes(q)) ||
                          (d.nomor_rumah && d.nomor_rumah.toLowerCase().includes(q));
        const matchBlock = (blockId === 'all' || d.blok_id == blockId);
        
        let matchTab = false;
        const datePosted = d.tanggal_posting;
        const isActuallyPosted = datePosted !== null && datePosted !== '' && datePosted !== 'null';
        
        if (window.laporanBlokActiveTab === 'belum_posting') matchTab = !isActuallyPosted;
        if (window.laporanBlokActiveTab === 'sudah_posting') matchTab = isActuallyPosted;

        return matchSearch && matchBlock && matchTab;
    });

    window.laporanBlokCurrentPage = 1;
    renderLaporanIuranBlok();
}

function switchLaporanIuranTab(tabName, btnElement) {
    if (window.laporanBlokActiveTab === tabName) return;
    
    window.laporanBlokActiveTab = tabName;
    
    // Reset classes
    document.querySelectorAll('.tab-pill-btn').forEach(btn => {
        btn.classList.remove('active-tab-pending', 'active-tab-posted');
    });

    // Apply specific class
    if (tabName === 'belum_posting') {
        btnElement.classList.add('active-tab-pending');
    } else {
        btnElement.classList.add('active-tab-posted');
    }
    
    const isSudah = tabName === 'sudah_posting';
    const buttonsToToggle = document.querySelectorAll('.header-actions button.button-primary, .header-actions button[onclick="bulkUnlockIuranRT()"]');
    buttonsToToggle.forEach(btn => btn.style.display = isSudah ? 'none' : '');
    
    const separator = document.querySelector('.header-actions div[style*="width: 1px"]');
    if (separator) separator.style.display = isSudah ? 'none' : '';

    filterLaporanBlok();
}

function exportLaporanBlokCSV() {
    if (window.filteredLaporanBlokData.length === 0) { showToast('Tidak ada data untuk di-export.', 'warning'); return; }
    
    const bulanText = document.getElementById('filter-bulan-laporan').options[document.getElementById('filter-bulan-laporan').selectedIndex].text;
    const tahunText = document.getElementById('filter-tahun-laporan').value;
    
    let csv = `LAPORAN RINCIAN IURAN WARGA (DISETOR) - PERIODE ${bulanText.toUpperCase()} ${tahunText}\n\n`;
    csv += 'Nama Warga;Blok;No Rumah;Nominal;Metode;Tgl Bayar;Tgl Setor RT\n';
    
    window.filteredLaporanBlokData.forEach(d => {
        const tglB = d.tanggal_bayar ? d.tanggal_bayar.split(' ')[0] : '-';
        const tglS = d.tanggal_setor ? d.tanggal_setor.split(' ')[0] : '-';
        csv += `"${d.nama_lengkap}";"${d.nama_blok}";"${d.nomor_rumah}";"${d.total_tagihan}";"${d.metode_pembayaran}";"${tglB}";"${tglS}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', `laporan_iuran_warga_setor_${bulanText}_${tahunText}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

window.currentLaporanWargaData = [];
window.filteredLaporanWargaData = [];
window.laporanWargaPage = 1;
const laporanWargaItemsPerPage = 20;

function initLaporanIuranWarga() {
    const selBlok = document.getElementById('filter-blok-laporan-warga');
    if (selBlok && selBlok.options.length <= 1) {
        fetch('api/get_bloks.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                res.data.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.text = b.nama_blok;
                    selBlok.appendChild(opt);
                });
            }
        });
    }
    loadLaporanIuranWarga();
}

function loadLaporanIuranWarga() {
    const tahun = document.getElementById('filter-tahun-laporan-warga').value;
    const blokId = document.getElementById('filter-blok-laporan-warga').value;
    const tbody = document.getElementById('laporan-warga-table-body');
    const svg = document.getElementById('svg-relations');

    // Perbaiki header tabel secara dinamis agar sesuai dengan data (15 kolom)
    const table = document.getElementById('laporan-warga-table');
    if (table) {
        const theadTr = table.querySelector('thead tr');
        if (theadTr) {
            if (theadTr.children.length === 14) {
                theadTr.children[1].innerText = 'NO/Blok';
                const thStatus = document.createElement('th');
                thStatus.innerText = 'Status';
                thStatus.className = 'text-center';
                theadTr.insertBefore(thStatus, theadTr.children[2]);
            } else if (theadTr.children.length >= 15) {
                theadTr.children[1].innerText = 'NO/Blok';
            }
        }
    }

    if (svg) svg.innerHTML = ''; 
    tbody.innerHTML = '<tr><td colspan="15" class="text-center py-5">Memuat data hubungan...</td></tr>';
    window.laporanWargaPage = 1;

    fetch(`api/get_laporan_iuran_warga.php?tahun=${tahun}&blok_id=${blokId}`)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            window.currentLaporanWargaData = res.data;
            window.filteredLaporanWargaData = [...res.data];
            
            if (res.summary) {
                document.getElementById('laporan-warga-total').innerText = res.summary.total_warga;
                document.getElementById('laporan-warga-lunas').innerText = res.summary.total_lunas_full;
                document.getElementById('laporan-warga-menunggak').innerText = res.summary.total_menunggak;
            }

            renderLaporanIuranWarga();
        } else {
            tbody.innerHTML = `<tr><td colspan="15" class="text-center text-red py-5">${res.message}</td></tr>`;
        }
    });
}

function filterLaporanWarga() {
    const q = document.getElementById('search-laporan-warga').value.toLowerCase();
    const blokId = document.getElementById('filter-blok-laporan-warga').value;

    window.filteredLaporanWargaData = window.currentLaporanWargaData.filter(w => {
        const matchQ = w.nama_lengkap.toLowerCase().includes(q) || (w.nomor_rumah && w.nomor_rumah.toLowerCase().includes(q));
        const matchB = (blokId === 'all' || w.blok_id == blokId);
        return matchQ && matchB;
    });

    window.laporanWargaPage = 1;
    renderLaporanIuranWarga();
}

function renderLaporanIuranWarga() {
    const tbody = document.getElementById('laporan-warga-table-body');
    const emptyMsg = document.getElementById('laporan-warga-empty');
    const svg = document.getElementById('svg-relations');
    const pagination = document.getElementById('laporan-pagination');
    const pageInfo = document.getElementById('laporan-page-info');
    const allData = window.filteredLaporanWargaData;

    if (svg) svg.innerHTML = ''; 
    
    // Pagination Logic
    const totalItems = allData.length;
    const start = (window.laporanWargaPage - 1) * 20;
    const end = Math.min(start + 20, totalItems);
    const slicedData = allData.slice(start, end);

    if (pagination) {
        pagination.style.display = totalItems > 20 ? 'flex' : 'none';
        if (pageInfo) pageInfo.innerText = `Menampilkan ${start + 1}-${end} dari ${totalItems} data`;
    }

    if (slicedData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="15" class="text-center py-5">Tidak ada data ditemukan.</td></tr>';
        return;
    }

    let html = '';
    slicedData.forEach((w, idx) => {
        let monthDots = '';
        // Hitung tunggakan untuk kolom status baru
        let tunggakanCount = 0;
        w.history.forEach(m => {
            if (m.status === 'MENUNGGAK') tunggakanCount++;
        });
        w.history.forEach((m, mIdx) => {
            let dotClass = 'rekon-dot-empty';
            let title = 'Belum Ditagih / Di Luar Periode';
            if (m.status === 'LUNAS') {
                dotClass = 'rekon-dot-lunas';
                title = `Lunas pada ${m.db_tanggal_bayar}`;
            } else if (m.status === 'MENUNGGAK') {
                dotClass = 'rekon-dot-menunggak';
                title = 'Menunggak';
            } else if (m.status === 'SEBELUM_MULAI') {
                dotClass = 'rekon-dot-sebelum';
                title = 'Belum Masuk Tahun Buku';
            }            
            // ID unik untuk koordinat garis nantinya, menyertakan blok_id untuk keunikan global
            const dotId = `dot-${w.blok_id}-${w.id}-${m.bulan}`;
            monthDots += `<td class="text-center" data-month="${m.bulan}"><span id="${dotId}" class="rekon-dot ${dotClass}" title="${title}"></span></td>`;
        });
        
        // Tentukan teks dan kelas badge untuk kolom status
        let statusText = '';
        let statusBadgeClass = '';
        if (tunggakanCount === 0) {
            statusText = 'Lunas';
            statusBadgeClass = 'bg-emerald-light text-emerald';
        } else {
            statusText = `${tunggakanCount} Bulan Menunggak`;
            statusBadgeClass = 'bg-red-light text-red';
        }

        html += `
            <tr data-warga-id="${w.id}">
                <td><div style="font-weight:600; font-size:0.9rem;">${w.nama_lengkap}</div></td>
                <td><span class="badge bg-secondary-light text-secondary" style="font-size:0.7rem;">${w.nama_blok}/${w.nomor_rumah || '-'}</span></td>
                <td><span class="badge ${statusBadgeClass}" style="font-size:0.7rem;">${statusText}</span></td>
                ${monthDots}
            </tr>
        `;
    });

    tbody.innerHTML = html;
    lucide.createIcons();

    // Gambar Garis Relasi setelah render (tunggu DOM stabil)
    // Set SVG dimensions to match the scrollable table area
    const table = document.getElementById('laporan-warga-table'); // Pastikan ID tabel ini benar di HTML
    if (table) {
        svg.style.width = table.scrollWidth + 'px';
        svg.style.height = table.scrollHeight + 'px';
    }
    setTimeout(() => {
        drawRelationLines(slicedData);
    }, 500);
}

function drawRelationLines(data) {
    const svg = document.getElementById('svg-relations');
    const container = document.getElementById('laporan-warga-scroll-wrapper'); // Asumsi ini adalah parent yang bisa di-scroll
    const containerRect = container.getBoundingClientRect(); // Dapatkan posisi viewport dari container yang bisa di-scroll
    
    svg.innerHTML = ''; // Reset SVG

    data.forEach(w => {
        let linesForWarga = []; // Collect all lines for the current warga before drawing

        w.history.forEach((m) => {
            // Jika ada relasi bulan (dibayar di bulan lain)
            if (m.relasi_bulan !== null && m.relasi_tahun == document.getElementById('filter-tahun-laporan-warga').value) {
                const startDot = document.getElementById(`dot-${w.blok_id}-${w.id}-${m.bulan}`);
                const endDot = document.getElementById(`dot-${w.blok_id}-${w.id}-${m.relasi_bulan}`);

                if (startDot && endDot) {
                    const startRect = startDot.getBoundingClientRect();
                    const endRect = endDot.getBoundingClientRect();

                    // Koordinat relatif terhadap kontainer wrapper (bukan scrollable)
                    const x1 = (startRect.left + startRect.width / 2) - containerRect.left;
                    const y1 = (startRect.top + startRect.height / 2) - containerRect.top;
                    const x2 = (endRect.left + endRect.width / 2) - containerRect.left;
                    const y2 = (endRect.top + endRect.height / 2) - containerRect.top;

                    linesForWarga.push({ x1, y1, x2, y2, bulan: m.bulan, startRectHeight: startRect.height });
                }
            }
        });

        // Now draw the collected lines, applying offsets if multiple exist for this warga
        linesForWarga.sort((a, b) => a.x1 - b.x1); // Sort by starting X to ensure consistent stacking
        linesForWarga.forEach((line, index) => {
            const dist = Math.abs(line.x2 - line.x1); // Jarak horizontal
            
            // Hitung tinggi puncak visual kurva (bukan titik kontrol)
            // Jarak dekat: puncak ~14px di atas dot. Jarak jauh: maksimal ~24px di atas dot.
            let visualPeak = 14 + (dist * 0.015); 
            visualPeak = Math.min(visualPeak, 24); // Cap agar stabil dan tidak melewati baris atas
            
            // Tambahkan offset jika ada banyak garis yang saling bertumpuk untuk warga ini
            visualPeak += (index * 4);
            
            const midX = (line.x1 + line.x2) / 2; // Midpoint X
            
            // Karena sifat kurva Bezier Kuadratik (Q), tinggi maksimal fisik kurva adalah 50% dari tinggi Control Point.
            // Maka dari itu, nilai Y dari Control Point (midY) harus dikali 2 dari target puncak visual.
            const midY = line.y1 - (visualPeak * 2);
            
            const d = `M ${line.x1} ${line.y1} Q ${midX} ${midY} ${line.x2} ${line.y2}`;
            const curve = document.createElementNS("http://www.w3.org/2000/svg", "path");
            curve.setAttribute("d", d);
            curve.setAttribute("class", "relation-line");
            curve.setAttribute("marker-end", "url(#arrowhead)");
            curve.setAttribute("id", `line-${w.id}-${line.bulan}`); // Keep original ID format
            svg.appendChild(curve);
        });
    });
}

function filterLaporanWarga() {
    const q = document.getElementById('search-laporan-warga').value.toLowerCase();
    const blokId = document.getElementById('filter-blok-laporan-warga').value;

    window.filteredLaporanWargaData = window.currentLaporanWargaData.filter(w => {
        const matchQ = w.nama_lengkap.toLowerCase().includes(q) || w.nomor_rumah.toLowerCase().includes(q);
        const matchB = (blokId === 'all' || w.blok_id == blokId);
        return matchQ && matchB;
    });

    window.laporanWargaPage = 1; // Reset page on filter
    renderLaporanIuranWarga();
}

function prevLaporanWargaPage() {
    if (window.laporanWargaPage > 1) {
        window.laporanWargaPage--;
        renderLaporanIuranWarga();
    }
}

function nextLaporanWargaPage() {
    const totalPages = Math.ceil(window.filteredLaporanWargaData.length / 20);
    if (window.laporanWargaPage < totalPages) {
        window.laporanWargaPage++;
        renderLaporanIuranWarga();
    }
}

function exportLaporanWargaCSV() {
    if (window.filteredLaporanWargaData.length === 0) { alert('Tidak ada data.'); return; }
    const tahun = document.getElementById('filter-tahun-laporan-warga').value;
    let csv = `LAPORAN RELASI PEMBAYARAN IURAN - TAHUN ${tahun}\n\n`;
    csv += 'Nama Warga;Blok;NO/Blok;Status;Jan;Feb;Mar;Apr;Mei;Jun;Jul;Agu;Sep;Okt;Nov;Des\n';
    
    window.filteredLaporanWargaData.forEach(w => {
        let tunggakanCount = 0;
        w.history.forEach(m => { if (m.status === 'MENUNGGAK') tunggakanCount++; });
        let statusTextTotal = tunggakanCount === 0 ? 'Lunas' : `${tunggakanCount} Bulan Menunggak`;
        
        let row = `"${w.nama_lengkap}";"${w.nama_blok}";"${w.nomor_rumah}";"${statusTextTotal}";`;
        w.history.forEach(m => {
            let statusText = m.status;
            if (m.relasi_bulan !== null) statusText += ` (Dibayar di bln ${m.relasi_bulan + 1})`;
            row += `"${statusText}";`;
        });
        csv += row + "\n";
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', `laporan_relasi_iuran_${tahun}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// --- WORKSPACE LAPORAN & RELASI LOGIC ---
window.wsLaporanWargaData = [];
window.wsFilteredLaporanWargaData = [];
window.wsLaporanWargaPage = 1;

function loadLaporanWargaWorkspace() {
    const blockId = window.currentBlokId;
    const year = document.getElementById('ws-relasi-year').value;
    const tbody = document.getElementById('ws-laporan-warga-table-body');
    const svg = document.getElementById('ws-svg-relations');

    // Perbaiki header tabel secara dinamis agar sesuai dengan data (15 kolom)
    const table = document.getElementById('ws-laporan-warga-table');
    if (table) {
        const theadTr = table.querySelector('thead tr');
        if (theadTr) {
            if (theadTr.children.length === 14) {
                theadTr.children[1].innerText = 'NO/Blok';
                const thStatus = document.createElement('th');
                thStatus.innerText = 'Status';
                thStatus.className = 'text-center';
                theadTr.insertBefore(thStatus, theadTr.children[2]);
            } else if (theadTr.children.length >= 15) {
                theadTr.children[1].innerText = 'NO/Blok';
            }
        }
    }

    if (svg) svg.innerHTML = '';
    tbody.innerHTML = '<tr><td colspan="15" class="text-center py-5">Memuat data relasi blok...</td></tr>';
    window.wsLaporanWargaPage = 1;

    fetch(`api/get_laporan_iuran_warga.php?tahun=${year}&blok_id=${blockId}`)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            window.wsLaporanWargaData = res.data;
            window.wsFilteredLaporanWargaData = [...res.data];
            
            if (res.summary) {
                document.getElementById('ws-laporan-warga-total').innerText = res.summary.total_warga;
                document.getElementById('ws-laporan-warga-lunas').innerText = res.summary.total_lunas_full;
                document.getElementById('ws-laporan-warga-menunggak').innerText = res.summary.total_menunggak;
            }

            renderWsLaporanWarga();
        } else {
            tbody.innerHTML = `<tr><td colspan="15" class="text-center text-red py-5">${res.message}</td></tr>`;
        }
    });
}

function filterWsLaporanWarga() {
    const q = document.getElementById('search-ws-laporan-warga').value.toLowerCase();

    window.wsFilteredLaporanWargaData = window.wsLaporanWargaData.filter(w => {
        return w.nama_lengkap.toLowerCase().includes(q) || (w.nomor_rumah && w.nomor_rumah.toLowerCase().includes(q));
    });

    window.wsLaporanWargaPage = 1;
    renderWsLaporanWarga();
}

function renderWsLaporanWarga() {
    const tbody = document.getElementById('ws-laporan-warga-table-body');
    const svg = document.getElementById('ws-svg-relations');
    const pagination = document.getElementById('ws-laporan-pagination');
    const pageInfo = document.getElementById('ws-laporan-page-info');
    const allData = window.wsFilteredLaporanWargaData;

    if (!tbody) return;
    if (svg) svg.innerHTML = '';

    const totalItems = allData.length;
    const start = (window.wsLaporanWargaPage - 1) * 20;
    const end = Math.min(start + 20, totalItems);
    const slicedData = allData.slice(start, end);

    if (pagination) {
        pagination.style.display = totalItems > 20 ? 'flex' : 'none';
        if (pageInfo) pageInfo.innerText = `Menampilkan ${start + 1}-${end} dari ${totalItems} data`;
    }

    let html = '';
    slicedData.forEach(w => {
        // Hitung tunggakan untuk kolom status baru
        let tunggakanCount = 0;
        w.history.forEach(m => {
            if (m.status === 'MENUNGGAK') tunggakanCount++;
        });
        let monthDots = '';
        w.history.forEach(m => {
            let dotClass = 'rekon-dot-empty';
            let title = 'Belum Ditagih / Di Luar Periode';
            if (m.status === 'LUNAS') { dotClass = 'rekon-dot-lunas'; title = `Lunas pada ${m.db_tanggal_bayar}`; }
            else if (m.status === 'MENUNGGAK') { dotClass = 'rekon-dot-menunggak'; title = 'Menunggak'; }
            else if (m.status === 'SEBELUM_MULAI') { dotClass = 'rekon-dot-sebelum'; title = 'Belum Masuk Tahun Buku'; }
            
            const dotId = `ws-dot-${w.blok_id}-${w.id}-${m.bulan}`;
            monthDots += `<td class="text-center" data-month="${m.bulan}"><span id="${dotId}" class="rekon-dot ${dotClass}" title="${title}"></span></td>`;
        });

        // Tentukan teks dan kelas badge untuk kolom status
        let statusText = '';
        let statusBadgeClass = '';
        if (tunggakanCount === 0) {
            statusText = 'Lunas';
            statusBadgeClass = 'bg-emerald-light text-emerald';
        } else {
            statusText = `${tunggakanCount} Bulan Menunggak`;
            statusBadgeClass = 'bg-red-light text-red';
        }

        html += `
            <tr data-warga-id="${w.id}">
                <td style="position: sticky; left: 0; z-index: 20; background: var(--secondary-bg);">
                    <div style="font-weight:600; font-size:0.875rem;">${w.nama_lengkap}</div>
                </td>
                <td><span class="badge bg-secondary-light text-secondary" style="font-size:0.7rem;">${w.nama_blok}/${w.nomor_rumah || '-'}</span></td>
                <td><span class="badge ${statusBadgeClass}" style="font-size:0.7rem;">${statusText}</span></td>
                ${monthDots}
            </tr>
        `;
    });
    tbody.innerHTML = html;
    lucide.createIcons();
    // Set SVG dimensions to match the scrollable table area
    const table = document.getElementById('ws-laporan-warga-table'); // Pastikan ID tabel ini benar di HTML
    if (table) {
        svg.style.width = table.scrollWidth + 'px';
        svg.style.height = table.scrollHeight + 'px';
    }
    setTimeout(() => { drawWsRelationLines(slicedData); }, 500); // Gambar garis setelah DOM diperbarui
}

function drawWsRelationLines(data) {
    const svg = document.getElementById('ws-svg-relations');
    if (!svg) return;
    const container = document.getElementById('ws-laporan-scroll-wrapper');
    const containerRect = container.getBoundingClientRect();
    const year = document.getElementById('ws-relasi-year').value;
    
    svg.innerHTML = '';

    data.forEach(w => {
        let linesForWarga = []; // Collect all lines for the current warga before drawing

        w.history.forEach((m) => {
            if (m.relasi_bulan !== null && m.relasi_tahun == year) {
                const startDot = document.getElementById(`ws-dot-${w.blok_id}-${w.id}-${m.bulan}`);
                const endDot = document.getElementById(`ws-dot-${w.blok_id}-${w.id}-${m.relasi_bulan}`);

                if (startDot && endDot) {
                    const startRect = startDot.getBoundingClientRect();
                    const endRect = endDot.getBoundingClientRect();

                    const x1 = (startRect.left + startRect.width / 2) - containerRect.left;
                    const y1 = (startRect.top + startRect.height / 2) - containerRect.top;
                    const x2 = (endRect.left + endRect.width / 2) - containerRect.left;
                    const y2 = (endRect.top + endRect.height / 2) - containerRect.top;

                    linesForWarga.push({ x1, y1, x2, y2, startRectHeight: startRect.height }); // Store coordinates for later drawing
                }
            }
        });

        // Now draw the collected lines, applying offsets if multiple exist for this warga
        linesForWarga.sort((a, b) => a.x1 - b.x1); // Sort by starting X to ensure consistent stacking
        linesForWarga.forEach((line, index) => {
            const dist = Math.abs(line.x2 - line.x1); // Jarak horizontal
            
            // Hitung tinggi puncak visual kurva (bukan titik kontrol)
            // Jarak dekat: puncak ~14px di atas dot. Jarak jauh: maksimal ~24px di atas dot.
            let visualPeak = 14 + (dist * 0.015); 
            visualPeak = Math.min(visualPeak, 24); // Cap agar stabil dan tidak melewati baris atas
            
            // Tambahkan offset jika ada banyak garis yang saling bertumpuk untuk warga ini
            visualPeak += (index * 4);
            
            const midX = (line.x1 + line.x2) / 2; // Midpoint X
            
            // Karena sifat kurva Bezier Kuadratik (Q), tinggi maksimal fisik kurva adalah 50% dari tinggi Control Point.
            // Maka dari itu, nilai Y dari Control Point (midY) harus dikali 2 dari target puncak visual.
            const midY = line.y1 - (visualPeak * 2);
            
            const d = `M ${line.x1} ${line.y1} Q ${midX} ${midY} ${line.x2} ${line.y2}`;
            const curve = document.createElementNS("http://www.w3.org/2000/svg", "path");
            curve.setAttribute("d", d);
            curve.setAttribute("class", "relation-line");
            curve.setAttribute("marker-end", "url(#ws-arrowhead)");
            svg.appendChild(curve);
        });
    });
}

function prevWsLaporanPage() {
    if (window.wsLaporanWargaPage > 1) {
        window.wsLaporanWargaPage--;
        renderWsLaporanWarga();
    }
}

function exportWsLaporanWargaCSV() {
    if (window.wsFilteredLaporanWargaData.length === 0) { alert('Tidak ada data.'); return; }
    const tahun = document.getElementById('ws-relasi-year').value;
    let csv = `LAPORAN RELASI PEMBAYARAN IURAN BLOK - TAHUN ${tahun}\n\n`;
    csv += 'Nama Warga;Blok;NO/Blok;Status;Jan;Feb;Mar;Apr;Mei;Jun;Jul;Agu;Sep;Okt;Nov;Des\n';
    
    window.wsFilteredLaporanWargaData.forEach(w => {
        let tunggakanCount = 0;
        w.history.forEach(m => { if (m.status === 'MENUNGGAK') tunggakanCount++; });
        let statusTextTotal = tunggakanCount === 0 ? 'Lunas' : `${tunggakanCount} Bulan Menunggak`;
        
        let row = `"${w.nama_lengkap}";"${w.nama_blok}";"${w.nomor_rumah}";"${statusTextTotal}";`;
        w.history.forEach(m => {
            let statusText = m.status;
            if (m.relasi_bulan !== null) statusText += ` (Dibayar di bln ${m.relasi_bulan + 1})`;
            row += `"${statusText}";`;
        });
        csv += row + "\n";
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', `laporan_relasi_iuran_blok_${tahun}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function nextWsLaporanPage() {
    const totalPages = Math.ceil(window.wsFilteredLaporanWargaData.length / 20);
    if (window.wsLaporanWargaPage < totalPages) {
        window.wsLaporanWargaPage++;
        renderWsLaporanWarga();
    }
}
