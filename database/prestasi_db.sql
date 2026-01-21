-- =============================================
-- Sistem Rekap Jurnal Bimbingan Belajar
-- Struktur Database untuk Aplikasi Prestasi
-- =============================================

-- 1. Tabel Users (untuk login admin / tim prestasi)
CREATE TABLE `bimbel_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tim') NOT NULL DEFAULT 'tim',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Kelas
CREATE TABLE `bimbel_kelas` (
  `id_kelas` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(20) NOT NULL,
  `tingkat` varchar(5) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Mapel
CREATE TABLE `bimbel_mapel` (
  `id_mapel` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mapel` varchar(50) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Guru
CREATE TABLE `bimbel_guru` (
  `id_guru` int(11) NOT NULL AUTO_INCREMENT,
  `nama_guru` varchar(100) NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guru`),
  KEY `id_kelas` (`id_kelas`),
  KEY `id_mapel` (`id_mapel`),
  CONSTRAINT `bimbel_guru_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `bimbel_kelas` (`id_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bimbel_guru_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `bimbel_mapel` (`id_mapel`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Sekolah (untuk informasi PDF)
CREATE TABLE `bimbel_sekolah` (
  `id_sekolah` int(11) NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(150) NOT NULL,
  `alamat` text,
  `logo` varchar(255) DEFAULT NULL,
  `kepala_sekolah` varchar(100) DEFAULT NULL,
  `nip_kepsek` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_sekolah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel Jurnal (tabel utama untuk rekap kegiatan bimbel)
CREATE TABLE `bimbel_jurnal` (
  `id_jurnal` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `materi` text NOT NULL,
  `jumlah_siswa` int(11) NOT NULL DEFAULT 0,
  `keterangan` text,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jurnal`),
  KEY `id_guru` (`id_guru`),
  KEY `id_kelas` (`id_kelas`),
  KEY `id_mapel` (`id_mapel`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `bimbel_jurnal_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `bimbel_guru` (`id_guru`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bimbel_jurnal_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `bimbel_kelas` (`id_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bimbel_jurnal_ibfk_3` FOREIGN KEY (`id_mapel`) REFERENCES `bimbel_mapel` (`id_mapel`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bimbel_jurnal_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `bimbel_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DATA AWAL (SEED DATA)
-- =============================================

-- Insert data untuk bimbel_users (admin default)
INSERT INTO `bimbel_users` (`nama`, `username`, `password`, `role`, `status`) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif');

-- Insert data untuk bimbel_kelas
INSERT INTO `bimbel_kelas` (`nama_kelas`, `tingkat`, `status`) VALUES
('7A', '7', 'aktif'),
('7B', '7', 'aktif'),
('8A', '8', 'aktif'),
('8B', '8', 'aktif'),
('9A', '9', 'aktif'),
('9B', '9', 'aktif');

-- Insert data untuk bimbel_mapel
INSERT INTO `bimbel_mapel` (`nama_mapel`, `status`) VALUES
('Matematika', 'aktif'),
('Bahasa Indonesia', 'aktif'),
('Bahasa Inggris', 'aktif'),
('IPA', 'aktif'),
('IPS', 'aktif');

-- Insert data untuk bimbel_sekolah
INSERT INTO `bimbel_sekolah` (`nama_sekolah`, `alamat`, `kepala_sekolah`, `nip_kepsek`) VALUES
('SMP Negeri Prestasi', 'Jl. Pendidikan No. 123, Jakarta', 'Drs. Ahmad Wijaya, M.Pd', '197605152005011001');

-- =============================================
-- INDEX UNTUK OPTIMASI QUERY
-- =============================================

-- Index untuk bimbel_jurnal (untuk laporan berdasarkan tanggal)
ALTER TABLE `bimbel_jurnal` ADD INDEX `idx_tanggal` (`tanggal`);

-- Index untuk bimbel_jurnal (untuk laporan berdasarkan guru)
ALTER TABLE `bimbel_jurnal` ADD INDEX `idx_guru_tanggal` (`id_guru`, `tanggal`);

-- Index untuk bimbel_jurnal (untuk laporan berdasarkan kelas)
ALTER TABLE `bimbel_jurnal` ADD INDEX `idx_kelas_tanggal` (`id_kelas`, `tanggal`);

-- =============================================
-- VIEW UNTUK KEMUDAHAN QUERY
-- =============================================

-- View untuk menampilkan data jurnal lengkap dengan relasi
CREATE VIEW `v_jurnal_lengkap` AS
SELECT 
  j.id_jurnal,
  j.tanggal,
  j.materi,
  j.jumlah_siswa,
  j.keterangan,
  j.foto_bukti,
  j.created_at,
  g.nama_guru,
  g.nip,
  k.nama_kelas,
  k.tingkat,
  m.nama_mapel,
  u.nama as nama_penginput
FROM bimbel_jurnal j
LEFT JOIN bimbel_guru g ON j.id_guru = g.id_guru
LEFT JOIN bimbel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN bimbel_mapel m ON j.id_mapel = m.id_mapel
LEFT JOIN bimbel_users u ON j.created_by = u.id_user;

-- =============================================
-- CATATAN PENTING
-- =============================================

-- 1. Password default untuk admin: password
-- 2. Folder untuk upload foto bukti: assets/uploads/foto_kegiatan/
-- 3. Pastikan folder tersebut memiliki permission write (755 atau 777)
-- 4. Foto bukti disimpan sebagai path, bukan blob untuk efisiensi database
-- 5. Struktur ini sudah mendukung relasi yang diperlukan untuk laporan PDF
-- 6. View v_jurnal_lengkap bisa digunakan untuk mempermudah query laporan