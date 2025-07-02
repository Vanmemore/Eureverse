<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eureverse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    
    <script src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js" defer></script>

    <!-- AlpineJS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('replyingTo', null);
            Alpine.store('editingComment', null);
        });
    </script>

    {{-- <style>[x-cloak] { display: none !important; }</style> --}}
    @stack('scripts')
</head>

</head>
<body class="bg-[#343A40] text-white min-h-screen">

    <!-- Header -->
    <header class="fixed top-0 left-0 w-full flex justify-between items-center px-6 py-4 bg-gray-800 bg-opacity-80 z-50 backdrop-blur-md">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold">EUREVERSE</h1>
        </div>
        <div class="absolute left-1/2 transform -translate-x-1/2">
            <span class="text-white text-lg font-medium">@yield('page_title', 'Home')</span>
        </div>

        @guest
            <a href="{{ route('login') }}" class="bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-200 transition">
                Log in
            </a>
        @else
            <div x-data="{ open: false }" class="relative">
                <div class="flex items-center space-x-3 cursor-pointer" @click="open = !open">
                    <div class="text-right">
                        <p class="text-sm font-bold">{{ Auth::user()->username }}</p>
                        <p class="text-xs text-gray-300">{{ Auth::user()->name }}</p>
                    </div>
                    <img 
                        src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('img/default.png') }}" 
                        class="w-10 h-10 rounded-full border-2 border-white" 
                        alt="profile">
                </div>

                <!-- Dropdown Mini Window -->
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white text-black rounded shadow-lg z-50 py-2">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">👤 Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">🚪 Logout</button>
                    </form>
                </div>
            </div>
        @endguest
    </header>

    <!-- Wrapper -->
    <div class="flex pt-20">

        <!-- Sidebar Kiri -->
        <aside class="w-20 fixed top-16 left-0 h-[calc(100vh-4rem)] bg-[#212529] bg-opacity-50 backdrop-blur-md flex flex-col justify-center items-center space-y-8 z-40">
            <a href="{{ route('home') }}" class="text-2xl hover:text-blue-400" title="Home">🏠</a>
            <a href="{{ route('search') }}" class="text-2xl hover:text-blue-400 {{ request()->routeIs('search') ? 'text-blue-400' : '' }}" title="Search">🔍</a>
            {{--  <a href="#" class="text-2xl hover:text-blue-400" title="Activity">📈</a>--}}
            <a href="{{ Auth::check() ? route('profile') : route('login') }}" class="text-2xl hover:text-blue-400" title="Profile">👤</a>

        </aside>



        <!-- Sidebar Kanan -->
        <aside class="w-72 fixed top-16 right-0 h-[calc(100vh-4rem)] bg-[#212529] bg-opacity-50 backdrop-blur-md z-40 overflow-y-auto p-4">
            @auth
                <h3 class="font-semibold mb-2 text-sm text-gray-300">Friends</h3>
                <div class="text-sm mb-2 text-green-400">Online - {{ count($onlineFriends ?? []) }}</div>
                <ul class="mb-4 space-y-2">
                    @foreach ($onlineFriends ?? [] as $friend)
                        <li class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-white"></div>
                            <span>{{ $friend->username }}</span>
                            <div class="w-2 h-2 rounded-full bg-green-400 ml-auto"></div>
                        </li>
                    @endforeach
                </ul>

                <div class="text-sm mb-2 text-gray-400">Offline - {{ count($offlineFriends ?? []) }}</div>
                <ul class="space-y-2">
                    @foreach ($offlineFriends ?? [] as $friend)
                        <li class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-white"></div>
                            <span>{{ $friend->username }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">
                    <input type="text" placeholder="Search Friends.." class="w-full bg-transparent border border-gray-500 px-3 py-2 rounded text-sm">
                </div>
            @endauth
        </aside>


        <!-- Main Content -->
        <main class="flex-1 mx-[18rem] px-6 py-6 space-y-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
