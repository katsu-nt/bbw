<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\EventArticleService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $eventService;
    protected $eventArticleService;

    public function __construct(EventService $eventService, EventArticleService $eventArticleService)
    {
        $this->eventService = $eventService;
        $this->eventArticleService = $eventArticleService;
    }

    public function showSpecialReports()
    {
        // Tạo cache key cho view special reports
        $cacheKey = 'special_reports_view';
        $fallbackKey = 'special_reports_view_fallback';

        try {
            // Kiểm tra cache view trước
            if (Cache::has($cacheKey)) {
                Log::info("Lấy special reports view từ cache");
                return response(Cache::get($cacheKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            Log::info("Không tìm thấy cache, tạo view mới cho special reports");

            // Lấy dữ liệu
            $channelId = 225;
            $events = $this->eventService->getEventsByChannel($channelId);

            $mostRecentEvent = $events[0] ?? null;
            $articles = [];

            if ($mostRecentEvent) {
                $articles = $this->eventArticleService->getArticlesByEvent($mostRecentEvent['EventId'], 10);
            }

            // Render view
            $view = view('special_reports', [
                'events' => $events,
                'mostRecentEvent' => $mostRecentEvent,
                'articles' => $articles
            ])->render();

            // Cache view trong 1 ngày
            Cache::put($cacheKey, $view, now()->addDay());
            Log::info("Đã cache special reports view với key: {$cacheKey}");

            // Cache fallback trong 2 ngày để backup
            Cache::put($fallbackKey, $view, now()->addDays(2));
            Log::info("Đã cập nhật fallback cache: {$fallbackKey}");

            return response($view)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo special reports view: " . $e->getMessage());

            // Thử lấy từ fallback cache
            if (Cache::has($fallbackKey)) {
                $fallbackView = Cache::get($fallbackKey);
                Log::info("Sử dụng fallback cache cho special reports");

                return response($fallbackView)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Không có fallback, hiển thị trang lỗi
            Log::error("Không có fallback cache, hiển thị trang lỗi");
            return response()->view('error_page', ['error' => 'Không thể tải trang báo cáo đặc biệt'], 500)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
    }
}
