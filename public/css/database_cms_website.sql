-- Tabel Pengaturan Web (Visi, Misi, Kontak, dll)
CREATE TABLE `web_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Data
INSERT INTO `web_settings` (`setting_key`, `setting_value`) VALUES
('web_nama', 'Portal Warga SmaRT'),
('web_alamat', 'Jl. Contoh Perumahan No. 1, Kota XYZ'),
('web_email', 'admin@smart.local'),
('web_telepon', '081234567890'),
('web_visi', 'Menjadi lingkungan perumahan yang aman, cerdas, dan harmonis.'),
('web_misi', '1. Mewujudkan keamanan terpadu.\n2. Meningkatkan transparansi keuangan.\n3. Membangun kerukunan antar warga.');

-- Tabel Menu Navigasi Frontend
CREATE TABLE `web_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_menu` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Draft') DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Artikel & Blog
CREATE TABLE `web_blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `konten` longtext,
  `status` enum('Publish','Draft') DEFAULT 'Publish',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;