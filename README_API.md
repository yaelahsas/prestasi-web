# API Jurnal untuk Integrasi WhatsApp Bot

## Overview

API ini dibuat untuk memungkinkan integrasi antara aplikasi Prestasi dengan WhatsApp bot. API ini menyediakan endpoint untuk membuat, membaca, dan mencari data jurnal.

## Struktur File

- `application/controllers/Api.php` - Controller utama untuk semua endpoint API
- `application/config/routes.php` - Konfigurasi routing untuk API (telah ditambahkan)
- `API_DOCUMENTATION.md` - Dokumentasi lengkap API
- `test_api.php` - Script testing untuk API
- `whatsapp_bot_example.py` - Contoh implementasi WhatsApp bot dalam Python
- `README_API.md` - File ini

## Fitur API

### 1. Autentikasi
- Menggunakan API Key yang dikirim melalui header `X-API-Key`
- API Key default: `whatsapp_bot_key_2024`
- Bisa ditambahkan lebih banyak API Key di method `_authenticate()`

### 2. Endpoint yang Tersedia

#### Membuat Jurnal
- **URL**: `POST /api/jurnal/create`
- **Fungsi**: Membuat jurnal baru
- **Fitur**:
  - Validasi data input
  - Upload foto bukti (base64)
  - Response JSON terstruktur

#### Mendapatkan Data Referensi
- `GET /api/guru/list` - Daftar guru
- `GET /api/kelas/list` - Daftar kelas
- `GET /api/mapel/list` - Daftar mata pelajaran

#### Membaca Data Jurnal
- `GET /api/jurnal/list` - Daftar jurnal dengan pagination
- `GET /api/jurnal/view/{id}` - Detail jurnal berdasarkan ID
- `GET /api/jurnal/search` - Mencari jurnal berdasarkan keyword

## Cara Penggunaan

### 1. Testing API
Jalankan script testing untuk memastikan API berfungsi:
```bash
php test_api.php
```

### 2. Integrasi dengan WhatsApp Bot
Lihat contoh implementasi di `whatsapp_bot_example.py`:
```bash
python whatsapp_bot_example.py
```

### 3. Format Pesan WhatsApp
Untuk membuat jurnal, gunakan format:
```
Jurnal
Tanggal: 2024-01-15
Guru: Ahmad Fauzi
Kelas: XII IPA 1
Mapel: Matematika
Materi: Pembahasan Soal Matematika
Jumlah Siswa: 25
Keterangan: Siswa antusias
```

## Keamanan

### 1. API Key
- API Key disimpan di controller `Api.php`
- Bisa ditambahkan atau diubah sesuai kebutuhan
- Disarankan untuk menggunakan API Key yang lebih kompleks di production

### 2. Validasi Input
- Semua input divalidasi sebelum diproses
- Required fields diperiksa
- Format data divalidasi (tanggal, angka, dll)

### 3. Error Handling
- Response error yang konsisten
- HTTP status code yang tepat
- Informasi error yang informatif tapi aman

## Konfigurasi Tambahan

### 1. Mengubah API Key
Edit file `application/controllers/Api.php` di method `_authenticate()`:
```php
$valid_api_keys = ['your_new_api_key_here'];
```

### 2. Menambahkan Rate Limiting (Opsional)
Bisa ditambahkan di constructor untuk membatasi request:
```php
public function __construct()
{
    parent::__construct();
    // Rate limiting logic here
    $this->_check_rate_limit();
}
```

### 3. Logging (Opsional)
Tambahkan logging untuk monitoring:
```php
log_message('info', 'API Request: ' . $this->uri->uri_string());
```

## Deployment

### 1. Production Environment
- Ganti API Key dengan yang lebih aman
- Aktifkan HTTPS
- Pertimbangkan untuk menambahkan CORS jika perlu
- Monitor API usage

### 2. Testing di Production
- Test semua endpoint
- Verifikasi autentikasi
- Cek error handling
- Monitor performance

## Troubleshooting

### 1. Common Issues
- **401 Unauthorized**: API Key salah atau tidak ada
- **400 Bad Request**: Data input tidak valid
- **404 Not Found**: Endpoint tidak ada
- **500 Internal Server Error**: Error di server

### 2. Debug Tips
- Cek log CodeIgniter di `application/logs/`
- Test dengan `test_api.php` terlebih dahulu
- Verifikasi database connection
- Pastikan semua model ter-load dengan benar

## Future Enhancements

### 1. Fitur yang Bisa Ditambahkan
- OAuth2 authentication
- Rate limiting
- API versioning
- Webhook untuk real-time updates
- Export/Import data
- Bulk operations

### 2. Performance
- Caching untuk data referensi
- Database query optimization
- Response compression

## Support

Untuk bantuan atau pertanyaan:
1. Cek dokumentasi di `API_DOCUMENTATION.md`
2. Test dengan `test_api.php`
3. Lihat contoh implementasi di `whatsapp_bot_example.py`
4. Cek log untuk error details

## Changelog

### v1.0.0 (2024-01-21)
- Initial release
- Basic CRUD operations for jurnal
- API Key authentication
- Base64 image upload support
- WhatsApp bot example implementation