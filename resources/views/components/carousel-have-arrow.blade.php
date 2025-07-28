<!-- TYPE != YKIEN -->
<div class="w-full overflow-hidden relative">
    <div id="{{$idList[0]}}" class="flex transition-transform duration-500 ease-in-out select-none">
        @foreach ($itemList as $index => $item)
        @php
        $formattedDate = null;
        $url = null;

        if (!empty($item['created_at'])) {
        $date = new DateTime($item['created_at']);
        $month = $date->format('n');
        $year = $date->format('Y');
        $formattedDate = 'Tháng ' . $date->format('m.Y');
        }

        if (!empty($type)) {
        switch ($type) {
        case 'chartOfWeek':
        $url = "/" . $item['FriendlyTitle'] . "-" . $item['PublisherId'] . ".html";
        break;

        case 'chuyenDe':
        $url = "/" . $item['FriendlyTitle'] . "-" . $item['PublisherId'] . ".html";
        break;

        case 'matterprinteds':
        $url = url("https://bloombergbusinessweek.bbw.vn/an-pham-thang-$month-$year/");
        break;
        }
        }
        @endphp


        @if ($url)
        <a href={{ $url }} class="w-full flex-shrink-0 flex flex-col items-center">
            @endif
            <div class="w-full flex-shrink-0 flex flex-col items-center">
                <div class="slide-link">
                    <div class="w-full {{ $aspect ?? 'aspect-[3/2]' }} relative overflow-hidden border border-Gray_04"
                        style="box-shadow: 1px 2px 2px 0px #00000026;">
                        <img
                            src="{{ $item['Image'] ?? ($thumbnail === '1050x700' && isset($item['Thumbnail_1050x700']) ? $item['Thumbnail_1050x700'] : ($item['Thumbnail_540x360'] ?? $item['image_url'] ?? '')) }}"
                            alt=""
                            class="w-full h-full object-cover" />
                    </div>

                    @if ($hasTitle)
                    <div class="pt-4 text-3xl md:text-4xl font-bold hover:text-Icon05">
                        {{ $item['Title'] }}
                    </div>
                    @endif

                    <p class="py-4 {{ $textCustom ?? 'text-sm font-medium' }}">
                        {{ $item['Description'] ?? $item['Headlines'] ?? $formattedDate }}
                    </p>

                    @if ($hasHashtag)
                    @php
                    $keywords = explode(', ', $item['Tags']);
                    @endphp
                    <div class="flex flex-wrap gap-x-2 mb-4 gap-y-2">
                        @foreach ($keywords as $keyword)
                        <a href="/search?q={{ trim($keyword) }}">
                            <div class="border rounded-full py-1 px-4 text-Icon05 border-Gray_14 text-sm hover:bg-Gray_05 hover:text-black">
                                #{{ $keyword }}
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @if ($url)
        </a>
        @endif
        @endforeach
    </div>
</div>

<div class="flex justify-between">
    <button id="{{$idList[0]}}_prev_phone" class="lg:hidden block nav-btn prev-btn transition-opacity duration-200 hover:bg-Gray_05 rounded-full w-[22px] h-[22px]">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 7L7 11M7 11L11 15M7 11H15M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="#BEBEBE" stroke-linecap="square" stroke-linejoin="round" />
        </svg>
    </button>

    <div id="{{$idList[1]}}" class="flex gap-x-2 py-2">
        @foreach ($itemList as $index => $item)
        <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="{{ $index }}"></button>
        @endforeach
    </div>
    <div class="flex gap-x-2">
        <button id="{{$idList[0]}}_prev" class="lg:block hidden nav-btn prev-btn transition-opacity duration-200 hover:bg-Gray_05 rounded-full w-[22px] h-[22px]">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 7L7 11M7 11L11 15M7 11H15M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="#BEBEBE" stroke-linecap="square" stroke-linejoin="round" />
            </svg>
        </button>

        <button id="{{$idList[0]}}_next" class="nav-btn next-btn transition-opacity duration-200 hover:bg-Gray_05 rounded-full w-[22px] h-[22px]">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 15L15 11M15 11L11 7M15 11H7M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>

@if ($type === 'chartOfWeek')
<a class="w-full h-full view-details-link" href="/">
    <div class="w-full py-3 px-4 mt-4 border rounded-lg font-semibold hover:bg-Gray_05 border-black text-center">
        <p>Xem chi tiết</p>
    </div>
</a>
@endif


{{ $children }}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.getElementById('{{$idList[0]}}');
        const dots = document.querySelectorAll('#{{$idList[1]}} button');
        const prevBtn = document.getElementById('{{$idList[0]}}_prev');
        const prevBtnPhone = document.getElementById('{{$idList[0]}}_prev_phone');
        const nextBtn = document.getElementById('{{$idList[0]}}_next');
        const viewDetailsLink = document.querySelector('.view-details-link');

        // Pass PHP itemList to JavaScript
        const itemList = @json($itemList);
        const type = '{{ $type }}';

        // Add null checks
        if (!slides || !dots.length || !prevBtn || !nextBtn) {
            console.error('Carousel elements not found');
            return;
        }

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

            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-black', index === activeIndex);
                dot.classList.toggle('bg-gray-300', index !== activeIndex);
            });

            // Update navigation buttons appearance
            updateNavButtons();

            // Update view details link href for chartOfWeek
            if (type === 'chartOfWeek' && viewDetailsLink && itemList[activeIndex]) {
                const item = itemList[activeIndex];
                const url = `/${item.FriendlyTitle}-${item.PublisherId}.html`;
                viewDetailsLink.setAttribute('href', url);
            }
        }

        function updateNavButtons() {
            // Update previous button
            if (activeIndex === 0) {
                prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = true;
                const prevPath = prevBtn.querySelector('path');
                if (prevPath) prevPath.setAttribute('stroke', '#BEBEBE');

                prevBtnPhone.classList.add('opacity-50', 'cursor-not-allowed');
                prevBtnPhone.disabled = true;
                const prevPathPhone = prevBtnPhone.querySelector('path');
                if (prevPathPhone) prevPathPhone.setAttribute('stroke', '#BEBEBE');
            } else {
                prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = false;
                const prevPath = prevBtn.querySelector('path');
                if (prevPath) prevPath.setAttribute('stroke', 'black');

                prevBtnPhone.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtnPhone.disabled = false;
                const prevPathPhone = prevBtnPhone.querySelector('path');
                if (prevPathPhone) prevPathPhone.setAttribute('stroke', 'black');
            }

            // Update next button
            if (activeIndex === totalSlides - 1) {
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                nextBtn.disabled = true;
                const nextPath = nextBtn.querySelector('path');
                if (nextPath) nextPath.setAttribute('stroke', '#BEBEBE');
            } else {
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                nextBtn.disabled = false;
                const nextPath = nextBtn.querySelector('path');
                if (nextPath) nextPath.setAttribute('stroke', 'black');
            }
        }

        function goToPrevious() {
            if (activeIndex > 0) {
                activeIndex--;
                updateCarousel();
            }
        }

        function goToNext() {
            if (activeIndex < totalSlides - 1) {
                activeIndex++;
                updateCarousel();
            }
        }

        function goToSlide(index) {
            if (index >= 0 && index < totalSlides) {
                activeIndex = index;
                updateCarousel();
            }
        }

        // Navigation button event listeners
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!prevBtn.disabled) {
                goToPrevious();
            }
        });

        prevBtnPhone.addEventListener('click', (e) => {
            e.preventDefault();
            if (!prevBtnPhone.disabled) {
                goToPrevious();
            }
        });


        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!nextBtn.disabled) {
                goToNext();
            }
        });

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (slides.getBoundingClientRect().top < window.innerHeight &&
                slides.getBoundingClientRect().bottom > 0) {
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        goToPrevious();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        goToNext();
                        break;
                }
            }
        });

        // Mouse events
        slides.addEventListener('mousedown', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDragging);

        // Touch events
        slides.addEventListener('touchstart', startDragging, {
            passive: false
        });
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

        function getEventX(e) {
            return e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
        }

        function getEventY(e) {
            return e.type.includes('mouse') ? e.pageY : e.touches[0].pageY;
        }

        function startDragging(e) {
            isDragging = true;
            hasMoved = false;
            startX = getEventX(e);
            startY = getEventY(e);
            slides.classList.remove('transition-transform', 'duration-500', 'ease-in-out');
        }

        function drag(e) {
            if (!isDragging) return;

            currentX = getEventX(e);
            currentY = getEventY(e);

            const diffX = currentX - startX;
            const diffY = currentY - startY;

            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 5) {
                e.preventDefault();

                const percentage = (diffX / slides.offsetWidth) * 100;
                let newTranslate = -activeIndex * 100 + percentage;

                if (activeIndex === 0 && diffX > 0) {
                    newTranslate = Math.min(newTranslate, percentage * 0.3);
                }

                if (activeIndex === totalSlides - 1 && diffX < 0) {
                    const baseTranslate = -activeIndex * 100;
                    newTranslate = Math.max(newTranslate, baseTranslate + (percentage * 0.3));
                }

                slides.style.transform = `translateX(${newTranslate}%)`;
                hasMoved = true;
            }
        }

        function stopDragging(e) {
            if (!isDragging) return;
            isDragging = false;

            let endX;
            if (e.type.includes('mouse')) {
                endX = e.pageX;
            } else if (e.changedTouches && e.changedTouches.length > 0) {
                endX = e.changedTouches[0].pageX;
            } else {
                endX = currentX;
            }

            const diff = endX - startX;
            const threshold = slides.offsetWidth * 0.3;

            if (Math.abs(diff) > threshold) {
                if (diff > 0 && activeIndex > 0) {
                    goToPrevious();
                } else if (diff < 0 && activeIndex < totalSlides - 1) {
                    goToNext();
                } else {
                    updateCarousel();
                }
            } else {
                updateCarousel();
            }
        }

        // Prevent image dragging
        slides.querySelectorAll('img').forEach(img => {
            img.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // Initialize carousel
        updateCarousel();
    });
</script>