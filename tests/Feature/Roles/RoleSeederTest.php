<?php

namespace Tests\Feature\Roles;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_seeding_creates_the_founder_account_with_the_founder_role(): void
    {
        config(['founder.password' => 'seeder-test-secret']);

        $this->seed(RoleSeeder::class);

        $founder = User::query()->where('email', config('founder.email'))->first();

        $this->assertNotNull($founder);
        $this->assertTrue($founder->hasRole('founder'));
    }

    public function test_all_three_roles_exist_after_seeding(): void
    {
        config(['founder.password' => 'roles-exist-secret']);

        $this->seed(RoleSeeder::class);

        $this->assertSame(3, Role::query()->count());
        $this->assertEqualsCanonicalizing(
            ['founder', 'admin', 'client'],
            Role::query()->pluck('name')->all()
        );
    }

    public function test_founder_is_a_singleton_and_holds_only_the_founder_role(): void
    {
        config(['founder.password' => 'singleton-secret']);

        $this->seed(RoleSeeder::class);

        $this->assertSame(1, User::query()->role('founder')->count());

        $founder = User::query()->where('email', config('founder.email'))->first();

        $this->assertNotNull($founder);
        $this->assertSame(['founder'], $founder->roles->pluck('name')->all());
    }

    public function test_seeding_twice_is_idempotent(): void
    {
        config(['founder.password' => 'idempotent-secret']);

        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class);

        $this->assertSame(3, Role::query()->count());
        $this->assertSame(1, User::query()->where('email', config('founder.email'))->count());
    }

    public function test_seeding_converges_from_a_partially_seeded_database(): void
    {
        foreach (['founder', 'admin', 'client'] as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web']);
        }

        config(['founder.password' => 'converge-secret']);

        $this->seed(RoleSeeder::class);

        $founder = User::query()->where('email', config('founder.email'))->first();

        $this->assertNotNull($founder);
        $this->assertTrue($founder->hasRole('founder'));
    }

    public function test_founder_passes_admin_capability_check(): void
    {
        config(['founder.password' => 'capability-secret']);

        $this->seed(RoleSeeder::class);

        $founder = User::query()->where('email', config('founder.email'))->first();

        $this->assertTrue($founder->hasAnyRole(['founder', 'admin']));

        $client = User::factory()->create();
        $client->assignRole('client');

        $this->assertFalse($client->hasAnyRole(['founder', 'admin']));
    }

    public function test_seeder_fails_loudly_when_the_founder_password_is_not_configured(): void
    {
        config(['founder.password' => null]);

        $thrown = false;

        try {
            $this->seed(RoleSeeder::class);
        } catch (\RuntimeException) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Expected RuntimeException was not thrown.');
        $this->assertDatabaseMissing('users', ['email' => config('founder.email')]);
    }

    public function test_reseeding_does_not_change_an_existing_founder_password(): void
    {
        config(['founder.password' => 'first-secret']);
        $this->seed(RoleSeeder::class);

        $originalHash = User::query()->where('email', config('founder.email'))->value('password');

        config(['founder.password' => 'second-different-secret']);
        $this->seed(RoleSeeder::class);

        $newHash = User::query()->where('email', config('founder.email'))->value('password');

        $this->assertSame($originalHash, $newHash);
    }
}
