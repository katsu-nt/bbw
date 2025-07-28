@php
use Illuminate\Support\Str;

$uniqueId = Str::random(8); // Tạo ID duy nhất cho component
$symbol = $symbol ?? 'ABB';
$currentYear = date('Y'); // 2025

// Dữ liệu hàng quý
// Sắp xếp và lấy 4 quý mới nhất
$incomeStatementQuarterlyStructure = collect($incomeStatementQuarterlyStructure)
->map(fn($item) => array_merge($item, ['sort' => $item['year'] * 10 + $item['quarter']]))
->sortByDesc('sort')
->take(4)
->sortBy('sort')
->values()
->all();

$quarterlyLabels = collect($incomeStatementQuarterlyStructure)
->map(fn($item) => [
'label' => 'Q' . $item['quarter'] . '/' . $item['year'],
'sort' => $item['year'] * 10 + $item['quarter'],
'year' => $item['year'],
'quarter' => $item['quarter']
])
->pluck('label')
->values()
->all();

$firstQuarter = $incomeStatementQuarterlyStructure[0] ?? [];

$incomeStatementQuarterlyStructuresets = !empty($incomeStatementQuarterlyStructure) ? [
[
'label' => $firstQuarter['revenue_label'] ?? 'Revenue',
'data' => collect($incomeStatementQuarterlyStructure)
->map(fn($item) => ($item['revenue'] ?? 0) / 1_000_000_000)
->all(),
'backgroundColor' => '#000000',
'borderRadius' => 4,
'barPercentage' => 0.3,
],
[
'label' => $firstQuarter['net_income_label'] ?? 'Net income',
'data' => collect($incomeStatementQuarterlyStructure)
->map(fn($item) => ($item['net_income'] ?? 0) / 1_000_000_000)
->all(),
'backgroundColor' => '#0032F0',
'borderRadius' => 4,
'barPercentage' => 0.3,
],
[
'label' => $firstQuarter['profit_margins_label'] ?? 'Profit margin',
'data' => collect($incomeStatementQuarterlyStructure)
->map(fn($item) => round(($item['profit_margins'] ?? 0) * 100, 2))
->all(),
'type' => 'line',
'borderColor' => '#B51001',
'fill' => false,
'tension' => 0.1,
'borderWidth' => 2,
'yAxisID' => 'percentage',
],
] : [];


// Dữ liệu hàng năm
$yearlyLabels = !empty($incomeStatementYearlyStructure)
? collect($incomeStatementYearlyStructure)->pluck('year')->sort()->reverse()->take(4)->sort()->values()->all()
: [];

$firstYear = $incomeStatementYearlyStructure[0] ?? [];

$incomeStatementYearlyStructuresets = !empty($incomeStatementYearlyStructure) ? [
[
'label' => $firstYear['revenue_label'] ?? 'Revenue',
'data' => collect($incomeStatementYearlyStructure)
->filter(fn($item) => in_array($item['year'], $yearlyLabels))
->sortBy('year')
->pluck('revenue')
->map(fn($v) => ($v ?? 0) / 1_000_000_000)
->all(),
'backgroundColor' => '#000000',
'borderRadius' => 4,
'barPercentage' => 0.3,
],
[
'label' => $firstYear['net_income_label'] ?? 'Net income',
'data' => collect($incomeStatementYearlyStructure)
->filter(fn($item) => in_array($item['year'], $yearlyLabels))
->sortBy('year')
->pluck('net_income')
->map(fn($v) => ($v ?? 0) / 1_000_000_000)
->all(),
'backgroundColor' => '#0032F0',
'borderRadius' => 4,
'barPercentage' => 0.3,
],
[
'label' => $firstYear['profit_margins_label'] ?? 'Profit margin',
'data' => collect($incomeStatementYearlyStructure)
->filter(fn($item) => in_array($item['year'], $yearlyLabels))
->sortBy('year')
->pluck('profit_margins')
->map(fn($v) => round(($v ?? 0) * 100, 2))
->all(),
'type' => 'line',
'borderColor' => '#B51001',
'fill' => false,
'tension' => 0.1,
'borderWidth' => 2,
'yAxisID' => 'percentage',
],
] : [];

// Tạo legend, bỏ đơn vị "tỷ" và "%"
$legend = [];
foreach ($incomeStatementQuarterlyStructuresets as $index => $dataset) {
$lastValue = end($dataset['data']) ?? 0;
$legend[] = [
'label' => $dataset['label'],
'color' => $dataset['borderColor'] ?? $dataset['backgroundColor'],
'value' => !empty($dataset['data']) ? number_format($lastValue, 2) : 'N/A',
];
}
@endphp

<div class="bg-white rounded-xl border p-6 mt-6 balance-sheet-{{ $uniqueId }}">
    <h4 class="text-[18px] leading-[26px] font-medium">Tài sản</h4>
    <!-- Container Biểu Đồ -->
    <div id="toggle-income-{{ $uniqueId }}" class="flex rounded-xl border overflow-hidden my-5 w-fit">
        <button id="quarterlyToggle-{{ $uniqueId }}"
            class="toggle-btn px-4 py-2 bg-white text-black font-semibold text-sm border-r-[0.5px] border-gray-300 active first:rounded-l-xl last:rounded-r-xl">
            Quý
        </button>
        <button id="yearlyToggle-{{ $uniqueId }}"
            class="toggle-btn px-4 py-2 bg-gray-100 text-gray-700 text-sm border-r-[0.5px] border-gray-300 first:rounded-l-xl last:rounded-r-xl">
            Năm
        </button>
    </div>
    <div>
        <x-bar-chart id="incomeStatementChart-{{ $uniqueId }}" :yearly-labels="$yearlyLabels"
            :yearly-datasets="$incomeStatementYearlyStructuresets" :quarterly-labels="$quarterlyLabels"
            :quarterly-datasets="$incomeStatementQuarterlyStructuresets" :legend="$legend"
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

// Khởi tạo Chart.js
document.addEventListener('DOMContentLoaded', function() {
    const uniqueId = '{{ $uniqueId }}';
    const ctx = document.getElementById(`incomeStatementChart-${uniqueId}`).getContext('2d');
    let chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($quarterlyLabels),
            datasets: @json($incomeStatementQuarterlyStructuresets)
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
                        /* stepSize: 2000 */
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
                        callback: value => value + '%'
                    }
                }
            }
        }
    });

    // Hàm cập nhật dữ liệu biểu đồ và chú thích
    function updateChart(type) {
        const labels = type === 'yearly' ? @json($yearlyLabels) : @json($quarterlyLabels);
        const datasets = type === 'yearly' ? @json($incomeStatementYearlyStructuresets) : @json(
            $incomeStatementQuarterlyStructuresets);
        const rightTitle = labels.length > 0 ? labels[labels.length - 1] : (type === 'yearly' ? @json(
                $currentYear) : 'Q' + Math.ceil((new Date().getMonth() + 1) / 3) + ' | ' + new Date()
            .getFullYear());

        chart.data.labels = labels;
        chart.data.datasets = datasets;

        // Cập nhật giá trị trong legend, bỏ đơn vị "tỷ" và "%"
        const legendElements = document.querySelectorAll(
            `#legend-item-incomeStatementChart-${uniqueId}-0, #legend-item-incomeStatementChart-${uniqueId}-1, #legend-item-incomeStatementChart-${uniqueId}-2`
        );
        chart.data.datasets.forEach((dataset, index) => {
            if (legendElements[index]) {
                const value = dataset.data.length > 0 ?
                    number_format(dataset.data[dataset.data.length - 1], 2) :
                    'N/A';
                legendElements[index].querySelector('span:last-child').textContent = value;
            }
        });


        // Cập nhật right-title
        const rightTitleElement = document.getElementById(`right-title-incomeStatementChart-${uniqueId}`);
        if (rightTitleElement) {
            rightTitleElement.textContent = rightTitle;
        }

        // Cập nhật trạng thái nút toggle
        document.querySelectorAll(`#toggle-income-${uniqueId} .toggle-btn`).forEach(btn => {
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

    // Lắng nghe sự kiện từ nút toggle
    document.getElementById(`yearlyToggle-${uniqueId}`).addEventListener('click', () => updateChart('yearly'));
    document.getElementById(`quarterlyToggle-${uniqueId}`).addEventListener('click', () => updateChart(
        'quarterly'));

    // Khởi tạo với chế độ Quý
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
</style>