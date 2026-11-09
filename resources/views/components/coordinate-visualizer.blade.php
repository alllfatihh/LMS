@props(['soal', 'teks'])

@php
    $boardId = 'jsxboard_' . uniqid();
    $wrapperId = 'coordviz_' . $boardId;
    $shouldRender = false;

    $graphFormula = $soal->graph_rendered ?? ($soal->variable_rules['graph_formula'] ?? null);
    $vizText = $teks ?? '';

    if ($soal && ($soal->jawaban_format === 'koordinat' || !empty($graphFormula))) {
        $shouldRender = true;
    } elseif (!empty($vizText) && preg_match('/berpenyiku|penyiku|berpelurus|pelurus|bertolak\s*belakang|berpotongan|komplemen|suplemen|segitiga|pythagoras|persegi|panjang|trapesium|jajar\s*genjang|belah\s*ketupat|layang|lingkaran|juring|busur|tabung|kerucut|bola|balok|kubus|prisma|limas|diagram\s*batang|diagram\s*venn|diagram\s*garis|garis\s*bilangan|rata-rata|median|modus|himpunan|koordinat|translasi|refleksi|rotasi|dilatasi|gradien|parabola|fungsi\s*kuadrat|\([A-Za-z0-9\s,\.-]+\)/i', $vizText)) {
        $shouldRender = true;
    }
@endphp

@if($shouldRender)
    <div id="{{ $wrapperId }}" class="mt-6 mb-4 rounded-2xl border-2 border-slate-100 p-4 bg-slate-50/50">
        <div class="flex items-center gap-2 mb-3 px-2">
            <svg class="h-4 w-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
            <span class="viz-title text-xs font-black uppercase tracking-widest text-slate-500">Visualisasi Geometri & Koordinat</span>
        </div>

        <div id="{{ $boardId }}" class="jxgbox w-full mx-auto rounded-xl shadow-inner border border-slate-200 bg-white"
            style="min-height: 220px;"></div>
    </div>

    <!-- Load Desmos & JSXGraph CSS & JS once per page if needed -->
    <script src="https://www.desmos.com/api/v1.8/calculator.js?apiKey=dcb31709b452b1cf9dc26972add0fda6"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraph.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraphcore.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.renderGeometryVisualizer === 'function') {
                window.renderGeometryVisualizer('{{ $boardId }}', @json($vizText), '#{{ $wrapperId }}', @json($graphFormula));
            } else if (typeof window.renderCoordinateVisualizer === 'function') {
                window.renderCoordinateVisualizer('{{ $boardId }}', @json($vizText), '#{{ $wrapperId }}');
            }
        });
    </script>
@endif