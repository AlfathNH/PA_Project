<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KelompokTani;
use App\Models\Kecamatan;
use App\Models\JenisAlsintan;
use App\Models\Kriteria;
use App\Models\PengajuanBantuan;
use App\Models\PenilaianTopsis;
use App\Models\RiwayatStatusPengajuan;
use App\Models\HasilTopsis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: StressTestSeeder
 * 
 * Menghasilkan 1000 data pengajuan bantuan Kelompok Tani secara massal
 * untuk pengujian beban (stress testing) algoritma SPK TOPSIS.
 *
 * Menggunakan chunking dan mass-insert (DB::table()->insert()) untuk
 * menjamin efisiensi memori (anti-OOM) dan eksekusi secepat kilat (<3 detik).
 *
 * Akun Admin dan Kepala Bidang tetap terlindungi dan tidak dihapus.
 */
class StressTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Memulai StressTestSeeder: Menyiapkan 1000 data pengajuan...');

        // 1. Pastikan akun Admin tersedia sebagai verifikator
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::create([
                'name'      => 'Ahmad Fathoni, S.P. (Admin Alsintan)',
                'email'     => 'admin@subang.go.id',
                'password'  => Hash::make('password'),
                'role'      => User::ROLE_ADMIN,
                'is_active' => true,
            ]);
        }

        // 2. Pastikan jenis alsintan target tersedia (Traktor Roda 2 / TR2)
        $alsintan = JenisAlsintan::where('kode_alsintan', 'TR2')->first() 
            ?? JenisAlsintan::where('is_active', true)->first();

        if (!$alsintan) {
            $alsintan = JenisAlsintan::create([
                'kode_alsintan' => 'TR2',
                'nama_alsintan' => 'Traktor Roda Dua (Hand Tractor)',
                'satuan'        => 'Unit',
                'kuota_unit'    => 200,
                'deskripsi'     => 'Traktor tangan pengolah tanah sawah untuk poktan.',
                'is_active'     => true,
            ]);
        }

        // 3. Ambil 5 kriteria aktif TOPSIS (C1 s/d C5)
        $c1 = Kriteria::where('kode_kriteria', 'C1')->first();
        $c2 = Kriteria::where('kode_kriteria', 'C2')->first();
        $c3 = Kriteria::where('kode_kriteria', 'C3')->first();
        $c4 = Kriteria::where('kode_kriteria', 'C4')->first();
        $c5 = Kriteria::where('kode_kriteria', 'C5')->first();

        if (!$c1 || !$c2 || !$c3 || !$c4 || !$c5) {
            $this->command?->error('Kriteria C1-C5 belum lengkap. Jalankan KriteriaSeeder terlebih dahulu.');
            return;
        }

        // 4. Ambil seluruh data Kecamatan di Subang
        $kecamatans = Kecamatan::all();
        if ($kecamatans->isEmpty()) {
            $this->command?->info('Master kecamatan kosong, menjalankan KecamatanSeeder...');
            $this->call(KecamatanSeeder::class);
            $kecamatans = Kecamatan::all();
        }
        $kecamatanArray = $kecamatans->values()->all();
        $totalKecamatan = count($kecamatanArray);

        // 5. Kosakata realistis Poktan Subang untuk variasi data
        $daftarNamaDepan = [
            'H. Endang', 'H. Ahmad', 'H. Dedi', 'H. Maman', 'H. Tatang',
            'Pak Ujang', 'Pak Asep', 'Pak Cecep', 'Pak Dadang', 'Pak Encep',
            'Haji Rohmat', 'Pak Wawan', 'Pak Sukarna', 'Pak Suryana', 'Pak Juhana',
            'H. Nanang', 'Pak Iwan', 'Pak Yayan', 'Pak Kuswara', 'Pak Suherman'
        ];
        $daftarNamaBelakang = [
            'Sujana', 'Kusnadi', 'Hidayat', 'Mulyana', 'Sutisna',
            'Permana', 'Gunawan', 'Kurniawan', 'Subagja', 'Wiguna',
            'Rustandi', 'Somantri', 'Kosasih', 'Ginanjar', 'Purnomo'
        ];
        $daftarPoktan = [
            'Sri Mukti', 'Mekar Sari', 'Tunas Jaya', 'Subur Makmur', 'Bina Karya',
            'Tani Sejahtera', 'Harapan Tani', 'Mitra Usaha', 'Sinar Pagi', 'Karya Mandiri',
            'Maju Bersama', 'Sumber Rejeki', 'Lestari Tani', 'Sukamaju', 'Margamukti',
            'Sugih Mukti', 'Tani Mandiri', 'Rukun Tani', 'Panca Usaha', 'Agraria Makmur'
        ];
        $daftarDesa = [
            'Gunungsari', 'Pangsor', 'Sukakarya', 'Mulyasari', 'Kertajaya',
            'Sukamukti', 'Jayamulya', 'Tanjungrasa', 'Pasirmuncang', 'Bojong',
            'Ciberes', 'Karanganyar', 'Wanakerta', 'Cilamaya', 'Cipunagara',
            'Gembor', 'Cikadu', 'Kumpay', 'Cibeusi', 'Nagrak'
        ];

        // 6. Pre-hash password satu kali saja untuk menghindari bottleneck CPU bcrypt
        $hashedPassword = Hash::make('password');
        $now = now();

        // 7. Bersihkan data stress test lama secara aman (idempotent, tanpa menyentuh data existing/admin/kepbid)
        $existingStressUsers = User::where('email', 'like', 'poktan.stress%@subang.go.id')
            ->orWhere('email', 'like', 'poktan.stress%@gmail.com')
            ->pluck('id');

        if ($existingStressUsers->isNotEmpty()) {
            $this->command?->info("Membersihkan {$existingStressUsers->count()} data stress test sebelumnya...");
            $stressPengajuans = PengajuanBantuan::whereIn('user_id', $existingStressUsers)->pluck('id');
            if ($stressPengajuans->isNotEmpty()) {
                HasilTopsis::whereIn('pengajuan_bantuan_id', $stressPengajuans)->delete();
                PenilaianTopsis::whereIn('pengajuan_bantuan_id', $stressPengajuans)->delete();
                RiwayatStatusPengajuan::whereIn('pengajuan_bantuan_id', $stressPengajuans)->delete();
                PengajuanBantuan::whereIn('id', $stressPengajuans)->delete();
            }
            KelompokTani::whereIn('user_id', $existingStressUsers)->delete();
            User::whereIn('id', $existingStressUsers)->delete();
        }

        // 8. Generate 1000 data menggunakan chunking (200 data per batch)
        $totalData  = 1000;
        $chunkSize  = 200;
        $totalChunks = (int) ceil($totalData / $chunkSize);

        for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
            $startIndex = ($chunk * $chunkSize) + 1;
            $endIndex   = min(($chunk + 1) * $chunkSize, $totalData);

            $usersToInsert        = [];
            $poktanEmailsInChunk  = [];
            $generatorData        = [];

            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $padIndex    = str_pad((string)$i, 4, '0', STR_PAD_LEFT);
                $email       = "poktan.stress{$padIndex}@subang.go.id";
                $namaDepan   = $daftarNamaDepan[($i * 3) % count($daftarNamaDepan)];
                $namaBlkg    = $daftarNamaBelakang[($i * 7) % count($daftarNamaBelakang)];
                $namaKetua   = "{$namaDepan} {$namaBlkg}";
                $namaPoktan  = $daftarPoktan[($i * 5) % count($daftarPoktan)] . " {$i}";
                $kecObj      = $kecamatanArray[$i % $totalKecamatan];
                $desa        = $daftarDesa[($i * 2) % count($daftarDesa)];

                // Nilai 5 Kriteria (C1-C5) realistis untuk simulasi TOPSIS
                $luasLahan = (float) number_format(15.0 + (($i * 1.37) % 50.0), 2, '.', ''); // C1: 15.0 - 65.0 Ha
                $produktivitas = (float) number_format(5.2 + (($i * 0.17) % 3.2), 2, '.', ''); // C2: 5.2 - 8.4 Ton/Ha
                $alsintanEksisting = (int) ($i % 4); // C3: 0, 1, 2, atau 3 unit (Cost)
                $jumlahAnggota = (int) (20 + (($i * 7) % 55)); // C4: 20 - 74 Anggota
                $kesiapanOperator = (float) number_format(2.5 + (($i * 0.13) % 2.5), 1, '.', ''); // C5: 2.5 - 5.0 Skor

                $generatorData[$email] = [
                    'index'             => $i,
                    'pad_index'         => $padIndex,
                    'nama_ketua'        => $namaKetua,
                    'nama_poktan'       => $namaPoktan,
                    'kecamatan_id'      => $kecObj->id,
                    'kecamatan'         => $kecObj->nama_kecamatan,
                    'desa'              => $desa,
                    'luas_lahan'        => $luasLahan,
                    'produktivitas'     => $produktivitas,
                    'alsintan_eksisting'=> $alsintanEksisting,
                    'jumlah_anggota'    => $jumlahAnggota,
                    'kesiapan_operator' => $kesiapanOperator,
                ];

                $poktanEmailsInChunk[] = $email;
                $usersToInsert[] = [
                    'name'       => "{$namaKetua} ({$namaPoktan})",
                    'email'      => $email,
                    'password'   => $hashedPassword,
                    'role'       => User::ROLE_POKTAN,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Mass Insert Users
            DB::table('users')->insert($usersToInsert);

            // Ambil ID User yang baru saja diinsert
            $usersMap = DB::table('users')
                ->whereIn('email', $poktanEmailsInChunk)
                ->pluck('id', 'email');

            $poktansToInsert    = [];
            $pengajuansToInsert = [];
            $nomorsInChunk      = [];

            foreach ($generatorData as $email => $data) {
                $userId = $usersMap[$email] ?? null;
                if (!$userId) continue;

                $nomorPengajuan = "PB-2026-STRESS-{$data['pad_index']}";
                $nomorsInChunk[] = $nomorPengajuan;

                $poktansToInsert[] = [
                    'user_id'           => $userId,
                    'kecamatan_id'      => $data['kecamatan_id'],
                    'nama_poktan'       => $data['nama_poktan'],
                    'nomor_badan_hukum' => "521.1/SK-STRESS-{$data['pad_index']}/DISTAN/2026",
                    'nama_ketua'        => $data['nama_ketua'],
                    'no_hp_ketua'       => '08' . str_pad((string)(122330000 + $data['index']), 10, '0', STR_PAD_RIGHT),
                    'desa'              => $data['desa'],
                    'kecamatan'         => $data['kecamatan'],
                    'alamat_lengkap'    => "Jl. Usaha Tani Blok " . (($data['index'] % 10) + 1) . ", Desa {$data['desa']}, Kec. {$data['kecamatan']}",
                    'luas_lahan_total'  => $data['luas_lahan'],
                    'jumlah_anggota'    => $data['jumlah_anggota'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                $pengajuansToInsert[] = [
                    'user_id'                   => $userId,
                    'jenis_alsintan_id'         => $alsintan->id,
                    'nomor_pengajuan'           => $nomorPengajuan,
                    'tahun_pengajuan'           => 2026,
                    'jumlah_diminta'            => ($data['index'] % 2 === 0) ? 2 : 1,
                    'jumlah_alsintan_eksisting' => $data['alsintan_eksisting'],
                    'alasan_pengajuan'          => "Kebutuhan peremajaan dan percepatan olah tanah hamparan seluas {$data['luas_lahan']} Ha di Desa {$data['desa']}, Kec. {$data['kecamatan']}.",
                    'status'                    => PengajuanBantuan::STATUS_DINILAI, // Siap masuk kalkulasi TOPSIS
                    'expired_at'                => $now->copy()->addDays(90),
                    'verified_by'               => $admin->id,
                    'verified_at'               => $now->copy()->subDays(2),
                    'catatan_admin'             => 'Proposal dan kelengkapan administrasi sah terverifikasi verifikator.',
                    'created_at'                => $now,
                    'updated_at'                => $now,
                ];
            }

            // Mass Insert KelompokTani & PengajuanBantuan
            DB::table('kelompok_tanis')->insert($poktansToInsert);
            DB::table('pengajuan_bantuans')->insert($pengajuansToInsert);

            // Ambil ID Pengajuan yang baru diinsert
            $pengajuansMap = DB::table('pengajuan_bantuans')
                ->whereIn('nomor_pengajuan', $nomorsInChunk)
                ->pluck('id', 'nomor_pengajuan');

            $penilaiansToInsert = [];
            $riwayatsToInsert   = [];

            foreach ($generatorData as $email => $data) {
                $nomor = "PB-2026-STRESS-{$data['pad_index']}";
                $pengajuanId = $pengajuansMap[$nomor] ?? null;
                $userId      = $usersMap[$email] ?? null;
                if (!$pengajuanId || !$userId) continue;

                // Skor 5 Kriteria (C1-C5)
                $penilaiansToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'kriteria_id'          => $c1->id,
                    'nilai'                => $data['luas_lahan'],
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $penilaiansToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'kriteria_id'          => $c2->id,
                    'nilai'                => $data['produktivitas'],
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $penilaiansToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'kriteria_id'          => $c3->id,
                    'nilai'                => $data['alsintan_eksisting'],
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $penilaiansToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'kriteria_id'          => $c4->id,
                    'nilai'                => $data['jumlah_anggota'],
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $penilaiansToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'kriteria_id'          => $c5->id,
                    'nilai'                => $data['kesiapan_operator'],
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];

                // Audit Trail log: Diajukan & Dinilai
                $riwayatsToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'user_id'              => $userId,
                    'status_lama'          => null,
                    'status_baru'          => PengajuanBantuan::STATUS_DIAJUKAN,
                    'catatan'              => 'Permohonan bantuan Alsintan diajukan secara mandiri oleh Poktan.',
                    'waktu_perubahan'      => $now->copy()->subDays(3),
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $riwayatsToInsert[] = [
                    'pengajuan_bantuan_id' => $pengajuanId,
                    'user_id'              => $admin->id,
                    'status_lama'          => PengajuanBantuan::STATUS_DIAJUKAN,
                    'status_baru'          => PengajuanBantuan::STATUS_DINILAI,
                    'catatan'              => 'Berkas persyaratan diverifikasi sah dan penilaian 5 kriteria CPCL telah diinput.',
                    'waktu_perubahan'      => $now->copy()->subDays(2),
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
            }

            // Mass Insert Penilaian (1000 baris per chunk) & Riwayat (400 baris per chunk)
            DB::table('penilaian_topsis')->insert($penilaiansToInsert);
            DB::table('riwayat_status_pengajuans')->insert($riwayatsToInsert);

            $this->command?->info("Batch " . ($chunk + 1) . "/{$totalChunks} selesai ({$endIndex}/{$totalData} data ter-generate).");
        }

        $this->command?->info("StressTestSeeder SUKSES: 1000 data pengajuan berstatus 'dinilai' siap dihitung TOPSIS.");
    }
}
