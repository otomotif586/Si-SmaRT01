<?php
require_once '../../config/database.php';
require_once '../../config/asset_url.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? ''; $subtitle = $_POST['subtitle'] ?? ''; 
    $badge_text = $_POST['badge_text'] ?? 'Promo'; $badge_icon = $_POST['badge_icon'] ?? 'fa-fire';
    $theme_color = $_POST['theme_color'] ?? 'emerald'; $urutan = $_POST['urutan'] ?? 1; $image = $_POST['image'] ?? '';

    // Tangani unggahan file gambar fisik jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = smart_public_fs_path('public/uploads/cms');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'slider_pasar_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $filename)) {
            $image = 'public/uploads/cms/' . $filename;
        }
    }

    if ($id > 0) {
        if ($image) {
            $pdo->prepare("UPDATE pasar_slider SET title=?, subtitle=?, badge_text=?, badge_icon=?, theme_color=?, urutan=?, image=? WHERE id=?")->execute([$title, $subtitle, $badge_text, $badge_icon, $theme_color, $urutan, $image, $id]);
        } else {
            $pdo->prepare("UPDATE pasar_slider SET title=?, subtitle=?, badge_text=?, badge_icon=?, theme_color=?, urutan=? WHERE id=?")->execute([$title, $subtitle, $badge_text, $badge_icon, $theme_color, $urutan, $id]);
        }
    } else {
        $pdo->prepare("INSERT INTO pasar_slider (title, subtitle, badge_text, badge_icon, theme_color, urutan, image) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute([$title, $subtitle, $badge_text, $badge_icon, $theme_color, $urutan, $image]);
    }
    echo json_encode(['status' => 'success']);
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }