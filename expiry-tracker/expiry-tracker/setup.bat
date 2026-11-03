@echo off
setlocal
cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
  echo.
  echo ERROR: PHP was not found on your PATH.
  echo.
  echo Recommended installation for Windows:
  echo   1. Download Laravel Herd from https://herd.laravel.com/windows
  echo   2. Install Laravel Herd, which provides PHP and Composer.
  echo   3. Restart VS Code or close and reopen Command Prompt.
  echo   4. Return to this project folder and run setup.bat again.
  echo.
  echo Setup stopped safely. Nothing was installed automatically.
  echo See INSTALL_WINDOWS.md for more information.
  exit /b 1
)
php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);"
if errorlevel 1 (
  echo.
  echo ERROR: The installed PHP version is too old. PHP 8.2 or newer is required.
  echo.
  echo Recommended fix:
  echo   1. Install or update Laravel Herd from https://herd.laravel.com/windows
  echo   2. Restart VS Code or close and reopen Command Prompt.
  echo   3. Return to this project folder and run setup.bat again.
  echo.
  echo Setup stopped safely. Nothing was installed automatically.
  echo See INSTALL_WINDOWS.md for more information.
  exit /b 1
)
where composer >nul 2>nul
if errorlevel 1 (
  echo.
  echo ERROR: Composer was not found on your PATH.
  echo.
  echo Recommended installation for Windows:
  echo   1. Download Laravel Herd from https://herd.laravel.com/windows
  echo   2. Install Laravel Herd, which provides PHP and Composer.
  echo   3. Restart VS Code or close and reopen Command Prompt.
  echo   4. Return to this project folder and run setup.bat again.
  echo.
  echo Setup stopped safely. Nothing was installed automatically.
  echo See INSTALL_WINDOWS.md for more information.
  exit /b 1
)

call composer install --no-interaction
if errorlevel 1 exit /b 1
if not exist .env copy .env.example .env >nul
php artisan key:generate --force
if errorlevel 1 exit /b 1
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan migrate --seed --force
if errorlevel 1 exit /b 1
php artisan optimize:clear
if errorlevel 1 exit /b 1

echo.
echo Setup complete. Run run.bat
echo Admin: admin@expirytracker.test / Admin@123
echo User:  user@expirytracker.test / User@123
endlocal
