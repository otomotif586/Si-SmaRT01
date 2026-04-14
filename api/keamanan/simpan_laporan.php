<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul'] ?? '';
    $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $status = $_POST['status'] ?? 'Baru';

    if (empty($judul) || empty($waktu_kejadian)) {
        echo json_encode(['status' => 'error', 'message' => 'Judul dan Waktu wajib diisi!']);
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE laporan_keamanan SET judul=?, waktu_kejadian=?, lokasi=?, deskripsi=?, status=? WHERE id=?");
        $stmt->execute([$judul, $waktu_kejadian, $lokasi, $deskripsi, $status, $id]);
        $msg = "Laporan berhasil diperbarui.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO laporan_keamanan (judul, waktu_kejadian, lokasi, deskripsi, status, pelapor, kategori) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$judul, $waktu_kejadian, $lokasi, $deskripsi, $status, 'Admin / Sistem', 'Keamanan']);
        $msg = "Laporan kejadian baru berhasil ditambahkan.";
    }

    echo json_encode(['status' => 'success', 'message' => $msg]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}