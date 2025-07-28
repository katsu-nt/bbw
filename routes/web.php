<?php
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventArticleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\MarketController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Thi truong


Route::get('/du-lieu/thi-truong', [MarketController::class, 'index'])->name('market.index');



// Cache Management Routes
Route::get('/du-lieu/doanh-nghiep', [StructureController::class, 'showStructure']);
Route::get('/api/normalized-performance', [StructureController::class, 'getNormalizedPerformance']);
Route::get('/latestFinancialMetrics', [StructureController::class, 'getlatestFinancialMetrics']);

// Route::get(uri: '/du-lieu/doanh-nghiep', [PagesController::class, 'showDataBusiness'])->name('show_data_business.show');

Route::prefix('cache')->group(function () {
    Route::get('/', [CacheController::class, 'index'])->name('cache.index');
    Route::get('/overview', [CacheController::class, 'overview'])->name('cache.overview');

    Route::prefix('redis')->group(function () {
        Route::get('/keys', [CacheController::class, 'getRedisKeys'])->name('cache.redis.keys');
        Route::post('/flush', [CacheController::class, 'flushRedis'])->name('cache.redis.flush');
        Route::post('/delete', [CacheController::class, 'deleteRedisKey'])->name('cache.redis.delete');
    });

    Route::prefix('file')->group(function () {
        Route::post('/clear', [CacheController::class, 'clearFileCache'])->name('cache.file.clear');
    });

    Route::post('/clear-all', [CacheController::class, 'clearAllCache'])->name('cache.clear-all');
    Route::post('/optimize', [CacheController::class, 'optimizeCache'])->name('cache.optimize');
});

Route::post('/clear-cache-and-redirect', function () {
    Cache::flush();
    return redirect()->route('error_page');
})->name('clear_cache_and_redirect');

Route::get('/error-page', function () {
    return view('error_page'); // Make sure you have a Blade view named error_page.blade.php
})->name('error_page');

Route::get('/', [HomepageController::class, 'showHomepage'])->middleware('simple.cache')->name('homepage');
Route::get('/saved', [BookmarkController::class, 'show'])->name('bookmark.show');
Route::get('/chinh-sach-bao-mat', [PagesController::class, 'showPrivacyPolicy'])->name('privacy_policy.show');
Route::get('/dieu-khoan-dich-vu', [PagesController::class, 'showTermsAndConditions'])->name('terms_and_conditions.show');

Route::get('/bao-cao-dac-biet', [EventController::class, 'showSpecialReports'])->name('special.reports');
Route::get('/search', [SearchController::class, 'showKeywordArticles'])
    ->name('search.articles');

Route::get('/{slug}.html', [EventArticleController::class, 'showEvent'])
    ->name('event.show')
    ->where('slug', '.*-event[0-9]+');

Route::get('/{slugPublisher}', [ArticleController::class, 'show'])
    ->name('article.show')
    ->where('slugPublisher', '.*-\d+\.html$')
    ->middleware('simple.cache');


Route::get('/{slug}', [CategoryController::class, 'show'])
    ->name('category.show')
    ->where('slug', '^[^/]*$')
    ->middleware('simple.cache');

Route::get('/loadmore/{channelId}/{page}/{limit}', [CategoryController::class, 'loadMore'])->name('category.loadmore');

Route::get('/oauth/redirect', [AuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/api/auth/callback', [AuthController::class, 'handleProviderCallback']);

Route::middleware(['auth.check'])->get('/user', 'UserController@getUserData')->name('user.data');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/ads/{zone}.js', [AdsController::class, 'serve']);
Route::get('/ads/all', [AdsController::class, 'serveAll']);
Route::get('/api/search-keyword-article', [SearchController::class, 'searchAjax'])->name('searchAjax');Route::get('/structure', [StructureController::class, 'showStructure']);