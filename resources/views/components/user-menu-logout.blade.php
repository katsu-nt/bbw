<form id="logout-form" method="POST"
    action="{{ route('logout', ['redirect_bbw' => $redirect_bbw]) }}">
    @csrf
    <button class="w-full pl-7  border-b border-b-gray-300 hover:underline text-start font-semibold text-white py-2.5 leading-9">
        Đăng Xuất
    </button>
</form>