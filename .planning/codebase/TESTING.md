# Testing Patterns

**Analysis Date:** 2026-09-01

## Test Framework

**Runner:**
- PHPUnit 12.5.23
- Config: `phpunit.xml`
- Bootstrap: `vendor/autoload.php`

**Assertion Library:**
- PHPUnit's built-in assertions
- Laravel testing helpers: `assertOk()`, `assertRedirect()`, `assertSessionHasNoErrors()`, etc.
- Livewire testing: `Livewire::test()` with component methods and assertions

**Run Commands:**
```bash
composer run test              # Run all tests with lint and types checking
php artisan test               # Run tests only
php artisan test --filter=DashboardTest  # Run specific test class
php artisan test tests/Feature/Auth/RegistrationTest.php  # Run specific file
composer run lint:check        # Check code style with Pint
composer run types:check       # Run static analysis with PHPStan
```

## Test File Organization

**Location:**
- Co-located with code under `tests/` directory parallel to `app/`
- Feature tests: `tests/Feature/` (route/integration tests)
- Unit tests: `tests/Unit/` (model/method tests)
- Feature subdirectories mirror feature areas: `tests/Feature/Auth/`, `tests/Feature/Settings/`

**Naming:**
- File pattern: `[Feature]Test.php` (e.g., `DashboardTest.php`, `RegistrationTest.php`)
- Class pattern: `[Feature]Test extends TestCase`
- Method pattern: `test_[scenario](): void` using snake_case description

**Structure:**
```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   ├── AuthenticationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── PasswordConfirmationTest.php
│   │   ├── EmailVerificationTest.php
│   │   ├── TwoFactorChallengeTest.php
│   ├── Settings/
│   │   ├── ProfileUpdateTest.php
│   │   ├── SecurityTest.php
│   ├── DashboardTest.php
├── Unit/
│   ├── ExampleTest.php
├── TestCase.php
```

## Test Structure

**Suite Organization:**
```php
class DashboardTest extends TestCase
{
    use RefreshDatabase;  // Reset database between tests

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }
}
```

**Patterns:**

- **Setup:** Use `protected function setUp(): void` to initialize test-specific configuration
- **Teardown:** Rarely needed; `RefreshDatabase` handles database cleanup
- **Assertion:** Chain assertions for fluent interface: `$response->assertOk()->assertSee('text')`
- **Database isolation:** `use RefreshDatabase;` automatically rolls back changes between tests
- **Feature skipping:** `$this->skipUnlessFortifyHas(Features::registration())` to conditionally skip tests based on feature flags

**Example setUp from `RegistrationTest.php`:**
```php
protected function setUp(): void
{
    parent::setUp();
    $this->skipUnlessFortifyHas(Features::registration());
}
```

## Mocking

**Framework:** Mockery for basic mocking, Laravel faking for specific types

**Patterns:**

**Notification faking:**
```php
Notification::fake();
User::factory()->create();
$this->post(route('password.request'), ['email' => $user->email]);
Notification::assertSentTo($user, ResetPassword::class);
```

**Livewire component faking:**
```php
Livewire::test('pages::settings.profile')
    ->set('name', 'Test User')
    ->call('updateProfileInformation')
    ->assertHasNoErrors();
```

**What to Mock:**
- External notifications (email, SMS) - use Notification::fake()
- Livewire components - use Livewire::test()
- Configuration for feature flags - use config() helper in tests

**What NOT to Mock:**
- Database queries (use real database with RefreshDatabase)
- Authentication (use actingAs() with real factory-created user)
- Validation rules (test actual validation logic)
- Route resolution (test actual routes)

## Fixtures and Factories

**Test Data:**
- Use model factories for creating test records
- Factory pattern: `User::factory()->create()` creates and saves a user
- State modifiers chain: `User::factory()->unverified()->create()`

**Location:**
- Factories: `database/factories/` directory
- Named as `[ModelName]Factory.php` (e.g., `UserFactory.php`)

**Factory Example from `UserFactory.php`:**
```php
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
```

**Usage in tests:**
```php
$user = User::factory()->create();                    // Basic user
$user = User::factory()->unverified()->create();      // Unverified email
$user = User::factory()->withTwoFactor()->create();   // With 2FA enabled
```

## Coverage

**Requirements:** Coverage not explicitly enforced; PHPStan Level 7 provides static analysis

**View Coverage:**
```bash
php artisan test --coverage  # Show coverage report (requires pcov/xdebug)
```

**Coverage scope:** Includes `app/` directory per `phpunit.xml` source configuration

## Test Types

**Unit Tests:**
- Scope: Individual methods/functions in isolation
- Approach: Test logic without database or HTTP requests
- Example: Testing `User::initials()` method logic
- File location: `tests/Unit/`
- Used rarely in this codebase (mostly scaffolding)

**Integration/Feature Tests:**
- Scope: Full request lifecycle (HTTP, routing, database)
- Approach: Test through real routes with real database (RefreshDatabase)
- Examples: Testing login flow, profile updates, settings changes
- File location: `tests/Feature/` with subdirectories by feature area
- Most tests in this codebase follow this pattern

**E2E Tests:**
- Framework: Not used
- Livewire provides in-process component testing as alternative to browser automation

## Common Patterns

**HTTP Testing:**
```php
// GET request and assertions
$response = $this->get(route('dashboard'));
$response->assertOk();
$response->assertSee('Dashboard');

// POST request with data
$response = $this->post(route('register.store'), [
    'name' => 'John Doe',
    'email' => 'test@example.com',
    'password' => 'password',
    'password_confirmation' => 'password',
]);

// Check for session errors
$response->assertSessionHasNoErrors();
$response->assertRedirect(route('dashboard'));
```

**Authentication Testing:**
```php
// Create user and authenticate
$user = User::factory()->create();
$this->actingAs($user);

// Test routes requiring authentication
$response = $this->get(route('profile.edit'));
$response->assertOk();

// Test unauthenticated redirect
$this->get(route('dashboard'))->assertRedirect(route('login'));
```

**Livewire Component Testing:**
```php
// Test component state and calls
$response = Livewire::test('pages::settings.profile')
    ->set('name', 'Test User')
    ->set('email', 'test@example.com')
    ->call('updateProfileInformation');

// Check for errors
$response->assertHasNoErrors();
$response->assertHasErrors(['field_name']);

// Verify state after update
$user->refresh();
$this->assertEquals('Test User', $user->name);
```

**Database Assertions:**
```php
// Check record exists in database
$this->assertDatabaseHas('users', [
    'id' => $user->id,
    'email' => $user->email,
]);

// Verify model instance was deleted
$this->assertNull($user->fresh());

// Check session state
$this->assertAuthenticated();
$this->assertFalse(auth()->check());
```

**Feature Flag Testing:**
```php
// Skip test if feature not enabled
$this->skipUnlessFortifyHas(Features::registration());

// Configure features for test
Features::twoFactorAuthentication(['confirm' => true]);

// Test with feature disabled
config(['fortify.features' => []]);
$response = $this->get(route('security.edit'));
$response->assertDontSee('Two-factor authentication');
```

**Error Testing:**
```php
// Test validation errors
$response = Livewire::test('pages::settings.delete-user-modal')
    ->set('password', 'wrong-password')
    ->call('deleteUser');

$response->assertHasErrors(['password']);

// Test that model still exists
$this->assertNotNull($user->fresh());
```

**Session Testing:**
```php
// Set session data for request
$response = $this->actingAs($user)
    ->withSession(['auth.password_confirmed_at' => time()])
    ->get(route('security.edit'));

// Check session errors are not present
$response->assertSessionHasNoErrors();
```

## Test Environment Configuration

**PHPUnit config (`phpunit.xml`):**
```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
</testsuites>

<source>
    <include>
        <directory>app</directory>
    </include>
</source>

<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <!-- Other testing environment variables -->
</php>
```

**Key testing env vars:**
- `APP_ENV=testing` - Disables certain services in test mode
- `DB_CONNECTION=sqlite` - Use SQLite for speed
- `DB_DATABASE=:memory:` - In-memory database, destroyed after each test
- `MAIL_MAILER=array` - Fake mail driver for testing
- `QUEUE_CONNECTION=sync` - Run jobs synchronously in tests
- `CACHE_STORE=array` - Use array cache in tests
- `PULSE_ENABLED=false`, `TELESCOPE_ENABLED=false` - Disable monitoring tools

---

*Testing analysis: 2026-09-01*
