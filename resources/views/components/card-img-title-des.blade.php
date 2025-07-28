@if ($isLoading === false)
<button class="w-full text-left">
    <a href={{$url}}>
        <div class="w-full">
            <div class="relative w-full aspect-[3/2] overflow-hidden">
                <!-- Skeleton Loader: This will be visible until the image has loaded -->
                <div class="skeleton-container">
                    <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                </div>
                <img src={{$image}} alt={{$title}} class="absolute inset-0 w-full h-full object-cover "
                    onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />

                @if ($isPremiumArticle === true)
                <p class=" bg-BG_Overlay_01 px-2.5 absolute top-0 left-0 h-6 font-semibold text-sm text-white flex items-center">Premium</p>
                @endif


            </div>
            <p class="text-2xl font-bold mt-4 hover:text-Icon05">
                {{ $title}}
            </p>
            <p class="text-sm text-black mt-2 md:mt-4 no-underline">
                {{ $description }}
            </p>
        </div>
    </a>
</button>
@else
<div class="w-full text-left">
    <div class="w-full">
        <div class="relative w-full aspect-[3/2] overflow-hidden">
            <!-- Skeleton Loader: This will be visible until the image has loaded -->
            <div class="w-full h-full">
                <x-skeleton containerStyle="h-full w-full"></x-skeleton>
            </div>

        </div>
        <x-skeleton containerStyle="text-2xl font-bold mt-4 mt-[1.125rem] w-full h-full">&nbsp;</x-skeleton>
        <x-skeleton containerStyle="text-sm text-black mt-4 w-full h-full">&nbsp;</x-skeleton>
    </div>
</div>
@endif