<!-- resources/views/layouts/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-slate-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-tight text-emerald-400">POS Admin</h1>
            </div>
            <nav class="flex-1 px-4 space-y-2">
                <a href="#" class="flex items-center py-3 px-4 rounded-lg bg-slate-800 text-emerald-400">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex items-center py-3 px-4 rounded-lg hover:bg-slate-800 transition">
                    <span>Orders</span>
                </a>
                <a href="{{ route('orders.create') }}" class="flex items-center py-3 px-4 rounded-lg hover:bg-slate-800 transition">
                    <span>New Order</span>
                </a>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <p class="text-xs text-slate-500 text-center">v1.2.0 - 2026 Edition</p>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b flex items-center justify-between px-8">
                <h2 class="text-xl font-semibold">@yield('title', 'System Overview')</h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 italic">Welcome, Operator</span>
                </div>
            </header>

            <!-- Scrollable Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-lg border border-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-100 p-4 text-blue-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-100 p-4 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>