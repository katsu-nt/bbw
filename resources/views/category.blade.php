@extends('layouts.app')
@section('content')
@php
$articleSubset = array_slice($categoryData['ListArticleTopRead'], 0, 5);
@endphp

<main class="flex justify-center">
    <section class="container-content">
        <section class="lg:grid lg:grid-cols-3 w-full">
            <!---- ---------------------- First Column -- ------------------------>
            <div class="lg:col-span-2 w-full md:border-x md:border-gray-400 h-fit">
                <!-- PHONG LƯU SECTION -->
                @if($categoryData['channelInfo']['Name'] === "Phong lưu")
                <section class="p-5 lg:p-7.5">
                    <h1 class="text-lg font-bold pb-4">
                        {{$categoryData['channelInfo']['Name']}}
                    </h1>
                    @php
                    $keywords = explode(',', $categoryData['ArticleFocus']['Keyword'] ?? '');
                    $isPremiumArticleFocus = isPremiumContent($keywords);
                    @endphp
                    <section class="md:grid md:grid-cols-3 gap-x-7.5 w-full pb-6 md:pb-7 border-b border-gray-300">
                        <div class="col-span-2 h-full">
                            <x-card-img-title
                                :heightImage="'h-auto'"
                                :image="$categoryData['ArticleFocus']['Thumbnail_1050x700']"
                                :title="$categoryData['ArticleFocus']['Title']"
                                :url="url($categoryData['ArticleFocus']['FriendlyTitle'] . '-' . $categoryData['ArticleFocus']['PublisherId'] . '.html')"
                                :type="'inside'"
                                :fontSize="'text-2xl'"
                                :isPremiumArticle="'isPremiumArticleFocus'" />
                        </div>
                        <div class="h-full mt-6 md:mt-0">
                            <div class="flex flex-col h-full">
                                @php
                                $keywords_1 = explode(',', $categoryData['ListArticleNewest'][1]['Keyword'] ?? '');
                                $isPremiumArticle1 = isPremiumContent($keywords_1);
                                $keywords_2 = explode(',', $categoryData['ListArticleNewest'][2]['Keyword'] ?? '');
                                $isPremiumArticle2 = isPremiumContent($keywords_2);
                                @endphp
                                <div class="flex-1 mb-6 md:mb-7.5">
                                    <x-card-img-title
                                        :heightImage="'h-auto'"
                                        :image="$categoryData['ListArticleNewest'][1]['Thumbnail_1050x700']"
                                        :title="$categoryData['ListArticleNewest'][1]['Title']"
                                        :type="'inside'"
                                        :isPremiumArticle="'isPremiumArticle1'"
                                        :url="url($categoryData['ListArticleNewest'][1]['FriendlyTitle'] . '-' . $categoryData['ListArticleNewest'][1]['PublisherId'] . '.html')" />
                                </div>
                                <div class="flex-1">
                                    <x-card-img-title
                                        :heightImage="'h-auto'"
                                        :image="$categoryData['ListArticleNewest'][2]['Thumbnail_1050x700']"
                                        :title="$categoryData['ListArticleNewest'][2]['Title']"
                                        :type="'inside'"
                                        :isPremiumArticle="'isPremiumArticle2'"
                                        :url="url($categoryData['ListArticleNewest'][2]['FriendlyTitle'] . '-' . $categoryData['ListArticleNewest'][2]['PublisherId'] . '.html')" />
                                </div>
                            </div>
                        </div>
                    </section>
                </section>
                @else
                <!-- OTHER CATEGORY SECTION -->
                @php
                $keywords = explode(',', $categoryData['ArticleFocus']['Keyword'] ?? '');
                $isPremiumArticle = isPremiumContent($keywords);
                @endphp
                <section class="p-5 lg:p-7.5">
                    <h1 class="text-lg font-bold pb-4">
                        {{$categoryData['channelInfo']['Name']}}
                    </h1>
                    <div class="border-b border-gray-300">
                        <x-article-focus :article="$categoryData['ArticleFocus']"
                            :widthImage="'w-full md:w-96'"
                            :aspectRadio="'aspect-[4/3]'"
                            :="'$isPremiumArticle'" />
                    </div>
                </section>
                @endif

                <!-- HIDE: ADVERTISEMENT SECTION -->
                <section class="p-5 lg:p-7.5 flex justify-center items-center md:hidden">
                    <div id="zone-2-hide" class="w-full relative" style="aspect-ratio: 1080/1450;">
                        <!-- Skeleton Container - thêm absolute và z-index cao -->
                        <div class="skeleton-container absolute inset-0 z-20">
                            <x-skeleton containerStyle="h-full w-full">
                            </x-skeleton>
                        </div>
                    </div>
                </section>


                <!-- HIDE: READ A LOT SECTION -->
                <section class="p-5 border-y border-gray-300 md:hidden block">
                    <p class="font-bold mb-5 text-xl">ĐỌC NHIỀU</p>
                    <x-read-a-lot :article="$articleSubset" />
                </section>

                <section class="mt-5 md:mt-0">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:gap-x-7.5 gap-x-5 md:gap-y-7.5 gap-y-5 px-5 lg:px-7.5">
                        <?php
                        $articleListByCategory = $categoryData['channelInfo']['Name'] === "Phong lưu" ? array_slice($categoryData['ListArticleNewest'], 3, 15) : array_slice($categoryData['ListArticleNewest'], 1, 15);
                        ?>
                        @php
                        $count = 0;
                        @endphp

                        @foreach ($articleListByCategory as $index => $article)
                        @if($article['Channel']['Name'] === $categoryData['channelInfo']['Name'])
                        @php
                        $count += 1;
                        $keywords = explode(',', $article['Keyword'] ?? '');
                        $isPremiumArticle = isPremiumContent($keywords);
                        if($count > 9)
                        break;
                        @endphp

                        <div class="{{$count > 8 ? 'hidden md:block' : ''}} md:pb-7.5 pb-5 {{ $count <= 6 ? 'border-b border-dotted border-b-gray-400' : '' }}">
                            <x-card-img-title
                                :heightImage="'h-auto'"
                                :widthImage="'w-full'"
                                :breakline="'true'"
                                :image="$article['Thumbnail_600x315']"
                                :title="$article['Title']"
                                :url="url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')"
                                :isPremiumArticle="$isPremiumArticle" />
                        </div>
                        @endif
                        @endforeach
                    </div>
                </section>
            </div>

            <!---- ---------------------- Second Column -- ------------------------>
            <div class="h-full border border-gray-400 border-y-0 border-l-0 hidden lg:block">
                <!-- ADS SECTION -->
                <section class="p-5 lg:p-7.5  flex justify-center items-center">
                    <div id="zone-2" class="w-full relative" style="aspect-ratio: 1080/1450;">
                        <!-- Skeleton Container - thêm absolute và z-index cao -->
                        <div class="skeleton-container absolute inset-0 z-20">
                            <x-skeleton containerStyle="h-full w-full">
                            </x-skeleton>
                        </div>
                    </div>
                </section>

                <!-- READ A LOT SECTION -->
                <section class="p-7.5 border-y border-gray-300">
                    <p class="font-bold mb-5 text-xl">ĐỌC NHIỀU</p>
                    <x-read-a-lot :article="$articleSubset" />
                </section>
            </div>
        </section>

        <!-- ---------------------------------------- LONG ADS SECTION ---------------------------------------- -->
        <section class="bg-[#f7f7f7] py-5 md:py-7.5 border-y md:border-x border-gray-400 flex justify-center items-center">
            <div class="lg:flex px-5 lg:px-7.5 justify-center items-center h-full w-full">
                <div id="zone-7" class=" lg:flex lg:justify-center lg:items-center lg:border-t-0 w-full relative" style="aspect-ratio: 970/250;">
                    <!-- Skeleton Container - thêm absolute và z-index cao -->
                    <div class="skeleton-container absolute inset-0 z-20">
                        <x-skeleton containerStyle="h-full w-full">
                        </x-skeleton>
                    </div>
                </div>
            </div>
        </section>

        <!-- ---------------------------------------- RELATED ARTICLES SECTION ---------------------------------------- -->
        <!--end c-main-banner-->
        <div class="l-content loadmore">
            <div class="l-container">
                <div class="lg:grid lg:grid-cols-3 w-full">
                    <!-- FIRST COLUMN -->
                    <div class="w-full h-fit lg:col-span-2 md:border md:border-y-0 md:border-gray-400">
                        <div class="c-box">
                            <div class="c-box__content">
                                <div class="c-template-list is-image-right is-slot-large">
                                    <ul class="loadAjax" id="article-list">
                                    </ul>
                                    <div class="loading_img" style="display: none;">
                                        <div class="timeline-wrapper">
                                            <div class="timeline-item">
                                                <div class="animated-background">
                                                    <div class="background-masker header-top"></div>
                                                    <div class="background-masker header-left"></div>
                                                    <div class="background-masker header-right"></div>
                                                    <div class="background-masker header-bottom"></div>
                                                    <div class="background-masker header-2-left"></div>
                                                    <div class="background-masker header-2-right"></div>
                                                    <div class="background-masker header-2-bottom"></div>
                                                    <div class="background-masker meta-left"></div>
                                                    <div class="background-masker meta-right"></div>
                                                    <div class="background-masker meta-bottom"></div>
                                                    <div class="background-masker description-left"></div>
                                                    <div class="background-masker description-right"></div>
                                                    <div class="background-masker description-bottom"></div>
                                                    <div class="background-masker description-2-left"></div>
                                                    <div class="background-masker description-2-right"></div>
                                                    <div class="background-masker description-2-bottom"></div>
                                                    <div class="background-masker description-3-left"></div>
                                                    <div class="background-masker description-3-right"></div>
                                                    <div class="background-masker description-3-bottom"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-wrapper">
                                            <div class="timeline-item">
                                                <div class="animated-background">
                                                    <div class="background-masker header-top"></div>
                                                    <div class="background-masker header-left"></div>
                                                    <div class="background-masker header-right"></div>
                                                    <div class="background-masker header-bottom"></div>
                                                    <div class="background-masker header-2-left"></div>
                                                    <div class="background-masker header-2-right"></div>
                                                    <div class="background-masker header-2-bottom"></div>
                                                    <div class="background-masker meta-left"></div>
                                                    <div class="background-masker meta-right"></div>
                                                    <div class="background-masker meta-bottom"></div>
                                                    <div class="background-masker description-left"></div>
                                                    <div class="background-masker description-right"></div>
                                                    <div class="background-masker description-bottom"></div>
                                                    <div class="background-masker description-2-left"></div>
                                                    <div class="background-masker description-2-right"></div>
                                                    <div class="background-masker description-2-bottom"></div>
                                                    <div class="background-masker description-3-left"></div>
                                                    <div class="background-masker description-3-right"></div>
                                                    <div class="background-masker description-3-bottom"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end loading_img-->
                                </div>
                                <!--end c-template-list-->
                                <div class="c-more py-5" id="load_more">
                                    <a href="javascript:;" data-channel-id="{{ $categoryData['channelInfo']['ChannelId'] }}"
                                        data-page="{{ $page + 1 }}" data-limit="{{ $limit }}">Xem thêm</a>
                                </div>
                            </div>
                            <!--end c-box__content-->
                        </div>
                        <!--end c-box-->
                    </div>

                    <!-- SECOND COLUMN -->
                    <div class="h-full border border-gray-400 border-y-0 border-l-0 hidden lg:block">
                        <div class="c-widget h-show-pc">
                            <div class="c-widget__content">
                                <section class="p-7.5 flex justify-center items-center">
                                    <script src="https://amb.beaconasiamedia.vn/ser.php?f=41"></script>
                                </section>
                            </div>
                        </div>
                        <!--end c-widget-->
                    </div>
                </div>
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
            <!--end container-->
        </div>
        <!--end l-content-->
    </section>
</main>

@endsection