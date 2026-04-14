<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/csv; charset=utf-8');

$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : date('Y');
$filename = 'Rekonsiliasi_' . $tahun . '.csv';
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

$bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
fputcsv($output, array_merge(['Nama Warga', 'No Rumah', 'Blok'], $bulanLabel, ['Total Tunggakan', 'Estimasi Hutang']));

try {
    $stmtWarga = $pdo->prepare("SELECT w.id, w.nama_lengkap, w.nomor_rumah, w.blok_id, b.nama_blok
        FROM warga w
        JOIN blok b ON w.blok_id = b.id
        ORDER BY b.nama_blok ASC, w.nomor_rumah ASC");
    $stmtWarga->execute();
    $wargaList = $stmtWarga->fetchAll(PDO::FETCH_ASSOC);

    $stmtBayar = $pdo->prepare("SELECT warga_id, bulan
        FROM pembayaran_iuran
        WHERE tahun = ? AND status = 'LUNAS'");
    $stmtBayar->execute([$tahun]);
    $pembayaran = $stmtBayar->fetchAll(PDO::FETCH_ASSOC);

    $paidMap = [];
    foreach ($pembayaran as $row) {
        $paidMap[$row['warga_id']][(int) $row['bulan']] = true;
    }

    $stmtMaster = $pdo->query("SELECT blok_id, SUM(nominal) AS total FROM master_iuran GROUP BY blok_id");
    $masters = $stmtMaster->fetchAll(PDO::FETCH_ASSOC);
    $masterMap = [];
    $defaultMaster = 0;
    foreach ($masters as $row) {
        if ($row['blok_id'] === null) {
            $defaultMaster = (float) $row['total'];
        } else {
            $masterMap[(int) $row['blok_id']] = (float) $row['total'];
        }
    }

    $currentMonth = (int) date('n') - 1;
    $currentYear = (int) date('Y');

    foreach ($wargaList as $warga) {
        $row = [$warga['nama_lengkap'], $warga['nomor_rumah'], $warga['nama_blok']];
        $tunggakanCount = 0;

        for ($bulan = 0; $bulan < 12; $bulan++) {
            $isPaid = !empty($paidMap[$warga['id']][$bulan]);
            $isPastOrPresent = ($tahun < $currentYear) || ($tahun == $currentYear && $bulan <= $currentMonth);

            if ($isPaid) {
                $row[] = 'LUNAS';
            } elseif ($isPastOrPresent) {
                $row[] = 'MENUNGGAK';
                $tunggakanCount++;
            } else {
                $row[] = '';
            }
        }

        $nominalPerBulan = $masterMap[(int) $warga['blok_id']] ?? $defaultMaster;
        $row[] = $tunggakanCount;
        $row[] = $tunggakanCount * $nominalPerBulan;

        fputcsv($output, $row);
    }
} catch (Exception $e) {
    fputcsv($output, ['Error: ' . $e->getMessage()]);
}

fclose($output);