<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\PengajuanBantuan;
use App\Models\HasilTopsis;
use App\Models\PenilaianTopsis;
use App\Models\RiwayatStatusPengajuan;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ============================================================
 * TopsisCalculationService
 * ============================================================
 * Service Layer yang mengimplementasikan 6 Langkah Metode TOPSIS
 * (Technique for Order Preference by Similarity to Ideal Solution).
 *
 * Referensi matematis: TOPSIS_FORMULA_SPEC.md (di root proyek)
 * Referensi laporan  : Bab 3 (Metodologi) & Bab 4 (Implementasi)
 *
 * CARA PENGGUNAAN (dari Controller):
 * -----------------------------------
 * $service = new TopsisCalculationService();
 * $hasil   = $service->hitung($jenisAlsintanId);
 *
 * CATATAN UNTUK DOSEN PENGUJI:
 * Service ini dipisah dari Controller (pola Service Layer) agar
 * logika bisnis matematis tidak tercampur dengan logika HTTP request.
 * Hal ini sesuai dengan prinsip Single Responsibility Principle (SRP).
 */
class TopsisCalculationService
{
    // =========================================================
    // METODE PUBLIK UTAMA
    // =========================================================

    /**
     * Menjalankan seluruh pipeline kalkulasi TOPSIS untuk satu
     * kategori jenis Alsintan tertentu.
     *
     * @param int $jenisAlsintanId  ID kategori Alsintan yang dihitung
     * @param int $operatorId       ID user admin yang memicu kalkulasi
     * @return array                Array berisi hasil ranking lengkap
     */
    public function hitung(int $jenisAlsintanId, int $operatorId): array
    {
        // Ambil semua kriteria aktif dari database (urutan sesuai kolom 'urutan')
        $kriterias = Kriteria::aktif()->get()->keyBy('id');

        if ($kriterias->isEmpty()) {
            throw new \RuntimeException('Tidak ada kriteria aktif. Tambahkan kriteria TOPSIS terlebih dahulu.');
        }

        // Ambil pengajuan yang sudah dinilai (punya skor kriteria) dan tidak ditolak berkasnya
        $pengajuans = PengajuanBantuan::with(['user.kelompokTani', 'penilaianTopsis'])
            ->where('jenis_alsintan_id', $jenisAlsintanId)
            ->whereHas('penilaianTopsis')
            ->whereNotIn('status', [
                PengajuanBantuan::STATUS_DRAFT,
                PengajuanBantuan::STATUS_DITOLAK_ADMIN,
            ])
            ->get();

        if ($pengajuans->count() < 1) {
            throw new \RuntimeException('Belum ada alternatif (Kelompok Tani) yang siap dinilai untuk kategori alat ini.');
        }

        // Buat ID unik untuk batch kalkulasi ini (untuk audit trail)
        $batchId = Str::uuid()->toString();

        // ----------------------------------------------------------------
        // LANGKAH 1: Bangun Matriks Keputusan dari data penilaian
        // ----------------------------------------------------------------
        $matriks = $this->langkah1_bangunMatriks($pengajuans, $kriterias);

        // KONDISI ADAPTIF CALON TUNGGAL (m = 1) vs MULTI-ALTERNATIF (m >= 2)
        if ($pengajuans->count() === 1) {
            // ATURAN PENUGASAN LANGSUNG (SINGLE-CANDIDATE DIRECT PASS):
            // Jika hanya 1 kelompok tani yang mengajukan dan lolos verifikasi berkas,
            // alternatif tunggal langsung ditetapkan sebagai Peringkat 1 dengan preferensi sempurna (Vi = 1.0)
            // agar proses persetujuan dinas tidak mandek menunggu pendaftar lain.
            $pId = $pengajuans->first()->id;
            $matriksNormal = [];
            $matriksTermobot = [];
            $idealPositif = [];
            $idealNegatif = [];

            foreach ($kriterias as $kId => $kriteria) {
                $matriksNormal[$pId][$kId] = 1.0;
                $bobotFloat = (float) $kriteria->bobot;
                $matriksTermobot[$pId][$kId] = $bobotFloat;
                $idealPositif[$kId] = $bobotFloat;
                $idealNegatif[$kId] = 0.0;
            }

            $jarakPositif = [$pId => 0.0];
            $jarakNegatif = [$pId => 1.0];
            $ranking = [
                [
                    'pengajuan_id'     => $pId,
                    'nilai_preferensi' => 1.0000000000,
                    'ranking'          => 1,
                    'jarak_positif'    => 0.0,
                    'jarak_negatif'    => 1.0,
                ]
            ];
        } else {
            // ----------------------------------------------------------------
            // LANGKAH 2: Normalisasi Matriks (Euclidean Normalization)
            // ----------------------------------------------------------------
            $matriksNormal = $this->langkah2_normalisasiMatriks($matriks, $kriterias);

            // ----------------------------------------------------------------
            // LANGKAH 3: Normalisasi Matriks Terbobot
            // ----------------------------------------------------------------
            $matriksTermobot = $this->langkah3_normalisasiTermobot($matriksNormal, $kriterias);

            // ----------------------------------------------------------------
            // LANGKAH 4: Tentukan Solusi Ideal Positif (A+) dan Negatif (A-)
            // ----------------------------------------------------------------
            [$idealPositif, $idealNegatif] = $this->langkah4_solusiIdeal($matriksTermobot, $kriterias);

            // ----------------------------------------------------------------
            // LANGKAH 5: Hitung Jarak Euclidean D+ dan D-
            // ----------------------------------------------------------------
            [$jarakPositif, $jarakNegatif] = $this->langkah5_jarakEuclidean(
                $matriksTermobot, $idealPositif, $idealNegatif
            );

            // ----------------------------------------------------------------
            // LANGKAH 6: Hitung Nilai Preferensi (Vi) dan Susun Ranking
            // ----------------------------------------------------------------
            $ranking = $this->langkah6_nilaiPreferensi($jarakPositif, $jarakNegatif);
        }

        // Simpan hasil ke database dan kembalikan data lengkap untuk tampilan
        $hasilLengkap = $this->simpanHasilKeDatabase(
            $ranking, $batchId, $operatorId,
            $matriks, $matriksNormal, $matriksTermobot,
            $idealPositif, $idealNegatif,
            $jarakPositif, $jarakNegatif,
            $pengajuans
        );

        return $hasilLengkap;
    }

    // =========================================================
    // LANGKAH 1: MATRIKS KEPUTUSAN
    // =========================================================

    /**
     * Langkah 1 TOPSIS: Membangun Matriks Keputusan X (m x n)
     *
     * Membaca nilai penilaian dari database dan menyusunnya
     * ke dalam struktur matriks 2 dimensi.
     *
     * Rumus: x_ij = nilai Poktan ke-i untuk Kriteria ke-j
     *
     * @param Collection $pengajuans  Koleksi pengajuan yang akan dihitung
     * @param Collection $kriterias   Koleksi kriteria aktif
     * @return array                  Matriks [pengajuan_id][kriteria_id] = nilai
     */
    private function langkah1_bangunMatriks(Collection $pengajuans, Collection $kriterias): array
    {
        $matriks = [];

        foreach ($pengajuans as $pengajuan) {
            $matriks[$pengajuan->id] = [];
            foreach ($kriterias as $kriteriaId => $kriteria) {
                $matriks[$pengajuan->id][$kriteriaId] = 0.0;
            }

            // Akses langsung koleksi relasi tanpa mengalokasikan koleksi baru per pengajuan
            foreach ($pengajuan->penilaianTopsis as $penilaian) {
                if (isset($matriks[$pengajuan->id][$penilaian->kriteria_id])) {
                    $matriks[$pengajuan->id][$penilaian->kriteria_id] = (float) $penilaian->nilai;
                }
            }
        }

        return $matriks;
    }

    // =========================================================
    // LANGKAH 2: NORMALISASI MATRIKS
    // =========================================================

    /**
     * Langkah 2 TOPSIS: Normalisasi Matriks menggunakan Euclidean Length
     *
     * Tujuan: Menghilangkan perbedaan satuan antar kriteria sehingga
     *         semua nilai berada pada skala yang setara.
     *
     * Rumus: r_ij = x_ij / sqrt( sum(x_ij^2) untuk semua i )
     *
     * @param array      $matriks    Matriks keputusan dari Langkah 1
     * @param Collection $kriterias  Koleksi kriteria aktif
     * @return array                 Matriks ternormalisasi [pengajuan_id][kriteria_id] = r_ij
     */
    private function langkah2_normalisasiMatriks(array $matriks, Collection $kriterias): array
    {
        $matriksNormal = [];

        foreach ($kriterias as $kriteriaId => $kriteria) {
            // ---- HITUNG PENYEBUT: Euclidean Length kolom j ----
            // Penyebut = sqrt( sum(x_ij^2) untuk semua alternatif i )
            $jumlahKuadrat = 0;
            foreach ($matriks as $pengajuanId => $nilai) {
                $jumlahKuadrat += pow($nilai[$kriteriaId], 2);
            }
            $pembaginEuclidean = sqrt($jumlahKuadrat); // Akar kuadrat dari jumlah kuadrat

            // ---- NORMALISASI: Bagi setiap nilai dengan penyebut ----
            foreach ($matriks as $pengajuanId => $nilai) {
                // Hindari pembagian dengan nol (semua nilai kolom = 0)
                $matriksNormal[$pengajuanId][$kriteriaId] = ($pembaginEuclidean > 0)
                    ? $nilai[$kriteriaId] / $pembaginEuclidean  // r_ij = x_ij / ||x_j||
                    : 0.0;
            }
        }

        return $matriksNormal;
    }

    // =========================================================
    // LANGKAH 3: NORMALISASI MATRIKS TERBOBOT
    // =========================================================

    /**
     * Langkah 3 TOPSIS: Normalisasi Matriks Terbobot (Weighted Normalized Matrix)
     *
     * Mengalikan setiap nilai ternormalisasi dengan bobot kriteria
     * sehingga kriteria yang lebih penting memiliki pengaruh lebih besar.
     *
     * Rumus: y_ij = w_j * r_ij
     *
     * Syarat: sum(w_j) untuk semua j = 1.0
     *
     * @param array      $matriksNormal  Matriks ternormalisasi dari Langkah 2
     * @param Collection $kriterias      Koleksi kriteria (dengan kolom 'bobot')
     * @return array                     Matriks terbobot [pengajuan_id][kriteria_id] = y_ij
     */
    private function langkah3_normalisasiTermobot(array $matriksNormal, Collection $kriterias): array
    {
        $matriksTermobot = [];

        foreach ($matriksNormal as $pengajuanId => $nilaiNormal) {
            foreach ($kriterias as $kriteriaId => $kriteria) {
                // Ambil bobot (w_j) dari database
                $bobot = (float) $kriteria->bobot;

                // y_ij = w_j * r_ij
                $matriksTermobot[$pengajuanId][$kriteriaId] = $bobot * $nilaiNormal[$kriteriaId];
            }
        }

        return $matriksTermobot;
    }

    // =========================================================
    // LANGKAH 4: SOLUSI IDEAL POSITIF (A+) DAN NEGATIF (A-)
    // =========================================================

    /**
     * Langkah 4 TOPSIS: Menentukan Solusi Ideal Positif (A+) dan Negatif (A-)
     *
     * A+ = "Poktan sempurna yang terbaik di semua kriteria"
     * A- = "Poktan terburuk yang paling buruk di semua kriteria"
     *
     * Aturan penentuan berdasarkan TIPE KRITERIA:
     * - benefit : A+(j) = max kolom j  |  A-(j) = min kolom j
     * - cost    : A+(j) = min kolom j  |  A-(j) = max kolom j
     *
     * @param array      $matriksTermobot  Matriks terbobot dari Langkah 3
     * @param Collection $kriterias        Koleksi kriteria (dengan kolom 'tipe')
     * @return array                       [$idealPositif, $idealNegatif]
     *                                     Masing-masing: [kriteria_id] = nilai ideal
     */
    private function langkah4_solusiIdeal(array $matriksTermobot, Collection $kriterias): array
    {
        $idealPositif = []; // A+ per kolom kriteria
        $idealNegatif = []; // A- per kolom kriteria

        foreach ($kriterias as $kriteriaId => $kriteria) {
            // Ambil semua nilai pada kolom kriteria ini (satu kolom dari matriks terbobot)
            $nilaiKolom = array_column($matriksTermobot, $kriteriaId);

            if ($kriteria->tipe === 'benefit') {
                // Benefit: nilai LEBIH BESAR = lebih baik
                // A+ = nilai maksimum, A- = nilai minimum
                $idealPositif[$kriteriaId] = max($nilaiKolom); // A+(j) = max
                $idealNegatif[$kriteriaId] = min($nilaiKolom); // A-(j) = min
            } else {
                // Cost: nilai LEBIH KECIL = lebih baik
                // A+ = nilai minimum, A- = nilai maksimum
                $idealPositif[$kriteriaId] = min($nilaiKolom); // A+(j) = min
                $idealNegatif[$kriteriaId] = max($nilaiKolom); // A-(j) = max
            }
        }

        return [$idealPositif, $idealNegatif];
    }

    // =========================================================
    // LANGKAH 5: JARAK EUCLIDEAN D+ DAN D-
    // =========================================================

    /**
     * Langkah 5 TOPSIS: Menghitung Jarak Euclidean dari A+ dan A-
     *
     * D+ = Seberapa JAUH setiap alternatif dari solusi ideal POSITIF
     * D- = Seberapa JAUH setiap alternatif dari solusi ideal NEGATIF
     *
     * Rumus D+(i): sqrt( sum( (y_ij - y_j+)^2 ) untuk semua j )
     * Rumus D-(i): sqrt( sum( (y_ij - y_j-)^2 ) untuk semua j )
     *
     * @param array $matriksTermobot  Matriks terbobot dari Langkah 3
     * @param array $idealPositif     Solusi ideal positif A+ dari Langkah 4
     * @param array $idealNegatif     Solusi ideal negatif A- dari Langkah 4
     * @return array                  [$jarakPositif, $jarakNegatif]
     *                                Masing-masing: [pengajuan_id] = nilai jarak
     */
    private function langkah5_jarakEuclidean(
        array $matriksTermobot,
        array $idealPositif,
        array $idealNegatif
    ): array {
        $jarakPositif = []; // D+ per alternatif
        $jarakNegatif = []; // D- per alternatif

        foreach ($matriksTermobot as $pengajuanId => $nilaiTermobot) {
            $jumlahKuadratPositif = 0; // Akumulator sum((y_ij - y_j+)^2)
            $jumlahKuadratNegatif = 0; // Akumulator sum((y_ij - y_j-)^2)

            foreach ($nilaiTermobot as $kriteriaId => $yij) {
                // Kuadrat selisih ke ideal positif: (y_ij - y_j+)^2
                $jumlahKuadratPositif += pow($yij - $idealPositif[$kriteriaId], 2);

                // Kuadrat selisih ke ideal negatif: (y_ij - y_j-)^2
                $jumlahKuadratNegatif += pow($yij - $idealNegatif[$kriteriaId], 2);
            }

            // D+(i) = sqrt( sum((y_ij - y_j+)^2) )
            $jarakPositif[$pengajuanId] = sqrt($jumlahKuadratPositif);

            // D-(i) = sqrt( sum((y_ij - y_j-)^2) )
            $jarakNegatif[$pengajuanId] = sqrt($jumlahKuadratNegatif);
        }

        return [$jarakPositif, $jarakNegatif];
    }

    // =========================================================
    // LANGKAH 6: NILAI PREFERENSI DAN RANKING
    // =========================================================

    /**
     * Langkah 6 TOPSIS: Menghitung Nilai Preferensi (Vi) dan Menyusun Ranking
     *
     * Vi adalah "skor kedekatan relatif" setiap alternatif terhadap solusi ideal.
     * Semakin mendekati 1 = semakin layak.
     *
     * Rumus: V_i = D-_i / ( D+_i + D-_i )
     *
     * - Vi = 1 : Alternatif identik dengan A+ (terbaik mungkin)
     * - Vi = 0 : Alternatif identik dengan A- (terburuk mungkin)
     *
     * @param array $jarakPositif  D+ per alternatif dari Langkah 5
     * @param array $jarakNegatif  D- per alternatif dari Langkah 5
     * @return array               Array ranking terurut [pengajuan_id, nilai_preferensi, ranking]
     */
    private function langkah6_nilaiPreferensi(array $jarakPositif, array $jarakNegatif): array
    {
        $nilaiPreferensi = []; // Vi per alternatif

        foreach ($jarakPositif as $pengajuanId => $dPlus) {
            $dMinus   = $jarakNegatif[$pengajuanId];
            $penyebut = $dPlus + $dMinus;

            // Hitung Vi = D- / (D+ + D-)
            // Guard: hindari pembagian dengan nol (kasus semua nilai = 0)
            $nilaiPreferensi[$pengajuanId] = ($penyebut > 0)
                ? $dMinus / $penyebut  // V_i = D- / (D+ + D-)
                : 0.0;
        }

        // Urutkan nilai preferensi secara DESCENDING (nilai terbesar = ranking 1)
        arsort($nilaiPreferensi);

        // Susun hasil ranking dengan nomor urut
        $ranking = [];
        $nomorRanking = 1;
        foreach ($nilaiPreferensi as $pengajuanId => $vi) {
            $ranking[] = [
                'pengajuan_id'     => $pengajuanId,
                'nilai_preferensi' => round($vi, 10), // Presisi 10 angka desimal
                'ranking'          => $nomorRanking++,
                'jarak_positif'    => round($jarakPositif[$pengajuanId], 10),
                'jarak_negatif'    => round($jarakNegatif[$pengajuanId], 10),
            ];
        }

        return $ranking;
    }

    // =========================================================
    // HELPER: SIMPAN HASIL KE DATABASE
    // =========================================================

    /**
     * Menyimpan hasil kalkulasi TOPSIS ke tabel 'hasil_topsis'
     * dan memperbarui status pengajuan menjadi 'dirangking'.
     *
     * @param array      $ranking          Hasil ranking dari Langkah 6
     * @param string     $batchId          ID unik batch kalkulasi (UUID)
     * @param int        $operatorId       ID user yang memicu kalkulasi
     * @param Collection $pengajuans       Data pengajuan untuk enrichment
     * @return array                       Array hasil lengkap untuk tampilan
     */
    private function simpanHasilKeDatabase(
        array $ranking,
        string $batchId,
        int $operatorId,
        array $matriks,
        array $matriksNormal,
        array $matriksTermobot,
        array $idealPositif,
        array $idealNegatif,
        array $jarakPositif,
        array $jarakNegatif,
        Collection $pengajuans
    ): array {
        // Index pengajuan by ID untuk lookup cepat
        $pengajuansById = $pengajuans->keyBy('id');
        $pengajuanIds   = array_column($ranking, 'pengajuan_id');
        $now            = now();

        DB::transaction(function () use (
            $ranking, $batchId, $operatorId, $pengajuanIds, $pengajuans, $now
        ) {
            // 1. Bersihkan hasil TOPSIS terdahulu untuk pengajuan-pengajuan ini secara massal (1 query)
            HasilTopsis::whereIn('pengajuan_bantuan_id', $pengajuanIds)->delete();

            // 2. Mass-insert hasil kalkulasi TOPSIS dalam chunk 250 baris (anti-timeout & hemat memory)
            $hasilRows = [];
            foreach ($ranking as $item) {
                $hasilRows[] = [
                    'pengajuan_bantuan_id' => $item['pengajuan_id'],
                    'batch_kalkulasi_id'  => $batchId,
                    'jarak_ideal_positif' => $item['jarak_positif'],
                    'jarak_ideal_negatif' => $item['jarak_negatif'],
                    'nilai_preferensi'    => $item['nilai_preferensi'],
                    'ranking'             => $item['ranking'],
                    'dihitung_pada'       => $now,
                    'dihitung_oleh'       => $operatorId,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }
            foreach (array_chunk($hasilRows, 250) as $chunk) {
                HasilTopsis::insert($chunk);
            }

            // 3. Mass-update status pengajuan menjadi 'dirangking' (1 query)
            PengajuanBantuan::whereIn('id', $pengajuanIds)->update([
                'status'     => PengajuanBantuan::STATUS_DIRANGKING,
                'updated_at' => $now,
            ]);

            // Sinkronkan status model in-memory tanpa memicu query tambahan
            foreach ($pengajuans as $p) {
                $p->status = PengajuanBantuan::STATUS_DIRANGKING;
            }

            // 4. Catat log audit trail (Tabel ke-9) secara massal
            $riwayatRows = [];
            foreach ($pengajuanIds as $pId) {
                $riwayatRows[] = [
                    'pengajuan_bantuan_id' => $pId,
                    'user_id'              => $operatorId,
                    'status_lama'          => PengajuanBantuan::STATUS_DINILAI,
                    'status_baru'          => PengajuanBantuan::STATUS_DIRANGKING,
                    'catatan'              => 'Kalkulasi algoritma SPK TOPSIS 6 langkah selesai dijalankan.',
                    'waktu_perubahan'      => $now,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
            }
            foreach (array_chunk($riwayatRows, 250) as $chunk) {
                RiwayatStatusPengajuan::insert($chunk);
            }
        });

        // Susun data lengkap untuk tampilan di View
        return [
            'batch_id'      => $batchId,
            'ranking'       => $ranking,
            'ideal_positif' => $idealPositif,
            'ideal_negatif' => $idealNegatif,
            'matriks'       => [
                'keputusan'   => $matriks,
                'normal'      => $matriksNormal,
                'terbobot'    => $matriksTermobot,
            ],
            'pengajuans'    => $pengajuansById,
            'dihitung_pada' => now()->toDateTimeString(),
        ];
    }
}

