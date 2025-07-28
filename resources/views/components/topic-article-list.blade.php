<!-- TITLE -->
@if (!empty($articleList))
<div class="font-bold mb-3 md:mb-4 text-lg">
    <a href="/{{mb_strtolower($articleList[0]['Channel']['FriendlyName'])}}">{{$title}}</a>
</div>

<!-- TWO ARTICLE -->
<div class="w-full flex flex-col gap-y-3 md:flex-row md:gap-x-7 md:justify-between">
    @foreach(array_slice($articleList,0, 2) as $index => $article)
    <div
        class="md:flex-1 {{
            $index === 1
                ? ""
                : "pb-3 border-b border-b-gray-300 md:pb-0 md:border-0"
            }}">
        @php
        $keywords = explode(',', $article['Keyword'] ?? '');
        $isPremiumArticle = isPremiumContent($keywords);
        @endphp
        <x-card-img-title-des
            :image="$article['Thumbnail_1050x700']"
            :heightImage="'h-auto'"
            :title="$article['Title']"
            :description="$article['Headlines']"
            :url="url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')"
            :isPremiumArticle="$isPremiumArticle" />
    </div>
    @endforeach
</div>

<!-- THREE ARTICLE -->
<div class=" flex flex-col gap-y-3 items-start mt-3 pt-3 md:mt-7.5 md:pt-7.5 border-t border-t-gray-300 w-full md:grid md:grid-cols-3 md:gap-x-7">
    @foreach(array_slice($articleList, 2, 3) as $index => $article)
    <div class="flex-1 w-full">
        <div>
            @php
            $keywords = explode(',', $article['Keyword'] ?? '');
            $isPremiumArticle = isPremiumContent($keywords);
            @endphp

            <x-card-img-title
                :heightImage="'h-auto'"
                :widthImage="'w-[7.75rem] md:w-full'"
                :title="$article['Title']"
                :image="$article['Thumbnail_600x315']"
                :url="url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')"
                :isPremiumArticle="$isPremiumArticle" />
            <div class="w-full md:w-0 {{ $index === count(array_slice($articleList, 2, 3)) - 1 ? '' : 'pb-3 md:pb-0 border-b border-gray-300' }}"></div>
        </div>
    </div>
    @endforeach
</div>
@else
<!-- TWO ARTICLE -->
<div class="w-full flex flex-col gap-y-3 md:flex-row md:gap-x-7 md:justify-between">
    @for($i = 0; $i < 2; $i++)
        <div
        class="md:flex-1 {{
            $i === 1
                ? ""
                : "pb-3 border-b border-b-gray-300 md:pb-0 md:border-0"
            }}">
        <x-card-img-title-des
            :image="''"
            :heightImage="''"
            :title="''"
            :description="''"
            :url="''"
            :isPremiumArticle="false"
            :isLoading="true" />
</div>
@endfor
</div>

<!-- THREE ARTICLE -->
<div class="flex flex-col gap-y-3 items-start mt-3 pt-3 md:mt-7.5 md:pt-7.5 border-t border-t-gray-300 w-full md:grid md:grid-cols-3 md:gap-x-7">
    @for($i = 0; $i < 3; $i++)
        <div class="flex-1 w-full">
        <x-card-img-title
            :heightImage="'h-auto'"
            :widthImage="'w-[7.75rem] md:w-full'"
            :title="''"
            :image="''"
            :url="''"
            :isPremiumArticle="false"
            :isLoading="true" />
        <div class="w-full md:w-0 {{ $i === 2 ? '' : 'pb-3 md:pb-0 border-b border-gray-300' }}"></div>
</div>
@endfor
</div>
@endif