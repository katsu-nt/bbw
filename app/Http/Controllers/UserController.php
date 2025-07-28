<?php

namespace App\Http\Controllers;

use App\Services\UserActivityService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    protected $activityService;
    public function __construct(UserActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function getUserData(Request $request)
    {
        $authResponse = $request->cookie('_bresponse');

        if (!$authResponse) {
            return response()->json(['error' => 'Không có token xác thực'], 401);
        }

        try {
            $accessToken = $authResponse['access_token'];

            $userResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(env('SUB_OAUTH_URL') . '/api/user');

            $userData = $userResponse->json();

            // Kiểm tra và lưu session
            if ($userData && isset($userData['data'])) {
                $request->session()->put('user_data', $userData);
                // Đảm bảo session được lưu ngay lập tức
                $request->session()->save();

                Log::info('Session stored:', ['user_data' => $userData]);
            } else {
                Log::warning('Invalid user data received', ['response' => $userData]);
            }

            return response()->json($userData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi lấy dữ liệu người dùng',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // if ($request->session()->get('user_data')) {
        //     $userDataId = ($request->session()->get('user_data'))['data']['id'];
        //     $articlesReadInMonth = $this->activityService->getRedisArticlesReadInMonth($userDataId);
        //     if ($articlesReadInMonth > count($request->session()->get('bbw_arts', [])))
        //         $request->session()->put('bbw_arts', array_slice($articlesReadInMonth, 0, 4));
        // }

        // Clear access token
        $token = $request->cookie('bbw_token');
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $accessToken->delete();
            }
        }

        $request->session()->forget('user_data');
        $request->session()->forget('intended_url');
        $request->session()->forget('_previous');

        // Cookie::queue(Cookie::forget('bbw_uid'));
        Cookie::forget('bbw_token');

        Cache::flush();
        $authResponse = Cookie::get('_bresponse');
        if (!$authResponse) {
            Log::error("No auth response available in the cookie.");
            return redirect()->intended('/');
        }


        $authData = json_decode($authResponse, true);
        $accessToken = $authData['access_token'] ?? null;

        if (!$accessToken) {
            Log::error("No access token found in the auth response.");
            return redirect()->intended('/');
        }

        $client = new Client();
        try {
            $response = $client->post(env('SUB_OAUTH_URL') . '/api/logout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept'        => 'application/json',
                ],
            ]);

            // Log response regardless of status
            // Log::info('Logout attempt response.', [
            //     'status_code' => $response->getStatusCode(),
            //     'body' => $response->getBody()->getContents()
            // ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error("Logout failed: " . $e->getMessage());
        }

        // Clear session and cookies regardless of the API response

        $cookie = Cookie::forget('_bresponse');

        $redirect_bbw = $request->query('redirect_bbw');
        // Redirect to the specified URL after logout
        return redirect()->away(env('SUB_OAUTH_URL') . "/users/logout?redirect_bbw=" . $redirect_bbw)->withCookie($cookie);
    }
}
