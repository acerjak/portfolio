<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    public function test_forwarded_proto_from_a_trusted_proxy_marks_the_request_as_secure(): void
    {
        Route::get('/__trusted-proxy-probe', fn (Request $request) => [
            'secure' => $request->secure(),
            'ip' => $request->ip(),
        ]);

        $response = $this->get('/__trusted-proxy-probe', ['X-Forwarded-Proto' => 'https']);

        $response->assertOk();
        $response->assertJsonPath('secure', true);
    }

    public function test_forwarded_for_from_a_trusted_proxy_resolves_the_real_client_ip(): void
    {
        Route::get('/__trusted-proxy-probe', fn (Request $request) => [
            'secure' => $request->secure(),
            'ip' => $request->ip(),
        ]);

        $response = $this->get('/__trusted-proxy-probe', ['X-Forwarded-For' => '203.0.113.9']);

        $response->assertOk();
        $response->assertJsonPath('ip', '203.0.113.9');
    }
}
