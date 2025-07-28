<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(config('services.elasticsearch.hosts'))
            ->build();
    }

    public function logArticleRead($userId, $publisherId)
    {
        $params = [
            'index' => 'user_read_articles',
            'body'  => [
                'user_id' => $userId,
                'publisher_id' => $publisherId,
                'timestamp' => now()->toAtomString(),
            ]
        ];

        return $this->client->index($params);
    }
}



