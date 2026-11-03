# macOS installation

## Requirements

- macOS 12 or newer
- PHP 8.2 or newer with SQLite enabled
- Composer 2

Homebrew is the simplest way to install the requirements:

```bash
brew install php composer
```

## Setup and run

From Terminal, open this project folder and run:

```bash
chmod +x setup.sh run.sh
./setup.sh
./run.sh
```

Open `http://127.0.0.1:8000`. Stop the server with `Control+C`.

No Node.js, npm, MySQL, XAMPP, Docker, or database server is needed. The SQLite file is `database/database.sqlite`.

## Troubleshooting

- Check PHP with `php -v`; it must report 8.2 or newer.
- Check SQLite with `php -m | grep -i sqlite`.
- If port 8000 is occupied, run `php artisan serve --port=8080`.
- To rebuild the demonstration data, run `php artisan migrate:fresh --seed` (this deletes existing product data).
