@extends('layouts.app')

@section('content')
<main class="flex justify-center items-center">
    <section class="container-content">
        <div class="border border-b-0 border-gray-400 p-5 lg:p-7.5">
            <div class="flex flex-col lg:flex-row gap-x-6 md:gap-x-7.5">
                <section class="md:flex-1">
                    <a href="{{$mostRecentEvent['FriendlyName']}}-event{{$mostRecentEvent['EventId']}}.html">
                        <img
                            src="{{ $mostRecentEvent['Thumbnail_600x315'] ?? 'default_image.jpg' }}"
                            alt="{{ $mostRecentEvent['Name'] }}"
                            class=" w-full h-auto object-cover" />
                    </a>
                </section>

                <section class="flex-1 mt-5 lg:mt-0">
                    <p class="w-fit px-2.5 h-6 text-sm font-bold text-white bg-BG_Overlay_01 flex items-center mb-2">Premium</p>
                    <a href="{{$mostRecentEvent['FriendlyName']}}-event{{$mostRecentEvent['EventId']}}.html">
                        <p class="font-bold text-3xl lg:text-4xl hover:text-Icon05">{{ $mostRecentEvent['Name'] }}</p>
                    </a>
                    @php
                    preg_match('/\d+/', $mostRecentEvent['PublishedTime'], $matches);
                    $timestamp = $matches[0] / 1000; // Convert milliseconds to seconds

                    // Convert the timestamp to a date format
                    $date = date("m.Y", $timestamp);

                    @endphp

                    <p class="text-lg mt-4">Báo cáo đặc biệt -
                        {{ $date}}
                    </p>

                    <ul>
                        @foreach ($articles as $index => $article)
                        @php
                        $keywords = explode(',', $article['Keyword'] ?? '');
                        $isPremiumArticle = isPremiumContent($keywords);
                        @endphp
                        <li class="mt-2 pb-2 {{$index !== count($articles) - 1 ? "border-b border-b-black" : ""}} w-full">
                            <a href="{{$article['FriendlyTitle']}}-{{$article['PublisherId']}}.html" class=" flex gap-x-3">
                                <p class="font-semibold text-lg">
                                    <!-- @if ($isPremiumArticle == true)
                                <span class=" bg-darkYellow px-2 py-1 border-darkYellow border text-white w-fit h-fit  text-[0.563rem] tracking-[.2em] z-20">PREMIUM</span>
                                @endif -->
                                    <span class="hover:text-Icon05">
                                        ● {{ $article['Title'] }}
                                    </span>
                                </p>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </section>
            </div>
        </div>
    </section>
</main>

<section class="flex justify-center items-center ">
    <section class="container-content">
        <!-- LONG ADS SECTION -->
        <section class="bg-white py-6 md:py-7.5 border border-gray-400">
            <div class="lg:flex px-6 lg:px-7.5 justify-center items-center h-full">
                <div id="zone-4" class=" lg:flex lg:justify-center lg:items-center lg:border-t-0 lg:w-[60.625rem] lg:h-[15.625rem] w-full relative" style="aspect-ratio: 970/250;">
                    <!-- Skeleton Container - thêm absolute và z-index cao -->
                    <div class="skeleton-container absolute inset-0 z-20">
                        <x-skeleton containerStyle="h-full w-full">
                        </x-skeleton>
                    </div>
                </div>
            </div>
        </section>


        <div class="border border-y-0 border-gray-400 p-5 lg:p-7.5 bg-Gray_04">
            @php $eventsCollection = collect($events); @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 lg:gap-x-7.5 gap-x-5 lg:gap-y-7.5 gap-y-5">
                @foreach ($eventsCollection->slice(1) as $event)
                <x-card-report
                    :title="$event['Name']"
                    :image="$event['Thumbnail']"
                    :description="$event['Description']"
                    :datetimeUpdated="$event['PublishedTime']"
                    :url="url($event['FriendlyName'] . '-event' . $event['EventId'] . '.html')" />
                @endforeach
            </div>
        </div>
    </section>
</section>

@endsection