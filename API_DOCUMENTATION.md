# API Documentation for WhatsApp Bot Integration

This API allows WhatsApp bot to create and retrieve jurnal entries from the Prestasi system.

## Base URL
```
http://your-domain.com/prestasi/api/
```

## Authentication
All API requests must include an API key in the header:
```
X-API-Key: whatsapp_bot_key_2024
```

## Endpoints

### 1. Create Jurnal
**Endpoint:** `POST /api/jurnal/create`

**Headers:**
- Content-Type: application/json
- X-API-Key: whatsapp_bot_key_2024

**Request Body (JSON):**
```json
{
    "tanggal": "2024-01-15",
    "id_guru": 1,
    "id_kelas": 1,
    "id_mapel": 1,
    "materi": "Pembahasan Soal Matematika Kelas 12",
    "jumlah_siswa": 25,
    "keterangan": "Siswa antusias mengikuti pembelajaran",
    "foto_bukti": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQ...",
    "created_by": 1
}
```

**Response (Success):**
```json
{
    "status": "success",
    "message": "Jurnal created successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": {
        "id_jurnal": 123,
        "jurnal_data": {
            "id_jurnal": 123,
            "tanggal": "2024-01-15",
            "id_guru": 1,
            "nama_guru": "Ahmad Fauzi",
            "id_kelas": 1,
            "nama_kelas": "XII IPA 1",
            "id_mapel": 1,
            "nama_mapel": "Matematika",
            "materi": "Pembahasan Soal Matematika Kelas 12",
            "jumlah_siswa": 25,
            "keterangan": "Siswa antusias mengikuti pembelajaran",
            "foto_bukti": "abc123.jpg",
            "created_by": 1,
            "nama_penginput": "Admin",
            "created_at": "2024-01-15 10:30:00"
        }
    }
}
```

**Response (Error):**
```json
{
    "status": "error",
    "message": "Field 'materi' is required",
    "timestamp": "2024-01-15 10:30:00"
}
```

### 2. Get List of Guru
**Endpoint:** `GET /api/guru/list`

**Response:**
```json
{
    "status": "success",
    "message": "Guru list retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": [
        {
            "id_guru": 1,
            "nama_guru": "Ahmad Fauzi"
        },
        {
            "id_guru": 2,
            "nama_guru": "Siti Nurhaliza"
        }
    ]
}
```

### 3. Get List of Kelas
**Endpoint:** `GET /api/kelas/list`

**Response:**
```json
{
    "status": "success",
    "message": "Kelas list retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": [
        {
            "id_kelas": 1,
            "nama_kelas": "XII IPA 1"
        },
        {
            "id_kelas": 2,
            "nama_kelas": "XII IPA 2"
        }
    ]
}
```

### 4. Get List of Mapel
**Endpoint:** `GET /api/mapel/list`

**Response:**
```json
{
    "status": "success",
    "message": "Mapel list retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": [
        {
            "id_mapel": 1,
            "nama_mapel": "Matematika"
        },
        {
            "id_mapel": 2,
            "nama_mapel": "Fisika"
        }
    ]
}
```

### 5. Get Jurnal by ID
**Endpoint:** `GET /api/jurnal/view/{id}`

**Response:**
```json
{
    "status": "success",
    "message": "Jurnal retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": {
        "id_jurnal": 123,
        "tanggal": "2024-01-15",
        "id_guru": 1,
        "nama_guru": "Ahmad Fauzi",
        "id_kelas": 1,
        "nama_kelas": "XII IPA 1",
        "id_mapel": 1,
        "nama_mapel": "Matematika",
        "materi": "Pembahasan Soal Matematika Kelas 12",
        "jumlah_siswa": 25,
        "keterangan": "Siswa antusias mengikuti pembelajaran",
        "foto_bukti": "abc123.jpg",
        "created_by": 1,
        "nama_penginput": "Admin",
        "created_at": "2024-01-15 10:30:00"
    }
}
```

### 6. Get All Jurnal (with pagination)
**Endpoint:** `GET /api/jurnal/list`

**Query Parameters:**
- page (optional): Page number (default: 1)
- limit (optional): Items per page (default: 10)

**Example:** `GET /api/jurnal/list?page=1&limit=5`

**Response:**
```json
{
    "status": "success",
    "message": "Jurnal list retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": {
        "jurnal": [
            {
                "id_jurnal": 123,
                "tanggal": "2024-01-15",
                "id_guru": 1,
                "nama_guru": "Ahmad Fauzi",
                "id_kelas": 1,
                "nama_kelas": "XII IPA 1",
                "id_mapel": 1,
                "nama_mapel": "Matematika",
                "materi": "Pembahasan Soal Matematika Kelas 12",
                "jumlah_siswa": 25,
                "keterangan": "Siswa antusias mengikuti pembelajaran",
                "foto_bukti": "abc123.jpg",
                "created_by": 1,
                "nama_penginput": "Admin",
                "created_at": "2024-01-15 10:30:00"
            }
        ],
        "pagination": {
            "page": 1,
            "limit": 5,
            "total": 50,
            "total_pages": 10
        }
    }
}
```

### 7. Search Jurnal
**Endpoint:** `GET /api/jurnal/search`

**Query Parameters:**
- keyword (required): Search keyword

**Example:** `GET /api/jurnal/search?keyword=matematika`

**Response:**
```json
{
    "status": "success",
    "message": "Search results retrieved successfully",
    "timestamp": "2024-01-15 10:30:00",
    "data": [
        {
            "id_jurnal": 123,
            "tanggal": "2024-01-15",
            "id_guru": 1,
            "nama_guru": "Ahmad Fauzi",
            "id_kelas": 1,
            "nama_kelas": "XII IPA 1",
            "id_mapel": 1,
            "nama_mapel": "Matematika",
            "materi": "Pembahasan Soal Matematika Kelas 12",
            "jumlah_siswa": 25,
            "keterangan": "Siswa antusias mengikuti pembelajaran",
            "foto_bukti": "abc123.jpg",
            "created_by": 1,
            "nama_penginput": "Admin",
            "created_at": "2024-01-15 10:30:00"
        }
    ]
}
```

## Error Responses

All error responses follow this format:
```json
{
    "status": "error",
    "message": "Error description",
    "timestamp": "2024-01-15 10:30:00"
}
```

Common HTTP Status Codes:
- 200: Success
- 400: Bad Request (validation errors)
- 401: Unauthorized (invalid API key)
- 404: Not Found
- 405: Method Not Allowed
- 500: Internal Server Error

## WhatsApp Bot Integration Example

### Example 1: Creating a jurnal from WhatsApp message
When a user sends a message like:
```
"Jurnal hari ini
Tanggal: 15 Januari 2024
Guru: Ahmad Fauzi
Kelas: XII IPA 1
Mapel: Matematika
Materi: Pembahasan Soal Matematika Kelas 12
Jumlah Siswa: 25
Keterangan: Siswa antusias mengikuti pembelajaran"
```

The bot should:
1. Parse the message to extract the data
2. Get the IDs for guru, kelas, and mapel from the respective API endpoints
3. Send a POST request to `/api/jurnal/create` with the extracted data

### Example 2: Requesting jurnal list
When a user sends: "Lihat jurnal hari ini"

The bot should:
1. Send a GET request to `/api/jurnal/list` with today's date filter
2. Format the response in a user-friendly way for WhatsApp

## Notes
- All dates should be in YYYY-MM-DD format
- Image uploads should be sent as base64 encoded strings
- The API key `whatsapp_bot_key_2024` should be kept secure
- The `created_by` field defaults to 1 (Admin) if not specified