<?php
// API Info - Simpan Pengaturan Web
require_once '../../config/database.php';
header('Content-Type: application/json');
try {
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
        'web_transparansi_deskripsi' => $_POST['web_transparansi_deskripsi'] ?? ''
    ];

    // Setup Direktori Upload Khusus CMS
    $uploadDir = '../../public/uploads/cms/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Fungsi Handler File Tunggal (Logo, Favicon, Banner, Dokumen)
    function handleUpload($fileArray, $prefix, $uploadDir) {
        if (isset($fileArray) && $fileArray['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
            $filename = $prefix . '_' . time() . '.' . $ext;
            if (move_uploaded_file($fileArray['tmp_name'], $uploadDir . $filename)) {
                return 'public/uploads/cms/' . $filename;
            }
        }
        return null;
    }

    if ($logoPath = handleUpload($_FILES['web_logo'] ?? null, 'logo', $uploadDir)) $settings['web_logo'] = $logoPath;
    if ($faviconPath = handleUpload($_FILES['web_favicon'] ?? null, 'favicon', $uploadDir)) $settings['web_favicon'] = $faviconPath;
    if ($heroPath = handleUpload($_FILES['web_hero_image'] ?? null, 'hero', $uploadDir)) $settings['web_hero_image'] = $heroPath;
    if ($transPath = handleUpload($_FILES['web_transparansi_file'] ?? null, 'transparansi', $uploadDir)) $settings['web_transparansi_file'] = $transPath;

    // Fungsi Handler Multi-file (Slider Carousel)
    if (isset($_FILES['web_slider_images'])) {
        $sliderPaths = [];
        $count = count($_FILES['web_slider_images']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['web_slider_images']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['web_slider_images']['name'][$i], PATHINFO_EXTENSION);
                $filename = 'slider_' . time() . '_' . $i . '.' . $ext;
                if (move_uploaded_file($_FILES['web_slider_images']['tmp_name'][$i], $uploadDir . $filename)) $sliderPaths[] = 'public/uploads/cms/' . $filename;
            }
        }
        if (!empty($sliderPaths)) $settings['web_slider_images'] = json_encode($sliderPaths);
    }
    $stmt = $pdo->prepare("INSERT INTO web_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach($settings as $key => $val) $stmt->execute([$key, $val]);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }