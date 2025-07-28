<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class EventArticleService
{
    private $client;
    private $apiDomain;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiDomain = env('API_DOMAIN', 'https://bbw.vn');
    }

    public function getArticlesByEvent($eventId, $limit)
    {
        $cacheKey = "event_{$eventId}_articles_limit_{$limit}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        } else {
            $response = $this->client->get("{$this->apiDomain}/api/v1/getarticlebyevent/{$eventId}/{$limit}");
            $data = json_decode($response->getBody()->getContents(), true);

            // Cache the articles for 24 hours
            Cache::put($cacheKey, $data, 86400);
            return $data;
        }
    }
}
