// Rekonsiliasi.js - Logic for Annual Audit/Reconciliation Summary
function initRekonsiliasi() {
    console.log("Initializing Rekonsiliasi Module...");
    loadGlobalRekonsiliasi();
    
    // Auto-update lucide icons
    if (window.lucide) {
        lucide.createIcons();
    }
}

async function loadGlobalRekonsiliasi() {
    const tahun = document.getElementById('filter-tahun-rekonsiliasi')?.value || new Date().getFullYear();
    const blokId = document.getElementById('filter-blok-rekonsiliasi')?.value || 'all';
    
    // Show Loading
    const tbody = document.getElementById('rekonsiliasi-table-body');
    if (tbody) tbody.innerHTML = '<tr><td colspan="15" class="text-center py-4">Memuat data audit...</td></tr>';
    
    try {
        const response = await fetch(`api/get_global_rekonsiliasi.php?tahun=${tahun}&blok_id=${blokId}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            renderRekonSummary(result.summary);
            renderRekonTable(result.data);
            
            // Animation Trigger
            const page = document.getElementById('page-rekonsiliasi');
            if (page) {
                page.classList.add('stagger-ready');
            }
        }
    } catch (error) {
        console.error("Error loading rekonsiliasi:", error);
    }
}

function renderRekonSummary(summary) {
    if (!summary) return;
    
    document.getElementById('rekon-stat-warga').textContent = summary.total_warga || 0;
    document.getElementById('rekon-stat-lunas').textContent = summary.total_lunas_full || 0;
    
    const piutangEl = document.getElementById('rekon-stat-piutang');
    if (piutangEl) {
        const value = summary.estimasi_piutang || 0;
        piutangEl.textContent = 'Rp ' + value.toLocaleString('id-ID');
    }
    
    const menunggakInfo = document.getElementById('rekon-stat-menunggak-info');
    if (menunggakInfo) {
        const count = summary.total_menunggak || 0;
        menunggakInfo.textContent = `${count} Warga belum lunas`;
    }
    
    // Hidden legacy element
    const menunggakLegacy = document.getElementById('rekon-stat-menunggak');
    if (menunggakLegacy) menunggakLegacy.textContent = summary.total_menunggak || 0;
}

function renderRekonTable(data) {
    const tbody = document.getElementById('rekonsiliasi-table-body');
    const emptyMsg = document.getElementById('rekonsiliasi-empty');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (!data || data.length === 0) {
        if (emptyMsg) emptyMsg.classList.remove('hidden');
        return;
    }
    
    if (emptyMsg) emptyMsg.classList.add('hidden');
    
    data.forEach(item => {
        let row = `<tr>
            <td class="font-bold text-color">${item.nama_warga}</td>
            <td class="text-secondary">${item.blok_no}</td>`;
            
        // 12 months dots
        item.history.forEach(m => {
            let dotClass = 'dot-empty';
            if (m.status === 'LUNAS') dotClass = 'dot-lunas';
            else if (m.status === 'MENUNGGAK') dotClass = 'dot-menunggak';
            
            row += `<td class="text-center"><span class="month-dot ${dotClass}" title="${m.bulan_text}: ${m.status}"></span></td>`;
        });
        
        const tunggakan = item.total_tunggakan || 0;
        row += `<td class="text-right font-bold ${tunggakan > 0 ? 'text-red' : 'text-emerald'}">Rp ${tunggakan.toLocaleString('id-ID')}</td>`;
        row += `</tr>`;
        
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function filterRekonsiliasi() {
    const query = document.getElementById('search-rekonsiliasi').value.toLowerCase();
    const rows = document.querySelectorAll('#rekonsiliasi-table-body tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

function exportRekonsiliasiCSV() {
    const tahun = document.getElementById('filter-tahun-rekonsiliasi').value;
    window.location.href = `api/export_rekonsiliasi.php?tahun=${encodeURIComponent(tahun)}`;
}
