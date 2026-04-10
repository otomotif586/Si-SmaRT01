<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $satpam_aktif = $pdo->query("SELECT COUNT(*) FROM km_satpam WHERE status = 'Aktif'")->fetchColumn();
    $laporan_baru = $pdo->query("SELECT COUNT(*) FROM km_laporan WHERE status = 'Baru'")->fetchColumn();
    
    $stmtAktivitas = $pdo->query("
        SELECT judul, DATE_FORMAT(waktu_kejadian, '%d-%m-%Y %H:%i') as waktu 
        FROM km_laporan 
        ORDER BY waktu_kejadian DESC LIMIT 5
    ");
    $aktifitas = $stmtAktivitas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success', 
        'data' => ['satpam_aktif' => $satpam_aktif, 'laporan_baru' => $laporan_baru, 'aktifitas' => $aktifitas]
    ]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }