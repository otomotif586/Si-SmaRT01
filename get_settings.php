<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = [];
    foreach($rows as $row) {
        $data[$row['setting_key']] = $row['setting_value'];
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }