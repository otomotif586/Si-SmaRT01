<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul'] ?? ''; $waktu = $_POST['waktu_kejadian'] ?? ''; $lokasi = $_POST['lokasi'] ?? ''; $deskripsi = $_POST['deskripsi'] ?? ''; $status = $_POST['status'] ?? 'Baru';
    if ($id > 0) {
        $pdo->prepare("UPDATE km_laporan SET judul=?, waktu_kejadian=?, lokasi=?, deskripsi=?, status=? WHERE id=?")->execute([$judul, $waktu, $lokasi, $deskripsi, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Laporan diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO km_laporan (judul, waktu_kejadian, lokasi, deskripsi, status) VALUES (?, ?, ?, ?, ?)")->execute([$judul, $waktu, $lokasi, $deskripsi, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Laporan ditambahkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }