<p align="center">
  <a href="https:linkeidn" target="_blank">
    <img src="https://img.shields.io/badge/LinkedIn-Connect-blue?style=for-the-badge&logo=linkedin" alt="LinkedIn">
  </a>
</p>

<p align="center">
  <a href="https://github.com/farizan-il/Technical-test-telenavi.git">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Laravel Version">
  </a>
  <a href="https://php.net/">
    <img src="https://img.shields.io/badge/PHP-%3E%3D8.1-777BB4.svg" alt="PHP Version">
  </a>
</p>

<h1 align="center">🚀 Technical Test: Todo List API</h1>

<p align="center">
  <strong>RESTful API untuk manajemen Todo List dengan fitur filtering, Excel report, dan chart data agregat</strong>
</p>

---

## 📖 Tentang Proyek

Proyek ini merupakan implementasi **Todo List API** sebagai bagian dari Technical Test Backend Developer. Sistem dibangun menggunakan **Laravel 12** dengan arsitektur RESTful API yang mendukung:

- ✅ CRUD operations untuk todo items
- 🔍 Advanced filtering dan searching
- 📊 Export data ke format Excel
- 📈 Data agregat untuk visualisasi chart
- ✨ Validasi data yang ketat

---

## 📋 Daftar Isi

- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi dan Konfigurasi](#-instalasi-dan-konfigurasi)
- [Struktur Database](#-struktur-database)
- [Dokumentasi API](#-dokumentasi-api)
- [Pengujian](#-pengujian)

---

## 🔧 Persyaratan Sistem

Pastikan lingkungan development Anda memenuhi persyaratan berikut:

| Komponen | Versi Minimum | Keterangan |
|----------|---------------|------------|
| PHP | 8.1 | Atau lebih baru |
| Composer | 2.x | Terinstal secara global |
| Database | MySQL 5.7+ | Atau MariaDB 10.3+ |
| Laravel | 10/11 | Framework utama |

**Package Dependencies:**
- `maatwebsite/excel` - Untuk ekspor laporan Excel

---

## ⚙️ Instalasi dan Konfigurasi

### 1️⃣ Clone Repositori

```bash
git clone https://github.com/farizan-il/Technical-test-telenavi.git
cd Technical-test-telenavi
```

### 2️⃣ Instalasi Dependencies

```bash
composer install
```

### 3️⃣ Konfigurasi Environment

**Buat file `.env`:**
```bash
cp .env.example .env
php artisan key:generate
```

**Konfigurasi Database:**

Edit file `.env` dan sesuaikan dengan kredensial database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

### 4️⃣ Setup Database

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

### 5️⃣ Jalankan Server

```bash
php artisan serve
```

Aplikasi akan tersedia di: **http://127.0.0.1:8000**

---

## 🗄️ Struktur Database

### Tabel: `todos`

| Field | Tipe Data | Keterangan | Validasi |
|-------|-----------|------------|----------|
| `id` | BIGINT | Primary Key | Auto Increment |
| `title` | VARCHAR(255) | Judul todo | **Required** |
| `assignee` | VARCHAR(255) | Nama pelaksana | Optional |
| `due_date` | DATE | Batas waktu penyelesaian | **Required**, tidak boleh masa lalu |
| `time_tracked` | INTEGER | Waktu yang dilacak (menit) | Default: `0` |
| `status` | ENUM | Status todo | `pending` (default), `open`, `in_progress`, `completed` |
| `priority` | ENUM | Prioritas | **Required**: `low`, `medium`, `high` |
| `created_at` | TIMESTAMP | Waktu dibuat | Auto |
| `updated_at` | TIMESTAMP | Waktu diupdate | Auto |

---

## 📡 Dokumentasi API

### 📦 Postman Collection

File Postman Collection tersedia di folder `docs/` untuk memudahkan testing API.

**Lokasi:** `docs/Todo_API_Collection.json`

**Cara Import:**
1. Buka Postman
2. Klik **Import** di pojok kiri atas
3. Pilih file `docs/Todo_API_Collection.json` dari project folder
4. Klik **Import**
5. Collection siap digunakan! 🚀

## 🎯 Fitur Utama

- 🔐 **Validasi Ketat** - Form Request dengan rule yang comprehensive
- 🔍 **Advanced Filtering** - Multiple filter dengan kombinasi fleksibel
- 📊 **Excel Export** - Laporan lengkap dengan summary row
- 📈 **Chart Data** - Data agregat untuk berbagai tipe visualisasi
- ✅ **Well Tested** - Coverage testing yang lengkap
- 📝 **Clean Code** - Mengikuti Laravel best practices
- 🚀 **RESTful API** - Design API yang konsisten dan mudah digunakan

---

## 📞 Kontak

**ILHAM FARIZAN**

- 💼 LinkedIn: 
- 📧 Email: 

---

<p align="center">
  <strong>Terima kasih atas kesempatan untuk menyelesaikan technical test ini!</strong><br>
  Saya siap untuk presentasi dan sesi Q&A 🙏
</p>
