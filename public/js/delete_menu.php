<?php
// API Info - Hapus Data Menu
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $pdo->prepare("DELETE FROM web_menus WHERE id=?")->execute([$_POST['id']]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }