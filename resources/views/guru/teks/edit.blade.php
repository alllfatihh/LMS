@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.min.css">
    <style>
        trix-toolbar .trix-button-group { border-color: #e5e7eb; }
        trix-editor { min-height: 250px; border-radius: 0.75rem; border-color: #e5e7eb !important; background: white; }
        trix-editor:focus { border-color: #ed1e28 !important; box-shadow: 0 0 0 2px rgba(237, 30, 40, 0.15); }
        .trix-button-group--file-tools { display: none !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.umd.min.js" defer></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">Ringkasan Bab: {{ $bab->title }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Edit teks pengantar yang akan muncul di halaman siswa.</p>
            </div>
            <a href="{{ route('guru.bab.tampil', $bab->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Batal</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 p-6 sm:p-8">
                <form action="{{ route('guru.teks.perbarui', $bab->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="content_hidden" class="block text-sm font-bold text-gray-700 mb-3">Teks Pengantar / Ringkasan</label>
                        <input id="content_hidden" type="hidden" name="content" value="{{ old('content', optional($teksBab)->content ?? '') }}">
                        <trix-editor input="content_hidden" class="trix-content focus:outline-none"></trix-editor>
                        @error('content')
                            <p class="mt-2 text-xs font-bold text-brand-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-brand-red to-brand-red-dark text-white font-bold py-3 px-10 rounded-xl shadow-md hover:shadow-brand-glow transition-all duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
