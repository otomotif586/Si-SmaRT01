<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DB CONNECT: OK\n";

$tables = [
    'blok',
    'warga',
    'laporan_masalah',
    'agenda_kegiatan',
    'pembayaran_iuran',
    'jurnal_keuangan',
    'web_users'
];

foreach ($tables as $t) {
    try {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo "TABLE {$t}: OK ({$count} rows)\n";
    } catch (Throwable $e) {
        echo "TABLE {$t}: ERR ({$e->getMessage()})\n";
    }
}

echo "\nAPI get_dashboard_summary.php test:\n";
try {
    $_GET['blok_id'] = 0;
    ob_start();
    include __DIR__ . '/../api/get_dashboard_summary.php';
    $out = ob_get_clean();
    echo trim($out) . "\n";
} catch (Throwable $e) {
    echo 'API ERR: ' . $e->getMessage() . "\n";
}
