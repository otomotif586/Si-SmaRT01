function initPembukuan() {
    const selBulan = document.getElementById('filter-bulan-pembukuan');
    const selTahun = document.getElementById('filter-tahun-pembukuan');
    
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
    loadPembukuan();
}

function prevMonthPembukuan() {
    const selBulan = document.getElementById('filter-bulan-pembukuan');
    const selTahun = document.getElementById('filter-tahun-pembukuan');
    if (selBulan.selectedIndex > 0) { selBulan.selectedIndex--; loadPembukuan(); } 
    else if (selTahun.selectedIndex < selTahun.options.length - 1) { selBulan.selectedIndex = 11; selTahun.selectedIndex++; loadPembukuan(); }
}

function nextMonthPembukuan() {
    const selBulan = document.getElementById('filter-bulan-pembukuan');
    const selTahun = document.getElementById('filter-tahun-pembukuan');
    if (selBulan.selectedIndex < 11) { selBulan.selectedIndex++; loadPembukuan(); } 
    else if (selTahun.selectedIndex > 0) { selBulan.selectedIndex = 0; selTahun.selectedIndex--; loadPembukuan(); }
}

window.pembukuanData = null;
window.pbCurrentPage = 1;
const pbItemsPerPage = 20;

function loadPembukuan() {
    const bulan = document.getElementById('filter-bulan-pembukuan').value;
    const tahun = document.getElementById('filter-tahun-pembukuan').value;
    const ledgerBody = document.getElementById('pb-ledger-body');
    const posCardsContainer = document.getElementById('laporan-pos-cards');
    
    ledgerBody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><i data-lucide="loader"></i> Memuat Rincian Buku Besar...</td></tr>';
    if(posCardsContainer) posCardsContainer.innerHTML = '<div class="text-center py-5 text-secondary"><i data-lucide="loader"></i> Memuat Laporan Pos Anggaran...</div>';
    lucide.createIcons();

    fetch(`api/get_pembukuan.php?bulan=${bulan}&tahun=${tahun}`)
    .then(async r => {
        const text = await r.text();
        try {
            return JSON.parse(text);
        } catch(e) {
            throw new Error("Gagal memuat API dari server.");
        }
    })
    .then(res => {
        if (res.status === 'success') {
            window.pembukuanData = res;
            window.pbCurrentPage = 1;
            renderPembukuan(res);
        } else {
            ledgerBody.innerHTML = `<tr><td colspan="6" class="text-center text-red py-5 bg-red-50 font-bold">${res.message}</td></tr>`;
            if(posCardsContainer) posCardsContainer.innerHTML = `<div class="text-center text-red py-5 bg-red-50 border border-red-200 rounded-xl font-bold">${res.message}</div>`;
        }
    }).catch(e => {
        ledgerBody.innerHTML = `<tr><td colspan="6" class="text-center text-red py-5 bg-red-50 font-bold">${e.message}</td></tr>`;
        if(posCardsContainer) posCardsContainer.innerHTML = `<div class="text-center text-red py-5 bg-red-50 border border-red-200 rounded-xl font-bold">${e.message}</div>`;
    });
}

function renderPembukuan(res) {
    // Update Top Cards
    document.getElementById('pb-debit').innerText = 'Rp ' + res.summary.debit.toLocaleString('id-ID');
    document.getElementById('pb-kredit').innerText = 'Rp ' + res.summary.kredit.toLocaleString('id-ID');
    document.getElementById('pb-saldo').innerText = 'Rp ' + res.global_saldo.toLocaleString('id-ID'); // Selalu tampilkan saldo net all time

    // 1. Render Laporan Sisa Pos Warga
    const selBulan = document.getElementById('filter-bulan-pembukuan');
    const selTahun = document.getElementById('filter-tahun-pembukuan');
    const periodeText = `${selBulan.options[selBulan.selectedIndex].text} ${selTahun.options[selTahun.selectedIndex].text}`;
    document.getElementById('laporan-periode-text').innerText = 'Periode: ' + periodeText;

    const posCardsContainer = document.getElementById('laporan-pos-cards');
    posCardsContainer.style.display = 'block'; // Ubah layout grid menjadi block agar tabel full width
    let posCardsHtml = '';
    const posDataArray = res.pos_estimasi || []; // Pastikan array tidak undefined
    
    if (posDataArray.length === 0) {
        posCardsHtml = '<div style="text-align: center; padding: 32px; color: var(--text-secondary-color);">Belum ada data pos anggaran pada periode ini.</div>';
    } else {
        posCardsHtml = `
            <div class="table-responsive" style="border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;">
                <table class="modern-table rekon-table" style="width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap;">
                    <thead style="background: var(--secondary-bg); border-bottom: 2px solid var(--border-color);">
                        <tr>
                            <th style="padding: 16px 20px;">Pos Anggaran</th>
                            <th style="padding: 16px 20px; text-align: right;">Masuk</th>
                            <th style="padding: 16px 20px; text-align: right;">Keluar</th>
                            <th style="padding: 16px 20px; text-align: right;">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        posDataArray.forEach(p => {
            const pDebit = parseFloat(p.pemasukan) || 0;
            const pKredit = parseFloat(p.pengeluaran) || 0;
            const saldo = parseFloat(p.sisa) || 0;
            const sColor = saldo < 0 ? 'text-red' : 'text-emerald';
            
            posCardsHtml += `
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.3s;" onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 16px 20px; font-weight: 600; color: var(--text-color);"><i data-lucide="folder" style="color: var(--text-secondary-color); width: 16px; height: 16px; display: inline-block; margin-right: 8px; vertical-align: middle;"></i> ${p.nama_pos || 'Kas Tak Dikenal'}</td>
                    <td style="padding: 16px 20px; text-align: right; color: var(--text-color);">Rp ${pDebit.toLocaleString('id-ID')}</td>
                    <td style="padding: 16px 20px; text-align: right; color: var(--text-color);">Rp ${pKredit.toLocaleString('id-ID')}</td>
                    <td style="padding: 16px 20px; text-align: right; font-weight: 800; font-size: 1.05rem;" class="${sColor}">Rp ${saldo.toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
        
        posCardsHtml += `</tbody></table></div>`;
    }
    posCardsContainer.innerHTML = posCardsHtml;
    
    document.getElementById('laporan-total-sisa').innerText = 'Rp ' + (parseFloat(res.total_estimasi_sisa) || 0).toLocaleString('id-ID');

    // 2. Kalkulasi Saldo Berjalan (Running Balance) untuk Rincian Transaksi
    let currentSaldo = res.saldo_awal || 0;
    res.transactions.forEach(t => {
        const nom = parseFloat(t.nominal);
        if (t.jenis === 'Masuk') currentSaldo += nom;
        else currentSaldo -= nom;
        t.running_saldo = currentSaldo;
    });

    // REVERSE array agar transaksi terbaru berada di atas (Descending Sort)
    res.transactions.reverse();
    
    const trxArray = res.transactions || [];
    const posHistoryArray = trxArray.filter(t => t.raw_pos_anggaran !== null);

    // Render History di Tab 2 (Laporan Pos) khusus transaksi dari Pos Anggaran
    const historyBody = document.getElementById('laporan-pos-history-body');
    if (historyBody) {
        let historyHtml = '';
        if (posHistoryArray.length === 0) {
                historyHtml = '<tr><td colspan="4" class="text-center py-4 text-secondary">Belum ada transaksi pos.</td></tr>';
            } else {
                posHistoryArray.forEach(t => {
                    const tgl = new Date(t.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
                    const isMasuk = t.jenis === 'Masuk';
                    const sign = isMasuk ? '+' : '-';
                    const colorClass = isMasuk ? 'text-emerald' : 'text-red';
                    
                    historyHtml += `
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 20px; white-space: nowrap;">${tgl}</td>
                            <td style="padding: 12px 20px; white-space: nowrap;"><span class="badge bg-secondary-light text-secondary">${t.pos_anggaran || '-'}</span></td>
                            <td style="padding: 12px 20px; color: var(--text-color);">${t.keterangan || '-'}</td>
                            <td style="padding: 12px 20px; text-align: right; white-space: nowrap;" class="${colorClass} font-bold">${sign} Rp ${(parseFloat(t.nominal) || 0).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });
            }
            historyBody.innerHTML = historyHtml;
        }

    // 3. Paginasi & Render General Ledger
    const ledgerBody = document.getElementById('pb-ledger-body');
    const ledgerFoot = document.getElementById('pb-ledger-foot');
    let ledgerHtml = '';

    const totalItems = res.transactions.length;
    const totalPages = Math.ceil(totalItems / pbItemsPerPage);
    if (window.pbCurrentPage > totalPages && totalPages > 0) window.pbCurrentPage = totalPages;
    if (window.pbCurrentPage < 1) window.pbCurrentPage = 1;

    const startIdx = (window.pbCurrentPage - 1) * pbItemsPerPage;
    const endIdx = Math.min(startIdx + pbItemsPerPage, totalItems);
    const paginatedData = res.transactions.slice(startIdx, endIdx);

    if (totalItems === 0) {
        ledgerHtml += '<tr><td colspan="6" class="text-center py-5 text-secondary">Belum ada transaksi.</td></tr>';
    } else {
        paginatedData.forEach(t => {
            const tgl = new Date(t.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
            const isMasuk = t.jenis === 'Masuk';
            const debit = isMasuk ? `Rp ${parseFloat(t.nominal).toLocaleString('id-ID')}` : '-';
            const kredit = !isMasuk ? `Rp ${parseFloat(t.nominal).toLocaleString('id-ID')}` : '-';
            const sColor = t.running_saldo < 0 ? 'text-red' : 'text-emerald';

            ledgerHtml += `<tr style="transition: background 0.3s;" onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 16px 24px; font-size: 0.85rem;">${tgl}</td>
                <td style="padding: 16px 24px;">
                    <div class="font-bold text-color">${t.keterangan}</div>
                    <div class="text-secondary" style="font-size:0.75rem;"><i data-lucide="hash" style="width:10px; height:10px; display:inline;"></i> ${t.doc_number || '-'}</div>
                </td>
                <td style="padding: 16px 24px; font-size: 0.85rem;"><span class="badge bg-secondary-light text-secondary">${t.pos_anggaran}</span></td>
                <td class="text-right text-emerald" style="padding: 16px 24px; font-size: 0.9rem;">${debit}</td>
                <td class="text-right text-red" style="padding: 16px 24px; font-size: 0.9rem;">${kredit}</td>
                <td class="text-right font-bold ${sColor}" style="padding: 16px 24px; font-size: 0.95rem; background: rgba(128,128,128,0.02);">Rp ${t.running_saldo.toLocaleString('id-ID')}</td>
            </tr>`;
        });
        
        // Letakkan Saldo Awal di halaman terakhir, karena urutan tabel dibalik (terlama di bawah)
        if (window.pbCurrentPage === totalPages) {
            ledgerHtml += `<tr style="background: rgba(59, 130, 246, 0.05);">
                <td colspan="5" class="text-right font-bold text-secondary" style="padding: 12px 24px;">SALDO AWAL</td>
                <td class="text-right font-bold text-color" style="padding: 12px 24px;">Rp ${(res.saldo_awal || 0).toLocaleString('id-ID')}</td>
            </tr>`;
        }
    }
    ledgerBody.innerHTML = ledgerHtml;

    if (totalItems > 0) {
        const endSaldo = (res.saldo_awal || 0) + res.summary.debit - res.summary.kredit;
        ledgerFoot.innerHTML = `<tr>
            <td colspan="3" class="text-right" style="padding: 16px 24px;">SALDO AKHIR</td>
            <td class="text-right text-emerald" style="padding: 16px 24px;">Rp ${res.summary.debit.toLocaleString('id-ID')}</td>
            <td class="text-right text-red" style="padding: 16px 24px;">Rp ${res.summary.kredit.toLocaleString('id-ID')}</td>
            <td class="text-right text-color" style="padding: 16px 24px; font-size: 1.1rem;">Rp ${endSaldo.toLocaleString('id-ID')}</td>
        </tr>`;
    } else { ledgerFoot.innerHTML = ''; }

    // 4. Render Paginasi Control
    const pagination = document.getElementById('pb-pagination');
    const pageInfo = document.getElementById('pb-page-info');
    const pageNumbers = document.getElementById('pb-page-numbers');
    
    if (totalItems > 0) {
        pagination.style.display = 'flex';
        pageInfo.innerText = `${startIdx + 1}-${endIdx} dari ${totalItems}`;
        
        pageNumbers.innerHTML = '';
        let startPage = Math.max(1, window.pbCurrentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
        
        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button'); btn.innerText = i;
            btn.className = i === window.pbCurrentPage ? 'button-primary button-sm' : 'button-secondary button-sm';
            btn.style.cssText = 'padding: 8px 12px; border-radius: 8px;';
            btn.onclick = () => { window.pbCurrentPage = i; renderPembukuan(window.pembukuanData); };
            pageNumbers.appendChild(btn);
        }
    } else { pagination.style.display = 'none'; }

    lucide.createIcons();
}

function switchPbTab(tabId, element) {
    document.querySelectorAll('.pb-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('#page-pembukuan .sub-nav-tab').forEach(el => el.classList.remove('active'));

    document.getElementById(tabId).classList.remove('hidden');
    element.classList.add('active');
}

function prevPbPage() { if(window.pbCurrentPage > 1) { window.pbCurrentPage--; renderPembukuan(window.pembukuanData); } }
function nextPbPage() { const max = Math.ceil(window.pembukuanData.transactions.length / pbItemsPerPage); if(window.pbCurrentPage < max) { window.pbCurrentPage++; renderPembukuan(window.pembukuanData); } }

function exportPembukuanCSV() {
    if (!window.pembukuanData || window.pembukuanData.transactions.length === 0) { alert('Tidak ada data transaksi.'); return; }
    const res = window.pembukuanData;
    const selBulan = document.getElementById('filter-bulan-pembukuan');
    const selTahun = document.getElementById('filter-tahun-pembukuan');
    
    let csv = `GENERAL LEDGER (BUKU BESAR RINCIAN KAS) - ${selBulan.options[selBulan.selectedIndex].text} ${selTahun.options[selTahun.selectedIndex].text}\n\n`;
    csv += 'Tanggal;Keterangan;No Ref;Pos Anggaran;Debit (Masuk);Kredit (Keluar);Saldo Berjalan\n';
    
    res.transactions.forEach(t => { 
        const tgl = t.tanggal.split(' ')[0];
        const debit = t.jenis === 'Masuk' ? t.nominal : 0;
        const kredit = t.jenis === 'Keluar' ? t.nominal : 0;
        csv += `"${tgl}";"${t.keterangan}";"${t.doc_number || ''}";"${t.pos_anggaran}";"${debit}";"${kredit}";"${t.running_saldo}"\n`; 
    });
    csv += `"Sebelum Periode";"SALDO AWAL KAS";"";"";"";"";"${res.saldo_awal}"\n`;
    
    const endSaldo = (res.saldo_awal || 0) + res.summary.debit - res.summary.kredit;
    csv += `"";"SALDO AKHIR PERIODE INI";"";"";"${res.summary.debit}";"${res.summary.kredit}";"${endSaldo}"\n`;
    csv += `\n"NET SALDO KAS TERSEDIA SAAT INI";;;${res.global_saldo}\n`;
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.setAttribute('download', `buku_besar_kas.csv`); document.body.appendChild(link); link.click(); document.body.removeChild(link);
}