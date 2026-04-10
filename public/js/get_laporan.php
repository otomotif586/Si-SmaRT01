<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT id, judul, DATE_FORMAT(waktu_kejadian, '%d %b %Y %H:%i') as waktu_kejadian, lokasi, deskripsi, pelapor, status FROM km_laporan ORDER BY waktu_kejadian DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }