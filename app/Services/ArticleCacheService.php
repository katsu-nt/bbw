<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ArticleCacheService
{
    /**
     * Kiểm tra có nên bypass cache không
     */
    public function shouldBypassCache($request, $publisherId)
    {
        // 1. Kiểm tra bài premium
        $isPremiumContent = Cache::get("is_premium_{$publisherId}", false);
        if ($isPremiumContent) {
            return true;
        }
        
        // 2. Kiểm tra user chưa login và đạt ngưỡng đọc bài
        return $this->shouldLimitNonLoginUser($request);
    }
    
    /**
     * Kiểm tra có nên cache view không
     */
    public function shouldCacheView($isPremiumContent, $isLogin, $articleList)
    {
        // Không cache nếu là bài premium
        if ($isPremiumContent) {
            return false;
        }
        
        // Không cache nếu user chưa đăng nhập và đã đọc >= 4 bài (bài thứ 5 trở đi)
        if (!$isLogin && count($articleList) >= 4) {
            return false;
        }
        
        // Các trường hợp khác thì cache bình thường
        return true;
    }
    
    /**
     * Logic kiểm tra giới hạn cho user chưa đăng nhập
     */
    private function shouldLimitNonLoginUser($request)
    {
        $isLogin = $request->session()->get('user_data') ? true : false;
        
        if (!$isLogin) {
            $articleList = $request->session()->get('bbw_arts', []);
            return count($articleList) >= 4;
        }
        
        return false;
    }
} 