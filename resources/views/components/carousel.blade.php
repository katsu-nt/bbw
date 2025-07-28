@if ($isLoadingArticleList === true)
<div class="flex flex-col items-center">
    <div class="w-full overflow-hidden relative">
        <div id={{$idList[0]}} class="flex transition-transform duration-500 ease-in-out select-none">
            <?php for ($index = 0; $index < 6; $index++): ?>
                <div class="w-full flex-shrink-0 flex flex-col items-center">
                    <div class="w-full h-full slide-link">
                        <div class="w-full aspect-[3/2] relative overflow-hidden">
                            <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                        </div>

                        <x-skeleton containerStyle="text-xl md:text-2xl mt-3 md:mt-7.5 font-bold text-center">&nbsp;</x-skeleton>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <div id={{$idList[1]}} class="flex gap-x-3 mt-6 md:mt-9">
        <?php for ($index = 0; $index < 6; $index++): ?>
            <button class="w-3 h-3 rounded-full bg-gray-300" data-index="<?= $index ?>"></button>
        <?php endfor; ?>
    </div>
</div>
@elseif($isLoadingMatterPrintedList === true)
<div class="flex flex-col items-center">
    <div class="w-full overflow-hidden relative">
        <div id={{$idList[0]}} class="flex transition-transform duration-500 ease-in-out select-none">
            <?php for ($index = 0; $index < 3; $index++): ?>
                <div class="w-full flex-shrink-0">
                    <div class="w-full h-full slide-link">
                        <div class="w-full relative overflow-hidden" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                            <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                        </div>
                        <x-skeleton containerStyle="text-base pt-3 md:pt-5 font-bold text-center">&nbsp;</x-skeleton>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <div id={{$idList[1]}} class="flex gap-x-2.5 mt-3 md:mt-5">
        <?php for ($index = 0; $index < 3; $index++): ?>
            <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="<?= $index ?>"></button>
        <?php endfor; ?>
    </div>
</div>
@else
<div class="flex flex-col items-center">
    <div class="w-full overflow-hidden relative">
        @if(!empty($articleList))
        <div id={{$idList[0]}} class="flex transition-transform duration-500 ease-in-out select-none">
            <?php foreach ($articleList as $index => $article): ?>
                @php
                $keywords = explode(',', $article['Keyword'] ?? '');
                $isPremiumArticle = isPremiumContent($keywords);
                @endphp
                <div class="w-full flex-shrink-0 flex flex-col items-center">
                    <a href="{{ url($article['FriendlyTitle'] . '-' . $article['PublisherId'] . '.html') ?? '' }}" class="slide-link">
                        <div class="w-full aspect-[3/2] relative overflow-hidden">
                            <img
                                src="<?= $article['Thumbnail_1050x700'] ?>"
                                alt=""
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="text-xl md:text-2xl pt-3 md:pt-7.5 font-bold text-center">
                            {{$article['Title']}}
                        </p>
                    </a>
                    @if($isPremiumArticle)
                    <p class=" bg-darkYellow px-2 py-1 mt-3 border-darkYellow border text-white w-fit  text-[0.563rem] tracking-[.2em] z-20">PREMIUM</p>
                    @endif
                </div>
            <?php endforeach; ?>
        </div>
        @else
        <div id={{$idList[0]}} class="flex transition-transform duration-500 ease-in-out select-none">
            <?php foreach ($matterPrintedList as $index => $matterPrinted): ?>
                @php
                $date = new DateTime($matterPrinted['created_at']);
                $formattedDate = $date->format('m.Y');
                $month = $date->format('n');
                $year = $date->format('Y');
                @endphp
                <div class="w-full flex-shrink-0">
                    <a href="{{ url('https://bloombergbusinessweek.bbw.vn/an-pham-thang-' . $month . '-' . $year . '/') }}" class="slide-link">
                        <div class="w-full relative overflow-hidden" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                            <img
                                src=" <?= $matterPrinted['image_url'] ?>"
                                alt=""
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="text-base pt-3 md:pt-5 font-bold text-center">
                            Tháng {{ $formattedDate }}
                        </p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        @endif
    </div>

    @if($articleList)
    <div id={{$idList[1]}} class="flex gap-x-3 mt-6 md:mt-9">
        <?php foreach ($articleList as $index => $article): ?>
            <button class="w-3 h-3 rounded-full bg-gray-300" data-index="<?= $index ?>"></button>
        <?php endforeach; ?>
    </div>
    @else
    <div id={{$idList[1]}} class="flex gap-x-2.5 mt-3 md:mt-5">
        <?php foreach ($matterPrintedList as $index => $matterPrinted): ?>
            <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="<?= $index ?>"></button>
        <?php endforeach; ?>
    </div>
    @endif
</div>
@endif


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.getElementById('{{$idList[0]}}');
        const dots = document.querySelectorAll('#{{$idList[1]}} button');
        const totalSlides = dots.length;
        let activeIndex = 0;
        let startX = 0;
        let startY = 0;
        let currentX = 0;
        let currentY = 0;
        let isDragging = false;
        let hasMoved = false;

        function updateCarousel(transition = true) {
            if (transition) {
                slides.classList.add('transition-transform', 'duration-500', 'ease-in-out');
            } else {
                slides.classList.remove('transition-transform', 'duration-500', 'ease-in-out');
            }
            slides.style.transform = `translateX(-${activeIndex * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-black', index === activeIndex);
                dot.classList.toggle('bg-gray-300', index !== activeIndex);
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                activeIndex = index;
                updateCarousel();
            });
        });

        // Mouse events
        slides.addEventListener('mousedown', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDragging);

        // Touch events
        slides.addEventListener('touchstart', startDragging);
        document.addEventListener('touchmove', drag, {
            passive: false
        });
        document.addEventListener('touchend', stopDragging);

        // Prevent click when dragging
        const links = slides.querySelectorAll('.slide-link');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (hasMoved) {
                    e.preventDefault();
                }
            });
        });

        function startDragging(e) {
            isDragging = true;
            hasMoved = false;
            startX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
            startY = e.type.includes('mouse') ? e.pageY : e.touches[0].pageY;
            slides.classList.remove('transition-transform', 'duration-500', 'ease-in-out');
        }

        function drag(e) {
            if (!isDragging) return;

            currentX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
            currentY = e.type.includes('mouse') ? e.pageY : e.touches[0].pageY;

            const diffX = currentX - startX;
            const diffY = currentY - startY;
            const percentage = (diffX / slides.offsetWidth) * 100;
            const newTranslate = -activeIndex * 100 + percentage;

            // If horizontal movement is greater than vertical and exceeds 10 pixels
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 5) {
                e.preventDefault(); // Prevent vertical scrolling
                slides.style.transform = `translateX(${newTranslate}%)`;
                hasMoved = true;
            }
        }

        function stopDragging(e) {
            if (!isDragging) return;
            isDragging = false;

            const endX = e.type.includes('mouse') ? e.pageX : (e.changedTouches ? e.changedTouches[0].pageX : currentX);
            const diff = endX - startX;
            const threshold = slides.offsetWidth * 0.3;

            if (Math.abs(diff) > threshold) {
                if (diff > 0 && activeIndex > 0) {
                    activeIndex--;
                } else if (diff < 0 && activeIndex < totalSlides - 1) {
                    activeIndex++;
                }
            }
            updateCarousel();
        }

        slides.querySelectorAll('img').forEach(img => {
            img.addEventListener('dragstart', (e) => e.preventDefault());
        });

        updateCarousel();
    });
</script>