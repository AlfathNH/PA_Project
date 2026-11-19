<?php

namespace Database\Seeders;

use App\Models\JenisAlsintan;
use App\Models\PeriodeBantuan;
use Illuminate\Database\Seeder;

/**
 * Seeder: JenisAlsintanSeeder
 * Master data kategori Alat dan Mesin Pertanian (Alsintan)
 * yang disediakan oleh Dinas Pertanian Kab. Subang.
 */
class JenisAlsintanSeeder extends Seeder
{
    public function run(): void
    {
        $periodeAktif = PeriodeBantuan::where('is_active', true)->first();

        $alsintans = [
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Traktor Roda 2 (Hand Tractor)',
                'kode_alsintan'      => 'TR2',
                'deskripsi'          => 'Alat pengolah tanah sawah ukuran kompak 8.5 HP untuk lahan sawah basah dan tegalan.',
                'satuan'             => 'unit',
                'kuota_unit'         => 15,
                'is_active'          => true,
            ],
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Traktor Roda 4 (Four-Wheel Tractor)',
                'kode_alsintan'      => 'TR4',
                'deskripsi'          => 'Traktor kapasitas besar 40-50 HP untuk olah tanah skala luas dan percepatan musim tanam.',
                'satuan'             => 'unit',
                'kuota_unit'         => 5,
                'is_active'          => true,
            ],
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Pompa Air Irigasi 3-4 Inch',
                'kode_alsintan'      => 'PMP',
                'deskripsi'          => 'Mesin pompa air diesel untuk antisipasi kekeringan dan pengairan lahan tadah hujan.',
                'satuan'             => 'unit',
                'kuota_unit'         => 30,
                'is_active'          => true,
            ],
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Power Sprayer (Alat Semprot Bertekanan)',
                'kode_alsintan'      => 'SPR',
                'deskripsi'          => 'Alat penyemprot hama terpadu bermesin untuk pengendalian Organisme Pengganggu Tumbuhan (OPT).',
                'satuan'             => 'unit',
                'kuota_unit'         => 20,
                'is_active'          => true,
            ],
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Rice Transplanter (Mesin Tanam Padi)',
                'kode_alsintan'      => 'RCT',
                'deskripsi'          => 'Mesin penanam bibit padi modern sistem jajar legowo untuk efisiensi tenaga kerja.',
                'satuan'             => 'unit',
                'kuota_unit'         => 8,
                'is_active'          => true,
            ],
            [
                'periode_bantuan_id' => $periodeAktif?->id,
                'nama_alsintan'      => 'Combine Harvester (Mesin Panen Padi)',
                'kode_alsintan'      => 'CBH',
                'deskripsi'          => 'Mesin pemanen gabah terpadu (potong, rontok, kemas) untuk menekan losses panen di bawah 3%.',
                'satuan'             => 'unit',
                'kuota_unit'         => 5,
                'is_active'          => true,
            ],
        ];

        foreach ($alsintans as $item) {
            JenisAlsintan::updateOrCreate(['kode_alsintan' => $item['kode_alsintan']], $item);
        }
    }
}

