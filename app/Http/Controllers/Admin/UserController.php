<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KelompokTani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Controller: Admin UserController
 * Modul Manajemen Akun Pengguna (Kelompok Tani, Admin Verifikator, & Pejabat Dinas).
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh akun pengguna dengan filter & pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role   = $request->query('role');
        $status = $request->query('status');

        $query = User::with('kelompokTani')->latest();

        // Filter Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('kelompokTani', function ($poktanQ) use ($search) {
                      $poktanQ->where('nama_poktan', 'like', "%{$search}%")
                              ->orWhere('desa', 'like', "%{$search}%")
                              ->orWhere('kecamatan', 'like', "%{$search}%")
                              ->orWhere('nama_ketua', 'like', "%{$search}%")
                              ->orWhere('no_hp_ketua', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Role
        if ($role && in_array($role, [User::ROLE_POKTAN, User::ROLE_ADMIN, User::ROLE_KEPBID])) {
            $query->where('role', $role);
        }

        // Filter Status Keaktifan
        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $users = $query->paginate(10)->withQueryString();

        // Statistik Ringkas Akun
        $stats = [
            'total_users'     => User::count(),
            'total_poktan'    => User::where('role', User::ROLE_POKTAN)->count(),
            'total_dinas'     => User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_KEPBID])->count(),
            'total_nonaktif'  => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search', 'role', 'status'));
    }

    /**
     * Membuat akun Pegawai Dinas baru (Admin Verifikator atau Kepala Bidang) secara aman dari panel Admin.
     */
    public function store(Request $request)
    {
        if ($request->filled('email')) {
            $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_KEPBID,
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'name.required'      => 'Nama lengkap dan gelar pegawai wajib diisi.',
            'email.required'     => 'Alamat email resmi pegawai wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'email.unique'       => 'Alamat email ini sudah terdaftar di portal.',
            'role.required'      => 'Peran otoritas pegawai wajib dipilih.',
            'role.in'            => 'Peran yang diizinkan hanya Admin Verifikator atau Kepala Bidang.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.letters'   => 'Kata sandi harus mengandung huruf.',
            'password.mixed'     => 'Kata sandi harus mengombinasikan huruf besar dan kecil.',
            'password.numbers'   => 'Kata sandi harus mengandung angka.',
        ]);

        $user = User::create([
            'name'      => trim($request->name),
            'email'     => strtolower(trim($request->email)),
            'role'      => $request->role,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $labelPeran = $user->isAdmin() ? 'Admin Verifikator' : 'Kepala Bidang';

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$labelPeran} baru [{$user->name}] ({$user->email}) berhasil dibuat dan langsung aktif.");
    }

    /**
     * Mengaktifkan atau menonaktifkan status akun (Toggle is_active).
     */
    public function toggleStatus(User $user)
    {
        // Cegah Admin menonaktifkan akunnya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang masuk.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusLabel = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan (dibekukan)';
        return back()->with('success', "Status akun [{$user->name}] ({$user->email}) berhasil {$statusLabel}.");
    }

    /**
     * Mengatur ulang (reset) kata sandi akun pengguna oleh Admin Dinas.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'password.min'     => 'Kata sandi baru minimal 8 karakter.',
            'password.letters' => 'Kata sandi baru harus mengandung huruf.',
            'password.mixed'   => 'Kata sandi baru harus mengombinasikan huruf kapital dan kecil.',
            'password.numbers' => 'Kata sandi baru harus mengandung angka.',
        ]);

        $newPassword = $request->input('password') ?: 'Subang2026!';

        $user->password = Hash::make($newPassword);
        $user->save();

        return back()->with('success', "Kata sandi akun [{$user->name}] ({$user->email}) berhasil direset menjadi standar: '{$newPassword}'. Harap infokan ke pemilik akun untuk menggantinya saat masuk.");
    }

    /**
     * Memperbarui profil akun pengguna & kelompok tani.
     */
    public function update(Request $request, User $user)
    {
        if ($request->filled('luas_lahan_total')) {
            $request->merge([
                'luas_lahan_total' => str_replace(',', '.', (string) $request->input('luas_lahan_total')),
            ]);
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'is_active'        => 'nullable|boolean',
            'nama_ketua'       => 'nullable|string|max:120',
            'no_hp_ketua'      => 'nullable|string|max:25',
            'desa'             => 'nullable|string|max:100',
            'kecamatan'        => 'nullable|string|max:100',
            'luas_lahan_total' => 'nullable|numeric|min:0.01|max:10000',
            'jumlah_anggota'   => 'nullable|integer|min:1|max:5000',
        ], [
            'name.required'            => 'Nama pengguna wajib diisi.',
            'email.required'           => 'Alamat email pengguna wajib diisi.',
            'email.unique'             => 'Alamat email ini sudah digunakan oleh akun lain.',
            'luas_lahan_total.numeric' => 'Luas lahan total harus berupa angka.',
            'luas_lahan_total.min'     => 'Luas lahan total minimal 0,01 hektar.',
            'jumlah_anggota.integer'   => 'Jumlah anggota harus berupa bilangan bulat.',
            'jumlah_anggota.min'       => 'Jumlah anggota minimal 1 orang.',
        ]);

        if ($user->id === Auth::id() && $request->has('is_active') && !$request->boolean('is_active')) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang masuk.');
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $user->is_active,
        ]);

        // Jika akun memiliki profil Kelompok Tani
        if ($user->isPoktan() && $user->kelompokTani) {
            $user->kelompokTani->update([
                'nama_ketua'       => $request->filled('nama_ketua') ? $request->nama_ketua : $user->kelompokTani->nama_ketua,
                'no_hp_ketua'      => $request->filled('no_hp_ketua') ? $request->no_hp_ketua : $user->kelompokTani->no_hp_ketua,
                'desa'             => $request->filled('desa') ? $request->desa : $user->kelompokTani->desa,
                'kecamatan'        => $request->filled('kecamatan') ? $request->kecamatan : $user->kelompokTani->kecamatan,
                'luas_lahan_total' => $request->filled('luas_lahan_total') ? $request->luas_lahan_total : $user->kelompokTani->luas_lahan_total,
                'jumlah_anggota'   => $request->filled('jumlah_anggota') ? $request->jumlah_anggota : $user->kelompokTani->jumlah_anggota,
            ]);
        }

        return back()->with('success', "Data profil akun [{$user->name}] berhasil diperbarui.");
    }
}

