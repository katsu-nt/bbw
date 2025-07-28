<?php
// Khởi tạo $labels và $values an toàn
$labels = isset($normalizedPerformance) && is_array($normalizedPerformance) ? array_keys($normalizedPerformance) : [];
$values = isset($normalizedPerformance) && is_array($normalizedPerformance) ? array_values($normalizedPerformance) : [];

// Khởi tạo $latestFinancialMetric an toàn
$latestFinancialMetric = isset($latestFinancialMetric) && is_array($latestFinancialMetric) && count($latestFinancialMetric) >= 2 ? $latestFinancialMetric : [
    ['timestamp' => 'N/A', 'Vốn hoá' => 0, 'Tổng doanh thu' => 0, 'symbol' => $symbol ?? 'ABB'],
    ['timestamp' => 'N/A', 'Vốn hoá' => 0, 'Tổng doanh thu' => 0, 'symbol' => $symbol ?? 'ABB']
];

// Khởi tạo $latestOhlcv an toàn
$latestOhlcv = isset($latestOhlcv) && is_array($latestOhlcv) ? $latestOhlcv : [
    'Giá mở cửa' => 0,
    'Giá cao nhất' => 0,
    'Giá thấp nhất' => 0,
    'Giá đóng cửa' => 0,
    'Khối lượng giao dịch' => 0,
    'Phần trăm thay đổi' => 0
];

// Lấy các trường từ $latestOhlcv, bỏ timestamp và symbol
$ohlcvFields = array_diff_key($latestOhlcv, ['timestamp' => '', 'symbol' => '']);
$ohlcvKeys = array_keys($ohlcvFields);

// Extract timestamps and sort by year
$timestamps = array_column($latestFinancialMetric, 'timestamp');
$dates = array_map(function($ts) {
    return is_string($ts) && strlen($ts) >= 10 ? substr($ts, 0, 10) : 'N/A';
}, $timestamps);

// Sort data so earlier year comes first
usort($latestFinancialMetric, function($a, $b) {
    return strcmp($a['timestamp'] ?? 'N/A', $b['timestamp'] ?? 'N/A');
});

// Get sorted dates
$dates = array_map(function($ts) {
    return is_string($ts) && strlen($ts) >= 10 ? substr($ts, 0, 10) : 'N/A';
}, array_column($latestFinancialMetric, 'timestamp'));

// Get metrics (exclude symbol and timestamp)
$metrics = !empty($latestFinancialMetric[0]) ? array_keys(array_diff_key($latestFinancialMetric[0], ['symbol' => '', 'timestamp' => ''])) : ['Vốn hoá', 'Tổng doanh thu', 'Lợi nhuận sau thuế'];
?>
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-7">
        <div class="border rounded-xl p-4">
            <div class="mb-4 flex flex-col gap-2 mb-20">
                <div class="flex justify-between items-center gap-2 mb-4">
                    <div id="time-range-group" class="flex items-center space-x-2 bg-gray-100 rounded-[8px]">
                        <p class="time-btn px-3 py-2 text-sm cursor-pointer" data-range="1m">1 Tháng</p>
                        <p class="time-btn px-3 py-2 text-sm cursor-pointer" data-range="6m">6 Tháng</p>
                        <p class="time-btn px-3 py-2 text-sm cursor-pointer" data-range="1y">1 Năm</p>
                        <p class="time-btn px-3 py-2 text-sm cursor-pointer" data-range="ytd">Year to Date</p>
                        <p class="time-btn px-3 py-2 text-sm cursor-pointer" data-range="3y">3 Năm</p>
                    </div>
                    <div class="relative w-60">
                        <div class="flex items-center border rounded-[8px] px-3 py-2 bg-white">
                            <svg width="18" height="18" fill="none" class="mr-2 text-gray-400" viewBox="0 0 17 17">
                                <path
                                    d="M16 15.5L12.375 11.875M14.3333 7.16667C14.3333 10.8486 11.3486 13.8333 7.66667 13.8333C3.98477 13.8333 1 10.8486 1 7.16667C1 3.48477 3.98477 0.5 7.66667 0.5C11.3486 0.5 14.3333 3.48477 14.3333 7.16667Z"
                                    stroke="#888" stroke-linecap="square" stroke-linejoin="round" />
                            </svg>
                            <input type="text" class="company-input bg-transparent outline-none text-gray-700 w-full"
                                placeholder="Thêm so sánh" id="searchSymbolToCompare">
                            <span class="tooltip">Chỉ so sánh tối đa 4 công ty</span>
                        </div>
                        <div id="dropdown"
                            class="absolute z-10 w-full bg-white border rounded mt-1 max-h-40 overflow-y-auto hidden">
                            @if (!empty($allSymbols))
                            @foreach (array_slice($allSymbols, 0, 8) as $sym)
                            <div class="px-2 py-1 hover:bg-gray-100 cursor-pointer symbol-option"
                                data-code="{{ $sym }}">{{ $sym }}
                            </div>
                            @endforeach
                            @else
                            <div class="px-2 py-1 text-gray-500">Không có dữ liệu</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 mt-2 text-sm" id="symbol-performance">
                    <span class="flex items-center gap-1 font-semibold text-black bg-[#f4f4f4] p-2.5 rounded-lg">
                        <span class="legend-dot"></span> <!-- Hình tròn cho dataset 1 -->
                        {{ $symbol ?? 'ABB' }}:
                        <span id="change-percent" class="font-normal">
                            {{ !empty($values) && is_array($values) ? (end($values) >= 0 ? '+' : '') . number_format(end($values), 2) . '%' : 'N/A' }}
                        </span>
                    </span>
                    <span id="compare-symbol-performance"
                        class="flex items-center gap-x-5 font-semibold text-black hidden">
                        <span class="legend-dot"></span>
                        <span id="compare-symbol" class="remove-symbol"></span>:
                        <span id="compare-change-percent" class="font-normal cursor-pointer"></span>
                    </span>
                </div>
            </div>
            <p class="text-[12px] leading-[18px] font-normal text-[#595959] mb-5">
                Đơn vị: Nghìn đồng
            </p>
            <canvas id="summary-chart" class="w-full h-64"></canvas>
        </div>
        <div class="bg-white rounded-xl border mt-8">
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-4 p-6 border-b pb-4">Tổng quan</h3>
                <div class="grid grid-cols-3 gap-4 border-b px-6 pb-4">
                    <?php
                $firstThree = array_slice($ohlcvKeys, 0, 3);
                $firstThree = array_reverse($firstThree);
                foreach ($firstThree as $key):
                    $value = $latestOhlcv[$key];
                    $formattedValue = is_numeric($value) ? number_format($value, 2, '.', ',') : $value;
                ?>
                    <div>
                        <div class="text-black text-[14px] leading-[20px] font-medium">
                            <?php echo htmlspecialchars($key); ?></div>
                        <div class="font-normal text-base text-[18px] leading-[26px]">
                            <?php echo htmlspecialchars($formattedValue); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4 border-b px-6 pb-4">
                    <?php
                $lastThree = array_slice($ohlcvKeys, 3, 3);
                $lastThree = array_reverse($lastThree);
                foreach ($lastThree as $key):
                    $value = $latestOhlcv[$key];
                    $formattedValue = is_numeric($value) ? number_format($value, 2, '.', ',') : $value;
                ?>
                    <div>
                        <div class="text-black text-[14px] leading-[20px] font-medium">
                            <?php echo htmlspecialchars($key); ?></div>
                        <div class="font-normal text-base text-[18px] leading-[26px]">
                            <?php echo htmlspecialchars($formattedValue); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-span-5">
        <div class="bg-white rounded-xl border w-full">
            <div class="flex items-center justify-between mb-4 px-4 pt-6">
                <span class="font-semibold text-base">Báo cáo tài chính</span>
                <div class="flex rounded-xl border overflow-hidden" data-toggle-buttons>
                    <button type="button" data-type="quarterly"
                        class="px-4 py-2 bg-gray-100 text-gray-500 text-sm border-[0.5px]">
                        Quý
                    </button>
                    <button type="button" data-type="yearly"
                        class="px-4 py-2 bg-white text-black font-semibold text-sm border-[0.5px]">
                        Năm
                    </button>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-400 mb-2 bg-gray-100 p-4">
                <span class="flex-1">Chỉ tiêu tài chính</span>
                <span class="w-28 text-center"
                    id="date-0"><?php echo isset($dates[0]) ? htmlspecialchars(date('d/m/Y', strtotime($dates[0]))) : 'N/A'; ?></span>
                <span class="w-28 text-center"
                    id="date-1"><?php echo isset($dates[1]) ? htmlspecialchars(date('d/m/Y', strtotime($dates[1]))) : 'N/A'; ?></span>
            </div>
            <div class="divide-y" id="financial-metrics">
                <?php
            $counter = 0;
            foreach ($metrics as $metric):
                if ($counter === 0): ?>
                <h5 class="font-semibold p-4">Tình hình kinh doanh</h5>
                <?php endif; ?>
                <div class="flex items-center p-4">
                    <span class="flex-1 text-sm"><?php echo htmlspecialchars($metric); ?></span>
                    <?php
                    $value1 = isset($latestFinancialMetric[0][$metric]) ? $latestFinancialMetric[0][$metric] : 0;
                    $value2 = isset($latestFinancialMetric[1][$metric]) ? $latestFinancialMetric[1][$metric] : 0;
                    if (!function_exists('formatLargeNumber')) {
                        function formatLargeNumber($value, $metric) {
                            if (!is_numeric($value)) return $value;
                            $absValue = abs($value);
                            if ($absValue >= 1000000000) {
                                return number_format($value / 1000000000, 2, ',', '.') . ' tỷ';
                            } elseif ($absValue >= 1000000) {
                                return number_format($value / 1000000, 2, ',', '.') . ' triệu';
                            } else {
                                return number_format($value, 2, ',', '.');
                            }
                        }
                    }
                    $class1 = $value1 < 0 ? 'text-red-500' : '';
                    $class2 = $value2 < 0 ? 'text-red-500' : '';
                    ?>
                    <span class="w-28 text-center text-[14px] <?php echo $class1; ?>"
                        data-metric="<?php echo htmlspecialchars($metric); ?>-0">
                        <?php echo formatLargeNumber($value1, $metric); ?>
                    </span>
                    <span class="w-28 text-center text-[14px] <?php echo $class2; ?>"
                        data-metric="<?php echo htmlspecialchars($metric); ?>-1">
                        <?php echo formatLargeNumber($value2, $metric); ?>
                    </span>
                </div>
                <?php
                $counter++;
                if ($counter === 5): ?>
                <h5 class="font-semibold p-4">Hiệu quả hoạt động</h5>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<style>
#searchSymbolToCompare {
    position: relative;
}

.tooltip {
    display: none;
    position: absolute;
    top: -43px;
    left: 51%;
    transform: translateX(-50%);
    background-color: #fff;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    color: black;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    border: 1px solid #c8c8c8;
    z-index: 1000;
    pointer-events: none;
}

.tooltip::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 20px;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-width: 7px;
    border-style: solid;
    border-color: #c4c4c4 transparent transparent transparent;
}

#searchSymbolToCompare:disabled:hover+.tooltip {
    display: block;
}

#dropdown {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: none;
    z-index: 2000;
}

#dropdown.show {
    display: block;
}

.symbol-option {
    font-size: 14px;
}

/* Định dạng cho chú thích với hình tròn và màu khớp với biểu đồ */
#symbol-performance .legend-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #000000;
}

#compare-symbol-performance .legend-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #0032F0;
}

/* Thêm class tùy chỉnh cho màu #0032F0 */
.bg-custom-blue {
    background-color: #0032F0;
}

.dash-style {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #f4f4f4;
    border: 1px solid #000000;
    border-radius: 50%;
    padding: 5px;
    margin-left: 0.25rem;
    cursor: pointer;
    width: 20px;
    height: 20px;
    font-weight: bold;
    line-height: 1;
    transition: 0.5s;
}

.dash-style:hover {
    background-color: #000000;
    color: #ffffff;
}

div.flex.items-center.border.rounded-\[8px\]:has(input#searchSymbolToCompare:disabled) {
    background-color: #f4f4f4;
    opacity: 0.7;
}

#searchSymbolToCompare:disabled {
    background-color: transparent;
    cursor: default;
}
</style>
<script>
let chartInstance;
let initialFinancialData = <?php echo json_encode($latestFinancialMetric); ?>;
let initialMetrics = <?php echo json_encode($metrics); ?>;
let currentRange = '1m';
let compareSymbols = []; // Mảng để lưu các symbol so sánh
const defaultSymbol = '<?php echo $symbol ?? 'ABB'; ?>'; // Lấy symbol mặc định từ PHP

// Định nghĩa mảng màu cố định
const customColors = ['#000000', '#0032F0', '#B51001', '#38D430'];

function setActiveFinancialButton(clickedBtn, buttonGroup) {
    buttonGroup.forEach(btn => {
        btn.classList.remove('bg-white', 'text-black', 'font-semibold');
        btn.classList.add('bg-gray-100', 'text-gray-500');
    });
    clickedBtn.classList.remove('bg-gray-100', 'text-gray-500');
    clickedBtn.classList.add('bg-white', 'text-black', 'font-semibold');
}

function setActiveTimeButton(clickedBtn, buttonGroup) {
    buttonGroup.forEach(btn => {
        btn.classList.remove('bg-white', 'text-black', 'font-semibold', 'border', 'rounded-xl',
            'border-gray-300');
        btn.classList.add('bg-gray-100', 'text-gray-500');
    });
    clickedBtn.classList.remove('bg-gray-100', 'text-gray-500');
    clickedBtn.classList.add('bg-white', 'text-black', 'font-semibold', 'border', 'rounded-xl', 'border-gray-300');
}

function renderChart(labels, initialValues, range = '1m') {
    const ctx = document.getElementById('summary-chart').getContext('2d');
    if (!chartInstance) {
        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: defaultSymbol,
                    data: initialValues.length ? initialValues : [0],
                    borderColor: customColors[0],
                    backgroundColor: customColors[0].replace(')', ', 0.15)').replace('rgb', 'rgba'),
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'category',
                        title: {
                            display: true,
                            text: 'Thời gian' // Nhãn cho trục X
                        },
                        ticks: {
                            maxTicksLimit: 6,
                            autoSkip: true
                        },
                        grid: {
                            display: false // Ẩn cột dọc trên trục x
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Đơn vị: Nghìn đồng' // Nhãn cho trục Y
                        },
                        ticks: {}
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    } else {
        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = initialValues.length ? initialValues : [0];
        chartInstance.data.datasets[0].borderColor = customColors[0];
        chartInstance.data.datasets[0].backgroundColor = customColors[0].replace(')', ', 0.15)').replace('rgb', 'rgba');
    }

    const existingSymbols = [defaultSymbol, ...compareSymbols];
    chartInstance.data.datasets = chartInstance.data.datasets.filter(ds => existingSymbols.includes(ds.label));

    compareSymbols.forEach((symbol, index) => {
        const existingDataset = chartInstance.data.datasets.find(ds => ds.label === symbol);
        if (!existingDataset) {
            const {
                from_date,
                to_date
            } = getDateRange(range);
            fetchNormalizedPerformance(symbol, from_date, to_date).then(data => {
                const newLabels = Object.keys(data || {});
                const newValues = Object.values(data || {}).map(val => val || 0);
                let finalValues = newValues;
                if (newLabels.length && labels.length && newLabels.join() !== labels.join()) {
                    finalValues = labels.map(label => {
                        const index = newLabels.indexOf(label);
                        return index !== -1 ? newValues[index] : 0;
                    });
                }
                const colorIndex = index + 1; // Bắt đầu từ màu thứ 2 cho các dataset so sánh
                chartInstance.data.datasets.push({
                    label: symbol,
                    data: finalValues,
                    borderColor: customColors[colorIndex % customColors.length],
                    backgroundColor: customColors[colorIndex % customColors.length].replace(')',
                        ', 0.15)').replace('rgb', 'rgba'),
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                });
                chartInstance.update();
                updateGrowthText(chartInstance.data.datasets[0].data, chartInstance.data.datasets.slice(
                    1).map(ds => ds.data));
            }).catch(err => console.error('Lỗi khi fetch dữ liệu so sánh:', err));
        }
    });

    chartInstance.update();
}

function updateGrowthText(initialValues, compareValues = []) {
    const latestChange = (initialValues && initialValues.length) ? (initialValues[initialValues.length - 1] || 0) : 0;
    const growthText = initialValues && initialValues.length ? (latestChange >= 0 ? '+' : '') + latestChange.toFixed(
        2) + '%' : 'N/A';
    const percentSpan = document.getElementById('change-percent');
    if (percentSpan) {
        percentSpan.textContent = growthText;
        percentSpan.className = 'font-normal';
    }

    const compareContainer = document.getElementById('compare-symbol-performance');
    compareContainer.innerHTML = '';
    compareSymbols.forEach((symbol, index) => {
        if (compareValues[index] && compareValues[index].length) {
            const compareChange = compareValues[index][compareValues[index].length - 1] || 0;
            const compareGrowthText = (compareChange >= 0 ? '+' : '') + compareChange.toFixed(2) + '%';
            const span = document.createElement('span');
            span.className =
                'flex items-center gap-1 bg-[#f4f4f4] p-2.5 rounded-lg font-semibold text-black remove-item';
            const symbolDisplay = symbol + ' :';
            const dashElement = `<span class="dash-style">-</span>`;
            const colorIndex = index + 1; // Bắt đầu từ màu thứ 2 cho các dataset so sánh
            span.innerHTML = `
                <span class="legend-dot" style="background-color: ${customColors[colorIndex % customColors.length]}"></span>
                <span class="remove-symbol" data-symbol="${symbol}">${symbolDisplay}</span>
                <span class="font-normal">${compareGrowthText}${dashElement}</span>
            `;
            compareContainer.appendChild(span);
        }
    });
    if (compareSymbols.length > 0) {
        compareContainer.classList.remove('hidden');
    } else {
        compareContainer.classList.add('hidden');
    }
}

function getDateRange(range) {
    const now = new Date();
    let from = new Date(now);
    switch (range) {
        case '1m':
            from.setMonth(now.getMonth() - 1);
            break;
        case '6m':
            from.setMonth(now.getMonth() - 6);
            break;
        case '1y':
            from.setFullYear(now.getFullYear() - 1);
            break;
        case 'ytd':
            from.setMonth(0); // Bắt đầu từ đầu năm (1/1)
            from.setDate(1); // Đặt ngày về 1
            break;
        case '3y':
            from.setFullYear(now.getFullYear() - 3);
            break;
    }
    const pad = n => n.toString().padStart(2, '0');
    return {
        from_date: `${from.getFullYear()}-${pad(from.getMonth() + 1)}-${pad(from.getDate())}`,
        to_date: `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
    };
}

function formatLargeNumber(value, metric) {
    if (!isNumeric(value)) return value;
    const absValue = Math.abs(value);
    const formatter = new Intl.NumberFormat('vi-VN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    if (absValue >= 1000000000) {
        return formatter.format(value / 1000000000) + ' tỷ';
    } else if (absValue >= 1000000) {
        return formatter.format(value / 1000000) + ' triệu';
    } else {
        return formatter.format(value);
    }
}

function isNumeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

function formatDateVN(dateString) {
    if (!dateString || isNaN(Date.parse(dateString))) return 'N/A';
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function updateFinancialMetrics(data, metrics) {
    const financialMetricsContainer = document.getElementById('financial-metrics');
    financialMetricsContainer.innerHTML = '';

    if (!Array.isArray(data) || data.length < 2) {
        financialMetricsContainer.innerHTML = '<p class="p-4 text-red-500">Dữ liệu không hợp lệ hoặc không đủ</p>';
        return;
    }

    data.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
    document.getElementById('date-0').textContent = formatDateVN(data[0]?.timestamp) || 'N/A';
    document.getElementById('date-1').textContent = formatDateVN(data[1]?.timestamp) || 'N/A';

    let counter = 0;
    metrics.forEach(metric => {
        if (counter === 0) {
            financialMetricsContainer.innerHTML += '<h5 class="font-semibold p-4">Tình hình kinh doanh</h5>';
        }
        if (counter === 5) {
            financialMetricsContainer.innerHTML += '<h5 class="font-semibold p-4">Hiệu quả hoạt động</h5>';
        }

        const value1 = data[0]?. [metric] || 0;
        const value2 = data[1]?. [metric] || 0;
        const class1 = value1 < 0 ? 'text-red-500' : '';
        const class2 = value2 < 0 ? 'text-red-500' : '';

        financialMetricsContainer.innerHTML += `
            <div class="flex items-center p-4">
                <span class="flex-1 text-sm">${metric}</span>
                <span class="w-28 text-center text-[14px] ${class1}" data-metric="${metric}-0">${formatLargeNumber(value1, metric)}</span>
                <span class="w-28 text-center text-[14px] ${class2}" data-metric="${metric}-1">${formatLargeNumber(value2, metric)}</span>
            </div>
        `;
        counter++;
    });
}

async function fetchFinancialMetrics(symbol, reportType) {
    try {
        const response = await fetch(
            `http://103.97.127.42:8001/overview/latest-financial-metrics/${symbol}?report_type=${reportType}`
        );
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Lỗi khi fetch dữ liệu tài chính:', error.message);
        return null;
    }
}

async function fetchNormalizedPerformance(symbol, fromDate, toDate) {
    try {
        const response = await fetch(
            `http://103.97.127.42:8001/overview/normalized-performance?symbol=${symbol}&from_date=${fromDate}&to_date=${toDate}`
        );
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Lỗi khi fetch normalized performance:', error.message);
        return {};
    }
}

async function fetchAndRender(range = '1m') {
    const {
        from_date,
        to_date
    } = getDateRange(range);
    const symbol = new URLSearchParams(window.location.search).get('symbol') || 'ABB';

    try {
        const initialResponse = await fetch(
            `/api/normalized-performance?symbol=${symbol}&from_date=${from_date}&to_date=${to_date}`
        );
        if (!initialResponse.ok) {
            throw new Error(`HTTP error! Status: ${initialResponse.status}`);
        }
        const initialData = await initialResponse.json();
        const initialLabels = Object.keys(initialData || {});
        const initialValues = Object.values(initialData || {}).map(val => val || 0);

        const compareDataPromises = compareSymbols.map(sym =>
            fetchNormalizedPerformance(sym, from_date, to_date).then(data => ({
                symbol: sym,
                labels: Object.keys(data || {}),
                values: Object.values(data || {}).map(val => val || 0)
            }))
        );
        const compareData = await Promise.all(compareDataPromises);

        renderChart(initialLabels.length ? initialLabels : [''], initialValues.length ? initialValues : [0], range);

        compareData.forEach(({
            symbol,
            labels,
            values
        }) => {
            let finalValues = values;
            if (labels.length && initialLabels.length && labels.join() !== initialLabels.join()) {
                finalValues = initialLabels.map(label => {
                    const index = labels.indexOf(label);
                    return index !== -1 ? values[index] : 0;
                });
            }
            const dataset = chartInstance.data.datasets.find(ds => ds.label === symbol);
            const colorIndex = [defaultSymbol, ...compareSymbols].indexOf(symbol);
            if (dataset) {
                dataset.data = finalValues;
                dataset.borderColor = customColors[colorIndex % customColors.length];
                dataset.backgroundColor = customColors[colorIndex % customColors.length].replace(')',
                    ', 0.15)').replace('rgb', 'rgba');
            } else {
                chartInstance.data.datasets.push({
                    label: symbol,
                    data: finalValues,
                    borderColor: customColors[colorIndex % customColors.length],
                    backgroundColor: customColors[colorIndex % customColors.length].replace(')',
                        ', 0.15)').replace('rgb', 'rgba'),
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                });
            }
        });

        chartInstance.update();
        updateGrowthText(chartInstance.data.datasets[0].data, chartInstance.data.datasets.slice(1).map(ds => ds
            .data));
    } catch (err) {
        console.error('Lỗi khi fetch data:', err.message);
        alert('Lỗi khi tải dữ liệu. Vui lòng thử lại.');
        renderChart([''], [0], range);
        updateGrowthText([0]);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const defaultTimeBtn = document.querySelector('.time-btn[data-range="1m"]');
    setActiveTimeButton(defaultTimeBtn, document.querySelectorAll('.time-btn'));

    const labels = <?php echo json_encode($labels); ?>;
    const values = <?php echo json_encode($values); ?>;
    const allSymbols = <?php echo json_encode($allSymbols ?? ['AAA', 'STB', 'ABB']); ?>;

    renderChart(labels.length ? labels : [''], values.length ? values : [0], '1m');
    updateGrowthText(values.length ? values : [0]);

    updateFinancialMetrics(initialFinancialData, initialMetrics);

    document.querySelectorAll('.time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentRange = this.dataset.range;
            setActiveTimeButton(this, document.querySelectorAll('.time-btn'));
            fetchAndRender(currentRange);
        });
    });

    document.querySelectorAll('[data-type]').forEach(btn => {
        btn.addEventListener('click', async function() {
            setActiveFinancialButton(this, document.querySelectorAll('[data-type]'));
            const data = await fetchFinancialMetrics(new URLSearchParams(window.location
                .search).get('symbol') || 'ABB', this.dataset.type);
            if (data) {
                updateFinancialMetrics(data, initialMetrics);
            } else {
                console.warn('Không có dữ liệu tài chính, sử dụng dữ liệu mặc định');
                updateFinancialMetrics(initialFinancialData, initialMetrics);
            }
        });
    });

    const input = document.querySelector('#searchSymbolToCompare');
    const dropdown = document.querySelector('#dropdown');

    if (!input || !dropdown) {
        console.error('Input or dropdown element not found');
        return;
    }

    input.addEventListener('focus', () => {
        if (compareSymbols.length >= 3) {
            alert('Chỉ so sánh tối đa 4 công ty');
            return;
        }
        updateDropdown(allSymbols.slice(0, 8));
        dropdown.classList.add('show');
        dropdown.classList.remove('hidden');
    });

    input.addEventListener('input', () => {
        if (compareSymbols.length >= 3) {
            alert('Chỉ so sánh tối đa 4 công ty');
            input.value = '';
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');
            return;
        }
        const searchTerm = input.value.trim().toUpperCase();
        if (searchTerm) {
            const filteredSymbols = allSymbols.filter(sym => sym.toUpperCase().includes(searchTerm))
                .slice(0, 8);
            updateDropdown(filteredSymbols);
            dropdown.classList.add('show');
            dropdown.classList.remove('hidden');
        } else {
            updateDropdown(allSymbols.slice(0, 8));
            dropdown.classList.add('show');
            dropdown.classList.remove('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');
        }
    });

    dropdown.addEventListener('click', async (e) => {
        if (e.target.classList.contains('symbol-option')) {
            const code = e.target.getAttribute('data-code');
            input.value = code;
            if (compareSymbols.length >= 3) {
                alert('Chỉ so sánh tối đa 4 công ty');
                input.value = '';
                dropdown.classList.remove('show');
                dropdown.classList.add('hidden');
                return;
            }
            if (!compareSymbols.includes(code) && code !== defaultSymbol) {
                compareSymbols.push(code);
                const {
                    from_date,
                    to_date
                } = getDateRange(currentRange);
                const newData = await fetchNormalizedPerformance(code, from_date, to_date);
                const newLabels = Object.keys(newData || {});
                const newValues = Object.values(newData || {}).map(val => val || 0);
                const initialLabels = chartInstance ? chartInstance.data.labels :
                    <?php echo json_encode($labels); ?>;
                let finalValues = newValues;
                if (newLabels.length && initialLabels.length && newLabels.join() !== initialLabels
                    .join()) {
                    finalValues = initialLabels.map(label => {
                        const index = newLabels.indexOf(label);
                        return index !== -1 ? newValues[index] : 0;
                    });
                }
                const colorIndex = compareSymbols.length; // Sử dụng chỉ số của dataset so sánh
                chartInstance.data.datasets.push({
                    label: code,
                    data: finalValues,
                    borderColor: customColors[colorIndex % customColors.length],
                    backgroundColor: customColors[colorIndex % customColors.length].replace(
                        ')', ', 0.15)').replace('rgb', 'rgba'),
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                });
                chartInstance.update();
                updateGrowthText(chartInstance.data.datasets[0].data, chartInstance.data.datasets
                    .slice(1).map(ds => ds.data));
            }
            if (compareSymbols.length >= 3) {
                input.disabled = true;
            }
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');
            input.value = '';
        }
    });

    input.addEventListener('keypress', async (e) => {
        if (e.key === 'Enter') {
            const code = input.value.trim().toUpperCase();
            if (compareSymbols.length >= 3) {
                alert('Chỉ so sánh tối đa 4 công ty');
                input.value = '';
                input.disabled = true;
                return;
            }
            if (allSymbols.includes(code) && !compareSymbols.includes(code) && code !==
                defaultSymbol) {
                compareSymbols.push(code);
                const {
                    from_date,
                    to_date
                } = getDateRange(currentRange);
                const newData = await fetchNormalizedPerformance(code, from_date, to_date);
                const newLabels = Object.keys(newData || {});
                const newValues = Object.values(newData || {}).map(val => val || 0);
                const initialLabels = chartInstance ? chartInstance.data.labels :
                    <?php echo json_encode($labels); ?>;
                let finalValues = newValues;
                if (newLabels.length && initialLabels.length && newLabels.join() !== initialLabels
                    .join()) {
                    finalValues = initialLabels.map(label => {
                        const index = newLabels.indexOf(label);
                        return index !== -1 ? newValues[index] : 0;
                    });
                }
                const colorIndex = compareSymbols.length; // Sử dụng chỉ số của dataset so sánh
                chartInstance.data.datasets.push({
                    label: code,
                    data: finalValues,
                    borderColor: customColors[colorIndex % customColors.length],
                    backgroundColor: customColors[colorIndex % customColors.length].replace(
                        ')', ', 0.15)').replace('rgb', 'rgba'),
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 0
                });
                chartInstance.update();
                updateGrowthText(chartInstance.data.datasets[0].data, chartInstance.data.datasets
                    .slice(1).map(ds => ds.data));
            } else if (!allSymbols.includes(code)) {
                alert('Mã công ty không hợp lệ. Vui lòng chọn từ danh sách.');
            }
            if (compareSymbols.length >= 3) {
                input.disabled = true;
            }
            input.value = '';
        }
    });

    function updateDropdown(symbols) {
        dropdown.innerHTML = '';
        if (compareSymbols.length >= 3) {
            const div = document.createElement('div');
            div.className = 'px-2 py-1 text-gray-500';
            div.textContent = 'Chỉ so sánh tối đa 4 công ty';
            dropdown.appendChild(div);
            return;
        }
        const availableSymbols = symbols.filter(sym => !compareSymbols.includes(sym) && sym !== defaultSymbol);
        if (availableSymbols.length > 0) {
            availableSymbols.forEach(sym => {
                const div = document.createElement('div');
                div.className = 'px-2 py-1 hover:bg-gray-100 cursor-pointer symbol-option';
                div.setAttribute('data-code', sym);
                div.textContent = sym;
                dropdown.appendChild(div);
            });
        } else {
            const div = document.createElement('div');
            div.className = 'px-2 py-1 text-gray-500';
            div.textContent = 'Không tìm thấy';
            dropdown.appendChild(div);
        }
    }
    document.getElementById('compare-symbol-performance').addEventListener('click', (e) => {
        if (e.target.classList.contains('dash-style')) {
            const spanParent = e.target.closest('.remove-item');
            if (spanParent) {
                const symbolToRemove = spanParent.querySelector('.remove-symbol').getAttribute(
                    'data-symbol');
                const index = compareSymbols.indexOf(symbolToRemove);
                if (index !== -1) {
                    compareSymbols.splice(index, 1);
                    chartInstance.data.datasets = chartInstance.data.datasets.filter(ds => ds.label !==
                        symbolToRemove);
                    chartInstance.update();
                    updateGrowthText(chartInstance.data.datasets[0].data, chartInstance.data.datasets
                        .slice(1).map(ds => ds.data));
                    input.disabled = false;
                }
            }
        }
    });
});
</script>