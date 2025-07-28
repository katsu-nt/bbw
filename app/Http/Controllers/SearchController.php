<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Services\UserActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;

class SearchController extends Controller
{
    protected $searchService;
    protected $activityService;

    public function __construct(SearchService $searchService, UserActivityService $activityService)
    {
        $this->searchService = $searchService;
        $this->activityService = $activityService;
    }

    public function showKeywordArticles(Request $request)
    {
        // Prevent browser caching
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            $keyword = $request->input('q');
            $page = $request->input('page', 1); // Current page
            $perPage = 5; // Items per page

            // Fetch articles
            $articleDetails = $this->searchService->getArticlesByKeyword(urldecode($keyword));
            $articles = $articleDetails['ListArticleResult'] ?? [];

            // Paginate articles using LengthAwarePaginator
            $total = count($articles);
            $articlesForPage = array_slice($articles, ($page - 1) * $perPage, $perPage);

            $paginatedArticles = new LengthAwarePaginator(
                $articlesForPage, // Current page items
                $total, // Total items
                $perPage, // Items per page
                $page, // Current page
                ['path' => url('search'), 'query' => ['q' => $keyword]] // Preserve query
            );

            return view('search_results', [
                'articleDetails' => ['ListArticleResult' => $paginatedArticles],
                'keyword' => $keyword
            ]);
        } catch (\Exception $e) {
            return view('error_page')->with('error', 'Không thể tải dữ liệu.');
        }
    }

    public function searchAjax(Request $request)
    {
        try {
            $keyword = $request->input('q');
            $sort = $request->input('sort', 'desc');
            $category = $request->input('cate', 'tat-ca');
            $time = $request->input('time', 'current');
            $page = $request->input('page', 1);
            $perPage = 5;

            // Fetch articles
            $articleDetails = $this->searchService->getArticlesByKeyword(urldecode($keyword));
            $articles = $articleDetails['ListArticleResult'] ?? [];

            // Filter by category
            if ($category !== 'tat-ca') {
                $articles = array_filter($articles, function ($article) use ($category) {
                    return strtolower($article['Channel']['FriendlyName']) === $category;
                });
            }

            // Filter by time period
            if ($time !== 'current') {
                $articles = $this->filterArticlesByTime($articles, $time);
            }

            // Sort by date
            usort($articles, function ($a, $b) use ($sort) {
                $dateA = (int) filter_var($a['PublishedTime'], FILTER_SANITIZE_NUMBER_INT);
                $dateB = (int) filter_var($b['PublishedTime'], FILTER_SANITIZE_NUMBER_INT);
                return $sort === 'asc' ? $dateA <=> $dateB : $dateB <=> $dateA;
            });

            // Paginate using LengthAwarePaginator
            $total = count($articles);
            $articlesForPage = array_slice($articles, ($page - 1) * $perPage, $perPage);

            $paginatedArticles = new LengthAwarePaginator(
                $articlesForPage,
                $total,
                $perPage,
                $page,
                ['path' => url('search-ajax'), 'query' => ['q' => $keyword, 'sort' => $sort, 'cate' => $category]]
            );

            return response()->json([
                'articleDetails' => $paginatedArticles->items(),
                'keyword' => $keyword,
                'meta' => [
                    'total' => $paginatedArticles->total(),
                    'current_page' => $paginatedArticles->currentPage(),
                    'per_page' => $paginatedArticles->perPage(),
                    'last_page' => $paginatedArticles->lastPage(),
                    'links' => [
                        'first' => $paginatedArticles->url(1),
                        'last' => $paginatedArticles->url($paginatedArticles->lastPage()),
                        'prev' => $paginatedArticles->previousPageUrl(),
                        'next' => $paginatedArticles->nextPageUrl(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể tải dữ liệu.'], 500);
        }
    }

    /**
     * Filter articles by time period
     */
    private function filterArticlesByTime($articles, $timeFilter)
    {
        $now = time();

        return array_filter($articles, function ($article) use ($timeFilter, $now) {
            $articleTime = null;

            if (isset($article['PublishedTime'])) {
                // Convert milliseconds to seconds
                $articleTime = (int) filter_var($article['PublishedTime'], FILTER_SANITIZE_NUMBER_INT) / 1000;
            } elseif (isset($article['Time_yyyyMMddHHmmss'])) {
                $dateString = $article['Time_yyyyMMddHHmmss'];
                if (strlen($dateString) >= 8) {
                    $year = substr($dateString, 0, 4);
                    $month = substr($dateString, 4, 2);
                    $day = substr($dateString, 6, 2);
                    $hour = strlen($dateString) >= 10 ? substr($dateString, 8, 2) : '00';
                    $minute = strlen($dateString) >= 12 ? substr($dateString, 10, 2) : '00';
                    $second = strlen($dateString) >= 14 ? substr($dateString, 12, 2) : '00';

                    $articleTime = mktime($hour, $minute, $second, $month, $day, $year);
                }
            }

            if (!$articleTime) {
                return true; // Or false, if you want to exclude articles with invalid time
            }

            switch ($timeFilter) {
                case 'in24Hours':
                    return $articleTime >= strtotime('-24 hours');
                case 'lastMonth':
                    return $articleTime >= strtotime('-1 month');
                case 'current':
                default:
                    return true;
            }
        });
    }


    private function parseArticleDate($dateString)
    {
        // Parse the date string in format yyyyMMddHHmmss
        return \DateTime::createFromFormat(
            'YmdHis',
            $dateString
        );
    }
}
