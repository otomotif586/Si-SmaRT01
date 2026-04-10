<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
    $settings = [
        'web_nama' => $_POST['web_nama'] ?? '',
        'web_email' => $_POST['web_email'] ?? '',
        'web_telepon' => $_POST['web_telepon'] ?? '',
        'web_alamat' => $_POST['web_alamat'] ?? '',
        'web_visi' => $_POST['web_visi'] ?? '',
        'web_misi' => $_POST['web_misi'] ?? ''
    ];

    $stmt = $pdo->prepare("INSERT INTO web_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach($settings as $key => $val) {
        $stmt->execute([$key, $val]);
    }
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }