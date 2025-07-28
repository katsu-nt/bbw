@extends('layouts.app')

@section('content')

<main class="flex justify-center items-center">
    <section class="container-content">
        <section class="lg:grid lg:grid-cols-3 w-full">
            <!---- ---------------------- First Column -------------------------- ---->
            <div class=" w-full h-full lg:col-span-2 md:border md:border-y-0 md:border-gray-400 py-5 md:py-7.5">
                <p class="text-3xl font-bold mb-5 px-6 md:px-7.5 md:text-start text-center">{{ $eventName }}</p>
                <div class="px-0 md:px-6">
                    <div class=" mb-6">
                        <x-article-focus :article="$articleDetails[0]"
                            :widthImage="'md:w-1/2 w-full'" />
                    </div>
                </div>


                @if(count($articleDetails) > 1)
                <section>
                    <section class="pt-6 md:pt-7.5 border-t mx-6 md:mx-7.5 border-t-gray-400">
                        <div class="grid grid-cols-2 md:grid-cols-3 md:gap-x-7.5 gap-x-6 md:gap-y-7.5 gap-y-6">
                            @foreach (array_slice($articleDetails, 1, count($articleDetails) - 1) as $index => $article)
                            <div class="{{$index > 7 ? 'hidden md:block' : ''}} {{ $index < (count($articleDetails) - 1) - ((count($articleDetails) - 1) % 3 === 0 ? 3 : (count($articleDetails) - 1) % 3)
                    ? 'border-b border-dotted border-b-gray-400 md:pb-7.5 pb-6 ' : '' }}">
                                <x-card-img-title
                                    :heightImage="'h-auto'"
                                    :widthImage="'w-full'"
                                    :breakline="'true'"
                                    :image="$article['Thumbnail_600x315']"
                                    :title="$article['Title']"
                                    :url="url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')" />
                            </div>
                            @endforeach
                        </div>
                    </section>
                </section>
                @endif
            </div>

            <!---- ---------------------- Second Column -------------------------- ---->
            <div class="h-full border-r border-r-gray-400  hidden lg:block">
                <section class="px-4 py-5 lg:p-7.5  flex justify-center items-center">
                    <div id="zone-2" class="w-full relative" style="aspect-ratio: 1080/1450;">
                        <!-- Skeleton Container - thêm absolute và z-index cao -->
                        <div class="skeleton-container absolute inset-0 z-20">
                            <x-skeleton containerStyle="h-full w-full">
                            </x-skeleton>
                        </div>
                    </div>
                </section>
            </div>
        </section>
        <section class="p-6 lg:p-7.5 flex justify-center items-center bg-gray-300 border-y border-y-gray-400">
            <div class="flex justify-center items-center h-full">
                <div id="zone-4" class=" lg:flex lg:justify-center lg:items-center lg:border-t-0 lg:w-[60.625rem] lg:h-[15.625rem] w-full relative" style="aspect-ratio: 970/250;">
                    <!-- Skeleton Container - thêm absolute và z-index cao -->
                    <div class="skeleton-container absolute inset-0 z-20">
                        <x-skeleton containerStyle="h-full w-full">
                        </x-skeleton>
                    </div>
                </div>
            </div>
        </section>
    </section>
</main>

<!-- ---------------------------------------- RELATED ARTICLES SECTION ---------------------------------------- -->
<div class="l-content loadmore flex justify-center items-center">
    <div class="container-content">
        <div class="lg:grid lg:grid-cols-3 w-full">
            <!-- FIRST COLUMN -->
            <div class="w-full h-fit lg:col-span-2 border-t md:border md:border-y-0 md:border-gray-400">
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
                        <div class="c-more mb-5" id="load_more">
                            <a href="javascript:;" data-channel-id="{{ $articleDetails[0]['Channel']['ChannelId'] }}"
                                data-page="1" data-limit="10">Xem thêm</a>
                        </div>
                    </div>
                    <!--end c-box__content-->
                </div>
                <!--end c-box-->
            </div>

            <!-- SECOND COLUMN -->
            <div class="h-full border border-gray-400 border-y-0 border-l-0 hidden lg:block mb-6 md:mb-7.5">
                <div class="c-widget h-show-pc">
                    <div class="c-widget__content">
                        <section class="p-7.5 flex justify-center items-center">
                            <div id="zone-6" class="w-full relative" style="aspect-ratio: 1080/1450;">
                                <!-- Skeleton Container - thêm absolute và z-index cao -->
                                <div class="skeleton-container absolute inset-0 z-20">
                                    <x-skeleton containerStyle="h-full w-full">
                                    </x-skeleton>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <!--end c-widget-->
            </div>
        </div>
    </div>
    <!--end container-->
</div>

@endsection