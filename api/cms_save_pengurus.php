<?php
require_once '../config/database.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $nama = $_POST['nama'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $urutan = (int)($_POST['urutan'] ?? 1);
    
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../public/uploads/cms/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'pengurus_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $filename)) $foto = 'public/uploads/cms/' . $filename;
    }

    if ($id > 0) {
        if ($foto) $pdo->prepare("UPDATE web_pengurus SET nama=?, jabatan=?, urutan=?, foto=? WHERE id=?")->execute([$nama, $jabatan, $urutan, $foto, $id]);
        else $pdo->prepare("UPDATE web_pengurus SET nama=?, jabatan=?, urutan=? WHERE id=?")->execute([$nama, $jabatan, $urutan, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Data pengurus diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO web_pengurus (nama, jabatan, urutan, foto) VALUES (?, ?, ?, ?)")->execute([$nama, $jabatan, $urutan, $foto]);
        echo json_encode(['status' => 'success', 'message' => 'Anggota ditambahkan ke struktur.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }