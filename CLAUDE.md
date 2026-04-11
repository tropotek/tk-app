# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Setup (first time)
composer run setup

# Development (runs server, queue, logs, and vite concurrently)
composer run dev

# Run tests
composer run test

# Run a single test
php artisan test --filter=TestName

# Asset build
npm run build

# Docker
./bin/up      # start containers
./bin/down    # stop containers
```

Default dev credentials: `admin@example.com` / `password`

## Architecture

**Laravel 12** app using **FrankenPHP** in Docker, **SQLite** database, and a local `ttek/tkl-ui` package for UI components.

### Routes

Routes are split across four files included from `routes/web.php`:
- `guestRoutes.php` — public (home, login, register)
- `userRoutes.php` — `auth` middleware (dashboard, logout)
- `adminRoutes.php` — `auth + role:admin` middleware, prefixed `/admin`
- `exampleRoutes.php` — `auth` middleware, prefixed `/examples`

Livewire full-page components are registered with `Route::livewire()` using the `pages::` namespace prefix, e.g.:
```php
Route::livewire('/ideas', 'pages::examples.ideas')->name('index');
```

### Livewire Conventions

Full-page Livewire components live in `resources/views/pages/` and are prefixed with `⚡` (e.g., `⚡index.blade.php`). These use anonymous class syntax at the top of the Blade file:

```php
new #[Layout('pages.main')]
class extends Component { ... }
```

### tkl-ui Package (`packages/ttek/tkl-ui`)

A local Composer package providing:

**Forms** — Blade components under `x-tkl-ui::form.*`:
- `x-tkl-ui::form` wrapper (supports `mode="view|edit|create"`)
- Fields: `form.fields.input`, `.select`, `.checkbox`, `.radio`, `.file`, `.textarea`, `.hidden`
- Buttons: `form.buttons.default-btns`, `.submit`, `.link`
- UI wrappers: `form.ui.fieldset`, `form.ui.fieldgroup`

**Tables** — Two traits for building sortable/paginated/filterable tables:
- `IsTable` — base trait (shared between Livewire and non-Livewire)
- `IsLivewireTable` — extends `IsTable` with `#[Url]` bound properties: `tableId`, `limit`, `sort`, `dir`, `search`

Cells are defined in `boot()` using `$this->appendCell(new Cell(...))` or fluent chaining:
```php
$this->appendCell('created_at')->setHeader('Created')->setSortable();
```

Cell `html()` and `text()` accept callables for custom rendering. For array-backed tables, use `$this->paginateArray($this->query())` and `$this->sortArray($rows, $sortCol)`.

**Other utilities:**
- `Breadcrumbs` facade — `Breadcrumbs::push('Label')`
- `MenuBuilder` facade — registered in `AppServiceProvider` via `NavBar` and `UserNav` classes
- `DefaultPageName` view composer — injects `$pageName` into all views

### Frontend

- **Vite** with `laravel-vite-plugin`
- **Bootstrap 5** + **SCSS** (`resources/scss/`)
- **HTMX 2** + **jQuery 4** + **Axios**
- Vite dev server runs on port 5173 (configurable via `VITE_PORT`)

### Auth & Permissions

Uses **Spatie Laravel Permission** (`spatie/laravel-permission`) with a `Roles` enum in `app/Enum/Roles.php`. Admin routes are gated with `role:admin` middleware.