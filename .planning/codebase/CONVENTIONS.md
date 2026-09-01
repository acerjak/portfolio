# Coding Conventions

**Analysis Date:** 2026-09-01

## Naming Patterns

**Files:**
- Controllers: `[Subject]Controller.php` (e.g., `HomeController.php`)
- Models: PascalCase matching table names (e.g., `User.php`, `Project.php`)
- Actions: PascalCase verbs/nouns in `app/Actions/[Context]/` (e.g., `CreateNewUser.php`)
- Traits/Concerns: PascalCase in `app/Concerns/` (e.g., `PasswordValidationRules.php`)
- Enums: PascalCase in `app/Enums/` (e.g., `InquiryReason.php`)
- Mail classes: PascalCase in `app/Mail/` (e.g., `InquiryReceived.php`)
- Factories: `[ModelName]Factory.php` in `database/factories/` (e.g., `UserFactory.php`)

**Functions/Methods:**
- camelCase for public/protected methods
- Snake_case for validation rule methods (e.g., `passwordRules()`, `profileRules()`)
- Single-responsibility naming: `create()`, `update()`, `delete()`, `__invoke()` for single-action classes
- Factory modifiers use descriptive verbs: `unverified()`, `withTwoFactor()`

**Variables:**
- camelCase for local variables (e.g., `$user`, `$projects`, `$email`)
- snake_case for array keys matching database columns (e.g., `['email' => '...']`)
- UPPERCASE for constants (rare, usually enum cases instead)

**Types/Classes:**
- PascalCase for all class names
- Enum cases: UPPERCASE with underscores (e.g., `case BrandDeal = 'brand_deal'`)

## Code Style

**Formatting:**
- Tool: Laravel Pint (PSR-12 preset)
- Run: `composer run lint` for formatting, `composer run lint:check` to verify
- Indentation: 4 spaces
- Line length: No hard limit enforced, but keep readable
- File ending: Single newline

**Linting:**
- Tool: Pint with Laravel preset (`pint.json`)
- Static analysis: PHPStan Level 7 (`phpstan.neon`)
- Config includes: Larastan extension, Carbon extension
- Paths analyzed: `app/`, `bootstrap/app.php`, `config/`, `database/`, `routes/`

## Import Organization

**Order:**
1. `<?php` opening tag with namespace
2. Namespace declaration (`namespace App\Models;`)
3. Blank line
4. Import statements (use declarations):
   - Framework imports first (`Illuminate\...`)
   - Third-party imports (Laravel packages like `Laravel\Fortify\...`)
   - Application imports (`App\Models\`, `App\Enums\`, `Database\Factories\`)
5. Blank line before class/trait definition

**Path Aliases:**
- PSR-4 autoloading: `App\` → `app/`, `Database\` → `database/`, `Tests\` → `tests/`
- No custom path aliases configured

## Error Handling

**Patterns:**
- Validation: Use `Validator::make()->validate()` in Action classes (throws `ValidationException`)
- Route validation: Use `Request` class with Laravel's built-in rules
- Feature detection: Use `Features::enabled()` to check Fortify features and skip tests accordingly
- Database operations: Let Laravel's error handling bubble (no explicit try-catch in most code)
- Constructor validation: Parameters are type-hinted, validation happens in calling layer

**Example from `CreateNewUser.php`:**
```php
Validator::make($input, [
    ...$this->profileRules(),
    'password' => $this->passwordRules(),
])->validate(); // Throws ValidationException on failure
```

## Logging

**Framework:** Built-in Laravel logging via `Illuminate\Support\Facades\Log`

**Patterns:**
- Not extensively used in application code; logging handled by framework middleware
- Debug info available via Laravel Pail: `composer require laravel/pail --dev` and `php artisan pail`
- Tests use Notification faking to verify mail was sent

## Comments

**When to Comment:**
- Class-level docblocks with `@property` annotations for Eloquent models (document cast/attribute types)
- Method-level docblocks for public methods with complex parameters or return types
- In-line comments rare; prefer self-documenting code with clear naming

**JSDoc/PHPDoc:**
- Type hints on array parameters: `@param array<string, string> $input`
- Return type documentation: `@return array<string, string>`
- Trait usage documentation: `@use HasFactory<UserFactory>`
- Model properties documented as comments referencing database columns and casts

## Function Design

**Size:** Keep methods focused on single responsibility. Example: `initials()` in User model is 6 lines.

**Parameters:**
- Use type hints on all parameters
- Constructor property promotion (readonly) for immutable dependency injection
- Array type hints with generic notation: `array<string, string>`

**Return Values:**
- All public methods must declare return type
- Fluent interfaces return `static` or `self` from factory state modifiers
- Action classes return specific types: `User`, `RedirectResponse`, `Redirector`

## Module Design

**Exports:**
- Models export via namespace; accessed as `App\Models\User`
- Actions implement interfaces: `implements CreatesNewUsers` (from Laravel Fortify)
- Traits provide reusable validation/behavior: `use PasswordValidationRules`

**Barrel Files:**
- Not used; direct imports from specific files

**Attribute-Based Configuration:**
- Use PHP 8 attributes on models: `#[Fillable([...])`, `#[Hidden([...])]`
- Replaces `$fillable` and `$hidden` properties

## Database/ORM Patterns

**Eloquent Models:**
- Use `#[Fillable]` attribute instead of `$fillable` property
- Use `#[Hidden]` attribute instead of `$hidden` property  
- Model properties documented with `@property` comments for IDE autocomplete
- Casts defined in `casts()` method: `protected function casts(): array`
- Cast types: `'datetime'`, `'hashed'`, `'array'`, `'boolean'`

**Query Building:**
- Method chaining: `Project::query()->orderByDesc('is_featured')->orderBy('sort_order')->get()`
- Single responsibility: Each model method does one thing

**Example from `HomeController.php`:**
```php
$projects = Project::query()
    ->orderByDesc('is_featured')
    ->orderBy('sort_order')
    ->get();
```

## Enum Pattern

**Usage:**
- Backed enums with string values: `enum InquiryReason: string`
- Case names: PascalCase (e.g., `case BrandDeal`)
- Values: snake_case (e.g., `'brand_deal'`)
- Methods for display: `label()` returns human-readable string
- Match expressions for conditional logic within enum

**Example from `InquiryReason.php`:**
```php
enum InquiryReason: string {
    case General = 'general';
    case BrandDeal = 'brand_deal';
    
    public function label(): string {
        return match($this) {
            self::General => 'General',
            self::BrandDeal => 'Brand deal',
        };
    }
}
```

## Mail Pattern

**Classes:**
- Extend `Mailable` and implement `Queueable`
- Use constructor property promotion for data: `public readonly string $name`
- Define envelope in `envelope()` method: subject, reply-to, from
- Define content in `content()` method: markdown template path
- Use `Illuminate\Mail\Mailables\Address` for addresses with names

---

*Convention analysis: 2026-09-01*
