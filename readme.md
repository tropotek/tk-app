# Tk App - Laravel Docker Example

## Documentation Links
- [Templating Standards](resources/views/readme.md)
- [tk-base Package](packages/ttek/tk-base/src/readme.md)
  - [Breadcrumbs](packages/ttek/tk-base/src/Breadcrumbs/readme.md)
  - [Form Templates](packages/ttek/tk-base/resources/views/components/form/readme.md)
  - [Menu Builder](packages/ttek/tk-base/src/Menu/readme.md)
  - [Table Builder](packages/ttek/tk-base/src/Table/readme.md)


## Overview
This is an experimental Laravel application built with Docker, focusing on modern tooling and efficient development workflows. It leverages FrankenPHP for a high-performance PHP server environment.

### Stack
- **Language:** PHP 8.4+
- **Framework:** [Laravel 12](https://laravel.com/)
- **Server:** [FrankenPHP](https://frankenphp.dev/) (Docker)
- **Frontend:** 
  - [Vite](https://vitejs.dev/)
  - [Tailwind CSS 4](https://tailwindcss.com/)
  - [Bootstrap 5](https://getbootstrap.com/)
  - [HTMX 2](https://htmx.org/)
  - [jQuery 3.7](https://jquery.com/)
- **Database:** SQLite
- **Package Managers:** Composer (PHP), NPM (JS)

---

## Project Structure
- `app/`: Core Laravel application logic.
- `app/packages/ttek/tk-base/`: Custom internal base package.
- `bin/`: Helper scripts for environment management.
- `docker/`: Docker-specific configuration files.
- `resources/`: Frontend assets (views, CSS, JS).
- `routes/`: Application route definitions.
- `tests/`: Automated tests.

---

## Requirements
- [Docker](https://www.docker.com/) and Docker Compose.
- Basic understanding of Laravel.

---

## Installation & Setup

### 1. Environment Configuration
Copy the example environment file and update it as needed:
```bash
cp .env.example .env
```

### 2. Docker Setup
Start the development environment:
```bash
# Using helper script
./bin/up

# OR manually
docker compose --env-file .env -f compose.yml up --build -d

```
The application will be accessible at: `http://localhost:8085` (default, configurable via `HTTP_PORT`).

### 3. Application Initialization
Run the update script to install dependencies, generate keys, and run migrations:
```bash
# Enter the container
docker exec -it tk-app /bin/bash

# Run update/setup
./bin/app-update
```
*Note: You may need to run `composer install` inside the container if it's a fresh install.*

### 4. Default Credentials
- **User:** `admin@example.com`
- **Password:** `password`

---

## Scripts & Commands

### Docker Helper Scripts (`bin/`)
- `./bin/up`: Starts the development containers.
- `./bin/down`: Shuts down the containers and removes orphans.
- `./bin/app-update`: Internal script to sync assets and refresh the database.

### Composer Scripts
- `composer setup`: Complete environment setup (install, key gen, migrate, npm build).
- `composer dev`: Runs Laravel server, queue listener, and Vite in parallel.
- `composer test`: Clears config and runs PHPUnit tests.

---

## Environment Variables
Key variables in `.env`:
- `HTTP_PORT`: Port for the web server (default: `8085`).
- `MAILPIT_HTTP_PORT`: Port for Mailpit Web UI (default: `8086`).
- `DB_CONNECTION`: Database driver (default: `sqlite`).
- `APP_ENV`: Application environment (`local`, `production`).

---

## Running Tests

### PHPUnit (Backend)

```bash
# Inside the container
composer test
# OR
php artisan test
```


---

## Troubleshooting

### Common Issues

- **Logs:** 
  - Tail Docker logs: `docker logs -f tk-app`
  - Tail Laravel logs: `tail -f storage/logs/laravel.log`

---
