<nav class="sticky top-0 z-50 bg-white">
    
    <!-- Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">

            <!-- LEFT -->
            <div class="flex items-center">
                <!-- Future: Tekflow One / Breadcrumbs -->
            </div>

            <!-- RIGHT -->
            <div class="flex items-center ml-auto">

                @auth
                <div class="relative group">

                    <!-- Trigger -->
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl cursor-pointer hover:bg-gray-100 transition">

                        <!-- Name -->
                        <div class="text-sm font-medium text-gray-700">
                            {{ Auth::user()->name }}
                        </div>

                        <!-- Avatar -->
                        <img 
                            src="{{ Auth::user()->avatar 
                                ? asset('storage/' . Auth::user()->avatar) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=111827&color=fff' }}"
                            class="w-8 h-8 rounded-full object-cover"
                        />

                    </div>

                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg 
                                opacity-0 invisible 
                                group-hover:opacity-100 group-hover:visible 
                                transform translate-y-2 group-hover:translate-y-0
                                transition-all duration-200 ease-out z-50">

                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-xl">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-xl">
                                Log Out
                            </button>
                        </form>

                    </div>

                </div>
                @endauth

                @guest
                <!-- Optional: show login button -->
                <a href="{{ route('login') }}"
                   class="text-sm text-gray-600 hover:text-black">
                    Login
                </a>
                @endguest

            </div>

        </div>
    </div>

</nav>