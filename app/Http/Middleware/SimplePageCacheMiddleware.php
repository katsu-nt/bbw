<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use App\Services\ArticleCacheService;

class SimplePageCacheMiddleware
{
    /**
     * Cache key prefix lấy từ cấu hình
     */
    protected $cachePrefix;
    
    /**
     * Danh sách các route cần cache
     */
    protected $cacheableRoutes = [
        'homepage' => 'homepage_view_current',
        'category.show' => 'category_view_{slug}_page{page}_limit{limit}',
        'article.show' => 'article_view_{id}',
        // Thêm các route khác khi cần
        
    ];

    /**
     * Danh sách fallback keys
     */
    protected $fallbackKeys = [
        'homepage' => 'homepage_view_fallback'
        // Thêm các route khác khi cần
    ];
    
    /**
     * Constructor để khởi tạo cache prefix
     */
    public function __construct(ArticleCacheService $articleCacheService)
    {
        $this->articleCacheService = $articleCacheService;
        
        // Lấy Redis prefix từ cấu hình database
        $redisPrefix = Config::get('database.redis.options.prefix', '');
        
        // Lấy Cache prefix từ cấu hình cache
        $cachePrefix = Config::get('cache.prefix', '');
        
        // Kết hợp cả hai prefix
        $this->cachePrefix = $redisPrefix . $cachePrefix;
        
        // Đảm bảo prefix kết thúc bằng dấu ':'
        if ($this->cachePrefix && !str_ends_with($this->cachePrefix, ':')) {
            $this->cachePrefix .= ':';
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestId = uniqid();
        Log::info("[Middleware-{$requestId}] ===== Cache middleware started =====");
        
        // Lấy route và parameters
        $route = $request->route();
        $routeName = $route ? $route->getName() : 'unnamed';
        
        // Kiểm tra route cacheable
        if (isset($this->cacheableRoutes[$routeName])) {
            // Xử lý article route
            if ($routeName === 'article.show') {
                $slugPublisher = $request->route('slugPublisher');
                preg_match('/-(\d+)\.html$/', $slugPublisher, $matches);
                $publisherId = $matches[1] ?? null;
                
                if ($publisherId) {
                    // Sử dụng service để kiểm tra
                    $shouldBypassCache = $this->articleCacheService->shouldBypassCache($request, $publisherId);
                    
                    if ($shouldBypassCache) {
                        Log::info("[Middleware-{$requestId}] Bypassing cache due to user conditions");
                        return $next($request);
                    }
                    
                    // Chỉ kiểm tra cache view nếu không có điều kiện bypass
                    $cacheKey = "article_view_{$publisherId}";
                    
                    Log::info("[Middleware-{$requestId}] Checking view cache: {$cacheKey}");
                    
                    if (Cache::has($cacheKey)) {
                        $cachedContent = Cache::get($cacheKey);
                        Log::info("[Middleware-{$requestId}] Cache hit, serving from cache");
                        
                        return response($cachedContent)
                            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                            ->header('Pragma', 'no-cache')
                            ->header('Expires', '0');
                    }
                    
                    Log::info("[Middleware-{$requestId}] Cache miss, proceeding to controller");
                }
            } else if ($routeName === 'category.show') {
                // Tương tự cho category route
                $slug = $request->route('slug');
                $page = $request->route('page', 1);
                $limit = $request->route('limit', 15);
                
                $cacheKey = "category_view_{$slug}_page{$page}_limit{$limit}";
                
                if (Cache::has($cacheKey)) {
                    Log::info("[Middleware-{$requestId}] Cache hit for category, serving from cache");
                    return response(Cache::get($cacheKey))
                        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', '0');
                }
                
                Log::info("[Middleware-{$requestId}] Cache miss for category, proceeding to controller");
            } else if ($routeName === 'homepage') {
                // Tương tự cho homepage route
                $cacheKey = "homepage_view_current";
                
                if (Cache::has($cacheKey)) {
                    Log::info("[Middleware-{$requestId}] Cache hit for homepage, serving from cache");
                    return response(Cache::get($cacheKey))
                        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', '0');
                }
                
                Log::info("[Middleware-{$requestId}] Cache miss for homepage, proceeding to controller");
            }
        }
        
        Log::info("[Middleware-{$requestId}] ===== Cache middleware completed =====");
        
        // Không có cache, chuyển cho controller xử lý
        return $next($request);
    }
}