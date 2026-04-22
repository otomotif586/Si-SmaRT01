<?php require_once __DIR__ . '/../../config/asset_url.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Ruang Warga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= htmlspecialchars(smart_asset('public/css/ruang-warga-standalone.css', 'auto'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="rw-fintech-theme">
<div id="rwBootLoader" class="rw-boot-loader" aria-hidden="true">
    <div class="rw-boot-card">
        <div class="rw-boot-logo"><i class="fas fa-house-user"></i></div>
        <h3>Ruang Warga</h3>
        <p>Menyiapkan dashboard personal Anda...</p>
        <div class="rw-boot-track"><span id="rwBootFill"></span></div>
    </div>
</div>

<div class="wrap app-shell">
    <div class="card topbar">
        <a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Kembali ke Portal</a>
        <div class="topbar-actions">
            <a href="pasar.php" class="btn">Pasar Warga</a>
            <a href="ruang_warga.php" class="btn">Ruang Warga</a>
            <?php if ($isLoggedIn): ?>
                <a href="ruang_penjual.php" class="btn primary">Ruang Penjual</a>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="logout">
                    <button class="btn danger" type="submit">Keluar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isLoggedIn): ?>
        <div class="rw-auth-shell">
            <div class="rw-auth-hero">
                <p class="rw-auth-kicker"><i class="fas fa-house-user"></i> Ruang Warga</p>
                <h2>Akses Cepat </h2>
                <p>Masuk cukup pakai NIK 16 digit. Registrasi warga baru juga bisa langsung dari halaman ini.</p>
                <div class="rw-auth-tags">
                    <span>Tanpa Password</span>
                    <span>Mobile Friendly</span>
                    <span>Realtime Sinkron</span>
                </div>
            </div>

            <div class="grid2 rw-auth-grid">
                <div class="card section rw-auth-card">
                    <h2>Login Ruang Warga</h2>
                    <p class="muted">Masukkan NIK 16 digit untuk langsung masuk.</p>
                    <form method="POST" class="form-grid rw-auth-form">
                        <input type="hidden" name="action" value="login">
                        <div class="form-group full">
                            <label>NIK</label>
                            <input name="nik" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" placeholder="Contoh: 3276xxxxxxxxxxxx" required>
                        </div>
                        <div class="form-group full">
                            <button type="submit" class="btn primary rw-auth-btn">Masuk</button>
                        </div>
                    </form>
                </div>

                <div class="card section rw-auth-card rw-auth-card-register">
                    <h2>Pendaftaran Warga</h2>
                    <p class="muted">Jika NIK sudah terdaftar, sistem akan menolak otomatis.</p>
                    <form method="POST" class="form-grid rw-auth-form">
                        <input type="hidden" name="action" value="register">
                        <div class="form-group"><label>NIK</label><input name="nik" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" placeholder="16 digit NIK" required></div>
                        <div class="form-group"><label>Nama</label><input name="nama" placeholder="Nama lengkap" required></div>
                        <div class="form-group"><label>Blok</label>
                            <select name="blok_id" required>
                                <option value="">Pilih Blok</option>
                                <?php foreach ($blokOptions as $bo): ?>
                                    <option value="<?= (int)($bo['id'] ?? 0) ?>"><?= htmlspecialchars((string)($bo['nama_blok'] ?? '-')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group"><label>No Rumah (2 Digit)</label><input name="no_rumah" maxlength="2" inputmode="numeric" pattern="[0-9]{2}" placeholder="Contoh: 01" required></div>
                        <div class="form-group"><label>No WA</label><input name="no_wa" inputmode="tel" placeholder="08xxxxxxxxxx" required></div>
                        <div class="form-group full"><button type="submit" class="btn primary rw-auth-btn">Daftar dan Masuk</button></div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php
            $missingDataFields = [];
            $profileCompletion = 0;
            $profileFilled = 0;
            $profileTotal = 0;
            $overdueRows = [];
            $overdueCount = 0;
            $overdueNominal = 0;
            if ($linkedWarga) {
                $requiredMap = [
                    'nik' => 'NIK',
                    'nama_lengkap' => 'Nama Lengkap',
                    'nomor_rumah' => 'Nomor Rumah',
                    'no_wa' => 'No WA',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'status_kependudukan' => 'Status Kependudukan'
                ];

                foreach ($requiredMap as $key => $label) {
                    $profileTotal++;
                    if (trim((string)($linkedWarga[$key] ?? '')) === '') {
                        $missingDataFields[] = $label;
                    } else {
                        $profileFilled++;
                    }
                }

                if (($linkedWarga['status_pernikahan'] ?? '') === 'Menikah') {
                    $profileTotal++;
                }
                if (($linkedWarga['status_pernikahan'] ?? '') === 'Menikah' && !$pasangan) {
                    $missingDataFields[] = 'Data Pasangan';
                } elseif (($linkedWarga['status_pernikahan'] ?? '') === 'Menikah' && $pasangan) {
                    $profileFilled++;
                }
            }
            $isProfileIncomplete = !empty($missingDataFields);
            if ($profileTotal > 0) {
                $profileCompletion = (int)round(($profileFilled / $profileTotal) * 100);
            }

            $profileBadgeClass = 'badge-green';
            if ($profileCompletion < 60) {
                $profileBadgeClass = 'badge-red';
            } elseif ($profileCompletion < 90) {
                $profileBadgeClass = 'badge-yellow';
            }

            if (!empty($historyRows)) {
                foreach ($historyRows as $hRow) {
                    $status = strtoupper(trim((string)($hRow['status'] ?? '')));
                    if ($status === 'MENUNGGAK' || $status === 'TUNGGAK') {
                        $overdueRows[] = $hRow;
                        $overdueNominal += (float)($hRow['total_tagihan'] ?? 0);
                    }
                }
            }
            $overdueCount = count($overdueRows);
            $paidCount = max(0, count((array)$historyRows) - $overdueCount);
            $totalPeriods = max(1, count((array)$historyRows));
            $iuranProgress = (int)round(($paidCount / $totalPeriods) * 100);
            $avatarPath = trim((string)($account['avatar'] ?? ''));
            $avatarUrl = $avatarPath !== '' ? $avatarPath : '';
        ?>
        <div class="rw-greeting-header card">
            <div>
                <p class="rw-greeting-eyebrow">Selamat Datang</p>
                <h1 class="rw-greeting-name">Halo, <?= htmlspecialchars((string)($account['nama'] ?? 'Warga')) ?></h1>
            </div>
            <div class="rw-profile-avatar" id="wargaAvatarHero">
                <div class="warga-avatar-large hero-avatar-core">
                    <?php if ($avatarUrl !== ''): ?>
                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar Warga">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <?php if ($linkedWarga): ?>
                    <span class="hero-profile-badge <?= htmlspecialchars($profileBadgeClass) ?>" aria-label="Kelengkapan profil <?= $profileCompletion ?> persen"><?= $profileCompletion ?>%</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="rw-primary-card card">
            <div class="rw-primary-head">
                <p class="rw-primary-label">Status Akun Warga</p>
                <h2><?= htmlspecialchars((string)($account['nama'] ?? 'Warga')) ?></h2>
            </div>
            <div class="rw-primary-info">
                <div class="rw-primary-item">
                    <span>Blok & Rumah</span>
                    <strong><?= htmlspecialchars((string)($linkedWarga['nama_blok'] ?? '-')) ?> / <?= htmlspecialchars((string)($linkedWarga['nomor_rumah'] ?? ($account['no_rumah'] ?? '-'))) ?></strong>
                </div>
                <div class="rw-primary-item">
                    <span>Status</span>
                    <strong><?= htmlspecialchars((string)($linkedWarga['status_kependudukan'] ?? 'Belum terhubung')) ?></strong>
                </div>
                <div class="rw-primary-item">
                    <span>Lunas</span>
                    <strong><?= (int)$paidCount ?> bulan</strong>
                </div>
                <div class="rw-primary-item">
                    <span>Tunggakan</span>
                    <strong><?= (int)$overdueCount ?> bulan</strong>
                </div>
            </div>
            <div class="rw-primary-progress-wrap">
                <div class="rw-primary-progress-head">
                    <span>Kepatuhan Iuran</span>
                    <strong><?= (int)$iuranProgress ?>%</strong>
                </div>
                <div class="rw-primary-progress-track" role="progressbar" aria-valuenow="<?= (int)$iuranProgress ?>" aria-valuemin="0" aria-valuemax="100">
                    <span class="rw-primary-progress-fill" style="width: <?= (int)$iuranProgress ?>%"></span>
                </div>
            </div>
        </div>

        <div class="rw-action-grid">
            <button class="rw-action-btn" type="button" data-go-tab="history"><i class="fas fa-credit-card"></i><span>Bayar Iuran</span></button>
            <button class="rw-action-btn" type="button" data-go-tab="history"><i class="fas fa-clock-rotate-left"></i><span>Cek History</span></button>
            <button class="rw-action-btn" type="button" data-go-tab="aduan" data-open-modal="rwAduanModal"><i class="fas fa-paper-plane"></i><span>Ajukan Aduan</span></button>
            <button class="rw-action-btn" type="button" data-go-tab="ringkasan"><i class="fas fa-bullhorn"></i><span>Lihat Info</span></button>
        </div>

        <?php if ($linkedWarga): ?>
            <div class="card section profile-progress-card">
                <div class="progress-head">
                    <h2><i class="fas fa-user-check"></i> Progress Kelengkapan Data Diri</h2>
                    <strong><?= $profileCompletion ?>%</strong>
                </div>
                <div class="progress-track" role="progressbar" aria-valuenow="<?= $profileCompletion ?>" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: <?= $profileCompletion ?>%"></span>
                </div>
                <p class="muted">Terisi <?= $profileFilled ?> dari <?= $profileTotal ?> komponen data utama.</p>
            </div>
        <?php endif; ?>

        <?php if ($linkedWarga && $isProfileIncomplete): ?>
            <div class="card section incomplete-alert">
                <h2><i class="fas fa-triangle-exclamation"></i> Data diri Anda belum lengkap</h2>
                <p class="muted">
                    Beberapa data masih kosong: <?= htmlspecialchars(implode(', ', array_slice($missingDataFields, 0, 5))) ?><?= count($missingDataFields) > 5 ? ', dan lainnya' : '' ?>.
                </p>
                <a href="#" class="btn primary" data-go-tab="profil-lengkap"><i class="fas fa-pen-to-square"></i> Lengkapi Data Diri Sekarang</a>
            </div>
        <?php endif; ?>

        <div class="card tabs" id="rwTabs">
            <button class="tab-btn active" data-tab="ringkasan"><i class="fas fa-house"></i> Ringkasan</button>
            <button class="tab-btn" data-tab="profil-lengkap"><i class="fas fa-id-card"></i> Data Diri Lengkap</button>
            <button class="tab-btn" data-tab="history"><i class="fas fa-clock-rotate-left"></i> History Iuran</button>
            <button class="tab-btn" data-tab="aduan"><i class="fas fa-paper-plane"></i> Aduan</button>
        </div>

        <div class="tab-panel active" data-panel="ringkasan">
            <div class="card section rw-wizard-head">
                <div class="rw-wizard-top">
                    <h2>Proses Pengajuan Layanan Warga</h2>
                    <span>1 dari 3</span>
                </div>
                <div class="rw-wizard-track" aria-hidden="true"><span style="width:34%"></span></div>
                <p class="muted">Ikuti alur ringkas untuk cek status akun, data warga, dan tindakan berikutnya.</p>
            </div>

            <div class="card section rw-process-flow">
                <div class="rw-process-item">
                    <span><i class="fas fa-circle-check"></i></span>
                    <p>Akun NIK terverifikasi dan siap digunakan untuk seluruh fitur Ruang Warga.</p>
                </div>
                <div class="rw-process-item">
                    <span><i class="fas fa-id-card"></i></span>
                    <p>Sinkronisasi data warga dilakukan otomatis agar histori iuran terbaca akurat.</p>
                </div>
                <div class="rw-process-item">
                    <span><i class="fas fa-receipt"></i></span>
                    <p>Pantau status iuran lalu lanjutkan pembayaran jika ada tunggakan aktif.</p>
                </div>
                <div class="rw-process-item">
                    <span><i class="fas fa-headset"></i></span>
                    <p>Jika butuh bantuan, kirim aduan dan pengurus akan menindaklanjuti.</p>
                </div>
            </div>

            <div class="card section pantau-info-section">
                <div class="pantau-info-head">
                    <h2><i class="fas fa-bullhorn"></i> Pantau Informasi</h2>
                    <span class="pantau-info-chip">RT 001</span>
                </div>
                <p>
                    Transparansi pelaporan informasi terkini di RT 001, untuk meningkatkan kepedulian lingkungan khususnya RT 001.
                </p>
                <?php if (!empty($pantauInfoRows)): ?>
                    <div class="pantau-info-mini-list">
                        <?php foreach (array_slice($pantauInfoRows, 0, 3) as $mini): ?>
                            <?php $miniTime = trim((string)($mini['waktu_kejadian'] ?? '')); ?>
                            <a href="<?= htmlspecialchars($portalPantauUrl) ?>" class="pantau-info-mini-item" target="_blank" rel="noopener">
                                <strong><?= htmlspecialchars((string)($mini['judul'] ?? 'Laporan Lingkungan')) ?></strong>
                                <span><?= htmlspecialchars($miniTime !== '' ? (string)date('d M Y, H:i', strtotime($miniTime)) : '-') ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid2">
                <div class="card section card-glow">
                    <h2>Informasi Akun</h2>
                    <p class="muted">Akun ini dipakai login NIK dan sinkron ke akun penjual.</p>
                    <div class="account-avatar-inline" id="wargaAvatarInline">
                        <?php if ($avatarUrl !== ''): ?>
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar Warga">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="form-grid">
                        <div class="form-group"><label>NIK</label><input value="<?= htmlspecialchars($account['nik']) ?>" readonly></div>
                        <div class="form-group"><label>Nama</label><input value="<?= htmlspecialchars($account['nama']) ?>" readonly></div>
                        <div class="form-group"><label>No Rumah</label><input value="<?= htmlspecialchars($account['no_rumah']) ?>" readonly></div>
                        <div class="form-group"><label>No WA</label><input value="<?= htmlspecialchars($account['no_wa']) ?>" readonly></div>
                    </div>
                </div>
                <div class="card section card-glow">
                    <h2>Koneksi Data Warga</h2>
                    <?php if (!$linkedWarga): ?>
                        <div class="empty">Data warga belum tersedia. Silakan lengkapi data diri di menu Data Diri Lengkap.</div>
                        <div style="margin-top:10px;">
                            <a href="#" class="btn" data-go-tab="profil-lengkap"><i class="fas fa-arrow-up-right-from-square"></i> Buka Form Data Diri Lengkap</a>
                        </div>
                    <?php else: ?>
                        <div class="form-grid">
                            <div class="form-group"><label>Nama Blok</label><input value="<?= htmlspecialchars((string)$linkedWarga['nama_blok']) ?>" readonly></div>
                            <div class="form-group"><label>Status</label><input value="<?= htmlspecialchars((string)$linkedWarga['status_kependudukan']) ?>" readonly></div>
                            <div class="form-group"><label>Tempat Lahir</label><input value="<?= htmlspecialchars((string)$linkedWarga['tempat_lahir']) ?>" readonly></div>
                            <div class="form-group"><label>Tanggal Lahir</label><input value="<?= htmlspecialchars((string)$linkedWarga['tanggal_lahir']) ?>" readonly></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid2 ringkasan-grid-extra" style="margin-top:12px;">
                <div class="card section reminder-card <?= $overdueCount > 0 ? 'is-warning' : 'is-safe' ?>">
                    <h2>
                        <i class="fas <?= $overdueCount > 0 ? 'fa-bell' : 'fa-circle-check' ?>"></i>
                        Pengingat Iuran
                    </h2>
                    <?php if ($overdueCount > 0): ?>
                        <p class="muted">
                            Anda memiliki <strong><?= (int)$overdueCount ?></strong> periode iuran menunggak dengan estimasi total
                            <strong>Rp <?= number_format($overdueNominal, 0, ',', '.') ?></strong>.
                        </p>
                        <div class="reminder-actions">
                            <a href="#" class="btn primary" data-go-tab="history"><i class="fas fa-clock-rotate-left"></i> Lihat Rincian Tunggakan</a>
                            <a href="#" class="btn" data-go-tab="aduan"><i class="fas fa-paper-plane"></i> Butuh Bantuan Pengurus</a>
                        </div>
                    <?php else: ?>
                        <p class="muted">Tidak ada tunggakan aktif. Pertahankan pembayaran iuran tepat waktu.</p>
                    <?php endif; ?>
                </div>

                <div class="card section info-important-card">
                    <h2><i class="fas fa-bullhorn"></i> Pantau Informasi Terbaru</h2>
                    <?php if (empty($pantauInfoRows)): ?>
                        <div class="empty">Belum ada data laporan pada Pantau Informasi portal.</div>
                        <div class="reminder-actions" style="margin-top:10px;">
                            <a href="<?= htmlspecialchars($portalPantauUrl) ?>" class="btn" target="_blank" rel="noopener"><i class="fas fa-arrow-up-right-from-square"></i> Buka Pantau Informasi Portal</a>
                        </div>
                    <?php else: ?>
                        <div class="info-important-list">
                            <?php foreach ($pantauInfoRows as $info): ?>
                                <?php
                                    $statusText = strtoupper(trim((string)($info['status'] ?? 'INFO')));
                                    $statusClass = 'chip-safe';
                                    if (stripos($statusText, 'PROSES') !== false || stripos($statusText, 'TINDAK') !== false) {
                                        $statusClass = 'chip-process';
                                    } elseif (stripos($statusText, 'TUNGGU') !== false || stripos($statusText, 'PENDING') !== false || stripos($statusText, 'BARU') !== false) {
                                        $statusClass = 'chip-pending';
                                    }
                                    $infoTime = trim((string)($info['waktu_kejadian'] ?? ''));
                                    $desc = trim((string)($info['deskripsi'] ?? ''));
                                ?>
                                <a href="<?= htmlspecialchars($portalPantauUrl) ?>" class="info-important-item info-link" target="_blank" rel="noopener">
                                    <div class="info-important-top">
                                        <span class="info-chip <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusText !== '' ? $statusText : 'INFO') ?></span>
                                        <time><?= htmlspecialchars($infoTime !== '' ? (string)date('d M Y, H:i', strtotime($infoTime)) : '-') ?></time>
                                    </div>
                                    <h4><?= htmlspecialchars((string)($info['judul'] ?? 'Laporan Lingkungan')) ?></h4>
                                    <p><?= htmlspecialchars($desc !== '' ? $desc : 'Klik untuk melihat detail lengkap di Pantau Informasi portal.') ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="reminder-actions" style="margin-top:10px;">
                            <a href="<?= htmlspecialchars($portalPantauUrl) ?>" class="btn primary" target="_blank" rel="noopener"><i class="fas fa-up-right-from-square"></i> Lihat Semua Laporan Pantau Informasi</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card section rw-panel-cta">
                <p>Selesaikan langkah berikutnya untuk menjaga status akun tetap aktif dan iuran tetap aman.</p>
                <a href="#" class="btn primary" data-go-tab="history"><i class="fas fa-arrow-right"></i> Lanjutkan ke History Iuran</a>
            </div>
        </div>

        <div class="tab-panel" data-panel="profil-lengkap">
            <div class="card section">
                <h2>Perbarui Data Diri Lengkap</h2>
                <p class="muted">Lengkapi data utama, data keluarga, kendaraan, dan dokumen pendukung Anda.</p>
                <?php if (!$linkedWarga): ?>
                    <div class="empty">Data warga belum tersedia. Isi form ini untuk membuat dan melengkapi data diri Anda.</div>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data" class="form-grid rw-profile-form" id="formWargaLengkap">
                        <input type="hidden" name="action" value="update_warga_full">

                        <div class="form-group full rw-profile-wizard-head">
                            <div class="rw-profile-wizard-title">
                                <strong>Lengkapi Data Diri</strong>
                                <small>Isi bertahap agar lebih nyaman di HP maupun PC.</small>
                            </div>
                            <div class="rw-profile-progress-track"><span class="rw-profile-progress-fill" style="width: 33%"></span></div>
                            <div class="rw-profile-steps">
                                <button class="rw-profile-step-dot active current" type="button">Data Utama</button>
                                <button class="rw-profile-step-dot" type="button">Keluarga</button>
                                <button class="rw-profile-step-dot" type="button">Aset & Dokumen</button>
                            </div>
                        </div>

                        <section class="rw-profile-step active" data-step="1">
                            <div class="form-group full">
                                <label>Icon Warga (Avatar)</label>
                                <div class="avatar-uploader-wrap">
                                    <div class="avatar-preview" id="avatarPreviewCard">
                                        <?php if ($avatarUrl !== ''): ?>
                                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar Warga">
                                        <?php else: ?>
                                            <i class="fas fa-user"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="avatar-uploader-field">
                                        <input type="file" name="avatar" id="avatarInput" accept="image/png,image/jpeg,image/webp">
                                        <small class="muted">Upload JPG/PNG/WEBP maksimal 2MB. Jika tidak diupload, akan memakai icon default.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group"><label>Nama Lengkap</label><input name="nama_lengkap" value="<?= htmlspecialchars((string)$linkedWarga['nama_lengkap']) ?>" required></div>
                            <div class="form-group"><label>NIK</label><input name="nik" maxlength="16" inputmode="numeric" value="<?= htmlspecialchars((string)$linkedWarga['nik']) ?>"></div>
                            <div class="form-group"><label>NIK Kepala</label><input name="nik_kepala" maxlength="16" inputmode="numeric" value="<?= htmlspecialchars((string)$linkedWarga['nik_kepala']) ?>"></div>
                            <div class="form-group"><label>Blok</label>
                                <select name="blok_id" required>
                                    <option value="">Pilih Blok</option>
                                    <?php foreach ($blokOptions as $bo): ?>
                                        <option value="<?= (int)($bo['id'] ?? 0) ?>" <?= ((int)($linkedWarga['blok_id'] ?? 0) === (int)($bo['id'] ?? 0)) ? 'selected' : '' ?>><?= htmlspecialchars((string)($bo['nama_blok'] ?? '-')) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>No Rumah (2 Digit)</label><input name="nomor_rumah" maxlength="2" inputmode="numeric" pattern="[0-9]{2}" value="<?= htmlspecialchars((string)substr(preg_replace('/\D+/', '', (string)($linkedWarga['nomor_rumah'] ?? '')), -2)) ?>"></div>
                            <div class="form-group"><label>No WA</label><input name="no_wa" inputmode="tel" value="<?= htmlspecialchars((string)$linkedWarga['no_wa']) ?>"></div>
                            <div class="form-group"><label>Tempat Lahir</label><input name="tempat_lahir" value="<?= htmlspecialchars((string)$linkedWarga['tempat_lahir']) ?>"></div>
                            <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="<?= htmlspecialchars((string)$linkedWarga['tanggal_lahir']) ?>"></div>
                            <div class="form-group"><label>Status Pernikahan</label>
                                <select name="status_pernikahan" id="statusPernikahan">
                                    <?php foreach ($statusOptions as $o): ?>
                                        <option value="<?= htmlspecialchars($o) ?>" <?= (($linkedWarga['status_pernikahan'] ?? '') === $o) ? 'selected' : '' ?>><?= htmlspecialchars($o) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Status Kependudukan</label>
                                <select name="status_kependudukan">
                                    <?php foreach ($kependudukanOptions as $o): ?>
                                        <option value="<?= htmlspecialchars($o) ?>" <?= (($linkedWarga['status_kependudukan'] ?? '') === $o) ? 'selected' : '' ?>><?= htmlspecialchars($o) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group full rw-profile-nav">
                                <button type="button" class="btn primary" data-profile-next>Lanjut ke Data Keluarga</button>
                            </div>
                        </section>

                        <section class="rw-profile-step" data-step="2">
                            <div class="form-group full" id="pasanganSection">
                                <label>Data Istri/Suami</label>
                                <div class="form-grid">
                                    <div class="form-group"><input name="pasangan_nik" placeholder="NIK Pasangan" maxlength="16" inputmode="numeric" value="<?= htmlspecialchars((string)($pasangan['nik'] ?? '')) ?>"></div>
                                    <div class="form-group"><input name="pasangan_nama" placeholder="Nama Pasangan" value="<?= htmlspecialchars((string)($pasangan['nama_lengkap'] ?? '')) ?>"></div>
                                    <div class="form-group"><input name="pasangan_tempat" placeholder="Tempat Lahir" value="<?= htmlspecialchars((string)($pasangan['tempat_lahir'] ?? '')) ?>"></div>
                                    <div class="form-group"><input type="date" name="pasangan_tgl" value="<?= htmlspecialchars((string)($pasangan['tanggal_lahir'] ?? '')) ?>"></div>
                                </div>
                            </div>

                            <div class="form-group full">
                                <label>Data Anak</label>
                                <div id="anakContainer" class="list-box">
                                    <?php if (empty($anak)): ?>
                                        <div class="empty">Belum ada data anak.</div>
                                    <?php else: ?>
                                        <?php foreach ($anak as $idx => $a): ?>
                                            <div class="form-grid list-item child-row">
                                                <div class="form-group"><input name="anak[<?= $idx ?>][nik]" placeholder="NIK Anak" value="<?= htmlspecialchars((string)$a['nik']) ?>"></div>
                                                <div class="form-group"><input name="anak[<?= $idx ?>][nama]" placeholder="Nama Anak" value="<?= htmlspecialchars((string)$a['nama_lengkap']) ?>"></div>
                                                <div class="form-group"><input name="anak[<?= $idx ?>][tempat]" placeholder="Tempat Lahir" value="<?= htmlspecialchars((string)$a['tempat_lahir']) ?>"></div>
                                                <div class="form-group"><input type="date" name="anak[<?= $idx ?>][tgl]" value="<?= htmlspecialchars((string)$a['tanggal_lahir']) ?>"></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button class="btn" type="button" id="addAnakBtn">Tambah Anak</button>
                            </div>

                            <div class="form-group full">
                                <label>Orang Lain Dalam Rumah</label>
                                <div id="orangContainer" class="list-box">
                                    <?php if (empty($orangLain)): ?>
                                        <div class="empty">Belum ada data orang lain.</div>
                                    <?php else: ?>
                                        <?php foreach ($orangLain as $idx => $o): ?>
                                            <div class="form-grid list-item person-row">
                                                <div class="form-group"><input name="orang_lain[<?= $idx ?>][nama]" placeholder="Nama" value="<?= htmlspecialchars((string)$o['nama_lengkap']) ?>"></div>
                                                <div class="form-group"><input name="orang_lain[<?= $idx ?>][umur]" type="number" placeholder="Umur" value="<?= htmlspecialchars((string)$o['umur']) ?>"></div>
                                                <div class="form-group full"><input name="orang_lain[<?= $idx ?>][hubungan]" placeholder="Status Hubungan" value="<?= htmlspecialchars((string)$o['status_hubungan']) ?>"></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button class="btn" type="button" id="addOrangBtn">Tambah Orang Lain</button>
                            </div>

                            <div class="form-group full rw-profile-nav">
                                <button type="button" class="btn" data-profile-prev>Kembali</button>
                                <button type="button" class="btn primary" data-profile-next>Lanjut ke Aset & Dokumen</button>
                            </div>
                        </section>

                        <section class="rw-profile-step" data-step="3">
                            <div class="form-group full">
                                <label>Data Kendaraan</label>
                                <div id="kendaraanContainer" class="list-box">
                                    <?php if (empty($kendaraan)): ?>
                                        <div class="empty">Belum ada data kendaraan.</div>
                                    <?php else: ?>
                                        <?php foreach ($kendaraan as $idx => $k): ?>
                                            <div class="form-grid list-item vehicle-row">
                                                <div class="form-group"><input name="kendaraan[<?= $idx ?>][nopol]" placeholder="No Polisi" value="<?= htmlspecialchars((string)$k['nopol']) ?>"></div>
                                                <div class="form-group"><input name="kendaraan[<?= $idx ?>][jenis]" placeholder="Jenis Kendaraan" value="<?= htmlspecialchars((string)$k['jenis_kendaraan']) ?>"></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button class="btn" type="button" id="addKendaraanBtn">Tambah Kendaraan</button>
                            </div>

                            <div class="form-group full">
                                <label>Upload Dokumen Baru</label>
                                <input type="file" name="dokumen[]" multiple>
                                <div class="doc-list" style="margin-top:8px;">
                                    <?php if (empty($dokumen)): ?>
                                        <div class="empty">Belum ada dokumen tersimpan.</div>
                                    <?php else: ?>
                                        <?php foreach ($dokumen as $d): ?>
                                            <a href="<?= htmlspecialchars(smart_asset((string)$d['file_path']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Dokumen #<?= (int)$d['id'] ?></a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group full rw-profile-nav">
                                <button type="button" class="btn" data-profile-prev>Kembali</button>
                                <button type="submit" class="btn primary">Simpan Data Diri Lengkap</button>
                            </div>
                        </section>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-panel" data-panel="history">
            <?php
                $historyTotalNominal = 0;
                foreach ($historyRows as $hNominal) {
                    $historyTotalNominal += (float)($hNominal['total_tagihan'] ?? 0);
                }
                $historyLatestNominal = !empty($historyRows) ? (float)($historyRows[0]['total_tagihan'] ?? 0) : 0;
                $historyPaidCount = 0;
                foreach ($historyRows as $hStatus) {
                    $st = strtoupper(trim((string)($hStatus['status'] ?? '')));
                    if ($st === 'LUNAS' || $st === 'DIBAYAR') {
                        $historyPaidCount++;
                    }
                }
            ?>
            <div class="card section rw-wizard-head">
                <div class="rw-wizard-top">
                    <h2>Nominal dan Riwayat Iuran</h2>
                    <span>2 dari 3</span>
                </div>
                <div class="rw-wizard-track" aria-hidden="true"><span style="width:67%"></span></div>
                <div class="rw-history-summary">
                    <div class="rw-history-number">Rp <?= number_format($historyLatestNominal, 0, ',', '.') ?></div>
                    <p class="muted">Nominal iuran terbaru Anda</p>
                </div>
                <div class="rw-history-stats">
                    <div><span>Total Periode</span><strong><?= (int)count($historyRows) ?></strong></div>
                    <div><span>Periode Lunas</span><strong><?= (int)$historyPaidCount ?></strong></div>
                    <div><span>Akumulasi</span><strong>Rp <?= number_format($historyTotalNominal, 0, ',', '.') ?></strong></div>
                </div>
            </div>

            <div class="card section">
                <h2>History Pembayaran</h2>
                <p class="muted">Histori iuran yang tercatat pada akun warga Anda.</p>
                <div class="rw-history-list">
                    <?php if (empty($historyRows)): ?>
                        <div class="empty">Belum ada history pembayaran.</div>
                    <?php else: ?>
                        <?php $bulanMap = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; ?>
                        <?php foreach ($historyRows as $r): ?>
                            <?php
                                $statusRaw = trim((string)($r['status'] ?? '-'));
                                $statusUpper = strtoupper($statusRaw);
                                $statusClass = 'chip-pending';
                                if ($statusUpper === 'LUNAS' || $statusUpper === 'DIBAYAR') {
                                    $statusClass = 'chip-safe';
                                } elseif ($statusUpper === 'MENUNGGAK' || $statusUpper === 'TUNGGAK') {
                                    $statusClass = 'chip-process';
                                }
                            ?>
                            <div class="rw-history-item">
                                <div class="rw-history-item-top">
                                    <strong><?= htmlspecialchars($bulanMap[(int)$r['bulan']] ?? '-') ?> <?= htmlspecialchars((string)$r['tahun']) ?></strong>
                                    <span class="info-chip <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusRaw !== '' ? $statusRaw : '-') ?></span>
                                </div>
                                <div class="rw-history-item-bottom">
                                    <span>Rp <?= number_format((float)$r['total_tagihan'], 0, ',', '.') ?></span>
                                    <small><?= htmlspecialchars((string)($r['tanggal_bayar'] ?: 'Belum ada tanggal bayar')) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card section rw-panel-cta">
                <p>Perlu bantuan terkait status iuran? Anda bisa langsung kirim aduan ke pengurus.</p>
                <a href="#" class="btn primary" data-go-tab="aduan"><i class="fas fa-paper-plane"></i> Lanjut ke Aduan</a>
            </div>
        </div>

        <div class="tab-panel" data-panel="aduan">
            <div class="card section rw-wizard-head">
                <div class="rw-wizard-top">
                    <h2>Finalisasi Layanan Aduan</h2>
                    <span>3 dari 3</span>
                </div>
                <div class="rw-wizard-track" aria-hidden="true"><span style="width:100%"></span></div>
                <p class="muted">Sampaikan kendala dengan detail, lampirkan bukti, lalu pantau statusnya di sini.</p>
            </div>

            <div class="grid2">
                <div class="card section">
                    <h2>Kirim Aduan ke Si-SmaRT</h2>
                    <p class="muted">Aduan masuk ke menu Keamanan > Laporan, lalu harus disetujui pengurus sebelum tampil di portal.</p>
                    <div class="rw-aduan-benefits">
                        <div><i class="fas fa-shield-heart"></i><span>Diterima langsung oleh pengurus</span></div>
                        <div><i class="fas fa-paperclip"></i><span>Lampiran foto/video/dokumen didukung</span></div>
                        <div><i class="fas fa-chart-line"></i><span>Status progres bisa dipantau</span></div>
                    </div>
                    <?php if (!$linkedWarga): ?>
                        <div class="empty">Data warga belum terhubung. Tidak dapat kirim aduan.</div>
                    <?php else: ?>
                        <button type="button" class="rw-sheet-trigger" data-open-modal="rwAduanModal">
                            <span class="rw-sheet-trigger-icon"><i class="fas fa-message"></i></span>
                            <span class="rw-sheet-trigger-main">
                                <strong>Tulis Aduan Baru</strong>
                                <small>Buka form aduan model bottom sheet</small>
                            </span>
                            <span class="rw-sheet-trigger-trail"><i class="fas fa-chevron-right"></i></span>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card section">
                    <h2>Riwayat Aduan Saya</h2>
                    <?php if (empty($laporanRows)): ?>
                        <div class="empty">Belum ada aduan dikirim.</div>
                    <?php else: ?>
                        <div class="list-box rw-aduan-list">
                            <?php foreach ($laporanRows as $l): ?>
                                <div class="list-item">
                                    <h4><?= htmlspecialchars((string)($l['judul'] ?? 'Aduan')) ?></h4>
                                    <p><?= nl2br(htmlspecialchars((string)($l['deskripsi'] ?? ''))) ?></p>
                                    <p style="margin-top:6px;">
                                        <span class="status <?= htmlspecialchars((string)($l['status'] ?? 'Baru')) ?>"><?= htmlspecialchars((string)($l['status'] ?? 'Baru')) ?></span>
                                        <span style="color:var(--muted); margin-left:8px;"><?= htmlspecialchars((string)($l['waktu_kejadian'] ?? '-')) ?></span>
                                        <span style="color:var(--muted); margin-left:8px;">Portal: <?= ((int)($l['approved_portal'] ?? 0) === 1) ? 'Disetujui' : 'Menunggu Approval' ?></span>
                                    </p>
                                    <?php if (!empty($l['lampiran_path'])): ?>
                                        <p style="margin-top:8px;"><a href="<?= htmlspecialchars(smart_asset((string)$l['lampiran_path']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Lihat Lampiran: <?= htmlspecialchars((string)($l['lampiran_name'] ?? 'File')) ?></a></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (($aduanTotalPages ?? 1) > 1): ?>
                            <div class="rw-aduan-pagination">
                                <span class="muted">Halaman <?= (int)$aduanPage ?> dari <?= (int)$aduanTotalPages ?> (<?= (int)$aduanTotalRows ?> data)</span>
                                <div class="rw-aduan-pagination-actions">
                                    <?php $prevAduan = max(1, (int)$aduanPage - 1); ?>
                                    <?php $nextAduan = min((int)$aduanTotalPages, (int)$aduanPage + 1); ?>
                                    <a class="btn" href="ruang_warga.php?tab=aduan&aduan_page=<?= $prevAduan ?>" <?= ((int)$aduanPage <= 1) ? 'style="pointer-events:none;opacity:.5;"' : '' ?>>Sebelumnya</a>
                                    <a class="btn" href="ruang_warga.php?tab=aduan&aduan_page=<?= $nextAduan ?>" <?= ((int)$aduanPage >= (int)$aduanTotalPages) ? 'style="pointer-events:none;opacity:.5;"' : '' ?>>Berikutnya</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($linkedWarga): ?>
            <div id="rwAduanModal" class="rw-form-modal hidden" aria-hidden="true">
                <div class="rw-form-sheet">
                    <div class="rw-form-handle"></div>
                    <div class="rw-form-header">
                        <h3>Kirim Aduan ke Si-SmaRT</h3>
                        <button type="button" class="rw-form-close" data-close-modal="rwAduanModal" aria-label="Tutup form aduan">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="rw-sheet-scroll">
                        <form method="POST" class="form-grid rw-sheet-form" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="submit_aduan_smart">
                            <div class="form-group full"><label>Judul Aduan</label><input name="adu_judul" required></div>
                            <div class="form-group full"><label>Keterangan</label><textarea name="adu_keterangan" rows="5"></textarea></div>
                            <div class="form-group full">
                                <label>Lampiran (Kamera / Upload)</label>
                                <input type="file" name="adu_lampiran" accept="image/*,video/*,.pdf">
                                <small class="muted">Bisa ambil langsung dari kamera HP atau upload file (jpg, png, webp, mp4, mov, pdf).</small>
                            </div>
                            <div class="form-group full"><button type="submit" class="btn primary">Kirim Aduan</button></div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if ($isLoggedIn): ?>
<div class="mobile-tab-dock" aria-label="Navigasi cepat Ruang Warga">
    <button class="tab-dock-btn active" data-tab="ringkasan"><i class="fas fa-house"></i><span>Ringkasan</span></button>
    <button class="tab-dock-btn" data-tab="profil-lengkap"><i class="fas fa-id-card"></i><span>Data</span></button>
    <button class="tab-dock-btn" data-tab="history"><i class="fas fa-clock-rotate-left"></i><span>Iuran</span></button>
    <button class="tab-dock-btn" data-tab="aduan"><i class="fas fa-paper-plane"></i><span>Aduan</span></button>
</div>
<?php endif; ?>

<?php if ($alertMessage): ?>
<script>
Swal.fire({
    icon: <?= json_encode($alertType ?: 'info') ?>,
    title: 'Informasi',
    text: <?= json_encode($alertMessage) ?>,
    confirmButtonColor: '#6b3db8'
});
</script>
<?php endif; ?>

<script>
(function () {
    window.addEventListener('pageshow', function (event) {
        var perfEntries = typeof performance !== 'undefined' && typeof performance.getEntriesByType === 'function'
            ? performance.getEntriesByType('navigation')
            : [];
        var isBackForward = !!(perfEntries && perfEntries[0] && perfEntries[0].type === 'back_forward');
        if (!event.persisted && !isBackForward) {
            return;
        }

        try {
            var url = new URL(window.location.href);
            url.searchParams.set('ref', String(Date.now()));
            window.location.replace(url.toString());
        } catch (e) {
            window.location.reload();
        }
    });

    function normalizeToTwoDigits(value, padLeft) {
        var digits = String(value || '').replace(/\D+/g, '');
        if (!digits) {
            return '';
        }
        var sliced = digits.length > 2 ? digits.slice(-2) : digits;
        return padLeft ? sliced.padStart(2, '0') : sliced;
    }

    function bindTwoDigitInput(input) {
        if (!input || input.dataset.twoDigitBound === '1') {
            return;
        }
        input.dataset.twoDigitBound = '1';

        input.value = normalizeToTwoDigits(input.value, true);

        input.addEventListener('input', function () {
            input.value = normalizeToTwoDigits(input.value, false);
        });

        var pad = function () {
            input.value = normalizeToTwoDigits(input.value, true);
        };

        input.addEventListener('blur', pad);
        input.addEventListener('change', pad);

        var form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                input.value = normalizeToTwoDigits(input.value, true);
            });
        }
    }

    bindTwoDigitInput(document.querySelector('input[name="no_rumah"]'));
    bindTwoDigitInput(document.querySelector('input[name="nomor_rumah"]'));
})();
</script>

<script src="<?= htmlspecialchars(smart_asset('public/js/file-source-picker.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(smart_asset('public/js/ruang-warga-standalone.js', 'auto'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
