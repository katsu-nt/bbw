<?php

use App\Http\Controllers\Api\ArticleMetaDataController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\UserBehaviorController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/get-articles-newest', [CategoryController::class, 'getArticlesNewest'])->name('articles-newest');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:sanctum')->group(function () {});

Route::middleware('api.key')->group(function () {
    Route::get('/check-exist-meta-data/{id}', [ArticleMetaDataController::class, 'checkExist']);
    Route::post('/add-meta-data', [ArticleMetaDataController::class, 'add']);
    Route::post('/cache/flush-redis', [CacheController::class, 'flushRedis'])->name('cache.redis.flush');
    Route::post('/cache/delete', [CacheController::class, 'deleteRedisKey'])->name('cache.redis.delete');
});

Route::post('/handle-bookmark-article', [UserBehaviorController::class, 'handleBookmarkArticle'])->name('user-behavior.bookmark');


// ----------------------------------------------------------------------------------------------------------------
// HANDLE USER MENU ROUTES
// Helper function to get current user data
function getCurrentUser()
{
    return session('user_data')['data'] ?? null;
}

// Helper function to get redirect URL from request
function getRedirectUrl(Request $request)
{
    $redirectUrl = $request->query('redirect_bbw');
    return $redirectUrl ? urldecode($redirectUrl) : null;
}

// Main user menu route
Route::get('/user-menu', function (Request $request) {
    $user = getCurrentUser();
    $redirect_bbw = getRedirectUrl($request);
    return view('components.user-menu', compact('user', 'redirect_bbw'));
})->name('user-menu');


// Phone menu route
Route::get('/user-menu-phone', function () {
    $user = getCurrentUser();
    return view('components.user-menu-phone', compact('user'));
})->name('user-menu-phone');



// Logout menu route
Route::get('/user-menu-logout', function (Request $request) {
    $user = getCurrentUser();

    if (!$user) {
        return response('', 204); // No content response for guests
    }

    $redirect_bbw = getRedirectUrl($request);

    return view('components.user-menu-logout', compact('redirect_bbw'));
})->name('user-menu-logout');
// ----------------------------------------------------------------------------------------------------------------