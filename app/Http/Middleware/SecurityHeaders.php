<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Ngăn chặn lưu cache nội dung nhạy cảm
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2020 00:00:00 GMT');

        // Ngăn chặn Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Content Security Policy
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        
        // Bảo vệ chống XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Ngăn chặn MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Strict Transport Security
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}