<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KelompokTani;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: UserSeeder
 * Membuat akun untuk 3 Role Aktor Sistem:
 * 1. Admin Verifikator Dinas
 * 2. Kepala Bidang (Approver Final)
 * 3. Kelompok Tani (Pengaju Alsintan)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $kecPagaden   = Kecamatan::where('nama_kecamatan', 'Pagaden')->first();
        $kecBinong    = Kecamatan::where('nama_kecamatan', 'Binong')->first();
        $kecCiasem    = Kecamatan::where('nama_kecamatan', 'Ciasem')->first();
        $kecPurwadadi = Kecamatan::where('nama_kecamatan', 'Purwadadi')->first();

        // 1. Akun Admin / Verifikator Dinas Pertanian
        User::create([
            'name'      => 'Ahmad Fathoni, S.P. (Admin Alsintan)',
            'email'     => 'admin@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        // 2. Akun Kepala Bidang Sarana & Prasarana Pertanian (Pimpinan)
        User::create([
            'name'      => 'Ir. H. Dedi Kusnadi, M.M. (Kepala Bidang)',
            'email'     => 'kepbid@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_KEPBID,
            'is_active' => true,
        ]);

        // 3. Akun Kelompok Tani 1: Poktan Sri Mukti (Pagaden)
        $poktan1 = User::create([
            'name'      => 'H. Endang Sujana (Ketua Poktan Sri Mukti)',
            'email'     => 'poktan.srimukti@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);
        KelompokTani::create([
            'user_id'           => $poktan1->id,
            'kecamatan_id'      => $kecPagaden?->id,
            'nama_poktan'       => 'Poktan Sri Mukti',
            'nomor_badan_hukum' => '521.1/SK-102/DISTAN/2021',
            'nama_ketua'        => 'H. Endang Sujana',
            'no_hp_ketua'       => '081223344551',
            'desa'              => 'Gunungsari',
            'kecamatan'         => 'Pagaden',
            'alamat_lengkap'    => 'Dusun Mekarjaya RT 04 RW 02, Desa Gunungsari, Kec. Pagaden',
            'luas_lahan_total'  => 38.5,
            'jumlah_anggota'    => 45,
        ]);

        // 4. Akun Kelompok Tani 2: Poktan Mekar Sari (Binong)
        $poktan2 = User::create([
            'name'      => 'Kusnadi Permana (Ketua Poktan Mekar Sari)',
            'email'     => 'poktan.mekarsari@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);
        KelompokTani::create([
            'user_id'           => $poktan2->id,
            'kecamatan_id'      => $kecBinong?->id,
            'nama_poktan'       => 'Poktan Mekar Sari',
            'nomor_badan_hukum' => '521.1/SK-145/DISTAN/2020',
            'nama_ketua'        => 'Kusnadi Permana',
            'no_hp_ketua'       => '081334455662',
            'desa'              => 'Cicadas',
            'kecamatan'         => 'Binong',
            'alamat_lengkap'    => 'Blok Karang Tengah RT 02 RW 01, Desa Cicadas, Kec. Binong',
            'luas_lahan_total'  => 29.0,
            'jumlah_anggota'    => 35,
        ]);

        // 5. Akun Kelompok Tani 3: Poktan Tunas Jaya (Ciasem)
        $poktan3 = User::create([
            'name'      => 'Asep Sunandar (Ketua Poktan Tunas Jaya)',
            'email'     => 'poktan.tunasjaya@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);
        KelompokTani::create([
            'user_id'           => $poktan3->id,
            'kecamatan_id'      => $kecCiasem?->id,
            'nama_poktan'       => 'Poktan Tunas Jaya',
            'nomor_badan_hukum' => '521.1/SK-089/DISTAN/2022',
            'nama_ketua'        => 'Asep Sunandar',
            'no_hp_ketua'       => '081556677883',
            'desa'              => 'Ciasem Hilir',
            'kecamatan'         => 'Ciasem',
            'alamat_lengkap'    => 'Kampung Sukajaya RT 08 RW 03, Ciasem Hilir',
            'luas_lahan_total'  => 45.0,
            'jumlah_anggota'    => 52,
        ]);

        // 6. Akun Kelompok Tani 4: Poktan Subur Makmur (Purwadadi)
        $poktan4 = User::create([
            'name'      => 'Dede Rahmat (Ketua Poktan Subur Makmur)',
            'email'     => 'poktan.suburmakmur@subang.go.id',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);
        KelompokTani::create([
            'user_id'           => $poktan4->id,
            'kecamatan_id'      => $kecPurwadadi?->id,
            'nama_poktan'       => 'Poktan Subur Makmur',
            'nomor_badan_hukum' => '521.1/SK-210/DISTAN/2023',
            'nama_ketua'        => 'Dede Rahmat',
            'no_hp_ketua'       => '081778899004',
            'desa'              => 'Pasirbungur',
            'kecamatan'         => 'Purwadadi',
            'alamat_lengkap'    => 'Jl. Perkebunan Sawit RT 03 RW 01, Desa Pasirbungur',
            'luas_lahan_total'  => 22.0,
            'jumlah_anggota'    => 28,
        ]);
    }
}

