@php
$articleUrl = url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') ?? '';
@endphp

@if ($isLoading)
<div class="flex flex-col gap-y-3 md:flex-row justify-between gap-x-7 w-full mb-[1.125rem] md:mb-7">
    <x-skeleton containerStyle="relative {{ $heightImage ?? 'h-full' }} {{ $widthImage ?? 'w-full' }} {{ $aspectRadio ?? 'aspect-[3/2]' }} shrink-0 overflow-hidden">
    </x-skeleton>
    <div class="grow flex flex-col gap-y-3 mx-6 md:mx-0">
        <x-skeleton containerStyle="h-10 w-full">
        </x-skeleton>
        <x-skeleton containerStyle="h-8 w-full">
        </x-skeleton>
    </div>
</div>
@else

<div class="flex flex-col gap-y-3 md:flex-row justify-between gap-x-4 w-full mb-[1.125rem] md:mb-7">
    <div class="relative {{ $heightImage ?? 'h-full' }} {{ $widthImage ?? 'w-full' }} {{ $aspectRadio ?? 'aspect-[3/2]' }} shrink-0 overflow-hidden">
        <!-- Skeleton Loader: This will be visible until the image has loaded -->
        <div class="skeleton-container">
            <x-skeleton containerStyle="h-full w-full"></x-skeleton>
        </div>

        <a href="{{ $articleUrl }}">
            <img
                src="{{ $article['Thumbnail_1050x700'] ?? 'default_image.jpg' }}"
                alt="{{ $article['Title'] }}"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
                onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
        </a>
        @if (isPremiumContent(explode(',', $article['Keyword'] ?? '')) === true)
        <p class=" absolute top-0 left-0 bg-BG_Overlay_01 h-4 px-2 text-white font-bold text-[10px] flex items-center">Premium</p>
        @endif
    </div>

    <div class="grow flex flex-col gap-y-2 md:gap-y-4">
        <a href="{{ $articleUrl }}" class="font-bold text-2xl md:text-3xl text-start">
            <span class=" hover:text-Icon05">
                {{ $article['Title'] ?? 'Title' }}
            </span>
        </a>

        <a href="{{ $articleUrl }}" class="text-sm text-start">
            {{ $article['Headlines'] ?? 'Headlines' }}
        </a>
    </div>
</div>
@endif