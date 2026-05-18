@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.min.css">
    <style>
        trix-toolbar .trix-button-group { border-color: #e5e7eb; }
        trix-editor { min-height: 220px; border-radius: 0.75rem; border-color: #e5e7eb !important; }
        trix-editor:focus { border-color: #ed1e28 !important; box-shadow: 0 0 0 2px rgba(237, 30, 40, 0.15); }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.umd.min.js" defer></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-800">Edit materi</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ $kelas->name }} — {{ $materi->judul }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('siswa.materi.tampil', $materi->slug) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Pratinjau</a>
                @if($bab ?? null)
                    <a href="{{ route('guru.bab.tampil', $bab) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Kembali ke bab</a>
                @else
                    <a href="{{ route('guru.kelas.tampil', $kelas) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Kembali ke kelas</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-3xl border-t-4 border-[#ed1e28] bg-white p-6 shadow-[0_20px_50px_-24px_rgba(15,23,42,0.18)] ring-1 ring-gray-100 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_28px_60px_-24px_rgba(237,30,40,0.12)] sm:p-8"
                x-data="{ tipe: @js(old('tipe', $materi->tipe)) }"
            >
                <form action="{{ route('guru.materi.perbarui', $materi) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order" value="{{ old('order', $materi->order) }}">

                    @if($errors->any())
                        <div class="rounded-xl bg-red-50 p-4 ring-1 ring-red-200 mb-6">
                            <ul class="list-disc list-inside text-xs text-red-700 font-bold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="judul" class="mb-2 block text-sm font-bold text-gray-700">Judul materi</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul', $materi->judul) }}" required minlength="3" maxlength="255" class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        @error('judul')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="ringkasan" class="mb-2 block text-sm font-bold text-gray-700">Ringkasan <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea name="ringkasan" id="ringkasan" rows="2" maxlength="1000" class="form-textarea mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">{{ old('ringkasan', $materi->ringkasan) }}</textarea>
                        @error('ringkasan')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <span class="mb-2 block text-sm font-bold text-gray-700">Format materi</span>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-gray-100 p-4 transition hover:border-[#ed1e28]/40 has-[:checked]:border-[#ed1e28] has-[:checked]:bg-brand-red-light/50">
                                <input type="radio" name="tipe" value="teks" class="text-[#ed1e28] focus:ring-[#ed1e28]" x-model="tipe">
                                <span class="text-sm font-bold text-gray-800">Teks (Trix)</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-gray-100 p-4 transition hover:border-[#ed1e28]/40 has-[:checked]:border-[#ed1e28] has-[:checked]:bg-brand-red-light/50">
                                <input type="radio" name="tipe" value="video" class="text-[#ed1e28] focus:ring-[#ed1e28]" x-model="tipe">
                                <span class="text-sm font-bold text-gray-800">YouTube</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-gray-100 p-4 transition hover:border-[#ed1e28]/40 has-[:checked]:border-[#ed1e28] has-[:checked]:bg-brand-red-light/50">
                                <input type="radio" name="tipe" value="pdf" class="text-[#ed1e28] focus:ring-[#ed1e28]" x-model="tipe">
                                <span class="text-sm font-bold text-gray-800">PDF</span>
                            </label>
                        </div>
                        @error('tipe')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="tipe === 'teks'" x-cloak x-transition>
                        <label for="isi_teks_hidden" class="mb-2 block text-sm font-bold text-gray-700">Isi materi</label>
                        <input id="isi_teks_hidden" type="hidden" name="konten_teks" value="{{ old('konten_teks', $materi->konten_teks ?? '') }}">
                        <trix-editor input="isi_teks_hidden" class="trix-content rounded-xl border border-gray-200 bg-white"></trix-editor>
                        @error('konten_teks')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="tipe === 'video'" x-cloak>
                        <label for="link_video" class="mb-2 block text-sm font-bold text-gray-700">URL YouTube</label>
                        <input type="text" name="link_video" id="link_video" value="{{ old('link_video', $materi->link_video) }}" placeholder="Contoh: https://www.youtube.com/watch?v=..." class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        @error('link_video')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="tipe === 'pdf'" x-cloak x-transition>
                        <label for="berkas_pdf" class="mb-2 block text-sm font-bold text-gray-700">Ganti PDF <span class="font-normal text-gray-400">(opsional)</span></label>
                        @if($materi->tipe === \App\Models\Materi::TIPE_PDF && filled($materi->file_path))
                            <p class="mb-2 text-xs text-gray-500">Berkas saat ini: <span class="font-mono">{{ basename($materi->file_path) }}</span></p>
                        @endif
                        <input type="file" name="berkas_pdf" id="berkas_pdf" accept="application/pdf,.pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-red-light file:px-4 file:py-2 file:text-sm file:font-bold file:text-[#ed1e28]">
                        @error('berkas_pdf')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end sm:gap-3">
                        @if($bab ?? null)
                            <a href="{{ route('guru.bab.tampil', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-6 text-sm font-semibold text-gray-800 hover:bg-gray-50">Batal</a>
                        @else
                            <a href="{{ route('guru.kelas.tampil', $kelas) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-6 text-sm font-semibold text-gray-800 hover:bg-gray-50">Batal</a>
                        @endif
                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#ed1e28] px-8 text-sm font-bold text-white shadow-md transition hover:bg-[#c91a23]">Perbarui materi</button>
                    </div>
                </form>

                <form action="{{ route('guru.materi.hapus', $materi) }}" method="POST" class="mt-6 border-t border-dashed border-gray-200 pt-4 text-center" onsubmit="return confirm('Hapus materi ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-bold text-[#ed1e28] underline decoration-2 underline-offset-2 hover:text-[#c91a23]">Hapus materi</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
