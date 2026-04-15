const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
const dockButtons = Array.from(document.querySelectorAll('.tab-dock-btn'));
const goTabLinks = Array.from(document.querySelectorAll('[data-go-tab]'));
const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));

function activateTab(tabId, shouldScroll = false) {
    if (!tabId) return;

    tabButtons.forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
    });

    dockButtons.forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
    });

    tabPanels.forEach((panel) => {
        panel.classList.toggle('active', panel.getAttribute('data-panel') === tabId);
    });

    if (shouldScroll) {
        const targetPanel = document.querySelector(`.tab-panel[data-panel="${tabId}"]`);
        if (targetPanel) {
            targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

function bindTabNavigation() {
    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => activateTab(btn.getAttribute('data-tab')));
    });

    dockButtons.forEach((btn) => {
        btn.addEventListener('click', () => activateTab(btn.getAttribute('data-tab'), true));
    });

    goTabLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            activateTab(link.getAttribute('data-go-tab'), true);
        });
    });
}

function initBootLoader() {
    const loader = document.getElementById('rwBootLoader');
    const fill = document.getElementById('rwBootFill');
    if (!loader || !fill) return;

    let value = 8;
    fill.style.width = value + '%';

    const tick = setInterval(() => {
        value = Math.min(94, value + Math.max(2, Math.round((100 - value) / 12)));
        fill.style.width = value + '%';
    }, 90);

    window.addEventListener('load', () => {
        clearInterval(tick);
        fill.style.width = '100%';
        setTimeout(() => loader.classList.add('hidden'), 240);
    });
}

const statusPernikahan = document.getElementById('statusPernikahan');
const pasanganSection = document.getElementById('pasanganSection');
function togglePasangan() {
    if (!statusPernikahan || !pasanganSection) return;
    pasanganSection.style.display = statusPernikahan.value === 'Menikah' ? '' : 'none';
}

function removeEmpty(containerId) {
    const c = document.getElementById(containerId);
    if (!c) return;
    c.querySelectorAll('.empty').forEach((e) => e.remove());
}

function nextIndex(containerSelector, rowClass) {
    return document.querySelectorAll(`${containerSelector} .${rowClass}`).length;
}

function bindDynamicRows() {
    document.getElementById('addAnakBtn')?.addEventListener('click', () => {
        removeEmpty('anakContainer');
        const i = nextIndex('#anakContainer', 'child-row');
        const wrap = document.createElement('div');
        wrap.className = 'form-grid list-item child-row';
        wrap.innerHTML = `
            <div class="form-group"><input name="anak[${i}][nik]" placeholder="NIK Anak"></div>
            <div class="form-group"><input name="anak[${i}][nama]" placeholder="Nama Anak"></div>
            <div class="form-group"><input name="anak[${i}][tempat]" placeholder="Tempat Lahir"></div>
            <div class="form-group"><input type="date" name="anak[${i}][tgl]"></div>
        `;
        document.getElementById('anakContainer')?.appendChild(wrap);
    });

    document.getElementById('addOrangBtn')?.addEventListener('click', () => {
        removeEmpty('orangContainer');
        const i = nextIndex('#orangContainer', 'person-row');
        const wrap = document.createElement('div');
        wrap.className = 'form-grid list-item person-row';
        wrap.innerHTML = `
            <div class="form-group"><input name="orang_lain[${i}][nama]" placeholder="Nama"></div>
            <div class="form-group"><input type="number" name="orang_lain[${i}][umur]" placeholder="Umur"></div>
            <div class="form-group full"><input name="orang_lain[${i}][hubungan]" placeholder="Status Hubungan"></div>
        `;
        document.getElementById('orangContainer')?.appendChild(wrap);
    });

    document.getElementById('addKendaraanBtn')?.addEventListener('click', () => {
        removeEmpty('kendaraanContainer');
        const i = nextIndex('#kendaraanContainer', 'vehicle-row');
        const wrap = document.createElement('div');
        wrap.className = 'form-grid list-item vehicle-row';
        wrap.innerHTML = `
            <div class="form-group"><input name="kendaraan[${i}][nopol]" placeholder="No Polisi"></div>
            <div class="form-group"><input name="kendaraan[${i}][jenis]" placeholder="Jenis Kendaraan"></div>
        `;
        document.getElementById('kendaraanContainer')?.appendChild(wrap);
    });
}

function renderAvatarNode(container, src) {
    if (!container) return;

    const target = container.classList.contains('hero-profile-photo')
        ? (container.querySelector('.hero-avatar-core') || container)
        : container;

    if (src) {
        target.innerHTML = `<img src="${src}" alt="Avatar Warga">`;
    } else {
        target.innerHTML = '<i class="fas fa-user"></i>';
    }
}

function bindAvatarPreview() {
    const input = document.getElementById('avatarInput');
    if (!input) return;

    const previewCard = document.getElementById('avatarPreviewCard');
    const heroAvatar = document.getElementById('wargaAvatarHero');
    const inlineAvatar = document.getElementById('wargaAvatarInline');

    input.addEventListener('change', () => {
        const file = input.files && input.files[0] ? input.files[0] : null;
        if (!file) return;

        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            const src = ev.target && ev.target.result ? String(ev.target.result) : '';
            renderAvatarNode(previewCard, src);
            renderAvatarNode(heroAvatar, src);
            renderAvatarNode(inlineAvatar, src);
        };
        reader.readAsDataURL(file);
    });
}

(function initRuangWargaStandalone() {
    initBootLoader();
    bindTabNavigation();
    bindDynamicRows();
    bindAvatarPreview();
    statusPernikahan?.addEventListener('change', togglePasangan);
    togglePasangan();
})();
