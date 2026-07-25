# Tahap 2 — Laravel 10 + Breeze + Bootstrap + MySQL

Paket ini berisi file overlay dan script setup untuk membuat fondasi company profile.

## Stack

- Laravel 10
- PHP 8.1+
- MySQL
- Laravel Breeze 1.29.x, Blade stack
- Bootstrap 5
- Bootstrap Icons
- Vite
- Registrasi publik dinonaktifkan
- Login admin: `/admin/login`
- Dashboard admin: `/admin/dashboard`

## Opsi A — Windows PowerShell

Jalankan dari folder paket ini:

```powershell
Set-ExecutionPolicy -Scope Process Bypass
.\scripts\setup-stage2.ps1 -ProjectName company-profile
cd company-profile
```

## Opsi B — Linux, macOS, Git Bash, atau WSL

```bash
./scripts/setup-stage2.sh company-profile
cd company-profile
```

## Opsi C — Manual

```bash
composer create-project "laravel/laravel:^10.0" company-profile
cd company-profile
composer require "laravel/breeze:^1.29" --dev
php artisan breeze:install blade
```

Salin seluruh isi folder `overlay` ke root project, kemudian:

```bash
npm install
php artisan key:generate
npm run build
```

## Konfigurasi MySQL

Buat database:

```sql
CREATE DATABASE company_profile CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Ubah bagian database di `.env`:

```env
APP_NAME="Company Profile"
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=company_profile
DB_USERNAME=root
DB_PASSWORD=
```

Pada `config/app.php`, gunakan environment untuk timezone dan locale:

```php
'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
'locale' => env('APP_LOCALE', 'id'),
'fallback_locale' => 'en',
'faker_locale' => 'id_ID',
```

Lalu bersihkan cache konfigurasi:

```bash
php artisan config:clear
```

## Menjalankan aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Akses:

- Public placeholder: `http://127.0.0.1:8000`
- Login admin: `http://127.0.0.1:8000/admin/login`

## Verifikasi

```bash
php artisan --version
php artisan route:list
npm run build
```

Output versi Laravel harus berada pada major version 10.

## Catatan migration

Jangan menjalankan migration bisnis pada tahap ini. Pada Tahap 3 akan dibuat migration final untuk:

- users/admin
- site_settings
- pages
- page_sections
- page_section_items
- services
- portfolios
- posts
- contact_messages
- activity_logs

Migration bawaan `users` juga akan disesuaikan sebelum `php artisan migrate` pertama kali dijalankan.
