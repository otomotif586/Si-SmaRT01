-- SQL migrasi fitur Aduan Warga -> CMS Keamanan -> Approval Portal
-- Jalankan sekali di database aplikasi (MySQL/MariaDB)

ALTER TABLE laporan_keamanan
    ADD COLUMN IF NOT EXISTS sumber_input VARCHAR(20) NOT NULL DEFAULT 'Sistem' AFTER kategori,
    ADD COLUMN IF NOT EXISTS sumber_warga_id INT NULL AFTER sumber_input,
    ADD COLUMN IF NOT EXISTS sumber_account_id INT NULL AFTER sumber_warga_id,
    ADD COLUMN IF NOT EXISTS butuh_approval_portal TINYINT(1) NOT NULL DEFAULT 0 AFTER sumber_account_id,
    ADD COLUMN IF NOT EXISTS approved_portal TINYINT(1) NOT NULL DEFAULT 1 AFTER butuh_approval_portal,
    ADD COLUMN IF NOT EXISTS approved_portal_at DATETIME NULL AFTER approved_portal,
    ADD COLUMN IF NOT EXISTS approved_portal_by VARCHAR(120) NULL AFTER approved_portal_at,
    ADD COLUMN IF NOT EXISTS lampiran_path VARCHAR(255) NULL AFTER approved_portal_by,
    ADD COLUMN IF NOT EXISTS lampiran_name VARCHAR(255) NULL AFTER lampiran_path;

-- Index untuk percepat filter portal dan riwayat aduan warga
ALTER TABLE laporan_keamanan
    ADD INDEX idx_laporan_keamanan_approved_portal (approved_portal),
    ADD INDEX idx_laporan_keamanan_sumber_warga (sumber_warga_id),
    ADD INDEX idx_laporan_keamanan_waktu (waktu_kejadian);

-- Opsional: foreign key jika tabel terkait sudah konsisten
-- ALTER TABLE laporan_keamanan
--     ADD CONSTRAINT fk_lapkeam_warga FOREIGN KEY (sumber_warga_id) REFERENCES warga(id) ON DELETE SET NULL,
--     ADD CONSTRAINT fk_lapkeam_account FOREIGN KEY (sumber_account_id) REFERENCES ruang_warga_accounts(id) ON DELETE SET NULL;
