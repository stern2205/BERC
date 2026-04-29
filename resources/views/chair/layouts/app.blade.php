<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">


    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <script src="{{ asset('js/functions.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bsu-dark': '#213C71',
                        'brand-red': '#D32F2F',
                        'light-bg': '#F8F9FA'
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .nav-item-active { border-bottom: 3px solid #D32F2F; color: #213C71; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

        /* Custom styling for the tutorial popover to match BSU Theme */
        .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
        .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
        .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
        .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
        .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
        .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
        .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
        .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }    </style>

    @stack('head')
</head>
<body class="bg-light-bg min-h-screen flex flex-col">
    @php
        $isChairManagementRoute =
            request()->routeIs('approval')
            || request()->routeIs('pipeline.approval')
            || request()->routeIs('add-staff')
            || request()->routeIs('reports');
    @endphp

    <header class="bg-white sticky top-0 z-50 shadow-sm" x-data="{ isNavDrawerOpen: false, isProtocolRoute: {{ request()->routeIs('pipeline.*') && !request()->routeIs('pipeline.approval') ? 'true' : 'false' }}, protocolMenuOpen: {{ $isChairManagementRoute ? 'true' : 'false' }}, mobileProtocolOpen: false }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-3">
            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                <img src="{{ asset('logo/BERC.png') }}" alt="BSU Logo" class="h-10 sm:h-7 w-auto object-contain shrink-0">
                <div class="flex flex-col min-w-0">
                    <h1 class="text-[11px] sm:text-[15px] font-bold text-bsu-dark leading-tight tracking-tight uppercase truncate">
                        Batangas State University - TNEU
                    </h1>
                    <p class="text-[9px] sm:text-[10px] font-bold text-brand-red leading-tight uppercase tracking-widest mt-0.5">
                        Ethics Review Committee
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block" x-data="{ currentSystemTime: new Date() }" x-init="setInterval(() => currentSystemTime = new Date(), 1000)">
                    <div class="time text-xs font-extrabold text-bsu-dark uppercase tracking-wider">
                        <span x-text="currentSystemTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })"></span> |
                        <span x-text="currentSystemTime.toLocaleDateString('en-US', { weekday: 'long' })"></span>
                    </div>
                    <div class="date text-[10px] font-bold text-brand-red mt-[1px]" x-text="currentSystemTime.toLocaleDateString('en-GB')"></div>
                </div>

                <button @click="isNavDrawerOpen = !isNavDrawerOpen; if (!isNavDrawerOpen) { mobileProtocolOpen = false; }" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 bg-white gap-[5px]" aria-label="Toggle menu">
                    <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all" :class="isNavDrawerOpen ? 'rotate-45 translate-y-1.5' : ''"></span>
                    <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all" :class="isNavDrawerOpen ? 'opacity-0' : ''"></span>
                    <span class="block w-5 h-[2px] bg-bsu-dark rounded transition-all" :class="isNavDrawerOpen ? '-rotate-45 -translate-y-1.5' : ''"></span>
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100 bg-white hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">

                <div class="flex space-x-1">
                    <a href="{{ route('dashboard') }}" x-show="!isProtocolRoute" x-cloak @click="protocolMenuOpen = false"
                    class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                    {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4m-4 0a1 1 0 001-1v-4a1 1 0 00-1-1h-2a1 1 0 00-1 1v4a1 1 0 001 1h2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('chair.calendar') }}" x-show="!isProtocolRoute" x-cloak @click="protocolMenuOpen = false"
                    class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                    {{ request()->routeIs('calendar') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('calendar') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Calendar</span>
                    </a>

                    <a href="{{ route('dashboard') }}" x-show="isProtocolRoute" x-cloak @click="protocolMenuOpen = true"
                    class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px] border-transparent text-gray-500 hover:text-bsu-dark hover:border-brand-red border-r border-gray-100">
                        <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back To Dashboard</span>
                    </a>

                    <div class="flex items-stretch">
                        <button type="button" x-show="!protocolMenuOpen" x-cloak @click="protocolMenuOpen = true"
                                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                                {{ $isChairManagementRoute ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                            <svg class="w-4 h-4 {{ $isChairManagementRoute ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>CHAIR MANAGEMENT</span>
                            <svg class="w-3 h-3 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <div x-show="protocolMenuOpen" x-cloak
                            x-transition:enter="transition-all duration-200 ease-out"
                            x-transition:enter-start="opacity-0 max-w-0"
                            x-transition:enter-end="opacity-100 max-w-[1400px]"
                            x-transition:leave="transition-all duration-150 ease-in"
                            x-transition:leave-start="opacity-100 max-w-[1400px]"
                            x-transition:leave-end="opacity-0 max-w-0"
                            class="flex items-stretch overflow-hidden bg-white">

                            <button type="button" @click="protocolMenuOpen = false" class="flex items-center justify-center px-4 py-3.5 border-b-[3px] border-transparent hover:text-bsu-dark hover:border-brand-red transition-all text-gray-500" aria-label="Collapse protocol tabs">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <a href="{{ route('chair.approval') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                                <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">For Approval</span>
                            </a>
                            <a href="{{ route('chair.revision.decision') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                                <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">Resubmission Decision Letter</span>
                            </a>
                            <a href="{{ route('chair.add-staff') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('add-staff') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                                <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">Staff</span>
                            </a>
                            <a href="{{ route('chair.history') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('reports') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                                <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">History</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-6 border-l border-gray-100 pl-6 py-4">
                    <button type="button"
                            onclick="playCurrentPageTutorial()"
                            class="flex items-center gap-2 transition-all hover:-translate-y-0.5 text-gray-500 hover:text-bsu-dark">
                        <svg class="w-4 h-4 shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 1.823-2 3.272-2 1.933 0 3.5 1.343 3.5 3 0 1.305-.973 2.416-2.333 2.83-.727.221-1.167.874-1.167 1.67M12 18h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                        </svg>
                        <span>VIEW TUTORIAL</span>
                    </button>
                    <a href="{{ route('settings') }}" class="flex items-center gap-2 transition-all hover:-translate-y-0.5 {{ request()->routeIs('settings') ? 'text-bsu-dark font-black' : 'text-gray-500 hover:text-bsu-dark' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </a>

                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>

                    <button type="button" onclick="showLogoutModal()"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase transition-all duration-300 hover:-translate-y-1 hover:shadow-md active:scale-95 shadow-sm">
                        Sign Out
                    </button>
                </div>
            </div>
        </div>

        <div x-show="isNavDrawerOpen" x-collapse x-cloak class="md:hidden border-t border-gray-100 bg-white shadow-inner">
            <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-5 py-4 transition-colors {{ request()->routeIs('dashboard') ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'hover:bg-gray-50 hover:text-bsu-dark' }}">
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('chair.calendar') }}" class="flex items-center space-x-3 px-5 py-4 transition-colors {{ request()->routeIs('calendar') ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'hover:bg-gray-50 hover:text-bsu-dark' }}">
                    <span>Calendar</span>
                </a>

                <button type="button" @click="mobileProtocolOpen = !mobileProtocolOpen" class="w-full flex items-center justify-between px-5 py-4 focus:outline-none transition-colors {{ $isChairManagementRoute ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'text-gray-500 hover:bg-gray-50 hover:text-bsu-dark' }}">
                    <span>CHAIR MANAGEMENT</span>
                    <svg class="w-3 h-3 transition-transform duration-150" :class="mobileProtocolOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="mobileProtocolOpen" x-collapse x-cloak class="bg-gray-50 border-t border-b border-gray-100">
                    <a href="{{ route('chair.approval') }}" @click="isNavDrawerOpen = false; mobileProtocolOpen = false" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                        For Approval
                    </a>
                    <a href="{{ route('chair.revision.decision') }}" @click="isNavDrawerOpen = false; mobileProtocolOpen = false" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('reports') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                        Revisions
                    </a>
                    <a href="{{ route('chair.add-staff') }}" @click="isNavDrawerOpen = false; mobileProtocolOpen = false" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('add-staff') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                        Staff
                    </a>
                    <a href="{{ route('chair.history') }}" @click="isNavDrawerOpen = false; mobileProtocolOpen = false" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('reports') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                        History
                    </a>
                </div>

                <div class="flex items-center justify-between px-5 py-4 bg-gray-50">
                    <a href="{{ route('settings') }}" class="hover:text-bsu-dark transition-colors font-bold">Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase shadow-sm transition-all duration-300 hover:-translate-y-0.5 active:scale-95">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-6">
            @if (!request()->routeIs('dashboard'))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
                    <div class="absolute inset-0 z-0">
                        <img src="{{ asset('images/background.jpg') }}" alt="Campus Background" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-bsu-dark/70"></div>
                    </div>
                    <div class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6">
                        <div class="shrink-0">
                            <div class="bg-white/20 backdrop-blur-sm p-1 rounded-2xl border border-white/20 shadow-lg">
                                <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}" alt="Student Photo"
                                    class="w-16 h-16 sm:w-24 sm:h-24 object-cover bg-gray-300 rounded-xl">
                            </div>
                        </div>
                        <div class="flex-1 text-white text-center sm:text-left">
                            <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">{{ $user->role ?? 'Staff' }}</p>
                            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight">{{ $user->name ?? 'John Doe' }}</h2>
                            <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Account ID</p>
                                    <p class="text-xs font-bold tracking-wide">{{ $user->id ?? 'STF-001' }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Email Address</p>
                                    <p class="text-xs font-bold tracking-wide">{{ $user->email ?? 'john.doe@example.com' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} Batangas State University. All rights reserved.
            </p>
        </div>
    </footer>

    <div id="logout-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 text-brand-red w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>

                <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Sign Out?</h3>

                <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                    Are you sure you want to end your current session?<br>
                    You will need to log in again to access your dashboard.
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" onclick="hideLogoutModal()"
                        class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                        Cancel
                    </button>

                    <button type="button" onclick="confirmLogout()"
                        class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">
                        Yes, Sign Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function playCurrentPageTutorial() {
            if (typeof window.startPageTutorial === 'function') {
                window.startPageTutorial(true);
            } else {
                alert('No tutorial is available for this page yet.');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>

