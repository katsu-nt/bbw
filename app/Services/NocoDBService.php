<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NocoDBService
{
    protected $client;
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('NOCODB_URL', ''), '/');
        $this->apiToken = env('NOCODB_API_TOKEN', '');

        // Temporary debug log
        // Log::debug('NocoDB Config', [
        //     'baseUrl' => $this->baseUrl,
        //     'tokenExists' => !empty($this->apiToken),
        // ]);

        if (empty($this->baseUrl) || empty($this->apiToken)) {
            throw new \RuntimeException('NOCODB_URL and NOCODB_API_TOKEN must be configured in .env');
        }

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'xc-auth' => $this->apiToken,
                'xc-token' => $this->apiToken, // Some versions use this
                'Accept' => 'application/json',
            ],
            'verify' => false, // Temporarily disable SSL verify if needed
        ]);
    }

    /**
     * Get rows from NocoDB table
     * 
     * @param array $params Query parameters
     * @return array
     * @throws \RuntimeException
     */
    public function getVideoData($projectId, $tableId, $linkFieldId)
    {
        try {
            $cacheKey = "video_data_{$projectId}_{$tableId}";
            $cacheTtl = 3600 * 24; // Cache time in seconds (1 day)

            // Return cached data if available
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Fetch from API
            $response = $this->client->get("/api/v1/db/data/v1/{$projectId}/{$tableId}?where=(show,eq,true)&limit=1");
            $currentData = json_decode($response->getBody(), true);

            if (empty($currentData['list'])) {
                return null;
            }

            $rowId = $currentData['list'][0]['Id'] ?? 0;

            if ($rowId) {
                $linkedResponse = $this->client->get("api/v2/tables/{$tableId}/links/{$linkFieldId}/records/{$rowId}?fields=Id,title,image,url,theme");
                $linkedData = json_decode($linkedResponse->getBody(), true);
                $result = $linkedData['list'] ?? null;

                // Save to cache
                Cache::put($cacheKey, $result, $cacheTtl);

                return $result;
            }

            return null;
        } catch (RequestException $e) {
            Log::error('NocoDB API request failed', [
                'url' => $this->baseUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return cached data if available
            return Cache::get($cacheKey, null);
        }
    }


    public function getMetaData($publisherId, $projectId, $tableId)
    {
        $cacheKey = "metadata_{$publisherId}_{$projectId}_{$tableId}";

        // Try to get cached data first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->get("/api/v1/db/data/v1/{$projectId}/{$tableId}", [
                'query' => [
                    'where' => "(publisherId,eq,{$publisherId})",
                ]
            ]);


            $currentData = json_decode($response->getBody(), true);

            if (empty($currentData['list']) || isset($currentData['list'][0]['summarize']['error'])) {
                // Cache null results to prevent repeated API calls
                Cache::put($cacheKey, ['summarize' => null, 'audio' => null], now()->addDay());
                return [
                    'summarize' => null,
                    'audio' => null,
                ];
            }


            $resultSummarize = $currentData['list'][0]['summarize'] ?? null;
            $resultAudio = $currentData['list'][0]['audio'] ?? null;

            // If both are null → cache null
            if (empty($resultSummarize) && empty($resultAudio)) {
                Cache::put($cacheKey, ['summarize' => null, 'audio' => null], now()->addDay());
                return [
                    'summarize' => null,
                    'audio' => null,
                ];
            }

            // If summarize is null → cache null
            if (empty($resultSummarize)) {
                Cache::put($cacheKey, ['summarize' => null, 'audio' => $resultAudio], now()->addDay());
                return [
                    'summarize' => null,
                    'audio' => $resultAudio,
                ];
            }

            // If summarize is null → cache null
            if (empty($resultAudio)) {
                Cache::put($cacheKey, ['summarize' => $resultSummarize, 'audio' => null], now()->addDay());
                return [
                    'summarize' => $resultSummarize,
                    'audio' => null,
                ];
            }

            // Summarize exists → return it (even if audio is null)
            $result = [
                'summarize' => $resultSummarize,
                'audio' => $resultAudio,
            ];

            // Cache the successful result for 6 hours
            Cache::put($cacheKey, $result, now()->addHours(6));

            return $result;
        } catch (RequestException $e) {
            Log::error('NocoDB API request failed', [
                'url' => $this->baseUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Cache the failure for 1 hour
            Cache::put($cacheKey, ['summarize' => null, 'audio' => null], now()->addDay());

            return [
                'summarize' => null,
                'audio' => null,
            ];
        }
    }

    public function getNecessaryNews($projectId, $tableId)
    {
        $cacheKey = "necessary_articles_{$projectId}_{$tableId}";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($projectId, $tableId) {
            try {
                $response = $this->client->get("/api/v1/db/data/v1/{$projectId}/{$tableId}?sort=-Id");
                $articleList = json_decode($response->getBody(), true);
                return $articleList['list'];
            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
