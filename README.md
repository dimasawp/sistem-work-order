# 📋 Work Order System (Laravel 12)

## 🚀 Deskripsi

Sistem Work Order adalah sistem berbasis web yang dibangun menggunakan Laravel 12 yang bertujuan untuk membantu perusahaan dalam mengelola dan memantau status pekerjaan secara efisien (paperless).

## ✨ Features

-   🔑 Autentikasi Pengguna: Pengguna dapat melakukan register, login, logout dan mengelola akun mereka.
-   🧾 Manajemen Work Order: Buat, edit, dan hapus work order dengan mudah menggunaakn modal + livewire.
-   📊 Status Pekerjaan: Tentukan dan pantau status pekerjaan untuk setiap work order.
-   📅 Kalender Jadwal (FullCalendar.js)
    -   Menampilkan jadwal pekerjaan dalam bentuk kalender interaktif.
    -   Drag & Drop pekerjaan ke calendar untuk menjadwalkan.
-   📑 Laporan: Generate laporan excel berkala mengenai status dan progres pekerjaan.
-   🦺 List daftar Karyawan dengan fitur sinkronisasi dengan api

## 🛠 Tech Stack

-   Backend: Laravel 12 (PHP 8.2+)
-   Frontend: Bootstrap & livewire
-   Database: MySQL
-   Library lainnya: Spatie/Laravel-Permission, Laravel Excel, FullCalendar.js

## ⚡ Quickstart

### 1️⃣ Requirements

-   PHP 8.2+
-   Composer
-   MySQL

### 2️⃣ Instalasi

```bash
# Kloning repositori
git clone https://github.com/dimasawp/sistem-work-order.git
cd sistem-work-order

# Instal dependensi
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3️⃣ Database & Running

```bash
# Konfigurasi database di .env lalu jalankan:
php artisan migrate --seed

# Jalankan server Laravel lokal
php artisan serve
```

## Struktur Folder

Berikut adalah tree dari struktur folder utama proyek (disederhanakan):

```
sistem-work-order/
├── app/
│   ├── Export/
│   │   └── JobsExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── JobController.php
│   │   │   └── UserController.php
│   ├── Models/
│   │   ├── Department.php
│   │   ├── DepartmentSubDepartment.php
│   │   ├── Employee.php
│   │   ├── Job.php
│   │   ├── SubDepartment.php
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── View /
│       └── Components /
│           └── JobModal.php
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_08_26_074643_craate_employees_table.php
│   │   ├── 2025_08_28_053942_create_permission_tables.php
│   │   ├── 2025_08_28_060223_create_departments_and_sub_departments_tables.php
│   │   ├── 2025_08_28_060803_add_foreign_keys_to_users_and_employees.php
│   │   ├── 2025_08_29_074833_craate_jobs_table.php
│   │   └── 2025_08_29_075834_craate_job_receivers_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── DepartmentSeeder.php
│       ├── RolePermissionSeeder.php
│       └── UserSeeder.php
├── public/
│   ├── bootstrap/
│   │   ├── css/
│   │   └── js/
│   ├── css/
│   ├── js/
│   │   ├── fullcaledar/
│   │   └── job-modal.js
│   ├── .htaccess
│   └── index.php
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── components/
│       │   ├── job-modal.blade.php
│       │   └── toast.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       ├── pages/
│       │   ├── dashboard.blade.php
│       │   ├── deliver-job.blade.php
│       │   ├── employee.blade.php
│       │   ├── job-history.blade.php
│       │   ├── job-received.blade.php
│       │   ├── landing.blade.php
│       │   ├── profile.blade.php
│       │   └── register.blade.php
│       └── vendor/
│           └── livewire/
│               ├── bootstrap.blade.php
│               ├── simple-bootstrap.blade.php
│               ├── simple-tailwind.blade.php
│               └── tailwind.blade.php
├── routes/
│   ├── web.php
│   └── api.php
├── .env.example
├── composer.json
├── package.json
├── artisan
└── README.md
```

## 🏆 Best Practices

-   memanfaatkan spatie laravel permission untuk memberi role dan juga permission terhadap user.
-   menggunakan laravel/excel untuk dalam fitur generate laporan job dengan format excel.
-   penggunaan livewire untuk membuat component yang digunakan berulang kali yaitu modal-job.
-   integrasi API data karyawan perusahaan dan juga mengamankan konfigurasi API

## 📝 Lisensi

MIT License © 2025
