<?php // BSU BERC - Application Status Page (Laravel Blade) ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{'bsu-dark':'#213C71','brand-red':'#D32F2F','light-bg':'#F8F9FA'}}}}</script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/functions.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>


    <style>
        /* Styles for PDF Generation */
        .pdf-content {
            width: 8.5in;
            padding: 0.5in;
            background: white;
            color: black;
            font-family: 'Times New Roman', serif; /* Standard for official docs */
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

        /* Force page breaks if a section is too long */
        .page-break {
            page-break-after: always;
        }

        /* Hide web-only elements */
        .no-print {
            display: none !important;
        }
        body { font-family: 'Inter', sans-serif; }
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
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* scrollbar thin */
        .thin-scroll::-webkit-scrollbar { width:4px; }
        .thin-scroll::-webkit-scrollbar-track { background:transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background:#D1D5DB; border-radius:99px; }

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
<body class="bg-light-bg min-h-screen">

<!-- ══════════════════ HEADER ══════════════════ -->
<header class="bg-white sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-3">
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            <img src="{{ asset('logo/BERC.png') }}" alt="BSU Logo" class="h-10 sm:h-7 w-auto object-contain shrink-0">
            <div class="flex flex-col min-w-0">
                <h1 class="text-[11px] sm:text-[15px] font-bold text-bsu-dark leading-tight tracking-tight uppercase truncate">Batangas State University - TNEU</h1>
                <p class="text-[9px] sm:text-[10px] font-bold text-brand-red leading-tight uppercase tracking-widest mt-0.5">Ethics Review Committee</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="date-block">
                <div class="time" id="clock-m">--:-- -- | -------</div>
                <div class="date" id="date-m">--/--/----</div>
            </div>
            <button id="hamburger-btn" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 gap-1.5 shrink-0">
                <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb1"></span>
                <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb2"></span>
                <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb3"></span>
            </button>
        </div>
    </div>
    <!-- Desktop nav bar -->
    <div class="border-t border-b border-gray-200 bg-[#FCFCFC] hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">
            <div class="flex space-x-1">
                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('application.status') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('application.status') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('application.status') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Applications Status</span>
                </a>

                <a href="{{ route('application.history') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('application.history') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('application.history') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Application History</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-6 py-4 hover:text-bsu-dark transition-colors">
                    <span>    </span>
                </a>
            </div>

            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">

                <button type="button"
                        onclick="restartStatusTutorial()"
                        class="flex items-center gap-2 transition-all hover:-translate-y-0.5 text-gray-500 hover:text-bsu-dark">

                    <svg class="w-4 h-4 shrink-0 text-brand-red"
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
                <a href="{{ route('settings') }}"
                class="flex items-center gap-2 transition-all hover:-translate-y-0.5 {{ request()->routeIs('settings') ? 'text-bsu-dark font-black' : 'text-gray-500 hover:text-bsu-dark' }}">
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
    <div id="mobile-menu" class="hidden-anim hidden md:hidden border-t border-gray-200 bg-[#FCFCFC]">
        <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Requirements</span>
            </a>
            <a href="dashboard#about-berc" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>About BERC</span>
            </a>
            <a href="#" class="flex items-center space-x-3 text-bsu-dark px-5 py-3.5">
                <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span>Short Cut</span>
            </a>
            <div class="flex items-center justify-between px-5 py-3.5">
                <a href="#" class="hover:text-bsu-dark">Change Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase">
                        Sign Out
                    </button>
                </form>
            </div>
            <div class="mobile-date">
                <div class="time" id="clock-m">--:-- -- | -------</div>
                <div class="date" id="date-m">--/--/----</div>
            </div>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-bsu-dark/70"></div>
        </div>
        <div class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
            <div class="shrink-0">
                <div class="bg-white/20 backdrop-blur-sm p-1 rounded-2xl border border-white/20 shadow-lg">
                    <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}" alt="Student Photo" class="w-16 h-16 sm:w-24 sm:h-24 object-cover bg-gray-300 rounded-xl">
                </div>
            </div>
            <div class="flex-1 text-white text-center sm:text-left">
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">{{ ucfirst($user->role) }}</p>
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight">{{ $user->name }}</h2>
                <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                    <div><p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Account ID</p><p class="text-xs font-bold tracking-wide">{{ $user->id }}</p></div>
                    <div><p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Email Address</p><p class="text-xs font-bold tracking-wide">{{ $user->email }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <div id="tour-status-tabs" class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
        <div class="flex border-b border-gray-100 py-2">
            <button id="tab-application" onclick="switchTab('application')"
                class="tab-btn flex items-center space-x-2 px-5 sm:px-8 py-3.5 text-[11px] sm:text-[12px] font-black uppercase tracking-wider transition-all border-b-[3px] border-brand-red text-bsu-dark bg-white">
                <svg class="w-4 h-4 shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Application Status</span>
                <span id="badge-application" class="bg-bsu-dark text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">0</span>
            </button>
            <button id="tab-resubmission" onclick="switchTab('resubmission')"
                class="tab-btn flex items-center space-x-2 px-5 sm:px-8 py-3.5 text-[11px] sm:text-[12px] font-black uppercase tracking-wider transition-all border-b-[3px] border-transparent text-gray-400 hover:text-bsu-dark hover:bg-gray-50">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Resubmission Status</span>
                <span id="badge-resubmission" class="bg-orange-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">0</span>
            </button>
        </div>
        <div id="panel-application"  class="tab-panel active pt-6 px-4 pb-4 sm:pt-8 sm:px-6 sm:pb-6 space-y-3"></div>
        <div id="panel-resubmission" class="tab-panel        pt-6 px-4 pb-4 sm:pt-8 sm:px-6 sm:pb-6 space-y-3"></div>
    </div>
</div>

<div id="detail-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="modal-card bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="bg-bsu-dark px-6 py-4 flex items-start justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <p id="m-type-label" class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-1"></p>
                <h3 id="m-title"  class="text-white font-black text-[15px] leading-tight"></h3>
                <p id="m-record" class="text-blue-200 text-[11px] font-semibold mt-1"></p>
            </div>
            <button onclick="closeDetailModal()" class="text-white/60 hover:text-white transition-colors mt-0.5 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto thin-scroll flex-1 p-6 space-y-5">

            <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-2">Application Progress</p>

            <div class="relative group">
                <button type="button" onclick="scrollProgress('left')"
                    class="absolute left-0 top-6 -translate-y-1/2 z-20 bg-white shadow-md border border-gray-200 text-gray-600 p-1 rounded-full hover:bg-bsu-dark hover:text-white transition-all opacity-0 group-hover:opacity-100 -ml-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                <button type="button" onclick="scrollProgress('right')"
                    class="absolute right-0 top-6 -translate-y-1/2 z-20 bg-white shadow-md border border-gray-200 text-gray-600 p-1 rounded-full hover:bg-bsu-dark hover:text-white transition-all opacity-0 group-hover:opacity-100 -mr-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <div id="progress-scroll-container" class="w-full overflow-x-auto pb-1 hide-scrollbar scroll-smooth">
                    <div class="relative grid grid-cols-7 min-w-[840px] w-full pt-2">
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
                <div id="m-version-list" class="space-y-2 mb-6">
                    </div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="h-px bg-gray-100 flex-1"></span>
                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Initial Application History (V0)</p>
                    <span class="h-px bg-gray-100 flex-1"></span>
                </div>
            </div>

            <div id="m-revision-notice" class="hidden space-y-3">
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 flex items-start gap-3">
                    <div class="shrink-0 w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mt-0.5">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-orange-700 uppercase tracking-wide mb-1">Revision Required</p>
                        <p class="text-[12px] text-orange-600 font-medium leading-relaxed" id="m-revision-remarks"></p>
                    </div>
                </div>

                <a href="#" id="m-revision-btn-link" target="_blank" class="w-full flex items-center gap-4 p-4 bg-white border border-gray-200 rounded-xl hover:border-bsu-dark hover:shadow-sm transition-all group text-left cursor-pointer">
                    <div class="shrink-0 w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center group-hover:bg-red-100 transition-colors">
                        <svg class="w-6 h-6 text-brand-red" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM7 13h10v1H7zm0 2h10v1H7zm0 2h7v1H7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-black text-bsu-dark uppercase tracking-wide">Resubmission Application</p>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">Click here to fill out the resubmission form</p>
                    </div>
                    <div class="shrink-0 text-gray-300 group-hover:text-bsu-dark transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
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
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Type of Study</p>
                    <p id="m-study-type" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
                <div id="m-comments-container" class="col-span-full w-full mt-5 p-4 bg-gray-50 border border-gray-200 rounded-xl hidden">
                    <div class="flex items-center gap-2 mb-1.5">
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Remarks / Comments</p>
                    </div>
                    <p id="m-comments-text" class="text-xs text-bsu-dark font-semibold leading-relaxed pl-6"></p>
                </div>
            </div>

            <div id="m-resubmit-container" class="hidden mt-6 border-t border-gray-200 pt-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <h4 class="text-[12px] font-black text-bsu-dark uppercase tracking-widest">Resubmit Requested Documents</h4>
                </div>
                <p class="text-[11px] text-gray-500 mb-4">Only re-upload the documents that were requested to be revised or added.</p>
                <form id="m-resubmit-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label for="doc-selector" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Select a Document to Resubmit</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="doc-selector" class="flex-1 w-full text-xs text-gray-700 border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red cursor-pointer">
                                <option value="" disabled selected>-- Choose Document Type --</option>
                                <option value="doc_letter_request">Letter request for review</option>
                                <option value="doc_endorsement_letter">Endorsement / Referral Letter</option>
                                <option value="doc_full_proposal">Full proposal / Study protocol</option>
                                <option value="doc_technical_review_approval">Technical Review Approval</option>
                                <option value="doc_informed_consent_form_english">Informed Consent Form (English)</option>
                                <option value="doc_informed_consent_form_filipino">Informed Consent Form (Filipino)</option>
                                <option value="doc_manuscript">Manuscript</option>
                            </select>
                            <button type="button" id="btn-add-doc-field" class="shrink-0 bg-bsu-dark hover:bg-blue-900 text-white text-[10px] font-black uppercase tracking-wider px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                Add Field
                            </button>
                        </div>
                    </div>
                    <div id="selected-docs-container" class="space-y-3"></div>
                    <p id="resubmit-error" class="hidden text-xs text-red-600 font-bold tracking-wide text-center">Please add at least one document before submitting.</p>
                    <div class="flex justify-end pt-2">
                        <button type="submit" id="btn-submit-docs" class="bg-brand-red hover:bg-red-700 text-white text-[11px] font-black uppercase tracking-wider px-6 py-3 rounded-lg shadow-md transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            Submit Documents
                        </button>
                    </div>
                </form>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Submitted Documents</p>
                    <span id="m-doc-count" class="text-[9px] font-black text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full uppercase tracking-wider"></span>
                </div>
                <div id="m-documents" class="space-y-2"></div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0 bg-white">
            <button onclick="closeDetailModal()" class="text-[11px] font-black uppercase tracking-wider text-gray-500 hover:text-bsu-dark border border-gray-200 px-5 py-2.5 rounded-lg transition-colors">Close</button>
            <button onclick="downloadAllDocs()" class="text-[11px] font-black uppercase tracking-wider bg-bsu-dark hover:bg-blue-900 text-white px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/></svg>
                Download All
            </button>
        </div>
    </div>
</div>

<div id="resubmitConfirmModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
        <div class="p-6 text-center">
            <svg class="mx-auto mb-4 text-brand-red w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Submit Documents?</h3>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                Are you sure you want to resubmit these documents? This will forward your application back to the Secretariat.
            </p>
            <div class="flex justify-center gap-3">
                <button type="button" id="cancelResubmitBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                <button type="button" id="confirmResubmitBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">Yes, Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="js-toast" class="fixed top-6 right-6 z-[10010] flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-xl shadow-xl border border-gray-100 transform transition-all duration-500 translate-x-full opacity-0 hidden" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
    </div>
    <div class="ml-3 text-sm font-bold text-gray-700 tracking-wide" id="js-toast-message"></div>
</div>

<div id="revision-form-modal" class="modal-overlay fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
    <div class="modal-card bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">

        <div class="bg-orange-600 px-6 py-4 flex items-start justify-between gap-3 shrink-0">
            <div>
                <p class="text-[9px] font-black text-orange-200 uppercase tracking-widest mb-1">Resubmission Form</p>
                <h3 id="rf-title"  class="text-white font-black text-[15px] leading-tight"></h3>
                <p id="rf-record" class="text-orange-200 text-[11px] font-semibold mt-1"></p>
            </div>
            <button onclick="closeRevisionForm()" class="text-white/60 hover:text-white transition-colors mt-0.5 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="bg-orange-50 border-b border-orange-100 px-6 py-3 flex flex-wrap gap-x-8 gap-y-1">
            <div><span class="text-[9px] font-black text-orange-400 uppercase tracking-widest">Reviewer Comments: </span><span id="rf-reviewer" class="text-[11px] font-bold text-orange-700"></span></div>
            <div><span class="text-[9px] font-black text-orange-400 uppercase tracking-widest">Deadline: </span><span class="text-[11px] font-bold text-orange-700">March 1, 2026</span></div>
        </div>

        <div class="overflow-y-auto thin-scroll flex-1 p-6 space-y-6">

            <div>
                <p class="form-section-title">1 — Response to Reviewer Comments</p>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Summary of Changes Made</label>
                        <textarea class="form-input resize-none" rows="4" placeholder="Briefly describe all revisions you have made in response to the reviewer's comments..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">Sections Revised</label>
                        <input type="text" class="form-input" placeholder="e.g. Section 3 (Methodology), Section 5 (Ethical Considerations)">
                    </div>
                </div>
            </div>

            <div>
                <p class="form-section-title">2 — Re-upload Revised Documents</p>
                <p class="text-[11px] text-gray-400 font-semibold mb-3">Upload the revised versions of the documents that were flagged. Previously accepted documents are retained automatically.</p>
                <div class="space-y-3" id="rf-doc-upload-list">
                    </div>
            </div>

            <div>
                <p class="form-section-title">3 — Additional Supporting Documents <span class="text-gray-300 font-semibold normal-case tracking-normal">(Optional)</span></p>
                <div class="upload-zone" onclick="document.getElementById('rf-extra-upload').click()">
                    <input type="file" id="rf-extra-upload" class="hidden" multiple accept=".pdf,.doc,.docx">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-[12px] font-bold text-gray-400">Click to upload additional files</p>
                    <p class="text-[10px] text-gray-300 font-semibold mt-1">PDF, DOC, DOCX accepted</p>
                </div>
            </div>

            <div>
                <p class="form-section-title">4 — Declaration</p>
                <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="rf-decl-1" class="mt-0.5 accent-orange-600 w-4 h-4 shrink-0">
                        <span class="text-[12px] font-semibold text-gray-600 leading-relaxed">I confirm that all revisions have been made in accordance with the reviewer's comments and the BERC ethical review guidelines.</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="rf-decl-2" class="mt-0.5 accent-orange-600 w-4 h-4 shrink-0">
                        <span class="text-[12px] font-semibold text-gray-600 leading-relaxed">I certify that the information and documents submitted are accurate and complete to the best of my knowledge.</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="rf-decl-3" class="mt-0.5 accent-orange-600 w-4 h-4 shrink-0">
                        <span class="text-[12px] font-semibold text-gray-600 leading-relaxed">I understand that submitting false or incomplete information may result in rejection or disqualification of my application.</span>
                    </label>
                </div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center gap-3 shrink-0 bg-white">
            <p class="text-[10px] text-gray-400 font-semibold">All fields marked are required before submission.</p>
            <div class="flex gap-3">
                <button onclick="closeRevisionForm()" class="text-[11px] font-black uppercase tracking-wider text-gray-500 hover:text-bsu-dark border border-gray-200 px-5 py-2.5 rounded-lg transition-colors">Cancel</button>
                <button onclick="submitRevision()" class="text-[11px] font-black uppercase tracking-wider bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Submit Resubmission
                </button>
            </div>
        </div>
    </div>
</div>

<div id="resub-detail-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="modal-card bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="bg-bsu-dark px-6 py-4 flex items-start justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <p class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-1">Resubmission Status</p>
                <h3 id="rd-title"  class="text-white font-black text-[15px] leading-tight"></h3>
                <p id="rd-record" class="text-blue-200 text-[11px] font-semibold mt-1"></p>
            </div>
            <button onclick="closeResubDetailModal()" class="text-white/60 hover:text-white transition-colors mt-0.5 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto thin-scroll flex-1 p-6 space-y-5">

            <div>
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider mb-5">Resubmission Progress</p>
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
            </div>

            <div class="h-px bg-gray-100"></div>

            <div id="rd-next-action-container" class="hidden">
                <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-black text-orange-800 uppercase tracking-tight">Further Revisions Needed</p>
                            <p class="text-[11px] text-orange-700 font-medium leading-snug">The committee has requested more changes. Review the comments and submit the next version below.</p>
                        </div>
                    </div>
                    <a id="rd-next-resub-btn" href="#" class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black uppercase tracking-widest px-6 py-3 rounded-xl shadow-md transition-all active:scale-95 whitespace-nowrap">
                        Submit Next Revision
                    </a>
                </div>
            </div>

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
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Type of Study</p>
                    <p id="rd-study-type" class="text-[12px] font-bold text-bsu-dark mt-1"></p>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Resubmitted Documents</p>
                    <span id="rd-doc-count" class="text-[9px] font-black text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full uppercase tracking-wider"></span>
                </div>
                <div id="rd-documents" class="space-y-2"></div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between shrink-0 bg-white">

            <button id="rd-back-btn" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-bsu-dark transition-colors px-2 py-2 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Original Application</span>
            </button>

            <div class="flex items-center gap-3">
                <button onclick="closeResubDetailModal()" class="text-[11px] font-black uppercase tracking-wider text-gray-500 hover:text-bsu-dark border border-gray-200 px-5 py-2.5 rounded-lg transition-colors">
                    Close
                </button>
                <button onclick="downloadResubDocs()" class="text-[11px] font-black uppercase tracking-wider bg-bsu-dark hover:bg-blue-900 text-white px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/>
                    </svg>
                    Download All
                </button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white border-t border-gray-200 mt-10 py-8">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em]">&copy; 2026 Batangas State University</p>
        <p class="text-xs font-semibold mt-2 italic text-brand-red">The National Engineering University</p>
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

<div id="download-notice-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10001] hidden flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-50 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Download Package</h3>
            </div>

            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                The system will now bundle your <span class="font-bold text-bsu-dark">uploaded documents</span> into a single ZIP file.
            </p>

            <div class="bg-gray-50 rounded-lg p-3 mb-6 max-h-48 overflow-y-auto border border-gray-100">
                <ul id="download-file-list" class="space-y-2">
                    </ul>
            </div>

            <div class="flex flex-col gap-3">
                <button type="button" id="start-zip-btn" class="w-full px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">
                    Start Download
                </button>
                <button type="button" onclick="closeDownloadModal()" class="w-full px-5 py-2 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════
   ① DATA  ─  APPLICATION SUBMISSIONS
   ─────────────────────────────────────────────────────
   STEP KEY (Always 7 visual steps):
     1 = Initial Submission
     2 = Documents Checking
     3 = Classification/Assignment
     4 = Under Review
     5 = Processing Assessment Forms
     6 = Processing Decision
     7 = DYNAMIC (Becomes "Approved" [Check] OR "Needs Revision" [Yellow Retry])
   ═══════════════════════════════════════════════════════ */
// FIX 1: Safely cast Laravel JSON outputs to standard Arrays
const _rawApps = @json($formattedApps);
const _rawResubs = @json($formattedRevisions);
const _rawHistory = @json($revisionHistory);

const APPLICATIONS = Array.isArray(_rawApps) ? _rawApps : Object.values(_rawApps || {});
const RESUBMISSIONS = Array.isArray(_rawResubs) ? _rawResubs : Object.values(_rawResubs || {});
const REVISION_HISTORY = Array.isArray(_rawHistory) ? _rawHistory : Object.values(_rawHistory || {});

/* ═══════════════════════════════════════════════════════
   CONFIG / LOOKUP TABLES
   ═══════════════════════════════════════════════════════ */
const APP_STEPS = [
    { id: 1, label: 'Initial\nSubmission' },
    { id: 2, label: 'Documents\nChecking' },
    { id: 3, label: 'Classification/\nAssignment' },
    { id: 4, label: 'Under\nReview' },
    { id: 5, label: 'Processing\nAssessment\nForms' },
    { id: 6, label: 'Processing\nDecision' },
    { id: 7, label: 'Final\nDecision' } // Dynamically overridden in openAppDetail
];

const RESUB_STEPS = [
    { id:1, label:'Resubmission\nReceived'  },
    { id:2, label:'Under\nReview'     },
    { id:3, label:'Processing\nAssessment\nForms'           },
    { id:4, label:'Processing\nDecision\nLetter'        },
    { id:5, label:'Completed'               },
];

const RESUB_REVIEWER_LABEL = {
    1: 'Secretarial Staff',
    2: 'Secretarial Staff',
    3: 'Assigned Reviewer',
    4: 'Chair',
    5: 'Assigned Reviewer',
};

const APP_REVIEWER_LABEL = {
    2: 'Secretarial Staff',
    3: 'Secretariat',
    4: 'Assigned Reviewer',
    5: 'Assigned Reviewer',
    6: 'Chair',
    7: 'Assigned Reviewer',
    8: 'Assigned Reviewer',
};

const STATUS_STYLE = {
    'Pending':                                  'bg-blue-50   text-blue-700   border-blue-200',
    'Checking Documents':                       'bg-blue-50   text-blue-700   border-blue-200',
    'Reupload Documents':                       'bg-red-50    text-red-700    border-red-300',
    'Awaiting Classification & Assignment': 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'Awaiting Reviewer Confirmation':    'bg-indigo-50 text-indigo-700 border-indigo-200',
    'Under Review':                     'bg-yellow-50 text-yellow-700 border-yellow-200',
    'Waiting for Review':               'bg-yellow-50 text-yellow-700 border-yellow-200', // ADDED
    'Processing Assessment Forms':      'bg-amber-50  text-amber-700  border-amber-200', // ADDED
    'Review Completed':                 'bg-amber-50  text-amber-700  border-amber-200',
    'In Meeting':                       'bg-orange-50 text-orange-700 border-orange-200',
    'Awaiting Chair Approval':          'bg-orange-50 text-orange-700 border-orange-200',
    'Drafting Decision Letter':         'bg-orange-50 text-orange-700 border-orange-200', // ADDED
    'Finalizing Decision':              'bg-orange-50 text-orange-700 border-orange-200',
    'Revision Required':                'bg-yellow-50 text-yellow-700 border-yellow-300',
    'Completed':                        'bg-green-50  text-green-700  border-green-200',
    'Approved':                         'bg-green-50  text-green-700  border-green-200',
};

const STATUS_DOT = {
    'Pending':                                  'bg-blue-500',
    'Checking Documents':                       'bg-blue-500',
    'Reupload Documents':                       'bg-red-600 animate-pulse',
    'Awaiting Classification & Assignment': 'bg-indigo-500',
    'Awaiting Reviewer Confirmation':    'bg-indigo-500',
    'Under Review':                     'bg-yellow-500 animate-pulse',
    'Waiting for Review':               'bg-yellow-500 animate-pulse', // ADDED
    'Processing Assessment Forms':      'bg-amber-500', // ADDED
    'Review Completed':                 'bg-amber-500',
    'In Meeting':                       'bg-orange-500',
    'Awaiting Chair Approval':          'bg-orange-500',
    'Drafting Decision Letter':         'bg-orange-500', // ADDED
    'Finalizing Decision':              'bg-orange-500',
    'Revision Required':                'bg-yellow-500 animate-pulse',
    'Completed':                        'bg-green-500',
    'Approved':                         'bg-green-500',
};

const CARD_ICON_BG = {
    'Reupload Documents': 'bg-red-50 group-hover:bg-red-100',
    'Revision Required':  'bg-yellow-50 group-hover:bg-yellow-100',
    'Under Review':       'bg-yellow-50 group-hover:bg-yellow-100',
    'Waiting for Review': 'bg-yellow-50 group-hover:bg-yellow-100', // ADDED
    'Completed':          'bg-green-50  group-hover:bg-green-100',
    'Approved':           'bg-green-50  group-hover:bg-green-100',
    'default':            'bg-gray-50 group-hover:bg-gray-100'
};

const CARD_ICON_COLOR = {
    'Reupload Documents': 'text-red-600',
    'Revision Required':  'text-yellow-600',
    'Under Review':       'text-yellow-600',
    'Waiting for Review': 'text-yellow-600', // ADDED
    'Completed':          'text-green-600',
    'Approved':           'text-green-600',
    'default':            'text-gray-500'
};

const FILE_BG    = { pdf:'bg-red-50 border-red-100', docx:'bg-blue-50 border-blue-100' };
const FILE_BADGE = { pdf:'text-red-500 bg-red-100',  docx:'text-blue-600 bg-blue-100' };

/* ── icons ── */
function fileIcon(type) {
    if (type==='pdf') return `<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 14.5h1.2c.7 0 1.3.6 1.3 1.3v.5c0 .7-.6 1.2-1.3 1.2H9.5v1H8.5v-4zm1 2h.2c.2 0 .3-.1.3-.3v-.4c0-.2-.1-.3-.3H9.5v1zM13 14.5h1.5c.5 0 1 .4 1 1v2c0 .6-.5 1-1 1H13v-4zm1 3h.4c.1 0 .1 0 .1-.1v-1.8c0-.1 0-.1-.1-.1H14v2zm3-3h2v1h-1v.7h.9v.9H18v1.4h-1v-4z"/></svg>`;
    if (type==='docx') return `<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM7 13h10v1H7zm0 2h10v1H7zm0 2h7v1H7z"/></svg>`;
    return `<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`;
}

function docRow(doc) {
    const bg = FILE_BG[doc.type] || 'bg-gray-50 border-gray-100';
    const badge = FILE_BADGE[doc.type] || 'text-gray-500 bg-gray-100';

    return `
    <a href="${doc.file}" onclick="triggerDownload('${doc.file}', event)" class="doc-item flex items-center gap-3 p-3 border ${bg} rounded-xl cursor-pointer hover:shadow-md active:scale-[0.99] transition-all">
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
            <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 16.5l-5-5 1.41-1.41L11 13.67V4h2v9.67l2.59-2.58L17 12.5l-5 5zM5 20v-2h14v2H5z"/>
            </svg>
        </div>
    </a>`;
}

function toggleReviewerDropdown(id, btn) {
    const menu = document.getElementById(id);
    const arrow = btn.querySelector('.dropdown-arrow');
    if (menu.style.display === "none") {
        menu.style.display = "block";
        if (arrow) { arrow.style.transform = "rotate(180deg)"; arrow.classList.add('bg-purple-50', 'text-purple-700', 'border-purple-200'); }
    } else {
        menu.style.display = "none";
        if (arrow) { arrow.style.transform = "rotate(0deg)"; arrow.classList.remove('bg-purple-50', 'text-purple-700', 'border-purple-200'); }
    }
}

/* ═══════════════════════════════════════════════════════
   BUILD CARDS
   ═══════════════════════════════════════════════════════ */
function buildCards() {
    const appHtml = APPLICATIONS.map((app, idx) => {
        const iconBg   = CARD_ICON_BG[app.status]   || 'bg-gray-50 group-hover:bg-gray-100';
        const iconClr  = CARD_ICON_COLOR[app.status] || 'text-gray-500';
        const dot      = STATUS_DOT[app.status]      || 'bg-gray-400';
        const sBadge   = STATUS_STYLE[app.status]    || 'bg-gray-50 text-gray-700 border-gray-200';

        const isRevision = (app.step === 8 || app.status === 'Revision Required' || app.status === 'returned_for_revision');

        return `
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 border
                    ${isRevision ? 'border-yellow-200 hover:border-yellow-400 bg-yellow-50/20' : 'border-gray-200 hover:border-bsu-dark bg-white'}
                    rounded-xl hover:shadow-sm transition-all group">
            <div class="shrink-0 ${iconBg} p-3 rounded-xl transition-colors">
                <svg class="w-7 h-7 ${iconClr}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-[14px] text-bsu-dark truncate">${app.title}</p>
                <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-0.5">Protocol Code: ${app.record}</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="inline-flex items-center gap-1.5 ${sBadge} text-[10px] font-black uppercase px-3 py-1 rounded-full tracking-wider border">
                    <span class="w-1.5 h-1.5 ${dot} rounded-full"></span>${app.status}
                </span>
                <button onclick="openAppDetail(${idx})"
                    class="${isRevision ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-bsu-dark hover:bg-blue-900 text-white'} text-[11px] font-black uppercase tracking-wider px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                    View Details
                </button>
            </div>
        </div>`;
    }).join('');

    document.getElementById('panel-application').innerHTML = appHtml || '<p class="text-center text-[12px] text-gray-400 py-8">No applications found.</p>';

    const resubHtml = RESUBMISSIONS.map((rs, idx) => {
        const dot    = STATUS_DOT[rs.status]   || 'bg-gray-400';
        const sBadge = STATUS_STYLE[rs.status] || 'bg-gray-50 text-gray-700 border-gray-200';
        return `
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 border border-purple-200 hover:border-purple-400 bg-purple-50/20 rounded-xl hover:shadow-sm transition-all group">
            <div class="shrink-0 bg-purple-50 group-hover:bg-purple-100 p-3 rounded-xl transition-colors">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-[14px] text-bsu-dark truncate">${rs.title}</p>
                <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-0.5">Record: ${rs.record}</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="inline-flex items-center gap-1.5 ${sBadge} text-[10px] font-black uppercase px-3 py-1 rounded-full tracking-wider border">
                    <span class="w-1.5 h-1.5 ${dot} rounded-full"></span>${rs.status}
                </span>
                <button onclick="openResubDetail(${idx})" class="bg-bsu-dark hover:bg-blue-900 text-white text-[11px] font-black uppercase tracking-wider px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                    View Details
                </button>
            </div>
        </div>`;
    }).join('');

    document.getElementById('panel-resubmission').innerHTML = resubHtml || '<p class="text-center text-[12px] text-gray-400 py-8">No resubmissions yet. Revisions you submit will appear here.</p>';
    if (RESUBMISSIONS.length) document.getElementById('panel-resubmission').insertAdjacentHTML('beforeend', `<p class="text-center text-[11px] text-gray-400 font-semibold uppercase tracking-widest pt-4 pb-2">— ${RESUBMISSIONS.length} resubmission${RESUBMISSIONS.length!==1?'s':''} in progress —</p>`);

    document.getElementById('badge-application').textContent  = APPLICATIONS.length;
    document.getElementById('badge-resubmission').textContent = RESUBMISSIONS.length;
}

function scrollProgress(direction) {
    const container = document.getElementById('progress-scroll-container');
    const stepWidth = 840 / 7;
    if (direction === 'left') container.scrollLeft -= stepWidth;
    else container.scrollLeft += stepWidth;
}

function scrollToCurrentStep() {
    const activeStep = document.querySelector('.step-active');
    if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    }
}

/* ═══════════════════════════════════════════════════════
   APPLICATION DETAIL MODAL (THE HUB)
   ═══════════════════════════════════════════════════════ */
let currentAppDocs = [];

function openAppDetail(idx) {
    const app = APPLICATIONS[idx];
    currentAppDocs = app.docs || [];

    // --- PRESERVED: Original Metadata Populating ---
    document.getElementById('m-type-label').textContent = 'Application Hub';
    document.getElementById('m-title').textContent      = app.title;
    document.getElementById('m-record').textContent     = 'Record: ' + app.record;
    document.getElementById('m-date').textContent       = app.date;
    document.getElementById('m-study-type').textContent = app.studyType || '—';

    const statusEl = document.getElementById('m-status');
    statusEl.textContent = app.status;
    statusEl.className   = 'inline-flex mt-1 text-[10px] font-black uppercase px-2 py-0.5 rounded-full border tracking-wider ' + (STATUS_STYLE[app.status] || 'bg-gray-50 text-gray-700 border-gray-200');

    // --- PRESERVED: Original Comments & Reupload Logic ---
    const commentsContainer = document.getElementById('m-comments-container');
    const commentsText = document.getElementById('m-comments-text');
    if (app.comments) {
        commentsText.textContent = app.comments;
        commentsContainer.classList.remove('hidden');
    } else {
        commentsContainer.classList.add('hidden');
    }

    const resubmitContainer = document.getElementById('m-resubmit-container');
    const resubmitForm = document.getElementById('m-resubmit-form');
    // Note: Purely for document re-uploads requested by Secretariat before a version is created
    if (app.status === 'Reupload Documents' || app.status === 'reupload_add_documents' || app.status === 'Incomplete Documents') {
        resubmitContainer.classList.remove('hidden');
        resubmitForm.action = `/researcher/application/${app.record}/resubmit`;
    } else {
        resubmitContainer.classList.add('hidden');
    }

    // --- PRESERVED: Original 7-Step Progress Bar Logic ---
    let displayStep = Math.min(Math.max(1, app.step || 1), 7);
    const isRevisionState = (app.step === 8 || app.status === 'Revision Required' || app.status === 'returned_for_revision' || app.status === 'resubmit');
    const isApprovedState = (app.status === 'Approved' || app.status === 'Completed');

    APP_STEPS.forEach(s => {
        let sCopy = { ...s };
        if (sCopy.id === 7) {
            if (isRevisionState) { sCopy.label = 'Needs\nRevision'; sCopy.customState = 'revision'; }
            else if (isApprovedState) { sCopy.label = 'Approved'; sCopy.customState = 'approved'; }
        }
        const el = document.getElementById('m-step-' + sCopy.id);
        if (el) {
            renderStep(el, sCopy, displayStep, 'green');
            // Re-added active class logic to ensure scroll helper works
            if (sCopy.id === displayStep) {
                el.classList.add('step-active');
            } else {
                el.classList.remove('step-active');
            }
        }
    });
    document.getElementById('m-progress-fill').style.width = (Math.max(0, displayStep - 1) / 6 * 100) + '%';

    // --- NEW & UPDATED: VERSIONING HUB LOGIC ---
    const versionsContainer = document.getElementById('m-versions-container');
    const initialRevisionBanner = document.getElementById('m-revision-notice');
    const versionList = document.getElementById('m-version-list');

    // Filter RESUBMISSIONS to find any versions linked to this protocol
    const appVersions = RESUBMISSIONS.filter(rs => rs.record === app.record);

    if (appVersions.length > 0) {
        // CASE: Revisions exist. Always show them as history regardless of current Approved status.
        if (versionsContainer) versionsContainer.classList.remove('hidden');
        if (initialRevisionBanner) initialRevisionBanner.classList.add('hidden');

        versionList.innerHTML = appVersions.map(rs => {
            const isVerApproved = ['approved', 'completed', 'Approved', 'Completed'].includes(rs.status);
            const sStyle = isVerApproved ? 'text-green-600' : 'text-gray-400';

            return `
            <button onclick="hideModal('detail-modal'); switchTab('resubmission'); openResubDetail(${RESUBMISSIONS.indexOf(rs)})"
                    class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl hover:border-bsu-dark transition-all group">
                <div class="text-left">
                    <p class="text-[12px] font-black text-bsu-dark uppercase tracking-tight">Version ${rs.revision_number}</p>
                    <p class="text-[10px] ${sStyle} font-bold uppercase tracking-widest">${rs.status.replace('_', ' ')}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-bold text-gray-300 uppercase">${rs.date}</span>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-bsu-dark transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </button>
            `;
        }).join('');
    }
    else if (isRevisionState) {
        // CASE: No versions submitted yet, but a revision is currently required (Initial V1)
        if (versionsContainer) versionsContainer.classList.add('hidden');
        if (initialRevisionBanner) initialRevisionBanner.classList.remove('hidden');

        document.getElementById('m-revision-remarks').textContent = app.comments || 'Revisions are required. Please review the comments and resubmit.';
        document.getElementById('m-revision-btn-link').href = `/resubmission-form/${app.record}`;
    }
    else {
        // CASE: Application is Approved (or standard) and NEVER required a revision
        if (versionsContainer) versionsContainer.classList.add('hidden');
        if (initialRevisionBanner) initialRevisionBanner.classList.add('hidden');
    }

    // Populate V0 Documents
    document.getElementById('m-doc-count').textContent  = currentAppDocs.length + ' file' + (currentAppDocs.length !== 1 ? 's' : '');
    document.getElementById('m-documents').innerHTML = currentAppDocs.map(docRow).join('') || '<p class="text-[12px] text-gray-400 italic text-center py-4">No documents available.</p>';

    showModal('detail-modal');

    // Smooth scroll to the active step in the progress bar
    setTimeout(scrollToCurrentStep, 300);
}

function closeDetailModal() { hideModal('detail-modal'); }

function downloadAllDocs() {
    if (!currentAppDocs.length) return;
    const modal = document.getElementById('download-notice-modal');
    const fileList = document.getElementById('download-file-list');
    const startBtn = document.getElementById('start-zip-btn');

    fileList.innerHTML = '';

    currentAppDocs.forEach(doc => {
        // Logic: Exclude if it's a system print route OR if it's the Decision Letter
        const isExcluded = doc.file.includes('/print') || doc.name.includes('Decision Letter');

        const li = document.createElement('li');
        li.className = "flex items-center justify-between text-[10px] uppercase font-bold tracking-tight mb-1";

        li.innerHTML = `
            <span class="${isExcluded ? 'text-gray-400' : 'text-gray-700'} truncate mr-4">
                ${doc.name}
            </span>
            <span class="${isExcluded ? 'text-brand-red' : 'text-green-600'} shrink-0">
                ${isExcluded ? 'Excluded' : 'Included'}
            </span>
        `;
        fileList.appendChild(li);
    });

    // When starting the ZIP, make sure to pass the filtered list
    startBtn.onclick = () => {
        const docsToZip = currentAppDocs.filter(doc =>
            !doc.file.includes('/print') && !doc.name.includes('Decision Letter')
        );
        performZipDownload(docsToZip);
    };

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDownloadModal() {
    const modal = document.getElementById('download-notice-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

async function performZipDownload() {
    closeDownloadModal();
    const btn = document.querySelector('button[onclick="downloadAllDocs()"]');
    const originalText = btn.innerHTML;
    const protocolCode = document.getElementById('m-record').textContent.replace('Record: ', '');
    const zip = new JSZip();

    btn.innerText = "Gathering Files...";
    btn.disabled = true;

    try {
        const uploadedFiles = currentAppDocs.filter(doc => !doc.file.includes('/print'));
        for (let doc of uploadedFiles) {
            const fileRes = await fetch(doc.file);
            if (!fileRes.ok) continue;
            const blob = await fileRes.blob();
            const safeName = doc.name.replace(/[/\\?%*:|"<>]/g, '-').trim();
            const fileName = safeName.toLowerCase().endsWith(`.${doc.type}`) ? safeName : `${safeName}.${doc.type}`;
            zip.file(fileName, blob);
        }
        const content = await zip.generateAsync({ type: "blob" });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(content);
        link.download = `Docs_${protocolCode}.zip`;
        link.click();
    } catch (err) {
        console.error("Zip Error:", err);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const docSelector = document.getElementById('doc-selector');
    const addDocBtn = document.getElementById('btn-add-doc-field');
    const container = document.getElementById('selected-docs-container');
    const form = document.getElementById('m-resubmit-form');
    const errorMsg = document.getElementById('resubmit-error');
    const confirmModal = document.getElementById('resubmitConfirmModal');
    const cancelResubmitBtn = document.getElementById('cancelResubmitBtn');
    const confirmResubmitBtn = document.getElementById('confirmResubmitBtn');
    let addedFields = new Set();

    if (addDocBtn) {
        addDocBtn.addEventListener('click', function() {
            const selectedOption = docSelector.options[docSelector.selectedIndex];
            const inputName = selectedOption.value;
            const labelText = selectedOption.text;

            if (!inputName) { alert("Please select a document type from the dropdown first."); return; }
            if (addedFields.has(inputName)) { alert(`You have already added an upload field for "${labelText}".`); return; }
            addedFields.add(inputName);

            const row = document.createElement('div');
            row.className = 'flex flex-col sm:flex-row gap-3 items-center bg-white p-3 border border-gray-200 rounded-xl shadow-sm relative group';
            row.innerHTML = `
                <div class="flex-1 w-full">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">${labelText}</label>
                    <input type="file" name="${inputName}" accept=".pdf,.doc,.docx" required class="w-full text-xs text-gray-600 border border-gray-300 rounded-lg p-1.5 bg-gray-50 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-bsu-dark file:text-white cursor-pointer hover:file:bg-blue-900">
                </div>
                <button type="button" class="btn-remove-row shrink-0 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-100" title="Remove this field" data-name="${inputName}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(row);
            docSelector.selectedIndex = 0;
            errorMsg.classList.add('hidden');

            row.querySelector('.btn-remove-row').addEventListener('click', function() {
                addedFields.delete(this.getAttribute('data-name'));
                row.remove();
            });
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (container.children.length === 0) { errorMsg.classList.remove('hidden'); return; }
            errorMsg.classList.add('hidden');
            if (confirmModal) confirmModal.classList.remove('hidden');
        });
    }

    if (cancelResubmitBtn) {
        cancelResubmitBtn.addEventListener('click', () => { if (confirmModal) confirmModal.classList.add('hidden'); });
    }

    if (confirmResubmitBtn) {
        confirmResubmitBtn.addEventListener('click', async () => {
            const originalText = confirmResubmitBtn.textContent;
            confirmResubmitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...`;
            confirmResubmitBtn.disabled = true;
            confirmResubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });

                if (response.ok) {
                    if (confirmModal) confirmModal.classList.add('hidden');
                    const jsToast = document.getElementById('js-toast');
                    const jsToastMessage = document.getElementById('js-toast-message');
                    if (jsToast && jsToastMessage) {
                        jsToastMessage.textContent = 'Documents resubmitted successfully!';
                        jsToast.classList.remove('hidden');
                        setTimeout(() => { jsToast.classList.remove('translate-x-full', 'opacity-0'); jsToast.classList.add('translate-x-0', 'opacity-100'); }, 10);
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    const result = await response.json();
                    alert('Error: ' + (result.message || 'Submission failed.'));
                    resetBtn();
                }
            } catch (error) {
                console.error('Upload Error:', error);
                alert('A network error occurred. Please check your connection.');
                resetBtn();
            }

            function resetBtn() {
                confirmResubmitBtn.innerHTML = originalText;
                confirmResubmitBtn.disabled = false;
                confirmResubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});

/* ═══════════════════════════════════════════════════════
   RESUBMISSION FORM MODAL
   ═══════════════════════════════════════════════════════ */
let currentRevisionAppIdx = null;

function openRevisionForm() {
    const btn = document.getElementById('m-revision-btn');
    const idx = parseInt(btn.dataset.appIdx);
    currentRevisionAppIdx = idx;
    const app = APPLICATIONS[idx];

    document.getElementById('rf-title').textContent    = app.title;
    document.getElementById('rf-record').textContent   = 'Record: ' + app.record;
    document.getElementById('rf-reviewer').textContent = 'Please address comments from ' + app.reviewer;

    const flaggedDocs = app.docs;
    document.getElementById('rf-doc-upload-list').innerHTML = flaggedDocs.map((doc, i) => `
        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
            <div class="shrink-0 w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center shadow-sm">${fileIcon(doc.type)}</div>
            <div class="flex-1 min-w-0">
                <p class="text-[12px] font-bold text-bsu-dark truncate">${doc.name}</p>
                <p class="text-[10px] text-gray-400 font-semibold">Previous: ${doc.size}</p>
            </div>
            <label class="shrink-0 flex items-center gap-1.5 bg-bsu-dark hover:bg-blue-900 text-white text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-lg cursor-pointer transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Replace
                <input type="file" class="hidden" accept=".pdf,.doc,.docx" onchange="markReplaced(this, ${i})">
            </label>
        </div>
    `).join('');

    document.querySelectorAll('#revision-form-modal textarea, #revision-form-modal input[type=text]').forEach(el => el.value='');
    document.querySelectorAll('#revision-form-modal input[type=checkbox]').forEach(el => el.checked=false);

    showModal('revision-form-modal');
}

function markReplaced(input, docIdx) {
    if (input.files.length) {
        const label = input.closest('label');
        label.classList.replace('bg-bsu-dark','bg-green-600');
        label.classList.replace('hover:bg-blue-900','hover:bg-green-700');
        label.innerHTML = `<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Replaced <input type="file" class="hidden">`;
    }
}

function closeRevisionForm() { hideModal('revision-form-modal'); }

function submitRevision() {
    const decl = ['rf-decl-1','rf-decl-2','rf-decl-3'].every(id => document.getElementById(id).checked);
    if (!decl) { alert('Please confirm all declarations before submitting.'); return; }

    const app = APPLICATIONS[currentRevisionAppIdx];
    RESUBMISSIONS.push({
        id: 'RESUB-NEW-' + Date.now(),
        sourceId: app.id,
        title: app.title,
        record: app.record,
        status: 'Pending',
        date: new Date().toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'}),
        reviewer: app.reviewer,
        studyType: app.studyType,
        step: 1,
        docs: app.docs.map(d => ({ ...d, name: d.name + ' (Revised)' })),
    });

    hideModal('revision-form-modal');
    hideModal('detail-modal');
    buildCards();
    switchTab('resubmission');

    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 z-[100] bg-green-600 text-white text-[12px] font-black uppercase tracking-wider px-5 py-3.5 rounded-xl shadow-xl flex items-center gap-3';
    toast.innerHTML = `<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Resubmission submitted successfully!`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

/* ═══════════════════════════════════════════════════════
   RESUBMISSION DETAIL MODAL (THE WORKSPACE)
   ═══════════════════════════════════════════════════════ */
let currentResubDocs = [];

function openResubDetail(idx) {
    const rs = RESUBMISSIONS[idx];
    currentResubDocs = rs.docs || [];

    // 1. Populate Metadata & Badge
    document.getElementById('rd-title').textContent      = rs.title;
    document.getElementById('rd-record').textContent     = 'Record: ' + rs.record;
    document.getElementById('rd-date').textContent       = rs.date;
    document.getElementById('rd-study-type').textContent = rs.studyType || '—';
    document.getElementById('rd-doc-count').textContent  = currentResubDocs.length + ' file' + (currentResubDocs.length !== 1 ? 's' : '');

    const statusEl = document.getElementById('rd-status');
    statusEl.textContent = rs.status;
    statusEl.className   = 'inline-flex mt-1 text-[10px] font-black uppercase px-2 py-0.5 rounded-full border tracking-wider ' + (STATUS_STYLE[rs.status] || 'bg-gray-50 text-gray-700 border-gray-200');

    // 2. Determine Dynamic Logic
    const step = Math.min(Math.max(1, rs.step || 1), 5);

    // ADDED 'incorrect' HERE to trigger the resubmission button at the bottom
    const needsMoreRevision = ['Revision Required', 'minor_revision', 'major_revision', 'resubmit', 'returned_for_revision', 'Incorrect'].includes(rs.status);

    const isApproved = ['approved', 'completed', 'Approved', 'Completed'].includes(rs.status);
    const isRejected = ['rejected', 'Rejected'].includes(rs.status);

    // 3. Render 5-step Progress Bar
    RESUB_STEPS.forEach(s => {
        let sCopy = { ...s };

        // DYNAMIC OVERRIDE: Update step labels based on status
        if (rs.status === 'Incorrect' && sCopy.id === 2) {
            // Specific override for Secretariat Validation failure
            sCopy.label = 'Incorrect\nFormat';
            sCopy.customState = 'rejected'; // Gives it a red/warning look if your renderStep supports it
        }
        else if (sCopy.id === 5) {
            // End of pipeline overrides
            if (needsMoreRevision && rs.status !== 'incorrect') {
                sCopy.label = 'Needs\nRevision';
                sCopy.customState = 'revision';
            } else if (isApproved) {
                sCopy.label = 'Approved';
                sCopy.customState = 'approved';
            } else if (isRejected) {
                sCopy.label = 'Rejected';
                sCopy.customState = 'rejected';
            }
        }

        const el = document.getElementById('rd-step-' + sCopy.id);
        if (el) {
            renderStep(el, sCopy, step, 'orange');

            // Add active class for the auto-scroll logic
            if (sCopy.id === step) el.classList.add('step-active');
            else el.classList.remove('step-active');
        }
    });
    document.getElementById('rd-progress-fill').style.width = (Math.max(0, step - 1) / 4 * 100) + '%';

    // 4. Populate Documents
    document.getElementById('rd-documents').innerHTML = currentResubDocs.map(docRow).join('') || '<p class="text-[12px] text-gray-400 italic text-center py-4">No documents available.</p>';

    // 5. RECURSIVE RESUBMISSION TRIGGER
    const nextActionBox = document.getElementById('rd-next-action-container');
    const nextResubBtn = document.getElementById('rd-next-resub-btn');

    // Because 'incorrect' is now in needsMoreRevision, this block executes successfully
    if (needsMoreRevision) {
        if (nextActionBox) nextActionBox.classList.remove('hidden');
        if (nextResubBtn) nextResubBtn.href = `/resubmission-form/${rs.record}`;
    } else {
        if (nextActionBox) nextActionBox.classList.add('hidden');
    }

    // 6. Navigation Back to Original App Hub
    const backBtn = document.getElementById('rd-back-btn');
    const originalAppIdx = APPLICATIONS.findIndex(app => app.record === rs.record);
    if (originalAppIdx !== -1 && backBtn) {
        backBtn.classList.remove('hidden');
        backBtn.onclick = () => {
            hideModal('resub-detail-modal');
            switchTab('application');
            openAppDetail(originalAppIdx);
        };
    } else if (backBtn) {
        backBtn.classList.add('hidden');
    }

    showModal('resub-detail-modal');
    setTimeout(scrollToCurrentStep, 300);
}

function closeResubDetailModal() { hideModal('resub-detail-modal'); }

function downloadResubDocs() {
    if (!currentResubDocs.length) return;
    alert('Downloading all ' + currentResubDocs.length + ' file(s)...\n\n' + currentResubDocs.map(d => d.file).join('\n'));
}

/* ═══════════════════════════════════════════════════════
   PROGRESS STEP RENDERER
   ═══════════════════════════════════════════════════════ */
function renderStep(container, step, currentStep, color) {
    const done    = step.id < currentStep;
    const current = step.id === currentStep;
    const label   = step.label.replace('\n', '<br>');
    const doneClr = color === 'orange' ? 'bg-orange-500 border-orange-500' : 'bg-green-500 border-green-500';
    const doneText = color === 'orange' ? 'text-orange-600' : 'text-green-600';
    let circle, textClass;

    // --- Dynamic Status Overrides (Custom States) ---
    if (step.customState === 'revision') {
        // Yellow Retry Icon
        circle = `<div class="w-8 h-8 rounded-full bg-yellow-500 border-yellow-500 flex items-center justify-center shadow-md ring-4 ring-yellow-100">
                     <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                  </div>`;
        textClass = 'text-yellow-600';
    } else if (step.customState === 'approved') {
        // Green Checkmark Icon
        circle = `<div class="w-8 h-8 rounded-full bg-green-500 border-green-500 flex items-center justify-center shadow-md ring-4 ring-green-100">
                     <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>`;
        textClass = 'text-green-600';
    } else if (step.customState === 'rejected') {
        // Red X Icon
        circle = `<div class="w-8 h-8 rounded-full bg-red-600 border-red-600 flex items-center justify-center shadow-md ring-4 ring-red-100">
                     <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                  </div>`;
        textClass = 'text-red-600';
    }
    // --- Standard Progress Logic ---
    else if (done) {
        circle = `<div class="w-8 h-8 rounded-full ${doneClr} flex items-center justify-center shadow-sm">
                     <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>`;
        textClass = doneText;
    } else if (current) {
        circle = `<div class="w-8 h-8 rounded-full bg-white border-[3px] border-bsu-dark flex items-center justify-center shadow-md ring-4 ring-blue-100">
                     <span class="text-[11px] font-black text-bsu-dark">${step.id}</span>
                  </div>`;
        textClass = 'text-bsu-dark';
    } else {
        circle = `<div class="w-8 h-8 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center shadow-sm">
                     <span class="text-[11px] font-black text-gray-400">${step.id}</span>
                  </div>`;
        textClass = 'text-gray-400';
    }
    container.innerHTML = `${circle}<p class="text-[8px] sm:text-[9px] font-black ${textClass} uppercase tracking-wide text-center leading-tight">${label}</p>`;
}

/* ═══════════════════════════════════════════════════════
   MODAL HELPERS
   ═══════════════════════════════════════════════════════ */
// FIX 2: Added void m.offsetWidth to force reflow and fix silently failing animations
function showModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('hidden');
    void m.offsetWidth;
    m.classList.add('visible');
}
function hideModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('visible');
    setTimeout(() => { m.classList.add('hidden'); }, 200);
}

['detail-modal','revision-form-modal','resub-detail-modal'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', function(e) {
            if (e.target === this) hideModal(id);
        });
    }
});

/* ═══════════════════════════════════════════════════════
   TABS
   ═══════════════════════════════════════════════════════ */
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-brand-red','text-bsu-dark','bg-white');
        btn.classList.add('border-transparent','text-gray-400');
    });
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    const btn = document.getElementById('tab-'+tab);
    if (btn) {
        btn.classList.add('border-brand-red','text-bsu-dark','bg-white');
        btn.classList.remove('border-transparent','text-gray-400');
    }
    const panel = document.getElementById('panel-'+tab);
    if (panel) panel.classList.add('active');
}

/* ═══════════════════════════════════════════════════════
   MISC
   ═══════════════════════════════════════════════════════ */
function triggerDownload(fileUrl, e) {
    e.preventDefault();
    if (!fileUrl || fileUrl === '#' || fileUrl.includes('Unknown')) { alert('This document is not available for preview.'); return; }
    const isPrintView = fileUrl.includes('/print');
    const newTab = window.open(fileUrl, '_blank');
    if (!newTab || newTab.closed || typeof newTab.closed == 'undefined') { alert('Please allow popups for this website to view the document.'); return; }
}

/* hamburger */
const hamburgerBtn = document.getElementById('hamburger-btn');
const mobileMenu   = document.getElementById('mobile-menu');
const hb1=document.getElementById('hb1'), hb2=document.getElementById('hb2'), hb3=document.getElementById('hb3');
let menuOpen = false;

if (hamburgerBtn && mobileMenu) {
    hamburgerBtn.addEventListener('click', () => {
        menuOpen = !menuOpen;
        if (menuOpen) {
            mobileMenu.classList.remove('hidden');
            requestAnimationFrame(() => mobileMenu.classList.replace('hidden-anim','open'));
            hb1.style.transform='translateY(6px) rotate(45deg)'; hb2.style.opacity='0'; hb3.style.transform='translateY(-6px) rotate(-45deg)';
        } else {
            mobileMenu.classList.replace('open','hidden-anim');
            mobileMenu.addEventListener('transitionend', ()=>mobileMenu.classList.add('hidden'), {once:true});
            hb1.style.transform=hb3.style.transform=''; hb2.style.opacity='';
        }
    });
}

/* ── Init ── */
buildCards();

document.addEventListener('DOMContentLoaded', () => {
    // 1. Check if it is their first login
    const isFirstLogin = @json(auth()->user()->is_first_login);

    // 2. Use the unique user key
    const userId = @json(auth()->id());
    const storageKey = 'berc_tutorial_step_' + userId;

    // 3. If they already changed their password, ensure the tour is dead
    if (!isFirstLogin) {
        localStorage.removeItem(storageKey);
        return;
    }

    // 4. Check the tracker state to ensure they didn't skip ahead
    const tourState = localStorage.getItem(storageKey);

    // ONLY trigger if they properly clicked "Next" on the Dashboard
    if (tourState === 'status') {
        const driver = window.driver.js.driver;
        const tour = driver({
            showProgress: true,
            allowClose: false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',

            // When they click the final button on this page:
            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    // Mark the tracker to move to the history page
                    localStorage.setItem(storageKey, 'history');
                    tour.destroy();
                    window.location.href = "{{ route('application.history') }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-status-tabs',
                    popover: {
                        title: 'Track Your Progress',
                        description: 'This is the active pipeline. Here, you can monitor exactly which stage of the review process your brand new protocols are currently in.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tab-resubmission',
                    popover: {
                        title: 'Action Required',
                        description: 'If the committee reviews your document and requests changes, it will be moved to this tab. You will switch here to read their comments and submit your revised files.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    // No element provided = centers horizontally/vertically on screen
                    popover: {
                        title: 'Detailed Feedback',
                        description: 'Whenever you have active protocols, clicking on a row opens a detailed timeline. You will see exactly whose desk it is on and read any required revisions.',
                        side: "top",
                        align: 'center'
                    }
                },
                {
                    // No element provided
                    popover: {
                        title: 'Next Stop: Application History',
                        description: 'Once an application gets a final Approval or Rejection, it leaves this active pipeline and is archived. Let\'s check out the History page next.',
                        side: "bottom",
                        align: 'center',
                        doneBtnText: 'Next Page →' // Renames the 'Done' button
                    }
                }
            ]
        });

        // Start the tour
        tour.drive();
    }
});
</script>

<script>
function restartStatusTutorial() {
    const userId = @json(auth()->id());
    const storageKey = 'berc_tutorial_step_' + userId;

    // erase old progress
    localStorage.removeItem(storageKey);

    // set current page as fresh start
    localStorage.setItem(storageKey, 'status');

    const driver = window.driver.js.driver;

    const tour = driver({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(33, 60, 113, 0.75)',
        nextBtnText: 'Next →',
        prevBtnText: '← Back',

        onDestroyStarted: () => {
            if (!tour.hasNextStep()) {
                localStorage.setItem(storageKey, 'history');
                tour.destroy();
                window.location.href = "{{ route('application.history') }}";
            } else {
                tour.destroy();
            }
        },

        steps: [
            {
                element: '#tour-status-tabs',
                popover: {
                    title: 'Track Your Progress',
                    description: 'This page shows all active protocols currently under review.',
                    side: "bottom",
                    align: "start"
                }
            },
            {
                element: '#tab-application',
                popover: {
                    title: 'Application Status',
                    description: 'Submitted protocols appear here while under evaluation.',
                    side: "bottom",
                    align: "center"
                }
            },
            {
                element: '#tab-resubmission',
                popover: {
                    title: 'Resubmission Status',
                    description: 'Protocols needing revisions move here.',
                    side: "bottom",
                    align: "center"
                }
            },
            {
                popover: {
                    title: 'Detailed Timeline',
                    description: 'Click a protocol row anytime to open full progress details.',
                    side: "top",
                    align: "center"
                }
            },
            {
                popover: {
                    title: 'Next Page',
                    description: 'Finished applications are stored in Application History.',
                    side: "bottom",
                    align: "center",
                    doneBtnText: 'Next Page →'
                }
            }
        ]
    });

    tour.drive(); // ALWAYS starts at step 1
}
</script>
</body>
</html>
