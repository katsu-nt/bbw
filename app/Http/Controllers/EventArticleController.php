<?php

namespace App\Http\Controllers;

use App\Services\EventArticleService;
use App\Services\EventService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EventArticleController extends Controller
{
    protected $eventArticleService;
    protected $eventService;
    
    public function __construct(EventArticleService $eventArticleService, EventService $eventService)
    {
        $this->eventArticleService = $eventArticleService;
        $this->eventService = $eventService;
    }

    public function showEvent($slug)
    {
        // Tạo cache key dựa trên slug
        $cacheKey = "event_articles_view_{$slug}";
        $fallbackKey = "event_articles_view_{$slug}_fallback";

        try {
            // Kiểm tra cache view trước
            if (Cache::has($cacheKey)) {
                Log::info("Lấy event articles view từ cache cho slug: {$slug}");
                return response(Cache::get($cacheKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            Log::info("Không tìm thấy cache, tạo view mới cho event articles slug: {$slug}");

            // Kiểm tra định dạng slug và lấy eventId
            if (preg_match('/event(\d+)/', $slug, $matches)) {
                $eventId = $matches[1] ?? null;
                $limit = 10;
                $channelId = 225;
                
                // Lấy dữ liệu events và articles
                $events = $this->eventService->getEventsByChannel($channelId);
                $eventName = '';
                
                foreach ($events as $event) {
                    if ($event['EventId'] == $eventId) {
                        $eventName = $event['Name'];
                        break;
                    }
                }

                $articleDetails = $this->eventArticleService->getArticlesByEvent($eventId, $limit);

                // Render view
                $view = view('events_articles', [
                    'articleDetails' => $articleDetails,
                    'eventName' => $eventName,
                ])->render();

                // Cache view trong 1 ngày
                Cache::put($cacheKey, $view, now()->addDay());
                Log::info("Đã cache event articles view với key: {$cacheKey}");

                // Cache fallback trong 2 ngày để backup
                Cache::put($fallbackKey, $view, now()->addDays(2));
                Log::info("Đã cập nhật fallback cache: {$fallbackKey}");

                return response($view)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');

            } else {
                // Slug không đúng định dạng
                Log::warning("Slug không đúng định dạng: {$slug}");
                abort(404);
            }

        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo event articles view cho slug {$slug}: " . $e->getMessage());

            // Thử lấy từ fallback cache
            if (Cache::has($fallbackKey)) {
                $fallbackView = Cache::get($fallbackKey);
                Log::info("Sử dụng fallback cache cho event articles slug: {$slug}");

                return response($fallbackView)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Không có fallback, hiển thị trang lỗi
            Log::error("Không có fallback cache cho slug {$slug}, hiển thị trang lỗi");
            return response()->view('error_page', ['error' => 'Không thể tải trang sự kiện'], 500)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
    }
}
