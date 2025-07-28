<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    private $client;
    private $apiDomain;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiDomain = env('API_DOMAIN', 'https://bbw.vn');
    }

    public function getArticlesByKeyword($keyword)
    {
        $cacheKey = "articles_by_keyword_{$keyword}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        } else {
            $response = $this->client->get("{$this->apiDomain}/api/v1/getarticlebykeyword", [
                'query' => ['q' => $keyword]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            // Cache the articles for 60 minutes
            Cache::put($cacheKey, $data, 60);
            return $data;
        }
    }
}
