<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/functions.js') }}" defer></script>

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
        body { font-family: 'Inter', sans-serif; }

        /* Mobile nav drawer */
        #mobile-menu { transition: transform 0.3s ease, opacity 0.3s ease; }
        #mobile-menu.hidden { transform: translateY(-10px); opacity: 0; pointer-events: none; }
        #mobile-menu.open { transform: translateY(0); opacity: 1; pointer-events: auto; }

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

    <header class="bg-white sticky top-0 z-50 shadow-sm">

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
                <div class="date-block">
                    <div class="time" id="clock-m">--:-- -- | -------</div>
                    <div class="date" id="date-m">--/--/----</div>
                </div>

                <button id="hamburger-btn" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 gap-1.5 shrink-0" aria-label="Toggle menu">
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb1"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb2"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb3"></span>
                </button>
            </div>
        </div>

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
                            onclick="startManualTutorial()"
                            class="flex items-center gap-2 transition-all hover:-translate-y-0.5 text-gray-500 hover:text-bsu-dark">
                        <svg class="w-4 h-4 shrink-0 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 1.823-2 3.272-2 1.933 0 3.5 1.343 3.5 3 0 1.305-.973 2.416-2.333 2.83-.727.221-1.167.874-1.167 1.67M12 18h.01M12 3a9 9 0 100 18 9 9 0 000-18z" />
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

        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-[#FCFCFC]">
            <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">
                <a href="#" class="flex items-center space-x-3 text-bsu-dark px-5 py-3.5">
                    <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Official Documents</span>
                </a>
                <a href="#about-berc" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>About BERC</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                    <span></span>
                </a>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <a href="{{ route('settings') }}" class="hover:text-bsu-dark transition-colors">Settings</a>

                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>

                    <button type="button" onclick="showLogoutModal()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all hover:-translate-y-1 hover:shadow-md active:scale-95 shadow-sm">
                        Sign Out
                    </button>
                </div>
                <div class="px-5 py-3 bg-white">
                    <p class="text-[11px] font-bold text-bsu-dark uppercase tracking-wide">12:00 AM | Monday</p>
                    <p class="text-[10px] font-bold text-brand-red">16/02/2026</p>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6">

        <div id="tour-profile" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/background.jpg') }}" alt="Background" class="w-full h-full object-cover">
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

        <div id="tour-documents" class="mb-6 sm:mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <h2 class="text-sm font-black text-brand-red uppercase tracking-wider whitespace-nowrap">Requirements</h2>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
                <h3 class="text-sm font-bold text-bsu-dark uppercase mb-4 tracking-wide">Official Documents</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">

                    <a id="tour-review-btn" href="{{ route('review.form') }}"
                       class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                        <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight">Research Review Application</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Forms</p>
                        </div>
                    </a>

                    <a id="tour-status-btn" href="{{ route('application.status') }}"
                       class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                        <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight">View Application Status</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Application Status</p>
                        </div>
                    </a>

                    <a id="tour-history-btn" href="{{ route('application.history') }}"
                       class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm sm:col-span-2 md:col-span-1">
                        <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight">Application History</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">History</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-200 mt-10 sm:mt-12 py-8 sm:py-10">
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Check if it is their first login
        const isFirstLogin = @json(auth()->user()->is_first_login);

        // 2. Create a memory key unique to this specific user ID
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        // 3. If they are no longer on their first login, wipe memory and abort
        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        // Trigger the Dashboard tour ONLY if they are just starting
        if (!tourState || tourState === 'dashboard') {
            const driver = window.driver.js.driver;
            const tour = driver({
                showProgress: true,
                allowClose: false, // Force them to finish or click next
                overlayColor: 'rgba(33, 60, 113, 0.75)', // BSU dark overlay

                nextBtnText: 'Next &rarr;',
                prevBtnText: '&larr; Back',

                // When they click the final button on this page:
                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        localStorage.setItem(storageKey, 'status'); // Save state for the next page
                        tour.destroy();
                        // Automatically take them to the Status page to continue the tour
                        window.location.href = "{{ route('application.status') }}";
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-profile',
                        popover: {
                            title: 'Welcome to BERC!',
                            description: 'This is your main dashboard. Verify your account details and current role here.',
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-review-btn',
                        popover: {
                            title: '1. Start an Application',
                            description: 'Click here to fill out the Research Review form and submit your new protocol to the committee.',
                            side: "bottom",
                            align: 'center'
                        }
                    },
                    {
                        element: '#tour-status-btn',
                        popover: {
                            title: '2. Track Active Protocols',
                            description: 'Once submitted, you will monitor the real-time status of your applications and handle any required resubmissions here.',
                            side: "bottom",
                            align: 'center'
                        }
                    },
                    {
                        element: '#tour-history-btn',
                        popover: {
                            title: '3. View Past Records',
                            description: 'Access all your finalized applications (Approved or Rejected) and download your official Decision Letters from the History hub.',
                            side: "bottom",
                            align: 'center',
                            doneBtnText: 'Next Page →' // Sends them to the Status page to continue
                        }
                    }
                ]
            });

            tour.drive();
        }
    });
</script>

<script>
    function startManualTutorial() {
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        localStorage.setItem(storageKey, 'dashboard');

        const driver = window.driver.js.driver;

        const tour = driver({
            showProgress: true,
            allowClose: true,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next &rarr;',
            prevBtnText: '&larr; Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    localStorage.setItem(storageKey, 'status');
                    tour.destroy();
                    window.location.href = "{{ route('application.status') }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-profile',
                    popover: {
                        title: 'Welcome to BERC!',
                        description: 'This is your main dashboard. Verify your account details and current role here.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-review-btn',
                    popover: {
                        title: '1. Start an Application',
                        description: 'Click here to fill out the Research Review form and submit your new protocol to the committee.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-status-btn',
                    popover: {
                        title: '2. Track Active Protocols',
                        description: 'Once submitted, you will monitor the real-time status of your applications and handle any required resubmissions here.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-history-btn',
                    popover: {
                        title: '3. View Past Records',
                        description: 'Access all your finalized applications and download your official Decision Letters from the History hub.',
                        side: "bottom",
                        align: 'center',
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        tour.drive();
    }
</script>
</body>
</html>
