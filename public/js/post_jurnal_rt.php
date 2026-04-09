<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids_json = $_POST['ids'] ?? '[]';
    $ids = json_decode($ids_json, true);

    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang dipilih untuk diposting.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        // Ambil data iuran yang valid (sudah divalidasi RT, belum diposting)
        $stmt = $pdo->prepare("
            SELECT p.id, p.total_tagihan, p.bulan, p.tahun, w.blok_id, b.nama_blok
            FROM pembayaran_iuran p
            JOIN warga w ON p.warga_id = w.id
            JOIN blok b ON w.blok_id = b.id
            WHERE p.id IN ($placeholders) AND p.tanggal_validasi_rt IS NOT NULL AND p.tanggal_posting IS NULL
        ");
        $stmt->execute($ids);
        $iuran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($iuran_list)) {
            echo json_encode(['status' => 'error', 'message' => 'Data yang dipilih tidak valid atau sudah pernah diposting.']);
            $pdo->rollBack();
            exit;
        }

        // Grouping per blok
        $grouped_by_blok = [];
        foreach ($iuran_list as $iuran) {
            $blok_id = $iuran['blok_id'];
            if (!isset($grouped_by_blok[$blok_id])) {
                $grouped_by_blok[$blok_id] = [
                    'nama_blok' => $iuran['nama_blok'],
                    'total_nominal' => 0,
                    'bulan' => $iuran['bulan'],
                    'tahun' => $iuran['tahun'],
                ];
            }
            $grouped_by_blok[$blok_id]['total_nominal'] += $iuran['total_tagihan'];
        }

        $bulanArr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $stmtInsertJurnal = $pdo->prepare("
            INSERT INTO jurnal_keuangan 
                (jenis, nominal, tanggal, keterangan, doc_number, created_at, source_type, source_id_blok, source_bulan, source_tahun) 
            VALUES 
                ('Masuk', ?, ?, ?, ?, NOW(), 'iuran_warga', ?, ?, ?)
        ");
        $today = date('Ymd');
        
        // Get last doc number for today to increment
        $stmtLast = $pdo->prepare("SELECT doc_number FROM jurnal_keuangan WHERE doc_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmtLast->execute(["JRN-$today-%"]);
        $lastDoc = $stmtLast->fetchColumn();
        $lastNum = 0;
        if ($lastDoc) {
            $parts = explode('-', $lastDoc);
            if(count($parts) === 3) {
                $lastNum = (int)$parts[2];
            }
        }

        // Insert Jurnal per Blok
        foreach ($grouped_by_blok as $blok_id => $data) {
            $lastNum++;
            $newNum = str_pad($lastNum, 3, '0', STR_PAD_LEFT);
            $docNumber = "JRN-$today-$newNum";
            
            $namaBulan = $bulanArr[(int)$data['bulan']];
            $keterangan = "Setoran Iuran Warga [{$data['nama_blok']}] - Periode {$namaBulan} {$data['tahun']}";
            
            $stmtInsertJurnal->execute([
                $data['total_nominal'], 
                date('Y-m-d'), 
                $keterangan, 
                $docNumber,
                $blok_id, $data['bulan'], $data['tahun']
            ]);
        }

        // Update tanggal_posting di tabel pembayaran_iuran
        $processed_ids = array_column($iuran_list, 'id');
        $placeholders_update = implode(',', array_fill(0, count($processed_ids), '?'));
        $stmtUpdate = $pdo->prepare("UPDATE pembayaran_iuran SET tanggal_posting = NOW() WHERE id IN ($placeholders_update)");
        $stmtUpdate->execute($processed_ids);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => count($grouped_by_blok) . ' jurnal berhasil diposting.']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}