<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdsController extends Controller
{
    public function serveAll()
    {
        $adUrlMap = [
            'zone-1' => 'https://amb.beaconasiamedia.vn/ser.php?f=33',
            'zone-2' => 'https://amb.beaconasiamedia.vn/ser.php?f=34',
            'zone-2-hide' => 'https://amb.beaconasiamedia.vn/ser.php?f=34',
            'zone-3' => 'https://amb.beaconasiamedia.vn/ser.php?f=35',
            'zone-4' => 'https://amb.beaconasiamedia.vn/ser.php?f=36',
            'zone-5' => 'https://amb.beaconasiamedia.vn/ser.php?f=37',
            'zone-5-hide' => 'https://amb.beaconasiamedia.vn/ser.php?f=37',
            'zone-6' => 'https://amb.beaconasiamedia.vn/ser.php?f=38',
            'zone-7' => 'https://amb.beaconasiamedia.vn/ser.php?f=39',
            'zone-8' => 'https://amb.beaconasiamedia.vn/ser.php?f=40',
            'zone-9' => 'https://amb.beaconasiamedia.vn/ser.php?f=41',
            'zone-10' => 'https://amb.beaconasiamedia.vn/ser.php?f=42'
        ];

        $results = [];
        $errors = [];
        $mainCacheKey = "ads_html_cache";
        $backupCacheKey = "ads_html_backup";

        // Lấy tất cả dữ liệu từ cache chung
        $cachedData = Cache::remember($mainCacheKey, now()->addDay(), function () use ($adUrlMap, $backupCacheKey) {
            $allData = [];
            $hasErrors = false;

            foreach ($adUrlMap as $zone => $url) {
                try {
                    // Timeout 3 giây để tránh chờ quá lâu
                    $response = Http::timeout(3)->get($url);

                    if (!$response->successful()) {
                        Log::warning("Ad fetch failed for URL {$url} with status code {$response->status()}");
                        throw new \Exception("Failed to fetch ad with status {$response->status()}");
                    }

                    $scriptContent = $response->body();
                    $allData[$zone] = $scriptContent;
                } catch (\Exception $e) {
                    $hasErrors = true;
                    Log::warning("Error fetching ad for zone {$zone}: {$e->getMessage()}");
                    $allData[$zone] = null;
                }
            }

            // Nếu lấy được dữ liệu mới, lưu vào backup cache với thời gian lưu dài hơn
            if (!empty($allData) && !$hasErrors) {
                Cache::put($backupCacheKey, $allData, now()->addDays(7));
            }

            return $allData;
        });

        // Nếu có lỗi trong cache chính, thử dùng backup
        if (empty($cachedData)) {
            $cachedData = Cache::get($backupCacheKey, []);
            if (!empty($cachedData)) {
                Log::info("Using backup cache for all zones due to main cache failure");
            }
        }

        // Xử lý và parse dữ liệu từ cache thành HTML
        foreach ($adUrlMap as $zone => $url) {
            try {
                $scriptContent = $cachedData[$zone] ?? null;

                if (empty($scriptContent)) {
                    throw new \Exception("No data available for zone {$zone}");
                }

                $htmlContent = $this->convertScriptToHtml($scriptContent);
                $results[$zone] = $htmlContent;
            } catch (\Exception $e) {
                // Xử lý lỗi - trả về div trống
                Log::error("Failure for zone {$zone}, using fallback: {$e->getMessage()}");
                $results[$zone] = null;
                $errors[$zone] = [
                    'message' => $e->getMessage(),
                    'using_fallback' => true
                ];
            }
        }

        // Trả về kết quả kèm thông tin lỗi (nếu có)
        return response()->json([
            'data' => $results,
            'errors' => !empty($errors) ? $errors : null,
            'timestamp' => now()->timestamp
        ]);
    }

    private function convertScriptToHtml(string $scriptContent)
    {
        // Trường hợp: document.write(unescape("..."))
        if (strpos($scriptContent, 'document.write') !== false) {
            // Trích xuất nội dung trong document.write
            preg_match_all('/document\.write\s*\(\s*unescape\s*\(\s*(["\'])(.*?)\1\s*\)\s*\)/s', $scriptContent, $matches);

            if (!empty($matches[2])) {
                // Giải mã URL-encoded string
                return urldecode($matches[2][0]);
            }
        }

        // Fallback: return the original script wrapped in script tags
        return '<script>' . $scriptContent . '</script>';
    }
}
