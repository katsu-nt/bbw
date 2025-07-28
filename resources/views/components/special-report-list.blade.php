<a href="/bao-cao-dac-biet">
    <p class="font-bold text-lg mb-4">{{$title}}</p>
</a>

<div class="grid grid-cols-2 md:grid-cols-4 gap-y-3 gap-x-3 md:gap-x-5">
    @foreach ($reportList as $index => $report)
    @php
    $keywords = explode(',', $report['Keyword'] ?? '');
    $isPremiumArticle = isPremiumContent($keywords);
    @endphp

    <div>
        <x-card-report
            :title="$report['Name']"
            :image="$report['Thumbnail']"
            :description="$report['Description']"
            :datetimeUpdated="$report['PublishedTime']"
            :url="url($report['FriendlyName'] . '-event' . $report['EventId'] . '.html')"
            :isPremiumArticle="$isPremiumArticle" />
    </div>
    @endforeach
</div>