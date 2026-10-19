<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwalPeriksas = Auth::user()->jadwalPeriksas()
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('dokter.jadwal-periksa.index', compact('jadwalPeriksas'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create', ['hariList' => self::HARI]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => ['required', 'in:'.implode(',', self::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        Auth::user()->jadwalPeriksas()->create($validated);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwalPeriksa = $this->findOwnedSchedule($id);

        return view('dokter.jadwal-periksa.edit', [
            'jadwalPeriksa' => $jadwalPeriksa,
            'hariList' => self::HARI,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'hari' => ['required', 'in:'.implode(',', self::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $jadwalPeriksa = $this->findOwnedSchedule($id);
        $jadwalPeriksa->update($validated);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->findOwnedSchedule($id)->delete();

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil dihapus.');
    }

    private function findOwnedSchedule($id)
    {
        return Auth::user()->jadwalPeriksas()->findOrFail($id);
    }
}
