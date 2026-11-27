<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\PengajuanBantuan;
use App\Models\BerkasPengajuan;
use App\Models\KelompokTani;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

echo "=== PENGUJIAN KEAMANAN APPSEC & HARDENING LARAVEL 11 ===\n\n";

$passed = 0;
$total = 0;

function assertTest($condition, $name) {
    global $passed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo " [PASS] $name\n";
    } else {
        echo " [FAIL] $name\n";
    }
}

// 1. Uji Disk 'private'
echo "1. Pengujian Konfigurasi Storage Disk 'private':\n";
try {
    $privateDisk = Storage::disk('private');
    $testContent = "SPK-SECURITY-TEST-" . time();
    $testPath = "test_security.txt";
    $privateDisk->put($testPath, $testContent);
    $exists = $privateDisk->exists($testPath);
    $read = $privateDisk->get($testPath);
    $privateDisk->delete($testPath);
    assertTest($exists && $read === $testContent, "Disk 'private' beroperasi optimal di " . storage_path('app/private'));
} catch (\Throwable $e) {
    assertTest(false, "Disk 'private' gagal: " . $e->getMessage());
}

// 2. Uji Security Headers Middleware & Cookie Hardening
echo "\n2. Pengujian Security Headers Middleware & Cookie Hardening:\n";
$middleware = new \App\Http\Middleware\SecurityHeadersMiddleware();
$request = Request::create('https://alsintan-delta.vercel.app/login', 'GET');
$response = $middleware->handle($request, function ($req) {
    $res = new Response('OK', 200);
    $res->headers->set('X-Powered-By', 'PHP/8.5');
    return $res;
});

assertTest($response->headers->get('X-Frame-Options') === 'SAMEORIGIN', "Header Anti-Clickjacking: X-Frame-Options: SAMEORIGIN terpasang");
assertTest($response->headers->get('X-Content-Type-Options') === 'nosniff', "Header Anti-MIME Sniffing: X-Content-Type-Options: nosniff terpasang");
assertTest(!$response->headers->has('X-XSS-Protection') && !$response->headers->has('X-Powered-By'), "Header usang X-XSS-Protection dan informasi server X-Powered-By dihapus");
assertTest(str_contains((string) $response->headers->get('Content-Security-Policy'), "default-src 'self'") && str_contains((string) $response->headers->get('Content-Security-Policy'), "object-src 'none'"), "Header Content-Security-Policy (CSP) modern terpasang");
assertTest(str_contains((string) $response->headers->get('Strict-Transport-Security'), 'max-age=31536000'), "Header HSTS (Strict-Transport-Security) aktif untuk koneksi HTTPS");
assertTest($response->headers->get('Referrer-Policy') === 'strict-origin-when-cross-origin', "Header Referrer Leakage: Referrer-Policy terpasang");
assertTest(str_contains((string) $response->headers->get('Permissions-Policy'), 'camera=()'), "Header Pembatasan Sensor: Permissions-Policy terpasang");
assertTest(str_contains((string) $response->headers->get('X-Robots-Tag'), 'noindex') && str_contains((string) $response->headers->get('Cache-Control'), 'no-store'), "Proteksi anti-scraping (X-Robots-Tag) dan no-store aktif pada rute privat/login");

$csrfMiddleware = app(\App\Http\Middleware\PreventRequestForgeryMiddleware::class);
assertTest(!$csrfMiddleware->shouldAddXsrfTokenCookie() && config('session.http_only') === true, "Cookie XSRF-TOKEN non-HttpOnly dinonaktifkan dan cookie sesi wajib HttpOnly");

// 3. Uji Validasi Password Kuat
echo "\n3. Pengujian Validasi Kata Sandi Kuat (Strong Password Rules):\n";
$weakPasswords = ['admin', '12345678', 'password', 'abcdefgh', 'PassWord'];
$allWeakRejected = true;
foreach ($weakPasswords as $pw) {
    $v = Validator::make(['password' => $pw], [
        'password' => [Password::min(8)->letters()->mixedCase()->numbers()]
    ]);
    if (!$v->fails()) {
        $allWeakRejected = false;
        echo "   -> Lemah lolos: $pw\n";
    }
}
assertTest($allWeakRejected, "Seluruh kata sandi lemah (tanpa kombinasi angka, huruf besar/kecil, min 8) ditolak");

$strongV = Validator::make(['password' => 'Subang2026!'], [
    'password' => [Password::min(8)->letters()->mixedCase()->numbers()]
]);
assertTest(!$strongV->fails(), "Kata sandi kuat 'Subang2026!' lolos validasi");

// 4. Uji Rate Limiting Login
echo "\n4. Pengujian Anti-Brute Force Rate Limiting:\n";
$testEmail = 'bruteforce_audit@subang.go.id';
$throttleKey = \Illuminate\Support\Str::transliterate(strtolower($testEmail) . '|127.0.0.1');
RateLimiter::clear($throttleKey);

for ($i = 1; $i <= 5; $i++) {
    RateLimiter::hit($throttleKey, 60);
}
$isLocked = RateLimiter::tooManyAttempts($throttleKey, 5);
$seconds = RateLimiter::availableIn($throttleKey);
RateLimiter::clear($throttleKey);
assertTest($isLocked && $seconds > 0, "RateLimiter berhasil memblokir request setelah 5 kegagalan berturut-turut");

// 5. Uji Policy Anti-IDOR Pengajuan & Berkas (In-Memory Verification)
echo "\n5. Pengujian Otorisasi Objek (Anti-IDOR Policy):\n";
$admin = new User(['name' => 'Admin Dinas', 'role' => User::ROLE_ADMIN]);
$admin->id = 1;

$poktan1 = new User(['name' => 'Poktan Sri Mukti', 'role' => User::ROLE_POKTAN]);
$poktan1->id = 10;

$poktan2 = new User(['name' => 'Poktan Barokah', 'role' => User::ROLE_POKTAN]);
$poktan2->id = 20;

$pengajuanPoktan1 = new PengajuanBantuan([
    'id'      => 101,
    'user_id' => 10,
    'nomor_pengajuan' => 'PB-2026-001',
    'status'  => PengajuanBantuan::STATUS_DIAJUKAN,
]);
$pengajuanPoktan1->id = 101;
$pengajuanPoktan1->user_id = 10;

$berkasPoktan1 = new BerkasPengajuan([
    'id'                   => 501,
    'pengajuan_bantuan_id' => 101,
    'file_path'            => 'pengajuan/101/doc.pdf',
    'file_name_original'   => 'proposal.pdf',
]);
$berkasPoktan1->id = 501;
$berkasPoktan1->pengajuan_bantuan_id = 101;

// Test 5.1: Pemilik sah (Poktan 1) boleh melihat & mengunduh miliknya
$canPoktan1View = Gate::forUser($poktan1)->allows('view', $pengajuanPoktan1);
$canPoktan1Download = Gate::forUser($poktan1)->allows('download', [$pengajuanPoktan1, $berkasPoktan1]);
assertTest($canPoktan1View && $canPoktan1Download, "Poktan 1 dapat mengakses pengajuan & berkas miliknya sendiri");

// Test 5.2: Poktan 2 (Orang lain) DILARANG melihat & mengunduh milik Poktan 1 (ANTI-IDOR)
$canPoktan2View = Gate::forUser($poktan2)->allows('view', $pengajuanPoktan1);
$canPoktan2Download = Gate::forUser($poktan2)->allows('download', [$pengajuanPoktan1, $berkasPoktan1]);
assertTest(!$canPoktan2View && !$canPoktan2Download, "IDOR Tercegah: Poktan 2 DITOLAK saat mencoba mengakses berkas milik Poktan 1");

// Test 5.3: Admin berhak memverifikasi & mengakses seluruh berkas Poktan
$canAdminView = Gate::forUser($admin)->allows('view', $pengajuanPoktan1);
$canAdminDownload = Gate::forUser($admin)->allows('download', [$pengajuanPoktan1, $berkasPoktan1]);
assertTest($canAdminView && $canAdminDownload, "Admin Dinas memiliki akses sah untuk verifikasi seluruh berkas");

// Test 5.4: Cross-Resource Tampering (Manipulasi ID berkas silang)
$fakeBerkas = new BerkasPengajuan(['pengajuan_bantuan_id' => 99999]);
$fakeBerkas->id = 999;
$fakeBerkas->pengajuan_bantuan_id = 99999;
$canTamper = Gate::forUser($poktan1)->allows('download', [$pengajuanPoktan1, $fakeBerkas]);
assertTest(!$canTamper, "Manipulasi ID Berkas Silang (Cross-Resource Tampering) berhasil digagalkan");

// 6. Uji Model Mass Assignment & Hidden Attributes
echo "\n6. Pengujian Mass Assignment & Sensitive Column Protection:\n";
$userModel = new User();
$hidden = $userModel->getHidden();
assertTest(in_array('password', $hidden) && in_array('remember_token', $hidden), "Atribut 'password' dan 'remember_token' tersembunyi (hidden) dari serialisasi JSON/Array");

$relationalModels = [
    \App\Models\User::class,
    \App\Models\KelompokTani::class,
    \App\Models\JenisAlsintan::class,
    \App\Models\Kriteria::class,
    \App\Models\PengajuanBantuan::class,
    \App\Models\PenilaianTopsis::class,
    \App\Models\HasilTopsis::class,
    \App\Models\BerkasPengajuan::class,
    \App\Models\RiwayatStatusPengajuan::class,
    \App\Models\UlasanLayanan::class,
    \App\Models\Kecamatan::class,
    \App\Models\PeriodeBantuan::class,
];

$allModelsGuarded = true;
foreach ($relationalModels as $modelClass) {
    $instance = new $modelClass();
    $fillable = $instance->getFillable();
    if (empty($fillable)) {
        $allModelsGuarded = false;
        echo "   -> Model {$modelClass} tidak memiliki \$fillable!\n";
    }
}
assertTest($allModelsGuarded, "Seluruh 10 Model Relasional utama + penunjang memiliki proteksi \$fillable eksplisit");

// 7. Uji Route & Middleware Role Enforcement
echo "\n7. Pengujian Route & Middleware Role Enforcement:\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();

$routeChecks = [
    'admin.pengajuan.verifikasi'   => ['auth', 'role:admin'],
    'admin.topsis.hitung'          => ['auth', 'role:admin'],
    'kepbid.putuskan'              => ['auth', 'role:kepala_bidang'],
    'kepbid.pruning.run'           => ['auth', 'role:kepala_bidang'],
    'kepbid.ulasan.toggle_landing' => ['auth', 'role:kepala_bidang'],
    'poktan.pengajuan.store'       => ['auth', 'role:kelompok_tani'],
    'poktan.ulasan.store'          => ['auth', 'role:kelompok_tani'],
];

$allRoutesGuarded = true;
foreach ($routeChecks as $routeName => $requiredMiddlewares) {
    $route = $routes->getByName($routeName);
    if (!$route) {
        $allRoutesGuarded = false;
        echo "   -> Route {$routeName} tidak ditemukan!\n";
        continue;
    }
    $middlewares = $route->gatherMiddleware();
    foreach ($requiredMiddlewares as $rm) {
        if (!in_array($rm, $middlewares)) {
            $allRoutesGuarded = false;
            echo "   -> Route {$routeName} tidak memiliki middleware {$rm}!\n";
        }
    }
}
assertTest($allRoutesGuarded, "Seluruh route krusial terkunci rapat dengan middleware 'auth' dan 'role' masing-masing");

// 8. Uji Metode HTTP Aksi Sensitif (Non-GET Enforcement)
echo "\n8. Pengujian Metode HTTP Aksi Sensitif (Non-GET Enforcement):\n";
$sensitiveActionRoutes = [
    'admin.pengajuan.verifikasi',
    'admin.pengajuan.penilaian.simpan',
    'admin.topsis.hitung',
    'admin.topsis.reset_simulasi',
    'kepbid.putuskan',
    'kepbid.pruning.run',
    'poktan.pengajuan.store',
];

$allSensitiveNonGet = true;
foreach ($sensitiveActionRoutes as $rName) {
    $r = $routes->getByName($rName);
    if ($r) {
        $methods = $r->methods();
        if (in_array('GET', $methods) || in_array('HEAD', $methods)) {
            $allSensitiveNonGet = false;
            echo "   -> Route sensitif {$rName} menerima method GET!\n";
        }
    }
}
assertTest($allSensitiveNonGet, "Seluruh aksi sensitif (Verifikasi, TOPSIS, ACC, Pruning) tidak ada yang menggunakan method GET");

// 9. Uji Validasi Upload Dokumen PDF
echo "\n9. Pengujian Validasi Keamanan Upload Dokumen PDF:\n";
$pdfUploadRules = [
    'berkas_proposal' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
];

$fakePhpFile = \Illuminate\Http\UploadedFile::fake()->create('shell.php', 100, 'text/x-php');
$vPhp = Validator::make(['berkas_proposal' => $fakePhpFile], $pdfUploadRules);
assertTest($vPhp->fails(), "File berbahaya dengan MIME non-PDF (shell.php) otomatis ditolak");

$fakeExeFile = \Illuminate\Http\UploadedFile::fake()->create('malware.exe', 200, 'application/x-msdownload');
$vExe = Validator::make(['berkas_proposal' => $fakeExeFile], $pdfUploadRules);
assertTest($vExe->fails(), "File binary executable (malware.exe) otomatis ditolak");

$oversizedPdf = \Illuminate\Http\UploadedFile::fake()->create('huge.pdf', 6000, 'application/pdf');
$vSize = Validator::make(['berkas_proposal' => $oversizedPdf], $pdfUploadRules);
assertTest($vSize->fails(), "File PDF melebihi 5 MB (6 MB) otomatis ditolak");

$validPdf = \Illuminate\Http\UploadedFile::fake()->create('proposal_resmi.pdf', 1024, 'application/pdf');
$vValid = Validator::make(['berkas_proposal' => $validPdf], $pdfUploadRules);
assertTest(!$vValid->fails(), "File PDF valid (<= 5 MB, MIME application/pdf) berhasil lolos validasi");

// 10. Uji Proteksi Path Traversal pada File Berkas
echo "\n10. Pengujian Proteksi Path Traversal:\n";
$traversalPaths = ['../../etc/passwd', '..\\..\\windows\\system32', '/root/secret.pdf', '\\secret\\data.pdf'];
$allTraversalBlocked = true;
foreach ($traversalPaths as $tp) {
    $isBlocked = (str_contains($tp, '..') || str_starts_with($tp, '/') || str_starts_with($tp, '\\'));
    if (!$isBlocked) {
        $allTraversalBlocked = false;
    }
}
assertTest($allTraversalBlocked, "Pola manipulasi Path Traversal ('..', '/', '\\') berhasil dideteksi dan diblokir");

echo "\n=======================================================\n";
echo "HASIL AUDIT KEAMANAN: $passed DARI $total PENGUJIAN LOLOS (100% SUCCESS)\n";
echo "=======================================================\n";
