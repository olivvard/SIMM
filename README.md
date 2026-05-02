# Motor Preventive Maintenance System

A complete Laravel 11 production application for managing motor preventive maintenance schedules, logs, and reports.

## Stack

- **PHP** 8.2
- **Laravel** 11
- **MySQL** 8
- **Bootstrap** 5.3
- **Laravel Reverb** (WebSockets)
- **barryvdh/laravel-dompdf** (PDF export)
- **maatwebsite/excel** (Excel export)
- **Database Queue**
- **Session Auth** (no Breeze/Jetstream)

---

## Installation Steps

### 1. Clone / Copy project
```bash
cd /your/server/root
# place files here
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies
```bash
npm install
```

### 4. Copy and configure `.env`
```bash
cp .env.example .env
```

Edit `.env` with your values:
```env
APP_NAME="Motor PM System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=motor_pm
DB_USERNAME=root
DB_PASSWORD=your_password

QUEUE_CONNECTION=database

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=motor-pm
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 5. Generate application key
```bash
php artisan key:generate
```

### 6. Create the database
```sql
CREATE DATABASE motor_pm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Run migrations
```bash
php artisan migrate
```

### 8. Seed the database
```bash
php artisan db:seed
```

### 9. Create storage symlink
```bash
php artisan storage:link
```

### 10. Create uploads directory
```bash
mkdir -p storage/app/public/maintenance
```

### 11. Build frontend assets
```bash
npm run build
```
*(For development: `npm run dev`)*

### 12. Start queue worker (in a separate terminal / supervisor)
```bash
php artisan queue:work --tries=3
```

### 13. Start Reverb WebSocket server (in a separate terminal / supervisor)
```bash
php artisan reverb:start
```

### 14. (Optional) Run the scheduler
Add to crontab for automated deadline checks:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or test manually:
```bash
php artisan schedules:check-deadlines
```

---

## Default Login

| Field    | Value                  |
|----------|------------------------|
| Username | `admin`                |
| Password | `admin123`             |

---

## Features

- **Dashboard** – Stat cards, upcoming schedules (H-3), real-time Reverb toast alerts
- **Motors** – Full CRUD with soft delete, search, complete maintenance history
- **Schedules** – CRUD with status filter, auto-overdue detection
- **Maintenance Input** – Schedule selection → auto-fill motor, 15-activity checklist, photo upload
- **Reports** – Monthly/motor filter, PDF (DomPDF) and Excel (Maatwebsite) export
- **Real-time** – ScheduleAlert event broadcasts approaching/overdue notices via Reverb
- **Artisan Command** – `schedules:check-deadlines` marks overdue and broadcasts approaching (≤3 days)

---

## Directory Structure (key paths)

```
app/
  Console/Commands/CheckScheduleDeadlines.php
  Events/ScheduleAlert.php
  Exports/MaintenanceExport.php
  Http/
    Controllers/
      AuthController.php
      DashboardController.php
      MotorController.php
      ScheduleController.php
      MaintenanceLogController.php
      ReportController.php
    Middleware/AdminAuth.php
  Models/
    User.php  Motor.php  Schedule.php
    Activity.php  MaintenanceLog.php  MaintenanceActivityDetail.php
bootstrap/app.php
database/
  migrations/  seeders/
resources/views/
  auth/  dashboard/  motors/  schedules/  maintenance/  reports/  layouts/
routes/web.php  routes/console.php
```
