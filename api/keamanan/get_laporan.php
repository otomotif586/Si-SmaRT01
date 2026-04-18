<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = (int)($_GET['per_page'] ?? 15);
    if ($perPage <= 0) {
        $perPage = 15;
    }
    if ($perPage > 100) {
        $perPage = 100;
    }
    $offset = ($page - 1) * $perPage;

    $total = (int)$pdo->query("SELECT COUNT(*) FROM laporan_keamanan")->fetchColumn();

    $stmt = $pdo->prepare("SELECT id, judul, waktu_kejadian, lokasi, deskripsi, status, pelapor, kategori, approved_portal, lampiran_path, lampiran_name FROM laporan_keamanan ORDER BY waktu_kejadian DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format waktu untuk tampilan tabel
    foreach ($data as &$row) {
        $row['waktu_kejadian'] = date('Y-m-d H:i', strtotime($row['waktu_kejadian']));
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $perPage > 0 ? (int)ceil($total / $perPage) : 1
        ]
    ]);
} catch (Exception $e) {
    // Jika tabel belum dibuat, kembalikan data kosong agar aplikasi tidak rusak
    if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
        echo json_encode(['status' => 'success', 'data' => []]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}