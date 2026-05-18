<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold leading-tight text-gray-800">Edit Kelas</h2>
                <p class="mt-0.5 text-sm text-gray-500">Perbarui informasi kelas Anda</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('guru.kelas.tampil', $kelas) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Detail kelas
                </a>
                <a href="{{ route('guru.kelas.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#ed1e28]/30 bg-white px-4 py-2 text-sm font-semibold text-[#ed1e28] transition hover:bg-brand-red-light">
                    Semua kelas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border-t-4 border-[#ed1e28] bg-white p-8 shadow-sm">
                <div class="mb-8 flex items-center space-x-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-red-light">
                        <svg class="h-6 w-6 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-800">Detail Kelas</h3>
                        <p class="text-sm text-gray-400">Nama, tahun ajaran, dan deskripsi dapat diubah. Kode akses tidak dapat diubah.</p>
                    </div>
                </div>

                <div class="mb-6 flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div>
                        <p class="mb-0.5 text-xs font-bold uppercase tracking-wider text-gray-400">Kode Akses Kelas</p>
                        <p class="font-mono text-2xl font-extrabold tracking-widest text-[#ed1e28]">{{ $kelas->class_code }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-red-light">
                        <svg class="h-5 w-5 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <form action="{{ route('guru.kelas.perbarui', $kelas) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="name" class="mb-2 block text-sm font-bold text-gray-700">Nama Kelas</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            minlength="3"
                            maxlength="255"
                            class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]"
                            placeholder="Contoh: Matematika 7A"
                            value="{{ old('name', $kelas->name) }}"
                            required
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="tahun" class="mb-2 block text-sm font-bold text-gray-700">Tahun Ajaran</label>
                        <x-tahun-ajaran-select :selected="$kelas->tahun" />
                        <p class="mt-1.5 text-xs text-gray-400">Tahun awal tahun ajaran (misalnya 2026 untuk 2026/2027).</p>
                        @error('tahun')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label for="description" class="mb-2 block text-sm font-bold text-gray-700">
                            Deskripsi Singkat <span class="font-normal text-gray-400">(Opsional)</span>
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="form-textarea mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]"
                            placeholder="Contoh: Kelas persiapan ujian tengah semester"
                        >{{ old('description', $kelas->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-[#ed1e28]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('guru.kelas.tampil', $kelas) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-semibold text-gray-800 hover:bg-gray-50 sm:min-w-[6.5rem]">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex h-11 min-w-[10rem] items-center justify-center gap-2 rounded-xl bg-[#ed1e28] px-8 text-sm font-bold text-white shadow-md transition hover:bg-[#c91a23] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#ed1e28] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Perbarui kelas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
