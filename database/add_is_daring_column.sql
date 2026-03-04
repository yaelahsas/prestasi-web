-- =============================================
-- Tambahkan kolom is_daring ke tabel bimbel_jurnal
-- =============================================

-- Tambahkan kolom is_daring dengan default value 0 (tidak daring)
ALTER TABLE `bimbel_jurnal` ADD COLUMN `is_daring` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Offline, 1=Daring' AFTER `keterangan`;

-- Update view v_jurnal_lengkap untuk menyertakan kolom is_daring
DROP VIEW IF EXISTS `v_jurnal_lengkap`;

CREATE VIEW `v_jurnal_lengkap` AS
SELECT 
  j.id_jurnal,
  j.tanggal,
  j.materi,
  j.jumlah_siswa,
  j.keterangan,
  j.is_daring,
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