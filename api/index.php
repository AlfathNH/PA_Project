<?php

// Buat struktur direktori sementara di /tmp agar Laravel bisa menulis cache & view di Vercel
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Teruskan seluruh request masuk ke public/index.php Laravel
require __DIR__ . '/../public/index.php';
