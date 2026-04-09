function initDetailKeuangan() {
    const selBulan = document.getElementById('filter-bulan-detail');
    const selTahun = document.getElementById('filter-tahun-detail');
    
    // JS Fallback: Jika opsi bulan masih blank karena alasan apapun, isi otomatis secara paksa
    if (selBulan && selBulan.options.length <= 1) {
        selBulan.innerHTML = '<option value="all" selected>Semua Bulan</option>';
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        months.forEach((m, i) => {
            const opt = document.createElement('option');
            opt.value = i;
            opt.text = m;
            selBulan.appendChild(opt);
        });

        selTahun.innerHTML = '<option value="all" selected>Semua Tahun</option>';
        const currentYear = new Date().getFullYear();
        for (let y = currentYear; y >= currentYear - 3; y--) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.text = y;
            selTahun.appendChild(opt);
        }
    }
    loadDetailKeuangan();
}

function prevMonthDetail() {
    const selBulan = document.getElementById('filter-bulan-detail');
    const selTahun = document.getElementById('filter-tahun-detail');
    if (selBulan.selectedIndex > 0) { selBulan.selectedIndex--; loadDetailKeuangan(); } 
    else if (selTahun.selectedIndex < selTahun.options.length - 1) { selBulan.selectedIndex = 11; selTahun.selectedIndex++; loadDetailKeuangan(); }
}

function nextMonthDetail() {
    const selBulan = document.getElementById('filter-bulan-detail');
    const selTahun = document.getElementById('filter-tahun-detail');
    if (selBulan.selectedIndex < 11) { selBulan.selectedIndex++; loadDetailKeuangan(); } 
    else if (selTahun.selectedIndex > 0) { selBulan.selectedIndex = 0; selTahun.selectedIndex--; loadDetailKeuangan(); }
}

window.detailKeuanganData = null;
window.dkCurrentPage = 1;
const dkItemsPerPage = 15;

function loadDetailKeuangan() {
    const bulan = document.getElementById('filter-bulan-detail').value;
    const tahun = document.getElementById('filter-tahun-detail').value;
    const tbody = document.getElementById('dk-table-body');
    
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-5">Memuat data...</td></tr>';

    fetch(`api/get_detail_keuangan.php?bulan=${bulan}&tahun=${tahun}`)
    .then(async r => {
        const text = await r.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error("Respon API gagal: " + text);
        }
    })
    .then(res => {
        if (res.status === 'success') {
            window.detailKeuanganData = res;
            window.dkCurrentPage = 1; // Reset halaman setiap filter berjalan
            renderDetailKeuangan(res);
        } else {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-red py-5">${res.message}</td></tr>`;
        }
    }).catch(e => {
        console.error(e);
        tbody.innerHTML = `<tr><td colspan="10" class="text-center text-red py-5">Terjadi kesalahan internal. Cek console log.</td></tr>`;
    });
}

function renderDetailKeuangan(res) {
    const thead = document.getElementById('dk-table-head');
    const tbody = document.getElementById('dk-table-body');
    const tfoot = document.getElementById('dk-table-foot');
    const pagination = document.getElementById('dk-pagination');
    const pageInfo = document.getElementById('dk-page-info');
    
    document.getElementById('dk-total-lunas').innerText = res.grand_total['Total Lunas'] + ' Setoran';
    document.getElementById('dk-total-pendapatan').innerText = 'Rp ' + res.grand_total['Total Nominal'].toLocaleString('id-ID');
    if (document.getElementById('dk-total-warga')) {
        document.getElementById('dk-total-warga').innerText = res.grand_total['Total Warga'] + ' KK';
    }

    let headHtml = '<tr><th style="position: sticky; left: 0; background: var(--secondary-bg); z-index: 10;">Blok</th><th class="text-center">Total KK Blok</th><th class="text-center text-emerald">Jml Setoran Lunas</th>';
    res.komponen_headers.forEach(c => { headHtml += `<th class="text-right">${c}</th>`; });
    headHtml += '<th class="text-right text-emerald" style="font-weight: 800;">TOTAL (Rp)</th></tr>';
    thead.innerHTML = headHtml;

    // Logic Paginasi
    const totalItems = res.data.length;
    const totalPages = Math.ceil(totalItems / dkItemsPerPage);
    if (window.dkCurrentPage > totalPages && totalPages > 0) window.dkCurrentPage = totalPages;
    if (window.dkCurrentPage < 1) window.dkCurrentPage = 1;

    const startIdx = (window.dkCurrentPage - 1) * dkItemsPerPage;
    const endIdx = Math.min(startIdx + dkItemsPerPage, totalItems);
    const paginatedData = res.data.slice(startIdx, endIdx);

    let bodyHtml = '';
    if (totalItems === 0) { 
        bodyHtml = `<tr><td colspan="${4 + res.komponen_headers.length}" class="text-center py-5">Tidak ada data.</td></tr>`; 
        if(pagination) pagination.style.display = 'none';
    } else {
        if(pagination) pagination.style.display = totalItems > dkItemsPerPage ? 'flex' : 'none';
        if(pageInfo) pageInfo.innerText = `Menampilkan ${startIdx + 1} - ${endIdx} dari ${totalItems} data blok`;

        paginatedData.forEach(row => {
            bodyHtml += `<tr>
                <td style="position: sticky; left: 0; background: var(--primary-bg); z-index: 10; font-weight: 600;">${row.nama_blok}</td>
                <td class="text-center">${row.total_warga}</td>
                <td class="text-center text-emerald font-bold">${row.lunas_count}</td>`;
            res.komponen_headers.forEach(c => { bodyHtml += `<td class="text-right text-secondary">${(row.komponen[c] || 0).toLocaleString('id-ID')}</td>`; });
            bodyHtml += `<td class="text-right text-emerald font-bold">${row.total_nominal.toLocaleString('id-ID')}</td></tr>`;
        });

        // Render Angka Halaman
        const pageNumbers = document.getElementById('dk-page-numbers');
        if (pageNumbers) {
            pageNumbers.innerHTML = '';
            let startPage = Math.max(1, window.dkCurrentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button'); btn.innerText = i;
                btn.className = i === window.dkCurrentPage ? 'button-primary button-sm' : 'button-secondary button-sm';
                btn.style.cssText = 'padding: 8px 12px; border-radius: 8px;';
                btn.onclick = () => { window.dkCurrentPage = i; renderDetailKeuangan(res); };
                pageNumbers.appendChild(btn);
            }
        }
    }
    tbody.innerHTML = bodyHtml;

    if (res.data.length > 0) {
        let footHtml = `<tr>
            <td style="position: sticky; left: 0; background: rgba(128,128,128,0.1); z-index: 10;">GRAND TOTAL</td>
            <td class="text-center">${res.grand_total['Total Warga']}</td>
            <td class="text-center text-emerald">${res.grand_total['Total Lunas']}</td>`;
        res.komponen_headers.forEach(c => { footHtml += `<td class="text-right">${(res.grand_total[c] || 0).toLocaleString('id-ID')}</td>`; });
        footHtml += `<td class="text-right text-emerald">${res.grand_total['Total Nominal'].toLocaleString('id-ID')}</td></tr>`;
        tfoot.innerHTML = footHtml;
    } else { tfoot.innerHTML = ''; }
    lucide.createIcons();
}

function prevDkPage() {
    if (window.dkCurrentPage > 1) {
        window.dkCurrentPage--;
        if (window.detailKeuanganData) renderDetailKeuangan(window.detailKeuanganData);
    }
}

function nextDkPage() {
    if (!window.detailKeuanganData) return;
    const totalPages = Math.ceil(window.detailKeuanganData.data.length / dkItemsPerPage);
    if (window.dkCurrentPage < totalPages) {
        window.dkCurrentPage++;
        renderDetailKeuangan(window.detailKeuanganData);
    }
}

function exportDetailKeuangan() {
    if (!window.detailKeuanganData || window.detailKeuanganData.data.length === 0) { alert('Tidak ada data untuk di-export.'); return; }
    const res = window.detailKeuanganData; 
    const selBulan = document.getElementById('filter-bulan-detail');
    const selTahun = document.getElementById('filter-tahun-detail');
    const bulanText = selBulan.options[selBulan.selectedIndex].text;
    const tahunText = selTahun.options[selTahun.selectedIndex].text;
    
    let csv = `LAPORAN DETAIL KEUANGAN PER BLOK - PERIODE ${bulanText.toUpperCase()} ${tahunText}\n\n`;
    let headers = ['Nama Blok', 'Total Warga', 'Lunas'].concat(res.komponen_headers); headers.push('Total Nominal (Rp)');
    csv += headers.map(h => `"${h}"`).join(';') + '\n';
    res.data.forEach(row => { let csvRow = [`"${row.nama_blok}"`, `"${row.total_warga}"`, `"${row.lunas_count}"`]; res.komponen_headers.forEach(c => { csvRow.push(`"${row.komponen[c] || 0}"`); }); csvRow.push(`"${row.total_nominal}"`); csv += csvRow.join(';') + '\n'; });
    let footRow = [`"GRAND TOTAL"`, `"${res.grand_total['Total Warga']}"`, `"${res.grand_total['Total Lunas']}"`]; res.komponen_headers.forEach(c => { footRow.push(`"${res.grand_total[c] || 0}"`); }); footRow.push(`"${res.grand_total['Total Nominal']}"`); csv += footRow.join(';') + '\n';
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.setAttribute('download', `detail_keuangan_${bulanText}_${tahunText}.csv`); document.body.appendChild(link); link.click(); document.body.removeChild(link);
}