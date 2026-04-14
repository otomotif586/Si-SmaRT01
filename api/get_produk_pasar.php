<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

try {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

    $penjual = isset($_GET['penjual_nama']) ? $_GET['penjual_nama'] : '';
    $where = '';
    $params = [];
    if ($penjual !== '') {
        $where = ' WHERE penjual_nama = :penjual ';
        $params[':penjual'] = $penjual;
    }

    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM pasar_produk $where");
    $stmtTotal->execute($params);
    $total = $stmtTotal->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM pasar_produk $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
    ]);
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
        echo json_encode(['status' => 'success', 'data' => [], 'total' => 0]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
