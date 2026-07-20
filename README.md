# SIMM — Sistem Informasi Manajemen Maintenance Motor

> Aplikasi web berbasis **Laravel 11** untuk manajemen preventive maintenance motor listrik industri, dilengkapi fitur keamanan data (*Data Protection*) berupa **Password Hashing**, **Digital Signature**, dan **Activity Log**.

---

## 📋 Daftar Isi

- [Tech Stack](#-tech-stack)
- [Fitur Utama](#-fitur-utama)
- [Fitur Keamanan (Data Protection)](#-fitur-keamanan-data-protection)
- [Struktur Database](#-struktur-database)
- [Instalasi](#-instalasi)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Perintah Artisan Kustom](#-perintah-artisan-kustom)
- [Struktur Direktori](#-struktur-direktori)
- [Lisensi](#-lisensi)

---

## 🛠 Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | PHP 8.2, Laravel 11 |
| Database | MySQL 8 |
| Frontend | Bootstrap 5, Feather Icons, Viho Admin Template |
| WebSocket | Laravel Reverb |
| PDF Export | barryvdh/laravel-dompdf |
| Excel Export | maatwebsite/laravel-excel |
| Queue | Database Queue |
| Auth | Session-based (tanpa Breeze/Jetstream) |

---

## ✨ Fitur Utama

### Dashboard
- Stat cards: total motor, jadwal pending, overdue, dan maintenance bulan ini
- Tabel jadwal mendekati deadline (H-3)
- Real-time toast notification via Laravel Reverb WebSocket

### Motors
- CRUD lengkap motor industri (kode motor, lokasi, area, kategori HP)
- Soft delete dengan kemampuan restore
- Riwayat maintenance per motor

### Schedules
- CRUD jadwal maintenance
- Filter status (pending/overdue/done)
- Auto-deteksi overdue via Artisan scheduler

### Maintenance Input
- Pilih jadwal → data motor otomatis terisi
- Checklist 15 aktivitas maintenance
- Upload foto bukti inspeksi
- Digital Signature otomatis di-generate saat log disimpan

### Reports
- Filter berdasarkan bulan dan motor
- Export ke **PDF** (DomPDF) dan **Excel** (Maatwebsite)

### Activity Log
- Rekam jejak seluruh aktivitas admin (login, logout, CRUD, dll)
- Deteksi dan pencatatan akses ilegal / tampering data
- Filter berdasarkan status, modul, dan keyword

---

## 🔐 Fitur Keamanan (Data Protection)

### 1. Password Hashing

Semua password admin di-hash menggunakan algoritma **Bcrypt** sebelum disimpan ke database. Tidak ada password yang pernah tersimpan dalam bentuk plaintext.

**Implementasi:**
```php
// app/Models/User.php
protected function casts(): array {
    return ['password' => 'hashed']; // Laravel auto-hash via Bcrypt
}

// app/Http/Controllers/AuthController.php — Register
'password' => Hash::make($validated['password'])

// Login — verifikasi hash otomatis
Auth::attempt(['username' => ..., 'password' => ...])
```

**Konfigurasi Bcrypt rounds** (default 12, dapat diubah di `.env`):
```env
BCRYPT_ROUNDS=12
```

---

### 2. Digital Signature

Setiap maintenance log yang disimpan akan mendapatkan **Digital Signature (HMAC-SHA256)** yang di-generate dari field-field krusial. Jika data diubah secara ilegal (via phpMyAdmin, MySQL CLI, Tinker, SQL Injection, dll.), sistem akan mendeteksi pelanggaran integritas data secara otomatis.

**Field yang diproteksi per log:**
```
admin_id | motor_id | schedule_id | inspection_date | general_notes
```

**Cara kerja:**

```
[Admin simpan Maintenance Log]
         ↓
generateSignature() → HMAC-SHA256 dari payload
         ↓
digital_signature + payload_snapshot (JSON) disimpan ke DB
         ↓
─────────── Setiap 30 detik ───────────
Browser → GET /integrity-check
         ↓
verifySignature() → re-generate HMAC, compare dengan yang tersimpan
         ↓
MISMATCH → getDiff() → Before/After diff per field
         ↓
Banner merah ⚠️ muncul di semua halaman + dicatat ke Activity Log
```

**Before/After Diff** — saat tampering terdeteksi, sistem menampilkan:

| Field | Sebelum (Asli) | Sesudah (Diubah) |
|---|---|---|
| Catatan Umum | Motor normal, tidak ada kerusakan | DATA DIUBAH VIA PHPMYADMIN |

**Vektor illegal yang terdeteksi:**
- phpMyAdmin / MySQL Workbench / DBeaver / HeidiSQL / TablePlus
- MySQL CLI (`mysql -u root -p`)
- `php artisan tinker` dengan raw `DB::table()->update()`
- SQL Injection (jika ada celah di form)
- SSH direct query ke database
- Script PHP lain di server yang bypass Laravel
- Restore backup lama yang menimpa data

---

### 3. Activity Log

Seluruh aktivitas admin dicatat otomatis ke tabel `activity_logs` dengan informasi: waktu, modul, aksi, deskripsi, status keparahan, IP address, dan user agent.

| Aksi | Modul | Status |
|---|---|---|
| Login berhasil | Auth | 🟢 Normal |
| Login gagal (percobaan akses) | Auth | 🟡 Warning |
| Register admin baru | Auth | 🟢 Normal |
| Logout | Auth | 🟢 Normal |
| Buat maintenance log | Maintenance | 🟢 Normal |
| Hapus maintenance log | Maintenance | 🟡 Warning |
| **Tampering terdeteksi** | Integrity | 🔴 **Danger** |

Deskripsi tampering di Activity Log menyertakan detail perubahan:
> *PELANGGARAN INTEGRITAS: Log #3 (Motor: P-002) terdeteksi telah dimodifikasi langsung di database. Perubahan: [Catatan Umum] 'Motor normal' → 'DATA DIUBAH!'*

---

## 🗄 Struktur Database

```
users                   — Akun admin
motors                  — Data motor industri (soft delete)
schedules               — Jadwal maintenance per motor
activities              — Master daftar aktivitas checklist
maintenance_logs        — Log inspeksi (+ digital_signature + payload_snapshot)
maintenance_activity_details — Detail checklist per log
activity_logs           — Rekam jejak semua aktivitas & kejadian keamanan
```

---

## 🚀 Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 8

### Langkah-langkah

**1. Clone repository**
```bash
git clone https://github.com/username/SIMM.git
cd SIMM
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi Node**
```bash
npm install
```

**4. Salin dan konfigurasi file environment**
```bash
cp .env.example .env
```

**5. Generate application key**
```bash
php artisan key:generate
```
> ⚠️ **Penting:** `APP_KEY` digunakan sebagai secret key untuk Digital Signature HMAC-SHA256. Jangan ganti key ini setelah data maintenance log tersimpan — semua signature akan invalid.

**6. Buat database MySQL**
```sql
CREATE DATABASE motor_pm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**7. Jalankan migrasi**
```bash
php artisan migrate
```

**8. Seed database (data awal)**
```bash
php artisan db:seed
```

**9. Buat storage symlink**
```bash
php artisan storage:link
```

**10. Buat direktori upload**
```bash
mkdir -p storage/app/public/maintenance
```

**11. Build frontend assets**
```bash
# Development
npm run dev

# Production
npm run build
```

**12. Generate Digital Signature untuk data yang sudah ada**

Jalankan setelah migrasi jika sudah ada data maintenance log sebelumnya:
```bash
php artisan maintenance:backfill-signatures
```

---

## ⚙️ Konfigurasi Environment

Edit file `.env` sesuai kebutuhan:

```env
# Aplikasi
APP_NAME="SIMM - Motor PM System"
APP_ENV=production
APP_KEY=                          # Di-generate otomatis via key:generate
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=motor_pm
DB_USERNAME=root
DB_PASSWORD=your_password

# Queue (wajib untuk broadcast)
QUEUE_CONNECTION=database

# WebSocket — Laravel Reverb
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=motor-pm-app
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Keamanan Password
BCRYPT_ROUNDS=12
```

---

## ▶️ Menjalankan Aplikasi

Jalankan masing-masing di terminal terpisah:

**1. Web server (development)**
```bash
php artisan serve
```

**2. Queue worker** (untuk broadcast real-time)
```bash
php artisan queue:work --tries=3
```

**3. Reverb WebSocket server**
```bash
php artisan reverb:start
```

**4. (Opsional) Scheduler** — cek deadline jadwal otomatis

Tambahkan ke crontab server:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan manual:
```bash
php artisan schedules:check-deadlines
```

---

## 🔑 Default Login

| Field    | Value      |
|----------|------------|
| Username | `admin`    |
| Password | `admin123` |

> ⚠️ Ganti password setelah login pertama kali.

---

## 🧰 Perintah Artisan Kustom

| Perintah | Keterangan |
|---|---|
| `php artisan maintenance:backfill-signatures` | Generate/regenerasi Digital Signature + Payload Snapshot untuk semua maintenance log yang ada. Wajib dijalankan setelah pertama kali menambahkan fitur Digital Signature ke data yang sudah ada. |
| `php artisan schedules:check-deadlines` | Cek semua jadwal: tandai yang overdue, broadcast alert untuk yang mendekati deadline (≤ 3 hari). |

---

## 📁 Struktur Direktori

```
app/
├── Console/
│   └── Commands/
│       ├── BackfillMaintenanceSignatures.php   ← Artisan: backfill signatures
│       └── CheckScheduleDeadlines.php          ← Artisan: cek deadline jadwal
├── Events/
│   └── ScheduleAlert.php                       ← Broadcast event via Reverb
├── Exports/
│   └── MaintenanceExport.php                   ← Excel export
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php                  ← Login, Register, Logout + Activity Log
│   │   ├── DashboardController.php
│   │   ├── IntegrityController.php             ← Polling endpoint /integrity-check
│   │   ├── ActivityLogController.php           ← Halaman Activity Log
│   │   ├── MaintenanceLogController.php        ← CRUD + Digital Signature
│   │   ├── MotorController.php
│   │   ├── ScheduleController.php
│   │   ├── ReportController.php
│   │   └── ProfileController.php
│   └── Middleware/
│       └── AdminAuth.php
└── Models/
    ├── User.php
    ├── Motor.php
    ├── Schedule.php
    ├── Activity.php
    ├── MaintenanceLog.php                      ← generateSignature(), verifySignature(), getDiff()
    ├── MaintenanceActivityDetail.php
    └── ActivityLog.php                         ← Static helper record()

database/
└── migrations/
    ├── ..._create_users_table.php
    ├── ..._create_motors_table.php
    ├── ..._create_schedules_table.php
    ├── ..._create_activities_table.php
    ├── ..._create_maintenance_logs_table.php
    ├── ..._create_maintenance_activity_details_table.php
    ├── 2026_07_20_000001_add_digital_signature_to_maintenance_logs_table.php
    ├── 2026_07_20_000002_create_activity_logs_table.php
    └── 2026_07_20_000003_add_payload_snapshot_to_maintenance_logs_table.php

resources/views/
├── auth/             ← Login, Register
├── dashboard/        ← Dashboard utama
├── motors/           ← CRUD motors
├── schedules/        ← CRUD schedules
├── maintenance/      ← Input & detail log
├── activity-logs/    ← Halaman Activity Log (Security)
├── reports/          ← Filter & export
├── layouts/
│   └── app.blade.php ← Layout utama + Tampering Alert Banner
├── components/
│   ├── navbar.blade.php
│   ├── sidebar.blade.php
│   ├── head-css.blade.php
│   ├── vendor.blade.php  ← JS + Mobile Sidebar Fix
│   └── footer.blade.php
└── user/             ← Profile

routes/
├── web.php           ← Semua route (protected middleware admin)
├── channels.php      ← Reverb channel authorization
└── console.php       ← Scheduled commands
```

---

## 📄 Lisensi

MIT License — bebas digunakan dan dimodifikasi.

---

<div align="center">
  <sub>Built with Laravel 11 · Dikembangkan untuk keperluan Kerja Praktek (KP)</sub>
</div>
