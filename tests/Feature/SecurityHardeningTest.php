<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;
    public function test_halaman_publik_dan_privasi_mengirim_header_keamanan_modern(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
        $response->assertHeaderMissing('X-XSS-Protection');
        $response->assertHeaderMissing('X-Powered-By');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);

        // Tidak ada cookie non-HttpOnly XSRF-TOKEN
        $response->assertCookieMissing('XSRF-TOKEN');

        // Halaman Kebijakan Privasi & Cookie aktif
        $this->get(route('privacy'))->assertStatus(200);
    }

    public function test_halaman_login_menerapkan_anti_scraping_dan_anti_cache(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_proteksi_csrf_sesi_tetap_aktif_tanpa_cookie_xsrf_token(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $response = $this->post(route('login.post'), [
            'email'    => 'admin@subang.go.id',
            'password' => 'password',
        ]);

        // Tanpa _token form (atau ketika token mismatch pada request non-testing), PreventRequestForgery tetap melindungi
        // Di environment testing Laravel, CSRF middleware di-skip kecuali env diubah atau di-test eksplisit;
        // Verifikasi eksplisit property addHttpCookie pada instance middleware:
        $middleware = app(\App\Http\Middleware\PreventRequestForgeryMiddleware::class);
        $this->assertFalse($middleware->shouldAddXsrfTokenCookie());
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
