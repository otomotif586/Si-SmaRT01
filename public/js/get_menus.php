<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT * FROM web_menus ORDER BY urutan ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }