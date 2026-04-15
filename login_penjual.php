<?php
session_start();
require_once 'config/database.php';

// Jika sudah login sebagai penjual, lempar ke ruang_penjual
if (isset($_SESSION['penjual_id'])) {
    header("Location: ruang_penjual.php");
    exit();
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal UMKM Warga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/mobile-ux.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:300,400,600,700&display=swap');

        :root {
            --primary-gradient: linear-gradient(to right, #10b981 0%, #059669 100%); /* Diubah ke Emerald hijau agar senada */
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: 1px solid rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        h1 { font-weight: 700; margin: 0; color: #333; }
        h1.white-text { color: #fff; }
        
        p { font-size: 14px; font-weight: 300; line-height: 20px; letter-spacing: 0.5px; margin: 20px 0 30px; color: #eee; }
        span { font-size: 12px; color: #666; margin-bottom: 10px; }
        a { color: #333; font-size: 14px; text-decoration: none; margin: 15px 0; transition: color 0.3s; }
        a:hover { color: #10b981; }

        button {
            border-radius: 50px;
            border: none;
            background: var(--primary-gradient);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in, box-shadow 0.3s;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        button:hover { box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); }
        button:active { transform: scale(0.95); }
        button:focus { outline: none; }
        button.ghost { background: transparent; border: 2px solid #FFFFFF; box-shadow: none; }

        form {
            background-color: rgba(255, 255, 255, 0.95);
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            padding: 0 50px; height: 100%; text-align: center;
        }

        input {
            background-color: transparent;
            border: none; border-bottom: 2px solid #ddd;
            padding: 12px 15px; margin: 8px 0; width: 100%;
            font-family: 'Poppins', sans-serif; transition: border-color 0.3s;
        }
        input:focus { outline: none; border-bottom-color: #10b981; }
        input::placeholder { color: #aaa; }

        .container {
            background: var(--glass-bg);
            box-shadow: var(--glass-shadow);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: var(--glass-border);
            border-radius: 20px;
            position: relative; overflow: hidden;
            width: 850px; max-width: 90%; min-height: 550px; z-index: 10;
        }

        .form-container { position: absolute; top: 0; height: 100%; transition: all 0.6s ease-in-out; }
        .sign-in-container { left: 0; width: 50%; z-index: 2; }
        .container.right-panel-active .sign-in-container { transform: translateX(100%); }
        
        .sign-up-container { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .container.right-panel-active .sign-up-container { transform: translateX(100%); opacity: 1; z-index: 5; animation: show 0.6s; }

        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }

        .overlay-container {
            position: absolute; top: 0; left: 50%; width: 50%; height: 100%;
            overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 100;
        }
        .container.right-panel-active .overlay-container { transform: translateX(-100%); }

        .overlay {
            background: var(--primary-gradient);
            background-repeat: no-repeat; background-size: cover; background-position: 0 0;
            color: #FFFFFF; position: relative; left: -100%; height: 100%; width: 200%;
            transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay { transform: translateX(50%); }

        .overlay-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 40px; text-align: center; top: 0;
            height: 100%; width: 50%; transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .overlay-left { transform: translateX(-20%); }
        .container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .container.right-panel-active .overlay-right { transform: translateX(20%); }

        #canvas1 {
            position: absolute; top: 0; left: 0; width: 100%; height: 100vh; z-index: -1;
            background: #ece9e6; background: linear-gradient(to right, #ece9e6, #ffffff);
        }
        
        .close-btn {
            position: absolute; top: 20px; right: 20px; width: 40px; height: 40px;
            background: #fff; color: #666; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer;
        }
        .close-btn:hover { background: #ef4444; color: #fff; }

        .mobile-switch { display: none; }
        @media (max-width: 768px) {
            .form-container { width: 100%; transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out; }
            .sign-in-container { z-index: 10; opacity: 1; }
            .container.right-panel-active .sign-in-container { transform: translateX(-100%); opacity: 0; z-index: 1; }
            .sign-up-container { z-index: 1; transform: translateX(100%); opacity: 0; }
            .container.right-panel-active .sign-up-container { transform: translateX(0); opacity: 1; z-index: 10; }
            .overlay-container { display: none; }
            form { padding: 0 30px; }
            .mobile-switch { display: block; margin-top: 15px; font-size: 13px; color: #10b981; font-weight: bold; cursor: pointer; text-decoration: underline; }
        }

        @media (max-width: 390px) {
            body {
                height: auto;
                min-height: 100dvh;
                overflow-y: auto;
                justify-content: flex-start;
                padding: 10px 0;
            }
            .close-btn {
                top: 10px;
                right: 10px;
                width: 34px;
                height: 34px;
                font-size: 12px;
            }
            .container {
                width: calc(100% - 12px);
                max-width: none;
                min-height: 620px;
                border-radius: 14px;
                margin-top: 40px;
                margin-bottom: 10px;
            }
            form {
                padding: 0 16px;
            }
            h1 {
                font-size: 1.15rem;
            }
            span {
                font-size: 11px;
            }
            input {
                margin: 6px 0;
                padding: 10px 10px;
                font-size: 13px;
            }
            button {
                width: 100%;
                padding: 11px 14px;
                font-size: 11px;
                letter-spacing: 0.5px;
            }
            .mobile-switch {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <canvas id="canvas1"></canvas>

    <div id="smartLoadingOverlay" class="smart-loading-overlay">
        <div class="smart-loading-card">
            <h3 class="smart-loading-title" id="smartLoadingTitle">Memproses...</h3>
            <p class="smart-loading-subtitle">Menyiapkan akun Anda</p>
            <div class="smart-loading-track"><div class="smart-loading-fill" id="smartLoadingFill"></div></div>
            <div class="smart-loading-meta"><span id="smartLoadingPct">8</span>%</div>
        </div>
    </div>
    
    <a href="pasar.php" class="close-btn" title="Kembali ke Pasar"><i class="fas fa-times"></i></a>

    <div class="container <?= $isRightPanelActive ? 'right-panel-active' : '' ?>" id="container">
        
        <!-- SIGN UP FORM -->
        <div class="form-container sign-up-container">
            <form action="login_penjual.php" method="POST" id="registerFormPenjual">
                <input type="hidden" name="action" value="register">
                <h1 style="font-size: 1.5rem; margin-bottom: 5px;">Buka Toko Baru</h1>
                <span style="margin-bottom: 10px;">Daftarkan UMKM ke Pengurus RT</span>
                <div style="width: 100%; display: flex; flex-direction: column; gap: 4px;">
                    <input type="text" name="nama_toko" placeholder="Nama Toko / Usaha" required style="padding: 10px 15px; margin: 0; font-size: 13px;" />
                    <input type="text" name="nama_pemilik" placeholder="Nama Pemilik" required style="padding: 10px 15px; margin: 0; font-size: 13px;" />
                    <input type="text" name="no_wa" placeholder="No. WhatsApp (Aktif)" required style="padding: 10px 15px; margin: 0; font-size: 13px;" />
                    <input type="text" name="alamat" placeholder="Alamat / Blok (Cth: Blok A No 1)" required style="padding: 10px 15px; margin: 0; font-size: 13px;" />
                    <div style="display: flex; gap: 8px; width: 100%;">
                        <input type="text" name="username" placeholder="Username" required style="padding: 10px 15px; margin: 0; font-size: 13px; flex: 1;" />
                        <input type="password" name="password" placeholder="Password" required style="padding: 10px 15px; margin: 0; font-size: 13px; flex: 1;" />
                    </div>
                </div>
                <button type="submit" style="margin-top: 15px;" id="btnSubmitRegister">Daftar Sekarang</button>
                <span class="mobile-switch" id="mobileSignIn">Sudah Punya Akun? Login</span>
            </form>
        </div>

        <!-- SIGN IN FORM -->
        <div class="form-container sign-in-container">
            <form action="login_penjual.php" method="POST" id="loginFormPenjual">
                <input type="hidden" name="action" value="login">
                <div style="font-size: 40px; color: #10b981; margin-bottom: 10px;"><i class="fas fa-store"></i></div>
                <h1>Login UMKM</h1>
                <span>Gunakan akun yang telah disetujui</span>
                <input type="text" name="username" placeholder="Username Toko" required />
                <input type="password" name="password" placeholder="Password" required />
                <a href="#">Lupa Password? Hubungi RT</a>
                <button type="submit" id="btnSubmitLogin">Masuk Ruang Penjual</button>
                <span class="mobile-switch" id="mobileSignUp">Buka Toko Baru? Daftar</span>
            </form>

            <form action="login_penjual.php" method="POST" id="nikLoginFormPenjual" style="padding: 0 50px; background: transparent; height: auto; position: absolute; left: 0; width: 100%; bottom: 34px; text-align: center;">
                <input type="hidden" name="action" value="login_nik">
                <div style="width:100%; border-top:1px dashed #d1d5db; padding-top:12px;">
                    <input type="text" name="nik" placeholder="Masuk Cepat dengan NIK (16 digit)" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" required style="margin: 0 0 8px 0;" />
                    <button type="submit" id="btnSubmitNikLogin" style="width:100%;">Masuk Tanpa Password</button>
                </div>
            </form>
        </div>

        <!-- OVERLAY PANELS -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 class="white-text">Sudah Punya Akun?</h1>
                    <p>Silakan login untuk mulai mengelola produk jualan dan pesanan Anda.</p>
                    <button class="ghost" id="signIn">Masuk Form Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1 class="white-text">Warga Buka Usaha?</h1>
                    <p>Ayo bergabung bersama pasar digital warga dan perluas jangkauan pembeli Anda.</p>
                    <button class="ghost" id="signUp">Daftar Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Form Toggle Logic ---
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const mainContainer = document.getElementById('container');

        signUpButton.addEventListener('click', () => { mainContainer.classList.add("right-panel-active"); });
        signInButton.addEventListener('click', () => { mainContainer.classList.remove("right-panel-active"); });
        
        const mSignUp = document.getElementById('mobileSignUp');
        const mSignIn = document.getElementById('mobileSignIn');
        if(mSignUp) mSignUp.addEventListener('click', () => { mainContainer.classList.add("right-panel-active"); });
        if(mSignIn) mSignIn.addEventListener('click', () => { mainContainer.classList.remove("right-panel-active"); });

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
        <?php if($alertMessage): ?>
            Swal.fire({
                icon: '<?= $alertType ?>',
                title: '<?= $alertType == "success" ? "Berhasil!" : "Pemberitahuan" ?>',
                text: '<?= $alertMessage ?>',
                confirmButtonColor: '#10b981',
                borderRadius: '1.5rem',
                customClass: { popup: 'smart-modal' }
            });
        <?php endif; ?>

        // --- Background Particle Effect Logic ---
        const canvas = document.getElementById("canvas1");
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let particlesArray;
        let mouse = { x: null, y: null, radius: (canvas.height / 80) * (canvas.width / 80) }

        window.addEventListener('mousemove', function (event) { mouse.x = event.x; mouse.y = event.y; });
        window.addEventListener('resize', function () {
            canvas.width = innerWidth; canvas.height = innerHeight;
            mouse.radius = ((canvas.height / 80) * (canvas.height / 80));
            init();
        });
        window.addEventListener('mouseout', function () { mouse.x = undefined; mouse.y = undefined; });

        class Particle {
            constructor(x, y, directionX, directionY, size, color) {
                this.x = x; this.y = y; this.directionX = directionX; this.directionY = directionY;
                this.size = size; this.color = color;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
                ctx.fillStyle = '#8E9EAB';
                ctx.fill();
            }
            update() {
                if (this.x > canvas.width || this.x < 0) this.directionX = -this.directionX;
                if (this.y > canvas.height || this.y < 0) this.directionY = -this.directionY;

                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < mouse.radius + this.size) {
                    if (mouse.x < this.x && this.x < canvas.width - this.size * 10) this.x += 3;
                    if (mouse.x > this.x && this.x > this.size * 10) this.x -= 3;
                    if (mouse.y < this.y && this.y < canvas.height - this.size * 10) this.y += 3;
                    if (mouse.y > this.y && this.y > this.size * 10) this.y -= 3;
                }
                this.x += this.directionX; this.y += this.directionY;
                this.draw();
            }
        }

        function init() {
            particlesArray = [];
            let numberOfParticles = (canvas.height * canvas.width) / 9000;
            for (let i = 0; i < numberOfParticles * 2; i++) {
                let size = (Math.random() * 3) + 1;
                let x = (Math.random() * ((innerWidth - size * 2) - (size * 2)) + size * 2);
                let y = (Math.random() * ((innerHeight - size * 2) - (size * 2)) + size * 2);
                let directionX = (Math.random() * 2) - 1;
                let directionY = (Math.random() * 2) - 1;
                let color = '#8E9EAB';
                particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
            }
        }

        function connect() {
            let opacityValue = 1;
            for (let a = 0; a < particlesArray.length; a++) {
                for (let b = a; b < particlesArray.length; b++) {
                    let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x))
                        + ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));
                    if (distance < (canvas.width / 7) * (canvas.height / 7)) {
                        opacityValue = 1 - (distance / 20000);
                        ctx.strokeStyle = 'rgba(142, 158, 171,' + opacityValue + ')';
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                        ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            requestAnimationFrame(animate);
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            for (let i = 0; i < particlesArray.length; i++) { particlesArray[i].update(); }
            connect();
        }

        init();
        animate();
    </script>
</body>
</html>