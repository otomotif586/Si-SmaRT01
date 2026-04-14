<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM laporan_keamanan WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Laporan berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}