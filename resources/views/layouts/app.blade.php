<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Tekflow One') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

@vite(['resources/css/app.css','resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
#sidebar.collapsed .sidebar-text { display:none; }
#sidebar.collapsed .sidebar-item { justify-content:center; }
#sidebar.collapsed .sidebar-icon { margin:0 auto; }
#sidebar.collapsed .sidebar-submenu { display:none !important; }

.sidebar-submenu a {
    display:flex;
    align-items:center;
    gap:10px;
}

.sidebar-dot {
    width:6px;
    height:6px;
    border-radius:999px;
    background-color:#6b7280;
}

.sidebar-submenu a.active .sidebar-dot {
    background-color:#ffffff;
}
</style>

<style>
    :root {
        --color-primary: {{ $primaryColor }};
        --color-primary-dark: {{ $primaryDark }};
        --color-primary-light: {{ $primaryLight }};
    }
</style>

</head>

<body class="font-sans antialiased bg-gray-100">

<div class="min-h-screen flex flex-col">

<nav class="sticky top-0 z-50 bg-white border-b shadow-sm">
<div class="flex items-center justify-between px-6 py-3">

<div class="flex items-center gap-4">
<button id="sidebarToggle" class="text-gray-600 text-xl">
<i class="fa-solid fa-bars"></i>
</button>

<span class="font-semibold text-lg">Tekflow One</span>
</div>

<div>@include('layouts.navigation')</div>

</div>
</nav>

<div id="mainContent" class="flex flex-1 ml-64 transition-all duration-300">

<aside id="sidebar" class="fixed top-12 left-0 h-[calc(100vh-3rem)] bg-gray-900 text-white w-64 transition-all duration-300 ease-in-out">

<nav class="mt-6 space-y-2">

<a href="/"
   class="sidebar-item {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
    <i class="fa-solid fa-chart-line sidebar-icon"></i>
    <span class="sidebar-text">Dashboard</span>
</a>

{{-- ================= ASSETS ================= --}}
@can('view assets')
<div class="mt-2">

    @php 
    $assetsOpen = request()->is('assets*') 
        || request()->is('assignments*') 
        || request()->is('movements*') 
    @endphp

    <button onclick="toggleAssets()"
        class="w-full flex items-center gap-2 px-4 py-2 sidebar-item text-left">

        <i class="fa-solid fa-laptop sidebar-icon"></i>
        <span class="sidebar-text flex-1">Assets</span>

        <i id="assetsArrow"
           class="fa-solid fa-chevron-down text-xs transition-transform {{ $assetsOpen ? 'rotate-180' : '' }}">
        </i>
    </button>

    <div id="assetsMenu" class="ml-6 mt-1 space-y-1 sidebar-submenu {{ $assetsOpen ? '' : 'hidden' }}">

        @php $active = request()->is('assets') @endphp
        <a href="{{ route('assets.index') }}"
        class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Asset Register
        </a>

        @php $active = request()->is('assignments*') @endphp
        <a href="{{ route('assignments.index') }}"
        class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Assignments
        </a>

        @php $active = request()->is('movements*') @endphp
        <a href="{{ route('movements.index') }}"
        class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Movements
        </a>

    </div>

</div>
@endcan

{{-- ================= AUDITS ================= --}}
@can('view audits')
<a href="{{ route('audits.index') }}"
   class="sidebar-item {{ request()->routeIs('audits.index') ? 'bg-gray-800 text-white' : '' }}">
    <i class="fa-solid fa-clipboard-check sidebar-icon"></i>
    <span class="sidebar-text">Audits</span>
</a>
@endcan

{{-- ================= AGENTS ================= --}}
@can('manage agents')
<a href="{{ route('agents.index') }}"
   class="sidebar-item {{ request()->is('agents*') ? 'bg-gray-800 text-white' : '' }}">
    <i class="fa-solid fa-user-gear sidebar-icon"></i>
    <span class="sidebar-text">Agents</span>
</a>
@endcan

<a href="#" class="sidebar-item">
    <i class="fa-solid fa-chart-pie sidebar-icon"></i>
    <span class="sidebar-text">Reports</span>
</a>

<hr class="border-gray-700 my-4">

{{-- ================= SETTINGS ================= --}}
@canany(['manage regions','manage campuses','manage asset statuses','manage roles'])
<div class="mt-2">

    @php 
    $settingsOpen = request()->is('regions*') 
        || request()->is('campuses*') 
        || request()->is('roles*') 
        || request()->is('settings/asset-statuses*')
        || request()->is('settings/appearance*') 
    @endphp

    <button onclick="toggleSettings()"
        class="w-full flex items-center gap-2 px-4 py-2 sidebar-item text-left">

        <i class="fa-solid fa-gear sidebar-icon"></i>
        <span class="sidebar-text flex-1">Settings</span>

        <i id="settingsArrow"
           class="fa-solid fa-chevron-down text-xs transition-transform {{ $settingsOpen ? 'rotate-180' : '' }}">
        </i>
    </button>

    <div id="settingsMenu" class="ml-6 mt-1 space-y-1 sidebar-submenu {{ $settingsOpen ? '' : 'hidden' }}">

        @php $active = request()->is('regions*') @endphp
        <a href="{{ route('regions.index') }}"
           class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Regions
        </a>

        @php $active = request()->is('campuses*') @endphp
        <a href="{{ route('campuses.index') }}"
           class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Campuses
        </a>

        @php $active = request()->is('settings/asset-statuses*') @endphp
        <a href="{{ route('asset-statuses.index') }}"
           class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Asset Statuses
        </a>

        {{-- Appearance Settings --}}
        @can('manage settings')
            @php $active = request()->is('settings/appearance*') @endphp
            <a href="{{ route('settings.appearance') }}"
            class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <span class="sidebar-dot"></span>
                Appearance
            </a>
        @endcan

        @php $active = request()->is('roles*') @endphp
        <a href="{{ route('roles.index') }}"
           class="px-4 py-2 rounded-lg text-sm {{ $active ? 'bg-gray-800 text-white active' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <span class="sidebar-dot"></span>
            Roles & Permissions
        </a>

    </div>

</div>
@endcanany

</nav>
</aside>

<main class="flex-1 min-w-0 overflow-y-auto p-6">
{{ $slot }}
</main>

</div>
</div>

<script>
const toggle = document.getElementById("sidebarToggle")
const sidebar = document.getElementById("sidebar")
const main = document.getElementById("mainContent")

toggle.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed")

    if (sidebar.classList.contains("collapsed")) {
        main.classList.replace("ml-64","ml-16")
        document.querySelectorAll('#assetsMenu,#settingsMenu,#auditsMenu').forEach(el=>el.classList.add('hidden'))
    } else {
        main.classList.replace("ml-16","ml-64")
    }
})

function toggleAssets(){
    const isActive = window.location.pathname.includes('assets') 
        || window.location.pathname.includes('assignments')
        || window.location.pathname.includes('movements');

    if (isActive) return;

    document.getElementById('assetsMenu').classList.toggle('hidden');
    document.getElementById('assetsArrow').classList.toggle('rotate-180');
}

function toggleSettings(){
    const path = window.location.pathname;

    const isActive = path.includes('regions') 
        || path.includes('campuses') 
        || path.includes('roles') 
        || path.includes('asset-statuses')
        || path.includes('appearance'); 

    if (isActive) return;

    document.getElementById('settingsMenu').classList.toggle('hidden');
    document.getElementById('settingsArrow').classList.toggle('rotate-180');
}
</script>

</body>
</html>