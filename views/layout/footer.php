<<<<<<< HEAD
<!-- Modular JS (split by function, load order matters) -->
<script src="<?= htmlspecialchars(smart_asset('public/js/core.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/file-source-picker.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/dashboard.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/workspace.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/warga.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/ruang-warga.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/global-warga.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keuangan.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/agenda.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/gallery.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/laporan-iuran.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/rekonsiliasi.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keuangan-global.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/detail-keuangan.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/pos-keuangan.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/pembukuan.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keamanan.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/info.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/smart-installer-theme.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
=======
<?php require_once __DIR__ . '/../../config/asset_url.php'; $v = smart_asset_version(); ?>
<script src="<?= htmlspecialchars(smart_asset('public/js/core.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/dashboard.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/workspace.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/warga.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/ruang-warga.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/global-warga.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keuangan.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/agenda.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/gallery.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/laporan-iuran.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/rekonsiliasi.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keuangan-global.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/detail-keuangan.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/pos-keuangan.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/pembukuan.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/keamanan.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/info.js', $v), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        if (typeof window.showPage === 'function') {
            window.showPage('dashboard');
        }
        if (typeof window.initDashboard === 'function') {
            window.initDashboard();
        }
        if (typeof window.safeCreateIcons === 'function') {
            window.safeCreateIcons();
        }
    } catch (error) {
        console.error('[Si-SmaRT] App bootstrap error:', error);
    }
});
</script>
>>>>>>> 2cfa5c83922f435094998b0ddbe53f3f01287bcf
