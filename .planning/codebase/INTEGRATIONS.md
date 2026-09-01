# External Integrations

**Analysis Date:** 2026-09-01

## APIs & External Services

**Email Delivery:**
- Postmark - Transactional email service
  - SDK/Client: Laravel Mail Transport (built-in)
  - Auth: `POSTMARK_API_KEY` environment variable
  - Config: `config/services.php`, `config/mail.php`
  - Alternative to: Resend, AWS SES, SMTP
  
- Resend - Modern email platform for developers
  - SDK/Client: Laravel Mail Transport (built-in)
  - Auth: `RESEND_API_KEY` environment variable
  - Config: `config/services.php`, `config/mail.php`
  
- AWS SES (Simple Email Service) - Amazon email service
  - SDK/Client: Laravel Mail Transport (built-in)
  - Auth: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`
  - Config: `config/services.php` (ses section)

**Bot Protection & Security:**
- Cloudflare Turnstile - CAPTCHA alternative for form protection
  - SDK/Client: JavaScript client library (via Cloudflare CDN)
  - Auth: 
    - `TURNSTILE_SITE_KEY` - Client-side (public)
    - `TURNSTILE_SECRET_KEY` - Server-side (secret)
  - Config: `config/services.php` (turnstile section)
  - Usage: Available for form protection (not currently implemented in views, but configured)

**Chat & Notifications:**
- Slack - Team messaging and notifications
  - SDK/Client: Laravel Notification Channel (built-in)
  - Auth: `SLACK_BOT_USER_OAUTH_TOKEN`
  - Config: `config/services.php` (slack section)
  - Usage: For sending error/log notifications to Slack channels
  - Channel: `SLACK_BOT_USER_DEFAULT_CHANNEL`

## Data Storage

**Databases:**
- SQLite (Default)
  - File: `database/database.sqlite`
  - Connection: `sqlite` in `config/database.php`
  - Client: PDO (PHP built-in)
  - Use case: Local development, testing
  - Foreign key support: Enabled by default

- MySQL 5.7+
  - Connection: `mysql` in `config/database.php`
  - Client: PDO MySQL (`pdo_mysql` PHP extension)
  - Charset: utf8mb4 (default)
  - Collation: utf8mb4_unicode_ci (default)
  - SSL support: Configurable via `MYSQL_ATTR_SSL_CA` env var

- MariaDB (MySQL-compatible)
  - Connection: `mariadb` in `config/database.php`
  - Client: PDO MariaDB
  - Configuration: Same as MySQL

- PostgreSQL 10+
  - Connection: `pgsql` in `config/database.php`
  - Client: PDO PostgreSQL (`pdo_pgsql` PHP extension)
  - SSL mode: Configurable via `DB_SSLMODE` (default: prefer)

- SQL Server 2017+
  - Connection: `sqlsrv` in `config/database.php`
  - Client: PDO SQL Server
  - Encryption: Configurable via `DB_ENCRYPT` env var

**ORM:**
- Eloquent (Laravel built-in) - Object-relational mapper
  - Models: `app/Models/` (User, Project)
  - Configuration: `config/database.php`

**File Storage:**
- Local Filesystem (Default)
  - Disk: `local` (private) and `public` (served via web)
  - Root: `storage/app/private` (local), `storage/app/public` (public)
  - Configuration: `config/filesystems.php`

- AWS S3
  - Disk: `s3` in `config/filesystems.php`
  - Auth: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`
  - Config: `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`
  - SDK: AWS SDK for PHP (via Laravel's S3 adapter)
  - Use case: Production file storage, asset CDN

**Caching:**
- Database Cache (Default)
  - Driver: `database` in `config/cache.php`
  - Table: `cache` (configurable via `DB_CACHE_TABLE`)
  - Lock table: Configurable via `DB_CACHE_LOCK_TABLE`
  - Use case: Simple, persistent cache without external dependencies

- Redis
  - Client: PhpRedis or Predis (configurable via `REDIS_CLIENT`)
  - Connection: `redis` in `config/cache.php`
  - Auth: `REDIS_USERNAME`, `REDIS_PASSWORD` (optional)
  - Config: `REDIS_HOST`, `REDIS_PORT`, `REDIS_DB`, `REDIS_PREFIX`
  - Cluster support: Configurable via `REDIS_CLUSTER`

- Memcached
  - Client: PHP Memcached extension
  - Servers: `MEMCACHED_HOST:MEMCACHED_PORT` (default: 127.0.0.1:11211)
  - Auth: `MEMCACHED_USERNAME`, `MEMCACHED_PASSWORD` (SASL)
  - Config: `config/cache.php` (memcached section)

- AWS DynamoDB
  - Service: Amazon DynamoDB serverless NoSQL database
  - Auth: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`
  - Table: `DYNAMODB_CACHE_TABLE` (default: cache)
  - Region: `AWS_DEFAULT_REGION`
  - Endpoint: `DYNAMODB_ENDPOINT` (optional, for local testing)

- Array Cache (Testing/Development)
  - Driver: `array` - In-memory cache, not shared across processes

- File Cache
  - Driver: `file`
  - Path: `storage/framework/cache/data`

## Authentication & Identity

**Auth Provider:**
- Laravel Fortify (Custom/Built-in)
  - Implementation: Session-based authentication
  - Features configured in `config/fortify.php`:
    - User registration
    - Password reset
    - Email verification
    - Two-factor authentication (TOTP via authenticator app)
    - Passkeys/WebAuthn support
  - Guard: `web` (session-based)
  - User model: `App\Models\User`

**WebAuthn/Passkeys:**
- Laravel Passkeys Package `@laravel/passkeys` 0.2.0
  - JavaScript: `resources/js/passkeys.js`
  - Backend: WebAuthn server-side protocol via web-auth/webauthn-lib
  - Config: `config/fortify.php` (passkeys section)
  - Relying Party ID: Configured from `APP_URL` hostname
  - Timeout: 60 seconds
  - User handle secret: Stored securely via `PASSKEYS_USER_HANDLE_SECRET` env var

## Monitoring & Observability

**Error Tracking:**
- Not configured - Application would need Sentry, Bugsnag, or similar integration added

**Logs:**
- File-based: `storage/logs/laravel.log`
- Configuration: `config/logging.php`
- Log channels: single, daily, stack (multiple channels)
- Rotation: Daily by default
- Laravel Pail: Built-in log viewer for development (`laravel/pail` 1.2.5)

## CI/CD & Deployment

**Hosting:**
- Not pre-configured - Can deploy to any PHP 8.3+ server
- Recommended environments: Laravel Forge, Vapor, Heroku, traditional VPS

**CI Pipeline:**
- Not configured - No GitHub Actions or CI configuration present
- Composer scripts available for local testing:
  ```bash
  composer test       # Run full test suite (linting, type checking, tests)
  composer lint       # PHP code formatting
  composer types:check  # PHPstan type checking
  ```

## Environment Configuration

**Required Environment Variables:**

Core:
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local, production, testing)
- `APP_KEY` - Encryption key (generated on setup)
- `APP_URL` - Base URL (used for Passkeys relying party ID)

Database:
- `DB_CONNECTION` - Database driver (default: sqlite)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` - Connection details

Mail:
- `MAIL_MAILER` - Email driver (smtp, postmark, resend, ses, log, sendmail)
- `MAIL_FROM_ADDRESS` - Sender email address
- `MAIL_FROM_NAME` - Sender name (default: APP_NAME)

Optional (Service Credentials):
- `POSTMARK_API_KEY` - Postmark API key
- `RESEND_API_KEY` - Resend API key
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` - AWS credentials for SES and S3
- `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` - Slack notifications
- `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY` - Cloudflare Turnstile
- `INQUIRY_TO_EMAIL` - Email address for contact form submissions

Caching:
- `CACHE_STORE` - Cache driver (database, redis, memcached, dynamodb)
- Redis/Memcached/DynamoDB config vars as needed

**Secrets Location:**
- `.env` file (excluded from git via .gitignore)
- Set via environment in production deployment

## Webhooks & Callbacks

**Incoming:**
- Contact/Inquiry form submission webhook endpoint:
  - Route: Not explicitly shown in exploration, but Inquiry model and InquiryReceived mail indicate form handling
  - Sends email to: `config/inquiry.php` - `INQUIRY_TO_EMAIL` (amandacojerean@gmail.com by default)

**Outgoing:**
- Email notifications via configured mail service (Postmark, Resend, SES, etc.)
- Slack notifications (optional, configured but not actively used)
- No external API webhook calls detected in current codebase

## External Package Dependencies

**Security:**
- `web-auth/webauthn-lib` - WebAuthn/FIDO2 server-side library (installed via passkeys)
- `bacon/bacon-qr-code` - QR code generation (for 2FA setup)
- `spomky-labs/cbor-php` - CBOR encoding/decoding (for WebAuthn)

**Utilities:**
- `carbon` - Date/time manipulation
- `monolog` - Logging framework
- `league/flysystem` - Abstract filesystem interface (for S3/local file storage)
- `guzzlehttp/guzzle` - HTTP client (used by various packages)
- `symfony/*` - Various Symfony components (database, event dispatcher, http foundation, etc.)

---

*Integration audit: 2026-09-01*
