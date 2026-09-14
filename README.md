# Tima-Ade University

This repository contains the Tima-Ade University web application — a Laravel-based school management system implementing user roles (super admin, admin, teacher, parent, student), attendance, exams and report-cards, fees, notifications, and related modules. Note: The Timetable module is deferred and not part of the core 17-phase implementation pass; see "Deferred modules" below.

This README is a focused setup, verification and deployment guide for the actual project as implemented in this repository (Phases 1–16). It documents required versions, installation, environment variables, migrations/seeders, the interactive super-admin creation command, and verification steps performed during Phase 17.

---

## Requirements

These requirements are taken from the project's composer.json, package.json and configuration files:

- PHP: ^8.2 (project composer.json)
- Laravel framework: ^12.0 (project composer.json)
- Composer (for PHP dependency management)
- Node.js and npm (for frontend tooling; project uses Vite and Tailwind via package.json)
- Database:
  - Supported drivers in config: sqlite, mysql, mariadb, pgsql, sqlsrv
  - The project ships with MySQL/MariaDB configuration in `.env.example` (DB_CONNECTION=mysql). The application requires the appropriate PDO PHP extension for the chosen driver (for MySQL/MariaDB, pdo_mysql must be installed/enabled).
- Required PHP extensions (typical for Laravel 12 and used here): pdo, pdo_mysql (if using MySQL), mbstring, openssl, tokenizer, xml, ctype, json, BCMath (if used), zip, and fileinfo.
- Web server: Apache, Nginx or the built-in PHP server for local development. Document root should point to the `public/` directory.

Note: The project defaults to using MySQL in `.env.example`, but `config/database.php` keeps `sqlite` as the fallback default. For local testing, `database/database.sqlite` can be used as an alternate non-destructive backend.

---

## Installation (from a fresh clone)

1. Clone the repository and enter the directory:

   git clone <repository-url> "tima-ade-university"
   cd "tima-ade-university"

2. Install PHP dependencies with Composer:

   composer install

3. Copy the example environment file and generate an application key:

   cp .env.example .env
   php artisan key:generate

4. Configure your `.env` (database, mail, etc.) — see the `.env configuration` section below.

5. Install frontend dependencies and build assets:

   npm install
   npm run build   # for production assets
   npm run dev     # for local development (starts Vite dev server)

6. Create the database (see MySQL setup below) and run migrations/seeders (development only):

   # Use a disposable/local database when running migrations fresh
   php artisan migrate --seed

   If you prefer an isolated SQLite file for development (non-destructive):

   touch database/database.sqlite
   # then set DB_CONNECTION=sqlite in .env and DB_DATABASE=/full/path/to/database/database.sqlite
   php artisan migrate --seed

7. Create the first super-admin user using the interactive artisan command (see below):

   php artisan moon:create-admin

8. Start the local server (for development):

   php artisan serve

   The application will be available at http://127.0.0.1:8000 (or the host and port shown).

---

## MySQL / MariaDB setup

The `.env.example` uses MySQL by default. The project supports MySQL and MariaDB via PDO — ensure `pdo_mysql` is enabled in PHP.

Example steps to create a database and user (adjust for your environment):

1. MySQL shell or administrative tool:

   CREATE DATABASE `moon_college` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'moon_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT ALL PRIVILEGES ON `moon_college`.* TO 'moon_user'@'localhost';
   FLUSH PRIVILEGES;

2. Update your `.env` with these values (example):

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=moon_college
   DB_USERNAME=moon_user
   DB_PASSWORD=your_secure_password

Notes:
- The project's `.env.example` uses the development defaults recommended for XAMPP/local setups: DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=moon_college, DB_USERNAME=root, DB_PASSWORD= (see the .env.example file). Use these development defaults only for local development — do not use them in production.
- If you prefer not to install MySQL locally, use SQLite for quick local testing (see installation step 6).
- Ensure the `pdo_mysql` PHP extension is installed and enabled when using MySQL/MariaDB.

---

## `.env` configuration (overview)

The project ships with `.env.example`. Key environment variables used by the application are listed below.

Required / commonly used:

- APP_NAME (e.g. "Tima-Ade University")
- APP_ENV (local|production)
- APP_KEY (generated via php artisan key:generate)
- APP_DEBUG (true|false)
- APP_URL (e.g. http://localhost)

Database:
- DB_CONNECTION (mysql|sqlite|pgsql|mariadb)
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

Mail:
- MAIL_MAILER (default: log for local)
- MAIL_HOST
- MAIL_PORT
- MAIL_USERNAME
- MAIL_PASSWORD
- MAIL_FROM_ADDRESS
- MAIL_FROM_NAME

Session / Cache / Queue (defaults in .env.example):
- SESSION_DRIVER (database recommended for multi-server setups)
- CACHE_STORE (database in .env.example)
- QUEUE_CONNECTION (database if you need queued jobs)

Optional / development helpers:
- REDIS_* for Redis connections
- AWS_* for S3 storage
- VITE_APP_NAME for frontend

Production notes: Keep sensitive values (DB_PASSWORD, MAIL_PASSWORD, AWS keys) out of version control. Use your host/CI secrets manager or environment configuration.

---

## Migrations and Seeders

- Run migrations (non-destructive):

  php artisan migrate

- Run migrations and seed (development/test disposable DB):

  php artisan migrate:fresh --seed

  WARNING: migrate:fresh deletes all tables. Only use it on local or disposable testing databases.

- Seeders: The project includes seeders referenced by database/seeders (check the `Database\Seeders` namespace). Running `php artisan db:seed` or `--seed` with migrate will populate demo/essential data such as roles.

- Role creation: Seeders create the required roles (including the super_admin role). If seeders are not run, you can create the first super-admin interactively with the artisan command described below.

---

## First Super Admin creation

The project provides an interactive Artisan command to create the initial super admin user. This is the canonical command (implemented in `app/Console/Commands/MoonCreateAdmin.php`):

  php artisan moon:create-admin

What it does:
- Prompts for Full Name and Email (options `--name` and `--email` may be provided to pre-fill).
- Prompts securely for a password and asks for confirmation (no password is echoed or logged).
- Validates the email and prevents duplicate emails.
- Enforces a minimum password length (interactive checks).
- Creates the user, assigns the `super_admin` role, marks the account active, and securely hashes the password.

Note: A disabled duplicate command file existed earlier and was renamed/disabled during verification to avoid signature collisions. The correct command to use is `php artisan moon:create-admin`.

---

## Login and account flows

- Login URL: /login
- Forgot-password: /forgot-password (sends reset link using configured mailer; default is `log` in development)
- Reset password: /reset-password/{token}
- Profile update and change password are available at /profile (PUT for update, PUT /profile/password for password change)
- Role-based redirects: After login the application redirects users according to their role as implemented in the authentication controller (student -> student dashboard, teacher -> teacher view, admins -> admin dashboard).

Development demo credentials (provided by the project's development seeders) — for local testing only:

- Email: admin@school.com
- Password: password

These credentials are for development/testing only. Do NOT use these in production. The recommended production process is to run:

  php artisan moon:create-admin

which interactively creates a secure super-admin account. Ensure any demo accounts are removed or changed before deploying to production.

---

## Deployment notes (production)

Recommended steps and checks before deploying to production:

1. Environment
   - APP_ENV=production
   - APP_DEBUG=false
   - APP_URL to your canonical URL
   - Generate and set APP_KEY if not present (php artisan key:generate)

2. Dependencies
   - composer install --no-dev --optimize-autoloader
   - npm install && npm run build

3. Storage & permissions
   - Ensure web server user can write to storage/, bootstrap/cache/ and public/ (as appropriate).
   - php artisan storage:link if using local disk for file uploads.

4. Optimize
   - php artisan config:cache
   - php artisan route:cache
   - php artisan view:cache

5. Database
   - Run non-destructive migrations on production database: php artisan migrate
   - Use a safe migration strategy and take backups before altering production schema.

6. Queue & Scheduler
   - If QUEUE_CONNECTION is configured (database/redis), ensure queue workers are running (supervisor, systemd, etc.)
   - Configure cron for scheduler: * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

7. HTTPS & web-server
   - Serve the app via HTTPS. Configure Nginx/Apache to point document root to the `public/` directory.

8. Monitoring
   - Monitor logs in storage/logs
   - Configure regular database backups

Do not run `php artisan migrate:fresh` on production.

---

## Verification performed (Phase 17)

Commands executed locally during verification:

- php artisan route:list
  - Result: Completed successfully. The application exposes expected routes for authentication, profile, users, parents, students, teachers, fees, reports (attendance/fees CSV), report-card, notifications and more. Note: the Timetable module is deferred in the project's Phase decisions and is not considered part of the core 17-phase implementation.

- php artisan test
  - Result: Unit tests passed (ExamMark, notifications, report routes and some unit coverage). Several Feature tests failed because the test environment lacked a configured DB driver ("could not find driver" for MySQL). See the Issues / Blockers section below.

Note on migrations: `php artisan migrate:fresh --seed` was NOT executed in this environment because it requires a configured, disposable database and the runtime here is missing the PDO MySQL driver. Running destructive migrations in an environment without DB driver or against a non-disposable production DB would be unsafe. Use the commands below in a DB-enabled local/CI environment.

---

## How to run tests (local / CI)

1. Ensure a database is available for testing and that the PHP pdo extension for your database driver (pdo_mysql for MySQL) is installed and enabled.
2. Configure `.env.testing` or the `phpunit.xml` environment variables to use a test database (do NOT point to production).
3. Run:

   php artisan migrate --env=testing
   php artisan test

If you prefer SQLite for CI or local tests, create a `:memory:` or file-based sqlite DB and set DB_CONNECTION=sqlite in your test configuration.

---

## Issues found and fixes applied during Phase 17 verification

1. Duplicate artisan command registration
   - Problem: Two command classes implementing the `moon:create-admin` concept existed. One canonical command (`MoonCreateAdmin.php`) is the active implementation. A duplicate `CreateSuperAdmin.php` contained a conflicting or malformed signature and risked a duplicate-command registration.
   - Action: The duplicate file was disabled/replaced with a small deprecated stub to avoid signature collisions. The canonical command (`moon:create-admin`) remains available and documented.

2. Fee payment update: server-side status computation
   - Problem: The `FeeController@update` method previously accepted `status` from user input. This allowed a client to manipulate payment status when updating a payment.
   - Action: `FeeController@update` was changed to compute `status` server-side using the fee structure amount, discount, late fee and the submitted `amount_paid`. The controller still validates numeric amounts/dates and preserves admin-only access.

Both of the above were fixes applied where safe and consistent with the existing application architecture. Syntax checks (php -l) were run on modified files and reported no parse errors.

---

## Issues not fully verifiable in this environment (blockers)

- Database driver missing: The execution environment used for verification lacked the required PDO MySQL driver (pdo_mysql). As a result:
  - Feature tests requiring a database failed with "could not find driver" errors.
  - Destructive verification step (`php artisan migrate:fresh --seed`) was not executed to avoid data loss and because the DB driver was missing.

Recommendation: Run the full verification in a DB-enabled environment (local machine or CI) with pdo_mysql enabled and a disposable test database. Suggested commands for full verification in such an environment:

  # For disposable local DB (MySQL)
  php artisan migrate:fresh --seed
  php artisan route:list
  php artisan test

Or using SQLite for tests (non-destructive quick pass):
  touch database/database.sqlite
  # set DB_CONNECTION=sqlite and DB_DATABASE=./database/database.sqlite in .env.testing or phpunit.xml
  php artisan migrate --env=testing
  php artisan test

---

## Security notes

- All authorization- and ownership-sensitive operations must be enforced server-side. The codebase implements explicit IDOR checks in controllers for student/parent/fee access and admin-only middleware for administrative actions. Do not rely on UI visibility for security.
- The fees update code now computes payment status server-side to prevent client tampering.
- The notification system and report-card controller implement ownership checks prior to returning sensitive data.

---

## Where to get help

- Inspect `routes/web.php`, `app/Http/Controllers/*`, `app/Models/*`, and `database/seeders/` for how modules are wired.
- For environment-specific issues (DB driver, PHP extensions), consult your OS/package manager to install required PHP extensions (for example `php-pdo_mysql` / enabling `pdo_mysql` in php.ini).

---

Thank you for working with Tima-Ade University. If you run the verification steps in a DB-enabled environment and share the test output, further fixes and CI configuration guidance can be provided.
