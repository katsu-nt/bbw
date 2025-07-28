<?php

namespace App\Http\Controllers;

use App\Models\UserBBW;
use App\Models\UserBehavior;
use App\Services\NocoDBService;
use App\Services\UserActivityService;
use App\Services\ArticleCacheService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ElasticsearchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Session;

class ArticleController extends Controller
{
    protected $elasticsearchService;
    protected $activityService;
    protected $nocoDBService;
    protected $articleCacheService;

    public function __construct(
        ElasticsearchService $elasticsearchService,
        UserActivityService $activityService,
        NocoDBService $nocoDBService,
        ArticleCacheService $articleCacheService
    ) {
        $this->elasticsearchService = $elasticsearchService;
        $this->activityService = $activityService;
        $this->nocoDBService = $nocoDBService;
        $this->articleCacheService = $articleCacheService;
    }
    public function show($slugPublisher, Request $request)
    {
        // Extract the PublisherId
        preg_match('/-(\d+)\.html$/', $slugPublisher, $matches);
        $publisherId = $matches[1] ?? null;

        if (!$publisherId) {
            return abort(404);
        }

        // Tạo fallback key cho bài viết này
        $fallbackKey = "article_view_fallback_{$publisherId}";

        try {
            // Gọi API một lần duy nhất
            $apiDomain = env('API_DOMAIN');
            $client = new Client();
            $apiUrl = "{$apiDomain}/api/v1/getarticleinfo/{$publisherId}";

            // Kiểm tra xem bài có phải premium không
            $isPremiumContent = Cache::remember("is_premium_{$publisherId}", now()->addHours(24), function () use ($client, $apiUrl) {
                try {
                    $response = $client->get($apiUrl);
                    $data = json_decode($response->getBody(), true);
                    $article = $data['articleInfo'] ?? [];

                    $keywords = explode(',', $article['Keyword'] ?? '');
                    foreach ($keywords as $keyword) {
                        if (strtolower(trim($keyword)) === 'premium') {
                            return true;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error checking premium: " . $e->getMessage());
                }

                return false;
            });

            // Lấy dữ liệu bài viết (cache cho cả premium và bài thường)
            $data = Cache::remember("article_data_{$publisherId}", now()->addHours(24), function() use ($client, $apiUrl) {
                $response = $client->get($apiUrl);
                return json_decode($response->getBody(), true);
            });

            // Từ đây trở đi, sử dụng $data đã có
            $article = $data['articleInfo'] ?? [];
            $relatedArticles = $data['ListArticleRelation'] ?? [];
            $topReadArticles = $data['ListArticleTopRead'] ?? [];

            // Xác định loại user
            $isLogin = $request->session()->get('user_data') ? true : false;
            $isPremiumUser = false;

            if ($isLogin && $isPremiumContent) {
                $isPremiumUser = $this->checkPremiumAccess();
                Log::info("[Article-{$publisherId}] User premium status: " . ($isPremiumUser ? 'Premium' : 'Not premium'));
            } else {
                Log::info("[Article-{$publisherId}] User premium status: Not premium (user not logged in or article is not premium)");
            }

            // Xử lý dữ liệu
            $contentParts = $this->splitContent($article['Content'] ?? '');
            $article['ContentPart1'] = $contentParts[0] ?? '';
            $article['ContentPart2'] = $contentParts[1] ?? '';

            $hidecontentPremium = $this->getFirstThreeParagraphs($article['Content'] ?? '');

            // Process keywords
            $processedKeywords = [];
            $keywords = explode(',', $article['Keyword'] ?? '');
            foreach ($keywords as $keyword) {
                $processedKeywords[] = [
                    'name' => trim($keyword),
                    'slug' => Str::slug(trim($keyword))
                ];
            }

            // Metadata và các xử lý khác
            $metadata = [
                'title' => $article['Title'] ?? 'Default Title',
                'author' => $article['AuthorAlias'] ?? 'Default Author',
                'keywords' => $article['Keyword'] ?? 'default,keywords',
                'description' => $article['Headlines'] ?? 'Default description',
                'og_image' => $article['Thumbnail'] ?? 'default.jpg',
                'og_url' => url($slugPublisher),
                'namechannel' => $article['NameChannel'] ?? 'Default name',
                'cateslug' => 'https://bbw.vn/' . Str::slug($article['NameChannel'])
            ];

            // Khởi tạo articleList mặc định ngay từ đầu
            $articleList = $request->session()->get('bbw_arts', []);

            // TRACK ARTICLE READ AND SAVE ARTICLES READ IN MONTH
            $isSave = null;
            $hasCookieToken = true;

            if ($isLogin) {
                $userDataId = ($request->session()->get('user_data'))['data']['id'];
                // $articleList = $this->activityService->getRedisArticlesReadInMonth($userDataId);

                $userCurrent = UserBBW::where('email', $request->session()->get('user_data')['data']['email'])->first();

                $cookie = null; // Initialize cookie variable
                if ($userCurrent === null || !$userCurrent->exists()) {
                    $hasCookieToken = false;
                    $user = UserBBW::Create(['email' => $request->session()->get('user_data')['data']['email']]);

                    // Save to Session
                    $userData = $request->session()->get('user_data', []);
                    $userData['data']['uuid'] = $user->getOriginal()['uuid'];
                    $request->session()->put('user_data', $userData);
                    $request->session()->save();

                    $token = $user->createToken('authToken')->plainTextToken;
                    $cookie = cookie(
                        'bbw_token',
                        $token,
                        config('session.lifetime'), // expiration
                        null, // path
                        null, // domain
                        true, // secure
                        true  // httponly
                    ); // HttpOnly = true
                } else {
                    $cookie = $request->cookie('bbw_token');
                    // Check if the user has a token in the cookie
                    if ($request->cookie('bbw_token') === null) {
                        $hasCookieToken = false;

                        // If not, create a new token and store it in the session
                        $token = $userCurrent->createToken('authToken')->plainTextToken;
                        $cookie = cookie(
                            'bbw_token',
                            $token,
                            config('session.lifetime'), // expiration
                            null, // path
                            null, // domain
                            true, // secure
                            true  // httponly
                        ); // HttpOnly = true
                    } else {
                        // If the token exists, use it
                        $token = $request->cookie('bbw_token');
                    }
                }

                // $isMetaDataExist = $this->addUserBehaviorIfNotExists($request->session()->get('user_data')['data']['uuid'],  $publisherId, $token, action: 'view');
                $isSave = $this->userBehaviorExists($request->session()->get('user_data')['data']['uuid'], $publisherId, 'save') ?? null;
            } else {
                $this->handleSessionArticleReadInMonth($publisherId, $request);
                $articleList = $request->session()->get('bbw_arts', []);
            }

            // Xử lý metadata từ NocoDB
            // if ($article['SourceType'] === 3) {
            $metaDataFromNoco = $this->nocoDBService->getMetaData($publisherId, 'pr2a2ugwrnaryud', 'mu0buxwp7m0gmst');
            $summarize = $metaDataFromNoco['summarize'] ?? null;
            $audio = $metaDataFromNoco['audio'] ?? null;
            // } else {
            // $summarize = null;
            // $audio = null;
            // }

            // Chuẩn bị dữ liệu để truyền vào view
            $isFromCache = false;
            $viewData = compact(
                'article',
                'relatedArticles',
                'topReadArticles',
                'processedKeywords',
                'metadata',
                'hidecontentPremium',
                'isPremiumUser',
                'isPremiumContent',
                'isFromCache',
                'isLogin',
                'articleList',
                'summarize',
                'audio',
                'isSave'
            );

            // Render view
            $view = view('article', $viewData)->render();

            // Xác định có nên cache view hay không - sử dụng service
            $shouldCacheView = $this->articleCacheService->shouldCacheView($isPremiumContent, $isLogin, $articleList);

            // Cache view dựa trên điều kiện
            if ($shouldCacheView) {
                // Key đơn giản, không phân biệt user
                $viewCacheKey = "article_view_{$publisherId}";
                Cache::put($viewCacheKey, $view, now()->addHours(24));

                // Cập nhật fallback cache
                Cache::put($fallbackKey, $view, now()->addHours(48));
                Log::info("Updated view cache and fallback for article: {$publisherId}");
            } else {
                Log::info("Not caching view for article: {$publisherId} - conditions not met for caching");
            }

            $response = response($view);

            // Thêm cookie nếu cần
            if (isset($cookie) && $hasCookieToken === false) {
                $response = $response->withCookie($cookie);
            }

            // Header không cache
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');

            return $response;
        } catch (\Exception $e) {
            Log::error("Error rendering article {$publisherId}: " . $e->getMessage());

            // Kiểm tra fallback nếu có lỗi
            if (Cache::has($fallbackKey)) {
                Log::info("Serving from fallback cache after error for article: {$publisherId}");
                return response(Cache::get($fallbackKey))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Không có fallback, trả về trang lỗi
            return response()->view('error_page', [], 500)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
    }

    private function userBehaviorExists($uuid, $publisherId, $action)
    {
        return UserBehavior::where('uuid', $uuid)
            ->where('publisherId', $publisherId)
            ->where('action', $action)
            ->exists();
    }

    // HANDLE USER BEHAVIOR
    private function addUserBehaviorIfNotExists($uuid, $publisherId, $token, $action)
    {
        try {
            $tokenModel = PersonalAccessToken::findToken($token);

            if (!$tokenModel) {
                throw new \Exception("Invalid token");
            }

            $user = $tokenModel->tokenable;
            if (!$user) {
                throw new \Exception("User not found");
            }

            $validator = Validator::make([
                'uuid' => $uuid,
                'publisherId' => $publisherId,
                'action' => $action
            ], [
                'uuid' => ['required', 'uuid'],
                'publisherId' => ['required', 'string'],
                'action' => ['required', 'string', Rule::in(['save', 'view', 'unsave'])]
            ]);

            if ($validator->fails()) {
                throw new \Exception("Validation error: " . $validator->errors()->first());
            }

            // ✅ Use the new function here
            if (!$this->userBehaviorExists($uuid, $publisherId, $action)) {
                return UserBehavior::create([
                    'uuid' => $uuid,
                    'publisherId' => $publisherId,
                    'action' => $action
                ]);
            }

            // Optional: return existing behavior or null
            return null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to process user behavior: " . $e->getMessage());
        }
    }

    private function handleSessionArticleReadInMonth($publisherId, Request $request)
    {
        // Retrieve the current list of articles from the session or initialize an empty array if not set
        $articlesReadInMonth = $request->session()->get('bbw_arts', []);
        $lastSessionUpdated = $request->session()->get('bbw_arts_last_updated', null);

        // Check if the session was updated in the current month
        $currentMonth = now()->format('Y-m'); // Format: YYYY-MM

        // If the session was updated in a different month, clear the session data
        if ($lastSessionUpdated !== $currentMonth) {
            $articlesReadInMonth = []; // Clear the list of articles
            $request->session()->put('bbw_arts_last_updated', $currentMonth); // Update the last updated timestamp
        }

        // Check if the article already exists in the session list
        if (!in_array($publisherId, $articlesReadInMonth)) {
            // Add the new article to the session array
            $articlesReadInMonth[] = $publisherId;

            // If there are fewer than 4 articles, store them in the session
            if (count($articlesReadInMonth) <= 4) {
                $request->session()->put('bbw_arts', $articlesReadInMonth);
            }
        }
    }

    private function splitContent($content)
    {
        // Đếm tổng số thẻ p
        preg_match_all('/<p[^>]*>/i', $content, $matches);
        $totalP = count($matches[0]);
        $middleP = ceil($totalP / 2);

        // Split theo paragraphs, giữ nguyên figure và blockquote
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $currentP = 0;
        $splitIndex = 0;

        // Tìm vị trí split dựa trên số thẻ p
        foreach ($paragraphs as $index => $part) {
            if (strpos($part, '<p') !== false) {
                $currentP++;
                if ($currentP == $middleP) {
                    $splitIndex = $index + 1;
                    // Kiểm tra nếu điểm cắt là figure hoặc nằm trong blockquote
                    while (
                        strpos($paragraphs[$splitIndex], '<figure') !== false ||
                        strpos($paragraphs[$splitIndex], '<blockquote') !== false ||
                        (
                            $index > 0 &&
                            strpos(implode('', array_slice($paragraphs, 0, $splitIndex)), '<blockquote') !== false &&
                            strpos(implode('', array_slice($paragraphs, 0, $splitIndex)), '</blockquote>') === false
                        )
                    ) {
                        $splitIndex += 2;
                    }
                    break;
                }
            }
        }

        // Combine the paragraphs back into two halves
        $contentPart1 = implode('', array_slice($paragraphs, 0, $splitIndex));
        $contentPart2 = implode('', array_slice($paragraphs, $splitIndex));

        return [$contentPart1, $contentPart2];
    }
    private function getFirstThreeParagraphs($content)
    {
        // Split the content by <p> tags
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Get only the first three <p> tags, including their closing tags
        $firstThreeParagraphs = array_slice($paragraphs, 0, 6); // Each <p> and </p> are separate items

        // Combine the extracted paragraphs back into a string
        $contentFirstThree = implode('', $firstThreeParagraphs);

        return $contentFirstThree;
    }


    private function checkPremiumAccess()
    {
        $bresponse = Cookie::get('_bresponse');

        try {
            $userID = Session::get('user_data')['data']['id'] ?? null;
            // $userID = Cookie::get('bbw_uid');
            // $userID = decrypt(Cookie::get('bbw_uid'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Log::info("Failed to decrypt user ID from cookie.");
            return false;
        }

        if (!$bresponse || !$userID) {
            // Log::info("No auth response or user ID available in cookies.");
            return false;
        }

        $accessToken = json_decode($bresponse, true)['access_token'] ?? null;
        if (!$accessToken) {
            // Log::info("No access token found in the auth response.");
            return false;
        }

        // Kiểm tra cache premium trước
        $premiumCacheKey = "user-is-premium-{$userID}";
        if (Cache::has($premiumCacheKey)) {
            Log::info("Found premium cache for user {$userID}");
            return Cache::get($premiumCacheKey); // Chắc chắn là true
        }

        // Nếu không có cache premium, gọi API
        $userPremium = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get(env('SUB_OAUTH_URL') . '/api/is-premium');

        // Log response details
        Log::info('API is-premium response', [
            'status_code' => $userPremium->status(),
            'headers' => $userPremium->headers(),
            'body' => $userPremium->body(),
            'successful' => $userPremium->successful(),
            'access_token' => $accessToken ? 'Present' : 'Missing'
        ]);

        if ($userPremium->successful()) {
            $isPremium = $userPremium->json()['isPremium'] ?? false;
            Log::info("API call successful, user premium status: " . json_encode($isPremium));
            
            // CHỈ cache khi isPremium = true
            if ($isPremium === true) {
                Cache::put($premiumCacheKey, true, 86400); // Cache 24 giờ
                Log::info("Cached premium status for user {$userID}: true");
            }
            
            return $isPremium;
        } else {
            Log::error("API call to check premium status failed", [
                'status_code' => $userPremium->status(),
                'response_body' => $userPremium->body(),
                'access_token' => $accessToken ? 'Present' : 'Missing'
            ]);
            return false;
        }
    }

    /**
     * Calculate the minutes until the end of the current month.
     * @return int
     */
    private function getMinutesUntilEndOfMonth()
    {
        $endOfMonth = Carbon::now()->endOfMonth();
        return Carbon::now()->diffInMinutes($endOfMonth);
    }
}
