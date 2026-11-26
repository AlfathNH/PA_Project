<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini adalah nama aplikasi yang akan digunakan ketika framework perlu
    | menampilkan nama aplikasi dalam notifikasi atau elemen UI lainnya.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Lingkungan Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini menentukan "environment" (lingkungan) di mana aplikasi sedang berjalan,
    | misalnya: local, staging, atau production. Atur nilainya di file ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode Debug Aplikasi
    |--------------------------------------------------------------------------
    |
    | Ketika aplikasi dalam mode debug, pesan error lengkap beserta stack trace
    | ditampilkan untuk setiap kesalahan yang terjadi. Jika dinonaktifkan,
    | hanya halaman error generik yang ditampilkan ke pengguna.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi
    |--------------------------------------------------------------------------
    |
    | URL ini digunakan oleh console untuk membuat URL yang benar saat memakai
    | Artisan command line. Isi dengan URL root aplikasi agar dapat diakses
    | dari dalam perintah-perintah Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona Waktu Aplikasi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan zona waktu default aplikasi yang akan digunakan
    | oleh fungsi-fungsi tanggal dan waktu PHP. Zona waktu default adalah "UTC"
    | karena cocok untuk sebagian besar kasus penggunaan.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Locale Aplikasi
    |--------------------------------------------------------------------------
    |
    | Locale aplikasi menentukan bahasa default yang digunakan oleh metode
    | terjemahan/lokalisasi Laravel. Dapat diubah ke locale apa pun yang
    | memiliki string terjemahan tersedia.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Kunci Enkripsi Aplikasi
    |--------------------------------------------------------------------------
    |
    | Kunci ini digunakan oleh layanan enkripsi Laravel dan harus berupa
    | string acak sepanjang 32 karakter untuk memastikan semua nilai terenkripsi
    | aman. Pastikan kunci ini sudah diisi sebelum deploy ke production.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Mode Pemeliharaan (Maintenance)
    |--------------------------------------------------------------------------
    |
    | Opsi ini menentukan driver yang digunakan untuk mengelola status
    | "maintenance mode" Laravel. Driver "cache" memungkinkan mode maintenance
    | dikontrol dari beberapa mesin server sekaligus.
    |
    | Driver yang didukung: "file", "cache", "array"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
