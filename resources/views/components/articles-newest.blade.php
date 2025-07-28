@if (!empty($listArticleNewest))
@foreach($listArticleNewest as $index => $article)
<a href="{{url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html')}}">
    <div class="flex gap-x-2 py-2 {{ $index === 5 ? '' : 'border-b border-b-Line_00' }} hover:text-Icon05">
        <p class="w-14 text-xs {{ isTimeGreaterThanHours($article['PublishedTime'], 4)  ? "text-Icon05": "text-TitleHighlight"}}  font-medium">
            {{ convertPublishedTime($article['PublishedTime']) }}
        </p>
        <p class="font-medium flex-1 hover:text-">{{ $article['Title'] }}</p>
    </div>
</a>

@endforeach
@else
<div>Không có bài viết nào.</div>
@endif