<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Illuminate\Support\Facades\Log;
use Predis\Client;

class CacheController extends Controller
{
    public function clearAllCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response()->json([
            'message' => 'Cache cleared successfully!'
        ]);
    }

    public function deleteRedisKey(Request $request)
    {
        try {
            $redis = $this->getRedisClient(2);
            $key = $request->get('key');
            
            if (!$key) {
                return response()->json([
                    'message' => 'Key parameter is required',
                    'success' => false
                ], 400); // Bad Request
            }
            
            $result = $redis->del($key);
            $redis->disconnect();
            
            if ($result > 0) {
                // Key deleted successfully
                return response()->json([
                    'message' => 'Key deleted successfully',
                    'success' => true,
                    'deleted_count' => $result
                ], 200); // OK
            } else {
                // Key not found
                return response()->json([
                    'message' => 'Key not found or already deleted',
                    'success' => false,
                    'key' => $key
                ], 404); // Not Found
            }
            
        } catch (Exception $e) {
            // Server error
            return response()->json([
                'message' => 'Failed to delete Redis key',
                'error' => $e->getMessage(),
                'success' => false
            ], 500); // Internal Server Error
        }
    }

    public function flushRedis(Request $request)
    {
        try {
            $redis = $this->getRedisClient(2);
            
            $isCluster = config('database.redis.options.cluster') === 'redis';
            if ($isCluster) {
                $redis->flushdb();
            } else {
                $redis->flushdb();
            }
            
            $redis->disconnect();

            return response()->json([
                'message' => 'Redis cache cleared successfully',
                'success' => true
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to clear Redis cache',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
    
    private function getRedisClient($database = null)
    {
        $config = config('database.redis.cache');
        $options = [];
        
        // Xử lý kết nối Cluster nếu được cấu hình
        if (config('database.redis.options.cluster') === 'redis') {
            $options['cluster'] = 'redis';
        }
        
        $parameters = [
            'host' => $config['host'],
            'port' => $config['port']
        ];
        
        if (!empty($config['password'])) {
            $parameters['password'] = $config['password'];
        }
        
        if ($database !== null) {
            $parameters['database'] = $database;
        } else if (isset($config['database'])) {
            $parameters['database'] = $config['database'];
        }
        
        return new \Predis\Client($parameters, $options);
    }
}
