-- =============================================
-- MIGRATION V2 - Sistem Prestasi Bimbel
-- Fitur Baru: RBAC Guru, Absensi, Multi-Kelas,
--             Activity Log, Laporan Tahunan
-- =============================================
-- Jalankan file ini setelah prestasi_db.sql
-- =============================================

-- =============================================
-- 1. UPDATE bimbel_users: Tambah role 'guru' dan kolom id_guru
-- =============================================

-- Tambah kolom id_guru (link ke bimbel_guru untuk role guru)
ALTER TABLE `bimbel_users`
    ADD COLUMN `id_guru` int(11) DEFAULT NULL AFTER `role`,
    ADD COLUMN `last_login` datetime DEFAULT NULL AFTER `status`,
    MODIFY COLUMN `role` enum('admin','tim','guru') NOT NULL DEFAULT 'tim';

-- Foreign key ke bimbel_guru (nullable, hanya untuk role guru)
ALTER TABLE `bimbel_users`
    ADD CONSTRAINT `bimbel_users_ibfk_1`
    FOREIGN KEY (`id_guru`) REFERENCES `bimbel_guru` (`id_guru`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- 2. TABEL bimbel_absensi (Kehadiran Guru)
-- =============================================

CREATE TABLE IF NOT EXISTS `bimbel_absensi` (
    `id_absensi` int(11) NOT NULL AUTO_INCREMENT,
    `tanggal`    date NOT NULL,
    `id_guru`    int(11) NOT NULL,
    `id_kelas`   int(11) DEFAULT NULL,
    `id_mapel`   int(11) DEFAULT NULL,
    `status`     enum('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'hadir',
    `keterangan` text DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_absensi`),
    UNIQUE KEY `uq_absensi_guru_tanggal` (`id_guru`, `tanggal`),
    KEY `id_guru`    (`id_guru`),
    KEY `id_kelas`   (`id_kelas`),
    KEY `id_mapel`   (`id_mapel`),
    KEY `created_by` (`created_by`),
    KEY `idx_tanggal` (`tanggal`),
    CONSTRAINT `bimbel_absensi_ibfk_1` FOREIGN KEY (`id_guru`)    REFERENCES `bimbel_guru`  (`id_guru`)  ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `bimbel_absensi_ibfk_2` FOREIGN KEY (`id_kelas`)   REFERENCES `bimbel_kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `bimbel_absensi_ibfk_3` FOREIGN KEY (`id_mapel`)   REFERENCES `bimbel_mapel` (`id_mapel`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `bimbel_absensi_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `bimbel_users` (`id_user`)  ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel kehadiran/absensi guru';

-- =============================================
-- 3. TABEL bimbel_guru_kelas_mapel (Multi-Kelas per Guru)
-- =============================================

CREATE TABLE IF NOT EXISTS `bimbel_guru_kelas_mapel` (
    `id`       int(11) NOT NULL AUTO_INCREMENT,
    `id_guru`  int(11) NOT NULL,
    `id_kelas` int(11) NOT NULL,
    `id_mapel` int(11) NOT NULL,
    `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = kelas/mapel utama guru',
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_guru_kelas_mapel` (`id_guru`, `id_kelas`, `id_mapel`),
    KEY `id_guru`  (`id_guru`),
    KEY `id_kelas` (`id_kelas`),
    KEY `id_mapel` (`id_mapel`),
    CONSTRAINT `gkm_ibfk_1` FOREIGN KEY (`id_guru`)  REFERENCES `bimbel_guru`  (`id_guru`)  ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `gkm_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `bimbel_kelas` (`id_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `gkm_ibfk_3` FOREIGN KEY (`id_mapel`) REFERENCES `bimbel_mapel` (`id_mapel`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Relasi many-to-many guru dengan kelas dan mapel';

-- Migrasi data existing: isi tabel pivot dari data guru yang sudah ada
INSERT IGNORE INTO `bimbel_guru_kelas_mapel` (`id_guru`, `id_kelas`, `id_mapel`, `is_primary`)
SELECT `id_guru`, `id_kelas`, `id_mapel`, 1
FROM `bimbel_guru`
WHERE `id_kelas` IS NOT NULL AND `id_mapel` IS NOT NULL;

-- =============================================
-- 4. TABEL bimbel_activity_log (Audit Trail)
-- =============================================

CREATE TABLE IF NOT EXISTS `bimbel_activity_log` (
    `id_log`     int(11) NOT NULL AUTO_INCREMENT,
    `id_user`    int(11) DEFAULT NULL,
    `action`     varchar(20) NOT NULL COMMENT 'INSERT, UPDATE, DELETE, LOGIN, LOGOUT, VIEW',
    `table_name` varchar(50) DEFAULT NULL,
    `record_id`  int(11) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_log`),
    KEY `id_user`    (`id_user`),
    KEY `idx_action` (`action`),
    KEY `idx_table`  (`table_name`),
    KEY `idx_created`(`created_at`),
    CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `bimbel_users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Audit trail semua aktivitas user';

-- =============================================
-- 5. TABEL bimbel_tahun_ajaran (Manajemen Tahun Ajaran)
-- =============================================

CREATE TABLE IF NOT EXISTS `bimbel_tahun_ajaran` (
    `id_tahun_ajaran` int(11) NOT NULL AUTO_INCREMENT,
    `nama`            varchar(20) NOT NULL COMMENT 'Contoh: 2024/2025',
    `tahun_mulai`     year(4) NOT NULL,
    `tahun_selesai`   year(4) NOT NULL,
    `semester`        enum('ganjil','genap') NOT NULL DEFAULT 'ganjil',
    `is_aktif`        tinyint(1) NOT NULL DEFAULT 0,
    `created_at`      datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_tahun_ajaran`),
    UNIQUE KEY `uq_tahun_semester` (`tahun_mulai`, `tahun_selesai`, `semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Manajemen tahun ajaran';

-- Insert tahun ajaran default
INSERT IGNORE INTO `bimbel_tahun_ajaran` (`nama`, `tahun_mulai`, `tahun_selesai`, `semester`, `is_aktif`) VALUES
('2024/2025', 2024, 2025, 'ganjil', 0),
('2024/2025', 2024, 2025, 'genap',  0),
('2025/2026', 2025, 2026, 'ganjil', 1);

-- =============================================
-- 6. INDEX TAMBAHAN untuk performa query
-- =============================================

-- Index pada bimbel_jurnal untuk query dashboard
ALTER TABLE `bimbel_jurnal`
    ADD INDEX IF NOT EXISTS `idx_tanggal`  (`tanggal`),
    ADD INDEX IF NOT EXISTS `idx_id_guru`  (`id_guru`),
    ADD INDEX IF NOT EXISTS `idx_id_kelas` (`id_kelas`),
    ADD INDEX IF NOT EXISTS `idx_id_mapel` (`id_mapel`);

-- =============================================
-- 7. CONTOH DATA: User dengan role guru
-- =============================================
-- Uncomment dan sesuaikan jika ingin menambah user guru
-- Password default: 'guru123' (ganti setelah login pertama)

-- INSERT INTO `bimbel_users` (`nama`, `username`, `password`, `role`, `id_guru`, `status`) VALUES
-- ('Nama Guru 1', 'guru1', '$2y$10$...hash...', 'guru', 1, 'aktif');

-- =============================================
-- SELESAI
-- =============================================
-- Catatan:
-- - Jalankan dengan user MySQL yang memiliki hak ALTER TABLE
-- - Backup database sebelum menjalankan migration ini
-- - Cek error setelah setiap statement
-- =============================================
