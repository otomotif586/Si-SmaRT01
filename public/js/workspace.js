// --- Workspace Full-Screen Modal Functions ---

function smartAssetUrl(path) {
    if (!path) return '';
    if (/^(?:https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) return path;
    const basePath = (window.__SMART_ASSET_BASE_PATH__ || '').replace(/\/$/, '');
    const cleanedPath = String(path).replace(/^\/+/, '');
    if (!basePath) return cleanedPath;
    return `${basePath}/${cleanedPath}`;
}

// Menyimpan ID blok yang sedang aktif agar bisa diakses fungsi lain
window.currentBlokId = 0;

function openWorkspaceModal(blokId, blockName, coord, warga, kas, logoClass, logoText, logoImage) {
    window.currentBlokId = blokId;
    // Set Modal Data
    document.getElementById('modal-block-title').innerText = blockName;
    document.getElementById('modal-block-coord').innerText = 'Koordinator: ' + coord;
    if (document.getElementById('dash-stat-warga')) document.getElementById('dash-stat-warga').innerText = warga + ' KK';
    if (document.getElementById('dash-stat-kas')) document.getElementById('dash-stat-kas').innerText = kas;
    
    // Set Modal Logo Dinamis
    const logoEl = document.getElementById('modal-block-logo');
    if (logoEl) {
        if (logoImage) {
            logoEl.className = 'ws-logo-container';
            logoEl.innerHTML = `<img src="${smartAssetUrl(logoImage)}" alt="Logo" class="ws-modal-img">`;
        } else {
            logoEl.className = 'ws-logo-container ' + logoClass;
            logoEl.innerHTML = logoText;
        }
    }

    // Load Data Warga Khusus Blok Ini via AJAX
    const wargaListContainer = document.getElementById('modal-warga-list-container');
    wargaListContainer.innerHTML = '<p class="text-secondary text-center py-4">Memuat data warga...</p>';
    
    fetch(`api/get_warga.php?blok_id=${blokId}`)
        .then(response => response.json())
        .then(data => {
            window.currentWargaData = data; // Simpan data global untuk fungsi Search
            
            // Kosongkan pengaturan filter setiap kali blok baru dibuka
            window.currentWargaPage = 1;
            document.getElementById('search-warga-input').value = ''; 
            document.getElementById('filter-pernikahan').value = ''; 
            document.getElementById('filter-status').value = ''; 
            
            if(document.getElementById('search-agenda-input')) document.getElementById('search-agenda-input').value = '';
            if(document.getElementById('filter-status-agenda')) document.getElementById('filter-status-agenda').value = '';
            if(document.getElementById('search-laporan-input')) document.getElementById('search-laporan-input').value = '';
            if(document.getElementById('filter-status-laporan')) document.getElementById('filter-status-laporan').value = '';
            
            filterWargaList();
        });

    loadDashboardSummary(blokId);

    // Tampilkan Modal
    const modal = document.getElementById('workspace-modal');
    modal.classList.remove('hidden');
    
    // Reset tab modal kembali ke "Ringkasan" agar tidak menumpuk ke tampilan sebelumnya
    const firstTabBtn = document.querySelector('.modal-nav button');
    if (firstTabBtn) switchModalTab('modal-dash', firstTabBtn);
    
    // Cegah body scrolling saat modal terbuka
    document.body.style.overflow = 'hidden'; 
    lucide.createIcons(); // Render ulang ikon dalam modal
}

function closeWorkspaceModal() {
    const modal = document.getElementById('workspace-modal');
    modal.classList.add('hidden');
    
    // Kembalikan body scrolling
    document.body.style.overflow = '';
}

function toggleModalSidebar() {
    const modalSidebar = document.getElementById('modal-sidebar');
    if (modalSidebar) {
        modalSidebar.classList.toggle('collapsed');
    }
}

function switchModalTab(tabId, element) {
    // Sembunyikan semua konten tab internal
    document.querySelectorAll('.modal-tab-content').forEach(el => el.classList.add('hidden'));
    
    // Hapus status aktif di semua tombol tab
    document.querySelectorAll('.modal-tab').forEach(el => el.classList.remove('active'));
    
    // Tampilkan konten yang dipilih dengan animasi
    const target = document.getElementById(tabId);
    target.classList.remove('hidden');
    
    // Aktifkan tombol yang diklik
    if(element) element.classList.add('active');

    // Trigger stagger animations for the new tab
    target.classList.remove('stagger-ready');
    void target.offsetWidth;
    target.classList.add('stagger-ready');

    // Jika tab keuangan diklik, inisialisasi fungsinya
    if (tabId === 'modal-keuangan') {
        initKeuanganBlok();
    } else if (tabId === 'modal-agenda') {
        initAgendaLaporan();
    } else if (tabId === 'modal-laporan-relasi') {
        loadLaporanWargaWorkspace();
    }
}

// --- DASHBOARD CHARTS & SUMMARY ---
let chartDemografi, chartPemasukan;

function loadDashboardSummary(blokId) {
    fetch(`api/get_dashboard_summary.php?blok_id=${blokId}`)
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            const d = res.data;
            document.getElementById('dash-stat-warga').innerText = d.total_warga + ' KK';
            document.getElementById('dash-stat-kas').innerText = 'Rp ' + parseInt(d.kas_blok).toLocaleString('id-ID');
            
            // Consolidated Status Card logic
            const statusMain = document.getElementById('dash-stat-status-main');
            const statusSub = document.getElementById('dash-stat-status-sub');
            if (statusMain) {
                statusMain.innerText = d.laporan_aktif > 0 ? 'Siaga' : 'Aman';
                statusMain.style.color = d.laporan_aktif > 0 ? '#f59e0b' : '#10b981';
            }
            if (statusSub) {
                statusSub.innerText = `${d.laporan_aktif} Laporan / ${d.agenda_terdekat || '0 Agenda'}`;
            }
            
            renderDashboardCharts(d.demografi, d.iuran_labels, d.iuran_data);
        }
    });
}

function renderDashboardCharts(demografi, labels, dataIuran) {
    const rootStyles = getComputedStyle(document.documentElement);
    const textColor = rootStyles.getPropertyValue('--text-color').trim() || '#64748b';
    const gridColor = rootStyles.getPropertyValue('--border-color').trim() || '#e2e8f0';

    // 1. Chart Demografi (Doughnut)
    if (chartDemografi) chartDemografi.destroy();
    const ctxD = document.getElementById('chartDemografi').getContext('2d');
    chartDemografi = new Chart(ctxD, {
        type: 'doughnut',
        data: {
            labels: ['Warga Tetap', 'Kontrak', 'Weekend'],
            datasets: [{
                data: [demografi.Tetap || 0, demografi.Kontrak || 0, demografi.Weekend || 0],
                backgroundColor: ['#10b981', '#f97316', '#a855f7'],
                borderWidth: 0, hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: textColor, font: { family: 'Inter', size: 11 } } } }, cutout: '75%' }
    });

    // 2. Chart Pemasukan (Bar)
    if (chartPemasukan) chartPemasukan.destroy();
    const ctxP = document.getElementById('chartPemasukan').getContext('2d');
    chartPemasukan = new Chart(ctxP, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ label: 'Iuran Lunas (Rp)', data: dataIuran, backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 6, barThickness: 'flex', maxBarThickness: 32 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } }, y: { ticks: { color: textColor, font: { size: 10 }, callback: function(val) { return 'Rp ' + (val/1000) + 'k'; } }, grid: { color: gridColor, drawBorder: false } } } }
    });
}

function handleQuickSearch(event) {
    if(event.key === 'Enter') {
        const q = event.target.value;
        const wargaTabBtn = document.querySelectorAll('.modal-tab')[1];
        if(wargaTabBtn) switchModalTab('modal-warga-list', wargaTabBtn);
        document.getElementById('search-warga-input').value = q;
        filterWargaList();
    }
}

// --- Add Workspace (Card Stack Form) Functions ---
let stackAnimating = false;
const stackConfig = { offsetY: 16, scaleStep: 0.05, brightnessStep: 10 };

// Stack Form & File Upload Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Stack Form Logic (Add Workspace)
    const formStack = document.getElementById('form-stack');
    if (formStack) {
        formStack.addEventListener('click', (e) => {
            const nextBtn = e.target.closest('.next-stack-btn');
            if (!nextBtn) return;

            const card = e.target.closest('.stack-card');
            if (!card || card !== formStack.firstElementChild || stackAnimating) return;

            stackAnimating = true;
            card.classList.add('throw-out-left');

            setTimeout(() => {
                card.classList.remove('throw-out-left');
                card.classList.add('no-transition');
                formStack.appendChild(card);
                updateFormStack();
                void card.offsetWidth;
                card.classList.remove('no-transition');
                stackAnimating = false;
            }, 600);
        });
    }

    // File Upload Logic
    const imageUploadInput = document.getElementById('blok-image-upload');
    if (imageUploadInput) {
        imageUploadInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : null;
            if (fileName) {
                document.getElementById('upload-text-main').innerText = "File Dipilih:";
                document.getElementById('upload-text-main').style.color = 'var(--accent-color)';
                document.getElementById('upload-text-sub').innerText = fileName;
            }
        });
    }
});

function updateFormStack() {
    const stack = document.getElementById('form-stack');
    if (!stack) return;
    const cards = Array.from(stack.children);

    cards.forEach((card, index) => {
        const scale = 1 - (index * stackConfig.scaleStep);
        const translateY = index * stackConfig.offsetY;
        const zIndex = cards.length - index;
        const brightness = 100 - (index * stackConfig.brightnessStep);

        card.style.pointerEvents = index === 0 ? 'auto' : 'none'; // Hanya kartu teratas yang aktif
        card.style.zIndex = zIndex;
        card.style.transform = `translateY(${translateY}px) scale(${scale})`;
        card.style.filter = `brightness(${brightness}%)`;
        card.style.opacity = index > 2 ? 0 : 1; // Sembunyikan kartu berlebih
    });
}

function openAddBlockModal() {
    document.getElementById('add-block-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(updateFormStack, 50); // Memuat posisi awal tumpukan kartu
}

function closeAddBlockModal() {
    document.getElementById('add-block-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function submitNewBlock(btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    lucide.createIcons();
    
    const namaBlok = document.getElementById('input-nama-blok').value;
    const koordinator = document.getElementById('input-koordinator-blok').value;
    const imageUpload = document.getElementById('blok-image-upload');

    const formData = new FormData();
    formData.append('nama_blok', namaBlok);
    formData.append('koordinator', koordinator);
    if (imageUpload.files.length > 0) {
        formData.append('logo_image', imageUpload.files[0]);
    }

    fetch('api/tambah_blok.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload(); // Refresh halaman untuk menampilkan blok baru
        } else {
            alert('Gagal: ' + data.message);
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan koneksi.');
        btn.innerHTML = originalText;
    });
}

// --- Edit & Delete Workspace Functions ---
function editBlok(id, nama, koordinator, bulanMulai, tahunMulai) {
    document.getElementById('edit-blok-id').value = id;
    document.getElementById('edit-nama-blok').value = nama;
    document.getElementById('edit-koordinator-blok').value = koordinator;
    
    if (bulanMulai !== null && tahunMulai !== null) {
        document.getElementById('edit-periode-bulan').value = bulanMulai;
        document.getElementById('edit-periode-tahun').value = tahunMulai;
    } else {
        document.getElementById('edit-periode-bulan').value = new Date().getMonth();
        document.getElementById('edit-periode-tahun').value = new Date().getFullYear();
    }

    document.getElementById('edit-logo-blok').value = ''; // Reset input gambar
    
    document.getElementById('edit-block-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditBlockModal() {
    document.getElementById('edit-block-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function submitEditBlock(btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader"></i> Menyimpan...';
    lucide.createIcons();

    const id = document.getElementById('edit-blok-id').value;
    const nama = document.getElementById('edit-nama-blok').value;
    const koordinator = document.getElementById('edit-koordinator-blok').value;
    const bulan = document.getElementById('edit-periode-bulan').value;
    const tahun = document.getElementById('edit-periode-tahun').value;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('nama_blok', nama);
    formData.append('koordinator', koordinator);
    
    // Tangkap gambar jika ada file yang diunggah saat edit
    const logoUpload = document.getElementById('edit-logo-blok');
    if (logoUpload && logoUpload.files.length > 0) {
        formData.append('logo_image', logoUpload.files[0]);
    }

    fetch('api/edit_blok.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            // Simpan periode (General Setting)
            const fdPeriod = new FormData();
            fdPeriod.append('blok_id', id);
            fdPeriod.append('bulan', bulan);
            fdPeriod.append('tahun', tahun);
            
            fetch('api/simpan_periode_blok.php', { method: 'POST', body: fdPeriod })
            .then(r => r.json())
            .then(res => {
                window.location.reload();
            })
            .catch(() => window.location.reload()); // Fallback reload
        }
        else { 
            alert('Gagal: ' + data.message); 
            btn.innerHTML = originalText; 
        }
    }).catch(e => { alert('Terjadi kesalahan koneksi'); btn.innerHTML = originalText; });
}

function hapusBlok(id, nama, totalWarga) {
    // Validasi pencegahan hapus jika masih ada warga
    if (totalWarga > 0) {
        alert(`PENGHAPUSAN DITOLAK!\n\nTidak dapat menghapus ${nama} karena masih terdapat ${totalWarga} data warga di dalamnya. Kosongkan atau pindahkan data warga terlebih dahulu.`);
        return;
    }

    if (confirm(`PERINGATAN:\nApakah Anda yakin ingin menghapus ${nama} secara permanen? Tindakan ini tidak dapat dibatalkan.`)) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/hapus_blok.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') window.location.reload();
            else alert('Gagal menghapus: ' + data.message);
        }).catch(e => alert('Terjadi kesalahan koneksi'));
    }
}
