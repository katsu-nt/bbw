<?php
use Illuminate\Support\Str;

// Tạo ID duy nhất cho bảng
$uniqueId = 'is_' . Str::random(8);
$symbol = $symbol ?? 'ABB';

// Lấy dữ liệu từ biến $cashflowFundamental
$data = $cashflowFundamental['data'] ?? [];

// Ghi log để debug dữ liệu
\Log::info('Cashflow Fundamental Input:', ['cashflowFundamental' => $cashflowFundamental]);
\Log::info('Data Before Processing:', ['data' => $data]);

// Kiểm tra dữ liệu rỗng hoặc không phải mảng
if (empty($data) || !is_array($data)) {
    echo '<p>Không có dữ liệu để hiển thị bảng lưu chuyển tiền tệ.</p>';
    return;
}

// Hàm tính toán giá trị cho các trường
$calculateValue = function ($item, $field) {
    $value = isset($item['value'][$field]) && is_numeric($item['value'][$field]) ? (float)$item['value'][$field] : 0;
    return $value;
};

// Hàm xử lý dữ liệu để tạo bảng và biểu đồ
function prepareData($data, $currentDate, $calculateValue) {
    if (empty($data) || !is_array($data)) {
        return [
            'quarterlyHeaders' => [],
            'quarterlySelectedKeys' => [],
            'yearlyHeaders' => [],
            'yearlySelectedKeys' => [],
            'yearlyHeadersWithDates' => []
        ];
    }

    // Tách dữ liệu quý và năm
    $quarterlyData = collect($data)->filter(function ($item) {
        return isset($item['key']) && is_string($item['key']) && strpos($item['key'], '_') !== false && preg_match('/^\d{4}_\d$/', $item['key']);
    });

    $yearlyData = collect($data)->filter(function ($item) {
        return isset($item['key']) && is_string($item['key']) && preg_match('/^\d{4}$/', $item['key']);
    });

    // Tạo tiêu đề cho quý
    $quarterlyHeaders = $quarterlyData->map(function ($item) {
        $parts = explode('_', $item['key']);
        return count($parts) >= 2 && preg_match('/^\d{4}$/', $parts[0]) ? 'Q' . $parts[1] . '/' . $parts[0] : $item['key'];
    })->values()->all();

    $quarterlyHeadersWithDates = $quarterlyData->mapWithKeys(function ($item) use ($currentDate) {
        $parts = explode('_', $item['key']);
        if (count($parts) < 2) {
            return [$item['key'] => now()];
        }
        $year = $parts[0];
        $quarter = (int)$parts[1];
        try {
            $month = ($quarter - 1) * 3 + 1;
            $month = min($month, 12);
            $date = new DateTime("{$year}-{$month}-01");
            return [$item['key'] => $date];
        } catch (Exception $e) {
            return [$item['key'] => now()];
        }
    })->sortKeys()->all();

    // Tạo tiêu đề cho năm
$yearlyHeaders = $yearlyData->map(function ($item) {
    return $item['key'];
})->values()->sortByDesc(function ($key) {
    return substr($key, 0, 4); // Sắp xếp theo năm giảm dần
})->all();
   $yearlyHeadersWithDates = $yearlyData->mapWithKeys(function ($item) use ($currentDate) {
    $year = substr($item['key'], 0, 4); // Lấy phần năm từ YYYY_X
    try {
        return [$item['key'] => new DateTime("{$year}-01-01")];
    } catch (Exception $e) {
        return [$item['key'] => now()];
    }
})->sortByDesc(function ($date) {
    return $date; // Sắp xếp giảm dần theo ngày
})->all();

$yearlySelectedKeys = array_keys($yearlyHeadersWithDates);
    return [
        'quarterlyHeaders' => $quarterlyHeaders,
        'quarterlySelectedKeys' => array_keys($quarterlyHeadersWithDates),
        'yearlyHeaders' => $yearlyHeaders,
        'yearlySelectedKeys' => $yearlySelectedKeys,
        'yearlyHeadersWithDates' => $yearlyHeadersWithDates
    ];
}

// Gọi hàm prepareData để xử lý dữ liệu
$currentDate = now();
$processedData = prepareData($data, $currentDate, $calculateValue);

// Tách headers và keys
$quarterlyHeaders = $processedData['quarterlyHeaders'];
$quarterlySelectedKeys = $processedData['quarterlySelectedKeys'];
$yearlyHeaders = $processedData['yearlyHeaders'];
$yearlySelectedKeys = $processedData['yearlySelectedKeys'];
$yearlyHeadersWithDates = $processedData['yearlyHeadersWithDates'];

// Lấy các trường hợp lệ từ dữ liệu API
$validFields = [];
foreach ($data as $item) {
    if (!isset($item['value']) || !is_array($item['value'])) {
        continue;
    }
    foreach ($item['value'] as $key => $val) {
        if (is_numeric($val) && !in_array($key, $validFields)) {
            $validFields[] = $key;
        }
        if (count($validFields) >= 10) {
            break 2;
        }
    }
}

// Tạo nhãn cho các trường
$mappedFields = [];
function formatFieldLabel($field) {
    return ucwords(str_replace('_', ' ', $field));
}
foreach ($validFields as $field) {
    $mappedFields[formatFieldLabel($field)] = $field;
}

// Tạo hàng cho bảng quý và năm
$quarterlyRows = [];
$yearlyRows = [];

foreach ($mappedFields as $label => $field) {
    // Quarterly
    $quarterlyRow = [$label];
    $quarterlyChartData = [];
    foreach ($quarterlySelectedKeys as $key) {
        $item = collect($data)->firstWhere('key', $key);
        $value = $item ? $calculateValue($item, $field) : null;
        if (is_null($value)) {
            $quarterlyRow[] = '';
            $quarterlyChartData[] = 0;
        } elseif (is_numeric($value)) {
            $formatted = number_format($value / 1_000_000_000, 2);
            $quarterlyRow[] = $formatted;
            $quarterlyChartData[] = (float)$formatted;
        } else {
            $quarterlyRow[] = (string)$value;
            $quarterlyChartData[] = 0;
        }
    }
    $quarterlyRows[] = [
        'label' => $label,
        'values' => array_slice($quarterlyRow, 1),
        'chartData' => $quarterlyChartData,
    ];

    // Yearly
    $yearlyRow = [$label];
    $yearlyChartData = [];
    foreach ($yearlySelectedKeys as $key) {
        $item = collect($data)->firstWhere('key', $key);
        $value = $item ? $calculateValue($item, $field) : null;
        if (is_null($value)) {
            $yearlyRow[] = '';
            $yearlyChartData[] = 0;
        } elseif (is_numeric($value)) {
            $formatted = number_format($value / 1_000_000_000, 2);
            $yearlyRow[] = $formatted;
            $yearlyChartData[] = (float)$formatted;
        } else {
            $yearlyRow[] = (string)$value;
            $yearlyChartData[] = 0;
        }
    }
    $yearlyRows[] = [
        'label' => $label,
        'values' => array_slice($yearlyRow, 1),
        'chartData' => $yearlyChartData,
    ];
}

// Lấy 5 quý và 5 năm gần nhất cho biểu đồ xu hướng
$latest5QuarterKeys = collect($quarterlySelectedKeys)
    ->sortByDesc(fn($key) => $processedData['quarterlyHeadersWithDates'][$key] ?? now())
    ->take(5)
    ->values()
    ->toArray();

$trenddataQuarterly = collect($quarterlyRows)->map(function ($row) use ($latest5QuarterKeys, $quarterlySelectedKeys) {
    $data = [];
    foreach ($latest5QuarterKeys as $key) {
        $index = array_search($key, $quarterlySelectedKeys);
        $data[] = $index !== false ? $row['chartData'][$index] : 0;
    }
    return $data;
})->toArray();

$trendQuarterLabels = array_map(function ($key) {
    $parts = explode('_', $key);
    $year = $parts[0];
    $quarter = isset($parts[1]) ? (int)$parts[1] : null;
    return $quarter ? 'Q' . $quarter . '/' . $year : $key;
}, $latest5QuarterKeys);

$latest5YearKeys = collect($yearlyHeadersWithDates)
    ->sortByDesc(fn($date) => $date) // Sắp xếp giảm dần
    ->take(5) // Lấy 5 năm gần nhất
    ->keys()
    ->values()
    ->toArray();

$trenddataYearly = collect($yearlyRows)->map(function ($row) use ($latest5YearKeys, $yearlySelectedKeys) {
    $data = [];
    foreach ($latest5YearKeys as $key) {
        $index = array_search($key, $yearlySelectedKeys);
        $data[] = $index !== false ? $row['chartData'][$index] : 0;
    }
    return $data;
})->toArray();

$trendYearLabels = array_map(function ($key) {
    $parts = explode('_', $key);
    $year = $parts[0];
    $quarter = isset($parts[1]) ? (int)$parts[1] : null;
    return $quarter ? 'Q' . $quarter . '/' . $year : $year; // Chuyển 2025_1 thành Q1/2025
}, $latest5YearKeys);

// Đảm bảo dữ liệu không rỗng
$trenddataQuarterly = !empty($trenddataQuarterly) ? $trenddataQuarterly : [];
$trenddataYearly = !empty($trenddataYearly) ? $trenddataYearly : [];

?>

<!-- HTML cho bảng và biểu đồ -->
<div class="bg-white rounded-xl border mt-6 income-statement-{{ $uniqueId }}">
    <div class="flex items-center gap-4 mb-4 p-6">
        <span class="font-semibold text-base">Báo cáo</span>
        <select class="bg-white border border-[#e7e7e7] text-[#b4b4b4] rounded-[5px] px-[10px] py-[5px] cursor-pointer"
            id="periodToggle-{{ $uniqueId }}">
            <option value="quarterly" selected>Theo Quý</option>
            <option value="yearly">Theo Năm</option>
        </select>
        <select class="bg-white border border-[#e7e7e7] text-[#b4b4b4] rounded-[5px] px-[10px] py-[5px] cursor-pointer"
            id="methodToggle-{{ $uniqueId }}">
            <option value="direct" selected>Trực tiếp</option>
            <option value="indirect">Gián tiếp</option>
        </select>
        <div class="ml-auto flex items-center gap-2">
            <button class="px-2 py-1 rounded-full border-black border hover:bg-gray-100" id="prevButton-{{ $uniqueId }}"
                aria-label="Cuộn bảng sang trái">←</button>
            <button class="px-2 py-1 rounded-full border-black border hover:bg-gray-100" id="nextButton-{{ $uniqueId }}"
                aria-label="Cuộn bảng sang phải">→</button>
        </div>
    </div>
    <div class="overflow-x-auto w-full" id="scrollContainer-{{ $uniqueId }}">
        <table class="min-w-max text-left" id="balanceTable-{{ $uniqueId }}">
            <thead>
                <tr class="text-gray-500 border-b bg-gray-200">
                    <th class="py-4 pr-4 font-medium pl-6 sticky left-0 bg-gray-200 text-sm z-10"
                        style="font-weight: 700;">Bảng lưu chuyển tiền tệ</th>
                    <th class="py-4 pr-4 font-medium pl-6 text-sm min-w-[100px]" style="font-weight: 700;">Xu hướng</th>
                    @foreach ($quarterlyHeaders as $index => $header)
                    <th class="px-4 py-2 font-medium text-sm text-center header-cell quarterly-header"
                        style="min-width: 100px;">{{ $header }}</th>
                    @endforeach
                    @foreach ($yearlyHeaders as $header)
                    <th class="px-4 py-2 font-medium text-sm text-center header-cell yearly-header"
                        style="display: none; min-width: 100px;">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="tableBody-{{ $uniqueId }}">
                @foreach ($quarterlyRows as $index => $row)
                <tr class="border-b last:border-none hover:bg-gray-50">
                    <td class="py-2 px-3 text-black sticky left-0 bg-white text-sm z-10">{{ $row['label'] }}</td>
                    <td class="py-6 px-6 text-center relative" style="min-width: 100px;">
                        <canvas class="cashflow-trend-chart-{{ $index }}-{{ $uniqueId }}" height="80"></canvas>
                    </td>
                    @foreach ($row['values'] as $cell)
                    <td
                        class="px-4 py-6 text-center text-xs data-cell quarterly-cell {{ str_starts_with($cell, '-') ? 'custom-negative' : '' }}">
                        {{ $cell }}</td>
                    @endforeach
                    @foreach ($yearlyRows[$index]['values'] as $cell)
                    <td class="px-4 py-6 text-center text-xs data-cell yearly-cell {{ str_starts_with($cell, '-') ? 'custom-negative' : '' }}"
                        style="display: none;">
                        {{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="chartPopup-{{ $uniqueId }}" class="is-chart-popup" style="display: none;">
        <div class="chart-popup-content">
            <div class="chart-popup-header">
                <h3 id="chartPopupTitle-{{ $uniqueId }}" class="chart-popup-title"></h3>
                <button id="closePopup-{{ $uniqueId }}" class="chart-popup-close">×</button>
            </div>
            <canvas id="popupChart-{{ $uniqueId }}" width="1010" height="460"></canvas>
        </div>
    </div>
</div>

<!-- JavaScript để xử lý tương tác và biểu đồ -->
<script>
(function() {
    document.addEventListener('DOMContentLoaded', () => {
        const uniqueId = '{{ $uniqueId }}';
        const table = document.getElementById(`balanceTable-${uniqueId}`);
        const scrollContainer = document.getElementById(`scrollContainer-${uniqueId}`);
        const nextButton = document.getElementById(`nextButton-${uniqueId}`);
        const prevButton = document.getElementById(`prevButton-${uniqueId}`);
        const popup = document.getElementById(`chartPopup-${uniqueId}`);
        const popupChartCanvas = document.getElementById(`popupChart-${uniqueId}`);
        const popupTitle = document.getElementById(`chartPopupTitle-${uniqueId}`);
        const closePopupButton = document.getElementById(`closePopup-${uniqueId}`);
        const periodToggle = document.getElementById(`periodToggle-${uniqueId}`);
        const methodToggle = document.getElementById(`methodToggle-${uniqueId}`);
        const tableBody = document.getElementById(`tableBody-${uniqueId}`);

        if (!table || !scrollContainer || !nextButton || !prevButton || !popup || !popupChartCanvas ||
            !popupTitle || !closePopupButton || !periodToggle || !methodToggle || !tableBody) {
            console.error('Missing required DOM elements for cash flow statement');
            return;
        }

        // Dữ liệu ban đầu
        let trenddataQuarterly = @json($trenddataQuarterly);
        let trenddataYearly = @json($trenddataYearly);
        let trendQuarterLabels = @json($trendQuarterLabels);
        let trendYearLabels = @json($trendYearLabels);
        let rowLabels = @json(array_column($quarterlyRows, 'label'));
        let quarterlyHeaders = @json($quarterlyHeaders);
        let yearlyHeaders = @json($yearlyHeaders);
        let quarterlyRows = @json($quarterlyRows);
        let yearlyRows = @json($yearlyRows);

        let charts = [];
        let popupChart = null;
        let currentPeriod = periodToggle.value;
        let currentMethod = methodToggle.value;

        async function fetchCashflowData(symbol, period, method) {
            console.log(
                `Fetching: http://103.97.127.42:8001/fundamental/cashflow?symbol=${symbol}&report_type=${period}&method=${method}`
            );
            try {
                const response = await fetch(
                    `http://103.97.127.42:8001/fundamental/cashflow?symbol=${encodeURIComponent(symbol)}&report_type=${period}&method=${method}`
                );
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();
                if (!data || !Array.isArray(data.data)) {
                    throw new Error('Invalid data format from API');
                }
                console.log('Fetched cashflow data:', data);
                return prepareDataFromAPI(data.data, method);
            } catch (err) {
                console.error('Error fetching cashflow data:', err);
                alert('Không thể tải dữ liệu lưu chuyển tiền tệ. Vui lòng thử lại sau.');
                return null;
            }
        }

        function prepareDataFromAPI(data, method) {
            const validData = data.filter(item => item && typeof item === 'object' && 'key' in item &&
                'value' in item && typeof item.value === 'object');
            if (!validData.length) {
                console.warn('No valid data items with key and value fields');
                return {
                    trendData: [],
                    trendLabels: [],
                    rows: [],
                    headers: []
                };
            }

            const quarterlyData = validData.filter(item => item.key && item.key.match(/^\d{4}_\d$/));
            const yearlyData = validData.filter(item => item.key && (item.key.match(/^\d{4}$/) || item.key
                .match(/^\d{4}_\d$/)));

            const quarterlyHeaders = quarterlyData.map(item => {
                const [year, quarter] = item.key.split('_');
                return `Q${quarter}/${year}`;
            });

            const yearlyHeaders = yearlyData.map(item => {
                const [year, quarter] = item.key.split('_');
                return quarter ? `Q${quarter}/${year}` : year; // Chuyển 2025_1 thành Q1/2025
            }).sort((a, b) => {
                const yearA = a.split('/')[1] || a;
                const yearB = b.split('/')[1] || b;
                return yearB.localeCompare(yearA); // Sắp xếp giảm dần
            });

            let validFields = [];
            for (const item of validData) {
                if (!item.value || typeof item.value !== 'object') continue;
                for (const [key, val] of Object.entries(item.value)) {
                    if (isNumeric(val) && !validFields.includes(key)) {
                        validFields.push(key);
                    }
                    if (validFields.length >= 10) break;
                }
                if (validFields.length >= 10) break;
            }

            if (!validFields.length) {
                console.warn('No valid fields found in API data');
                return {
                    trendData: [],
                    trendLabels: [],
                    rows: [],
                    headers: periodToggle.value === 'quarterly' ? quarterlyHeaders : yearlyHeaders
                };
            }

            const mappedFields = {};
            validFields.forEach(field => {
                mappedFields[formatFieldLabel(field)] = field;
            });

            const quarterlyRows = [];
            const yearlyRows = [];
            for (const [label, field] of Object.entries(mappedFields)) {
                const quarterlyRow = {
                    label,
                    values: [],
                    chartData: []
                };
                const yearlyRow = {
                    label,
                    values: [],
                    chartData: []
                };

                for (const item of quarterlyData) {
                    const value = calculateValueFromAPI(item, field);
                    quarterlyRow.values.push(value !== null ? number_format(value / 1000000000, 2) : '');
                    quarterlyRow.chartData.push(value !== null ? parseFloat(number_format(value /
                        1000000000, 2)) : 0);
                }

                for (const item of yearlyData) {
                    const value = calculateValueFromAPI(item, field);
                    yearlyRow.values.push(value !== null ? number_format(value / 1000000000, 2) : '');
                    yearlyRow.chartData.push(value !== null ? parseFloat(number_format(value / 1000000000,
                        2)) : 0);
                }

                quarterlyRows.push(quarterlyRow);
                yearlyRows.push(yearlyRow);
            }

            const trendDataQuarterly = quarterlyRows.map(row => row.chartData.slice(-5)); // Lấy 5 gần nhất
            const trendDataYearly = yearlyRows.map(row => row.chartData.slice(-5)); // Lấy 5 gần nhất
            const trendQuarterLabels = quarterlyHeaders.slice(-5); // Lấy 5 gần nhất
            const trendYearLabels = yearlyHeaders.slice(0, 5); // Lấy 5 gần nhất (sau khi sắp xếp giảm dần)

            return {
                trendData: periodToggle.value === 'quarterly' ? trendDataQuarterly : trendDataYearly,
                trendLabels: periodToggle.value === 'quarterly' ? trendQuarterLabels : trendYearLabels,
                rows: periodToggle.value === 'quarterly' ? quarterlyRows : yearlyRows,
                headers: periodToggle.value === 'quarterly' ? quarterlyHeaders : yearlyHeaders
            };
        }

        function calculateValueFromAPI(item, field) {
            return item.value && isNumeric(item.value[field]) ? parseFloat(item.value[field]) : 0;
        }

        function updateTable(period, method, data = null) {
            currentPeriod = period;
            currentMethod = method;

            let trendData, trendLabels, rows, headers;
            if (data) {
                trendData = data.trendData;
                trendLabels = data.trendLabels;
                rows = data.rows;
                headers = data.headers;
            } else {
                trendData = period === 'quarterly' ? trenddataQuarterly : trenddataYearly;
                trendLabels = period === 'quarterly' ? trendQuarterLabels : trendYearLabels;
                rows = period === 'quarterly' ? quarterlyRows : yearlyRows;
                headers = period === 'quarterly' ? quarterlyHeaders : yearlyHeaders;
            }

            if (!rows.length || !headers.length) {
                tableBody.innerHTML =
                    '<tr><td colspan="100" class="text-center py-4">Không có dữ liệu hợp lệ để hiển thị.</td></tr>';
                return;
            }

            // Cập nhật bảng
            table.querySelector('thead tr').innerHTML = `
                <th class="py-4 pr-4 font-medium pl-6 sticky left-0 bg-gray-200 text-sm z-10" style="font-weight: 700;">Bảng lưu chuyển tiền tệ</th>
                <th class="py-4 pr-4 font-medium pl-6 text-sm min-w-[100px]" style="font-weight: 700;">Xu hướng</th>
                ${headers.map(header => `<th class="px-4 py-2 font-medium text-sm text-center header-cell ${period === 'quarterly' ? 'quarterly-header' : 'yearly-header'}" style="min-width: 100px;">${header}</th>`).join('')}
            `;
            tableBody.innerHTML = rows.map((row, index) => `
    <tr class="border-b last:border-none hover:bg-gray-50">
        <td class="py-2 px-3 text-black sticky left-0 bg-white text-sm z-10">${row.label}</td>
        <td class="py-6 px-6 text-center relative" style="min-width: 100px;">
            <canvas class="cashflow-trend-chart-${index}-${uniqueId}" height="80"></canvas>
        </td>
        ${row.values.map(cell => `
            <td class="px-4 py-6 text-center text-xs data-cell ${period === 'quarterly' ? 'quarterly-cell' : 'yearly-cell'} ${cell.toString().startsWith('-') ? 'custom-negative' : ''}" style="display: table-cell;">
                ${cell}
            </td>
        `).join('')}
    </tr>
`).join('');
            // Hiển thị/ẩn các cột header
            document.querySelectorAll('.quarterly-header').forEach(header => {
                header.style.display = period === 'quarterly' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.yearly-header').forEach(header => {
                header.style.display = period === 'yearly' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.quarterly-cell').forEach(cell => {
                cell.style.display = period === 'quarterly' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.yearly-cell').forEach(cell => {
                cell.style.display = period === 'yearly' ? 'table-cell' : 'none';
            });

            periodToggle.value = period;
            methodToggle.value = method;

            if (!trendData || trendData.length === 0) {
                console.warn('No trend data available for', {
                    period,
                    method
                });
                tableBody.innerHTML =
                    '<tr><td colspan="100" class="text-center py-4">Không có dữ liệu để hiển thị.</td></tr>';
                return;
            }

            // Cập nhật biểu đồ
            charts.forEach(chart => chart.destroy());
            charts = [];
            rowLabels = rows.map(row => row.label);
            rows.forEach((row, index) => {
                const canvas = document.querySelector(`.cashflow-trend-chart-${index}-${uniqueId}`);
                if (!canvas) {
                    console.warn(`Canvas not found for index ${index}`);
                    return;
                }
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    console.warn(`Canvas context not available for index ${index}`);
                    return;
                }
                const validData = trendData[index] ? trendData[index].filter(val => val !== null &&
                    !isNaN(val) && val !== 0) : [];
                const validLabels = trendLabels.filter((_, i) => trendData[index] && trendData[
                    index][i] !== null && !isNaN(trendData[index][i]) && trendData[index][
                    i
                ] !== 0);

                if (validData.length > 0) {
                    const isSinglePoint = validData.length === 1;
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: validLabels,
                            datasets: [{
                                data: validData,
                                label: rowLabels[index] || 'Trend',
                                borderColor: '#1e90ff',
                                backgroundColor: 'rgba(30, 144, 255, 0.1)',
                                fill: !isSinglePoint,
                                tension: isSinglePoint ? 0 : 0.3,
                                pointRadius: isSinglePoint ? 5 : 0,
                                pointHoverRadius: isSinglePoint ? 7 : 0,
                                showLine: !isSinglePoint,
                                borderWidth: isSinglePoint ? 0 : 1.5,
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
                                duration: 1000,
                                easing: 'easeOutQuad'
                            }
                        }
                    });
                    charts.push(chart);
                    canvas.addEventListener('click', () => showPopup(index));
                } else {
                    console.warn(
                        `No valid data for chart at index ${index} (Label: ${rowLabels[index]})`, {
                            validData,
                            validLabels
                        });
                    canvas.style.display = 'none';
                    const placeholder = document.createElement('span');
                    placeholder.textContent = 'Không có dữ liệu';
                    placeholder.style.fontSize = '12px';
                    placeholder.style.color = '#999';
                    canvas.parentElement.appendChild(placeholder);
                }
            });

            // Cập nhật popup chart nếu đang mở
            if (popupChart && popup.style.display === 'block') {
                const index = popupChart.index;
                popupChart.data.labels = trendLabels || [];
                popupChart.data.datasets[0].data = trendData[index] || [];
                popupChart.data.datasets[0].pointRadius = trendData[index] && trendData[index].length ===
                    1 ? 5 : 0;
                popupChart.data.datasets[0].showLine = trendData[index] && trendData[index].length === 1 ?
                    false : true;
                popupChart.data.datasets[0].borderWidth = trendData[index] && trendData[index].length ===
                    1 ? 0 : 2;
                popupChart.update();
            }

            updateButtonState();
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

        function showPopup(index) {
            if (!rowLabels[index] || !popupChartCanvas) return;
            popupTitle.textContent = rowLabels[index];
            popup.style.display = 'block';
            if (popupChart) popupChart.destroy();
            fetchCashflowData('<?php echo $symbol; ?>', currentPeriod, currentMethod).then(data => {
                if (!data) return;
                const trendData = data.trendData[index] || [];
                const trendLabels = data.trendLabels || [];

                const sortedIndices = [...Array(trendLabels.length).keys()].sort((a, b) => {
                    const dateA = new Date(trendLabels[a].split('/')[1] || trendLabels[a], (
                            trendLabels[a].split('/')[0]?.replace('Q', '') - 1) * 3 ||
                        0);
                    const dateB = new Date(trendLabels[b].split('/')[1] || trendLabels[b], (
                            trendLabels[b].split('/')[0]?.replace('Q', '') - 1) * 3 ||
                        0);
                    return dateA - dateB;
                });
                const sortedLabels = sortedIndices.map(i => trendLabels[i]);
                const sortedData = sortedIndices.map(i => trendData[i]);

                const ctx = popupChartCanvas.getContext('2d');
                if (!ctx) return;
                const isSinglePoint = sortedData.length === 1;

                // Tính chiều cao động dựa trên số lượng nhãn (label)
                const labelCount = sortedLabels.length;
                const baseHeight = 300; // Chiều cao cơ bản
                const heightPerLabel = 40; // Chiều cao ước lượng cho mỗi nhãn
                const dynamicHeight = Math.min(baseHeight + (labelCount * heightPerLabel), window
                    .innerHeight * 0.9 - 80); // Giới hạn 90vh trừ header

                popupChartCanvas.height = dynamicHeight; // Đặt chiều cao canvas
                popupChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: sortedLabels,
                        datasets: [{
                            data: sortedData,
                            label: rowLabels[index] || 'Trend',
                            borderColor: '#1e90ff',
                            backgroundColor: 'rgba(30, 144, 255, 0.1)',
                            fill: !isSinglePoint,
                            tension: isSinglePoint ? 0 : 0.3,
                            pointRadius: isSinglePoint ? 5 : 0,
                            pointHoverRadius: isSinglePoint ? 7 : 0,
                            showLine: !isSinglePoint,
                            borderWidth: isSinglePoint ? 0 : 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: currentPeriod === 'quarterly' ? 'Quý' : 'Năm'
                                },
                                reverse: false
                            },
                            y: {
                                display: true,
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Tỷ VNĐ'
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
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            const originalValue = context.parsed.y *
                                                1000000000;
                                            label += number_format(originalValue /
                                                1000000000, 2) + ' Tỷ VNĐ';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuad'
                        },
                        events: ['mousemove', 'mouseout', 'click', 'touchstart',
                            'touchmove'
                        ]
                    }
                });
                popupChart.index = index
                // Điều chỉnh kích thước container popup
                const popupContent = popup.querySelector('.chart-popup-content');
                popupContent.style.height = `${dynamicHeight + 60}px`; // Thêm padding và header
            });
        }

        function closePopup() {
            popup.style.display = 'none';
            if (popupChart) {
                popupChart.destroy();
                popupChart = null;
            }
        }

        function initializeTableAndCharts() {
            const canvases = rowLabels.map((_, index) => document.querySelector(
                `.cashflow-trend-chart-${index}-${uniqueId}`));
            if (!canvases.length || (!trenddataQuarterly.length && !trenddataYearly.length)) {
                console.warn('No canvases or trend data available');
                tableBody.innerHTML =
                    '<tr><td colspan="100" class="text-center py-4">Không có dữ liệu để hiển thị.</td></tr>';
                return;
            }
            if (table.offsetWidth > 0 && scrollContainer.offsetWidth > 0) {
                canvases.forEach((canvas, index) => {
                    if (!canvas) {
                        console.warn(`Canvas not found for index ${index}`);
                        return;
                    }
                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        console.warn(`Canvas context not available for index ${index}`);
                        return;
                    }
                    let trendData = trenddataQuarterly[index] || [];
                    let trendLabels = trendQuarterLabels || [];
                    const validData = trendData.filter(val => val !== null && !isNaN(val) && val !==
                        0);
                    const validLabels = trendLabels.filter((_, i) => trendData[i] !== null && !
                        isNaN(trendData[i]) && trendData[i] !== 0);

                    if (validData.length > 0) {
                        const isSinglePoint = validData.length === 1;
                        const chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: validLabels,
                                datasets: [{
                                    data: validData,
                                    label: rowLabels[index] || 'Trend',
                                    borderColor: '#1e90ff',
                                    backgroundColor: 'rgba(30, 144, 255, 0.1)',
                                    fill: !isSinglePoint,
                                    tension: isSinglePoint ? 0 : 0.3,
                                    pointRadius: isSinglePoint ? 5 : 0,
                                    pointHoverRadius: isSinglePoint ? 7 : 0,
                                    showLine: !isSinglePoint,
                                    borderWidth: isSinglePoint ? 0 : 1.5,
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
                                    duration: 1000,
                                    easing: 'easeOutQuad'
                                }
                            }
                        });
                        charts.push(chart);
                        canvas.addEventListener('click', () => showPopup(index));
                    } else {
                        console.warn(
                            `No valid data for initial chart at index ${index} (Label: ${rowLabels[index]})`, {
                                validData,
                                validLabels
                            });
                        canvas.style.display = 'none';
                        const placeholder = document.createElement('span');
                        placeholder.textContent = 'Không có dữ liệu';
                        placeholder.style.fontSize = '12px';
                        placeholder.style.color = '#999';
                        canvas.parentElement.appendChild(placeholder);
                    }
                });
                prevButton.disabled = true;
                nextButton.disabled = table.offsetWidth <= scrollContainer.offsetWidth;
                updateButtonState();
                updateTable(currentPeriod, currentMethod);
            } else {
                setTimeout(initializeTableAndCharts, 100);
            }
        }

        document.addEventListener('companyCodeChanged', async (e) => {
            const newSymbol = e.detail.code;
            const period = periodToggle.value;
            const method = methodToggle.value;
            const data = await fetchCashflowData(newSymbol, period, method);
            if (data) {
                trenddataQuarterly = period === 'quarterly' ? data.trendData :
                    trenddataQuarterly;
                trenddataYearly = period === 'yearly' ? data.trendData : trenddataYearly;
                trendQuarterLabels = period === 'quarterly' ? data.trendLabels :
                    trendQuarterLabels;
                trendYearLabels = period === 'yearly' ? data.trendLabels : trendYearLabels;
                quarterlyRows = period === 'quarterly' ? data.rows : quarterlyRows;
                yearlyRows = period === 'yearly' ? data.rows : yearlyRows;
                quarterlyHeaders = period === 'quarterly' ? data.headers : quarterlyHeaders;
                yearlyHeaders = period === 'yearly' ? data.headers : yearlyHeaders;
                updateTable(period, method, data);
            }
        });

        periodToggle.addEventListener('change', async function() {
            const period = this.value;
            const method = methodToggle.value;
            const data = await fetchCashflowData('<?php echo $symbol; ?>', period, method);
            if (data) {
                trenddataQuarterly = data.trendData;
                trenddataYearly = data.trendData;
                trendQuarterLabels = data.trendLabels;
                trendYearLabels = data.trendLabels;
                quarterlyRows = data.rows;
                yearlyRows = data.rows;
                quarterlyHeaders = data.headers;
                yearlyHeaders = data.headers;
                updateTable(period, method, data);
            }
        });

        methodToggle.addEventListener('change', async function() {
            const period = periodToggle.value;
            const method = this.value;
            const data = await fetchCashflowData('<?php echo $symbol; ?>', period, method);
            if (data) {
                trenddataQuarterly = period === 'quarterly' ? data.trendData :
                    trenddataQuarterly;
                trenddataYearly = period === 'yearly' ? data.trendData : trenddataYearly;
                trendQuarterLabels = period === 'quarterly' ? data.trendLabels :
                    trendQuarterLabels;
                trendYearLabels = period === 'yearly' ? data.trendLabels : trendYearLabels;
                quarterlyRows = period === 'quarterly' ? data.rows : quarterlyRows;
                yearlyRows = period === 'yearly' ? data.rows : yearlyRows;
                quarterlyHeaders = period === 'quarterly' ? data.headers : quarterlyHeaders;
                yearlyHeaders = period === 'yearly' ? data.headers : yearlyHeaders;
                updateTable(period, method, data);
            }
        });

        scrollContainer.addEventListener('scroll', updateButtonState);
        nextButton.addEventListener('click', () => scrollOneColumn(1));
        prevButton.addEventListener('click', () => scrollOneColumn(-1));
        closePopupButton.addEventListener('click', closePopup);
        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                closePopup();
            }
        });

        initializeTableAndCharts();
    });

    function number_format(number, decimals, dec_point = '.', thousands_sep = ',') {
        const rounded = Number(Math.round(number + 'e' + decimals) + 'e-' + decimals).toFixed(decimals);
        const parts = rounded.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
        return parts.join(dec_point);
    }

    function isNumeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }

    function formatFieldLabel(field) {
        return field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
})();
</script>

<!-- CSS cho giao diện -->
<style>
.income-statement- {
        {
        $uniqueId
    }
}

    {
    max-width: 100%;
    overflow-x: auto;
}

.income-statement- {
        {
        $uniqueId
    }
}

select {
    background-color: #f5f5f5;
    color: #333;
    border-radius: 5px;
    padding: 6px 12px;
}

.income-statement- {
        {
        $uniqueId
    }
}

select:focus {
    background-color: #fff;
    color: #000;
    border-color: #1e90ff;
}

#balanceTable- {
        {
        $uniqueId
    }
}

td:nth-child(2) {
    padding: 10px;
    height: 80px;
    position: relative;
    min-width: 100px;
}

#balanceTable- {
        {
        $uniqueId
    }
}

td:first-child {
    min-width: 150px;
    font-size: 13px;
    padding-left: 8px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.cashflow-trend-chart- {
        {
        $uniqueId
    }
}

    {
    width: 100px;
    height: 50px;
    display: block;
    cursor: pointer;
}

#scrollContainer- {
        {
        $uniqueId
    }
}

    {
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
}

#balanceTable- {
        {
        $uniqueId
    }
}

th.header-cell,
#balanceTable- {
        {
        $uniqueId
    }
}

td.data-cell {
    min-width: 100px;
    white-space: nowrap;
}

#balanceTable- {
        {
        $uniqueId
    }
}

th,
#balanceTable- {
        {
        $uniqueId
    }
}

td {
    padding: 8px;
}

.income-statement- {
        {
        $uniqueId
    }
}

button:disabled {
    background-color: rgb(240, 240, 240);
    cursor: not-allowed;
    opacity: 0.6;
    color: #767676;
    border: 1px solid #767676;
}

.custom-negative {
    color: #B51001 !important;
}

.is-chart-popup {
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

.is-chart-popup .chart-popup-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 1050px;
    max-width: 90%;
    /* Đảm bảo responsive trên màn hình nhỏ */
    min-height: 300px;
    /* Chiều cao tối thiểu */
    max-height: 90vh;
    /* Giới hạn chiều cao tối đa là 90% viewport height */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    overflow-y: auto;
    box-sizing: border-box;
    display: block;
}

.is-chart-popup .chart-popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.is-chart-popup .chart-popup-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.is-chart-popup .chart-popup-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

.is-chart-popup .chart-popup-close:hover {
    color: #000;
}

#popupChart- {
        {
        $uniqueId
    }
}

    {
    display: block;
    box-sizing: border-box;
    width: 100%;
    /* Sử dụng 100% chiều rộng của container */
    height: auto !important;
    /* Chiều cao tự động dựa trên nội dung */
    max-height: calc(90vh - 80px);
    /* Trừ đi padding và header */
    pointer-events: auto !important;
}

[class^="cashflow-trend-chart-"] {
    width: 120px !important;
    max-width: 120px !important;
}
</style>