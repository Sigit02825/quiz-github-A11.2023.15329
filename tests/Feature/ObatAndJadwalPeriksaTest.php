<?php

namespace Tests\Feature;

use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObatAndJadwalPeriksaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_can_create_update_and_delete_obat(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');
        $this->actingAs($admin);

        $this->get(route('obat.index'))->assertOk();
        $this->get(route('obat.create'))->assertOk();
        $this->post(route('obat.store'), [
            'nama_obat' => 'Paracetamol',
            'kemasan' => 'Tablet',
            'harga' => 5000,
        ])->assertRedirect(route('obat.index'));

        $obat = Obat::query()->firstOrFail();
        $this->assertDatabaseHas('obat', [
            'id' => $obat->id,
            'nama_obat' => 'Paracetamol',
            'kemasan' => 'Tablet',
            'harga' => 5000,
        ]);

        $this->get(route('obat.edit', $obat))->assertOk();
        $this->put(route('obat.update', $obat), [
            'nama_obat' => 'Paracetamol Forte',
            'kemasan' => 'Kaplet',
            'harga' => 7500,
        ])->assertRedirect(route('obat.index'));
        $this->assertDatabaseHas('obat', [
            'id' => $obat->id,
            'nama_obat' => 'Paracetamol Forte',
            'kemasan' => 'Kaplet',
            'harga' => 7500,
        ]);

        $this->delete(route('obat.destroy', $obat))->assertRedirect(route('obat.index'));
        $this->assertDatabaseMissing('obat', ['id' => $obat->id]);
    }

    public function test_obat_requires_valid_name_packaging_and_price(): void
    {
        $this->actingAs($this->createUser('admin', 'admin-validation@example.test'))
            ->post(route('obat.store'), [
                'nama_obat' => '',
                'kemasan' => '',
                'harga' => -1,
            ])
            ->assertSessionHasErrors(['nama_obat', 'kemasan', 'harga']);
    }

    public function test_doctor_can_manage_only_their_own_schedules_and_status(): void
    {
        $doctor = $this->createUser('dokter', 'doctor@example.test');
        $otherDoctor = $this->createUser('dokter', 'other-doctor@example.test');
        $this->actingAs($doctor);

        $this->get(route('jadwal-periksa.index'))->assertOk();
        $this->get(route('jadwal-periksa.create'))->assertOk();
        $this->post(route('jadwal-periksa.store'), [
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'status' => 'aktif',
        ])->assertRedirect(route('jadwal-periksa.index'));

        $jadwalPeriksa = JadwalPeriksa::query()->firstOrFail();
        $this->assertSame($doctor->id, $jadwalPeriksa->id_dokter);
        $this->assertDatabaseHas('jadwal_periksa', [
            'id' => $jadwalPeriksa->id,
            'status' => 'aktif',
        ]);
        $this->get(route('jadwal-periksa.edit', $jadwalPeriksa))->assertOk();

        $this->put(route('jadwal-periksa.update', $jadwalPeriksa), [
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '13:00',
            'status' => 'nonaktif',
        ])->assertRedirect(route('jadwal-periksa.index'));
        $this->assertDatabaseHas('jadwal_periksa', [
            'id' => $jadwalPeriksa->id,
            'hari' => 'Selasa',
            'status' => 'nonaktif',
        ]);

        $ownedByOtherDoctor = $otherDoctor->jadwalPeriksas()->create([
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'status' => 'aktif',
        ]);
        $this->get(route('jadwal-periksa.edit', $ownedByOtherDoctor))->assertNotFound();
        $this->delete(route('jadwal-periksa.destroy', $ownedByOtherDoctor))->assertNotFound();
        $this->assertDatabaseHas('jadwal_periksa', ['id' => $ownedByOtherDoctor->id]);

        $this->delete(route('jadwal-periksa.destroy', $jadwalPeriksa))
            ->assertRedirect(route('jadwal-periksa.index'));
        $this->assertDatabaseMissing('jadwal_periksa', ['id' => $jadwalPeriksa->id]);
    }

    public function test_schedule_requires_valid_day_times_and_status(): void
    {
        $this->actingAs($this->createUser('dokter', 'doctor-validation@example.test'))
            ->post(route('jadwal-periksa.store'), [
                'hari' => 'Hari Tidak Valid',
                'jam_mulai' => '12:00',
                'jam_selesai' => '11:00',
                'status' => 'invalid',
            ])
            ->assertSessionHasErrors(['hari', 'jam_selesai', 'status']);
    }

    private function createUser(string $role, string $email): User
    {
        return User::query()->create([
            'nama' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
