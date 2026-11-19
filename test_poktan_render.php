<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

$user = App\Models\User::where('role', 'kelompok_tani')->first();
if ($user) {
    Auth::login($user);
    $req = Illuminate\Http\Request::create('/poktan/dashboard', 'GET');
    $res = $kernel->handle($req);
    echo "POKTAN DASHBOARD STATUS: " . $res->getStatusCode() . " (length: " . strlen($res->getContent()) . ")\n";
    $content = $res->getContent();
    echo "HAS WORKER.GLB: " . (strpos($content, 'worker.glb') !== false ? "YES" : "NO") . "\n";
    echo "HAS MODEL-VIEWER: " . (strpos($content, '<model-viewer') !== false ? "YES" : "NO") . "\n";
    $kernel->terminate($req, $res);
} else {
    echo "User not found\n";
}
