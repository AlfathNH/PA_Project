<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: SecurityHeadersMiddleware
 * 
 * Menambahkan HTTP Security Response Headers sesuai standar industri OWASP & Web App Security:
 * 1. X-Frame-Options: SAMEORIGIN
 *    Mencegah serangan Clickjacking (UI Redressing) dengan melarang situs di-embed ke dalam iframe domain lain.
 * 2. X-Content-Type-Options: nosniff
 *    Mencegah browser melakukan MIME-sniffing terhadap berkas statis atau lampiran dokumen.
 * 3. X-XSS-Protection: 1; mode=block
 *    Mengaktifkan filter perlindungan Cross-Site Scripting (XSS) bawaan pada browser client.
 * 4. Referrer-Policy: strict-origin-when-cross-origin
 *    Mencegah kebocoran parameter URL sensitif saat browser berpindah domain.
 * 5. Permissions-Policy: camera=(), microphone=(), geolocation=()
 *    Membatasi hak akses perangkat keras yang tidak dibutuhkan oleh portal bantuan pertanian.
 */
class SecurityHeadersMiddleware
{
    /**
     * Memproses request yang masuk dan menambahkan header keamanan HTTP ke response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Pasang header keamanan pada objek response
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}

