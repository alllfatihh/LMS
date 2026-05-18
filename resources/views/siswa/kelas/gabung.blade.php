<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-gray-800 leading-tight">
            {{ __('Bergabung dengan Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red">
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <div class="mx-auto w-16 h-16 bg-brand-red-light rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-800">Halo, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-500 mt-2 text-sm leading-relaxed">Kamu belum bergabung ke kelas manapun.<br>Masukkan <strong>Kode Kelas</strong> dari Gurumu di bawah ini.</p>
                    </div>

                    @if(session('error'))
                        <div class="bg-brand-red-light border border-red-200 text-brand-red p-4 mb-6 rounded-xl flex items-start space-x-3" role="alert">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="font-bold text-sm">Gagal!</p>
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('siswa.gabung-kelas.simpan') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="class_code" class="block text-sm font-bold text-gray-700 mb-2">Kode Kelas</label>
                            <input type="text" name="class_code" id="class_code" 
                                class="focus:ring-brand-red focus:border-brand-red block w-full text-2xl border-gray-200 rounded-2xl p-5 text-center tracking-[0.3em] font-mono uppercase bg-gray-50 shadow-inner" 
                                placeholder="X123YZ" required maxlength="10" autofocus>
                            @error('class_code')
                                <p class="text-brand-red text-sm mt-2 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-brand-red-darker text-white font-bold py-4 px-4 rounded-lg shadow-md hover:shadow-brand-glow transition-all duration-200 text-lg flex justify-center items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Bergabung Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
