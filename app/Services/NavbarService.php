<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Cache;

class NavbarService
{
    private $client;
    private $apiDomain;
    private $categoriesToFetch;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiDomain = env('API_DOMAIN', 'https://bbw.vn');
        $this->categoriesToFetch = [186, 216, 217, 218, 219, 220]; // List of category IDs to fetch
    }

    public function getNavbarData()
    {
        try {
            // Cache 1 ngày (86400 giây)
            return Cache::remember('navbar_data', 86400, function () {
                $navbarResponse = $this->client->get("{$this->apiDomain}/api/v1/navbar");
                $navbarData = json_decode($navbarResponse->getBody()->getContents(), true);

                $filteredCategories = array_filter($navbarData['categories'], function ($category) {
                    return in_array($category['cate_id'], $this->categoriesToFetch);
                });

                // Tạo promises cho cả category data và top read articles
                $promises = [];
                $topReadPromises = [];

                foreach ($this->categoriesToFetch as $categoryId) {
                    $promises[$categoryId] = $this->client->getAsync("{$this->apiDomain}/api/v1/getdatabycategory/{$categoryId}/1/6");
                    $topReadPromises[$categoryId] = $this->client->getAsync("{$this->apiDomain}/api/v1/getdatabycategory/{$categoryId}/1/1");
                }

                // Chờ tất cả requests hoàn thành cùng lúc
                $results = Utils::settle($promises)->wait();
                $topReadResults = Utils::settle($topReadPromises)->wait();

                $data = [
                    'categories' => $filteredCategories,
                    'articles' => [],
                    'topReadArticles' => []
                ];

                foreach ($results as $categoryId => $result) {
                    if ($result['state'] === 'fulfilled') {
                        $responseData = json_decode($result['value']->getBody()->getContents(), true);

                        $data['articles'][$categoryId] = [
                            'ArticleFocus' => $responseData['ArticleFocus'] ?? [],
                            'ListArticleHighLight' => $responseData['ListArticleHighLight'] ?? [],
                            'ListArticleResult' => $responseData['ListArticleResult'] ?? [],
                        ];
                    }
                }

                // Xử lý top read articles
                foreach ($topReadResults as $categoryId => $result) {
                    if ($result['state'] === 'fulfilled') {
                        $responseData = json_decode($result['value']->getBody()->getContents(), true);
                        $topReadArticles = $responseData['ListArticleTopRead'] ?? [];

                        if (!empty($topReadArticles)) {
                            $data['topReadArticles'][] = $topReadArticles[0];
                        }
                    }
                }

                // Sắp xếp theo thời gian và giới hạn
                usort($data['topReadArticles'], function ($a, $b) {
                    return strtotime($b['PublishedTime']) - strtotime($a['PublishedTime']);
                });

                $data['topReadArticles'] = array_slice($data['topReadArticles'], 0, 4);

                return $data;
            });
        } catch (\Exception $e) {
            Cache::forget('navbar_data');
            \Log::error("Error fetching navbar data: " . $e->getMessage());
            return null;
        }
    }

    public function getLatestEvents($limit = 3)
    {
        $cacheKey = "latest_events_limit_{$limit}";

        try {
            // Cache 1 ngày (86400 giây)
            return Cache::remember($cacheKey, 86400, function () use ($limit) {
                $response = $this->client->get("{$this->apiDomain}/api/v1/getevent/1/{$limit}");
                $data = json_decode($response->getBody()->getContents(), true);
                return $data;
            });
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            \Log::error("Error fetching latest events: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear navbar cache manually
     */
    public function clearCache()
    {
        Cache::forget('navbar_data');
        // Clear all event caches
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("latest_events_limit_{$i}");
        }
    }
}
