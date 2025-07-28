<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArticleMetaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleMetaDataController extends Controller
{
    // Check if metadata exists by publisherId
    public function checkExist($id)
    {
        try {
            $exists = ArticleMetaData::where('publisherId', $id)->exists();
            return response()->json($exists, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error checking metadata for ID: ' . $id, ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Database error',
                'message' => app()->environment('production') ? 'An error occurred' : $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error checking metadata for ID: ' . $id, ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Server error',
                'message' => app()->environment('production') ? 'An error occurred' : $e->getMessage()
            ], 500);
        }
    }

    // Add new article metadata
    public function add(Request $request)
    {
        try {
            $data = $request->validate([
                'publisherId' => 'required|string|max:255',
                'title' => 'nullable|string|max:255',
                'author' => 'nullable|string|max:255',
                'keywords' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'content' => 'required|string',
                'og_image' => 'nullable|string|max:255',
                'og_url' => 'nullable|string|max:255',
                'namechannel' => 'nullable|string|max:255',
                'cateslug' => 'nullable|string|max:255',
                'summarize' => 'nullable|string',
                'audio' => 'nullable|string',
            ]);

            $metaData = ArticleMetaData::updateOrCreate($data);

            return response()->json(['success' => true, 'data' => $metaData]);
        } catch (\Exception $e) {
            Log::error('Error adding article metadata: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => app()->environment('production') ? 'An error occurred' : $e->getMessage()
            ], 500);
        }
    }

    public function getByPushlisherId(Request $request) {}
}
