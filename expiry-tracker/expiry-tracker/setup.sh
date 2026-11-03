#!/usr/bin/env bash
set -e
cd "$(dirname "$0")"

if ! command -v php >/dev/null 2>&1; then
  echo "Error: PHP 8.2 or newer is required. See INSTALL_MAC.md."
  exit 1
fi
if ! php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);"; then
  echo "Error: PHP 8.2 or newer is required."
  exit 1
fi
if ! command -v composer >/dev/null 2>&1; then
  echo "Error: Composer is required. See INSTALL_MAC.md."
  exit 1
fi

composer install --no-interaction
if [ ! -f .env ]; then cp .env.example .env; fi
php artisan key:generate --force
if [ ! -f database/database.sqlite ]; then touch database/database.sqlite; fi
php artisan migrate --seed --force
php artisan optimize:clear

echo ""
echo "Setup complete. Run ./run.sh"
echo "Admin: admin@expirytracker.test / Admin@123"
echo "User:  user@expirytracker.test / User@123"
