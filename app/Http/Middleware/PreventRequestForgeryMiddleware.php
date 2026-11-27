<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

/**
 * Middleware CSRF Khusus Dinas Pertanian Kabupaten Subang.
 * Menonaktifkan cookie XSRF-TOKEN non-HttpOnly bawaan Laravel karena seluruh form
 * aplikasi menggunakan token terenkripsi (@csrf) di dalam sesi HttpOnly terproteksi.
 */
class PreventRequestForgeryMiddleware extends PreventRequestForgery
{
    /**
     * Jangan kirim cookie XSRF-TOKEN ke client agar tidak ada cookie non-HttpOnly.
     *
     * @var bool
     */
    protected $addHttpCookie = false;
}
