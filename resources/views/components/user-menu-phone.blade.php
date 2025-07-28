@if($user === null)
<div class="flex flex-col">
    <a href="{{ route('oauth.redirect') }}" class="flex gap-x-2 items-center text-white text-base leading-6 py-2.5 font-bold">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.5 2.5H15.8333C16.2754 2.5 16.6993 2.67559 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H12.5M8.33333 14.1667L12.5 10M12.5 10L8.33333 5.83333M12.5 10H2.5" stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square" stroke-linejoin="round" />
        </svg>

        <p>Đăng Nhập</p>
    </a>

    <a href="{{ route('oauth.redirect') }}" class="flex gap-x-2 items-center text-white text-base leading-6 py-2.5 font-bold border-y border-y-Line_03">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.1654 17.5V15.8333C14.1654 14.9493 13.8142 14.1014 13.1891 13.4763C12.5639 12.8512 11.7161 12.5 10.832 12.5H4.16536C3.28131 12.5 2.43346 12.8512 1.80834 13.4763C1.18322 14.1014 0.832031 14.9493 0.832031 15.8333V17.5M19.1654 17.5V15.8333C19.1648 15.0948 18.919 14.3773 18.4665 13.7936C18.014 13.2099 17.3805 12.793 16.6654 12.6083M13.332 2.60833C14.049 2.79192 14.6846 3.20892 15.1384 3.79359C15.5922 4.37827 15.8386 5.09736 15.8386 5.8375C15.8386 6.57764 15.5922 7.29673 15.1384 7.88141C14.6846 8.46608 14.049 8.88308 13.332 9.06667M10.832 5.83333C10.832 7.67428 9.33965 9.16667 7.4987 9.16667C5.65775 9.16667 4.16536 7.67428 4.16536 5.83333C4.16536 3.99238 5.65775 2.5 7.4987 2.5C9.33965 2.5 10.832 3.99238 10.832 5.83333Z" stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="square" stroke-linejoin="round" />
        </svg>

        <p>Đăng Ký</p>
    </a>
</div>
@else
<div class="flex flex-col">
    <p class="text-white text-base leading-6 py-2.5 font-bold flex items-center gap-x-2">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.6654 17.5V15.8333C16.6654 14.9493 16.3142 14.1014 15.6891 13.4763C15.0639 12.8512 14.2161 12.5 13.332 12.5H6.66536C5.78131 12.5 4.93346 12.8512 4.30834 13.4763C3.68322 14.1014 3.33203 14.9493 3.33203 15.8333V17.5M13.332 5.83333C13.332 7.67428 11.8396 9.16667 9.9987 9.16667C8.15775 9.16667 6.66536 7.67428 6.66536 5.83333C6.66536 3.99238 8.15775 2.5 9.9987 2.5C11.8396 2.5 13.332 3.99238 13.332 5.83333Z" stroke="#6E6E6E" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        {{ trim($user['first_name'] . ' ' . $user['last_name']) ? $user['first_name'] . ' ' . $user['last_name'] : $user['email'] }}
    </p>
    <a href="/saved" class="w-full hover:underline text-white text-base leading-6 pl-7 py-2.5 font-bold  border-y border-y-Line_03">
        Đã Lưu
    </a>
</div>
@endif