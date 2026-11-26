<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Guard & Broker Autentikasi Default
    |--------------------------------------------------------------------------
    |
    | Opsi ini menentukan "guard" autentikasi dan "broker" reset kata sandi
    | yang digunakan secara default oleh aplikasi. Nilainya dapat diubah
    | sesuai kebutuhan, namun konfigurasi ini sudah cukup untuk kebanyakan aplikasi.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guard Autentikasi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mendefinisikan setiap guard autentikasi yang digunakan
    | aplikasi. Konfigurasi default sudah tersedia dan menggunakan penyimpanan
    | sesi (session) bersama provider Eloquent.
    |
    | Setiap guard memiliki user provider yang menentukan cara data pengguna
    | diambil dari database atau sistem penyimpanan lainnya. Umumnya menggunakan Eloquent.
    |
    | Didukung: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Pengguna
    |--------------------------------------------------------------------------
    |
    | Setiap guard memiliki user provider yang mendefinisikan cara data pengguna
    | diambil dari database atau sistem penyimpanan aplikasi. Biasanya Eloquent.
    |
    | Jika memiliki lebih dari satu tabel atau model pengguna, Anda dapat
    | mengkonfigurasi beberapa provider, lalu menetapkan masing-masing ke guard
    | autentikasi yang sesuai.
    |
    | Didukung: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reset Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Opsi-opsi ini mengatur perilaku fitur reset kata sandi Laravel, termasuk
    | tabel yang digunakan untuk menyimpan token dan provider pengguna yang
    | dipanggil untuk mengambil data akun.
    |
    | Waktu kedaluwarsa (expire) adalah jumlah menit setiap token reset dianggap
    | masih valid. Fitur keamanan ini membatasi umur token agar lebih sulit ditebak.
    |
    | Pengaturan throttle adalah jumlah detik yang harus ditunggu pengguna sebelum
    | dapat membuat token reset baru, untuk mencegah pembuatan token dalam jumlah besar.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Konfirmasi Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan jumlah detik sebelum jendela konfirmasi
    | kata sandi kedaluwarsa dan pengguna diminta memasukkan ulang kata sandinya
    | melalui layar konfirmasi. Secara default, batas waktu adalah tiga jam.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
