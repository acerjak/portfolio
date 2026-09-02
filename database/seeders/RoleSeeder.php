<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Seed the founder/admin/client roles and Amanda's founder account.
     *
     * Idempotent and safe to run repeatedly: roles and the founder user are
     * both created via `firstOrCreate`, so a re-run never duplicates data
     * and never touches an already-set founder password. Invoked
     * independently of `DatabaseSeeder::run()` via
     * `php artisan db:seed --class=RoleSeeder`.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['founder', 'admin', 'client'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $password = config('founder.password');

        if (! is_string($password) || $password === '') {
            throw new \RuntimeException('FOUNDER_PASSWORD must be set to seed the founder account.');
        }

        $founder = User::firstOrCreate(
            ['email' => config('founder.email')],
            [
                'name' => 'Amanda Cojerean',
                'password' => $password,
            ]
        );

        if ($founder->email_verified_at === null) {
            $founder->forceFill(['email_verified_at' => now()])->save();
        }

        if (! $founder->hasRole('founder')) {
            $founder->assignRole('founder');
        }
    }
}
