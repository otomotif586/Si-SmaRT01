<?php
error_reporting(0); // Matikan error HTML agar tidak memecah JSON
require_once '../config/database.php';
require_once '../config/asset_url.php';
header('Content-Type: application/json');
try {
    // Pastikan PDO terhubung
    if (!isset($pdo)) {
        throw new Exception("Koneksi database tidak tersedia.");
    }

    $settings = [
        'web_nama' => $_POST['web_nama'] ?? '',
        'web_email' => $_POST['web_email'] ?? '',
        'web_telepon' => $_POST['web_telepon'] ?? '',
        'web_alamat' => $_POST['web_alamat'] ?? '',
        'web_visi' => $_POST['web_visi'] ?? '',
        'web_misi' => $_POST['web_misi'] ?? '',
        'web_title' => $_POST['web_title'] ?? '',
        'web_hero_title' => $_POST['web_hero_title'] ?? '',
        'web_use_gallery' => $_POST['web_use_gallery'] ?? 'Ya',
        'web_transparansi_judul' => $_POST['web_transparansi_judul'] ?? '',
        'web_transparansi_deskripsi' => $_POST['web_transparansi_deskripsi'] ?? '',
        'web_slider_1_title' => $_POST['web_slider_1_title'] ?? '',
        'web_slider_1_subtitle' => $_POST['web_slider_1_subtitle'] ?? '',
        'web_slider_1_description' => $_POST['web_slider_1_description'] ?? '',
        'web_slider_2_title' => $_POST['web_slider_2_title'] ?? '',
        'web_slider_2_subtitle' => $_POST['web_slider_2_subtitle'] ?? '',
        'web_slider_2_description' => $_POST['web_slider_2_description'] ?? '',
        'web_slider_3_title' => $_POST['web_slider_3_title'] ?? '',
        'web_slider_3_subtitle' => $_POST['web_slider_3_subtitle'] ?? '',
        'web_slider_3_description' => $_POST['web_slider_3_description'] ?? '',
        'web_wisata_1_title' => $_POST['web_wisata_1_title'] ?? '',
        'web_wisata_1_category' => $_POST['web_wisata_1_category'] ?? '',
        'web_wisata_1_description' => $_POST['web_wisata_1_description'] ?? '',
        'web_wisata_2_title' => $_POST['web_wisata_2_title'] ?? '',
        'web_wisata_2_category' => $_POST['web_wisata_2_category'] ?? '',
        'web_wisata_2_description' => $_POST['web_wisata_2_description'] ?? '',
        // Info Penting Warga
        'web_info_penting_judul' => $_POST['web_info_penting_judul'] ?? '',
        'web_info_penting_deskripsi' => $_POST['web_info_penting_deskripsi'] ?? '',
        'web_info_item_1_icon' => $_POST['web_info_item_1_icon'] ?? '',
        'web_info_item_1_title' => $_POST['web_info_item_1_title'] ?? '',
        'web_info_item_1_desc' => $_POST['web_info_item_1_desc'] ?? '',
        'web_info_item_2_icon' => $_POST['web_info_item_2_icon'] ?? '',
        'web_info_item_2_title' => $_POST['web_info_item_2_title'] ?? '',
        'web_info_item_2_desc' => $_POST['web_info_item_2_desc'] ?? '',
        'web_info_item_3_icon' => $_POST['web_info_item_3_icon'] ?? '',
        'web_info_item_3_title' => $_POST['web_info_item_3_title'] ?? '',
        'web_info_item_3_desc' => $_POST['web_info_item_3_desc'] ?? '',
        'web_info_item_4_icon' => $_POST['web_info_item_4_icon'] ?? '',
        'web_info_item_4_title' => $_POST['web_info_item_4_title'] ?? '',
        'web_info_item_4_desc' => $_POST['web_info_item_4_desc'] ?? '',
    ];

    $uploadDir = smart_public_fs_path('public/uploads/cms');
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0777, true)) {
            throw new Exception("Gagal membuat direktori upload: " . $uploadDir);
        }
    }

    // Pastikan folder dapat ditulis
    if (!is_writable($uploadDir)) {
        throw new Exception("Folder penyimpanan tidak memiliki izin tulis (Permission Denied). Silakan hubungi admin server.");
    }

    function handleUpload($fileArray, $prefix, $uploadDir) {
        if (isset($fileArray) && $fileArray['name'] !== '') {
            if ($fileArray['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
                $filename = $prefix . '_' . time() . '.' . $ext;
                if (@move_uploaded_file($fileArray['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $filename)) {
                    return 'public/uploads/cms/' . $filename;
                } else {
                    throw new Exception("Gagal memindahkan file yang diunggah ($prefix). Cek izin folder.");
                }
            } elseif ($fileArray['error'] !== UPLOAD_ERR_NO_FILE) {
                $errMap = [
                    1 => 'File terlalu besar (PHP Limit)',
                    2 => 'File terlalu besar (HTML Limit)',
                    3 => 'File hanya terunggah sebagian',
                    6 => 'Folder sementara hilang',
                    7 => 'Gagal menulis ke disk',
                    8 => 'Ekstensi diblokir'
                ];
                $msg = $errMap[$fileArray['error']] ?? 'Error ' . $fileArray['error'];
                throw new Exception("Upload $prefix gagal: $msg");
            }
        }
        return null;
    }

    if ($logoPath = handleUpload($_FILES['web_logo'] ?? null, 'logo', $uploadDir)) $settings['web_logo'] = $logoPath;
    if ($faviconPath = handleUpload($_FILES['web_favicon'] ?? null, 'favicon', $uploadDir)) $settings['web_favicon'] = $faviconPath;
    if ($heroPath = handleUpload($_FILES['web_hero_image'] ?? null, 'hero', $uploadDir)) $settings['web_hero_image'] = $heroPath;
    if ($transPath = handleUpload($_FILES['web_transparansi_file'] ?? null, 'transparansi', $uploadDir)) $settings['web_transparansi_file'] = $transPath;

    // Handle Parallax Slider Uploads
    if ($s1Path = handleUpload($_FILES['web_slider_1_image'] ?? null, 'slide1', $uploadDir)) $settings['web_slider_1_image'] = $s1Path;
    if ($s2Path = handleUpload($_FILES['web_slider_2_image'] ?? null, 'slide2', $uploadDir)) $settings['web_slider_2_image'] = $s2Path;
    if ($s3Path = handleUpload($_FILES['web_slider_3_image'] ?? null, 'slide3', $uploadDir)) $settings['web_slider_3_image'] = $s3Path;

    // Handle Wisata Uploads
    if ($w1Path = handleUpload($_FILES['web_wisata_1_image'] ?? null, 'wisata1', $uploadDir)) $settings['web_wisata_1_image'] = $w1Path;
    if ($w2Path = handleUpload($_FILES['web_wisata_2_image'] ?? null, 'wisata2', $uploadDir)) $settings['web_wisata_2_image'] = $w2Path;

    if (isset($_FILES['web_slider_images'])) {
        $sliderPaths = [];
        $count = count($_FILES['web_slider_images']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['web_slider_images']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['web_slider_images']['name'][$i], PATHINFO_EXTENSION);
                $filename = 'slider_' . time() . '_' . $i . '.' . $ext;
                if (move_uploaded_file($_FILES['web_slider_images']['tmp_name'][$i], $uploadDir . DIRECTORY_SEPARATOR . $filename)) $sliderPaths[] = 'public/uploads/cms/' . $filename;
            }
        }
        if (!empty($sliderPaths)) $settings['web_slider_images'] = json_encode($sliderPaths);
    }
    $stmt = $pdo->prepare("INSERT INTO web_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach($settings as $key => $val) $stmt->execute([$key, $val]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }