<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        return view('cache.dashboard');
    }

    public function overview()
    {
        try {
            $data = [
                'redis' => $this->getRedisStats(),
                'file' => $this->getFileCacheStats(),
                'system' => $this->getSystemCacheStats()
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRedisStats()
    {
        try {
            if (!extension_loaded('redis')) {
                return ['status' => 'unavailable', 'message' => 'Redis extension not loaded'];
            }

            try {
                // Thử kết nối Redis trực tiếp
                $redis = $this->getRedisClient(2);
                $info = $redis->info();
                $redis->disconnect();
                
                // Nếu không có lỗi, Redis đang kết nối
                return ['status' => 'connected'];
            } catch (Exception $redisException) {
                // Nếu có lỗi kết nối Redis
                Log::error('Redis connection error in getRedisStats: ' . $redisException->getMessage());
                return [
                    'status' => 'connection_error',
                    'message' => $redisException->getMessage()
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function getRedisSingleStats()
    {
        try {
            $redis = $this->getRedisClient(2);
            
            $info = $redis->info();

            if (is_array($info)) {
                $usedMemory = $info['used_memory_human'] ?? $info['Memory']['used_memory_human'] ?? 'N/A';
                $peakMemory = $info['used_memory_peak_human'] ?? $info['Memory']['used_memory_peak_human'] ?? 'N/A';
                $version = $info['redis_version'] ?? $info['Server']['redis_version'] ?? 'N/A';
                $clients = $info['connected_clients'] ?? $info['Clients']['connected_clients'] ?? 0;
                $uptime = $info['uptime_in_seconds'] ?? $info['Server']['uptime_in_seconds'] ?? 0;
            } else {
                $infoArray = [];
                foreach (explode("\r\n", $info) as $line) {
                    if (strpos($line, ':') !== false) {
                        list($key, $value) = explode(':', $line, 2);
                        $infoArray[$key] = $value;
                    }
                }
                $usedMemory = $this->formatBytes($infoArray['used_memory'] ?? 0);
                $peakMemory = $this->formatBytes($infoArray['used_memory_peak'] ?? 0);
                $version = $infoArray['redis_version'] ?? 'N/A';
                $clients = $infoArray['connected_clients'] ?? 0;
                $uptime = $infoArray['uptime_in_seconds'] ?? 0;
            }

            $result = [
                'status' => 'connected',
                'mode' => 'single',
                'memory_usage' => $usedMemory,
                'memory_peak' => $peakMemory,
                'total_keys' => $redis->dbSize(),
                'connected_clients' => $clients,
                'uptime' => $uptime,
                'version' => $version,
                'hit_rate' => $this->calculateHitRate($info),
                'keyspace' => $this->getKeyspaceInfo($redis),
                'connection' => config('database.redis.default')
            ];

            $redis->close();
            return $result;
        } catch (Exception $e) {
            return [
                'status' => 'connection_error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function getRedisClusterStats()
    {
        try {
            $redis = new \Predis\Client();
            $config = config('database.redis.cache');
            $redis->connect($config['host'], $config['port']);
            if (!empty($config['password'])) {
                $redis->auth($config['password']);
            }
            $redis->select($config['database'] ?? 0);

            $clusterInfo = $redis->rawCommand('CLUSTER', 'INFO');
            $clusterInfoArray = [];

            foreach (explode("\n", $clusterInfo) as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $clusterInfoArray[trim($key)] = trim($value);
                }
            }

            $nodes = $redis->rawCommand('CLUSTER', 'NODES');
            $nodeList = [];
            $masterCount = 0;

            foreach (explode("\n", $nodes) as $line) {
                if (empty(trim($line))) continue;

                $parts = preg_split('/\s+/', $line);
                if (count($parts) < 8) continue;

                $flags = $parts[2];
                if (strpos($flags, 'master') !== false) {
                    $masterCount++;
                    $nodeList[] = [
                        'id' => $parts[0],
                        'address' => $parts[1],
                        'flags' => $flags,
                        'slots' => $parts[8] ?? 'N/A'
                    ];
                }
            }

            $result = [
                'status' => 'connected',
                'mode' => 'cluster',
                'cluster_state' => $clusterInfoArray['cluster_state'] ?? 'unknown',
                'cluster_slots_assigned' => $clusterInfoArray['cluster_slots_assigned'] ?? 0,
                'cluster_slots_ok' => $clusterInfoArray['cluster_slots_ok'] ?? 0,
                'cluster_known_nodes' => $clusterInfoArray['cluster_known_nodes'] ?? 0,
                'cluster_size' => $clusterInfoArray['cluster_size'] ?? 0,
                'master_nodes' => $masterCount,
                'nodes' => $nodeList,
                'total_keys' => $this->getClusterTotalKeys($redis),
                'connection' => config('database.redis.default')
            ];

            $redis->close();
            return $result;
        } catch (Exception $e) {
            return [
                'status' => 'cluster_error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function getClusterTotalKeys($redis)
    {
        try {
            $totalKeys = 0;
            $nodes = $redis->rawCommand('CLUSTER', 'NODES');
            $lines = explode("\n", $nodes);

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                $parts = explode(' ', $line);
                if (count($parts) < 8) continue;

                $flags = $parts[2];
                if (strpos($flags, 'master') !== false) {
                    $nodeAddress = $parts[1];
                    list($host, $port) = explode(':', $nodeAddress);

                    $nodeRedis = new \Predis\Client();
                    $config = config('database.redis.cache');
                    $nodeRedis->connect($host, $port);
                    if (!empty($config['password'])) {
                        $nodeRedis->auth($config['password']);
                    }
                    $nodeRedis->select($config['database'] ?? 0);

                    $cursor = 0;
                    $nodeKeys = 0;
                    do {
                        $result = $nodeRedis->scan($cursor, ['COUNT' => 1000]);
                        if ($result !== false) {
                            list($cursor, $keys) = $result;
                            $nodeKeys += count($keys);
                        }
                    } while ($cursor != 0);

                    $totalKeys += $nodeKeys;
                    $nodeRedis->close();
                }
            }

            return $totalKeys;
        } catch (Exception $e) {
            Log::error('Failed to count keys in cluster: ' . $e->getMessage());
            return 0;
        }
    }

    public function getFileCacheStats()
    {
        try {
            $cachePath = storage_path('framework/cache/data');
            $size = 0;
            $fileCount = 0;

            if (File::exists($cachePath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($cachePath)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                        $fileCount++;
                    }
                }
            }

            return [
                'status' => 'active',
                'total_size' => $this->formatBytes($size),
                'file_count' => $fileCount,
                'cache_path' => $cachePath,
                'writable' => is_writable($cachePath)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getSystemCacheStats()
    {
        return [
            'config_cached' => File::exists(base_path('bootstrap/cache/config.php')),
            'routes_cached' => File::exists(base_path('bootstrap/cache/routes-v7.php')),
            'events_cached' => File::exists(base_path('bootstrap/cache/events.php')),
            'views_cached' => File::exists(storage_path('framework/views')),
            'default_driver' => config('cache.default'),
            'drivers' => array_keys(config('cache.stores')),
            'redis_mode' => config('database.redis.options.cluster') === 'redis' ? 'cluster' : 'single'
        ];
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

    public function getRedisKeys(Request $request)
    {
        try {
            // Sử dụng database 2 như được đề cập trong mã
            $redis = $this->getRedisClient(2);
            Log::info('Connected to Redis at ' . config('database.redis.cache.host') . ':' . config('database.redis.cache.port'));
            
            $search = $request->get('search', '');
            Log::info('Search query: "' . $search . '"');
            
            $isCluster = config('database.redis.options.cluster') === 'redis';
            Log::info('Is Redis cluster: ' . ($isCluster ? 'Yes' : 'No'));

            // Lấy thông tin tổng quan
            $info = $redis->info();
            $usedMemory = is_array($info) ? 
                ($info['used_memory_human'] ?? $info['Memory']['used_memory_human'] ?? 'N/A') :
                $this->formatBytes(0);
            $totalKeys = $redis->dbSize();
            $clients = is_array($info) ? 
                ($info['connected_clients'] ?? $info['Clients']['connected_clients'] ?? 0) : 
                0;
            $hitRate = $this->calculateHitRate($info);

            // Lấy danh sách key
            if ($isCluster) {
                Log::info('Using getRedisClusterKeys method');
                $result = $this->getRedisClusterKeys($redis, $search);
            } else {
                Log::info('Using getRedisSingleKeys method');
                $result = $this->getRedisSingleKeys($redis, $search);
            }

            // Lấy nội dung JSON từ kết quả
            $keysData = json_decode($result->getContent(), true);
            
            // Thêm thông tin tổng quan vào kết quả
            $keysData['overview'] = [
                'status' => 'connected',
                'memory_usage' => $usedMemory,
                'total_keys' => $totalKeys,
                'connected_clients' => $clients,
                'hit_rate' => $hitRate
            ];

            $redis->disconnect();
            return response()->json($keysData);
        } catch (Exception $e) {
            Log::error('Redis connection error in getRedisKeys: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getRedisSingleKeys($redis, $search)
    {
        $keyData = [];
        $cursor = 0;
        $limit = 100; // Limit to 100 keys for performance
        $count = 0;

        try {
            do {
                Log::info("SCAN with cursor: $cursor");
                $result = $redis->scan($cursor, ['COUNT' => 1000]);
                if ($result !== false) {
                    list($cursor, $keys) = $result;
                    Log::info("SCAN returned cursor: $cursor, keys count: " . count($keys));

                    foreach ($keys as $key) {
                        // Case-insensitive substring match
                        if ($search === '' || stripos($key, $search) !== false) {
                            if ($count >= $limit) {
                                Log::info("Reached limit of $limit keys, breaking loop");
                                break 2; // Exit both loops if limit reached
                            }

                            try {
                                $type = $redis->type($key);
                                $ttl = $redis->ttl($key);
                                $value = $redis->get($key);
                                $size = is_string($value) ? strlen($value) : 0;

                                $keyData[] = [
                                    'key' => $key,
                                    'type' => $type,
                                    'ttl' => $ttl,
                                    'size' => $this->formatBytes($size)
                                ];
                                $count++;
                                Log::info("Added key: $key, total so far: $count");
                            } catch (\Exception $keyError) {
                                Log::error("Error processing key '$key': " . $keyError->getMessage());
                            }
                        }
                    }
                } else {
                    Log::warning("SCAN returned false");
                }
            } while ($cursor != 0 && $count < $limit);

            Log::info("getRedisSingleKeys finished with $count keys found");
            return response()->json([
                'keys' => $keyData,
                'total' => count($keyData)
            ]);
        } catch (Exception $e) {
            Log::error('Error scanning Redis keys: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json([
                'keys' => [],
                'total' => 0,
                'error' => 'Failed to scan keys: ' . $e->getMessage()
            ]);
        }
    }

    private function getRedisClusterKeys($redis, $search)
    {
        $keyData = [];
        $limit = 100; // Limit to 100 keys for performance

        try {
            Log::info("Getting keys directly");
            $allKeys = $redis->keys('*');
            Log::info('Found ' . count($allKeys) . ' total keys');
            
            // Debug the actual keys
            if (count($allKeys) > 0) {
                Log::info('First 5 keys: ' . json_encode(array_slice($allKeys, 0, 5)));
            }
            
            // Debug the search term more carefully
            Log::info('Search term: "' . $search . '", length: ' . strlen($search) . ', empty check: ' . (empty($search) ? 'true' : 'false'));
            
            // FORCE USE ALL KEYS
            $filteredKeys = $allKeys;
            Log::info('Forcing use of all keys: ' . count($filteredKeys));
            
            // Process just the first 100 keys
            $keysToProcess = array_slice($filteredKeys, 0, $limit);
            Log::info('Processing ' . count($keysToProcess) . ' keys');
            
            foreach ($keysToProcess as $key) {
                Log::info("Processing key: $key");
                
                try {
                    $ttl = $redis->ttl($key);
                    $type = $redis->type($key);
                    $size = 'N/A';
                    
                    try {
                        $value = $redis->get($key);
                        if ($value !== false) {
                            $size = strlen($value) . ' bytes';
                        }
                    } catch (\Exception $e) {
                        // Just use N/A for size if we can't get it
                    }
                    
                    $keyData[] = [
                        'key' => $key,
                        'type' => $type,
                        'ttl' => $ttl,
                        'size' => $size
                    ];
                    Log::info("Added key to results: $key");
                } catch (\Exception $e) {
                    Log::error("Error processing key $key: " . $e->getMessage());
                }
            }
            
            Log::info("Final count of keys in keyData: " . count($keyData));
        } catch (\Exception $e) {
            Log::error('Redis error: ' . $e->getMessage());
        }
        
        // Explicitly log what we're returning
        Log::info("Returning JSON with " . count($keyData) . " keys");
        
        return response()->json([
            'keys' => $keyData,
            'total' => count($keyData),
            'total_found' => count($allKeys ?? [])
        ]);
    }

    public function deleteRedisKey(Request $request)
    {
        try {
            $redis = $this->getRedisClient(2);
            $key = $request->get('key');
            $result = $redis->del($key);

            $redis->disconnect();
            return response()->json([
                'message' => $result ? 'Key deleted successfully' : 'Key not found',
                'success' => $result > 0
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function clearFileCache()
    {
        try {
            Artisan::call('cache:clear');
            return response()->json(['message' => 'File cache cleared successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function flushRedis()
    {
        try {
            $redis = $this->getRedisClient();
            
            $isCluster = config('database.redis.options.cluster') === 'redis';
            if ($isCluster) {
                $redis->rawCommand('FLUSHALL');
            } else {
                $redis->flushDB();
            }
            $redis->disconnect();

            return response()->json(['message' => 'Redis cache cleared successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function clearAllCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            try {
                $redis = new \Predis\Client();
                $config = config('database.redis.cache');
                $redis->connect($config['host'], $config['port']);
                if (!empty($config['password'])) {
                    $redis->auth($config['password']);
                }
                $redis->select($config['database'] ?? 0);

                $isCluster = config('database.redis.options.cluster') === 'redis';
                if ($isCluster) {
                    $redis->rawCommand('FLUSHALL');
                } else {
                    $redis->flushDB();
                }
                $redis->close();
            } catch (Exception $e) {
                // Redis might not be available
            }

            return response()->json(['message' => 'All caches cleared successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function optimizeCache()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('event:cache');
            Artisan::call('view:cache');
            Log::info('Cache optimization completed successfully');
            return response()->json(['message' => 'Cache optimization completed'], 200, ['Content-Type' => 'application/json']);
        } catch (Exception $e) {
            Log::error('Cache optimization failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500, ['Content-Type' => 'application/json']);
        }
    }

    private function calculateHitRate($info)
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    private function getKeyspaceInfo($redis)
    {
        try {
            $info = $redis->info('keyspace');
            $keyspace = [];

            foreach ($info as $key => $value) {
                if (strpos($key, 'db') === 0) {
                    preg_match('/keys=(\d+),expires=(\d+)/', $value, $matches);
                    $keyspace[$key] = [
                        'keys' => $matches[1] ?? 0,
                        'expires' => $matches[2] ?? 0
                    ];
                }
            }

            return $keyspace;
        } catch (Exception $e) {
            return [];
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
