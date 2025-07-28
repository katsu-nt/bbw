@if ($isLoading === false)
@if ($type === 'inside')
<div class="relative h-full">
    <a href="{{ $url }}">
        <div class="w-full h-full relative">
            <!-- Skeleton Loader: This will be visible until the image has loaded -->
            <div class="skeleton-container">
                <x-skeleton containerStyle="h-full w-full"></x-skeleton>
            </div>
            <img
                src="{{ $image }}"
                alt=""
                class="w-full h-full"
                onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
        </div>

        <div class="absolute top-0 w-full h-full z-10 bg-custom-radial">
            <p class="text-white absolute bottom-0 py-2 left-0 font-bold px-3 {{ $fontSize }}">
                {{ $title }}
            </p>
        </div>

        @if ($isPremiumArticle === true)
        <p class=" absolute top-0 left-0 bg-BG_Overlay_01 z-50 h-4 px-2 text-white font-bold text-[10px] flex items-center">Premium</p>
        @endif
    </a>
</div>
@else
<button class="h-full w-full flex">
    <a href="{{ $url }}" class="w-full">
        <div class="md:block flex justify-between items-start md:gap-x-5 gap-x-2
                        {{ $breakline ? 'flex-col' : ($isReverse ? 'flex-row-reverse' : 'flex-row') }}">

            <div class="relative shrink-0 {{ $heightImage ?? 'h-full' }} {{ $widthImage ?? 'w-full' }} aspect-[3/2] overflow-hidden">
                <!-- Skeleton Loader: This will be visible until the image has loaded -->
                <div class="skeleton-container">
                    <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                </div>
                <img src="{{ $image }}" alt="{{ $title }}"
                    class="absolute inset-0 w-full h-full object-cover"
                    onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
                @if ($isPremiumArticle === true)
                <p class=" absolute top-0 left-0 bg-BG_Overlay_01 h-4 px-2 text-white font-bold text-[10px] flex items-center">Premium</p>
                @endif
            </div>

            @if (!empty($nameChannel))
            <p class="flex-1 mt-2 md:mt-3 text-left text-darkYellow {{ $fontSize }} text-sm font-semibold">
                {{ $nameChannel}}
            </p>
            @endif

            <p class="font-medium flex-1  {{!empty($nameChannel) ? "mt-1" : "md:mt-4"}} mt-0 text-left {{ $fontSize }} hover:text-Icon05">
                {{ $title }}
            </p>
        </div>
    </a>
</button>
@endif
@else
@if ($type === 'inside')
<div class="relative h-full">
    <a href="{{ $url }}">
        <div class="w-full h-full relative">
            <div class="w-full h-full">
                <x-skeleton containerStyle="h-full w-full"></x-skeleton>
            </div>
        </div>

        <div class="absolute top-0 w-full h-full z-10 bg-custom-radial">
            <x-skeleton containerStyle="text-white absolute bottom-0 py-2 left-0 font-bold px-3 {{ $fontSize }}">&nbsp;</x-skeleton>
        </div>
    </a>
</div>
@else
<div class="h-full w-full flex">
    <div class="w-full">
        <div class="md:block flex justify-between items-start gap-x-5 {{ $breakline ? 'flex-col' : ($isReverse ? 'flex-row-reverse' : 'flex-row') }}">
            <div class="relative shrink-0 {{ $heightImage ?? 'h-full' }} {{ $widthImage ?? 'w-full' }} aspect-[3/2] overflow-hidden">
                <div class="w-full h-full">
                    <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                </div>
            </div>

            @if (!empty($nameChannel))
            <x-skeleton containerStyle="flex-1 mt-2 md:mt-3 text-left text-darkYellow {{ $fontSize }} text-sm font-semibold"> </x-skeleton>
            @endif

            <x-skeleton containerStyle="font-bold flex-1 {{ !empty($nameChannel) ? 'mt-1' : 'md:mt-5' }} {{ $isReverse === true ? 'mt-0' : 'mt-3' }} text-left {{ $fontSize }} w-full"> </x-skeleton>
        </div>
    </div>
</div>
@endif
@endif