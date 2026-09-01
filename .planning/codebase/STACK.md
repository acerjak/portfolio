# Technology Stack

**Analysis Date:** 2026-09-01

## Languages

**Primary:**
- PHP 8.3+ - Backend framework and business logic
- JavaScript - Frontend interactivity and form handling

**Secondary:**
- HTML/CSS - View templates (Blade) and styling

## Runtime

**Environment:**
- PHP 8.3+ with Composer
- Node.js (version not specified in .nvmrc; inferred from package dependencies)

**Package Managers:**
- Composer 2.x - PHP dependency management
  - Lockfile: `composer.lock` present
- npm - JavaScript/frontend dependency management
  - Lockfile: `package-lock.json` present (inferred)

## Frameworks

**Core Backend:**
- Laravel 13.17 - Full-stack web framework
  - Location: `app/`, `routes/`, `config/`
  - Handles routing, ORM (Eloquent), middleware, authentication
- Livewire 4.1 - Real-time component framework
  - Components: `app/Livewire/`
  - Used for interactive UI without JavaScript SPAs
- Livewire Blaze 1.0 - UI component library for Livewire
  - Built on top of Livewire for pre-built interactive components
- Livewire Flux 2.13.1 - Component library built on Blaze
  - Higher-level abstraction over Blaze components
- Laravel Fortify 1.37.2 - Authentication/authorization scaffold
  - Configured in `config/fortify.php`
  - Provides login, registration, password reset, 2FA, passkeys

**Frontend Build:**
- Vite 8.0.0 - Module bundler and dev server
  - Config: `vite.config.js`
  - Entry points: `resources/js/app.js`, `resources/js/passkeys.js`
- Tailwind CSS 4.0.7 - Utility-first CSS framework
  - Configured via `@tailwindcss/vite` plugin in vite.config.js
  - Stylesheet: `resources/css/app.css`
- Laravel Vite Plugin 3.1 - Bridge between Laravel and Vite
  - Handles asset manifest and hot module reloading
- Vite Plus 0.3.0 - Enhanced Vite configuration utilities
  - Used in `vite.config.js` for `defineConfig` and `lazyPlugins`

**Testing:**
- PHPUnit 12.5.23 - PHP unit testing framework
  - Config: `phpunit.xml`
  - Test suites: `tests/Unit/`, `tests/Feature/`
- Faker 1.24 - Fake data generation for tests
- Mockery 1.6 - Mocking library for PHP

**Development Tools:**
- Laravel Pint 1.27 - PHP code formatter/linter
  - Used in composer scripts: `lint`, `lint:check`
- Larastan 3.9 - Static type checker for Laravel
  - Used via `phpstan analyse`
- Laravel Pail 1.2.5 - Log viewer for development
- Laravel Pao 1.0.6 - Performance analysis tool
- Laravel Tinker 3.0 - REPL for interactive development
- Concurrently 10.0.3 - Run multiple processes simultaneously
  - Used to run dev server and Vite together

## Key Dependencies

**Critical:**
- `laravel/chisel` 0.1.0 - Database schema management and migrations
- `@laravel/passkeys` 0.2.0 - WebAuthn/passkey implementation (frontend)
- `web-auth/webauthn-lib` (via passkeys) - WebAuthn server-side protocol

**Utilities:**
- `laravel/tinker` 3.0 - REPL shell for exploring application

## Configuration

**Environment:**
- Configuration via `.env` file (secrets, service credentials)
- Multiple config files in `config/`:
  - `app.php` - Application name, timezone, etc.
  - `auth.php` - Authentication guards and password brokers
  - `cache.php` - Cache store configuration
  - `database.php` - Database connections (SQLite default)
  - `fortify.php` - Fortify authentication features
  - `inquiry.php` - Inquiry form configuration
  - `mail.php` - Email service configuration
  - `services.php` - Third-party service API keys
  - `filesystems.php` - Storage disk configuration
  - `logging.php` - Log channel configuration
  - `queue.php` - Job queue configuration
  - `session.php` - Session storage configuration

**Key Configuration Variables:**
- `APP_ENV` - Application environment (local, production, testing)
- `APP_URL` - Base URL for the application
- `DB_CONNECTION` - Database driver (default: sqlite)
- `MAIL_MAILER` - Email driver (default: log)
- `CACHE_STORE` - Cache store (default: database)
- Various service API keys: `POSTMARK_API_KEY`, `RESEND_API_KEY`, `AWS_*`, `TURNSTILE_*`, etc.

## Build System

**Vite Configuration:**
- `vite.config.js` - Main build configuration
- Input files: `resources/css/app.css`, `resources/js/app.js`, `resources/js/passkeys.js`
- Configured with Laravel Vite Plugin for manifest generation and hot reload
- Tailwind CSS Vite plugin for CSS processing
- Lazy plugin loading for optimized startup
- Watch exclusions for `.agents/**`, `.claude/**`, `.cursor/**`, storage/framework/views, vendor

**Build Commands:**
```bash
npm run build      # Production build
npm run dev        # Development with hot reload
composer dev       # Run artisan dev (PHP + Vite together)
```

## Platform Requirements

**Development:**
- PHP 8.3 or higher
- Composer 2.x
- Node.js (version not pinned, but modern version recommended)
- SQLite (default), or MySQL/MariaDB/PostgreSQL/SQL Server for production

**Production:**
- Deployment target: Any server supporting PHP 8.3+
- Database: SQLite (not recommended), MySQL 5.7+, PostgreSQL 10+, or SQL Server 2017+
- Optional: Redis/Memcached for caching
- Optional: AWS S3 for file storage
- Optional: Email service providers (Postmark, Resend, AWS SES)

## Runtime Behavior

**Server:**
- Laravel application runs as PHP application (via built-in server in dev, FastCGI/FPM in production)
- Vite dev server runs on separate port (typically 5173)
- Laravel Artisan (`artisan dev`) runs both simultaneously using `concurrently`

**Asset Pipeline:**
- Development: Vite serves assets with hot module replacement
- Production: Built assets in `public/build/` (manifest in `public/build/manifest.json`)

---

*Stack analysis: 2026-09-01*
