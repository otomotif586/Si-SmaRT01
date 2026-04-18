<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $blok_id = (int)($_POST['blok_id'] ?? 0);
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $nik = $_POST['nik'] ?? '';
    $nik_kepala = $_POST['nik_kepala'] ?? '';
    $no_wa = $_POST['no_wa'] ?? '';

    if (empty($nama_lengkap) || empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama Kepala Keluarga & ID Warga wajib diisi!']);
        exit;
    }

    if ($blok_id <= 0) {
        $stmtCurrentBlok = $pdo->prepare("SELECT blok_id FROM warga WHERE id = ? LIMIT 1");
        $stmtCurrentBlok->execute([$id]);
        $blok_id = (int)($stmtCurrentBlok->fetchColumn() ?: 0);
    }

    $stmtBlokNama = $pdo->prepare("SELECT nama_blok FROM blok WHERE id = ? LIMIT 1");
    $stmtBlokNama->execute([$blok_id]);
    $blokNama = trim((string)($stmtBlokNama->fetchColumn() ?: ''));
    if ($blokNama === '') {
        echo json_encode(['status' => 'error', 'message' => 'Blok tidak valid.']);
        exit;
    }

    $rawNoRumah = (string)($_POST['nomor_rumah'] ?? '');
    $noRumahDigits = preg_replace('/\D+/', '', $rawNoRumah);
    if ($noRumahDigits === '') {
        echo json_encode(['status' => 'error', 'message' => 'Nomor rumah wajib 2 digit.']);
        exit;
    }
    if (strlen($noRumahDigits) > 2) {
        $noRumahDigits = substr($noRumahDigits, -2);
    }
    $nomorRumahFormatted = $blokNama . '-' . str_pad($noRumahDigits, 2, '0', STR_PAD_LEFT);

    // Validasi angka
    if ((!empty($nik) && !preg_match('/^[0-9]+$/', $nik)) || (!empty($nik_kepala) && !preg_match('/^[0-9]+$/', $nik_kepala)) || (!empty($no_wa) && !preg_match('/^[0-9]+$/', $no_wa))) {
        echo json_encode(['status' => 'error', 'message' => 'No KK, NIK Kepala, dan No WhatsApp harus murni angka!']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare(" 
            UPDATE warga SET blok_id = ?, nik = ?, nik_kepala = ?, nama_lengkap = ?, nomor_rumah = ?, no_wa = ?, tempat_lahir = ?, tanggal_lahir = ?, status_pernikahan = ?, status_kependudukan = ?
            WHERE id = ?
        ");
        $stmt->execute([$blok_id, $nik, $nik_kepala, $nama_lengkap, $nomorRumahFormatted, $no_wa, $_POST['tempat_lahir'], empty($_POST['tanggal_lahir']) ? null : $_POST['tanggal_lahir'], $_POST['status_pernikahan'], $_POST['status_kependudukan'], $id]);
        
        // Hapus data relasi lama
        $pdo->prepare("DELETE FROM warga_pasangan WHERE warga_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM warga_anak WHERE warga_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM warga_kendaraan WHERE warga_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM warga_orang_lain WHERE warga_id = ?")->execute([$id]);
        // Dokumen lama tidak dihapus otomatis kecuali ada request khusus (prevent data loss file)

        // 1. Simpan Pasangan
        if ($_POST['status_pernikahan'] === 'Menikah' && !empty($_POST['pasangan_nama'])) {
            $stmtPasangan = $pdo->prepare("INSERT INTO warga_pasangan (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
            $tgl = empty($_POST['pasangan_tgl']) ? null : $_POST['pasangan_tgl'];
            $nik_pasangan = empty($_POST['pasangan_nik']) ? null : $_POST['pasangan_nik'];
            $stmtPasangan->execute([$id, $nik_pasangan, $_POST['pasangan_nama'], $_POST['pasangan_tempat'], $tgl]);
        }

        // 2. Simpan Anak
        if (isset($_POST['anak']) && is_array($_POST['anak'])) {
            $stmtAnak = $pdo->prepare("INSERT INTO warga_anak (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
            foreach ($_POST['anak'] as $a) {
                $tgl = empty($a['tgl']) ? null : $a['tgl'];
                $stmtAnak->execute([$id, $a['nik'], $a['nama'], $a['tempat'], $tgl]);
            }
        }
        
        // 3. Simpan Kendaraan
        if (isset($_POST['kendaraan']) && is_array($_POST['kendaraan'])) {
            $stmtKendaraan = $pdo->prepare("INSERT INTO warga_kendaraan (warga_id, nopol, jenis_kendaraan) VALUES (?, ?, ?)");
            foreach ($_POST['kendaraan'] as $k) {
                $stmtKendaraan->execute([$id, $k['nopol'], $k['jenis']]);
            }
        }

        // 4. Simpan Orang Lain (jika ada)
        if (isset($_POST['orang_lain']) && is_array($_POST['orang_lain'])) {
            $stmtOrang = $pdo->prepare("INSERT INTO warga_orang_lain (warga_id, nama_lengkap, umur, status_hubungan) VALUES (?, ?, ?, ?)");
            foreach ($_POST['orang_lain'] as $o) {
                $stmtOrang->execute([$id, $o['nama'], empty($o['umur']) ? null : $o['umur'], $o['hubungan']]);
            }
        }
        
        // 5. Tambah Dokumen Baru (Append)
        if (isset($_FILES['dokumen'])) {
            $stmtDokumen = $pdo->prepare("INSERT INTO warga_dokumen (warga_id, file_path) VALUES (?, ?)");
            foreach ($_FILES['dokumen']['tmp_name'] as $idx => $tmpName) {
                if (!empty($tmpName)) {
                    $fileName = time() . '_' . uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", $_FILES['dokumen']['name'][$idx]);
                    $destPath = '../public/uploads/' . $fileName;
                    if (move_uploaded_file($tmpName, $destPath)) {
                        $stmtDokumen->execute([$id, 'public/uploads/' . $fileName]);
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}