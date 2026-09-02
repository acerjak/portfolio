<?php

namespace Tests\Feature\Roles;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
