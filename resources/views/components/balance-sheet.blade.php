@php
use Illuminate\Support\Str;

$uniqueId = Str::random(8); // Tạo ID duy nhất cho component
$symbol = $symbol ?? 'ABB';
$currentYear = date('Y');

// Sắp xếp và lấy 4 quý mới nhất cho $balanceQuarterlyStructure
$balanceQuarterlyStructure = collect($balanceQuarterlyStructure)
->sortBy(function ($item) {
return [$item['year'], $item['quarter']];
})
->reverse() // Đảm bảo quý mới nhất ở cuối
->take(4)
->values()
->all();

$quarterlyLabels = collect($balanceQuarterlyStructure)
->map(fn($item) => 'Q' . $item['quarter'] . '/' . $item['year'])
->values()
->all();

$yearlyLabels = collect($balanceYearlyStructure)
->pluck('year')
->sort()
->reverse()
->take(4)
->sort()
->values()
->all();

function buildDatasets($data, $labels = [], $isQuarterly = true) {
if (empty($data)) {
return [
[
'label' => 'Không có dữ liệu',
'data' => [],
'backgroundColor' => '#ccc',
'borderRadius' => 4,
'barPercentage' => 0.3,
],
];
}

$fields = array_keys($data[0] ?? []);
$exclude = ['year', 'quarter', 'symbol', 'timestamp']; // Thêm 'timestamp' vào danh sách loại trừ

$colorMap = [
'total_assets' => '#000000',
'liabilities' => '#0032F0',
'debt_to_assets' => '#B51001',
];

$datasets = [];

foreach ($fields as $field) {
if (in_array($field, $exclude)) continue;

$isPercentage = Str::contains($field, ['ratio', 'rate', 'percent']) || $field === 'debt_to_assets';

$filtered = collect($data);
if (!$isQuarterly && !empty($labels)) {
$filtered = $filtered->filter(fn($item) => in_array($item['year'], $labels));
}

$values = $filtered->map(function ($item) use ($field, $isPercentage) {
$value = $item[$field] ?? 0; // Xử lý giá trị thiếu
return $isPercentage
? round((float) $value * 100, 2)
: (float) $value / 1_000_000_000;
})->values();

$color = $colorMap[$field] ?? '#' . substr(md5($field), 0, 6);

$dataset = [
'label' => Str::headline(str_replace('_', ' ', $field)),
'data' => $values->all(),
'backgroundColor' => $color,
'borderRadius' => 4,
'barPercentage' => 0.3,
];

if ($isPercentage) {
$dataset['type'] = 'line';
$dataset['borderColor'] = $color;
$dataset['fill'] = false;
$dataset['tension'] = 0.1;
$dataset['borderWidth'] = 2;
$dataset['yAxisID'] = 'percentage';
}

$datasets[] = $dataset;
}

return $datasets;
}

$balanceQuarterlyStructuresets = buildDatasets($balanceQuarterlyStructure, [], true);
$balanceYearlyStructuresets = buildDatasets($balanceYearlyStructure, $yearlyLabels, false);

// Debug datasets (có thể bỏ sau khi xác nhận)
// @dd($balanceQuarterlyStructuresets, $balanceYearlyStructuresets);

$legend = collect($balanceQuarterlyStructuresets)->map(function ($dataset) {
$lastValue = end($dataset['data']) ?? 0;
return [
'label' => $dataset['label'],
'color' => $dataset['borderColor'] ?? $dataset['backgroundColor'] ?? '#ccc',
'value' => !empty($dataset['data']) ? number_format($lastValue, 2) : 'N/A',
];
})->values()->all();
@endphp

<div class="bg-white rounded-xl border p-6 mt-6 balance-sheet-{{ $uniqueId }}">
    <h4 class="text-[18px] leading-[26px] font-medium">Bảng cân đối kế toán</h4>
    <div id="toggle-balance-{{ $uniqueId }}" class="flex rounded-xl border overflow-hidden my-5 w-fit">
        <button id="quarterlyToggle-{{ $uniqueId }}"
            class="toggle-btn px-4 py-2 bg-white text-black font-semibold text-sm border-r-[0.5px] border-gray-300 active first:rounded-l-xl last:rounded-r-xl">
            Quý
        </button>
        <button id="yearlyToggle-{{ $uniqueId }}"
            class="toggle-btn px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-sm border-r-[0.5px] border-gray-300 first:rounded-l-xl last:rounded-r-xl">
            Năm
        </button>
    </div>
    <div>
        <x-bar-chart id="balanceSheetChart-{{ $uniqueId }}" :yearly-labels="$yearlyLabels"
            :yearly-datasets="$balanceYearlyStructuresets" :quarterly-labels="$quarterlyLabels"
            :quarterly-datasets="$balanceQuarterlyStructuresets" :legend="$legend"
            :right-title="$quarterlyLabels[count($quarterlyLabels) - 1] ?? 'Q' . ceil(date('n') / 3) . ' | ' . date('Y')"
            :height="120" />
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Hàm format số
function number_format(number, decimals = 2, dec_point = '.', thousands_sep = ',') {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    const n = !isFinite(+number) ? 0 : +number;
    const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    const sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    const dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
    let s = '';
    const toFixedFix = function(n, prec) {
        const k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

document.addEventListener('DOMContentLoaded', function() {
    const uniqueId = '{{ $uniqueId }}';
    const ctx = document.getElementById(`balanceSheetChart-${uniqueId}`);
    if (!ctx) {
        console.error(`Canvas element with ID "balanceSheetChart-${uniqueId}" not found`);
        return;
    }

    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }

    let chart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($quarterlyLabels),
            datasets: @json($balanceQuarterlyStructuresets)
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Giá trị (tỷ)'
                    },
                    ticks: {
                        callback: function(value) {
                            return number_format(value, 0);
                        }
                    }
                },
                percentage: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    min: 0,
                    title: {
                        display: true,
                        text: 'Tỷ lệ (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    function updateChart(type) {
        const labels = type === 'yearly' ? @json($yearlyLabels) : @json($quarterlyLabels);
        const datasets = type === 'yearly' ? @json($balanceYearlyStructuresets) : @json(
            $balanceQuarterlyStructuresets);
        const rightTitle = labels.length > 0 ? labels[labels.length - 1] : (type === 'yearly' ? @json(
                $currentYear) : 'Q' + Math.ceil((new Date().getMonth() + 1) / 3) + ' | ' + new Date()
            .getFullYear());

        chart.data.labels = labels;
        chart.data.datasets = datasets;

        // Cập nhật legend
        const legendElements = document.querySelectorAll(
            `#legend-item-balanceSheetChart-${uniqueId}-0, #legend-item-balanceSheetChart-${uniqueId}-1, #legend-item-balanceSheetChart-${uniqueId}-2`
        );
        chart.data.datasets.forEach((dataset, index) => {
            if (legendElements[index]) {
                const value = dataset.data.length > 0 ? number_format(dataset.data[dataset.data.length -
                    1], 2) : 'N/A';
                const span = legendElements[index].querySelector('span:last-child');
                if (span) {
                    span.textContent = value;
                } else {
                    console.warn(`Span not found for legend item ${index}`);
                }
            }
        });

        // Cập nhật right-title
        const rightTitleElement = document.getElementById(`right-title-balanceSheetChart-${uniqueId}`);
        if (rightTitleElement) {
            rightTitleElement.textContent = rightTitle;
        } else {
            console.warn(`Right title element not found for ID right-title-balanceSheetChart-${uniqueId}`);
        }

        // Cập nhật trạng thái nút toggle
        document.querySelectorAll(`#toggle-balance-${uniqueId} .toggle-btn`).forEach(btn => {
            btn.classList.remove('bg-white', 'text-black', 'active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        const toggleId = type === 'yearly' ? `yearlyToggle-${uniqueId}` : `quarterlyToggle-${uniqueId}`;
        const toggleButton = document.getElementById(toggleId);
        if (toggleButton) {
            toggleButton.classList.remove('bg-gray-100', 'text-gray-700');
            toggleButton.classList.add('bg-white', 'text-black', 'active');
        }

        chart.update();
    }

    const yearlyToggle = document.getElementById(`yearlyToggle-${uniqueId}`);
    const quarterlyToggle = document.getElementById(`quarterlyToggle-${uniqueId}`);
    if (yearlyToggle) {
        yearlyToggle.addEventListener('click', () => updateChart('yearly'));
    }
    if (quarterlyToggle) {
        quarterlyToggle.addEventListener('click', () => updateChart('quarterly'));
    }

    updateChart('quarterly');
});
</script>

<style>
.balance-sheet- {
        {
        $uniqueId
    }
}

.toggle-btn.active {
    background-color: white;
    color: black;
}

.balance-sheet- {
        {
        $uniqueId
    }
}

.toggle-btn {
    background-color: #f5f5f5;
    color: #4b5563;
}

#balanceSheetChart- {
        {
        $uniqueId
    }
}

    {
    max-width: 100%;
    width: 100% !important;
}
</style>