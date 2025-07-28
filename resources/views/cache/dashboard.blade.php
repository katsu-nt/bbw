@extends('cache.layout')
@extends('cache.layout')
@extends('cache.layout')

@section('content')
<div class="col-span-10 p-6">
    <!-- Overview Section -->
    <div id="overview-section" class="content-section">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Cache Overview</h2>
            <p class="text-gray-600">Monitor and manage your application's cache systems</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Redis Status</p>
                        <p id="redis-status" class="text-2xl font-bold text-gray-800">Loading...</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">File Cache Size</p>
                        <p id="file-cache-size" class="text-2xl font-bold text-gray-800">Loading...</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9l-7-7H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Memory Usage</p>
                        <p id="memory-usage" class="text-2xl font-bold text-gray-800">Loading...</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                <button onclick="clearAllCache()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M8 6V4h8v2m-9 0v14a2 2 0 002 2h6a2 2 0 002-2V6" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6" />
                    </svg>
                    <span>Clear All Cache</span>
                </button>

                <!-- <button onclick="optimizeCache()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 13 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                    <span>Optimize Cache</span>
                </button> -->

                <button onclick="refreshStats()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <polyline points="23 4 23 10 17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        <polyline points="1 20 1 14 7 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        <path d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.36 5.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                    <span>Refresh Stats</span>
                </button>

                <button onclick="checkRedisStatus()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded-lg transition-colors ml-2">
                    Check Status
                </button>
            </div>
        </div>

        <!-- System Cache Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Cache Status</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="system-cache-status">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Redis Cache Section -->
    <div id="redis-section" class="content-section hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Redis Cache</h2>
            <p class="text-gray-600">Manage your Redis cache keys and monitor performance</p>
        </div>

        <!-- Redis Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Keys</p>
                        <p id="redis-total-keys" class="text-xl font-bold text-gray-800">0</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Memory Usage</p>
                        <p id="redis-memory" class="text-xl font-bold text-gray-800">0 B</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hit Rate</p>
                        <p id="redis-hit-rate" class="text-xl font-bold text-gray-800">0%</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Connected Clients</p>
                        <p id="redis-clients" class="text-xl font-bold text-gray-800">0</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Redis Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Redis Keys</h3>
                <div class="flex space-x-4">
                    <input type="text" id="key-search" placeholder="Search keys (e.g., Cache)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <button onclick="searchRedisKeys()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">Search</button>
                    <button onclick="flushRedis()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Flush Redis</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Key</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Type</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">TTL</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Size</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="redis-keys-table">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- File Cache Section -->
    <div id="file-section" class="content-section hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">File Cache</h2>
            <p class="text-gray-600">Manage your application's file-based cache</p>
        </div>

        <!-- File Cache Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Size</p>
                        <p id="file-total-size" class="text-xl font-bold text-gray-800">0 B</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">File Count</p>
                        <p id="file-count" class="text-xl font-bold text-gray-800">0</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9l-7-7H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <p id="file-status" class="text-xl font-bold text-gray-800">Active</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- File Cache Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">File Cache Management</h3>
            <div class="flex flex-wrap gap-4">
                <button onclick="clearFileCache()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M8 6V4h8v2m-9 0v14a2 2 0 002 2h6a2 2 0 002-2V6" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6" />
                    </svg>
                    <span>Clear File Cache</span>
                </button>
                <button onclick="refreshFileStats()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <polyline points="23 4 23 10 17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        <polyline points="1 20 1 14 7 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        <path d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.36 5.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                    <span>Refresh Stats</span>
                </button>
            </div>

            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-700 mb-3">Cache Path Information</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Cache Path: <span id="cache-path" class="font-mono text-gray-800"></span></p>
                    <p class="text-sm text-gray-600 mt-1">Writable: <span id="cache-writable" class="font-medium"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Processing...</span>
    </div>
</div>

<!-- Notification Toast -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none" />
            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span id="toast-message">Success!</span>
    </div>
</div>

<script>
    let debounceTimeout;

    // Biến lưu trữ tất cả keys đã lấy từ server
    let allRedisKeys = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Set up navigation
        setupNavigation();

        // Load initial data
        loadOverviewData();
        loadFileData();

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (!document.getElementById('overview-section').classList.contains('hidden')) {
                loadOverviewData();
            }
            if (!document.getElementById('file-section').classList.contains('hidden')) {
                loadFileData();
            }
        }, 30000);

        // Add input event listener for real-time search
        const keySearchInput = document.getElementById('key-search');
        keySearchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(searchRedisKeys, 300);
        });
    });

    function setupNavigation() {
        const navItems = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.content-section');

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all nav items
                navItems.forEach(nav => {
                    nav.classList.remove('active');
                    nav.classList.add('text-gray-300');
                    nav.classList.remove('text-white');
                });

                // Add active class to clicked item
                this.classList.add('active');
                this.classList.remove('text-gray-300');
                this.classList.add('text-white');

                // Hide all sections
                sections.forEach(section => section.classList.add('hidden'));

                // Show target section
                const target = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(target + '-section');
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                    // Update URL hash
                    window.history.pushState(null, null, `#${target}`);
                    // Load Redis keys when Redis section is opened
                    if (target === 'redis') {
                        loadAllRedisKeys();
                    }
                }
            });
        });

        // Handle initial page load with hash
        window.addEventListener('load', () => {
            const hash = window.location.hash.substring(1); // Get hash without '#'
            if (hash) {
                const targetSection = document.getElementById(hash + '-section');
                if (targetSection) {
                    // Hide all sections
                    sections.forEach(section => section.classList.add('hidden'));
                    // Show the section corresponding to the hash
                    targetSection.classList.remove('hidden');
                    // Update active nav item
                    navItems.forEach(nav => {
                        nav.classList.remove('active', 'text-white');
                        nav.classList.add('text-gray-300');
                        if (nav.getAttribute('href').substring(1) === hash) {
                            nav.classList.add('active', 'text-white');
                            nav.classList.remove('text-gray-300');
                        }
                    });
                    // Load Redis keys if the hash is 'redis'
                    if (hash === 'redis') {
                        loadAllRedisKeys();
                    }
                }
            }
        });

        // Handle hash change (e.g., back/forward navigation)
        window.addEventListener('hashchange', () => {
            const hash = window.location.hash.substring(1);
            if (hash) {
                const targetSection = document.getElementById(hash + '-section');
                if (targetSection) {
                    // Hide all sections
                    sections.forEach(section => section.classList.add('hidden'));
                    // Show the section corresponding to the hash
                    targetSection.classList.remove('hidden');
                    // Update active nav item
                    navItems.forEach(nav => {
                        nav.classList.remove('active', 'text-white');
                        nav.classList.add('text-gray-300');
                        if (nav.getAttribute('href').substring(1) === hash) {
                            nav.classList.add('active', 'text-white');
                            nav.classList.remove('text-gray-300');
                        }
                    });
                    // Load Redis keys if the hash is 'redis'
                    if (hash === 'redis') {
                        loadAllRedisKeys();
                    }
                }
            } else {
                // If no hash, show overview section
                sections.forEach(section => section.classList.add('hidden'));
                document.getElementById('overview-section').classList.remove('hidden');
                navItems.forEach(nav => {
                    nav.classList.remove('active', 'text-white');
                    nav.classList.add('text-gray-300');
                    if (nav.getAttribute('href').substring(1) === 'overview') {
                        nav.classList.add('active', 'text-white');
                        nav.classList.remove('text-gray-300');
                    }
                });
            }
        });
    }

    async function loadOverviewData() {
        try {
            const response = await fetch('/cache/overview');
            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // console.log('Overview Data:', data);

            // Kiểm tra và cập nhật trạng thái Redis từ getRedisKeys
            try {
                const redisResponse = await fetch('/cache/redis/keys?search=');
                const redisData = await redisResponse.json();
                
                // Nếu thành công trong việc lấy keys, đặt trạng thái là Connected
                document.getElementById('redis-status').textContent = 'Connected';
                document.getElementById('redis-status').classList.remove('text-red-600');
                document.getElementById('redis-status').classList.add('text-green-600');
                
                // Cập nhật thông tin từ response keys
                if (redisData.overview) {
                    document.getElementById('memory-usage').textContent = redisData.overview.memory_usage || 'N/A';
                    
                    // Cập nhật các thông tin Redis chi tiết
                    document.getElementById('redis-total-keys').textContent = redisData.overview.total_keys || 0;
                    document.getElementById('redis-memory').textContent = redisData.overview.memory_usage || '0 B';
                    document.getElementById('redis-hit-rate').textContent = (redisData.overview.hit_rate || 0) + '%';
                    document.getElementById('redis-clients').textContent = redisData.overview.connected_clients || 0;
                }
                
            } catch (redisError) {
                console.error('Redis connection error:', redisError);
                // Nếu không thể lấy keys, hiển thị Disconnected
                document.getElementById('redis-status').textContent = 'Disconnected';
                document.getElementById('redis-status').classList.remove('text-green-600');
                document.getElementById('redis-status').classList.add('text-red-600');
                document.getElementById('memory-usage').textContent = 'N/A';
            }

            // Update file cache size (vẫn giữ nguyên)
            document.getElementById('file-cache-size').textContent = data.file.total_size || '0 B';

            // Update system cache status (vẫn giữ nguyên)
            updateSystemCacheStatus(data.system);
            
        } catch (error) {
            console.error('Error loading overview data:', error);
            showToast('Error loading overview data', 'error');
        }
    }

    async function loadFileData() {
        try {
            const response = await fetch('/cache/overview');
            const data = await response.json();

            document.getElementById('file-total-size').textContent = data.file.total_size || '0 B';
            document.getElementById('file-count').textContent = data.file.file_count || 0;
            document.getElementById('file-status').textContent = data.file.status || 'Unknown';
            document.getElementById('cache-path').textContent = data.file.cache_path || 'N/A';
            document.getElementById('cache-writable').textContent = data.file.writable ? 'Yes' : 'No';
        } catch (error) {
            console.error('Error loading file data:', error);
            showToast('Error loading file data', 'error');
        }
    }

    function updateSystemCacheStatus(systemData) {
        const container = document.getElementById('system-cache-status');
        container.innerHTML = '';

        const caches = [{
                name: 'Config',
                key: 'config_cached'
            },
            {
                name: 'Routes',
                key: 'routes_cached'
            },
            {
                name: 'Events',
                key: 'events_cached'
            },
            {
                name: 'Views',
                key: 'views_cached'
            }
        ];

        caches.forEach(cache => {
            const status = systemData[cache.key];
            const div = document.createElement('div');
            div.className = `flex items-center space-x-2 p-3 rounded-lg ${status ? 'bg-green-100' : 'bg-red-100'}`;
            div.innerHTML = `
            ${status
                    ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none"/>
                        <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`
                    : `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none"/>
                        <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`}
                <span class="text-sm font-medium ${status ? 'text-green-800' : 'text-red-800'}">${cache.name}</span>
            `;

            container.appendChild(div);
        });
    }

    // Hàm tìm kiếm ban đầu sẽ tải tất cả keys từ server
    async function loadAllRedisKeys() {
        try {
            showLoading();
            const response = await fetch(`/cache/redis/keys?search=`);
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Lưu lại tất cả keys
            allRedisKeys = data.keys || [];
            
            // Cập nhật thông tin tổng quan nếu có
            if (data.overview) {
                document.getElementById('redis-total-keys').textContent = data.overview.total_keys || 0;
                document.getElementById('redis-memory').textContent = data.overview.memory_usage || '0 B';
                document.getElementById('redis-hit-rate').textContent = (data.overview.hit_rate || 0) + '%';
                document.getElementById('redis-clients').textContent = data.overview.connected_clients || 0;
            }
            
            // Hiển thị tất cả keys ban đầu
            updateRedisKeysTable(allRedisKeys);
            showToast(`Loaded ${allRedisKeys.length} keys`);
        } catch (error) {
            console.error('Error loading Redis keys:', error);
            showToast('Error loading Redis keys', 'error');
        } finally {
            hideLoading();
        }
    }

    // Hàm tìm kiếm sẽ lọc từ dữ liệu đã có sẵn
    function searchRedisKeys() {
        const searchTerm = document.getElementById('key-search').value.toLowerCase();
        
        if (!allRedisKeys || allRedisKeys.length === 0) {
            showToast('No keys available. Please refresh the data.', 'error');
            return;
        }
        
        // Lọc keys theo chuỗi tìm kiếm
        const filteredKeys = searchTerm 
            ? allRedisKeys.filter(key => key.key.toLowerCase().includes(searchTerm))
            : allRedisKeys;
        
        // Cập nhật bảng
        updateRedisKeysTable(filteredKeys);
        
        // Hiển thị kết quả
        if (searchTerm) {
            showToast(`Found ${filteredKeys.length} matching keys`);
        }
    }

    function updateRedisKeysTable(keys) {
        const tbody = document.getElementById('redis-keys-table');
        tbody.innerHTML = '';

        if (!keys || keys.length === 0) {
            // Hiển thị thông báo khi không có keys
            const emptyRow = document.createElement('tr');
            emptyRow.className = 'border-b border-gray-200';
            emptyRow.innerHTML = `
                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No keys found</td>
            `;
            tbody.appendChild(emptyRow);
            return;
        }

        keys.forEach(key => {
            // Debug log để xem dữ liệu thực tế
            // console.log('Key data:', key);
            
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50';
            
            // Chuyển đổi TTL thành định dạng dễ đọc
            const formatTTL = (ttl) => {
                if (ttl === -1) return 'Never';
                if (ttl < 60) return `${ttl}s`;
                
                const hours = Math.floor(ttl / 3600);
                const minutes = Math.floor((ttl % 3600) / 60);
                const seconds = ttl % 60;
                
                if (hours > 0) {
                    return `${hours}h ${minutes}m ${seconds}s`;
                } else {
                    return `${minutes}m ${seconds}s`;
                }
            };
            
            // Chuyển đổi size từ byte sang đơn vị dễ đọc
            const formatSize = (sizeValue) => {
                // console.log('Size input:', sizeValue, 'Type:', typeof sizeValue);
                
                // Xử lý các trường hợp đặc biệt
                if (sizeValue === null || sizeValue === undefined) {
                    return 'N/A';
                }
                
                // Nếu đã là string formatted (như "1.2 KB")
                if (typeof sizeValue === 'string' && (sizeValue.includes('B') || sizeValue.includes('K') || sizeValue.includes('M'))) {
                    return sizeValue;
                }
                
                // Chuyển đổi thành số
                let bytes = parseFloat(sizeValue);
                
                if (isNaN(bytes) || bytes < 0) {
                    return sizeValue || 'N/A'; // Trả về giá trị gốc nếu không parse được
                }
                
                if (bytes === 0) return '0 B';
                if (bytes < 1024) return `${Math.round(bytes)} B`;
                
                const kb = bytes / 1024;
                if (kb < 1024) return `${kb.toFixed(1)} KB`;
                
                const mb = kb / 1024;
                if (mb < 1024) return `${mb.toFixed(1)} MB`;
                
                const gb = mb / 1024;
                return `${gb.toFixed(1)} GB`;
            };
            
            row.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-800 font-mono">${key.key || 'N/A'}</td>
                <td class="px-4 py-2 text-sm text-gray-600">${key.type || 'N/A'}</td>
                <td class="px-4 py-2 text-sm text-gray-600">${formatTTL(key.ttl)}</td>
                <td class="px-4 py-2 text-sm text-gray-600">${formatSize(key.size)}</td>
                <td class="px-4 py-2">
                    <button onclick="deleteRedisKey('${key.key}')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    async function deleteRedisKey(key) {
        if (!confirm(`Are you sure you want to delete the key: ${key}?`)) return;

        try {
            showLoading();
            const response = await fetch('/cache/redis/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    key: key
                })
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Xóa key khỏi mảng allRedisKeys để cập nhật UI ngay lập tức
            allRedisKeys = allRedisKeys.filter(item => item.key !== key);
            
            // Cập nhật lại bảng
            const searchTerm = document.getElementById('key-search').value.toLowerCase();
            const filteredKeys = searchTerm 
                ? allRedisKeys.filter(item => item.key.toLowerCase().includes(searchTerm))
                : allRedisKeys;
            
            updateRedisKeysTable(filteredKeys);

            // Cập nhật số lượng keys hiển thị trong thông tin tổng quan
            const redisKeysElement = document.getElementById('redis-total-keys');
            if (redisKeysElement) {
                const currentKeyCount = parseInt(redisKeysElement.textContent || '0');
                redisKeysElement.textContent = Math.max(0, currentKeyCount - 1);
            }

            showToast(data.message);
        } catch (error) {
            console.error('Error deleting Redis key:', error);
            showToast('Error deleting Redis key', 'error');
        } finally {
            hideLoading();
        }
    }

    async function flushRedis() {
        if (!confirm('Are you sure you want to flush all Redis keys? This action cannot be undone.')) return;

        try {
            showLoading();
            const response = await fetch('/cache/redis/flush', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            showToast(data.message);
            loadOverviewData();
            searchRedisKeys();

        } catch (error) {
            console.error('Error flushing Redis:', error);
            showToast('Error flushing Redis', 'error');
        } finally {
            hideLoading();
        }
    }

    async function clearFileCache() {
        if (!confirm('Are you sure you want to clear the file cache?')) return;

        try {
            showLoading();
            const response = await fetch('/cache/file/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            showToast(data.message);
            loadFileData();
            loadOverviewData();

        } catch (error) {
            console.error('Error clearing file cache:', error);
            showToast('Error clearing file cache', 'error');
        } finally {
            hideLoading();
        }
    }

    async function clearAllCache() {
        if (!confirm('Are you sure you want to clear all caches? This will clear application, config, route, view, and Redis caches.')) return;

        try {
            showLoading();
            const response = await fetch('/cache/clear-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            showToast(data.message);
            loadOverviewData();
            loadFileData();
            searchRedisKeys();

        } catch (error) {
            console.error('Error clearing all cache:', error);
            showToast('Error clearing all cache', 'error');
        } finally {
            hideLoading();
        }
    }

    async function optimizeCache() {
        try {
            showLoading();
            const response = await fetch('/cache/optimize', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }

            showToast(data.message);
            loadOverviewData();
        } catch (error) {
            console.error('Error optimizing cache:', error);
            showToast('Error optimizing cache', 'error');
        } finally {
            hideLoading();
        }
    }

    function refreshStats() {
        loadOverviewData();
        loadFileData();
        showToast('Stats refreshed');
    }

    function refreshFileStats() {
        loadFileData();
        showToast('File cache stats refreshed');
    }

    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        toastMessage.textContent = message;

        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} text-white`;

        toast.classList.remove('translate-x-full');

        setTimeout(() => {
            toast.classList.add('translate-x-full');
        }, 3000);
    }

    async function checkRedisStatus() {
        try {
            showLoading();
            const response = await fetch('/cache/redis/keys?search=');
            const data = await response.json();
            
            if (data.error) {
                document.getElementById('redis-status').textContent = 'Disconnected';
                document.getElementById('redis-status').classList.remove('text-green-600');
                document.getElementById('redis-status').classList.add('text-red-600');
                document.getElementById('memory-usage').textContent = 'N/A';
            } else {
                document.getElementById('redis-status').textContent = 'Connected';
                document.getElementById('redis-status').classList.remove('text-red-600');
                document.getElementById('redis-status').classList.add('text-green-600');
                
                if (data.overview) {
                    document.getElementById('memory-usage').textContent = data.overview.memory_usage || 'N/A';
                }
            }
            
            showToast('Redis status updated');
        } catch (error) {
            document.getElementById('redis-status').textContent = 'Disconnected';
            document.getElementById('redis-status').classList.remove('text-green-600');
            document.getElementById('redis-status').classList.add('text-red-600');
            document.getElementById('memory-usage').textContent = 'N/A';
            console.error('Error checking Redis status:', error);
            showToast('Redis connection failed', 'error');
        } finally {
            hideLoading();
        }
    }
</script>
@endsection