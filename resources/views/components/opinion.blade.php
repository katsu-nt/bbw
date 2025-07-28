@php
$subTotalArray = array_chunk($articles, 5);
@endphp

<div class="w-full overflow-hidden relative">
    <div id="{{$idList[0]}}" class="flex transition-transform duration-500 ease-in-out select-none">
        @foreach ($subTotalArray as $subArray)
        <div class="w-full flex-shrink-0 flex flex-col items-center"> <!-- Added flex-shrink-0 -->
            <div class="slide-link w-full"> <!-- Added w-full -->
                @foreach($subArray as $index=> $opinion)
                @php
                $keywords = explode(',', $opinion['Keyword'] ?? '');
                $isPremiumArticle = isPremiumContent($keywords);
                @endphp
                <div
                    class="{{$index === count($subArray) - 1
        ? "pt-2"
        : "py-2 border-b border-b-gray-300"}}">
                    <div class="flex gap-x-3 items-center">
                        <p class="text-sm text-Icon05 font-medium">{{$opinion['AuthorAlias']}}</p>
                        @if ($isPremiumArticle === true)
                        <p class=" bg-BG_Overlay_01 px-2 py-[0.125rem] text-white w-fit text-[0.625rem] z-20 mb-1 font-bold h-4 flex items-center">Premium</p>
                        @endif
                    </div>

                    <div class="font-medium text-start hover:text-Icon05 text-base">
                        <a href="{{ url($opinion['FriendlyTitle'] . '-' . $opinion['PublisherId'] . '.html') ?? '' }}">
                            <span>
                                {{$opinion['Title']}}
                            </span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="flex justify-between mt-4">
    <div id="{{$idList[1]}}" class="flex gap-x-2 py-2">
        @foreach ($subTotalArray as $indexSubTotalArray => $item)
        <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="{{ $indexSubTotalArray }}"></button>
        @endforeach
    </div>
    <div class="flex gap-x-2">
        <button id="{{$idList[0]}}_prev" class="nav-btn prev-btn transition-opacity duration-200">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 7L7 11M7 11L11 15M7 11H15M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="#BEBEBE" stroke-linecap="square" stroke-linejoin="round" />
            </svg>
        </button>

        <button id="{{$idList[0]}}_next" class="nav-btn next-btn transition-opacity duration-200">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 15L15 11M15 11L11 7M15 11H7M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="black" stroke-linecap="square" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.getElementById('{{$idList[0]}}');
        const dots = document.querySelectorAll('#{{$idList[1]}} button');
        const prevBtn = document.getElementById('{{$idList[0]}}_prev');
        const nextBtn = document.getElementById('{{$idList[0]}}_next');

        console.log(document.getElementById('{{$idList[1]}}'))
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
            console.log('Current activeIndex:', activeIndex);

            if (transition) {
                slides.classList.add('transition-transform', 'duration-500', 'ease-in-out');
            } else {
                slides.classList.remove('transition-transform', 'duration-500', 'ease-in-out');
            }

            // This line is crucial - it moves the slides
            slides.style.transform = `translateX(-${activeIndex * 100}%)`;
            console.log(slides)

            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-black', index === activeIndex);
                dot.classList.toggle('bg-gray-300', index !== activeIndex);
            });

            // Update navigation buttons appearance
            updateNavButtons();
        }

        function updateNavButtons() {
            // Update previous button
            if (activeIndex === 0) {
                prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = true;
                const prevPath = prevBtn.querySelector('path');
                if (prevPath) prevPath.setAttribute('stroke', '#BEBEBE');
            } else {
                prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = false;
                const prevPath = prevBtn.querySelector('path');
                if (prevPath) prevPath.setAttribute('stroke', 'black');
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

        // Initialize carousel
        updateCarousel();
    });
</script>