@extends('layouts.app')
@section('content')
<!--Content page part-->

<main class="flex justify-center">
    <section class="container-content border-x border-gray-400 px-8 py-10">
        <x-allsymbol-section :allSymbols="$allSymbols" :getSymbols="$getSymbols" />
        <div id="tab-container-js">
            <ul class="flex gap-6 border-b mt-4 overflow-x-auto">
                <li class="tab-btn pb-2 text-[16px] leading-[24px] border-b-2 border-black font-semibold text-black cursor-pointer"
                    data-tab="0">Tóm tắt</li>
                <li class="tab-btn pb-2 text-[16px] leading-[24px] text-gray-500 cursor-pointer	hover:text-black"
                    data-tab="1">Cơ
                    cấu</li>
                <li class="tab-btn pb-2 text-[16px] leading-[24px] text-gray-500 cursor-pointer	hover:text-black"
                    data-tab="2">Chỉ
                    số tài chính</li>
                <li class="tab-btn pb-2 text-[16px] leading-[24px] text-gray-500 cursor-pointer	hover:text-black"
                    data-tab="3">Cân
                    đối kế toán</li>
                <li class="tab-btn pb-2 text-[16px] leading-[24px] text-gray-500 cursor-pointer	hover:text-black"
                    data-tab="4">Kết
                    quả kinh doanh</li>
                <li class="tab-btn pb-2 text-[16px] leading-[24px] text-gray-500 cursor-pointer	hover:text-black"
                    data-tab="5">Lưu
                    chuyển tiền tệ</li>
            </ul>
            <div class="mt-6">
                <!-- SUMMARY SECTION -->
                <div class="tab-content active">
                    <x-summary-section :symbol="$symbol" :allSymbols="$allSymbols"
                        :latestFinancialMetric="$latestFinancialMetric" :latestOhlcv="$latestOhlcv"
                        :normalizedPerformance="$normalizedPerformance" />
                </div>
                <!-- STRUCTURE SECTION -->
                <div class="tab-content">
                    <x-income-statement :incomeStatementQuarterlyStructure="$incomeStatementQuarterlyStructure"
                        :incomeStatementYearlyStructure="$incomeStatementYearlyStructure" />
                    <x-balance-sheet :balanceYearlyStructure="$balanceYearlyStructure"
                        :balanceQuarterlyStructure="$balanceQuarterlyStructure" />
                    <x-cash-flow :cashflowYearlyStructure="$cashflowYearlyStructure"
                        :cashflowQuarterlyStructure="$cashflowQuarterlyStructure" />
                </div>
                <div class="tab-content">
                    <x-ratio-section :symbol="$symbol" :ratioFundamental="$ratioFundamental" />
                </div>
                <div class="tab-content">
                    <x-balancesheet-section :symbol="$symbol" :balanceSheetFundamental="$balanceSheetFundamental" />
                </div>
                <div class="tab-content">
                    <x-incomestatement-section :symbol="$symbol"
                        :incomeStatementFundamental="$incomeStatementFundamental" />
                </div>
                <div class="tab-content">
                    <x-cashflow-section :symbol="$symbol" :cashflowFundamental="$cashflowFundamental" />
                </div>
            </div>
        </div>

    </section>
</main>

<style>
#tab-container-js .tab-content {
    display: none;
}

#tab-container-js .tab-content.active {
    display: block;
}
</style>
<script>
//Chuyển tab Section
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('#tab-container-js .tab-btn');
    const tabContents = document.querySelectorAll('#tab-container-js .tab-content');
    tabBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            tabContents.forEach(tc => tc.classList.remove('active'));
            tabBtns.forEach(b => b.classList.remove('border-black', 'font-semibold',
                'text-black', 'border-b-2'));
            tabBtns.forEach(b => b.classList.add('text-gray-500'));
            tabContents[idx].classList.add('active');
            btn.classList.add('border-black', 'font-semibold', 'text-black', 'border-b-2');
            btn.classList.remove('text-gray-500');
        });
    });
    // Hiển thị tab đầu tiên khi load trang
    if (tabContents.length) tabContents[0].classList.add('active');

});

// Search company
document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#companyInput');
    const urlParams = new URLSearchParams(window.location.search);
    const symbol = urlParams.get('symbol');

    if (input) {
        input.value = symbol;

        // Lắng nghe Enter → chuyển URL
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const code = input.value.trim().toUpperCase();
                if (code) {
                    window.location.href =
                        `${window.location.pathname}?symbol=${encodeURIComponent(code)}`;
                }
            }
        });
    } else {
        console.warn('Không tìm thấy input có id #companyInput');
    }
});
</script>

<style>
.company-input {
    font-size: 14px;
    padding: 2px 4px;
}

.company-input::placeholder {
    color: #9ca3af;
}
</style>


@endsection