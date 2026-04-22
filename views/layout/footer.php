<script>
(() => {
	let appBooted = false;
	let loaderRunning = false;
	const debugState = {
		items: {
			boot: 'loading',
			scripts: 'loading',
			page: '-',
			bootstrap: '-',
			api: '-',
			errors: '0'
		}
	};

	function debugSet(key, value) {
		debugState.items[key] = value;
		const el = document.getElementById('app-debug-' + key);
		if (el) el.innerText = value;
	}

	function createDebugPanel() {
		if (document.getElementById('app-debug-panel')) return;
		const panel = document.createElement('div');
		panel.id = 'app-debug-panel';
		panel.setAttribute('aria-live', 'polite');
		panel.style.cssText = [
			'position:fixed',
			'right:12px',
			'bottom:12px',
			'z-index:2147483647',
			'width:min(360px, calc(100vw - 24px))',
			'max-height:min(45vh, 420px)',
			'overflow:auto',
			'padding:12px 14px',
			'border-radius:16px',
			'background:rgba(15,23,42,0.92)',
			'color:#e2e8f0',
			'font:12px/1.45 monospace',
			'box-shadow:0 18px 50px rgba(0,0,0,0.35)',
			'border:1px solid rgba(255,255,255,0.12)',
			'backdrop-filter:blur(14px)'
		].join(';');
		panel.innerHTML = `
			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;gap:10px;">
				<strong style="font-size:13px;color:#f8fafc;">Si-SmaRT Debug</strong>
				<span style="padding:2px 8px;border-radius:999px;background:rgba(16,185,129,0.16);color:#86efac;" id="app-debug-status">booting</span>
			</div>
			<div style="display:grid;grid-template-columns:1fr auto;gap:6px 10px;align-items:center;">
				<span>boot</span><span id="app-debug-boot">${debugState.items.boot}</span>
				<span>scripts</span><span id="app-debug-scripts">${debugState.items.scripts}</span>
				<span>page</span><span id="app-debug-page">${debugState.items.page}</span>
				<span>bootstrap</span><span id="app-debug-bootstrap">${debugState.items.bootstrap}</span>
				<span>api</span><span id="app-debug-api">${debugState.items.api}</span>
				<span>errors</span><span id="app-debug-errors">${debugState.items.errors}</span>
			</div>
			<div id="app-debug-log" style="margin-top:10px;color:#93c5fd;white-space:pre-wrap;"></div>
		`;
		document.body.appendChild(panel);
		debugSet('boot', 'panel-ready');
	}

	function debugLog(message) {
		const log = document.getElementById('app-debug-log');
		if (!log) return;
		log.innerText = String(message);
	}

	const scriptQueue = [
		<?= json_encode(smart_asset('public/js/core.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/dashboard.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/workspace.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/warga.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/ruang-warga.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/global-warga.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/keuangan.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/agenda.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/gallery.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/laporan-iuran.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/rekonsiliasi.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/keuangan-global.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/detail-keuangan.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/pos-keuangan.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/pembukuan.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/keamanan.js', (string)time())) ?>,
		<?= json_encode(smart_asset('public/js/info.js', (string)time())) ?>
	];

	function loadScript(src) {
		return new Promise((resolve, reject) => {
			const script = document.createElement('script');
			script.src = src;
			script.async = false;
			script.defer = false;
			script.onload = () => resolve(src);
			script.onerror = () => reject(new Error('Failed to load ' + src));
			document.body.appendChild(script);
		});
	}

	async function loadScriptQueue(startIndex, endIndex, label) {
		for (let index = startIndex; index < endIndex; index++) {
			const src = scriptQueue[index];
			debugSet('scripts', (src || '').split('/').pop() || label);
			try {
				await loadScript(src);
			} catch (error) {
				debugSet('bootstrap', 'loader-error');
				debugSet('api', 'n/a');
				debugLog(error.message || String(error));
				console.error('[Si-SmaRT] script load failed:', src, error);
				return false;
			}
		}
		return true;
	}

	window.__appDebug = {
		set: debugSet,
		log: debugLog
	};

	window.addEventListener('error', (event) => {
		const currentErrors = Number(debugState.items.errors || '0') + 1;
		debugSet('errors', String(currentErrors));
		debugSet('bootstrap', 'error');
		document.body.setAttribute('data-app-error', '1');
		debugLog(event.message || 'runtime error');
	});

	window.addEventListener('unhandledrejection', (event) => {
		const currentErrors = Number(debugState.items.errors || '0') + 1;
		debugSet('errors', String(currentErrors));
		debugSet('bootstrap', 'rejection');
		document.body.setAttribute('data-app-error', '1');
		debugLog(event.reason && event.reason.message ? event.reason.message : String(event.reason || 'unhandled rejection'));
	});

	function setText(id, value) {
		const el = document.getElementById(id);
		if (el) el.innerText = value;
	}

	function ensureVisibleDashboard() {
		const dashboard = document.getElementById('page-dashboard');
		if (!dashboard) return;

		document.querySelectorAll('.page-content').forEach((p) => {
			p.classList.add('hidden');
			p.style.display = 'none';
		});

		dashboard.classList.remove('hidden');
		dashboard.style.display = 'block';
	}

	function bootstrapDashboardData() {
		debugSet('bootstrap', 'fetching');
		fetch('api/get_dashboard_summary.php?blok_id=0', { credentials: 'same-origin', cache: 'no-store' })
			.then((r) => r.json())
			.then((res) => {
				if (!res || res.status !== 'success' || !res.data) return;
				const data = res.data;
				debugSet('api', 'ok');
				setText('dash-saldo', 'Rp ' + Number(data.kas_blok || 0).toLocaleString('id-ID'));
				setText('dash-iuran-percent', data.total_warga ? '100%' : '0%');
				setText('dash-iuran-detail', `${data.total_warga || 0}/${data.total_warga || 0} Warga`);
				setText('dash-iuran-progress', '');
				setText('dash-laporan-count', data.laporan_aktif || 0);
				setText('dash-agenda-text', data.agenda_terdekat || 'Tidak ada agenda terdekat');
				setText('page-title', 'Beranda');
				setText('page-subtitle', 'Ringkasan data warga');
				debugSet('bootstrap', 'dashboard-ok');
				debugLog('dashboard loaded: ' + JSON.stringify({ kas: data.kas_blok, warga: data.total_warga, laporan: data.laporan_aktif }));
				if (typeof window.safeCreateIcons === 'function') window.safeCreateIcons();
			})
			.catch((e) => {
				debugSet('api', 'error');
				debugSet('bootstrap', 'failed');
				debugLog(e && e.message ? e.message : String(e));
				console.error('[Si-SmaRT] bootstrap dashboard failed:', e);
			});
	}

	function runPageInitializers(pageId) {
		try {
			if (pageId === 'dashboard' && typeof window.initDashboard === 'function') window.initDashboard();
			if (pageId === 'global-warga' && typeof window.loadGlobalWarga === 'function') window.loadGlobalWarga();
			if (pageId === 'keuangan' && typeof window.initKeuanganGlobal === 'function') window.initKeuanganGlobal();
			if (pageId === 'warga' && typeof window.loadAllBloks === 'function') window.loadAllBloks();
			if (pageId === 'pasar') {
				if (typeof window.initPasarPage === 'function') window.initPasarPage();
				if (typeof window.initPasar === 'function') window.initPasar();
			}
		} catch (e) {
			console.error('[Si-SmaRT] emergency initializer failed:', pageId, e);
		}
	}

	function emergencyShowPage(pageId) {
		const pages = document.querySelectorAll('.page-content');
		pages.forEach((p) => {
			p.classList.add('hidden');
			p.style.display = 'none';
		});

		const target = document.getElementById('page-' + pageId) || document.getElementById('page-dashboard') || pages[0];
		if (!target) return;

		target.classList.remove('hidden');
		target.style.display = 'block';

		const resolved = target.id.replace(/^page-/, '');
		document.querySelectorAll('#sidebar .sidebar-nav button').forEach((b) => b.classList.remove('active-tab'));
		const nav = document.getElementById('nav-' + resolved);
		if (nav) nav.classList.add('active-tab');

		if (typeof window.safeCreateIcons === 'function') window.safeCreateIcons();
		runPageInitializers(resolved);
	}

	function bindEmergencyNav() {
		document.querySelectorAll('#sidebar .sidebar-nav button[id^="nav-"]').forEach((btn) => {
			if (btn.dataset.emergencyBound === '1') return;
			btn.dataset.emergencyBound = '1';

			btn.addEventListener('click', () => {
				const id = btn.id || '';
				if (id.startsWith('nav-group-')) {
					const submenuId = 'submenu-' + id.replace('nav-group-', '');
					if (typeof window.toggleSubmenu === 'function') {
						window.toggleSubmenu(submenuId);
					} else {
						const submenu = document.getElementById(submenuId);
						if (submenu) submenu.classList.toggle('hidden');
					}
					return;
				}
				const pageId = id.replace(/^nav-/, '');
				if (!pageId) return;
				if (typeof window.showPage === 'function') {
					try {
						window.showPage(pageId);
						return;
					} catch (e) {
						console.error('[Si-SmaRT] showPage fallback:', e);
					}
				}
				emergencyShowPage(pageId);
			});
		});
	}

	function start() {
		if (appBooted) return;
		appBooted = true;

		createDebugPanel();
		debugSet('scripts', 'start');
		document.getElementById('app-debug-status')?.innerText = 'booting';
		bindEmergencyNav();

		const visiblePage = Array.from(document.querySelectorAll('.page-content')).find((el) => !el.classList.contains('hidden') && el.style.display !== 'none');
		const fallbackPage = visiblePage ? visiblePage.id.replace(/^page-/, '') : 'dashboard';

		if (typeof window.showPage === 'function') {
			try {
				window.showPage(fallbackPage);
				debugSet('page', fallbackPage);
			} catch (e) {
				debugSet('page', 'fallback');
				emergencyShowPage(fallbackPage);
			}
		} else {
			debugSet('page', 'fallback');
			emergencyShowPage(fallbackPage);
		}

		ensureVisibleDashboard();
		bootstrapDashboardData();
		debugSet('boot', 'done');
		document.getElementById('app-debug-status')?.innerText = 'running';

		setTimeout(bindEmergencyNav, 500);
		setTimeout(() => {
			ensureVisibleDashboard();
			bootstrapDashboardData();
			debugSet('scripts', 'post-check');
		}, 1500);
	}

	async function bootShell() {
		if (loaderRunning) return;
		loaderRunning = true;

		createDebugPanel();
		debugSet('boot', 'panel-ready');
		debugSet('scripts', 'loading');
		document.getElementById('app-debug-status')?.innerText = 'booting';

		const coreReady = await loadScriptQueue(0, 2, 'core');
		if (!coreReady) return;

		start();
		loadScriptQueue(2, scriptQueue.length, 'modules');
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootShell, { once: true });
	} else {
		bootShell();
	}
})();
</script>
