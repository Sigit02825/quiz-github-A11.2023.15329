<x-layouts.app title="Tambah Jadwal Periksa">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal-periksa.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Tambah Jadwal Periksa</h2>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">
            <form action="{{ route('jadwal-periksa.store') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="hari" class="block text-sm font-semibold text-slate-700 mb-1">
                        Hari <span class="text-red-500">*</span>
                    </label>
                    <select id="hari" name="hari"
                        class="select select-bordered w-full rounded-lg @error('hari') select-error @enderror" required>
                        <option value="">Pilih hari</option>
                        @foreach($hariList as $hari)
                        <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label for="jam_mulai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input id="jam_mulai" type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                            class="input input-bordered w-full rounded-lg @error('jam_mulai') input-error @enderror" required>
                        @error('jam_mulai')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-semibold text-slate-700 mb-1">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input id="jam_selesai" type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                            class="input input-bordered w-full rounded-lg @error('jam_selesai') input-error @enderror" required>
                        @error('jam_selesai')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-8">
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="select select-bordered w-full rounded-lg @error('status') select-error @enderror" required>
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-sm font-semibold transition">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <a href="{{ route('jadwal-periksa.index') }}"
                        class="flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg text-sm font-semibold transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
