<?php
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

// Khởi tạo $uniqueId
$uniqueId = 'ratio_' . Str::random(8);
$symbol = request()->get(key: 'symbol') ?? $symbol ?? 'ABB';

$data = $incomeStatementFundamental['data'] ?? [];

if (empty($data)) {
    echo '<p>Không có dữ liệu để hiển thị tỷ số tài chính.</p>';
    return;
}

$headersWithDates = [];
$selectedKeys = [];
$headers = [];
$rows = [];

$headersWithDates = collect($data)->mapWithKeys(function ($item) {
    if (strpos($item['key'], '_') !== false) {
        [$year, $quarter] = explode('_', $item['key']);
        $quarter = (int)$quarter;
        if (($quarter >= 1 && $quarter <= 4 || $quarter == 5) && preg_match('/^\d{4}$/', $year)) {
            try {
                if ($quarter >= 1 && $quarter <= 4) {
                    $quarterStart = new DateTime("{$year}-" . (($quarter - 1) * 3 + 1) . "-01");
                } else {
                    $quarterStart = new DateTime("{$year}-01-01"); // Xử lý Q5
                }
                return [$item['key'] => $quarterStart];
            } catch (Exception $e) {
                \Log::error("DateTime error for key {$item['key']}: {$e->getMessage()}");
                return [$item['key'] => now()];
            }
        }
    }
    \Log::warning("Invalid key for DateTime: {$item['key']}");
    return [$item['key'] => now()];
})->sortByDesc(fn($date) => $date->getTimestamp())->all();

$selectedKeys = array_keys($headersWithDates);
$headers = array_map(function ($key) {
    if (strpos($key, '_') !== false) {
        [$year, $quarter] = explode('_', $key);
        if ($quarter == 5 && preg_match('/^\d{4}$/', $year)) {
            return 'Q5/' . $year;
        } elseif ($quarter >= 1 && $quarter <= 4 && preg_match('/^\d{4}$/', $year)) {
            return 'Q' . $quarter . '/' . $year;
        }
    }
    return $key;
}, $selectedKeys);

$currentDate = now();

// Dynamically generate mappedFields by selecting fields where the value in the first period is not null and not zero
$mappedFields = [];
$firstPeriodKey = $selectedKeys[0] ?? null;

if (!empty($data) && $firstPeriodKey) {
    $firstPeriodItem = collect($data)->firstWhere('key', $firstPeriodKey);
    
    if ($firstPeriodItem && isset($firstPeriodItem['value']) && is_array($firstPeriodItem['value'])) {
        $validFields = [];
        foreach ($firstPeriodItem['value'] as $field => $value) {
            if (!is_null($value) && $value != 0) {
                $validFields[] = $field;
            }
        }
        $validFields = array_diff($validFields, ['symbol', 'timestamp']);

        sort($validFields);
        $validFields = array_slice($validFields, 0, 15);

        $mappedFields = array_combine(
            array_map(function ($field) {
                return str_replace('_', ' ', ucwords($field, '_'));
            }, $validFields),
            $validFields
        );
    }
}

$formatNumber = function ($value) {
    if (!is_numeric($value)) {
        return (string)$value;
    }
    $absValue = abs($value);
    $sign = $value < 0 ? '-' : '';
    
    if ($absValue >= 1_000_000_000_000) {
        return $sign . number_format($absValue / 1_000_000_000_000, 2) . 'T';
    } elseif ($absValue >= 1_000_000_000) {
        return $sign . number_format($absValue / 1_000_000_000, 2) . 'B';
    } elseif ($absValue >= 1_000_000) {
        return $sign . number_format($absValue / 1_000_000, 2) . 'M';
    } elseif ($absValue >= 1_000) {
        return $sign . number_format($absValue / 1_000, 2) . 'K';
    }
    return $sign . number_format($absValue, 2);
};

$calculateValue = function ($item, $field) {
    return $item['value'][$field] ?? null;
};

foreach ($mappedFields as $label => $field) {
    $row = [$label];
    $chartData = [];
    foreach ($selectedKeys as $key) {
        $item = collect($data)->firstWhere('key', $key);
        $value = $item ? $calculateValue($item, $field) : null;
        if (is_null($value)) {
            $row[] = '0';
            $chartData[] = 0;
        } elseif (is_numeric($value)) {
            $row[] = $formatNumber($value);
            $chartData[] = (float)$value;
        } else {
            $row[] = (string)$value;
            $chartData[] = 0;
        }
    }
    $rows[] = [
        'label' => $label,
        'values' => array_slice($row, 1),
        'chartData' => $chartData,
    ];
}

$latest5Keys = collect($headersWithDates)
    ->sortByDesc(fn($date) => $date->getTimestamp()) // sắp xếp mới nhất trước
    ->take(5)                                        // lấy 5 kỳ gần nhất
    ->reverse()                                      // đảo lại để theo thứ tự tăng dần
    ->keys()
    ->values()
    ->toArray();

$trendData = collect($rows)->map(function ($row) use ($latest5Keys, $selectedKeys) {
    $data = [];
    foreach ($latest5Keys as $key) {
        $index = array_search($key, $selectedKeys);
        $data[] = $index !== false ? $row['chartData'][$index] : 0;
    }
    return $data;
})->toArray();

$trendLabels = array_map(function ($key) {
    if (strpos($key, '_') !== false) {
        [$year, $quarter] = explode('_', $key);
        return 'Q' . $quarter . '/' . $year;
    }
    return $key;
}, $latest5Keys);

$startYear = now()->year;
?>

<div class="bg-white rounded-xl border mt-6 incomestatment-<?php echo $uniqueId; ?>">
    <div class="flex items-center gap-4 mb-4 p-6">
        <span class="font-semibold text-base">Báo cáo</span>
        <select class="bg-white border border-[#e7e7e7] text-[#b4b4b4] rounded-[5px] px-[10px] py-[5px] cursor-pointer"
            id="periodToggle-<?php echo $uniqueId; ?>">
            <option value="quarterly" selected>Theo Quý</option>
            <option value="yearly">Theo Năm</option>
        </select>
        <div class="ml-auto flex items-center gap-2">
            <button class="px-2 py-1 rounded-full border-black border hover:bg-gray-100"
                id="prevButton-<?php echo $uniqueId; ?>" aria-label="Cuộn bảng sang trái">←</button>
            <button class="px-2 py-1 rounded-full border-black border hover:bg-gray-100"
                id="nextButton-<?php echo $uniqueId; ?>" aria-label="Cuộn bảng sang phải">→</button>
        </div>
    </div>

    <div class="overflow-x-auto w-full" id="scrollContainer-<?php echo $uniqueId; ?>">
        <table class="min-w-max text-left" id="balanceTable-<?php echo $uniqueId; ?>">
            <thead>
                <tr class="text-gray-500 border-b bg-gray-200">
                    <th class="py-4 pr-4 font-medium pl-6 sticky left-0 bg-gray-200 text-sm z-10"
                        style="font-weight: 700;">Bảng chỉ số tài chính</th>
                    <th class="py-4 pr-4 font-medium pl-6 text-sm min-w-[120px]" style="font-weight: 700;">Xu hướng</th>
                    @foreach ($headers as $header)
                    <th class="px-4 py-2 font-medium text-sm text-center header" style="min-width: 100px;">
                        {{ $header }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $index => $row)
                <tr class="border-b last:border-none hover:bg-gray-50">
                    <td class="py-2 px-3 text-black sticky left-0 bg-white text-sm z-10">
                        {{ $row['label'] }}</td>
                    <td class="py-6 px-6 text-center relative" style="min-width: 120px;">
                        <canvas class="incomeStatementFundamental-<?php echo $index; ?>-<?php echo $uniqueId; ?>"
                            height="40"></canvas>
                    </td>
                    @foreach ($row['values'] as $cell)
                    <td
                        class="px-4 py-6 text-center text-xs cell {{ str_starts_with($cell, '-') ? 'custom-negative' : '' }} ">
                        {{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="chartPopup-<?php echo $uniqueId; ?>" class="bs-chart-popup" style="display: none;">
        <div class="chart-popup-content-bs">
            <div class="chart-popup-header-bs">
                <div class="chart-popup-title-container">
                    <h3 id="chartPopupTitle-<?php echo $uniqueId; ?>" class="chart-popup-title"></h3>
                    <div id="symbolWrapper-<?php echo $uniqueId; ?>" class="symbol-wrapper">
                        <span class="symbol-icon"><?php echo $symbol; ?></span>
                        <span id="hoverValue-<?php echo $uniqueId; ?>" class="hover-value hidden">Giá trị</span>
                    </div>
                </div>
                <button id="closePopup-<?php echo $uniqueId; ?>" class="chart-popup-close">×</button>
            </div>
            <canvas id="incomeStatementFundamentalPopup" style="margin-top: 20px;"></canvas>
        </div>
    </div>
</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', () => {
        const uniqueId = '<?php echo $uniqueId; ?>';
        const table = document.getElementById(`balanceTable-${uniqueId}`);
        const scrollContainer = document.getElementById(`scrollContainer-${uniqueId}`);
        const popup = document.getElementById(`chartPopup-${uniqueId}`);
        const popupChartCanvas = document.getElementById(`incomeStatementFundamentalPopup`);
        const popupTitle = document.getElementById(`chartPopupTitle-${uniqueId}`);
        const symbolIcon = document.querySelector(`#symbolWrapper-${uniqueId} .symbol-icon`);
        const hoverValue = document.getElementById(`hoverValue-${uniqueId}`);
        const closePopupButton = document.getElementById(`closePopup-${uniqueId}`);
        const prevButton = document.getElementById(`prevButton-${uniqueId}`);
        const nextButton = document.getElementById(`nextButton-${uniqueId}`);
        const periodToggle = document.getElementById(`periodToggle-${uniqueId}`);

        if (!table || !scrollContainer || !popup || !popupChartCanvas || !popupTitle || !closePopupButton ||
            !symbolIcon || !hoverValue || !prevButton || !nextButton || !periodToggle) {
            return;
        }

        let rows = <?php echo json_encode($rows); ?>; // Khởi tạo rows từ dữ liệu PHP

        // Handle touch scrolling
        scrollContainer.addEventListener('touchmove', (e) => {
            const touch = e.touches[0];
            const deltaX = touch.clientX - (scrollContainer.lastTouchX || touch.clientX);
            scrollContainer.lastTouchX = touch.clientX;
            if (Math.abs(deltaX) > 0) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        scrollContainer.addEventListener('touchstart', (e) => {
            scrollContainer.lastTouchX = e.touches[0].clientX;
        });

        function formatNumberForChart(value) {
            if (!isFinite(value)) return value;
            const absValue = Math.abs(value);
            const sign = value < 0 ? '-' : '';
            let formattedValue;
            if (absValue >= 1000000000000) {
                formattedValue = sign + (absValue / 1000000000000).toFixed(2) + 'T';
            } else if (absValue >= 1000000000) {
                formattedValue = sign + (absValue / 1000000000).toFixed(2) + 'B';
            } else if (absValue >= 1000000) {
                formattedValue = sign + (absValue / 1000000).toFixed(2) + 'M';
            } else if (absValue >= 1000) {
                formattedValue = sign + (absValue / 1000).toFixed(2) + 'K';
            } else {
                formattedValue = sign + absValue.toFixed(2);
            }
            return formattedValue;
        }
        async function fetchRatioData(symbol, reportType) {
            try {
                const response = await fetch(
                    `http://103.97.127.42:8001/fundamental/income-statement?symbol=${symbol}&report_type=${reportType}`
                );
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();
                if (!data || !data.data || !Array.isArray(data.data) || data.data.length === 0) {
                    throw new Error('Dữ liệu rỗng hoặc không hợp lệ');
                }
                return data.data;
            } catch (error) {
                alert(`Không thể tải dữ liệu ${reportType}: ${error.message}`);
                return [];
            }
        }

        function processData(data, reportType) {
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('No valid data provided for processing');
                return {
                    headers: [],
                    rows: [],
                    trendLabels: [],
                    trendData: []
                };
            }
            const headersWithDates = {};
            data.forEach(item => {
                let date;
                if (item['key'].includes('_')) {
                    let [year, quarter] = item['key'].split('_');
                    quarter = parseInt(quarter);
                    if (/^\d{4}$/.test(year)) {
                        try {
                            if (quarter >= 1 && quarter <= 4) {
                                date = new Date(`${year}-${((quarter - 1) * 3 + 1)}-01`);
                            } else if (quarter === 5) {
                                date = new Date(`${year}-01-01`); // Xử lý yearly
                            } else {
                                console.warn(`Invalid quarter for key ${item['key']}: ${quarter}`);
                                date = new Date();
                            }
                        } catch (e) {
                            console.error(`DateTime error for key ${item['key']}: ${e.message}`);
                            date = new Date();
                        }
                    }
                } else {
                    console.warn(`Invalid key format: ${item['key']}`);
                    date = new Date();
                }
                headersWithDates[item['key']] = date;
            });

            const selectedKeys = Object.keys(headersWithDates)
                .filter(key => data.some(item => item.key === key))
                .sort((a, b) => headersWithDates[b].getTime() - headersWithDates[a].getTime());
            const headers = selectedKeys.map(key => {
                if (key.includes('_')) {
                    let [year, quarter] = key.split('_');
                    if (reportType === 'yearly' && quarter === '5') {
                        return `Q5/${year}`;
                    } else if (quarter >= 1 && quarter <= 4) {
                        return `Q${quarter}/${year}`;
                    }
                }
                return key;
            });

            const mappedFields = {};
            const firstPeriodKey = selectedKeys[0];
            if (data.length && firstPeriodKey) {
                const firstPeriodItem = data.find(item => item.key === firstPeriodKey);
                if (firstPeriodItem && firstPeriodItem.value && typeof firstPeriodItem.value === 'object') {
                    const validFields = Object.entries(firstPeriodItem.value)
                        .filter(([field, value]) => value !== null && value !== 0)
                        .map(([field]) => field)
                        .filter(field => !['symbol', 'timestamp'].includes(field))
                        .sort()
                        .slice(0, 15);
                    validFields.forEach(field => {
                        mappedFields[field.replace(/_/g, ' ').replace(/\b\w/g, c => c
                            .toUpperCase())] = field;
                    });
                }
            }

            const rows = [];
            const formatNumber = function(value) {
                if (!isNumeric(value)) return String(value);
                const absValue = Math.abs(value);
                const sign = value < 0 ? '-' : '';
                if (absValue >= 1000000000000) return sign + (absValue / 1000000000000).toFixed(2) +
                    'T';
                if (absValue >= 1000000000) return sign + (absValue / 1000000000).toFixed(2) + 'B';
                if (absValue >= 1000000) return sign + (absValue / 1000000).toFixed(2) + 'M';
                if (absValue >= 1000) return sign + (absValue / 1000).toFixed(2) + 'K';
                return sign + absValue.toFixed(2);
            };

            const calculateValue = function(item, field) {
                return item.value[field] ?? null;
            };

            Object.entries(mappedFields).forEach(([label, field]) => {
                const row = [label];
                const chartData = [];
                selectedKeys.forEach(key => {
                    const item = data.find(item => item.key === key);
                    const value = item ? calculateValue(item, field) : null;
                    if (value === null) {
                        row.push('0');
                        chartData.push(0);
                    } else if (isNumeric(value)) {
                        row.push(formatNumber(value));
                        chartData.push(parseFloat(value));
                    } else {
                        row.push(String(value));
                        chartData.push(0);
                    }
                });
                rows.push({
                    label: label,
                    values: row.slice(1),
                    chartData: chartData
                });
            });

            const latest5Keys = selectedKeys.slice(0, 5)
                .reverse(); // Lấy 5 kỳ gần nhất theo thứ tự tăng dần

            const trendData = rows.map(row => {
                const data = [];
                latest5Keys.forEach(key => {
                    const index = selectedKeys.indexOf(key);
                    data.push(index !== -1 ? row.chartData[index] : 0);
                });
                return data;
            });

            const trendLabels = latest5Keys.map(key => {
                if (key.includes('_')) {
                    let [year, quarter] = key.split('_');
                    if (quarter >= 1 && quarter <= 4) {
                        return `Q${quarter}/${year}`;
                    } else if (quarter === 5) {
                        return `Q5/${year}`; // Sửa thành Q5/YYYY
                    }
                }
                return key;
            });

            return {
                headers,
                rows,
                trendLabels,
                trendData
            };
        }

        function scrollOneColumn(direction) {
            const columnWidth = 100;
            const currentScroll = scrollContainer.scrollLeft;
            const maxScroll = table.offsetWidth - scrollContainer.offsetWidth;
            let newScroll = currentScroll + (direction * columnWidth);

            newScroll = Math.max(0, Math.min(newScroll, maxScroll));
            scrollContainer.scrollTo({
                left: newScroll,
                behavior: 'smooth'
            });

            updateButtonState();
        }

        function updateButtonState() {
            const currentScroll = scrollContainer.scrollLeft;
            const maxScroll = table.offsetWidth - scrollContainer.offsetWidth;

            prevButton.disabled = currentScroll === 0;
            nextButton.disabled = currentScroll >= maxScroll || maxScroll <= 0;
        }

        function updateTable(type) {
            fetchRatioData('<?php echo $symbol; ?>', type).then(data => {
                const {
                    headers,
                    rows: newRows,
                    trendLabels,
                    trendData
                } = processData(data, type);
                rows = newRows; // Gán lại toàn bộ rows từ dữ liệu API

                // Cập nhật headers
                const headerRow = table.querySelector('thead tr');
                const headerCells = headerRow.querySelectorAll('th.header');
                headerCells.forEach((cell, index) => {
                    if (index >= 2) cell.textContent = headers[index - 2] || '';
                });

                // Cập nhật rows và phá hủy chart cũ
                const rowElements = table.querySelectorAll('tbody tr');
                charts.forEach(chart => chart.destroy());
                charts = [];

                rowElements.forEach((row, rowIndex) => {
                    const cells = row.querySelectorAll('td.cell');
                    const rowData = rows[rowIndex] || {
                        values: [],
                        chartData: []
                    };
                    cells.forEach((cell, cellIndex) => {
                        if (cellIndex >= 2) {
                            cell.textContent = rowData.values[cellIndex - 2] || '0';
                            cell.classList.toggle('custom-negative', rowData.values[
                                cellIndex - 2]?.startsWith('-'));
                        }
                    });

                    // Tạo lại chart mới
                    const canvas = row.querySelector(
                        `canvas.incomeStatementFundamental-${rowIndex}-${uniqueId}`);
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
                        gradient.addColorStop(0, 'rgba(30, 144, 255, 0.1)');
                        gradient.addColorStop(0.3, 'rgba(30, 144, 255, 0.05)');
                        gradient.addColorStop(1, 'rgba(30, 144, 255, 0)');

                        const chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: trendLabels,
                                datasets: [{
                                    data: trendData[rowIndex] || [],
                                    label: 'Trend',
                                    borderColor: '#1e90ff',
                                    backgroundColor: gradient,
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 0,
                                    borderWidth: 1.5,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        display: false
                                    },
                                    y: {
                                        display: false,
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        enabled: false
                                    }
                                },
                                hover: {
                                    mode: 'nearest',
                                    intersect: true
                                },
                                animation: {
                                    duration: 200,
                                    easing: 'linear'
                                }
                            }
                        });
                        charts[rowIndex] = chart;
                        canvas.addEventListener('click', () => showPopup(rowIndex));
                    }
                });

                periodToggle.value = type;
                updateButtonState();
            }).catch(error => console.error('Error updating table:', error));
        }
        let charts = [];
        let popupChart = null;
        let currentChartType = 'quarterly';

        function showPopup(index) {
            if (!rows[index]) {
                console.warn('No data available for index:', index);
                return;
            }
            document.body.style.overflow = 'hidden';
            popupTitle.textContent = rows[index].label || ''; // Đặt tên field làm tiêu đề
            hoverValue.textContent = 'Giá trị';
            hoverValue.classList.add('hidden');
            popup.style.display = 'block';

            // Đảm bảo phá hủy chart cũ và làm sạch canvas
            if (popupChart) {
                popupChart.destroy();
                popupChart = null;
            }
            const ctx = popupChartCanvas.getContext('2d');
            ctx.clearRect(0, 0, popupChartCanvas.width, popupChartCanvas.height); // Làm sạch canvas

            fetchRatioData('<?php echo $symbol; ?>', currentChartType).then(data => {
                const {
                    trendLabels,
                    trendData
                } = processData(data, currentChartType);
                const dataArray = trendData[index] || [];
                const labels = trendLabels;

                const gradient = ctx.createLinearGradient(0, 0, 0, popupChartCanvas.height);
                gradient.addColorStop(0, 'rgba(30, 143, 255, 0.38)'); // Màu nhạt ở đầu
                gradient.addColorStop(0.3, 'rgba(30, 143, 255, 0.24)'); // Nhạt dần
                gradient.addColorStop(1, 'rgba(30, 144, 255, 0)'); // Thành trong suốt ở cuối

                popupChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataArray,
                            label: 'Trend',
                            borderColor: '#1e90ff',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            showLine: true,
                            borderWidth: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        devicePixelRatio: window.devicePixelRatio,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: currentChartType === 'quarterly' ? 'Quý' : 'Năm'
                                }
                            },
                            y: {
                                display: true,
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: ''
                                },
                                ticks: {
                                    callback: function(value) {
                                        return formatNumberForChart(
                                            value); // Định dạng giá trị trên trục Y
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 4,
                                borderColor: '#1e90ff',
                                borderWidth: 1,
                                callbacks: {
                                    label: context => formatNumberForChart(context.parsed.y)
                                }
                            }
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        animation: {
                            duration: 200,
                            easing: 'linear'
                        },
                        events: ['mousemove', 'mouseout', 'click', 'touchstart',
                            'touchmove'
                        ]
                    }
                });

                popupChart.index = index;

                popupChartCanvas.addEventListener('mousemove', (e) => {
                    const chartArea = popupChart.chartArea;
                    if (e.offsetX >= chartArea.left && e.offsetX <= chartArea.right && e
                        .offsetY >= chartArea.top && e.offsetY <= chartArea.bottom) {
                        const activePoints = popupChart.getElementsAtEventForMode(e,
                            'index', {
                                intersect: false
                            }, true);
                        if (activePoints.length > 0) {
                            const idx = activePoints[0].index;
                            const value = dataArray[idx];
                            hoverValue.textContent = formatNumberForChart(value);
                            hoverValue.classList.remove('hidden');
                        }
                    }
                });

                popupChartCanvas.addEventListener('mouseout', () => {
                    hoverValue.classList.add('hidden');
                    hoverValue.textContent = 'Giá trị';
                });
            }).catch(error => console.error('Error loading popup chart:', error));
        }

        function closePopup() {
            document.body.style.overflow = '';
            popup.style.display = 'none';
            if (popupChart) {
                popupChart.destroy();
                popupChart = null;
            }
            hoverValue.classList.add('hidden');
            hoverValue.textContent = 'Giá trị';
        }

        function initializeTableAndCharts() {
            fetchRatioData('<?php echo $symbol; ?>', 'quarterly').then(data => {
                const {
                    headers,
                    rows: newRows,
                    trendLabels,
                    trendData
                } = processData(data, 'quarterly');
                rows = newRows;
                const canvases = document.querySelectorAll(
                    `.incomestatment-${uniqueId} [class^="incomeStatementFundamental-"]`);
                canvases.forEach((canvas, index) => {
                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
                    gradient.addColorStop(0, 'rgba(30, 144, 255, 0.1)');
                    gradient.addColorStop(0.3, 'rgba(30, 144, 255, 0.05)');
                    gradient.addColorStop(1, 'rgba(30, 144, 255, 0)');

                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                data: trendData[index] || [],
                                label: 'Trend',
                                borderColor: '#1e90ff',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 0,
                                borderWidth: 1.5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    display: false
                                },
                                y: {
                                    display: false,
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 10,
                                    cornerRadius: 4,
                                    borderColor: '#1e90ff',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: context => formatNumberForChart(
                                            context.parsed.y)
                                    }
                                }
                            },
                            hover: {
                                mode: 'index',
                                intersect: false
                            },
                            animation: {
                                duration: 200,
                                easing: 'linear'
                            }
                        }
                    });
                    charts[index] = chart;
                    canvas.addEventListener('click', () => showPopup(index));
                });
                updateButtonState(); // Cập nhật trạng thái nút sau khi khởi tạo
            }).catch(error => console.error('Error initializing charts:', error));
        }
        initializeTableAndCharts();

        scrollContainer.addEventListener('scroll', updateButtonState);
        prevButton.addEventListener('click', () => scrollOneColumn(-1));
        nextButton.addEventListener('click', () => scrollOneColumn(1));

        periodToggle.addEventListener('change', function() {
            updateTable(this.value);
        });

        closePopupButton.addEventListener('click', closePopup);
        popup.addEventListener('click', (e) => {
            if (e.target === popup) closePopup();
        });
    });
})();
</script>

<style>
.symbol-icon {
    font-weight: bold;
    color: #1e90ff;
    font-size: 14px;
}

.hover-value {
    font-size: 13px;
    background: #f6f6f6;
    padding: 4px 8px;
    border-radius: 4px;
    top: -10px;
    left: 41px;
    white-space: nowrap;
    z-index: 10;
    transition: opacity 0.2s ease;
}

.hover-value.hidden {
    opacity: 0;
    pointer-events: none;
}

.incomestatment-<?php echo $uniqueId;

?>select {
    background-color: #f5f5f5;
    color: #333;
    border-radius: 5px;
    padding: 6px 12px;
}

.incomestatment-<?php echo $uniqueId;

?>select:focus {
    background-color: #fff;
    color: #000;
    border-color: #1e90ff;
}

.incomestatment-<?php echo $uniqueId;

?>button:disabled {
    background-color: rgb(240, 240, 240);
    cursor: not-allowed;
    opacity: 0.6;
    color: #767676;
    border: 1px solid #767676;
}

#balanceTable-<?php echo $uniqueId;

?>td:nth-child(2) {
    padding: 10px;
    height: 60px;
    position: relative;
    min-width: 120px;
}

#balanceTable-<?php echo $uniqueId;

?>td:first-child {
    min-width: 200px;
    font-size: 13px;
    padding-left: 12px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.incomeStatementFundamental-0-<?php echo $uniqueId;

?> {
    width: 120px;
    height: 40px;
    display: block;
    cursor: pointer;
}

[class^="incomeStatementFundamental-<?php echo $uniqueId; ?>"]:not(.incomeStatementFundamental-0-<?php echo $uniqueId; ?>) {
    width: 100px;
    height: 40px;
    display: block;
    cursor: pointer;
}

#scrollContainer-<?php echo $uniqueId;

?> {
    overflow-x: auto;
    overflow-y: auto;
    scroll-behavior: smooth;
    width: 100%;
}

#balanceTable-<?php echo $uniqueId;
?>th.header,
#balanceTable-<?php echo $uniqueId;

?>td.cell {
    min-width: 100px;
    white-space: nowrap;
}

#balanceTable-<?php echo $uniqueId;
?>th,
#balanceTable-<?php echo $uniqueId;

?>td {
    padding: 8px;
}

/* Thay đổi màu đỏ của số âm thành #B51001 */
.custom-negative {
    color: #B51001 !important;
}

.bs-chart-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0 0 0 / 81%);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.bs-chart-popup .chart-popup-content-bs {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 1100px;
    width: 90%;
    height: 90vh;
    max-height: 80vh;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-sizing: border-box;
    display: block;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
}

.bs-chart-popup .chart-popup-header-bs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    height: 40px;
    min-height: 40px;
}

.bs-chart-popup .chart-popup-title {
    margin-bottom: 0;
    /* Loại bỏ margin bottom */
    font-size: 18px;
    font-weight: 600;
    line-height: 1;
    padding: 0;
}

.bs-chart-popup .chart-popup-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #333;
    position: absolute;
    top: 5px;
    right: 15px;
}

.bs-chart-popup .chart-popup-close:hover {
    color: #000;
}

#incomeStatementFundamentalPopup {
    display: block;
    box-sizing: border-box;
    width: 100%;
    height: calc(100% - 50px);
    max-height: 100%;
    max-width: 100%;
    overflow: hidden;
}

.chart-popup-value {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
    padding: 5px;
    background-color: #f0f0f0;
    margin-bottom: 20px;
    border-radius: 5px;
    display: none;
}

.chart-popup-value:hover {
    display: none;
}

#incomeStatementFundamentalPopup:hover~.chart-popup-value {
    display: none;
}

[class^="incomeStatementFundamental-"] {
    width: 120px !important;
    max-width: 120px !important;
}
</style>