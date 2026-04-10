<?php
require_once '../config/database.php';
header('Content-Type: application/json');
try {
    // Auto-create tabel jika belum ada untuk memudahkan instalasi
    $pdo->exec("CREATE TABLE IF NOT EXISTS `web_pengurus` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nama` varchar(100) NOT NULL,
      `jabatan` varchar(100) NOT NULL,
      `foto` varchar(255) DEFAULT NULL,
      `urutan` int(11) DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $pdo->query("SELECT * FROM web_pengurus ORDER BY urutan ASC, id ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }