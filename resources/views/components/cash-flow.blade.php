@php
use Illuminate\Support\Str;

$symbol = $symbol ?? 'ABB';
$currentYear = date('Y');


// ======== LABELS =========
$quarterlyLabels = collect($cashflowQuarterlyStructure)
->map(fn($item) => [
'label' => 'Q' . $item['quarter'] . '/' . $item['year'],
'sort' => $item['year'] * 10 + $item['quarter'],
])
->sortByDesc('sort')->take(4)->sortBy('sort')
->pluck('label')->values()->all();

$yearlyLabels = collect($cashflowYearlyStructure)->pluck('year')->sort()->reverse()->take(4)->sort()->values()->all();

// ======== DYNAMIC DATASETS =========
function buildCashflowDatasets($data, $labels = [], $isQuarterly = true) {
if (empty($data)) return [];

$fields = array_keys($data[0] ?? []);
// Add 'timestamp' to the excluded fields
$exclude = ['year', 'quarter', 'symbol', 'timestamp'];

$validFields = array_filter($fields, fn($field) => !in_array($field, $exclude));
$validFields = array_values($validFields);

$datasets = [];

$specialFields = [
'cff' => ['yAxisID' => 'y', 'type' => 'line'],
];
$percentageFields = array_filter($validFields, fn($field) =>
Str::contains($field, ['ratio', 'rate', 'percent']) || $field === 'debt_to_assets'
);

foreach ($validFields as $index => $field) {
$filtered = collect($data);

// Lọc đúng label nếu là quarterly
if ($isQuarterly && !empty($labels)) {
$filtered = $filtered->filter(function ($item) use ($labels) {
$label = 'Q' . $item['quarter'] . '/' . $item['year'];
return in_array($label, $labels);
})->sortBy(function ($item) {
return $item['year'] * 10 + $item['quarter'];
})->values();
}

if (!$isQuarterly && !empty($labels)) {
$filtered = $filtered->filter(fn($item) => in_array($item['year'], $labels));
}

$dataset = [
'label' => Str::headline(str_replace('_', ' ', $field)),
'data' => $filtered->map(function ($item) use ($field) {
$value = $item[$field] ?? 0;
return Str::contains($field, ['ratio', 'rate', 'percent']) || $field === 'debt_to_assets'
? round((float) $value * 100, 2)
: (float) $value / 1_000_000_000;
})->values()->all(),
'backgroundColor' => !in_array($field, array_keys($specialFields)) && !in_array($field, $percentageFields)
? getColorByIndex($index) : null,
'borderRadius' => 4,
'barPercentage' => 0.3,
];

if (isset($specialFields[$field])) {
$dataset['type'] = $specialFields[$field]['type'];
$dataset['borderColor'] = getColorByIndex($index);
$dataset['fill'] = false;
$dataset['tension'] = 0.1;
$dataset['borderWidth'] = 2;
$dataset['yAxisID'] = $specialFields[$field]['yAxisID'];
}

if (in_array($field, $percentageFields)) {
$dataset['type'] = 'line';
$dataset['borderColor'] = getColorByIndex($index);
$dataset['fill'] = false;
$dataset['tension'] = 0.1;
$dataset['borderWidth'] = 2;
$dataset['yAxisID'] = 'percentage';
}

$datasets[] = $dataset;
}

return $datasets;
}

// ======== COLOR HELPER =========
function getColorByIndex($index) {
$colors = ['#000000', '#0032F0', '#B51001'];
return $colors[$index % count($colors)] ?? '#ccc';
}

// ======== BUILD DATASETS =========
$cashflowQuarterlyStructuresets = buildCashflowDatasets($cashflowQuarterlyStructure, $quarterlyLabels, true);
$cashflowYearlyStructuresets = buildCashflowDatasets($cashflowYearlyStructure, $yearlyLabels, false);

// ======== LEGEND FIXED =========
$lastQuarterLabel = $quarterlyLabels[count($quarterlyLabels) - 1] ?? null;

$legend = collect($cashflowQuarterlyStructuresets)->map(function ($dataset) use ($lastQuarterLabel, $quarterlyLabels) {
$dataIndex = array_search($lastQuarterLabel, $quarterlyLabels);
$lastValue = $dataset['data'][$dataIndex] ?? 0;
$isPercentage = ($dataset['type'] ?? '') === 'line';

return [
'label' => $dataset['label'],
'color' => $dataset['backgroundColor'] ?? $dataset['borderColor'] ?? '#ccc',
'value' => number_format($lastValue, 2) . ($isPercentage ? '%' : ''),
];
})->values()->all();
@endphp

<!-- Biểu đồ Dòng tiền -->
<div class="bg-white rounded-xl border p-6 mt-6">
    <h4 class="text-[18px] leading-[26px] font-medium">Dòng tiền</h4>
    <div class="toggle-cashflow flex rounded-xl border overflow-hidden my-5 w-fit">
        <button id="cashFlowQuarterlyToggle"
            class="px-4 py-2 bg-white text-black font-semibold text-sm border-r-[0.5px] border-gray-300 active first:rounded-l-xl last:rounded-r-xl">Quý</button>
        <button id="cashFlowYearlyToggle"
            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm border-r-[0.5px] border-gray-300 first:rounded-l-xl last:rounded-r-xl">Năm</button>
    </div>
    <div>
        <x-bar-chart id="cashFlowChart" :yearly-labels="$yearlyLabels" :yearly-datasets="$cashflowYearlyStructuresets"
            :quarterly-labels="$quarterlyLabels" :quarterly-datasets="$cashflowQuarterlyStructuresets" :legend="$legend"
            :right-title="$quarterlyLabels[count($quarterlyLabels) - 1] ?? 'Q' . ceil(date('n') / 3) . ' | ' . date('Y')"
            :height="120" />
    </div>
</div>

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

// Khởi tạo Chart.js cho cashFlowChart
const ctxCashFlow = document.getElementById('cashFlowChart')?.getContext('2d');
let cashFlowChart;
if (ctxCashFlow) {
    cashFlowChart = new Chart(ctxCashFlow, {
        type: 'bar',
        data: {
            labels: @json($quarterlyLabels),
            datasets: @json($cashflowQuarterlyStructuresets)
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
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Giá trị (tỷ)'
                    },
                    ticks: {
                        stepSize: 2000
                    }
                },
                percentage: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
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
} else {
    console.error('cashFlowChart canvas not found');
}

// Hàm cập nhật cho cashFlowChart
function updateCashFlowChart(type) {
    if (!cashFlowChart) {
        console.error('cashFlowChart not initialized');
        return;
    }
    const labels = type === 'yearly' ? @json($yearlyLabels) : @json($quarterlyLabels);
    const datasets = type === 'yearly' ? @json($cashflowYearlyStructuresets) : @json($cashflowQuarterlyStructuresets);
    const rightTitle = labels.length > 0 ? labels[labels.length - 1] : (type === 'yearly' ? @json($currentYear) : 'Q' +
        Math.ceil((new Date().getMonth() + 1) / 3) + ' | ' + new Date().getFullYear());

    cashFlowChart.data.labels = labels;
    cashFlowChart.data.datasets = datasets;

    const legendElements = document.querySelectorAll(
        '#legend-item-cashFlowChart-0, #legend-item-cashFlowChart-1, #legend-item-cashFlowChart-2');
    cashFlowChart.data.datasets.forEach((dataset, index) => {
        if (legendElements[index]) {
            const value = dataset.data.length > 0 ? number_format(dataset.data[dataset.data.length - 1], 2) :
                'N/A';
            legendElements[index].querySelector('span:last-child').textContent = value;
        }
    });

    const rightTitleElement = document.getElementById('right-title-cashFlowChart');
    if (rightTitleElement) {
        rightTitleElement.textContent = rightTitle;
    }

    document.querySelectorAll('.toggle-cashflow button').forEach(btn => {
        btn.classList.remove('bg-white', 'text-black', 'active');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    const toggleId = `cashFlow${type === 'yearly' ? 'Yearly' : 'Quarterly'}Toggle`;
    const toggleButton = document.getElementById(toggleId);
    if (toggleButton) {
        toggleButton.classList.remove('bg-gray-100', 'text-gray-700');
        toggleButton.classList.add('bg-white', 'text-black', 'active');
    } else {}

    cashFlowChart.update();
}

// Lắng nghe sự kiện
document.addEventListener('DOMContentLoaded', () => {
    const cashFlowQuarterlyToggle = document.getElementById('cashFlowQuarterlyToggle');
    const cashFlowYearlyToggle = document.getElementById('cashFlowYearlyToggle');

    if (cashFlowQuarterlyToggle) {
        cashFlowQuarterlyToggle.addEventListener('click', () => updateCashFlowChart('quarterly'));
    } else {
        console.error('cashFlowQuarterlyToggle not found');
    }
    if (cashFlowYearlyToggle) {
        cashFlowYearlyToggle.addEventListener('click', () => updateCashFlowChart('yearly'));
    } else {
        console.error('cashFlowYearlyToggle not found');
    }

    // Khởi tạo ban đầu
    if (cashFlowChart) updateCashFlowChart('quarterly');
});
</script>