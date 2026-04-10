<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $satpam_id = $_POST['satpam_id'] ?? 0; $tanggal = $_POST['tanggal'] ?? ''; $shift = $_POST['shift'] ?? 'Pagi';
    if ($id > 0) {
        $pdo->prepare("UPDATE km_jadwal SET satpam_id=?, tanggal=?, shift=? WHERE id=?")->execute([$satpam_id, $tanggal, $shift, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Jadwal diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO km_jadwal (satpam_id, tanggal, shift) VALUES (?, ?, ?)")->execute([$satpam_id, $tanggal, $shift]);
        echo json_encode(['status' => 'success', 'message' => 'Jadwal ditambahkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }