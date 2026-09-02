<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_name_resolves_to_amanda_cojerean(): void
    {
        $this->assertSame('Amanda Cojerean', config('app.name'));
    }

    public function test_homepage_title_reads_amanda_cojerean_portfolio(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>Amanda Cojerean &middot; Portfolio</title>', false);
    }
}
