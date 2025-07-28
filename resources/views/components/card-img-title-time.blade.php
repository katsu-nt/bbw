<button class="w-full">
    <a href={{$url || "/" }}>
        <div class="grid grid-cols-5 pt-7.5">
            <div class="col-span-3 md:col-span-4 md:grid md:grid-cols-4 md:gap-x-5">
                <p class="text-sm text-[#B89659] text-nowrap text-start">
                    {{$datetimeUpdated}}
                </p>
                <p class="font-bold text-lg w-5/6 col-span-3 hover:text-Icon05 text-start">
                    {{$title}}
                </p>
            </div>

            <div class="col-span-2 md:col-span-1 w-full relative aspect-[3/2] overflow-hidden">
                <!-- Skeleton Loader: This will be visible until the image has loaded -->
                <div class="skeleton-container">
                    <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                </div>
                <img
                    src={{$image}}
                    alt={{$title}}
                    class="absolute inset-0 w-full h-full object-cover"
                    onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
            </div>
        </div>
    </a>
</button>