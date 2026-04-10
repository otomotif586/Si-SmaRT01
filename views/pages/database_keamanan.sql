-- Tabel Master Personel Satpam
CREATE TABLE `km_satpam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Penjadwalan Shift
CREATE TABLE `km_jadwal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `satpam_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `shift` enum('Pagi','Malam') NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`satpam_id`) REFERENCES `km_satpam`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Laporan Kejadian & Tamu Lingkungan
CREATE TABLE `km_laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `waktu_kejadian` datetime NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `deskripsi` text,
  `pelapor` varchar(100) DEFAULT 'Sistem',
  `status` enum('Baru','Diproses','Selesai') DEFAULT 'Baru',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Izin & Cuti
CREATE TABLE `km_izin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `satpam_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis` enum('Sakit','Izin','Cuti') NOT NULL,
  `keterangan` text,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`satpam_id`) REFERENCES `km_satpam`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;