@php
$timestamp = null;
if (!empty($datetimeUpdated)) {
preg_match('/\d+/', $datetimeUpdated, $match);
$timestamp = $match ? (int) $match[0] : null;

if ($timestamp) {
$timestamp = $timestamp / 1000; // Convert milliseconds to seconds
$date = (new DateTime())->setTimestamp($timestamp);
}
}
@endphp

<a href="{{ $url ?? '/' }}">
    <div class="bg-white p-2.5 md:p-4 text-start w-full h-full flex flex-col justify-start hover:[box-shadow:1px_2px_2px_0px_#00000026]">
        <div class="w-full">
            <div class="relative w-full aspect-[3/2] overflow-hidden">
                <div class="skeleton-container">
                    <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                </div>
                <img src="{{ $image }}" alt={{ $title }} class="absolute inset-0 w-full h-full object-cover"
                    onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
            </div>
            @if ($timestamp)
            <p class="text-Icon05 mt-2 md:mt-[0.625rem] text-sm font-medium">
                {{ $date->format('n')}}.{{ $date->format('Y')}}
            </p>
            @endif
            <p class="font-medium text-black text-base">
                @if ($isPremiumArticle === true)
                <span class="inline-block">
                    <svg width="15" height="15" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10.5" cy="10.5" r="10.5" fill="#B89659" />
                        <path d="M10.5 4.66663L11.8097 8.69736H16.0478L12.6191 11.1885L13.9288 15.2192L10.5 12.7281L7.07127 15.2192L8.38094 11.1885L4.95219 8.69736H9.19036L10.5 4.66663Z" fill="white" />
                    </svg>
                </span>
                @endif
                {{ $title }}
            </p>
            <p class="text-white opacity-70 text-sm mt-2 md:mt-[0.625rem]">
                {{ $description }}
            </p>
        </div>
    </div>
</a>