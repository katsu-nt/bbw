@extends('layouts.app')
@section('content')
<?php
// if ($isPremiumContent) {
//     if ($isPremiumUser)
//         $canView = true;
//     else {
//         if ($isLogin) {
//             if ($articleList) {
//                 if (count($articleList) !== 7)
//                     $canView =  true;
//                 else $canView =  false;
//             } else $canView = true;
//         } else {
//             if ($articleList) {
//                 if (count($articleList) !== 5)
//                     $canView =  true;
//                 else $canView =  false;
//             } else
//                 $canView = true;
//         };
//     }
// } else {
//     $canView = true;
// }

// if ($isPremiumContent) {
//     if ($isPremiumUser)
//         $canView = true;
//     else {
//         $canView = false;
//     }
// } else {
//     $canView = true;
// }

// // Logic hạn chế số lượng bài đọc cho user không đăng nhập
// if (!$isLogin && !$isPremiumContent) {
//     if (count($articleList) > 3)
//         $canView = false;
//     else
//         $canView = true;
// }

if ($isLogin)
    $canView = true;
else
    $canView = false;

function sanitizeHtml($html)
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    return $dom->saveHTML($dom->documentElement);
}

//DATA ARTICLE CURRENT
$articlePublisherId = $article['PublisherId'];
$articleContent = $article['Content'];



?>

<main>
    <section>
        @if ($article['TypeId'] == 1)
        <!-- Standard Article -->
        <div class="onecms__detail">
            <x-standard-article
                :articleDetail="$article"
                :articleTopReadList="$topReadArticles"
                :hidecontentPremium="$hidecontentPremium"
                :canView="$canView"
                :isPremiumContent="$isPremiumContent"
                :isSave="$isSave"
                :isLogin="$isLogin"
                :audio="$audio"
                :summarize="$summarize" />
        </div>
        @elseif($article['TypeId'] == 6)
        <x-long-form-upload-article
            :articleDetail="$article"
            :canView="$canView" />
        @else
        <!-- Long Form Article -->
        <div class="onecms__detail">
            <x-long-form-article
                :articleDetail="$article"
                :hidecontentPremium="$hidecontentPremium"
                :canView="$canView"
                :isSave="$isSave"
                :audio="$audio"
                :summarize="$summarize"
                :isLogin="$isLogin" />
            @endif
        </div>
    </section>


    <!-- RELATED ARTICLES SECTION -->
    @if (count($relatedArticles) > 0)
    <section class="flex justify-center items-center">
        <div class="container-content md:border-0 border-t border-gray-400">
            <div class="p-5 lg:p-7.5 {{ $article['TypeId'] == 1 ? 'md:border md:border-b-0 border-t md:border-gray-400' : '' }}">
                <p class="font-bold text-lg mb-3">BÀI LIÊN QUAN</p>
                <div class="grid grid-cols-2 lg:grid-cols-4 lg:gap-x-6 md:gap-x-5 gap-x-3 gap-y-3">
                    @foreach ($relatedArticles as $index => $article)
                    <div class="w-full">
                        <x-card-img-title
                            :title="$article['Title']"
                            :image="$article['Thumbnail_1050x700']"
                            :widthImage="'w-full'"
                            :heightImage="'h-auto'"
                            :breakline="'true'"
                            :url="url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')"
                            fontSize="md:text-xl text-sm" />
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="flex justify-center items-center">
        <div class="container-content">
            <section class="bg-[#f7f7f7] py-5 md:py-7.5 border-t md:border-x border-gray-400 flex justify-center items-center">
                <div class="lg:flex px-6 lg:px-7.5 justify-center items-center h-full w-full">
                    <div id="zone-7" class=" lg:flex lg:justify-center lg:items-center lg:border-t-0 w-full relative" style="aspect-ratio: 970/250;">
                        <!-- Skeleton Container - thêm absolute và z-index cao -->
                        <div class="skeleton-container absolute inset-0 z-20">
                            <x-skeleton containerStyle="h-full w-full">
                            </x-skeleton>
                        </div>
                    </div>
                </div>
            </section>
            <section class="p-5 lg:py-14 lg:px-7.5 flex justify-center items-center md:border border border-x-0 md:border-b-0 border-gray-400">
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
                                        <div id="mauticform_bbw_email" data-validate="email" data-validation-type="email" class="w-full md:w-[65%]">
                                            <input id="mauticform_input_bbw_email" name="mauticform[email]" value=""
                                                placeholder="Nhập E-mail" type="email"
                                                class="bg-white border border-[#cccccc] text-gray-900 text-sm rounded-[8px] px-2.5 sm:px-6 w-full h-[44px]"
                                                required>
                                        </div>

                                        <!-- reCAPTCHA hidden field -->
                                        <input id="mauticform_input_bbw_recaptcha" name="mauticform[recaptcha]" value="" type="hidden">

                                        <!-- Submit button -->
                                        <button type="submit" name="mauticform[submit]" id="mauticform_input_bbw_submit" value=""
                                            class="whitespace-nowrap w-full md:w-[35%] bg-black text-white text-sm md:text-base px-2.5 sm:px-6 font-semibold rounded-[8px] h-[44px]">
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
        </div>
    </section>

    @if (!$canView)
    <!-- REGISTER SUBSCRIBER SECTION SHORT -->
    <section id="short-section" class="fixed bottom-0  w-full bg-black flex justify-center md:py-7.5 py-6 z-40">
        <div class="flex flex-col md:flex-row  items-center">
            <p class="text-white font-normal opacity-80 text-lg">
                <!-- Tiết kiệm ngay với ưu đãi đặc biệt. -->
                Đăng nhập ngay để xem đầy đủ nội dung hấp dẫn!
                &nbsp;
            </p>

            <div class="flex  items-center hover:underline">
                <a class="font-bold text-white text-lg" href="{{ route('oauth.redirect') }}">
                    <!-- Khám phá ngay ưu đãi -->
                    Đăng nhập
                </a>

                <svg width="31" height="30" viewBox="0 0 31 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.9 14.525C22.8405 14.3715 22.7513 14.2314 22.6375 14.1125L16.3875 7.86249C16.271 7.74594 16.1326 7.65349 15.9803 7.59041C15.828 7.52734 15.6648 7.49487 15.5 7.49487C15.1671 7.49487 14.8479 7.62711 14.6125 7.86249C14.496 7.97904 14.4035 8.1174 14.3404 8.26968C14.2773 8.42195 14.2449 8.58516 14.2449 8.74999C14.2449 9.08286 14.3771 9.40211 14.6125 9.63749L18.7375 13.75H9.25C8.91848 13.75 8.60054 13.8817 8.36612 14.1161C8.1317 14.3505 8 14.6685 8 15C8 15.3315 8.1317 15.6495 8.36612 15.8839C8.60054 16.1183 8.91848 16.25 9.25 16.25H18.7375L14.6125 20.3625C14.4953 20.4787 14.4023 20.6169 14.3389 20.7693C14.2754 20.9216 14.2428 21.085 14.2428 21.25C14.2428 21.415 14.2754 21.5784 14.3389 21.7307C14.4023 21.883 14.4953 22.0213 14.6125 22.1375C14.7287 22.2546 14.867 22.3476 15.0193 22.4111C15.1716 22.4746 15.335 22.5072 15.5 22.5072C15.665 22.5072 15.8284 22.4746 15.9807 22.4111C16.133 22.3476 16.2713 22.2546 16.3875 22.1375L22.6375 15.8875C22.7513 15.7686 22.8405 15.6284 22.9 15.475C23.025 15.1707 23.025 14.8293 22.9 14.525Z" fill="white" />
                </svg>
            </div>

            <!-- Close Button -->
            <button onclick="closeShortSection()" class="text-3xl absolute top-0 right-0 px-7.5 py-2.5 text-white bg-transparent rounded-full">
                &times;
            </button>
        </div>
    </section>

    <!-- REGISTER SUBSCRIBER SECTION LONG -->
    <section id="long-section" class="fixed bottom-0 w-full bg-black md:p-10 p-8 text-center text-white flex flex-col items-center gap-y-5 z-40">
        <p class="md:text-lg text-base">
            Bạn cần đăng nhập để khám phá toàn bộ nội dung
            <!-- Đây là bài viết có tính phí. Đăng ký ngay để trải nghiệm quyền truy cập không giới hạn. -->
        </p>
        <p class=" font-bold md:text-2xl text-lg">
            <!-- Nhận ưu đãi đặc biệt ngay hôm nay! -->
            Mở khóa toàn bộ nội dung chỉ với một thao tác đơn giản!
        </p>
        <a target="_blank"
            href="{{ route('oauth.redirect') }}"
            class=" px-5 py-2.5 bg-white text-black font-semibold w-fit hover:bg-primary rounded">Đăng Nhập</a>
        <!-- Close Button -->
        <button onclick="closeLongSection()" class="md:text-3xl text-2xl absolute top-0 right-0 md:px-7.5 py-2.5 px-2.5 text-white bg-transparent rounded-full">
            &times;
        </button>
    </section>
    @else
    <section id="short-section"></section>
    <section id="long-section"></section>
    @endif

</main>

<script>
    let sectionsClosed = false; // Flag to track if sections are closed

    window.addEventListener('scroll', function() {
        if (sectionsClosed) return; // Prevent further toggling if sections are closed

        // Get the current scroll position
        let scrollPosition = window.scrollY;

        // Get references to the two sections
        let shortSection = document.getElementById('short-section');
        let longSection = document.getElementById('long-section');

        // Check the scroll position and toggle the sections with CSS animations
        if (scrollPosition > 100) { // Adjust this value as needed
            // Apply animation to hide the short section and show the long section
            shortSection.style.animation = 'hideShort 0.5s ease-in-out forwards';
            longSection.style.display = 'flex';
            longSection.style.animation = 'showLong 0.5s ease-in-out forwards';
            longSection.style.zIndex = '40';
            shortSection.style.zIndex = '30';
        } else {
            // Apply animation to hide the long section and show the short section
            longSection.style.animation = 'hideLong 0.5s ease-in-out forwards';
            shortSection.style.display = 'flex';
            shortSection.style.animation = 'showShort 0.5s ease-in-out forwards';
            longSection.style.zIndex = '30';
            shortSection.style.zIndex = '40';
        }
    });

    function closeShortSection() {
        // Hide the short section and set the closed flag
        let shortSection = document.getElementById('short-section');
        shortSection.style.display = 'none';
        sectionsClosed = true; // Prevent sections from toggling back
    }

    function closeLongSection() {
        // Hide the long section and set the closed flag
        let longSection = document.getElementById('long-section');
        longSection.style.display = 'none';
        sectionsClosed = true; // Prevent sections from toggling back
    }
</script>

<div
    id="gtm-article-data"
    data-publisher="{{ $article['PublisherId'] ?? '' }}"
    data-keyword="{{ $article['Keyword'] ?? '' }}">
</div>

@endsection

@push('head')

<!-- GOOGLE SEARCH STRUCTURED DATA FOR ARTICLE -->
<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "NewsArticle",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}"
        },
        "headline": "{{ $metadata['title'] }}",
        "description": "{{ $metadata['description'] }}",
        "image": {
            "@type": "ImageObject",
            "url": "{{ $metadata['og_image'] }}",
            "width": 900,
            "height": 540
        },
        "datePublished": "",
        "dateModified": "",
        "author": {
            "@type": "Person",
            "name": "{{ $metadata['author'] }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Bloomberg Businessweek VietNam",
            "logo": {
                "@type": "ImageObject",
                "url": "",
                "width": 70,
                "height": 70
            }
        }
    }
</script>
<!-- GOOGLE BREADCRUMB STRUCTURED DATA -->
<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "item": {
                "@id": "https://bbw.vn",
                "name": "Trang chủ"
            }
        }, {
            "@type": "ListItem",
            "position": 2,
            "item": {
                "@id": "{{ $metadata['cateslug'] }}",
                "name": "{{ $metadata['namechannel'] }}"
            }
        }]
    }
</script>
<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "Organization",
        "name": "Bloomberg Businessweek VietNam",
        "url": "https://bbw.vn",
        "logo": "asset('images/logo-bbw.png')",
        "email": "mailto: bientap@bloombergbusinessweek.vn",
        "sameAs": [],
        "contactPoint": [{
            "@type": "ContactPoint",
            "telephone": "028 8889 0868",
            "contactType": "customer service"
        }],
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "3",
            "addressRegion": "Hồ Chí Minh",
            "addressCountry": "Việt Nam",
            "postalCode": "700000",
            "streetAddress": "Lầu 12A, số 412 Nguyễn Thị Minh Khai, phường 5, Quận 3, Thành phố Hồ Chí Minh"
        }
    }
</script>
@endpush