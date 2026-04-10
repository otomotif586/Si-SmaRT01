<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $satpam_id = $_POST['satpam_id'] ?? 0; $tgl_mulai = $_POST['tanggal_mulai'] ?? ''; $tgl_selesai = $_POST['tanggal_selesai'] ?? ''; $jenis = $_POST['jenis'] ?? ''; $ket = $_POST['keterangan'] ?? ''; $status = $_POST['status'] ?? 'Pending';
    if ($id > 0) {
        $pdo->prepare("UPDATE km_izin SET satpam_id=?, tanggal_mulai=?, tanggal_selesai=?, jenis=?, keterangan=?, status=? WHERE id=?")->execute([$satpam_id, $tgl_mulai, $tgl_selesai, $jenis, $ket, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Izin diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO km_izin (satpam_id, tanggal_mulai, tanggal_selesai, jenis, keterangan, status) VALUES (?, ?, ?, ?, ?, ?)")->execute([$satpam_id, $tgl_mulai, $tgl_selesai, $jenis, $ket, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Izin diajukan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }