<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserActivityService
{
    /**
     * Tracking article read
     *
     * @param int $userId
     * @param int $articleId
     * @return void
     */
    public function trackArticleRead($userId, $articleId)
    {
        // Redis key structure: user:{userId}:activity
        $keyArticleRead = "user:{$userId}:articles_read";

        // Increment the "articles_read" count compatitive with articles_id
        Redis::hincrby($keyArticleRead, $articleId, 1);

        // Set an expiry for this key (e.g., 1 month)
        Redis::expire($keyArticleRead, 604800); // 7 days
    }

    /**
     * Get the number of articles a user has read.
     *
     * @param int $userId
     * @return int
     */
    public function getArticleReadStats($userId)
    {
        // Redis key structure: user:{userId}:articles_read
        $keyArticleRead = "user:{$userId}:articles_read";

        // Retrieve all article IDs and their read counts
        $articleReadStats = Redis::hgetall($keyArticleRead);

        return $articleReadStats; // Returns an associative array: ['articleId' => count, ...]
    }

    /**
     * Tracking keyword when searching
     *
     * @param int $userId
     * @param string $keyword
     * @return void
     */
    public function trackKeywordSearch($userId, $keyword)
    {
        // Redis key structure: user:{userId}:activity
        $keySearch = "user:{$userId}:keyword_search";

        // Increment the "articles_read" count compatitive with articles_id
        Redis::hincrby($keySearch, $keyword, 1);

        // Set an expiry for this key (e.g., 1 month)
        Redis::expire($keySearch, 604800); // 7 days
    }

    /**
     * Handle Save List Read Articles In Month
     *
     * @param int $publisherId
     * @param Request $request
     * @return void
     */
    public function handleRedisArticlesReadInMonth($publisherId, Request $request)
    {
        $userData = $request->session()->get('user_data');
        if ($userData && isset($userData['data'])) {
            $userDataId = $userData['data']['id']; // Assuming the user ID is stored under 'data' -> 'id'
            $keyArticlesListInMonth = "user:{$userDataId}:articles_read_in_month";
            $articlesReadInMonth = ($request->session()->get('bbw_arts'));
            if (!empty($articlesReadInMonth) && count($articlesReadInMonth) === 5) {
                array_pop($articlesReadInMonth);
            }

            if (!empty($articlesReadInMonth)) {
                Redis::ltrim($keyArticlesListInMonth, 0, 7);

                foreach ($articlesReadInMonth as $index => $article) {
                    // Ensure Redis list has at least the required number of elements
                    if (Redis::llen($keyArticlesListInMonth) <= $index) {
                        // If the list is shorter than needed, push the new article
                        Redis::rpush($keyArticlesListInMonth, $article);
                    } else {
                        // Set each article at the respective index
                        Redis::lset($keyArticlesListInMonth, $index, $article);
                    }
                }
            }

            $articleList = $this->getRedisArticlesReadInMonth($userDataId);

            if (count($articleList) !== 7 && !in_array($publisherId, $articleList)) {
                Redis::rpush($keyArticlesListInMonth, $publisherId);

                // Calculate expiration time until the start of the next month
                $currentDate = new \DateTime();
                $nextMonth = new \DateTime('first day of next month midnight');
                $secondsUntilNextMonth = $nextMonth->getTimestamp() - $currentDate->getTimestamp();

                // Set the key to expire at the start of the next month
                Redis::expire($keyArticlesListInMonth, $secondsUntilNextMonth);
            }
        }
    }

    /**
     * Handle Get List Read Articles In Month
     *
     * @param int $userDataId
     */
    public function getRedisArticlesReadInMonth($userDataId)
    {
        $keyArticlesListInMonth = "user:{$userDataId}:articles_read_in_month";

        // Retrieve the list of articles
        $articlesReadListInMonth = Redis::lrange($keyArticlesListInMonth, 0, -1);

        return $articlesReadListInMonth; // Returns an array of article IDs
    }
}
