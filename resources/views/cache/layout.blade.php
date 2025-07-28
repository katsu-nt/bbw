<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cache Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkYellow: '#FFA500',
                        primary: '#1f2937',
                        secondary: '#374151'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
    <div class="w-full min-h-screen grid grid-cols-12">
        <!-- Sidebar -->
        <div class="col-span-2 bg-black h-screen sticky top-0 p-6">
            <div class="text-center mb-8">
                <h1 class="text-darkYellow font-bold text-lg mb-2">CACHE MANAGEMENT</h1>
                <div class="w-full h-0.5 bg-darkYellow rounded"></div>
            </div>

            <nav class="space-y-4">
                <a href="#overview" class="nav-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-gray-800 transition-colors">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Overview</span>
                </a>
                <a href="#redis" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                    <i data-lucide="database" class="w-5 h-5"></i>
                    <span>Redis Cache</span>
                </a>
                <a href="#file" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                    <i data-lucide="file" class="w-5 h-5"></i>
                    <span>File Cache</span>
                </a>
            </nav>
        </div>

        @yield('content')
    </div>
</body>

</html>