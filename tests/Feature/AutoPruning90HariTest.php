<?php

namespace Tests\Feature;

use App\Models\BerkasPengajuan;
use App\Models\HasilTopsis;
use App\Models\JenisAlsintan;
use App\Models\PengajuanBantuan;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AutoPruning90HariTest extends TestCase
{
    use RefreshDatabase;

    private User $poktan;

    private User $kepbid;

    private JenisAlsintan $alsintan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->poktan = User::factory()->create([
            'name'      => 'Ketua Poktan Srimukti',
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);

        $this->kepbid = User::factory()->create([
            'name'      => 'Kepala Bidang PSP',
            'role'      => User::ROLE_KEPBID,
            'is_active' => true,
        ]);

        $this->alsintan = JenisAlsintan::create([
            'nama_alsintan' => 'Traktor Roda 2',
            'kode_alsintan' => 'TR2',
            'satuan'        => 'unit',
            'kuota_unit'    => 10,
            'is_active'     => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Mengembalikan instance disk 'private' bertipe FilesystemAdapter
     * agar pemanggilan assertion storage terdeteksi aman oleh static analyzer.
     */
    private function privateDisk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk;
    }

    /**
     * Membuat pengajuan bantuan beserta 1 file PDF fisik pada disk 'private' (fake).
     */
    private function buatPengajuanDenganBerkas(
        string $nomor,
        string $status = PengajuanBantuan::STATUS_DIAJUKAN,
        ?Carbon $expiredAt = null,
        bool $buatFileFisik = true
    ): array {
        $attributes = [
            'user_id'           => $this->poktan->id,
            'jenis_alsintan_id' => $this->alsintan->id,
            'nomor_pengajuan'   => $nomor,
            'tahun_pengajuan'   => 2026,
            'jumlah_diminta'    => 1,
            'alasan_pengajuan'  => 'Pengujian retensi berkas dan auto-pruning 90 hari.',
            'status'            => $status,
        ];

        if ($expiredAt !== null) {
            $attributes['expired_at'] = $expiredAt;
        }

        $pengajuan = PengajuanBantuan::create($attributes);
        $filePath = "pengajuan/{$pengajuan->id}/proposal_{$pengajuan->id}.pdf";
        $dummyPdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF";

        if ($buatFileFisik) {
            $this->privateDisk()->put($filePath, $dummyPdf);
        }

        $berkas = BerkasPengajuan::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'nama_berkas'          => 'Surat Permohonan Bantuan',
            'jenis_berkas'         => 'surat_permohonan',
            'file_path'            => $filePath,
            'file_name_original'   => "Proposal_{$nomor}.pdf",
            'file_mime_type'       => 'application/pdf',
            'file_size'            => strlen($dummyPdf),
        ]);

        return [$pengajuan, $berkas, $filePath];
    }

    public function test_pengajuan_baru_otomatis_mendapat_masa_retensi_90_hari(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan] = $this->buatPengajuanDenganBerkas('PRUNE-001');

        $this->assertNotNull($pengajuan->expired_at);
        $this->assertTrue($pengajuan->expired_at->equalTo($waktuMulai->copy()->addDays(90)));
        $this->assertFalse($pengajuan->isExpired());
        $this->assertFalse($pengajuan->isFilesPruned());
        $this->assertSame(90, $pengajuan->sisaHariRetensi());
    }

    public function test_berkas_dalam_masa_retensi_di_bawah_90_hari_tidak_dipruning(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan, $berkas, $filePath] = $this->buatPengajuanDenganBerkas(
            'PRUNE-ACTIVE',
            PengajuanBantuan::STATUS_DITOLAK_ADMIN
        );

        // Maju ke hari ke-89 (masih tersisa 1 hari sebelum kedaluwarsa 90 hari)
        Carbon::setTestNow($waktuMulai->copy()->addDays(89));

        $this->artisan('spk:prune-files')
            ->expectsOutputToContain('Tidak ada berkas pengajuan yang kedaluwarsa saat ini')
            ->assertExitCode(0);

        $this->privateDisk()->assertExists($filePath);
        $this->assertNull($pengajuan->fresh()->files_pruned_at);
        $this->assertFalse($pengajuan->fresh()->isExpired());
        $this->assertDatabaseHas('berkas_pengajuans', ['id' => $berkas->id]);
    }

    public function test_auto_pruning_menghapus_berkas_fisik_setelah_90_hari_dan_menjaga_metadata_serta_hasil_topsis(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan, $berkas, $filePath] = $this->buatPengajuanDenganBerkas(
            'PRUNE-EXPIRED-90',
            PengajuanBantuan::STATUS_DITOLAK_KABID
        );

        $hasilTopsis = HasilTopsis::create([
            'pengajuan_bantuan_id' => $pengajuan->id,
            'batch_kalkulasi_id'   => Str::uuid()->toString(),
            'jarak_ideal_positif'  => 0.1250000000,
            'jarak_ideal_negatif'  => 0.0850000000,
            'nilai_preferensi'     => 0.4047619048,
            'ranking'              => 3,
            'dihitung_pada'        => $waktuMulai,
            'dihitung_oleh'        => $this->kepbid->id,
        ]);

        // Maju melewati batas 90 hari (hari ke-91)
        $waktuPruning = $waktuMulai->copy()->addDays(91);
        Carbon::setTestNow($waktuPruning);

        $this->assertTrue($pengajuan->fresh()->isExpired());

        $this->artisan('spk:prune-files')
            ->expectsOutputToContain('Ditemukan 1 usulan yang masa retensi berkasnya telah habis')
            ->expectsOutputToContain('Berhasil! 1 file PDF kedaluwarsa telah dibersihkan')
            ->assertExitCode(0);

        // File fisik dan folder kosong pengajuan/{id} harus terhapus dari disk private
        $this->privateDisk()->assertMissing($filePath);
        $this->assertFalse($this->privateDisk()->exists("pengajuan/{$pengajuan->id}"));

        // Timestamp files_pruned_at harus tercatat
        $pengajuanSegar = $pengajuan->fresh();
        $this->assertNotNull($pengajuanSegar->files_pruned_at);
        $this->assertTrue($pengajuanSegar->isFilesPruned());
        $this->assertTrue($pengajuanSegar->files_pruned_at->equalTo($waktuPruning));

        // Record pengajuan, metadata berkas, dan hasil TOPSIS tetap utuh untuk keperluan audit
        $this->assertDatabaseHas('pengajuan_bantuans', [
            'id'              => $pengajuan->id,
            'nomor_pengajuan' => 'PRUNE-EXPIRED-90',
            'status'          => PengajuanBantuan::STATUS_DITOLAK_KABID,
        ]);
        $this->assertDatabaseHas('berkas_pengajuans', ['id' => $berkas->id]);
        $this->assertDatabaseHas('hasil_topsis', [
            'id'                   => $hasilTopsis->id,
            'pengajuan_bantuan_id' => $pengajuan->id,
            'ranking'              => 3,
        ]);
    }

    public function test_mode_dry_run_tidak_menghapus_berkas_fisik_dan_tidak_mengubah_files_pruned_at(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan, , $filePath] = $this->buatPengajuanDenganBerkas(
            'PRUNE-DRYRUN',
            PengajuanBantuan::STATUS_DITOLAK_ADMIN
        );

        Carbon::setTestNow($waktuMulai->copy()->addDays(95));

        $this->artisan('spk:prune-files', ['--dry-run' => true])
            ->expectsOutputToContain('MODE SIMULASI (DRY-RUN)')
            ->expectsOutputToContain('Total file yang siap dipruning: 1 file')
            ->assertExitCode(0);

        $this->privateDisk()->assertExists($filePath);
        $this->assertNull($pengajuan->fresh()->files_pruned_at);
    }

    public function test_idempotensi_pruning_tidak_memproses_ulang_usulan_yang_sudah_dipruning(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan, , $filePath] = $this->buatPengajuanDenganBerkas(
            'PRUNE-IDEMPOTENT',
            PengajuanBantuan::STATUS_DITOLAK_KABID
        );

        $eksekusiPertama = $waktuMulai->copy()->addDays(91);
        Carbon::setTestNow($eksekusiPertama);

        $this->artisan('spk:prune-files')->assertExitCode(0);
        $this->privateDisk()->assertMissing($filePath);
        $timestampPertama = $pengajuan->fresh()->files_pruned_at;

        // Jalankan lagi 10 hari kemudian: tidak boleh diproses ulang
        Carbon::setTestNow($eksekusiPertama->copy()->addDays(10));

        $this->artisan('spk:prune-files')
            ->expectsOutputToContain('Tidak ada berkas pengajuan yang kedaluwarsa saat ini')
            ->assertExitCode(0);

        $this->assertTrue($pengajuan->fresh()->files_pruned_at->equalTo($timestampPertama));
    }

    public function test_keputusan_kabid_menetapkan_retensi_90_hari_untuk_penolakan_dan_13_bulan_untuk_persetujuan(): void
    {
        $waktuKeputusan = Carbon::create(2026, 2, 1, 10, 0, 0);
        Carbon::setTestNow($waktuKeputusan);

        [$usulanDitolak, , $fileDitolak] = $this->buatPengajuanDenganBerkas(
            'KEPBID-TOLAK',
            PengajuanBantuan::STATUS_DIRANGKING
        );
        [$usulanDisetujui, , $fileDisetujui] = $this->buatPengajuanDenganBerkas(
            'KEPBID-ACC',
            PengajuanBantuan::STATUS_DIRANGKING
        );

        // Kepala Bidang menolak usulan pertama -> retensi harus 90 hari
        $this->actingAs($this->kepbid)
            ->post(route('kepbid.putuskan', $usulanDitolak), [
                'keputusan'      => 'tolak',
                'catatan_kepbid' => 'Kuota diprioritaskan untuk Poktan dengan skor lebih tinggi.',
            ])
            ->assertRedirect();

        // Kepala Bidang menyetujui usulan kedua -> retensi harus 13 bulan
        $this->actingAs($this->kepbid)
            ->post(route('kepbid.putuskan', $usulanDisetujui), [
                'keputusan'      => 'setuju',
                'catatan_kepbid' => 'Disetujui sesuai peringkat 1 TOPSIS.',
            ])
            ->assertRedirect();

        $usulanDitolak->refresh();
        $usulanDisetujui->refresh();

        $this->assertSame(PengajuanBantuan::STATUS_DITOLAK_KABID, $usulanDitolak->status);
        $this->assertTrue($usulanDitolak->expired_at->equalTo($waktuKeputusan->copy()->addDays(90)));

        $this->assertSame(PengajuanBantuan::STATUS_DISETUJUI_KABID, $usulanDisetujui->status);
        $this->assertTrue($usulanDisetujui->expired_at->equalTo($waktuKeputusan->copy()->addMonths(13)));

        // Pada hari ke-91: berkas usulan ditolak terhapus, sedangkan berkas ACC (13 bulan) tetap aman
        Carbon::setTestNow($waktuKeputusan->copy()->addDays(91));
        $this->artisan('spk:prune-files')->assertExitCode(0);

        $this->privateDisk()->assertMissing($fileDitolak);
        $this->assertNotNull($usulanDitolak->fresh()->files_pruned_at);

        $this->privateDisk()->assertExists($fileDisetujui);
        $this->assertNull($usulanDisetujui->fresh()->files_pruned_at);

        // Setelah melewati 13 bulan + 1 hari: berkas ACC baru dibersihkan
        Carbon::setTestNow($waktuKeputusan->copy()->addMonths(13)->addDay());
        $this->artisan('spk:prune-files')->assertExitCode(0);

        $this->privateDisk()->assertMissing($fileDisetujui);
        $this->assertNotNull($usulanDisetujui->fresh()->files_pruned_at);
    }

    public function test_endpoint_manual_pruning_kepala_bidang_dan_proteksi_rbac_poktan(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan, , $filePath] = $this->buatPengajuanDenganBerkas(
            'HTTP-PRUNE-01',
            PengajuanBantuan::STATUS_DITOLAK_ADMIN
        );

        Carbon::setTestNow($waktuMulai->copy()->addDays(91));

        // Poktan tidak boleh menjalankan endpoint pruning Kepala Bidang (diarahkan ulang oleh RoleMiddleware)
        $this->actingAs($this->poktan)
            ->post(route('kepbid.pruning.run'))
            ->assertRedirect(route('poktan.dashboard'))
            ->assertSessionHas('warning');

        $this->privateDisk()->assertExists($filePath);
        $this->assertNull($pengajuan->fresh()->files_pruned_at);

        // Kepala Bidang menjalankan pruning manual melalui HTTP endpoint
        $this->actingAs($this->kepbid)
            ->post(route('kepbid.pruning.run'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->privateDisk()->assertMissing($filePath);
        $this->assertNotNull($pengajuan->fresh()->files_pruned_at);
    }

    public function test_scope_prunable_model_memilih_kandidat_sesuai_aturan_bisnis(): void
    {
        $sekarang = Carbon::create(2026, 6, 1, 12, 0, 0);
        Carbon::setTestNow($sekarang);

        [$aktifBelumExpired] = $this->buatPengajuanDenganBerkas(
            'SCOPE-AKTIF',
            PengajuanBantuan::STATUS_DIAJUKAN,
            $sekarang->copy()->addDays(30)
        );
        [$expiredBelumPruned] = $this->buatPengajuanDenganBerkas(
            'SCOPE-EXPIRED',
            PengajuanBantuan::STATUS_DIAJUKAN,
            $sekarang->copy()->subDay()
        );
        [$ditolakBelumPruned] = $this->buatPengajuanDenganBerkas(
            'SCOPE-DITOLAK',
            PengajuanBantuan::STATUS_DITOLAK_ADMIN,
            $sekarang->copy()->addDays(45)
        );
        [$sudahPruned] = $this->buatPengajuanDenganBerkas(
            'SCOPE-PRUNED',
            PengajuanBantuan::STATUS_DITOLAK_KABID,
            $sekarang->copy()->subDays(10)
        );
        $sudahPruned->updateQuietly(['files_pruned_at' => $sekarang->copy()->subDays(5)]);

        $kandidatIds = (new PengajuanBantuan())->prunable()->pluck('id')->all();

        $this->assertNotContains($aktifBelumExpired->id, $kandidatIds);
        $this->assertNotContains($sudahPruned->id, $kandidatIds);
        $this->assertContains($expiredBelumPruned->id, $kandidatIds);
        $this->assertContains($ditolakBelumPruned->id, $kandidatIds);
    }

    public function test_pruning_tetap_sukses_meski_berkas_fisik_sudah_tidak_ada_di_disk(): void
    {
        $waktuMulai = Carbon::create(2026, 1, 1, 8, 0, 0);
        Carbon::setTestNow($waktuMulai);

        [$pengajuan] = $this->buatPengajuanDenganBerkas(
            'PRUNE-MISSING-FILE',
            PengajuanBantuan::STATUS_DITOLAK_ADMIN,
            null,
            false
        );

        Carbon::setTestNow($waktuMulai->copy()->addDays(91));

        $this->artisan('spk:prune-files')
            ->expectsOutputToContain('Berhasil! 0 file PDF kedaluwarsa telah dibersihkan')
            ->assertExitCode(0);

        $this->assertNotNull($pengajuan->fresh()->files_pruned_at);
    }
}
