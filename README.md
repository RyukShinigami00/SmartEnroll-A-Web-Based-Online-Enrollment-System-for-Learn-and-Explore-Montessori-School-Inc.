# SmartEnroll

Web-based online enrollment & student scheduling system for **Learn and Explore Montessori School (LEMS)**.

Built for a 12-week Agile capstone project. Roles: **Student · Admin · Super Admin**.

## Status: Phase 2 — Database & Cloud Backend ✅ (skeleton)

- [x] Database schema (`database/schema.sql`)
- [x] Seed data / initial Super Admin (`database/seeders/001_super_admin_seed.sql`)
- [x] Base PHP MVC structure (Router, Controller, Model, Views)
- [x] User authentication: register, login, logout, session, role-based access
- [ ] Cloud SQL instance provisioned on GCP (manual step — see below)

## Stack

- PHP 8.1+, plain PDO (MySQL), custom lightweight MVC (no framework)
- MySQL 8 (GCP Cloud SQL in production, XAMPP/MySQL locally)
- Sessions for auth; bcrypt password hashing via `password_hash()`
- Prepared statements everywhere (SQL injection protection, per roadmap Phase 4 security review)

## Folder Structure

```
public/            Web root — front controller (index.php), assets
app/
  Config/          Env loader, Database (PDO) connection
  Core/            Router, base Controller, base Model
  Controllers/      AuthController, DashboardController, ...
  Models/           User, AuditLog, ...
  Middleware/       Auth (login/role guards)
  Helpers/          Session
  Views/            layouts/, auth/, dashboard/, errors/
database/
  schema.sql        Full DDL — users, students, sections, schedule_entries,
                     enrollment_applications, audit_logs
  seeders/          Initial Super Admin + starter sections
vendor/autoload.php Minimal PSR-4 autoloader (swap for `composer install`
                     once PHPMailer/Dompdf are added in later sprints)
```

## Local Setup (Laragon)

1. Clone/copy this project into Laragon's `www/` folder, e.g. `C:\laragon\www\smartenroll`.
2. Start Laragon and click **Start All** (Apache + MySQL).
3. Copy `.env.example` to `.env`. Laragon's default MySQL credentials are:
   - `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty)
4. Create the database and load the schema — either via Laragon's **Database** button (opens HeidiSQL) and running the `.sql` files, or from the terminal:
   ```
   mysql -u root < database/schema.sql
   mysql -u root < database/seeders/001_super_admin_seed.sql
   ```
   - Default login: `superadmin@lems.local` / `ChangeMe123!` — **change this immediately.**
5. Laragon auto-detects the project and creates a pretty URL for it — the site will be reachable at `http://smartenroll.test` automatically (no manual vhost editing needed). Laragon serves the **document root as-is**, so either:
   - point Laragon's site root at the `public/` folder directly (recommended — Preferences → Apache, or use a custom root per site), or
   - if it's serving the project root, browse to `http://smartenroll.test/public` instead.
6. Visit `/register` to create a student account, or `/login` with the seeded Super Admin.

> Laragon uses Apache by default (same as this skeleton's `public/.htaccess` expects). If you switch Laragon to Nginx, you'll need an equivalent rewrite rule sending all requests to `public/index.php`.

## Cloud Setup (GCP — Phase 2 infra task, manual)

1. Create a Cloud SQL (MySQL) instance, note the connection name.
2. In `.env`, set `DB_HOST=/cloudsql/PROJECT_ID:REGION:INSTANCE_ID` (unix socket) when deployed on
   Compute Engine/Cloud Run, or the instance's public/private IP otherwise.
3. Run `schema.sql` and the seeder against the Cloud SQL instance.
4. Configure Cloud Storage for backups per the roadmap's backup plan.

## What's next (Phase 3 — Sprint 1)

- Build the enrollment application form (`enrollment_applications` table is ready).
- Client + server-side validation.
- Email confirmation via PHPMailer (add with `composer require phpmailer/phpmailer`).
- Student "My Applications" page.

## Security notes

- Passwords are hashed with bcrypt (`PASSWORD_BCRYPT`), never stored in plain text.
- All queries use PDO prepared statements — no string-concatenated SQL.
- Session cookies are `httponly` + `SameSite=Lax`; enable `secure` once served over HTTPS.
- Role checks happen server-side in `App\Middleware\Auth`, not just hidden in the UI.
