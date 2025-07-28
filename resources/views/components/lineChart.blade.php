@props([
'id' => 'lineChart-' . Str::random(6),
'labels' => [],
'datasets' => [],
'height' => 120,
])

<div class="w-full">
    <canvas id="{{ $id }}" height="{{ $height }}"></canvas>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById(@json($id));
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: @json($datasets)
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // BẬT HOẶC TẮT CHỦ THÍCH
                    }
                }
            }
        });
    }
});
</script>
@endpush