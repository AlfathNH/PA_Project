<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

/**
 * Seeder: KriteriaSeeder
 * Kriteria SPK TOPSIS untuk menentukan prioritas kelayakan Poktan.
 *
 * Syarat matematis TOPSIS:
 * - Total bobot w_j = 1.0000 (100%)
 * - Tipe:
 *   - 'benefit' : Nilai makin tinggi makin layak (A+ = max, A- = min)
 *   - 'cost'    : Nilai makin tinggi makin tidak layak (A+ = min, A- = max)
 */
class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $kriterias = [
            [
                'kode_kriteria' => 'C1',
                'nama_kriteria' => 'Luas Lahan Hamparan Poktan (Ha)',
                'deskripsi'     => 'Luas total lahan garapan efektif yang dikelola oleh anggota Poktan.',
                'bobot'         => 0.3000,
                'tipe'          => 'benefit',
                'urutan'        => 1,
                'is_active'     => true,
            ],
            [
                'kode_kriteria' => 'C2',
                'nama_kriteria' => 'Produktivitas Panen Rata-rata (Ton/Ha)',
                'deskripsi'     => 'Tingkat produktivitas padi/komoditas pada 2 musim tanam terakhir.',
                'bobot'         => 0.2500,
                'tipe'          => 'benefit',
                'urutan'        => 2,
                'is_active'     => true,
            ],
            [
                'kode_kriteria' => 'C3',
                'nama_kriteria' => 'Jumlah Alsintan Sejenis Eksisting (Unit)',
                'deskripsi'     => 'Jumlah alat serupa yang sudah dimiliki oleh Poktan (faktor pemerataan bantuan).',
                'bobot'         => 0.2000,
                'tipe'          => 'cost', // Kriteria Cost: semakin banyak alat lama, prioritas semakin kecil
                'urutan'        => 3,
                'is_active'     => true,
            ],
            [
                'kode_kriteria' => 'C4',
                'nama_kriteria' => 'Jumlah Anggota Poktan Aktif (Orang)',
                'deskripsi'     => 'Banyaknya petani aktif penerima manfaat operasional alsintan di kelompok.',
                'bobot'         => 0.1500,
                'tipe'          => 'benefit',
                'urutan'        => 4,
                'is_active'     => true,
            ],
            [
                'kode_kriteria' => 'C5',
                'nama_kriteria' => 'Kesiapan Operator & Tempat Penyimpanan (Skor 1-5)',
                'deskripsi'     => 'Kesiapan shelter/garasi dan tenaga operator terlatih untuk merawat alat.',
                'bobot'         => 0.1000,
                'tipe'          => 'benefit',
                'urutan'        => 5,
                'is_active'     => true,
            ],
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::updateOrCreate(['kode_kriteria' => $kriteria['kode_kriteria']], $kriteria);
        }
    }
}

