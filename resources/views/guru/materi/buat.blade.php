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
                <h2 class="text-xl font-extrabold text-gray-800">Buat materi</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ $bab->title }} · {{ $kelas->name }}</p>
            </div>
            <a href="{{ route('guru.bab.tampil', $bab) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Kembali ke bab
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-3xl border-t-4 border-[#ed1e28] bg-white p-6 shadow-[0_20px_50px_-24px_rgba(15,23,42,0.18)] ring-1 ring-gray-100 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_28px_60px_-24px_rgba(237,30,40,0.12)] sm:p-8"
                x-data="{ 
                    tipe: @js(old('tipe', 'teks')),
                    openAiModal: false,
                    aiPrompt: '',
                    aiLoading: false,
                    sisaKuota: null,
                    batasKuota: 5,
                    async init() {
                        try {
                            const res = await fetch('{{ route('guru.ai.sisa-kuota') }}');
                            const d = await res.json();
                            this.sisaKuota = d.sisa;
                            this.batasKuota = d.batas;
                        } catch(e) {}
                    },
                    async generateAiMateri() {
                        if (!this.aiPrompt.trim()) return;
                        if (this.sisaKuota !== null && this.sisaKuota <= 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Kuota Habis',
                                text: 'Kuota AI harian Anda telah habis (' + this.batasKuota + ' kali/hari). Silakan coba lagi besok.',
                                confirmButtonColor: '#ed1e28'
                            });
                            return;
                        }
                        this.aiLoading = true;
                        try {
                            const response = await fetch('{{ route('guru.ai.generate-materi') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify({ poin_inti: this.aiPrompt })
                            });
                            const data = await response.json();
                            if (data.sisa !== undefined) this.sisaKuota = data.sisa;
                            if (data.batas !== undefined) this.batasKuota = data.batas;
                            if (response.status === 429) {
                                this.sisaKuota = 0;
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Kuota Habis',
                                    text: data.pesan || 'Kuota AI harian Anda telah habis.',
                                    confirmButtonColor: '#ed1e28'
                                });
                                return;
                            }
                            if (data.success) {
                                this.openAiModal = false;
                                this.aiPrompt = '';
                                const trixEditor = document.querySelector('trix-editor');
                                if (trixEditor && trixEditor.editor) {
                                    trixEditor.editor.insertHTML(data.html);
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Materi AI berhasil disisipkan. (Sisa kuota: ' + this.sisaKuota + '/' + this.batasKuota + ')',
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Peringatan AI',
                                    text: data.pesan || 'Gagal terhubung ke AI.',
                                    confirmButtonColor: '#ed1e28'
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan jaringan atau server kepenuhan (Error 503).',
                                confirmButtonColor: '#ed1e28'
                            });
                        } finally {
                            this.aiLoading = false;
                        }
                    }
                }"
            >
                <form action="{{ route('guru.materi.simpan', $bab) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="order" value="{{ old('order', $urutanBerikutnya) }}">

                    <div>
                        <label for="judul" class="mb-2 block text-sm font-bold text-gray-700">Judul materi</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required minlength="3" maxlength="255" class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        @error('judul')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="ringkasan" class="mb-2 block text-sm font-bold text-gray-700">Ringkasan <span class="font-normal text-gray-400">(opsional)</span></label>
                        <textarea name="ringkasan" id="ringkasan" rows="2" maxlength="1000" class="form-textarea mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">{{ old('ringkasan') }}</textarea>
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
                        <div class="flex items-center justify-between mb-2">
                            <label for="isi_teks_hidden" class="block text-sm font-bold text-gray-700">Isi materi</label>
                            <button type="button" @click="openAiModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-colors shadow-sm border border-indigo-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Tulis dengan AI
                            </button>
                        </div>
                        <input id="isi_teks_hidden" type="hidden" name="konten_teks" value="{{ old('konten_teks', '') }}">
                        <trix-editor input="isi_teks_hidden" class="trix-content rounded-xl border border-gray-200 bg-white"></trix-editor>
                        @error('konten_teks')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                        <p class="mt-2 text-xs text-gray-500">Editor Trix menyimpan format tebal, daftar, dan tautan sebagai HTML aman.</p>
                    </div>

                    <div x-show="tipe === 'video'" x-cloak x-transition>
                        <label for="link_video" class="mb-2 block text-sm font-bold text-gray-700">URL YouTube</label>
                        <input type="url" name="link_video" id="link_video" value="{{ old('link_video') }}" placeholder="https://www.youtube.com/watch?v=…" class="form-input mt-0 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        @error('link_video')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="tipe === 'pdf'" x-cloak x-transition>
                        <label for="berkas_pdf" class="mb-2 block text-sm font-bold text-gray-700">Unggah PDF</label>
                        <input type="file" name="berkas_pdf" id="berkas_pdf" accept="application/pdf,.pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-red-light file:px-4 file:py-2 file:text-sm file:font-bold file:text-[#ed1e28]">
                        @error('berkas_pdf')<p class="mt-1 text-xs text-[#ed1e28]">{{ $message }}</p>@enderror
                        <p class="mt-2 text-xs text-gray-500">Maks. 12 MB. Pastikan <code class="rounded bg-gray-100 px-1">php artisan storage:link</code> sudah dijalankan di server.</p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end sm:gap-3">
                        <a href="{{ route('guru.bab.tampil', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-6 text-sm font-semibold text-gray-800 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#ed1e28] px-8 text-sm font-bold text-white shadow-md transition hover:bg-[#c91a23]">Simpan materi</button>
                    </div>
                </form>

                <!-- Modal AI Materi diletakkan di DALAM scope x-data container utama -->
                <div x-show="openAiModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                    <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div x-show="openAiModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                        <div x-show="openAiModal" x-transition.scale class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-extrabold leading-6 text-gray-900" id="modal-title">Asisten Penulis AI</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 mb-3">Ketikkan poin-poin inti yang ingin Anda sampaikan. AI akan mengembangkannya menjadi paragraf materi yang profesional dan edukatif.</p>
                                            <div x-show="sisaKuota !== null" class="mb-3 flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold" :class="sisaKuota > 0 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-red-50 text-red-700 ring-1 ring-red-200'">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                    Sisa kuota: <span x-text="sisaKuota"></span>/<span x-text="batasKuota"></span> hari ini
                                                </span>
                                            </div>
                                            <textarea x-model="aiPrompt" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Jelaskan pengertian rasio, berikan analogi membuat sirup (air vs sirup 3:1), dan berikan contoh soal sederhananya." :disabled="sisaKuota !== null && sisaKuota <= 0"></textarea>
                                            <p x-show="sisaKuota !== null && sisaKuota <= 0" class="mt-1 text-xs text-red-600 font-bold">Kuota harian Anda sudah habis. Silakan coba lagi besok.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" @click="generateAiMateri" :disabled="aiLoading" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2 text-base font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    <span x-show="!aiLoading">Generate Materi</span>
                                    <span x-show="aiLoading" class="flex items-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Memproses...
                                    </span>
                                </button>
                                <button type="button" @click="openAiModal = false" :disabled="aiLoading" class="mt-3 inline-flex w-full justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
