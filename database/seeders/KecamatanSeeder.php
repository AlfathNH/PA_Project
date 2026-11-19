<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

/**
 * Seeder: KecamatanSeeder (Tabel ke-7)
 * Memuat master data 30 Kecamatan di Kabupaten Subang
 * dikelompokkan ke dalam 3 Zona Pertanian Unggulan Pemkab Subang:
 * 1. Pantura (Sentra Padi Irigasi Teknis & Pompanisasi)
 * 2. Tengah (Lumbung Padi & Palawija)
 * 3. Selatan (Dataran Tinggi Hortikultura & Perkebunan)
 */
class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            // Zona Pantura (7 Kecamatan)
            ['nama_kecamatan' => 'Pamanukan',     'kode_kecamatan' => 'KEC-PMN', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Sentra lumbung padi pesisir utara dan pompanisasi sungai Cipunagara.'],
            ['nama_kecamatan' => 'Ciasem',        'kode_kecamatan' => 'KEC-CSM', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Wilayah persawahan irigasi teknis Tarum Timur dan tambak.'],
            ['nama_kecamatan' => 'Pusakanagara',  'kode_kecamatan' => 'KEC-PKN', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Pusat mekanisasi combine harvester dan kawasan penyangga pelabuhan Patimban.'],
            ['nama_kecamatan' => 'Pusakajaya',    'kode_kecamatan' => 'KEC-PKJ', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Hamparan sawah tadah hujan dan percontohan mekanisasi transplanter.'],
            ['nama_kecamatan' => 'Blanakan',      'kode_kecamatan' => 'KEC-BLK', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Pesisir utara dengan integrasi mina padi dan pompanisasi muara.'],
            ['nama_kecamatan' => 'Sukasari',      'kode_kecamatan' => 'KEC-SKS', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Kawasan persawahan dataran rendah dan pengendali banjir pesisir.'],
            ['nama_kecamatan' => 'Legonkulon',    'kode_kecamatan' => 'KEC-LGK', 'zona_wilayah' => 'Pantura', 'deskripsi_wilayah' => 'Pertanian pesisir dengan tantangan intrusi air asin dan kebutuhan alsintan tangguh.'],

            // Zona Tengah (15 Kecamatan)
            ['nama_kecamatan' => 'Subang',         'kode_kecamatan' => 'KEC-SBG', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Ibukota kabupaten, pusat administrasi Dinas Pertanian, dan kebun percobaan.'],
            ['nama_kecamatan' => 'Pagaden',        'kode_kecamatan' => 'KEC-PGD', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Lumbung padi utama Subang tengah dengan produktivitas tinggi.'],
            ['nama_kecamatan' => 'Pagaden Barat',  'kode_kecamatan' => 'KEC-PGB', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Sentra benih padi bersertifikat dan alsintan traktor roda 4.'],
            ['nama_kecamatan' => 'Purwadadi',      'kode_kecamatan' => 'KEC-PWD', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Wilayah pertanian tanaman pangan dan perkebunan terpadu.'],
            ['nama_kecamatan' => 'Kalijati',       'kode_kecamatan' => 'KEC-KLT', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Kawasan agribisnis buah nanas Si Madu dan palawija.'],
            ['nama_kecamatan' => 'Dawuan',         'kode_kecamatan' => 'KEC-DWN', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Pertanian lahan sawah irigasi setengah teknis.'],
            ['nama_kecamatan' => 'Binong',         'kode_kecamatan' => 'KEC-BNG', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Sentra padi produktif dan konsentrasi kelompok tani pengusul alsintan.'],
            ['nama_kecamatan' => 'Tambakdahan',    'kode_kecamatan' => 'KEC-TBD', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Pertanian padi terhampar luas berbatasan dengan zona pantura.'],
            ['nama_kecamatan' => 'Compreng',       'kode_kecamatan' => 'KEC-CMP', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Kawasan lumbung padi dengan intensitas tanam IP 300.'],
            ['nama_kecamatan' => 'Cikaum',         'kode_kecamatan' => 'KEC-CKM', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Pertanian pangan terintegrasi saluran irigasi sekunder.'],
            ['nama_kecamatan' => 'Patokbeusi',     'kode_kecamatan' => 'KEC-PTB', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Sentra padi jalur pantura-tengah dengan gapoktan mandiri.'],
            ['nama_kecamatan' => 'Cibogo',         'kode_kecamatan' => 'KEC-CBG', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Kawasan penyangga waduk Sadawarna dan irigasi terpadu.'],
            ['nama_kecamatan' => 'Cipunagara',     'kode_kecamatan' => 'KEC-CPN', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Pertanian bantaran sungai Cipunagara dan tegalan palawija.'],
            ['nama_kecamatan' => 'Cipeundeuy',     'kode_kecamatan' => 'KEC-CPD', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Pertanian hortikultura dan pengembangan lahan kering terpadu.'],
            ['nama_kecamatan' => 'Cijambe',        'kode_kecamatan' => 'KEC-CJM', 'zona_wilayah' => 'Tengah', 'deskripsi_wilayah' => 'Kawasan perbukitan transisi, padi gogo, dan perkebunan buah.'],

            // Zona Selatan (8 Kecamatan)
            ['nama_kecamatan' => 'Jalancagak',     'kode_kecamatan' => 'KEC-JLC', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Pusat agrowisata nanas, teh, dan sayuran dataran tinggi.'],
            ['nama_kecamatan' => 'Ciater',         'kode_kecamatan' => 'KEC-CTR', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Kawasan perkebunan teh, hortikultura, dan konservasi tanah.'],
            ['nama_kecamatan' => 'Sagalaherang',   'kode_kecamatan' => 'KEC-SGH', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Pertanian terasering sawah pegunungan dan palawija.'],
            ['nama_kecamatan' => 'Serangpanjang',  'kode_kecamatan' => 'KEC-SPJ', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Pertanian hortikultura cabai, tomat, dan sayuran daun.'],
            ['nama_kecamatan' => 'Kasomalang',     'kode_kecamatan' => 'KEC-KSM', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Sentra mata air, padi sawah pegunungan, dan kolam air deras.'],
            ['nama_kecamatan' => 'Cisalak',        'kode_kecamatan' => 'KEC-CSL', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Lembah sungai Cipunagara hulu dan perkebunan cengkeh/kopi.'],
            ['nama_kecamatan' => 'Tanjungsiang',   'kode_kecamatan' => 'KEC-TJS', 'zona_wilayah' => 'Selatan', 'deskripsi_wilayah' => 'Padi sawah organik dataran tinggi dan hortikultura buah.'],
        ];

        foreach ($kecamatans as $item) {
            Kecamatan::updateOrCreate(
                ['kode_kecamatan' => $item['kode_kecamatan']],
                $item
            );
        }
    }
}

