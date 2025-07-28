<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Client;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function show($slug, $page = 1, $limit = 15)
    {
        // Tạo request ID cho logging
        $requestId = uniqid();
        Log::info("[CategoryController-{$requestId}] Method started for slug: {$slug}, page: {$page}, limit: {$limit}");

        // Tạo cache key
        $cacheKey = "category_view_{$slug}_page{$page}_limit{$limit}";
        // Tạo fallback key chung cho category này (không phân biệt page/limit)
        $fallbackKey = "category_view_fallback_{$slug}";

        Log::info("[CategoryController-{$requestId}] Using cache key: {$cacheKey}");
        Log::info("[CategoryController-{$requestId}] Using fallback key: {$fallbackKey}");

        try {
            // Kiểm tra cache view
            if (Cache::has($cacheKey)) {
                Log::info("[CategoryController-{$requestId}] Cache hit, serving from cache");
                return response(Cache::get($cacheKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            Log::info("[CategoryController-{$requestId}] Cache miss, fetching data");

            // Lấy dữ liệu danh mục (không cache riêng)
            $categoryData = $this->categoryService->getDataByCategory($slug, $page, $limit);

            if (!$categoryData) {
                Log::warning("[CategoryController-{$requestId}] No data found for category");
                return abort(404);
            }

            // Render view
            Log::info("[CategoryController-{$requestId}] Rendering view");
            $viewHtml = view('category', [
                'categoryData' => $categoryData,
                'slug' => $slug,
                'page' => $page,
                'limit' => $limit,
            ])->render();

            // Cache toàn bộ view đã render
            Cache::put($cacheKey, $viewHtml, now()->addHours(24));
            Log::info("[CategoryController-{$requestId}] View cached for 24 hours");

            // Cập nhật fallback cache với TTL dài hơn
            Cache::put($fallbackKey, $viewHtml, now()->addHours(48));
            Log::info("[CategoryController-{$requestId}] Fallback cache updated with TTL 48 hours");

            // Trả về response
            Log::info("[CategoryController-{$requestId}] Method completed successfully");
            return response($viewHtml)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error("[CategoryController-{$requestId}] Error: " . $e->getMessage());

            // Kiểm tra fallback trong trường hợp có lỗi
            if (Cache::has($fallbackKey)) {
                Log::info("[CategoryController-{$requestId}] Serving from fallback after error");
                return response(Cache::get($fallbackKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Không có fallback, hiển thị trang lỗi
            Log::error("[CategoryController-{$requestId}] No fallback available, showing error page");
            return response()->view('error_page', ['error' => 'Unable to load category'], 500)
                            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                            ->header('Pragma', 'no-cache')
                            ->header('Expires', '0');
        }
    }

    public function loadMore($channelId, $page, $limit)
    {
        // Method loadMore có thể không cần cache vì đây là API Ajax
        // hoặc có thể thêm cache nếu cần
        $requestId = uniqid();
        Log::info("[CategoryController-{$requestId}] LoadMore started for channel: {$channelId}, page: {$page}, limit: {$limit}");

        try {
            // Đặt header cho browser không cache
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Lấy dữ liệu bổ sung
            $additionalData = $this->categoryService->getAdditionalDataByCategory($channelId, $page, $limit);

            if (!$additionalData) {
                Log::warning("[CategoryController-{$requestId}] No additional data found");
                return response()->json(['error' => 'No data found'], 404);
            }

            Log::info("[CategoryController-{$requestId}] LoadMore completed successfully");
            return response()->json($additionalData);
        } catch (\Exception $e) {
            Log::error("[CategoryController-{$requestId}] LoadMore error: " . $e->getMessage());
            return response()->view('error_page');
        }
    }

    public function getArticlesNewest(Request $request)
    {
        try {
            $type = $request->query('type', 'all');
            $slug = $request->query('slug', '');
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 6);

            // Create cache key based on parameters
            $cacheKey = "articles_newest_{$type}_{$slug}_{$page}_{$limit}";

            $listArticleNewest = Cache::remember($cacheKey, now()->addHours(24), function () use ($type, $slug, $page, $limit) {
                if ($type === 'all') {
                    $client = new Client();
                    $promises = ['homepage' => $client->getAsync(env('API_DOMAIN') . '/api/v1/homepage')];

                    $responses = Utils::settle($promises)->wait();

                    // Throw only if 'homepage' failed (critical)
                    if ($responses['homepage']['state'] === 'rejected') {
                        Log::error('error get articles newest: ');
                        throw new \Exception("Failed to fetch homepage data.");
                    }

                    $data = json_decode($responses['homepage']['value']->getBody(), true);
                    $articles = $data['ListArticleHighLight'] ?? [];

                    if ($articles) {
                        $articles = array_slice($articles, 0, 6);
                    }

                    return $articles;
                } else {
                    Log::info("slug" . $slug . "page" . $page . "limit" . $limit);
                    $result = $this->categoryService->getDataByCategory($slug, $page, $limit);
                    $articles = [];

                    if ($result) {
                        $articles = array_slice($result['ListArticleNewest'], 0, 6);
                    }

                    return $articles;
                }
            });

            return view('components.articles-newest', compact('listArticleNewest'))->render();
        } catch (Exception $exception) {
            Log::error('error get articles newest: ' . $exception);
            return null;
        }
    }
}
