<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT id, judul, konten, status, DATE_FORMAT(created_at, '%d %b %Y') as created_at FROM web_blogs ORDER BY created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }