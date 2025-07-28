<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserBBW;
use App\Models\UserBehavior;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookmarkController extends Controller
{
    const ITEMS_PER_PAGE = 5;

    public function show(Request $request)
    {
        $isLogin = $request->session()->get('user_data') ? true : false;
        $hasCookieToken = true;

        if ($isLogin) {
            $userCurrent = UserBBW::where('email', $request->session()->get('user_data')['data']['email'])->first();

            $cookie = null; // Initialize cookie variable
            if (!$userCurrent->exists()) {
                $hasCookieToken = false;
                $user = UserBBW::Create(['email' => $request->session()->get('user_data')['data']['email']]);

                // Save to Session
                $userData = $request->session()->get('user_data', []);
                $userData['data']['uuid'] = $user->getOriginal()['uuid'];
                $request->session()->put('user_data', $userData);
                $request->session()->save();

                $token = $user->createToken('authToken')->plainTextToken;
                $cookie = cookie(
                    'bbw_token',
                    $token,
                    config('session.lifetime'), // expiration
                    null, // path
                    null, // domain
                    true, // secure
                    true  // httponly
                ); // HttpOnly = true
            } else {
                $cookie = $request->cookie('bbw_token');
                // Check if the user has a token in the cookie
                if ($request->cookie('bbw_token') === null) {
                    $hasCookieToken = false;

                    // If not, create a new token and store it in the session
                    $token = $userCurrent->createToken('authToken')->plainTextToken;
                    $cookie = cookie(
                        'bbw_token',
                        $token,
                        config('session.lifetime'), // expiration
                        null, // path
                        null, // domain
                        true, // secure
                        true  // httponly
                    ); // HttpOnly = true
                } else {
                    // If the token exists, use it
                    $token = $request->cookie('bbw_token');
                    $hasCookieToken = true;
                }
            }

            $uuid = $request->session()->get('user_data')['data']['uuid'];
            $bookmarks = $this->getInfoBookmarks($uuid);
        } else {
            return response()->view('bookmark', [
                'bookmarks' => [],
                'paginator' => [],
                'isLogin' => false,
            ]);
        }

        if ($hasCookieToken === true)
            return view('bookmark', [
                'bookmarks' => $bookmarks['data'],
                'paginator' => $bookmarks['paginator'],
                'isLogin' => $isLogin,
            ]);
        else
            return response()->view('bookmark', [
                'bookmarks' => $bookmarks['data'],
                'paginator' => $bookmarks['paginator'],
                'isLogin' => $isLogin,
            ])->withCookie($cookie);
    }

    protected function getAllBookmarks($uuid)
    {
        return UserBehavior::where('uuid', $uuid)
            ->where('action', 'save')
            ->orderBy('created_at', 'desc')
            ->paginate(self::ITEMS_PER_PAGE);
    }

    protected function getInfoBookmarks($uuid)
    {
        $bookmarks = $this->getAllBookmarks($uuid);
        $apiDomain = env('API_DOMAIN');

        $results = [];

        foreach ($bookmarks as $bookmark) {
            $publisherId = $bookmark['publisherId'];
            $apiUrl = "{$apiDomain}/api/v1/getarticleinfo/{$publisherId}";

            try {
                $response = Http::get($apiUrl);
                $articleData = $response->json();

                $results[] = [
                    'bookmark' => $bookmark,
                    'article' => $articleData,
                ];
            } catch (\Exception $e) {
                \Log::error("Failed to fetch article {$publisherId}: " . $e->getMessage());
                $results[] = [
                    'bookmark' => $bookmark,
                    'article' => null,
                ];
            }
        }

        return [
            'data' => $results,
            'paginator' => $bookmarks,
        ];
    }

    // Add this method to your BookmarkController
    protected function removeBookmark(Request $request)
    {
        $uuid = $request->session()->get('user_data')['data']['uuid'];
        $publisherId = $request->input('publisherId');

        // Delete the bookmark
        UserBehavior::where('uuid', $uuid)
            ->where('publisherId', $publisherId)
            ->where('action', 'save')
            ->delete();

        // Return remaining count for pagination
        $remainingCount = UserBehavior::where('uuid', $uuid)
            ->where('action', 'save')
            ->count();

        return response()->json([
            'success' => true,
            'remainingCount' => $remainingCount,
            'perPage' => self::ITEMS_PER_PAGE
        ]);
    }
}
