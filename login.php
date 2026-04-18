<?php
session_start();
require_once 'config/database.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: app.php");
    exit();
}

$error = "";

// Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user && $pass) {
        $stmt = $pdo->prepare("SELECT * FROM web_users WHERE username = ?");
        $stmt->execute([$user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];

            header("Location: app.php");
            exit();
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Mohon isi semua bidang.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Si-SmaRT | Portal Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/mobile-ux.css">
    <style>
        :root {
            --bg-night: #071025;
            --bg-ink: #0d1b38;
            --card: rgba(255, 255, 255, 0.92);
            --line: rgba(15, 23, 42, 0.12);
            --text: #0f172a;
            --muted: #5b6476;
            --brand: #0f766e;
            --brand-2: #14b8a6;
            --danger-bg: #fff1f2;
            --danger-text: #be123c;
        }
        * { box-sizing: border-box; }
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background:
                radial-gradient(1000px 520px at -12% -15%, rgba(20, 184, 166, 0.45), transparent 70%),
                radial-gradient(820px 500px at 108% 115%, rgba(59, 130, 246, 0.42), transparent 68%),
                linear-gradient(140deg, var(--bg-night), var(--bg-ink));
            overflow-x: hidden;
        }

        .login-screen {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 26px 16px;
        }

        .pulse-shape {
            position: fixed;
            z-index: -1;
            border-radius: 999px;
            filter: blur(90px);
            opacity: 0.42;
            animation: floatPulse 10s ease-in-out infinite alternate;
        }
        .pulse-shape.a { width: 380px; height: 380px; background: #2dd4bf; top: -120px; left: -140px; }
        .pulse-shape.b { width: 360px; height: 360px; background: #60a5fa; bottom: -120px; right: -120px; animation-delay: -2s; }

        @keyframes floatPulse {
            from { transform: translateY(-14px) scale(1); }
            to { transform: translateY(18px) scale(1.06); }
        }

        .auth-shell {
            width: min(1020px, 100%);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(20px);
            border-radius: 34px;
            padding: 12px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 450px);
            gap: 12px;
            box-shadow: 0 36px 90px -45px rgba(2, 6, 23, 0.9);
        }

        .brand-panel {
            border-radius: 28px;
            padding: 30px;
            color: #fff;
            background:
                radial-gradient(420px 220px at 15% 0%, rgba(45, 212, 191, 0.32), transparent 60%),
                radial-gradient(400px 240px at 100% 100%, rgba(59, 130, 246, 0.3), transparent 65%),
                linear-gradient(155deg, rgba(8, 47, 73, 0.84), rgba(15, 23, 42, 0.82));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 560px;
        }

        .brand-badge {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .brand-panel h1 {
            margin: 20px 0 10px;
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            letter-spacing: -0.02em;
            line-height: 1.08;
        }
        .brand-panel p { margin: 0; color: rgba(255, 255, 255, 0.84); font-size: 0.95rem; }

        .brand-pills {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .brand-pill {
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.12);
        }

        .brand-foot {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.72);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .auth-card {
            border-radius: 28px;
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 30px 75px -50px rgba(15, 23, 42, 0.8);
            padding: 26px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 560px;
        }

        .auth-top small {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #0f766e;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
        }
        .auth-top h2 {
            margin: 10px 0 6px;
            font-size: 1.9rem;
            line-height: 1.05;
            letter-spacing: -0.02em;
        }
        .auth-top p { margin: 0 0 18px; color: var(--muted); font-size: 0.92rem; }

        .error-box {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid #fecdd3;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .input-group {
            margin-bottom: 11px;
        }
        .input-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 10px;
            font-weight: 800;
            color: #536177;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .field {
            display: flex;
            align-items: center;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 16px;
            padding: 0 12px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        .field:focus-within {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
            transform: translateY(-1px);
        }
        .field i { color: #94a3b8; font-size: 14px; margin-right: 8px; }
        .field input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            padding: 14px 0;
            font-size: 14px;
            font-family: inherit;
            font-weight: 600;
            color: var(--text);
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-image: none;
        }
        .field input::placeholder { color: #94a3b8; }

        .field input:-webkit-autofill,
        .field input:-webkit-autofill:hover,
        .field input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text);
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
            box-shadow: 0 0 0 1000px #ffffff inset;
            transition: background-color 9999s ease-in-out 0s;
        }

        .utility-row {
            margin: 4px 0 12px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }
        .remember {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #4b5563;
            font-weight: 600;
        }
        .remember input { accent-color: #0f766e; }
        .utility-row a { color: #0f766e; font-weight: 700; text-decoration: none; }

        .alt-link {
            display: block;
            text-align: center;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: #334155;
            border-radius: 14px;
            padding: 12px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 14px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }
        .alt-link:hover { background: #f8fafc; border-color: #94a3b8; }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 16px;
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            box-shadow: 0 20px 36px -22px rgba(15, 118, 110, 0.75);
            cursor: pointer;
            transition: transform 0.16s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 24px 42px -24px rgba(15, 118, 110, 0.86); }
        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }

        .auth-foot {
            margin-top: 14px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }
        .auth-foot a { color: #0f766e; font-weight: 800; text-decoration: none; }

        .submit-btn { order: 2; }
        .alt-link { order: 1; }

        .legal {
            margin-top: 12px;
            text-align: center;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.72);
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        @media (max-width: 768px) {
            .login-screen { padding: 10px; }
            .auth-shell {
                grid-template-columns: 1fr;
                gap: 10px;
                border-radius: 24px;
                padding: 9px;
            }
            .brand-panel {
                min-height: auto;
                padding: 18px;
                border-radius: 18px;
            }
            .brand-panel h1 { font-size: 1.45rem; }
            .brand-foot { display: none; }
            .auth-card {
                min-height: auto;
                border-radius: 18px;
                padding: 16px;
            }
            .auth-top h2 { font-size: 1.45rem; }
            .field input { padding: 13px 0; }
            .submit-btn { padding: 12px 14px; }
            .alt-link { margin-top: 10px; margin-bottom: 8px; }
            .legal { margin-top: 8px; letter-spacing: 0.1em; }
        }

        @media (max-width: 390px) {
            .auth-card { padding: 16px 14px; }
            .brand-panel { padding: 16px 14px; }
            .auth-top p { font-size: 0.85rem; }
            .utility-row { font-size: 11px; }
        }
    </style>
    <link rel="stylesheet" href="public/css/theme-glass.css?v=20260417">
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000);
    </script>
</head>
<body>

    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Memproses...</h3>
            <p class="smart-loading-subtitle">Mohon tunggu sebentar</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>

    <div class="pulse-shape a"></div>
    <div class="pulse-shape b"></div>

    <main class="login-screen">
        <div class="auth-shell">
            <section class="brand-panel">
                <div>
                    <div class="brand-badge"><i class="fas fa-users-rectangle"></i></div>
                    <h1>Selamat Datang di Si-SmaRT</h1>
                    <p>Portal digital kawasan yang ringan, cepat, dan terasa seperti aplikasi mobile modern.</p>
                    <div class="brand-pills">
                        <span class="brand-pill">Smart Dashboard</span>
                        <span class="brand-pill">Mobile Native Feel</span>
                        <span class="brand-pill">Realtime Data</span>
                    </div>
                </div>
                <div class="brand-foot">
                    <span>RT DIGITAL EXPERIENCE</span>
                    <span>2026</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-top">
                    <small><i class="fas fa-lock"></i> Akses Internal</small>
                    <h2>Masuk ke Dashboard</h2>
                    <p>Gunakan akun petugas untuk masuk dan kelola seluruh data warga.</p>
                </div>

                <?php if($error): ?>
                <div class="error-box">
                    <i class="fas fa-circle-exclamation"></i>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form action="login.php" method="POST" id="loginFormMain">
                    <div class="input-group">
                        <label>Username</label>
                        <div class="field">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" required placeholder="Masukkan username admin">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <div class="field">
                            <i class="fas fa-key"></i>
                            <input type="password" name="password" required placeholder="Masukkan password">
                        </div>
                    </div>

                    <div class="utility-row">
                        <label class="remember">
                            <input type="checkbox">
                            Ingat Saya
                        </label>
                    </div>
                    <button type="submit" id="loginSubmitBtn" class="submit-btn">
                        <span id="loginSubmitText">Masuk Sekarang</span>
                    </button>
                    <a href="index.php" class="alt-link">
                        Lihat Portal Warga
                    </a>
                </form>

                <div class="auth-foot">
                    Belum punya akun? <a href="#">Hubungi Admin RT</a>
                </div>
            </section>
        </div>
        <p class="legal">&copy; 2026 Si-SmaRT Digital System. All Rights Reserved.</p>
    </main>
    <script>
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
                value = Math.min(92, value + Math.max(1, Math.round((100 - value) / 12)));
                fill.style.width = value + '%';
                pct.textContent = String(value);
            }, 160);
        }

        document.getElementById('loginFormMain')?.addEventListener('submit', function () {
            const btn = document.getElementById('loginSubmitBtn');
            const text = document.getElementById('loginSubmitText');
            if (btn) btn.disabled = true;
            if (text) text.textContent = 'MEMPROSES...';
            showPageLoading('Memverifikasi akun...');
        });
    </script>
</body>
</html>
