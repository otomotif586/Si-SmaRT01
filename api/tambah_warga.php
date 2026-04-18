<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blok_id = $_POST['blok_id'] ?? null;
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $nik = $_POST['nik'] ?? '';
    $nik_kepala = $_POST['nik_kepala'] ?? '';
    $no_wa = $_POST['no_wa'] ?? '';
    
    if (empty($nama_lengkap) || empty($blok_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama Kepala Keluarga & Blok ID wajib diisi!']);
        exit;
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
        $stmt = $pdo->prepare("
            INSERT INTO warga (blok_id, nik, nik_kepala, nama_lengkap, nomor_rumah, no_wa, tempat_lahir, tanggal_lahir, status_pernikahan, status_kependudukan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$blok_id, $nik, $nik_kepala, $nama_lengkap, $nomorRumahFormatted, $no_wa, $_POST['tempat_lahir'], empty($_POST['tanggal_lahir']) ? null : $_POST['tanggal_lahir'], $_POST['status_pernikahan'], $_POST['status_kependudukan']]);
        
        $warga_id = $pdo->lastInsertId();

        // 1. Simpan Pasangan
        if ($_POST['status_pernikahan'] === 'Menikah' && !empty($_POST['pasangan_nama'])) {
            $stmtPasangan = $pdo->prepare("INSERT INTO warga_pasangan (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
            $tgl = empty($_POST['pasangan_tgl']) ? null : $_POST['pasangan_tgl'];
            $nik_pasangan = empty($_POST['pasangan_nik']) ? null : $_POST['pasangan_nik'];
            $stmtPasangan->execute([$warga_id, $nik_pasangan, $_POST['pasangan_nama'], $_POST['pasangan_tempat'], $tgl]);
        }

        // 2. Simpan Anak
        if (isset($_POST['anak']) && is_array($_POST['anak'])) {
            $stmtAnak = $pdo->prepare("INSERT INTO warga_anak (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
            foreach ($_POST['anak'] as $a) {
                $tgl = empty($a['tgl']) ? null : $a['tgl'];
                $stmtAnak->execute([$warga_id, $a['nik'], $a['nama'], $a['tempat'], $tgl]);
            }
        }
        
        // 3. Simpan Kendaraan
        if (isset($_POST['kendaraan']) && is_array($_POST['kendaraan'])) {
            $stmtKendaraan = $pdo->prepare("INSERT INTO warga_kendaraan (warga_id, nopol, jenis_kendaraan) VALUES (?, ?, ?)");
            foreach ($_POST['kendaraan'] as $k) {
                $stmtKendaraan->execute([$warga_id, $k['nopol'], $k['jenis']]);
            }
        }

        // 4. Simpan Orang Lain (jika ada)
        if (isset($_POST['orang_lain']) && is_array($_POST['orang_lain'])) {
            $stmtOrang = $pdo->prepare("INSERT INTO warga_orang_lain (warga_id, nama_lengkap, umur, status_hubungan) VALUES (?, ?, ?, ?)");
            foreach ($_POST['orang_lain'] as $o) {
                $stmtOrang->execute([$warga_id, $o['nama'], empty($o['umur']) ? null : $o['umur'], $o['hubungan']]);
            }
        }

        // 5. Simpan Dokumen
        if (isset($_FILES['dokumen'])) {
            $stmtDokumen = $pdo->prepare("INSERT INTO warga_dokumen (warga_id, file_path) VALUES (?, ?)");
            foreach ($_FILES['dokumen']['tmp_name'] as $idx => $tmpName) {
                if (!empty($tmpName)) {
                    // Generate a safe unique name
                    $fileName = time() . '_' . uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", $_FILES['dokumen']['name'][$idx]);
                    $destPath = '../public/uploads/' . $fileName;
                    if (move_uploaded_file($tmpName, $destPath)) {
                        $stmtDokumen->execute([$warga_id, 'public/uploads/' . $fileName]);
                    }
                }
            }
        }

        // 6. Deteksi Otomatis & Generate Tagihan (Iuran) dari Periode Mulai Blok ke Bulan Sekarang
        $stmtBlok = $pdo->prepare("SELECT periode_mulai_bulan, periode_mulai_tahun FROM blok WHERE id = ?");
        $stmtBlok->execute([$blok_id]);
        $blok = $stmtBlok->fetch(PDO::FETCH_ASSOC);

        if ($blok && $blok['periode_mulai_bulan'] !== null && $blok['periode_mulai_tahun'] !== null) {
            $startMonth = (int)$blok['periode_mulai_bulan'];
            $startYear = (int)$blok['periode_mulai_tahun'];
            $currentMonth = (int)date('n') - 1;
            $currentYear = (int)date('Y');

            // Ambil nominal master iuran
            $stmtMaster = $pdo->prepare("SELECT SUM(nominal) FROM master_iuran WHERE blok_id = ?");
            $stmtMaster->execute([$blok_id]);
            $total_master = $stmtMaster->fetchColumn();
            if (!$total_master) {
                $total_master = $pdo->query("SELECT SUM(nominal) FROM master_iuran WHERE blok_id IS NULL")->fetchColumn() ?: 0;
            }

            if ($total_master > 0) {
                $stmtCekIuran = $pdo->prepare("SELECT id FROM pembayaran_iuran WHERE warga_id = ? AND bulan = ? AND tahun = ?");
                $stmtInsertIuran = $pdo->prepare("INSERT INTO pembayaran_iuran (warga_id, bulan, tahun, total_tagihan, status) VALUES (?, ?, ?, ?, 'MENUNGGAK')");
                
                // Loop dari Tahun/Bulan Mulai Operasional sampai Sekarang
                for ($y = $startYear; $y <= $currentYear; $y++) {
                    $m_start = ($y == $startYear) ? $startMonth : 0;
                    $m_end = ($y == $currentYear) ? $currentMonth : 11;
                    
                    for ($m = $m_start; $m <= $m_end; $m++) {
                        $stmtCekIuran->execute([$warga_id, $m, $y]);
                        if (!$stmtCekIuran->fetch()) {
                            $stmtInsertIuran->execute([$warga_id, $m, $y, $total_master]);
                        }
                    }
                }
            }
        }

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}