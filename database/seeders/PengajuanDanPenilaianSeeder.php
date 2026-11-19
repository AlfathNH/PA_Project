<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JenisAlsintan;
use App\Models\Kriteria;
use App\Models\PengajuanBantuan;
use App\Models\PenilaianTopsis;
use App\Models\BerkasPengajuan;
use App\Models\RiwayatStatusPengajuan;
use Illuminate\Database\Seeder;

/**
 * Seeder: PengajuanDanPenilaianSeeder
 * Menyiapkan contoh pengajuan bantuan Alsintan (kategori Traktor Roda 2)
 * beserta berkas persyaratan dan skor penilaian per kriteria.
 *
 * Berguna untuk demonstrasi instan saat pengujian sistem dan sidang TA!
 */
class PengajuanDanPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $tr2   = JenisAlsintan::where('kode_alsintan', 'TR2')->first();

        $c1 = Kriteria::where('kode_kriteria', 'C1')->first();
        $c2 = Kriteria::where('kode_kriteria', 'C2')->first();
        $c3 = Kriteria::where('kode_kriteria', 'C3')->first();
        $c4 = Kriteria::where('kode_kriteria', 'C4')->first();
        $c5 = Kriteria::where('kode_kriteria', 'C5')->first();

        // 4 Poktan pengaju sampel
        $samples = [
            [
                'email'   => 'poktan.srimukti@subang.go.id',
                'nomor'   => 'PB-2026-001',
                'alasan'  => 'Percepatan pengolahan tanah seluas 38.5 Ha sebelum awal musim penghujan di Desa Gunungsari.',
                'nilai'   => [
                    $c1->id => 38.5, // Luas Lahan (Ha)
                    $c2->id => 6.8,  // Produktivitas (Ton/Ha)
                    $c3->id => 1.0,  // Alsintan Eksisting (Cost)
                    $c4->id => 45.0, // Jumlah Anggota
                    $c5->id => 4.5,  // Kesiapan Shelter & Operator
                ],
            ],
            [
                'email'   => 'poktan.mekarsari@subang.go.id',
                'nomor'   => 'PB-2026-002',
                'alasan'  => 'Poktan belum memiliki hand tractor mandiri, sering terkendala sewa alat dari luar desa.',
                'nilai'   => [
                    $c1->id => 29.0,
                    $c2->id => 6.2,
                    $c3->id => 0.0, // Belum punya alat sama sekali (sangat menguntungkan di kriteria Cost)
                    $c4->id => 35.0,
                    $c5->id => 4.0,
                ],
            ],
            [
                'email'   => 'poktan.tunasjaya@subang.go.id',
                'nomor'   => 'PB-2026-003',
                'alasan'  => 'Lahan hamparan luas 45 Ha dengan intensitas tanam IP300 memerlukan tambahan unit olah tanah.',
                'nilai'   => [
                    $c1->id => 45.0,
                    $c2->id => 7.1,
                    $c3->id => 2.0, // Sudah punya 2 unit (skor cost lebih besar = kurang diprioritaskan)
                    $c4->id => 52.0,
                    $c5->id => 5.0,
                ],
            ],
            [
                'email'   => 'poktan.suburmakmur@subang.go.id',
                'nomor'   => 'PB-2026-004',
                'alasan'  => 'Mendukung intensifikasi padi sawah tadah hujan di perbatasan Purwadadi.',
                'nilai'   => [
                    $c1->id => 22.0,
                    $c2->id => 5.5,
                    $c3->id => 0.0,
                    $c4->id => 28.0,
                    $c5->id => 3.5,
                ],
            ],
        ];

        foreach ($samples as $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) continue;

            $pengajuan = PengajuanBantuan::updateOrCreate(
                ['nomor_pengajuan' => $data['nomor']],
                [
                    'user_id'                   => $user->id,
                    'jenis_alsintan_id'         => $tr2->id,
                    'tahun_pengajuan'           => 2026,
                    'jumlah_diminta'            => 1,
                    'jumlah_alsintan_eksisting' => (int) ($data['nilai'][$c3->id] ?? 0),
                    'alasan_pengajuan'          => $data['alasan'],
                    'status'                    => PengajuanBantuan::STATUS_DINILAI, // Siap dihitung TOPSIS
                    'expired_at'                => now()->addDays(90),
                    'verified_by'               => $admin->id,
                    'verified_at'               => now()->subDays(2),
                    'catatan_admin'             => 'Berkas persyaratan lengkap, proposal dan legalitas SK sah terverifikasi.',
                ]
            );

            // Berkas sampel dummy
            BerkasPengajuan::updateOrCreate(
                [
                    'pengajuan_bantuan_id' => $pengajuan->id,
                    'jenis_berkas'         => 'surat_permohonan',
                ],
                [
                    'nama_berkas'         => 'Surat Permohonan Bantuan Alsintan',
                    'file_path'           => 'pengajuan/' . $pengajuan->id . '/surat_permohonan.pdf',
                    'file_name_original'  => 'Surat_Permohonan_' . str_replace(' ', '_', $user->kelompokTani?->nama_poktan ?? 'poktan') . '.pdf',
                    'file_mime_type'      => 'application/pdf',
                    'file_size'           => 1420500, // ~1.4 MB
                ]
            );

            // Simpan skor penilaian per kriteria (matriks x_ij)
            foreach ($data['nilai'] as $kriteriaId => $nilaiAngka) {
                PenilaianTopsis::updateOrCreate(
                    [
                        'pengajuan_bantuan_id' => $pengajuan->id,
                        'kriteria_id'          => $kriteriaId,
                    ],
                    [
                        'nilai' => $nilaiAngka,
                    ]
                );
            }

            // Catat log audit trail kronologis (Tabel ke-9)
            RiwayatStatusPengajuan::updateOrCreate(
                [
                    'pengajuan_bantuan_id' => $pengajuan->id,
                    'status_baru'          => PengajuanBantuan::STATUS_DIAJUKAN,
                ],
                [
                    'user_id'         => $user->id,
                    'status_lama'     => null,
                    'catatan'         => 'Permohonan bantuan Alsintan diajukan secara mandiri oleh Poktan.',
                    'waktu_perubahan' => now()->subDays(3),
                ]
            );

            RiwayatStatusPengajuan::updateOrCreate(
                [
                    'pengajuan_bantuan_id' => $pengajuan->id,
                    'status_baru'          => PengajuanBantuan::STATUS_DINILAI,
                ],
                [
                    'user_id'         => $admin->id,
                    'status_lama'     => PengajuanBantuan::STATUS_DIAJUKAN,
                    'catatan'         => 'Berkas persyaratan diverifikasi sah dan penilaian 5 kriteria CPCL telah diinput.',
                    'waktu_perubahan' => now()->subDays(2),
                ]
            );
        }
    }
}

