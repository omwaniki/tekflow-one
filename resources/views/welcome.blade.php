<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tekflow One • Cloud Asset Intelligence</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }

        .hero-bg {
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, 0.12) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.12) 0%, transparent 50%);
        }

        .glow-blue {
            position: absolute;
            width: 620px;
            height: 620px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.22), transparent);
            filter: blur(150px);
            z-index: -1;
        }

        .glow-green {
            position: absolute;
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.20), transparent);
            filter: blur(150px);
            z-index: -1;
        }

        .feature-card {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px) scale(1.02);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 10px 25px rgba(59,130,246,0.08);
        }

        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(16px);
        }
    </style>
</head>

<body class="bg-[#020617] text-white overflow-x-hidden">

    <!-- NAV -->
    <nav class="border-b border-white/10 bg-[#020617]/80 backdrop-blur-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-5 flex items-center justify-between">
            <div class="flex items-center gap-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">T1</div>
                <span class="text-xl font-semibold">Tekflow One</span>
            </div>

            <div class="hidden md:flex items-center gap-x-8 text-sm text-gray-300">
                <a href="#" class="hover:text-white">Product</a>
                <a href="#" class="hover:text-white">Solutions</a>
                <a href="#" class="hover:text-white">For Campuses</a>
                <a href="#" class="hover:text-white">Pricing</a>
            </div>

            <div class="flex items-center gap-x-4">
                <a href="#" onclick="switchToLogin()" class="text-sm px-5 py-2 rounded-lg hover:bg-white/10">
                    Log in
                </a>
                <a href="#" onclick="switchToSignup()" class="text-sm bg-white text-[#020617] px-5 py-2 rounded-lg font-medium">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <div class="relative min-h-screen pt-24 flex items-center hero-bg px-6">

        <div class="glow-blue top-20 -left-48"></div>
        <div class="glow-green bottom-10 -right-52"></div>

        <div class="w-full max-w-7xl mx-auto grid lg:grid-cols-2 gap-24 items-center">

            <!-- LEFT -->
            <div class="max-w-2xl">

                <h1 class="text-4xl lg:text-5xl font-semibold leading-[1.1] tracking-tight mb-6">
                    Smart Asset Intelligence<br>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">
                        for Modern Teams
                    </span>
                </h1>

                <p class="text-lg text-gray-400 mb-12 max-w-lg">
                    Track, manage, and analyze assets across campuses with clarity,
                    control, and real-time insights.
                </p>

                <!-- FEATURE CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                    <div class="feature-card p-6 rounded-xl">
                        <div class="w-10 h-10 bg-blue-500/10 text-blue-400 rounded-lg flex items-center justify-center mb-4">📍</div>
                        <h3 class="text-base font-semibold mb-1">Centralized Tracking</h3>
                        <p class="text-sm text-gray-400">All assets across campuses in one unified system.</p>
                    </div>

                    <div class="feature-card p-6 rounded-xl">
                        <div class="w-10 h-10 bg-emerald-500/10 text-emerald-400 rounded-lg flex items-center justify-center mb-4">⚡</div>
                        <h3 class="text-base font-semibold mb-1">Real-time Insights</h3>
                        <p class="text-sm text-gray-400">Instant visibility into asset distribution.</p>
                    </div>

                    <div class="feature-card p-6 rounded-xl">
                        <div class="w-10 h-10 bg-purple-500/10 text-purple-400 rounded-lg flex items-center justify-center mb-4">🔐</div>
                        <h3 class="text-base font-semibold mb-1">Secure & Scalable</h3>
                        <p class="text-sm text-gray-400">Role-based access and enterprise scalability.</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="flex justify-center lg:justify-end">

                <div id="auth-card" class="auth-card w-full max-w-md rounded-2xl p-8 shadow-xl">

                    <!-- Tabs -->
                    <div class="flex border-b border-white/10 mb-8">
                        <button onclick="switchToLogin()" class="flex-1 pb-4 text-lg font-medium border-b-2 border-emerald-400">Log in</button>
                        <button onclick="switchToSignup()" class="flex-1 pb-4 text-lg text-gray-400">Sign up</button>
                    </div>

                    <!-- ERROR MESSAGES -->
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-sm text-red-300">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- LOGIN -->
                    <div id="login-form">
                        <form method="POST" action="{{ route('login') }}" class="space-y-5" onsubmit="handleLogin(event)">
                            @csrf

                            <input type="email" name="email" placeholder="Email"
                                class="w-full px-4 py-3 bg-[#0a0f1c] border border-white/10 rounded-lg focus:ring-2 focus:ring-blue-500">

                            <!-- PASSWORD WITH TOGGLE -->
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Password"
                                    class="w-full px-4 py-3 bg-[#0a0f1c] border border-white/10 rounded-lg focus:ring-2 focus:ring-blue-500">

                                <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-3 text-gray-400 hover:text-white">
                                    👁
                                </button>
                            </div>

                            <!-- REMEMBER + FORGOT -->
                            <div class="flex justify-between text-sm text-gray-400">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="remember"> Remember me
                                </label>
                                <a href="{{ route('password.request') }}" class="hover:text-white">
                                    Forgot password?
                                </a>
                            </div>

                            <!-- LOGIN BUTTON -->
                            <button id="login-btn" type="submit"
                                class="w-full py-3 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-lg font-medium flex justify-center items-center gap-2">
                                <span id="login-text">Sign in</span>
                                <span id="login-loader" class="hidden">⏳</span>
                            </button>

                            <!-- DIVIDER -->
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-px bg-white/10"></div>
                                <span class="text-xs text-gray-500">or continue with</span>
                                <div class="flex-1 h-px bg-white/10"></div>
                            </div>

                            <!-- GOOGLE -->
                            <a href="#"
                                class="flex justify-center items-center gap-3 py-3 border border-white/10 rounded-lg hover:bg-white/5">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                                <span class="text-sm text-gray-300">Continue with Google</span>
                            </a>

                        </form>
                    </div>

                    <!-- SIGNUP -->
                    <div id="signup-form" class="hidden">
                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf
                            <input type="text" name="name" placeholder="Full name" class="w-full px-4 py-3 bg-[#0a0f1c] border border-white/10 rounded-lg">
                            <input type="email" name="email" placeholder="Email" class="w-full px-4 py-3 bg-[#0a0f1c] border border-white/10 rounded-lg">
                            <input type="password" name="password" placeholder="Password" class="w-full px-4 py-3 bg-[#0a0f1c] border border-white/10 rounded-lg">
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-blue-500 rounded-lg font-medium">
                                Create account
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        function switchToLogin() {
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('signup-form').classList.add('hidden');
        }

        function switchToSignup() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('signup-form').classList.remove('hidden');
        }

        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function handleLogin(e) {
            document.getElementById('login-text').innerText = 'Signing in...';
            document.getElementById('login-loader').classList.remove('hidden');
        }

        window.onload = switchToLogin;
    </script>

</body>
</html>