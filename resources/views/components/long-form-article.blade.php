<style>
    @import url("https://fonts.googleapis.com/css2?family=IBM+Plex+Serif&display=swap");

    .text-font {
        font-family: 'IBM Plex Serif', serif !important;
    }

    @media screen and (max-width: 768px) {
        .outset-xl {
            padding-inline: 15px !important;
        }
    }

    .entry .sc-longform-header-no-bg-img {
        padding-bottom: unset !important;
    }

    #summarizeList {
        cursor: grab;
        user-select: none;
    }

    #summarizeList.active {
        cursor: grabbing;
    }
</style>
@php
$content = $articleDetail['Content'] ?? '';

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($doc);
$headerNode = $xpath->query('//div[contains(@class, "sc-longform-header")]')->item(0);

$firstPart = '';
$secondPart = '';

if ($headerNode) {
// Save the header HTML
$firstPart = $doc->saveHTML($headerNode);

// Remove the node from the DOM
$headerNode->parentNode->removeChild($headerNode);

// Extract the remaining body content
$bodyNode = $doc->getElementsByTagName('body')->item(0);
$secondPart = '';
foreach ($bodyNode->childNodes as $child) {
$secondPart .= $doc->saveHTML($child);
}
} else {
$secondPart = $content;
}

function formatDate($dateString)
{
$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
if ($date) {
return $date->format('d') . ' tháng ' . $date->format('n') . ', ' . $date->format('Y') . ' lúc ' . $date->format('g:i A');
}
return "Invalid date";
}

@endphp

@php
$currentUrl = request()->fullUrl(); // Or use url()->current() for URL without query params

$shareLink = [
'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($currentUrl),
'LinkedIn' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($currentUrl),
// Add other platforms if needed
];
@endphp

@if($canView === true)
<div class="entry production longform content1">
    @if(!empty($firstPart))
    {!! $firstPart !!}
    @endif

    <section class="flex justify-center flex-col items-start md:items-center gap-y-0" style="margin-block: 1rem !important; margin-inline:1rem; font-family: 'Inter', sans-serif !important; line-height: 1.2 !important; ">
        @if(isset($articleDetail['AuthorAlias']))
        <p class="md:text-xl text-lg font-normal" style="margin: 0 !important;  max-width:unset !important">Tác giả: {{$articleDetail['AuthorAlias']}}</p>
        @endif

        @if(isset($articleDetail['Time_yyyyMMddHHmmss']) && function_exists('formatDate'))
        <p class="md:text-lg text-base text-[#79747E] font-normal" style="margin: unset !important; padding:0 !important; max-width:unset !important; margin-bottom: 1rem !important">{{formatDate($articleDetail['Time_yyyyMMddHHmmss'])}}</p>

        <div class="flex gap-x-3" style="margin-bottom: 1.5rem !important;">
            <x-social-media direction="horizontal" :hasCircle="'true'" :shareLink="$shareLink" />
            <!-- Save Link -->
            <div class="relative flex gap-x-2 items-center">
                <div class="w-9 h-9 rounded-full border border-black flex justify-center items-center cursor-pointer overflow-hidden" onclick="saveLink()">
                    <button id="copy-btn" title="Sao chép liên kết" style="background: transparent; outline: none; border: none; cursor: pointer">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7.33301 10.9999H14.6663M8.24967 7.33325H5.49967C4.52721 7.33325 3.59458 7.71956 2.90695 8.40719C2.21932 9.09483 1.83301 10.0275 1.83301 10.9999C1.83301 11.9724 2.21932 12.905 2.90695 13.5926C3.59458 14.2803 4.52721 14.6666 5.49967 14.6666H8.24967M13.7497 7.33325H16.4997C17.4721 7.33325 18.4048 7.71956 19.0924 8.40719C19.78 9.09483 20.1663 10.0275 20.1663 10.9999C20.1663 11.9724 19.78 12.905 19.0924 13.5926C18.4048 14.2803 17.4721 14.6666 16.4997 14.6666H13.7497"
                                stroke="black"
                                stroke-width="2.29167"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <!-- Popover -->
                <div id="popover-section"
                    class="absolute left-full z-20 hidden">
                    <div class="flex items-center gap-2 p-3 text-nowrap rounded-lg bg-gray-800 text-white shadow-lg top-[-1.25rem]" style="margin-left:1rem; padding-inline:1rem">
                        <!-- Triangle Pointer -->
                        <div class="rotate-90 absolute top-1/2 left-[4px] transform -translate-y-1/2 w-0 h-0 border-l-[8px] border-r-[8px] border-t-[8px] border-t-gray-800 border-l-transparent border-r-transparent"></div>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11" stroke="#449e80" stroke-width="2" />
                            <path d="M8 12.5L11 15.5L16 9.5" stroke="#449e80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-sm" style="margin-block: 0.5rem !important;">Đã copy</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-x-2.5">
            <!-- LƯU BÀI VIẾT -->
            @if ($isSave !== null)
            <button
                id="saveButton"
                style="
                padding-top: 0.25rem !important;
                padding-bottom: 0.25rem !important;
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
                border-radius: 1.5rem !important;
                display: flex !important;
                align-items: center !important;
                border: 1px solid black !important;
                background-color: white !important;
                transition: color 0.3s, background-color 0.3s !important;
                position: relative !important;
                ">
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                    <path id="bookmarkIcon" fill={{ $isSave === true ? 'black':'white' }} stroke="black" stroke-width="1" d="M6 2C5.44772 2 5 2.44772 5 3V21.382C5 21.7645 5.42458 21.9875 5.76537 21.7932L12 18.118L18.2346 21.7932C18.5754 21.9875 19 21.7645 19 21.382V3C19 2.44772 18.5523 2 18 2H6Z" />
                </svg>
                <span id="buttonText" style="color: black; font-weight:200 !important">{{ $isSave === true ? 'Đã Lưu' : 'Lưu'}}</span>
            </button>
            @else
            <button
                id="saveButtonWhenNotLogin"
                style="
                padding-top: 0.25rem !important;
                padding-bottom: 0.25rem !important;
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
                border-radius: 1.5rem !important;
                display: flex !important;
                align-items: center !important;
                border: 1px solid black !important;
                background-color: white !important;
                transition: color 0.3s, background-color 0.3s !important;
                position: relative !important;
                ">
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                    <path id="bookmarkIcon" fill='white' stroke="black" stroke-width="1" d="M6 2C5.44772 2 5 2.44772 5 3V21.382C5 21.7645 5.42458 21.9875 5.76537 21.7932L12 18.118L18.2346 21.7932C18.5754 21.9875 19 21.7645 19 21.382V3C19 2.44772 18.5523 2 18 2H6Z" />
                </svg>
                <span id="buttonText" class="text-black font-extralight">Lưu</span>
            </button>
            @endif

            <!-- NGHE BÀI VIẾT -->
            @if($audio !== null)
            @if($isLogin)
            <div>
                <button onclick="playAudio()" id="playAudioButton"
                    class="relative py-1 px-7 rounded-3xl  bg-white transition-colors duration-300 mb-5 gap-x-2.5" style="border: 1px solid black; color: black; display: flex; align-items: center; ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 12 12" fill="none">
                        <path d="M1 9.33333V6C1 4.67392 1.52678 3.40215 2.46447 2.46447C3.40215 1.52678 4.67392 1 6 1C7.32608 1 8.59785 1.52678 9.53553 2.46447C10.4732 3.40215 11 4.67392 11 6V9.33333M11 9.88889C11 10.1836 10.8829 10.4662 10.6746 10.6746C10.4662 10.8829 10.1836 11 9.88889 11H9.33333C9.03865 11 8.75603 10.8829 8.54766 10.6746C8.33929 10.4662 8.22222 10.1836 8.22222 9.88889V8.22222C8.22222 7.92754 8.33929 7.64492 8.54766 7.43655C8.75603 7.22817 9.03865 7.11111 9.33333 7.11111H11V9.88889ZM1 9.88889C1 10.1836 1.11706 10.4662 1.32544 10.6746C1.53381 10.8829 1.81643 11 2.11111 11H2.66667C2.96135 11 3.24397 10.8829 3.45234 10.6746C3.66071 10.4662 3.77778 10.1836 3.77778 9.88889V8.22222C3.77778 7.92754 3.66071 7.64492 3.45234 7.43655C3.24397 7.22817 2.96135 7.11111 2.66667 7.11111H1V9.88889Z" stroke="#1E1E1E" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Nghe bài viết
                    <p class="text-gray-400 font-light" id="audioDuration" style="margin: 0;"></p>
                </button>

                <!-- Hidden audio player -->
                <div id="audioContainer" style="display: none; ">
                    <audio id="audioPlayer" controls class="w-50 md:w-56" style="height: 45px;" controlsList="nodownload noplaybackrate nofullscreen">
                        <source src={{ $audio }} type="audio/mpeg">
                    </audio>
                </div>
            </div>
            @else
            <button id="playAudioButtonWhenNotLogin"
                class="relative py-1 px-7 rounded-3xl  bg-white transition-colors duration-300 mb-5 gap-x-2.5" style="border: 1px solid black; color: black; display: flex; align-items: center; ">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 12 12" fill="none">
                    <path d="M1 9.33333V6C1 4.67392 1.52678 3.40215 2.46447 2.46447C3.40215 1.52678 4.67392 1 6 1C7.32608 1 8.59785 1.52678 9.53553 2.46447C10.4732 3.40215 11 4.67392 11 6V9.33333M11 9.88889C11 10.1836 10.8829 10.4662 10.6746 10.6746C10.4662 10.8829 10.1836 11 9.88889 11H9.33333C9.03865 11 8.75603 10.8829 8.54766 10.6746C8.33929 10.4662 8.22222 10.1836 8.22222 9.88889V8.22222C8.22222 7.92754 8.33929 7.64492 8.54766 7.43655C8.75603 7.22817 9.03865 7.11111 9.33333 7.11111H11V9.88889ZM1 9.88889C1 10.1836 1.11706 10.4662 1.32544 10.6746C1.53381 10.8829 1.81643 11 2.11111 11H2.66667C2.96135 11 3.24397 10.8829 3.45234 10.6746C3.66071 10.4662 3.77778 10.1836 3.77778 9.88889V8.22222C3.77778 7.92754 3.66071 7.64492 3.45234 7.43655C3.24397 7.22817 2.96135 7.11111 2.66667 7.11111H1V9.88889Z" stroke="#1E1E1E" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Nghe bài viết
                <p class="text-gray-400 font-light" id="audioDuration" style="margin: 0;"></p>
            </button>
            @endif
            @endif
        </div>

        @if ($summarize !== null)
        <section class=" bg-[#f0f0f0]  mt-2 overflow-hidden" style="max-width: 600px; width:100%; border-radius: 0.5rem; margin-top:1.5rem; padding-bottom:0.5rem">
            <section style="display: flex; justify-content: space-between; align-items: center; padding-inline:1rem !important; padding-top:1rem">
                <div class="flex items-center gap-x-2">
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="20" height="19" fill="url(#pattern0_2090_4765)" />
                        <defs>
                            <pattern id="pattern0_2090_4765" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use xlink:href="#image0_2090_4765" transform="matrix(0.00848214 0 0 0.00892857 -0.000446429 0)" />
                            </pattern>
                            <image id="image0_2090_4765" width="118" height="112" preserveAspectRatio="none" xlink:href="data:image/png;base64,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" />
                        </defs>
                    </svg>
                    <p class="font-bold text-lg md:text-xl text-font" style="margin: 0 !important; padding:0 !important">
                        Tóm tắt bài viết
                    </p>
                </div>
                <button id="toggleButton" class="flex gap-x-1 items-center hover:underline" style="height:unset; padding: 0; margin: 0;background: transparent; outline: none; border: none; cursor: pointer">
                    <span id="toggleText" class="text-font">Ẩn</span>
                    <svg id="toggleIcon" style="transform: rotate(180deg);" class="transition-transform duration-300" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </section>

            <div id="AISummarize" style="max-height: 600px; padding-bottom:0.5rem;" class="opacity-100 overflow-hidden transition-all duration-500 ease-in-out">
                <section id="summarizeList" class=" flex gap-x-5 mt-3 overflow-x-auto scroll-hidden " style="margin-top:0.5rem; padding-inline: 1.25rem;">
                    @foreach ($summarize as $index => $item)
                    <div class="border border-gray-500 rounded-lg bg-white item-card"
                        style=" flex: shrink 0; height:100%; padding:0.625rem; min-width: 240px; max-width: 240px; height: 280px; display: flex; flex-direction: column; justify-content: space-between;"
                        data-index="{{ $index }}>
                        <p style=" line-height: 1; font-size:large; " class=" text-font">{{$item}}</p>
                    </div>
                    @endforeach
                </section>

                <section class=" lg:flex justify-between hidden">
                    <div></div>
                    <div id="dotSummarize" style="display: flex; justify-content: center; align-items: center; gap: 0.625rem; margin-top: 1.25rem !important;">
                        <?php foreach ($summarize as $index => $summarizeItem): ?>
                            <button style="width:  0.625rem !important; height: 0.625rem !important; border-radius:999px; padding: 0" data-index="<?= $index ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div>
                    </div>
                </section>
            </div>
        </section>
        @endif
        <!-- @endif -->
    </section>

    @if(!empty($secondPart))
    {!! $secondPart !!}
    @endif
</div>
@else
@php
$hidecontentPremium = sanitizeHtml($hidecontentPremium);
@endphp

<div class="relative">
    <div class="entry production longform content1">
        {!! $hidecontentPremium ?? '' !!}
    </div>

    <div style="background:  linear-gradient(to top, white, rgba(255, 255, 255, 0.5), transparent); pointer-events: none;" class="absolute bottom-0 w-full h-full">
    </div>
</div>
@endif

<div class="flex justify-center">
    <div class="w-[630px] px-[15px]">
        <section class="flex justify-end mb-7.5 ">
            <div>
                <div class="pb-4">
                    @if ($articleDetail['SourceType'] === 11 || $articleDetail['SourceType'] === 12)
                    <p class="text-gray italic text-lg">
                        Theo Bloomberg
                    </p>
                    @endif
                </div>
                <div class="md:px-[15px]">
                    <section class="w-full relative flex flex-wrap justify-end mb-7 text-right">
                        <p class="italic text-right w-full" style="color:#b5b5b5ee;font-size: 10px; line-height:1.2 !important; margin: 0 !important;">Theo phattrienxanh.baotainguyenmoitruong.vn</p>
                        <p
                            style="color:#b5b5b5ee;font-size: 10px; line-height:1.2 !important; margin: 0 !important;"
                            class="text-right w-full italic break-all">
                            https://phattrienxanh.baotainguyenmoitruong.vn{{ request()->getPathInfo() }}
                        </p>
                    </section>
                </div>
            </div>
        </section>

        <!-- HANDLE KEYWORD -->
        @if (!empty($articleDetail['Keyword']))
        @php
        // Convert keywords into an array for processing
        $keywords = explode(",", $articleDetail['Keyword']);
        @endphp

        <!-- Skip rendering if there's only one keyword and it's 'Premium' -->
        @if (!(count($keywords) === 1 && trim($keywords[0]) === 'Premium'))
        <section class="flex flex-wrap gap-y-3 gap-x-3 pb-7 pt-7.5 border-t border-[#B2B2B2] border-dashed">
            @foreach ($keywords as $keyword)
            @if (trim($keyword) !== 'Premium')
            <a href="/search?q={{ trim($keyword) }}">
                <div class="p-2 rounded-full text-xs border border-black py-2.5 px-5 text-nowrap font-semibold hover:bg-black hover:text-white">
                    #{{ trim($keyword) }}
                </div>
            </a>

            @endif
            @endforeach
        </section>
        @endif
        @endif
    </div>
</div>

<!-- WHEN SAVE OR UNSAVE TOAST -->
@if ($isSave !== null)
<section id="toast-announce" class=" fixed bottom-3 left-1/2 translate-x-[-50%] z-50  flex items-center bg-black py-5 px-7.5 gap-x-7.5 rounded-lg">
    <div class="text-white flex gap-x-2 items-center">
        <svg width="25" height="25" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="45" stroke="white" stroke-width="8" fill="none" />
            <path d="M30 50 L45 65 L70 35" stroke="white" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p class="text-lg" id="toast-text">
            Đã Lưu
        </p>
    </div>
    <a href="/saved" id="toast-all" class="relative inline-block text-white after:absolute after:left-0 after:bottom-0 after:h-[0.5px] after:opacity-50 after:w-full after:bg-white after:content-['']">
        Xem các tin đã lưu
    </a>
    <button id="closeToastBtn">
        <svg width=" 18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 17C7.24839 10.7516 10.7516 7.24839 17 1M1 1L17 17" stroke="white" stroke-width="2" />
        </svg>
    </button>
</section>
@endif

<!-- WHEN SAVE NOT LOGIN -->
<section id="toastRequireLogin" class="hidden fixed top-0 w-screen h-full bg-black bg-opacity-50 justify-center items-center " style="z-index: 1000;">
    <div class="bg-white p-7.5 md:mx-0 mx-4 md:w-3/5 lg:w-1/3 w-full flex flex-col items-center rounded relative">
        <p class="font-bold text-base md:text-xl mb-2">Khám phá nhiều hơn với tài khoản</p>
        <p class="text-sm md:text-base pb-2.5 text-center w-full gap-y-2 " style="line-height: 1.2;" id="textDescription">
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

        <!-- CLOSE BUTTON  -->
        <button onclick="document.getElementById('toastRequireLogin').classList.add('hidden')" class="absolute top-1 right-0 mr-2 p-2">
            <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 17C7.24839 10.7516 10.7516 7.24839 17 1M1 1L17 17" stroke="currentColor" stroke-width="2" />
            </svg>
        </button>
    </div>
</section>


<script>
    function saveLinkPTX() {
        const ptxURL = "https://phattrienxanh.baotainguyenmoitruong.vn"
        const currentPageUrl = window.location.pathname;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            // Use Clipboard API if supported
            navigator.clipboard.writeText(ptxURL + currentPageUrl)
            const popover = document.getElementById("popover-section-below");

            if (popover) {
                popover.classList.remove("hidden");
                setTimeout(() => {
                    popover.classList.add("hidden");
                }, 2000); // 2 seconds delay
            } else {
                console.error('Popover element not found');
            }
        }
    }

    function saveLink() {
        const currentPageUrl = window.location.href;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            // Use Clipboard API if supported
            navigator.clipboard.writeText(currentPageUrl)
                .then(() => {
                    showPopover();
                })
                .catch(err => {
                    console.error('Failed to copy link: ', err);
                });
        } else {
            // Fallback for unsupported environments
            const textarea = document.createElement("textarea");
            textarea.value = currentPageUrl;
            textarea.style.position = "absolute";
            textarea.style.left = "-9999px"; // Hide off-screen
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand("copy");
                showPopover();
            } catch (err) {
                console.error('Failed to copy link using fallback: ', err);
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }

    function showPopover() {
        const popover = document.getElementById("popover-section");

        if (popover) {
            popover.classList.remove("hidden");
            setTimeout(() => {
                popover.classList.add("hidden");
            }, 2000); // 2 seconds delay
        } else {
            console.error('Popover element not found');
        }
    }
</script>

<script>
    const isSave = @json($isSave);
    if (isSave !== null) {
        // Initialize the toast element and its components
        const publisherId = String(@json($articleDetail['PublisherId']));
        const toast = document.getElementById('toast-announce');
        const toastText = document.getElementById('toast-text');
        const toastAll = document.getElementById('toast-all');
        const closeToastBtn = document.getElementById('closeToastBtn');

        toast.classList.add('hidden');

        document.getElementById('saveButton').addEventListener('click', async function() {
            const button = this;
            const icon = document.getElementById('bookmarkIcon');
            const buttonText = document.getElementById('buttonText');

            // Disable button during request
            button.disabled = true;

            try {
                const response = await fetch('{{ route("user-behavior.bookmark") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        publisherId: publisherId,
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                // Toggle bookmark status
                const isSaved = buttonText.textContent === 'Lưu';

                icon.style.fill = isSaved ? 'black' : 'white';
                buttonText.textContent = isSaved ? 'Đã Lưu' : 'Lưu';

                // Show toast
                toast.classList.remove('hidden');
                toastText.textContent = isSaved ? 'Đã Lưu' : 'Bỏ Lưu';
                toastAll.classList.toggle('hidden', !isSaved);

                // Hide toast after 3 seconds
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);

            } catch (error) {
                console.error('Error:', error);
                icon.style.fill = '#EF4444';
                buttonText.textContent = 'Lỗi!';
                setTimeout(() => {
                    buttonText.textContent = 'Lưu';
                    icon.style.fill = 'currentColor';
                }, 1500);
            } finally {
                setTimeout(() => {
                    button.disabled = false;
                }, 1500);
            }
        });

        closeToastBtn.addEventListener('click', () => {
            toast.classList.add('hidden');
        });
    }
</script>

<script>
    const modal = document.getElementById('toastRequireLogin');

    if (document.getElementById('playAudioButtonWhenNotLogin'))
        document.getElementById('playAudioButtonWhenNotLogin').addEventListener('click', function() {
            console.log("clickkkkkkkkk")
            document.getElementById("textDescription").textContent =
                "Đăng nhập để lắng nghe và không bỏ lỡ bất kỳ bài viết yêu thích nào trên Bloomberg Businessweek Việt Nam";
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });

    if (document.getElementById('saveButtonWhenNotLogin'))
        document.getElementById('saveButtonWhenNotLogin').addEventListener('click', function() {
            document.getElementById("textDescription").textContent =
                "Đăng nhập để lưu trữ và dễ dàng truy cập những bài viết bạn yêu thích trên Bloomberg Businessweek Việt Nam";
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
</script>

<script>
    const audio = document.getElementById('audioPlayer');
    if (audio) {

        const durationEl = document.getElementById('audioDuration');
        // Convert seconds to mm:ss
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        }

        // Load real duration
        audio.addEventListener('loadedmetadata', () => {
            durationEl.textContent = formatTime(audio.duration);
        });

        function playAudio() {
            // Show the audio player
            document.getElementById('audioContainer').style.display = 'block';
            document.getElementById('playAudioButton').style.display = 'none';

            // Play the audio
            var audio = document.getElementById('audioPlayer');
            audio.play();
        }
    }
</script>

<script>
    const list = document.getElementById('summarizeList');
    const buttons = document.querySelectorAll('#dotSummarize button');
    let activeIndex = 0;

    function updateActiveDot(newIndex) {
        buttons.forEach((button, index) => {
            if (index === newIndex) {
                button.style.backgroundColor = 'black';
            } else {
                button.style.backgroundColor = '#D1D5DB'; // Tailwind's bg-gray-300
            }
        });

        const cards = document.querySelectorAll('.item-card');
        cards.forEach((card, index) => {
            if (index === newIndex) {
                card.classList.add('border-gray-500');
                card.classList.remove('border-transparent');
            } else {
                card.classList.remove('border-gray-500');
                card.classList.add('border-transparent');
            }
        });

        activeIndex = newIndex;
    }

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.getAttribute('data-index'));
            const itemWidth = 240 + 20; // item width + gap
            const listWidth = list.clientWidth;
            const scrollPosition = index * itemWidth - (listWidth / 2) + (itemWidth / 2);

            list.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
            });

            updateActiveDot(index);
        });
    });

    // Initialize first active dot
    updateActiveDot(activeIndex);

    // Mouse sliding logic
    let isDown = false;
    let startX;
    let scrollLeft;

    list.addEventListener('mousedown', (e) => {
        isDown = true;
        list.classList.add('active');
        startX = e.pageX - list.offsetLeft;
        scrollLeft = list.scrollLeft;
    });

    list.addEventListener('mouseleave', () => {
        isDown = false;
        list.classList.remove('active');
    });

    list.addEventListener('mouseup', () => {
        isDown = false;
        list.classList.remove('active');
    });

    list.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - list.offsetLeft;
        const walk = (x - startX) * 1.5;
        list.scrollLeft = scrollLeft - walk;
    });

    // Update dots while scrolling
    list.addEventListener('scroll', () => {
        const itemWidth = 240 + 20;
        const scrollLeftCenter = list.scrollLeft + (list.clientWidth / 2);
        let newIndex = Math.round(scrollLeftCenter / itemWidth - 0.5);

        // Detect scroll to start
        if (list.scrollLeft <= 0) {
            newIndex = 0;
        }
        // Detect scroll to end
        else if (list.scrollLeft + list.clientWidth >= list.scrollWidth - 1) {
            newIndex = buttons.length - 1;
        }

        if (newIndex !== activeIndex && newIndex >= 0 && newIndex < buttons.length) {
            updateActiveDot(newIndex);
        }
    });
</script>

<script>
    const toggleButton = document.getElementById('toggleButton');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');
    const AISummarize = document.getElementById('AISummarize');

    toggleButton.addEventListener('click', () => {
        const isShown = AISummarize.style.maxHeight !== '0px' && AISummarize.style.maxHeight !== '0';

        if (isShown) {
            // Ẩn
            AISummarize.style.maxHeight = '0';
            AISummarize.style.opacity = '0';
            toggleText.textContent = 'Hiện';
            toggleIcon.style.transform = 'rotate(0deg)';
        } else {
            // Hiện
            AISummarize.style.maxHeight = '600px'; // Tùy chỉnh cao tối đa (600px tương đương 24rem)
            AISummarize.style.opacity = '1';
            toggleText.textContent = 'Ẩn';
            toggleIcon.style.transform = 'rotate(180deg)';
        }
    });
</script>