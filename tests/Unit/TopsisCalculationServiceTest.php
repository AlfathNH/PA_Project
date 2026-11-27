<?php

namespace Tests\Unit;

use App\Models\HasilTopsis;
use App\Models\JenisAlsintan;
use App\Models\Kriteria;
use App\Models\PengajuanBantuan;
use App\Models\PenilaianTopsis;
use App\Models\RiwayatStatusPengajuan;
use App\Models\User;
use App\Services\TopsisCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class TopsisCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TopsisCalculationService $service;

    private User $admin;

    private JenisAlsintan $alsintan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TopsisCalculationService();
        $this->admin = User::factory()->create([
            'name'      => 'Admin Verifikator',
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
        $this->alsintan = JenisAlsintan::create([
            'nama_alsintan' => 'Traktor Roda 2',
            'kode_alsintan' => 'TR2',
            'deskripsi'     => 'Hand tractor pengolahan sawah',
            'satuan'        => 'unit',
            'kuota_unit'    => 10,
            'is_active'     => true,
        ]);
    }

    /**
     * Membuat 5 kriteria standar sesuai KriteriaSeeder dan Bab IV (4 benefit + 1 cost, total bobot = 1.0).
     *
     * @return array<string, Kriteria>
     */
    private function seedKriteriaStandar(): array
    {
        $definisi = [
            ['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan (Ha)', 'bobot' => 0.30, 'tipe' => 'benefit', 'urutan' => 1],
            ['kode_kriteria' => 'C2', 'nama_kriteria' => 'Produktivitas (Ton/Ha)', 'bobot' => 0.25, 'tipe' => 'benefit', 'urutan' => 2],
            ['kode_kriteria' => 'C3', 'nama_kriteria' => 'Alsintan Eksisting (Unit)', 'bobot' => 0.20, 'tipe' => 'cost', 'urutan' => 3],
            ['kode_kriteria' => 'C4', 'nama_kriteria' => 'Jumlah Anggota Aktif', 'bobot' => 0.15, 'tipe' => 'benefit', 'urutan' => 4],
            ['kode_kriteria' => 'C5', 'nama_kriteria' => 'Kesiapan Operator & Shelter', 'bobot' => 0.10, 'tipe' => 'benefit', 'urutan' => 5],
        ];

        $kriterias = [];
        foreach ($definisi as $item) {
            $kriterias[$item['kode_kriteria']] = Kriteria::create([
                'kode_kriteria' => $item['kode_kriteria'],
                'nama_kriteria' => $item['nama_kriteria'],
                'bobot'         => $item['bobot'],
                'tipe'          => $item['tipe'],
                'urutan'        => $item['urutan'],
                'is_active'     => true,
            ]);
        }

        return $kriterias;
    }

    /**
     * Membuat pengajuan bantuan beserta skor penilaian TOPSIS per kriteria.
     *
     * @param array<int, float> $skorPerKriteriaId
     */
    private function buatPengajuanDenganNilai(
        string $nomor,
        array $skorPerKriteriaId,
        string $status = PengajuanBantuan::STATUS_DINILAI,
        ?JenisAlsintan $alsintan = null
    ): PengajuanBantuan {
        $poktan = User::factory()->create([
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);

        $pengajuan = PengajuanBantuan::create([
            'user_id'           => $poktan->id,
            'jenis_alsintan_id' => ($alsintan ?? $this->alsintan)->id,
            'nomor_pengajuan'   => $nomor,
            'tahun_pengajuan'   => 2026,
            'jumlah_diminta'    => 1,
            'alasan_pengajuan'  => 'Pengujian akurasi perhitungan TOPSIS.',
            'status'            => $status,
            'verified_by'       => $this->admin->id,
            'verified_at'       => now(),
        ]);

        foreach ($skorPerKriteriaId as $kriteriaId => $nilai) {
            PenilaianTopsis::create([
                'pengajuan_bantuan_id' => $pengajuan->id,
                'kriteria_id'          => $kriteriaId,
                'nilai'                => $nilai,
            ]);
        }

        return $pengajuan;
    }

    /**
     * Referensi independen 6 langkah TOPSIS tanpa pembulatan dini di Langkah 1 sampai 5
     * sesuai spesifikasi TOPSIS_FORMULA_SPEC.md untuk memverifikasi keluaran service.
     *
     * @param array<int, array<int, float>> $matriksX
     * @param array<int, array{bobot: float, tipe: string}> $kriteriaMeta
     * @return array<string, mixed>
     */
    private function hitungReferensiTopsis(array $matriksX, array $kriteriaMeta): array
    {
        $matriksR = [];
        $matriksY = [];
        $idealPositif = [];
        $idealNegatif = [];

        foreach ($kriteriaMeta as $kriteriaId => $meta) {
            $sumSquares = 0.0;
            foreach ($matriksX as $row) {
                $sumSquares += ($row[$kriteriaId] ** 2);
            }
            $norm = sqrt($sumSquares);

            $kolomY = [];
            foreach ($matriksX as $pengajuanId => $row) {
                $r = $norm > 0.0 ? $row[$kriteriaId] / $norm : 0.0;
                $y = $meta['bobot'] * $r;
                $matriksR[$pengajuanId][$kriteriaId] = $r;
                $matriksY[$pengajuanId][$kriteriaId] = $y;
                $kolomY[] = $y;
            }

            if ($meta['tipe'] === 'benefit') {
                $idealPositif[$kriteriaId] = max($kolomY);
                $idealNegatif[$kriteriaId] = min($kolomY);
            } else {
                $idealPositif[$kriteriaId] = min($kolomY);
                $idealNegatif[$kriteriaId] = max($kolomY);
            }
        }

        $dPlus = [];
        $dMinus = [];
        $vi = [];

        foreach ($matriksY as $pengajuanId => $rowY) {
            $sumPos = 0.0;
            $sumNeg = 0.0;
            foreach ($rowY as $kriteriaId => $yij) {
                $sumPos += (($yij - $idealPositif[$kriteriaId]) ** 2);
                $sumNeg += (($yij - $idealNegatif[$kriteriaId]) ** 2);
            }
            $dPlus[$pengajuanId] = sqrt($sumPos);
            $dMinus[$pengajuanId] = sqrt($sumNeg);
            $penyebut = $dPlus[$pengajuanId] + $dMinus[$pengajuanId];
            $vi[$pengajuanId] = $penyebut > 0.0 ? $dMinus[$pengajuanId] / $penyebut : 0.0;
        }

        arsort($vi);

        $ranking = [];
        $rank = 1;
        foreach ($vi as $pengajuanId => $score) {
            $ranking[] = [
                'pengajuan_id'     => $pengajuanId,
                'nilai_preferensi' => round($score, 10),
                'ranking'          => $rank++,
                'jarak_positif'    => round($dPlus[$pengajuanId], 10),
                'jarak_negatif'    => round($dMinus[$pengajuanId], 10),
            ];
        }

        return [
            'matriks_r'     => $matriksR,
            'matriks_y'     => $matriksY,
            'ideal_positif' => $idealPositif,
            'ideal_negatif' => $idealNegatif,
            'ranking'       => $ranking,
        ];
    }

    public function test_akurasi_6_langkah_topsis_sesuai_dataset_bab_iv_4_poktan_5_kriteria(): void
    {
        $k = $this->seedKriteriaStandar();

        $p1 = $this->buatPengajuanDenganNilai('PB-2026-001', [
            $k['C1']->id => 38.5,
            $k['C2']->id => 6.8,
            $k['C3']->id => 1.0,
            $k['C4']->id => 45.0,
            $k['C5']->id => 4.5,
        ]);
        $p2 = $this->buatPengajuanDenganNilai('PB-2026-002', [
            $k['C1']->id => 29.0,
            $k['C2']->id => 6.2,
            $k['C3']->id => 0.0,
            $k['C4']->id => 35.0,
            $k['C5']->id => 4.0,
        ]);
        $p3 = $this->buatPengajuanDenganNilai('PB-2026-003', [
            $k['C1']->id => 45.0,
            $k['C2']->id => 7.1,
            $k['C3']->id => 2.0,
            $k['C4']->id => 52.0,
            $k['C5']->id => 5.0,
        ]);
        $p4 = $this->buatPengajuanDenganNilai('PB-2026-004', [
            $k['C1']->id => 22.0,
            $k['C2']->id => 5.5,
            $k['C3']->id => 0.0,
            $k['C4']->id => 28.0,
            $k['C5']->id => 3.5,
        ]);

        $matriksInput = [
            $p1->id => [$k['C1']->id => 38.5, $k['C2']->id => 6.8, $k['C3']->id => 1.0, $k['C4']->id => 45.0, $k['C5']->id => 4.5],
            $p2->id => [$k['C1']->id => 29.0, $k['C2']->id => 6.2, $k['C3']->id => 0.0, $k['C4']->id => 35.0, $k['C5']->id => 4.0],
            $p3->id => [$k['C1']->id => 45.0, $k['C2']->id => 7.1, $k['C3']->id => 2.0, $k['C4']->id => 52.0, $k['C5']->id => 5.0],
            $p4->id => [$k['C1']->id => 22.0, $k['C2']->id => 5.5, $k['C3']->id => 0.0, $k['C4']->id => 28.0, $k['C5']->id => 3.5],
        ];
        $kriteriaMeta = [];
        foreach ($k as $kriteria) {
            $kriteriaMeta[$kriteria->id] = ['bobot' => (float) $kriteria->bobot, 'tipe' => $kriteria->tipe];
        }
        $referensi = $this->hitungReferensiTopsis($matriksInput, $kriteriaMeta);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertTrue(Str::isUuid($hasil['batch_id']));

        // Langkah 1: Matriks Keputusan X harus identik dengan skor input
        foreach ($matriksInput as $pengajuanId => $row) {
            foreach ($row as $kriteriaId => $nilai) {
                $this->assertEqualsWithDelta($nilai, $hasil['matriks']['keputusan'][$pengajuanId][$kriteriaId], 1e-12);
            }
        }

        // Langkah 2: Normalisasi Euclidean R (tiap kolom non-nol memiliki jumlah kuadrat = 1.0)
        foreach ($kriteriaMeta as $kriteriaId => $meta) {
            $sumKuadratKolom = 0.0;
            foreach ($matriksInput as $pengajuanId => $row) {
                $aktualR = $hasil['matriks']['normal'][$pengajuanId][$kriteriaId];
                $this->assertEqualsWithDelta($referensi['matriks_r'][$pengajuanId][$kriteriaId], $aktualR, 1e-12);
                $sumKuadratKolom += ($aktualR ** 2);
            }
            $this->assertEqualsWithDelta(1.0, $sumKuadratKolom, 1e-12);
        }

        // Langkah 3: Normalisasi Matriks Terbobot Y = w_j * r_ij
        foreach ($matriksInput as $pengajuanId => $row) {
            foreach ($kriteriaMeta as $kriteriaId => $meta) {
                $this->assertEqualsWithDelta(
                    $referensi['matriks_y'][$pengajuanId][$kriteriaId],
                    $hasil['matriks']['terbobot'][$pengajuanId][$kriteriaId],
                    1e-12
                );
            }
        }

        // Langkah 4: Solusi Ideal Positif (A+) dan Negatif (A-) untuk benefit & cost
        foreach ($kriteriaMeta as $kriteriaId => $meta) {
            $this->assertEqualsWithDelta($referensi['ideal_positif'][$kriteriaId], $hasil['ideal_positif'][$kriteriaId], 1e-12);
            $this->assertEqualsWithDelta($referensi['ideal_negatif'][$kriteriaId], $hasil['ideal_negatif'][$kriteriaId], 1e-12);
        }
        // Kriteria C3 (cost): A+ harus 0.0 (nilai terkecil), A- harus lebih besar dari A+
        $this->assertEqualsWithDelta(0.0, $hasil['ideal_positif'][$k['C3']->id], 1e-12);
        $this->assertGreaterThan($hasil['ideal_positif'][$k['C3']->id], $hasil['ideal_negatif'][$k['C3']->id]);

        // Langkah 5 & 6: Jarak D+, D-, Nilai Preferensi Vi (10 desimal), dan urutan ranking descending
        $this->assertCount(4, $hasil['ranking']);
        $previousVi = 1.1;
        foreach ($referensi['ranking'] as $idx => $expectedItem) {
            $actualItem = $hasil['ranking'][$idx];
            $this->assertSame($expectedItem['pengajuan_id'], $actualItem['pengajuan_id']);
            $this->assertSame($expectedItem['ranking'], $actualItem['ranking']);
            $this->assertEqualsWithDelta($expectedItem['jarak_positif'], $actualItem['jarak_positif'], 1e-10);
            $this->assertEqualsWithDelta($expectedItem['jarak_negatif'], $actualItem['jarak_negatif'], 1e-10);
            $this->assertEqualsWithDelta($expectedItem['nilai_preferensi'], $actualItem['nilai_preferensi'], 1e-10);
            $this->assertGreaterThanOrEqual(0.0, $actualItem['nilai_preferensi']);
            $this->assertLessThanOrEqual(1.0, $actualItem['nilai_preferensi']);
            $this->assertLessThanOrEqual($previousVi, $actualItem['nilai_preferensi']);
            $previousVi = $actualItem['nilai_preferensi'];
        }

        // Verifikasi persistensi hasil_topsis, pembaruan status ke 'dirangking', dan audit trail
        foreach ($hasil['ranking'] as $item) {
            $this->assertDatabaseHas('hasil_topsis', [
                'pengajuan_bantuan_id' => $item['pengajuan_id'],
                'batch_kalkulasi_id'   => $hasil['batch_id'],
                'ranking'              => $item['ranking'],
                'dihitung_oleh'        => $this->admin->id,
            ]);

            $tersimpan = HasilTopsis::where('pengajuan_bantuan_id', $item['pengajuan_id'])->firstOrFail();
            $this->assertEqualsWithDelta($item['nilai_preferensi'], $tersimpan->nilai_preferensi, 1e-10);
            $this->assertEqualsWithDelta($item['jarak_positif'], $tersimpan->jarak_ideal_positif, 1e-10);
            $this->assertEqualsWithDelta($item['jarak_negatif'], $tersimpan->jarak_ideal_negatif, 1e-10);

            $this->assertSame(
                PengajuanBantuan::STATUS_DIRANGKING,
                PengajuanBantuan::findOrFail($item['pengajuan_id'])->status
            );

            $this->assertTrue(
                RiwayatStatusPengajuan::where('pengajuan_bantuan_id', $item['pengajuan_id'])
                    ->where('status_baru', PengajuanBantuan::STATUS_DIRANGKING)
                    ->exists()
            );
        }
    }

    public function test_akurasi_analitik_6_langkah_topsis_kriteria_benefit_dan_cost(): void
    {
        // Dataset contoh pada TOPSIS_FORMULA_SPEC.md: 3 alternatif, 4 kriteria (3 benefit + 1 cost)
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 0.30, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $c2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Produktivitas', 'bobot' => 0.25, 'tipe' => 'benefit', 'urutan' => 2, 'is_active' => true]);
        $c3 = Kriteria::create(['kode_kriteria' => 'C3', 'nama_kriteria' => 'Kepemilikan Alat', 'bobot' => 0.20, 'tipe' => 'cost', 'urutan' => 3, 'is_active' => true]);
        $c4 = Kriteria::create(['kode_kriteria' => 'C4', 'nama_kriteria' => 'Jumlah Anggota', 'bobot' => 0.25, 'tipe' => 'benefit', 'urutan' => 4, 'is_active' => true]);

        $a1 = $this->buatPengajuanDenganNilai('SPEC-001', [$c1->id => 5, $c2->id => 4, $c3->id => 2, $c4->id => 30]);
        $a2 = $this->buatPengajuanDenganNilai('SPEC-002', [$c1->id => 3, $c2->id => 5, $c3->id => 3, $c4->id => 25]);
        $a3 = $this->buatPengajuanDenganNilai('SPEC-003', [$c1->id => 4, $c2->id => 3, $c3->id => 1, $c4->id => 40]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        // Verifikasi nilai analitik langsung: pembagi Euclidean kolom C1=sqrt(50), C2=sqrt(50), C3=sqrt(14), C4=sqrt(3125)
        $this->assertEqualsWithDelta(5 / sqrt(50), $hasil['matriks']['normal'][$a1->id][$c1->id], 1e-12);
        $this->assertEqualsWithDelta(1 / sqrt(14), $hasil['matriks']['normal'][$a3->id][$c3->id], 1e-12);
        $this->assertEqualsWithDelta(40 / sqrt(3125), $hasil['matriks']['normal'][$a3->id][$c4->id], 1e-12);

        // Verifikasi solusi ideal A+ dan A- secara eksak
        $this->assertEqualsWithDelta(0.30 * (5 / sqrt(50)), $hasil['ideal_positif'][$c1->id], 1e-12);
        $this->assertEqualsWithDelta(0.30 * (3 / sqrt(50)), $hasil['ideal_negatif'][$c1->id], 1e-12);
        $this->assertEqualsWithDelta(0.20 * (1 / sqrt(14)), $hasil['ideal_positif'][$c3->id], 1e-12);
        $this->assertEqualsWithDelta(0.20 * (3 / sqrt(14)), $hasil['ideal_negatif'][$c3->id], 1e-12);

        // A3 unggul di C3 (cost terendah) dan C4 (anggota terbanyak) sehingga menduduki peringkat 1; A2 peringkat 3
        $urutanId = array_column($hasil['ranking'], 'pengajuan_id');
        $this->assertSame([$a3->id, $a1->id, $a2->id], $urutanId);
    }

    public function test_solusi_ideal_ekstrem_menghasilkan_preferensi_satu_dan_nol(): void
    {
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 0.60, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $c2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Alat Eksisting', 'bobot' => 0.40, 'tipe' => 'cost', 'urutan' => 2, 'is_active' => true]);

        // Dominan mutlak di semua kriteria (benefit lebih besar, cost lebih kecil)
        $terbaik = $this->buatPengajuanDenganNilai('EXT-001', [$c1->id => 100.0, $c2->id => 1.0]);
        $terburuk = $this->buatPengajuanDenganNilai('EXT-002', [$c1->id => 10.0, $c2->id => 5.0]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertSame($terbaik->id, $hasil['ranking'][0]['pengajuan_id']);
        $this->assertSame(1, $hasil['ranking'][0]['ranking']);
        $this->assertEqualsWithDelta(0.0, $hasil['ranking'][0]['jarak_positif'], 1e-10);
        $this->assertEqualsWithDelta(1.0, $hasil['ranking'][0]['nilai_preferensi'], 1e-10);

        $this->assertSame($terburuk->id, $hasil['ranking'][1]['pengajuan_id']);
        $this->assertSame(2, $hasil['ranking'][1]['ranking']);
        $this->assertEqualsWithDelta(0.0, $hasil['ranking'][1]['jarak_negatif'], 1e-10);
        $this->assertEqualsWithDelta(0.0, $hasil['ranking'][1]['nilai_preferensi'], 1e-10);
    }

    public function test_guard_pembagian_nol_saat_kolom_kriteria_bernilai_nol_semua(): void
    {
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 0.70, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $c2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Alat Eksisting', 'bobot' => 0.30, 'tipe' => 'cost', 'urutan' => 2, 'is_active' => true]);

        // Semua Poktan belum punya alat (C2 = 0.0) -> pembagi Euclidean kolom C2 adalah 0
        $p1 = $this->buatPengajuanDenganNilai('ZERO-COL-1', [$c1->id => 50.0, $c2->id => 0.0]);
        $p2 = $this->buatPengajuanDenganNilai('ZERO-COL-2', [$c1->id => 20.0, $c2->id => 0.0]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertSame(0.0, $hasil['matriks']['normal'][$p1->id][$c2->id]);
        $this->assertSame(0.0, $hasil['matriks']['normal'][$p2->id][$c2->id]);
        $this->assertSame($p1->id, $hasil['ranking'][0]['pengajuan_id']);
        $this->assertEqualsWithDelta(1.0, $hasil['ranking'][0]['nilai_preferensi'], 1e-10);
        $this->assertEqualsWithDelta(0.0, $hasil['ranking'][1]['nilai_preferensi'], 1e-10);
    }

    public function test_guard_pembagian_nol_saat_semua_alternatif_bernilai_identik(): void
    {
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 0.50, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $c2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Alat Eksisting', 'bobot' => 0.50, 'tipe' => 'cost', 'urutan' => 2, 'is_active' => true]);

        // Nilai identik -> A+ == A- -> D+ = 0 dan D- = 0 -> guard penyebut == 0 menghasilkan Vi = 0.0
        $this->buatPengajuanDenganNilai('IDENTIK-1', [$c1->id => 25.0, $c2->id => 2.0]);
        $this->buatPengajuanDenganNilai('IDENTIK-2', [$c1->id => 25.0, $c2->id => 2.0]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertCount(2, $hasil['ranking']);
        foreach ($hasil['ranking'] as $item) {
            $this->assertFalse(is_nan($item['nilai_preferensi']));
            $this->assertFalse(is_infinite($item['nilai_preferensi']));
            $this->assertEqualsWithDelta(0.0, $item['jarak_positif'], 1e-10);
            $this->assertEqualsWithDelta(0.0, $item['jarak_negatif'], 1e-10);
            $this->assertEqualsWithDelta(0.0, $item['nilai_preferensi'], 1e-10);
        }
    }

    public function test_kriteria_nonaktif_diabaikan_dan_penilaian_kosong_default_nol(): void
    {
        $aktif1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 0.60, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $aktif2 = Kriteria::create(['kode_kriteria' => 'C2', 'nama_kriteria' => 'Produktivitas', 'bobot' => 0.40, 'tipe' => 'benefit', 'urutan' => 2, 'is_active' => true]);
        $nonaktif = Kriteria::create(['kode_kriteria' => 'C9', 'nama_kriteria' => 'Kriteria Lama', 'bobot' => 0.50, 'tipe' => 'cost', 'urutan' => 9, 'is_active' => false]);

        // p1 lengkap, p2 sengaja belum memiliki skor C2 (harus diperlakukan sebagai 0.0)
        $p1 = $this->buatPengajuanDenganNilai('PARTIAL-1', [$aktif1->id => 40.0, $aktif2->id => 7.0, $nonaktif->id => 99.0]);
        $p2 = $this->buatPengajuanDenganNilai('PARTIAL-2', [$aktif1->id => 40.0, $nonaktif->id => 1.0]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertArrayNotHasKey($nonaktif->id, $hasil['matriks']['keputusan'][$p1->id]);
        $this->assertArrayNotHasKey($nonaktif->id, $hasil['ideal_positif']);
        $this->assertSame(0.0, $hasil['matriks']['keputusan'][$p2->id][$aktif2->id]);
        $this->assertSame($p1->id, $hasil['ranking'][0]['pengajuan_id']);
    }

    public function test_pengajuan_draft_ditolak_tanpa_penilaian_dan_alsintan_lain_dikecualikan(): void
    {
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 1.00, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);
        $alsintanLain = JenisAlsintan::create([
            'nama_alsintan' => 'Pompa Air',
            'kode_alsintan' => 'POMPA',
            'satuan'        => 'unit',
            'kuota_unit'    => 5,
            'is_active'     => true,
        ]);

        $valid1 = $this->buatPengajuanDenganNilai('VALID-1', [$c1->id => 40.0], PengajuanBantuan::STATUS_DINILAI);
        $valid2 = $this->buatPengajuanDenganNilai('VALID-2', [$c1->id => 20.0], PengajuanBantuan::STATUS_DIVERIFIKASI);
        $draft = $this->buatPengajuanDenganNilai('EXCL-DRAFT', [$c1->id => 999.0], PengajuanBantuan::STATUS_DRAFT);
        $ditolak = $this->buatPengajuanDenganNilai('EXCL-REJECT', [$c1->id => 999.0], PengajuanBantuan::STATUS_DITOLAK_ADMIN);
        $tanpaNilai = $this->buatPengajuanDenganNilai('EXCL-NOSCORE', [], PengajuanBantuan::STATUS_DIVERIFIKASI);
        $kategoriLain = $this->buatPengajuanDenganNilai('EXCL-OTHER', [$c1->id => 999.0], PengajuanBantuan::STATUS_DINILAI, $alsintanLain);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $rankedIds = array_column($hasil['ranking'], 'pengajuan_id');
        $this->assertSame([$valid1->id, $valid2->id], $rankedIds);

        // Pengajuan yang dikecualikan tidak boleh memiliki hasil_topsis ataupun berubah status
        foreach ([$draft, $ditolak, $tanpaNilai, $kategoriLain] as $excluded) {
            $this->assertDatabaseMissing('hasil_topsis', ['pengajuan_bantuan_id' => $excluded->id]);
        }
        $this->assertSame(PengajuanBantuan::STATUS_DRAFT, $draft->fresh()->status);
        $this->assertSame(PengajuanBantuan::STATUS_DITOLAK_ADMIN, $ditolak->fresh()->status);
        $this->assertSame(PengajuanBantuan::STATUS_DIVERIFIKASI, $tanpaNilai->fresh()->status);
        $this->assertSame(PengajuanBantuan::STATUS_DINILAI, $kategoriLain->fresh()->status);
    }

    public function test_melempar_exception_jika_tidak_ada_kriteria_aktif(): void
    {
        Kriteria::create([
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Nonaktif',
            'bobot'         => 1.00,
            'tipe'          => 'benefit',
            'urutan'        => 1,
            'is_active'     => false,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tidak ada kriteria aktif');

        $this->service->hitung($this->alsintan->id, $this->admin->id);
    }

    public function test_calon_tunggal_otomatis_ranking_satu_dengan_preferensi_penuh(): void
    {
        $c1 = Kriteria::create([
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Luas Lahan',
            'bobot'         => 1.00,
            'tipe'          => 'benefit',
            'urutan'        => 1,
            'is_active'     => true,
        ]);

        // Hanya 1 alternatif valid yang dinilai (Aturan Adaptif Calon Tunggal)
        $p1 = $this->buatPengajuanDenganNilai('ONLY-ONE', [$c1->id => 35.0]);

        $hasil = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertCount(1, $hasil['ranking']);
        $this->assertSame($p1->id, $hasil['ranking'][0]['pengajuan_id']);
        $this->assertSame(1, $hasil['ranking'][0]['ranking']);
        $this->assertEqualsWithDelta(1.0, $hasil['ranking'][0]['nilai_preferensi'], 1e-6);
    }

    public function test_melempar_exception_jika_alternatif_kosong(): void
    {
        Kriteria::create([
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Luas Lahan',
            'bobot'         => 1.00,
            'tipe'          => 'benefit',
            'urutan'        => 1,
            'is_active'     => true,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Belum ada alternatif (Kelompok Tani) yang siap dinilai');

        $this->service->hitung($this->alsintan->id, $this->admin->id);
    }

    public function test_hitung_ulang_memperbarui_hasil_tanpa_duplikasi_dan_menghasilkan_batch_baru(): void
    {
        $c1 = Kriteria::create(['kode_kriteria' => 'C1', 'nama_kriteria' => 'Luas Lahan', 'bobot' => 1.00, 'tipe' => 'benefit', 'urutan' => 1, 'is_active' => true]);

        $p1 = $this->buatPengajuanDenganNilai('RECALC-1', [$c1->id => 60.0]);
        $p2 = $this->buatPengajuanDenganNilai('RECALC-2', [$c1->id => 30.0]);

        $hasilPertama = $this->service->hitung($this->alsintan->id, $this->admin->id);
        $this->assertSame([$p1->id, $p2->id], array_column($hasilPertama['ranking'], 'pengajuan_id'));

        // Perbarui skor p2 agar lebih tinggi dari p1, lalu hitung ulang
        PenilaianTopsis::where('pengajuan_bantuan_id', $p2->id)
            ->where('kriteria_id', $c1->id)
            ->update(['nilai' => 90.0]);

        $hasilKedua = $this->service->hitung($this->alsintan->id, $this->admin->id);

        $this->assertNotSame($hasilPertama['batch_id'], $hasilKedua['batch_id']);
        $this->assertSame([$p2->id, $p1->id], array_column($hasilKedua['ranking'], 'pengajuan_id'));
        $this->assertSame(2, HasilTopsis::count());
        $this->assertSame(1, HasilTopsis::where('pengajuan_bantuan_id', $p2->id)->firstOrFail()->ranking);
        $this->assertSame(2, HasilTopsis::where('pengajuan_bantuan_id', $p1->id)->firstOrFail()->ranking);
    }
}
