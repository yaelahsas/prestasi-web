-- =============================================
-- Contoh Query Umum untuk Aplikasi Prestasi
-- =============================================

-- =============================================
-- 1. QUERY UNTUK DASHBOARD
-- =============================================

-- Total jurnal hari ini
SELECT COUNT(*) as total_hari_ini 
FROM tabel_jurnal 
WHERE DATE(tanggal) = CURDATE();

-- Total jurnal bulan ini
SELECT COUNT(*) as total_bulan_ini 
FROM tabel_jurnal 
WHERE MONTH(tanggal) = MONTH(CURDATE()) 
AND YEAR(tanggal) = YEAR(CURDATE());

-- Total jurnal per bulan dalam tahun ini
SELECT 
  MONTH(tanggal) as bulan,
  MONTHNAME(tanggal) as nama_bulan,
  COUNT(*) as total_jurnal
FROM tabel_jurnal 
WHERE YEAR(tanggal) = YEAR(CURDATE())
GROUP BY MONTH(tanggal), MONTHNAME(tanggal)
ORDER BY bulan;

-- Total guru aktif
SELECT COUNT(*) as total_guru 
FROM tabel_guru 
WHERE status = 'aktif';

-- =============================================
-- 2. QUERY UNTUK LAPORAN JURNAL
-- =============================================

-- Laporan jurnal per tanggal range
SELECT 
  j.id_jurnal,
  j.tanggal,
  g.nama_guru,
  k.nama_kelas,
  m.nama_mapel,
  j.materi,
  j.jumlah_siswa,
  j.keterangan,
  j.foto_bukti,
  u.nama as penginput
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
LEFT JOIN tabel_users u ON j.created_by = u.id_user
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
ORDER BY j.tanggal DESC, g.nama_guru ASC;

-- Laporan jurnal per guru
SELECT 
  g.nama_guru,
  g.nip,
  COUNT(*) as total_jurnal,
  SUM(j.jumlah_siswa) as total_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
GROUP BY g.id_guru, g.nama_guru, g.nip
ORDER BY total_jurnal DESC;

-- Laporan jurnal per kelas
SELECT 
  k.nama_kelas,
  k.tingkat,
  COUNT(*) as total_jurnal,
  SUM(j.jumlah_siswa) as total_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
GROUP BY k.id_kelas, k.nama_kelas, k.tingkat
ORDER BY k.tingkat, k.nama_kelas;

-- Laporan jurnal per mapel
SELECT 
  m.nama_mapel,
  COUNT(*) as total_jurnal,
  SUM(j.jumlah_siswa) as total_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
GROUP BY m.id_mapel, m.nama_mapel
ORDER BY total_jurnal DESC;

-- =============================================
-- 3. QUERY UNTUK GRAFIK
-- =============================================

-- Grafik jurnal per bulan (untuk chart bar/line)
SELECT 
  MONTHNAME(tanggal) as bulan,
  COUNT(*) as total
FROM tabel_jurnal 
WHERE YEAR(tanggal) = YEAR(CURDATE())
GROUP BY MONTH(tanggal), MONTHNAME(tanggal)
ORDER BY MONTH(tanggal);

-- Grafik jurnal per guru (untuk chart pie/bar)
SELECT 
  g.nama_guru,
  COUNT(*) as total
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
GROUP BY g.id_guru, g.nama_guru
ORDER BY total DESC
LIMIT 10;

-- Grafik kehadiran siswa per kelas
SELECT 
  k.nama_kelas,
  SUM(j.jumlah_siswa) as total_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
GROUP BY k.id_kelas, k.nama_kelas
ORDER BY total_siswa DESC;

-- =============================================
-- 4. QUERY UNTUK SEARCH/FILTER
-- =============================================

-- Search jurnal berdasarkan guru
SELECT 
  j.*,
  g.nama_guru,
  k.nama_kelas,
  m.nama_mapel
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
WHERE g.nama_guru LIKE '%Ahmad%'
ORDER BY j.tanggal DESC;

-- Search jurnal berdasarkan materi
SELECT 
  j.*,
  g.nama_guru,
  k.nama_kelas,
  m.nama_mapel
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
WHERE j.materi LIKE '%persamaan linear%'
ORDER BY j.tanggal DESC;

-- Filter jurnal berdasarkan tanggal, guru, dan kelas
SELECT 
  j.*,
  g.nama_guru,
  k.nama_kelas,
  m.nama_mapel
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
AND g.id_guru = 1
AND k.id_kelas = 3
ORDER BY j.tanggal DESC;

-- =============================================
-- 5. QUERY UNTUK CRUD OPERATIONS
-- =============================================

-- Insert jurnal baru
INSERT INTO tabel_jurnal (
  tanggal, id_guru, id_kelas, id_mapel, 
  materi, jumlah_siswa, keterangan, foto_bukti, created_by
) VALUES (
  '2023-12-01', 1, 3, 2, 
  'Persamaan Linear Satu Variabel', 15, 'Siswa antusias mengikuti pelajaran', 
  'foto_bukti_20231201_001.jpg', 1
);

-- Update jurnal
UPDATE tabel_jurnal 
SET 
  materi = 'Persamaan Linear Satu Variabel dan Dua Variabel',
  jumlah_siswa = 16,
  keterangan = 'Siswa antusias mengikuti pelajaran, ada tambahan 1 siswa'
WHERE id_jurnal = 1;

-- Delete jurnal
DELETE FROM tabel_jurnal WHERE id_jurnal = 1;

-- =============================================
-- 6. QUERY UNTUK EXPORT DATA
-- =============================================

-- Export data jurnal untuk Excel/CSV
SELECT 
  j.tanggal AS 'Tanggal',
  g.nama_guru AS 'Nama Guru',
  g.nip AS 'NIP',
  k.nama_kelas AS 'Kelas',
  m.nama_mapel AS 'Mata Pelajaran',
  j.materi AS 'Materi',
  j.jumlah_siswa AS 'Jumlah Siswa',
  j.keterangan AS 'Keterangan',
  u.nama AS 'Penginput'
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
LEFT JOIN tabel_users u ON j.created_by = u.id_user
WHERE j.tanggal BETWEEN '2023-01-01' AND '2023-12-31'
ORDER BY j.tanggal DESC, g.nama_guru ASC;

-- =============================================
-- 7. QUERY UNTUK STATISTIK
-- =============================================

-- Statistik jurnal per tahun
SELECT 
  YEAR(tanggal) as tahun,
  COUNT(*) as total_jurnal,
  SUM(jumlah_siswa) as total_siswa,
  AVG(jumlah_siswa) as rata_rata_siswa,
  MIN(jumlah_siswa) as min_siswa,
  MAX(jumlah_siswa) as max_siswa
FROM tabel_jurnal
GROUP BY YEAR(tanggal)
ORDER BY tahun DESC;

-- Statistik guru paling aktif
SELECT 
  g.nama_guru,
  COUNT(*) as total_jurnal,
  SUM(j.jumlah_siswa) as total_siswa,
  AVG(j.jumlah_siswa) as rata_rata_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
GROUP BY g.id_guru, g.nama_guru
ORDER BY total_jurnal DESC
LIMIT 5;

-- Statistik kelas paling aktif
SELECT 
  k.nama_kelas,
  COUNT(*) as total_jurnal,
  SUM(j.jumlah_siswa) as total_siswa,
  AVG(j.jumlah_siswa) as rata_rata_siswa
FROM tabel_jurnal j
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
GROUP BY k.id_kelas, k.nama_kelas
ORDER BY total_jurnal DESC
LIMIT 5;

-- =============================================
-- 8. QUERY UNTUK MANAJEMEN USER
-- =============================================

-- Get user by username untuk login
SELECT * FROM tabel_users 
WHERE username = 'admin' AND status = 'aktif';

-- Get all users untuk manajemen user
SELECT 
  id_user,
  nama,
  username,
  role,
  status,
  created_at
FROM tabel_users
ORDER BY created_at DESC;

-- =============================================
-- 9. QUERY UNTUK DROPDOWN/OPTIONS
-- =============================================

-- Get all guru aktif untuk dropdown
SELECT id_guru, nama_guru 
FROM tabel_guru 
WHERE status = 'aktif'
ORDER BY nama_guru ASC;

-- Get all kelas aktif untuk dropdown
SELECT id_kelas, nama_kelas 
FROM tabel_kelas 
WHERE status = 'aktif'
ORDER BY tingkat, nama_kelas;

-- Get all mapel aktif untuk dropdown
SELECT id_mapel, nama_mapel 
FROM tabel_mapel 
WHERE status = 'aktif'
ORDER BY nama_mapel ASC;