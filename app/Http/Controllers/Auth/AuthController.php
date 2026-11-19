<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KelompokTani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

/**
 * Controller: AuthController
 * Menangani proses otentikasi login, registrasi Poktan mandiri, logout, dan quick-login.
 */
class AuthController extends Controller
{
    private const MAX_AUTH_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS   = 300; // 5 menit pembekuan setelah 5x gagal

    /**
     * Menghitung key limiter akun+IP dan IP agar serangan spam email acak maupun target tunggal sama-sama terblokir.
     */
    private function throttleKeys(string $scope, Request $request): array
    {
        $emailNormalized = Str::transliterate(Str::lower(trim((string) $request->input('email', ''))));
        $ipKey = "auth-ip:{$scope}:" . $request->ip();
        $accountKey = $emailNormalized !== ''
            ? "auth-acct:{$scope}:" . $emailNormalized . '|' . $request->ip()
            : $ipKey;

        return [$accountKey, $ipKey];
    }

    /**
     * Mengecek sisa waktu tunggu (detik) jika IP atau akun sedang terkunci.
     */
    private function getLockoutRemaining(string $accountKey, string $ipKey): int
    {
        $rem = 0;
        if (RateLimiter::tooManyAttempts($accountKey, self::MAX_AUTH_ATTEMPTS)) {
            $rem = max($rem, RateLimiter::availableIn($accountKey));
        }
        if (RateLimiter::tooManyAttempts($ipKey, self::MAX_AUTH_ATTEMPTS)) {
            $rem = max($rem, RateLimiter::availableIn($ipKey));
        }
        return $rem;
    }

    /**
     * Mencatat kegagalan ke limiter dan mengembalikan sisa detik jika batas 5x telah tercapai.
     */
    private function recordAuthFailure(string $accountKey, string $ipKey): int
    {
        RateLimiter::hit($ipKey, self::LOCKOUT_SECONDS);
        if ($accountKey !== $ipKey) {
            RateLimiter::hit($accountKey, self::LOCKOUT_SECONDS);
        }

        return $this->getLockoutRemaining($accountKey, $ipKey);
    }

    private function formatWaitDuration(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;
        if ($minutes > 0 && $secs > 0) {
            return "{$minutes} menit {$secs} detik";
        }
        if ($minutes > 0) {
            return "{$minutes} menit";
        }
        return "{$secs} detik";
    }

    /**
     * Menampilkan form login utama sistem.
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        [, $ipKey] = $this->throttleKeys('login', $request);
        $lockoutRemaining = RateLimiter::tooManyAttempts($ipKey, self::MAX_AUTH_ATTEMPTS)
            ? RateLimiter::availableIn($ipKey)
            : (int) session('lockout_seconds', 0);

        return view('auth.login', compact('lockoutRemaining'));
    }

    /**
     * Memproses autentikasi form login tanpa memberi petunjuk field yang salah,
     * serta mengunci sementara selama 5 menit setelah 5 kali kegagalan.
     */
    public function login(Request $request)
    {
        [$accountKey, $ipKey] = $this->throttleKeys('login', $request);

        $lockedSeconds = $this->getLockoutRemaining($accountKey, $ipKey);
        if ($lockedSeconds > 0) {
            return back()
                ->withInput($request->only('email'))
                ->with('lockout_seconds', $lockedSeconds)
                ->withErrors([
                    'auth_error' => 'Terlalu banyak percobaan masuk yang gagal. Demi keamanan akun, silakan tunggu ' . $this->formatWaitDuration($lockedSeconds) . ' sebelum mencoba kembali.',
                ]);
        }

        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Jangan ungkapkan field mana yang salah (email kosong, tanpa '@', atau password kosong)
        if ($validator->fails()) {
            $nowLocked = $this->recordAuthFailure($accountKey, $ipKey);
            if ($nowLocked > 0) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('lockout_seconds', $nowLocked)
                    ->withErrors([
                        'auth_error' => 'Percobaan masuk gagal sebanyak 5 kali. Silakan tunggu ' . $this->formatWaitDuration($nowLocked) . ' sebelum mencoba kembali.',
                    ]);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'auth_error' => 'Kredensial masuk tidak valid. Periksa kembali data akun Anda.',
                ]);
        }

        if (Auth::attempt($validator->validated(), $request->boolean('remember'))) {
            RateLimiter::clear($accountKey);
            RateLimiter::clear($ipKey);
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'auth_error' => 'Akses akun Anda sedang dinonaktifkan oleh administrator. Silakan hubungi Dinas Pertanian Subang.',
                ]);
            }

            return $this->redirectBasedOnRole($user);
        }

        // Gagal autentikasi (email tidak ditemukan atau kata sandi salah): respons selalu identik
        $nowLocked = $this->recordAuthFailure($accountKey, $ipKey);
        if ($nowLocked > 0) {
            return back()
                ->withInput($request->only('email'))
                ->with('lockout_seconds', $nowLocked)
                ->withErrors([
                    'auth_error' => 'Percobaan masuk gagal sebanyak 5 kali. Silakan tunggu ' . $this->formatWaitDuration($nowLocked) . ' sebelum mencoba kembali.',
                ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'auth_error' => 'Kredensial masuk tidak valid. Periksa kembali data akun Anda.',
            ]);
    }

    /**
     * Menampilkan formulir pemulihan / lupa kata sandi akun.
     */
    public function showForgotPassword(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        [, $ipKey] = $this->throttleKeys('forgot', $request);
        $lockoutRemaining = RateLimiter::tooManyAttempts($ipKey, self::MAX_AUTH_ATTEMPTS)
            ? RateLimiter::availableIn($ipKey)
            : (int) session('lockout_seconds', 0);

        return view('auth.forgot-password', compact('lockoutRemaining'));
    }

    /**
     * Memproses pengaturan ulang kata sandi mandiri tanpa membocorkan status keberadaan akun
     * serta mengunci sementara 5 menit setelah 5 kali kegagalan.
     */
    public function updatePassword(Request $request)
    {
        [$accountKey, $ipKey] = $this->throttleKeys('forgot', $request);

        $lockedSeconds = $this->getLockoutRemaining($accountKey, $ipKey);
        if ($lockedSeconds > 0) {
            return back()
                ->withInput($request->only('email'))
                ->with('lockout_seconds', $lockedSeconds)
                ->withErrors([
                    'auth_error' => 'Terlalu banyak percobaan pemulihan akun yang gagal. Silakan tunggu ' . $this->formatWaitDuration($lockedSeconds) . ' sebelum mencoba kembali.',
                ]);
        }

        $emailInput = trim((string) $request->input('email', ''));
        $inputKey   = trim((string) $request->input('kode_legalitas', ''));

        $emailValid = filter_var($emailInput, FILTER_VALIDATE_EMAIL) !== false;
        $user = $emailValid
            ? User::with('kelompokTani')->where('email', $emailInput)->first()
            : null;

        $isAuthorized = false;

        if ($user && $inputKey !== '') {
            if ($user->isPoktan() && $user->kelompokTani) {
                $skPoktan    = trim((string) ($user->kelompokTani->nomor_badan_hukum ?? ''));
                $noHpPoktan  = preg_replace('/[^0-9]/', '', (string) ($user->kelompokTani->no_hp_ketua ?? ''));
                $cleanInput  = preg_replace('/[^0-9]/', '', $inputKey);

                $normHpPoktan = preg_replace('/^(?:62|0)/', '', preg_replace('/\D/', '', (string) $noHpPoktan));
                $normInputHp  = preg_replace('/^(?:62|0)/', '', preg_replace('/\D/', '', (string) $cleanInput));
                if ((!empty($skPoktan) && hash_equals(mb_strtolower(trim($skPoktan)), mb_strtolower($inputKey))) ||
                    (!empty($normHpPoktan) && strlen($normInputHp) >= 8 && hash_equals($normHpPoktan, $normInputHp))) {
                    $isAuthorized = true;
                }
            } elseif ($user->isAdmin() || $user->isKepBid()) {
                if (in_array(strtoupper($inputKey), ['DISTAN-SUBANG-2026', 'DISTAN-SUBANG', 'SUBANG2026'], true)) {
                    $isAuthorized = true;
                }
            }
        }

        // Jangan ungkap apakah email terdaftar, format email salah, atau kunci legalitas yang keliru
        if (!$user || !$isAuthorized) {
            $nowLocked = $this->recordAuthFailure($accountKey, $ipKey);
            if ($nowLocked > 0) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('lockout_seconds', $nowLocked)
                    ->withErrors([
                        'auth_error' => 'Verifikasi akun gagal sebanyak 5 kali. Silakan tunggu ' . $this->formatWaitDuration($nowLocked) . ' sebelum mencoba kembali.',
                    ]);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'auth_error' => 'Data verifikasi akun tidak cocok. Pastikan alamat email dan kunci otorisasi yang dimasukkan sudah benar.',
                ]);
        }

        // Setelah identitas terverifikasi sah, validasi kekuatan kata sandi baru
        $request->validate([
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'password_confirmation' => 'required',
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.letters'   => 'Kata sandi baru harus mengandung huruf.',
            'password.mixed'     => 'Kata sandi baru harus mengombinasikan huruf besar dan kecil.',
            'password.numbers'   => 'Kata sandi baru harus mengandung setidaknya satu angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        RateLimiter::clear($accountKey);
        RateLimiter::clear($ipKey);

        $user->password = Hash::make($request->password);
        $user->save();

        $namaPenerima = ($user->isPoktan() && $user->kelompokTani) ? $user->kelompokTani->nama_poktan : $user->name;

        return redirect()->route('login')
            ->with('success', "Identitas terverifikasi sah! Kata sandi untuk akun [{$namaPenerima}] ({$user->email}) berhasil diperbarui. Silakan masuk menggunakan kata sandi baru.");
    }

    /**
     * Menampilkan halaman registrasi mandiri untuk Kelompok Tani (Poktan).
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru Kelompok Tani.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'password_confirmation' => 'required',
            'nama_poktan'       => 'required|string|max:255',
            'nomor_badan_hukum' => 'nullable|string|max:100',
            'nama_ketua'        => 'required|string|max:255',
            'no_hp_ketua'       => 'required|string|max:20',
            'desa'              => 'required|string|max:100',
            'kecamatan'         => 'required|string|max:100',
            'alamat_lengkap'    => 'required|string|max:500',
            'luas_lahan_total'  => 'required|numeric|min:0.1',
            'jumlah_anggota'    => 'required|integer|min:1',
        ], [
            'name.required'              => 'Nama operator/pengelola wajib diisi.',
            'email.required'             => 'Alamat email aktif wajib diisi.',
            'email.unique'               => 'Alamat email ini sudah terdaftar dalam sistem.',
            'password.required'          => 'Kata sandi wajib diisi.',
            'password.min'               => 'Kata sandi minimal 8 karakter.',
            'password.letters'           => 'Kata sandi harus mengandung huruf.',
            'password.mixed'             => 'Kata sandi harus mengombinasikan huruf besar (kapital) dan kecil.',
            'password.numbers'           => 'Kata sandi harus mengandung setidaknya satu angka.',
            'password.confirmed'         => 'Konfirmasi kata sandi tidak cocok.',
            'nama_poktan.required'       => 'Nama kelompok tani wajib diisi.',
            'nama_ketua.required'        => 'Nama ketua kelompok tani wajib diisi.',
            'no_hp_ketua.required'       => 'Nomor kontak/WhatsApp ketua wajib diisi.',
            'desa.required'              => 'Desa/kelurahan wajib diisi.',
            'kecamatan.required'         => 'Kecamatan wajib dipilih.',
            'luas_lahan_total.required'  => 'Luas hamparan lahan wajib diisi.',
            'jumlah_anggota.required'    => 'Jumlah anggota tani aktif wajib diisi.',
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1. Buat User dengan role kelompok_tani
            $newUser = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => User::ROLE_POKTAN,
                'is_active' => true,
            ]);

            // 2. Buat profil KelompokTani terkait
            KelompokTani::create([
                'user_id'           => $newUser->id,
                'nama_poktan'       => $request->nama_poktan,
                'nomor_badan_hukum' => $request->nomor_badan_hukum,
                'nama_ketua'        => $request->nama_ketua,
                'no_hp_ketua'       => $request->no_hp_ketua,
                'desa'              => $request->desa,
                'kecamatan'         => $request->kecamatan,
                'alamat_lengkap'    => $request->alamat_lengkap,
                'luas_lahan_total'  => $request->luas_lahan_total,
                'jumlah_anggota'    => $request->jumlah_anggota,
            ]);

            return $newUser;
        });

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('poktan.dashboard')->with('success', 'Selamat datang! Pendaftaran Kelompok Tani ' . $request->nama_poktan . ' berhasil. Anda sekarang dapat mengajukan usulan bantuan Alsintan.');
    }

    /**
     * Fitur Quick Login (1-Klik untuk presentasi sidang / testing).
     */
    public function quickLogin(string $role)
    {
        abort_unless(
            app()->environment(['local', 'testing']) || config('app.debug'),
            403,
            'Fitur masuk cepat demo hanya tersedia pada lingkungan pengembangan.'
        );

        $user = match ($role) {
            'admin'  => User::where('role', User::ROLE_ADMIN)->where('is_active', true)->first(),
            'kepbid' => User::where('role', User::ROLE_KEPBID)->where('is_active', true)->first(),
            'poktan' => User::where('role', User::ROLE_POKTAN)->where('is_active', true)->first(),
            default  => null,
        };

        if (!$user) {
            return redirect()->route('login')->with('error', 'Akun demo aktif tidak ditemukan. Jalankan seeder database terlebih dahulu.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user)->with('success', 'Berhasil masuk sebagai: ' . $user->name);
    }

    /**
     * Logout pengguna dan hancurkan sesi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Helper untuk mengarahkan pengguna ke dashboard sesuai perannya.
     */
    private function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            User::ROLE_ADMIN  => redirect()->route('admin.dashboard'),
            User::ROLE_KEPBID => redirect()->route('kepbid.dashboard'),
            User::ROLE_POKTAN => redirect()->route('poktan.dashboard'),
            default           => redirect()->route('login'),
        };
    }
}
