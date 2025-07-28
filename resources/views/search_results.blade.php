@extends('layouts.app')
@section('content')

<?php
function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
    if ($date) {
        return $date->format('d') . ' tháng ' . $date->format('n') . ', ' . $date->format('Y') . ' lúc ' . $date->format('g:i A');
    }
    return "Invalid date";
}
?>

<main class="flex justify-center">
    <section class="container-content">
        <section class="lg:grid grid-cols-4">
            <!-- First Column -->
            <div class="col-span-1 p-7.5 border border-y-0 border-r-0 md:border-r lg:border-r-0 border-gray-400 flex flex-col gap-y-6">
                <p class="text-lg font-bold pb-2 border-b border-gray-400 w-full text-start">Tìm kiếm</p>
                <div class="relative">
                    <input class="w-full py-2.5 pr-[14px] pl-[42px] border rounded border-gray-400" value="{{ $keyword }}" />
                    <div class="absolute top-0 left-[14px]  translate-y-1/2">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.5 18.0361L13.875 14.4111M15.8333 9.7028C15.8333 13.3847 12.8486 16.3695 9.16667 16.3695C5.48477 16.3695 2.5 13.3847 2.5 9.7028C2.5 6.0209 5.48477 3.03613 9.16667 3.03613C12.8486 3.03613 15.8333 6.0209 15.8333 9.7028Z" stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>

                <!-- CATEGORY SECTION -->
                <div>
                    @php
                    $categoryData = [
                    ['name' => 'Tất cả', 'value' => 'tat-ca'],
                    ['name' => 'Kinh doanh', 'value' => 'kinh-doanh'],
                    ['name' => 'Công nghệ', 'value' => 'cong-nghe'],
                    ['name' => 'Tài chính', 'value' => 'tai-chinh'],
                    ['name' => 'Kinh tế', 'value' => 'kinh-te'],
                    ['name' => 'Giải pháp', 'value' => 'giai-phap'],
                    ['name' => 'Phong lưu', 'value' => 'phong-luu'],
                    ['name' => 'Chuyên đề', 'value' => 'chuyen-de'],
                    ['name' => 'Ý kiến', 'value' => 'y-kien'],
                    ['name' => 'Hồ sơ', 'value' => 'ho-so'],
                    ];
                    @endphp

                    <p class="text-lg font-bold pb-1 border-b border-gray-400 w-full text-start mb-2 px-2.5">Chuyên mục</p>
                    <div class="flex flex-col gap-y-2">
                        @foreach ($categoryData as $cateItem)
                        <div class="flex justify-between items-center cursor-pointer cate-option {{ $cateItem['value'] === request()->get('cate', 'tat-ca') ? 'selected bg-Gray_04 text-Icon05' : '' }} p-2.5 font-medium" data-value="{{ $cateItem['value'] }}">
                            <p>{{ $cateItem['name'] }}</p>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-icon">
                                <path d="M1 8H15M15 8L8 1M15 8L8 15" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
                            </svg>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- SORT SECTION -->
                <div>
                    @php
                    $sortData = [
                    ['name' => 'Mới nhất', 'value' => 'desc'],
                    ['name' => 'Cũ nhất', 'value' => 'asc']
                    ];
                    @endphp

                    <p class="text-lg font-bold pb-1 border-b border-gray-400 w-full text-start mb-4 px-2.5">Sắp xếp</p>
                    <div class="flex flex-col gap-y-2">
                        @foreach ($sortData as $optionSort)
                        <div class="flex justify-between items-center cursor-pointer sort-option {{ $optionSort['value'] === request()->get('sort', 'desc') ? 'selected bg-Gray_04 text-Icon05' : '' }} p-2.5" data-value="{{ $optionSort['value'] }}">
                            <p class="font-medium">{{ $optionSort['name'] }}</p>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-icon">
                                <path d="M1 8H15M15 8L8 1M15 8L8 15" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
                            </svg>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- TIME SECTION -->
                <div>
                    @php
                    $timeData = [
                    ['name' => 'Bất kỳ', 'value' => 'current'],
                    ['name' => '24 giờ qua', 'value' => 'in24Hours'],
                    ['name' => 'Tháng trước', 'value' => 'lastMonth']
                    ];
                    @endphp

                    <p class="text-lg font-bold pb-1 border-b border-gray-400 w-full text-start mb-4 px-2.5">Sắp xếp</p>
                    <div class="flex flex-col gap-y-2">
                        @foreach ($timeData as $optionTime)
                        <div class="flex justify-between items-center cursor-pointer time-option {{ $optionTime['value'] === request()->get('sort', 'desc') ? 'selected bg-Gray_04 text-Icon05' : '' }} p-2.5" data-value="{{ $optionTime['value'] }}">
                            <p class="font-medium">{{ $optionTime['name'] }}</p>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-icon">
                                <path d="M1 8H15M15 8L8 1M15 8L8 15" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
                            </svg>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- REFRESH FILTER SECTION -->
                <button id="refresh-filter" class="rounded-lg flex justify-center items-center font-semibold px-[14px] py-2.5 border w-full border-black hover:bg-Gray_05">
                    <div class="flex gap-x-2 items-center">
                        <p class="w-full">Làm mới bộ lọc</p>
                        <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1673 1.86945V6.86945M19.1673 6.86945H14.1673M19.1673 6.86945L15.3007 3.23611C14.405 2.34004 13.297 1.68545 12.08 1.33343C10.8629 0.981399 9.57657 0.943407 8.34089 1.223C7.10521 1.50259 5.96048 2.09064 5.01354 2.9323C4.06659 3.77395 3.34829 4.84177 2.92565 6.03611M0.833984 15.2028V10.2028M0.833984 10.2028H5.83398M0.833984 10.2028L4.70065 13.8361C5.59627 14.7322 6.70429 15.3868 7.92132 15.7388C9.13835 16.0908 10.4247 16.1288 11.6604 15.8492C12.8961 15.5696 14.0408 14.9816 14.9878 14.1399C15.9347 13.2983 16.653 12.2305 17.0756 11.0361" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
                        </svg>
                    </div>
                </button>
            </div>

            <!-- Results Section -->
            @if($keyword === "" || $keyword === null)
            <div class="col-span-2 py-5 lg:py-7.5 lg:px-0 px-5">
                <p class="text-2xl font-bold">Vui lòng nhập từ khóa để bắt đầu tìm kiếm</p>
            </div>
            @else
            @if (!empty($articleDetails['ListArticleResult']) && $articleDetails['ListArticleResult']->isNotEmpty())
            <div class="col-span-2 lg:px-0 px-5">
                <p class="text-2xl font-bold mb-3">Kết quả tìm kiếm cho từ khóa "{{ $keyword }}"</p>
                @foreach ($articleDetails['ListArticleResult'] as $articleResult)
                <a href="{{ url($articleResult['FriendlyTitle'] . '-' . $articleResult['PublisherId'] . '.html') }}">
                    <div class="flex gap-x-2 md:gap-x-4 py-4 border-b border-gray-400">
                        <div class="w-30 h-20 md:w-64 md:h-44">
                            <img src="{{ $articleResult['Thumbnail_600x315'] }}" class="w-full h-full object-cover" alt="{{ $articleResult['ThumbnailAlt'] ?? $articleResult['Title'] }}" />
                        </div>
                        <div class="flex-1 flex flex-col gap-y-2">
                            <p class="hover:text-Icon05 font-medium text-base md:text-lg md:block hidden">{{ $articleResult['Channel']['Name'] }}</p>
                            <p class="font-medium text-base md:text-xl hover:text-Icon05">{{ $articleResult['Title'] }}</p>
                            <p class="font-normal text-xs md:text-sm md:block hidden">{{ $articleResult['Headlines'] }}</p>
                            <p class="text-[10px] md:text-sm text-[#767676]">{{ formatDate($articleResult['Time_yyyyMMddHHmmss']) }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
                <div class="mt-6 flex justify-center server-side-pagination">

                </div>
            </div>
            @else
            <div class="col-span-2 py-5 lg:py-7.5 lg:px-0 px-5">
                <p class="text-2xl font-bold">Rất tiếc, chúng tôi không tìm thấy kết quả nào phù hợp với từ khóa "{{ $keyword }}"</p>
            </div>
            @endif
            @endif

            <!-- Second Column -->
            <div class="col-span-1 p-7.5 border border-y-0 border-l-0 border-gray-400 lg:block hidden">
                <section class="flex flex-col justify-center items-center">
                    <div id="zone-2" class="w-full relative" style="aspect-ratio: 1080/1450;">
                        <div class="skeleton-container absolute inset-0 z-20">
                            <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                        </div>
                    </div>
                </section>
            </div>
        </section>

        <!-- Footer Sections (unchanged) -->
        <section class="bg-[#f7f7f7] py-6 md:py-7.5 border-t md:border-x border-gray-400 flex justify-center items-center">
            <div class="lg:flex px-6 lg:px-7.5 justify-center items-center h-full w-full">
                <div id="zone-7" class="lg:flex lg:justify-center lg:items-center lg:border-t-0 w-full relative" style="aspect-ratio: 970/250;">
                    <div class="skeleton-container absolute inset-0 z-20">
                        <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                    </div>
                </div>
            </div>
        </section>
        <section class="p-6 lg:py-14 lg:px-7.5 flex justify-center items-center md:border border-x-0 md:border-b-0 border-gray-400">
            <div class="lg:flex lg:justify-center lg:items-center">
                <div class="flex flex-col lg:items-center gap-[20px] md:gap-[35px]">
                    <p id="newsletter-section" class="font-semibold text-2xl md:text-4xl">
                        Đăng ký nhận bản tin miễn phí
                    </p>
                    <div id="mauticform_wrapper_bbw" class="w-full">
                        <form autocomplete="false" role="form" method="post" action="https://email.beaconasiamedia.vn/form/submit?formId=10"
                            id="mauticform_bbw" data-mautic-form="bbw" enctype="multipart/form-data" class="w-full flex items-center" data-xhr-headers='{"X-Requested-With": "XMLHttpRequest"}'>
                            <div class="mauticform-error" id="mauticform_bbw_error"></div>
                            <div class="mauticform-message" id="mauticform_bbw_message"></div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col md:flex-row gap-y-[20px] md:gap-x-4 lg:mt-0 w-full">
                                    <div id="mauticform_bbw_email" data-validate="email" data-validation-type="email" class="w-full md:w-[65%]">
                                        <input id="mauticform_input_bbw_email" name="mauticform[email]" value=""
                                            placeholder="Nhập E-mail" type="email"
                                            class="bg-white border border-[#cccccc] text-gray-900 text-sm rounded-[8px] px-2.5 sm:px-6 w-full h-[44px]"
                                            required>
                                    </div>
                                    <input id="mauticform_input_bbw_recaptcha" name="mauticform[recaptcha]" value="" type="hidden">
                                    <button type="submit" name="mauticform[submit]" id="mauticform_input_bbw_submit" value=""
                                        class="whitespace-nowrap w-full md:w-[35%] bg-black text-white text-sm md:text-base px-2.5 sm:px-6 font-semibold rounded-[8px] h-[44px]">
                                        Đăng ký
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="mauticform[messenger]" value="1">
                            <input type="hidden" name="mauticform[formId]" id="mauticform_bbw_id" value="10">
                            <input type="hidden" name="mauticform[return]" id="mauticform_bbw_return" value="">
                            <input type="hidden" name="mauticform[formName]" id="mauticform_bbw_name" value="bbw">
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchInput = document.querySelector('input[value="{{ $keyword }}"]');
        const resultContainer = document.querySelector(".col-span-2");
        const sortOptions = document.querySelectorAll('.sort-option');
        const cateOptions = document.querySelectorAll('.cate-option');
        const timeOptions = document.querySelectorAll('.time-option');
        const refreshFilterBtn = document.getElementById('refresh-filter');

        let typingTimer;
        const doneTypingInterval = 500;
        let currentKeyword = '';
        let currentSort = 'desc';
        let currentCate = 'tat-ca';
        let currentTime = 'current'
        let currentPage = 1; // Track current page

        // Default values for reset
        const defaultSort = 'desc';
        const defaultCate = 'tat-ca';
        const defaultTime = 'current';

        // Handle refresh filter button
        if (refreshFilterBtn) {
            refreshFilterBtn.addEventListener('click', function() {
                // Reset all filters to default values
                currentSort = defaultSort;
                currentCate = defaultCate;
                currentTime = defaultTime;
                currentPage = 1;

                // Reset sort options UI
                sortOptions.forEach(opt => {
                    const isSelected = opt.dataset.value === defaultSort;
                    opt.classList.toggle('selected', isSelected);
                    opt.classList.toggle('bg-Gray_04', isSelected);
                    opt.classList.toggle('text-Icon05', isSelected);
                });

                // Reset category options UI
                cateOptions.forEach(opt => {
                    const isSelected = opt.dataset.value === defaultCate;
                    opt.classList.toggle('selected', isSelected);
                    opt.classList.toggle('bg-Gray_04', isSelected);
                    opt.classList.toggle('text-Icon05', isSelected);
                });

                // Reset time options UI
                timeOptions.forEach(opt => {
                    const isSelected = opt.dataset.value === defaultTime;
                    opt.classList.toggle('selected', isSelected);
                    opt.classList.toggle('bg-Gray_04', isSelected);
                    opt.classList.toggle('text-Icon05', isSelected);
                });

                // Clear results and show initial message
                if (resultContainer) {
                    resultContainer.innerHTML = `
                        <div class="py-5 lg:py-7.5 lg:px-0 px-5">
                            <p class="text-2xl font-bold">Vui lòng nhập từ khóa để bắt đầu tìm kiếm</p>
                        </div>
                    `;
                }

                // Update URL to reflect reset state
                updateUrl(searchInput.value, defaultSort, defaultCate, defaultTime, 1);
                fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
            });
        }

        // Handle sort selection
        sortOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                if (currentSort !== value) {
                    currentSort = value;
                    currentPage = 1; // Reset to first page on sort change
                    sortOptions.forEach(opt => {
                        const isSelected = opt.dataset.value === value;
                        opt.classList.toggle('selected', isSelected);
                        opt.classList.toggle('bg-Gray_04', isSelected);
                        opt.classList.toggle('text-Icon05', isSelected);
                    });
                    fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
                }
            });
        });

        // Handle category selection
        cateOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                if (currentCate !== value) {
                    currentCate = value;
                    currentPage = 1; // Reset to first page on category change
                    cateOptions.forEach(opt => {
                        const isSelected = opt.dataset.value === value;
                        opt.classList.toggle('selected', isSelected);
                        opt.classList.toggle('bg-Gray_04', isSelected);
                        opt.classList.toggle('text-Icon05', isSelected);
                    });
                    fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
                }
            });
        });

        // Handle time selection
        timeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                if (currentTime !== value) {
                    currentTime = value;
                    currentPage = 1; // Reset to first page on category change
                    timeOptions.forEach(opt => {
                        const isSelected = opt.dataset.value === value;
                        opt.classList.toggle('selected', isSelected);
                        opt.classList.toggle('bg-Gray_04', isSelected);
                        opt.classList.toggle('text-Icon05', isSelected);
                    });
                    fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
                }
            });
        });

        // Handle search input
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                clearTimeout(typingTimer);
                currentKeyword = e.target.value;
                currentPage = 1; // Reset to first page on keyword change
                typingTimer = setTimeout(() => {
                    fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
                }, doneTypingInterval);
            });
        }

        // Handle pagination clicks
        resultContainer.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination-link');
            if (paginationLink) {
                e.preventDefault();
                const page = paginationLink.dataset.page;
                if (page) {
                    currentPage = parseInt(page);
                    fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
                }
            }
        });

        function fetchResults(keyword, sort, cate, time, page) {
            fetch(`/api/search-keyword-article?q=${encodeURIComponent(keyword)}&sort=${sort}&cate=${cate}&time=${time}&page=${page}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateSearchResults(data.articleDetails, data.keyword, data.meta);
                    updateUrl(keyword, sort, cate, time, page);
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (resultContainer) {
                        resultContainer.innerHTML = `
                            <div class="py-5 lg:py-7.5 lg:px-0 px-5">
                                <p class="text-2xl font-bold">Đã xảy ra lỗi khi tải kết quả tìm kiếm</p>
                            </div>
                        `;
                    }
                });
        }

        function updateSearchResults(articles, keyword, meta) {
            if (!resultContainer) return;

            if (keyword === "" || keyword === null) {
                resultContainer.innerHTML = `
                    <div class="py-5 lg:py-7.5 lg:px-0 px-5">
                        <p class="text-2xl font-bold">Vui lòng nhập từ khóa để bắt đầu tìm kiếm</p>
                    </div>
                `;
                return;
            }

            if (!articles || articles.length === 0) {
                resultContainer.innerHTML = `
                    <div class="py-5 lg:py-7.5 lg:px-0 px-5">
                        <p class="text-2xl font-bold">Rất tiếc, chúng tôi không tìm thấy kết quả nào phù hợp với từ khóa "${keyword}"</p>
                    </div>
                `;
                return;
            }

            let html = `
                <div class="py-5 lg:py-7.5 lg:px-0 px-5">
                    <p class="text-2xl font-bold mb-3">Kết quả tìm kiếm cho từ khoá "${keyword}"</p>`;

            articles.forEach(article => {
                const friendlyLink = `${article.FriendlyTitle}-${article.PublisherId}.html`;
                html += `
                    <a href="${friendlyLink}">
                        <div class="flex gap-x-2 md:gap-x-4 py-4 border-b border-gray-400">
                            <div class="w-30 h-20 md:w-64 md:h-44">
                                <img src="${article.Thumbnail_600x315 || article.Thumbnail_540x360}" class="w-full h-full object-cover" alt="${article.ThumbnailAlt || article.Title}" />
                            </div>
                            <div class="flex-1 flex flex-col gap-y-2">
                                <p class="hover:text-Icon05 font-medium text-base md:text-lg md:block hidden">${article.Channel.Name}</p>
                                <p class="font-medium text-base md:text-xl hover:text-Icon05">${article.Title}</p>
                                <p class="font-normal text-xs md:text-sm md:block hidden">${article.Headlines}</p>
                                <p class="text-[10px] md:text-sm text-[#767676]">${formatDate(article.Time_yyyyMMddHHmmss)}</p>
                            </div>
                        </div>
                    </a>`;
            });

            // Add pagination controls
            if (meta && meta.total > meta.per_page) {
                html += `
                        <nav class="mt-6 flex items-center justify-between gap-4">
                            <!-- Previous Page Link -->
                            ${meta.links.prev 
                                ? `<a href="#" class="pagination-link px-4 py-2 border border-gray-300 rounded hover:bg-gray-100" data-page="${meta.current_page - 1}">←</a>`
                                : `<span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">←</span>`
                            }
                            <div class="flex gap-x-2">
                            ${generatePageLinks(meta.current_page, meta.last_page)}
                            </div>
                            <!-- Next Page Link -->
                            ${meta.links.next 
                                ? `<a href="#" class="pagination-link px-4 py-2 border border-gray-300 rounded hover:bg-gray-100" data-page="${meta.current_page + 1}">→</a>`
                                : `<span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">→</span>`
                            }
                        </nav>`;
            }

            html += `</div>`;
            resultContainer.innerHTML = html;
        }

        function generatePageLinks(currentPage, lastPage) {
            let links = '';
            const delta = 1; // Number of pages around current page to show
            let left = currentPage - delta;
            let right = currentPage + delta;
            let range = [];
            let rangeWithDots = [];

            // Generate basic range
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= left && i <= right)) {
                    range.push(i);
                }
            }

            // Add dots where gaps exist
            let prev = null;
            for (let i of range) {
                if (prev !== null && i - prev !== 1) {
                    rangeWithDots.push('...');
                }
                rangeWithDots.push(i);
                prev = i;
            }

            // Generate HTML for page links
            for (let i of rangeWithDots) {
                if (typeof i === 'string') {
                    links += `<span class="px-4 py-2">...</span>`;
                } else if (i === currentPage) {
                    links += `<span class="px-4 py-2 bg-black text-white rounded">${i}</span>`;
                } else {
                    links += `<a href="#" class="pagination-link px-4 py-2 border border-gray-300 rounded hover:bg-gray-100" data-page="${i}">${i}</a>`;
                }
            }

            return links;
        }

        function updateUrl(keyword, sort, cate, time, page) {
            const url = new URL(window.location.href);
            url.searchParams.set("q", keyword);
            url.searchParams.set("sort", sort);
            url.searchParams.set("cate", cate);
            url.searchParams.set("time", time);
            url.searchParams.set("page", page);
            window.history.replaceState({}, "", url);
        }

        function formatDate(rawDate) {
            const d = new Date(rawDate);
            if (isNaN(d.getTime())) return "Invalid date";
            const day = d.getDate();
            const month = d.getMonth() + 1;
            const year = d.getFullYear();
            const time = d.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            return `${day} tháng ${month}, ${year} lúc ${time}`;
        }

        // Initialize with URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const initialKeyword = urlParams.get('q') || '';
        const initialSort = urlParams.get('sort') || 'desc';
        const initialCate = urlParams.get('cate') || 'tat-ca';
        const initialTime = urlParams.get('time') || 'current';

        const initialPage = parseInt(urlParams.get('page')) || 1;
        if (initialKeyword && searchInput) {
            searchInput.value = initialKeyword;
            currentKeyword = initialKeyword;
        }
        if (initialSort) {
            currentSort = initialSort;
            sortOptions.forEach(opt => {
                const isSelected = opt.dataset.value === currentSort;
                opt.classList.toggle('selected', isSelected);
                opt.classList.toggle('bg-Gray_04', isSelected);
                opt.classList.toggle('text-Icon05', isSelected);
            });
        }
        if (initialCate) {
            currentCate = initialCate;
            cateOptions.forEach(opt => {
                const isSelected = opt.dataset.value === currentCate;
                opt.classList.toggle('selected', isSelected);
                opt.classList.toggle('bg-Gray_04', isSelected);
                opt.classList.toggle('text-Icon05', isSelected);
            });
        }
        if (initialTime) {
            currentTime = initialTime;
            timeOptions.forEach(opt => {
                const isSelected = opt.dataset.value === currentTime;
                opt.classList.toggle('selected', isSelected);
                opt.classList.toggle('bg-Gray_04', isSelected);
                opt.classList.toggle('text-Icon05', isSelected);
            });
        }
        if (initialPage) {
            currentPage = initialPage;
        }

        // Fetch initial results
        if (currentKeyword) {
            fetchResults(currentKeyword, currentSort, currentCate, currentTime, currentPage);
        }
    });
</script>
@endsection