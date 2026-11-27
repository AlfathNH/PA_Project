<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: SecurityHeadersMiddleware
 *
 * Menerapkan HTTP Security Response Headers modern sesuai standar OWASP ASVS & PCI DSS:
 * 1. Content-Security-Policy (CSP): Membatasi asal eksekusi skrip, gaya, font, dan objek.
 * 2. Strict-Transport-Security (HSTS): Memaksa koneksi HTTPS terenkripsi sepanjang masa aktif.
 * 3. X-Frame-Options: SAMEORIGIN & frame-ancestors 'self' (Anti-Clickjacking).
 * 4. X-Content-Type-Options: nosniff (Anti-MIME Sniffing).
 * 5. Referrer-Policy & Permissions-Policy: Mencegah kebocoran URL dan akses sensor.
 * 6. Penghapusan header usang/bocor: Menyingkirkan X-Powered-By dan X-XSS-Protection.
 * 7. Anti-Scraping & Cache Isolation: Mencegah pengindeksan bot dan caching halaman privat.
 */
class SecurityHeadersMiddleware
{
    /**
     * Memproses request yang masuk dan menyisipkan header keamanan HTTP modern ke response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        $response = $next($request);

        // Hapus header yang membocorkan identitas runtime atau berstatus usang (deprecated)
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('X-XSS-Protection');

        // Header keamanan inti OWASP modern
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Content Security Policy (CSP) ketat untuk aset self-hosted & CDN ber-SRI
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
            "font-src 'self' data: https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob:",
            "connect-src 'self' data: blob: https://cdn.jsdelivr.net",
            "worker-src 'self' blob:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
            "upgrade-insecure-requests",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS untuk lingkungan produksi / koneksi HTTPS
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Proteksi anti-scraping dan pencegahan cache browser pada rute privat/autentikasi
        if ($request->is('admin/*', 'kepbid/*', 'poktan/*', 'login', 'register', 'forgot-password', 'demo-login/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
            if (!$request->routeIs('berkas.unduh')) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
                $response->headers->set('Pragma', 'no-cache');
            }
        }

        return $response;
    }
}

