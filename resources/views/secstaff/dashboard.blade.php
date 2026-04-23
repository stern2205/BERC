<?php
$currentMonth = date('F Y');
$currentYear  = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee – Secretarial Staff</title>
    <link rel="icon" type="image/png" href="/logo/BERC.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        .nav-tab-active { border-bottom: 3px solid #D32F2F; color: #213C71; }

        .kpi-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
        }
        .kpi-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .kpi-value {
            font-size: 30px;
            font-weight: 900;
            color: #111827;
            line-height: 1;
        }
        .kpi-value.red { color: #D32F2F; }

        .notif-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
        }
        .notif-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #1e3a5f;
        }
        .notif-body {
            font-size: 11px;
            font-weight: 600;
            color: #D32F2F;
            margin-top: 2px;
        }

        .chart-panel-title {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: #111827;
        }

        .pill-select {
            appearance: none;
            -webkit-appearance: none;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 3px 22px 3px 10px;
            color: #374151;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 7px center;
            cursor: pointer;
            outline: none;
        }
        .pill-select:focus { border-color: #213C71; }

        .leg { display:inline-flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#374151; }
        .leg-dot { width:12px; height:10px; border-radius:2px; flex-shrink:0; }

        .btn-print {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px 16px;
            background: white;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #f9fafb; border-color: #213C71; color: #213C71; }

        .about-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .about-card:hover { border-color: #213C71; box-shadow: 0 2px 8px rgba(33,60,113,0.08); }

        .analytics-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .analytics-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            border-bottom: 1px solid #f3f4f6;
            background: linear-gradient(to right, #f8faff, #f9fafb);
        }
        .analytics-card-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .analytics-card-body {
            padding: 16px;
        }
        .accent-dot-blue,
        .accent-dot-red {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .accent-dot-blue { background: #213C71; }
        .accent-dot-red { background: #D32F2F; }

        .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
        .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
        .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
        .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
        .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
        .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
        .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
        .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }

        @media print {
            body * {
                visibility: hidden !important;
            }

            #analytics,
            #analytics * {
                visibility: visible !important;
            }

            #analytics {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .btn-print {
                display: none !important;
            }

            canvas {
                max-width: 100% !important;
                height: auto !important;
            }

            .analytics-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>

<header class="bg-[#ffffff] border-b border-gray-200 sticky top-0 z-50">
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
        <div class="flex items-center gap-3">
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
    </div>

    <div class="border-t border-b border-gray-200 bg-[#FCFCFC] hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-gray-500">
            <div class="flex space-x-1">
                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('dashboard') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('secstaff.applications') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.applications') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.applications') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Applications</span>
                </a>

                <a href="{{ route('secstaff.calendar') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.calendar') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.calendar') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Calendar</span>
                </a>

                <a href="{{ route('secstaff.history') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.history') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.history') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 1 0 .5-3M3 4v4h4"/>
                    </svg>
                    <span>History</span>
                </a>

                <a href="{{ route('secstaff.payment_settings') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest transition border-b-[3px]
                {{ request()->routeIs('secstaff.payment_settings') ? 'text-bsu-dark border-brand-red bg-white' : 'text-gray-500 border-transparent hover:text-bsu-dark hover:border-brand-red' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('secstaff.payment_settings') ? 'text-bsu-dark' : 'text-brand-red' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Payment Settings</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-6 py-4 hover:text-bsu-dark transition-colors">
                    <span>    </span>
                </a>
            </div>

            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
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
                <span>Dashboard</span>
            </a>
            <a href="{{ route('secstaff.applications') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Applications</span>
            </a>
            <a href="{{ route('secstaff.calendar') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Calendar</span>
            </a>
            <a href="{{ route('secstaff.history') }}" class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:text-bsu-dark transition border-b-[3px] border-transparent hover:border-brand-red">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 1 0 .5-3M3 4v4h4"/>
                </svg>
                History
            </a>
            <a href="{{ route('secstaff.payment_settings') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Payment Settings</span>
            </a>
            <div class="flex items-center space-x-6 border-l border-gray-200 pl-6 py-4">
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
</header>

<script>
    var secHamburger = document.getElementById('sec-hamburger');
    var secMenu = document.getElementById('sec-mobile-menu');
    var menuOpen = false;
    if (secHamburger && secMenu) {
        secHamburger.addEventListener('click', function() {
            menuOpen = !menuOpen;
            secMenu.classList.toggle('hidden', !menuOpen);
            document.getElementById('hb1').style.transform = menuOpen ? 'translateY(7px) rotate(45deg)' : '';
            document.getElementById('hb2').style.opacity   = menuOpen ? '0' : '1';
            document.getElementById('hb3').style.transform = menuOpen ? 'translateY(-7px) rotate(-45deg)' : '';
        });
    }
    function updateSecClock() {
        var now = new Date(), days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var h = now.getHours(), m = now.getMinutes(), ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var t = (h<10?'0':'')+h+':'+(m<10?'0':'')+m+' '+ampm+' | '+days[now.getDay()].toUpperCase();
        var el = document.getElementById('sec-clock'), elm = document.getElementById('sec-clock-m');
        if (el) el.textContent = t;
        if (elm) elm.textContent = t;
    }
    updateSecClock(); setInterval(updateSecClock, 1000);
</script>

<div class="max-w-7xl mx-auto px-4 py-4">

    <div id="tour-profile" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-bsu-dark/70"></div>
        </div>
        <div class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
            <div class="shrink-0">
                <div class="bg-white/20 backdrop-blur-sm p-1 rounded-2xl border border-white/20 shadow-lg">
                    <img src="{{ asset($user->profile_image ?? 'profiles/default.png') }}" alt="Student Photo"
                            class="w-16 h-16 sm:w-24 sm:h-24 object-cover bg-gray-300 rounded-xl">
                </div>
            </div>
            <div class="flex-1 text-white text-center sm:text-left">
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">Secretarial Staff</p>
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight">{{ $user->name }}</h2>
                <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Account ID</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Email Address</p>
                        <p class="text-xs font-bold tracking-wide">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 notif-banner">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="notif-title">Important Notification!</p>
            <p class="notif-body">
                There are <span>{{ $newToday == 0 ? 'no' : $newToday }}</span> new applications today and
                <span>{{ $notScreened == 0 ? 'no' : $notScreened }}</span> unscreened applications requiring attention.
            </p>
        </div>
    </div>

    <div id="tour-kpi" class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="kpi-card">
            <p class="kpi-label">New Applications Today</p>
            <p class="kpi-value">{{ $newToday }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">{{ date('F Y') }}</p>
            <p class="kpi-value">{{ $totalMonth }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Not Yet Screened</p>
            <p class="kpi-value red">{{ $notScreened }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Total Applications (All Time)</p>
            <p class="kpi-value">{{ $totalAllTime }}</p>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center mt-8 mb-4 gap-3">
            <div class="w-1 h-5 rounded-full bg-brand-red"></div>
            <h2 class="text-[12px] font-black uppercase tracking-widest text-bsu-dark">Protocol Management</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

                <a id="tour-sec-apps" href="{{ route('secstaff.applications') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $notScreened }}</span>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Applications</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">View and Screen Applications</p>
                    </div>
                </a>

                <a id="tour-sec-calendar" href="{{ route('secstaff.calendar') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Calendar</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">View Calendar Deadlines</p>
                    </div>
                </a>

                <a id="tour-sec-history" href="{{ route('secstaff.history') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">History</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">View Application History</p>
                    </div>
                </a>

                <a id="tour-sec-payment" href="{{ route('secstaff.payment_settings') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Payment Methods</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Add/Change/Toggle Payment Methods</p>
                    </div>
                </a>

            </div>
        </div>
    </div>

    <div id="tour-analytics" class="mt-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-1 h-5 rounded-full bg-brand-red"></div>
                <h2 class="text-[12px] font-black uppercase tracking-widest text-bsu-dark">Analytics Overview</h2>
                <span class="text-[9px] font-black bg-bsu-dark text-white px-2.5 py-0.5 rounded-full uppercase tracking-wide">{{ date('Y') }}</span>
            </div>
            <button class="btn-print" onclick="window.print()">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Analytics
            </button>
        </div>

        <div id="analytics">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <div class="analytics-card-header-left">
                            <div class="accent-dot-blue"></div>
                            <p class="chart-panel-title">Application Status Analytics</p>
                        </div>
                        <select class="pill-select" id="statusTimeFilter" onchange="updateStatusChart()"></select>
                    </div>
                    <div class="analytics-card-body">
                        <div style="height:200px;"><canvas id="statusChart"></canvas></div>
                        <div class="flex flex-wrap gap-x-4 gap-y-2 mt-3 pt-3 border-t border-gray-100">
                            <span class="leg"><span class="leg-dot" style="background:#213C71;"></span>Pending</span>
                            <span class="leg"><span class="leg-dot" style="background:#3B6CC7;"></span>For Review</span>
                            <span class="leg"><span class="leg-dot" style="background:#16A34A;"></span>Approved</span>
                            <span class="leg"><span class="leg-dot" style="background:#D97706;"></span>For Revision</span>
                            <span class="leg"><span class="leg-dot" style="background:#D32F2F;"></span>Rejected</span>
                        </div>
                    </div>
                </div>

                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <div class="analytics-card-header-left">
                            <div class="accent-dot-blue"></div>
                            <p class="chart-panel-title">Monthly Application Trend</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <select class="pill-select" id="trendYearFilter" onchange="updateTrendChart()"></select>
                        </div>
                    </div>
                    <div class="analytics-card-body">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="leg">
                                <span class="leg-dot" style="background:#213C71; width:20px; border-radius:3px;"></span>
                                Applications by Month
                            </span>
                        </div>
                        <div style="height:195px;"><canvas id="trendChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="analytics-card mb-3">
                <div class="analytics-card-header">
                    <div class="analytics-card-header-left">
                        <div class="accent-dot-red"></div>
                        <p class="chart-panel-title">Applications per Type of Study</p>
                    </div>
                    <select class="pill-select" id="typeStudyYearFilter" onchange="updateTypeStudyChart()"></select>
                </div>
                <div class="analytics-card-body">
                    <div class="w-full overflow-x-auto">
                        <div style="min-width:400px; height:255px;">
                            <canvas id="typeStudyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <div class="analytics-card-header-left">
                            <div class="accent-dot-blue"></div>
                            <p class="chart-panel-title">Applications per Year</p>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">Dynamic</span>
                    </div>
                    <div class="analytics-card-body">
                        <div style="height:180px;"><canvas id="appYearChart"></canvas></div>
                    </div>
                </div>

                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <div class="analytics-card-header-left">
                            <div class="accent-dot-red"></div>
                            <p class="chart-panel-title">Average Revisions per Application</p>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">Dynamic</span>
                    </div>
                    <div class="analytics-card-body">
                        <div style="height:180px;"><canvas id="avgRevYearChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white border-t border-gray-200 mt-4 py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} Batangas State University
        </p>
        <p class="text-xs text-red-600 mt-2 italic">The National Engineering University</p>
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
const BSU   = '#213C71';
const RED   = '#D32F2F';
const BLUE2 = '#3B6CC7';
const STEEL = '#4E7AC7';
const TEAL  = '#1A7A8A';
const AMBER = '#D97706';
const GREEN = '#16A34A';
const PINK  = '#BE185D';
const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#6b7280';

const sharedTooltip = {
    backgroundColor: '#1e293b',
    titleColor: '#f1f5f9',
    bodyColor: '#cbd5e1',
    borderColor: '#334155',
    borderWidth: 1,
    padding: 10,
    cornerRadius: 6,
    titleFont: { weight: '700', size: 11 },
    bodyFont: { size: 11 },
};

const chartData = @json($chartData ?? []);

const STATUS_LABELS = ['Pending', 'For Review', 'Approved', 'For Revision', 'Rejected'];
const TYPE_STUDY_LABELS = [
    'Clinical Trial (Sponsored)',
    'Clinical Trials (Researcher-initiated)',
    'Health Operations Research',
    'Social-Behavioral Research',
    'Public Health-Epidemiologic',
    'Biomedical Research (Retrospective/Prospective)',
    'Stem Cell Research',
    'Others'
];
const TYPE_STUDY_COLORS = [BSU, BLUE2, STEEL, TEAL, AMBER, GREEN, PINK, RED];

const availableYears = (chartData.appYear?.years || []).map(String).sort((a, b) => Number(b) - Number(a));
const statusYears = ['all', ...availableYears];
const typeYears = ['all', ...availableYears];

function populateSelect(id, values, defaultValue, formatter = v => v === 'all' ? 'All Time' : v) {
    const select = document.getElementById(id);
    if (!select) return;
    select.innerHTML = '';
    values.forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = formatter(value);
        if (String(value) === String(defaultValue)) option.selected = true;
        select.appendChild(option);
    });
}

populateSelect('statusTimeFilter', statusYears, availableYears[0] || 'all', v => v === 'all' ? 'All Time' : v);
populateSelect('trendYearFilter', ['all', ...availableYears], 'all', v => v === 'all' ? 'All Years' : v);
populateSelect('typeStudyYearFilter', typeYears, availableYears[0] || 'all', v => v === 'all' ? 'All Years' : v);

const ctxStatus = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctxStatus, {
    type: 'bar',
    data: {
        labels: STATUS_LABELS,
        datasets: [{
            label: 'Applications',
            data: chartData.status?.byYear?.[(availableYears[0] || 'all')] || chartData.status?.byYear?.all || [0,0,0,0,0],
            backgroundColor: [BSU, BLUE2, GREEN, AMBER, RED],
            borderRadius: 5,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.x} application(s)` } }
        },
        scales: {
            x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } }, border: { display: false } },
            y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' } }, border: { display: false } }
        }
    }
});
function updateStatusChart() {
    const yr = document.getElementById('statusTimeFilter').value;
    statusChart.data.datasets[0].data = chartData.status?.byYear?.[yr] || [0,0,0,0,0];
    statusChart.update('active');
}

const ctxTrend = document.getElementById('trendChart').getContext('2d');
const gradTrend = ctxTrend.createLinearGradient(0, 0, 0, 195);
gradTrend.addColorStop(0, 'rgba(33,60,113,0.15)');
gradTrend.addColorStop(1, 'rgba(33,60,113,0)');
const trendChart = new Chart(ctxTrend, {
    type: 'line',
    data: {
        labels: MONTHS,
        datasets: [{
            label: 'Applications',
            data: chartData.appMonth?.all || Array(12).fill(0),
            borderColor: BSU,
            backgroundColor: gradTrend,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#fff',
            pointBorderColor: BSU,
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 2.5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } }
        },
        scales: {
            x: { grid: { color: '#f9fafb' }, ticks: { font: { size: 10 } }, border: { display: false } },
            y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } }
        }
    }
});
function updateTrendChart() {
    const yr = document.getElementById('trendYearFilter').value;
    trendChart.data.datasets[0].data = chartData.appMonth?.[yr] || chartData.appMonth?.all || Array(12).fill(0);
    trendChart.update('active');
}

const ctxType = document.getElementById('typeStudyChart').getContext('2d');
const typeStudyChart = new Chart(ctxType, {
    type: 'bar',
    data: {
        labels: TYPE_STUDY_LABELS,
        datasets: [{
            label: 'Applications',
            data: chartData.typeStudy?.byYear?.[(availableYears[0] || 'all')] || chartData.typeStudy?.byYear?.all || Array(8).fill(0),
            backgroundColor: TYPE_STUDY_COLORS,
            borderRadius: 5,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.x} application(s)` } }
        },
        scales: {
            x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } }, border: { display: false } },
            y: {
                grid: { display: false },
                ticks: {
                    font: { size: 10, weight: '600' },
                    callback: function(val) {
                        const lbl = this.getLabelForValue(val);
                        return lbl.length > 34 ? lbl.substring(0, 32) + '…' : lbl;
                    }
                },
                border: { display: false }
            }
        }
    }
});
function updateTypeStudyChart() {
    const yr = document.getElementById('typeStudyYearFilter').value;
    typeStudyChart.data.datasets[0].data = chartData.typeStudy?.byYear?.[yr] || Array(8).fill(0);
    typeStudyChart.update('active');
}

new Chart(document.getElementById('appYearChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartData.appYear?.years || [],
        datasets: [{
            label: 'Applications',
            data: chartData.appYear?.totals || [],
            backgroundColor: (chartData.appYear?.years || []).map((y, i, arr) =>
                i === arr.length - 1 ? 'rgba(33,60,113,0.35)' : BSU
            ),
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } }
        }
    }
});

new Chart(document.getElementById('avgRevYearChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartData.avgRevYear?.years || [],
        datasets: [{
            label: 'Average Revisions',
            data: chartData.avgRevYear?.averages || [],
            backgroundColor: (chartData.avgRevYear?.years || []).map((y, i, arr) =>
                i === arr.length - 1 ? 'rgba(211,47,47,0.35)' : RED
            ),
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} avg. revision(s)` } }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } }
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const isFirstLogin = @json(auth()->user()->is_first_login);
    const userId = @json(auth()->id());
    const storageKey = 'berc_tutorial_step_' + userId;

    if (!isFirstLogin) {
        localStorage.removeItem(storageKey);
        return;
    }

    const tourState = localStorage.getItem(storageKey);

    if (!tourState || tourState === 'dashboard') {
        if (typeof window.driver === 'undefined') {
            console.error("Driver.js is missing on the Dashboard!");
            return;
        }

        const driver = window.driver.js.driver;
        const tour = driver({
            showProgress: true,
            allowClose: false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    localStorage.setItem(storageKey, 'sec_applications');
                    tour.destroy();
                    window.location.href = "{{ route('secstaff.applications') }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-profile',
                    popover: {
                        title: 'Welcome Secretarial Staff!',
                        description: 'This is your main dashboard. From here, you manage the flow of all incoming research protocols.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-kpi',
                    popover: {
                        title: 'At-a-Glance Metrics',
                        description: 'Keep track of the volume of incoming applications and immediately spot protocols that need screening.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-analytics',
                    popover: {
                        title: 'Data & Analytics',
                        description: 'Review historical trends, filter by year, and print the analytics board for meetings and reports.',
                        side: "top",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-sec-apps',
                    popover: {
                        title: 'Next Stop: Screen Applications',
                        description: 'Let’s head over to the Applications panel, where you will evaluate incoming submissions.',
                        side: "bottom",
                        align: 'center',
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        tour.drive();
    }
});
</script>

</body>
</html>
