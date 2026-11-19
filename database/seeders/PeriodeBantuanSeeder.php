<?php

namespace Database\Seeders;

use App\Models\PeriodeBantuan;
use Illuminate\Database\Seeder;

/**
 * Seeder: PeriodeBantuanSeeder (Tabel ke-8)
 * Memuat master data periode tahun anggaran penyaluran Alsintan.
 */
class PeriodeBantuanSeeder extends Seeder
{
    public function run(): void
    {
        $periodes = [
            [
                'nama_periode'     => 'Program Penyaluran Alsintan APBD Murni TA 2026',
                'tahun_anggaran'   => 2026,
                'tanggal_buka'     => '2026-01-02',
                'tanggal_tutup'    => '2026-11-30',
                'kuota_total_unit' => 150,
                'is_active'        => true,
                'keterangan'       => 'Alokasi utama pengadaan alsintan mendukung swasembada pangan berkelanjutan Kabupaten Subang TA 2026.',
            ],
            [
                'nama_periode'     => 'Program Darurat Pompanisasi & Antisipasi El Nino TA 2025',
                'tahun_anggaran'   => 2025,
                'tanggal_buka'     => '2025-02-01',
                'tanggal_tutup'    => '2025-10-31',
                'kuota_total_unit' => 80,
                'is_active'        => false,
                'keterangan'       => 'Periode anggaran khusus pompanisasi dan mitigasi kekeringan tahun lalu (arsip selesai).',
            ],
        ];

        foreach ($periodes as $item) {
            PeriodeBantuan::updateOrCreate(
                [
                    'nama_periode'   => $item['nama_periode'],
                    'tahun_anggaran' => $item['tahun_anggaran'],
                ],
                $item
            );
        }
    }
}

