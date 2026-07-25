#!/usr/bin/env bash
set -euo pipefail

PROJECT_NAME="${1:-company-profile}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PACKAGE_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

if ! command -v composer >/dev/null 2>&1; then
    echo "Composer belum tersedia. Install Composer terlebih dahulu."
    exit 1
fi

if [ -e "$PROJECT_NAME" ]; then
    echo "Folder $PROJECT_NAME sudah ada. Gunakan nama folder lain atau hapus folder tersebut."
    exit 1
fi

composer create-project "laravel/laravel:^10.0" "$PROJECT_NAME"
cd "$PROJECT_NAME"
composer require "laravel/breeze:^1.29" --dev
php artisan breeze:install blade
rm -f tailwind.config.js postcss.config.js
cp -R "$PACKAGE_DIR/overlay/." .
npm install
php artisan key:generate
npm run build

echo ""
echo "Setup selesai. Salin nilai MySQL dari .env.company-profile.example ke .env, lalu jalankan:"
echo "  php artisan config:clear"
echo "  php artisan serve"
echo "Migration akan dilanjutkan pada Tahap 3."
