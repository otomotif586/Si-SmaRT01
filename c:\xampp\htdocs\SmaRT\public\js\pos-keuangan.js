function initPosKeuangan() {
    const selBulan = document.getElementById('filter-bulan-pos');
    const selTahun = document.getElementById('filter-tahun-pos');
    
    if (selBulan && selBulan.options.length <= 1) {
        selBulan.innerHTML = '<option value="all" selected>Semua Bulan</option>';
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        months.forEach((m, i) => {
            const opt = document.createElement('option'); opt.value = i; opt.text = m;
            selBulan.appendChild(opt);
        });

        selTahun.innerHTML = '<option value="all" selected>Semua Tahun</option>';
        const currentYear = new Date().getFullYear();
        for (let y = currentYear; y >= currentYear - 3; y--) {
            const opt = document.createElement('option'); opt.value = y; opt.text = y;
            selTahun.appendChild(opt);
        }
    }
    loadPosKeuangan();
}

function prevMonthPos() {
    const selBulan = document.getElementById('filter-bulan-pos');
    const selTahun = document.getElementById('filter-tahun-pos');
    if (selBulan.selectedIndex > 0) { selBulan.selectedIndex--; loadPosKeuangan(); } 
    else if (selTahun.selectedIndex < selTahun.options.length - 1) { selBulan.selectedIndex = 11; selTahun.selectedIndex++; loadPosKeuangan(); }
}

function nextMonthPos() {
    const selBulan = document.getElementById('filter-bulan-pos');
    const selTahun = document.getElementById('filter-tahun-pos');
    if (selBulan.selectedIndex < 11) { selBulan.selectedIndex++; loadPosKeuangan(); } 
    else if (selTahun.selectedIndex > 0) { selBulan.selectedIndex = 0; selTahun.selectedIndex--; loadPosKeuangan(); }
}

function loadPosKeuangan() {
    const bulan = document.getElementById('filter-bulan-pos').value;
    const tahun = document.getElementById('filter-tahun-pos').value;
    
    fetch(`api/get_pos_keuangan.php?bulan=${bulan}&tahun=${tahun}`)
    .then(async r => {
        const txt = await r.text();
        try {
            return JSON.parse(txt);
        } catch(e) {
            throw new Error(txt);
        }
    })
    .then(res => {
        if (res.status === 'success') {
            try {
                renderPosCards(res.pos_data);
                renderPosHistory(res.history);
            } catch(e) {
                document.getElementById('pos-cards-container').innerHTML = `<div class="text-red p-4 border border-red bg-red-light rounded-xl font-bold" style="grid-column: 1 / -1;">Render Error: ${e.message}</div>`;
            }
        } else {
            document.getElementById('pos-cards-container').innerHTML = `<p class="text-red text-center py-4" style="grid-column: 1 / -1;">${res.message}</p>`;
        }
    }).catch(e => {
        document.getElementById('pos-cards-container').innerHTML = `<div style="grid-column: 1 / -1; background:#111; color:#0f0; padding:16px; font-family:monospace; border-radius:12px; overflow:auto; max-height: 200px;"><b>API RAW ERROR:</b><br>${e.message}</div>`;
    });
}

function renderPosCards(posData) {
    const container = document.getElementById('pos-cards-container');
    if(posData.length === 0) {
        container.innerHTML = '<p class="text-secondary text-center py-4" style="grid-column: 1 / -1;">Belum ada pos anggaran.</p>';
        return;
    }

    let html = '';
    posData.forEach(p => {
        const pct = p.pemasukan > 0 ? ((p.pengeluaran / p.pemasukan) * 100).toFixed(0) : 0;
        html += `
            <div class="glass-card" style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                    <div>
                        <h4 style="margin: 0; font-size: 1.1rem; color: var(--accent-color); font-weight: 700;">Pos ${p.pos}</h4>
                        <p class="text-secondary" style="font-size: 0.8rem; margin-top: 4px;">Sisa Saldo</p>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center;"><i data-lucide="briefcase"></i></div>
                </div>
                <h2 style="margin: 0 0 16px 0; font-size: 1.8rem; font-weight: 800; color: var(--text-color);">Rp ${p.sisa.toLocaleString('id-ID')}</h2>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 8px;">
                    <span class="text-secondary">Terkumpul: <b class="text-emerald">Rp ${p.pemasukan.toLocaleString('id-ID')}</b></span>
                    <span class="text-secondary">Terpakai: <b class="text-red">Rp ${p.pengeluaran.toLocaleString('id-ID')}</b></span>
                </div>
                <div style="width: 100%; height: 6px; background: var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 20px;">
                    <div style="width: ${Math.min(pct, 100)}%; height: 100%; background: var(--accent-color);"></div>
                </div>
                <div style="display: flex; gap: 8px; width: 100%;">
                    <button class="button-secondary" style="flex: 1; justify-content: center; padding: 10px 0; font-size: 0.8rem;" onclick="postPemasukanPos('${p.pos}', ${p.pemasukan})"><i data-lucide="upload-cloud" style="margin-right: 6px; width: 16px; height: 16px;"></i> Posting</button>
                    <button class="button-primary" style="flex: 1; justify-content: center; padding: 10px 0; font-size: 0.8rem;" onclick="openCatatPengeluaranPos('${p.pos}')"><i data-lucide="minus-circle" style="margin-right: 6px; width: 16px; height: 16px;"></i> Keluar</button>
                </div>
            </div>
        `;
    });
    container.innerHTML = html; lucide.createIcons();
}

function renderPosHistory(history) {
    const container = document.getElementById('pos-history-container');
    if(history.length === 0) { container.innerHTML = '<p class="text-center text-secondary py-5">Belum ada riwayat transaksi.</p>'; return; }

    let html = '';
    history.forEach(h => {
        const tgl = new Date(h.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        html += `<div style="padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;"><div style="display: flex; align-items: center; gap: 16px;"><div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i data-lucide="arrow-up-right"></i></div><div><h4 style="margin: 0; font-size: 1rem; color: var(--text-color);">${h.keterangan}</h4><p class="text-secondary" style="font-size: 0.8rem; margin-top: 4px;"><span class="badge bg-blue-light text-blue" style="font-size: 0.65rem; padding: 2px 6px; margin-right: 6px;">Pos ${h.pos_anggaran}</span> ${tgl}</p></div></div><div style="text-align: right;"><h3 class="text-red" style="margin: 0; font-size: 1.1rem;">- Rp ${parseInt(h.nominal).toLocaleString('id-ID')}</h3><button class="button-secondary button-sm" style="padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; color: #ef4444; border: none; margin-top: 4px;" onclick="hapusKeuangan(${h.id}); setTimeout(loadPosKeuangan, 500);"><i data-lucide="trash" style="width: 12px; height: 12px; display: inline;"></i> Hapus</button></div></div>`;
    });
    container.innerHTML = html; lucide.createIcons();
}

function openCatatPengeluaranPos(posName) {
    document.getElementById('pos-nama-label').innerText = posName;
    document.getElementById('pos-input-nama').value = posName;
    document.getElementById('pos-input-nominal').value = '';
    document.getElementById('pos-input-tanggal').value = getLocalDateString();
    document.getElementById('pos-input-keterangan').value = '';
    document.getElementById('modal-pengeluaran-pos').classList.remove('hidden');
}

function postPemasukanPos(posName, nominal) {
    const selBulan = document.getElementById('filter-bulan-pos');
    const selTahun = document.getElementById('filter-tahun-pos');
    const bulan = selBulan.value;
    const tahun = selTahun.value;

    if (bulan === 'all' || tahun === 'all') {
        Swal.fire({ icon: 'warning', title: 'Filter Diperlukan', text: 'Pilih bulan dan tahun spesifik di filter atas terlebih dahulu untuk memposting periode tersebut.' });
        return;
    }

    if (nominal <= 0) {
        Swal.fire({ icon: 'error', title: 'Saldo Kosong', text: 'Tidak ada nominal pemasukan yang terkumpul untuk diposting pada periode ini.' });
        return;
    }

    const namaBulan = selBulan.options[selBulan.selectedIndex].text;

    Swal.fire({
        title: 'Posting Pemasukan Pos?',
        text: `Posting total pemasukan Rp ${nominal.toLocaleString('id-ID')} dari Pos ${posName} periode ${namaBulan} ${tahun} ke dalam Jurnal Keuangan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Ya, Posting!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData(); fd.append('pos_anggaran', posName); fd.append('nominal', nominal); fd.append('bulan', bulan); fd.append('tahun', tahun); fd.append('nama_bulan', namaBulan);
            fetch('api/post_jurnal_pos.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if (res.status === 'success') { Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 2000, showConfirmButton: false }); } 
                else { Swal.fire({ icon: 'error', title: 'Gagal', text: res.message }); }
            }).catch(e => console.error(e));
        }
    });
}

function submitPengeluaranPos(btn) {
    const pos = document.getElementById('pos-input-nama').value; const nom = document.getElementById('pos-input-nominal').value; const tgl = document.getElementById('pos-input-tanggal').value; const ket = document.getElementById('pos-input-keterangan').value;
    if(!nom || !tgl || !ket) { showToast('Lengkapi semua data!', 'error'); return; }
    const fd = new FormData(); fd.append('pos_anggaran', pos); fd.append('nominal', nom); fd.append('tanggal', tgl); fd.append('keterangan', ket);
    btn.innerHTML = 'Memproses...';
    fetch('api/simpan_pengeluaran_pos.php', {method:'POST', body:fd}).then(r=>r.json()).then(res=>{
        if(res.status === 'success') { document.getElementById('modal-pengeluaran-pos').classList.add('hidden'); showToast(res.message); loadPosKeuangan(); } else alert('Gagal: ' + res.message);
        btn.innerHTML = '<i data-lucide="check-circle" style="margin-right: 8px;"></i> Simpan'; lucide.createIcons();
    });
}