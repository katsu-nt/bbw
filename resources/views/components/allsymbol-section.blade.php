<section>
    <!-- Breadcrumb -->
    <div class="flex items-center gap-[8px] text-sm font-medium">
        <span class="text-black font-bold text-[18px] leading-[26px]">Dữ liệu</span>
        <span class="mx-2">|</span>
        <span class="text-black font-bold text-[18px] leading-[26px]">Doanh nghiệp</span>
    </div>
    <!-- Header -->
    <div class="flex items-center justify-between py-4">
        <!-- Show symbol (using $getSymbols) -->
        <div class="flex items-center gap-x-5">
            <span class="company-name font-medium text-[24px] leading-[32px]">
                {{ $getSymbols['organName'] ?? 'Unknown Company' }}
            </span>
            <span class="company-code font-mono text-[24px] leading-[32px]">
                {{ $getSymbols['symbol'] ?? 'N/A' }}
            </span>
            <span class="text-[24px] leading-[32px]">
                {{ number_format($getSymbols['close'] ?? 489.29, 2) }}
            </span>
            <span
                class="text-{{ $getSymbols['change_pct'] < 0 ? 'red-600' : 'green-600' }} font-semibold text-[24px] leading-[32px]">
                {{ $getSymbols['change_pct'] ? ($getSymbols['change_pct'] > 0 ? '▲' : '▼') . number_format(abs($getSymbols['change_pct']), 2) . '%' : '▼0.33%' }}
            </span>
        </div>

        <!-- Search Symbol (using $allSymbols) -->
        <div class="relative">
            <div class="flex items-center border rounded px-2 py-1 bg-gray-50">
                <svg width="18" height="18" fill="none" class="mr-2 text-gray-400" viewBox="0 0 17 17">
                    <path
                        d="M16 15.5L12.375 11.875M14.3333 7.16667C14.3333 10.8486 11.3486 13.8333 7.66667 13.8333C3.98477 13.8333 1 10.8486 1 7.16667C1 3.48477 3.98477 0.5 7.66667 0.5C11.3486 0.5 14.3333 3.48477 14.3333 7.16667Z"
                        stroke="#888" stroke-linecap="square" stroke-linejoin="round" />
                </svg>
                <input type="text" class="company-input bg-transparent outline-none text-gray-400 w-32"
                    placeholder="Nhập mã công ty" id="companyInput">
            </div>
            <!-- Dropdown for suggestions -->
            <div id="symbolDropdown"
                class="absolute z-10 w-full bg-white border rounded mt-1 max-h-40 overflow-y-auto hidden">
                @if (!empty($allSymbols))
                @foreach (array_slice($allSymbols, 0, 8) as $sym)
                <div class="px-2 py-1 hover:bg-gray-100 cursor-pointer symbol-option" data-code="{{ $sym }}">{{ $sym }}
                </div>
                @endforeach
                @else
                <div class="px-2 py-1 text-gray-500">Không có dữ liệu</div>
                @endif
            </div>
        </div>
    </div>
    <p class="text-stone-400" id="timestampDisplay"></p> <!-- Thêm id cho thẻ p -->
</section>

<style>
#symbolDropdown {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: none;
}

#symbolDropdown.show {
    display: block;
}

.symbol-option {
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#companyInput');
    const dropdown = document.querySelector('#symbolDropdown');
    const companyName = document.querySelector('.company-name');
    const companyCode = document.querySelector('.company-code');
    const timestampDisplay = document.querySelector('#timestampDisplay'); // Thêm tham chiếu đến thẻ p
    let allSymbols = @json($allSymbols);
    let getSymbols = @json($getSymbols);
    let selectedSymbol = '';

    // Normalize getSymbols
    if (getSymbols && !Array.isArray(getSymbols) && typeof getSymbols === 'object') {
        getSymbols = {
            symbol: getSymbols['symbol'] || 'N/A',
            organName: getSymbols['organName'] || 'Unknown Company',
            close: getSymbols['close'] || 489.29,
            change_pct: getSymbols['change_pct'] || -0.33,
            timestamp: getSymbols['timestamp'] || 'N/A' // Thêm timestamp
        };
    } else {
        getSymbols = {
            symbol: 'N/A',
            organName: 'Unknown Company',
            close: 489.29,
            change_pct: -0.33,
            timestamp: 'N/A'
        };
    }

    // Hàm định dạng ngày
    function formatTimestamp(timestamp) {
        if (!timestamp || timestamp === 'N/A') {
            return 'Ngày không xác định';
        }
        try {
            const date = new Date(timestamp);
            if (isNaN(date)) {
                return 'Ngày không hợp lệ';
            }
            const day = date.getUTCDate().toString().padStart(2, '0');
            const month = (date.getUTCMonth() + 1).toString().padStart(2, '0');
            const year = date.getUTCFullYear();
            return `Ngày ${day}/${month}/${year}`;
        } catch (e) {
            console.error('Lỗi khi định dạng timestamp:', e);
            return 'Ngày không hợp lệ';
        }
    }

    // Update company display
    const updateCompany = (code) => {
        const company = code && allSymbols.includes(code.toUpperCase()) ? {
            code: code.toUpperCase(),
            name: getSymbols.organName, // Dùng organName từ getSymbols hoặc API
            timestamp: getSymbols.timestamp // Dùng timestamp từ getSymbols
        } : {
            code: getSymbols.symbol,
            name: getSymbols.organName,
            timestamp: getSymbols.timestamp
        };
        companyName.textContent = company.name;
        companyCode.textContent = company.code;
        timestampDisplay.textContent = formatTimestamp(company.timestamp); // Cập nhật timestamp

        const event = new CustomEvent('companyCodeChanged', {
            detail: {
                code: company.code
            }
        });
        document.dispatchEvent(event);
    };

    // Initialize with symbol from query string
    const urlParams = new URLSearchParams(window.location.search);
    const initialSymbol = urlParams.get('symbol');
    updateCompany(initialSymbol);
    input.value = ''; // Clear input to show placeholder

    // Show dropdown on input focus
    input.addEventListener('focus', () => {
        updateDropdown(allSymbols.slice(0, 8));
        dropdown.classList.add('show');
        dropdown.classList.remove('hidden');
    });

    // Filter dropdown on input
    input.addEventListener('input', () => {
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

    // Hide dropdown on click outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');
        }
    });

    // Handle dropdown selection and trigger page reload
    dropdown.addEventListener('click', (e) => {
        if (e.target.classList.contains('symbol-option')) {
            const code = e.target.getAttribute('data-code');
            input.value = code;
            selectedSymbol = code;
            dropdown.classList.remove('show');
            dropdown.classList.add('hidden');

            if (allSymbols.includes(code)) {
                console.log('Reloading with symbol:', code);
                window.location.href = `${window.location.pathname}?symbol=${encodeURIComponent(code)}`;
            } else {
                alert('Mã công ty không hợp lệ. Vui lòng chọn từ danh sách.');
                input.value = '';
                selectedSymbol = '';
            }
        }
    });

    // Reload page on Enter key
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const code = selectedSymbol || input.value.trim().toUpperCase();
            if (allSymbols.includes(code)) {
                console.log('Reloading with symbol:', code);
                window.location.href = `${window.location.pathname}?symbol=${encodeURIComponent(code)}`;
            } else {
                alert('Mã công ty không hợp lệ. Vui lòng chọn từ danh sách.');
                input.value = '';
                selectedSymbol = '';
            }
        }
    });

    // Update dropdown content
    function updateDropdown(symbols) {
        dropdown.innerHTML = '';
        if (symbols.length > 0) {
            symbols.forEach(sym => {
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
});
</script>