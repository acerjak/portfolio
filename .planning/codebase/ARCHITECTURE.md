<!-- refreshed: 2026-09-01 -->
# Architecture

**Analysis Date:** 2026-09-01

## System Overview

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                         Frontend (Browser)                              │
│  Blade Templates + Alpine.js + Tailwind CSS + Flux Components          │
│                    `resources/views/**`                                  │
└────────────┬─────────────────────────────────┬────────────────────────┘
             │                                 │
    Public Routes                    Authenticated Routes
         (GET)                        (POST/PUT form submission)
             │                                 │
             ▼                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         HTTP Layer (Laravel)                             │
│  Routes → Controllers → Livewire Components → Views                     │
│  `routes/web.php` + `app/Http/Controllers/**`                          │
│  `routes/settings.php` + Livewire Components at `resources/views/**`   │
└────────────┬──────────────────────────────────┬───────────────────────┘
             │                                  │
             ▼                                  ▼
┌──────────────────────────┐    ┌──────────────────────────────────────┐
│   Model/Business Layer   │    │  Authentication & Authorization      │
│  `app/Models/**`         │    │  Laravel Fortify + Passkeys         │
│  `app/Actions/**`        │    │  `app/Providers/FortifyServiceProvider`
│  `app/Concerns/**`       │    │  `app/Actions/Fortify/**`           │
└────────────┬─────────────┘    └──────────────────────────────────────┘
             │                                  │
             └──────────────────┬───────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                    Database Layer (Eloquent ORM)                         │
│  SQLite Database with migrations at `database/migrations/**`            │
│  Tables: users, projects, passkeys, cache, jobs                         │
│  `database/database.sqlite`                                              │
└─────────────────────────────────────────────────────────────────────────┘
```

## Component Responsibilities

| Component | Responsibility | File |
|-----------|----------------|------|
| HomeController | Fetch and render portfolio projects | `app/Http/Controllers/HomeController.php` |
| User Model | User authentication, profile data | `app/Models/User.php` |
| Project Model | Portfolio project data with meta info | `app/Models/Project.php` |
| Fortify Service Provider | Configure auth actions and views | `app/Providers/FortifyServiceProvider.php` |
| Settings Pages (Livewire) | Profile, security, appearance management | `resources/views/pages/settings/**` |
| Layouts | App sidebar layout, auth layout | `resources/views/layouts/**` |
| Components | Reusable Blade/Flux UI components | `resources/views/components/**` |

## Pattern Overview

**Overall:** Server-side rendered full-stack application with Livewire for interactivity.

**Key Characteristics:**
- Laravel 11 backend with Blade template engine
- Livewire 3 for reactive frontend components (form handling, state management)
- Vite for frontend asset bundling (CSS, JS)
- Tailwind CSS v4 with custom theme (retro color palette)
- Flux UI library providing pre-built components (sidebar, forms, modals)
- SQLite for data persistence

## Layers

**Presentation Layer (Frontend):**
- Purpose: Render user interfaces and handle user interactions
- Location: `resources/views/**`, `resources/css/**`, `resources/js/**`
- Contains: Blade templates, Livewire components (inline class definitions), Tailwind styles, minimal JavaScript
- Depends on: HTTP routes that serve templates
- Used by: Web browsers via HTTP requests

**HTTP/Routing Layer:**
- Purpose: Route requests to appropriate handlers
- Location: `routes/web.php`, `routes/settings.php`
- Contains: Route definitions for public pages, authenticated pages, settings
- Depends on: Controllers and Livewire components
- Used by: Browser requests, Livewire component method calls

**Application/Business Logic Layer:**
- Purpose: Implement core application logic
- Location: `app/Http/Controllers/**`, `app/Actions/**`, `app/Concerns/**`, `app/Enums/**`
- Contains: Request handlers, action classes (Fortify actions), validation rule traits
- Depends on: Models, configuration
- Used by: Routes, Livewire components

**Model/Data Layer:**
- Purpose: Define data structures and relationships
- Location: `app/Models/**`
- Contains: Eloquent models (User, Project) with attributes, methods, factories
- Depends on: Database schema
- Used by: Controllers, actions, Livewire components

**Database Layer:**
- Purpose: Persist application data
- Location: `database/migrations/**`, `database/seeders/**`, `database/database.sqlite`
- Contains: Schema definitions, data seeders
- Depends on: Nothing
- Used by: Eloquent ORM (models)

**Infrastructure/Configuration:**
- Purpose: Configure application settings and providers
- Location: `config/**`, `app/Providers/**`, `vite.config.js`, `tailwind.config.js`
- Contains: App configuration, service provider bootstrapping, build tools
- Depends on: Environment variables
- Used by: Laravel bootstrap process

## Data Flow

### Primary Request Path (Portfolio Home)

1. Browser requests `/` route (`routes/web.php:6`)
2. HomeController invoked (`app/Http/Controllers/HomeController.php:14`)
3. Query Project model ordered by featured/sort_order (`app/Http/Controllers/HomeController.php:16-19`)
4. Render `home.blade.php` template with projects data (`resources/views/home.blade.php`)
5. Template rendered to HTML with Blade/Alpine.js directives processed
6. Browser receives complete HTML with Tailwind-styled markup

### Settings Update Path (Authenticated)

1. User at `/settings/profile` route (Livewire route `resources/views/pages/settings/⚡profile.blade.php`)
2. Settings page component loaded as Livewire component
3. Form input captured by Livewire reactive properties (e.g., `$name`, `$email`)
4. User submits form → Livewire intercepts and calls component method (`updateProfileInformation()`)
5. Validation happens in component using ProfileValidationRules trait
6. User model updated in database
7. Toast notification displayed via Flux component
8. Component state updated reactively

### Authentication Path

1. User visits login page served by Fortify view
2. Registration/login form submits to Fortify endpoint
3. Fortify validates using CreateNewUser action
4. User created/authenticated via middleware
5. Redirected to dashboard or home

**State Management:**
- Livewire component properties hold form state (`public $name`, `public $email`)
- Component methods validate and persist state to database
- No centralized state management; Livewire handles reactivity per component
- Database is source of truth for user data

## Key Abstractions

**Validation Traits:**
- Purpose: Encapsulate validation rules for reuse
- Examples: `ProfileValidationRules`, `PasswordValidationRules` in `app/Concerns/**`
- Pattern: Trait methods return validation rule arrays used by Livewire components and actions

**Fortify Actions:**
- Purpose: Decouple authentication logic from framework
- Examples: `CreateNewUser`, `ResetUserPassword` in `app/Actions/Fortify/**`
- Pattern: Classes implement action pattern for user registration and password management

**Livewire Components (Inline):**
- Purpose: Define reactive components with logic and templates inline
- Examples: Settings pages with ⚡ prefix in `resources/views/pages/settings/`
- Pattern: PHP file with class definition extending Livewire Component, rendering Blade template at end

**Blade Layouts:**
- Purpose: Define page structure and common UI elements
- Examples: `app.blade.php`, `auth.blade.php` in `resources/views/layouts/**`
- Pattern: Parent layouts with `{{ $slot }}` placeholders, included via `@extends` or component syntax

## Entry Points

**Public Home Page:**
- Location: `routes/web.php:6`
- Triggers: GET `/`
- Responsibilities: Display portfolio projects, navigation, hero section

**Authentication Routes:**
- Location: `app/Providers/FortifyServiceProvider.php` configures views
- Triggers: GET `/login`, `/register`, `/forgot-password`; POST to Fortify endpoints
- Responsibilities: User registration, login, password reset

**Dashboard (Authenticated):**
- Location: `routes/web.php:8-10`
- Triggers: GET `/dashboard` (requires auth + verified middleware)
- Responsibilities: Show authenticated user landing page

**Settings Routes:**
- Location: `routes/settings.php:5-19`
- Triggers: GET `/settings/*` (requires auth)
- Responsibilities: User profile, security, appearance settings management via Livewire

## Architectural Constraints

- **Threading:** Single-threaded HTTP request/response model (standard Laravel)
- **Global state:** User authentication state held in session; no module-level singletons
- **Request lifecycle:** Each HTTP request/Livewire call follows Laravel's request lifecycle with middleware
- **Circular imports:** None detected
- **Database transactions:** Explicit transactions used for critical operations (auth actions)
- **File structure:** Laravel conventions (app, routes, resources, database, config, tests)

## Anti-Patterns

### Inline Livewire Components

**What happens:** Livewire components defined as inline PHP classes in Blade files (⚡ prefix convention)
**Why it's wrong:** Mixes presentation and logic in template files; harder to test; violates separation of concerns
**Do this instead:** Extract to proper Livewire component classes in `app/Livewire/` with separate Blade view templates

### Empty App Entrypoint

**What happens:** `resources/js/app.js` is empty despite being listed as Vite entry point
**Why it's wrong:** No client-side JavaScript bundling leveraged; potential for script initialization needs to be met elsewhere
**Do this instead:** Move client-side initialization (Alpine.js directives, event listeners) to app.js or use explicit script tags in layouts

### Direct Model Queries in Controllers

**What happens:** HomeController directly queries Project model without repository/service layer
**Why it's wrong:** Couples controller to query logic; harder to test and reuse
**Do this instead:** Extract query logic to repository or service class (e.g., `ProjectRepository`, `ProjectService`)

## Error Handling

**Strategy:** Laravel exception handling with custom error pages and validation error messages.

**Patterns:**
- Model validation through traits (`ProfileValidationRules`, `PasswordValidationRules`)
- Livewire form validation returns errors to component, displayed inline
- Fortify actions validate during registration/password reset
- Rate limiting middleware on auth endpoints (configured in FortifyServiceProvider)

## Cross-Cutting Concerns

**Logging:** Standard Laravel logging via facades (`Log::info()`)

**Validation:** 
- Trait-based validation rules for reuse
- Livewire components validate via `$this->validate()`
- Custom rules via Laravel's Validation API

**Authentication:**
- Session-based auth via Laravel Fortify
- Passkey support via `@laravel/passkeys` and PasskeyAuthenticatable trait
- Two-factor authentication via `TwoFactorAuthenticatable` trait
- Email verification enforced on sensitive routes

---

*Architecture analysis: 2026-09-01*
