<header class="h-16 border-gray-200 border-b shadow-sm flex items-center justify-between px-6">

    <!-- LEFT -->
    <div class="flex items-center gap-3">
        <!-- MOBILE HAMBURGER -->
        <button
            @click="sidebarOpen = true"
            class="md:hidden p-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition">

            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- RIGHT SIDE -->
    <div class="flex items-center gap-4">
        @auth
        <span class="text-sm text-gray-600">
            {{ Auth::user()->name }}
        </span>

        <div class="w-9 h-9 bg-blue-600 text-white
                    rounded-full flex items-center justify-center
                    font-semibold uppercase font-serif">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="ml-2 px-4 py-2 text-sm font-serif
                       border-2 border-red-500 text-red-500
                       rounded-lg hover:bg-red-500 hover:text-white
                       transition active:scale-95">
                Logout
            </button>
        </form>
        @endauth
    </div>

</header>