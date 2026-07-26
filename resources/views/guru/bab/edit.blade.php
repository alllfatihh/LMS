<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-gray-800 leading-tight">Ubah bab</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red p-8">
                <form action="{{ route('guru.bab.perbarui', $bab) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul bab</label>
                        <input type="text" name="title" id="title" class="focus:ring-brand-red focus:border-brand-red block w-full text-sm border-gray-200 rounded-xl shadow-sm py-3 px-4" value="{{ old('title', $bab->title) }}" required>
                        @error('title')
                            <p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="order" class="block text-sm font-bold text-gray-700 mb-2">Urutan</label>
                        <input type="number" name="order" id="order" class="focus:ring-brand-red focus:border-brand-red block w-full text-sm border-gray-200 rounded-xl shadow-sm py-3 px-4" value="{{ old('order', $bab->order) }}" required min="1">
                        @error('order')
                            <p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-8">
                        <label for="durasi" class="block text-sm font-bold text-gray-700 mb-2">Durasi Kuis (menit)</label>
                        <input type="number" name="durasi" id="durasi" class="focus:ring-brand-red focus:border-brand-red block w-full text-sm border-gray-200 rounded-xl shadow-sm py-3 px-4" value="{{ old('durasi', $bab->durasi) }}" required min="1">
                        @error('durasi')
                            <p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('guru.bab.tampil', $bab) }}" class="text-brand-red font-medium text-sm px-5 py-2.5 rounded-lg border border-brand-red hover:bg-brand-red-light transition">Batal</a>
                        <button type="submit" class="bg-gradient-to-r from-brand-red to-brand-red-dark text-white font-bold py-3 px-8 rounded-lg shadow-md">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
