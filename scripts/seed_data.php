<?php
require_once __DIR__ . '/../config/database.php';

$pdo->exec("TRUNCATE TABLE `web_settings`");
$pdo->exec("INSERT INTO `web_settings` (`setting_key`, `setting_value`) VALUES
('web_nama', 'Pesona Kahuripan'),
('web_title', 'Pesona Kahuripan - Portal Warga & Organisasi RT'),
('web_hero_title', 'Harmoni Alam <br> <span class=\"text-gradient\">Kampung Asri.</span>'),
('web_visi', 'Perumahan Pesona Kahuripan hadir dengan pemandangan bukit yang menyejukkan, warga yang produktif, dan lingkungan religius yang hangat.'),
('web_misi', '')");

$pdo->exec("TRUNCATE TABLE `web_blogs`");
$pdo->exec("INSERT INTO `web_blogs` (`judul`, `konten`, `status`, `created_at`) VALUES
('Pengajian Bulanan Sektor A', 'Sinergi rohani warga untuk keharmonisan lingkungan.', 'Publish', '2024-04-25 10:00:00'),
('Update CCTV Jalan Utama', 'Penambahan titik keamanan demi kenyamanan istirahat warga.', 'Publish', '2024-04-18 10:00:00')");

$pdo->exec("TRUNCATE TABLE `web_pengurus`");
$pdo->exec("INSERT INTO `web_pengurus` (`nama`, `jabatan`, `urutan`) VALUES
('Bpk. Ahmad Ridwan', 'Ketua RT', 1),
('Ibu Siti Zulaikha', 'Sekretaris', 2),
('Bpk. Budi Hermawan', 'Bendahara', 3),
('Tim Satgas', 'Keamanan', 4)");

echo "Data seeded successfully.\n";
?>
