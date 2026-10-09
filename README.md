# Task Management System

A Laravel task management application with separate workspaces for regular users and administrators.

## Features

- User registration and authentication
- Separate user and administrator panels
- Create, view, update, and delete tasks
- Ownership rules that prevent users from accessing other users' tasks
- Administrator access to all tasks
- Task completion status, search, filters, and pagination
- Responsive interface powered by Filament

## Requirements

- PHP 8.3 or newer
- Composer
- SQLite PHP extension

Node.js is only needed when rebuilding the default frontend assets.

## Installation

```bash
git clone <repository-url>
cd <repository-directory>
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Open `http://localhost:8000` in a browser.

All users sign in at `/login`. The application redirects regular users to `/app` and administrators to `/admin` after authentication.

## Demo accounts

| Role | Email | Password | Panel |
| --- | --- | --- | --- |
| Administrator | `admin@example.com` | `password` | `/admin` |
| Regular user | `user@example.com` | `password` | `/app` |

These accounts are intended for local demonstration only.

## Database

SQLite is configured by default, so MySQL and MySQL Workbench are not required. To use MySQL instead, create a database and update the `DB_*` values in `.env`, then run:

```bash
php artisan migrate --seed
```

## Testing and code style

```bash
php artisan test
vendor/bin/pint --test
```

## Security design

- New registrations always receive the regular-user role.
- Administrator registration is not exposed publicly.
- The administrator panel is protected by role middleware.
- Task policies enforce ownership for viewing, updating, and deleting.
- User-panel queries are scoped to the authenticated user.
- A task's owner is assigned on the server when a regular user creates it.
