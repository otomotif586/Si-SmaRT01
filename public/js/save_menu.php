<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $nama = $_POST['nama_menu'] ?? ''; $url = $_POST['url'] ?? ''; $urutan = (int)($_POST['urutan'] ?? 1); $status = $_POST['status'] ?? 'Aktif';
    
    if ($id > 0) {
        $pdo->prepare("UPDATE web_menus SET nama_menu=?, url=?, urutan=?, status=? WHERE id=?")->execute([$nama, $url, $urutan, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Menu diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO web_menus (nama_menu, url, urutan, status) VALUES (?, ?, ?, ?)")->execute([$nama, $url, $urutan, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Menu ditambahkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }