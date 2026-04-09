function initKeamanan() {
    loadKeamananData();
    // Initialize default numbers if none exist
    if (!localStorage.getItem('smartrt_panic_numbers')) {
        localStorage.setItem('smartrt_panic_numbers', JSON.stringify(['08123456789', '08987654321']));
    }
}

function loadKeamananData() {
    renderRecentActivity();
    renderSchedule();
    renderGuardList();
    renderIncidentLogs();
    renderLeaveRequests();
}

function switchKeamananTab(tabId, element) {
    document.querySelectorAll('.km-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('#page-keamanan .sub-nav-tab').forEach(el => el.classList.remove('active'));

    document.getElementById(tabId).classList.remove('hidden');
    element.classList.add('active');
}

// --- Panic Button Logic ---
function triggerPanic() {
    const numbers = JSON.parse(localStorage.getItem('smartrt_panic_numbers') || '[]');
    const list = document.getElementById('panic-recipient-list');
    list.innerHTML = '';

    if (numbers.length === 0) {
        list.innerHTML = '<p class="text-secondary py-4">Belum ada nomor kontak darurat. Klik ikon gerigi untuk mengatur.</p>';
    } else {
        numbers.forEach(num => {
            const waLink = `https://wa.me/${num.replace(/^0/, '62')}?text=DARURAT! Saya membutuhkan bantuan segera di lokasi saya. (Sistem SmartRT Pro)`;
            list.innerHTML += `
                <a href="${waLink}" target="_blank" class="glass-card-deluxe p-4 text-center block no-underline" style="border: 1px solid rgba(239, 68, 68, 0.2);">
                    <div style="color: #25d366; margin-bottom: 8px;"><i data-lucide="phone"></i></div>
                    <div class="font-bold text-color" style="font-size: 0.85rem;">${num}</div>
                    <div class="text-secondary" style="font-size: 0.65rem;">Hubungi via WhatsApp</div>
                </a>
            `;
        });
    }
    
    openModal('modal-panic-broadcast');
    lucide.createIcons();
}

function openPanicSettings() {
    const numbers = JSON.parse(localStorage.getItem('smartrt_panic_numbers') || '[]');
    const container = document.getElementById('panic-numbers-container');
    container.innerHTML = '';
    
    numbers.forEach((num, index) => {
        container.innerHTML += `
            <div class="input-group">
                <input type="text" class="input-field" value="${num}" placeholder="Contoh: 0812..." style="padding-left: 16px;">
                <button class="button-secondary" onclick="this.parentElement.remove()" style="padding: 0 16px; border-radius: 99px;"><i data-lucide="trash-2"></i></button>
            </div>
        `;
    });
    
    if (numbers.length === 0) addPanicNumber();
    
    openModal('modal-panic-settings');
    lucide.createIcons();
}

function addPanicNumber() {
    const container = document.getElementById('panic-numbers-container');
    const div = document.createElement('div');
    div.className = 'input-group';
    div.innerHTML = `
        <input type="text" class="input-field" placeholder="Contoh: 0812..." style="padding-left: 16px;">
        <button class="button-secondary" onclick="this.parentElement.remove()" style="padding: 0 16px; border-radius: 99px;"><i data-lucide="trash-2"></i></button>
    `;
    container.appendChild(div);
    lucide.createIcons();
}

function savePanicSettings() {
    const inputs = document.querySelectorAll('#panic-numbers-container input');
    const numbers = Array.from(inputs).map(i => i.value.trim()).filter(v => v !== '');
    
    localStorage.setItem('smartrt_panic_numbers', JSON.stringify(numbers));
    closeModal('modal-panic-settings');
    
    Swal.fire({
        icon: 'success',
        title: 'Pengaturan Disimpan',
        text: 'Daftar kontak darurat telah diperbarui.',
        timer: 1500,
        showConfirmButton: false
    });
}

// --- Mock Data Rendering ---

function renderRecentActivity() {
    const container = document.getElementById('km-recent-activity');
    const activities = [
        { title: 'Patroli Blok A Selesai', meta: '10 Menit lalu • Oleh Danu', color: 'emerald' },
        { title: 'Tamu Bp. Salim (Blok B-02)', meta: '1 Jam lalu • Masuk via Pos 1', color: 'secondary' },
        { title: 'Pengecekan CCTV Area Parkir', meta: '2 Jam lalu • Berfungsi Normal', color: 'emerald' }
    ];
    
    container.innerHTML = activities.map(a => `
        <div class="report-item border-left-${a.color}" style="padding: 12px 20px;">
            <p class="report-title" style="font-weight: 700; margin: 0; font-size: 0.9rem;">${a.title}</p>
            <p class="report-meta" style="font-size: 0.75rem; color: var(--text-secondary-color); margin-top: 4px;">${a.meta}</p>
        </div>
    `).join('');
}

function renderSchedule() {
    const body = document.getElementById('km-schedule-body');
    const days = ['Senin, 10 Apr', 'Selasa, 11 Apr', 'Rabu, 12 Apr', 'Kamis, 13 Apr', 'Jumat, 14 Apr'];
    
    body.innerHTML = days.map(d => `
        <tr>
            <td class="font-bold">${d}</td>
            <td>Rudi, Ahmad, Danu</td>
            <td>Suryo, Budi, Hendra</td>
        </tr>
    `).join('');
}

function renderGuardList() {
    const container = document.getElementById('km-guard-list');
    const guards = [
        { name: 'Danu Wijaya', role: 'Danru (Komandan Regu)', status: 'On Duty', id: 'S-001' },
        { name: 'Ahmad Subast', role: 'Anggota', status: 'On Duty', id: 'S-002' },
        { name: 'Rudi Hermawan', role: 'Anggota', status: 'On Duty', id: 'S-003' },
        { name: 'Suryo Putro', role: 'Anggota', status: 'Off', id: 'S-004' }
    ];
    
    container.innerHTML = guards.map(g => `
        <div class="glass-card-deluxe p-4 flex items-center gap-4">
            <div class="avatar" style="background: var(--accent-color); color: white; width: 50px; height: 50px; font-size: 1.2rem;">
                ${g.name.split(' ').map(n => n[0]).join('')}
            </div>
            <div>
                <h5 class="font-bold text-color" style="margin: 0;">${g.name}</h5>
                <p class="text-secondary" style="font-size: 0.7rem; margin: 2px 0;">${g.role} • ${g.id}</p>
                <span class="badge ${g.status === 'On Duty' ? 'badge-status-resolved' : 'badge-status-waiting'}">${g.status}</span>
            </div>
        </div>
    `).join('');
}

function renderIncidentLogs() {
    const body = document.getElementById('km-incident-body');
    const logs = [
        { time: '14:20', guard: 'Danu', event: 'Kurir J&T - Paket A-12', loc: 'Pos Utama', status: 'Selesai' },
        { time: '10:15', guard: 'Ahmad', event: 'Tamu Bp. Budi (Keluarga)', loc: 'Blok B-05', status: 'Selesai' },
        { time: '08:00', guard: 'System', event: 'Serah Terima Shift', loc: 'Ruang Keamanan', status: 'Internal' }
    ];
    
    body.innerHTML = logs.map(l => `
        <tr>
            <td>${l.time}</td>
            <td class="font-bold">${l.guard}</td>
            <td>${l.event}</td>
            <td>${l.loc}</td>
            <td><span class="badge badge-status-resolved">${l.status}</span></td>
        </tr>
    `).join('');
}

function renderLeaveRequests() {
    const container = document.getElementById('km-leave-requests');
    const requests = [
        { name: 'Hendra Saputra', type: 'Izin (Sakit)', date: '12 Apr - 13 Apr', status: 'Menunggu Approval' },
        { name: 'Budi Santoso', type: 'Cuti Tahunan', date: '20 Apr - 25 Apr', status: 'Disetujui' }
    ];
    
    container.innerHTML = requests.map(r => `
        <div class="report-item border-left-secondary" style="padding: 12px 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p class="report-title" style="font-weight: 700; margin: 0;">${r.name}</p>
                <p class="report-meta" style="font-size: 0.7rem;">${r.type} • ${r.date}</p>
            </div>
            <span class="badge ${r.status === 'Disetujui' ? 'badge-status-resolved' : 'badge-status-waiting'}">${r.status}</span>
        </div>
    `).join('');
    
    const stats = document.getElementById('km-attendance-stats');
    stats.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="flex justify-between items-center bg-emerald-light p-3 rounded-xl border border-emerald">
                <span class="text-emerald font-bold">Hadir Hari Ini</span>
                <span class="text-emerald font-bold">3/4</span>
            </div>
            <div class="flex justify-between items-center bg-orange-light p-3 rounded-xl border border-orange">
                <span class="text-orange font-bold">Izin / Cuti</span>
                <span class="text-orange font-bold">1</span>
            </div>
            <p class="text-secondary text-center" style="font-size: 0.7rem; font-style: italic;">Data diperbarui otomatis setiap pergantian shift.</p>
        </div>
    `;
}

// Global modal helpers if not defined
if (typeof openModal !== 'function') {
    window.openModal = function(id) { document.getElementById(id).classList.remove('hidden'); }
}
if (typeof closeModal !== 'function') {
    window.closeModal = function(id) { document.getElementById(id).classList.add('hidden'); }
}
