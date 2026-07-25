param(
    [string]$ProjectName = "company-profile"
)

$ErrorActionPreference = "Stop"
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$PackageDir = Split-Path -Parent $ScriptDir

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer belum tersedia. Install Composer terlebih dahulu."
}

if (Test-Path $ProjectName) {
    throw "Folder $ProjectName sudah ada. Gunakan nama folder lain atau hapus folder tersebut."
}

composer create-project "laravel/laravel:^10.0" $ProjectName
Set-Location $ProjectName
composer require "laravel/breeze:^1.29" --dev
php artisan breeze:install blade

Remove-Item "tailwind.config.js" -Force -ErrorAction SilentlyContinue
Remove-Item "postcss.config.js" -Force -ErrorAction SilentlyContinue
Copy-Item (Join-Path $PackageDir "overlay\*") "." -Recurse -Force
Copy-Item (Join-Path $PackageDir "overlay\.env.company-profile.example") ".env.company-profile.example" -Force

npm install
php artisan key:generate
npm run build

Write-Host ""
Write-Host "Setup selesai. Salin nilai MySQL dari .env.company-profile.example ke .env."
Write-Host "Migration akan dilanjutkan pada Tahap 3."
