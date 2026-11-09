import './bootstrap';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import Alpine from 'alpinejs';

window.Swal = Swal;

window.Alpine = Alpine;

/**
 * Sumber tunggal logika visualisasi titik koordinat (dipakai oleh komponen
 * <x-coordinate-visualizer> untuk soal tersimpan, DAN oleh live preview saat guru
 * membuat/mengedit soal) supaya keduanya selalu konsisten, tidak ada 2 salinan kode
 * yang bisa saling drift. Mem-parsing pola "A(2, 3)" atau "(2, 3)" dari teks soal.
 *
 * `wrapperSelector` (opsional) adalah elemen pembungkus (header + kotak grafik) yang
 * disembunyikan total kalau tidak ada satu pun koordinat ditemukan di teks — supaya soal
 * yang formatnya "koordinat" tapi teksnya tidak menyebut titik literal (mis. soal titik
 * puncak parabola yang jawabannya koordinat tapi cuma berisi rumus fungsi) tidak
 * menampilkan kotak grid kosong yang membingungkan siswa.
 */
window.renderCoordinateVisualizer = function (containerId, text, wrapperSelector) {
    const el = document.getElementById(containerId);
    if (!el) {
        return 0;
    }

    el.innerHTML = '';

    const coordRegex = /([A-Z])?\s*\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/g;
    const points = [];
    let minX = -10, maxX = 10, minY = -10, maxY = 10;
    let match;

    while ((match = coordRegex.exec(text || '')) !== null) {
        const label = match[1] || '';
        const x = parseFloat(match[2]);
        const y = parseFloat(match[3]);

        points.push({ label, x, y });

        if (x < minX) minX = x - 2;
        if (x > maxX) maxX = x + 2;
        if (y < minY) minY = y - 2;
        if (y > maxY) maxY = y + 2;
    }

    const wrapperEl = wrapperSelector ? document.querySelector(wrapperSelector) : null;

    if (points.length === 0) {
        if (wrapperEl) wrapperEl.style.display = 'none';
        return 0;
    }

    if (wrapperEl) wrapperEl.style.display = '';

    // Prioritaskan Desmos Graphing API jika tersedia
    if (typeof Desmos !== 'undefined') {
        const calc = Desmos.GraphingCalculator(el, {
            keypad: false,
            expressions: false,
            settingsMenu: false,
            zoomButtons: true,
            lockViewport: false
        });

        points.forEach((p, index) => {
            const pointLabel = p.label ? `${p.label}(${p.x}, ${p.y})` : `P${index + 1}(${p.x}, ${p.y})`;
            calc.setExpression({
                id: 'pt_' + index,
                latex: `(${p.x}, ${p.y})`,
                label: pointLabel,
                showLabel: true,
                color: '#2563eb',
                pointSize: 9
            });
        });

        const allX = points.map(p => p.x);
        const allY = points.map(p => p.y);
        const bMinX = Math.min(...allX, 0) - 3;
        const bMaxX = Math.max(...allX, 0) + 3;
        const bMinY = Math.min(...allY, 0) - 3;
        const bMaxY = Math.max(...allY, 0) + 3;
        calc.setMathBounds({ left: bMinX, right: bMaxX, bottom: bMinY, top: bMaxY });

        return points.length;
    }

    if (typeof JXG !== 'undefined') {
        minX = Math.min(-10, minX);
        maxX = Math.max(10, maxX);
        minY = Math.min(-10, minY);
        maxY = Math.max(10, maxY);

        const board = JXG.JSXGraph.initBoard(containerId, {
            boundingbox: [minX, maxY, maxX, minY],
            axis: true,
            showCopyright: false,
            showNavigation: false,
            pan: { enabled: true },
            zoom: { enabled: true, wheel: true },
        });

        points.forEach((p, index) => {
            const pointLabel = p.label ? p.label : `P${index + 1}`;
            board.create('point', [p.x, p.y], {
                name: pointLabel,
                size: 4,
                fillColor: '#ed1e28',
                strokeColor: '#ed1e28',
                label: { offset: [10, 10], fontSize: 14, strokeColor: '#1e293b' },
            });
        });

        return points.length;
    }

    return points.length;
};

/**
 * Unified Mathematical Visualizer Engine for SMP (Kelas 7, 8, 9)
 * Mendukung visualisasi dinamis SVG & JSXGraph untuk:
 * - Geometri Sudut: Berpenyiku (90°), Berpelurus (180° & Rasio), Bertolak Belakang, Sudut Segitiga
 * - Bangun Datar 2D: Pythagoras, Persegi Panjang, Trapesium, Jajar Genjang, Lingkaran & Juring
 * - Bangun Ruang 3D: Tabung, Kerucut, Bola, Balok, Kubus, Prisma, Limas
 * - Statistika: Diagram Batang (Bar Chart) & Rata-rata Dinamis
 * - Himpunan: Diagram Venn (Irisan & Gabungan)
 * - Aljabar & Bilangan: Garis Bilangan (Operasi Bilangan Bulat / Suhu) & Parabola Fungsi Kuadrat
 */

function setVizHeader(wrapperEl, title) {
    if (!wrapperEl) return;
    wrapperEl.style.display = '';
    const titleEl = wrapperEl.querySelector('.viz-title');
    if (titleEl) titleEl.innerText = title;
}

// 1. RENDERER GARIS BILANGAN (Operasi Bilangan Bulat & Perubahan Suhu)
function tryRenderNumberLine(el, cleanText, wrapperEl) {
    const isNumLine = cleanText.match(/garis\s*bilangan|suhu|derajat\s*celcius|kedalaman|permukaan\s*laut/i);
    if (!isNumLine) return false;

    // Cari angka suhu / nilai awal dan perubahan
    const nums = [...cleanText.matchAll(/(-?\d+(?:\.\d+)?)/g)].map(m => parseFloat(m[1])).filter(n => !isNaN(n));
    if (nums.length < 2) return false;

    let startVal = nums[0];
    let changeVal = nums[1];
    let isDown = /turun|berkurang|menyelam|minus|dingin/i.test(cleanText);
    let endVal = isDown ? (startVal - changeVal) : (startVal + changeVal);

    setVizHeader(wrapperEl, `Visualisasi Garis Bilangan: Perubahan Nilai (${startVal} ${isDown ? 'turun' : 'naik'} ${changeVal})`);

    let minAxis = Math.min(startVal, endVal, -5) - 3;
    let maxAxis = Math.max(startVal, endVal, 10) + 3;
    let range = maxAxis - minAxis;
    if (range < 10) range = 10;

    const svgW = 340, svgH = 140, padX = 30, axisY = 85;
    const toSvgX = (v) => padX + ((v - minAxis) / range) * (svgW - padX * 2);

    let x1 = toSvgX(startVal);
    let x2 = toSvgX(endVal);
    let midX = (x1 + x2) / 2;
    let arcY = axisY - Math.min(45, Math.abs(x2 - x1) * 0.4 + 15);

    // Ticks
    let step = Math.max(1, Math.round(range / 10));
    let ticksSvg = '';
    for (let v = Math.ceil(minAxis); v <= Math.floor(maxAxis); v += step) {
        let tx = toSvgX(v);
        ticksSvg += `
            <line x1="${tx}" y1="${axisY - 5}" x2="${tx}" y2="${axisY + 5}" stroke="#94a3b8" stroke-width="1.5" />
            <text x="${tx}" y="${axisY + 18}" text-anchor="middle" font-family="sans-serif" font-size="10" fill="#64748b">${v}</text>
        `;
    }

    el.innerHTML = `
        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
            <svg viewBox="0 0 ${svgW} ${svgH}" class="w-full max-w-sm drop-shadow-sm select-none">
                <defs>
                    <marker id="nl-arr" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#334155" />
                    </marker>
                    <marker id="jump-arr" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="${isDown ? '#ed1e28' : '#10b981'}" />
                    </marker>
                </defs>

                <!-- Sumbu Utama -->
                <line x1="${padX - 10}" y1="${axisY}" x2="${svgW - padX + 10}" y2="${axisY}" stroke="#334155" stroke-width="2.5" marker-start="url(#nl-arr)" marker-end="url(#nl-arr)" />
                ${ticksSvg}

                <!-- Busur Lompatan -->
                <path d="M ${x1} ${axisY} Q ${midX} ${arcY} ${x2} ${axisY - 6}" fill="none" stroke="${isDown ? '#ed1e28' : '#10b981'}" stroke-width="2.5" stroke-dasharray="4,3" marker-end="url(#jump-arr)" />

                <!-- Label Perubahan -->
                <g transform="translate(${midX}, ${arcY - 6})">
                    <rect x="-30" y="-12" width="60" height="20" rx="5" fill="${isDown ? '#fef2f2' : '#ecfdf5'}" stroke="${isDown ? '#fca5a5' : '#6ee7b7'}" stroke-width="1" />
                    <text x="0" y="3" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="bold" fill="${isDown ? '#dc2626' : '#059669'}">${isDown ? '-' : '+'}${changeVal}</text>
                </g>

                <!-- Titik Awal -->
                <circle cx="${x1}" cy="${axisY}" r="5" fill="#3b82f6" />
                <text x="${x1}" y="${axisY - 12}" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="bold" fill="#2563eb">Awal (${startVal})</text>

                <!-- Titik Akhir / Target -->
                <circle cx="${x2}" cy="${axisY}" r="5" fill="${isDown ? '#ed1e28' : '#10b981'}" />
                <text x="${x2}" y="${axisY - 12}" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="bold" fill="${isDown ? '#dc2626' : '#059669'}">Akhir (?)</text>
            </svg>
            <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                Ilustrasi Garis Bilangan: Nilai awal ${startVal}°C ${isDown ? 'turun' : 'naik'} ${changeVal}°C
            </div>
        </div>
    `;
    return true;
}

// 2. RENDERER DIAGRAM VENN (Himpunan Irisan & Gabungan)
function tryRenderVennDiagram(el, cleanText, wrapperEl) {
    const isVenn = cleanText.match(/diagram\s*venn|himpunan|irisan|gabungan|A\s*∩\s*B|A\s*∪\s*B/i);
    if (!isVenn) return false;

    setVizHeader(wrapperEl, 'Visualisasi Diagram Venn: Irisan & Gabungan Himpunan');

    el.innerHTML = `
        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
            <svg viewBox="0 0 340 180" class="w-full max-w-sm drop-shadow-sm select-none">
                <!-- Semesta (S) -->
                <rect x="15" y="10" width="310" height="155" rx="12" fill="#f8fafc" stroke="#64748b" stroke-width="2" />
                <text x="32" y="32" font-family="sans-serif" font-size="14" font-weight="bold" fill="#475569">S</text>

                <!-- Lingkaran A -->
                <circle cx="125" cy="90" r="55" fill="#3b82f6" fill-opacity="0.25" stroke="#2563eb" stroke-width="2.5" />
                <!-- Lingkaran B -->
                <circle cx="215" cy="90" r="55" fill="#f59e0b" fill-opacity="0.25" stroke="#d97706" stroke-width="2.5" />

                <!-- Label Himpunan -->
                <text x="95" y="50" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1d4ed8">A</text>
                <text x="245" y="50" font-family="sans-serif" font-size="14" font-weight="bold" fill="#b45309">B</text>

                <!-- Label Daerah Irisan A ∩ B -->
                <text x="170" y="94" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#6d28d9">A ∩ B</text>
                <text x="110" y="94" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1e40af">A - B</text>
                <text x="230" y="94" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#92400e">B - A</text>
            </svg>
            <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                Ilustrasi Diagram Venn: Himpunan A (Biru), Himpunan B (Kuning), Irisan A ∩ B (Tengah)
            </div>
        </div>
    `;
    return true;
}

// 3. RENDERER STATISTIKA (Diagram Batang / Bar Chart Dinamis)
function tryRenderStatisticsChart(el, cleanText, wrapperEl) {
    const isStat = cleanText.match(/diagram\s*batang|rata-rata|rata\s*rata|produksi|frekuensi|nilai\s*ujian/i);
    if (!isStat) return false;

    // Cari deret angka dalam teks (cth: "31, 31, 44, 48, 40, 47, 41" atau "{n1}, {n2}...")
    const listMatch = cleanText.match(/(?:adalah|sebagai berikut:?)\s*([\d\s,.\-dan]+)/i);
    let values = [];
    if (listMatch) {
        values = [...listMatch[1].matchAll(/(\d+(?:\.\d+)?)/g)].map(m => parseFloat(m[1])).filter(v => !isNaN(v) && v > 0);
    }
    if (values.length < 3) {
        // Fallback cari semua angka berurutan di teks
        const allNums = [...cleanText.matchAll(/(\d+(?:\.\d+)?)/g)].map(m => parseFloat(m[1])).filter(v => v > 0 && v <= 1000);
        if (allNums.length >= 4) {
            values = allNums.slice(0, 7);
        }
    }

    if (values.length < 3) return false;

    setVizHeader(wrapperEl, 'Visualisasi Statistika: Diagram Batang Data');

    const labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const maxVal = Math.max(...values);

    const svgW = 340, svgH = 180, padL = 40, padR = 20, padT = 25, padB = 30;
    const chartW = svgW - padL - padR;
    const chartH = svgH - padT - padB;
    const barW = Math.min(32, (chartW / values.length) * 0.65);
    const gap = chartW / values.length;

    const yMax = Math.ceil(maxVal * 1.25);
    const toSvgY = (v) => padT + chartH - (v / yMax) * chartH;

    // Bar colors
    const colors = ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#f97316'];

    let barsSvg = '';
    values.forEach((val, idx) => {
        let x = padL + idx * gap + (gap - barW) / 2;
        let y = toSvgY(val);
        let h = (val / yMax) * chartH;
        let col = colors[idx % colors.length];
        let lbl = labels[idx] || `D${idx + 1}`;

        barsSvg += `
            <rect x="${x}" y="${y}" width="${barW}" height="${h}" rx="4" fill="${col}" fill-opacity="0.85" stroke="${col}" stroke-width="1.5" />
            <text x="${x + barW / 2}" y="${y - 5}" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="bold" fill="#334155">${val}</text>
            <text x="${x + barW / 2}" y="${svgH - 10}" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="600" fill="#64748b">${lbl}</text>
        `;
    });

    el.innerHTML = `
        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
            <svg viewBox="0 0 ${svgW} ${svgH}" class="w-full max-w-sm drop-shadow-sm select-none">
                <!-- Garis Grid Horizontal -->
                <line x1="${padL}" y1="${padT + chartH}" x2="${svgW - padR}" y2="${padT + chartH}" stroke="#cbd5e1" stroke-width="1.5" />
                <line x1="${padL}" y1="${toSvgY(yMax / 2)}" x2="${svgW - padR}" y2="${toSvgY(yMax / 2)}" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <line x1="${padL}" y1="${toSvgY(yMax)}" x2="${svgW - padR}" y2="${toSvgY(yMax)}" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />

                <!-- Batang -->
                ${barsSvg}
            </svg>
            <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                Diagram Batang Distribusi Data (${values.length} data harian)
            </div>
        </div>
    `;
    return true;
}

// 4. RENDERER BANGUN RUANG 3D (Tabung, Kerucut, Bola, Balok, Kubus)
function tryRender3DSolid(el, cleanText, wrapperEl) {
    // 4A. TABUNG (Silinder)
    if (/tabung|silinder/i.test(cleanText)) {
        const rMatch = cleanText.match(/(?:jari-jari|r)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let rVal = rMatch ? parseFloat(rMatch[1]) : 7;
        let tVal = tMatch ? parseFloat(tMatch[1]) : 15;

        setVizHeader(wrapperEl, `Visualisasi Bangun Ruang: Tabung (r = ${rVal} cm, t = ${tVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 210" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <linearGradient id="cylGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#93c5fd" stop-opacity="0.8" />
                            <stop offset="50%" stop-color="#eff6ff" stop-opacity="0.9" />
                            <stop offset="100%" stop-color="#60a5fa" stop-opacity="0.8" />
                        </linearGradient>
                    </defs>
                    <!-- Sisi Selimut Tabung -->
                    <path d="M 110 65 L 110 155 A 60 22 0 0 0 230 155 L 230 65 Z" fill="url(#cylGrad)" stroke="#2563eb" stroke-width="2" />
                    <!-- Garis Belakang Alas Putus-putus -->
                    <path d="M 110 155 A 60 22 0 0 1 230 155" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-dasharray="4,3" />
                    <!-- Tutup Atas Tabung -->
                    <ellipse cx="170" cy="65" rx="60" ry="22" fill="#dbeafe" stroke="#2563eb" stroke-width="2" />
                    <!-- Pusat & Jari-jari Atas -->
                    <circle cx="170" cy="65" r="3.5" fill="#1e40af" />
                    <line x1="170" y1="65" x2="230" y2="65" stroke="#ed1e28" stroke-width="2" />
                    <text x="200" y="58" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">r = ${rVal} cm</text>
                    <!-- Garis Tinggi Tabung -->
                    <line x1="90" y1="65" x2="90" y2="155" stroke="#475569" stroke-width="2" />
                    <line x1="85" y1="65" x2="95" y2="65" stroke="#475569" stroke-width="2" />
                    <line x1="85" y1="155" x2="95" y2="155" stroke="#475569" stroke-width="2" />
                    <text x="75" y="115" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#334155">t = ${tVal} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Ruang Tabung (r = ${rVal} cm, t = ${tVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 4B. KERUCUT (Cone)
    if (/kerucut/i.test(cleanText)) {
        const rMatch = cleanText.match(/(?:jari-jari|r)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let rVal = rMatch ? parseFloat(rMatch[1]) : 6;
        let tVal = tMatch ? parseFloat(tMatch[1]) : 8;

        setVizHeader(wrapperEl, `Visualisasi Bangun Ruang: Kerucut (r = ${rVal} cm, t = ${tVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 210" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <linearGradient id="coneGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#fde68a" stop-opacity="0.8" />
                            <stop offset="50%" stop-color="#fffbeb" stop-opacity="0.9" />
                            <stop offset="100%" stop-color="#fbbf24" stop-opacity="0.8" />
                        </linearGradient>
                    </defs>
                    <!-- Selimut Kerucut -->
                    <path d="M 170 35 L 105 160 A 65 22 0 0 0 235 160 Z" fill="url(#coneGrad)" stroke="#d97706" stroke-width="2" />
                    <!-- Alas Belakang Putus-putus -->
                    <path d="M 105 160 A 65 22 0 0 1 235 160" fill="none" stroke="#d97706" stroke-width="1.5" stroke-dasharray="4,3" />
                    <!-- Garis Tinggi Kerucut -->
                    <line x1="170" y1="35" x2="170" y2="160" stroke="#334155" stroke-width="2" stroke-dasharray="3,3" />
                    <!-- Siku-siku di pusat alas -->
                    <rect x="170" y="148" width="10" height="12" fill="none" stroke="#64748b" stroke-width="1.5" />
                    <!-- Jari-jari alas -->
                    <line x1="170" y1="160" x2="235" y2="160" stroke="#ed1e28" stroke-width="2" />
                    <circle cx="170" cy="160" r="3" fill="#0f172a" />
                    <text x="202" y="176" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">r = ${rVal} cm</text>
                    <!-- Label Tinggi & Sisi Miring (s) -->
                    <text x="155" y="105" text-anchor="end" font-family="sans-serif" font-size="11" font-weight="bold" fill="#334155">t = ${tVal} cm</text>
                    <text x="215" y="95" text-anchor="start" font-family="sans-serif" font-size="11" font-weight="bold" fill="#d97706">s (garis pelukis)</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Ruang Kerucut (r = ${rVal} cm, t = ${tVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 4C. BOLA (Sphere)
    if (/bola/i.test(cleanText)) {
        const rMatch = cleanText.match(/(?:jari-jari|r)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let rVal = rMatch ? parseFloat(rMatch[1]) : 14;

        setVizHeader(wrapperEl, `Visualisasi Bangun Ruang: Bola (r = ${rVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 200" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <radialGradient id="sphereGrad" cx="35%" cy="35%" r="65%">
                            <stop offset="0%" stop-color="#ffffff" />
                            <stop offset="40%" stop-color="#fed7aa" />
                            <stop offset="85%" stop-color="#f97316" />
                            <stop offset="100%" stop-color="#c2410c" />
                        </radialGradient>
                    </defs>
                    <!-- Bola Lingkaran Luar -->
                    <circle cx="170" cy="100" r="70" fill="url(#sphereGrad)" stroke="#c2410c" stroke-width="2" />
                    <!-- Garis Ekuator Depan & Belakang -->
                    <path d="M 100 100 A 70 24 0 0 0 240 100" fill="none" stroke="#7c2d12" stroke-width="2" />
                    <path d="M 100 100 A 70 24 0 0 1 240 100" fill="none" stroke="#7c2d12" stroke-width="1.5" stroke-dasharray="4,3" />
                    <!-- Pusat & Jari-jari -->
                    <circle cx="170" cy="100" r="4" fill="#0f172a" />
                    <line x1="170" y1="100" x2="240" y2="100" stroke="#ffffff" stroke-width="2.5" />
                    <line x1="170" y1="100" x2="240" y2="100" stroke="#dc2626" stroke-width="1.5" stroke-dasharray="2,2" />
                    <text x="205" y="92" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#ffffff" stroke="#0f172a" stroke-width="0.5">r = ${rVal} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Ruang Bola (r = ${rVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 4D. BALOK / KUBUS
    if (/balok|kubus/i.test(cleanText)) {
        const isKubus = /kubus/i.test(cleanText);
        const pMatch = cleanText.match(/(?:panjang|p|rusuk|s)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const lMatch = cleanText.match(/(?:lebar|l)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);

        let pVal = pMatch ? parseFloat(pMatch[1]) : 12;
        let lVal = lMatch ? parseFloat(lMatch[1]) : (isKubus ? pVal : 8);
        let tVal = tMatch ? parseFloat(tMatch[1]) : (isKubus ? pVal : 6);

        setVizHeader(wrapperEl, `Visualisasi Bangun Ruang: ${isKubus ? 'Kubus' : 'Balok'} (${isKubus ? `s = ${pVal} cm` : `p = ${pVal}, l = ${lVal}, t = ${tVal}`})`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 200" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Sisi Belakang Putus-putus -->
                    <line x1="140" y1="50" x2="140" y2="130" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="4,3" />
                    <line x1="80" y1="130" x2="140" y2="130" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="4,3" />
                    <line x1="140" y1="130" x2="260" y2="130" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="4,3" />

                    <!-- Sisi Depan -->
                    <polygon points="80,90 200,90 200,170 80,170" fill="#e0f2fe" fill-opacity="0.8" stroke="#0284c7" stroke-width="2" />
                    <!-- Sisi Atas -->
                    <polygon points="80,90 140,50 260,50 200,90" fill="#bae6fd" fill-opacity="0.8" stroke="#0284c7" stroke-width="2" />
                    <!-- Sisi Kanan -->
                    <polygon points="200,90 260,50 260,130 200,170" fill="#7dd3fc" fill-opacity="0.8" stroke="#0284c7" stroke-width="2" />

                    <!-- Label Dimensi -->
                    <text x="140" y="186" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#0369a1">${isKubus ? `s = ${pVal} cm` : `p = ${pVal} cm`}</text>
                    <text x="240" y="160" text-anchor="start" font-family="sans-serif" font-size="11" font-weight="bold" fill="#0369a1">${isKubus ? `s = ${tVal} cm` : `t = ${tVal} cm`}</text>
                    <text x="235" y="65" text-anchor="start" font-family="sans-serif" font-size="11" font-weight="bold" fill="#0369a1">${isKubus ? `s = ${lVal} cm` : `l = ${lVal} cm`}</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Ruang ${isKubus ? `Kubus (Panjang Rusuk s = ${pVal} cm)` : `Balok (p = ${pVal} cm, l = ${lVal} cm, t = ${tVal} cm)`}
                </div>
            </div>
        `;
        return true;
    }

    return false;
}

// 5. RENDERER BANGUN DATAR 2D (Pythagoras, Persegi, Persegi Panjang, Trapesium, Lingkaran/Juring)
function tryRender2DShape(el, cleanText, wrapperEl) {
    // 5A. PYTHAGORAS / SEGITIGA SIKU-SIKU
    if (/pythagoras|segitiga\s*siku|sisi\s*miring/i.test(cleanText)) {
        const aMatch = cleanText.match(/(?:alas|a)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const bMatch = cleanText.match(/(?:tinggi|b)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let aVal = aMatch ? parseFloat(aMatch[1]) : 6;
        let bVal = bMatch ? parseFloat(bMatch[1]) : 8;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Segitiga Siku-Siku (a = ${aVal} cm, b = ${bVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 210" class="w-full max-w-sm drop-shadow-sm select-none">
                    <polygon points="90,165 250,165 90,45" fill="#eff6ff" stroke="#2563eb" stroke-width="2.5" stroke-linejoin="round" />
                    <!-- Siku-siku -->
                    <rect x="90" y="145" width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" />
                    <circle cx="100" cy="155" r="2" fill="#2563eb" />
                    <!-- Label Alas (a) -->
                    <text x="170" y="184" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#1d4ed8">alas (a) = ${aVal} cm</text>
                    <!-- Label Tinggi (b) -->
                    <text x="75" y="110" text-anchor="end" font-family="sans-serif" font-size="12" font-weight="bold" fill="#1d4ed8">tinggi (b) = ${bVal} cm</text>
                    <!-- Label Sisi Miring (c) -->
                    <text x="185" y="95" text-anchor="start" font-family="sans-serif" font-size="12" font-weight="bold" fill="#dc2626">c = ?</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Geometri: Segitiga Siku-siku (alas a = ${aVal} cm, tinggi b = ${bVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5A-2. SEGITIGA UMUM (ALAS & TINGGI)
    if (/segitiga/i.test(cleanText) && (/alas/i.test(cleanText) || /tinggi/i.test(cleanText) || /luas/i.test(cleanText))) {
        const aMatch = cleanText.match(/(?:alas|a)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let aVal = aMatch ? parseFloat(aMatch[1]) : 10;
        let tVal = tMatch ? parseFloat(tMatch[1]) : 8;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Segitiga (alas a = ${aVal} cm, tinggi t = ${tVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 200" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Segitiga Lancip -->
                    <polygon points="60,155 270,155 170,45" fill="#eff6ff" stroke="#2563eb" stroke-width="2.5" stroke-linejoin="round" />
                    <!-- Garis Tinggi Putus-putus -->
                    <line x1="170" y1="45" x2="170" y2="155" stroke="#1d4ed8" stroke-width="1.8" stroke-dasharray="4,3" />
                    <rect x="170" y="143" width="12" height="12" fill="none" stroke="#1d4ed8" stroke-width="1.5" />
                    <circle cx="176" cy="149" r="1.5" fill="#1d4ed8" />
                    <!-- Labels -->
                    <text x="165" y="178" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#1d4ed8">alas (a) = ${aVal} cm</text>
                    <text x="162" y="105" text-anchor="end" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1d4ed8">tinggi (t) = ${tVal} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Datar Segitiga (alas = ${aVal} cm, tinggi = ${tVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5B. PERSEGI & PERSEGI PANJANG
    if (/persegi\s*panjang|persegi/i.test(cleanText)) {
        const isPersegiPanjang = /persegi\s*panjang/i.test(cleanText);
        const isPersegi = !isPersegiPanjang && /persegi/i.test(cleanText);

        if (isPersegi) {
            // PERSEGI (SQUARE) - Semua 4 sisi sama panjang
            const sMatch = cleanText.match(/(?:sisi|s|panjang|rusuk)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
            let sVal = sMatch ? parseFloat(sMatch[1]) : 9;

            setVizHeader(wrapperEl, `Visualisasi Geometri: Persegi (sisi s = ${sVal} cm)`);

            el.innerHTML = `
                <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                    <svg viewBox="0 0 340 200" class="w-full max-w-sm drop-shadow-sm select-none">
                        <!-- Bujur Sangkar / Persegi -->
                        <rect x="105" y="35" width="130" height="130" rx="4" fill="#f0fdf4" stroke="#16a34a" stroke-width="2.5" />
                        <!-- Siku-siku di 4 sudut -->
                        <rect x="105" y="35" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="223" y="35" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="105" y="153" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="223" y="153" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <!-- Tanda Sama Panjang (Tick marks) -->
                        <line x1="170" y1="30" x2="170" y2="40" stroke="#16a34a" stroke-width="2" />
                        <line x1="170" y1="160" x2="170" y2="170" stroke="#16a34a" stroke-width="2" />
                        <line x1="100" y1="100" x2="110" y2="100" stroke="#16a34a" stroke-width="2" />
                        <line x1="230" y1="100" x2="240" y2="100" stroke="#16a34a" stroke-width="2" />
                        <!-- Labels -->
                        <text x="170" y="186" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#15803d">sisi (s) = ${sVal} cm</text>
                        <text x="92" y="104" text-anchor="end" font-family="sans-serif" font-size="12" font-weight="bold" fill="#15803d">s = ${sVal} cm</text>
                    </svg>
                    <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                        Ilustrasi Bangun Datar Persegi (Panjang Sisi s = ${sVal} cm)
                    </div>
                </div>
            `;
            return true;
        } else {
            // PERSEGI PANJANG (RECTANGLE)
            const pMatch = cleanText.match(/(?:panjang|p)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
            const lMatch = cleanText.match(/(?:lebar|l)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
            let pVal = pMatch ? parseFloat(pMatch[1]) : 12;
            let lVal = lMatch ? parseFloat(lMatch[1]) : 6;

            setVizHeader(wrapperEl, `Visualisasi Geometri: Persegi Panjang (p = ${pVal} cm, l = ${lVal} cm)`);

            el.innerHTML = `
                <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                    <svg viewBox="0 0 340 190" class="w-full max-w-sm drop-shadow-sm select-none">
                        <rect x="70" y="45" width="200" height="100" rx="4" fill="#f0fdf4" stroke="#16a34a" stroke-width="2.5" />
                        <!-- Siku-siku -->
                        <rect x="70" y="45" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="258" y="45" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="70" y="133" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <rect x="258" y="133" width="12" height="12" fill="none" stroke="#16a34a" stroke-width="1.5" />
                        <!-- Labels -->
                        <text x="170" y="165" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#15803d">panjang (p) = ${pVal} cm</text>
                        <text x="55" y="100" text-anchor="end" font-family="sans-serif" font-size="12" font-weight="bold" fill="#15803d">lebar (l) = ${lVal} cm</text>
                    </svg>
                    <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                        Ilustrasi Bangun Datar Persegi Panjang (p = ${pVal} cm, l = ${lVal} cm)
                    </div>
                </div>
            `;
            return true;
        }
    }

    // 5C. TRAPESIUM
    if (/trapesium/i.test(cleanText)) {
        const aMatch = cleanText.match(/(?:sisi\s*atas|a)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const bMatch = cleanText.match(/(?:sisi\s*bawah|alas|b)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let aVal = aMatch ? parseFloat(aMatch[1]) : 8;
        let bVal = bMatch ? parseFloat(bMatch[1]) : 14;
        let tVal = tMatch ? parseFloat(tMatch[1]) : 6;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Trapesium (a = ${aVal} cm, b = ${bVal} cm, t = ${tVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 190" class="w-full max-w-sm drop-shadow-sm select-none">
                    <polygon points="110,50 230,50 275,145 65,145" fill="#faf5ff" stroke="#9333ea" stroke-width="2.5" stroke-linejoin="round" />
                    <!-- Garis Tinggi Putus-putus -->
                    <line x1="110" y1="50" x2="110" y2="145" stroke="#7e22ce" stroke-width="1.5" stroke-dasharray="4,3" />
                    <rect x="110" y="133" width="12" height="12" fill="none" stroke="#7e22ce" stroke-width="1.5" />
                    <!-- Labels -->
                    <text x="170" y="40" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#7e22ce">sisi atas (a) = ${aVal} cm</text>
                    <text x="170" y="165" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#7e22ce">sisi bawah (b) = ${bVal} cm</text>
                    <text x="98" y="102" text-anchor="end" font-family="sans-serif" font-size="11" font-weight="bold" fill="#7e22ce">tinggi (t) = ${tVal} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Datar Trapesium (a = ${aVal} cm, b = ${bVal} cm, t = ${tVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5D. LAYANG-LAYANG
    if (/layang\s*-\s*layang|layang/i.test(cleanText)) {
        const d1Match = cleanText.match(/(?:diagonal\s*(?:1|satu|ke-1|pertama|d1)?|d1)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const d2Match = cleanText.match(/(?:diagonal\s*(?:2|dua|ke-2|kedua|d2)?|d2)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const allNums = cleanText.match(/\b(\d+(?:\.\d+)?)\s*(?:cm|m)?/gi) || [];
        
        let d1Val = 22, d2Val = 16;
        if (d1Match && d2Match) {
            d1Val = parseFloat(d1Match[1]);
            d2Val = parseFloat(d2Match[1]);
        } else if (allNums.length >= 2) {
            d1Val = parseFloat(allNums[0]);
            d2Val = parseFloat(allNums[1]);
        }

        setVizHeader(wrapperEl, `Visualisasi Geometri: Layang-Layang (d₁ = ${d1Val} cm, d₂ = ${d2Val} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 210" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Bentuk Layang-Layang -->
                    <polygon points="170,25 250,85 170,185 90,85" fill="#fdf4ff" stroke="#a855f7" stroke-width="2.5" stroke-linejoin="round" />
                    
                    <!-- Diagonal Vertikal (d1) -->
                    <line x1="170" y1="25" x2="170" y2="185" stroke="#7c3aed" stroke-width="1.8" stroke-dasharray="4,3" />
                    <!-- Diagonal Horizontal (d2) -->
                    <line x1="90" y1="85" x2="250" y2="85" stroke="#7c3aed" stroke-width="1.8" stroke-dasharray="4,3" />
                    
                    <!-- Tanda Siku-siku di Titik Potong Diagonal -->
                    <rect x="170" y="85" width="10" height="10" fill="none" stroke="#7c3aed" stroke-width="1.5" />
                    <circle cx="175" cy="90" r="1.5" fill="#7c3aed" />

                    <!-- Tanda Sama Panjang Sisi Atas (1 strip) -->
                    <line x1="126" y1="52" x2="134" y2="58" stroke="#a855f7" stroke-width="2" />
                    <line x1="206" y1="58" x2="214" y2="52" stroke="#a855f7" stroke-width="2" />

                    <!-- Tanda Sama Panjang Sisi Bawah (2 strip) -->
                    <line x1="124" y1="130" x2="132" y2="138" stroke="#a855f7" stroke-width="2" />
                    <line x1="128" y1="134" x2="136" y2="142" stroke="#a855f7" stroke-width="2" />
                    <line x1="204" y1="138" x2="212" y2="130" stroke="#a855f7" stroke-width="2" />
                    <line x1="208" y1="142" x2="216" y2="134" stroke="#a855f7" stroke-width="2" />

                    <!-- Labels Diagonal -->
                    <text x="180" y="145" text-anchor="start" font-family="sans-serif" font-size="11" font-weight="bold" fill="#6b21a8">d₁ = ${d1Val} cm</text>
                    <text x="130" y="80" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#6b21a8">d₂ = ${d2Val} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Datar Layang-Layang (Diagonal d₁ = ${d1Val} cm, d₂ = ${d2Val} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5E. BELAH KETUPAT
    if (/belah\s*ketupat|belah-ketupat/i.test(cleanText)) {
        const d1Match = cleanText.match(/(?:diagonal\s*(?:1|satu|ke-1|pertama|d1)?|d1)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const d2Match = cleanText.match(/(?:diagonal\s*(?:2|dua|ke-2|kedua|d2)?|d2)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const sMatch = cleanText.match(/(?:sisi|s)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const allNums = cleanText.match(/\b(\d+(?:\.\d+)?)\s*(?:cm|m)?/gi) || [];

        let d1Val = 16, d2Val = 12, sVal = 10;
        if (d1Match && d2Match) {
            d1Val = parseFloat(d1Match[1]);
            d2Val = parseFloat(d2Match[1]);
        } else if (allNums.length >= 2) {
            d1Val = parseFloat(allNums[0]);
            d2Val = parseFloat(allNums[1]);
        }
        if (sMatch) sVal = parseFloat(sMatch[1]);

        setVizHeader(wrapperEl, `Visualisasi Geometri: Belah Ketupat (d₁ = ${d1Val} cm, d₂ = ${d2Val} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 210" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Bentuk Belah Ketupat -->
                    <polygon points="170,25 255,105 170,185 85,105" fill="#f0fdfa" stroke="#0d9488" stroke-width="2.5" stroke-linejoin="round" />
                    
                    <!-- Diagonal Vertikal (d1) -->
                    <line x1="170" y1="25" x2="170" y2="185" stroke="#0f766e" stroke-width="1.8" stroke-dasharray="4,3" />
                    <!-- Diagonal Horizontal (d2) -->
                    <line x1="85" y1="105" x2="255" y2="105" stroke="#0f766e" stroke-width="1.8" stroke-dasharray="4,3" />
                    
                    <!-- Siku-siku di Titik Pusat -->
                    <rect x="170" y="105" width="10" height="10" fill="none" stroke="#0f766e" stroke-width="1.5" />
                    <circle cx="175" cy="110" r="1.5" fill="#0f766e" />

                    <!-- Tanda Sama Panjang di 4 Sisi -->
                    <line x1="124" y1="62" x2="132" y2="68" stroke="#0d9488" stroke-width="2" />
                    <line x1="208" y1="68" x2="216" y2="62" stroke="#0d9488" stroke-width="2" />
                    <line x1="124" y1="148" x2="132" y2="142" stroke="#0d9488" stroke-width="2" />
                    <line x1="208" y1="142" x2="216" y2="148" stroke="#0d9488" stroke-width="2" />

                    <!-- Labels -->
                    <text x="180" y="150" text-anchor="start" font-family="sans-serif" font-size="11" font-weight="bold" fill="#115e59">d₁ = ${d1Val} cm</text>
                    <text x="125" y="100" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#115e59">d₂ = ${d2Val} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Datar Belah Ketupat (Diagonal d₁ = ${d1Val} cm, d₂ = ${d2Val} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5F. JAJAR GENJANG
    if (/jajar\s*genjang|jajargenjang/i.test(cleanText)) {
        const aMatch = cleanText.match(/(?:alas|a)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const tMatch = cleanText.match(/(?:tinggi|t)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        let aVal = aMatch ? parseFloat(aMatch[1]) : 12;
        let tVal = tMatch ? parseFloat(tMatch[1]) : 7;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Jajar Genjang (alas a = ${aVal} cm, tinggi t = ${tVal} cm)`);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 190" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Bentuk Jajar Genjang -->
                    <polygon points="105,45 270,45 235,145 70,145" fill="#eff6ff" stroke="#3b82f6" stroke-width="2.5" stroke-linejoin="round" />
                    
                    <!-- Garis Tinggi Putus-putus -->
                    <line x1="105" y1="45" x2="105" y2="145" stroke="#1d4ed8" stroke-width="1.8" stroke-dasharray="4,3" />
                    <rect x="105" y="133" width="12" height="12" fill="none" stroke="#1d4ed8" stroke-width="1.5" />
                    <circle cx="111" cy="139" r="1.5" fill="#1d4ed8" />

                    <!-- Labels -->
                    <text x="152" y="165" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#1d4ed8">alas (a) = ${aVal} cm</text>
                    <text x="96" y="100" text-anchor="end" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1d4ed8">tinggi (t) = ${tVal} cm</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Bangun Datar Jajar Genjang (alas = ${aVal} cm, tinggi = ${tVal} cm)
                </div>
            </div>
        `;
        return true;
    }

    // 5G. LINGKARAN, JURING & BUSUR
    if (/lingkaran|juring|busur|sudut\s*pusat/i.test(cleanText)) {
        const rMatch = cleanText.match(/(?:jari-jari|r)\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)/i);
        const aMatch = cleanText.match(/(?:sudut\s*pusat|\b)(\d+(?:\.\d+)?)\s*(?:°|derajat)/i);
        let rVal = rMatch ? parseFloat(rMatch[1]) : 14;
        let alpha = aMatch ? parseFloat(aMatch[1]) : 60;
        if (alpha <= 0 || alpha >= 360) alpha = 60;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Lingkaran & Juring (r = ${rVal} cm, α = ${alpha}°)`);

        const cx = 170, cy = 95, R = 65;
        const rad = (alpha * Math.PI) / 180;
        const x2 = cx + R * Math.cos(rad);
        const y2 = cy - R * Math.sin(rad);
        const largeArc = alpha > 180 ? 1 : 0;

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 190" class="w-full max-w-sm drop-shadow-sm select-none">
                    <!-- Lingkaran Luar -->
                    <circle cx="${cx}" cy="${cy}" r="${R}" fill="#f8fafc" stroke="#64748b" stroke-width="2" />
                    <!-- Juring Terarsir -->
                    <path d="M ${cx} ${cy} L ${cx + R} ${cy} A ${R} ${R} 0 ${largeArc} 0 ${x2} ${y2} Z" fill="#ec4899" fill-opacity="0.3" stroke="#db2777" stroke-width="2" />
                    <!-- Busur Tebal Merah -->
                    <path d="M ${cx + R} ${cy} A ${R} ${R} 0 ${largeArc} 0 ${x2} ${y2}" fill="none" stroke="#ed1e28" stroke-width="3.5" />
                    <!-- Pusat -->
                    <circle cx="${cx}" cy="${cy}" r="4" fill="#0f172a" />
                    <text x="${cx - 10}" y="${cy + 14}" font-family="sans-serif" font-size="12" font-weight="bold" fill="#334155">O</text>
                    <!-- Label r & sudut pusat -->
                    <text x="${cx + R / 2}" y="${cy + 14}" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#0f172a">r = ${rVal} cm</text>
                    <text x="${cx + 20}" y="${cy - 8}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#db2777">${alpha}°</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Lingkaran: Sudut Pusat ${alpha}° dan Jari-jari r = ${rVal} cm
                </div>
            </div>
        `;
        return true;
    }

    return false;
}

// 6. RENDERER GEOMETRI SUDUT (Penyiku, Pelurus, Bertolak Belakang, Segitiga)
function tryRenderAngleGeometry(el, cleanText, wrapperEl) {
    // 6A. SUDUT BERPENYIKU (90°)
    const penyikuMatch = cleanText.match(/berpenyiku|penyiku|komplemen/i);
    const angleMatch = cleanText.match(/(?:sudut\s+([A-Za-z]))?\s*(?:=|\:)?\s*(\d+(?:\.\d+)?)\s*(?:°|derajat)/i)
                    || cleanText.match(/(\d+(?:\.\d+)?)\s*(?:°|derajat)/i);
    const ratioMatch = cleanText.match(/(\d+(?:\.\d+)?)\s*:\s*(\d+(?:\.\d+)?)/);

    if (penyikuMatch && (angleMatch || ratioMatch)) {
        let label1Text = '', label2Text = '', angleVal = 35;
        let isRatio = !angleMatch && ratioMatch;

        if (isRatio) {
            let r1 = parseFloat(ratioMatch[1]) || 1;
            let r2 = parseFloat(ratioMatch[2]) || 2;
            let total = r1 + r2;
            angleVal = (r1 / total) * 90;
            label1Text = `${r1}x`;
            label2Text = `${r2}x`;
        } else {
            let labelName = (angleMatch[1] && isNaN(angleMatch[1])) ? angleMatch[1].toUpperCase() : 'P';
            angleVal = parseFloat(angleMatch[2] || angleMatch[1]);
            if (isNaN(angleVal) || angleVal <= 0 || angleVal >= 90) angleVal = 35;
            let otherLabel = labelName === 'P' ? 'Q' : (labelName === 'A' ? 'B' : 'Q');
            label1Text = `${labelName} = ${angleVal}°`;
            label2Text = `${otherLabel} = ?`;
        }

        setVizHeader(wrapperEl, isRatio ? `Visualisasi Geometri: Sudut Berpenyiku Perbandingan ${ratioMatch[1]}:${ratioMatch[2]} (90°)` : 'Visualisasi Geometri: Sudut Berpenyiku (90°)');

        const rad = (angleVal * Math.PI) / 180;
        const cx = 80, cy = 180, len = 150;
        const px = cx + len * Math.cos(rad);
        const py = cy - len * Math.sin(rad);

        const arcR1 = 55;
        const arc1EndX = cx + arcR1 * Math.cos(rad);
        const arc1EndY = cy - arcR1 * Math.sin(rad);

        const arcR2 = 70;
        const arc2StartX = cx + arcR2 * Math.cos(rad);
        const arc2StartY = cy - arcR2 * Math.sin(rad);

        const midRad1 = rad / 2;
        const label1X = cx + (arcR1 + 22) * Math.cos(midRad1);
        const label1Y = cy - (arcR1 + 22) * Math.sin(midRad1);

        const midRad2 = (rad + Math.PI / 2) / 2;
        const label2X = cx + (arcR2 + 22) * Math.cos(midRad2);
        const label2Y = cy - (arcR2 + 22) * Math.sin(midRad2);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 230" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <marker id="arrow-penyiku" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#334155" />
                        </marker>
                        <marker id="arrow-penyiku-red" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#ed1e28" />
                        </marker>
                    </defs>
                    
                    <rect x="${cx}" y="${cy - 20}" width="20" height="20" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="3,3" />
                    <path d="M ${cx + arcR1} ${cy} A ${arcR1} ${arcR1} 0 0 0 ${arc1EndX} ${arc1EndY}" fill="none" stroke="#ed1e28" stroke-width="3" stroke-linecap="round" />
                    <path d="M ${arc2StartX} ${arc2StartY} A ${arcR2} ${arcR2} 0 0 0 ${cx} ${cy - arcR2}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-dasharray="4,3" />

                    <line x1="${cx}" y1="${cy}" x2="${cx + len}" y2="${cy}" stroke="#334155" stroke-width="2.5" marker-end="url(#arrow-penyiku)" />
                    <line x1="${cx}" y1="${cy}" x2="${cx}" y2="${cy - len}" stroke="#334155" stroke-width="2.5" marker-end="url(#arrow-penyiku)" />
                    <line x1="${cx}" y1="${cy}" x2="${px}" y2="${py}" stroke="#ed1e28" stroke-width="2.5" marker-end="url(#arrow-penyiku-red)" />
                    
                    <circle cx="${cx}" cy="${cy}" r="4" fill="#0f172a" />
                    <text x="${cx - 15}" y="${cy + 15}" font-family="sans-serif" font-size="12" font-weight="bold" fill="#64748b">O</text>
                    
                    <g transform="translate(${label1X}, ${label1Y})">
                        <rect x="-26" y="-12" width="52" height="24" rx="6" fill="#fef2f2" stroke="#fecaca" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">${label1Text}</text>
                    </g>
                    
                    <g transform="translate(${label2X}, ${label2Y})">
                        <rect x="-24" y="-12" width="48" height="24" rx="6" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#2563eb">${label2Text}</text>
                    </g>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Geometri: Dua sudut saling berpenyiku (Total Sudut = 90°)
                </div>
            </div>
        `;
        return true;
    }

    // 6B. SUDUT BERPELURUS (180°)
    const pelurusMatch = cleanText.match(/berpelurus|pelurus|suplemen/i);
    if (pelurusMatch && (angleMatch || ratioMatch)) {
        let label1Text = '', label2Text = '', angleVal = 50;
        let isRatio = !angleMatch && ratioMatch;

        if (isRatio) {
            let r1 = parseFloat(ratioMatch[1]) || 1;
            let r2 = parseFloat(ratioMatch[2]) || 3;
            let total = r1 + r2;
            angleVal = (r1 / total) * 180;
            label1Text = `${r1}x`;
            label2Text = `${r2}x`;
        } else {
            let labelName = (angleMatch[1] && isNaN(angleMatch[1])) ? angleMatch[1].toUpperCase() : 'A';
            angleVal = parseFloat(angleMatch[2] || angleMatch[1]);
            if (isNaN(angleVal) || angleVal <= 0 || angleVal >= 180) angleVal = 60;
            let otherLabel = labelName === 'A' ? 'B' : (labelName === 'P' ? 'Q' : 'B');
            label1Text = `${labelName} = ${angleVal}°`;
            label2Text = `${otherLabel} = ?`;
        }

        setVizHeader(wrapperEl, isRatio ? `Visualisasi Geometri: Sudut Berpelurus Perbandingan ${ratioMatch[1]}:${ratioMatch[2]} (180°)` : 'Visualisasi Geometri: Sudut Berpelurus (180°)');

        const rad = (angleVal * Math.PI) / 180;
        const cx = 170, cy = 170, len = 135;
        const px = cx + len * Math.cos(rad);
        const py = cy - len * Math.sin(rad);

        const arcR1 = 50;
        const arc1EndX = cx + arcR1 * Math.cos(rad);
        const arc1EndY = cy - arcR1 * Math.sin(rad);

        const arcR2 = 65;
        const arc2StartX = cx + arcR2 * Math.cos(rad);
        const arc2StartY = cy - arcR2 * Math.sin(rad);

        const midRad1 = rad / 2;
        const label1X = cx + (arcR1 + 24) * Math.cos(midRad1);
        const label1Y = cy - (arcR1 + 24) * Math.sin(midRad1);

        const midRad2 = (rad + Math.PI) / 2;
        const label2X = cx + (arcR2 + 24) * Math.cos(midRad2);
        const label2Y = cy - (arcR2 + 24) * Math.sin(midRad2);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 230" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <marker id="arrow-pelurus" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#334155" />
                        </marker>
                        <marker id="arrow-pelurus-red" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#ed1e28" />
                        </marker>
                    </defs>
                    
                    <path d="M ${cx + arcR1} ${cy} A ${arcR1} ${arcR1} 0 0 0 ${arc1EndX} ${arc1EndY}" fill="none" stroke="#ed1e28" stroke-width="3" stroke-linecap="round" />
                    <path d="M ${arc2StartX} ${arc2StartY} A ${arcR2} ${arcR2} 0 0 0 ${cx - arcR2} ${cy}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-dasharray="4,3" />

                    <line x1="${cx - len}" y1="${cy}" x2="${cx + len}" y2="${cy}" stroke="#334155" stroke-width="2.5" marker-start="url(#arrow-pelurus)" marker-end="url(#arrow-pelurus)" />
                    <line x1="${cx}" y1="${cy}" x2="${px}" y2="${py}" stroke="#ed1e28" stroke-width="2.5" marker-end="url(#arrow-pelurus-red)" />
                    
                    <circle cx="${cx}" cy="${cy}" r="4" fill="#0f172a" />
                    <text x="${cx - 5}" y="${cy + 18}" font-family="sans-serif" font-size="12" font-weight="bold" fill="#64748b">O</text>
                    
                    <g transform="translate(${label1X}, ${label1Y})">
                        <rect x="-26" y="-12" width="52" height="24" rx="6" fill="#fef2f2" stroke="#fecaca" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">${label1Text}</text>
                    </g>
                    
                    <g transform="translate(${label2X}, ${label2Y})">
                        <rect x="-24" y="-12" width="48" height="24" rx="6" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#2563eb">${label2Text}</text>
                    </g>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Geometri: Dua sudut saling berpelurus (Total Sudut = 180°)
                </div>
            </div>
        `;
        return true;
    }

    // 6C. SUDUT BERTOLAK BELAKANG
    const tolakMatch = cleanText.match(/bertolak\s*belakang|berpotongan/i);
    if (tolakMatch && angleMatch) {
        let angleVal = parseFloat(angleMatch[2] || angleMatch[1]);
        if (isNaN(angleVal) || angleVal <= 0 || angleVal >= 180) angleVal = 94;

        setVizHeader(wrapperEl, `Visualisasi Geometri: Sudut Bertolak Belakang (${angleVal}°)`);

        const rad = (angleVal * Math.PI) / 180;
        const cx = 170, cy = 115, len = 120;
        const l1x1 = cx - len, l1y1 = cy, l1x2 = cx + len, l1y2 = cy;
        const l2x1 = cx - len * Math.cos(rad), l2y1 = cy + len * Math.sin(rad);
        const l2x2 = cx + len * Math.cos(rad), l2y2 = cy - len * Math.sin(rad);

        const arcR1 = 45;
        const arc1StartX = cx + arcR1;
        const arc1StartY = cy;
        const arc1EndX = cx + arcR1 * Math.cos(rad);
        const arc1EndY = cy - arcR1 * Math.sin(rad);
        const midRad1 = rad / 2;
        const label1X = cx + (arcR1 + 25) * Math.cos(midRad1);
        const label1Y = cy - (arcR1 + 25) * Math.sin(midRad1);

        const arcR2 = 45;
        const arc2StartX = cx - arcR2;
        const arc2StartY = cy;
        const arc2EndX = cx - arcR2 * Math.cos(rad);
        const arc2EndY = cy + arcR2 * Math.sin(rad);
        const midRad2 = Math.PI + rad / 2;
        const label2X = cx + (arcR2 + 25) * Math.cos(midRad2);
        const label2Y = cy - (arcR2 + 25) * Math.sin(midRad2);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 230" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <marker id="arrow-tolak" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#334155" />
                        </marker>
                        <marker id="arrow-tolak-red" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#ed1e28" />
                        </marker>
                    </defs>
                    
                    <path d="M ${arc1StartX} ${arc1StartY} A ${arcR1} ${arcR1} 0 0 0 ${arc1EndX} ${arc1EndY}" fill="none" stroke="#ed1e28" stroke-width="3" stroke-linecap="round" />
                    <path d="M ${arc2StartX} ${arc2StartY} A ${arcR2} ${arcR2} 0 0 0 ${arc2EndX} ${arc2EndY}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-dasharray="4,3" />

                    <line x1="${l1x1}" y1="${l1y1}" x2="${l1x2}" y2="${l1y2}" stroke="#334155" stroke-width="2.5" marker-start="url(#arrow-tolak)" marker-end="url(#arrow-tolak)" />
                    <line x1="${l2x1}" y1="${l2y1}" x2="${l2x2}" y2="${l2y2}" stroke="#ed1e28" stroke-width="2.5" marker-start="url(#arrow-tolak-red)" marker-end="url(#arrow-tolak-red)" />
                    
                    <circle cx="${cx}" cy="${cy}" r="4" fill="#0f172a" />
                    <text x="${cx - 15}" y="${cy - 8}" font-family="sans-serif" font-size="12" font-weight="bold" fill="#64748b">O</text>
                    
                    <g transform="translate(${label1X}, ${label1Y})">
                        <rect x="-24" y="-12" width="48" height="24" rx="6" fill="#fef2f2" stroke="#fecaca" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#dc2626">${angleVal}°</text>
                    </g>
                    
                    <g transform="translate(${label2X}, ${label2Y})">
                        <rect x="-20" y="-12" width="40" height="24" rx="6" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#2563eb">?</text>
                    </g>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Geometri: Dua sudut yang saling bertolak belakang
                </div>
            </div>
        `;
        return true;
    }

    // 6D. SUDUT SEGITIGA (180°)
    const segitigaMatch = cleanText.match(/segitiga\s*([A-Za-z]{3})?/i);
    const allAngleMatches = [...cleanText.matchAll(/(?:sudut\s*([A-Za-z])\s*=\s*|\b)([0-9]+(?:\.[0-9]+)?)\s*(?:°|derajat)/gi)];

    if (segitigaMatch && allAngleMatches.length >= 2) {
        let triangleName = segitigaMatch[1] ? segitigaMatch[1].toUpperCase() : 'ABC';
        let vA = triangleName[0] || 'A';
        let vB = triangleName[1] || 'B';
        let vC = triangleName[2] || 'C';

        let m1 = allAngleMatches[0];
        let m2 = allAngleMatches[1];

        let labelA = (m1[1] && isNaN(m1[1])) ? m1[1].toUpperCase() : vA;
        let valA = parseFloat(m1[2] || m1[1]);

        let labelB = (m2[1] && isNaN(m2[1])) ? m2[1].toUpperCase() : vB;
        let valB = parseFloat(m2[2] || m2[1]);

        if (isNaN(valA) || valA <= 5 || valA >= 170) valA = 65;
        if (isNaN(valB) || valB <= 5 || valB >= (175 - valA)) valB = 42;

        let valC = 180 - (valA + valB);
        if (valC <= 0) {
            valA = 60; valB = 60; valC = 60;
        }

        let labelC = vC;
        if (labelA === labelC || labelB === labelC) {
            const letters = ['A', 'B', 'C', 'P', 'Q', 'R', 'K', 'L', 'M'];
            labelC = letters.find(l => l !== labelA && l !== labelB) || 'C';
        }

        setVizHeader(wrapperEl, `Visualisasi Geometri: Sudut Segitiga ${triangleName} (180°)`);

        const radA = (valA * Math.PI) / 180;
        const radB = (valB * Math.PI) / 180;
        const radC = (valC * Math.PI) / 180;

        const baseC = 180;
        const sideB = (baseC * Math.sin(radB)) / Math.sin(radC);
        const h = sideB * Math.sin(radA);
        const xRelC = sideB * Math.cos(radA);

        const minX = Math.min(0, xRelC);
        const maxX = Math.max(baseC, xRelC);
        const triWidth = maxX - minX;
        const triHeight = h;

        const scale = Math.min(220 / Math.max(triWidth, 1), 110 / Math.max(triHeight, 1));
        const baseY = 175;
        const startX = 170 - (triWidth * scale) / 2 - minX * scale;

        const Ax = startX, Ay = baseY;
        const Bx = startX + baseC * scale, By = baseY;
        const Cx = startX + xRelC * scale, Cy = baseY - h * scale;

        const arcR = 26;
        const arcA_endX = Ax + arcR * Math.cos(radA);
        const arcA_endY = Ay - arcR * Math.sin(radA);
        const midRadA = radA / 2;
        const labelAx = Ax + (arcR + 24) * Math.cos(midRadA);
        const labelAy = Ay - (arcR + 24) * Math.sin(midRadA);

        const arcB_startX = Bx - arcR * Math.cos(radB);
        const arcB_startY = By - arcR * Math.sin(radB);
        const midRadB = Math.PI - radB / 2;
        const labelBx = Bx + (arcR + 24) * Math.cos(midRadB);
        const labelBy = By - (arcR + 24) * Math.sin(midRadB);

        const angleCA = Math.atan2(Ay - Cy, Ax - Cx);
        const angleCB = Math.atan2(By - Cy, Bx - Cx);
        const arcC_startX = Cx + arcR * Math.cos(angleCB);
        const arcC_startY = Cy + arcR * Math.sin(angleCB);
        const arcC_endX = Cx + arcR * Math.cos(angleCA);
        const arcC_endY = Cy + arcR * Math.sin(angleCA);
        const midRadC = (angleCA + angleCB) / 2;
        const labelCx = Cx + (arcR + 22) * Math.cos(midRadC);
        const labelCy = Cy + (arcR + 22) * Math.sin(midRadC);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 340 230" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <linearGradient id="triGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#eff6ff" stop-opacity="0.9" />
                            <stop offset="100%" stop-color="#f8fafc" stop-opacity="0.9" />
                        </linearGradient>
                    </defs>
                    
                    <polygon points="${Ax},${Ay} ${Bx},${By} ${Cx},${Cy}" fill="url(#triGrad)" stroke="#334155" stroke-width="2.5" stroke-linejoin="round" />

                    <path d="M ${Ax + arcR} ${Ay} A ${arcR} ${arcR} 0 0 0 ${arcA_endX} ${arcA_endY}" fill="none" stroke="#ed1e28" stroke-width="3" stroke-linecap="round" />
                    <path d="M ${arcB_startX} ${arcB_startY} A ${arcR} ${arcR} 0 0 0 ${Bx - arcR} ${By}" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" />
                    <path d="M ${arcC_startX} ${arcC_startY} A ${arcR} ${arcR} 0 0 0 ${arcC_endX} ${arcC_endY}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-dasharray="4,3" />

                    <circle cx="${Ax}" cy="${Ay}" r="4" fill="#0f172a" />
                    <text x="${Ax - 16}" y="${Ay + 16}" font-family="sans-serif" font-size="13" font-weight="bold" fill="#334155">${labelA}</text>

                    <circle cx="${Bx}" cy="${By}" r="4" fill="#0f172a" />
                    <text x="${Bx + 8}" y="${By + 16}" font-family="sans-serif" font-size="13" font-weight="bold" fill="#334155">${labelB}</text>

                    <circle cx="${Cx}" cy="${Cy}" r="4" fill="#0f172a" />
                    <text x="${Cx - 5}" y="${Cy - 12}" font-family="sans-serif" font-size="13" font-weight="bold" fill="#334155">${labelC}</text>

                    <g transform="translate(${labelAx}, ${labelAy})">
                        <rect x="-24" y="-12" width="48" height="24" rx="6" fill="#fef2f2" stroke="#fecaca" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">${valA}°</text>
                    </g>

                    <g transform="translate(${labelBx}, ${labelBy})">
                        <rect x="-24" y="-12" width="48" height="24" rx="6" fill="#fffbeb" stroke="#fde68a" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#d97706">${valB}°</text>
                    </g>

                    <g transform="translate(${labelCx}, ${labelCy})">
                        <rect x="-22" y="-12" width="44" height="24" rx="6" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1" />
                        <text x="0" y="4" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#2563eb">${labelC}=?</text>
                    </g>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Geometri: Segitiga ${triangleName} (∠${labelA} + ∠${labelB} + ∠${labelC} = 180°)
                </div>
            </div>
        `;
        return true;
    }

    return false;
}

// 7. RENDERER PARABOLA / FUNGSI KUADRAT & GRAFIK PERSAMAAN (DESMOS)
function tryRenderParabola(el, cleanText, wrapperEl) {
    const isMathGraph = /fungsi|parabola|sumbu\s*simetri|titik\s*puncak|persamaan\s*garis|gradien|grafik|spldv|garis\s*lurus/i.test(cleanText);
    
    // Cek keberadaan persamaan matematika dalam teks / formula
    // Contoh: y = 2x + 3, f(x) = 3x - 5, y = x^2 + 4x + 3, 2x - 3y = 6, 2y - 4x = 10
    const quadraticMatch = cleanText.match(/(?:f\(x\)|y)\s*=\s*(-?\d*(?:\.\d+)?)\s*x(?:\^2|²)?\s*(?:[+-]\s*\d*(?:\.\d+)?\s*x)?\s*(?:[+-]\s*\d+(?:\.\d+)?)?/i);
    const linearMatch = cleanText.match(/(?:f\(x\)|y)\s*=\s*(-?\d*(?:\.\d+)?)\s*x\s*(?:[+-]\s*\d+(?:\.\d+)?)?/i);
    const implicitLineMatch = cleanText.match(/(-?\d+)\s*[xy]\s*([+-]\s*\d+)\s*[xy]\s*=\s*(-?\d+)/i);
    const pointMatch = cleanText.match(/\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/g);

    if (!isMathGraph && !quadraticMatch && !linearMatch && !implicitLineMatch) return false;

    if (typeof Desmos !== 'undefined' && (quadraticMatch || linearMatch || implicitLineMatch || (isMathGraph && pointMatch))) {
        setVizHeader(wrapperEl, 'Visualisasi Grafik Desmos (Fungsi & Persamaan Garis)');
        const desmosId = 'desmos_' + Math.random().toString(36).substr(2, 9);
        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl shadow-inner border border-slate-200">
                <div id="${desmosId}" class="w-full rounded-lg overflow-hidden" style="height: 270px;"></div>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Grafik Interaktif Desmos: Eksplorasi persamaan, titik perpotongan, dan kemiringan
                </div>
            </div>
        `;

        setTimeout(() => {
            const desmosEl = document.getElementById(desmosId);
            if (desmosEl && typeof Desmos !== 'undefined') {
                const calc = Desmos.GraphingCalculator(desmosEl, {
                    keypad: false,
                    expressions: false,
                    settingsMenu: false,
                    zoomButtons: true,
                    lockViewport: false
                });

                let exprCount = 0;

                // 1. Tambah persamaan kuadrat jika ada
                if (quadraticMatch && /x(?:\^2|²)/i.test(quadraticMatch[0])) {
                    let latexEq = quadraticMatch[0].replace(/²/g, '^2').replace(/f\(x\)/i, 'y').replace(/\s+/g, '');
                    calc.setExpression({ id: 'expr_' + (++exprCount), latex: latexEq, color: '#2563eb', lineWidth: 2.5 });
                } else if (linearMatch) {
                    let latexEq = linearMatch[0].replace(/f\(x\)/i, 'y').replace(/\s+/g, '');
                    calc.setExpression({ id: 'expr_' + (++exprCount), latex: latexEq, color: '#2563eb', lineWidth: 2.5 });
                } else if (implicitLineMatch) {
                    let latexEq = implicitLineMatch[0].replace(/\s+/g, '');
                    calc.setExpression({ id: 'expr_' + (++exprCount), latex: latexEq, color: '#2563eb', lineWidth: 2.5 });
                }

                // 2. Tambah titik-titik koordinat jika ada
                if (pointMatch) {
                    pointMatch.forEach((ptStr, idx) => {
                        const m = ptStr.match(/\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/);
                        if (m) {
                            const px = parseFloat(m[1]);
                            const py = parseFloat(m[2]);
                            calc.setExpression({
                                id: 'pt_' + (++exprCount),
                                latex: `(${px}, ${py})`,
                                label: `(${px}, ${py})`,
                                showLabel: true,
                                color: '#dc2626',
                                pointSize: 8
                            });
                        }
                    });
                }

                calc.setMathBounds({ left: -10, right: 10, bottom: -10, top: 10 });
            }
        }, 50);
        return true;
    }

    // Fallback SVG Parabola jika offline
    setVizHeader(wrapperEl, 'Visualisasi Aljabar: Grafik Parabola Fungsi Kuadrat');

    el.innerHTML = `
        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
            <svg viewBox="0 0 340 200" class="w-full max-w-sm drop-shadow-sm select-none">
                <!-- Sumbu Kartesius -->
                <line x1="20" y1="160" x2="320" y2="160" stroke="#64748b" stroke-width="2" />
                <line x1="170" y1="185" x2="170" y2="20" stroke="#64748b" stroke-width="2" />
                <text x="315" y="152" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569">X</text>
                <text x="178" y="30" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569">Y</text>

                <!-- Kurva Parabola -->
                <path d="M 70 30 Q 170 200 270 30" fill="none" stroke="#2563eb" stroke-width="3" />

                <!-- Garis Sumbu Simetri Putus-putus -->
                <line x1="170" y1="20" x2="170" y2="180" stroke="#ed1e28" stroke-width="1.5" stroke-dasharray="4,3" />

                <!-- Titik Puncak (Vertex) -->
                <circle cx="170" cy="115" r="4.5" fill="#dc2626" />
                <text x="170" y="135" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">Titik Puncak (xp, yp)</text>
            </svg>
            <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                Grafik Fungsi Kuadrat: $y = ax^2 + bx + c$, Sumbu simetri $x = -\\frac{b}{2a}$, Nilai optimum $y = -\\frac{D}{4a}$
            </div>
        </div>
    `;
    return true;
}

// 8. RENDERER TRANSFORMASI GEOMETRI & KOORDINAT KARTESIUS (Desmos Graphing & SVG Fallback)
function tryRenderCoordinateTransformation(el, cleanText, wrapperEl) {
    // Bersihkan instruksi format seperti "misal (2,3)" atau "(Tulis dalam format (x,y)...)"
    const pureText = cleanText.replace(/\([^)]*(?:format|tulis|misal|contoh)[^)]*\)/gi, '')
                              .replace(/misal\s*\([^)]*\)/gi, '');

    const isTranslasi = /translasi|ditranslasikan|digeser|vektor\s*translasi/i.test(pureText);
    const isRefleksi = /refleksi|dicerminkan|pencerminan/i.test(pureText);
    const isRotasi = /rotasi|dirotasi|perputaran/i.test(pureText);
    const isDilatasi = /dilatasi|didilatasikan|faktor\s*skala/i.test(pureText);
    const isKoordinat = /koordinat|titik\s+[A-Z]/i.test(pureText);

    if (!isTranslasi && !isRefleksi && !isRotasi && !isDilatasi && !isKoordinat) {
        return false;
    }

    // Cari titik awal (misal "Titik A berada pada koordinat (11, 4)" atau "A(11, 4)" atau "(11, 4)")
    const pointMatch = pureText.match(/(?:titik\s+)?([A-Z])?\s*(?:berada\s+pada\s+koordinat\s*)?\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/i)
                    || pureText.match(/([A-Z])\s*\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/i)
                    || pureText.match(/\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/);

    let pName = (pointMatch && pointMatch[1]) ? pointMatch[1].toUpperCase() : 'A';
    let xVal = pointMatch ? parseFloat(pointMatch[2] || pointMatch[1]) : 3;
    let yVal = pointMatch ? parseFloat(pointMatch[3] || pointMatch[2]) : 4;
    if (isNaN(xVal)) xVal = 3;
    if (isNaN(yVal)) yVal = 4;

    // --- 8A. TRANSLASI (PERGESERAN TITIK / VEKTOR TRANSLASI) ---
    if (isTranslasi) {
        const vectorMatch = pureText.match(/(?:T|vektor|translasi)\s*(?:=|\:)?\s*\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/i);
        let dx = 4, dy = 3;

        if (vectorMatch) {
            dx = parseFloat(vectorMatch[1]);
            dy = parseFloat(vectorMatch[2]);
        } else {
            const shiftX = pureText.match(/(-?\d+(?:\.\d+)?)\s*(?:searah\s*sumbu\s*X|satuan\s*ke\s*kanan|ke\s*kanan|ke\s*kiri|unit\s*X)/i)
                        || pureText.match(/sumbu\s*X\s*(?:sebesar|sejauh)?\s*(-?\d+(?:\.\d+)?)/i);
            const shiftY = pureText.match(/(-?\d+(?:\.\d+)?)\s*(?:searah\s*sumbu\s*Y|satuan\s*ke\s*atas|ke\s*atas|ke\s*bawah|unit\s*Y)/i)
                        || pureText.match(/sumbu\s*Y\s*(?:sebesar|sejauh)?\s*(-?\d+(?:\.\d+)?)/i);
            if (shiftX) dx = parseFloat(shiftX[1]);
            if (shiftY) dy = parseFloat(shiftY[1]);
        }

        setVizHeader(wrapperEl, `Grafik Desmos: Translasi Titik ${pName}(${xVal}, ${yVal})`);

        const targetX = xVal + dx;
        const targetY = yVal + dy;

        // Gunakan Desmos Graphing Calculator jika tersedia
        if (typeof Desmos !== 'undefined') {
            const desmosId = 'desmos_' + Math.random().toString(36).substr(2, 9);
            el.innerHTML = `
                <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl shadow-inner border border-slate-200">
                    <div id="${desmosId}" class="w-full rounded-lg overflow-hidden" style="height: 270px;"></div>
                    <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                        Grafik Desmos: Titik ${pName}(${xVal}, ${yVal}) digeser ${Math.abs(dx)} unit arah X dan ${Math.abs(dy)} unit arah Y
                    </div>
                </div>
            `;

            setTimeout(() => {
                const desmosEl = document.getElementById(desmosId);
                if (desmosEl && typeof Desmos !== 'undefined') {
                    const calc = Desmos.GraphingCalculator(desmosEl, {
                        keypad: false,
                        expressions: false,
                        settingsMenu: false,
                        zoomButtons: true,
                        lockViewport: false
                    });

                    // Titik Awal A(x, y)
                    calc.setExpression({
                        id: 'pt_A',
                        latex: `(${xVal}, ${yVal})`,
                        label: `${pName}(${xVal}, ${yVal})`,
                        showLabel: true,
                        color: '#2563eb',
                        pointSize: 9
                    });

                    // Garis Pergeseran X (Horizontal)
                    const minX_seg = Math.min(xVal, targetX);
                    const maxX_seg = Math.max(xVal, targetX);
                    calc.setExpression({
                        id: 'line_dx',
                        latex: `y = ${yVal} \\{${minX_seg} \\le x \\le ${maxX_seg}\\}`,
                        color: '#2563eb',
                        lineStyle: Desmos.Styles.DASHED,
                        lineWidth: 2
                    });

                    // Garis Pergeseran Y (Vertikal)
                    const minY_seg = Math.min(yVal, targetY);
                    const maxY_seg = Math.max(yVal, targetY);
                    calc.setExpression({
                        id: 'line_dy',
                        latex: `x = ${targetX} \\{${minY_seg} \\le y \\le ${maxY_seg}\\}`,
                        color: '#2563eb',
                        lineStyle: Desmos.Styles.DASHED,
                        lineWidth: 2
                    });

                    // Titik Target A'(?, ?)
                    calc.setExpression({
                        id: 'pt_target',
                        latex: `(${targetX}, ${targetY})`,
                        label: `${pName}'(?, ?)`,
                        showLabel: true,
                        color: '#dc2626',
                        pointStyle: Desmos.Styles.OPEN,
                        pointSize: 9
                    });

                    const allX = [0, xVal, targetX];
                    const allY = [0, yVal, targetY];
                    const minX = Math.min(...allX) - 3;
                    const maxX = Math.max(...allX) + 3;
                    const minY = Math.min(...allY) - 3;
                    const maxY = Math.max(...allY) + 3;

                    calc.setMathBounds({ left: minX, right: maxX, bottom: minY, top: maxY });
                }
            }, 50);

            return true;
        }

        // SVG Fallback (jika offline)
        const svgW = 350, svgH = 220;
        const padX = 45, padY = 30;
        const plotW = svgW - 2 * padX;
        const plotH = svgH - 2 * padY;

        const allX = [0, xVal, targetX];
        const allY = [0, yVal, targetY];
        const minX = Math.min(...allX) - Math.max(2, Math.abs(dx) * 0.15);
        const maxX = Math.max(...allX) + Math.max(2, Math.abs(dx) * 0.15);
        const minY = Math.min(...allY) - Math.max(2, Math.abs(dy) * 0.15);
        const maxY = Math.max(...allY) + Math.max(2, Math.abs(dy) * 0.15);

        const toSvgX = (x) => padX + ((x - minX) / (maxX - minX || 1)) * plotW;
        const toSvgY = (y) => svgH - padY - ((y - minY) / (maxY - minY || 1)) * plotH;

        const origX = toSvgX(0);
        const origY = toSvgY(0);
        const ptAx = toSvgX(xVal);
        const ptAy = toSvgY(yVal);
        const ptCornerX = toSvgX(targetX);
        const ptCornerY = ptAy;
        const ptTargetX = toSvgX(targetX);
        const ptTargetY = toSvgY(targetY);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 ${svgW} ${svgH}" class="w-full max-w-sm drop-shadow-sm select-none">
                    <defs>
                        <marker id="arrowB" markerWidth="7" markerHeight="7" refX="5" refY="3.5" orient="auto">
                            <polygon points="0 1, 6 3.5, 0 6" fill="#2563eb" />
                        </marker>
                        <marker id="arrowP" markerWidth="7" markerHeight="7" refX="5" refY="3.5" orient="auto">
                            <polygon points="0 1, 6 3.5, 0 6" fill="#9333ea" />
                        </marker>
                    </defs>
                    <rect x="${padX}" y="${padY}" width="${plotW}" height="${plotH}" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" rx="4" />
                    <line x1="${padX - 8}" y1="${origY}" x2="${svgW - padX + 12}" y2="${origY}" stroke="#64748b" stroke-width="1.5" />
                    <line x1="${origX}" y1="${svgH - padY + 8}" x2="${origX}" y2="${padY - 12}" stroke="#64748b" stroke-width="1.5" />
                    <text x="${svgW - padX + 16}" y="${origY + 4}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#475569">X</text>
                    <text x="${origX + 4}" y="${padY - 14}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#475569">Y</text>
                    <text x="${origX - 8}" y="${origY + 12}" font-family="sans-serif" font-size="10" font-weight="bold" fill="#94a3b8">0</text>
                    <line x1="${ptAx}" y1="${ptAy}" x2="${ptCornerX}" y2="${ptCornerY}" stroke="#2563eb" stroke-width="2" stroke-dasharray="3,3" marker-end="url(#arrowB)" />
                    <text x="${(ptAx + ptCornerX) / 2}" y="${ptAy - 7}" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="bold" fill="#1d4ed8">${dx >= 0 ? '+' + dx : dx} (arah X)</text>
                    <line x1="${ptCornerX}" y1="${ptCornerY}" x2="${ptTargetX}" y2="${ptTargetY}" stroke="#2563eb" stroke-width="2" stroke-dasharray="3,3" marker-end="url(#arrowB)" />
                    <text x="${ptCornerX + (dx >= 0 ? 8 : -8)}" y="${(ptCornerY + ptTargetY) / 2 + 3}" text-anchor="${dx >= 0 ? 'start' : 'end'}" font-family="sans-serif" font-size="10" font-weight="bold" fill="#1d4ed8">${dy >= 0 ? '+' + dy : dy} (arah Y)</text>
                    <line x1="${ptAx}" y1="${ptAy}" x2="${ptTargetX}" y2="${ptTargetY}" stroke="#9333ea" stroke-width="1.8" stroke-dasharray="4,2" marker-end="url(#arrowP)" />
                    <circle cx="${ptAx}" cy="${ptAy}" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                    <text x="${ptAx}" y="${ptAy + 16}" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1e40af">${pName}(${xVal}, ${yVal})</text>
                    <circle cx="${ptTargetX}" cy="${ptTargetY}" r="5" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" />
                    <text x="${ptTargetX}" y="${ptTargetY - 8}" text-anchor="middle" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">${pName}'(?, ?)</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Translasi: Titik ${pName}(${xVal}, ${yVal}) digeser ${Math.abs(dx)} unit arah X dan ${Math.abs(dy)} unit arah Y
                </div>
            </div>
        `;
        return true;
    }

    // --- 8B. REFLEKSI (PENCERMINAN) ---
    if (isRefleksi) {
        const isSumbuX = /sumbu\s*-\s*X|sumbu\s*X/i.test(pureText);
        const isSumbuY = /sumbu\s*-\s*Y|sumbu\s*Y/i.test(pureText);
        const isGarisYX = /garis\s*y\s*=\s*x/i.test(pureText);
        const isTitikAsal = /titik\s*asal|O\(0,0\)/i.test(pureText);

        let cerminLabel = isSumbuX ? 'Sumbu-X' : (isSumbuY ? 'Sumbu-Y' : (isGarisYX ? 'Garis y = x' : 'Titik Asal O(0,0)'));
        setVizHeader(wrapperEl, `Grafik Desmos: Refleksi Titik ${pName}(${xVal}, ${yVal}) terhadap ${cerminLabel}`);

        let targetX = isSumbuY ? -xVal : (isGarisYX ? yVal : (isTitikAsal ? -xVal : xVal));
        let targetY = isSumbuX ? -yVal : (isGarisYX ? xVal : (isTitikAsal ? -yVal : yVal));

        if (typeof Desmos !== 'undefined') {
            const desmosId = 'desmos_' + Math.random().toString(36).substr(2, 9);
            el.innerHTML = `
                <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl shadow-inner border border-slate-200">
                    <div id="${desmosId}" class="w-full rounded-lg overflow-hidden" style="height: 270px;"></div>
                    <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                        Grafik Desmos: Titik ${pName}(${xVal}, ${yVal}) dicerminkan terhadap ${cerminLabel}
                    </div>
                </div>
            `;

            setTimeout(() => {
                const desmosEl = document.getElementById(desmosId);
                if (desmosEl && typeof Desmos !== 'undefined') {
                    const calc = Desmos.GraphingCalculator(desmosEl, {
                        keypad: false,
                        expressions: false,
                        settingsMenu: false,
                        zoomButtons: true,
                        lockViewport: false
                    });

                    // Titik Awal
                    calc.setExpression({
                        id: 'pt_A',
                        latex: `(${xVal}, ${yVal})`,
                        label: `${pName}(${xVal}, ${yVal})`,
                        showLabel: true,
                        color: '#2563eb',
                        pointSize: 9
                    });

                    // Garis Cermin
                    if (isSumbuX) {
                        calc.setExpression({ id: 'mirror', latex: 'y = 0', color: '#f59e0b', lineWidth: 3, lineStyle: Desmos.Styles.DASHED });
                    } else if (isSumbuY) {
                        calc.setExpression({ id: 'mirror', latex: 'x = 0', color: '#f59e0b', lineWidth: 3, lineStyle: Desmos.Styles.DASHED });
                    } else if (isGarisYX) {
                        calc.setExpression({ id: 'mirror', latex: 'y = x', color: '#f59e0b', lineWidth: 3, lineStyle: Desmos.Styles.DASHED });
                    }

                    // Garis Penghubung Proyeksi
                    calc.setExpression({
                        id: 'proj',
                        latex: `((1-t)*${xVal} + t*${targetX}, (1-t)*${yVal} + t*${targetY})`,
                        parametricDomain: { min: '0', max: '1' },
                        color: '#94a3b8',
                        lineStyle: Desmos.Styles.DASHED,
                        lineWidth: 1.5
                    });

                    // Titik Bayangan
                    calc.setExpression({
                        id: 'pt_target',
                        latex: `(${targetX}, ${targetY})`,
                        label: `${pName}'(?, ?)`,
                        showLabel: true,
                        color: '#dc2626',
                        pointStyle: Desmos.Styles.OPEN,
                        pointSize: 9
                    });

                    const allX = [0, xVal, targetX];
                    const allY = [0, yVal, targetY];
                    const maxBound = Math.max(...allX.map(Math.abs), ...allY.map(Math.abs), 5) + 3;
                    calc.setMathBounds({ left: -maxBound, right: maxBound, bottom: -maxBound, top: maxBound });
                }
            }, 50);

            return true;
        }

        const maxBound = Math.max(Math.abs(xVal), Math.abs(yVal), 5) + 2;
        const minX = -maxBound, maxX = maxBound, minY = -maxBound, maxY = maxBound;
        const svgW = 350, svgH = 220;
        const padX = 45, padY = 30;
        const plotW = svgW - 2 * padX;
        const plotH = svgH - 2 * padY;

        const toSvgX = (x) => padX + ((x - minX) / (maxX - minX)) * plotW;
        const toSvgY = (y) => svgH - padY - ((y - minY) / (maxY - minY)) * plotH;

        const origX = toSvgX(0);
        const origY = toSvgY(0);
        const ptAx = toSvgX(xVal);
        const ptAy = toSvgY(yVal);
        const ptTargetX = toSvgX(targetX);
        const ptTargetY = toSvgY(targetY);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 ${svgW} ${svgH}" class="w-full max-w-sm drop-shadow-sm select-none">
                    <rect x="${padX}" y="${padY}" width="${plotW}" height="${plotH}" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" rx="4" />
                    <line x1="${padX}" y1="${origY}" x2="${svgW - padX}" y2="${origY}" stroke="${isSumbuX ? '#f59e0b' : '#64748b'}" stroke-width="${isSumbuX ? '2.5' : '1.5'}" stroke-dasharray="${isSumbuX ? '5,3' : 'none'}" />
                    <line x1="${origX}" y1="${svgH - padY}" x2="${origX}" y2="${padY}" stroke="${isSumbuY ? '#f59e0b' : '#64748b'}" stroke-width="${isSumbuY ? '2.5' : '1.5'}" stroke-dasharray="${isSumbuY ? '5,3' : 'none'}" />
                    <line x1="${ptAx}" y1="${ptAy}" x2="${ptTargetX}" y2="${ptTargetY}" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="3,3" />
                    <circle cx="${ptAx}" cy="${ptAy}" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                    <text x="${ptAx + 8}" y="${ptAy - 8}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1e40af">${pName}(${xVal}, ${yVal})</text>
                    <circle cx="${ptTargetX}" cy="${ptTargetY}" r="5" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" />
                    <text x="${ptTargetX + 8}" y="${ptTargetY - 8}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#dc2626">${pName}'(?, ?)</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Refleksi: Titik ${pName}(${xVal}, ${yVal}) dicerminkan terhadap ${cerminLabel}
                </div>
            </div>
        `;
        return true;
    }

    // --- 8C. ROTASI / DILATASI / KOORDINAT STANDAR ---
    if (isRotasi || isDilatasi || isKoordinat) {
        setVizHeader(wrapperEl, `Grafik Desmos: Titik ${pName}(${xVal}, ${yVal})`);

        if (typeof Desmos !== 'undefined') {
            const desmosId = 'desmos_' + Math.random().toString(36).substr(2, 9);
            el.innerHTML = `
                <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl shadow-inner border border-slate-200">
                    <div id="${desmosId}" class="w-full rounded-lg overflow-hidden" style="height: 270px;"></div>
                    <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                        Grafik Desmos: Titik ${pName}(${xVal}, ${yVal}) pada Bidang Kartesius
                    </div>
                </div>
            `;

            setTimeout(() => {
                const desmosEl = document.getElementById(desmosId);
                if (desmosEl && typeof Desmos !== 'undefined') {
                    const calc = Desmos.GraphingCalculator(desmosEl, {
                        keypad: false,
                        expressions: false,
                        settingsMenu: false,
                        zoomButtons: true,
                        lockViewport: false
                    });

                    calc.setExpression({
                        id: 'pt_A',
                        latex: `(${xVal}, ${yVal})`,
                        label: `${pName}(${xVal}, ${yVal})`,
                        showLabel: true,
                        color: '#2563eb',
                        pointSize: 9
                    });

                    const maxBound = Math.max(Math.abs(xVal), Math.abs(yVal), 5) + 3;
                    calc.setMathBounds({ left: -maxBound, right: maxBound, bottom: -maxBound, top: maxBound });
                }
            }, 50);

            return true;
        }

        const maxBound = Math.max(Math.abs(xVal), Math.abs(yVal), 5) + 3;
        const minX = -maxBound, maxX = maxBound, minY = -maxBound, maxY = maxBound;
        const svgW = 350, svgH = 220;
        const padX = 45, padY = 30;
        const plotW = svgW - 2 * padX;
        const plotH = svgH - 2 * padY;

        const toSvgX = (x) => padX + ((x - minX) / (maxX - minX)) * plotW;
        const toSvgY = (y) => svgH - padY - ((y - minY) / (maxY - minY)) * plotH;

        const origX = toSvgX(0);
        const origY = toSvgY(0);
        const ptAx = toSvgX(xVal);
        const ptAy = toSvgY(yVal);

        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-inner border border-slate-200">
                <svg viewBox="0 0 ${svgW} ${svgH}" class="w-full max-w-sm drop-shadow-sm select-none">
                    <rect x="${padX}" y="${padY}" width="${plotW}" height="${plotH}" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" rx="4" />
                    <line x1="${padX}" y1="${origY}" x2="${svgW - padX}" y2="${origY}" stroke="#64748b" stroke-width="1.5" />
                    <line x1="${origX}" y1="${svgH - padY}" x2="${origX}" y2="${padY}" stroke="#64748b" stroke-width="1.5" />
                    <line x1="${ptAx}" y1="${ptAy}" x2="${ptAx}" y2="${origY}" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2" />
                    <line x1="${ptAx}" y1="${ptAy}" x2="${origX}" y2="${ptAy}" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2" />
                    <circle cx="${ptAx}" cy="${ptAy}" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                    <text x="${ptAx + 8}" y="${ptAy - 8}" font-family="sans-serif" font-size="11" font-weight="bold" fill="#1e40af">${pName}(${xVal}, ${yVal})</text>
                </svg>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Ilustrasi Koordinat Titik ${pName}(${xVal}, ${yVal}) pada Bidang Kartesius
                </div>
            </div>
        `;
        return true;
    }

    return false;
}

/**
 * RENDERER KHUSUS: RUMUS GRAFIK DINAMIS (DESMOS)
 * Jika guru mengisi rumus grafik dinamis secara eksplisit, fungsi ini akan langsung me-render
 * grafik Desmos sesuai rumus tersebut dan mengabaikan pembacaan otomatis teks soal.
 */
function tryRenderExplicitGraph(el, formula, wrapperEl) {
    if (!formula || typeof formula !== 'string' || !formula.trim()) return false;
    const cleanFormula = formula.trim();

    if (typeof Desmos !== 'undefined') {
        setVizHeader(wrapperEl, 'Grafik Desmos: Visualisasi Rumus Interaktif');
        const desmosId = 'desmos_' + Math.random().toString(36).substr(2, 9);
        el.innerHTML = `
            <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl shadow-inner border border-slate-200">
                <div id="${desmosId}" class="w-full rounded-lg overflow-hidden" style="height: 270px;"></div>
                <div class="mt-2 text-xs font-semibold text-slate-500 text-center">
                    Grafik Desmos Dinamis: ${cleanFormula}
                </div>
            </div>
        `;

        setTimeout(() => {
            const desmosEl = document.getElementById(desmosId);
            if (desmosEl && typeof Desmos !== 'undefined') {
                const calc = Desmos.GraphingCalculator(desmosEl, {
                    keypad: false,
                    expressions: false,
                    settingsMenu: false,
                    zoomButtons: true,
                    lockViewport: false
                });

                const expressions = cleanFormula.split(/[\n;]+/).map(s => s.trim()).filter(Boolean);
                let exprCount = 0;
                let maxCoord = 5;

                expressions.forEach(expr => {
                    // Cek jika titik koordinat murni, contoh: (3, 4) atau 3, 4
                    const ptMatch = expr.match(/^\(?\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)?$/);
                    if (ptMatch) {
                        const px = parseFloat(ptMatch[1]);
                        const py = parseFloat(ptMatch[2]);
                        calc.setExpression({
                            id: 'pt_' + (++exprCount),
                            latex: `(${px}, ${py})`,
                            label: `(${px}, ${py})`,
                            showLabel: true,
                            color: '#2563eb',
                            pointSize: 9
                        });
                        maxCoord = Math.max(maxCoord, Math.abs(px), Math.abs(py));
                    } else {
                        // Persamaan fungsi / garis matematika (misal: y = 2x + 3, y = x^2 + 4x + 3)
                        let latex = expr.replace(/²/g, '^2').replace(/f\(x\)/i, 'y');
                        calc.setExpression({
                            id: 'expr_' + (++exprCount),
                            latex: latex,
                            color: '#2563eb',
                            lineWidth: 2.5
                        });
                    }
                });

                const bound = Math.max(maxCoord + 3, 10);
                calc.setMathBounds({ left: -bound, right: bound, bottom: -bound, top: bound });
            }
        }, 50);
        return true;
    }
    return false;
}

/**
 * MAIN VISUALIZER ROUTER & DISPATCHER
 */
window.renderGeometryVisualizer = function (containerId, text, wrapperSelector, explicitGraphFormula) {
    const el = document.getElementById(containerId);
    if (!el) return 0;

    const wrapperEl = wrapperSelector ? document.querySelector(wrapperSelector) : null;

    // 1. JIKA GURU MENGISI RUMUS GRAFIK DINAMIS:
    // Utamakan rumus grafik dinamis dan JANGAN jalankan auto-reader teks soal
    if (explicitGraphFormula && typeof explicitGraphFormula === 'string' && explicitGraphFormula.trim()) {
        if (tryRenderExplicitGraph(el, explicitGraphFormula, wrapperEl)) {
            return 1;
        }
    }

    // 2. JIKA GURU TIDAK MENGISI RUMUS GRAFIK DINAMIS:
    // Sistem secara otomatis membaca dan mengenali teks soal (Auto-detect)
    const cleanText = text || '';

    // Dispatcher Urutan Prioritas
    if (tryRenderCoordinateTransformation(el, cleanText, wrapperEl)) return 1;
    if (tryRenderAngleGeometry(el, cleanText, wrapperEl)) return 1;
    if (tryRender2DShape(el, cleanText, wrapperEl)) return 1;
    if (tryRender3DSolid(el, cleanText, wrapperEl)) return 1;
    if (tryRenderStatisticsChart(el, cleanText, wrapperEl)) return 1;
    if (tryRenderVennDiagram(el, cleanText, wrapperEl)) return 1;
    if (tryRenderNumberLine(el, cleanText, wrapperEl)) return 1;
    if (tryRenderParabola(el, cleanText, wrapperEl)) return 1;

    // Fallback: JSXGraph Kartesius
    return window.renderCoordinateVisualizer(containerId, cleanText, wrapperSelector);
};

const SMP_PRESETS = {
    pythagoras: {
        template: 'Sebuah segitiga siku-siku memiliki panjang alas {a} cm dan tinggi {b} cm. Berapakah panjang sisi miringnya?',
        format: 'angka',
        variables: [
            { name: 'a', type: 'integer', min: 3, max: 20, kelipatan: '', formula: '', count: '' },
            { name: 'b', type: 'integer', min: 4, max: 21, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'sqrt(a^2 + b^2)',
        pembahasan: 'Berdasarkan Teorema Pythagoras, $c = \\sqrt{a^2 + b^2}$. \n\nMaka:\n$c = \\sqrt{ {a}^2 + {b}^2 }$\n$c = \\sqrt{ {{ a*a }} + {{ b*b }} }$\n$c = \\sqrt{ {{ a*a + b*b }} }$\n$c = {{ sqrt(a*a + b*b) }}$ cm.'
    },
    sudut_berpenyiku: {
        template: 'Perhatikan gambar berikut. Sudut P dan sudut Q saling berpenyiku. Jika besar sudut P = {a}°, berapakah besar sudut Q?',
        format: 'bulat',
        variables: [
            { name: 'a', type: 'integer', min: 15, max: 75, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'sudut_penyiku(a)',
        pembahasan: 'Dua sudut dikatakan saling berpenyiku (komplementer) jika jumlah kedua sudut sama dengan $90^\\circ$.\n\nMaka:\n$\\angle P + \\angle Q = 90^\\circ$\n${a}^\\circ + \\angle Q = 90^\\circ$\n$\\angle Q = 90^\\circ - {a}^\\circ$\n$\\angle Q = {{ 90 - a }}^\\circ$\n\nJadi, besar sudut Q adalah {{ sudut_penyiku(a) }}^\\circ.'
    },
    sudut_berpelurus: {
        template: 'Perhatikan gambar berikut. Sudut A dan sudut B saling berpelurus. Jika besar sudut A = {a}°, berapakah besar sudut B?',
        format: 'bulat',
        variables: [
            { name: 'a', type: 'integer', min: 20, max: 160, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'sudut_pelurus(a)',
        pembahasan: 'Dua sudut dikatakan saling berpelurus (suplementer) jika jumlah kedua sudut sama dengan $180^\\circ$.\n\nMaka:\n$\\angle A + \\angle B = 180^\\circ$\n${a}^\\circ + \\angle B = 180^\\circ$\n$\\angle B = 180^\\circ - {a}^\\circ$\n$\\angle B = {{ 180 - a }}^\\circ$\n\nJadi, besar sudut B adalah {{ sudut_pelurus(a) }}^\\circ.'
    },
    sudut_bertolak_belakang: {
        template: 'Dua garis berpotongan membentuk sudut {a}°. Berapakah besar sudut yang bertolak belakang dengannya?',
        format: 'bulat',
        variables: [
            { name: 'a', type: 'integer', min: 20, max: 160, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'a',
        pembahasan: 'Dua sudut yang saling bertolak belakang (vertically opposite angles) memiliki besar sudut yang sama (kongruen).\n\nKarena salah satu sudut besarnya ${a}^\\circ$, maka sudut yang bertolak belakang dengannya juga sama besar, yaitu ${{ a }}^\\circ$.\n\nJadi, besar sudut tersebut adalah {{ a }}^\\circ.'
    },
    sudut_segitiga: {
        template: 'Pada segitiga ABC, sudut A = {a}° dan sudut B = {b}°. Berapakah besar sudut C?',
        format: 'bulat',
        variables: [
            { name: 'a', type: 'integer', min: 30, max: 80, kelipatan: '', formula: '', count: '' },
            { name: 'b', type: 'integer', min: 30, max: 80, kelipatan: '', formula: '', count: '' }
        ],
        formula: '180 - (a + b)',
        pembahasan: 'Jumlah seluruh sudut dalam pada sebuah segitiga selalu sama dengan $180^\\circ$.\n\nMaka:\n$\\angle A + \\angle B + \\angle C = 180^\\circ$\n${a}^\\circ + {b}^\\circ + \\angle C = 180^\\circ$\n${{ a + b }}^\\circ + \\angle C = 180^\\circ$\n$\\angle C = 180^\\circ - {{ a + b }}^\\circ$\n$\\angle C = {{ 180 - a - b }}^\\circ$\n\nJadi, besar sudut C adalah {{ 180 - a - b }}^\\circ.'
    },
    sudut_berpelurus_rasio: {
        template: 'Jika dua sudut berpelurus dan perbandingannya {a}:{b}, berapakah sudut yang lebih besar?',
        format: 'bulat',
        variables: [
            { name: 'a', type: 'integer', min: 1, max: 4, kelipatan: '', formula: '', count: '' },
            { name: 'b', type: 'integer', min: 2, max: 8, kelipatan: '', formula: '', count: '' }
        ],
        formula: '(nilai_maks(a, b) / (a + b)) * 180',
        pembahasan: 'Dua sudut yang saling berpelurus memiliki jumlah besar sudut sama dengan $180^\\circ$.\n\nMisalkan perbandingan kedua sudut adalah ${a} : {b}$, maka:\nBesar perbandingan sudut yang lebih besar = ${{ nilai_maks(a, b) }}\nJumlah perbandingan = ${a} + {b} = {{ a + b }}$\n\nBesar sudut yang lebih besar:\n$= \\frac{ {{ nilai_maks(a, b) }} }{ {{ a + b }} } \\times 180^\\circ$\n$= {{ (nilai_maks(a, b) / (a + b)) * 180 }}^\\circ$\n\nJadi, besar sudut yang lebih besar adalah {{ (nilai_maks(a, b) / (a + b)) * 180 }}^\\circ.'
    },
    geometri_luas: {
        template: 'Perhatikan gambar persegi panjang di samping. Jika diketahui panjangnya adalah {panjang} cm dan lebarnya adalah {lebar} cm. Berapakah luas persegi panjang tersebut?',
        format: 'angka',
        variables: [
            { name: 'panjang', type: 'integer', min: 10, max: 50, kelipatan: '', formula: '', count: '' },
            { name: 'lebar', type: 'integer', min: 2, max: 15, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'panjang * lebar',
        pembahasan: 'Luas persegi panjang = $p \\times l$\n\nLuas = ${panjang} \\times {lebar}$\nLuas = ${{ panjang * lebar }}$ cm$^2$'
    },
    geometri_trapesium: {
        template: 'Sebuah trapesium memiliki sisi sejajar atas a = {a} cm, sisi sejajar bawah b = {b} cm, dan tinggi t = {t} cm. Berapakah luas trapesium tersebut?',
        format: 'angka',
        variables: [
            { name: 'a', type: 'integer', min: 6, max: 15, kelipatan: '', formula: '', count: '' },
            { name: 'b', type: 'integer', min: 16, max: 28, kelipatan: '', formula: '', count: '' },
            { name: 't', type: 'integer', min: 4, max: 12, kelipatan: 2, formula: '', count: '' }
        ],
        formula: '0.5 * (a + b) * t',
        pembahasan: 'Luas Trapesium = $\\frac{1}{2} \\times (a + b) \\times t$\n\n$= \\frac{1}{2} \\times ({a} + {b}) \\times {t}$\n$= \\frac{1}{2} \\times {{ a + b }} \\times {t}$\n$= {{ 0.5 * (a + b) * t }}$ cm$^2$'
    },
    geometri_lingkaran_juring: {
        template: 'Sebuah lingkaran memiliki jari-jari r = {r} cm dengan sudut pusat juring {sudut}°. Berapakah luas juring tersebut? (Gunakan π = 3.14)',
        format: 'desimal',
        variables: [
            { name: 'r', type: 'integer', min: 7, max: 21, kelipatan: 7, formula: '', count: '' },
            { name: 'sudut', type: 'integer', min: 30, max: 120, kelipatan: 30, formula: '', count: '' }
        ],
        formula: '(sudut / 360) * 3.14 * r * r',
        pembahasan: 'Luas Juring = $\\frac{\\alpha}{360^\\circ} \\times \\pi r^2$\n\n$= \\frac{ {sudut} }{360} \\times 3.14 \\times {r}^2$\n$= {{ (sudut / 360) * 3.14 * r * r }}$ cm$^2$'
    },
    bangun_ruang_tabung: {
        template: 'Sebuah tabung memiliki jari-jari r = {r} cm dan tinggi t = {t} cm. Berapakah volume tabung tersebut? (Gunakan π = 22/7)',
        format: 'angka',
        variables: [
            { name: 'r', type: 'integer', min: 7, max: 21, kelipatan: 7, formula: '', count: '' },
            { name: 't', type: 'integer', min: 10, max: 30, kelipatan: 5, formula: '', count: '' }
        ],
        formula: '(22/7) * r * r * t',
        pembahasan: 'Volume Tabung = $\\pi r^2 t$\n\n$= \\frac{22}{7} \\times {r}^2 \\times {t}$\n$= {{ (22/7) * r * r * t }}$ cm$^3$'
    },
    bangun_ruang_kerucut: {
        template: 'Sebuah kerucut memiliki jari-jari r = {r} cm dan tinggi t = {t} cm. Berapakah volume kerucut tersebut? (Gunakan π = 22/7)',
        format: 'angka',
        variables: [
            { name: 'r', type: 'integer', min: 7, max: 21, kelipatan: 7, formula: '', count: '' },
            { name: 't', type: 'integer', min: 9, max: 27, kelipatan: 3, formula: '', count: '' }
        ],
        formula: '(1/3) * (22/7) * r * r * t',
        pembahasan: 'Volume Kerucut = $\\frac{1}{3} \\pi r^2 t$\n\n$= \\frac{1}{3} \\times \\frac{22}{7} \\times {r}^2 \\times {t}$\n$= {{ (1/3) * (22/7) * r * r * t }}$ cm$^3$'
    },
    garis_bilangan_suhu: {
        template: 'Suhu di kota Puncak pada siang hari adalah {s} derajat Celcius. Jika pada malam harinya suhu tersebut turun sebanyak {t} derajat Celcius, berapakah suhu pada malam hari?',
        format: 'angka',
        variables: [
            { name: 's', type: 'integer', min: 5, max: 25, kelipatan: '', formula: '', count: '' },
            { name: 't', type: 'integer', min: 8, max: 20, kelipatan: '', formula: '', count: '' }
        ],
        formula: 's - t',
        pembahasan: 'Suhu malam hari = Suhu siang hari - Penurunan suhu\n= ${s} - {t}$\n= ${{ s - t }}^\\circ\\text{C}$'
    },
    diagram_venn_himpunan: {
        template: 'Dari 40 siswa di suatu kelas, {a} siswa gemar Matematika, {b} siswa gemar IPA, dan {c} siswa gemar keduanya. Berapakah banyak siswa yang tidak gemar Matematika maupun IPA?',
        format: 'angka',
        variables: [
            { name: 'a', type: 'integer', min: 18, max: 25, kelipatan: '', formula: '', count: '' },
            { name: 'b', type: 'integer', min: 15, max: 22, kelipatan: '', formula: '', count: '' },
            { name: 'c', type: 'integer', min: 5, max: 10, kelipatan: '', formula: '', count: '' }
        ],
        formula: '40 - (a + b - c)',
        pembahasan: 'Berdasarkan Diagram Venn:\nSiswa yang gemar paling sedikit salah satu mata pelajaran = $n(A \\cup B) = n(A) + n(B) - n(A \\cap B)$\n$= {a} + {b} - {c} = {{ a + b - c }}$ siswa\n\nBanyak siswa yang tidak gemar keduanya:\n$= 40 - {{ a + b - c }} = {{ 40 - (a + b - c) }}$ siswa.'
    },
    spldv: {
        template: 'Diketahui harga {jumlah_buku} buku dan {jumlah_pensil} pensil adalah Rp{total_harga}. Jika harga 1 buku adalah Rp{harga_buku}, berapakah harga 1 pensil?',
        format: 'angka',
        variables: [
            { name: 'jumlah_buku', type: 'integer', min: 2, max: 5, kelipatan: '', formula: '', count: '' },
            { name: 'jumlah_pensil', type: 'integer', min: 2, max: 5, kelipatan: '', formula: '', count: '' },
            { name: 'harga_buku', type: 'integer', min: 2000, max: 10000, kelipatan: 500, formula: '', count: '' },
            { name: 'harga_pensil', type: 'integer', min: 1000, max: 5000, kelipatan: 500, formula: '', count: '' },
            { name: 'total_harga', type: 'derived', min: 1, max: 10, kelipatan: '', formula: '(jumlah_buku * harga_buku) + (jumlah_pensil * harga_pensil)', count: '' }
        ],
        formula: 'harga_pensil',
        pembahasan: 'Model matematika:\n${jumlah_buku}B + {jumlah_pensil}P = {total_harga}$\n$B = {harga_buku}$\n\nSubstitusi harga buku:\n${jumlah_buku}({harga_buku}) + {jumlah_pensil}P = {total_harga}$\n${{ jumlah_buku * harga_buku }} + {jumlah_pensil}P = {total_harga}$\n${jumlah_pensil}P = {total_harga} - {{ jumlah_buku * harga_buku }}$\n${jumlah_pensil}P = {{ total_harga - (jumlah_buku * harga_buku) }}$\n\n$P = \\frac{ {{ total_harga - (jumlah_buku * harga_buku) }} }{ {jumlah_pensil} }$\n$P = {{ (total_harga - (jumlah_buku * harga_buku)) / jumlah_pensil }}$'
    },
    diskon: {
        template: 'Sebuah toko memberikan diskon {persen_diskon}% untuk sepasang sepatu. Jika harga awal sepatu tersebut adalah Rp{harga_awal}, berapakah harga yang harus dibayar setelah diskon?',
        format: 'angka',
        variables: [
            { name: 'persen_diskon', type: 'integer', min: 10, max: 75, kelipatan: 5, formula: '', count: '' },
            { name: 'harga_awal', type: 'integer', min: 50000, max: 500000, kelipatan: 10000, formula: '', count: '' }
        ],
        formula: 'diskon(harga_awal, persen_diskon)',
        pembahasan: 'Rumus harga setelah diskon:\n$Harga = Harga Awal - (Harga Awal \\times \\frac{Diskon}{100})$\n\n$Harga = {harga_awal} - ({harga_awal} \\times \\frac{ {persen_diskon} }{100})$\n$Harga = {harga_awal} - {{ harga_awal * (persen_diskon / 100) }}$\n$Harga = {{ diskon(harga_awal, persen_diskon) }}$\n\nJadi, harga setelah diskon adalah Rp{{ diskon(harga_awal, persen_diskon) }}.'
    },
    statistika_rata_rata: {
        template: 'Nilai ujian matematika dari 5 orang siswa adalah sebagai berikut: {n1}, {n2}, {n3}, {n4}, dan {n5}. Berapakah rata-rata nilai ujian mereka?',
        format: 'angka',
        variables: [
            { name: 'n1', type: 'integer', min: 60, max: 100, kelipatan: 5, formula: '', count: '' },
            { name: 'n2', type: 'integer', min: 60, max: 100, kelipatan: 5, formula: '', count: '' },
            { name: 'n3', type: 'integer', min: 60, max: 100, kelipatan: 5, formula: '', count: '' },
            { name: 'n4', type: 'integer', min: 60, max: 100, kelipatan: 5, formula: '', count: '' },
            { name: 'n5', type: 'integer', min: 60, max: 100, kelipatan: 5, formula: '', count: '' }
        ],
        formula: 'rata_rata(n1, n2, n3, n4, n5)',
        pembahasan: 'Rumus rata-rata:\n$\\bar{x} = \\frac{\\text{Jumlah Seluruh Data}}{\\text{Banyaknya Data}}$\n\n$\\bar{x} = \\frac{ {n1} + {n2} + {n3} + {n4} + {n5} }{ 5 }$\n$\\bar{x} = \\frac{ {{ n1 + n2 + n3 + n4 + n5 }} }{ 5 }$\n$\\bar{x} = {{ rata_rata(n1, n2, n3, n4, n5) }}$'
    },
    refleksi: {
        template: 'Sebuah titik A({x}, {y}) dicerminkan terhadap sumbu X. Tentukan koordinat titik bayangannya!',
        format: 'koordinat',
        variables: [
            { name: 'x', type: 'integer', min: -10, max: 10, kelipatan: '', formula: '', count: '' },
            { name: 'y', type: 'integer', min: -10, max: 10, kelipatan: '', formula: '', count: '' }
        ],
        formula: 'refleksi_x(x, y)',
        graphFormula: '({x}, {y})',
        pembahasan: 'Pencerminan terhadap Sumbu X memiliki aturan:\n$A(x, y) \\rightarrow A\'(x, -y)$\n\nKoordinat titik awal: A({x}, {y})\nMaka bayangannya:\n$A\'({x}, -({y}))$\n$A\'({x}, {{ -y }})$\n\nJadi, koordinat bayangannya adalah {{ refleksi_x(x, y) }}.'
    }
};

window.SMP_PRESETS = SMP_PRESETS;

document.addEventListener('alpine:init', () => {
    Alpine.data('questionEditor', (init = {}) => ({
        template: init.template ?? '',
        formula: init.formula ?? '',
        graphFormula: init.graphFormula ?? '',
        format: init.format ?? 'angka',
        pembahasan: init.pembahasan ?? '',
        variables:
            init.variables && init.variables.length
                ? init.variables
                : [
                      { name: 'x', type: 'integer', min: 1, max: 10, kelipatan: '', decimals: 2, formula: '', count: '' },
                      { name: 'y', type: 'integer', min: 1, max: 10, kelipatan: '', decimals: 2, formula: '', count: '' },
                  ],
        isLoading: false,
        hasPreview: false,
        previewTemplate: '',
        previewGraph: null,
        previewVariables: {},
        previewAnswer: null,
        previewError: null,
        // Kunci jawaban dirender sebagai matematika bila memungkinkan:
        // - mengandung perintah LaTeX (mis. \sqrt) -> render langsung
        // - berbentuk pecahan a/b -> render sebagai \frac{a}{b} (pecahan bertingkat)
        // - selain itu tampil apa adanya (angka, koordinat, himpunan).
        formattedAnswer() {
            if (this.previewAnswer === null || this.previewAnswer === undefined) return '';
            const s = String(this.previewAnswer).trim();
            const escaped = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            if (s.includes('\\')) {
                return '\\(' + escaped + '\\)';
            }
            const frac = s.match(/^(-?\d+)\s*\/\s*(-?\d+)$/);
            if (frac) {
                return '\\(\\frac{' + frac[1] + '}{' + frac[2] + '}\\)';
            }
            return escaped;
        },
        imagePreviewUrl: init.imageUrl ?? null,
        openMathModal: false,
        openFormulaMathModal: false,

        handleImageUpload(event) {
            const file = event.target.files[0];
            const removeCheckbox = document.querySelector('input[name="remove_image"]');
            
            if (file) {
                this.imagePreviewUrl = URL.createObjectURL(file);
                if (removeCheckbox) removeCheckbox.checked = false;
            } else {
                if (removeCheckbox && removeCheckbox.checked) {
                    this.imagePreviewUrl = null;
                } else {
                    this.imagePreviewUrl = init.imageUrl ?? null;
                }
            }
        },

        handleRemoveImageCheckbox(event) {
            if (event.target.checked) {
                this.imagePreviewUrl = null;
                const fileInput = document.getElementById('image');
                if (fileInput) fileInput.value = '';
            } else {
                const fileInput = document.getElementById('image');
                if (fileInput && fileInput.files && fileInput.files[0]) {
                     this.imagePreviewUrl = URL.createObjectURL(fileInput.files[0]);
                } else {
                     this.imagePreviewUrl = init.imageUrl ?? null;
                }
            }
        },

        insertMathToTemplate() {
            const mf = document.getElementById('soal_math_field');
            if (mf && mf.value) {
                const latex = '\\(' + mf.value + '\\)';
                const textarea = document.getElementById('question_template');
                
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    this.template = this.template.substring(0, start) + latex + this.template.substring(end);
                    
                    this.$nextTick(() => {
                        textarea.focus();
                        textarea.setSelectionRange(start + latex.length, start + latex.length);
                    });
                } else {
                    this.template += ' ' + latex;
                }
                
                this.openMathModal = false;
                mf.value = '';
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Rumus matematika disisipkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },

        insertMathToFormula() {
            const mf = document.getElementById('formula_math_field');
            if (mf && mf.value) {
                // Konversi MathLive latex output menjadi MathExecutor compatible string (ASCII Math)
                let mathString = mf.getValue('ascii-math');
                
                // Beberapa penyesuaian dari ascii-math MathLive agar kompatibel dengan NXP\MathExecutor
                // Contoh ascii-math: sqrt(x) -> sudah kompatibel, x*y -> kompatibel
                mathString = mathString.replace(/⋅/g, '*'); // ganti dot product dengan bintang
                mathString = mathString.replace(/\|(.*)\|/g, 'abs($1)'); // |x| menjadi abs(x)
                
                this.formula = mathString;
                
                this.openFormulaMathModal = false;
                mf.value = '';
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Logika rumus matematika diterapkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },

        addVariable() {
            this.variables.push({ name: '', type: 'integer', min: 1, max: 10, kelipatan: '', decimals: 2, formula: '', count: 3 });
        },

        removeVariable(index) {
            this.variables.splice(index, 1);
        },

        autoDetectVariables() {
            const regex = /{([a-zA-Z_][a-zA-Z0-9_]*)}/g;
            let match;
            const detected = new Set();
            while ((match = regex.exec(this.template)) !== null) {
                detected.add(match[1]);
            }
            while ((match = regex.exec(this.pembahasan)) !== null) {
                detected.add(match[1]);
            }

            let addedCount = 0;
            detected.forEach(varName => {
                const exists = this.variables.find(v => v.name === varName);
                if (!exists) {
                    this.variables.push({ name: varName, type: 'integer', min: 1, max: 10, kelipatan: '', decimals: 2, formula: '', count: 3 });
                    addedCount++;
                }
            });

            if (addedCount > 0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Variabel Terdeteksi',
                    text: `${addedCount} variabel baru berhasil ditambahkan!`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Variabel Baru',
                    text: 'Semua variabel di teks sudah terdaftar.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },

        loadPreset(preset) {
            if (!preset) return;
            Swal.fire({
                title: 'Ganti Template?',
                text: "Isian form saat ini akan ditimpa. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E63946',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Ya, Gunakan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.template = preset.template;
                    this.variables = JSON.parse(JSON.stringify(preset.variables));
                    this.formula = preset.formula;
                    this.format = preset.format || 'angka';
                    this.pembahasan = preset.pembahasan || '';
                    this.graphFormula = preset.graphFormula || '';
                    Swal.fire({
                        icon: 'success',
                        title: 'Preset Dimuat!',
                        text: 'Silakan sesuaikan konfigurasi sesuka hati.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        },

        async runPreview() {
            this.isLoading = true;
            this.previewError = null;
            this.hasPreview = false;

            const rules = {};
            this.variables.forEach((v) => {
                if (v.name) {
                    if (v.type === 'derived') {
                        rules[v.name] = {
                            type: 'derived',
                            formula: v.formula || '',
                        };
                    } else {
                        rules[v.name] = {
                            type: v.type || 'integer',
                            min: v.type === 'float' ? parseFloat(v.min) : parseInt(v.min, 10),
                            max: v.type === 'float' ? parseFloat(v.max) : parseInt(v.max, 10),
                        };
                        if (v.kelipatan) {
                            rules[v.name].kelipatan = parseInt(v.kelipatan, 10);
                        }
                        if (v.decimals !== undefined && v.decimals !== '') {
                            rules[v.name].decimals = parseInt(v.decimals, 10);
                        } else if (v.type === 'float') {
                            rules[v.name].decimals = 2;
                        }
                        if (v.count) {
                            rules[v.name].count = parseInt(v.count, 10);
                        }
                    }
                }
            });

            const previewUrl = document.querySelector('meta[name="guru-soal-pratinjau-url"]')?.content;
            if (!previewUrl) {
                this.previewError = 'URL preview tidak tersedia.';
                this.isLoading = false;
                return;
            }

            try {
                const response = await fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        question_template: this.template,
                        answer_formula: this.formula,
                        graph_formula: this.graphFormula,
                        format: this.format,
                        variable_rules: rules,
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.previewTemplate = data.parsed_template;
                    this.previewGraph = data.parsed_graph;
                    this.previewVariables = data.variables;
                    this.previewAnswer = data.answer;
                    this.hasPreview = true;
                    this.$nextTick(() => {
                        if (window.MathJax) {
                            window.MathJax.typesetPromise();
                        }
                    });
                } else {
                    this.previewError = data.message || 'Terjadi kesalahan saat memvalidasi rumus.';
                }
            } catch (error) {
                this.previewError = 'Koneksi ke server gagal atau terjadi kesalahan internal.';
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        // Dynamic Wizard State
        openDynamicWizard: false,
        dynamicWizardInput: '',
        dynamicWizardLoading: false,
        dynamicWizardResult: null,
        dynamicWizardError: null,

        async runDynamicWizard() {
            let inputText = this.dynamicWizardInput ? this.dynamicWizardInput.trim() : '';
            if (!inputText) {
                inputText = "Soal:\n" + this.template + "\n\nPembahasan:\n" + this.pembahasan;
            }
            
            if (inputText.trim().length < 5) return;
            
            this.dynamicWizardLoading = true;
            this.dynamicWizardError = null;
            this.dynamicWizardResult = null;

            try {
                const response = await fetch('/guru/ai/bantu-template', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        deskripsi: inputText
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.dynamicWizardResult = data.data;
                    this.applyDynamicWizard(); // Langsung terapkan ke form
                    
                    // Dispatch event for usage update
                    window.dispatchEvent(new CustomEvent('ai-usage-updated', {
                        detail: { sisa: data.sisa, batas: data.batas }
                    }));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Rumus jawaban dan aturan variabel berhasil dihasilkan!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (response.status === 429) {
                    this.dynamicWizardError = data.pesan || 'Kuota AI habis.';
                } else {
                    this.dynamicWizardError = data.pesan || 'Terjadi kesalahan pada AI.';
                }
            } catch (error) {
                this.dynamicWizardError = 'Koneksi ke server gagal atau terjadi kesalahan internal.';
                console.error(error);
            } finally {
                this.dynamicWizardLoading = false;
            }
        },

        applyDynamicWizard() {
            if (!this.dynamicWizardResult) return;

            const res = this.dynamicWizardResult;
            
            // Set simple fields
            this.template = res.question_template || '';
            this.formula = res.answer_formula || '';
            this.format = res.jawaban_format || 'angka';
            
            // If Trix editor is available for pembahasan
            if (res.pembahasan_template) {
                this.pembahasan = res.pembahasan_template;
                const trixEditor = document.querySelector("trix-editor[input='pembahasan']");
                if (trixEditor && trixEditor.editor) {
                    trixEditor.editor.loadHTML(res.pembahasan_template);
                }
            }

            // Transform variable_rules to UI array
            if (res.variable_rules) {
                const newVars = [];
                for (const [name, rule] of Object.entries(res.variable_rules)) {
                    newVars.push({
                        name: name,
                        type: rule.type || 'integer',
                        min: rule.min !== undefined ? rule.min : '',
                        max: rule.max !== undefined ? rule.max : '',
                        kelipatan: rule.kelipatan || '',
                        formula: rule.formula || '',
                        count: rule.count || ''
                    });
                }
                this.variables = newVars;
            }

            this.openDynamicWizard = false;
            
            // Auto-trigger preview
            this.$nextTick(() => this.runPreview());
        },
    }));
});

Alpine.start();
