<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-extrabold leading-tight text-gray-800">{{ __('Buat Kelas Baru') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border-t-4 border-[#ed1e28] bg-white p-8 shadow-sm">
                <div class="mb-8 flex items-center space-x-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-red-light">
                        <svg class="h-6 w-6 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-800">Detail Kelas</h3>
                        <p class="text-sm text-gray-400">Isi informasi kelas. Kode akses 6 karakter akan dibuat otomatis.</p>
                    </div>
                </div>
                <form action="{{ route('guru.kelas.simpan') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="name" class="mb-2 block text-sm font-bold text-gray-700">Nama Kelas</label>
                        <input type="text" name="name" id="name" minlength="3" maxlength="255" class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]" placeholder="Contoh: Matematika 7A" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="tahun" class="mb-2 block text-sm font-bold text-gray-700">Tahun Ajaran</label>
                        <x-tahun-ajaran-select />
                        <p class="mt-1.5 text-xs text-gray-400">Pilih tahun awal tahun ajaran (misalnya 2026 untuk 2026/2027).</p>
                        @error('tahun')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-8">
                        <label for="description" class="mb-2 block text-sm font-bold text-gray-700">Deskripsi Singkat <span class="font-normal text-gray-400">(Opsional)</span></label>
                        <textarea name="description" id="description" rows="3" class="form-textarea mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]" placeholder="Contoh: Kelas persiapan ujian tengah semester">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('guru.kelas.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-semibold text-gray-800 hover:bg-gray-50 sm:min-w-[6.5rem]">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex h-11 min-w-[10rem] items-center justify-center gap-2 rounded-xl bg-[#ed1e28] px-6 text-sm font-bold text-white shadow-md ring-1 ring-black/10 transition hover:bg-[#c91a23] hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#ed1e28] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Simpan kelas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
