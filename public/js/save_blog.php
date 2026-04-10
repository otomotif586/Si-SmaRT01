<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul'] ?? ''; $konten = $_POST['konten'] ?? ''; $status = $_POST['status'] ?? 'Publish';
    
    if ($id > 0) {
        $pdo->prepare("UPDATE web_blogs SET judul=?, konten=?, status=? WHERE id=?")->execute([$judul, $konten, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Artikel diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO web_blogs (judul, konten, status) VALUES (?, ?, ?)")->execute([$judul, $konten, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diterbitkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }