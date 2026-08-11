# SIMM — Sistem Informasi Manajemen Maintenance Motor

> Aplikasi web berbasis **Laravel 11** untuk manajemen preventive maintenance motor listrik industri, dilengkapi fitur keamanan data (*Data Protection*) berupa **Password Hashing**, **CAPTCHA Login**, **Rate Limiting**, dan **Digital Signature** untuk deteksi tampering data.

---

## 📋 Daftar Isi

- [Tech Stack](#-tech-stack)
- [Fitur Utama](#-fitur-utama)
- [Fitur Keamanan (Data Protection)](#-fitur-keamanan-data-protection)
- [Struktur Database](#-struktur-database)
- [Role & Hak Akses](#-role--hak-akses)
- [Instalasi](#-instalasi)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Perintah Artisan Kustom](#-perintah-artisan-kustom)
- [Struktur Direktori](#-struktur-direktori)
- [Default Login](#-default-login)
- [Lisensi](#-lisensi)

---

## 🛠 Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | PHP 8.2, Laravel 11 |
| Database | MySQL 8 |
| Frontend | Bootstrap 5, Feather Icons, Font Awesome 4, Viho Admin Template |
| WebSocket | Laravel Reverb |
| PDF Export | barryvdh/laravel-dompdf |
| Excel Export | maatwebsite/laravel-excel |
| Queue | Database Queue |
| Auth | Session-based (tanpa Breeze/Jetstream) |

---

## ✨ Fitur Utama

### Dashboard
- Stat cards: total motor, jadwal pending, overdue, dan maintenance bulan ini
- Tabel jadwal mendekati deadline (H-3) — Admin melihat link ke Schedules, Teknisi melihat link ke Maintenance Input
- Ringkasan sistem: total log, total user, jumlah admin & teknisi
- Grafik tren maintenance 6 bulan terakhir (Bar Chart)
- Distribusi status jadwal (Donut Chart)
- Tabel 5 log maintenance terbaru
- Real-time toast notification via Laravel Reverb WebSocket

### Motors *(Admin only)*
- CRUD lengkap motor industri (kode motor, lokasi, area, kategori HP)
- Soft delete dengan kemampuan restore & force delete
- Riwayat maintenance per motor

### Schedules *(Admin only)*
- CRUD jadwal maintenance per motor
- Filter status (pending / overdue / done)
- Auto-deteksi overdue via Artisan scheduler

### Maintenance Input *(Teknisi only)*
- Pilih jadwal → data motor otomatis terisi
- Checklist aktivitas maintenance (dari tabel `activities`)
- Upload foto bukti inspeksi
- Digital Signature otomatis di-generate saat log disimpan
- Hapus log maintenance beserta foto

### Reports *(Admin & Teknisi)*
- Filter berdasarkan bulan dan motor
- Export ke **PDF** (DomPDF) dan **Excel** (Maatwebsite)
- Export PDF per-log dari halaman detail maintenance

---

## 🔐 Fitur Keamanan (Data Protection)

### 1. Password Hashing

Semua password user di-hash menggunakan algoritma **Bcrypt** sebelum disimpan ke database. Tidak ada password yang pernah tersimpan dalam bentuk plaintext.

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

### 2. CAPTCHA pada Login

Setiap percobaan login dilindungi oleh **CAPTCHA SVG dinamis** yang di-generate server-side (tanpa library pihak ketiga). CAPTCHA terdiri dari 5 karakter alphanumeric acak dengan distorsi, noise line, dan noise dot untuk mencegah bot otomatis.

**Alur validasi:**
```
[User input CAPTCHA] -> dibandingkan dengan session('captcha_code')
         |
GAGAL -> CAPTCHA di-refresh + pesan error
         |
BERHASIL -> lanjut verifikasi username/password
```

---

### 3. Rate Limiting Login

Sistem membatasi percobaan login sebanyak **5 kali** per kombinasi username + IP address. Jika batas terlampaui, akun dikunci sementara selama **5 menit**.

| Kondisi | Respon Sistem |
|---|---|
| CAPTCHA salah | Error + CAPTCHA di-refresh |
| Password salah (1-4x) | Error + sisa percobaan ditampilkan |
| Password salah (5x) | Akun terkunci 5 menit |
| Akun terkunci, akses ulang | Tampilkan sisa waktu tunggu |

---

### 4. Digital Signature

Setiap maintenance log yang disimpan mendapatkan **Digital Signature (HMAC-SHA256)** yang di-generate dari field-field krusial. Jika data diubah secara ilegal (via phpMyAdmin, MySQL CLI, dll.), sistem menampilkan status verifikasi **invalid** di halaman detail log.

**Field yang diproteksi per log:**
```
admin_id | motor_id | schedule_id | inspection_date | general_notes
```

**Cara kerja:**
```
[Teknisi simpan Maintenance Log]
         |
generateSignature() -> HMAC-SHA256 dari payload + APP_KEY
         |
digital_signature + payload_snapshot (JSON) disimpan ke DB
         |
Saat view detail -> verifySignature() -> re-compute & compare
         |
VALID   -> tampilkan badge "Terverifikasi"
INVALID -> tampilkan badge "Signature Tidak Valid"
```

> **Catatan:** `APP_KEY` digunakan sebagai secret key HMAC. Jangan ganti key ini setelah data tersimpan — semua signature akan invalid.

---

## 🗄 Struktur Database

```
users                        — Akun admin & teknisi (role: admin | teknisi)
motors                       — Data motor industri (soft delete)
schedules                    — Jadwal maintenance per motor
activities                   — Master daftar aktivitas checklist
maintenance_logs             — Log inspeksi (+ digital_signature + payload_snapshot)
maintenance_activity_details — Detail checklist per log
jobs                         — Queue jobs (untuk broadcast WebSocket)
sessions                     — Session database
cache                        — Cache database
```

---

## 👤 Role & Hak Akses

| Menu / Fitur | Admin | Teknisi |
|---|:---:|:---:|
| Dashboard | ✅ | ✅ |
| Motors (CRUD) | ✅ | ❌ |
| Schedules (CRUD) | ✅ | ❌ |
| Maintenance Input (CRUD) | ❌ | ✅ |
| Reports & Export PDF/Excel | ✅ | ✅ |
| Export PDF per-log (dari detail) | ❌ | ✅ |
| Manajemen Profil | ✅ | ✅ |

**Middleware Stack:**
- Semua halaman protected oleh middleware `admin` (cek autentikasi)
- Halaman admin-only: tambah middleware `role:admin`
- Halaman teknisi-only: tambah middleware `role:teknisi`
- Semua role diarahkan ke `/dashboard` setelah login

---

## 🚀 Instalasi

### Prasyarat

- PHP >= 8.2 (dengan ekstensi: `zip`, `pdo_mysql`, `gd`)
- Composer
- Node.js & NPM
- MySQL 8

### Langkah-langkah

**1. Clone repository**
```bash
git clone https://github.com/olivvard/SIMM.git
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
> **Penting:** `APP_KEY` digunakan sebagai secret key untuk Digital Signature HMAC-SHA256. Jangan ganti key ini setelah data maintenance log tersimpan.

**6. Buat database MySQL**
```sql
CREATE DATABASE motor_pm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**7. Jalankan migrasi**
```bash
php artisan migrate
```

**8. Seed database (data awal: user admin + daftar aktivitas)**
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

| Field | Value |
|---|---|
| Username | `admin` |
| Password | `admin123` |

> Ganti password setelah login pertama kali melalui halaman Profile.

---

## 🧰 Perintah Artisan Kustom

| Perintah | Keterangan |
|---|---|
| `php artisan maintenance:backfill-signatures` | Generate / regenerasi Digital Signature + Payload Snapshot untuk semua maintenance log yang sudah ada. Wajib dijalankan setelah pertama kali mengaktifkan fitur Digital Signature pada data yang sudah ada, atau setelah `APP_KEY` berubah. |
| `php artisan schedules:check-deadlines` | Cek semua jadwal: tandai yang overdue, broadcast WebSocket alert untuk yang mendekati deadline (H-3). |

---

## 📁 Struktur Direktori

```
app/
├── Console/
│   └── Commands/
│       ├── BackfillMaintenanceSignatures.php   <- Artisan: backfill signatures
│       └── CheckScheduleDeadlines.php          <- Artisan: cek deadline jadwal
├── Events/
│   └── ScheduleAlert.php                       <- Broadcast event via Reverb
├── Exports/
│   └── MaintenanceExport.php                   <- Excel export
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php                  <- Login (CAPTCHA, Rate Limit), Register, Logout
│   │   ├── DashboardController.php             <- Dashboard stats & charts
│   │   ├── MaintenanceLogController.php        <- CRUD + Digital Signature
│   │   ├── MotorController.php                 <- CRUD motors (soft delete)
│   │   ├── ScheduleController.php              <- CRUD schedules
│   │   ├── ReportController.php                <- Filter, export PDF & Excel
│   │   └── ProfileController.php              <- Update foto profil
│   └── Middleware/
│       ├── AdminAuth.php                       <- Cek autentikasi (semua role)
│       └── CheckRole.php                       <- Cek role spesifik (admin/teknisi)
└── Models/
    ├── User.php
    ├── Motor.php
    ├── Schedule.php
    ├── Activity.php
    ├── MaintenanceLog.php                      <- generateSignature(), verifySignature(), getDiff()
    └── MaintenanceActivityDetail.php

database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_motors_table.php
│   ├── ..._create_schedules_table.php
│   ├── ..._create_activities_table.php
│   ├── ..._create_maintenance_logs_table.php
│   ├── ..._create_maintenance_activity_details_table.php
│   ├── ..._create_jobs_table.php
│   ├── ..._create_sessions_table.php
│   ├── ..._create_cache_table.php
│   ├── 2026_07_20_000001_add_digital_signature_to_maintenance_logs_table.php
│   ├── 2026_07_20_000003_add_payload_snapshot_to_maintenance_logs_table.php
│   ├── 2026_07_25_000001_add_role_to_users_table.php
│   └── 2026_08_11_..._drop_activity_logs_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php                          <- User admin & teknisi default
    └── ActivitySeeder.php                      <- 15 aktivitas checklist maintenance

resources/views/
├── auth/             <- Login (+ CAPTCHA SVG), Register
├── dashboard/        <- Dashboard utama (role-aware)
├── motors/           <- CRUD motors (admin only)
├── schedules/        <- CRUD schedules (admin only)
├── maintenance/      <- Input, list, dan detail log + Export PDF (teknisi only)
├── reports/          <- Filter & export PDF/Excel (semua role)
├── layouts/
│   └── app.blade.php <- Layout utama
├── components/
│   ├── navbar.blade.php
│   ├── sidebar.blade.php  <- Role-aware menu (feather icons)
│   ├── head-css.blade.php
│   ├── vendor.blade.php   <- JS vendors + Feather icon re-render fix + Mobile sidebar fix
│   └── footer.blade.php
└── user/             <- Profile

routes/
├── web.php           <- RBAC routes (admin: motors+schedules, teknisi: maintenance, semua: dashboard+reports)
├── channels.php      <- Reverb channel authorization
└── console.php       <- Scheduled commands
```

---

## 📄 Lisensi

MIT License — bebas digunakan dan dimodifikasi.

---

<div align="center">
  <sub>Built with Laravel 11 · Dikembangkan untuk keperluan Kerja Praktek (KP) — PT WEA Indonesia</sub>
</div>
