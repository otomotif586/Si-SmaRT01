<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = $_POST['id'] ?? 0;
    $approvalOnly = isset($_POST['approved_portal']) && ($id > 0);

    if ($approvalOnly) {
        $approved = (int)($_POST['approved_portal'] ?? 0) === 1 ? 1 : 0;
        $approvedAt = $approved ? date('Y-m-d H:i:s') : null;
        $approvedBy = $approved ? 'Admin / Sistem' : null;

        $stmt = $pdo->prepare("UPDATE laporan_keamanan SET approved_portal = ?, approved_portal_at = ?, approved_portal_by = ? WHERE id = ?");
        $stmt->execute([$approved, $approvedAt, $approvedBy, $id]);
        echo json_encode(['status' => 'success', 'message' => $approved ? 'Aduan disetujui tampil di portal.' : 'Aduan tidak ditampilkan di portal.']);
        exit;
    }

    $judul = $_POST['judul'] ?? '';
    $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $status = $_POST['status'] ?? 'Baru';
    $pelapor = trim((string)($_POST['pelapor'] ?? 'Pengurus'));
    $kategori = trim((string)($_POST['kategori'] ?? 'Info Pengurus'));
    $sumberInput = trim((string)($_POST['sumber_input'] ?? 'Pengurus'));

    if ($pelapor === '') $pelapor = 'Pengurus';
    if ($kategori === '') $kategori = 'Info Pengurus';
    if ($sumberInput === '') $sumberInput = 'Pengurus';

    $lampiranPath = null;
    $lampiranName = null;
    $replaceLampiran = false;

    if (isset($_FILES['lampiran_file']) && (int)($_FILES['lampiran_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $uploadError = (int)($_FILES['lampiran_file']['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload lampiran gagal.');
        }

        $size = (int)($_FILES['lampiran_file']['size'] ?? 0);
        if ($size > 8 * 1024 * 1024) {
            throw new RuntimeException('Ukuran lampiran maksimal 8MB.');
        }

        $tmpName = (string)($_FILES['lampiran_file']['tmp_name'] ?? '');
        $origName = basename((string)($_FILES['lampiran_file']['name'] ?? 'lampiran'));
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'ogg', 'mov', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];
        if (!in_array($ext, $allowedExt, true)) {
            throw new RuntimeException('Format lampiran tidak didukung.');
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/lampiran_aduan';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Folder upload lampiran tidak dapat dibuat.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
        $newName = date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '_' . $safeName;
        $targetAbs = $uploadDir . '/' . $newName;
        if (!move_uploaded_file($tmpName, $targetAbs)) {
            throw new RuntimeException('Gagal menyimpan file lampiran.');
        }

        $lampiranPath = 'public/uploads/lampiran_aduan/' . $newName;
        $lampiranName = $origName;
        $replaceLampiran = true;
    }

    if (empty($judul) || empty($waktu_kejadian)) {
        echo json_encode(['status' => 'error', 'message' => 'Judul dan Waktu wajib diisi!']);
        exit;
    }

    if ($id > 0) {
        $oldLampiranPath = '';
        if ($replaceLampiran) {
            $stmtOld = $pdo->prepare("SELECT lampiran_path FROM laporan_keamanan WHERE id = ? LIMIT 1");
            $stmtOld->execute([$id]);
            $oldLampiranPath = (string)($stmtOld->fetchColumn() ?: '');
        }

        if ($replaceLampiran) {
            $stmt = $pdo->prepare("UPDATE laporan_keamanan SET judul=?, waktu_kejadian=?, lokasi=?, deskripsi=?, status=?, pelapor=?, kategori=?, sumber_input=?, lampiran_path=?, lampiran_name=? WHERE id=?");
            $stmt->execute([$judul, $waktu_kejadian, $lokasi, $deskripsi, $status, $pelapor, $kategori, $sumberInput, $lampiranPath, $lampiranName, $id]);

            if ($oldLampiranPath !== '') {
                $oldAbs = dirname(__DIR__, 2) . '/' . ltrim($oldLampiranPath, '/');
                if (is_file($oldAbs)) {
                    @unlink($oldAbs);
                }
            }
        } else {
            $stmt = $pdo->prepare("UPDATE laporan_keamanan SET judul=?, waktu_kejadian=?, lokasi=?, deskripsi=?, status=?, pelapor=?, kategori=?, sumber_input=? WHERE id=?");
            $stmt->execute([$judul, $waktu_kejadian, $lokasi, $deskripsi, $status, $pelapor, $kategori, $sumberInput, $id]);
        }
        $msg = "Laporan berhasil diperbarui.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO laporan_keamanan (judul, waktu_kejadian, lokasi, deskripsi, status, pelapor, kategori, sumber_input, butuh_approval_portal, approved_portal, approved_portal_at, approved_portal_by, lampiran_path, lampiran_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 1, NOW(), 'Admin / Sistem', ?, ?)");
        $stmt->execute([$judul, $waktu_kejadian, $lokasi, $deskripsi, $status, $pelapor, $kategori, $sumberInput, $lampiranPath, $lampiranName]);
        $msg = "Laporan kejadian baru berhasil ditambahkan.";
    }

    echo json_encode(['status' => 'success', 'message' => $msg]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}