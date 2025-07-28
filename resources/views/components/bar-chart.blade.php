@php
$chartId = $id ?? 'barChart' . uniqid();
@endphp

{{-- file: resources/views/components/bar-chart.blade.php --}}

<div class="flex items-start gap-8">
    <div class="flex-1">
        <canvas id="{{ $id }}" height="{{ $height }}"></canvas>
    </div>
    <div class="w-64 flex flex-col items-center">
        @if($rightTitle)
        <div id="right-title-{{ $id }}"
            class="right-title w-full text-[18px] leading-[26px] font-medium mb-2 text-left border-b border-[#eee] px-4 py-[22px] font-bold">
            {{ $rightTitle }}
        </div>
        @endif
        @foreach($legend as $index => $item)
        <div id="legend-item-{{ $id }}-{{ $index }}"
            class="legend-item flex items-center justify-between w-full border-b border-[#eee] px-4 py-[22px]">
            <div class="flex items-center gap-2 text-sm">
                <p class="inline-block w-2 h-2 rounded-full" style="background: {{ $item['color'] }}">
                </p>
                <p> {{ $item['label'] }}</p>
            </div>
            <span class="text-sm font-semibold"> {{ $item['value'] }}</span>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')

@endpush