<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">Tambah Soal Dinamis</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $bab->kelas->name }} — {{ $bab->title }}</p>
            </div>
            <a href="{{ route('guru.kelas.tampil', $bab->kelas->id) }}" class="flex items-center space-x-2 text-brand-red text-sm font-semibold border border-brand-red px-4 py-2 rounded-lg hover:bg-brand-red-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Kelas</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="questionEditor({})">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red p-0 flex flex-col lg:flex-row">
                
                <!-- Kiri: Form Input -->
                <div class="w-full lg:w-2/3 p-8 border-r border-gray-100">

                    <div class="flex items-center space-x-3 mb-8">
                        <div class="w-10 h-10 bg-brand-red-light rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-800">Buat Soal Baru</h3>
                            <p class="text-xs text-gray-400">Isi template, aturan variabel, dan rumus jawaban.</p>
                        </div>
                    </div>

                    <form action="{{ route('guru.soal.simpan', $bab) }}" method="POST" id="questionForm">
                        @csrf
                        
                        <!-- Template Soal -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Teks Soal</label>
                            <p class="text-xs text-gray-400 mb-2">Gunakan kurung kurawal untuk variabel. Contoh: <code class="bg-gray-100 px-1 rounded font-mono">Berapa {x} + {y}?</code></p>
                            <textarea name="question_template" x-model="template" rows="4" class="block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" required placeholder="Ketik soal di sini..."></textarea>
                            @error('question_template')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <!-- Variabel Dinamis (Alpine.js) -->
                        <div class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Rentang Angka Variabel</label>
                                    <p class="text-xs text-gray-400 mt-0.5">Definisikan min & max untuk setiap variabel.</p>
                                </div>
                                <button type="button" @click="addVariable"
                                    class="bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-brand-red-darker text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition-all duration-200 flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah Variabel</span>
                                </button>
                            </div>
                            
                            <template x-for="(v, index) in variables" :key="index">
                                <div class="flex items-center space-x-2 mb-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="w-8 h-8 bg-brand-red-light rounded-lg flex items-center justify-center shrink-0">
                                        <span class="text-brand-red font-bold text-xs font-mono" x-text="v.name || '?'"></span>
                                    </div>
                                    <input type="text" x-model="v.name" class="w-20 border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-lg text-sm font-mono py-1.5 px-2" placeholder="x" required>
                                    <input type="hidden" :name="'variable_rules['+v.name+'][min]'" :value="v.min">
                                    <input type="hidden" :name="'variable_rules['+v.name+'][max]'" :value="v.max">
                                    <span class="text-gray-400 text-xs font-semibold">Min</span>
                                    <input type="number" x-model="v.min" class="w-20 border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-lg text-sm py-1.5 px-2" required>
                                    <span class="text-gray-400 text-xs font-semibold">Max</span>
                                    <input type="number" x-model="v.max" class="w-20 border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-lg text-sm py-1.5 px-2" required>
                                    <button type="button" @click="removeVariable(index)"
                                        class="ml-auto text-gray-300 hover:text-brand-red transition-colors p-1.5 rounded-lg hover:bg-brand-red-light">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                            <p x-show="variables.length === 0" class="text-xs text-brand-red italic text-center py-3">Belum ada variabel. Silakan tambah.</p>
                            @error('variable_rules')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <!-- Rumus Jawaban -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Rumus Jawaban Benar</label>
                            <p class="text-xs text-gray-400 mb-2">Gunakan operator <code class="bg-gray-100 px-1 rounded">+</code> <code class="bg-gray-100 px-1 rounded">-</code> <code class="bg-gray-100 px-1 rounded">*</code> <code class="bg-gray-100 px-1 rounded">/</code>. Contoh: <code class="bg-gray-100 px-1 rounded font-mono">x + y * 2</code></p>
                            <input type="text" name="answer_formula" x-model="formula" class="font-mono block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" required placeholder="Contoh: x + y">
                            @error('answer_formula')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <a href="{{ route('guru.bab.tampil', $bab) }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold py-2 transition-colors">Batal</a>
                            <div class="flex space-x-3">
                                <button type="button" @click="runPreview"
                                    class="flex items-center space-x-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg shadow transition-all duration-200">
                                    <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span x-text="isLoading ? 'Menguji...' : 'Uji Soal'"></span>
                                </button>
                                <button type="submit"
                                    class="flex items-center space-x-2 bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-brand-red-darker text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:shadow-brand-glow transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Simpan Soal</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Kanan: Preview Panel -->
                <div class="w-full lg:w-1/3 bg-gray-900 p-6">
                    <h3 class="text-white font-extrabold mb-1 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Live Preview Ujian</span>
                    </h3>
                    <p class="text-gray-500 text-xs mb-5">Klik "Uji Soal" untuk simulasi generate angka acak.</p>
                    <template x-if="!hasPreview && !previewError">
                        <div class="flex flex-col items-center justify-center text-center py-16 px-4">
                            <div class="w-14 h-14 bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-gray-500 text-sm">Tekan <strong class="text-gray-300">Uji Soal</strong> untuk melihat preview tampilan soal kepada siswa.</p>
                        </div>
                    </template>

                    <template x-if="hasPreview">
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-widest mb-2 font-semibold">Tampilan Siswa</p>
                                <div class="bg-gray-800 border border-gray-700 p-4 rounded-xl text-gray-100 text-sm leading-relaxed" x-html="previewTemplate"></div>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-widest mb-2 font-semibold">Log Variabel</p>
                                <div class="bg-black border border-gray-800 p-3 rounded-xl font-mono text-xs text-green-400 overflow-x-auto">
                                    <pre x-text="JSON.stringify(previewVariables, null, 2)"></pre>
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-widest mb-2 font-semibold">Kunci Jawaban</p>
                                <div class="bg-gradient-to-r from-brand-red to-brand-red-dark p-4 rounded-xl font-extrabold text-2xl text-center shadow text-white">
                                    <span x-text="previewAnswer"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="previewError">
                        <div class="bg-red-900/50 border border-red-700 text-red-200 p-4 rounded-xl text-sm flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="previewError"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
