<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/asset_url.php';
header('Content-Type: application/json');

if (!isset($_SESSION['penjual_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    $nama = $_POST['nama_toko'] ?? '';
    $wa = $_POST['no_wa'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    $logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = smart_public_fs_path('public/uploads/penjual');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $filename)) {
            $logo = 'public/uploads/penjual/' . $filename;
        }
    }

    if ($logo) {
        $stmt = $pdo->prepare("UPDATE pasar_penjual SET nama_toko=?, no_wa=?, alamat=?, logo=? WHERE id=?");
        $stmt->execute([$nama, $wa, $alamat, $logo, $_SESSION['penjual_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE pasar_penjual SET nama_toko=?, no_wa=?, alamat=? WHERE id=?");
        $stmt->execute([$nama, $wa, $alamat, $_SESSION['penjual_id']]);
    }
    
    // Update session
    $_SESSION['penjual_nama_toko'] = $nama;
    
    echo json_encode(['status' => 'success', 'message' => 'Profil toko diperbarui.']);
} catch (Exception $e) { 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
}