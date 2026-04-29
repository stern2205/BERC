<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee - Application History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/functions.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
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
        body { font-family: 'Inter', sans-serif; background-color: #F8F9FA; }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

        #mobile-menu { transition: transform .3s ease, opacity .3s ease; }
        #mobile-menu.hidden-anim { transform:translateY(-8px); opacity:0; pointer-events:none; }
        #mobile-menu.open  { transform:translateY(0); opacity:1; pointer-events:auto; }

        /* ── shared modal base ── */
        .modal-overlay { transition: opacity .2s ease; }
        .modal-overlay.hidden  { opacity:0; pointer-events:none; }
        .modal-overlay.visible { opacity:1; pointer-events:all; }
        .modal-card { transition: transform .2s ease, opacity .2s ease; }
        .modal-overlay.hidden  .modal-card { transform:scale(.96); opacity:0; }
        .modal-overlay.visible .modal-card { transform:scale(1);   opacity:1; }

        /* doc row */
        .doc-item:hover .doc-icon-wrap { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.10); color:#213C71; }

        /* form inputs */
        .form-input {
            width:100%; border:1.5px solid #E5E7EB; border-radius:.5rem;
            padding:.55rem .75rem; font-size:.8rem; font-weight:600; color:#213C71;
            outline:none; transition:border-color .15s;
        }
        .form-input:focus { border-color:#213C71; }
        .form-label { font-size:.65rem; font-weight:900; text-transform:uppercase; letter-spacing:.07em; color:#9CA3AF; margin-bottom:.3rem; display:block; }
        .form-section-title { font-size:.7rem; font-weight:900; text-transform:uppercase; letter-spacing:.1em; color:#213C71; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:2px solid #E5E7EB; }

        /* upload zone */
        .upload-zone { border:2px dashed #D1D5DB; border-radius:.75rem; padding:1.25rem; text-align:center; cursor:pointer; transition:border-color .15s, background .15s; }
        .upload-zone:hover { border-color:#213C71; background:#F0F4FF; }

        /* Hide scrollbar while keeping functionality */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* scrollbar thin */
        .thin-scroll::-webkit-scrollbar { width:4px; }
        .thin-scroll::-webkit-scrollbar-track { background:transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background:#D1D5DB; border-radius:99px; }

        /* Dynamic Row Utility Classes */
        .dynamic-icon { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; margin-right: 16px; flex-shrink: 0; }
        .dynamic-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; }
        .dynamic-dot { width: 6px; height: 6px; border-radius: 50%; }

        /* Styles for PDF Generation */
        .pdf-content {
            width: 8.5in;
            padding: 0.5in;
            background: white;
            color: black;
            font-family: 'Times New Roman', serif;
        }
        .pdf-content table {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 11px;
        }
        .pdf-content th, .pdf-content td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .page-break { page-break-after: always; }
        .no-print { display: none !important; }

        /* Custom styling for the tutorial popover to match BSU Theme */
        .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
        .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
        .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
        .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
        .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
        .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
        .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
        .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
    </style>
</head>
<body>

@php
    $isChairManagementRoute =
        request()->routeIs('approval')
        || request()->routeIs('pipeline.approval')
        || request()->routeIs('add-staff')
        || request()->routeIs('reports');

    $isProtocolRoute = request()->routeIs('pipeline.*') && !request()->routeIs('pipeline.approval');
@endphp

<header class="bg-white sticky top-0 z-50 shadow-sm" id="main-header">

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
            <div class="date-block hidden sm:block text-right">
                <div class="time text-[11px] font-bold text-bsu-dark uppercase" id="clock-m">--:-- -- | -------</div>
                <div class="date text-[10px] font-bold text-brand-red" id="date-m">--/--/----</div>
            </div>

            <button id="hamburger-btn" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 gap-1.5 shrink-0" aria-label="Toggle menu">
                <span class="block w-5 h-[2.5px] bg-bsu-dark rounded transition-all duration-300" id="hb1"></span>
                <span class="block w-5 h-[2.5px] bg-bsu-dark rounded transition-all duration-300" id="hb2"></span>
                <span class="block w-5 h-[2.5px] bg-bsu-dark rounded transition-all duration-300" id="hb3"></span>
            </button>
        </div>
    </div>

    <div class="border-t border-b border-gray-200 bg-[#FCFCFC] hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">

            <div class="flex space-x-1">

                <div id="desktop-dash-link" class="{{ $isChairManagementRoute ? 'hidden' : 'flex space-x-1' }}">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px] {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('chair.calendar') }}" class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px] {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('calendar') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Calendar</span>
                    </a>
                </div>

                <div class="flex items-stretch">
                    <button type="button" id="desktop-chair-btn"
                            class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                            {{ $isChairManagementRoute ? 'hidden' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                        <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>CHAIR MANAGEMENT</span>
                        <svg class="w-3 h-3 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <div id="desktop-chair-menu" class="items-stretch overflow-hidden bg-white {{ $isChairManagementRoute ? 'flex' : 'hidden' }}">
                        <button type="button" id="desktop-chair-close" class="flex items-center justify-center px-4 py-3.5 border-b-[3px] border-transparent hover:text-bsu-dark hover:border-brand-red transition-all text-gray-500" aria-label="Collapse protocol tabs">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>

                        <a href="{{ route('chair.approval') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                            <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">For Approval</span>
                        </a>
                        <a href="{{ route('chair.revision.decision') }}" class="flex items-center px-5 py-3.5 whitespace-nowrap transition border-b-[3px] {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red hover:bg-gray-50' }}">
                            <span class="text-[11px] font-bold uppercase tracking-wider leading-tight">Resubmission Decisions</span>
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

            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
                <button type="button"
                        onclick="startHistoryManualTutorial()"
                        class="flex items-center gap-2 text-gray-500 hover:text-bsu-dark transition-all hover:-translate-y-0.5">

                    <svg class="w-4 h-4 text-brand-red shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
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

                <button type="button" onclick="showLogoutModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase transition-all duration-300 hover:-translate-y-1 hover:shadow-md active:scale-95 shadow-sm">
                    Sign Out
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-[#FCFCFC]">
        <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">

            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-5 py-3.5 transition-colors {{ request()->routeIs('dashboard') ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'hover:bg-gray-50 hover:text-bsu-dark' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-5 py-3.5 transition-colors {{ request()->routeIs('dashboard') ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'hover:bg-gray-50 hover:text-bsu-dark' }}">
                <span>Calendar</span>
            </a>

            <button type="button" id="mobile-chair-btn" class="w-full flex items-center justify-between px-5 py-3.5 focus:outline-none transition-colors {{ $isChairManagementRoute ? 'text-bsu-dark bg-gray-50 border-l-4 border-brand-red' : 'text-gray-500 hover:bg-gray-50 hover:text-bsu-dark' }}">
                <span>CHAIR MANAGEMENT</span>
                <svg id="mobile-chair-icon" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="mobile-chair-menu" class="hidden bg-gray-50 border-t border-b border-gray-100">
                <a href="{{ route('chair.approval') }}" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('approval') || request()->routeIs('pipeline.approval') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                    For Approval
                </a>
                <a href="{{ route('chair.revision.decision') }}" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('reports') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                    Resubmission Decisions
                </a>
                <a href="{{ route('chair.add-staff') }}" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('add-staff') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                    Staff
                </a>
                <a href="{{ route('chair.history') }}" class="block pl-9 pr-5 py-3 text-[10px] font-bold transition-colors {{ request()->routeIs('reports') ? 'text-bsu-dark bg-white border-l-4 border-brand-red' : 'hover:text-bsu-dark hover:bg-gray-100' }}">
                    History
                </a>
            </div>

            <div class="flex items-center justify-between px-5 py-3.5">
                <a href="{{ route('settings') }}" class="hover:text-bsu-dark transition-colors">Settings</a>
                <button type="button" onclick="showLogoutModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase">
                    Sign Out
                </button>
            </div>

        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-bsu-dark/70"></div>
        </div>
        <div class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6">
            <div class="shrink-0">
                <div class="bg-white/20 backdrop-blur-sm p-1 rounded-2xl border border-white/20 shadow-lg">
                    <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}" alt="Student Photo" class="w-16 h-16 sm:w-24 sm:h-24 object-cover bg-gray-300 rounded-xl">
                </div>
            </div>
            <div class="flex-1 text-white text-center sm:text-left">
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">{{ ucfirst($user->role ?? 'Researcher') }}</p>
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight">{{ $user->name ?? 'John Doe' }}</h2>
                <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Account ID</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->id ?? '12345' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Email Address</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->email ?? 'john@example.com' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="main">
        <div class="search-filter-bar">
            <div class="search-wrap">
                <span class="search-icon"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg></span>
                <input type="text" id="search-input" class="search-input" placeholder="Search title or record (Press Enter)..." value="{{ request('search') }}" autocomplete="off">
                <button class="search-clear {{ request('search') ? 'visible' : '' }}" id="search-clear" title="Clear search">&times;</button>
            </div>
            <div class="filter-wrap">
                <button class="filter-btn" id="filter-btn" data-active="{{ request('status', 'all') }}">
                    <span id="filter-label">{{ request('status') && request('status') !== 'all' ? ucfirst(request('status')) : 'All Status' }}</span>
                    <svg class="filter-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="filter-dropdown" id="filter-dropdown">
                    <div class="filter-dropdown-header">Filter by Status</div>
                    <button class="filter-option {{ request('status', 'all') === 'all' ? 'selected' : '' }}" data-value="all">All Applications <span class="opt-count">{{ $counts['all'] ?? 0 }}</span></button>
                    <button class="filter-option {{ request('status') === 'completed' ? 'selected' : '' }}" data-value="completed">Completed <span class="opt-count">{{ $counts['completed'] ?? 0 }}</span></button>
                    <button class="filter-option {{ request('status') === 'rejected' ? 'selected' : '' }}" data-value="rejected">Rejected <span class="opt-count">{{ $counts['rejected'] ?? 0 }}</span></button>
                </div>
            </div>
            @if(request('search') || (request('status') && request('status') !== 'all'))
            <div class="results-bar flex" id="results-bar" style="width:100%;">
                <span class="results-text" id="results-text">
                    Showing results
                    @if(request('search')) matching <strong>"{{ request('search') }}"</strong> @endif
                    @if(request('status') && request('status') !== 'all') in <strong>{{ ucfirst(request('status')) }}</strong> @endif
                </span>
                <button class="clear-filters-btn" id="clear-all-btn">Clear All</button>
            </div>
            @endif
        </div>

        <div class="app-card" id="tour-history-list">
            <div class="card-header border-b border-gray-100 flex justify-between items-center bg-white p-4">
                <div class="card-tab flex items-center gap-2 text-sm font-bold text-bsu-dark uppercase tracking-wide">
                    <svg width="16" height="16" fill="none" stroke="#D32F2F" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Application History
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rows:</span>
                    <select id="per-page-select" class="form-input py-1 text-[11px] font-bold w-16 text-center cursor-pointer">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </div>
            </div>

            <div class="card-body" id="applications-container">
                <div class="empty-state" id="empty-state" style="display: none;">
                    <div class="empty-icon"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="empty-title">No applications found</div>
                    <div class="empty-sub" id="empty-sub">Try adjusting your search or filter.</div>
                </div>
            </div>
        </div>

        @if($applications->hasPages())
        <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $applications->links('pagination::tailwind') }}
        </div>
        @endif
    </main>

</div>

<div id="detail-modal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) hideModal('detail-modal')">
    <div class="modal-card bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="bg-bsu-dark px-6 py-4 flex items-start justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <p id="m-type-label" class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-1">Application Hub</p>
                <h3 id="m-title"  class="text-white font-black text-[15px] leading-tight"></h3>
                <p id="m-record" class="text-blue-200 text-[11px] font-semibold mt-1"></p>
            </div>
            <button onclick="hideModal('detail-modal')" class="text-white/60 hover:text-white transition-colors mt-0.5 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto thin-scroll flex-1 p-6 space-y-5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-2">Application Progress</p>
            <div class="relative group">
                <button type="button" onclick="scrollProgress('left', 'progress-scroll-container')" class="absolute left-0 top-6 -translate-y-1/2 z-20 bg-white shadow-md border border-gray-200 text-gray-600 p-1 rounded-full hover:bg-bsu-dark hover:text-white transition-all opacity-0 group-hover:opacity-100 -ml-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                <button type="button" onclick="scrollProgress('right', 'progress-scroll-container')" class="absolute right-0 top-6 -translate-y-1/2 z-20 bg-white shadow-md border border-gray-200 text-gray-600 p-1 rounded-full hover:bg-bsu-dark hover:text-white transition-all opacity-0 group-hover:opacity-100 -mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>

                <div id="progress-scroll-container" class="w-full overflow-x-auto pb-1 hide-scrollbar scroll-smooth">
                    <div id="tour-modal-progress" class="relative grid grid-cols-7 min-w-[840px] w-full pt-2">
                        <div class="absolute top-6 left-[7.14%] right-[7.14%] h-0.5 bg-gray-200 z-0">
                            <div id="m-progress-fill" class="h-full bg-green-500 transition-all duration-700 rounded-full"></div>
                        </div>
                        <div id="m-step-1" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-2" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-3" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-4" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-5" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-6" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                        <div id="m-step-7" class="relative z-10 flex flex-col items-center gap-1 text-center px-2 whitespace-normal break-words scroll-ml-6"></div>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-100"></div>

            <div id="m-versions-container" class="hidden">
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-3">Submission Versions</p>
                <div id="m-version-list" class="space-y-2 mb-6"></div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="h-px bg-gray-100 flex-1"></span>
                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Initial Application History (V0)</p>
                    <span class="h-px bg-gray-100 flex-1"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div class="col-span-2 sm:col-span-3 pb-2 border-b border-gray-200/60 mb-1">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status</p>
                    <span id="m-status"></span>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Date Submitted</p>
                    <p id="m-date" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
                <div>
                    <p id="m-reviewer-label" class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Assigned Reviewer</p>
                    <p id="m-reviewer" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Type of Study</p>
                    <p id="m-study-type" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
            </div>

            <div id="tour-modal-docs" class="docs-section mt-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Submitted Documents</p>
                    <span id="m-doc-count" class="text-[9px] font-black text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full uppercase tracking-wider"></span>
                </div>
                <div id="m-documents" class="space-y-2"></div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0 bg-white">
            <button onclick="hideModal('detail-modal')" class="text-[11px] font-black uppercase tracking-wider text-gray-500 hover:text-bsu-dark border border-gray-200 px-5 py-2.5 rounded-lg transition-colors">Close</button>
            <button onclick="downloadAllDocs()" class="text-[11px] font-black uppercase tracking-wider bg-bsu-dark hover:bg-blue-900 text-white px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/></svg>
                Download All
            </button>
        </div>
    </div>
</div>

<div id="resub-detail-modal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) hideModal('resub-detail-modal')">
    <div class="modal-card bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="bg-bsu-dark px-6 py-4 flex items-start justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <p class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-1">Version Details</p>
                <h3 id="rd-title"  class="text-white font-black text-[15px] leading-tight"></h3>
                <p id="rd-record" class="text-blue-200 text-[11px] font-semibold mt-1"></p>
            </div>
            <button onclick="hideModal('resub-detail-modal')" class="text-white/60 hover:text-white transition-colors mt-0.5 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto thin-scroll flex-1 p-6 space-y-5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-5">Version Progress</p>
            <div class="relative flex items-start justify-between px-1">
                <div class="absolute top-4 left-[8%] right-[8%] h-0.5 bg-gray-200 z-0">
                    <div id="rd-progress-fill" class="h-full bg-orange-500 transition-all duration-700 rounded-full"></div>
                </div>
                <div id="rd-step-1" class="relative z-10 flex flex-col items-center gap-2" style="width:20%"></div>
                <div id="rd-step-2" class="relative z-10 flex flex-col items-center gap-2" style="width:20%"></div>
                <div id="rd-step-3" class="relative z-10 flex flex-col items-center gap-2" style="width:20%"></div>
                <div id="rd-step-4" class="relative z-10 flex flex-col items-center gap-2" style="width:20%"></div>
                <div id="rd-step-5" class="relative z-10 flex flex-col items-center gap-2" style="width:20%"></div>
            </div>

            <div class="h-px bg-gray-100"></div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status</p>
                    <span id="rd-status" class="inline-flex mt-1 text-[10px] font-black uppercase px-2 py-0.5 rounded-full border tracking-wider"></span>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Date Resubmitted</p>
                    <p id="rd-date" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
                <div>
                    <p id="rd-reviewer-label" class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Reviewer</p>
                    <p id="rd-reviewer" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Type of Study</p>
                    <p id="rd-study-type" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Version Files</p>
                    <span id="rd-doc-count" class="text-[9px] font-black text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full uppercase tracking-wider"></span>
                </div>
                <div id="rd-documents" class="space-y-2"></div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between shrink-0 bg-white">
            <button id="rd-back-btn" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-bsu-dark transition-colors px-2 py-2 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Hub</span>
            </button>

            <div class="flex items-center gap-3">
                <button onclick="hideModal('resub-detail-modal')" class="text-[11px] font-black uppercase tracking-wider text-gray-500 hover:text-bsu-dark border border-gray-200 px-5 py-2.5 rounded-lg transition-colors">Close</button>
                <button onclick="downloadResubDocs()" class="text-[11px] font-black uppercase tracking-wider bg-bsu-dark hover:bg-blue-900 text-white px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/></svg>
                    Download
                </button>
            </div>
        </div>
    </div>
</div>

<div id="logout-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
        <div class="p-6 text-center">
            <svg class="mx-auto mb-4 text-brand-red w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Sign Out?</h3>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">Are you sure you want to end your current session?<br>You will need to log in again to access your dashboard.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="hideLogoutModal()" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                <button type="button" onclick="confirmLogout()" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">Yes, Sign Out</button>
            </div>
        </div>
    </div>
</div>

<script>
// ── Clock ──
function updateClock() {
    var now  = new Date();
    var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var h    = now.getHours(), m = now.getMinutes();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    var timeStr = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm + ' | ' + days[now.getDay()].toUpperCase();
    var dd = (now.getDate() < 10 ? '0' : '') + now.getDate();
    var mm = (now.getMonth() < 9 ? '0' : '') + (now.getMonth() + 1);
    var dateStr = dd + '/' + mm + '/' + now.getFullYear();

    if(document.getElementById('clock')) document.getElementById('clock').textContent = timeStr;
    if(document.getElementById('datestamp')) document.getElementById('datestamp').textContent = dateStr;
    if(document.getElementById('clock-m')) document.getElementById('clock-m').textContent = timeStr;
    if(document.getElementById('date-m')) document.getElementById('date-m').textContent = dateStr;
}
updateClock();
setInterval(updateClock, 1000);

// ── Hamburger ──
document.getElementById('hamburger-btn').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('open');
});

// ── Dynamic Data Injection (Server-Side Paginated) ──
const APPLICATIONS = @json($formattedApps);
const REVISION_HISTORY = @json($revisionHistory);

const APP_STEPS = [
    { id: 1, label: 'Initial\nSubmission' }, { id: 2, label: 'Documents\nChecking' }, { id: 3, label: 'Classification/\nAssignment' },
    { id: 4, label: 'Under\nReview' }, { id: 5, label: 'Processing\nAssessment\nForms' }, { id: 6, label: 'Processing\nDecision' }, { id: 7, label: 'Final\nDecision' }
];

const RESUB_STEPS = [
    { id:1, label:'Resubmission\nReceived'  }, { id:2, label:'Under\nReview'     }, { id:3, label:'Processing\nAssessment\nForms'            },
    { id:4, label:'Decision\nLetter'        }, { id:5, label:'Completed'        },
];

const APP_REVIEWER_LABEL = { 2: 'Secretarial Staff', 3: 'Secretariat', 4: 'Assigned Reviewer', 5: 'Assigned Reviewer', 6: 'Chair', 7: 'Assigned Reviewer', 8: 'Assigned Reviewer' };

const STATUS_UI = {
    'Approved': { bg: '#dcfce7', text: '#166534', border: '#86efac', icon: '#22c55e', badgeClass: 'badge-approved' },
    'Disapproved': { bg: '#fee2e2', text: '#991b1b', border: '#fca5a5', icon: '#ef4444', badgeClass: 'badge-revision' },
    'Rejected': { bg: '#fee2e2', text: '#991b1b', border: '#fca5a5', icon: '#ef4444', badgeClass: 'badge-revision' },
    'default': { bg: '#f3f4f6', text: '#374151', border: '#e5e7eb', icon: '#6b7280', badgeClass: 'badge-review' }
};

// ── Server-Side URL Parameter Helper ──
function updateUrlParams(key, value) {
    const url = new URL(window.location.href);
    if (value) { url.searchParams.set(key, value); }
    else { url.searchParams.delete(key); }

    if (key !== 'page') url.searchParams.delete('page'); // Reset to page 1 on new filter
    window.location.href = url.toString();
}

// ── Icon Helpers ──
function fileIcon(type) {
    if (type==='pdf') return `<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 14.5h1.2c.7 0 1.3.6 1.3 1.3v.5c0 .7-.6 1.2-1.3 1.2H9.5v1H8.5v-4zm1 2h.2c.2 0 .3-.1.3-.3v-.4c0-.2-.1-.3-.3-.3H9.5v1zM13 14.5h1.5c.5 0 1 .4 1 1v2c0 .6-.5 1-1 1H13v-4zm1 3h.4c.1 0 .1 0 .1-.1v-1.8c0-.1 0-.1-.1-.1H14v2zm3-3h2v1h-1v-4z"/></svg>`;
    if (type==='docx' || type==='doc') return `<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM7 13h10v1H7zm0 2h10v1H7zm0 2h7v1H7z"/></svg>`;
    return `<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`;
}

function docRow(doc) {
    const bg = doc.type === 'pdf' ? 'bg-red-50 border-red-100' : (doc.type === 'docx' ? 'bg-blue-50 border-blue-100' : 'bg-gray-50 border-gray-100');
    const badge = doc.type === 'pdf' ? 'text-red-500 bg-red-100' : (doc.type === 'docx' ? 'text-blue-600 bg-blue-100' : 'text-gray-500 bg-gray-100');

    return `
    <a href="${doc.file}" target="_blank" class="doc-item flex items-center gap-3 p-3 border ${bg} rounded-xl cursor-pointer hover:shadow-md active:scale-[0.99] transition-all">
        <div class="shrink-0 w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center shadow-sm">
            ${fileIcon(doc.type)}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[12px] font-bold text-bsu-dark truncate">${doc.name}</p>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="text-[8px] font-black uppercase tracking-wider ${badge} px-1.5 py-0.5 rounded">${doc.type.toUpperCase()}</span>
                <span class="text-[10px] text-gray-400 font-semibold">${doc.size}</span>
            </div>
        </div>
        <div class="doc-icon-wrap shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-bsu-dark transition-all">
            <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/></svg>
        </div>
    </a>`;
}

function highlightText(text, query) {
    if (!query) return text;
    var re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
    return text.replace(re, '<mark>$1</mark>');
}

// ── Render Dynamic Cards ──
function renderApplications() {
    const container = document.getElementById('applications-container');
    const emptyStateHTML = document.getElementById('empty-state');
    const currentSearch = new URLSearchParams(window.location.search).get('search') || '';

    if (APPLICATIONS.length === 0) {
        emptyStateHTML.style.display = 'flex';
        emptyStateHTML.classList.add('visible');
        return;
    } else {
        emptyStateHTML.style.display = 'none';
        emptyStateHTML.classList.remove('visible');
    }

    let html = '';

    APPLICATIONS.forEach((app, idx) => {
        const ui = STATUS_UI[app.status] || STATUS_UI['default'];
        let displayTitle = highlightText(app.title, currentSearch);
        let displayRecord = highlightText('Record: ' + app.record, currentSearch);

        html += `
        <div class="app-row hover:bg-gray-50 border-b border-gray-100 p-3 transition-colors cursor-pointer flex items-center" onclick="openAppDetail(${idx})">
            <div class="dynamic-icon" style="color: ${ui.icon}; background: ${ui.bg}; border: 1px solid ${ui.border};">
                ${app.status === 'Approved'
                    ? '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                    : '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                }
            </div>
            <div class="app-row-info" style="flex:1;">
                <div class="app-row-title" style="font-weight:700; font-size:14px; color:#1f2937; margin-bottom:2px;">${displayTitle}</div>
                <div class="app-row-record" style="font-size:11px; color:#6b7280; font-weight:600;">${displayRecord}</div>
            </div>
            <div class="app-row-right" style="display:flex; align-items:center; gap:12px;">
                <span class="dynamic-badge" style="background:${ui.bg}; color:${ui.text}; border:1px solid ${ui.border};">
                    <span class="dynamic-dot" style="background:${ui.icon};"></span>
                    ${app.status}
                </span>
                <svg class="chevron" width="16" height="16" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        `;
    });

    container.innerHTML = html;
}

// ── Server-Side Interactions & Listeners ──
document.getElementById('per-page-select')?.addEventListener('change', function() {
    updateUrlParams('per_page', this.value);
});

const searchInput = document.getElementById('search-input');
const searchClear = document.getElementById('search-clear');

searchInput?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        updateUrlParams('search', this.value.trim());
    }
});

searchClear?.addEventListener('click', function() {
    updateUrlParams('search', '');
});

const filterBtn = document.getElementById('filter-btn');
const filterDropdown = document.getElementById('filter-dropdown');

filterBtn?.addEventListener('click', function(e) {
    e.stopPropagation();
    filterBtn.classList.toggle('open');
    filterDropdown.classList.toggle('open');
});

document.querySelectorAll('.filter-option').forEach(function(opt) {
    opt.addEventListener('click', function() {
        updateUrlParams('status', this.getAttribute('data-value'));
    });
});

document.addEventListener('click', function(e) {
    if (filterBtn && !filterBtn.contains(e.target) && filterDropdown && !filterDropdown.contains(e.target)) {
        filterBtn.classList.remove('open');
        filterDropdown.classList.remove('open');
    }
});

document.getElementById('clear-all-btn')?.addEventListener('click', function() {
    window.location.href = window.location.pathname; // Strips all params cleanly
});

// ── Modals & Steps ──
function showModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('visible');
    document.body.style.overflow = 'hidden';
}

function hideModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('visible');
    el.classList.add('hidden');
    document.body.style.overflow = '';
}

function renderStep(container, step, currentStep, color) {
    const done    = step.id < currentStep;
    const current = step.id === currentStep;
    const label   = step.label.replace('\n','<br>');
    const doneClr = color === 'orange' ? 'bg-orange-500 border-orange-500' : 'bg-green-500 border-green-500';
    const doneText = color === 'orange' ? 'text-orange-600' : 'text-green-600';
    let circle, textClass;

    if (step.customState === 'revision') {
        circle = `<div class="w-8 h-8 rounded-full bg-yellow-500 border-yellow-500 flex items-center justify-center shadow-md ring-4 ring-yellow-100"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></div>`;
        textClass = 'text-yellow-600';
    } else if (step.customState === 'approved') {
        circle = `<div class="w-8 h-8 rounded-full bg-green-500 border-green-500 flex items-center justify-center shadow-md ring-4 ring-green-100"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>`;
        textClass = 'text-green-600';
    } else if (step.customState === 'rejected') {
        circle = `<div class="w-8 h-8 rounded-full bg-red-600 border-red-600 flex items-center justify-center shadow-md ring-4 ring-red-100"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg></div>`;
        textClass = 'text-red-600';
    } else if (done) {
        circle = `<div class="w-8 h-8 rounded-full ${doneClr} flex items-center justify-center shadow-sm"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>`;
        textClass = doneText;
    } else if (current) {
        circle = `<div class="w-8 h-8 rounded-full bg-white border-[3px] border-bsu-dark flex items-center justify-center shadow-md ring-4 ring-blue-100"><span class="text-[11px] font-black text-bsu-dark">${step.id}</span></div>`;
        textClass = 'text-bsu-dark';
    } else {
        circle = `<div class="w-8 h-8 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center shadow-sm"><span class="text-[11px] font-black text-gray-400">${step.id}</span></div>`;
        textClass = 'text-gray-400';
    }
    container.innerHTML = `${circle}<p class="text-[8px] sm:text-[9px] font-black ${textClass} uppercase tracking-wide text-center leading-tight">${label}</p>`;
}

function scrollProgress(direction, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const stepWidth = 840 / 7;
    if (direction === 'left') container.scrollLeft -= stepWidth;
    else container.scrollLeft += stepWidth;
}

function scrollToCurrentStep(containerId = 'progress-scroll-container') {
    const container = document.getElementById(containerId);
    const activeStep = document.querySelector(`#${containerId} .step-active`);
    if (container && activeStep) {
        const containerWidth = container.offsetWidth;
        const stepOffset = activeStep.offsetLeft;
        const stepWidth = activeStep.offsetWidth;
        const scrollTarget = stepOffset - (containerWidth / 2) + (stepWidth / 2);
        container.scrollTo({ left: scrollTarget, behavior: 'smooth' });
    }
}

// ── Modals Logic ──
let currentAppDocs = [];

function openAppDetail(idx) {
    const app = APPLICATIONS[idx];
    if (!app) return;

    currentRecordCode = app.record;
    currentAppDocs = app.docs || [];

    // --- APPLY MAIN PROTOCOL STATUS SWITCH LOGIC ---
    let currentStep = 2;
    let statusText = 'Pending';
    // Fallback to formatted status if raw_status isn't passed from blade
    const dbStatus = (app.raw_status || app.status).toLowerCase().replace(/ /g, '_');

    switch (dbStatus) {
        case 'submitted':
        case 'incomplete_documents':
            currentStep = 2;
            statusText = (dbStatus === 'submitted') ? 'Checking Documents' : 'Reupload Documents';
            break;
        case 'documents_checking':
        case 'documents_complete':
            currentStep = 3;
            statusText = 'Awaiting Classification & Assignment';
            break;
        case 'exempted_awaiting_chair_approval':
            currentStep = 6;
            statusText = 'Awaiting Chair Approval';
            break;
        case 'awaiting_reviewer_approval':
            currentStep = 3;
            statusText = 'Awaiting Reviewer Confirmation';
            break;
        case 'under_review':
            currentStep = 4;
            statusText = 'Under Review';
            break;
        case 'review_finished':
            currentStep = 5;
            statusText = 'Review Completed';
            break;
        case 'assessment_processed':
        case 'drafting_decision':
            currentStep = 6;
            statusText = 'Finalizing Decision';
            break;
        case 'awaiting_approval':
        case 'awaiting_chair_approval_decision':
            currentStep = 6;
            statusText = 'Awaiting Chair Approval';
            break;
        case 'approved':
        case 'completed':
            currentStep = 7;
            statusText = (dbStatus === 'approved') ? 'Approved' : 'Completed';
            break;
        case 'resubmit':
        case 'returned_for_revision':
        case 'minor_revision':
        case 'major_revision':
            currentStep = 7; // Cap at 7 for UI bounds
            statusText = 'Revision Required';
            break;
        default:
            currentStep = 2;
            statusText = 'Pending';
    }

    // 1. Basic Information Injection
    document.getElementById('m-title').textContent = app.title;
    document.getElementById('m-record').textContent = 'Record: ' + app.record;
    document.getElementById('m-date').textContent = app.date;
    document.getElementById('m-reviewer-label').textContent = APP_REVIEWER_LABEL[currentStep] || 'Assigned Reviewer';
    document.getElementById('m-reviewer').textContent = app.reviewer;
    document.getElementById('m-study-type').textContent = app.studyType || '—';

    // 2. Status Badge Styling
    const uiKey = STATUS_UI[statusText] ? statusText : (STATUS_UI[app.status] ? app.status : 'default');
    const ui = STATUS_UI[uiKey];

    const sb = document.getElementById('m-status');
    sb.textContent = statusText;
    sb.style.cssText = `display:inline-block; font-size:10px; font-weight:800; text-transform:uppercase; padding:4px 10px; border-radius:6px; background:${ui.bg}; color:${ui.text}; border:1px solid ${ui.border};`;

    // 3. Progress Bar Rendering
    const isApprovedState = (dbStatus === 'approved' || dbStatus === 'completed');
    const isRejectedState = (dbStatus === 'disapproved' || dbStatus === 'rejected');
    const isRevisionState = (dbStatus === 'resubmit' || dbStatus === 'returned_for_revision' || dbStatus === 'minor_revision' || dbStatus === 'major_revision');

    APP_STEPS.forEach(s => {
        let sCopy = { ...s };
        if (sCopy.id === 7) {
            if (isApprovedState) { sCopy.label = 'Approved'; sCopy.customState = 'approved'; }
            else if (isRejectedState) { sCopy.label = 'Rejected'; sCopy.customState = 'rejected'; }
            else if (isRevisionState) { sCopy.label = 'Revision\nRequired'; sCopy.customState = 'revision'; }
        }
        const el = document.getElementById('m-step-' + sCopy.id);
        if (el) {
            renderStep(el, sCopy, currentStep, 'green');
            if (sCopy.id === currentStep) el.classList.add('step-active');
            else el.classList.remove('step-active');
        }
    });
    document.getElementById('m-progress-fill').style.width = (Math.max(0, currentStep - 1) / 6 * 100) + '%';

    // 4. Version History (Resubmissions)
    const versionsContainer = document.getElementById('m-versions-container');
    const versionList = document.getElementById('m-version-list');
    const appVersions = REVISION_HISTORY.filter(rs => rs.record === app.record);

    if (appVersions.length > 0) {
        versionsContainer.classList.remove('hidden');
        versionList.innerHTML = appVersions.map(rs => {
            const vApproved = ['Approved', 'approved', 'Completed', 'completed'].includes(rs.status);
            const vRejected = ['Rejected', 'rejected', 'Disapproved'].includes(rs.status);
            const sStyle = vApproved ? 'text-green-600' : (vRejected ? 'text-red-600' : 'text-gray-400');
            return `
            <button onclick="hideModal('detail-modal'); openResubDetailById('${rs.id}')"
                    class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl hover:border-bsu-dark transition-all group">
                <div class="text-left">
                    <p class="text-[12px] font-black text-bsu-dark uppercase tracking-tight">Version ${rs.revision_number}</p>
                    <p class="text-[10px] ${sStyle} font-bold uppercase tracking-widest">${rs.status}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-bold text-gray-300 uppercase">${rs.date}</span>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-bsu-dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </button>`;
        }).join('');
    } else {
        versionsContainer.classList.add('hidden');
    }

    // 5. Categorize Documents for Reordering
    document.getElementById('m-doc-count').textContent = currentAppDocs.length + ' file' + (currentAppDocs.length !== 1 ? 's' : '');

    // Define which names stay at the very top
    const topDocNames = [
        'Incoming Communications Logbook',
        'Outgoing Communications Logbook',
        'Application Form (System Generated)'
    ];

    const topDocs = currentAppDocs.filter(d => topDocNames.includes(d.name));
    const assessmentForms = currentAppDocs.filter(d => d.name.includes('Assessment Form'));
    const icfForms = currentAppDocs.filter(d => d.name.includes('ICF Assessment'));

    // Everything else (Manuscripts, Proposals, Decision Letters)
    const bottomDocs = currentAppDocs.filter(d =>
        !topDocNames.includes(d.name) &&
        !d.name.includes('Assessment Form') &&
        !d.name.includes('ICF Assessment')
    );

    let docsHtml = '';

    // --- A. Render Top Documents ---
    docsHtml += topDocs.map(docRow).join('');

    // Helper for dropdown UI
    const createDropdown = (title, count, docs, iconColorClass) => `
        <div class="mt-3 mb-3 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <details class="group">
                <summary class="flex items-center justify-between p-3.5 bg-gray-50 cursor-pointer list-none hover:bg-gray-100 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg ${iconColorClass} flex items-center justify-center text-white shadow-inner">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-bsu-dark uppercase tracking-tight">${title}</p>
                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">${count} Reviewer Form${count !== 1 ? 's' : ''}</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="p-2 space-y-2 bg-white border-t border-gray-100">
                    ${docs.map(docRow).join('')}
                </div>
            </details>
        </div>
    `;

    // --- B. Render Dropdowns (Sandwiched in the middle) ---
    if (assessmentForms.length > 0) {
        docsHtml += createDropdown('General Assessment Forms', assessmentForms.length, assessmentForms, 'bg-blue-600');
    }

    if (icfForms.length > 0) {
        docsHtml += createDropdown('Informed Consent Assessments', icfForms.length, icfForms, 'bg-orange-500');
    }

    // --- C. Render Bottom Documents (Manuscripts, etc.) ---
    docsHtml += bottomDocs.map(docRow).join('');

    // 6. Inject Final Document HTML
    document.getElementById('m-documents').innerHTML = docsHtml ||
        '<p class="text-[12px] text-gray-400 italic text-center py-8">No documents associated with this record.</p>';

    // 7. Show Modal and Adjust Scroll
    showModal('detail-modal');
    setTimeout(() => scrollToCurrentStep('progress-scroll-container'), 300);
}

let currentResubDocs = [];
function openResubDetail(idx) {
    const rs = REVISION_HISTORY[idx];
    if(!rs) return;

    currentRecordCode = rs.record;
    currentResubDocs = rs.docs || [];

    // --- APPLY REVISION STATUS SWITCH LOGIC ---
    let step = 2;
    let statusText = 'Documents Checking';
    const revStatus = (rs.raw_status || rs.status).toLowerCase().replace(/ /g, '_');

    switch (revStatus) {
        case 'submitted':
        case 'under_review':
            step = 2;
            statusText = 'Waiting for Review';
            break;
        case 'incorrect':
            step = 2;
            statusText = 'Incorrect';
            break;
        case 'review_finished':
        case 'processing_assessment':
            step = 3;
            statusText = 'Processing Assessment Forms';
            break;
        case 'assessment_processed':
        case 'drafting_decision':
        case 'awaiting_chair_approval':
            step = 4;
            statusText = 'Drafting Decision Letter';
            break;
        case 'minor_revision':
        case 'major_revision':
        case 'resubmit':
        case 'returned_for_revision':
            step = 5;
            statusText = 'Revision Required';
            break;
        case 'approved':
        case 'completed':
            step = 5;
            statusText = 'Completed';
            break;
        default:
            step = 2;
            statusText = 'Pending Secretariat Review';
            break;
    }

    document.getElementById('rd-title').textContent      = rs.title || 'Version Info';
    document.getElementById('rd-record').textContent     = 'Record: ' + rs.record;
    document.getElementById('rd-date').textContent       = rs.date;
    document.getElementById('rd-reviewer').textContent   = rs.reviewer || 'Assigned Reviewer';
    document.getElementById('rd-study-type').textContent = rs.studyType || '—';
    document.getElementById('rd-doc-count').textContent  = currentResubDocs.length + ' file' + (currentResubDocs.length !== 1 ? 's' : '');

    const vApproved = ['approved', 'completed'].includes(revStatus);
    const vRejected = ['rejected', 'disapproved'].includes(revStatus);
    const vRevision = ['resubmit', 'minor_revision', 'major_revision', 'returned_for_revision'].includes(revStatus);

    let uiKey = 'default';
    if(vApproved) uiKey = 'Approved';
    if(vRejected) uiKey = 'Rejected';
    if(vRevision) uiKey = 'Revision Required';

    const ui = STATUS_UI[uiKey] || STATUS_UI['default'];
    const statusEl = document.getElementById('rd-status');
    statusEl.textContent = statusText;
    statusEl.style.cssText = `display:inline-flex; font-size:10px; font-weight:800; text-transform:uppercase; padding:4px 10px; border-radius:6px; background:${ui.bg}; color:${ui.text}; border:1px solid ${ui.border};`;

    RESUB_STEPS.forEach(s => {
        let sCopy = { ...s };
        if (sCopy.id === 5) {
            if (vApproved) { sCopy.label = 'Approved'; sCopy.customState = 'approved'; }
            else if (vRejected) { sCopy.label = 'Rejected'; sCopy.customState = 'rejected'; }
            else if (vRevision) { sCopy.label = 'Revision\nRequired'; sCopy.customState = 'revision'; }
        }
        const el = document.getElementById('rd-step-' + sCopy.id);
        if (el) {
            renderStep(el, sCopy, step, 'orange');
            if (sCopy.id === step) el.classList.add('step-active');
            else el.classList.remove('step-active');
        }
    });
    document.getElementById('rd-progress-fill').style.width = (Math.max(0, step - 1) / 4 * 100) + '%';

    document.getElementById('rd-documents').innerHTML = currentResubDocs.map(docRow).join('') || '<p class="text-[12px] text-gray-400 italic text-center py-4">No documents available.</p>';

    const backBtn = document.getElementById('rd-back-btn');
    const originalAppIdx = APPLICATIONS.findIndex(app => app.record === rs.record);
    if (originalAppIdx !== -1 && backBtn) {
        backBtn.classList.remove('hidden');
        backBtn.onclick = () => {
            hideModal('resub-detail-modal');
            openAppDetail(originalAppIdx);
        };
    }

    showModal('resub-detail-modal');
}

function openResubDetailById(id) {
    const idx = REVISION_HISTORY.findIndex(r => r.id === id);
    if (idx !== -1) openResubDetail(idx);
}

// ── Auto-Open Modal from Calendar ──
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const targetAppId = urlParams.get('openApp');

    if (targetAppId) {
        const appIndex = APPLICATIONS.findIndex(app => app.record === targetAppId);

        if (appIndex !== -1) {
            setTimeout(() => { openAppDetail(appIndex); }, 150);

            // Clean up the URL to prevent reopening on refresh
            urlParams.delete('openApp');
            let newUrl = window.location.pathname;
            if (urlParams.toString()) newUrl += '?' + urlParams.toString();
            window.history.replaceState({}, document.title, newUrl);
        } else {
            console.warn(`Calendar requested protocol ${targetAppId}, but it was not found on this specific page of History.`);
        }
    }
});

// ── Download Handlers ──
function downloadAllDocs() {
    if (!currentRecordCode) return;
    // Change this URL to match your exact route name for the ZIP download method
    window.location.href = `/documents/api/download-zip/${currentRecordCode}`;
}

function downloadResubDocs() {
    if (!currentRecordCode) return;
    // Since your backend logic bundles revisions into the same ZIP, we use the same endpoint
    window.location.href = `/documents/api/download-zip/${currentRecordCode}`;
}

// ── Initialize App ──
renderApplications();

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.driver === 'undefined') {
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css';
        document.head.appendChild(css);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = initTour;
        document.head.appendChild(script);
    } else {
        initTour();
    }

    function initTour() {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        if (tourState === 'chair_history') {
            const driver = window.driver.js.driver;
            const tour = driver({
                showProgress: true,
                allowClose: false,
                overlayColor: 'rgba(33, 60, 113, 0.75)',
                nextBtnText: 'Next &rarr;',
                prevBtnText: '&larr; Back',

                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        hideModal('detail-modal');
                        localStorage.setItem(storageKey, 'chair_settings');
                        tour.destroy();
                        window.location.href = "{{ route('settings') }}";
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-history-search',
                        popover: {
                            title: 'Search & Filter Archives',
                            description: 'Need to pull up an old protocol? Use this bar to quickly search by ID, title, or proponent. You can also filter the list to show only "Completed" (Approved) or "Rejected" applications.',
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-history-list',
                        popover: {
                            title: 'The Permanent Archive',
                            description: 'This is the secure history hub. Any application that has received a final verdict automatically leaves the active queues and is permanently stored here for record-keeping. Click "Next" to see what an archived record looks like.',
                            side: "top",
                            align: 'start',
                            onNextClick: () => {
                                // MOCK MODAL LOGIC FOR PLAIN JS
                                const mockApp = {
                                    record: '2026-MOCK-HIST',
                                    title: 'Effects of AI on System Architecture (Archived)',
                                    proponent: 'Dr. Jane Doe',
                                    status: 'Approved',
                                    step: 7,
                                    date: '2026-04-10',
                                    reviewer: 'Dr. Smith',
                                    studyType: 'Biomedical Research',
                                    docs: [
                                        { name: 'Application Form (System Generated)', type: 'pdf', size: '120 KB', file: '#' },
                                        { name: 'Study Protocol', type: 'pdf', size: '2.4 MB', file: '#' },
                                        { name: 'Decision Letter', type: 'pdf', size: '85 KB', file: '#' }
                                    ]
                                };
                                APPLICATIONS.push(mockApp);
                                openAppDetail(APPLICATIONS.length - 1);
                                setTimeout(() => { tour.moveNext(); }, 350);
                            }
                        }
                    },
                    {
                        element: '#tour-modal-progress',
                        popover: {
                            title: 'Historical Timeline',
                            description: 'Inside the archive, you can review the complete timeline of the protocol, tracking its journey from initial submission all the way to the final decision.',
                            side: "bottom",
                            align: 'center'
                        }
                    },
                    {
                        element: '#tour-modal-docs',
                        popover: {
                            title: 'Permanent Document Storage',
                            description: 'All files, including legacy versions, assessment forms, and the final decision letters, are kept forever. You can securely download them at any time.',
                            side: "top",
                            align: 'start',
                            onNextClick: () => {
                                hideModal('detail-modal');
                                setTimeout(() => { tour.moveNext(); }, 350);
                            }
                        }
                    },
                    {
                        // Floating popover
                        popover: {
                            title: 'Final Step: Account Security 🔒',
                            description: 'You have completed the system tour! Because you are using a default, auto-generated password, your final requirement is to update it. Click below to proceed to your Account Settings.',
                            side: "bottom",
                            align: 'center',
                            doneBtnText: 'Update Password →'
                        }
                    }
                ]
            });

            tour.drive();
        }
    }
});
</script>

<script>
function startHistoryManualTutorial() {

    const driver = window.driver.js.driver;

    const tour = driver({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(33, 60, 113, 0.75)',
        nextBtnText: 'Next →',
        prevBtnText: '← Back',

        steps: [
            {
                element: '.search-filter-bar',
                popover: {
                    title: 'Search & Filter',
                    description: 'Use the search bar and filters to quickly locate finalized applications.',
                    side: "bottom",
                    align: "start"
                }
            },
            {
                element: '#tour-history-list',
                popover: {
                    title: 'Application History',
                    description: 'All completed, approved, and rejected protocols are stored here.',
                    side: "top",
                    align: "start"
                }
            },
            {
                popover: {
                    title: 'Detailed Records',
                    description: 'Click any row to open its full timeline, submitted files, and decision history.',
                    side: "bottom",
                    align: "center"
                }
            },
            {
                popover: {
                    title: 'Tutorial Complete!',
                    description: 'You may replay tutorials anytime using the View Tutorial button.',
                    side: "bottom",
                    align: "center",
                    doneBtnText: 'Finish'
                }
            }
        ]
    });

    tour.drive();
}
</script>
</body>
</html>
