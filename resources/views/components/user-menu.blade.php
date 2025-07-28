<div class="flex gap-x-2 h-fit">
    @if (!$user)
    {{-- Guest menu --}}
    <a class="w-30 bg-white font-semibold px-2 md:px-6 py-1 rounded-lg text-center hover:bg-Gray_05 hover:text-Icon05 text-sm text-nowrap hidden md:block whitespace-nowrap"
        href="{{ route('oauth.redirect') }}">
        Đăng ký
    </a>
    <a class="w-30 text-white font-semibold px-3 py-1 rounded-lg border text-center border-white hover:bg-Gray_15 text-sm hidden md:block whitespace-nowrap"
        href="{{ route('oauth.redirect') }}">
        Đăng nhập
    </a>
    @else
    {{-- Authenticated user menu --}}
    <div class="relative hidden md:block" id="user-menu">
        <button id="toggle-menu"
            onclick="toggleDropdown('content-dropdown');"
            class="focus:outline-none flex gap-x-3 items-center border border-white font-semibold px-4 py-1 rounded text-white hover:bg-Gray_15 text-xs md:text-base text-nowrap">
            <div class="text-start">
                <p>{{ $user['first_name'] . ' ' . $user['last_name'] ?: $user['email'] }}</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 16c-0.3 0-0.6-0.1-0.8-0.3L5.2 10.3C4.6 9.7 5 9 6 9h12c1 0 1.4 0.7 0.8 1.3l-6 5.4c-0.2 0.2-0.5 0.3-0.8 0.3z" fill="white" stroke-linejoin="round" />
            </svg>
        </button>


        <div id="content-dropdown"
            class="origin-top-right right-0 shadow-lg ring-1 ring-black ring-opacity-5 animate-fadeIn absolute left-0 top-full py-2 mt-2 z-10 bg-darkGray border-[0.5px] border-gray-100 w-full hidden">
            <a href="/saved" class="w-full">
                <div class="px-4 py-2 w-full hover:bg-lightBlack text-start font-semibold text-white text-sm">
                    Đã Lưu
                </div>
            </a>
            <form id="logout-form" method="POST"
                action="{{ route('logout', ['redirect_bbw' => $redirect_bbw]) }}">
                @csrf
                <button class="px-4 py-2 w-full hover:bg-lightBlack text-start font-semibold text-primary text-sm">
                    Đăng Xuất
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@if (!$user)
<p class="text-white text-[0.5rem] text-end mt-2">Một sản phẩm của <span class="font-bold text-darkYellow">BEACON</span><span class="font-bold"> MEDIA</span></p>
@endif