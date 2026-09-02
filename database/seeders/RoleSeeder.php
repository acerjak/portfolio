<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
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
        // Validate before any write: a misconfigured FOUNDER_PASSWORD must fail
        // closed before roles or the founder account are touched, not partway
        // through (retry-safe either way since every write below is firstOrCreate,
        // but failing first avoids a deploy that "half seeds" on the first attempt).
        $password = config('founder.password');

        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::default()]],
        );

        if ($validator->fails()) {
            throw new \RuntimeException(
                'FOUNDER_PASSWORD must be set and meet the app\'s password strength rule: '
                    .$validator->errors()->first('password')
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['founder', 'admin', 'client'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

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
