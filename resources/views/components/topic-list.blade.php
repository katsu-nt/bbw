<div>
    @if ($oneColumn === false)
    @if (!empty($title))
    <p class="text-white font-bold py-1 px-3 mb-2 {{ $titleSize }} ">{{ $title }}</p>
    @endif

    <div class="{{ count($itemList) > 8 ? 'grid grid-cols-3 gap-x-5' : 'flex flex-col' }} {{ $gap }} text-sm">
        @if ($isCategory)
        {{-- ICategory Type --}}
        @foreach ($itemList as $cate)
        <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-sm text-nowrap py-1 px-3">
            <a href={{ url($cate['slug']) }}
                {{ $isOpenNewTab === true ? 'target="_blank"' : '' }}>{{ $cate['cate_name'] }}</a>
        </div>
        @endforeach

        @else
        {{-- IEvent Type --}}
        @foreach ($itemList as $event)
        <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start py-1 px-3 {{ $gap }}">
            <a href="{{ url($event['FriendlyName'] . '-event' . $event['EventId'] . '.html') ?? '' }}"
                {{ $isOpenNewTab === true ? 'target="_blank"' : '' }}>{{ $event['Name'] }}</a>
        </div>
        @endforeach
        @endif
    </div>
    @else
    @if (!empty($title))
    <div class="flex gap-x-2 items-center w-full border-b border-Line_03 py-2.5">
        {{ $icon }}
        <p class="text-white font-bold  {{ $icon? "leading-6" : "leading-9" }}">
            {{ $title }}
        </p>
    </div>
    @endif

    <div class="flex flex-col">
        @if ($isCategory)
        {{-- ICategory Type --}}
        @foreach ($itemList as $cate)
        <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start text-nowrap  {{ $icon? "leading-6" : "leading-9" }} py-2.5 border-b border-Line_03
            {{$title ? "opacity-80" : ""}}">
            <a href="{{ url($cate['slug']) }}" {{ $isOpenNewTab === true ? 'target="_blank"' : '' }} class="w-full {{ $icon ? "pl-7": "" }}">
                {{ $cate['cate_name'] }}
            </a>
        </div>
        @endforeach

        @else
        {{-- IEvent Type --}}
        @foreach ($itemList as $event)
        <div class="text-white hover:text-Icon03 hover:bg-Gray_15 text-start {{ $gap }} {{ $icon? "leading-6" : "leading-9" }} py-2.5 border-b border-Line_03 {{ $title ? 'opacity-80' : '' }}">
            <a href="{{ url($event['FriendlyName'] . '-event' . $event['EventId'] . '.html') ?? '' }}" class="w-full {{ $icon ? "pl-7": "" }}"
                {{ $isOpenNewTab === true ? 'target="_blank"' : '' }}>
                {{ $event['Name'] }}
            </a>
        </div>
        @endforeach
        @endif
    </div>
    @endif

</div>