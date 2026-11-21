<?php

namespace Tests\Feature\Admin;

use App\Models\Kamar;
use App\Models\Kontrak;
use App\Models\TipeKamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KontrakControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_update_accept_duration_submitted_as_a_string(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $tipeKamar = TipeKamar::create(['nama' => 'Standard']);
        $kamar = Kamar::create([
            'tipe_kamar_id' => $tipeKamar->id,
            'nomor_kamar' => 'A-01',
            'harga_sewa' => 1000000,
            'deposit' => 500000,
            'status' => 'tersedia',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kontrak.store'), [
                'kamar_id' => $kamar->id,
                'nama' => 'Penghuni',
                'email' => 'penghuni@example.test',
                'no_hp' => '08123456789',
                'no_ktp' => '1234567890123456',
                'tanggal_mulai' => '2024-01-31',
                'durasi_bulan' => '2',
            ])
            ->assertSessionHasNoErrors();

        $kontrak = Kontrak::firstOrFail();
        $this->assertSame('2024-03-31', $kontrak->tanggal_selesai->toDateString());

        $this->put(route('admin.kontrak.update', $kontrak), [
            'nama' => 'Penghuni',
            'no_hp' => '08123456789',
            'no_ktp' => '1234567890123456',
            'durasi_bulan' => '2',
        ])->assertSessionHasNoErrors();

        $this->assertSame('2024-03-31', $kontrak->fresh()->tanggal_selesai->toDateString());
    }
}
