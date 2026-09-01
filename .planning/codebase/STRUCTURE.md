# Codebase Structure

**Analysis Date:** 2026-09-01

## Directory Layout

```
portfolio/
├── app/                          # Application source code (PHP)
│   ├── Actions/                  # Action classes (Fortify auth actions)
│   │   └── Fortify/
│   ├── Concerns/                 # Reusable traits (validation rules)
│   ├── Enums/                    # PHP enums (InquiryReason)
│   ├── Http/
│   │   └── Controllers/          # Route handlers (HomeController)
│   ├── Livewire/                 # Livewire components (minimal)
│   ├── Mail/                     # Mailable classes (InquiryReceived)
│   ├── Models/                   # Eloquent models (User, Project)
│   └── Providers/                # Service providers (AppServiceProvider, FortifyServiceProvider)
├── bootstrap/                    # Bootstrap files (cache, runtime)
├── config/                       # Configuration files
│   ├── app.php                   # App configuration
│   ├── auth.php                  # Authentication configuration
│   ├── fortify.php               # Fortify auth configuration
│   ├── inquiry.php               # Inquiry form configuration
│   ├── mail.php                  # Mail driver configuration
│   └── [others]/                 # Database, cache, session, etc.
├── database/                     # Database files
│   ├── migrations/               # Schema migrations
│   ├── seeders/                  # Database seeders
│   └── database.sqlite           # SQLite database (development)
├── public/                       # Web server root
│   ├── build/                    # Compiled Vite assets (CSS, JS)
│   ├── images/                   # Static images
│   ├── favicon.ico               # Site favicon
│   ├── favicon.svg               # SVG favicon
│   ├── apple-touch-icon.png      # iOS icon
│   └── index.php                 # Laravel entry point
├── resources/                    # Frontend assets and views
│   ├── css/
│   │   └── app.css               # Tailwind CSS entry point
│   ├── js/
│   │   ├── app.js                # Main JS entry point (empty)
│   │   └── passkeys.js           # Passkey authentication JS
│   └── views/                    # Blade templates
│       ├── components/           # Reusable Blade components
│       ├── flux/                 # Flux UI customizations
│       ├── layouts/
│       │   ├── app.blade.php     # Main authenticated layout
│       │   ├── auth.blade.php    # Authentication layout
│       │   ├── app/              # App layout partials (header, sidebar)
│       │   └── auth/             # Auth layout variants
│       ├── pages/
│       │   ├── auth/             # Auth pages (login, register, reset)
│       │   └── settings/         # Settings pages (profile, security, appearance)
│       ├── partials/             # Template partials (head, etc.)
│       ├── mail/                 # Email templates
│       ├── home.blade.php        # Public home page
│       └── dashboard.blade.php   # Authenticated dashboard
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes (public and auth)
│   ├── settings.php              # Settings routes (auth)
│   └── console.php               # Console commands
├── storage/                      # Runtime storage (logs, uploads)
│   ├── logs/                     # Application logs
│   ├── app/                      # Application storage
│   └── framework/                # Framework cache
├── tests/                        # Test suite
│   ├── Feature/                  # Feature tests (HTTP, Livewire)
│   │   ├── Auth/                 # Authentication tests
│   │   ├── Settings/             # Settings feature tests
│   │   └── DashboardTest.php
│   ├── Unit/                     # Unit tests
│   └── TestCase.php              # Base test class
├── vendor/                       # Composer dependencies (gitignored)
├── .claude/                      # Claude Code local configuration
├── .codex/                       # Codex integration
├── .github/                      # GitHub Actions, etc.
├── .planning/                    # Planning documents
│   └── codebase/                 # This file and architecture docs
├── package.json                  # Node.js dependencies (Vite, Tailwind)
├── composer.json                 # PHP dependencies
├── vite.config.js                # Vite build configuration
├── tailwind.config.js            # Tailwind CSS configuration
├── phpunit.xml                   # PHPUnit test configuration
└── README.md                     # Project documentation
```

## Directory Purposes

**`app/`:**
- Purpose: Core application source code
- Contains: Controllers, models, actions, middleware, service providers
- Key files: `Models/User.php`, `Models/Project.php`, `Http/Controllers/HomeController.php`

**`app/Actions/`:**
- Purpose: Decouple reusable business logic from controllers
- Contains: Fortify authentication actions (user creation, password reset)
- Key files: `Fortify/CreateNewUser.php`, `Fortify/ResetUserPassword.php`

**`app/Concerns/`:**
- Purpose: Encapsulate shared behavior as traits
- Contains: Validation rule traits for reuse across components and actions
- Key files: `ProfileValidationRules.php`, `PasswordValidationRules.php`

**`app/Models/`:**
- Purpose: Define application data models
- Contains: Eloquent models with relationships, methods, factories
- Key files: `User.php` (auth + 2FA + passkeys), `Project.php` (portfolio projects)

**`app/Http/Controllers/`:**
- Purpose: Handle HTTP requests
- Contains: Single-action controllers mapping routes to business logic
- Key files: `HomeController.php` (portfolio display)

**`config/`:**
- Purpose: Application configuration
- Contains: Environment-specific settings, service configs
- Key files: `fortify.php`, `auth.php`, `inquiry.php`

**`database/`:**
- Purpose: Database schema and seeding
- Contains: Migrations defining tables, seeders populating test data
- Key files: `migrations/2026_08_31_055858_create_projects_table.php`

**`resources/css/`:**
- Purpose: Frontend styling
- Contains: Tailwind CSS configuration and imports
- Key files: `app.css` (imports Tailwind, Flux, theme config)

**`resources/js/`:**
- Purpose: Frontend JavaScript
- Contains: Application-level JS initialization
- Key files: `app.js` (empty), `passkeys.js` (passkey auth)

**`resources/views/`:**
- Purpose: Server-rendered HTML templates
- Contains: Blade templates for pages, layouts, components
- Key files: `home.blade.php`, `dashboard.blade.php`, `layouts/app.blade.php`

**`resources/views/components/`:**
- Purpose: Reusable Blade components
- Contains: Partial templates for modular UI pieces
- Examples: `app-logo.blade.php`, `passkey-registration.blade.php`, `desktop-user-menu.blade.php`

**`resources/views/layouts/`:**
- Purpose: Page layout wrappers
- Contains: Parent templates defining structure for pages
- Key files: `app.blade.php`, `auth.blade.php`

**`resources/views/pages/`:**
- Purpose: Page-specific templates
- Contains: Auth pages (login, register), settings pages (Livewire components)
- Key directories: `pages/auth/`, `pages/settings/`

**`routes/`:**
- Purpose: Route definitions
- Contains: Mapping of URLs to controllers and view paths
- Key files: `web.php` (public/home routes), `settings.php` (auth settings routes)

**`tests/`:**
- Purpose: Automated tests
- Contains: Feature tests (HTTP routes, Livewire), unit tests
- Key directories: `Feature/Auth/`, `Feature/Settings/`

**`public/`:**
- Purpose: Web-accessible files served directly
- Contains: Compiled frontend assets, static files, entry point
- Key files: `index.php` (Laravel entry point), `build/` (Vite output)

**`storage/`:**
- Purpose: Runtime data storage
- Contains: Logs, cache, uploaded files
- Key directories: `logs/`, `app/`, `framework/`

## Key File Locations

**Entry Points:**
- `public/index.php` - Web server entry point; bootstraps Laravel
- `routes/web.php` - Defines all routes (public and authenticated)
- `app/Providers/AppServiceProvider.php` - Application bootstrap

**Configuration:**
- `.env` - Environment variables (secrets, database, mail)
- `config/app.php` - Application name, timezone, locale
- `config/fortify.php` - Authentication features (2FA, passkeys, emails)
- `vite.config.js` - Frontend build configuration
- `tailwind.config.js` - Tailwind CSS theme and plugins

**Core Logic:**
- `app/Http/Controllers/HomeController.php` - Fetch and render projects
- `app/Models/User.php` - User authentication and profile
- `app/Models/Project.php` - Portfolio project data
- `app/Actions/Fortify/CreateNewUser.php` - User registration logic

**Styling:**
- `resources/css/app.css` - Tailwind imports and theme (retro palette: paper, ink, pink, teal, mustard)
- `tailwind.config.js` - Custom color tokens and plugins

**Frontend:**
- `resources/views/home.blade.php` - Public portfolio home page
- `resources/views/dashboard.blade.php` - Authenticated dashboard
- `resources/views/layouts/app.blade.php` - Main authenticated layout (Flux sidebar)
- `resources/views/layouts/auth.blade.php` - Authentication layout variants

**Testing:**
- `phpunit.xml` - PHPUnit configuration
- `tests/Feature/` - Feature tests (HTTP integration tests)
- `tests/Unit/` - Unit tests

## Naming Conventions

**Files:**
- Controllers: `*Controller.php` (e.g., `HomeController.php`)
- Models: PascalCase (e.g., `User.php`, `Project.php`)
- Migrations: `YYYY_MM_DD_HHMMSS_description.php` (timestamp-based)
- Livewire components: ⚡-prefixed for inline components (e.g., `⚡profile.blade.php`)
- Tests: `*Test.php` in `tests/Feature/` or `tests/Unit/`

**Directories:**
- App namespaces: `app/{Feature}` (Actions, Concerns, Http, Livewire, Mail, Models, Providers)
- Features: CamelCase (e.g., `Enums/`, `Concerns/`)
- View paths: snake_case (e.g., `pages/auth/`, `pages/settings/`)

**Classes:**
- PascalCase (e.g., `HomeController`, `CreateNewUser`, `ProfileValidationRules`)
- Traits: End with plural or adjective (e.g., `ProfileValidationRules`, `HasFactory`, `TwoFactorAuthenticatable`)

**Database:**
- Tables: snake_case plural (e.g., `users`, `projects`, `passkeys`)
- Columns: snake_case (e.g., `email_verified_at`, `is_featured`, `sort_order`)
- Pivot tables: alphabetical singular names joined with underscore (e.g., `model_role`)

## Where to Add New Code

**New Feature:**
- Backend logic: `app/Actions/{Feature}/` or `app/Services/{Feature}/` (create if needed)
- Database: `database/migrations/` (run `php artisan make:migration`)
- Model: `app/Models/` (new or extend existing)
- Primary code: See next section for API-specific guidance

**New Controller/Route:**
- Controller: `app/Http/Controllers/{Feature}Controller.php` (run `php artisan make:controller`)
- Route: Add to `routes/web.php` or new route file
- Views: `resources/views/{feature}/`

**New Livewire Component:**
- Inline component (quick): `resources/views/pages/{feature}/⚡component-name.blade.php`
- Full component (testable): `app/Livewire/{Feature}/ComponentName.php` + `resources/views/livewire/{feature}/component-name.blade.php`

**New Blade Component:**
- Location: `resources/views/components/{component-name}.blade.php`
- Usage: `<x-component-name />`
- Namespace: Use `x-` prefix when including in views

**Utilities/Helpers:**
- Shared traits: `app/Concerns/{Concern}.php`
- Validation: `app/Concerns/{Feature}ValidationRules.php` trait
- Services: `app/Services/{Feature}Service.php` (create if needed)

**Frontend Assets:**
- Styles: `resources/css/app.css` (import modules if large)
- Scripts: `resources/js/` (minimal; prefer Alpine.js directives in templates)
- Images: `public/images/` (static) or `storage/app/public/` (user uploads)

**Configuration:**
- New config file: `config/{feature}.php`
- Environment settings: `.env` file (not committed)
- Per-environment: `config/{feature}-{environment}.php` if needed

**Testing:**
- Feature tests: `tests/Feature/{Feature}/FeatureTest.php`
- Unit tests: `tests/Unit/{Feature}/UnitTest.php`

## Special Directories

**`public/build/`:**
- Purpose: Vite-compiled frontend assets
- Generated: Yes (by `npm run build`)
- Committed: No (gitignored)
- Content: `app.css`, `app.js`, manifest JSON

**`storage/logs/`:**
- Purpose: Application error and debug logs
- Generated: Yes (by Laravel at runtime)
- Committed: No (gitignored)
- Content: Daily or single log file depending on config

**`storage/framework/views/`:**
- Purpose: Compiled Blade template cache
- Generated: Yes (by Laravel at runtime)
- Committed: No (gitignored)
- Content: Cached PHP-compiled Blade views

**`database/database.sqlite`:**
- Purpose: SQLite database file (development only)
- Generated: Yes (by migration)
- Committed: No (gitignored)
- Content: All application data

**`.planning/codebase/`:**
- Purpose: Architecture and structure documentation
- Generated: No (manually maintained via `/gsd-map-codebase`)
- Committed: Yes (planning documents)
- Content: ARCHITECTURE.md, STRUCTURE.md, CONVENTIONS.md, TESTING.md, etc.

---

*Structure analysis: 2026-09-01*
