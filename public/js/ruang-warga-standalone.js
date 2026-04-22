const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
const dockButtons = Array.from(document.querySelectorAll('.tab-dock-btn'));
const goTabLinks = Array.from(document.querySelectorAll('[data-go-tab]'));
const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));
const modalTriggers = Array.from(document.querySelectorAll('[data-open-modal]'));
const modalClosers = Array.from(document.querySelectorAll('[data-close-modal]'));
const RW_STANDALONE_TAB_KEY = 'rw.standalone.activeTab';
let rwActivePanel = tabPanels.find((panel) => panel.classList.contains('active')) || null;

function rwStandaloneSaveTab(tabId) {
    if (!tabId) return;
    try {
        window.localStorage.setItem(RW_STANDALONE_TAB_KEY, tabId);
    } catch (e) {
        // Ignore storage restrictions silently.
    }
}

function rwStandaloneReadTab() {
    try {
        return window.localStorage.getItem(RW_STANDALONE_TAB_KEY) || '';
    } catch (e) {
        return '';
    }
}

function rwStandalonePulse(target) {
    if (!target) return;
    target.classList.remove('rw-haptic-hit');
    window.requestAnimationFrame(() => {
        target.classList.add('rw-haptic-hit');
        window.setTimeout(() => target.classList.remove('rw-haptic-hit'), 220);
    });
}

function rwStandaloneRipple(target, event) {
    if (!target || !event) return;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const rect = target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height) * 1.2;
    const x = (event.clientX || (rect.left + (rect.width / 2))) - rect.left;
    const y = (event.clientY || (rect.top + (rect.height / 2))) - rect.top;

    target.classList.add('rw-ripple-host');

    const ripple = document.createElement('span');
    ripple.className = 'rw-ripple-dot';
    ripple.style.width = `${size}px`;
    ripple.style.height = `${size}px`;
    ripple.style.left = `${x - (size / 2)}px`;
    ripple.style.top = `${y - (size / 2)}px`;

    target.appendChild(ripple);
    window.setTimeout(() => ripple.remove(), 520);
}

function rwStandaloneStaggerReveal(scope) {
    const root = scope || document;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const nodes = root.querySelectorAll([
        '.hero',
        '.tabs .tab-btn',
        '.section',
        '.list-item',
        '.info-important-item',
        '.pantau-info-mini-item',
        '.table-wrap tr'
    ].join(', '));

    nodes.forEach((node, index) => {
        node.classList.remove('rw-reveal-ready');
        node.style.setProperty('--rw-reveal-delay', `${Math.min(index * 24, 320)}ms`);
    });

    window.requestAnimationFrame(() => {
        nodes.forEach((node) => node.classList.add('rw-reveal-ready'));
    });
}

function activateTab(tabId, shouldScroll = false) {
    if (!tabId) return;

    tabButtons.forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
    });

    dockButtons.forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
    });

    const targetPanel = tabPanels.find((panel) => panel.getAttribute('data-panel') === tabId) || null;
    const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!targetPanel) return;

    if (rwActivePanel && rwActivePanel !== targetPanel) {
        if (reduceMotion) {
            rwActivePanel.classList.remove('active');
        } else {
            rwActivePanel.classList.add('rw-panel-exit');
            window.setTimeout(() => {
                rwActivePanel.classList.remove('active', 'rw-panel-exit');
            }, 180);
        }
    }

    if (!targetPanel.classList.contains('active')) {
        targetPanel.classList.add('active');
    }

    if (!reduceMotion) {
        targetPanel.classList.remove('rw-panel-enter-active');
        targetPanel.classList.add('rw-panel-enter');
        window.requestAnimationFrame(() => {
            targetPanel.classList.add('rw-panel-enter-active');
        });
        window.setTimeout(() => {
            targetPanel.classList.remove('rw-panel-enter', 'rw-panel-enter-active');
        }, 220);
    }

    rwActivePanel = targetPanel;

    if (shouldScroll) {
        const scrollPanel = document.querySelector(`.tab-panel[data-panel="${tabId}"]`);
        if (scrollPanel) {
            scrollPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    rwStandaloneSaveTab(tabId);
    rwStandaloneStaggerReveal(document.querySelector(`.tab-panel[data-panel="${tabId}"]`) || document);
}

function bindProfileWizard() {
    const form = document.getElementById('formWargaLengkap');
    if (!form) return;

    const steps = Array.from(form.querySelectorAll('.rw-profile-step'));
    const dots = Array.from(form.querySelectorAll('.rw-profile-step-dot'));
    const progressFill = form.querySelector('.rw-profile-progress-fill');
    if (!steps.length) return;

    let stepIndex = 0;

    const updateStep = () => {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === stepIndex);
        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index <= stepIndex);
            dot.classList.toggle('current', index === stepIndex);
        });

        if (progressFill) {
            const progress = ((stepIndex + 1) / steps.length) * 100;
            progressFill.style.width = `${progress}%`;
        }
    };

    form.querySelectorAll('[data-profile-next]').forEach((button) => {
        button.addEventListener('click', () => {
            stepIndex = Math.min(steps.length - 1, stepIndex + 1);
            updateStep();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    form.querySelectorAll('[data-profile-prev]').forEach((button) => {
        button.addEventListener('click', () => {
            stepIndex = Math.max(0, stepIndex - 1);
            updateStep();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stepIndex = index;
            updateStep();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    updateStep();
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

function bindStandaloneHaptic() {
    const selector = [
        '.btn',
        '.rw-action-btn',
        '.rw-sheet-trigger',
        '.tab-btn',
        '.tab-dock-btn',
        '.info-important-item',
        '.pantau-info-mini-item'
    ].join(', ');

    document.addEventListener('pointerdown', (event) => {
        const target = event.target.closest(selector);
        if (!target) return;
        rwStandalonePulse(target);
        rwStandaloneRipple(target, event);
    });
}

function bindRwHeaderInteractions() {
    const appHeader = document.getElementById('rwAppHeader');
    if (!appHeader) return;
    let ticking = false;
    let isCompact = false;

    const computeThreshold = () => {
        const headerHeight = appHeader.offsetHeight || 120;
        const enter = Math.max(42, Math.min(130, Math.round(headerHeight * 0.34)));
        const exit = Math.max(22, Math.round(enter * 0.56));
        return { enter, exit };
    };

    let threshold = computeThreshold();
    window.addEventListener('resize', () => {
        threshold = computeThreshold();
    }, { passive: true });

    const syncCompactHeader = () => {
        const y = window.scrollY || 0;
        if (!isCompact && y >= threshold.enter) {
            isCompact = true;
        } else if (isCompact && y <= threshold.exit) {
            isCompact = false;
        }
        appHeader.classList.toggle('is-sticky-compact', isCompact);
        ticking = false;
    };

    syncCompactHeader();
    window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(syncCompactHeader);
    }, { passive: true });

    document.querySelectorAll('[data-rw-scroll]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-rw-scroll');
            if (!targetId) return;
            const target = document.getElementById(targetId);
            if (!target) return;
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const metrics = document.querySelectorAll('[data-rw-metric] strong');
    metrics.forEach((node) => {
        const parent = node.closest('[data-rw-metric]');
        const rawValue = parent ? Number(parent.getAttribute('data-value') || 0) : 0;
        const target = Number.isFinite(rawValue) ? Math.max(0, rawValue) : 0;

        if (target <= 0) {
            node.textContent = '0';
            return;
        }

        let current = 0;
        const step = Math.max(1, Math.ceil(target / 16));
        const timer = window.setInterval(() => {
            current = Math.min(target, current + step);
            node.textContent = String(current);
            if (current >= target) {
                window.clearInterval(timer);
            }
        }, 26);
    });
}

function initRwSummarySkeleton() {
    const summary = document.querySelector('.rw-app-summary');
    if (!summary) return;
    summary.classList.add('is-loading');
    window.setTimeout(() => {
        summary.classList.remove('is-loading');
    }, 520);
}

function openStandaloneModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('rw-sheet-open');

    const sheet = modal.querySelector('.rw-form-sheet');
    const scrollWrap = modal.querySelector('.rw-sheet-scroll');
    const form = modal.querySelector('.rw-sheet-form');
    if (sheet) sheet.scrollTop = 0;
    if (scrollWrap) scrollWrap.scrollTop = 0;
    if (form) form.scrollTop = 0;
}

function closeStandaloneModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    const hasOpenModal = document.querySelector('.rw-form-modal:not(.hidden)');
    if (!hasOpenModal) {
        document.body.classList.remove('rw-sheet-open');
    }
}

function bindSheetDrag(modal) {
    if (!modal) return;
    const sheet = modal.querySelector('.rw-form-sheet');
    if (!sheet) return;

    let startY = 0;
    let deltaY = 0;
    let dragging = false;

    const onMove = (event) => {
        if (!dragging) return;
        const currentY = event.touches ? event.touches[0].clientY : event.clientY;
        deltaY = Math.max(0, currentY - startY);
        sheet.style.transform = `translateY(${deltaY}px)`;
    };

    const onEnd = () => {
        if (!dragging) return;
        dragging = false;
        sheet.style.transform = '';
        if (deltaY > 90) {
            closeStandaloneModal(modal.id);
        }
        deltaY = 0;
    };

    const onStart = (event) => {
        const origin = event.target.closest('.rw-form-handle, .rw-form-header');
        if (!origin) return;
        dragging = true;
        startY = event.touches ? event.touches[0].clientY : event.clientY;
        deltaY = 0;
    };

    sheet.addEventListener('touchstart', onStart, { passive: true });
    sheet.addEventListener('touchmove', onMove, { passive: true });
    sheet.addEventListener('touchend', onEnd);
    sheet.addEventListener('mousedown', onStart);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);
}

function bindStandaloneModals() {
    modalTriggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modalId = trigger.getAttribute('data-open-modal');
            if (!modalId) return;
            openStandaloneModal(modalId);
        });
    });

    modalClosers.forEach((closer) => {
        closer.addEventListener('click', () => {
            const modalId = closer.getAttribute('data-close-modal');
            if (!modalId) return;
            closeStandaloneModal(modalId);
        });
    });

    document.querySelectorAll('.rw-form-modal').forEach((modal) => {
        bindSheetDrag(modal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeStandaloneModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const activeModal = document.querySelector('.rw-form-modal:not(.hidden)');
        if (activeModal) {
            closeStandaloneModal(activeModal.id);
        }
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


function rwInitMiniCalendar() {
    const grid = document.getElementById('rwCalGrid');
    const monthEl = document.getElementById('rwCalMonth');
    const yearEl = document.getElementById('rwCalYear');
    const prevBtn = document.getElementById('rwCalPrev');
    const nextBtn = document.getElementById('rwCalNext');
    if (!grid || !monthEl || !yearEl) return;

    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const now = new Date();
    let viewYear = now.getFullYear();
    let viewMonth = now.getMonth(); // 0-indexed

    // Tenggat iuran = tanggal 10 setiap bulan
    const IURAN_DAY = 10;

    function renderCalendar() {
        monthEl.textContent = MONTHS_ID[viewMonth];
        yearEl.textContent = String(viewYear);
        grid.innerHTML = '';

        const firstDay = new Date(viewYear, viewMonth, 1).getDay(); // 0=Sun
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const todayDate = now.getDate();
        const isCurrentMonth = viewYear === now.getFullYear() && viewMonth === now.getMonth();

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'rw-cal-day empty';
            grid.appendChild(empty);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const cell = document.createElement('div');
            cell.className = 'rw-cal-day';
            cell.textContent = String(d);

            const dayOfWeek = new Date(viewYear, viewMonth, d).getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) cell.classList.add('is-weekend');
            if (d === IURAN_DAY) { cell.classList.add('is-iuran', 'has-event'); }
            if (isCurrentMonth && d === todayDate) { cell.classList.add('today'); }

            grid.appendChild(cell);
        }
    }

    renderCalendar();

    prevBtn && prevBtn.addEventListener('click', () => {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });

    nextBtn && nextBtn.addEventListener('click', () => {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });
}

function rwInitAgendaTabs() {
    const widget = document.getElementById('rwAgendaWidget');
    if (!widget) return;

    const tabBtns = Array.from(widget.querySelectorAll('.rw-agenda-tab-btn'));
    const panels = Array.from(widget.querySelectorAll('.rw-agenda-panel'));

    tabBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-agenda-tab');
            tabBtns.forEach((b) => {
                b.classList.toggle('active', b === btn);
                b.setAttribute('aria-selected', String(b === btn));
            });
            panels.forEach((p) => {
                p.classList.toggle('active', p.getAttribute('data-agenda-panel') === targetId);
            });
        });
    });
}

(function initRuangWargaStandalone() {
    initBootLoader();
    bindTabNavigation();
    bindStandaloneModals();
    bindDynamicRows();
    bindAvatarPreview();
    bindProfileWizard();
    bindStandaloneHaptic();
    bindRwHeaderInteractions();
    initRwSummarySkeleton();
    rwInitMiniCalendar();
    rwInitAgendaTabs();
    statusPernikahan?.addEventListener('change', togglePasangan);
    togglePasangan();

    const params = new URLSearchParams(window.location.search);
    const forcedTab = params.get('tab');
    if (forcedTab === 'aduan' || params.has('aduan_page')) {
        activateTab('aduan');
    } else {
        const savedTab = rwStandaloneReadTab();
        if (savedTab) {
            activateTab(savedTab);
        }
    }

    rwStandaloneStaggerReveal(document);
})();

