<!-- GET DATA -->
@inject('navbarService', 'App\Services\NavbarService')
@php
$navbarData = $navbarService->getNavbarData();
$latestEvents = $navbarService->getLatestEvents();
@endphp

<!-- ASSIGN DATA  -->
@php
$categoriesList = [
['cate_name' => 'Kinh doanh', 'slug' => 'kinh-doanh'],
['cate_name' => 'Công nghệ', 'slug' => 'cong-nghe'],
['cate_name' => 'Chuyên đề', 'slug' => 'chuyen-de'],

['cate_name' => 'Tài chính', 'slug' => 'tai-chinh'],
['cate_name' => 'Kinh tế', 'slug' => 'kinh-te'],
['cate_name' => 'Ý kiến', 'slug' => 'y-kien'],

['cate_name' => 'Giải pháp', 'slug' => 'giai-phap'],
['cate_name' => 'Phong lưu', 'slug' => 'phong-luu'],
['cate_name' => 'Hồ sơ', 'slug' => 'ho-so'],
];

$mediaCategories = [
['cate_name' => 'E-Magazine', 'slug' => 'https://bloombergbusinessweek.vn/thu-vien-an-pham/'],
['cate_name' => 'Sự kiện', 'slug' => 'https://bloombergbusinessweek.vn/su-kien/'],
];

$greenCategories = [
['cate_name' => 'Công nghệ mới', 'slug' => 'cong-nghe-moi'],
['cate_name' => 'Năng lượng mới', 'slug' => 'nang-luong-moi'],
['cate_name' => 'COP', 'slug' => 'cop'],
['cate_name' => 'ESG', 'slug' => 'esg'],
['cate_name' => 'Đô thị tương lai', 'slug' => 'do-thi-tuong-lai'],
]
@endphp

<!-- VIDEO ADVERTISEMENT SECTION -->
<section class="bg-lightGray md:py-9 py-0 l-nav" id="header-banner-top">
    <div class=" container mx-auto">
        <div class="flex justify-center items-center md:px-24 px-0">
            <div id="zone-1" class="w-full relative" style="aspect-ratio: 970/250;">
                <!-- Skeleton Container - thêm absolute và z-index cao -->
                <div class="skeleton-container absolute inset-0 z-20">
                    <x-skeleton containerStyle="h-full w-full">
                    </x-skeleton>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NAVBAR SECTION -->
<section class="z-50 h-full transform duration-500 sticky top-0 lg:relative "
    style="overflow: visible; z-index: 1000; box-shadow: 1px 2px 2px 0px #00000026;" id="nav-section">
    <div class="lg:relative z-50 transform transition-transform duration-500" id="main-nav-section">
        <section class="shadow-md transition-transform duration-300 bg-black w-full ">
            <div class="flex flex-col justify-center items-center">
                <section
                    class="w-full flex justify-between items-center py-3 md:py-5 container-content container-content-phone">
                    <div class="flex gap-x-5 md:gap-x-7 md:items-center w-full">
                        <!-- HIDE: HAMBURGER BUTTON -->
                        <button class="lg:hidden block" id="menu-button">
                            <svg width="30" height="16" viewBox="0 0 18 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0.888889 2.16667H16.8889C17.1246 2.16667 17.3507 2.07887 17.5174 1.92259C17.6841 1.76631 17.7778 1.55435 17.7778 1.33333C17.7778 1.11232 17.6841 0.900358 17.5174 0.744078C17.3507 0.587798 17.1246 0.5 16.8889 0.5H0.888889C0.653141 0.5 0.427049 0.587798 0.26035 0.744078C0.0936505 0.900358 0 1.11232 0 1.33333C0 1.55435 0.0936505 1.76631 0.26035 1.92259C0.427049 2.07887 0.653141 2.16667 0.888889 2.16667ZM16.8889 8.83333H0.888889C0.653141 8.83333 0.427049 8.92113 0.26035 9.07741C0.0936505 9.23369 0 9.44565 0 9.66667C0 9.88768 0.0936505 10.0996 0.26035 10.2559C0.427049 10.4122 0.653141 10.5 0.888889 10.5H16.8889C17.1246 10.5 17.3507 10.4122 17.5174 10.2559C17.6841 10.0996 17.7778 9.88768 17.7778 9.66667C17.7778 9.44565 17.6841 9.23369 17.5174 9.07741C17.3507 8.92113 17.1246 8.83333 16.8889 8.83333ZM16.8889 4.66667H0.888889C0.653141 4.66667 0.427049 4.75446 0.26035 4.91074C0.0936505 5.06702 0 5.27899 0 5.5C0 5.72101 0.0936505 5.93297 0.26035 6.08926C0.427049 6.24554 0.653141 6.33333 0.888889 6.33333H16.8889C17.1246 6.33333 17.3507 6.24554 17.5174 6.08926C17.6841 5.93297 17.7778 5.72101 17.7778 5.5C17.7778 5.27899 17.6841 5.06702 17.5174 4.91074C17.3507 4.75446 17.1246 4.66667 16.8889 4.66667Z"
                                    fill="white" />
                            </svg>
                        </button>

                        <!-- LOGO -->
                        <div class="w-full md:w-2/3 lg:w-[36.5rem]">
                            <div class="w-full">
                                <a href="/">
                                    <img src="{{ asset('images/logo-bbw-v2.svg') }}" alt="logo" class="w-full h-full" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <div class="flex md:gap-x-2 w-full h-fit items-start">
                            <div id="user-menu-container">
                                <div class="flex gap-x-2 h-fit items-center">
                                    {{-- Guest menu --}}
                                    <a class="w-30 bg-white font-semibold px-2 md:px-5 py-1 rounded-lg text-center hover:bg-primary text-sm text-nowrap hidden lg:block whitespace-nowrap"
                                        href="{{ route('oauth.redirect') }}">
                                        Đăng ký
                                    </a>
                                    <a class="w-30 text-white font-semibold px-3 py-1 rounded-lg border text-center border-white hover:text-primary text-sm hidden lg:block whitespace-nowrap"
                                        href="{{ route('oauth.redirect') }}">
                                        Đăng nhập
                                    </a>
                                </div>

                                <p class="text-white text-[0.5rem] text-end mt-2 mr-8">Một sản phẩm của <span
                                        class="font-bold text-darkYellow">BEACON</span><span class="font-bold">
                                        MEDIA</span></p>
                            </div>
                            <!-- SEARCH BUTTON -->
                            <button class="rounded hidden md:block w-6 h-auto" id="search-btn">
                                <img src="{{ asset('images/search-icon-v2.svg') }}" alt="search" class="w-full h-fit" />
                            </button>
                        </div>
                    </div>
                </section>
            </div>


            <!-- SHOW NAVBAR SECTION -->
            <!-- SHOW CATEGORIES -->
            <section id="navbar-section">
                <div class="bg-Gray_16 hidden lg:flex justify-center items-center relative">
                    <div class="container-content container-content-phone w-full">
                        <div class="">
                            <div class="flex gap-x-4">
                                @foreach($navbarData['categories'] as $cate)
                                @php
                                $categoryId = $cate['cate_id'];
                                $categoryArticles = $navbarData['articles'][$categoryId] ?? null;
                                @endphp

                                <div>
                                    <div class="group">
                                        <!-- CATEGORIES HEADER SECTION -->
                                        <div
                                            class="text-white font-semibold text-sm px-4 py-2 hover:text-Icon03 hover:bg-Gray_15 border-b border-b-transparent hover:border-b-Icon03 text-start focus:text-Icon03 focus:bg-Gray_15 focus:border-b focus:border-b-Icon03">
                                            <a href="{{$cate['slug']}}">
                                                {{$cate['cate_name']}}
                                            </a>
                                        </div>

                                        <!-- HIDE DROPDOWN SECTION -->
                                        <section
                                            class="hidden group-hover:block absolute left-0 z-50 bg-Gray_16 w-full ">
                                            <div class="flex justify-center items-center">
                                                <div
                                                    class="container-content container-content-phone border border-b-0 border-Line_03 py-4">
                                                    <!-- CATEGORY 4 ARTICLE SECTION -->
                                                    <div class="mb-5 flex justify-between px-4">
                                                        <div class="flex gap-x-4">
                                                            <!-- ARTICLE FOCUS -->
                                                            @if(isset($categoryArticles['ArticleFocus']))
                                                            @php
                                                            $keywords = explode(',',
                                                            $categoryArticles['ArticleFocus']['Keyword'] ?? '');
                                                            $isPremiumArticle = isPremiumContent($keywords);
                                                            @endphp
                                                            <div
                                                                class="flex-1 flex gap-x-2 pr-4 border-r border-r-Line_03">
                                                                <div class="w-full">
                                                                    <div
                                                                        class="relative w-36 aspect-[3/2] overflow-hidden">
                                                                        <a
                                                                            href="{{ url($categoryArticles["ArticleFocus"]['FriendlyTitle'] . '-' . $categoryArticles["ArticleFocus"]['PublisherId'] . '.html') ?? '' }}">
                                                                            <img src={{$categoryArticles["ArticleFocus"]['Thumbnail_540x360'] ?? ""}}
                                                                                alt=""
                                                                                class="w-full h-full object-cover" />
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <div class="text-white font-normal text-start text-xs">
                                                                    @if ($isPremiumArticle === true)
                                                                    <p
                                                                        class=" bg-BG_Overlay_01 px-2 py-1 text-white w-fit text-[10px] tracking-[.2em] z-20">
                                                                        PREMIUM</p>
                                                                    @endif
                                                                    <a class="hover:text-Icon03"
                                                                        href="{{ url($categoryArticles["ArticleFocus"]['FriendlyTitle'] . '-' . $categoryArticles["ArticleFocus"]['PublisherId'] . '.html') ?? '' }}">
                                                                        {{$categoryArticles["ArticleFocus"]['Title']}}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            @endif

                                                            <!-- LIST ARTICLE HIGHLIGHT -->
                                                            @if(isset($categoryArticles["ListArticleHighLight"]))
                                                            @foreach(array_slice($categoryArticles["ListArticleHighLight"],
                                                            0, 3) as $index => $article)
                                                            @php
                                                            $keywords = explode(',', $article['Keyword'] ?? '');
                                                            $isPremiumArticle = isPremiumContent($keywords);
                                                            @endphp
                                                            <div
                                                                class="flex-1 flex gap-x-2 {{ $index !== 2 ? "pr-4 border-r border-r-Line_03": ""}}">
                                                                <div class="w-full">
                                                                    <div
                                                                        class="relative w-24 xl:w-40 aspect-[3/2] overflow-hidden">
                                                                        <a
                                                                            href="{{ url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') ?? '' }}">
                                                                            <img src={{$article['Thumbnail_540x360']}}
                                                                                alt="" class="w-full h-full" />
                                                                        </a>

                                                                    </div>
                                                                </div>


                                                                <div class="text-white font-normal text-start text-xs">
                                                                    @if ($isPremiumArticle === true)
                                                                    <p
                                                                        class=" bg-BG_Overlay_01 px-2 py-[0.125rem] text-white w-fit text-[0.625rem] z-20 mb-1 font-bold">
                                                                        Premium</p>
                                                                    @endif

                                                                    <a class="hover:text-Icon03"
                                                                        href="{{ url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') ?? '' }}">
                                                                        {{$article['Title']}}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- SUMMARY CATEGORY SECTION -->
                                                    <div
                                                        class="flex justify-between items-start border-t border-t-Line_03 px-1">
                                                        <div class="flex gap-x-4 pt-5 w-3/4">
                                                            <x-topic-list title="Chuyên mục" :itemList="$categoriesList"
                                                                :isCategory="true" />
                                                            <div class="flex gap-x-4 pl-4 border-l border-l-Line_03">
                                                                <x-topic-list title="Media" :itemList="$mediaCategories"
                                                                    :isCategory="true" :isOpenNewTab="true" />
                                                                @if ($latestEvents && count($latestEvents) > 0)
                                                                <x-topic-list title="Báo cáo đặc biệt"
                                                                    :itemList="$latestEvents" :isCategory="false" />
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="text-nowrap pt-5 text-right">
                                                            <p class="text-white font-normal mb-2 mr-4 text-sm">
                                                                Theo dõi chúng tôi
                                                            </p>
                                                            <x-social-media color="white" :gap="'gap-x-2.5'" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>

                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- SEARCH INPUT SECTION -->
            <section id="search-section" class="bg-white py-3 hidden w-full">
                <div class="flex justify-center">
                    <div class="container-content">
                        <section class="flex justify-between gap-x-4">
                            <input id="search-input"
                                class="w-full bg-white text-base rounded-md border border-Gray_07 py-2.5 px-4 text-Icon06"
                                placeholder="Nhập từ khóa cần tìm" />
                            <button id="search-button"
                                class="w-64 bg-black py-2.5 text-center text-white font-semibold rounded-lg flex justify-center items-center gap-x-2">
                                <p class="text-lg font-bold">Tìm kiếm</p>
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16 15.5L12.375 11.875M14.3333 7.16667C14.3333 10.8486 11.3486 13.8333 7.66667 13.8333C3.98477 13.8333 1 10.8486 1 7.16667C1 3.48477 3.98477 0.5 7.66667 0.5C11.3486 0.5 14.3333 3.48477 14.3333 7.16667Z"
                                        stroke="white" stroke-linecap="square" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </section>
                    </div>
                </div>
            </section>
        </section>

        <!-- HAMBURGER MENU OPEN -->
        <div class="w-full h-full hidden min-h-screen lg:hidden absolute left-0 z-60 overflow-y-auto" id="menu-section">
            <section
                class="w-full h-full min-h-screen absolute left-0 bg-Gray_16 overflow-y-auto flex flex-col items-center"
                id="menu-container">
                <section class="py-4 w-full bg-black flex flex-col items-center">
                    <div class="container-content container-content-phone">
                        <div class="relative ">
                            <div class="absolute top-1/2 left-4 -translate-y-1/2">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16 15.5L12.375 11.875M14.3333 7.16667C14.3333 10.8486 11.3486 13.8333 7.66667 13.8333C3.98477 13.8333 1 10.8486 1 7.16667C1 3.48477 3.98477 0.5 7.66667 0.5C11.3486 0.5 14.3333 3.48477 14.3333 7.16667Z"
                                        stroke="white" stroke-linecap="square" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <input id="search-input-in-phone"
                                class="w-full text-base rounded-lg border focus:outline-none border-white bg-black py-2 pl-10 pr-4 text-white placeholder:text-white"
                                placeholder="Nhập từ khóa cần tìm" />
                        </div>
                    </div>
                </section>

                <section class="container-content container-content-phone">
                    <div id="user-menu-phone-container" class="w-full container-content">
                        <div class="flex flex-col">
                            <a href="{{ route('oauth.redirect') }}"
                                class="flex gap-x-2 items-center text-white text-base leading-6 py-2.5 font-bold ">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.5 2.5H15.8333C16.2754 2.5 16.6993 2.67559 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H12.5M8.33333 14.1667L12.5 10M12.5 10L8.33333 5.83333M12.5 10H2.5"
                                        stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square"
                                        stroke-linejoin="round" />
                                </svg>

                                <p>Đăng Nhập</p>
                            </a>

                            <a href="{{ route('oauth.redirect') }}"
                                class="flex gap-x-2 items-center text-white text-base leading-6 py-2.5 font-bold border-y border-y-Line_03">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M14.1654 17.5V15.8333C14.1654 14.9493 13.8142 14.1014 13.1891 13.4763C12.5639 12.8512 11.7161 12.5 10.832 12.5H4.16536C3.28131 12.5 2.43346 12.8512 1.80834 13.4763C1.18322 14.1014 0.832031 14.9493 0.832031 15.8333V17.5M19.1654 17.5V15.8333C19.1648 15.0948 18.919 14.3773 18.4665 13.7936C18.014 13.2099 17.3805 12.793 16.6654 12.6083M13.332 2.60833C14.049 2.79192 14.6846 3.20892 15.1384 3.79359C15.5922 4.37827 15.8386 5.09736 15.8386 5.8375C15.8386 6.57764 15.5922 7.29673 15.1384 7.88141C14.6846 8.46608 14.049 8.88308 13.332 9.06667M10.832 5.83333C10.832 7.67428 9.33965 9.16667 7.4987 9.16667C5.65775 9.16667 4.16536 7.67428 4.16536 5.83333C4.16536 3.99238 5.65775 2.5 7.4987 2.5C9.33965 2.5 10.832 3.99238 10.832 5.83333Z"
                                        stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square"
                                        stroke-linejoin="round" />
                                </svg>

                                <p>Đăng Ký</p>
                            </a>
                        </div>
                    </div>
                    <x-topic-list title="Chuyên mục" :itemList="$categoriesList" :isCategory="true" :oneColumn="true">
                        <x-slot name="icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.3346 6.6671L1.66797 18.3338M14.5846 12.5004H7.5013M16.868 10.2004C17.8062 9.26223 18.3333 7.98976 18.3333 6.66294C18.3333 5.33612 17.8062 4.06364 16.868 3.12544C15.9298 2.18723 14.6573 1.66016 13.3305 1.66016C12.0036 1.66016 10.7312 2.18723 9.79297 3.12544L4.16797 8.75044V15.8338H11.2513L16.868 10.2004Z"
                                    stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square"
                                    stroke-linejoin="round" />
                            </svg>
                        </x-slot>
                    </x-topic-list>

                    <x-topic-list title="Media" :itemList="$mediaCategories" :isCategory="true" :oneColumn="true"
                        :isOpenNewTab="true">
                        <x-slot name="icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1241_35061)">
                                    <path
                                        d="M5.83464 1.66699V18.3337M14.168 1.66699V18.3337M1.66797 10.0003H18.3346M1.66797 5.83366H5.83464M1.66797 14.167H5.83464M14.168 14.167H18.3346M14.168 5.83366H18.3346M3.48464 1.66699H16.518C17.5213 1.66699 18.3346 2.48034 18.3346 3.48366V16.517C18.3346 17.5203 17.5213 18.3337 16.518 18.3337H3.48464C2.48132 18.3337 1.66797 17.5203 1.66797 16.517V3.48366C1.66797 2.48034 2.48132 1.66699 3.48464 1.66699Z"
                                        stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square"
                                        stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_1241_35061">
                                        <rect width="20" height="20" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </x-slot>
                    </x-topic-list>


                    @if ($latestEvents && count($latestEvents) > 0)
                    <x-topic-list title="Báo cáo đặc biệt" :itemList="$latestEvents" :isCategory="false"
                        :oneColumn="true">
                        <x-slot name="icon">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 16L11 21L21 16M1 11L11 16L21 11M11 1L1 6L11 11L21 6L11 1Z" stroke="#6E6E6E"
                                    stroke-width="1.66667" stroke-linecap="square" stroke-linejoin="round" />
                            </svg>
                        </x-slot>
                    </x-topic-list>
                    @endif

                    <div id="user-menu-logout"></div>

                    <a href="https://beaconasiamedia.vn/">
                        <p class="text-white py-2.5 leading-9">Một sản phẩm của
                            <span class="'font-bold text-darkYellow">BEACON</span>
                            <span class="font-bold">MEDIA</span>
                        </p>
                    </a>
                    <div class="h-6">
                    </div>
                </section>
            </section>
        </div>
    </div>

    <!-- Sub Navigation Section (Moved outside main-nav-section) -->
    <div id="sub-nav-section"
        class="w-full max-w-screen lg:hidden border-b border-gray-300 bg-white z-50 flex justify-center"
        style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
        <div class="md:ml-5 container-content container-content-phone relative  flex gap-x-3 items-center">
            <div class="absolute top-0 right-0 opacity-60 w-10 h-full z-50"
                style="width: 2rem !important; background-image: linear-gradient(to right, rgb(255, 255, 255, 0.5) , rgb(255, 255, 255))  !important;">
            </div>

            <a href="/">
                <svg width="34" height="18" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 15V5L7 0L14 5V15H8.75V9.16667H5.25V15H0Z" fill="black" />
                </svg>
            </a>

            <div class="scroll-hidden flex gap-x-6 overflow-x-auto">
                @foreach ($categoriesList as $cate)
                @php
                $isActive = request()->is($cate['slug']);
                @endphp
                <a href="/{{ $cate['slug'] }}"
                    class="text-sm whitespace-nowrap px-1 py-2 {{ $isActive ? 'font-bold active-category' : '' }}">{{ $cate['cate_name'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Animation -->
<style>
@media (min-width: 1024px) {
    #sub-nav-section {
        display: none;
    }
}


@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Add these CSS rules for ultra-smooth navigation */
#nav-section {
    /* GPU acceleration for smooth transforms */
    will-change: transform;
    transform: translateZ(0);
    backface-visibility: hidden;

    /* Optimized transition timing */
    transition: transform 0.25s cubic-bezier(0.4, 0.0, 0.2, 1);
}

/* Remove transition during fast scrolling */
.scrolling-fast #nav-section {
    transition: none !important;
}

/* Optimize for mobile */
@media (max-width: 1024px) {
    #nav-section {
        /* Use 3D transforms for better performance */
        transform: translate3d(0, 0, 0);
    }
}

/* Reduce repaints in dropdown menus */
.group section {
    will-change: opacity, visibility;
    contain: layout style;
}

/* Optimize scrollable areas */
.scroll-hidden {
    /* Better momentum scrolling */
    -webkit-overflow-scrolling: touch;
    overflow-scrolling: touch;

    /* Reduce scroll lag */
    scroll-behavior: auto;
}
</style>

<!-- HANDLE LOGIC -->
<script>
const searchInput = document.getElementById("search-input");
const searchButton = document.getElementById("search-button");

// Handle Enter key in input
searchInput.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        search();
    }
});

// Handle button click
searchButton.addEventListener("click", function() {
    search();
});

// Common search function
function search() {
    const keyword = searchInput.value.trim();
    if (keyword) {
        window.location.href = `/search?q=${encodeURIComponent(keyword)}`;
    }
}

document.getElementById("search-input-in-phone").addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        const keyword = this.value.trim();
        if (keyword) {
            window.location.href = `/search?q=${encodeURIComponent(keyword)}`;
        }
    }
});

// Handle Open Hamburger Menu
document.addEventListener("DOMContentLoaded", () => {
    const menuButton = document.getElementById("menu-button");
    const menuSection = document.getElementById("menu-section");
    const subNNavSection = document.getElementById("sub-nav-section");

    menuButton.addEventListener("click", () => {
        // Toggle the 'hidden' class on the menu section
        menuSection.classList.toggle("hidden");

        if (menuSection.classList.contains("hidden")) {
            subNNavSection.classList.remove("hidden"); // Hide sub nav when menu is visible
            document.body.classList.remove("overflow-hidden"); // Remove overflow when menu is hidden
        } else {
            subNNavSection.classList.add("hidden"); // Hide sub nav when menu is visible
            document.body.classList.add("overflow-hidden"); // Add overflow when menu is visible
        }
    });
});

// HANDLE OPEN SEARCH
document.addEventListener("DOMContentLoaded", () => {
    const searchSection = document.getElementById("search-section");
    const searchButton = document.getElementById("search-btn");

    // Toggle search on button click
    searchButton.addEventListener("click", (e) => {
        console.log("Search button clicked");
        if (window.setIgnoreScrollOnce) window.setIgnoreScrollOnce();
        const isSearchVisible = !searchSection.classList.contains("hidden");

        if (isSearchVisible) {
            // Hide search and show navbar
            searchSection.classList.add("hidden");
        } else {
            // Show search and hide navbar
            searchSection.classList.remove("hidden");
        }

        // Prevent the click from bubbling up to the document
        e.preventDefault();
        e.stopPropagation();
    });

    // Prevent clicks inside the search section from closing it
    searchSection.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
    });

    // Close search if clicking outside
    document.addEventListener("click", () => {
        if (!searchSection.classList.contains("hidden")) {
            searchSection.classList.add("hidden");
        }
    });
});

// HANDLE DROPDOWN
function toggleDropdown(menuId) {
    const dropdownMenu = document
        .getElementById(menuId);
    dropdownMenu.classList.toggle('hidden');
}

document.addEventListener("DOMContentLoaded", function() {
    function paddingBottomMenuContainer() {
        const screenWidth = window.innerWidth;
        const menuContainer = document.getElementById("menu-container");

        if (screenWidth < 375) {
            menuContainer.style.paddingBottom = "10rem";
        } else if (screenWidth <= 430) {
            menuContainer.style.paddingBottom = "11rem";
        } else if (screenWidth <= 500) {
            menuContainer.style.paddingBottom = "12.5rem";
        } else if (screenWidth <= 600) {
            menuContainer.style.paddingBottom = "13.5rem";
        } else if (screenWidth <= 700) {
            menuContainer.style.paddingBottom = "14.5rem";
        } else {
            menuContainer.style.paddingBottom = "20rem";
        }

    }

    // Run on page load
    paddingBottomMenuContainer();

    // Re-run on window resize
    window.addEventListener("resize", paddingBottomMenuContainer);
});

document.addEventListener('DOMContentLoaded', () => {
    const navSection = document.getElementById('nav-section');
    const mainNavSection = document.getElementById('main-nav-section');
    const headerBannerTop = document.getElementById('header-banner-top');
    const menuSection = document.getElementById("menu-section");

    let ignoreNextScroll = false;
    let ticking = false;
    let lastScrollTop = 0;

    // Cache measurements to avoid repeated DOM queries
    let cachedNavHeight = 0;
    let cachedBannerHeight = 0;
    let cachedThreshold = 0;
    let cachedIsMobile = false;

    // Update cached values
    const updateCache = () => {
        cachedIsMobile = window.innerWidth < 1025;
        cachedNavHeight = cachedIsMobile ? mainNavSection.offsetHeight : navSection.offsetHeight;
        cachedBannerHeight = headerBannerTop.offsetHeight;
        cachedThreshold = cachedNavHeight + cachedBannerHeight;

        if (lastScrollTop === 0) {
            lastScrollTop = cachedThreshold;
        }
    };

    // Initial cache update
    updateCache();

    // Optimized scroll handler using requestAnimationFrame
    const handleScroll = () => {
        if (!ticking) {
            requestAnimationFrame(updateNavigation);
            ticking = true;
        }
    };

    const updateNavigation = () => {
        ticking = false;

        // Early exits for performance
        if (!menuSection.classList.contains("hidden") || ignoreNextScroll) {
            return;
        }

        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        const scrollDelta = currentScroll - lastScrollTop;

        // Skip tiny scroll movements to reduce jitter
        if (Math.abs(scrollDelta) < 3) {
            return;
        }

        // Main scroll logic
        if (currentScroll > cachedThreshold) {
            const isScrollingDown = scrollDelta > 0;

            if (isScrollingDown) {
                // Hide navigation when scrolling down
                hideNavigation();
            } else {
                // Show navigation when scrolling up
                showNavigation();
            }

            lastScrollTop = Math.max(currentScroll, cachedThreshold);
        } else if (currentScroll <= cachedBannerHeight) {
            // Reset navigation at top
            resetNavigation();
            lastScrollTop = cachedThreshold;
        }
    };

    // Navigation state management functions
    const hideNavigation = () => {
        const transformValue = cachedIsMobile ?
            `translateY(-${cachedNavHeight}px)` :
            'translateY(-100%)';

        if (navSection.style.transform !== transformValue) {
            navSection.style.transform = transformValue;
        }
    };

    const showNavigation = () => {
        if (navSection.style.transform !== 'translateY(0px)') {
            navSection.style.position = 'sticky';
            navSection.style.top = '0';
            navSection.style.transform = 'translateY(0)';
        }
    };

    const resetNavigation = () => {
        if (navSection.style.position || navSection.style.top || navSection.style.transform) {
            navSection.style.removeProperty('position');
            navSection.style.removeProperty('top');
            navSection.style.removeProperty('transform');
        }
    };

    // Debounced resize handler
    let resizeTimer;
    const handleResize = () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateCache();
        }, 150);
    };

    // Event listeners with passive option for better performance
    window.addEventListener('scroll', handleScroll, {
        passive: true
    });
    window.addEventListener('resize', handleResize, {
        passive: true
    });

    // Global ignore function
    window.setIgnoreScrollOnce = () => {
        ignoreNextScroll = true;
        setTimeout(() => {
            ignoreNextScroll = false;
        }, 200);
    };
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const activeCategory = document.querySelector('.active-category');
    if (activeCategory) {
        activeCategory.scrollIntoView({
            behavior: 'smooth',
            inline: 'start',
            block: 'nearest'
        });
    }
});
</script>

<script>
// Utility function to load content into containers
async function loadMenuContent(url, containerId, errorMessage = 'Error loading menu content') {
    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const html = await response.text();
        const container = document.getElementById(containerId);

        if (container) {
            container.innerHTML = html;
        } else {
            console.warn(`⚠️ Container with ID '${containerId}' not found`);
        }
    } catch (error) {
        console.error(`❌ ${errorMessage}:`, error);
    }
}

// Load all menu components when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    const currentUrl = encodeURIComponent(window.location.href);

    // Load main user menu
    await loadMenuContent(
        `{{ route('user-menu') }}?redirect_bbw=${currentUrl}`,
        'user-menu-container',
        'Error loading user menu'
    );

    // Load phone menu
    await loadMenuContent(
        `{{ route('user-menu-phone') }}`,
        'user-menu-phone-container',
        'Error loading user menu phone'
    );

    // Load logout menu
    await loadMenuContent(
        `{{ route('user-menu-logout') }}?redirect_bbw=${currentUrl}`,
        'user-menu-logout',
        'Error loading user menu logout'
    );
});
</script>