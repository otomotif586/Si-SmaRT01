<?php
require_once '../../config/database.php';
require_once '../../config/asset_url.php';
header('Content-Type: application/json');

// Auto-patch kolom logo jika belum ada di tabel
try {
    $pdo->query("SELECT logo FROM pasar_penjual LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE pasar_penjual ADD COLUMN logo VARCHAR(255) NULL");
}

try {
    $id = $_POST['id'] ?? 0;
    $toko = $_POST['nama_toko'] ?? ''; 
    $pemilik = $_POST['nama_pemilik'] ?? ''; 
    $wa = $_POST['no_wa'] ?? ''; 
    $alamat = $_POST['alamat'] ?? '';
    $username = $_POST['username'] ?? '';
    $status = $_POST['status'] ?? 'Aktif';
    
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
    
    if ($id > 0) {
        $updateFields = ["nama_toko=?", "nama_pemilik=?", "no_wa=?", "alamat=?", "username=?", "status=?"];
        $params = [$toko, $pemilik, $wa, $alamat, $username, $status];

        if (!empty($_POST['password'])) {
            $updateFields[] = "password=?";
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        if ($logo) {
            $updateFields[] = "logo=?";
            $params[] = $logo;
        }

        $params[] = $id;
        $sql = "UPDATE pasar_penjual SET " . implode(", ", $updateFields) . " WHERE id=?";
        $pdo->prepare($sql)->execute($params);
    } else {
        $pdo->prepare("INSERT INTO pasar_penjual (nama_toko, nama_pemilik, no_wa, alamat, username, password, status, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")->execute([$toko, $pemilik, $wa, $alamat, $username, password_hash($_POST['password'], PASSWORD_DEFAULT), $status, $logo]);
    }
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }