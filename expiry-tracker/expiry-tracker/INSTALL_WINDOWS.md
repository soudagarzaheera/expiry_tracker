# Windows installation

## Requirements

- Windows 10 or 11
- PHP 8.2 or newer with `pdo_sqlite` and `sqlite3` enabled
- Composer 2

## Recommended: Laravel Herd

[Laravel Herd for Windows](https://herd.laravel.com/windows) is the easiest way to install the required PHP and Composer tools:

1. Download and install Laravel Herd.
2. Restart VS Code or close and reopen Command Prompt so the updated `PATH` is loaded.
3. Open this project folder in the new terminal.
4. Run `setup.bat` again.

The setup script never attempts to install PHP or Composer automatically. If either command is missing, it prints these instructions and stops safely before changing the project.

Verify the installation in a new terminal:

```bat
php -v
composer --version
```

## Manual alternative

Advanced users may install PHP from the official Windows PHP distribution and Composer from `getcomposer.org`. Add both to `PATH`. In `php.ini`, ensure these extensions are enabled:

```ini
extension=pdo_sqlite
extension=sqlite3
```

After changing `PATH` or `php.ini`, restart VS Code or Command Prompt and run `setup.bat` again.

## Setup and run

Open Command Prompt in this project folder:

```bat
setup.bat
run.bat
```

Open `http://127.0.0.1:8000`. Stop the server with `Ctrl+C`.

No Node.js, npm, MySQL, XAMPP, Docker, or database server is needed. The SQLite file is `database\database.sqlite`.

If port 8000 is occupied, use `php artisan serve --port=8080`.
