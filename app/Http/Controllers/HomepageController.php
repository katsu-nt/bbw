<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\NavbarService;
use App\Services\NocoDBService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    protected $navbarService;
    protected $categoryService;
    protected $nocoDBService;

    public function __construct(NavbarService $navbarService, CategoryService $categoryService, NocoDBService $nocoDBService)
    {
        $this->navbarService = $navbarService;
        $this->categoryService = $categoryService;
        $this->nocoDBService = $nocoDBService;
    }

    public function showHomepage(Request $request)
    {
        $requestId = $request->attributes->get('middleware_request_id', 'unknown');
        Log::info("[Controller-{$requestId}] Controller method started");

        try {
            // Lấy cache key từ middleware (không có prefix vì Laravel tự thêm)
            $cacheKey = $request->attributes->get('cache_key', 'homepage_view_current');
            $fallbackKey = $request->attributes->get('fallback_key', 'homepage_view_fallback');

            // Kiểm tra nếu cache view đã tồn tại (middleware đã kiểm tra nhưng kiểm tra lại)
            if (Cache::has($cacheKey)) {
                Log::info("[Controller-{$requestId}] Cache found with key: {$cacheKey}, returning cached view");
                return response(Cache::get($cacheKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            Log::info("[Controller-{$requestId}] No cache found, fetching data and rendering view");

            // Lấy dữ liệu và render view (không cache dữ liệu)
            $data = $this->fetchHomepageData();
            $view = $this->renderHomepageView($data)->render();

            // Cache toàn bộ view đã render
            Cache::put($cacheKey, $view, now()->addMinutes(30));
            Log::info("[Controller-{$requestId}] Created cache with key: {$cacheKey}");

            // Cache fallback
            if ($fallbackKey) {
                Cache::put($fallbackKey, $view, now()->addHours(6));
                Log::info("[Controller-{$requestId}] Updated fallback cache: {$fallbackKey}");
            }

            Log::info("[Controller-{$requestId}] Controller completed successfully");

            return response($view)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error("[Controller-{$requestId}] Failed to generate view: " . $e->getMessage());

            // Thử lấy fallback
            $fallbackKey = $request->attributes->get('fallback_key', 'homepage_view_fallback');
            if (Cache::has($fallbackKey)) {
                $fallbackView = Cache::get($fallbackKey);
                Log::info("[Controller-{$requestId}] Serving from fallback in controller");

                return response($fallbackView)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Không có fallback, hiển thị trang lỗi
            Log::error("[Controller-{$requestId}] No fallback available, showing error page");
            return response()->view('error_page', ['error' => 'Unable to load homepage'], 500)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
    }

    private function fetchHomepageData()
    {
        $client = new Client();

        // Add cache for matterprinteds
        $matterprintedsCacheKey = 'matterprinteds_data';
        $matterprintedsData = Cache::remember($matterprintedsCacheKey, now()->addMonth(), function () use ($client) {
            try {
                $response = $client->get("https://subscribe.beaconasiamedia.vn/api/latest-issues");
                $data = json_decode($response->getBody(), true);
                return $data['data'] ?? [];
            } catch (RequestException $e) {
                Log::error('Failed to fetch matterprinteds data: ' . $e->getMessage());
                return [];
            }
        });

        $promises = [
            'homepage' => $client->getAsync(env('API_DOMAIN') . '/api/v1/homepage'),
        ];

        $responses = Utils::settle($promises)->wait();

        // Throw only if 'homepage' failed (critical)
        if ($responses['homepage']['state'] === 'rejected') {
            throw new \Exception("Failed to fetch homepage data.");
        }

        $data = json_decode($responses['homepage']['value']->getBody(), true);

        $articleFocus = $data['ArticleFocus'] ?? null;
        $highlightArticles = $data['ListArticleHighLight'] ?? [];
        $listChuyenDe = $data['ListChuyenDe'] ?? [];
        $listYKien = $data['ListYKien'] ?? [];
        $doThiTrongTuan = $data['DoThiTrongTuan'] ?? null;
        $noiBatTrongNgay = $data['NoiBatTrongNgay'] ?? [];
        $baoCaoDacBiet = $data['ListEventBaoCaoDacBiet'] ?? [];
        $phongLuu = array_slice($data['PhongLuu'] ?? [], 0, 4);
        $xanh = array_slice($data['Xanh'] ?? [], 0, 4);
        $congNghe = array_slice($data['CongNghe'] ?? [], 0, 4);
        $taiChinh = array_slice($data['TaiChinh'] ?? [], 0, 4);
        $giaiPhap = array_slice($data['GiaiPhap'] ?? [], 0, 4);
        $kinhTe = array_slice($data['KinhTe'] ?? [], 0, 4);
        $listArticleTicker = $data['ListArticleTicker'] ?? [];
        $listArticleTopRead = $data['ListArticleTopRead'] ?? [];

        return [
            'matterprinteds' => array_slice($matterprintedsData, 0, 3),
            'articleFocus' => $articleFocus,
            'highlightArticles' => $highlightArticles,
            'listChuyenDe' => $listChuyenDe,
            'listYKien' => $listYKien,
            'doThiTrongTuan' => $doThiTrongTuan,
            'noiBatTrongNgay' => $noiBatTrongNgay,
            'baoCaoDacBiet' => $baoCaoDacBiet,
            'phongLuu' => $phongLuu,
            'xanh' => $xanh,
            'congNghe' => $congNghe,
            'taiChinh' => $taiChinh,
            'giaiPhap' => $giaiPhap,
            'kinhTe' => $kinhTe,
            'listArticleTicker' => $listArticleTicker,
            'listArticleTopRead' => $listArticleTopRead,
            'categoryData' => $this->fetchCategoryData(),
        ];
    }

    private function fetchCategoryData()
    {
        $categories = [
            'kinhdoanh' => 186,
            'congnghe' => 216,
            'taichinh' => 217,
            'kinhte' => 218,
            'phongluu' => 220,
            'chuyende' => 227,
            'xanh' => 234
        ];

        $categoryData = [];

        foreach ($categories as $key => $categoryId) {
            $categoryData[$key] = $this->categoryService->getDataCategoryById($categoryId, 1, 15);
        }

        return $categoryData;
    }

    private function renderHomepageView($data)
    {
        if (count($data['articleFocus']) > 1) {
            $totalIdArticlesFirstSection = array_merge(
                [
                    $data['articleFocus'][0]['PublisherId'],
                    $data['articleFocus'][1]['PublisherId'],
                ],
                array_map(function ($article) {
                    return $article['PublisherId'];
                }, $data['highlightArticles'])
            );
        } else {
            $totalIdArticlesFirstSection = [$data['articleFocus'][0]['PublisherId']];
        }

        $projectId = 'p4zt97atgleffqx';
        $tableId = 'mznvfj8xh8eewjm';
        $linkFieldId = 'ca4j39vzx4w06j7';

        if ($this->nocoDBService->getVideoData($projectId, $tableId, $linkFieldId)) {
            $dataVideos = array_values($this->nocoDBService->getVideoData($projectId, $tableId, $linkFieldId));
        } else {
            $dataVideos = [];
        }

        $necessaryNews = $this->nocoDBService->getNecessaryNews('ptwwv8xz1mmule7', 'mvqi3svo1ccc5y4');

        return view('index', [
            'matterprinteds' => $data['matterprinteds'],
            'articleFocus' => $data['articleFocus'],
            'highlightArticles' => $data['highlightArticles'],
            'listChuyenDe' => $data['listChuyenDe'],
            'listYKien' => $data['listYKien'],
            'doThiTrongTuan' => $data['doThiTrongTuan'],
            'noiBatTrongNgay' => $data['noiBatTrongNgay'],
            'baoCaoDacBiet' => $data['baoCaoDacBiet'],
            'videos' => $dataVideos ?? [],
            'nhipSong' => $data['categoryData']['phongluu'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['phongluu']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                220
            ) : [],
            'listArticleTicker' => $data['listArticleTicker'],
            'listArticleTopRead' => $data['listArticleTopRead'],
            'businessData' => $data['categoryData']['kinhdoanh'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['kinhdoanh']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                186
            ) : [],
            'technologyData' => $data['categoryData']['congnghe'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['congnghe']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                216
            ) : [],
            'financeData' => $data['categoryData']['taichinh'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['taichinh']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                217
            ) : [],
            'economyData' => $data['categoryData']['kinhte'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['kinhte']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                218
            ) : [],
            'greenData' => $data['categoryData']['xanh'] ?  $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['xanh']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                234
            ) : [],
            'solutionData' => $data['giaiPhap'],
            'topicData' => $data['categoryData']['chuyende'] ? $this->filterArrayByIdArticlesFirstSection(
                $data['categoryData']['chuyende']['ListArticleNewest'] ?? [],
                $totalIdArticlesFirstSection,
                227
            ) : [],
            'necessaryNews' => $necessaryNews
        ]);
    }

    private function filterArrayByIdArticlesFirstSection($array, $totalIdArticlesFirstSection, $idChannel)
    {
        // Ensure $array is an array
        if (!is_array($array)) {
            Log::error('filterArrayByIdArticlesFirstSection: $array is not an array', ['array' => $array]);
            return [];
        }

        // Ensure $totalIdArticlesFirstSection is an array
        if (!is_array($totalIdArticlesFirstSection)) {
            Log::error('filterArrayByIdArticlesFirstSection: $totalIdArticlesFirstSection is not an array', ['totalIdArticlesFirstSection' => $totalIdArticlesFirstSection]);
            return [];
        }

        $filteredArray = array_filter(
            $array,
            function ($article) use ($totalIdArticlesFirstSection, $idChannel) {
                // Check if $article is an array and has required keys
                if (!is_array($article) || !isset($article['PublisherId']) || !isset($article['Channel']) || !is_array($article['Channel']) || !isset($article['Channel']['ChannelId'])) {
                    Log::warning('Invalid article structure in filterArrayByIdArticlesFirstSection', ['article' => $article]);
                    return false;
                }

                return !in_array($article['PublisherId'], $totalIdArticlesFirstSection)
                    && $article['Channel']['ChannelId'] == $idChannel; // Loose comparison to handle type mismatches
            }
        );

        // Re-index the array to start from 0
        return array_values($filteredArray);
    }
}
