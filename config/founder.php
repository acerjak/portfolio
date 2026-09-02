<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Founder Account
    |--------------------------------------------------------------------------
    |
    | The single founder account is seeded by `database/seeders/RoleSeeder.php`.
    | The email is a fixed, deliberate constant (the portfolio-brand address,
    | not Amanda's personal/dev email). The password is read from the
    | FOUNDER_PASSWORD environment variable and must never be hardcoded or
    | committed. Reading it through this config file (rather than the env()
    | helper directly in the seeder) keeps it available even after
    | `php artisan config:cache` has run on a deploy.
    |
    */

    'email' => 'amandacojerean@gmail.com',

    'password' => env('FOUNDER_PASSWORD'),

];
