<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: RoleMiddleware
 * Memastikan pengguna memiliki salah satu role yang diizinkan untuk mengakses rute.
 * Role yang didukung: 'kelompok_tani', 'admin', 'kepala_bidang'
 *
 * Contoh penggunaan di routes/web.php:
 * ->middleware('role:admin')
 * ->middleware('role:admin,kepala_bidang')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles Daftar role yang diizinkan (dipisah koma)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        // 2. Cek apakah status akun aktif
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh administrator.');
        }

        // 3. Cek apakah role user ada dalam daftar role yang diperbolehkan
        if (!in_array($user->role, $roles)) {
            // Arahkan ke dashboard sesuai role masing-masing jika salah kamar
            return match ($user->role) {
                'admin'         => redirect()->route('admin.dashboard')->with('warning', 'Anda tidak memiliki hak akses ke halaman tersebut.'),
                'kepala_bidang' => redirect()->route('kepbid.dashboard')->with('warning', 'Anda tidak memiliki hak akses ke halaman tersebut.'),
                'kelompok_tani' => redirect()->route('poktan.dashboard')->with('warning', 'Anda tidak memiliki hak akses ke halaman tersebut.'),
                default         => abort(403, 'Akses ditolak: Peran akun tidak valid.'),
            };
        }

        return $next($request);
    }
}

