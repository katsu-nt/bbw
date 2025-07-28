<div class="text-start w-full h-full">
    <a href={{ $url}} class="w-full text-start">
        <div class="flex flex-col gap-y-2">
            <div class="flex gap-x-3 items-center">
                <p class="text-white text-sm">{{ $category }}</p>

                @if ($isPremiumArticle === true)
                <p class="px-2 py-1 border-darkYellow border text-white bg-darkYellow w-fit  text-[0.375rem] tracking-[.2em]">PREMIUM</p>
                @endif

            </div>

            <p class="font-semibold text-lg">
                <span class=" hover:underline text-base text-white">
                    {{ $title }}
                </span>
            </p>
        </div>
    </a>
</div>