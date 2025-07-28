<div class="flex flex-col gap-y-5">
    @foreach($article as $index => $news)
    <div class="flex gap-x-2.5 {{$index === count($article) - 1 ? '': 'pb-5 border-b border-gray-300'}}">
        <!-- CHECK HAS NAME CHANNEL -->
        @if (!empty($isNeedNameChannel))
        <p class="text-xs text-mediumGray font-bold w-1/4">
            {{$news['NameChannel']}}
        </p>
        @endif

        <!-- CHECK HAS TIME CHANNEL -->
        @if (!empty($isNeedTime))
        <p class=" text-xs text-darkYellow font-bold w-1/4">
            {{$news['TimeX']}}
        </p>
        @endif



        <a class="flex gap-x-3" href="{{ url($news['FriendlyTitle'] . '-' . $news['PublisherId'] . '.html') ?? '' }}"
            class="{{empty($isNeedNameChannel) && empty($isNeedTime) ? "w-full" : "w-3/4"}} font-semibold text-start text-bold {{$isNeedNameChannel ? "col-span-3" : "col-span-4"}}">
            <span class="font-semibold hover:underline">
                {{$news['Title']}}
            </span>

            @if ($isNeedCheckPremium == true)
            @php
            $keywords = explode(',', $news['Keyword'] ?? '');
            $isPremiumArticle = isPremiumContent($keywords);
            @endphp

            @if ($isPremiumArticle === true)
            <p class=" bg-darkYellow px-2 py-1 border-darkYellow border text-white w-fit h-fit  text-[0.563rem] tracking-[.2em] z-20">PREMIUM</p>
            @endif
            @endif
        </a>
    </div>
    @endforeach
</div>