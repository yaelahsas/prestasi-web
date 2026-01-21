# Database Documentation - Sistem Rekap Jurnal Bimbingan Belajar

## Overview

Database ini dirancang untuk aplikasi rekap jurnal bimbingan belajar dengan struktur yang sederhana namun powerful. Database menggunakan MySQL dengan engine InnoDB untuk mendukung transaksi dan relasi foreign key.

## Struktur Tabel

### 1. tabel_users
Tabel untuk menyimpan data user yang dapat login ke sistem (admin/tim prestasi).

**Field:**
- `id_user` (Primary Key) - ID unik user
- `nama` - Nama lengkap user
- `username` - Username untuk login (unik)
- `password` - Password yang sudah di-hash
- `role` - Hak akses (admin/tim)
- `status` - Status user (aktif/nonaktif)
- `created_at` - Waktu pembuatan record

**Default Login:**
- Username: `admin`
- Password: `password`

### 2. tabel_kelas
Tabel untuk menyimpan data kelas bimbingan.

**Field:**
- `id_kelas` (Primary Key) - ID unik kelas
- `nama_kelas` - Nama kelas (contoh: 8A)
- `tingkat` - Tingkat kelas (7/8/9)
- `status` - Status kelas (aktif/nonaktif)

### 3. tabel_mapel
Tabel untuk menyimpan data mata pelajaran.

**Field:**
- `id_mapel` (Primary Key) - ID unik mata pelajaran
- `nama_mapel` - Nama mata pelajaran
- `status` - Status mata pelajaran (aktif/nonaktif)

### 4. tabel_guru
Tabel untuk menyimpan data guru bimbingan.

**Field:**
- `id_guru` (Primary Key) - ID unik guru
- `nama_guru` - Nama lengkap guru
- `nip` - Nomor Induk Pegawai (opsional)
- `id_kelas` (Foreign Key) - Relasi ke tabel_kelas
- `id_mapel` (Foreign Key) - Relasi ke tabel_mapel
- `status` - Status guru (aktif/nonaktif)
- `created_at` - Waktu pembuatan record

**Catatan:** 1 guru = 1 kelas + 1 mapel

### 5. tabel_sekolah
Tabel untuk menyimpan data sekolah yang digunakan untuk laporan PDF.

**Field:**
- `id_sekolah` (Primary Key) - ID unik sekolah
- `nama_sekolah` - Nama sekolah
- `alamat` - Alamat sekolah
- `logo` - Path logo sekolah
- `kepala_sekolah` - Nama kepala sekolah
- `nip_kepsek` - NIP kepala sekolah

**Catatan:** Biasanya hanya ada 1 record dalam tabel ini.

### 6. tabel_jurnal
Tabel utama untuk menyimpan data jurnal kegiatan bimbingan belajar.

**Field:**
- `id_jurnal` (Primary Key) - ID unik jurnal
- `tanggal` - Tanggal kegiatan bimbel
- `id_guru` (Foreign Key) - Relasi ke tabel_guru
- `id_kelas` (Foreign Key) - Relasi ke tabel_kelas
- `id_mapel` (Foreign Key) - Relasi ke tabel_mapel
- `materi` - Materi yang diajarkan
- `jumlah_siswa` - Jumlah siswa yang hadir
- `keterangan` - Keterangan tambahan (opsional)
- `foto_bukti` - Path foto bukti kegiatan
- `created_by` (Foreign Key) - ID user yang menginput data
- `created_at` - Waktu pembuatan record

## Relasi Database

```
tabel_users
  └── tabel_jurnal.created_by

tabel_guru
  ├── tabel_jurnal.id_guru
  ├── tabel_guru.id_kelas → tabel_kelas.id_kelas
  └── tabel_guru.id_mapel → tabel_mapel.id_mapel

tabel_kelas
  └── tabel_jurnal.id_kelas

tabel_mapel
  └── tabel_jurnal.id_mapel
```

## View

### v_jurnal_lengkap
View yang menggabungkan semua tabel untuk mempermudah query laporan.

**Query:**
```sql
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
FROM tabel_jurnal j
LEFT JOIN tabel_guru g ON j.id_guru = g.id_guru
LEFT JOIN tabel_kelas k ON j.id_kelas = k.id_kelas
LEFT JOIN tabel_mapel m ON j.id_mapel = m.id_mapel
LEFT JOIN tabel_users u ON j.created_by = u.id_user;
```

## Index untuk Optimasi

Beberapa index telah ditambahkan untuk optimasi query:
- `idx_tanggal` pada tabel_jurnal (untuk query berdasarkan tanggal)
- `idx_guru_tanggal` pada tabel_jurnal (untuk laporan per guru)
- `idx_kelas_tanggal` pada tabel_jurnal (untuk laporan per kelas)

## Cara Import Database

1. Buat database baru di MySQL:
   ```sql
   CREATE DATABASE prestasi_db;
   ```

2. Import file SQL:
   ```bash
   mysql -u username -p prestasi_db < database/prestasi_db.sql
   ```

3. Atau gunakan phpMyAdmin/MySQL Workbench untuk import file `prestasi_db.sql`

## Konfigurasi CodeIgniter

Update file `application/config/database.php` sesuai dengan konfigurasi database Anda:

```php
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'prestasi_db',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

## Catatan Penting

1. Password default untuk admin: `password`
2. Folder untuk upload foto bukti: `assets/uploads/foto_kegiatan/`
3. Pastikan folder tersebut memiliki permission write (755 atau 777)
4. Foto bukti disimpan sebagai path, bukan blob untuk efisiensi database
5. Struktur ini sudah mendukung relasi yang diperlukan untuk laporan PDF
6. View `v_jurnal_lengkap` bisa digunakan untuk mempermudah query laporan