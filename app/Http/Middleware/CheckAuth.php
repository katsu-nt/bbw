<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * This method checks if the user data is present in the session. If not, it returns a JSON response
     * indicating that the user must be logged in to access the requested resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem session có chứa dữ liệu người dùng không
        if (!$request->session()->has('user_data')) {
            // Nếu không có dữ liệu người dùng, trả về response dạng JSON
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be logged in to access this page.'
            ], 401); // Mã 401 là mã cho Unauthorized
        }

        // Nếu có dữ liệu người dùng, tiếp tục xử lý request
        return $next($request);
    }
}
