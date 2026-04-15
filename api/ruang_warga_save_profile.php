<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$nama = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($nama === '' || $username === '') {
    echo json_encode(['status' => 'error', 'message' => 'Nama lengkap dan username wajib diisi']);
    exit;
}

try {
    $stmtCheck = $pdo->prepare("SELECT id FROM web_users WHERE username = ? AND id != ? LIMIT 1");
    $stmtCheck->execute([$username, $userId]);
    if ($stmtCheck->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah dipakai user lain']);
        exit;
    }

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE web_users SET nama_lengkap = ?, username = ?, password = ? WHERE id = ?");
        $stmt->execute([$nama, $username, $hash, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE web_users SET nama_lengkap = ?, username = ? WHERE id = ?");
        $stmt->execute([$nama, $username, $userId]);
    }

    $_SESSION['username'] = $username;
    $_SESSION['nama_lengkap'] = $nama;

    echo json_encode(['status' => 'success']);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
