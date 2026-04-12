<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    $pdo->prepare("UPDATE pasar_penjual SET status = 'Aktif' WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
}