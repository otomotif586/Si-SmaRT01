let ruangWargaInitialized = false;
let ruangWargaState = {
    profile: null,
    linkedWarga: null,
    dashboard: null,
    history: [],
    pengaduan: [],
    updates: [],
    infoFeed: []
};

function statusClass(status) {
    if (status === 'Selesai') return 'done';
    if (status === 'Diproses') return 'process';
    if (status === 'Ditolak') return 'reject';
    return 'pending';
}

function formatDateTime(value) {
    if (!value) return '-';
    try {
        return new Date(value).toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    } catch (e) {
        return value;
    }
}

function bulanNama(index) {
    const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return m[index] || '-';
}

async function rwFetchJson(url, options = {}) {
    const response = await fetch(url, options);
    return response.json();
}

function rwSetText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function renderRuangWarga() {
    const data = ruangWargaState;

    rwSetText('rw-stat-global-warga', `${data.dashboard?.total_warga_global ?? 0}`);
    rwSetText('rw-stat-global-blok', `${data.dashboard?.total_blok ?? 0}`);
    rwSetText('rw-stat-lunas', `${data.dashboard?.total_lunas_saya ?? 0} Bulan`);
    rwSetText('rw-stat-tunggakan', `${data.dashboard?.total_tunggakan_saya ?? 0} Bulan`);

    if (data.profile) {
        const p = data.profile;
        const nama = document.getElementById('rw-nama');
        const user = document.getElementById('rw-username');
        const role = document.getElementById('rw-role');
        if (nama) nama.value = p.nama_lengkap || '';
        if (user) user.value = p.username || '';
        if (role) role.value = p.role || '-';
    }

    const linked = document.getElementById('rw-linked-warga');
    if (linked) {
        if (!data.linkedWarga) {
            linked.innerHTML = '<p class="text-secondary">Belum ada data warga terhubung ke akun ini.</p>';
        } else {
            linked.innerHTML = `
                <p><strong>Nama:</strong> ${data.linkedWarga.nama_lengkap || '-'}</p>
                <p><strong>Blok:</strong> ${data.linkedWarga.nama_blok || '-'}</p>
                <p><strong>Rumah:</strong> ${data.linkedWarga.nomor_rumah || '-'}</p>
                <p><strong>No WA:</strong> ${data.linkedWarga.no_wa || '-'}</p>
                <p><strong>Status:</strong> ${data.linkedWarga.status_kependudukan || '-'}</p>
            `;
        }
    }

    const historyBody = document.getElementById('rw-history-body');
    if (historyBody) {
        if (!data.history.length) {
            historyBody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary">Belum ada history pembayaran.</td></tr>';
        } else {
            historyBody.innerHTML = data.history.map((row) => `
                <tr>
                    <td>${row.tahun || '-'}</td>
                    <td>${bulanNama(Number(row.bulan))}</td>
                    <td>${row.status || '-'}</td>
                    <td>Rp ${(Number(row.total_tagihan || 0)).toLocaleString('id-ID')}</td>
                    <td>${formatDateTime(row.tanggal_bayar)}</td>
                </tr>
            `).join('');
        }
    }

    const pengaduanList = document.getElementById('rw-pengaduan-list');
    if (pengaduanList) {
        if (!data.pengaduan.length) {
            pengaduanList.innerHTML = '<p class="text-secondary">Belum ada pengaduan.</p>';
        } else {
            pengaduanList.innerHTML = data.pengaduan.map((row) => `
                <div class="rw-list-item">
                    <h4>${row.judul}</h4>
                    <p>${row.isi}</p>
                    <div class="rw-item-meta">
                        <span class="rw-pill ${statusClass(row.status)}">${row.status}</span>
                        <small class="text-secondary">${formatDateTime(row.created_at)}</small>
                    </div>
                    <div class="rw-item-actions">
                        <button class="rw-item-btn" onclick="rwEditPengaduan(${row.id})">Edit</button>
                        <button class="rw-item-btn delete" onclick="rwDeletePengaduan(${row.id})">Hapus</button>
                    </div>
                </div>
            `).join('');
        }
    }

    const updateList = document.getElementById('rw-update-list');
    if (updateList) {
        if (!data.updates.length) {
            updateList.innerHTML = '<p class="text-secondary">Belum ada update informasi.</p>';
        } else {
            updateList.innerHTML = data.updates.map((row) => `
                <div class="rw-list-item">
                    <h4>${row.judul}</h4>
                    <p>${row.isi}</p>
                    <div class="rw-item-meta">
                        <small class="text-secondary">${formatDateTime(row.created_at)}</small>
                    </div>
                    <div class="rw-item-actions">
                        <button class="rw-item-btn" onclick="rwEditUpdate(${row.id})">Edit</button>
                        <button class="rw-item-btn delete" onclick="rwDeleteUpdate(${row.id})">Hapus</button>
                    </div>
                </div>
            `).join('');
        }
    }

    const feed = document.getElementById('rw-info-feed');
    if (feed) {
        if (!data.infoFeed.length) {
            feed.innerHTML = '<p class="text-secondary">Belum ada informasi terbaru.</p>';
        } else {
            feed.innerHTML = data.infoFeed.map((row) => `
                <div class="rw-list-item">
                    <h4>${row.judul}</h4>
                    <p>${row.ringkas}</p>
                    <div class="rw-item-meta"><small class="text-secondary">${formatDateTime(row.waktu)}</small></div>
                </div>
            `).join('');
        }
    }
}

async function loadRuangWargaData() {
    const res = await rwFetchJson('api/ruang_warga_bootstrap.php');
    if (res.status !== 'success') throw new Error(res.message || 'Gagal memuat ruang warga');
    ruangWargaState = {
        profile: res.profile || null,
        linkedWarga: res.linked_warga || null,
        dashboard: res.dashboard || {},
        history: res.history || [],
        pengaduan: res.pengaduan || [],
        updates: res.updates || [],
        infoFeed: res.info_feed || []
    };
    renderRuangWarga();
}

function rwSwitchTab(tabId) {
    document.querySelectorAll('#page-ruang-warga .rw-tab-btn').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.rwTab === tabId);
    });

    document.querySelectorAll('#page-ruang-warga .rw-tab-panel').forEach((panel) => {
        panel.classList.add('hidden');
    });

    const panel = document.getElementById(`rw-tab-${tabId}`);
    if (panel) panel.classList.remove('hidden');
}

async function rwSaveProfile(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('nama_lengkap', document.getElementById('rw-nama')?.value || '');
    formData.append('username', document.getElementById('rw-username')?.value || '');
    formData.append('password', document.getElementById('rw-password')?.value || '');

    const res = await rwFetchJson('api/ruang_warga_save_profile.php', { method: 'POST', body: formData });
    if (res.status !== 'success') throw new Error(res.message || 'Gagal menyimpan profil');

    document.getElementById('rw-password').value = '';
    showToast('Profil berhasil diperbarui');
    await loadRuangWargaData();
}

async function rwSavePengaduan(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('id', document.getElementById('rw-pengaduan-id')?.value || '0');
    formData.append('judul', document.getElementById('rw-pengaduan-judul')?.value || '');
    formData.append('isi', document.getElementById('rw-pengaduan-isi')?.value || '');

    const res = await rwFetchJson('api/ruang_warga_save_pengaduan.php', { method: 'POST', body: formData });
    if (res.status !== 'success') throw new Error(res.message || 'Gagal menyimpan pengaduan');

    rwResetPengaduanForm();
    showToast('Pengaduan tersimpan');
    await loadRuangWargaData();
}

async function rwSaveUpdate(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('id', document.getElementById('rw-update-id')?.value || '0');
    formData.append('judul', document.getElementById('rw-update-judul')?.value || '');
    formData.append('isi', document.getElementById('rw-update-isi')?.value || '');

    const res = await rwFetchJson('api/ruang_warga_save_update.php', { method: 'POST', body: formData });
    if (res.status !== 'success') throw new Error(res.message || 'Gagal menyimpan update');

    rwResetUpdateForm();
    showToast('Update informasi tersimpan');
    await loadRuangWargaData();
}

function rwResetPengaduanForm() {
    const id = document.getElementById('rw-pengaduan-id');
    const judul = document.getElementById('rw-pengaduan-judul');
    const isi = document.getElementById('rw-pengaduan-isi');
    if (id) id.value = '';
    if (judul) judul.value = '';
    if (isi) isi.value = '';
}

function rwResetUpdateForm() {
    const id = document.getElementById('rw-update-id');
    const judul = document.getElementById('rw-update-judul');
    const isi = document.getElementById('rw-update-isi');
    if (id) id.value = '';
    if (judul) judul.value = '';
    if (isi) isi.value = '';
}

window.rwEditPengaduan = function (id) {
    const item = ruangWargaState.pengaduan.find((x) => Number(x.id) === Number(id));
    if (!item) return;
    document.getElementById('rw-pengaduan-id').value = item.id;
    document.getElementById('rw-pengaduan-judul').value = item.judul;
    document.getElementById('rw-pengaduan-isi').value = item.isi;
    rwSwitchTab('pengaduan');
};

window.rwDeletePengaduan = async function (id) {
    const formData = new FormData();
    formData.append('id', String(id));
    const res = await rwFetchJson('api/ruang_warga_delete_pengaduan.php', { method: 'POST', body: formData });
    if (res.status !== 'success') {
        showToast(res.message || 'Gagal menghapus', 'error');
        return;
    }
    showToast('Pengaduan dihapus');
    await loadRuangWargaData();
};

window.rwEditUpdate = function (id) {
    const item = ruangWargaState.updates.find((x) => Number(x.id) === Number(id));
    if (!item) return;
    document.getElementById('rw-update-id').value = item.id;
    document.getElementById('rw-update-judul').value = item.judul;
    document.getElementById('rw-update-isi').value = item.isi;
    rwSwitchTab('informasi');
};

window.rwDeleteUpdate = async function (id) {
    const formData = new FormData();
    formData.append('id', String(id));
    const res = await rwFetchJson('api/ruang_warga_delete_update.php', { method: 'POST', body: formData });
    if (res.status !== 'success') {
        showToast(res.message || 'Gagal menghapus', 'error');
        return;
    }
    showToast('Update informasi dihapus');
    await loadRuangWargaData();
};

function bindRuangWargaEvents() {
    document.querySelectorAll('#page-ruang-warga .rw-tab-btn').forEach((btn) => {
        btn.addEventListener('click', () => rwSwitchTab(btn.dataset.rwTab));
    });

    document.getElementById('rw-profile-form')?.addEventListener('submit', async (e) => {
        try {
            await rwSaveProfile(e);
        } catch (err) {
            showToast(err.message, 'error');
        }
    });

    document.getElementById('rw-pengaduan-form')?.addEventListener('submit', async (e) => {
        try {
            await rwSavePengaduan(e);
        } catch (err) {
            showToast(err.message, 'error');
        }
    });

    document.getElementById('rw-update-form')?.addEventListener('submit', async (e) => {
        try {
            await rwSaveUpdate(e);
        } catch (err) {
            showToast(err.message, 'error');
        }
    });

    document.getElementById('rw-pengaduan-reset')?.addEventListener('click', rwResetPengaduanForm);
    document.getElementById('rw-update-reset')?.addEventListener('click', rwResetUpdateForm);
}

window.initRuangWarga = async function () {
    try {
        if (!ruangWargaInitialized) {
            bindRuangWargaEvents();
            ruangWargaInitialized = true;
        }
        await loadRuangWargaData();
    } catch (err) {
        showToast(err.message || 'Gagal memuat Ruang Warga', 'error');
    }
};
