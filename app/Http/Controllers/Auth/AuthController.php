<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserBBW;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function redirectToProvider(Request $request)
    {
        $intendedUrl = $request->headers->get('referer') ?? '/';
        session(['intended_url' => $intendedUrl]); // Store the intended URL in the session

        // Now redirect to the OAuth provider
        $query = http_build_query([
            'client_id' => env('SUB_CLIENT_ID'),
            'redirect_uri' => env('SUB_AUTH_URL') . '/api/auth/callback',
            'response_type' => 'code',
            'scope' => '',
        ]);

        return redirect(env('SUB_OAUTH_URL') . '/oauth/authorize?' . $query);
    }

    public function handleProviderCallback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return response()->json(['error' => 'Authorization code is required'], 400);
        }

        $client = new Client();
        try {
            $response = $client->post(env('SUB_OAUTH_URL') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => env('SUB_CLIENT_ID'),
                    'client_secret' => env('SUB_CLIENT_SECRET'),
                    'redirect_uri' => env('SUB_AUTH_URL') . '/api/auth/callback',
                    'code' => $code,
                ]
            ]);

            $data = json_decode((string) $response->getBody(), true);


            $accessToken = $data['access_token'];
            // Log::info('Received access token', ['access_token' => $accessToken]);
            $userResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(env('SUB_OAUTH_URL') . '/api/user');
            $userData = $userResponse->json();

            $email = $userData['data']['email'];
            $user = UserBBW::firstOrCreate(['email' => $email]);
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            $userData['data']['uuid'] = $user['uuid'];
            session(['user_data' => $userData]);

            $authToken = cookie(
                'bbw_token',
                $tokenResult,
                config('session.lifetime'), // expiration
                null, // path
                null, // domain
                true, // secure
                true  // httponly
            );

            // Tạo cookie mới cho UUID (không set httpOnly để JS có thể đọc được)
            $uuidCookie = cookie(
                'bbw_uuid',                                   // tên cookie
                $user['uuid'],                                // giá trị
                config('session.lifetime'),                   // thời gian hết hạn (phút)
                '/',                                          // path
                null,                                         // domain
                true,                                         // secure
                false,                                        // httpOnly
                false                                         // raw - set false để không mã hóa
            );
            // Retrieve the stored intended URL from the session
            $intendedUrl = session('intended_url', '/');

            // Create a response and attach cookies to it
            return redirect($intendedUrl)
                // tạo cookies chứa access_token, đặt tên viết tắt của Beacon Response
                ->cookie('_bresponse', json_encode($data), config('session.lifetime'), '/', null, true, true)
                ->withCookie($authToken)
                ->withCookie($uuidCookie);  // Thêm cookie UUID
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error exchanging code for token',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
