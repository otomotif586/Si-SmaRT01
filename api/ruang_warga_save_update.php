<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$id = (int)($_POST['id'] ?? 0);
$judul = trim($_POST['judul'] ?? '');
$isi = trim($_POST['isi'] ?? '');

if ($judul === '' || $isi === '') {
    echo json_encode(['status' => 'error', 'message' => 'Judul dan isi update wajib diisi']);
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS warga_update_informasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        judul VARCHAR(255) NOT NULL,
        isi TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE warga_update_informasi SET judul = ?, isi = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$judul, $isi, $id, $userId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO warga_update_informasi (user_id, judul, isi) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $judul, $isi]);
    }

    echo json_encode(['status' => 'success']);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
