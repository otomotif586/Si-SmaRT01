<?php
require_once '../config/database.php';
require_once '../config/asset_url.php';
header('Content-Type: application/json');
try {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul'] ?? ''; 
    $konten = $_POST['konten'] ?? ''; 
    $status = $_POST['status'] ?? 'Publish';
    $youtube_url = $_POST['youtube_url'] ?? '';

    $uploadDir = smart_public_fs_path('public/uploads/cms');
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    function handleUpload($fileArray, $prefix, $uploadDir) {
        if (isset($fileArray) && $fileArray['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
            $filename = $prefix . '_' . time() . '.' . $ext;
                if (move_uploaded_file($fileArray['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $filename)) {
                return 'public/uploads/cms/' . $filename;
            }
        }
        return null;
    }

    $thumbnail = handleUpload($_FILES['thumbnail'] ?? null, 'blog_thumb', $uploadDir);
    $video_url = handleUpload($_FILES['video'] ?? null, 'blog_video', $uploadDir);

    if ($id > 0) {
        $sql = "UPDATE web_blogs SET judul=?, konten=?, status=?, youtube_url=?";
        $params = [$judul, $konten, $status, $youtube_url];
        if ($thumbnail) { $sql .= ", thumbnail=?"; $params[] = $thumbnail; }
        if ($video_url) { $sql .= ", video_url=?"; $params[] = $video_url; }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $pdo->prepare($sql)->execute($params);
        echo json_encode(['status' => 'success', 'message' => 'Artikel diperbarui.']);
    } else {
        $pdo->prepare("INSERT INTO web_blogs (judul, konten, status, thumbnail, video_url, youtube_url) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$judul, $konten, $status, $thumbnail, $video_url, $youtube_url]);
        echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diterbitkan.']);
    }
} catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }