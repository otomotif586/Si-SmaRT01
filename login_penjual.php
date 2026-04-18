<?php
session_start();
require_once 'config/database.php';

// Jika sudah login sebagai penjual, lempar ke ruang_penjual
if (isset($_SESSION['penjual_id'])) {
    header("Location: ruang_penjual.php");
    exit();
}

// Jika sudah login sebagai warga, otomatis aktifkan akun penjual yang terhubung.
if (!isset($_SESSION['penjual_id']) && isset($_SESSION['rw_account_id'])) {
    try {
        $stmtRw = $pdo->prepare("SELECT * FROM ruang_warga_accounts WHERE id = ? LIMIT 1");
        $stmtRw->execute([(int)$_SESSION['rw_account_id']]);
        $rwAccount = $stmtRw->fetch(PDO::FETCH_ASSOC);

        if ($rwAccount) {
            $nikRw = preg_replace('/\D+/', '', (string)($rwAccount['nik'] ?? ''));
            $penjual = null;

            if ($nikRw !== '') {
                $stmtPenjual = $pdo->prepare("SELECT * FROM pasar_penjual WHERE nik = ? LIMIT 1");
                $stmtPenjual->execute([$nikRw]);
                $penjual = $stmtPenjual->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            if (!$penjual) {
                $baseUsername = 'nik' . substr($nikRw !== '' ? $nikRw : (string)$_SESSION['rw_account_id'], -8);
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

                $insert = $pdo->prepare("INSERT INTO pasar_penjual (nama_toko, nama_pemilik, no_wa, alamat, username, password, nik, status)
                                         VALUES (?, ?, ?, ?, ?, ?, ?, 'Aktif')");
                $insert->execute([
                    'Toko ' . trim((string)($rwAccount['nama'] ?? 'Warga')),
                    trim((string)($rwAccount['nama'] ?? '')),
                    trim((string)($rwAccount['no_wa'] ?? '')),
                    'No Rumah ' . trim((string)($rwAccount['no_rumah'] ?? '-')),
                    $username,
                    password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT),
                    $nikRw !== '' ? $nikRw : null
                ]);

                $stmtPenjual = $pdo->prepare("SELECT * FROM pasar_penjual WHERE id = ? LIMIT 1");
                $stmtPenjual->execute([(int)$pdo->lastInsertId()]);
                $penjual = $stmtPenjual->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            if ($penjual) {
                $_SESSION['penjual_id'] = (int)$penjual['id'];
                $_SESSION['penjual_nama_toko'] = $penjual['nama_toko'];
                $up = $pdo->prepare("UPDATE ruang_warga_accounts SET penjual_id = ?, last_login_at = NOW() WHERE id = ?");
                $up->execute([(int)$penjual['id'], (int)$rwAccount['id']]);

                header("Location: ruang_penjual.php");
                exit();
            }
        }
    } catch (Throwable $e) {
        // Abaikan, biarkan user melihat form login jika sinkronisasi gagal.
    }
}

// Auto-init tabel penjual agar halaman login/register tidak gagal di DB baru.
try {
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
} catch (Exception $e) {}

// Auto-patch kolom status agar bisa menampung nilai 'Pending' (jika sebelumnya ENUM)
try {
    $pdo->exec("ALTER TABLE pasar_penjual MODIFY COLUMN status VARCHAR(20) DEFAULT 'Aktif'");
} catch (Exception $e) {}

// Auto-patch kolom logo untuk kompatibilitas API lama.
try {
    $pdo->query("SELECT logo FROM pasar_penjual LIMIT 1");
} catch (Exception $e) {
    try {
        $pdo->exec("ALTER TABLE pasar_penjual ADD COLUMN logo VARCHAR(255) NULL");
    } catch (Exception $ignored) {}
}

// Auto-patch kolom NIK untuk login cepat tanpa password.
try {
    $pdo->query("SELECT nik FROM pasar_penjual LIMIT 1");
} catch (Exception $e) {
    try {
        $pdo->exec("ALTER TABLE pasar_penjual ADD COLUMN nik VARCHAR(16) NULL");
    } catch (Exception $ignored) {}
}

$alertMessage = "";
$alertType = "";
$isRightPanelActive = false; // Untuk mengunci posisi form jika registrasi gagal

// Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';

        if ($user && $pass) {
            $stmt = $pdo->prepare("SELECT * FROM pasar_penjual WHERE username = ?");
            $stmt->execute([$user]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($pass, $row['password'])) {
                if ($row['status'] == 'Pending') {
                    $alertMessage = "Toko Anda sedang dalam tahap review dan menunggu persetujuan Admin RT.";
                    $alertType = "warning";
                } else if ($row['status'] == 'Nonaktif') {
                    $alertMessage = "Akun toko Anda sedang dinonaktifkan oleh Admin.";
                    $alertType = "error";
                } else {
                    $_SESSION['penjual_id'] = $row['id'];
                    $_SESSION['penjual_nama_toko'] = $row['nama_toko'];
                    header("Location: ruang_penjual.php");
                    exit();
                }
            } else {
                $alertMessage = "Username atau password salah.";
                $alertType = "error";
            }
        } else {
            $alertMessage = "Mohon isi semua bidang.";
            $alertType = "error";
        }

    } elseif ($action === 'login_nik') {
        $nik = preg_replace('/\D+/', '', ($_POST['nik'] ?? ''));

        if (!preg_match('/^\d{16}$/', $nik)) {
            $alertMessage = "NIK wajib 16 digit angka.";
            $alertType = "error";
        } else {
            $stmtN = $pdo->prepare("SELECT * FROM pasar_penjual WHERE nik = ? LIMIT 1");
            $stmtN->execute([$nik]);
            $row = $stmtN->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $alertMessage = "NIK belum terhubung ke akun penjual. Silakan daftar lewat Ruang Warga.";
                $alertType = "warning";
            } elseif ($row['status'] == 'Nonaktif') {
                $alertMessage = "Akun toko Anda sedang dinonaktifkan oleh Admin.";
                $alertType = "error";
            } else {
                $_SESSION['penjual_id'] = $row['id'];
                $_SESSION['penjual_nama_toko'] = $row['nama_toko'];
                header("Location: ruang_penjual.php");
                exit();
            }
        }
        
    } elseif ($action === 'register') {
        $isRightPanelActive = true;
        $toko = $_POST['nama_toko'] ?? '';
        $pemilik = $_POST['nama_pemilik'] ?? '';
        $wa = $_POST['no_wa'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($toko && $username && $password) {
            // Cek apakah username sudah ada
            $stmtCek = $pdo->prepare("SELECT id FROM pasar_penjual WHERE username = ?");
            $stmtCek->execute([$username]);
            if ($stmtCek->fetch()) {
                $alertMessage = "Username sudah digunakan, silakan pilih yang lain.";
                $alertType = "error";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmtIns = $pdo->prepare("INSERT INTO pasar_penjual (nama_toko, nama_pemilik, no_wa, alamat, username, password, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
                if ($stmtIns->execute([$toko, $pemilik, $wa, $alamat, $username, $hash])) {
                    $alertMessage = "Registrasi Berhasil! Silakan tunggu Admin menyetujui toko Anda.";
                    $alertType = "success";
                    $isRightPanelActive = false; // Kembalikan ke form login
                } else {
                    $alertMessage = "Terjadi kesalahan sistem saat mendaftar.";
                    $alertType = "error";
                }
            }
        }
    }
}

$isRightPanelActive = $isRightPanelActive ?? false;
$alertMessage = $alertMessage ?? '';
$alertType = $alertType ?? 'info';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal UMKM Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/mobile-ux.css">
    <style>
        :root {
            --bg-1: #071029;
            --bg-2: #0f172a;
            --brand: #0f766e;
            --brand-2: #14b8a6;
            --card: rgba(255, 255, 255, 0.94);
            --line: rgba(15, 23, 42, 0.12);
            --text: #0f172a;
            --muted: #5b6477;
        }
        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background:
                radial-gradient(840px 460px at -15% -12%, rgba(45, 212, 191, 0.34), transparent 72%),
                radial-gradient(880px 500px at 115% 112%, rgba(59, 130, 246, 0.32), transparent 70%),
                linear-gradient(145deg, var(--bg-1), var(--bg-2));
            overflow-x: hidden;
        }

        button, input, select, textarea {
            font-family: 'Outfit', sans-serif;
        }

        .backdrop-dot {
            position: fixed;
            z-index: -1;
            border-radius: 999px;
            filter: blur(90px);
            opacity: 0.45;
            animation: dotFloat 9s ease-in-out infinite alternate;
        }
        .backdrop-dot.a { width: 350px; height: 350px; top: -90px; left: -110px; background: #2dd4bf; }
        .backdrop-dot.b { width: 310px; height: 310px; right: -90px; bottom: -120px; background: #60a5fa; animation-delay: -2s; }

        @keyframes dotFloat {
            from { transform: translateY(-10px) scale(1); }
            to { transform: translateY(16px) scale(1.08); }
        }

        .screen {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px 14px;
        }

        .layout {
            width: min(1180px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 340px) minmax(0, 1fr);
            gap: 12px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 30px;
            padding: 10px;
            backdrop-filter: blur(20px);
            box-shadow: 0 36px 90px -45px rgba(2, 6, 23, 0.9);
        }

        .intro {
            border-radius: 22px;
            color: #fff;
            padding: 24px;
            background:
                radial-gradient(240px 180px at 15% 0%, rgba(45, 212, 191, 0.28), transparent 70%),
                radial-gradient(260px 180px at 100% 100%, rgba(59, 130, 246, 0.26), transparent 70%),
                linear-gradient(155deg, rgba(15, 118, 110, 0.88), rgba(15, 23, 42, 0.87));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .intro-logo {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 22px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .intro h2 {
            margin: 14px 0 10px;
            font-size: 1.8rem;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .intro p {
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .intro-pills {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .intro-pill {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.14);
            padding: 7px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
        }

        .intro-foot {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255, 255, 255, 0.78);
        }

        .auth-shell {
            position: relative;
            min-height: 700px;
            padding: 16px;
            border-radius: 26px;
            background:
                radial-gradient(900px 320px at 50% 0%, rgba(255, 255, 255, 0.36), transparent 65%),
                linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.96));
            border: 1px solid rgba(255, 255, 255, 0.72);
            box-shadow: 0 28px 60px -38px rgba(15, 23, 42, 0.8);
            overflow: hidden;
        }

        .auth-shell::before {
            content: '';
            position: absolute;
            inset: -40% 55% auto auto;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20,184,166,0.16), transparent 65%);
            filter: blur(10px);
            pointer-events: none;
        }

        .auth-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .auth-head h1 {
            margin: 0 0 8px;
            font-size: clamp(1.35rem, 3vw, 1.9rem);
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--text);
        }

        .auth-head p {
            margin: 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.55;
            max-width: 620px;
        }

        .auth-tabs {
            flex: 0 0 auto;
            display: inline-flex;
            gap: 8px;
            padding: 6px;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.05);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .auth-tab {
            border: none;
            background: transparent;
            color: #475569;
            text-transform: none;
            letter-spacing: 0;
            box-shadow: none;
            padding: 11px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
        }

        .auth-tab:hover { transform: none; }

        .auth-tab.active {
            background: linear-gradient(140deg, var(--brand), var(--brand-2));
            color: #fff;
            box-shadow: 0 14px 24px -18px rgba(15, 118, 110, 0.95);
        }

        .auth-progress {
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 18px;
            border: 1px solid rgba(15, 118, 110, 0.12);
            background: linear-gradient(180deg, rgba(240,253,250,0.95), rgba(255,255,255,0.96));
        }

        .auth-progress-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .auth-progress-head span {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f766e;
        }

        .auth-progress-head strong {
            font-size: 12px;
            color: var(--text);
        }

        .auth-progress-track {
            height: 10px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .auth-progress-track span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #0f766e, #14b8a6, #22c55e);
            transition: width 0.25s ease;
        }

        .auth-stage {
            position: relative;
            min-height: 610px;
        }

        .form-panel {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            padding: 22px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
            opacity: 0;
            transform: translateY(10px) scale(0.985);
            pointer-events: none;
            transition: opacity 0.28s ease, transform 0.28s ease;
            overflow: hidden;
        }

        .sign-in-panel {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .right-panel-active .sign-up-panel {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
            animation: panelIn 0.34s ease;
        }

        .right-panel-active .sign-in-panel {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
            pointer-events: none;
        }

        .sign-up-panel {
            overflow-y: auto;
        }

        .panel-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .panel-top h2 {
            margin: 0 0 8px;
            color: var(--text);
            font-size: 1.7rem;
            line-height: 1.08;
            letter-spacing: -0.03em;
        }

        .panel-top p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: 0.9rem;
        }

        .panel-badge {
            flex: 0 0 auto;
            padding: 8px 11px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0f766e;
            background: rgba(15, 118, 110, 0.08);
            border: 1px solid rgba(15, 118, 110, 0.12);
        }

        .register-step {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 4px;
            margin-bottom: 10px;
        }

        .register-step span {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f766e;
        }

        .register-step strong {
            font-size: 12px;
            color: var(--text);
        }

        .login-metrics {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .metric-card {
            padding: 12px;
            border-radius: 16px;
            background: linear-gradient(180deg, #f8fafc, #ffffff);
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 10px 20px -18px rgba(15, 23, 42, 0.6);
        }

        .metric-card strong {
            display: block;
            font-size: 1.02rem;
            margin-bottom: 4px;
            color: var(--text);
        }

        .metric-card span {
            display: block;
            margin: 0;
            font-size: 11px;
            color: var(--muted);
            text-transform: none;
            letter-spacing: 0;
            font-weight: 600;
        }

        .panel-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .panel-actions .ghost,
        .panel-actions .submit-btn,
        .panel-actions .nik-trigger-btn {
            flex: 1 1 180px;
        }

        .register-panel-footer {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
        }

        .container {
            background: var(--card);
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 30px 70px -50px rgba(15, 23, 42, 0.8);
            position: relative;
            overflow: hidden;
            min-height: 620px;
        }

        .form-container {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .sign-in-container { left: 0; z-index: 2; }
        .container.right-panel-active .sign-in-container { transform: translateX(100%); }
        .sign-up-container { left: 0; opacity: 0; z-index: 1; }
        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .container.right-panel-active .overlay-container { transform: translateX(-100%); }

        .overlay {
            background: linear-gradient(145deg, var(--brand), var(--brand-2));
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay { transform: translateX(50%); }

        .overlay-panel {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 36px;
            top: 0;
            width: 50%;
            height: 100%;
            transition: transform 0.6s ease-in-out;
        }
        .overlay-left { transform: translateX(-20%); }
        .container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; }
        .container.right-panel-active .overlay-right { transform: translateX(20%); }

        .overlay-panel h1 {
            margin: 0;
            font-size: 1.9rem;
            line-height: 1.08;
            letter-spacing: -0.02em;
        }
        .overlay-panel p {
            margin: 12px 0 18px;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.55;
        }

        .mobile-switch { display: none; }

        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            min-height: 100%;
            padding: 28px;
        }

        .sign-up-container form {
            justify-content: flex-start;
            overflow-y: auto;
            padding: 24px 24px 22px;
        }

        .register-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 10px;
        }

        .register-fields .input-wrap {
            margin-bottom: 0;
        }

        .register-fields .input-wrap.full {
            grid-column: 1 / -1;
        }

        .register-fields .field input {
            min-width: 0;
            padding: 11px 0;
            font-size: 13px;
        }

        .form-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--brand);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-title {
            margin: 0;
            color: var(--text);
            font-size: 1.65rem;
            letter-spacing: -0.02em;
            line-height: 1.18;
        }

        .form-sub {
            margin: 10px 0 16px;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .sign-up-container .form-title,
        .sign-up-container .form-sub {
            padding-right: 4px;
        }

        .sign-up-container .form-title {
            font-size: 1.4rem;
            line-height: 1.12;
            margin-bottom: 2px;
        }

        .sign-up-container .form-sub {
            margin: 6px 0 12px;
            font-size: 0.84rem;
        }

        .sign-up-container .form-kicker {
            margin-bottom: 6px;
        }

        .register-fields .input-wrap label {
            font-size: 9px;
            margin-bottom: 5px;
        }

        .register-fields .field {
            min-height: 42px;
        }

        .input-wrap { margin-bottom: 10px; }
        .input-wrap label {
            display: block;
            margin-bottom: 6px;
            color: #536177;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 800;
        }
        .field {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 0 12px;
            transition: all 0.2s ease;
        }
        .field:focus-within {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.15);
            transform: translateY(-1px);
        }
        .field i { color: #94a3b8; font-size: 13px; }
        .field input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            font-weight: 600;
            padding: 13px 0;
        }

        a {
            color: var(--brand);
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            margin: 2px 0 10px;
            align-self: flex-end;
        }

        button {
            border-radius: 14px;
            border: none;
            background: linear-gradient(140deg, var(--brand), var(--brand-2));
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            padding: 13px 16px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: transform 0.16s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            cursor: pointer;
            box-shadow: 0 18px 30px -20px rgba(15, 118, 110, 0.9);
        }
        button:hover { transform: translateY(-1px); }
        button:active { transform: translateY(0); }
        button:focus { outline: none; }
        button:disabled { opacity: 0.72; cursor: not-allowed; }
        button.ghost {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: none;
            text-transform: none;
            letter-spacing: 0;
            border-radius: 12px;
        }

        .close-btn {
            position: fixed;
            top: 16px;
            right: 16px;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            color: #475569;
            display: grid;
            place-items: center;
            z-index: 1200;
            box-shadow: 0 18px 32px -22px rgba(2, 6, 23, 0.88);
            text-decoration: none;
        }
        .close-btn:hover { color: #fff; background: #ef4444; }

        .nik-trigger-wrap { margin-top: 9px; }
        .nik-trigger-btn {
            width: 100%;
            background: rgba(15, 118, 110, 0.08);
            border: 1px solid rgba(15, 118, 110, 0.16);
            color: #0f766e;
            text-transform: none;
            letter-spacing: 0;
            box-shadow: none;
        }
        .nik-trigger-btn:hover {
            background: rgba(15, 118, 110, 0.12);
            box-shadow: none;
        }

        .nik-modal {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(10px);
        }
        .nik-modal.active { display: flex; }
        .nik-modal-card {
            width: min(440px, 100%);
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 24px 60px -22px rgba(15, 23, 42, 0.45);
            overflow: hidden;
        }
        .nik-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(226,232,240,.9);
        }
        .nik-modal-head h3 { margin: 0; font-size: 1rem; color: #0f172a; }
        .nik-modal-close {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: none;
            background: #f8fafc;
            color: #64748b;
            padding: 0;
            box-shadow: none;
        }
        .nik-modal-body { padding: 20px; }
        .nik-modal-body .hint { margin: 0 0 14px; color: #64748b; font-size: 13px; }

        .nik-modal-body form {
            display: grid;
            gap: 10px;
            padding: 0;
            min-height: auto;
        }

        .nik-modal-body input[type="text"] {
            width: 100%;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 14px;
            background: #fff;
            padding: 14px 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .nik-modal-body input[type="text"]:focus {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.15);
            transform: translateY(-1px);
        }

        .nik-modal-body button {
            width: 100%;
            min-height: 46px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        @media (max-width: 768px) {
            .screen { padding: 10px; }
            .layout {
                grid-template-columns: 1fr;
                border-radius: 22px;
                padding: 8px;
            }
            .intro {
                border-radius: 16px;
                padding: 16px;
            }
            .intro h2 { font-size: 1.4rem; }
            .intro-foot { display: none; }
            .auth-shell { min-height: auto; padding: 12px; border-radius: 18px; }
            .auth-head { flex-direction: column; align-items: stretch; }
            .auth-tabs { width: 100%; justify-content: space-between; }
            .auth-tab { flex: 1 1 0; }
            .auth-stage { min-height: auto; }
            .form-panel { position: relative; inset: auto; display: none; opacity: 0; transform: none; pointer-events: none; padding: 16px; border-radius: 18px; }
            .sign-in-panel { display: flex; opacity: 1; pointer-events: auto; }
            .right-panel-active .sign-in-panel { display: none; opacity: 0; pointer-events: none; }
            .right-panel-active .sign-up-panel { display: flex; opacity: 1; pointer-events: auto; }
            .panel-top { flex-direction: column; }
            .panel-top h2 { font-size: 1.4rem; }
            .register-fields,
            .login-metrics,
            .hero-cards { grid-template-columns: 1fr; }
            form { padding: 0; min-height: auto; }
            .sign-up-panel { overflow: visible; }
            .sign-up-container .form-title {
                font-size: 1.45rem;
            }
            .sign-up-container .form-sub {
                font-size: 0.84rem;
                margin-bottom: 14px;
            }
            .panel-actions .ghost,
            .panel-actions .submit-btn,
            .panel-actions .nik-trigger-btn { flex: 1 1 100%; }
            .mobile-switch {
                display: block;
                margin-top: 12px;
                font-size: 12px;
                color: var(--brand);
                font-weight: 700;
                text-align: center;
                text-decoration: underline;
                cursor: pointer;
            }
            a { align-self: flex-start; margin-top: 0; }
        }

        @media (max-width: 390px) {
            .close-btn { width: 36px; height: 36px; border-radius: 12px; top: 10px; right: 10px; }
            .form-title { font-size: 1.4rem; }
            .form-sub { font-size: 0.84rem; }
            .field input { font-size: 13px; }
            button { width: 100%; }
            .mobile-switch { font-size: 12px; margin-top: 12px; }
        }
    </style>
    <link rel="stylesheet" href="public/css/theme-glass.css?v=20260417">
</head>
<body>
    <div class="backdrop-dot a"></div>
    <div class="backdrop-dot b"></div>

    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Memproses...</h3>
            <p class="smart-loading-subtitle">Menyiapkan akun Anda</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>
    
    <a href="pasar.php" class="close-btn" title="Kembali ke Pasar"><i class="fas fa-times"></i></a>

    <main class="screen">
        <section class="layout">
            <aside class="intro">
                <div>
                    <div class="intro-logo"><i class="fas fa-store"></i></div>
                    <h2>Portal UMKM Warga</h2>
                    <p>Buka toko, kelola pesanan, dan pantau aktivitas jualan dari satu dashboard yang nyaman seperti aplikasi mobile.</p>
                    <div class="intro-pills">
                        <span class="intro-pill">Startup Feel</span>
                        <span class="intro-pill">Mobile Ready</span>
                        <span class="intro-pill">Fast Access</span>
                    </div>
                </div>
                <div class="intro-foot">
                    <span>UMKM RT 001</span>
                    <span>2026</span>
                </div>
            </aside>

            <div class="container <?= !empty($isRightPanelActive) ? 'right-panel-active' : '' ?>" id="container">
                <div class="auth-shell">
                    <div class="auth-head">
                        <div>
                            <span class="form-kicker"><i class="fas fa-store"></i> Portal Penjual</span>
                            <h1>Kelola toko lebih cepat, nyaman di desktop dan mobile</h1>
                            <p>Layout baru dibuat lebih lega, modern, dan dinamis dengan animasi halus, progress registrasi, serta loading yang tetap ringan.</p>
                        </div>
                        <div class="auth-tabs" role="tablist" aria-label="Mode akses penjual">
                            <button type="button" class="auth-tab <?= empty($isRightPanelActive) ? 'active' : '' ?>" id="signIn">Login</button>
                            <button type="button" class="auth-tab <?= !empty($isRightPanelActive) ? 'active' : '' ?>" id="signUp">Registrasi</button>
                        </div>
                    </div>

                    <div class="auth-progress">
                        <div class="auth-progress-head">
                            <span>Progress registrasi</span>
                            <strong id="registerProgressText">0%</strong>
                        </div>
                        <div class="auth-progress-track" aria-hidden="true"><span id="registerProgressFill"></span></div>
                    </div>

                    <div class="auth-stage" id="authStage">
                        <form action="login_penjual.php" method="POST" id="loginFormPenjual" class="form-panel sign-in-panel">
                            <input type="hidden" name="action" value="login">
                            <div class="panel-top">
                                <div>
                                    <span class="form-kicker"><i class="fas fa-lock"></i> Login Penjual</span>
                                    <h2>Masuk Ruang UMKM</h2>
                                    <p>Gunakan akun toko yang sudah disetujui pengurus RT.</p>
                                </div>
                                <span class="panel-badge">Secure Access</span>
                            </div>

                            <div class="input-wrap">
                                <label>Username Toko</label>
                                <div class="field">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="username" placeholder="Masukkan username" required>
                                </div>
                            </div>

                            <div class="input-wrap">
                                <label>Password</label>
                                <div class="field">
                                    <i class="fas fa-key"></i>
                                    <input type="password" name="password" placeholder="Masukkan password" required>
                                </div>
                            </div>

                            <div class="panel-actions">
                                <button type="submit" id="btnSubmitLogin" class="submit-btn">Masuk Ruang Penjual</button>
                                <button type="button" class="nik-trigger-btn" id="openNikModalBtn">Masuk dengan NIK</button>
                            </div>

                            <div class="login-metrics">
                                <div class="metric-card">
                                    <strong>Fast Login</strong>
                                    <span>Masuk lebih cepat tanpa layout yang padat.</span>
                                </div>
                                <div class="metric-card">
                                    <strong>Responsive</strong>
                                    <span>Nyaman dipakai di desktop maupun HP.</span>
                                </div>
                            </div>

                            <span class="mobile-switch" id="mobileGoRegister">Belum punya toko? Daftar sekarang</span>
                        </form>

                        <form action="login_penjual.php" method="POST" id="registerFormPenjual" class="form-panel sign-up-panel">
                            <input type="hidden" name="action" value="register">
                            <div class="panel-top">
                                <div>
                                    <span class="form-kicker"><i class="fas fa-user-plus"></i> Registrasi Penjual</span>
                                    <h2>Buka Toko Baru</h2>
                                    <p>Lengkapi data usaha Anda untuk diajukan ke pengurus RT.</p>
                                </div>
                                <span class="panel-badge">Form Dinamis</span>
                            </div>

                            <div class="register-step">
                                <span>Step progress</span>
                                <strong id="registerProgressLabel">0 dari 6 terisi</strong>
                            </div>

                            <div class="register-fields">
                                <div class="input-wrap full">
                                    <label>Nama Toko / Usaha</label>
                                    <div class="field">
                                        <i class="fas fa-shop"></i>
                                        <input type="text" name="nama_toko" placeholder="Contoh: Toko Sembako Maju" required>
                                    </div>
                                </div>

                                <div class="input-wrap">
                                    <label>Nama Pemilik</label>
                                    <div class="field">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="nama_pemilik" placeholder="Nama lengkap pemilik" required>
                                    </div>
                                </div>

                                <div class="input-wrap">
                                    <label>No WhatsApp</label>
                                    <div class="field">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" name="no_wa" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>

                                <div class="input-wrap full">
                                    <label>Alamat / Blok</label>
                                    <div class="field">
                                        <i class="fas fa-location-dot"></i>
                                        <input type="text" name="alamat" placeholder="Contoh: Blok A No 1" required>
                                    </div>
                                </div>

                                <div class="input-wrap">
                                    <label>Username</label>
                                    <div class="field">
                                        <i class="fas fa-at"></i>
                                        <input type="text" name="username" placeholder="Username login toko" required>
                                    </div>
                                </div>

                                <div class="input-wrap">
                                    <label>Password</label>
                                    <div class="field">
                                        <i class="fas fa-key"></i>
                                        <input type="password" name="password" placeholder="Buat password" required>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-actions">
                                <button type="submit" id="btnSubmitRegister" class="submit-btn">Daftar Sekarang</button>
                                <button type="button" class="ghost" id="mobileGoLogin">Sudah punya akun? Login</button>
                            </div>

                            <div class="register-panel-footer">
                                <span>Proses registrasi dibuat sederhana dan cepat</span>
                                <span>Auto-progress aktif</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="nikModal" class="nik-modal" aria-hidden="true">
        <div class="nik-modal-card" role="dialog" aria-modal="true" aria-labelledby="nikModalTitle">
            <div class="nik-modal-head">
                <h3 id="nikModalTitle">Masuk Cepat dengan NIK</h3>
                <button type="button" class="nik-modal-close" id="closeNikModalBtn"><i class="fas fa-times"></i></button>
            </div>
            <div class="nik-modal-body">
                <p class="hint">Masukkan NIK 16 digit untuk masuk ke ruang penjual.</p>
                <form action="login_penjual.php" method="POST" id="nikLoginFormPenjual">
                    <input type="hidden" name="action" value="login_nik">
                    <input type="text" name="nik" placeholder="Masuk Cepat dengan NIK (16 digit)" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" required />
                    <button type="submit" id="btnSubmitNikLogin" style="width:100%;">Masuk Tanpa Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- Form Toggle Logic ---
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const mainContainer = document.getElementById('container');
        const mobileGoRegister = document.getElementById('mobileGoRegister');
        const mobileGoLogin = document.getElementById('mobileGoLogin');
        const authTabs = Array.from(document.querySelectorAll('.auth-tab'));
        const nikModal = document.getElementById('nikModal');
        const openNikModalBtn = document.getElementById('openNikModalBtn');
        const closeNikModalBtn = document.getElementById('closeNikModalBtn');
        const registerProgressFill = document.getElementById('registerProgressFill');
        const registerProgressText = document.getElementById('registerProgressText');
        const registerProgressLabel = document.getElementById('registerProgressLabel');

        function setMode(isRegister) {
            if (!mainContainer) return;
            mainContainer.classList.toggle('right-panel-active', isRegister);
            authTabs.forEach((tab) => {
                const isRegisterTab = tab.id === 'signUp';
                tab.classList.toggle('active', isRegister ? isRegisterTab : !isRegisterTab);
            });
        }

        signUpButton?.addEventListener('click', () => { setMode(true); });
        signInButton?.addEventListener('click', () => { setMode(false); });
        
        mobileGoRegister?.addEventListener('click', () => { setMode(true); });
        mobileGoLogin?.addEventListener('click', () => { setMode(false); });

        function openNikModal() {
            if (!nikModal) return;
            nikModal.classList.add('active');
            nikModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.querySelector('#nikLoginFormPenjual input[name="nik"]')?.focus(), 50);
        }

        function closeNikModal() {
            if (!nikModal) return;
            nikModal.classList.remove('active');
            nikModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        openNikModalBtn?.addEventListener('click', openNikModal);
        closeNikModalBtn?.addEventListener('click', closeNikModal);
        nikModal?.addEventListener('click', (e) => {
            if (e.target === nikModal) closeNikModal();
        });
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeNikModal();
        });

        function updateRegisterProgress() {
            const fields = Array.from(document.querySelectorAll('#registerFormPenjual input:not([type="hidden"]):not([type="submit"]):not([type="button"])'));
            const total = fields.length || 1;
            const filled = fields.filter((el) => (el.value || '').trim() !== '').length;
            const percent = Math.round((filled / total) * 100);
            if (registerProgressFill) registerProgressFill.style.width = percent + '%';
            if (registerProgressText) registerProgressText.textContent = percent + '%';
            if (registerProgressLabel) registerProgressLabel.textContent = filled + ' dari ' + total + ' terisi';
        }

        document.querySelectorAll('#registerFormPenjual input:not([type="hidden"]):not([type="submit"]):not([type="button"])').forEach((input) => {
            input.addEventListener('input', updateRegisterProgress);
            input.addEventListener('change', updateRegisterProgress);
        });
        updateRegisterProgress();

        let loadingTick;
        function showPageLoading(title) {
            const overlay = document.getElementById('smartLoadingOverlay');
            const fill = document.getElementById('smartLoadingFill');
            const pct = document.getElementById('smartLoadingPct');
            const label = document.getElementById('smartLoadingTitle');
            if (!overlay || !fill || !pct) return;
            if (label && title) label.textContent = title;
            let value = 8;
            fill.style.width = value + '%';
            pct.textContent = String(value);
            overlay.classList.add('active');
            clearInterval(loadingTick);
            loadingTick = setInterval(() => {
                value = Math.min(92, value + Math.max(1, Math.round((100 - value) / 11)));
                fill.style.width = value + '%';
                pct.textContent = String(value);
            }, 160);
        }

        document.getElementById('loginFormPenjual')?.addEventListener('submit', () => {
            const btn = document.getElementById('btnSubmitLogin');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            }
            showPageLoading('Memverifikasi akun toko...');
        });

        document.getElementById('registerFormPenjual')?.addEventListener('submit', () => {
            const btn = document.getElementById('btnSubmitRegister');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Mengirim...';
            }
            showPageLoading('Mengirim registrasi toko...');
        });

        document.getElementById('nikLoginFormPenjual')?.addEventListener('submit', () => {
            const btn = document.getElementById('btnSubmitNikLogin');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            }
            showPageLoading('Memverifikasi NIK penjual...');
        });

        // --- Alert Logic dari PHP ---
        <?php if(!empty($alertMessage)): ?>
            Swal.fire({
                icon: '<?= $alertType ?>',
                title: '<?= $alertType == "success" ? "Berhasil!" : "Pemberitahuan" ?>',
                text: '<?= $alertMessage ?>',
                confirmButtonColor: '#10b981',
                borderRadius: '1.5rem',
                customClass: { popup: 'smart-modal' }
            });
        <?php endif; ?>
    </script>
</body>
</html>