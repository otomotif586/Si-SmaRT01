<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT j.*, s.nama as nama_satpam FROM km_jadwal j JOIN km_satpam s ON j.satpam_id = s.id ORDER BY j.tanggal DESC, j.shift ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }