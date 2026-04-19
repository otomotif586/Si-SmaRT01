<?php
session_start();
require_once 'config/database.php';
require_once __DIR__ . '/../../config/asset_url.php';

$alertMessage = '';
$alertType = '';

$pdo->exec("CREATE TABLE IF NOT EXISTS `ruang_warga_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `no_rumah` varchar(50) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
    `avatar` varchar(255) DEFAULT NULL,
  `penjual_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ruang_warga_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    $pdo->query("SELECT avatar FROM ruang_warga_accounts LIMIT 1");
} catch (Throwable $e) {
    try {
        $pdo->exec("ALTER TABLE ruang_warga_accounts ADD COLUMN avatar VARCHAR(255) NULL AFTER no_wa");
    } catch (Throwable $ignored) {}
}

$pdo->exec("CREATE TABLE IF NOT EXISTS `pasar_penjual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `alamat` text,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Aktif',
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_pasar_penjual_username` (`username`),
  UNIQUE KEY `uniq_pasar_penjual_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    $pdo->query("SELECT nik FROM pasar_penjual LIMIT 1");
} catch (Throwable $e) {
    try {
        $pdo->exec("ALTER TABLE pasar_penjual ADD COLUMN nik VARCHAR(16) NULL AFTER password");
    } catch (Throwable $ignored) {}
}

$normalizeNik = function (string $nik): string {
    return preg_replace('/\D+/', '', trim($nik));
};

$normalizeWa = function (string $wa): string {
    $digits = preg_replace('/\D+/', '', trim($wa));
    if ($digits === '') {
        return '';
    }
    if (strpos($digits, '62') === 0) {
        return '0' . substr($digits, 2);
    }
    return $digits;
};

$normalizeNoRumahDigits = function (string $value): string {
    $digits = preg_replace('/\D+/', '', trim($value));
    if ($digits === '') {
        return '';
    }
    if (strlen($digits) > 2) {
        $digits = substr($digits, -2);
    }
    return str_pad($digits, 2, '0', STR_PAD_LEFT);
};

$fetchBlokNameById = function (int $blokId) use ($pdo): ?string {
    if ($blokId <= 0) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT nama_blok FROM blok WHERE id = ? LIMIT 1");
    $stmt->execute([$blokId]);
    $name = $stmt->fetchColumn();
    if ($name === false) {
        return null;
    }
    return trim((string)$name);
};

$formatNomorRumah = function (?string $blokNama, string $nomorRaw) use ($normalizeNoRumahDigits): string {
    $nomor2Digit = $normalizeNoRumahDigits($nomorRaw);
    if ($nomor2Digit === '') {
        return '';
    }
    $blok = trim((string)$blokNama);
    if ($blok === '') {
        return $nomor2Digit;
    }
    return $blok . '-' . $nomor2Digit;
};

$normalizeName = function (string $name): string {
    $name = strtolower(trim($name));
    return preg_replace('/\s+/', ' ', $name);
};

$ensurePenjualAccount = function (array $warga) use ($pdo): array {
    $nik = $warga['nik'];
    $stmt = $pdo->prepare("SELECT * FROM pasar_penjual WHERE nik = ? LIMIT 1");
    $stmt->execute([$nik]);
    $penjual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penjual) {
        $baseUsername = 'nik' . substr($nik, -8);
        $username = $baseUsername;
        $counter = 1;

        while (true) {
            $check = $pdo->prepare("SELECT id FROM pasar_penjual WHERE username = ? LIMIT 1");
            $check->execute([$username]);
            if (!$check->fetch(PDO::FETCH_ASSOC)) {
                break;
            }
            $counter++;
            $username = $baseUsername . $counter;
        }

        $namaToko = 'Toko ' . $warga['nama'];
        $alamat = 'No Rumah ' . $warga['no_rumah'];
        $hashPassword = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);

        $insert = $pdo->prepare("INSERT INTO pasar_penjual (nama_toko, nama_pemilik, no_wa, alamat, username, password, nik, status)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, 'Aktif')");
        $insert->execute([$namaToko, $warga['nama'], $warga['no_wa'], $alamat, $username, $hashPassword, $nik]);

        $id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM pasar_penjual WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $penjual = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $update = $pdo->prepare("UPDATE pasar_penjual SET nama_pemilik = ?, no_wa = ?, alamat = ?, status = 'Aktif' WHERE id = ?");
        $update->execute([$warga['nama'], $warga['no_wa'], 'No Rumah ' . $warga['no_rumah'], $penjual['id']]);
        $stmt = $pdo->prepare("SELECT * FROM pasar_penjual WHERE id = ? LIMIT 1");
        $stmt->execute([$penjual['id']]);
        $penjual = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return $penjual;
};

$getLinkedWargaByNik = function (string $nik) use ($pdo): ?array {
    if ($nik === '') {
        return null;
    }
    $stmt = $pdo->prepare("SELECT w.*, b.nama_blok FROM warga w LEFT JOIN blok b ON b.id = w.blok_id WHERE w.nik = ? LIMIT 1");
    $stmt->execute([$nik]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
};

$getLinkedWarga = function (array $account) use ($pdo, $normalizeWa, $normalizeName, $getLinkedWargaByNik): ?array {
    $nik = preg_replace('/\D+/', '', (string)($account['nik'] ?? ''));
    $nama = trim((string)($account['nama'] ?? ''));
    $noRumah = trim((string)($account['no_rumah'] ?? ''));
    $noWa = $normalizeWa((string)($account['no_wa'] ?? ''));

    $row = $getLinkedWargaByNik($nik);
    if ($row) {
        return $row;
    }

    if ($noWa !== '') {
        $stmt = $pdo->prepare("SELECT w.*, b.nama_blok
            FROM warga w
            LEFT JOIN blok b ON b.id = w.blok_id
            WHERE REPLACE(REPLACE(IFNULL(w.no_wa,''), '+62', '0'), ' ', '') = ?
            ORDER BY w.id DESC
            LIMIT 1");
        $stmt->execute([$noWa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
    }

    if ($nama !== '' && $noRumah !== '') {
        $stmt = $pdo->prepare("SELECT w.*, b.nama_blok
            FROM warga w
            LEFT JOIN blok b ON b.id = w.blok_id
            WHERE LOWER(TRIM(IFNULL(w.nama_lengkap,''))) = ?
              AND TRIM(IFNULL(w.nomor_rumah,'')) = ?
            ORDER BY w.id DESC
            LIMIT 1");
        $stmt->execute([$normalizeName($nama), $noRumah]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
    }

    return null;
};

$ensureLinkedWarga = function (array $account) use ($pdo, $normalizeNik, $normalizeWa, $getLinkedWarga, $getLinkedWargaByNik): ?array {
    $nik = $normalizeNik((string)($account['nik'] ?? ''));
    $nama = trim((string)($account['nama'] ?? ''));
    $noRumah = trim((string)($account['no_rumah'] ?? ''));
    $noWa = $normalizeWa((string)($account['no_wa'] ?? ''));

    $row = $getLinkedWarga($account);
    if ($row) {
        $patch = [];
        $params = [];

        if ($nik !== '' && trim((string)($row['nik'] ?? '')) === '') {
            $patch[] = 'nik = ?';
            $params[] = $nik;
        }
        if ($noRumah !== '' && trim((string)($row['nomor_rumah'] ?? '')) === '') {
            $patch[] = 'nomor_rumah = ?';
            $params[] = $noRumah;
        }
        if ($noWa !== '' && trim((string)($row['no_wa'] ?? '')) === '') {
            $patch[] = 'no_wa = ?';
            $params[] = $noWa;
        }
        if ($nama !== '' && trim((string)($row['nama_lengkap'] ?? '')) === '') {
            $patch[] = 'nama_lengkap = ?';
            $params[] = $nama;
        }

        if (!empty($patch)) {
            $params[] = (int)$row['id'];
            $stmtPatch = $pdo->prepare('UPDATE warga SET ' . implode(', ', $patch) . ' WHERE id = ?');
            $stmtPatch->execute($params);
            if ($nik !== '') {
                $fresh = $getLinkedWargaByNik($nik);
                if ($fresh) {
                    return $fresh;
                }
            }
            $row = $getLinkedWarga($account) ?: $row;
        }
        return $row;
    }

    if ($nama === '') {
        return null;
    }

    $blokId = (int)($pdo->query('SELECT id FROM blok ORDER BY id ASC LIMIT 1')->fetchColumn() ?: 0);
    if ($blokId <= 0) {
        return null;
    }

    $stmtInsert = $pdo->prepare("INSERT INTO warga
        (blok_id, nik, nik_kepala, nama_lengkap, nomor_rumah, no_wa, tempat_lahir, tanggal_lahir, status_pernikahan, status_kependudukan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtInsert->execute([
        $blokId,
        $nik,
        $nik !== '' ? $nik : null,
        $nama,
        $noRumah,
        $noWa,
        '',
        null,
        'Lajang',
        'Tetap'
    ]);

    if ($nik !== '') {
        return $getLinkedWargaByNik($nik);
    }

    return $getLinkedWarga($account);
};

$sanitizeRows = function ($rows, array $allowed) {
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $r) {
        if (!is_array($r)) {
            continue;
        }
        $tmp = [];
        foreach ($allowed as $k) {
            $tmp[$k] = trim((string)($r[$k] ?? ''));
        }
        $keep = false;
        foreach ($tmp as $v) {
            if ($v !== '') {
                $keep = true;
                break;
            }
        }
        if ($keep) {
            $out[] = $tmp;
        }
    }
    return $out;
};

$isLoggedIn = isset($_SESSION['rw_account_id']);
$account = null;
$linkedWarga = null;
$aduanPage = max(1, (int)($_GET['aduan_page'] ?? 1));
$aduanPerPage = 15;
$aduanTotalRows = 0;
$aduanTotalPages = 1;

$blokOptions = [];
try {
    $stmtBlok = $pdo->query("SELECT id, nama_blok FROM blok ORDER BY nama_blok ASC");
    $blokOptions = $stmtBlok->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $ignored) {
    $blokOptions = [];
}

if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT * FROM ruang_warga_accounts WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_SESSION['rw_account_id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        unset($_SESSION['rw_account_id'], $_SESSION['rw_nik'], $_SESSION['rw_nama']);
        $isLoggedIn = false;
    } else {
        try {
            $linkedWarga = $ensureLinkedWarga($account);
        } catch (Throwable $e) {
            $linkedWarga = null;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $nik = $normalizeNik($_POST['nik'] ?? '');

        if (!preg_match('/^\d{16}$/', $nik)) {
            $alertType = 'error';
            $alertMessage = 'NIK harus terdiri dari 16 digit angka.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM ruang_warga_accounts WHERE nik = ? LIMIT 1");
            $stmt->execute([$nik]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                $alertType = 'warning';
                $alertMessage = 'Akun belum terdaftar. Silakan lakukan pendaftaran dulu.';
            } else {
                $penjual = $ensurePenjualAccount($account);

                $_SESSION['rw_account_id'] = (int)$account['id'];
                $_SESSION['rw_nik'] = $account['nik'];
                $_SESSION['rw_nama'] = $account['nama'];
                $_SESSION['penjual_id'] = (int)$penjual['id'];
                $_SESSION['penjual_nama_toko'] = $penjual['nama_toko'];

                $up = $pdo->prepare("UPDATE ruang_warga_accounts SET last_login_at = NOW(), penjual_id = ? WHERE id = ?");
                $up->execute([$penjual['id'], $account['id']]);

                header('Location: ruang_warga.php');
                exit;
            }
        }
    }

    if ($action === 'register') {
        $nik = $normalizeNik($_POST['nik'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $blokId = (int)($_POST['blok_id'] ?? 0);
        $blokNama = $fetchBlokNameById($blokId);
        $noRumah = $formatNomorRumah($blokNama, (string)($_POST['no_rumah'] ?? ''));
        $noWa = $normalizeWa($_POST['no_wa'] ?? '');

        if (!preg_match('/^\d{16}$/', $nik)) {
            $alertType = 'error';
            $alertMessage = 'NIK harus terdiri dari 16 digit angka.';
        } elseif ($nama === '' || $noRumah === '' || $noWa === '') {
            $alertType = 'error';
            $alertMessage = 'Nama, Blok, No Rumah (2 digit), dan No WA wajib diisi.';
        } else {
            $check = $pdo->prepare("SELECT id FROM ruang_warga_accounts WHERE nik = ? LIMIT 1");
            $check->execute([$nik]);

            if ($check->fetch(PDO::FETCH_ASSOC)) {
                $alertType = 'warning';
                $alertMessage = 'NIK sudah terdaftar. Silakan login menggunakan NIK Anda.';
            } else {
                $ins = $pdo->prepare("INSERT INTO ruang_warga_accounts (nik, nama, no_rumah, no_wa) VALUES (?, ?, ?, ?)");
                $ins->execute([$nik, $nama, $noRumah, $noWa]);

                $id = (int)$pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT * FROM ruang_warga_accounts WHERE id = ? LIMIT 1");
                $stmt->execute([$id]);
                $account = $stmt->fetch(PDO::FETCH_ASSOC);

                $penjual = $ensurePenjualAccount($account);
                $up = $pdo->prepare("UPDATE ruang_warga_accounts SET penjual_id = ?, last_login_at = NOW() WHERE id = ?");
                $up->execute([$penjual['id'], $id]);

                $_SESSION['rw_account_id'] = $id;
                $_SESSION['rw_nik'] = $account['nik'];
                $_SESSION['rw_nama'] = $account['nama'];
                $_SESSION['penjual_id'] = (int)$penjual['id'];
                $_SESSION['penjual_nama_toko'] = $penjual['nama_toko'];

                header('Location: ruang_warga.php');
                exit;
            }
        }
    }

    if ($action === 'update_warga_full' && $isLoggedIn && $account) {
        $linkedWarga = $ensureLinkedWarga($account);
        if (!$linkedWarga) {
            $alertType = 'warning';
            $alertMessage = 'Data warga belum dapat disinkronkan otomatis. Pastikan minimal ada 1 data blok atau hubungi admin.';
        } else {
            $idWarga = (int)$linkedWarga['id'];
            $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
            $nik = $normalizeNik($_POST['nik'] ?? '');
            $nikKepala = $normalizeNik($_POST['nik_kepala'] ?? '');
            $noWa = $normalizeWa($_POST['no_wa'] ?? '');
            $blokId = (int)($_POST['blok_id'] ?? 0);
            $blokNama = $fetchBlokNameById($blokId);
            $nomorRumahFormatted = $formatNomorRumah($blokNama, (string)($_POST['nomor_rumah'] ?? ''));

            if ($namaLengkap === '') {
                $alertType = 'error';
                $alertMessage = 'Nama lengkap wajib diisi.';
            } elseif ($blokId <= 0 || $blokNama === null) {
                $alertType = 'error';
                $alertMessage = 'Blok wajib dipilih.';
            } elseif ($nomorRumahFormatted === '') {
                $alertType = 'error';
                $alertMessage = 'Nomor rumah wajib 2 digit.';
            } elseif ($nik !== '' && !preg_match('/^\d{16}$/', $nik)) {
                $alertType = 'error';
                $alertMessage = 'NIK harus 16 digit angka.';
            } elseif ($nikKepala !== '' && !preg_match('/^\d{16}$/', $nikKepala)) {
                $alertType = 'error';
                $alertMessage = 'NIK Kepala Keluarga harus 16 digit angka.';
            } else {
                try {
                    $anakRows = $sanitizeRows($_POST['anak'] ?? [], ['nik', 'nama', 'tempat', 'tgl']);
                    $kendaraanRows = $sanitizeRows($_POST['kendaraan'] ?? [], ['nopol', 'jenis']);
                    $orangRows = $sanitizeRows($_POST['orang_lain'] ?? [], ['nama', 'umur', 'hubungan']);

                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare("UPDATE warga SET blok_id = ?, nik = ?, nik_kepala = ?, nama_lengkap = ?, nomor_rumah = ?, no_wa = ?, tempat_lahir = ?, tanggal_lahir = ?, status_pernikahan = ?, status_kependudukan = ? WHERE id = ?");
                    $stmt->execute([
                        $blokId,
                        $nik,
                        $nikKepala,
                        $namaLengkap,
                        $nomorRumahFormatted,
                        $noWa,
                        trim($_POST['tempat_lahir'] ?? ''),
                        trim($_POST['tanggal_lahir'] ?? '') !== '' ? trim($_POST['tanggal_lahir']) : null,
                        trim($_POST['status_pernikahan'] ?? 'Lajang'),
                        trim($_POST['status_kependudukan'] ?? 'Tetap'),
                        $idWarga
                    ]);

                    $pdo->prepare("DELETE FROM warga_pasangan WHERE warga_id = ?")->execute([$idWarga]);
                    $pdo->prepare("DELETE FROM warga_anak WHERE warga_id = ?")->execute([$idWarga]);
                    $pdo->prepare("DELETE FROM warga_kendaraan WHERE warga_id = ?")->execute([$idWarga]);
                    $pdo->prepare("DELETE FROM warga_orang_lain WHERE warga_id = ?")->execute([$idWarga]);

                    if (trim($_POST['status_pernikahan'] ?? '') === 'Menikah' && trim($_POST['pasangan_nama'] ?? '') !== '') {
                        $stmtPasangan = $pdo->prepare("INSERT INTO warga_pasangan (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
                        $stmtPasangan->execute([
                            $idWarga,
                            $normalizeNik($_POST['pasangan_nik'] ?? ''),
                            trim($_POST['pasangan_nama'] ?? ''),
                            trim($_POST['pasangan_tempat'] ?? ''),
                            trim($_POST['pasangan_tgl'] ?? '') !== '' ? trim($_POST['pasangan_tgl']) : null
                        ]);
                    }

                    if (!empty($anakRows)) {
                        $stmtAnak = $pdo->prepare("INSERT INTO warga_anak (warga_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir) VALUES (?, ?, ?, ?, ?)");
                        foreach ($anakRows as $a) {
                            $stmtAnak->execute([$idWarga, $normalizeNik($a['nik']), $a['nama'], $a['tempat'], $a['tgl'] !== '' ? $a['tgl'] : null]);
                        }
                    }

                    if (!empty($kendaraanRows)) {
                        $stmtKen = $pdo->prepare("INSERT INTO warga_kendaraan (warga_id, nopol, jenis_kendaraan) VALUES (?, ?, ?)");
                        foreach ($kendaraanRows as $k) {
                            $stmtKen->execute([$idWarga, strtoupper($k['nopol']), $k['jenis']]);
                        }
                    }

                    if (!empty($orangRows)) {
                        $stmtOr = $pdo->prepare("INSERT INTO warga_orang_lain (warga_id, nama_lengkap, umur, status_hubungan) VALUES (?, ?, ?, ?)");
                        foreach ($orangRows as $o) {
                            $stmtOr->execute([$idWarga, $o['nama'], $o['umur'] !== '' ? (int)$o['umur'] : null, $o['hubungan']]);
                        }
                    }

                    if (isset($_FILES['dokumen']) && isset($_FILES['dokumen']['tmp_name']) && is_array($_FILES['dokumen']['tmp_name'])) {
                        $uploadDir = smart_public_fs_path('public/uploads');
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $stmtDoc = $pdo->prepare("INSERT INTO warga_dokumen (warga_id, file_path) VALUES (?, ?)");
                        foreach ($_FILES['dokumen']['tmp_name'] as $idx => $tmpName) {
                            if (empty($tmpName) || !is_uploaded_file($tmpName)) {
                                continue;
                            }
                            $safe = preg_replace("/[^a-zA-Z0-9.-]/", "_", $_FILES['dokumen']['name'][$idx]);
                            $fileName = time() . '_' . uniqid() . '_' . $safe;
                            $destPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                            if (move_uploaded_file($tmpName, $destPath)) {
                                $stmtDoc->execute([$idWarga, 'public/uploads/' . $fileName]);
                            }
                        }
                    }

                    $avatarPathToSave = null;
                    if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                        $avatarUploadDir = smart_public_fs_path('public/uploads/avatars');
                        if (!is_dir($avatarUploadDir)) {
                            mkdir($avatarUploadDir, 0777, true);
                        }

                        $avatarExt = strtolower(pathinfo((string)($_FILES['avatar']['name'] ?? ''), PATHINFO_EXTENSION));
                        $allowedAvatarExt = ['jpg', 'jpeg', 'png', 'webp'];
                        $avatarSize = (int)($_FILES['avatar']['size'] ?? 0);

                        if (in_array($avatarExt, $allowedAvatarExt, true) && $avatarSize > 0 && $avatarSize <= (2 * 1024 * 1024)) {
                            $avatarName = 'avatar_' . ((int)$account['id']) . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $avatarExt;
                            $avatarDest = $avatarUploadDir . DIRECTORY_SEPARATOR . $avatarName;
                            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarDest)) {
                                $avatarPathToSave = 'public/uploads/avatars/' . $avatarName;
                            }
                        }
                    }

                    if ($avatarPathToSave !== null) {
                        $upAcc = $pdo->prepare("UPDATE ruang_warga_accounts SET nama = ?, no_rumah = ?, no_wa = ?, avatar = ? WHERE id = ?");
                        $upAcc->execute([$namaLengkap, $nomorRumahFormatted, $noWa, $avatarPathToSave, (int)$account['id']]);
                    } else {
                        $upAcc = $pdo->prepare("UPDATE ruang_warga_accounts SET nama = ?, no_rumah = ?, no_wa = ? WHERE id = ?");
                        $upAcc->execute([$namaLengkap, $nomorRumahFormatted, $noWa, (int)$account['id']]);
                    }

                    $_SESSION['rw_nama'] = $namaLengkap;
                    $pdo->commit();

                    $alertType = 'success';
                    $alertMessage = 'Data diri warga berhasil diperbarui lengkap.';
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $alertType = 'error';
                    $alertMessage = 'Gagal memperbarui data diri: ' . $e->getMessage();
                }
            }
        }
    }

    if ($action === 'submit_aduan_smart' && $isLoggedIn && $account) {
        try {
            $linkedWarga = $ensureLinkedWarga($account);
        } catch (Throwable $e) {
            $linkedWarga = null;
        }
        if (!$linkedWarga) {
            $alertType = 'warning';
            $alertMessage = 'Data warga belum terhubung, aduan belum bisa dikirim.';
        } else {
            $judul = trim($_POST['adu_judul'] ?? '');
            $keterangan = trim($_POST['adu_keterangan'] ?? '');
            if ($judul === '') {
                $alertType = 'error';
                $alertMessage = 'Judul aduan wajib diisi.';
            } else {
                try {
                    $lampiranPath = null;
                    $lampiranName = null;

                    if (isset($_FILES['adu_lampiran']) && (int)($_FILES['adu_lampiran']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                        $uploadError = (int)($_FILES['adu_lampiran']['error'] ?? UPLOAD_ERR_NO_FILE);
                        if ($uploadError !== UPLOAD_ERR_OK) {
                            throw new RuntimeException('Lampiran gagal diunggah.');
                        }

                        $tmp = (string)($_FILES['adu_lampiran']['tmp_name'] ?? '');
                        $origName = basename((string)($_FILES['adu_lampiran']['name'] ?? 'lampiran'));
                        $size = (int)($_FILES['adu_lampiran']['size'] ?? 0);
                        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'pdf'];

                        if ($size > 8 * 1024 * 1024) {
                            throw new RuntimeException('Ukuran lampiran maksimal 8MB.');
                        }
                        if (!in_array($ext, $allowedExt, true)) {
                            throw new RuntimeException('Format lampiran tidak didukung.');
                        }

                        $dirAbs = __DIR__ . '/../../public/uploads/lampiran_aduan';
                        if (!is_dir($dirAbs) && !mkdir($dirAbs, 0777, true) && !is_dir($dirAbs)) {
                            throw new RuntimeException('Folder upload tidak dapat dibuat.');
                        }

                        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
                        $newName = date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '_' . $safeName;
                        $targetAbs = $dirAbs . '/' . $newName;
                        if (!move_uploaded_file($tmp, $targetAbs)) {
                            throw new RuntimeException('Gagal menyimpan lampiran aduan.');
                        }

                        $lampiranPath = 'public/uploads/lampiran_aduan/' . $newName;
                        $lampiranName = $origName;
                    }

                    $pelapor = trim((string)($linkedWarga['nama_lengkap'] ?? ''));
                    if ($pelapor === '') {
                        $pelapor = trim((string)($account['nama'] ?? 'Sistem'));
                    }

                    $stmtLap = $pdo->prepare("INSERT INTO laporan_keamanan (judul, waktu_kejadian, lokasi, deskripsi, status, pelapor, kategori, sumber_input, sumber_warga_id, sumber_account_id, butuh_approval_portal, approved_portal, approved_portal_at, approved_portal_by, lampiran_path, lampiran_name) VALUES (?, NOW(), ?, ?, 'Baru', ?, 'Aduan Warga', 'Warga', ?, ?, 1, 0, NULL, NULL, ?, ?)");
                    $stmtLap->execute([
                        $judul,
                        (string)($linkedWarga['nama_blok'] ?? ''),
                        $keterangan,
                        $pelapor,
                        (int)$linkedWarga['id'],
                        (int)$account['id'],
                        $lampiranPath,
                        $lampiranName,
                    ]);

                    $alertType = 'success';
                    $alertMessage = 'Aduan berhasil dikirim. Menunggu persetujuan pengurus untuk tampil di portal.';
                } catch (Throwable $e) {
                    $alertType = 'error';
                    $alertMessage = 'Gagal kirim aduan: ' . $e->getMessage();
                }
            }
        }
    }

    if ($action === 'logout') {
        header('Location: logout_ruang_warga.php');
        exit;
    }

    if ($action !== 'login' && $action !== 'register') {
        header('Location: ruang_warga.php?ref=1');
        exit;
    }
}

$isLoggedIn = isset($_SESSION['rw_account_id']);
$account = null;
$linkedWarga = null;
$historyRows = [];
$laporanRows = [];
$importantInfoRows = [];
$pantauInfoRows = [];
$portalPantauUrl = 'index.php#laporan_terbaru';
$pasangan = null;
$anak = [];
$kendaraan = [];
$orangLain = [];
$dokumen = [];

if ($isLoggedIn) {
    $stmtAcc = $pdo->prepare("SELECT * FROM ruang_warga_accounts WHERE id = ? LIMIT 1");
    $stmtAcc->execute([(int)$_SESSION['rw_account_id']]);
    $account = $stmtAcc->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        unset($_SESSION['rw_account_id'], $_SESSION['rw_nik'], $_SESSION['rw_nama']);
        $isLoggedIn = false;
    } else {
        $linkedWarga = $ensureLinkedWarga($account);

        // Sinkronkan sumber Pantau Informasi dengan section portal (laporan_terbaru).
        try {
            $stmtPantau = $pdo->query("SELECT id, judul, deskripsi, status, kategori, pelapor, waktu_kejadian FROM laporan_keamanan ORDER BY waktu_kejadian DESC LIMIT 12");
            $pantauInfoRows = $stmtPantau->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $ignored) {
            $pantauInfoRows = [];
        }

        if ($linkedWarga) {
            $stmtHistory = $pdo->prepare("SELECT tahun, bulan, status, total_tagihan, tanggal_bayar FROM pembayaran_iuran WHERE warga_id = ? ORDER BY tahun DESC, bulan DESC LIMIT 24");
            $stmtHistory->execute([(int)$linkedWarga['id']]);
            $historyRows = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

            try {
                $stmtCountLap = $pdo->prepare("SELECT COUNT(*) FROM laporan_keamanan WHERE sumber_warga_id = ?");
                $stmtCountLap->execute([(int)$linkedWarga['id']]);
                $aduanTotalRows = (int)$stmtCountLap->fetchColumn();
                $aduanTotalPages = max(1, (int)ceil($aduanTotalRows / $aduanPerPage));
                if ($aduanPage > $aduanTotalPages) {
                    $aduanPage = $aduanTotalPages;
                }
                $aduanOffset = ($aduanPage - 1) * $aduanPerPage;

                $stmtLap = $pdo->prepare("SELECT id, judul, deskripsi, status, waktu_kejadian, approved_portal, lampiran_path, lampiran_name FROM laporan_keamanan WHERE sumber_warga_id = ? ORDER BY waktu_kejadian DESC LIMIT ? OFFSET ?");
                $stmtLap->bindValue(1, (int)$linkedWarga['id'], PDO::PARAM_INT);
                $stmtLap->bindValue(2, $aduanPerPage, PDO::PARAM_INT);
                $stmtLap->bindValue(3, $aduanOffset, PDO::PARAM_INT);
                $stmtLap->execute();
                $laporanRows = $stmtLap->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $ignored) {
                $laporanRows = [];
                $aduanTotalRows = 0;
                $aduanTotalPages = 1;
                $aduanPage = 1;
            }

            $stmtPas = $pdo->prepare("SELECT * FROM warga_pasangan WHERE warga_id = ? LIMIT 1");
            $stmtPas->execute([(int)$linkedWarga['id']]);
            $pasangan = $stmtPas->fetch(PDO::FETCH_ASSOC) ?: null;

            $stmtAnak = $pdo->prepare("SELECT * FROM warga_anak WHERE warga_id = ? ORDER BY id ASC");
            $stmtAnak->execute([(int)$linkedWarga['id']]);
            $anak = $stmtAnak->fetchAll(PDO::FETCH_ASSOC);

            $stmtKen = $pdo->prepare("SELECT * FROM warga_kendaraan WHERE warga_id = ? ORDER BY id ASC");
            $stmtKen->execute([(int)$linkedWarga['id']]);
            $kendaraan = $stmtKen->fetchAll(PDO::FETCH_ASSOC);

            $stmtOr = $pdo->prepare("SELECT * FROM warga_orang_lain WHERE warga_id = ? ORDER BY id ASC");
            $stmtOr->execute([(int)$linkedWarga['id']]);
            $orangLain = $stmtOr->fetchAll(PDO::FETCH_ASSOC);

            $stmtDoc = $pdo->prepare("SELECT * FROM warga_dokumen WHERE warga_id = ? ORDER BY id DESC");
            $stmtDoc->execute([(int)$linkedWarga['id']]);
            $dokumen = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);

            // Feed informasi penting untuk warga (gabungan berita + agenda terbaru)
            try {
                $blogRows = [];
                $agendaRows = [];

                try {
                    $stmtBlog = $pdo->query("SELECT judul, LEFT(REPLACE(REPLACE(konten, '\\r', ' '), '\\n', ' '), 180) AS ringkas, created_at AS waktu, 'Berita' AS jenis FROM web_blogs ORDER BY created_at DESC LIMIT 5");
                    $blogRows = $stmtBlog->fetchAll(PDO::FETCH_ASSOC);
                } catch (Throwable $ignored) {}

                try {
                    $stmtAgenda = $pdo->query("SELECT judul, LEFT(IFNULL(deskripsi, ''), 180) AS ringkas, created_at AS waktu, 'Agenda' AS jenis FROM agenda_kegiatan ORDER BY created_at DESC LIMIT 5");
                    $agendaRows = $stmtAgenda->fetchAll(PDO::FETCH_ASSOC);
                } catch (Throwable $ignored) {}

                $importantInfoRows = array_merge($blogRows, $agendaRows);
                usort($importantInfoRows, function ($a, $b) {
                    return strcmp((string)($b['waktu'] ?? ''), (string)($a['waktu'] ?? ''));
                });
                $importantInfoRows = array_slice($importantInfoRows, 0, 6);
            } catch (Throwable $ignored) {
                $importantInfoRows = [];
            }
        }
    }
}

$statusOptions = ['Lajang', 'Menikah', 'Pisah'];
$kependudukanOptions = ['Tetap', 'Kontrak', 'Weekend'];
?>
