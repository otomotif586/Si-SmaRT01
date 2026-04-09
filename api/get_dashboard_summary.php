<?php
require_once '../config/database.php';
header('Content-Type: application/json');
$blok_id = $_GET['blok_id'] ?? 0;

try {
    // 1. Kas Blok (Total Kas if blok_id=0)
    if ($blok_id == 0) {
        $stmtKas = $pdo->prepare("SELECT SUM(kas_blok) FROM blok");
        $stmtKas->execute();
    } else {
        $stmtKas = $pdo->prepare("SELECT kas_blok FROM blok WHERE id = ?");
        $stmtKas->execute([$blok_id]);
    }
    $kas_blok = $stmtKas->fetchColumn() ?: 0;

    // 2. Demografi Warga
    if ($blok_id == 0) {
        $stmtWarga = $pdo->prepare("SELECT status_kependudukan, COUNT(*) as count FROM warga GROUP BY status_kependudukan");
        $stmtWarga->execute();
    } else {
        $stmtWarga = $pdo->prepare("SELECT status_kependudukan, COUNT(*) as count FROM warga WHERE blok_id = ? GROUP BY status_kependudukan");
        $stmtWarga->execute([$blok_id]);
    }
    $demografi = $stmtWarga->fetchAll(PDO::FETCH_ASSOC);
    
    $total_warga = 0; $demo_data = ['Tetap' => 0, 'Kontrak' => 0, 'Weekend' => 0];
    foreach ($demografi as $row) {
        $stat = $row['status_kependudukan'] ?: 'Lainnya';
        if (isset($demo_data[$stat])) $demo_data[$stat] += $row['count'];
        else $demo_data[$stat] = $row['count'];
        $total_warga += $row['count'];
    }

    // 3. Laporan Aktif
    if ($blok_id == 0) {
        $stmtLap = $pdo->prepare("SELECT COUNT(*) FROM laporan_masalah WHERE status != 'Selesai'");
        $stmtLap->execute();
    } else {
        $stmtLap = $pdo->prepare("SELECT COUNT(*) FROM laporan_masalah WHERE blok_id = ? AND status != 'Selesai'");
        $stmtLap->execute([$blok_id]);
    }
    $laporan_aktif = $stmtLap->fetchColumn() ?: 0;

    // 4. Agenda Terdekat
    if ($blok_id == 0) {
        $stmtAg = $pdo->prepare("SELECT judul FROM agenda_kegiatan WHERE tanggal_kegiatan >= NOW() AND status != 'Dibatalkan' ORDER BY tanggal_kegiatan ASC LIMIT 1");
        $stmtAg->execute();
    } else {
        $stmtAg = $pdo->prepare("SELECT judul FROM agenda_kegiatan WHERE blok_id = ? AND tanggal_kegiatan >= NOW() AND status != 'Dibatalkan' ORDER BY tanggal_kegiatan ASC LIMIT 1");
        $stmtAg->execute([$blok_id]);
    }
    $agenda_terdekat = $stmtAg->fetchColumn() ?: 'Tidak ada agenda';

    // 5. Histori Pemasukan 6 Bulan Terakhir
    $chart_labels = []; $chart_data = [];
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    if ($blok_id == 0) {
        $stmtIuran = $pdo->prepare("SELECT SUM(total_tagihan) FROM pembayaran_iuran WHERE bulan = ? AND tahun = ? AND status = 'LUNAS'");
    } else {
        $stmtIuran = $pdo->prepare("SELECT SUM(total_tagihan) FROM pembayaran_iuran p JOIN warga w ON p.warga_id = w.id WHERE w.blok_id = ? AND p.bulan = ? AND p.tahun = ? AND p.status = 'LUNAS'");
    }
    
    for ($i = 5; $i >= 0; $i--) {
        $m = (int)date('n', strtotime("-$i months")) - 1;
        $y = (int)date('Y', strtotime("-$i months"));
        $chart_labels[] = $months[$m];
        
        if ($blok_id == 0) {
            $stmtIuran->execute([$m, $y]);
        } else {
            $stmtIuran->execute([$blok_id, $m, $y]);
        }
        $chart_data[] = $stmtIuran->fetchColumn() ?: 0;
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'kas_blok' => $kas_blok, 'total_warga' => $total_warga,
            'laporan_aktif' => $laporan_aktif, 'agenda_terdekat' => $agenda_terdekat,
            'demografi' => $demo_data, 'iuran_labels' => $chart_labels, 'iuran_data' => $chart_data
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}