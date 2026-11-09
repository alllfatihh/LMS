@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraph.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://www.desmos.com/api/v1.8/calculator.js?apiKey=dcb31709b452b1cf9dc26972add0fda6"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraphcore.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const imageInput = document.getElementById('image');
            const fileNameDisplay = document.getElementById('file-name-display');
            if (imageInput && fileNameDisplay) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                    } else {
                        fileNameDisplay.textContent = 'Belum ada file yang dipilih';
                    }
                });
            }
        });
    </script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">Ubah Soal Dinamis</h2>
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

    <div class="py-10" x-data="questionEditor(@js($editorInitial))">
        <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="w-full bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red flex flex-col lg:flex-row">
                
                <!-- Kiri: Form Input -->
                <div class="w-full lg:w-7/12 p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-gray-100 min-w-0">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 pb-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-brand-red-light rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-gray-800 text-lg">Buat Soal Dinamis Baru</h3>
                                <p class="text-xs text-gray-400">Isi template, aturan variabel, dan rumus jawaban.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <!-- Tombol Jadikan Dinamis -->
                            <button type="button" @click="openDynamicWizard = true; runDynamicWizard()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow-md transition-all">
                                🪄 Jadikan Dinamis
                            </button>
                            <!-- Tombol Preset -->
                            <div class="relative" x-data="{ openPreset: false }">
                                <button type="button" @click="openPreset = !openPreset" @click.away="openPreset = false" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Template Preset
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openPreset" x-cloak x-transition class="absolute right-0 mt-2 w-72 max-h-96 overflow-y-auto bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                    <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Geometri 2D & Sudut</div>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.pythagoras); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Pythagoras (Segitiga Siku)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.geometri_luas); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Persegi Panjang
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.geometri_trapesium); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Luas Trapesium
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.geometri_lingkaran_juring); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-pink-500"></span> Lingkaran & Juring
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.sudut_berpenyiku); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Sudut Berpenyiku (90°)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.sudut_berpelurus); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-violet-500"></span> Sudut Berpelurus (180°)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.sudut_bertolak_belakang); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span> Sudut Bertolak Belakang
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.sudut_segitiga); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sudut Segitiga (180°)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.sudut_berpelurus_rasio); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Sudut Berpelurus Rasio
                                    </button>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Bangun Ruang 3D</div>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.bangun_ruang_tabung); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Volume Tabung
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.bangun_ruang_kerucut); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-600"></span> Volume Kerucut
                                    </button>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Statistika & Aljabar</div>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.statistika_rata_rata); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-500"></span> Statistika Rata-rata (Chart)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.diagram_venn_himpunan); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Diagram Venn (Himpunan)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.garis_bilangan_suhu); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Garis Bilangan (Suhu)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.spldv); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> SPLDV (Cerita)
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.diskon); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Aritmetika Diskon
                                    </button>
                                    <button type="button" @click="loadPreset(window.SMP_PRESETS.refleksi); openPreset = false" class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-pink-500"></span> Transformasi (Refleksi X)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('guru.soal.perbarui', $soal) }}" method="POST" id="questionForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Template Soal -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-bold text-gray-700">Teks Soal</label>
                                <button type="button" @click="openMathModal = true; setTimeout(() => document.getElementById('soal_math_field').focus(), 100)" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-brand-red hover:bg-red-100 transition-colors shadow-sm border border-red-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Sisipkan Rumus
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mb-2">Gunakan kurung kurawal untuk variabel. Contoh: <code class="bg-gray-100 px-1 rounded font-mono">Berapa {x} + {y}?</code></p>
                            <textarea id="question_template" name="question_template" x-model="template" rows="4" class="block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" required placeholder="Ketik soal di sini..."></textarea>
                            @error('question_template')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <!-- Gambar Pendukung -->
                        <div class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Visual / Gambar Pendukung (Opsional)</label>
                            <p class="text-xs text-gray-400 mb-3">Tambahkan atau ubah gambar pendukung untuk soal. Format yang diizinkan: JPG, PNG, GIF, SVG (Maks. 2MB)</p>
                            
                            @if($soal->image_path)
                                <div class="mb-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-start gap-4">
                                    <div class="w-32 h-32 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0 border border-gray-200">
                                        <img src="{{ asset('storage/' . $soal->image_path) }}" alt="Gambar Soal" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex flex-col justify-center pt-2">
                                        <p class="text-sm font-bold text-gray-700">Gambar saat ini</p>
                                        <p class="text-xs text-gray-500 mb-3">{{ basename($soal->image_path) }}</p>
                                        
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-brand-red focus:ring-brand-red" @change="handleRemoveImageCheckbox">
                                            <span class="text-sm font-medium text-red-600 hover:text-red-700">Hapus gambar ini</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="image" class="relative cursor-pointer rounded-md bg-white font-medium text-brand-red hover:text-brand-red-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-brand-red focus-within:ring-offset-2">
                                            <span>Unggah file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml" @change="handleImageUpload">
                                        </label>
                                        <p class="pl-1">atau seret ke sini</p>
                                    </div>
                                    <p class="text-xs text-gray-500" id="file-name-display">Belum ada file yang dipilih</p>
                                </div>
                            </div>
                            @error('image')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <!-- Variabel Dinamis (Alpine.js) -->
                        <div class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Rentang Angka Variabel</label>
                                    <p class="text-xs text-gray-400 mt-0.5">Definisikan min & max untuk setiap variabel.</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" @click="autoDetectVariables"
                                        class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold py-2 px-3 rounded-lg shadow-sm transition-all duration-200 flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        <span>Deteksi Variabel</span>
                                    </button>
                                    <button type="button" @click="addVariable"
                                        class="bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-brand-red-darker text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition-all duration-200 flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Tambah Variabel</span>
                                    </button>
                                </div>
                            </div>
                            
                            <template x-for="(v, index) in variables" :key="index">
                                <div class="mb-4 bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-all">
                                    <!-- Baris Atas: Identitas & Tipe Variabel -->
                                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-100 mb-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <!-- Badge Variabel Dinamis (Auto-width, Anti-Tumpang-Tindih) -->
                                            <div class="inline-flex items-center justify-center px-2.5 py-1 min-w-[2.25rem] bg-red-50 border border-red-200 text-brand-red rounded-lg font-mono font-bold text-xs shrink-0 select-none">
                                                <span>{</span><span x-text="v.name || '?'"></span><span>}</span>
                                            </div>
                                            
                                            <!-- Input Nama Variabel -->
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-gray-500">Nama:</span>
                                                <input type="text" x-model="v.name" :name="'variables['+index+'][name]'" 
                                                    class="w-28 border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm font-mono font-bold py-1.5 px-2.5 bg-gray-50/50 focus:bg-white transition-all" 
                                                    placeholder="nama_var" required>
                                            </div>

                                            <!-- Tipe Variabel Dropdown -->
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-gray-500">Tipe:</span>
                                                <select x-model="v.type" :name="'variables['+index+'][type]'" 
                                                    class="bg-gray-50 hover:bg-gray-100 border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-xs font-bold py-1.5 px-3 text-gray-700 cursor-pointer transition-all">
                                                    <option value="integer">🔢 Bilangan Bulat</option>
                                                    <option value="float">📐 Bilangan Desimal</option>
                                                    <option value="derived">⚡ Turunan (Rumus)</option>
                                                    <option value="set">📦 Himpunan (Array)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Tombol Hapus Variabel -->
                                        <button type="button" @click="removeVariable(index)" title="Hapus Variabel"
                                            class="text-gray-400 hover:text-brand-red hover:bg-red-50 p-2 rounded-xl transition-all shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                    <!-- Baris Bawah: Detail Parameter Berdasarkan Tipe -->
                                    <div>
                                        <!-- Tipe Derived (Rumus Turunan) -->
                                        <template x-if="v.type === 'derived'">
                                            <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <label class="text-xs font-extrabold text-purple-900 flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        Logika Rumus Turunan:
                                                    </label>
                                                    <span class="text-[11px] text-purple-700 font-mono font-medium">Contoh: -(x1 + x2) atau 3*k</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-purple-700 text-xs font-mono font-bold shrink-0" x-text="(v.name || 'var') + ' ='"></span>
                                                    <input type="text" x-model="v.formula" :name="'variables['+index+'][formula]'" 
                                                        class="flex-1 border border-purple-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 rounded-lg text-sm font-mono py-1.5 px-3 bg-white text-purple-900 placeholder:text-purple-300 font-semibold" 
                                                        placeholder="Contoh: -(x1 + x2) atau x1 * x2" required>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Tipe Standar: Integer / Float / Set -->
                                        <template x-if="v.type !== 'derived'">
                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                                <!-- Min -->
                                                <div>
                                                    <label class="block text-[11px] font-bold text-gray-500 mb-1" x-text="v.type === 'set' ? 'Nilai Min Angka' : 'Nilai Minimum'"></label>
                                                    <input type="number" step="any" x-model="v.min" :name="'variables['+index+'][min]'" 
                                                        class="w-full border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm font-semibold py-1.5 px-2.5 bg-gray-50/50 focus:bg-white transition-all" required>
                                                </div>

                                                <!-- Max -->
                                                <div>
                                                    <label class="block text-[11px] font-bold text-gray-500 mb-1" x-text="v.type === 'set' ? 'Nilai Max Angka' : 'Nilai Maksimum'"></label>
                                                    <input type="number" step="any" x-model="v.max" :name="'variables['+index+'][max]'" 
                                                        class="w-full border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm font-semibold py-1.5 px-2.5 bg-gray-50/50 focus:bg-white transition-all" required>
                                                </div>

                                                <!-- Kelipatan (Hanya Integer) -->
                                                <template x-if="v.type === 'integer' || !v.type">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 mb-1">
                                                            Kelipatan <span class="text-gray-400 font-normal">(opsional)</span>
                                                        </label>
                                                        <input type="number" step="any" min="0" x-model="v.kelipatan" :name="'variables['+index+'][kelipatan]'" 
                                                            class="w-full border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm py-1.5 px-2.5 bg-gray-50/50 focus:bg-white font-mono transition-all" 
                                                            placeholder="cth: 2, 7">
                                                    </div>
                                                </template>

                                                <!-- Banyak Desimal (Hanya Float) -->
                                                <template x-if="v.type === 'float'">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 mb-1">
                                                            Banyak Desimal
                                                        </label>
                                                        <input type="number" min="1" max="6" x-model="v.decimals" :name="'variables['+index+'][decimals]'" 
                                                            class="w-full border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm py-1.5 px-2.5 bg-gray-50/50 focus:bg-white font-mono transition-all" 
                                                            placeholder="2">
                                                    </div>
                                                </template>

                                                <!-- Jumlah Anggota (Hanya Set) -->
                                                <template x-if="v.type === 'set'">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 mb-1">Banyak Anggota</label>
                                                        <input type="number" min="1" x-model="v.count" :name="'variables['+index+'][count]'" 
                                                            class="w-full border border-gray-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 rounded-lg text-sm py-1.5 px-2.5 bg-gray-50/50 focus:bg-white font-mono font-semibold transition-all" 
                                                            placeholder="5">
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <p x-show="variables.length === 0" class="text-xs text-brand-red italic text-center py-3">Belum ada variabel. Silakan tambah.</p>
                            @error('variables')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                            @error('variables.*.name')<p class="text-brand-red text-xs mt-1">Nama variabel wajib diisi.</p>@enderror
                        </div>

                        <!-- Rumus Jawaban -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-bold text-gray-700">Rumus Jawaban Benar</label>
                                <button type="button" @click="openFormulaMathModal = true; setTimeout(() => { const mf = document.getElementById('formula_math_field'); mf.value = ''; mf.focus(); }, 100)" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-brand-red hover:bg-red-100 transition-colors shadow-sm border border-red-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Keyboard Matematika
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mb-2">Ketik manual atau gunakan keyboard matematika. Fungsi statis seperti <code class="bg-gray-100 px-1 rounded text-gray-700">rata_rata(x)</code> hanya bisa diketik manual.</p>
                            
                            <!-- Bantuan Fungsi Lanjutan (Premium Light UI) -->
                            <div x-data="{ openHelp: false }" class="mb-5 mt-3">
                                <button type="button" @click="openHelp = !openHelp" 
                                    class="inline-flex items-center space-x-2 text-sm font-bold text-brand-red bg-red-50 hover:bg-brand-red hover:text-white transition-all duration-200 px-4 py-2 rounded-full focus:outline-none focus:ring-2 focus:ring-brand-red focus:ring-offset-1 shadow-sm border border-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="openHelp ? 'Tutup Kamus Fungsi' : 'Buka Kamus Rumus Matematika'"></span>
                                </button>

                                <div x-show="openHelp" 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                                    style="display: none;" 
                                    class="mt-4 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden border border-gray-200">
                                    
                                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                        <h4 class="text-gray-800 font-bold flex items-center">
                                            <div class="bg-brand-red/10 p-1.5 rounded-lg mr-2.5">
                                                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </div>
                                            Kamus Fungsi (Math Engine)
                                        </h4>
                                    </div>
                                    
                                    <div x-data="{ activeTab: 'kelas7' }" class="p-5">
                                        {{-- Tab Navigation --}}
                                        <div class="flex flex-wrap gap-2 mb-5 border-b border-gray-200 pb-3">
                                            <button type="button" @click="activeTab = 'kelas7'" :class="activeTab === 'kelas7' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200">📐 Kelas 7</button>
                                            <button type="button" @click="activeTab = 'kelas8'" :class="activeTab === 'kelas8' ? 'bg-purple-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200">⚡ Kelas 8</button>
                                            <button type="button" @click="activeTab = 'kelas9'" :class="activeTab === 'kelas9' ? 'bg-pink-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200">🎯 Kelas 9</button>
                                            <button type="button" @click="activeTab = 'bangun'" :class="activeTab === 'bangun' ? 'bg-teal-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200">🧊 Bangun Ruang</button>
                                            <button type="button" @click="activeTab = 'statistika'" :class="activeTab === 'statistika' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200">📊 Statistika & Lainnya</button>
                                        </div>

                                        {{-- Reusable row style: grid with fixed columns --}}
                                        <style>
                                            .rumus-row { display: grid; grid-template-columns: minmax(0, 1fr) 20px minmax(0, 1fr); align-items: center; padding: 6px 12px; background: white; border-radius: 8px; border: 1px solid #f3f4f6; gap: 0; }
                                            .rumus-row code { font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace; font-size: 11px; font-weight: 700; color: #dc2626; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                                            .rumus-row .rumus-arrow { text-align: center; color: #d1d5db; font-size: 10px; flex-shrink: 0; }
                                            .rumus-row .rumus-desc { font-size: 11px; color: #6b7280; font-weight: 500; text-align: right; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                                        </style>

                                        {{-- ═══════ TAB: KELAS 7 ═══════ --}}
                                        <div x-show="activeTab === 'kelas7'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                {{-- Bilangan & Rasio --}}
                                                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-5 border border-blue-100">
                                                    <h5 class="font-bold text-sm text-blue-700 mb-4 flex items-center">
                                                        <span class="bg-blue-100 text-blue-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🔢</span>
                                                        Bilangan & Rasio
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>fpb(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">FPB</span></div>
                                                        <div class="rumus-row"><code>kpk(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">KPK</span></div>
                                                        <div class="rumus-row"><code>pangkat(a, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">aⁿ</span></div>
                                                        <div class="rumus-row"><code>rasio(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Rasio a:b</span></div>
                                                        <div class="rumus-row"><code>skala(jarak, skala)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Jarak Sebenarnya</span></div>
                                                        <div class="rumus-row"><code>perbandingan_senilai(a1, b1, a2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Senilai</span></div>
                                                        <div class="rumus-row"><code>perbandingan_berbalik(a1, b1, a2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Berbalik Nilai</span></div>
                                                        <div class="rumus-row"><code>pecahan(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Pecahan a/b</span></div>
                                                        <div class="rumus-row"><code>persen(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Persentase</span></div>
                                                        <div class="rumus-row"><code>diskon(harga, persen)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Aritmetika Sosial</span></div>
                                                        <div class="rumus-row"><code>substitusi_linear(a, x, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Nilai ax + b</span></div>
                                                    </div>
                                                </div>
                                                {{-- Geometri Datar & Sudut --}}
                                                <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl p-5 border border-emerald-100">
                                                    <h5 class="font-bold text-sm text-emerald-700 mb-4 flex items-center">
                                                        <span class="bg-emerald-100 text-emerald-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">📐</span>
                                                        Geometri Datar & Sudut
                                                    </h5>
                                                    <div class="space-y-1.5 max-h-80 overflow-y-auto pr-1 custom-scrollbar">
                                                        <div class="rumus-row"><code>sudut_pelurus(a)</code><span class="rumus-arrow">→</span><span class="rumus-desc">180° − a</span></div>
                                                        <div class="rumus-row"><code>sudut_penyiku(a)</code><span class="rumus-arrow">→</span><span class="rumus-desc">90° − a</span></div>
                                                        <div class="rumus-row"><code>luas_persegi(s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">s²</span></div>
                                                        <div class="rumus-row"><code>keliling_persegi(s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">4s</span></div>
                                                        <div class="rumus-row"><code>luas_persegi_panjang(p, l)</code><span class="rumus-arrow">→</span><span class="rumus-desc">p × l</span></div>
                                                        <div class="rumus-row"><code>keliling_persegi_panjang(p, l)</code><span class="rumus-arrow">→</span><span class="rumus-desc">2(p + l)</span></div>
                                                        <div class="rumus-row"><code>luas_segitiga(a, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">½ × a × t</span></div>
                                                        <div class="rumus-row"><code>luas_trapesium(a, b, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">½(a+b) × t</span></div>
                                                        <div class="rumus-row"><code>luas_jajar_genjang(a, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">a × t</span></div>
                                                        <div class="rumus-row"><code>luas_belah_ketupat(d1, d2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">½ × d₁ × d₂</span></div>
                                                        <div class="rumus-row"><code>luas_layang_layang(d1, d2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">½ × d₁ × d₂</span></div>
                                                        <div class="rumus-row"><code>luas_lingkaran(r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Luas Lingkaran πr²</span></div>
                                                        <div class="rumus-row"><code>keliling_lingkaran(r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Keliling Lingkaran 2πr</span></div>
                                                        <div class="rumus-row"><code>panjang_busur(r, sudut)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Busur Lingkaran</span></div>
                                                        <div class="rumus-row"><code>luas_juring(r, sudut)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Juring Lingkaran</span></div>
                                                        <div class="rumus-row"><code>sisi_sebangun(a, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Kesebangunan</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ═══════ TAB: KELAS 8 ═══════ --}}
                                        <div x-show="activeTab === 'kelas8'" style="display:none" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                {{-- Pythagoras & Bilangan --}}
                                                <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-5 border border-purple-100">
                                                    <h5 class="font-bold text-sm text-purple-700 mb-4 flex items-center">
                                                        <span class="bg-purple-100 text-purple-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">📏</span>
                                                        Pythagoras & Bilangan
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>hipotenusa(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Sisi Miring</span></div>
                                                        <div class="rumus-row"><code>sisi_siku_siku(c, a)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Sisi Siku Pythagoras</span></div>
                                                        <div class="rumus-row"><code>bentuk_baku(a, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">a × 10ⁿ</span></div>
                                                        <div class="rumus-row"><code>sederhanakan_akar(n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">√n → a√b</span></div>
                                                        <div class="rumus-row"><code>plsv(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">ax + b = 0 → x</span></div>
                                                        <div class="rumus-row"><code>fungsi_linear(a, x, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">f(x) = ax + b</span></div>
                                                    </div>
                                                </div>
                                                {{-- Persamaan Garis & Koordinat --}}
                                                <div class="bg-gradient-to-br from-violet-50 to-white rounded-xl p-5 border border-violet-100">
                                                    <h5 class="font-bold text-sm text-violet-700 mb-4 flex items-center">
                                                        <span class="bg-violet-100 text-violet-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">📈</span>
                                                        Persamaan Garis & Koordinat
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>gradien(x1, y1, x2, y2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Gradien Garis</span></div>
                                                        <div class="rumus-row"><code>gradien_dari_persamaan(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">m = −a/b</span></div>
                                                        <div class="rumus-row"><code>gradien_tegak_lurus(m)</code><span class="rumus-arrow">→</span><span class="rumus-desc">m⊥ = −1/m</span></div>
                                                        <div class="rumus-row"><code>persamaan_garis(m, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">y = mx + c</span></div>
                                                        <div class="rumus-row"><code>jarak(x1, y1, x2, y2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Jarak 2 Titik</span></div>
                                                        <div class="rumus-row"><code>titik_tengah(x1, y1, x2, y2)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Titik Tengah</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ═══════ TAB: KELAS 9 ═══════ --}}
                                        <div x-show="activeTab === 'kelas9'" style="display:none" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                {{-- Persamaan Kuadrat --}}
                                                <div class="bg-gradient-to-br from-pink-50 to-white rounded-xl p-5 border border-pink-100">
                                                    <h5 class="font-bold text-sm text-pink-700 mb-4 flex items-center">
                                                        <span class="bg-pink-100 text-pink-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🧮</span>
                                                        Persamaan Kuadrat
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>diskriminan(a, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">b² − 4ac</span></div>
                                                        <div class="rumus-row"><code>akar_kuadrat_1(a, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">x₁ Rumus ABC</span></div>
                                                        <div class="rumus-row"><code>akar_kuadrat_2(a, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">x₂ Rumus ABC</span></div>
                                                        <div class="rumus-row"><code>jumlah_akar(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Vieta: −b/a</span></div>
                                                        <div class="rumus-row"><code>hasil_kali_akar(a, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Vieta: c/a</span></div>
                                                        <div class="rumus-row"><code>nilai_fungsi_kuadrat(a, x, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">f(x) = ax² + bx + c</span></div>
                                                        <div class="rumus-row"><code>titik_puncak(a, b, c)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Puncak Parabola</span></div>
                                                        <div class="rumus-row"><code>sumbu_simetri(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">x = −b/2a</span></div>
                                                        <div class="rumus-row"><code>faktorisasi_kuadrat(p, q)</code><span class="rumus-arrow">→</span><span class="rumus-desc">(x+p)(x+q)</span></div>
                                                    </div>
                                                </div>
                                                {{-- Transformasi --}}
                                                <div class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-5 border border-amber-100">
                                                    <h5 class="font-bold text-sm text-amber-700 mb-4 flex items-center">
                                                        <span class="bg-amber-100 text-amber-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🔄</span>
                                                        Transformasi Geometri
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>translasi(x, y, a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Geser (a, b)</span></div>
                                                        <div class="rumus-row"><code>rotasi_90(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Rotasi 90°</span></div>
                                                        <div class="rumus-row"><code>rotasi_180(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Rotasi 180°</span></div>
                                                        <div class="rumus-row"><code>rotasi_270(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Rotasi 270°</span></div>
                                                        <div class="rumus-row"><code>refleksi_x(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Cermin Sumbu X</span></div>
                                                        <div class="rumus-row"><code>refleksi_y(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Cermin Sumbu Y</span></div>
                                                        <div class="rumus-row"><code>refleksi_y_sama_dengan_x(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Cermin y = x</span></div>
                                                        <div class="rumus-row"><code>refleksi_titik_asal(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Cermin O(0,0)</span></div>
                                                        <div class="rumus-row"><code>dilatasi(x, y, k)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Dilatasi Skala k</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ═══════ TAB: BANGUN RUANG ═══════ --}}
                                        <div x-show="activeTab === 'bangun'" style="display:none" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                {{-- Sisi Datar --}}
                                                <div class="bg-gradient-to-br from-teal-50 to-white rounded-xl p-5 border border-teal-100">
                                                    <h5 class="font-bold text-sm text-teal-700 mb-4 flex items-center">
                                                        <span class="bg-teal-100 text-teal-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">📦</span>
                                                        Sisi Datar (Kubus, Balok, Prisma, Limas)
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>volume_kubus(s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Kubus s³</span></div>
                                                        <div class="rumus-row"><code>luas_permukaan_kubus(s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Luas Permukaan 6s²</span></div>
                                                        <div class="rumus-row"><code>volume_balok(p, l, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Balok p×l×t</span></div>
                                                        <div class="rumus-row"><code>luas_permukaan_balok(p, l, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">LP 2(pl+pt+lt)</span></div>
                                                        <div class="rumus-row"><code>volume_prisma(alas, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Prisma</span></div>
                                                        <div class="rumus-row"><code>volume_limas(alas, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Limas</span></div>
                                                    </div>
                                                </div>
                                                {{-- Sisi Lengkung --}}
                                                <div class="bg-gradient-to-br from-cyan-50 to-white rounded-xl p-5 border border-cyan-100">
                                                    <h5 class="font-bold text-sm text-cyan-700 mb-4 flex items-center">
                                                        <span class="bg-cyan-100 text-cyan-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🔵</span>
                                                        Sisi Lengkung (Tabung, Kerucut, Bola)
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>volume_tabung(r, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Tabung πr²t</span></div>
                                                        <div class="rumus-row"><code>luas_permukaan_tabung(r, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">LP Tabung 2πr(r+t)</span></div>
                                                        <div class="rumus-row"><code>volume_kerucut(r, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Kerucut ⅓πr²t</span></div>
                                                        <div class="rumus-row"><code>luas_permukaan_kerucut(r, s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">LP Kerucut πr(r+s)</span></div>
                                                        <div class="rumus-row"><code>luas_selimut_kerucut(r, s)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Selimut Kerucut πrs</span></div>
                                                        <div class="rumus-row"><code>garis_pelukis_kerucut(r, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Garis Pelukis √(r²+t²)</span></div>
                                                        <div class="rumus-row"><code>volume_bola(r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Volume Bola ⁴⁄₃πr³</span></div>
                                                        <div class="rumus-row"><code>luas_permukaan_bola(r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">LP Bola 4πr²</span></div>
                                                        <div class="rumus-row"><code>volume_setengah_bola(r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">½ Bola ⅔πr³</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ═══════ TAB: STATISTIKA & LAINNYA ═══════ --}}
                                        <div x-show="activeTab === 'statistika'" style="display:none" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                                {{-- Statistika --}}
                                                <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-5 border border-indigo-100">
                                                    <h5 class="font-bold text-sm text-indigo-700 mb-4 flex items-center">
                                                        <span class="bg-indigo-100 text-indigo-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">📊</span>
                                                        Statistika
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>rata_rata(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Mean</span></div>
                                                        <div class="rumus-row"><code>median(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Median</span></div>
                                                        <div class="rumus-row"><code>modus(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Modus</span></div>
                                                        <div class="rumus-row"><code>jangkauan(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Range</span></div>
                                                        <div class="rumus-row"><code>kuartil(a, b, ..., k)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Q1 / Q2 / Q3</span></div>
                                                        <div class="rumus-row"><code>variansi(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Variansi</span></div>
                                                        <div class="rumus-row"><code>simpangan_baku(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Std. Deviasi</span></div>
                                                    </div>
                                                </div>
                                                {{-- Barisan & Deret --}}
                                                <div class="bg-gradient-to-br from-orange-50 to-white rounded-xl p-5 border border-orange-100">
                                                    <h5 class="font-bold text-sm text-orange-700 mb-4 flex items-center">
                                                        <span class="bg-orange-100 text-orange-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🔗</span>
                                                        Barisan & Deret
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>suku_n_aritmetika(a, b, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Suku ke-n Aritmetika</span></div>
                                                        <div class="rumus-row"><code>jumlah_n_aritmetika(a, b, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Deret Aritmetika</span></div>
                                                        <div class="rumus-row"><code>suku_n_geometri(a, r, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Suku ke-n Geometri</span></div>
                                                        <div class="rumus-row"><code>jumlah_n_geometri(a, r, n)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Deret Geometri</span></div>
                                                    </div>
                                                </div>
                                                {{-- Himpunan, Peluang & Format --}}
                                                <div class="bg-gradient-to-br from-rose-50 to-white rounded-xl p-5 border border-rose-100">
                                                    <h5 class="font-bold text-sm text-rose-700 mb-4 flex items-center">
                                                        <span class="bg-rose-100 text-rose-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🎲</span>
                                                        Himpunan & Peluang
                                                    </h5>
                                                    <div class="space-y-1.5">
                                                        <div class="rumus-row"><code>himpunan(a, b, ...)</code><span class="rumus-arrow">→</span><span class="rumus-desc">{a, b, ...}</span></div>
                                                        <div class="rumus-row"><code>irisan(A, B)</code><span class="rumus-arrow">→</span><span class="rumus-desc">A ∩ B</span></div>
                                                        <div class="rumus-row"><code>gabungan(A, B)</code><span class="rumus-arrow">→</span><span class="rumus-desc">A ∪ B</span></div>
                                                        <div class="rumus-row"><code>selisih(A, B)</code><span class="rumus-arrow">→</span><span class="rumus-desc">A − B</span></div>
                                                        <div class="rumus-row"><code>peluang(k, t)</code><span class="rumus-arrow">→</span><span class="rumus-desc">P(A) = k/t</span></div>
                                                        <div class="rumus-row"><code>kombinasi(n, r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">nCr</span></div>
                                                        <div class="rumus-row"><code>permutasi(n, r)</code><span class="rumus-arrow">→</span><span class="rumus-desc">nPr</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Utilitas (full width) --}}
                                            <div class="mt-5 bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border border-gray-200">
                                                <h5 class="font-bold text-sm text-gray-700 mb-4 flex items-center">
                                                    <span class="bg-gray-200 text-gray-600 w-7 h-7 rounded-lg flex items-center justify-center mr-2 text-xs">🛠️</span>
                                                    Utilitas & Format Output
                                                </h5>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                    <div class="rumus-row"><code>akar(x)</code><span class="rumus-arrow">→</span><span class="rumus-desc">√x</span></div>
                                                    <div class="rumus-row"><code>abs(x)</code><span class="rumus-arrow">→</span><span class="rumus-desc">|x|</span></div>
                                                    <div class="rumus-row"><code>format_titik(x, y)</code><span class="rumus-arrow">→</span><span class="rumus-desc">(x, y)</span></div>
                                                    <div class="rumus-row"><code>urutkan_naik(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Ascending</span></div>
                                                    <div class="rumus-row"><code>urutkan_turun(a, b)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Descending</span></div>
                                                    <div class="rumus-row"><code>sin(d), cos(d), tan(d)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Trigonometri</span></div>
                                                    <div class="rumus-row"><code>log(x), ln(x)</code><span class="rumus-arrow">→</span><span class="rumus-desc">Logaritma</span></div>
                                                    <div class="rumus-row"><code>pi</code><span class="rumus-arrow">→</span><span class="rumus-desc">Konstanta π</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Format Jawaban</label>
                                    <select name="jawaban_format" x-model="format" class="block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors">
                                        <option value="bulat">Bilangan Bulat (Contoh: -5, 0, 42)</option>
                                        <option value="desimal">Bilangan Desimal (Contoh: 3.14 atau 3,14)</option>
                                        <option value="angka">Angka Bebas (Bulat / Desimal)</option>
                                        <option value="pecahan">Pecahan (a/b)</option>
                                        <option value="koordinat">Koordinat (x,y)</option>
                                        <option value="himpunan">Himpunan {a,b,...}</option>
                                        <option value="aljabar">Aljabar (Misal: 2x+4)</option>
                                        <option value="akar">Bentuk Akar (Misal: 5\sqrt{2})</option>
                                    </select>
                                    @error('jawaban_format')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Bobot Soal</label>
                                    <input type="number" name="bobot" class="block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" required value="{{ old('bobot', $soal->bobot) }}" min="1">
                                    @error('bobot')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                                </div>
                                <div class="md:col-span-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Rumus Jawaban Benar</label>
                                    <input type="text" id="answer_formula_input" name="answer_formula" x-model="formula" class="font-mono block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" required placeholder="Contoh: x + y atau x/y">
                                    @error('answer_formula')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Rumus Grafik Dinamis (Opsional)</label>
                                <p class="text-xs text-gray-400 mb-2">Ditampilkan ke siswa sebagai grafik Desmos. Ketik persamaan dalam LaTeX. Contoh: <code class="bg-gray-100 px-1 rounded font-mono">y = {a}x + {b}</code></p>
                                <input type="text" name="graph_formula" x-model="graphFormula" class="font-mono block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" placeholder="Contoh: y = {m}x + {c}">
                                @error('graph_formula')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Template Pembahasan Langkah Penyelesaian (Opsional)</label>
                                <p class="text-xs text-gray-400 mb-2">Ditampilkan ke siswa setelah ujian. Gunakan kurung siku ganda untuk operasi hitung matematika, misal: <code class="bg-gray-100 px-1 rounded font-mono">Luas = [[ alas * tinggi ]]</code></p>
                                <textarea name="pembahasan_template" x-model="pembahasan" rows="4" class="block w-full border-2 border-gray-200 focus:border-brand-red focus:ring-brand-red rounded-xl text-sm py-3 px-4 transition-colors" placeholder="Langkah-langkah penyelesaian..."></textarea>
                                @error('pembahasan_template')<p class="text-brand-red text-xs mt-1.5">{{ $message }}</p>@enderror
                            </div>
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Modal MathLive untuk Teks Soal -->
                        <div x-show="openMathModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                                <div x-show="openMathModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                    <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
                                </div>
                                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                                <div x-show="openMathModal" x-transition.scale class="inline-block transform overflow-visible rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:align-middle">
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 rounded-t-2xl">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="h-6 w-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </div>
                                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                <h3 class="text-lg font-extrabold leading-6 text-gray-900">Asisten Rumus Matematika</h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500 mb-4">Gunakan keyboard matematika di bawah ini untuk mengetikkan persamaan, pecahan, atau akar. Rumus akan disisipkan ke Teks Soal.</p>
                                                    
                                                    <!-- MathLive Element -->
                                                    <div class="relative group">
                                                        <math-field 
                                                            id="soal_math_field" 
                                                            class="block w-full border-2 border-gray-200 focus-within:border-brand-red focus-within:ring-4 focus-within:ring-brand-red/20 rounded-xl py-4 px-5 text-2xl font-black text-slate-900 transition-all bg-gray-50 shadow-inner"
                                                        >
                                                        </math-field>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-2xl">
                                        <button type="button" @click="insertMathToTemplate" class="inline-flex w-full justify-center rounded-xl bg-brand-red px-4 py-2 text-base font-bold text-white shadow-sm hover:bg-brand-red-dark focus:outline-none focus:ring-2 focus:ring-brand-red focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                            Sisipkan ke Soal
                                        </button>
                                        <button type="button" @click="openMathModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal MathLive untuk Rumus Logika -->
                        <div x-show="openFormulaMathModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                                <div x-show="openFormulaMathModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                    <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
                                </div>
                                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                                <div x-show="openFormulaMathModal" x-transition.scale class="inline-block transform overflow-visible rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:align-middle">
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 rounded-t-2xl">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                <h3 class="text-lg font-extrabold leading-6 text-gray-900">Konverter Logika Rumus</h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500 mb-4">Gunakan keyboard matematika untuk membangun <b>logika jawaban benar</b>. Sistem otomatis akan menerjemahkannya ke dalam bahasa yang dipahami <i>Math Engine</i> (ASCII Math).</p>
                                                    
                                                    <!-- MathLive Element -->
                                                    <div class="relative group">
                                                        <math-field 
                                                            id="formula_math_field" 
                                                            class="block w-full border-2 border-gray-200 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/20 rounded-xl py-4 px-5 text-2xl font-black text-slate-900 transition-all bg-gray-50 shadow-inner"
                                                        >
                                                        </math-field>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-2xl">
                                        <button type="button" @click="insertMathToFormula" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-base font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                            Konversi ke Logika
                                        </button>
                                        <button type="button" @click="openFormulaMathModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Kanan: Preview Panel -->
                <div class="w-full lg:w-5/12 bg-gray-900 p-6 lg:p-8 min-w-0 flex flex-col justify-start">
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center shrink-0 border border-gray-700">
                                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-lg leading-tight">Live Preview</h3>
                                <p class="text-xs text-gray-400">Simulasi angka acak tampilan siswa.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-800 border border-gray-700 text-xs font-semibold text-gray-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Mode Siswa
                        </span>
                    </div>
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
                                <div class="bg-gray-800 border border-gray-700 p-4 rounded-xl text-gray-100 text-sm leading-relaxed">
                                    <template x-if="imagePreviewUrl">
                                        <div class="mb-4">
                                            <img :src="imagePreviewUrl" alt="Preview Gambar" class="max-w-full h-auto rounded-lg shadow">
                                        </div>
                                    </template>
                                    <div id="jxgboard-preview-soal-wrapper" class="mb-4 rounded-xl overflow-hidden border border-gray-700 shadow-sm bg-white" style="width: 100%; min-height: 220px; display: none;" x-effect="if (previewTemplate && $el) { setTimeout(() => { if (typeof window.renderGeometryVisualizer === 'function') { window.renderGeometryVisualizer('jxgboard-preview-soal', previewTemplate, '#jxgboard-preview-soal-wrapper', previewGraph); } else if (typeof window.renderCoordinateVisualizer === 'function') { window.renderCoordinateVisualizer('jxgboard-preview-soal', previewTemplate, '#jxgboard-preview-soal-wrapper'); } }, 50); }">
                                        <div id="jxgboard-preview-soal" class="w-full h-full"></div>
                                    </div>
                                    <div x-html="previewTemplate"></div>
                                </div>
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
                                    <span x-html="formattedAnswer()"></span>
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

<!-- ======================== MODAL JADIKAN DINAMIS ======================== -->
<div x-show="openDynamicWizard" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openDynamicWizard = false"></div>

    <!-- Modal Panel -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden" @click.stop>
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-5 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🪄</div>
                <div>
                    <h3 class="font-extrabold text-white text-lg">Jadikan Dinamis</h3>
                    <p class="text-emerald-100 text-xs">Paste soal statis Anda, AI akan merubahnya menjadi template dinamis otomatis</p>
                </div>
            </div>
            <button @click="openDynamicWizard = false" class="text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Body -->
        <div class="overflow-y-auto flex-1 p-6 space-y-5">

            <!-- Step 1: Input Deskripsi -->
            <div x-show="!dynamicWizardResult && !dynamicWizardLoading" class="text-center py-6">
                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">📝</span>
                </div>
                <h4 class="text-lg font-bold text-gray-800 mb-2">Gunakan Soal & Pembahasan dari Form</h4>
                <p class="text-sm text-gray-500 mb-6">Sistem AI akan membaca teks yang telah Anda masukkan ke dalam kotak <strong>Teks Soal</strong> dan <strong>Template Pembahasan</strong>, lalu meracik Rumus Jawaban Benar serta Aturan Variabel secara otomatis.</p>
            </div>

            <!-- Loading State -->
            <div x-show="dynamicWizardLoading" class="flex flex-col items-center justify-center py-10 gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 flex items-center justify-center animate-pulse">
                    <svg class="w-7 h-7 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
                <div class="text-center">
                    <p class="font-bold text-gray-700">AI sedang memproses soal Anda...</p>
                    <p class="text-sm text-gray-400 mt-1">Mengidentifikasi angka dan membuat variabel (5-15 detik)</p>
                </div>
            </div>

            <!-- Result State -->
            <div x-show="dynamicWizardResult && !dynamicWizardLoading" class="space-y-4">
                <!-- Penjelasan AI -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-start gap-2">
                        <span class="text-lg flex-shrink-0">🤖</span>
                        <div>
                            <p class="text-xs font-bold text-emerald-700 mb-1">Penjelasan AI:</p>
                            <p class="text-sm text-emerald-800" x-text="dynamicWizardResult?.penjelasan"></p>
                        </div>
                    </div>
                    <template x-if="dynamicWizardResult?.peringatan">
                        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-2">
                            <span class="text-yellow-500">⚠️</span>
                            <p class="text-xs text-yellow-700" x-text="dynamicWizardResult?.peringatan"></p>
                        </div>
                    </template>
                </div>

                <!-- Preview Hasil -->
                <div class="bg-gray-50 rounded-xl border border-gray-200 divide-y divide-gray-200">
                    <div class="p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Teks Soal</p>
                        <p class="text-sm font-medium text-gray-800 break-words" x-text="dynamicWizardResult?.question_template"></p>
                    </div>
                    <div class="p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Rumus Jawaban</p>
                        <code class="text-sm text-brand-red font-mono" x-text="dynamicWizardResult?.answer_formula"></code>
                    </div>
                </div>

                <p class="text-xs text-gray-400 text-center">Klik <strong>Terapkan ke Form</strong> untuk mengisi kolom secara otomatis.</p>
            </div>

            <!-- Error State -->
            <div x-show="dynamicWizardError && !dynamicWizardLoading" class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="font-bold text-red-700 text-sm">Terjadi Kesalahan</p>
                    <p class="text-sm text-red-600 mt-1" x-text="dynamicWizardError"></p>
                </div>
            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex-shrink-0 border-t border-gray-100 p-4 flex justify-between items-center gap-3 bg-gray-50">
            <div>
                <template x-if="dynamicWizardResult">
                    <button type="button" @click="dynamicWizardResult = null; dynamicWizardError = null;"
                        class="text-sm text-gray-500 hover:text-gray-700 font-semibold flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Coba Lagi
                    </button>
                </template>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="openDynamicWizard = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">Tutup</button>
                <template x-if="!dynamicWizardResult">
                    <button type="button" @click="runDynamicWizard()"
                        :disabled="!template || template.trim().length < 5 || dynamicWizardLoading"
                        class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Generate
                    </button>
                </template>
                <template x-if="dynamicWizardResult">
                    <button type="button" @click="applyDynamicWizard()"
                        class="px-5 py-2 bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Terapkan ke Form
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

</div>

</x-app-layout>
