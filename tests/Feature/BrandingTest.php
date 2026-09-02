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

    public function test_homepage_icon_links_are_cache_busted(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('href="/favicon.ico?v=2"', false);
        $response->assertSee('href="/favicon.svg?v=2"', false);
        $response->assertSee('href="/apple-touch-icon.png?v=2"', false);
    }

    public function test_auth_page_icon_links_are_cache_busted(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('href="/favicon.ico?v=2"', false);
        $response->assertSee('href="/favicon.svg?v=2"', false);
        $response->assertSee('href="/apple-touch-icon.png?v=2"', false);
    }
}
