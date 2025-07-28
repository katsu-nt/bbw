<style>
    @import url("https://fonts.googleapis.com/css2?family=IBM+Plex+Serif&display=swap");


    .text-font {
        font-family: 'IBM Plex Serif', serif;
    }

    .entry figure {
        max-width: unset !important;
    }

    audio::-webkit-media-controls-timeline {
        padding: 0.5rem !important
    }

    audio::-webkit-media-controls-panel {
        padding-inline: 5px !important;
    }

    audio::-webkit-media-controls-panel {
        padding-inline: 5px !important;
    }

    @media screen and (max-width: 1024px) {
        audio::-webkit-media-controls-volume-control-container {
            display: none !important;
        }
    }

    #summarizeList {
        cursor: grab;
        user-select: none;
    }

    #summarizeList.active {
        cursor: grabbing;
    }
</style>

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

@php
$currentUrl = request()->fullUrl(); // Or use url()->current() for URL without query params

$shareLink = [
'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($currentUrl),
'LinkedIn' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($currentUrl),
// Add other platforms if needed
];
@endphp

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

<!-- HANDLE FIRST CONTENT -->
<div class="flex justify-center">
    <section class="container-content container-content-phone">
        <div class="xl:grid xl:grid-cols-4 w-full lg:py-7.5 py-5 md:border-x md:border-gray-400">
            <!-- TWO COLUMN -->
            <section class="col-span-4 xl:grid xl:grid-cols-4 xl:px-0 md:px-7.5">
                <section class="col-span-1">
                    <!-- First Column: SHOW SOCIAL MEDIA -->
                    <div class=" h-full hidden xl:block">
                        @if ($isPremiumContent)
                        <p class="text-xl text-darkYellow ml-7.5 tracking-[.2em]">PREMIUM</p>
                        @endif
                        <p class="font-bold text-2xl ml-7.5">{{mb_strtoupper($articleDetail['NameChannel'])}}</p>
                        @if ($articleDetail['Keyword'] === 'Triển vọng 2025')
                        <p class="text-2xl text-[#B2B2B2] ml-7.5 font-500 mt-1">TRIỂN VỌNG 2025</p>
                        @endif
                    </div>
                </section>
                <section class="col-span-3">
                    <div class="xl:grid xl:grid-cols-5">
                        <div class="col-span-4">
                            <p class="font-bold text-base md:text-2xl md:mb-5 mb-3 xl:hidden block">{{$articleDetail['NameChannel']}}</p>
                            <h2 class="font-bold md:text-5xl text-2xl">{{$articleDetail['Title']}}</h2>
                            <p class="md:text-xl text-base md:mt-7.5 mt-6">{{$articleDetail['Headlines']}}</p>
                            <!-- SOCIAL SECTION -->
                            <div class="mt-6 xl:hidden block">
                                <x-social-media direction="horizontal" :hasCircle="'true'" :shareLink="$shareLink" />
                            </div>
                        </div>
                    </div>
                </section>
            </section>

            <section class="col-span-3 xl:grid xl:grid-cols-3">
                <section class="col-span-3 xl:grid xl:grid-cols-3">
                    <div class="col-span-1"></div>
                    <!-- Second Column: SHOW CONTENT ARTICLE -->
                    <div class="xl:col-span-2 w-full h-fit md:px-7.5 xl:px-0">
                        <div class="flex justify-center">
                            <section class="w-full">
                                <div class="relative w-full aspect-[3/2] md:mt-7.5 mt-6 flex flex-col items-center justify-center overflow-hidden">
                                    <img
                                        src="{{$articleDetail['Thumbnail_1050x700']}}"
                                        alt="{{$articleDetail['ThumbnailAlt']}}"
                                        class="absolute inset-0 w-full h-full object-cover" />
                                </div>

                                @if($articleDetail['ThumbnailAlt'])
                                <p class="mt-2.5 italic text-sm font-normal">{{$articleDetail['ThumbnailAlt']}}</p>
                                @endif
                            </section>
                        </div>
                    </div>
                </section>
                <section class="col-span-3 xl:grid xl:grid-cols-3">
                </section>
            </section>
            <section class="col-span-1">

            </section>
            <!-- THIRD COLUMN: SHOW CONTENT ARTICLE -->
            <section class="col-span-4 xl:grid xl:grid-cols-4">
                <section class="col-span-1 h-full hidden xl:flex flex-col justify-between">
                    <!-- SOCIAL SECTION -->
                    <div class="{{ $canView ? 'sticky top-7.5' : '' }} mt-5">
                        <div class="w-full ml-7.5 flex gap-x-3">
                            <x-social-media direction="horizontal" :hasCircle="'true'" :shareLink="$shareLink" />

                            <!-- Save Link -->
                            <div class="relative flex gap-x-3 items-center">
                                <div class="w-9 h-9 rounded-full border border-black flex justify-center items-center cursor-pointer" onclick="saveLink()">
                                    <button id="copy-btn" title="Sao chép liên kết">
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
                                    class="absolute left-full z-50 hidden">
                                    <div class="flex items-center ml-4 gap-2 p-3 text-nowrap rounded-lg bg-gray-800 text-white shadow-lg top-[-1.25rem]">
                                        <!-- Triangle Pointer -->
                                        <div class="rotate-90 absolute top-1/2 left-[4px] transform -translate-y-1/2 w-0 h-0 border-l-[8px] border-r-[8px] border-t-[8px] border-t-gray-800 border-l-transparent border-r-transparent"></div>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="11" stroke="#449e80" stroke-width="2" />
                                            <path d="M8 12.5L11 15.5L16 9.5" stroke="#449e80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="text-sm">Đã copy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CONTENT SECTION -->
                <section class="col-span-2 xl:px-0 md:px-6">
                    <div>
                        <div class="flex justify-center">
                            <section class="w-full my-5">
                                <p class="md:text-xl text-lg font-normal">Tác giả: {{$articleDetail['AuthorAlias']}}</p>
                                <p class="md:text-xl text-lg mt-1 text-[#79747E] font-normal">{{formatDate($articleDetail['Time_yyyyMMddHHmmss'])}}</p>
                            </section>
                        </div>

                        <div class="flex gap-x-3 items-center">
                            <!-- SAVE SECTION -->
                            @if ($isSave !== null)
                            <button
                                id="saveButton"
                                class="relative py-1 px-6 rounded-3xl flex items-center border border-black bg-white transition-colors duration-300 mb-5">
                                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                    <path id="bookmarkIcon" fill={{ $isSave === true ? 'black':'white' }} stroke="black" stroke-width="1" d="M6 2C5.44772 2 5 2.44772 5 3V21.382C5 21.7645 5.42458 21.9875 5.76537 21.7932L12 18.118L18.2346 21.7932C18.5754 21.9875 19 21.7645 19 21.382V3C19 2.44772 18.5523 2 18 2H6Z" />
                                </svg>
                                <span id="buttonText" class="text-black font-extralight">{{ $isSave === true ? 'Đã Lưu' : 'Lưu'}}</span>
                            </button>
                            @else
                            <button
                                id="saveButtonWhenNotLogin"
                                class="relative py-1 px-6 rounded-3xl flex items-center border border-black bg-white transition-colors duration-300 mb-5">
                                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                    <path id="bookmarkIcon" fill='white' stroke="black" stroke-width="1" d="M6 2C5.44772 2 5 2.44772 5 3V21.382C5 21.7645 5.42458 21.9875 5.76537 21.7932L12 18.118L18.2346 21.7932C18.5754 21.9875 19 21.7645 19 21.382V3C19 2.44772 18.5523 2 18 2H6Z" />
                                </svg>
                                <span id="buttonText" class="text-black font-extralight">Lưu</span>
                            </button>
                            @endif

                            @if($audio !== null)
                            @if ($isLogin)
                            <!-- Button Play Audio -->
                            <button onclick="playAudioInPhone()" id="playAudioButtonInPhone"
                                class="relative py-1 px-6 rounded-3xl flex items-center border border-black bg-white transition-colors duration-300 mb-5 gap-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 12 12" fill="none">
                                    <path d="M1 9.33333V6C1 4.67392 1.52678 3.40215 2.46447 2.46447C3.40215 1.52678 4.67392 1 6 1C7.32608 1 8.59785 1.52678 9.53553 2.46447C10.4732 3.40215 11 4.67392 11 6V9.33333M11 9.88889C11 10.1836 10.8829 10.4662 10.6746 10.6746C10.4662 10.8829 10.1836 11 9.88889 11H9.33333C9.03865 11 8.75603 10.8829 8.54766 10.6746C8.33929 10.4662 8.22222 10.1836 8.22222 9.88889V8.22222C8.22222 7.92754 8.33929 7.64492 8.54766 7.43655C8.75603 7.22817 9.03865 7.11111 9.33333 7.11111H11V9.88889ZM1 9.88889C1 10.1836 1.11706 10.4662 1.32544 10.6746C1.53381 10.8829 1.81643 11 2.11111 11H2.66667C2.96135 11 3.24397 10.8829 3.45234 10.6746C3.66071 10.4662 3.77778 10.1836 3.77778 9.88889V8.22222C3.77778 7.92754 3.66071 7.64492 3.45234 7.43655C3.24397 7.22817 2.96135 7.11111 2.66667 7.11111H1V9.88889Z" stroke="#1E1E1E" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Nghe bài viết
                                <p class="text-gray-400 font-light hidden md:block" id="audioDurationInPhone"></p>
                            </button>

                            <!-- Hidden audio player -->
                            <div id="audioContainerInPhone" style="display: none;">
                                <audio id="audioPlayerInPhone" controls class="w-50 md:w-56" style="height: 40px; margin-bottom: 1.25rem;" controlsList="nodownload noplaybackrate nofullscreen">
                                    <source src={{ $audio }} type="audio/mpeg">
                                </audio>
                            </div>
                            @else
                            <button onclick="playAudioInPhone()" id="playAudioButtonWhenNotLogin"
                                class="relative py-1 px-6 rounded-3xl flex items-center border border-black bg-white transition-colors duration-300 mb-5 gap-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 12 12" fill="none">
                                    <path d="M1 9.33333V6C1 4.67392 1.52678 3.40215 2.46447 2.46447C3.40215 1.52678 4.67392 1 6 1C7.32608 1 8.59785 1.52678 9.53553 2.46447C10.4732 3.40215 11 4.67392 11 6V9.33333M11 9.88889C11 10.1836 10.8829 10.4662 10.6746 10.6746C10.4662 10.8829 10.1836 11 9.88889 11H9.33333C9.03865 11 8.75603 10.8829 8.54766 10.6746C8.33929 10.4662 8.22222 10.1836 8.22222 9.88889V8.22222C8.22222 7.92754 8.33929 7.64492 8.54766 7.43655C8.75603 7.22817 9.03865 7.11111 9.33333 7.11111H11V9.88889ZM1 9.88889C1 10.1836 1.11706 10.4662 1.32544 10.6746C1.53381 10.8829 1.81643 11 2.11111 11H2.66667C2.96135 11 3.24397 10.8829 3.45234 10.6746C3.66071 10.4662 3.77778 10.1836 3.77778 9.88889V8.22222C3.77778 7.92754 3.66071 7.64492 3.45234 7.43655C3.24397 7.22817 2.96135 7.11111 2.66667 7.11111H1V9.88889Z" stroke="#1E1E1E" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Nghe bài viết
                                <p class="text-gray-400 font-light" id="audioDurationInPhone"></p>
                            </button>
                            @endif

                            @endif
                        </div>

                        @if ($summarize !== null)
                        <section class="pb-5 bg-[#f0f0f0] rounded-lg mt-2 mb-5 overflow-hidden">
                            <section class="pt-5 px-5 flex justify-between items-center">
                                <div class="flex items-center gap-x-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H18C18.5304 4 19.0391 4.21071 19.4142 4.58579C19.7893 4.96086 20 5.46957 20 6V20C20 20.5304 19.7893 21.0391 19.4142 21.4142C19.0391 21.7893 18.5304 22 18 22H4C3.46957 22 2.96086 21.7893 2.58579 21.4142C2.21071 21.0391 2 20.5304 2 20V13" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
                                        <path d="M11.3945 7.87793C11.7139 10.6495 13.9003 12.8368 16.6719 13.1562L18 13.3086L16.6719 13.4619C13.9003 13.7813 11.7139 15.9686 11.3945 18.7402L11.2412 20.0674L11.0879 18.7402C10.7686 15.9687 8.58206 13.7814 5.81055 13.4619L4.48242 13.3086L5.81055 13.1562C8.58205 12.8368 10.7685 10.6495 11.0879 7.87793L11.2412 6.55078L11.3945 7.87793ZM5.64258 4.69922C5.81121 6.1601 6.96394 7.31278 8.4248 7.48145L9.125 7.5625L8.4248 7.64258C6.96392 7.81122 5.81122 8.96392 5.64258 10.4248L5.5625 11.125L5.48145 10.4248C5.31278 8.96394 4.1601 7.81121 2.69922 7.64258L2 7.5625L2.69922 7.48145C4.16012 7.31281 5.31281 6.16012 5.48145 4.69922L5.5625 4L5.64258 4.69922ZM8.13867 9.10449C8.19128 9.56359 8.55367 9.9251 9.0127 9.97852L9.2334 10.0039L9.0127 10.0293C8.55367 10.0821 8.19187 10.4442 8.13867 10.9033L8.11426 11.123L8.08887 10.9033C8.03626 10.4442 7.67387 10.0827 7.21484 10.0293L6.99414 10.0039L7.21484 9.97852C7.67387 9.9257 8.03566 9.56359 8.08887 9.10449L8.11426 8.88477L8.13867 9.10449ZM7.40234 5.46777C7.42866 5.69681 7.60983 5.87799 7.83887 5.9043L7.94824 5.91602L7.83887 5.92871C7.60983 5.95502 7.42866 6.1362 7.40234 6.36523L7.38965 6.47461L7.37695 6.36523C7.35064 6.1362 7.17044 5.95502 6.94141 5.92871L6.83203 5.91602L6.94141 5.9043C7.17044 5.87799 7.35064 5.69681 7.37695 5.46777L7.38965 5.3584L7.40234 5.46777Z" fill="black" />
                                    </svg>


                                    <p class="font-bold text-lg md:text-xl text-font">
                                        Tóm tắt bài viết
                                    </p>
                                </div>
                                <button id="toggleButton" class="flex gap-x-1 items-center hover:underline">
                                    <span id="toggleText" class="text-font">Ẩn</span>
                                    <svg id="toggleIcon" style="transform: rotate(180deg);" class="transition-transform duration-300" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </section>

                            <div id="AISummarize" style="max-height: 600px;" class="opacity-100 overflow-hidden transition-all duration-500 ease-in-out">
                                <section id="summarizeList" class="flex gap-x-5 px-5 mt-3 overflow-x-auto scroll-hidden ">
                                    @foreach ($summarize as $index => $item)
                                    <div class="border border-transparent rounded-lg p-2.5 bg-white flex-shrink-0 flex flex-col justify-between h-full item-card"
                                        style="min-width: 240px; max-width: 240px; height: 280px;"
                                        data-index="{{ $index }}">
                                        <p class="text-lg text-font" style="line-height: 1.3;">{{$item}}</p>
                                    </div>
                                    @endforeach

                                </section>

                                <section class="lg:flex justify-between hidden">
                                    <div></div>
                                    <div id="dotSummarize" class="flex gap-x-2.5  mt-5">
                                        <?php foreach ($summarize as $index => $summarizeItem): ?>
                                            <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="<?= $index ?>"></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div>
                                    </div>
                                </section>
                            </div>
                        </section>
                        @endif

                        @if ($canView === true)
                        @php
                        $adsHtml = '
                        <section style="background-color: #f5f5f5; padding-top: 1.5rem; padding-bottom: 1.5rem; margin: 1.35rem 0;">
                            <section style="padding-left: 1.5rem; padding-right: 1.5rem; justify-content: center; align-items: center; height: 100%;">
                                <section style="position: relative; box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px; overflow: hidden;">
                                    <div id="zone-10" style="aspect-ratio: 970/250;">
                                    </div>
                                </section>
                            </section>
                        </section>';

                        $content = $articleDetail['Content'];

                        // Split content by paragraph ending
                        $parts = preg_split('#(</p>)#i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $newContent = '';
                        $paragraphCount = 0;

                        for ($i = 0; $i < count($parts); $i++) {
                            $newContent .=$parts[$i];

                            // Count complete paragraph after closing </p>
                            if (strtolower(trim($parts[$i])) === '</p>') {
                            $paragraphCount++;

                            if ($paragraphCount === 4 && count($parts) > 4) {
                            $newContent .= $adsHtml;
                            }
                            }
                            }

                            $articleDetail['Content'] = $newContent;

                            @endphp

                            <div class="entry content1" id="standard-custom">
                                {!! $articleDetail['Content'] !!}
                            </div>

                            @else
                            <?php
                            $content = $articleDetail['Content'];
                            $firstTwoParagraphs = '';
                            // Use regular expression to extract the first two <p>...</p>
                            if (preg_match_all('/<p[^>]*>(.*?)<\/p>/s', $content, $matches)) {
                                $paragraphs = $matches[0]; // $matches[0] contains the full matches (including <p> tags)
                                $firstTwoParagraphs = implode('', array_slice($paragraphs, 0, 2)); // Get the first two paragraphs
                            } else {
                                if (preg_match_all('/<div[^>]*>(.*?)<\/div>/s', $content, $matches)) {
                                    $paragraphs = $matches[0]; // $matches[0] contains the full matches (including <p> tags)
                                    $firstTwoParagraphs = implode('', array_slice($paragraphs, 0, 2)); // Get the first two paragraphs
                                }
                            }
                            ?>

                            <div class="entry content1 relative" id="standard-custom">
                                {!! $firstTwoParagraphs !!}
                                <div style="background:  linear-gradient(to top, white, rgba(255, 255, 255, 0.5), transparent); pointer-events: none;" class="absolute bottom-0 w-full h-full">
                                </div>
                            </div>
                            @endif
                            <div class="pb-4">
                                @if ($articleDetail['SourceType'] === 11 || $articleDetail['SourceType'] === 12)
                                <p class="text-gray italic text-lg">
                                    Theo Bloomberg </p>
                                @endif
                            </div>
                            <div class="md:px-[15px]">
                                <section class="w-full relative flex flex-wrap justify-end mb-7 text-right">
                                    <p class="italic text-right w-full" style="color:#b5b5b5ee;font-size: 10px;line-height:1.2 !important; margin: 0 !important;">Theo phattrienxanh.baotainguyenmoitruong.vn</p>
                                    <p
                                        style="color:#b5b5b5ee;font-size: 10px;line-height:1.2 !important; margin: 0 !important;"
                                        class="italic text-right w-full break-all">
                                        https://phattrienxanh.baotainguyenmoitruong.vn{{ request()->getPathInfo() }}
                                    </p>
                                </section>
                            </div>
                    </div>
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
                </section>


                <div class="h-full hidden xl:flex flex-col  md:mt-7.5 mt-6">
                    <div class="flex justify-center items-center">
                        <div class="pb-7 w-fit px-7.5">
                            <!-- <script src="https://amb.beaconasiamedia.vn/ser.php?f=44"></script> -->
                            <div id="zone-2" class="w-full relative" style="aspect-ratio: 1080/1450;">
                                <!-- Skeleton Container - thêm absolute và z-index cao -->
                                <div class="skeleton-container absolute inset-0 z-20">
                                    <x-skeleton containerStyle="h-full w-full">
                                    </x-skeleton>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($canView === true)
                    <div class="px-7.5 sticky top-7.5 mb-7.5">
                        <p class="font-bold pb-2.5 text-lg border-b border-b-gray-300 mb-6">ĐỌC NHIỀU</p>
                        @php
                        $articleSubset = array_slice($articleTopReadList, 0, 5);
                        @endphp
                        <x-read-a-lot :article="$articleSubset" :isNeedCheckPremium='false'></x-read-a-lot>
                    </div>
                    @endif
                    <div></div>
                </div>
            </section>

        </div>
    </section>
</div>

<!-- WHEN SAVE OR UNSAVE TOAST -->
@if ($isSave !== null)
<section id="toast-announce" class=" fixed bottom-3 left-1/2 translate-x-[-50%] z-50  flex items-center bg-black py-5 px-7.5 gap-x-7.5 rounded-lg">
    <div class="flex md:flex-row flex-col gap-y-3 gap-x-7.5">
        <div class="text-white flex gap-x-2 items-center">
            <svg width="25" height="25" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="white" stroke-width="8" fill="none" />
                <path d="M30 50 L45 65 L70 35" stroke="white" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="text-base md:text-lg text-nowrap" id="toast-text">
                Đã Lưu
            </p>
        </div>
        <a href="/saved" id="toast-all" class="text-base text-nowrap relative inline-block text-white after:absolute after:left-0 after:bottom-0 after:h-[0.5px] after:opacity-50 after:w-full after:bg-white after:content-['']">
            Xem các tin đã lưu
        </a>
    </div>
    <button id="closeToastBtn">
        <svg width=" 18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 17C7.24839 10.7516 10.7516 7.24839 17 1M1 1L17 17" stroke="white" stroke-width="2" />
        </svg>
    </button>
</section>
@endif


<script>
    function saveLinkPTX() {
        const ptxURL = " https://phattrienxanh.baotainguyenmoitruong.vn"
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
    const audioInPhone = document.getElementById('audioPlayerInPhone');
    if (audioInPhone) {
        const durationElInPhone = document.getElementById('audioDurationInPhone');
        // Convert seconds to mm:ss
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        }

        // Load real duration
        audioInPhone.addEventListener('loadedmetadata', () => {
            durationElInPhone.textContent = formatTime(audioInPhone.duration);
        });

        // Modify your playAudioInPhone function to check device first
        function playAudioInPhone() {
            // Show the audio player
            document.getElementById('audioContainerInPhone').style.display = 'block';
            document.getElementById('playAudioButtonInPhone').style.display = 'none';

            // Play the audio
            var audio = document.getElementById('audioPlayerInPhone');
            audio.play();
        }
    }
</script>

<script>
    const list = document.getElementById('summarizeList');
    if (list) {
        const buttons = document.querySelectorAll('#dotSummarize button');
        let activeIndex = 0;

        function updateActiveDot(newIndex) {
            buttons.forEach((button, index) => {
                button.classList.toggle('bg-black', index === newIndex);
                button.classList.toggle('bg-gray-300', index !== newIndex);
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
    }
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