<?php

namespace Tests\Feature;

use App\Models\KelompokTani;
use App\Models\UlasanLayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UlasanLayananTest extends TestCase
{
    use RefreshDatabase;

    private User $poktanUser;
    private KelompokTani $poktan;
    private User $kepbidUser;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poktanUser = User::factory()->create([
            'name'      => 'Alfath',
            'role'      => User::ROLE_POKTAN,
            'is_active' => true,
        ]);

        $this->poktan = KelompokTani::create([
            'user_id'           => $this->poktanUser->id,
            'nama_poktan'       => 'Poktan Alfa',
            'nomor_badan_hukum' => 'SK-001/2026',
            'nama_ketua'        => 'Alfath',
            'no_hp_ketua'       => '081234567890',
            'desa'              => 'Sidamulya',
            'kecamatan'         => 'Purwadadi',
            'alamat_lengkap'    => 'Dusun Sidamulya RT 01/02',
            'luas_lahan_total'  => 30.00,
            'jumlah_anggota'    => 20,
        ]);

        $this->kepbidUser = User::factory()->create([
            'name'      => 'Kepala Bidang PSP',
            'role'      => User::ROLE_KEPBID,
            'is_active' => true,
        ]);

        $this->adminUser = User::factory()->create([
            'name'      => 'Admin Dinas',
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_poktan_dapat_mengirim_ulasan_dan_default_tersembunyi(): void
    {
        $response = $this->actingAs($this->poktanUser)
            ->post(route('poktan.ulasan.store'), [
                'rating_bintang' => 5,
                'komentar'       => 'Pelayanan sangat membantu kelompok tani kami.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ulasan_layanans', [
            'kelompok_tani_id'  => $this->poktan->id,
            'rating_bintang'    => 5,
            'is_tampil_landing' => false,
        ]);
    }

    public function test_kepala_bidang_dapat_mengkurasi_dan_menyetujui_ulasan_ke_beranda(): void
    {
        $ulasan = UlasanLayanan::create([
            'kelompok_tani_id'  => $this->poktan->id,
            'rating_bintang'    => 5,
            'komentar'          => 'Transparan dan cepat.',
            'is_tampil_landing' => false,
        ]);

        $response = $this->actingAs($this->kepbidUser)
            ->post(route('kepbid.ulasan.toggle_landing', $ulasan->id));

        $response->assertRedirect();
        $ulasan->refresh();
        $this->assertTrue($ulasan->is_tampil_landing);
        $this->assertSame($this->kepbidUser->id, $ulasan->ditinjau_oleh);
        $this->assertNotNull($ulasan->ditinjau_pada);
    }

    public function test_non_kepala_bidang_ditolak_saat_mencoba_moderasi_ulasan(): void
    {
        $ulasan = UlasanLayanan::create([
            'kelompok_tani_id'  => $this->poktan->id,
            'rating_bintang'    => 4,
            'is_tampil_landing' => false,
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('kepbid.ulasan.toggle_landing', $ulasan->id))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($this->poktanUser)
            ->post(route('kepbid.ulasan.toggle_landing', $ulasan->id))
            ->assertRedirect(route('poktan.dashboard'));

        $this->assertFalse($ulasan->fresh()->is_tampil_landing);
    }

    public function test_landing_page_maksimal_menampilkan_tiga_ulasan_terkurasi(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            UlasanLayanan::create([
                'kelompok_tani_id'  => $this->poktan->id,
                'rating_bintang'    => 5,
                'komentar'          => "Ulasan ke-{$i}",
                'is_tampil_landing' => true,
                'ditinjau_oleh'     => $this->kepbidUser->id,
                'ditinjau_pada'     => now()->addMinutes($i),
            ]);
        }

        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $ulasanLanding = $response->viewData('ulasanLanding');
        $this->assertCount(3, $ulasanLanding);
    }
}
