@props([
    'selected' => null,
])

@php
    $nilai = old('tahun', $selected ?? date('Y'));
@endphp

<select
    {{ $attributes->merge(['name' => 'tahun', 'id' => 'tahun']) }}
    required
    class="mt-0 block w-full cursor-pointer rounded-xl border-0 bg-gray-50 py-3 pl-3 pr-10 text-sm font-semibold text-gray-900 shadow-inner ring-1 ring-inset ring-gray-200/80 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#ed1e28]/25"
>
    @for ($y = 2035; $y >= 2015; $y--)
        <option value="{{ $y }}" @selected((int) $nilai === $y)>TA {{ $y }} / {{ $y + 1 }}</option>
    @endfor
</select>
