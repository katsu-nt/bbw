<div class="w-full overflow-hidden relative">
    <div id="{{$idList[0]}}" class="flex gap-x-4 transition-transform duration-500 ease-in-out select-none">
        @foreach ($reportList as $index => $report)
        <div class="w-3/4 flex-shrink-0 flex flex-col items-center">
            <div class="slide-link w-full h-full">
                @php
                $datetimeUpdated = $report['PublishedTime'];
                $timestamp = null;
                if (!empty($datetimeUpdated)) {
                preg_match('/\d+/', $datetimeUpdated, $match);
                $timestamp = $match ? (int) $match[0] : null;

                if ($timestamp) {
                $timestamp = $timestamp / 1000; // Convert milliseconds to seconds
                $date = (new DateTime())->setTimestamp($timestamp);
                }
                }

                $title=$report['Name'];
                $image=$report['Thumbnail'];
                $description=$report['Description'];
                $datetimeUpdated=$report['PublishedTime'];
                $url=url($report['FriendlyName'] . '-event' . $report['EventId'] . '.html');
                @endphp

                <div class="bg-white p-4 text-start w-full h-full flex flex-col justify-start hover:[box-shadow:1px_2px_2px_0px_#00000026]">
                    <a href="{{ $url ?? '/' }}" class="w-full">
                        <div class="relative w-full aspect-[3/2] overflow-hidden">
                            <div class="skeleton-container">
                                <x-skeleton containerStyle="h-full w-full"></x-skeleton>
                            </div>
                            <img src="{{ $image }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover"
                                onload="this.style.opacity='1'; this.parentNode.querySelector('.skeleton-container').style.display='none';" />
                        </div>
                        @if ($timestamp)
                        <p class="text-Icon05 mt-4 md:mt-[0.625rem] text-sm font-medium">
                            {{ $date->format('n')}}.{{ $date->format('Y')}}
                        </p>
                        @endif
                        <p class="font-medium text-black text-base">
                            {{ $title }}
                        </p>
                        <p class="text-white opacity-70 text-sm mt-2 md:mt-[0.625rem]">
                            {{ $description }}
                        </p>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="flex justify-between mt-4">
    <button id="{{$idList[0]}}_prev" class="nav-btn prev-btn transition-opacity duration-200">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 7L7 11M7 11L11 15M7 11H15M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="#BEBEBE" stroke-linecap="square" stroke-linejoin="round" />
        </svg>
    </button>

    <div id="{{$idList[1]}}" class="flex gap-x-2 py-2">
        @foreach ($reportList as $indexSubTotalArray => $item)
        <button class="w-2.5 h-2.5 rounded-full bg-gray-300" data-index="{{ $indexSubTotalArray }}"></button>
        @endforeach
    </div>

    <div class="flex gap-x-2">
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

        // FIXED: Better calculation for positioning slides
        function calculateCenterTransform(index) {
            const slideElements = slides.children;
            if (slideElements.length === 0) return 0;

            const slideWidth = slideElements[0].offsetWidth;
            const gapWidth = 16; // gap-x-4 = 1rem = 16px
            const containerWidth = slides.parentElement.offsetWidth;

            // Calculate total content width
            const totalContentWidth = (slideWidth + gapWidth) * totalSlides - gapWidth;

            // If total content fits in container, don't translate
            if (totalContentWidth <= containerWidth) {
                return 0;
            }

            // Calculate position for each slide
            const slideWithGapWidth = slideWidth + gapWidth;
            const slidePosition = index * slideWithGapWidth;

            // For the last slide, align it to the right edge
            if (index === totalSlides - 1) {
                return Math.max(0, totalContentWidth - containerWidth);
            }

            // For other slides, try to center them, but don't exceed the last slide position
            const centerOffset = (containerWidth - slideWidth) / 2;
            const centeredPosition = slidePosition - centerOffset;
            const maxTranslate = totalContentWidth - containerWidth;

            return Math.min(maxTranslate, Math.max(0, centeredPosition));
        }

        function updateCarousel(transition = true) {
            if (transition) {
                slides.classList.add('transition-transform', 'duration-500', 'ease-in-out');
            } else {
                slides.classList.remove('transition-transform', 'duration-500', 'ease-in-out');
            }

            // Calculate and apply transform
            const translateX = calculateCenterTransform(activeIndex);
            slides.style.transform = `translateX(-${translateX}px)`;

            // Update dots
            dots.forEach((dot, index) => {
                if (index === activeIndex) {
                    dot.classList.remove('bg-gray-300');
                    dot.classList.add('bg-black');
                } else {
                    dot.classList.remove('bg-black');
                    dot.classList.add('bg-gray-300');
                }
            });

            // Update navigation buttons
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
            if (index >= 0 && index < totalSlides && index !== activeIndex) {
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
            const rect = slides.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
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

        // Touch and mouse drag functionality
        slides.addEventListener('mousedown', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDragging);

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

            // Only handle horizontal drag
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 5) {
                e.preventDefault();

                const currentTranslateX = calculateCenterTransform(activeIndex);
                const newTranslateX = currentTranslateX - diffX;

                // Add boundary constraints with resistance
                const maxTranslateX = calculateCenterTransform(totalSlides - 1);
                const minTranslateX = 0;

                if (newTranslateX < minTranslateX) {
                    // At the beginning, add resistance
                    const resistance = Math.max(0.3, 1 - Math.abs(newTranslateX) / 100);
                    slides.style.transform = `translateX(${Math.abs(newTranslateX) * resistance}px)`;
                } else if (newTranslateX > maxTranslateX) {
                    // At the end, add resistance
                    const resistance = Math.max(0.3, 1 - Math.abs(newTranslateX - maxTranslateX) / 100);
                    slides.style.transform = `translateX(-${maxTranslateX + (newTranslateX - maxTranslateX) * resistance}px)`;
                } else {
                    slides.style.transform = `translateX(-${newTranslateX}px)`;
                }

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
            const threshold = 50; // Fixed threshold instead of percentage

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

            // Reset hasMoved flag after a short delay
            setTimeout(() => {
                hasMoved = false;
            }, 100);
        }

        // Prevent image dragging
        slides.querySelectorAll('img').forEach(img => {
            img.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            updateCarousel(false);
        });

        // Initialize carousel - this ensures the first item is properly positioned
        setTimeout(() => {
            updateCarousel(false);
        }, 100);
    });
</script>