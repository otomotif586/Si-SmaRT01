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
    <title>Login Si-SmaRT | Modern Portal Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/mobile-ux.css">
    <style>
        /* Mencegah FOUC */
        html { visibility: hidden; opacity: 0; transition: opacity 0.5s ease; }
        html.js-loaded { visibility: visible; opacity: 1; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .font-space { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        .floating-bg {
            position: fixed;
            z-index: -1;
            filter: blur(100px);
            opacity: 0.5;
            border-radius: 50%;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        .input-focus {
            transition: all 0.3s ease;
        }
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.2);
        }

        @media (max-width: 768px) {
            body { padding: 14px; align-items: flex-start; }
            .w-full.max-w-\[450px\] { max-width: 380px; margin-top: 6px; }
            .text-center.mb-10 { margin-bottom: 1rem; }
            .text-center.mb-10 .w-20.h-20 { width: 62px !important; height: 62px !important; border-radius: 1rem !important; margin-bottom: 0.9rem !important; }
            .text-center.mb-10 h1 { font-size: 1.65rem !important; }
            .text-center.mb-10 p { font-size: 0.8rem !important; margin-top: 0.3rem !important; }
            .glass-container.rounded-\[2\.5rem\].p-8 { border-radius: 1.4rem !important; padding: 1rem !important; }
            .glass-container h2 { font-size: 1rem; margin-bottom: 1rem !important; }
            .space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.75rem !important; }
            #loginFormMain label { margin-bottom: 0.45rem !important; font-size: 0.63rem !important; }
            #loginFormMain input[type='text'],
            #loginFormMain input[type='password'] { min-height: 42px; padding-top: 0.7rem !important; padding-bottom: 0.7rem !important; font-size: 0.9rem; border-radius: 0.9rem !important; }
            #loginFormMain .text-sm { font-size: 0.72rem !important; }
            #loginSubmitBtn { min-height: 42px; padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; font-size: 0.75rem !important; border-radius: 0.9rem !important; }
            .mt-10.text-center { margin-top: 1rem !important; }
            .mt-10.text-center p { font-size: 0.76rem !important; }
            .text-center.text-slate-400.text-\[10px\].mt-12 { margin-top: 1rem !important; font-size: 0.58rem !important; letter-spacing: 0.16em !important; }
        }

        @media (max-width: 390px) {
            body { padding: 10px; }
            .w-full.max-w-\[450px\] { max-width: 350px; }
            .glass-container { border-radius: 1.3rem !important; }
            .text-center.mb-10 .w-20.h-20 { width: 56px !important; height: 56px !important; }
            .text-center.mb-10 h1 { font-size: 1.5rem !important; }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => { document.documentElement.classList.add("js-loaded"); });
        setTimeout(() => document.documentElement.classList.add("js-loaded"), 2000);
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Memproses...</h3>
            <p class="smart-loading-subtitle">Mohon tunggu sebentar</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>

    <!-- Background Decoration -->
    <div class="floating-bg w-96 h-96 bg-emerald-300 top-0 left-0"></div>
    <div class="floating-bg w-[500px] h-[500px] bg-blue-200 bottom-0 right-0" style="animation-delay: -5s;"></div>

    <div class="w-full max-w-[450px] relative">
        <!-- Logo / Brand -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-600 rounded-[2rem] shadow-2xl shadow-emerald-200 mb-6 group hover:rotate-6 transition-transform">
                <i class="fas fa-users-rectangle text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight font-space">Si-SmaRT</h1>
            <p class="text-slate-500 font-medium mt-2">Sistem Informasi Kawasan Mandiri</p>
        </div>

        <!-- Login Form Card -->
        <div class="glass-container rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-slate-800 mb-8">Selamat Datang Kembali!</h2>

                <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold flex items-center gap-3 animate-pulse">
                    <i class="fas fa-circle-exclamation"></i>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="space-y-6" id="loginFormMain">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Username</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="username" required 
                                class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 text-slate-800 font-bold focus:outline-none focus:border-emerald-500 input-focus placeholder-slate-300"
                                placeholder="admin">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" name="password" required 
                                class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 text-slate-800 font-bold focus:outline-none focus:border-emerald-500 input-focus placeholder-slate-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm px-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-500 font-medium">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Ingat Saya
                        </label>
                        <a href="#" class="text-emerald-600 font-bold hover:underline">Lupa Password?</a>
                    </div>

                    <a href="index.php" class="w-full block text-center border border-emerald-200 text-emerald-700 font-semibold py-3 rounded-2xl hover:bg-emerald-50 transition-all text-sm">
                        Lihat Portal Warga
                    </a>

                    <button type="submit" id="loginSubmitBtn"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-5 rounded-2xl shadow-xl shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <span id="loginSubmitText">MASUK SEKARANG</span>
                        <i class="fas fa-arrow-right-long"></i>
                    </button>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-slate-400 text-sm font-medium">Belum punya akun? <a href="#" class="text-emerald-600 font-bold">Hubungi Admin RT</a></p>
                </div>
            </div>
            
            <!-- Decorative circle -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/5 rounded-full z-0"></div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-slate-400 text-[10px] mt-12 font-bold uppercase tracking-[0.2em]">
            &copy; 2026 Si-SmaRT Digital System. All Rights Reserved.
        </p>
    </div>
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
