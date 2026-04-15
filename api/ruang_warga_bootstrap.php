<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS warga_pengaduan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        linked_warga_id INT NULL,
        judul VARCHAR(255) NOT NULL,
        isi TEXT NOT NULL,
        status VARCHAR(20) DEFAULT 'Diajukan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS warga_update_informasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        judul VARCHAR(255) NOT NULL,
        isi TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmtUser = $pdo->prepare("SELECT id, username, nama_lengkap, role, created_at FROM web_users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $profile = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan']);
        exit;
    }

    $stmtWarga = $pdo->prepare("SELECT w.*, b.nama_blok
        FROM warga w
        LEFT JOIN blok b ON b.id = w.blok_id
        WHERE w.nama_lengkap = ?
           OR REPLACE(REPLACE(IFNULL(w.no_wa,''), '+62', '0'), ' ', '') = REPLACE(REPLACE(?, '+62', '0'), ' ', '')
        ORDER BY CASE WHEN w.nama_lengkap = ? THEN 0 ELSE 1 END, w.id DESC
        LIMIT 1");
    $stmtWarga->execute([$profile['nama_lengkap'], $profile['username'], $profile['nama_lengkap']]);
    $linkedWarga = $stmtWarga->fetch(PDO::FETCH_ASSOC) ?: null;

    $totalWargaGlobal = (int)$pdo->query("SELECT COUNT(*) FROM warga")->fetchColumn();
    $totalBlok = (int)$pdo->query("SELECT COUNT(*) FROM blok")->fetchColumn();

    $history = [];
    $totalLunasSaya = 0;
    $totalTunggakanSaya = 0;

    if ($linkedWarga && !empty($linkedWarga['id'])) {
        $wargaId = (int)$linkedWarga['id'];

        $stmtHistory = $pdo->prepare("SELECT tahun, bulan, status, total_tagihan, tanggal_bayar
            FROM pembayaran_iuran
            WHERE warga_id = ?
            ORDER BY tahun DESC, bulan DESC
            LIMIT 36");
        $stmtHistory->execute([$wargaId]);
        $history = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

        $stmtLunas = $pdo->prepare("SELECT COUNT(*) FROM pembayaran_iuran WHERE warga_id = ? AND status = 'LUNAS'");
        $stmtLunas->execute([$wargaId]);
        $totalLunasSaya = (int)$stmtLunas->fetchColumn();

        $stmtTunggak = $pdo->prepare("SELECT COUNT(*) FROM pembayaran_iuran WHERE warga_id = ? AND status = 'MENUNGGAK'");
        $stmtTunggak->execute([$wargaId]);
        $totalTunggakanSaya = (int)$stmtTunggak->fetchColumn();
    }

    $stmtPengaduan = $pdo->prepare("SELECT id, judul, isi, status, created_at FROM warga_pengaduan WHERE user_id = ? ORDER BY created_at DESC");
    $stmtPengaduan->execute([$userId]);
    $pengaduan = $stmtPengaduan->fetchAll(PDO::FETCH_ASSOC);

    $stmtUpdate = $pdo->prepare("SELECT id, judul, isi, created_at FROM warga_update_informasi WHERE user_id = ? ORDER BY created_at DESC");
    $stmtUpdate->execute([$userId]);
    $updates = $stmtUpdate->fetchAll(PDO::FETCH_ASSOC);

    $infoFeed = [];
    try {
        $stmtBlog = $pdo->query("SELECT judul, LEFT(REPLACE(REPLACE(konten, '\\r', ' '), '\\n', ' '), 140) AS ringkas, created_at AS waktu FROM web_blogs ORDER BY created_at DESC LIMIT 4");
        $blogs = $stmtBlog->fetchAll(PDO::FETCH_ASSOC);
        $infoFeed = array_merge($infoFeed, $blogs);
    } catch (Throwable $e) {
    }

    try {
        $stmtAgenda = $pdo->query("SELECT judul, LEFT(deskripsi, 140) AS ringkas, created_at AS waktu FROM agenda_kegiatan ORDER BY created_at DESC LIMIT 4");
        $agenda = $stmtAgenda->fetchAll(PDO::FETCH_ASSOC);
        $infoFeed = array_merge($infoFeed, $agenda);
    } catch (Throwable $e) {
    }

    usort($infoFeed, function ($a, $b) {
        return strcmp((string)($b['waktu'] ?? ''), (string)($a['waktu'] ?? ''));
    });
    $infoFeed = array_slice($infoFeed, 0, 6);

    echo json_encode([
        'status' => 'success',
        'profile' => $profile,
        'linked_warga' => $linkedWarga,
        'dashboard' => [
            'total_warga_global' => $totalWargaGlobal,
            'total_blok' => $totalBlok,
            'total_lunas_saya' => $totalLunasSaya,
            'total_tunggakan_saya' => $totalTunggakanSaya,
        ],
        'history' => $history,
        'pengaduan' => $pengaduan,
        'updates' => $updates,
        'info_feed' => $infoFeed,
    ]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
