<div class="relative">
    <div class="relative {{$width}} {{$height}} overflow-hidden">
        <img src="{{ $image }}" alt="" class="w-full h-full {{ $isLongAds ? '' : '' }} object-cover" />
    </div>
    <div class="absolute top-0 left-0 z-10">
        <div class="bg-gray-300 p-2 h-200 w-200 text-sm italic font-semibold">
            Ad Banner
        </div>
    </div>
</div>