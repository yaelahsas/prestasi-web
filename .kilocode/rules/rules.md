# rules.md

Standar Pengembangan Aplikasi CodeIgniter 3

## Tujuan

Dokumen ini digunakan sebagai pedoman penulisan kode agar:

* Kode mudah dibaca developer lain
* Struktur proyek rapi dan konsisten
* Mudah dikembangkan dan di-maintenance
* Tampilan modern menggunakan Tailwind CSS
* Interaksi aplikasi terasa tanpa reload (AJAX / Fetch API)

---

## 1. Struktur Folder Wajib

```
application/
│
├── modules/
│   ├── users/
│   │   ├── controllers/
│   │   │   └── Users.php
│   │   ├── models/
│   │   │   └── Users_model.php
│   │   ├── views/
│   │   │   ├── index.php
│   │   │   ├── form.php
│   │   │   └── js/
│   │   │       └── users.js
│   │   └── config/
│   │       └── routes.php
│   │
│   ├── produk/
│   └── laporan/
│
├── core/
├── helpers/
└── config/
```

### Aturan:

* **1 modul = 1 fitur**
* Tidak boleh mencampur logic antar modul
* Setiap modul berdiri sendiri (controller, model, view, js)

---

## 2. Penamaan File dan Class

### Controller

```php
Users.php
class Users extends CI_Controller
```

### Model

```php
Users_model.php
class Users_model extends CI_Model
```

### Method

* Gunakan **snake_case**
* Nama harus jelas

```php
get_data()
save_data()
delete_data()
```

❌ `proses()`, `tes()`, `data1()`
✅ `insert_user()`, `get_user_by_id()`

---

## 3. Aturan Controller

Controller **hanya mengatur alur**, bukan logika berat.

### Wajib:

* Ambil request
* Validasi ringan
* Lempar ke model
* Return JSON

Contoh:

```php
public function get_data()
{
    // Ambil request dari ajax
    $id = $this->input->post('id');

    // Panggil model
    $data = $this->Users_model->get_by_id($id);

    // Kembalikan response JSON
    echo json_encode($data);
}
```

❌ Jangan query langsung di controller
❌ Jangan HTML panjang di controller

---

## 4. Aturan Model

Model adalah tempat **semua proses database**.

### Wajib:

* Query DB
* Logic bisnis
* Return data ke controller

Contoh:

```php
public function get_by_id($id)
{
    // Ambil satu data user berdasarkan ID
    return $this->db
        ->where('id', $id)
        ->get('users')
        ->row();
}
```

---

## 5. Struktur View (WAJIB DIPISAH)

View harus dipisah menjadi:

```
views/
├── index.php        → halaman utama
├── form.php         → modal / form input
└── js/
    └── users.js     → semua javascript
```

### Aturan:

* ❌ Tidak boleh script JS panjang di index.php
* ❌ Tidak boleh query database di view
* ✅ View hanya untuk tampilan

---

## 6. Integrasi JavaScript (Full JS / No Reload)

Gunakan:

* Fetch API / AJAX
* JSON response
* Tanpa reload halaman

Contoh:

```javascript
function loadData() {
    fetch(base_url + 'users/get_data')
        .then(res => res.json())
        .then(data => {
            console.log(data);
        });
}
```

### Aturan:

* Semua JS masuk ke folder `views/js`
* Satu modul = satu file JS
* Tidak boleh inline JS berlebihan

---

## 7. Standar Komentar (WAJIB)

Setiap function **wajib ada komentar**.

### PHP

```php
/**
 * Fungsi untuk menyimpan data user
 * @return json
 */
public function save_data()
{
    ...
}
```

### JavaScript

```javascript
/**
 * Mengambil data user dari server
 */
function getUser() {
    ...
}
```

---

## 8. Tailwind CSS

### Aturan:

* Semua UI menggunakan **Tailwind CSS**
* Tidak menggunakan Bootstrap
* Class harus konsisten

Contoh:

```html
<button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
    Simpan
</button>
```

---

## 9. Response API Standar

Semua response AJAX harus seragam:

```json
{
  "status": true,
  "message": "Data berhasil disimpan",
  "data": []
}
```

Contoh di controller:

```php
echo json_encode([
    'status' => true,
    'message' => 'Berhasil',
    'data' => $data
]);
```

---

## 10. Larangan Keras 🚫

* ❌ Query database di view
* ❌ Logic berat di controller
* ❌ Campur JS dan HTML berlebihan
* ❌ Satu file JS untuk semua modul
* ❌ Nama function tidak jelas

---

## 11. Prinsip Utama

> “Controller tipis, Model kuat, View bersih.”

Kalau satu file mulai ribet:
➡️ pecah
➡️ rapikan
➡️ jangan maksa satu file jadi dewa

---

## 12. Catatan Akhir

* Semua fitur harus berbasis modul
* Semua interaksi menggunakan AJAX / Fetch
* Kode harus bisa dipahami developer lain tanpa dijelasin
* Kode bagus itu bukan yang pintar, tapi yang **mudah dibaca**

---
