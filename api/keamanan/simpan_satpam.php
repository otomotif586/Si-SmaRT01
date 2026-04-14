<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $nama = $_POST['nama'] ?? ''; $no_hp = $_POST['no_hp'] ?? ''; $status = $_POST['status'] ?? 'Aktif';
    if ($id > 0) {
        $pdo->prepare("UPDATE km_satpam SET nama=?, no_hp=?, status=? WHERE id=?")->execute([$nama, $no_hp, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Data diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO km_satpam (nama, no_hp, status) VALUES (?, ?, ?)")->execute([$nama, $no_hp, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Personel ditambahkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }