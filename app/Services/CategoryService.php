<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    private $client;
    private $apiDomain;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiDomain = env('API_DOMAIN', 'https://bbw.vn');
    }

    public function getCategories()
    {
        $cacheKey = 'categories';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->client->get("{$this->apiDomain}/api/v1/navbar");
        $data = json_decode($response->getBody(), true);
        $categories = $data['categories'] ?? [];

        Cache::put($cacheKey, $categories, 1440); // Cache for 24 hours

        return $categories;
    }

    public function getCategoryIdBySlug($slug)
    {
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            if ($category['slug'] === $slug) {
                return $category['cate_id'];
            }
        }
        return null;
    }

    public function getDataByCategory($slug, $page, $limit)
    {
        $cateId = $this->getCategoryIdBySlug($slug);
        if (!$cateId) {
            return null;
        }

        return $this->fetchCategoryData($cateId, $page, $limit);
    }

    public function getDataCategoryById($cateId, $page, $limit)
    {
        return $this->fetchCategoryData($cateId, $page, $limit);
    }


    public function getAdditionalDataByCategory($channelId, $page, $limit)
    {
        return $this->fetchCategoryData($channelId, $page, $limit);
    }

    private function fetchCategoryData($cateId, $page, $limit)
    {
        $cacheKey = "category_{$cateId}_page_{$page}_limit_{$limit}";

        try {
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get("{$this->apiDomain}/api/v1/getdatabycategory/{$cateId}/{$page}/{$limit}");
                $responseData = json_decode($response->getBody()->getContents(), true);

                if (!is_array($responseData)) {
                    Log::error("Failed to fetch data for category {$cateId}: ");
                    return null;
                }

                // Log::info("API Response for Category {$cateId}: ", $responseData);
                Cache::put($cacheKey, $responseData, now()->addHours(24));
            } else {
                $responseData = Cache::get($cacheKey);
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error("Failed to fetch data for category {$cateId}: " . $e->getMessage());
            return null;
        }
    }


    public function getDataByCategories(array $categories, $page, $limit)
    {
        try {
            $promises = [];
            foreach ($categories as $cateName => $cateId) {
                $cacheKey = "category_{$cateId}_page_{$page}_limit_{$limit}";
                if (!Cache::has($cacheKey)) {
                    $promises[$cateName] = $this->client->getAsync("{$this->apiDomain}/api/v1/getdatabycategory/{$cateId}/{$page}/{$limit}");
                }
            }

            $results = Utils::settle($promises)->wait();
            $data = [];

            foreach ($categories as $cateName => $cateId) {
                $cacheKey = "category_{$cateId}_page_{$page}_limit_{$limit}";
                if (isset($results[$cateName]) && $results[$cateName]['state'] === 'fulfilled') {
                    $responseData = json_decode($results[$cateName]['value']->getBody()->getContents(), true);

                    // Fallback if decoding fails
                    if (!is_array($responseData)) {
                        throw new \Exception("Invalid JSON for category {$cateName}");
                    }

                    Cache::put($cacheKey, $responseData, 1440);
                    $data[$cateName] = $responseData;
                } else {
                    // fallback to cache
                    $data[$cateName] = Cache::get($cacheKey, []);
                }
            }

            return $data;
        } catch (\Exception $e) {
            \Log::error('Category data fetch failed: ' . $e->getMessage());
            // Optionally return only cached data
            $fallbackData = [];
            foreach ($categories as $cateName => $cateId) {
                $cacheKey = "category_{$cateId}_page_{$page}_limit_{$limit}";
                $fallbackData[$cateName] = Cache::get($cacheKey, []);
            }
            return $fallbackData;
        }
    }
}
