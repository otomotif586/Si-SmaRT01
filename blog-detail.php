<?php
require_once 'config/database.php';

$id = $_GET['id'] ?? 0;
try {
    $stmt = $pdo->prepare("SELECT * FROM web_blogs WHERE id = ? AND status = 'Publish'");
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$blog) {
        header("Location: index.php");
        exit;
    }

    // Ambil Logo & Title dari settings
    $stmtSet = $pdo->query("SELECT setting_key, setting_value FROM web_settings");
    $settings = $stmtSet->fetchAll(PDO::FETCH_KEY_PAIR);
    $web_logo = $settings['web_logo'] ?? '';
    $web_nama = $settings['web_nama'] ?? 'Portal Warga';
} catch (Exception $e) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['judul']) ?> - <?= htmlspecialchars($web_nama) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; }
        .blog-content img { max-width: 100%; height: auto; border-radius: 1rem; margin: 2rem 0; }
        .blog-content p { margin-bottom: 1.5rem; line-height: 1.8; color: #334155; }
        .blog-content h1, .blog-content h2, .blog-content h3 { font-weight: 800; color: #0f172a; margin: 2.5rem 0 1rem; }
        .glossy { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
    <link rel="stylesheet" href="public/css/theme-glass.css?v=20260417">
</head>
<body class="bg-slate-50">
    <nav class="fixed top-0 w-full z-50 p-6">
        <div class="container mx-auto flex justify-between items-center p-4 rounded-3xl glossy shadow-sm">
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <span class="font-bold text-slate-800">Kembali</span>
            </a>
            <?php if($web_logo): ?>
                <img src="<?= $web_logo ?>" class="h-8" alt="Logo">
            <?php endif; ?>
        </div>
    </nav>

    <main class="container mx-auto px-6 pt-32 pb-20">
        <article class="max-w-4xl mx-auto">
            <header class="mb-12 text-center">
                <span class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold uppercase tracking-widest mb-6 inline-block">Berita Desa</span>
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-8 text-slate-900"><?= htmlspecialchars($blog['judul']) ?></h1>
                <div class="flex items-center justify-center space-x-4 text-slate-400 font-medium">
                    <i class="far fa-calendar-alt"></i>
                    <span><?= date('d F Y', strtotime($blog['created_at'])) ?></span>
                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                    <i class="far fa-user"></i>
                    <span>Admin RT</span>
                </div>
            </header>

            <?php if($blog['youtube_url']): ?>
                <div class="aspect-video rounded-[3rem] overflow-hidden shadow-2xl mb-12 border-8 border-white">
                    <?php 
                        $yt_id = '';
                        if(preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $blog['youtube_url'], $match)) $yt_id = $match[1];
                    ?>
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?= $yt_id ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php elseif($blog['video_url']): ?>
                <video src="<?= $blog['video_url'] ?>" controls class="w-full rounded-[3rem] shadow-2xl mb-12 border-8 border-white"></video>
            <?php elseif($blog['thumbnail']): ?>
                <img src="<?= $blog['thumbnail'] ?>" class="w-full h-[500px] object-cover rounded-[3rem] shadow-2xl mb-12 border-8 border-white" alt="<?= htmlspecialchars($blog['judul']) ?>">
            <?php endif; ?>

            <div class="blog-content prose prose-lg prose-slate max-w-none text-lg">
                <?= $blog['konten'] ?>
            </div>
        </article>
    </main>

    <footer class="py-12 border-t border-slate-200 text-center text-slate-400 text-sm font-medium">
        <p>&copy; 2026 <?= htmlspecialchars($web_nama) ?> • Portal Warga Digital</p>
    </footer>
</body>
</html>
