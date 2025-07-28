@php
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

$uniqueId = Str::random(8);

$dataQuarterly = $balanceSheetQuarterlyFundamental['data'] ?? array();
$dataYearly = $balanceSheetYearlyFundamental['data'] ?? array();

$quarterlyHeaders = array();
$quarterlyHeadersWithDates = array();
$quarterlySelectedKeys = array();
$quarterlyRows = array();

$quarterlyHeadersWithDates = collect($dataQuarterly)->mapWithKeys(function ($item) {
if (strpos($item['key'], '_') !== false) {
list($year, $quarter) = explode('_', $item['key']);
$quarter = (int)$quarter;
if ($quarter >= 1 && $quarter <= 5 && preg_match('/^\d{4}$/', $year)) { try { $quarterStart=new DateTime("{$year}-" .
    (($quarter - 1) * 3 + 1) . "-01" ); return array($item['key']=> $quarterStart);
    } catch (Exception $e) {
    \Log::error("DateTime error for quarterly key {$item['key']}: {$e->getMessage()}");
    return array($item['key'] => now());
    }
    }
    }
    \Log::warning("Invalid quarterly key for DateTime: {$item['key']}");
    return array($item['key'] => now());
    })->sortKeys()->all();

    $currentDate = now();
    $desiredQuarters = ['2004_4', '2005_4', '2006_4', '2007_1', '2007_2', '2007_3', '2007_4', '2008_1', '2008_2'];
    $quarterlySelectedKeys = array_keys($quarterlyHeadersWithDates);
    $quarterlySelectedKeys = array_unique(array_merge(
    array_intersect($quarterlySelectedKeys, $desiredQuarters),
    array_filter($quarterlySelectedKeys, function ($key) use ($quarterlyHeadersWithDates, $currentDate) {
    return $quarterlyHeadersWithDates[$key] <= $currentDate; }) )); usort($quarterlySelectedKeys, function ($a, $b) {
        list($yearA, $quarterA)=explode('_', $a); list($yearB, $quarterB)=explode('_', $b); $yearA=(int)$yearA;
        $yearB=(int)$yearB; $quarterA=(int)$quarterA; $quarterB=(int)$quarterB; if ($yearA===$yearB) { return $quarterA
        <=> $quarterB;
        }
        return $yearA <=> $yearB;
            });

            $quarterlyHeaders = array_map(function ($key) {
            if (strpos($key, '_') !== false) {
            list($year, $quarter) = explode('_', $key);
            if ($quarter >= 1 && $quarter <= 5 && preg_match('/^\d{4}$/', $year)) { return 'Q' . $quarter . '/' . $year;
                } } return $key; }, $quarterlySelectedKeys); $yearlyHeaders=array(); $yearlyHeadersWithDates=array();
                $yearlySelectedKeys=array(); $yearlyRows=array(); $aggregatedYearly=array(); foreach ($dataYearly as
                $item) { if (strpos($item['key'], '_' ) !==false) { list($year, $quarter)=explode('_', $item['key']);
                $quarter=(int)$quarter; if (preg_match('/^\d{4}$/', $year)) { $yearKey=$year; if
                (!isset($aggregatedYearly[$yearKey]) || (int)explode('_', $aggregatedYearly[$yearKey]['key'])[1] <
                $quarter) { $aggregatedYearly[$yearKey]=array('key'=> $item['key'], 'value' => $item['value']);
                }
                } else {
                \Log::warning("Invalid yearly key (quarterly format): {$item['key']}");
                $aggregatedYearly[$item['key']] = $item;
                }
                } else if (preg_match('/^\d{4}$/', $item['key'])) {
                $aggregatedYearly[$item['key']] = $item;
                } else {
                \Log::warning("Invalid yearly key: {$item['key']}");
                }
                }

                if (empty($aggregatedYearly)) {
                $latestQuarterly = collect($dataQuarterly)->filter(function ($item) use ($currentDate) {
                if (strpos($item['key'], '_') !== false) {
                list($year, $quarter) = explode('_', $item['key']);
                $quarter = (int)$quarter;
                if ($quarter >= 1 && $quarter <= 5 && preg_match('/^\d{4}$/', $year)) { try { $date=new
                    DateTime("{$year}-" . (($quarter - 1) * 3 + 1) . "-01" ); return $date <=$currentDate; } catch
                    (Exception $e) { \Log::error("DateTime error for quarterly fallback key {$item['key']}: {$e->
                    getMessage()}");
                    }
                    }
                    }
                    return false;
                    })->groupBy(function ($item) {
                    return explode('_', $item['key'])[0];
                    })->map(function ($group) {
                    return $group->sortByDesc(function ($item) {
                    return (int)explode('_', $item['key'])[1];
                    })->first();
                    })->toArray();
                    $aggregatedYearly = array_map(function ($item) {
                    return array('key' => $item['key'], 'value' => $item['value']);
                    }, $latestQuarterly);
                    }

                    $yearlyHeadersWithDates = collect($aggregatedYearly)->mapWithKeys(function ($item) {
                    if (strpos($item['key'], '_') !== false) {
                    list($year, $quarter) = explode('_', $item['key']);
                    if (preg_match('/^\d{4}$/', $year)) {
                    try {
                    $quarterStart = new DateTime("{$year}-" . ((int)$quarter - 1) * 3 + 1 . "-01");
                    return array($item['key'] => $quarterStart);
                    } catch (Exception $e) {
                    \Log::error("DateTime error for yearly key {$item['key']}: {$e->getMessage()}");
                    return array($item['key'] => now());
                    }
                    }
                    }
                    if (preg_match('/^\d{4}$/', $item['key'])) {
                    try {
                    return array($item['key'] => new DateTime("{$item['key']}-01-01"));
                    } catch (Exception $e) {
                    \Log::error("DateTime error for yearly key {$item['key']}: {$e->getMessage()}");
                    return array($item['key'] => now());
                    }
                    }
                    \Log::warning("Invalid yearly key for DateTime: {$item['key']}");
                    return array($item['key'] => now());
                    })->sortKeys()->all();

                    $yearlySelectedKeys = array_keys($yearlyHeadersWithDates);

                    $yearlyHeaders = array_map(function ($key) {
                    if (strpos($key, '_') !== false) {
                    list($year, $quarter) = explode('_', $key);
                    return 'Q' . $quarter . '/' . $year;
                    }
                    return $key;
                    }, $yearlySelectedKeys);

                    $mappedFields = array(
                    'Tài sản ngắn hạn' => 'current_assets_and_short_term_investments',
                    'Tiền và các khoản tương đương tiền' => 'cash_and_cash_equivalents',
                    'Đầu tư tài chính ngắn hạn' => 'short_term_financial_investments',
                    'Các khoản phải thu ngắn hạn' => 'short_term_receivables',
                    'Hàng tồn kho, ròng' => 'total_inventories',
                    'Tài sản ngắn hạn khác' => 'other_current_assets',
                    'Tổng tài sản ngắn hạn' => 'current_assets_and_short_term_investments',
                    'Tài sản dài hạn' => 'fixed_assets_and_long_term_investments',
                    'Tài sản tài chính dài hạn' => 'long_term_financial_investments',
                    'Các khoản phải thu dài hạn' => 'long_term_receivables',
                    'Tài sản dài hạn khác' => 'total_other_long_term_assets',
                    'Lợi thế thương mại' => 'goodwill',
                    'Tài sản cố định' => 'fixed_assets',
                    'Bất động sản đầu tư' => 'investment_property',
                    'Tổng tài sản' => 'total_assets'
                    );

                    $calculateValue = function ($item, $field) {
                    $value = $item['value'][$field] ?? null;
                    if ($field === 'cash_and_cash_equivalents') {
                    $value = (float)($item['value']['cash'] ?? 0) + (float)($item['value']['cash_equivalents'] ?? 0);
                    } elseif ($field === 'short_term_receivables') {
                    $value = (float)($item['value']['trade_receivables_short_term'] ?? 0) +
                    (float)($item['value']['other_short_term_receivables'] ?? 0);
                    } elseif ($field === 'total_inventories') {
                    $value = (float)($item['value']['inventories'] ?? 0) +
                    (float)($item['value']['allowance_for_inventory_devaluation'] ?? 0);
                    } elseif ($field === 'fixed_assets_and_long_term_investments') {
                    $value = (float)($item['value']['property_plant_and_equipment'] ?? 0) +
                    (float)($item['value']['long_term_construction_in_progress'] ?? 0) +
                    (float)($item['value']['intangible_assets'] ?? 0) +
                    (float)($item['value']['other_long_term_assets_item'] ?? 0);
                    } elseif ($field === 'short_term_financial_investments') {
                    $value = (float)($item['value']['held_to_maturity_investments_short_term'] ?? 0);
                    } elseif ($field === 'long_term_financial_investments') {
                    $value = (float)($item['value']['held_to_maturity_investments_long_term'] ?? 0);
                    } elseif ($field === 'long_term_receivables') {
                    $value = (float)($item['value']['other_long_term_receivables'] ?? 0);
                    } elseif ($field === 'other_current_assets') {
                    $value = (float)($item['value']['vat_deductible'] ?? 0);
                    } elseif ($field === 'total_other_long_term_assets') {
                    $value = (float)($item['value']['long_term_prepayments'] ?? 0) +
                    (float)($item['value']['other_long_term_assets_item'] ?? 0);
                    }
                    return $value;
                    };

                    foreach ($mappedFields as $label => $field) {
                    $quarterlyRow = array($label);
                    foreach ($quarterlySelectedKeys as $key) {
                    $item = collect($dataQuarterly)->firstWhere('key', $key);
                    $value = $item ? $calculateValue($item, $field) : null;
                    if (is_null($value)) {
                    $quarterlyRow[] = '';
                    } elseif (is_numeric($value)) {
                    $quarterlyRow[] = number_format($value / 1_000_000_000, 2);
                    } else {
                    $quarterlyRow[] = (string) $value;
                    }
                    }
                    $quarterlyRows[] = array('label' => $label, 'values' => array_slice($quarterlyRow, 1));

                    $yearlyRow = array($label);
                    foreach ($yearlySelectedKeys as $key) {
                    $item = collect($aggregatedYearly)->firstWhere('key', $key);
                    $value = $item ? $calculateValue($item, $field) : null;
                    if (is_null($value)) {
                    $yearlyRow[] = '';
                    } elseif (is_numeric($value)) {
                    $yearlyRow[] = number_format($value / 1_000_000_000, 2);
                    } else {
                    $yearlyRow[] = (string) $value;
                    }
                    }
                    $yearlyRows[] = array('label' => $label, 'values' => array_slice($yearlyRow, 1));
                    }

                    $quarterlyStartIndex = 0;
                    $yearlyStartIndex = max(0, count($yearlySelectedKeys) - 9);
                    $quarterlyTotal = count($quarterlySelectedKeys);
                    $yearlyTotal = count($yearlySelectedKeys);

                    @endphp

                    <div class="bg-white rounded-xl border mt-6 balance-sheet-{{ $uniqueId }}">
                        <div class="flex items-center gap-4 mb-4 p-6">
                            <span class="font-semibold text-base">Báo cáo</span>
                            <select
                                class="bg-white border border-[#e7e7e7] text-[#b4b4b4] rounded-[5px] px-[10px] py-[5px] cursor-pointer"
                                id="periodToggle-{{ $uniqueId }}">
                                <option value="quarterly" selected>Theo Quý</option>
                                <option value="yearly">Theo Năm</option>
                            </select>
                            <div class="ml-auto flex items-center gap-2">
                                <button id="prevBtn-{{ $uniqueId }}"
                                    class="px-2 py-1 rounded-full border-black border hover:bg-gray-100">previous←</button>
                                <button id="nextBtn-{{ $uniqueId }}"
                                    class="px-2 py-1 rounded-full border-black border hover:bg-gray-100">next→</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left" id="balanceTable-{{ $uniqueId }}">
                                <thead>
                                    <tr class="text-gray-500 border-b bg-gray-200">
                                        <th class="py-4 pr-4 font-medium pl-6" style="font-weight: 700;">Bảng cân đối kế
                                            toán</th>
                                        @foreach ($quarterlyHeaders as $index => $header)
                                        <th class="px-4 py-2 font-medium text-center quarterly-header"
                                            style="{{ $index < $quarterlyStartIndex || $index >= $quarterlyStartIndex + 9 ? 'display: none;' : '' }}">
                                            {{ $header }}</th>
                                        @endforeach
                                        @foreach ($yearlyHeaders as $index => $header)
                                        <th class="px-4 py-2 font-medium text-center yearly-header"
                                            style="{{ $index < $yearlyStartIndex || $index >= $yearlyStartIndex + 9 ? 'display: none;' : '' }}">
                                            {{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quarterlyRows as $index => $row)
                                    @php
                                    $isBold = in_array($row['label'], array('Tài sản ngắn hạn', 'Tài sản dài hạn', 'Tổng
                                    tài sản'));
                                    $fontWeight = $isBold ? 'font-weight: 700;' : 'font-weight: 400;';
                                    @endphp
                                    <tr class="border-b last:border-none hover:bg-gray-50">
                                        <td class="py-6 px-6 text-black flex items-center gap-2"
                                            style="{{ $fontWeight }}">{{ $row['label'] }}</td>
                                        @foreach ($row['values'] as $cellIndex => $cell)
                                        <td class="px-6 py-6 text-center quarterly-cell {{ str_contains($cell, '-') ? 'text-red-500' : '' }} {{ $isBold ? 'font-bold text-black' : '' }}"
                                            style="{{ $cellIndex < $quarterlyStartIndex || $cellIndex >= $quarterlyStartIndex + 9 ? 'display: none;' : '' }}">
                                            {{ $cell }}</td>
                                        @endforeach
                                        @foreach ($yearlyRows[$index]['values'] as $cellIndex => $cell)
                                        <td class="px-6 py-6 text-center yearly-cell {{ str_contains($cell, '-') ? 'text-red-500' : '' }} {{ $isBold ? 'font-bold text-black' : '' }}"
                                            style="{{ $cellIndex < $yearlyStartIndex || $cellIndex >= $yearlyStartIndex + 9 ? 'display: none;' : '' }}">
                                            {{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const uniqueId = '{{ $uniqueId }}';
                        const table = document.getElementById(`balanceTable-${uniqueId}`);
                        if (!table) {
                            console.error(`Table element with ID "balanceTable-${uniqueId}" not found`);
                            return;
                        }

                        let currentType = 'quarterly';
                        let quarterlyStartIndex = {
                            {
                                $quarterlyStartIndex
                            }
                        };
                        let yearlyStartIndex = {
                            {
                                $yearlyStartIndex
                            }
                        };
                        const quarterlyTotal = {
                            {
                                $quarterlyTotal
                            }
                        };
                        const yearlyTotal = {
                            {
                                $yearlyTotal
                            }
                        };

                        function updateTable(type, startIndex) {
                            const quarterlyHeaders = table.querySelectorAll('.quarterly-header');
                            const yearlyHeaders = table.querySelectorAll('.yearly-header');
                            const quarterlyCells = table.querySelectorAll('.quarterly-cell');
                            const yearlyCells = table.querySelectorAll('.yearly-cell');

                            quarterlyHeaders.forEach((header, index) => {
                                header.style.display = type === 'quarterly' && index >= startIndex &&
                                    index < startIndex + 9 ? '' : 'none';
                            });
                            yearlyHeaders.forEach((header, index) => {
                                header.style.display = type === 'yearly' && index >= startIndex &&
                                    index < startIndex + 9 ? '' : 'none';
                            });
                            quarterlyCells.forEach((cell, index) => {
                                const cellIndex = Math.floor(index / {
                                    {
                                        count($quarterlyRows)
                                    }
                                });
                                cell.style.display = type === 'quarterly' && cellIndex >= startIndex &&
                                    cellIndex < startIndex + 9 ? '' : 'none';
                            });
                            yearlyCells.forEach((cell, index) => {
                                const cellIndex = Math.floor(index / {
                                    {
                                        count($yearlyRows)
                                    }
                                });
                                cell.style.display = type === 'yearly' && cellIndex >= startIndex &&
                                    cellIndex < startIndex + 9 ? '' : 'none';
                            });

                            const select = document.getElementById(`periodToggle-${uniqueId}`);
                            if (select) {
                                select.value = type;
                            }
                        }

                        const periodToggle = document.getElementById(`periodToggle-${uniqueId}`);
                        if (periodToggle) {
                            periodToggle.addEventListener('change', function() {
                                currentType = this.value;
                                updateTable(currentType, currentType === 'quarterly' ?
                                    quarterlyStartIndex : yearlyStartIndex);
                            });
                        }

                        const prevBtn = document.getElementById(`prevBtn-${uniqueId}`);
                        const nextBtn = document.getElementById(`nextBtn-${uniqueId}`);

                        if (prevBtn) {
                            prevBtn.addEventListener('click', function() {
                                if (currentType === 'quarterly' && quarterlyStartIndex > 0) {
                                    quarterlyStartIndex--;
                                    updateTable('quarterly', quarterlyStartIndex);
                                } else if (currentType === 'yearly' && yearlyStartIndex > 0) {
                                    yearlyStartIndex--;
                                    updateTable('yearly', yearlyStartIndex);
                                }
                            });
                        }

                        if (nextBtn) {
                            nextBtn.addEventListener('click', function() {
                                if (currentType === 'quarterly' && quarterlyStartIndex <
                                    quarterlyTotal - 9) {
                                    quarterlyStartIndex++;
                                    updateTable('quarterly', quarterlyStartIndex);
                                } else if (currentType === 'yearly' && yearlyStartIndex < yearlyTotal -
                                    9) {
                                    yearlyStartIndex++;
                                    updateTable('yearly', yearlyStartIndex);
                                }
                            });
                        }

                        updateTable('quarterly', quarterlyStartIndex);
                    });
                    </script>

                    <style>
                    .balance-sheet- {
                            {
                            $uniqueId
                        }
                    }

                    select {
                        background-color: #f5f5f5;
                        color: #4b5563;
                    }

                    .balance-sheet- {
                            {
                            $uniqueId
                        }
                    }

                    select:focus {
                        background-color: white;
                        color: black;
                    }
                    </style>