<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class EventService
{
    private $client;
    private $apiDomain;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiDomain = env('API_DOMAIN', 'https://bbw.vn');
    }

    public function getEventsByChannel($channelId)
    {
        $cacheKey = "events_channel_{$channelId}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->client->get("{$this->apiDomain}/api/v1/getallevent");
        $data = json_decode($response->getBody(), true);
        $events = collect($data)->where('ChannelId', $channelId)->sortBy('Priority')->toArray();

        Cache::put($cacheKey, $events, 86400); // Cache for 24 hours

        return $events;
    }
}
