@extends('layouts.app')
@section('content')

<?php
function formatDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
    if ($date) {
        return $date->format('d') . '.' . $date->format('m') . '.' . $date->format('Y');
    }
    return "Invalid date";
}
?>


<main class="flex justify-center">
    <section class="container-content">
        <section class="lg:grid lg:grid-cols-3 w-full">
            <div class="col-span-2">
                @if($isLogin == false)
                <section class="w-full md:border-x md:border-gray-400 bg-gray-200 h-fit p-7.5">
                    <div class="bg-white p-7.5 flex flex-col items-center">
                        <h1 class="text-lg font-bold pb-4">
                            Tin đã lưu
                        </h1>
                        <p class="text-base pb-5 text-center w-full md:w-2/3 gap-y-2">
                            Đăng nhập để lưu trữ và dễ dàng truy cập những bài viết bạn yêu thích trên Bloomberg Businessweek Việt Nam.
                        </p>
                        <div class="flex justify-center items-center gap-x-2.5 mt-5">
                            <a href="{{ route('oauth.redirect') }}" class="bg-black whitespace-nowrap text-white px-4 py-2 font-bold rounded transition-all duration-300 hover:bg-gray-800 hover:scale-105 transform hover:shadow-lg">
                                Đăng nhập
                            </a>
                            <a href="{{ route('oauth.redirect') }}" class="border whitespace-nowrap border-black font-bold text-black px-4 py-2 rounded transition-all duration-300 hover:bg-gray-100 hover:scale-105 transform hover:shadow-md">
                                Tạo tài khoản
                            </a>
                        </div>
                    </div>
                </section>
                @else
                <section class="w-full md:border-x md:border-gray-400 h-fit p-7.5 flex flex-col gap-y-2.5 md:gap-y-4 ">
                    <h1 class="text-lg font-bold">
                        Tin đã lưu
                    </h1>
                    <p class="text-base w-full">

                        Xem các bài viết bạn đã lưu trên trang web của Bloomberg Businessweek Việt Nam.
                    </p>

                    @forelse ($bookmarks as $bookmarkItem)
                    @php
                    $article = $bookmarkItem['article']['articleInfo'];
                    $bookmarkDate = new DateTime($bookmarkItem['bookmark']['created_at']);
                    $now = new DateTime();
                    @endphp

                    <div class="flex gap-x-4 h-full items-start bg-Gray_03">
                        <div class="flex-1">
                            <a class="hidden md:block w-60 h-full" href="{{ url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') }}">
                                <img
                                    style=" aspect-ratio: 3/2;"
                                    src="{{ $article['Thumbnail_600x315'] }}"
                                    alt="{{ $article['Title'] }}"
                                    class="w-full object-cover"
                                    loading="lazy" />
                            </a>
                        </div>

                        <div class="w-full h-full flex gap-x-4 items-start py-4">
                            <a class="h-full flex flex-col justify-between gap-y-5 w-full" href="{{ url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') }}">
                                <div class="flex flex-col gap-y-1">
                                    <p class="text-sm text-Gray_12 font-medium">{{formatDate($article['Time_yyyyMMddHHmmss'])}}</p>
                                    <h2 class="text-xl font-medium hover:text-Icon05">{{ $article['Title'] }}</h2>
                                </div>
                            </a>
                        </div>
                        <button
                            class="remove-bookmark"
                            data-publisher-id="{{ $article['PublisherId'] }}"
                            aria-label="Bỏ lưu bài viết">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 17C7.24839 10.7516 10.7516 7.24839 17 1M1 1L17 17" stroke="currentColor" stroke-width="1" />
                            </svg>
                        </button>
                    </div>
                    @empty
                    <p class="py-5 text-lg">Bạn chưa lưu bài viết nào.</p>
                    @endforelse

                    {{-- Add this after your @endforelse --}}
                    @if($paginator->hasPages())
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center gap-4">
                            {{-- Previous Page Link --}}
                            @if($paginator->onFirstPage())
                            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                                &larr;
                            </span>
                            @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="p-2 md:px-4 md:py-2 border border-gray-300 rounded hover:bg-gray-100">
                                &larr;
                            </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                            $current = $paginator->currentPage();
                            $last = $paginator->lastPage();
                            $delta = 1; // Number of pages around current page to show
                            $left = $current - $delta;
                            $right = $current + $delta;
                            $range = [];
                            $rangeWithDots = [];

                            // Generate basic range
                            for ($i = 1; $i <= $last; $i++) {
                                if ($i==1 || $i==$last || ($i>= $left && $i <= $right)) {
                                    $range[]=$i;
                                    }
                                    }

                                    // Add dots where gaps exist
                                    $prev=null;
                                    foreach ($range as $i) {
                                    if ($prev !==null && $i - $prev !==1) {
                                    $rangeWithDots[]='...' ;
                                    }
                                    $rangeWithDots[]=$i;
                                    $prev=$i;
                                    }
                                    @endphp

                                    @foreach($rangeWithDots as $i)
                                    @if(is_string($i))
                                    <span class="px-1 md:px-4 py-2">...</span>
                                    @elseif($i == $current)
                                    <span class="px-4 py-2 bg-black text-white rounded">{{ $i }}</span>
                                    @else
                                    <a href="{{ $paginator->url($i) }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">{{ $i }}</a>
                                    @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if($paginator->hasMorePages())
                                    <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                                        &rarr;
                                    </a>
                                    @else
                                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                                        &rarr;
                                    </span>
                                    @endif
                        </nav>
                    </div>
                    @endif
                </section>
                @endif
            </div>
        </section>

        <section class="p-6 lg:py-14 lg:px-7.5 flex justify-center items-center md:border border border-x-0 md:border-b-0 border-gray-400">
            <div class="lg:flex lg:justify-center lg:items-center">
                <div class="flex flex-col lg:items-center gap-[20px] md:gap-[35px]">
                    <p id="newsletter-section" class="font-semibold text-2xl md:text-4xl">
                        Đăng ký nhận bản tin miễn phí
                    </p>

                    <!-- Form Mautic với kiểu dáng đã chỉnh sửa -->
                    <div id="mauticform_wrapper_bbw" class="w-full">
                        <form autocomplete="false" role="form" method="post" action="https://email.beaconasiamedia.vn/form/submit?formId=10"
                            id="mauticform_bbw" data-mautic-form="bbw" enctype="multipart/form-data" class="w-full flex items-center" data-xhr-headers='{"X-Requested-With": "XMLHttpRequest"}'>
                            <div class="mauticform-error" id="mauticform_bbw_error"></div>
                            <div class="mauticform-message" id="mauticform_bbw_message"></div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col md:flex-row gap-y-[20px] md:gap-x-4 lg:mt-0 w-full">
                                    <!-- Email input -->
                                    <div id="mauticform_bbw_email" data-validate="email" data-validation-type="email" class="w-full md:w-[55%]">
                                        <input id="mauticform_input_bbw_email" name="mauticform[email]" value=""
                                            placeholder="Nhập E-mail" type="email"
                                            class="bg-white border border-[#cccccc] text-gray-900 text-sm rounded-[8px] px-2.5 sm:px-6 w-full h-[44px]"
                                            required>
                                    </div>

                                    <!-- reCAPTCHA hidden field -->
                                    <input id="mauticform_input_bbw_recaptcha" name="mauticform[recaptcha]" value="" type="hidden">

                                    <!-- Submit button -->
                                    <button type="submit" name="mauticform[submit]" id="mauticform_input_bbw_submit" value=""
                                        class="whitespace-nowrap w-full md:w-[45%] bg-black text-white text-sm md:text-base px-2.5 sm:px-6 font-semibold rounded-[8px] h-[44px]">
                                        Đăng ký
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden fields -->
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
    document.addEventListener('DOMContentLoaded', function() {
        const bookmarkButtons = document.querySelectorAll('.remove-bookmark');

        bookmarkButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const publisherId = this.getAttribute('data-publisher-id');

                try {
                    const response = await fetch('{{ route("user-behavior.bookmark") }}');

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();

                    // Remove this bookmark from DOM
                    this.closest('article').remove();

                    // Check if there are any bookmarks left
                    const remainingBookmarks = document.querySelectorAll('article').length;
                    if (remainingBookmarks === 0) {
                        // Go to previous page (current page - 1)
                        const url = new URL(window.location.href);
                        let page = parseInt(url.searchParams.get('page') || '1', 10);

                        if (page > 1) {
                            url.searchParams.set('page', page - 1);
                            window.location.href = url.toString();
                        } else {
                            // If it's page 1, just reload
                            window.location.reload();
                        }
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi bỏ lưu bài viết');
                }
            });
        });
    });
</script>
@endsection