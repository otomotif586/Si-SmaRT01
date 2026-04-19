<?php
require_once '../config/database.php';
require_once '../config/asset_url.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $jenis = $_POST['jenis'] ?? '';
    $nominal = str_replace(['Rp', '.', ','], '', $_POST['nominal'] ?? '0');
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($jenis) || empty($nominal) || empty($tanggal) || empty($keterangan)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Lengkapi nominal, tanggal, dan keterangan.']);
        exit;
    }

    $lampiran_path = null;
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = smart_public_fs_path('public/uploads/keuangan');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = time() . '_' . uniqid() . '_' . basename($_FILES['lampiran']['name']);
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . preg_replace("/[^a-zA-Z0-9.-]/", "_", $fileName);
        
        if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $targetPath)) {
            $lampiran_path = 'public/uploads/keuangan/' . basename($targetPath);
        }
    }

    try {
        if ($id > 0) {
            $sql = $lampiran_path ? "UPDATE jurnal_keuangan SET jenis=?, nominal=?, tanggal=?, keterangan=?, lampiran=? WHERE id=?" : "UPDATE jurnal_keuangan SET jenis=?, nominal=?, tanggal=?, keterangan=? WHERE id=?";
            $params = $lampiran_path ? [$jenis, $nominal, $tanggal, $keterangan, $lampiran_path, $id] : [$jenis, $nominal, $tanggal, $keterangan, $id];
        } else {
            // Generate Running Number: JRN-YYYYMMDD-001
            $today = date('Ymd');
            $stmtLast = $pdo->prepare("SELECT doc_number FROM jurnal_keuangan WHERE doc_number LIKE ? ORDER BY id DESC LIMIT 1");
            $stmtLast->execute(["JRN-$today-%"]);
            $lastDoc = $stmtLast->fetchColumn();
            
            $newNum = '001';
            if ($lastDoc) { $parts = explode('-', $lastDoc); if(count($parts) === 3) { $newNum = str_pad((int)$parts[2] + 1, 3, '0', STR_PAD_LEFT); } }
            $doc_number = "JRN-$today-$newNum";
            $created_at = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO jurnal_keuangan (jenis, nominal, tanggal, keterangan, lampiran, doc_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$jenis, $nominal, $tanggal, $keterangan, $lampiran_path, $doc_number, $created_at];
        }
        $pdo->prepare($sql)->execute($params);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
}