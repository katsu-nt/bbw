@php
$mediaKitLink = "https://bloombergbusinessweek.bbw.vn/thong-tin/";
$datMuaAnPhamInLink = "https://subscribe.beaconasiamedia.vn/";
$datMuaSachLink = "https://beaconasiamedia.vn/bloomberg-by-bloomberg-vn/";
$dieuKhoan="/dieu-khoan-dich-vu";
$chinhSach="/chinh-sach-bao-mat";
$email = "contact@bloombergbusinessweek.vn";
$information = "https://www.bloombergmedia.com/press/bloomberg-businessweek-collaborates-with-beacon-asia-media-to-launch-phat-trien-xanh-bloomberg-businessweek-vietnam/";
$tuyenDung = "https://bloombergbusinessweek.bbw.vn/thong-tin/";

@endphp

<!-- FOOTER SECTION -->
<section class="bg-[#1A1A1A] z-10 flex justify-center items-center">
    <div class="container-content pt-5 md2:pt-7.5">
        <section class="w-full flex flex-col gap-y-3 pr-5 md:pr-0 pl-5 md2:pl-0 pb-5 md2:pb-7.5 md:flex-row md:justify-between md:items-center">
            <!-- LOGO -->
            <div class="w-full md:w-1/3 lg:w-[30rem] flex flex-col gap-y-3">
                <a href="/">
                    <img src="{{ asset('images/logo-bbw-v2.svg') }}" alt="logo" class="w-full" />
                </a>

                <!-- SOCIAL MEDIA dưới logo khi màn hình < 900px -->
                <div class="flex flex-row gap-x-3 md2:hidden mt-2">
                    <x-social-media color="white" />
                </div>
            </div>

            <!-- SOCIAL MEDIA SECTION WITH TEXT - chỉ hiển thị khi màn hình >= 900px -->
            <div class="hidden md2:flex md2:flex-col items-end gap-y-2 self-end pr-[18px]">
                <p class="text-white text-xs opacity-80">Theo dõi chúng tôi</p>
                <x-social-media color="white" />
            </div>
        </section>

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

        $categories1List = [
        ['cate_name' => 'Kinh doanh', 'slug' => 'kinh-doanh'],
        ['cate_name' => 'Công nghệ', 'slug' => 'cong-nghe'],
        ['cate_name' => 'Tài chính', 'slug' => 'tai-chinh'],
        ['cate_name' => 'Kinh tế', 'slug' => 'kinh-te'],
        ];

        $categories2List = [
        ['cate_name' => 'Giải pháp', 'slug' => 'giai-phap'],
        ['cate_name' => 'Phong lưu', 'slug' => 'phong-luu'],
        ['cate_name' => 'Ý kiến', 'slug' => 'y-kien'],
        ['cate_name' => 'Hồ sơ', 'slug' => 'ho-so'],
        ['cate_name' => 'E-Magazine', 'slug' => 'https://bloombergbusinessweek.bbw.vn/thu-vien-an-pham/'],
        ['cate_name' => 'Sự kiện', 'slug' => 'https://bloombergbusinessweek.bbw.vn/su-kien/'],
        ];

        $mediaCategories = [
        ['cate_name' => 'E-Magazine', 'slug' => 'https://bloombergbusinessweek.bbw.vn/thu-vien-an-pham/'],
        //['cate_name' => 'Podcast', 'slug' => 'podcast'],
        //['cate_name' => 'Video', 'slug' => 'video'],
        ['cate_name' => 'Sự kiện', 'slug' => 'https://bloombergbusinessweek.bbw.vn/su-kien/'],
        ];

        $greenCategories = [
        ['cate_name' => 'Danh sách', 'slug' => '/'],
        ['cate_name' => 'Đồ thị trong tuần', 'slug' => '/'],
        ['cate_name' => 'Báo cáo đặc biệt', 'slug' => '/'],
        ['cate_name' => 'Nội dung tài trợ', 'slug' => '/'],
        ]
        @endphp

        <section class="flex md:mt-0 border-[#323232] border-t border-b md2:border">
            <!-- Column 1: Chuyên mục -->
            <div class="hidden md2:block md2:w-auto pl-1 pr-5 py-4">
                <x-topic-list
                    title="Chuyên mục"
                    :titleSize="'text-base mb-3'"
                    :itemList="$categoriesList"
                    :gap="'gap-y-1'"
                    :isCategory="true" />
            </div>

            <!-- Column 2: Truy cập nhanh -->
            <div class="hidden md2:block md2:w-auto px-4 py-4 border-[#323232] border-l">
                <div class="flex flex-col items-start">
                    <p class="text-white font-bold py-1 px-3 text-base mb-3">Truy cập nhanh</p>
                    <div class="flex gap-x-5 gap-y-1 w-full text-sm">
                        <!-- Cột trái: 3 liên kết -->
                        <div class="flex flex-col gap-y-1">
                            <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3 opacity-80">
                                <a href={{$datMuaAnPhamInLink}}>Đặt mua ấn phẩm in</a>
                            </div>
                            <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3 opacity-80">
                                <a href={{$datMuaSachLink}}>Đặt mua sách</a>
                            </div>
                            <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3 opacity-80">
                                <button id="newsletter-btn" class="text-start text-white">Đăng ký nhận Newsletter</button>
                            </div>
                        </div>

                        <!-- Cột phải: 2 liên kết -->
                        <div class="flex flex-col gap-y-1">
                            <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3 opacity-80">
                                <a href={{$information}}>Về chúng tôi</a>
                            </div>
                            <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3 opacity-80">
                                <a href={{$tuyenDung}}>Tuyển dụng</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 3: Liên hệ -->
            <div class="w-full md2:w-auto xl:col-span-1 px-5 py-5">
                <div class="flex flex-col gap-y-3 items-start">
                    <p class="font-bold text-white">Liên hệ</p>
                    <a href="https://bloombergbusinessweek.bbw.vn/lien-he/" class="border border-white rounded-[8px] py-[6px] px-[16px] text-white text-xs hover:bg-white hover:text-black transition-colors h-[32px] w-[204px] flex items-center justify-center">
                        Hợp tác quảng cáo
                    </a>
                </div>
            </div>

            <!-- Column 4: Trống -->
            <div class="xl:col-span-1 px-5 py-5 hidden xl:block">
                <!-- Cột này để trống -->
            </div>
        </section>

        <section class="flex flex-col lg:flex-row lg:justify-between lg:gap-x-7 pt-5 pb-[35px] px-4 lg:mt-0 md2:border-[#323232] md2:border-x">
            <div class="text-white text-sm opacity-80 lg:w-[60%]">
                <p>
                    Giấy phép thiết lập trang thông tin điện tử tổng hợp trên mạng số 30/ GP-STTTT do Sở Thông Tin và Truyền Thông thành phố Hồ Chí Minh cấp ngày 24/12/2024
                </p>
                <p>
                    Chịu trách nhiệm nội dung: Ông Võ Quốc Khánh
                </p>
                <p>
                    Trụ sở: Lầu 12A, số 412 Nguyễn Thị Minh Khai, phường 5, Quận 3, Thành phố Hồ Chí Minh
                </p>
                <p>
                    Điện thoại: (028) 8889.0868
                </p>
                <p>
                    Email: bientap@bloombergbusinessweek.vn
                </p>
            </div>
            <div class="lg:w-[40%]">
                <div class="flex-1 flex md2:flex-row flex-col md:gap-x-5 gap-y-2.5 text-white text-sm underline text-left md2:text-center md2:justify-between lg:justify-end mt-3 lg:mt-0">
                    <p>
                        <a class=" opacity-80 hover:opacity-100 text-nowrap" href={{$dieuKhoan}}>
                            Điều kiện và điều khoản sử dụng
                        </a>
                    </p>
                    <p>
                        <a class=" opacity-80 hover:opacity-100 md:text-nowrap text-wrap" href={{$chinhSach}}>Chính sách bảo mật</a>
                    </p>
                </div>
                <div class="md:mb-0 mb-3 flex-1 flex gap-x-5 text-white text-sm text-left md2:text-center md2:justify-between justify-start lg:justify-end mt-3 md:mt-3">
                    <p class=" opacity-80 md:text-nowrap text-wrap text-red lg:text-right text-center">© Copyright 2023-2025 Công ty Cổ phần Beacon Asia Media</p>
                </div>
            </div>
        </section>

    </div>
</section>


<!-- <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script> -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('js/all.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}?v=1.2.5"></script>


<!--Page specific javascripts part-->
@if (Request::routeIs('article.show'))
<script src="https://asset.1cdn.vn/onecms/all/editor/snippets-custom.bundle.min.js?t=2024062009"></script>
@endif