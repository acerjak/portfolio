<?php

namespace Tests\Feature\Roles;

use Tests\TestCase;

class RegistrationClosedTest extends TestCase
{
    public function test_register_route_is_unreachable(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_login_page_still_renders_after_registration_is_disabled(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_the_login_page_offers_no_sign_up_link(): void
    {
        $this->get(route('login'))->assertDontSee('Sign up');
    }
}
