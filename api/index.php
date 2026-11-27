<?php

// Nonaktifkan header X-Powered-By PHP dan sembunyikan informasi versi runtime
@ini_set('expose_php', '0');
if (function_exists('header_remove')) {
    @header_remove('X-Powered-By');
}

// Paksa HTTPS flag pada lingkungan serverless Vercel agar tidak ada mixed-content HTTP
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// Buat struktur direktori sementara di /tmp agar Laravel bisa menulis cache, session & upload di Vercel
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/private',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Teruskan seluruh request masuk ke public/index.php Laravel
require __DIR__ . '/../public/index.php';
