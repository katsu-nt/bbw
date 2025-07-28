<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserBehavior;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UserBehaviorController extends Controller
{
    /**
     * Add user behavior if it doesn't exist.
     *
     * @param string $uuid
     * @param string $publisherId
     * @param string $token
     * @param string $action
     * @return \App\Models\UserBehavior
     * @throws \Exception
     */
    public function handleBookmarkArticle(Request $request)
    {
        try {
            // Get token from cookie
            $token = $request->cookie('bbw_token');
            $uuid = $request->session()->get('user_data')['data']['uuid'] ?? null;
            if (!$token) {
                return response()->json(['message' => 'Unauthorized: Missing token'], 401);
            }

            $tokenModel = PersonalAccessToken::findToken($token);
            if (!$tokenModel || !$tokenModel->tokenable) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Validate the request input
            $validated = Validator::make($request->all(), [
                'publisherId' => ['required', 'string'],
            ]);

            if ($validated->fails()) {
                return response()->json(['message' => 'Validation error', 'errors' => $validated->errors()], 422);
            }

            // Get validated values
            $publisherId = $request->input('publisherId');

            // Check if behavior already exists
            $checkExistSave = UserBehavior::where('uuid', $uuid)
                ->where('publisherId', $publisherId)
                ->where('action', 'save')
                ->first();
            $checkExistUnsave = UserBehavior::where('uuid', $uuid)
                ->where('publisherId', $publisherId)
                ->where('action', 'unsave')
                ->first();

            // Create new behavior if it doesn't exist
            if (!$checkExistSave && !$checkExistUnsave) {
                $behavior = UserBehavior::create([
                    'uuid' => $uuid,
                    'publisherId' => $publisherId,
                    'action' => 'save',
                ]);
            } else {
                $action = $checkExistSave ? 'save' : 'unsave';

                // If it exists, update the action to 'unsave'
                if ($action === 'save') {
                    $behavior = UserBehavior::where('uuid', $uuid)
                        ->where('publisherId', $publisherId)
                        ->where('action', 'save')
                        ->update(['action' => 'unsave']);
                } else {
                    // If it exists, update the action to 'save'
                    $behavior = UserBehavior::where('uuid', $uuid)
                        ->where('publisherId', $publisherId)
                        ->where('action', 'unsave')
                        ->update(['action' => 'save']);
                }
            }

            return response()->json([
                'message' => 'Action recorded',
                'data' => $behavior,
            ]);
        } catch (\Exception $e) {
            \Log::error('UserBehavior error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
