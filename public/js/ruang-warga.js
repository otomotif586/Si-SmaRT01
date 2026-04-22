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

const RW_TAB_STORAGE_KEY = 'rw.activeTab';
const RW_VALID_TABS = ['profil', 'history', 'pengaduan', 'informasi', 'pasar'];

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

function rwExtractNumber(value) {
    const onlyDigits = String(value ?? '').replace(/[^0-9.-]/g, '');
    const parsed = Number(onlyDigits);
    return Number.isFinite(parsed) ? parsed : 0;
}

function rwAnimateCounter(el, targetValue, options = {}) {
    if (!el) return;

    const {
        duration = 560,
        prefix = '',
        suffix = '',
        locale = 'id-ID'
    } = options;

    const target = Number(targetValue || 0);
    const start = rwExtractNumber(el.textContent);
    if (!Number.isFinite(target)) {
        el.textContent = `${prefix}${targetValue}${suffix}`;
        return;
    }

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.textContent = `${prefix}${Math.round(target).toLocaleString(locale)}${suffix}`;
        return;
    }

    const startTs = performance.now();
    const delta = target - start;

    const tick = (now) => {
        const elapsed = now - startTs;
        const progress = Math.min(1, elapsed / duration);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = start + (delta * eased);
        const rounded = Math.round(current);

        el.textContent = `${prefix}${rounded.toLocaleString(locale)}${suffix}`;

        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    };

    requestAnimationFrame(tick);
}

function rwSkeletonRows(count = 3) {
    return Array.from({ length: count }).map(() => `
        <div class="rw-skeleton-row">
            <span class="rw-skeleton-icon"></span>
            <span class="rw-skeleton-lines">
                <strong></strong>
                <small></small>
            </span>
            <span class="rw-skeleton-end"></span>
        </div>
    `).join('');
}

function rwSetLoading(isLoading) {
    const page = document.getElementById('page-ruang-warga');
    if (!page) return;

    page.classList.toggle('rw-loading', Boolean(isLoading));

    if (!isLoading) {
        rwStaggerReveal(page);
        return;
    }

    const linked = document.getElementById('rw-linked-warga');
    const history = document.getElementById('rw-history-list');
    const pengaduan = document.getElementById('rw-pengaduan-list');
    const update = document.getElementById('rw-update-list');
    const feed = document.getElementById('rw-info-feed');

    if (linked) linked.innerHTML = `<div class="rw-skeleton-row rw-skeleton-row-tight"><span class="rw-skeleton-icon"></span><span class="rw-skeleton-lines"><strong></strong><small></small></span></div>`;
    if (history) history.innerHTML = rwSkeletonRows(3);
    if (pengaduan) pengaduan.innerHTML = rwSkeletonRows(2);
    if (update) update.innerHTML = rwSkeletonRows(2);
    if (feed) feed.innerHTML = rwSkeletonRows(2);
}

function rwPulseHaptic(target) {
    if (!target) return;
    target.classList.remove('rw-haptic-hit');
    window.requestAnimationFrame(() => {
        target.classList.add('rw-haptic-hit');
        window.setTimeout(() => target.classList.remove('rw-haptic-hit'), 220);
    });
}

function rwCreateRipple(target, event) {
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

function rwStaggerReveal(page) {
    if (!page) return;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const nodes = page.querySelectorAll([
        '.rw-primary-card',
        '.rw-action-grid .rw-action-btn',
        '.rw-summary-grid .rw-stat',
        '.rw-tab-panel:not(.hidden) .rw-card',
        '.rw-tab-panel:not(.hidden) .rw-list-item',
        '.rw-tab-panel:not(.hidden) .rw-info-btn'
    ].join(', '));

    nodes.forEach((node, index) => {
        node.classList.remove('rw-reveal-ready');
        node.style.setProperty('--rw-reveal-delay', `${Math.min(index * 26, 360)}ms`);
    });

    window.requestAnimationFrame(() => {
        nodes.forEach((node) => node.classList.add('rw-reveal-ready'));
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function rwEncodeData(value) {
    return encodeURIComponent(String(value ?? ''));
}

function rwDecodeData(value) {
    try {
        return decodeURIComponent(String(value ?? ''));
    } catch (e) {
        return String(value ?? '');
    }
}

function rwInfoButton({ title, subtitle, detailHtml, icon, tone = 'blue' }) {
    const safeTitle = escapeHtml(title);
    const safeSubtitle = escapeHtml(subtitle || 'Tap untuk lihat detail');
    return `
        <button type="button" class="rw-info-btn rw-tone-${tone}" data-rw-popup-title="${rwEncodeData(title)}" data-rw-popup-body="${rwEncodeData(detailHtml)}">
            <span class="rw-info-leading"><i data-lucide="${icon}"></i></span>
            <span class="rw-info-main">
                <strong>${safeTitle}</strong>
                <small>${safeSubtitle}</small>
            </span>
            <span class="rw-info-trail"><i data-lucide="chevron-right"></i></span>
        </button>
    `;
}

function rwOpenInfoPopup(title, detailHtml) {
    const popup = document.getElementById('rw-info-popup');
    const titleEl = document.getElementById('rw-popup-title');
    const bodyEl = document.getElementById('rw-popup-content');
    if (!popup || !titleEl || !bodyEl) return;

    titleEl.textContent = title || 'Detail';
    bodyEl.innerHTML = detailHtml || '<p class="text-secondary">Tidak ada detail.</p>';
    popup.classList.remove('hidden');
    popup.setAttribute('aria-hidden', 'false');
}

function rwAttachStatPopup(valueId, title, subtitle, detailHtml) {
    const valueEl = document.getElementById(valueId);
    const card = valueEl?.closest('.rw-stat');
    if (!card) return;

    card.classList.add('rw-stat-tap');
    card.dataset.rwPopupTitle = rwEncodeData(title);
    card.dataset.rwPopupBody = rwEncodeData(`
        <div class="rw-popup-lines">
            <div class="rw-popup-line"><span>Ringkasan</span><strong>${escapeHtml(subtitle)}</strong></div>
            <div class="rw-popup-line"><span>Nilai</span><strong>${escapeHtml(valueEl.textContent || '-')}</strong></div>
        </div>
        <div class="rw-popup-text">${detailHtml}</div>
    `);
}

function rwCloseInfoPopup() {
    const popup = document.getElementById('rw-info-popup');
    if (!popup) return;
    popup.classList.add('hidden');
    popup.setAttribute('aria-hidden', 'true');
    const sheet = popup.querySelector('.rw-popup-sheet');
    if (sheet) {
        sheet.style.transform = '';
        sheet.style.transition = '';
    }
}

function rwOpenFormModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
}

function rwCloseFormModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    const sheet = modal.querySelector('.rw-form-sheet');
    if (sheet) {
        sheet.style.transform = '';
        sheet.style.transition = '';
    }
}

function rwCloseAllFormModals() {
    ['rw-profile-modal', 'rw-pengaduan-modal', 'rw-update-modal'].forEach((modalId) => {
        rwCloseFormModal(modalId);
    });
}

function rwGetInitials(name) {
    const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return 'RW';
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return `${parts[0][0] || ''}${parts[1][0] || ''}`.toUpperCase();
}

function rwGetTierInfo(progressPercent) {
    if (progressPercent >= 90) return { name: 'Diamond', key: 'diamond', icon: 'gem' };
    if (progressPercent >= 75) return { name: 'Platinum', key: 'platinum', icon: 'sparkles' };
    if (progressPercent >= 50) return { name: 'Gold', key: 'gold', icon: 'award' };
    if (progressPercent >= 25) return { name: 'Silver', key: 'silver', icon: 'shield-check' };
    return { name: 'Bronze', key: 'bronze', icon: 'shield' };
}

function rwBindSheetDrag(sheetEl, handleEl, onClose) {
    if (!sheetEl || !handleEl || typeof onClose !== 'function') return;

    let startY = 0;
    let currentY = 0;
    let dragging = false;
    const closeThreshold = 90;
    const springTransition = 'transform 0.34s cubic-bezier(0.22, 1.25, 0.36, 1)';
    const closeTransition = 'transform 0.18s ease-out';

    const onTouchStart = (event) => {
        if (!event.touches || event.touches.length !== 1) return;
        dragging = true;
        startY = event.touches[0].clientY;
        currentY = startY;
        sheetEl.style.transition = 'none';
    };

    const onTouchMove = (event) => {
        if (!dragging || !event.touches || event.touches.length !== 1) return;
        currentY = event.touches[0].clientY;
        const delta = Math.max(0, currentY - startY);
        sheetEl.style.transform = `translateY(${delta}px)`;
    };

    const onTouchEnd = () => {
        if (!dragging) return;
        dragging = false;

        const delta = Math.max(0, currentY - startY);

        if (delta > closeThreshold) {
            sheetEl.style.transition = closeTransition;
            sheetEl.style.transform = 'translateY(100%)';
            window.setTimeout(() => {
                onClose();
            }, 180);
            return;
        }

        sheetEl.style.transition = springTransition;
        sheetEl.style.transform = 'translateY(0)';
        window.setTimeout(() => {
            sheetEl.style.transition = '';
        }, 360);
    };

    handleEl.addEventListener('touchstart', onTouchStart, { passive: true });
    handleEl.addEventListener('touchmove', onTouchMove, { passive: true });
    handleEl.addEventListener('touchend', onTouchEnd);
    handleEl.addEventListener('touchcancel', onTouchEnd);
}

function rwBindHapticFeedback(page) {
    if (!page) return;

    const interactiveSelector = [
        '.rw-action-btn',
        '.rw-info-btn',
        '.rw-sheet-trigger',
        '.rw-bottom-nav-btn',
        '.rw-item-btn',
        '.rw-popup-close',
        '.rw-form-close',
        '.button-primary',
        '.button-secondary'
    ].join(', ');

    page.addEventListener('pointerdown', (event) => {
        const target = event.target.closest(interactiveSelector);
        if (target) {
            rwPulseHaptic(target);
            rwCreateRipple(target, event);
        }
    });
}

function rwSaveActiveTab(tabId) {
    if (!RW_VALID_TABS.includes(tabId)) return;
    try {
        window.localStorage.setItem(RW_TAB_STORAGE_KEY, tabId);
    } catch (e) {
        // Ignore storage errors silently (privacy mode, disabled storage)
    }
}

function rwReadActiveTab() {
    try {
        const saved = window.localStorage.getItem(RW_TAB_STORAGE_KEY) || '';
        return RW_VALID_TABS.includes(saved) ? saved : 'profil';
    } catch (e) {
        return 'profil';
    }
}

function rwRenderAvatar(data, displayName) {
    const avatarEl = document.getElementById('rw-profile-avatar');
    if (!avatarEl) return;

    const profile = data.profile || {};
    const linked = data.linkedWarga || {};
    const avatarUrl = profile.foto_url || profile.avatar_url || profile.photo_url || profile.foto || linked.foto_url || linked.avatar_url || linked.photo_url || '';

    const initials = rwGetInitials(displayName || 'Warga');
    avatarEl.classList.remove('rw-avatar-has-image');
    avatarEl.classList.add('rw-avatar-initials');
    avatarEl.innerHTML = `<span>${escapeHtml(initials)}</span>`;

    if (avatarUrl && String(avatarUrl).trim()) {
        const img = document.createElement('img');
        img.src = String(avatarUrl);
        img.alt = `Foto ${displayName || 'Warga'}`;
        img.loading = 'lazy';
        img.referrerPolicy = 'no-referrer';
        img.onerror = () => {
            avatarEl.classList.remove('rw-avatar-has-image');
            avatarEl.classList.add('rw-avatar-initials');
            avatarEl.innerHTML = `<span>${escapeHtml(initials)}</span>`;
        };

        avatarEl.innerHTML = '';
        avatarEl.appendChild(img);
        avatarEl.classList.remove('rw-avatar-initials');
        avatarEl.classList.add('rw-avatar-has-image');
    }
}

function renderRuangWarga() {
    const data = ruangWargaState;

    // === Greeting & Primary Card ===
    const greetingName = document.getElementById('rw-greeting-name');
    const primaryName = document.getElementById('rw-primary-name');
    const primaryBlok = document.getElementById('rw-primary-blok');
    const primaryStatus = document.getElementById('rw-primary-status');
    const primaryLunas = document.getElementById('rw-primary-lunas');
    const primaryTunggakan = document.getElementById('rw-primary-tunggakan');
    const primaryLevel = document.getElementById('rw-primary-level');
    const progressText = document.getElementById('rw-primary-progress-text');
    const progressFill = document.getElementById('rw-primary-progress-fill');

    const displayName = data.profile?.nama_lengkap || data.linkedWarga?.nama_lengkap || 'Warga';
    if (greetingName) greetingName.textContent = `Halo, ${displayName}`;
    if (primaryName) primaryName.textContent = displayName;
    rwRenderAvatar(data, displayName);

    if (data.linkedWarga) {
        const w = data.linkedWarga;
        const blokInfo = `${w.nama_blok || '-'} • ${w.nomor_rumah || '-'}`;
        if (primaryBlok) primaryBlok.textContent = blokInfo;
        if (primaryStatus) primaryStatus.textContent = w.status_kependudukan || '-';
    } else {
        if (primaryBlok) primaryBlok.textContent = '-';
        if (primaryStatus) primaryStatus.textContent = '-';
    }

    const totalLunas = Number(data.dashboard?.total_lunas_saya ?? 0);
    const totalTunggakan = Number(data.dashboard?.total_tunggakan_saya ?? 0);
    const totalPeriode = totalLunas + totalTunggakan;
    const progressPercent = totalPeriode > 0 ? Math.round((totalLunas / totalPeriode) * 100) : 0;
    const level = Math.max(1, Math.min(5, Math.floor(progressPercent / 20) + 1));
    const tier = rwGetTierInfo(progressPercent);

    if (primaryLunas) rwAnimateCounter(primaryLunas, totalLunas);
    if (primaryTunggakan) rwAnimateCounter(primaryTunggakan, totalTunggakan);
    if (primaryLevel) {
        primaryLevel.innerHTML = `<i data-lucide="${tier.icon}"></i><span>${tier.name} • Lv ${level}</span>`;
    }
    if (progressText) progressText.textContent = `${progressPercent}% progress`;
    if (progressFill) {
        progressFill.style.width = `${progressPercent}%`;
        progressFill.classList.remove('rw-tier-bronze', 'rw-tier-silver', 'rw-tier-gold', 'rw-tier-platinum', 'rw-tier-diamond');
        progressFill.classList.add(`rw-tier-${tier.key}`);
    }

    rwAnimateCounter(document.getElementById('rw-stat-global-warga'), Number(data.dashboard?.total_warga_global ?? 0));
    rwAnimateCounter(document.getElementById('rw-stat-global-blok'), Number(data.dashboard?.total_blok ?? 0));
    rwAnimateCounter(document.getElementById('rw-stat-lunas'), Number(data.dashboard?.total_lunas_saya ?? 0), { suffix: ' Bulan' });
    rwAnimateCounter(document.getElementById('rw-stat-tunggakan'), Number(data.dashboard?.total_tunggakan_saya ?? 0), { suffix: ' Bulan' });

    rwAttachStatPopup(
        'rw-stat-global-warga',
        'Data Warga Terdaftar',
        'Total warga yang tercatat di sistem RT',
        'Data ini menampilkan total warga terdaftar secara global.'
    );
    rwAttachStatPopup(
        'rw-stat-global-blok',
        'Total Blok',
        'Jumlah blok aktif di lingkungan RT',
        'Data ini digunakan untuk mengetahui cakupan area pengelolaan.'
    );
    rwAttachStatPopup(
        'rw-stat-lunas',
        'Iuran Lunas Saya',
        'Jumlah bulan yang sudah dibayar',
        'Riwayat ini dihitung dari data warga yang terhubung dengan akun Anda.'
    );
    rwAttachStatPopup(
        'rw-stat-tunggakan',
        'Tunggakan Saya',
        'Jumlah bulan yang belum dibayar',
        'Segera selesaikan tunggakan agar status iuran kembali normal.'
    );

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
            const w = data.linkedWarga;
            const detail = `
                <div class="rw-popup-lines">
                    <div class="rw-popup-line"><span>Nama</span><strong>${escapeHtml(w.nama_lengkap || '-')}</strong></div>
                    <div class="rw-popup-line"><span>Blok</span><strong>${escapeHtml(w.nama_blok || '-')}</strong></div>
                    <div class="rw-popup-line"><span>Rumah</span><strong>${escapeHtml(w.nomor_rumah || '-')}</strong></div>
                    <div class="rw-popup-line"><span>No WA</span><strong>${escapeHtml(w.no_wa || '-')}</strong></div>
                    <div class="rw-popup-line"><span>Status</span><strong>${escapeHtml(w.status_kependudukan || '-')}</strong></div>
                </div>
            `;
            linked.innerHTML = rwInfoButton({
                title: w.nama_lengkap || 'Data Warga',
                subtitle: `Blok ${w.nama_blok || '-'} • Rumah ${w.nomor_rumah || '-'}`,
                detailHtml: detail,
                icon: 'user-check',
                tone: 'green'
            });
        }
    }

    const historyList = document.getElementById('rw-history-list');
    if (historyList) {
        if (!data.history.length) {
            historyList.innerHTML = '<p class="text-secondary">Belum ada history pembayaran.</p>';
        } else {
            historyList.innerHTML = data.history.map((row) => {
                const total = `Rp ${(Number(row.total_tagihan || 0)).toLocaleString('id-ID')}`;
                const detail = `
                    <div class="rw-popup-lines">
                        <div class="rw-popup-line"><span>Tahun</span><strong>${escapeHtml(row.tahun || '-')}</strong></div>
                        <div class="rw-popup-line"><span>Bulan</span><strong>${escapeHtml(bulanNama(Number(row.bulan)))}</strong></div>
                        <div class="rw-popup-line"><span>Status</span><strong>${escapeHtml(row.status || '-')}</strong></div>
                        <div class="rw-popup-line"><span>Total</span><strong>${escapeHtml(total)}</strong></div>
                        <div class="rw-popup-line"><span>Tanggal Bayar</span><strong>${escapeHtml(formatDateTime(row.tanggal_bayar))}</strong></div>
                    </div>
                `;
                return rwInfoButton({
                    title: `${bulanNama(Number(row.bulan))} ${row.tahun || ''}`,
                    subtitle: `${row.status || '-'} • ${total}`,
                    detailHtml: detail,
                    icon: 'wallet',
                    tone: row.status === 'LUNAS' ? 'green' : 'orange'
                });
            }).join('');
        }
    }

    const pengaduanList = document.getElementById('rw-pengaduan-list');
    if (pengaduanList) {
        if (!data.pengaduan.length) {
            pengaduanList.innerHTML = '<p class="text-secondary">Belum ada pengaduan.</p>';
        } else {
            pengaduanList.innerHTML = data.pengaduan.map((row) => {
                const detail = `
                    <div class="rw-popup-lines">
                        <div class="rw-popup-line"><span>Status</span><strong>${escapeHtml(row.status || '-')}</strong></div>
                        <div class="rw-popup-line"><span>Dibuat</span><strong>${escapeHtml(formatDateTime(row.created_at))}</strong></div>
                    </div>
                    <div class="rw-popup-text">${escapeHtml(row.isi || '-').replace(/\n/g, '<br>')}</div>
                `;
                return `
                    <div class="rw-list-item">
                        ${rwInfoButton({
                            title: row.judul || 'Pengaduan',
                            subtitle: `${row.status || '-'} • ${formatDateTime(row.created_at)}`,
                            detailHtml: detail,
                            icon: 'message-square-warning',
                            tone: row.status === 'Selesai' ? 'green' : 'orange'
                        })}
                        <div class="rw-item-actions">
                            <button class="rw-item-btn" onclick="rwEditPengaduan(${row.id})">Edit</button>
                            <button class="rw-item-btn delete" onclick="rwDeletePengaduan(${row.id})">Hapus</button>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    const updateList = document.getElementById('rw-update-list');
    if (updateList) {
        if (!data.updates.length) {
            updateList.innerHTML = '<p class="text-secondary">Belum ada update informasi.</p>';
        } else {
            updateList.innerHTML = data.updates.map((row) => {
                const detail = `
                    <div class="rw-popup-lines">
                        <div class="rw-popup-line"><span>Dibuat</span><strong>${escapeHtml(formatDateTime(row.created_at))}</strong></div>
                    </div>
                    <div class="rw-popup-text">${escapeHtml(row.isi || '-').replace(/\n/g, '<br>')}</div>
                `;
                return `
                    <div class="rw-list-item">
                        ${rwInfoButton({
                            title: row.judul || 'Update',
                            subtitle: formatDateTime(row.created_at),
                            detailHtml: detail,
                            icon: 'newspaper',
                            tone: 'blue'
                        })}
                        <div class="rw-item-actions">
                            <button class="rw-item-btn" onclick="rwEditUpdate(${row.id})">Edit</button>
                            <button class="rw-item-btn delete" onclick="rwDeleteUpdate(${row.id})">Hapus</button>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    const feed = document.getElementById('rw-info-feed');
    if (feed) {
        if (!data.infoFeed.length) {
            feed.innerHTML = '<p class="text-secondary">Belum ada informasi terbaru.</p>';
        } else {
            feed.innerHTML = data.infoFeed.map((row) => {
                const detail = `
                    <div class="rw-popup-lines">
                        <div class="rw-popup-line"><span>Waktu</span><strong>${escapeHtml(formatDateTime(row.waktu))}</strong></div>
                    </div>
                    <div class="rw-popup-text">${escapeHtml(row.ringkas || '-').replace(/\n/g, '<br>')}</div>
                `;
                return rwInfoButton({
                    title: row.judul || 'Informasi',
                    subtitle: formatDateTime(row.waktu),
                    detailHtml: detail,
                    icon: 'bell-ring',
                    tone: 'indigo'
                });
            }).join('');
        }
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

async function loadRuangWargaData() {
    rwSetLoading(true);
    try {
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
    } finally {
        rwSetLoading(false);
    }
}

function rwSwitchTab(tabId) {
    if (!RW_VALID_TABS.includes(tabId)) {
        tabId = 'profil';
    }

    document.querySelectorAll('#page-ruang-warga .rw-tab-btn').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.rwTab === tabId);
    });

    document.querySelectorAll('#page-ruang-warga .rw-tab-panel').forEach((panel) => {
        panel.classList.add('hidden');
    });

    const panel = document.getElementById(`rw-tab-${tabId}`);
    if (panel) panel.classList.remove('hidden');

    rwSaveActiveTab(tabId);
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
    rwCloseFormModal('rw-profile-modal');
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
    rwCloseFormModal('rw-pengaduan-modal');
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
    rwCloseFormModal('rw-update-modal');
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
    rwOpenFormModal('rw-pengaduan-modal');
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
    rwOpenFormModal('rw-update-modal');
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
    const page = document.getElementById('page-ruang-warga');

    rwBindHapticFeedback(page);

    document.querySelectorAll('#page-ruang-warga .rw-tab-btn').forEach((btn) => {
        btn.addEventListener('click', () => rwSwitchTab(btn.dataset.rwTab));
    });

    page?.addEventListener('click', (event) => {
        const infoBtn = event.target.closest('.rw-info-btn, .rw-stat-tap');
        if (infoBtn) {
            const title = rwDecodeData(infoBtn.dataset.rwPopupTitle || 'Detail');
            const body = rwDecodeData(infoBtn.dataset.rwPopupBody || '');
            rwOpenInfoPopup(title, body);
        }
    });

    // Action buttons trigger tab switching
    page?.querySelectorAll('.rw-action-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const tabName = btn.dataset.rwTab;
            if (tabName) {
                rwSwitchTab(tabName);
            }
        });
    });

    document.getElementById('rw-open-profile-form')?.addEventListener('click', () => {
        rwOpenFormModal('rw-profile-modal');
    });

    document.getElementById('rw-open-pengaduan-form')?.addEventListener('click', () => {
        rwResetPengaduanForm();
        rwOpenFormModal('rw-pengaduan-modal');
    });

    document.getElementById('rw-open-update-form')?.addEventListener('click', () => {
        rwResetUpdateForm();
        rwOpenFormModal('rw-update-modal');
    });

    page?.querySelectorAll('.rw-form-close').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modalId = btn.getAttribute('data-rw-close-modal');
            if (modalId) rwCloseFormModal(modalId);
        });
    });

    page?.querySelectorAll('.rw-form-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                rwCloseFormModal(modal.id);
            }
        });
    });

    rwBindSheetDrag(
        document.querySelector('#rw-info-popup .rw-popup-sheet'),
        document.querySelector('#rw-info-popup .rw-popup-handle'),
        rwCloseInfoPopup
    );

    rwBindSheetDrag(
        document.querySelector('#rw-profile-modal .rw-form-sheet'),
        document.querySelector('#rw-profile-modal .rw-form-handle'),
        () => rwCloseFormModal('rw-profile-modal')
    );

    rwBindSheetDrag(
        document.querySelector('#rw-pengaduan-modal .rw-form-sheet'),
        document.querySelector('#rw-pengaduan-modal .rw-form-handle'),
        () => rwCloseFormModal('rw-pengaduan-modal')
    );

    rwBindSheetDrag(
        document.querySelector('#rw-update-modal .rw-form-sheet'),
        document.querySelector('#rw-update-modal .rw-form-handle'),
        () => rwCloseFormModal('rw-update-modal')
    );

    document.getElementById('rw-popup-close')?.addEventListener('click', rwCloseInfoPopup);
    document.getElementById('rw-info-popup')?.addEventListener('click', (event) => {
        if (event.target.id === 'rw-info-popup') {
            rwCloseInfoPopup();
        }
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            rwCloseInfoPopup();
            rwCloseAllFormModals();
        }
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
        rwSwitchTab(rwReadActiveTab());
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
        await loadRuangWargaData();
    } catch (err) {
        showToast(err.message || 'Gagal memuat Ruang Warga', 'error');
    }
};
