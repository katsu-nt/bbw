<?php
$content = file_get_contents($articleDetail['Content']);
?>
@if($canView === true)
{!! $content !!}
@else
<?php
$paragraphs = preg_split('/(<\/section>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
$firstThreeParagraphs = array_slice($paragraphs, 0, 3); // Each <p> and </p> are separate items
$contentFirstThree = sanitizeHtml(implode('', $firstThreeParagraphs));
?>

<div class="relative">
    {!! $contentFirstThree !!}
    <div style="background:  linear-gradient(to top, white, rgba(255, 255, 255, 0.5), transparent); pointer-events: none; z-index: 20;" class="absolute left-0 bottom-0 w-full h-full">
    </div>
</div>
@endif

<div class="flex justify-center mt-4">
    <div class="w-[630px] px-[15px]">
        <div class="pb-4">
            @if ($articleDetail['SourceType'] === 11 || $articleDetail['SourceType'] === 12)
            <p class="text-gray italic text-lg">
                Theo Bloomberg
            </p>
            @endif
        </div>
        <div class="md:px-[15px]">
            <section class="w-full relative flex flex-wrap justify-end mb-7 text-right">
                <p class="italic text-right w-full" style="color:#b5b5b5ee;font-size: 10px; line-height:1.2 !important; margin: 0 !important;">Theo phattrienxanh.baotainguyenmoitruong.vn</p>
                <p
                    style="color:#b5b5b5ee;font-size: 10px; line-height:1.2 !important; margin: 0 !important;"
                    class="italic text-right w-full break-all">
                    https://phattrienxanh.baotainguyenmoitruong.vn{{ request()->getPathInfo() }}
                </p>
            </section>
        </div>

        <!-- HANDLE KEYWORD -->
        @if (!empty($articleDetail['Keyword']))
        @php
        // Convert keywords into an array for processing
        $keywords = explode(",", $articleDetail['Keyword']);
        @endphp

        <!-- Skip rendering if there's only one keyword and it's 'Premium' -->
        @if (!(count($keywords) === 1 && trim($keywords[0]) === 'Premium'))
        <section class="flex flex-wrap gap-y-3 gap-x-3 pb-7 pt-7.5 border-t border-[#B2B2B2] border-dashed">
            @foreach ($keywords as $keyword)
            @if (trim($keyword) !== 'Premium')
            <div class="p-2 rounded-full text-xs border border-black py-2.5 px-5 text-nowrap font-semibold hover:bg-black hover:text-white">
                <a href="/search?q={{ trim($keyword) }}">#{{ trim($keyword) }}</a>
            </div>
            @endif
            @endforeach
        </section>
        @endif
        @endif
    </div>
</div>

<script>
    function saveLinkPTX() {
        const ptxURL = "https://phattrienxanh.baotainguyenmoitruong.vn"
        const currentPageUrl = window.location.pathname;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            // Use Clipboard API if supported
            navigator.clipboard.writeText(ptxURL + currentPageUrl)
            const popover = document.getElementById("popover-section-below");

            if (popover) {
                popover.classList.remove("hidden");
                setTimeout(() => {
                    popover.classList.add("hidden");
                }, 2000); // 2 seconds delay
            } else {
                console.error('Popover element not found');
            }
        }
    }
</script>