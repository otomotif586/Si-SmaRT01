<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    if ($id > 0) {
        $stmtFile = $pdo->prepare("SELECT lampiran_path FROM laporan_keamanan WHERE id = ? LIMIT 1");
        $stmtFile->execute([$id]);
        $lampiranPath = (string)($stmtFile->fetchColumn() ?: '');

        if ($lampiranPath !== '') {
            $abs = dirname(__DIR__, 2) . '/' . ltrim($lampiranPath, '/');
            if (is_file($abs)) {
                @unlink($abs);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM laporan_keamanan WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Laporan berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}