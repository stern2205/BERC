<?php
$currentMonth = date('F Y');
$currentYear  = date('Y');
$currentMonthNum = (int)date('n');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee – Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/functions.js') }}" defer></script>
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

        /* ── Legend ── */
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
        }
        .legend-box {
            width: 13px;
            height: 13px;
            border-radius: 3px;
            border: 1.5px solid;
            flex-shrink: 0;
        }
        .lbox-deadline   { background: #fee2e2; border-color: #fca5a5; }
        .lbox-secretariat{ background: #dbeafe; border-color: #93c5fd; }
        .lbox-reviewer   { background: #fef9c3; border-color: #fde047; }
        .lbox-assigned   { background: #dcfce7; border-color: #86efac; }
        .lbox-meeting    { background: #fef3c7; border-color: #fcd34d; }
        .lbox-cutoff     { background: #f3e8ff; border-color: #d8b4fe; }

        /* ── Calendar outer card ── */
        .cal-outer {
            background: white;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            overflow: hidden;
            width: 100%;
        }

        .cal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px 10px;
            background: white;
            flex-wrap: wrap;
            gap: 8px;
        }
        .cal-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cal-icon { color: #D32F2F; flex-shrink: 0; }
        .cal-title {
            font-size: 16px;
            font-weight: 900;
            color: #213C71;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .cal-selects { display: flex; gap: 8px; }
        .cal-select {
            appearance: none;
            -webkit-appearance: none;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 8px center;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            padding: 5px 26px 5px 10px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            min-width: 95px;
        }
        .cal-select:focus { outline: none; border-color: #213C71; }

        .cal-grid-card {
            background: #e8eef8;
            margin: 0 12px 12px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #d1daf0;
        }

        .cal-day-headers {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }
        .cal-day-hdr {
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #374151;
            padding: 8px 0 6px;
            min-width: 0;
        }

        .cal-grid-new {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
            padding: 0 4px 4px;
        }
        .cal-cell-new {
            background: white;
            border-radius: 6px;
            min-height: 96px;
            padding: 6px 6px 4px;
            cursor: pointer;
            transition: box-shadow 0.12s;
            border: 1.5px solid transparent;
            min-width: 0;
            overflow: hidden;
        }
        .cal-cell-new:hover {
            box-shadow: 0 2px 6px rgba(33,60,113,0.13);
            border-color: #bfcfff;
        }
        .cal-cell-new.other-month-new { background: #f0f3fa; opacity: 0.6; }
        .cal-cell-new.meeting-day-cell { background: #fffde7; }
        .cal-cell-new.has-deadline { background: #fff5f5; }
        .cal-cell-new.cutoff-day  { background: #faf5ff; border: 1.5px solid #d8b4fe !important; }

        /* Purple CUTOFF badge */
        .cutoff-badge {
            display: inline-flex;
            align-items: center;
            background: #7c3aed;
            color: white;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 999px;
            margin-bottom: 3px;
        }
        /* Deferred (missed cut-off) pill */
        .deferred-badge {
            display: inline-flex;
            align-items: center;
            background: #5b21b6;
            color: white;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 999px;
            margin-bottom: 3px;
        }
        /* Vertical cut-off rule shown inside the grid */
        .cutoff-banner {
            grid-column: 1 / -1;
            background: linear-gradient(90deg, #7c3aed10, #7c3aed25, #7c3aed10);
            border-top: 2px dashed #a855f7;
            border-bottom: 2px dashed #a855f7;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 6px;
            margin: 1px 0;
            font-size: 9px;
            font-weight: 800;
            color: #6b21a8;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            text-align: center;
            flex-wrap: wrap;
        }
        .cal-cell-new.selected-cell {
            border-color: #213C71 !important;
            box-shadow: 0 0 0 2px rgba(33,60,113,0.18);
        }

        .day-num-new {
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
            line-height: 1;
        }
        .day-num-new.other-month-num { color: #9ca3af; }
        .day-num-today {
            background: #1e3a8a;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        /* Orange MEETING badge */
        .meeting-badge {
            display: inline-flex;
            align-items: center;
            background: #f97316;
            color: white;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 999px;
            margin-bottom: 3px;
        }
        /* Red DEADLINE badge */
        .deadline-badge {
            display: inline-flex;
            align-items: center;
            background: #dc2626;
            color: white;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 999px;
            margin-bottom: 3px;
        }

        /* Event chips */
        .ev-chip-new {
            display: block;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 500;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border: 1px solid transparent;
            max-width: 100%;
        }
        /* Location dot prefix added via JS */
        .ev-chip-secretariat { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
        .ev-chip-reviewer    { background: #fefce8; color: #854d0e; border-color: #fde68a; }
        .ev-chip-assigned    { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .ev-chip-deadline    { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
        .ev-chip-meeting     { background: #fef9c3; color: #713f12; border-color: #fef08a; }
        .ev-chip-cutoff      { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
        .ev-chip-deferred    { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }

        /* ── Sidebar ── */
        .side-event-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .side-event-date-box {
            min-width: 38px;
            text-align: center;
            background: #eff4ff;
            border-radius: 7px;
            padding: 5px 4px 4px;
        }
        .side-event-date-box.meeting  { background: #fef3c7; }
        .side-event-date-box.deadline { background: #fee2e2; }
        .side-event-date-box.reviewer { background: #fefce8; }
        .side-event-date-box.assigned { background: #dcfce7; }
        .side-event-date-box.cutoff   { background: #f3e8ff; }
        .side-event-date-box.deferred { background: #ede9fe; }
        .side-event-date-box .month-lbl {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }
        .side-event-date-box .day-lbl {
            font-size: 18px;
            font-weight: 900;
            line-height: 1;
            color: #213C71;
        }
        .side-event-date-box.meeting  .day-lbl { color: #92400e; }
        .side-event-date-box.deadline .day-lbl { color: #991b1b; }
        .side-event-date-box.reviewer .day-lbl { color: #854d0e; }
        .side-event-date-box.assigned .day-lbl { color: #166534; }
        .side-event-date-box.cutoff   .day-lbl { color: #6b21a8; }
        .side-event-date-box.deferred .day-lbl { color: #5b21b6; }

        /* Location badge pill inside sidebar */
        .loc-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 3px;
        }
        .loc-pill-sec  { background: #dbeafe; color: #1e40af; }
        .loc-pill-rev  { background: #fef9c3; color: #92400e; }
        .loc-pill-asgn { background: #dcfce7; color: #166534; }
        .loc-pill-meet { background: #ffedd5; color: #9a3412; }
        .loc-pill-dead { background: #fee2e2; color: #991b1b; }
        .loc-pill-cut  { background: #f3e8ff; color: #6b21a8; }
        .loc-pill-def  { background: #ede9fe; color: #5b21b6; }

        /* Filter tabs */
        .filter-btn {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            border: 1.5px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .filter-btn:hover { border-color: #213C71; color: #213C71; }
        .filter-btn.active { background: #213C71; color: white; border-color: #213C71; }
        .filter-btn.active-red { background: #dc2626; color: white; border-color: #dc2626; }

        @media (max-width: 768px) {
            .cal-cell-new  { min-height: 50px; }
            .ev-chip-new   { display: none; }
            .meeting-badge, .deadline-badge { display: none; }
            .event-dot-mobile {
                width: 5px; height: 5px; border-radius: 50%;
                display: inline-block; margin-right: 2px;
            }
        }
        @media print {
            header, footer, .no-print { display: none !important; }
            .cal-cell-new { min-height: 70px; }
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- ═══ HEADER ═══ -->
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

                <!-- Hamburger (visible on small screens) -->
                <button id="hamburger-btn" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg border border-gray-200 gap-1.5 shrink-0" aria-label="Toggle menu">
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb1"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb2"></span>
                    <span class="block w-5 h-0.5 bg-bsu-dark rounded transition-all" id="hb3"></span>
                </button>
            </div>
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
</header>

<script>
    var secHamburger = document.getElementById('sec-hamburger');
    var secMenu = document.getElementById('sec-mobile-menu');
    var menuOpen = false;
    if (secHamburger) {
        secHamburger.addEventListener('click', function() {
            menuOpen = !menuOpen;
            if (secMenu) secMenu.classList.toggle('hidden', !menuOpen);
        });
    }
    function updateSecClock() {
        var now = new Date(), days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var h = now.getHours(), m = now.getMinutes(), ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var t = (h<10?'0':'')+h+':'+(m<10?'0':'')+m+' '+ampm+' | '+days[now.getDay()].toUpperCase();
        var el = document.getElementById('sec-clock');
        if (el) el.textContent = t;
    }
    updateSecClock(); setInterval(updateSecClock, 1000);
</script>

<!-- ═══ MAIN ═══ -->
<div class="max-w-7xl mx-auto px-4 py-4">

    <!-- ── Profile Banner ── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="" class="w-full h-full object-cover">
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
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">{{ $user->role }}</p>
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

    <!-- ── Page Header ── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-1 h-6 rounded-full bg-brand-red"></div>
            <div>
                <h2 class="text-[13px] font-black uppercase tracking-widest text-bsu-dark">Calendar</h2>
                <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Application deadlines, assignments &amp; committee meetings</p>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-3">
            <span class="legend-item"><span class="legend-box lbox-deadline"></span>Deadline</span>
            <span class="legend-item"><span class="legend-box lbox-secretariat"></span>At Secretariat</span>
            <span class="legend-item"><span class="legend-box lbox-reviewer"></span>At Reviewer</span>
            <span class="legend-item"><span class="legend-box lbox-assigned"></span>Assigned</span>
            <span class="legend-item"><span class="legend-box lbox-meeting"></span>Meeting Day</span>
            <span class="legend-item"><span class="legend-box lbox-cutoff"></span>Cut-off Date</span>
        </div>
    </div>

    <!-- ── Filter Row ── -->
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mr-1">Filter:</span>
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="deadline">
            <span style="color:#dc2626">●</span> Deadlines
        </button>
        <button class="filter-btn" data-filter="secretariat">
            <span style="color:#1e40af">●</span> At Secretariat
        </button>
        <button class="filter-btn" data-filter="reviewer">
            <span style="color:#92400e">●</span> At Reviewer
        </button>
        <button class="filter-btn" data-filter="assigned">
            <span style="color:#166534">●</span> Assigned
        </button>
        <button class="filter-btn" data-filter="meeting">
            <span style="color:#f97316">●</span> Meetings
        </button>
        <button class="filter-btn" data-filter="cutoff">
            <span style="color:#7c3aed">●</span> Cut-off Date
        </button>
        <button class="filter-btn" data-filter="deferred">
            <span style="color:#5b21b6">●</span> Deferred
        </button>
    </div>

    <!-- ── Calendar + Sidebar ── -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Calendar Panel -->
        <div class="flex-1 min-w-0 overflow-hidden">
            <div class="cal-outer">

                <div class="cal-header">
                    <div class="cal-title-row">
                        <svg class="cal-icon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <h3 class="cal-title" id="calMonthLabel"></h3>
                    </div>
                    <div class="cal-selects">
                        <select class="cal-select" id="monthSelect"></select>
                        <select class="cal-select" id="yearSelect"></select>
                    </div>
                </div>

                <div class="cal-grid-card">
                    <div class="cal-day-headers" id="calDayHeaders"></div>
                    <div class="cal-grid-new" id="calBody"></div>
                </div>

            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-72 xl:w-80 shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-28">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest text-bsu-dark" id="sidebarTitle">Upcoming This Month</p>
                    <button id="clearSelection" class="hidden text-[9px] font-bold text-gray-400 hover:text-bsu-dark uppercase tracking-wide transition">
                        ✕ Clear
                    </button>
                </div>
                <div class="px-4 py-1 max-h-[420px] overflow-y-auto" id="sidebarEvents"></div>
            </div>

            <!-- Quick stats -->
            <div class="mt-3 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-bsu-dark mb-3">This Month Summary</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#dc2626] inline-block"></span>
                            Deadlines
                        </span>
                        <span class="text-[13px] font-black text-red-700" id="statDeadline">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#1e40af] inline-block"></span>
                            At Secretariat
                        </span>
                        <span class="text-[13px] font-black text-blue-800" id="statSecretariat">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#d97706] inline-block"></span>
                            At Reviewer
                        </span>
                        <span class="text-[13px] font-black text-yellow-800" id="statReviewer">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#166534] inline-block"></span>
                            Assigned
                        </span>
                        <span class="text-[13px] font-black text-green-800" id="statAssigned">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#D97706] inline-block"></span>
                            Meeting Days
                        </span>
                        <span class="text-[13px] font-black text-[#92400e]" id="statMeeting">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#7c3aed] inline-block"></span>
                            Cut-off Dates
                        </span>
                        <span class="text-[13px] font-black text-purple-800" id="statCutoff">—</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-sm bg-[#5b21b6] inline-block"></span>
                            Deferred (Next Mtg.)
                        </span>
                        <span class="text-[13px] font-black text-violet-800" id="statDeferred">—</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="bg-white border-t border-gray-200 mt-6 py-8">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em]">
            &copy; <?php echo date('Y'); ?> Batangas State University
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

<!-- ═══ CALENDAR SCRIPT ═══ -->
<script>
const MONTH_NAMES = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
const DAY_HEADERS = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

let activeFilter = 'all';

// ── Meeting day: Wednesday of 2nd week ──
function getMeetingDay(year, month) {
    for (let d = 8; d <= 14; d++) {
        if (new Date(year, month, d).getDay() === 3) return d;
    }
    return 10;
}

function seededRand(seed) {
    let s = seed;
    return function() {
        s = (s * 1103515245 + 12345) & 0x7fffffff;
        return s / 0x7fffffff;
    };
}

// ══════════════════════════════════════════════════════
// CUT-OFF DATE: 21 days (3 weeks) before the meeting day
// Apps submitted AFTER cut-off → deferred to NEXT meeting
// ══════════════════════════════════════════════════════
function getCutoffDay(meetingDay) {
    return meetingDay - 21;
}

// Next month's meeting day (for deferred label)
function getNextMeetingInfo(year, month) {
    let nm = month + 1, ny = year;
    if (nm > 11) { nm = 0; ny++; }
    const nmd = getMeetingDay(ny, nm);
    return MONTH_NAMES[nm].substring(0, 3) + ' ' + nmd + ', ' + ny;
}

// ══════════════════════════════════════════════════════
// MOCK APPLICATION DATA  — location = where file currently is
// type: 'deadline' | 'secretariat' | 'reviewer' | 'assigned' | 'meeting' | 'cutoff' | 'deferred'
// ══════════════════════════════════════════════════════
function getMockEvents(year, month) {
    const events = {};
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const rand = seededRand(year * 100 + month);
    const meetingDay = getMeetingDay(year, month);
    const cutoffDay  = getCutoffDay(meetingDay);
    const nextMtgStr = getNextMeetingInfo(year, month);

    function addEv(day, ev) {
        if (day < 1 || day > daysInMonth) return;
        if (!events[day]) events[day] = [];
        events[day].push(ev);
    }

    // ── 1. COMMITTEE MEETING DAY ──
    addEv(meetingDay, {
        type: 'meeting',
        appId: null,
        label: 'BERC Bi-Monthly Review Meeting',
        applicant: null,
        location: null,
        reviewer: 'Full Committee',
        notes: 'All assigned reviewers must submit preliminary reports before this date.'
    });

    // ── 2. CUT-OFF DATE EVENT ──
    if (cutoffDay >= 1 && cutoffDay <= daysInMonth) {
        addEv(cutoffDay, {
            type: 'cutoff',
            appId: null,
            label: 'Application Cut-off Date',
            applicant: null,
            location: null,
            reviewer: null,
            notes: `Applications received AFTER this date will NOT be included in the ${MONTH_NAMES[month]} meeting. They will be deferred to the next meeting (${nextMtgStr}).`
        });
    }

    // ── 3. APPLICATIONS — split by cut-off ──
    // Apps submitted ON or BEFORE cutoffDay → included in THIS meeting
    // Apps submitted AFTER cutoffDay → deferred to NEXT meeting
    const apps = [
        // ─── BEFORE CUT-OFF (will be in this meeting) ───
        {
            appId: 'ERC-2026-001',
            title: 'Survey on Sleep Hygiene Among Nursing Students',
            applicant: 'Maria Santos',
            college: 'College of Nursing',
            submitDay:   clamp(cutoffDay - 14, 1, daysInMonth),
            secDay:      clamp(cutoffDay - 11, 1, daysInMonth),
            revDay:      clamp(cutoffDay - 6,  1, daysInMonth),
            deadlineDay: clamp(meetingDay - 5, 1, daysInMonth),
            reviewer: 'Dr. Reyes',
            location: 'reviewer',
            deferred: false,
        },
        {
            appId: 'ERC-2026-002',
            title: 'Nutritional Counseling Trial for Adolescents',
            applicant: 'Juan dela Cruz',
            college: 'College of Medicine',
            submitDay:   clamp(cutoffDay - 12, 1, daysInMonth),
            secDay:      clamp(cutoffDay - 9,  1, daysInMonth),
            revDay:      clamp(cutoffDay - 4,  1, daysInMonth),
            deadlineDay: clamp(meetingDay - 3, 1, daysInMonth),
            reviewer: 'Dr. Mendoza',
            location: 'reviewer',
            deferred: false,
        },
        {
            appId: 'ERC-2026-003',
            title: 'Public Health Data Collection in Urban Barangays',
            applicant: 'Ana Reyes',
            college: 'College of Public Health',
            submitDay:   clamp(cutoffDay - 16, 1, daysInMonth),
            secDay:      clamp(cutoffDay - 13, 1, daysInMonth),
            revDay:      clamp(cutoffDay - 7,  1, daysInMonth),
            deadlineDay: clamp(meetingDay - 6, 1, daysInMonth),
            reviewer: 'Dr. Garcia',
            location: 'reviewer',
            deferred: false,
        },
        {
            appId: 'ERC-2026-004',
            title: 'School Mental Health Intervention Program',
            applicant: 'Pedro Bautista',
            college: 'College of Education',
            submitDay:   clamp(cutoffDay - 10, 1, daysInMonth),
            secDay:      clamp(cutoffDay - 7,  1, daysInMonth),
            revDay:      null,
            deadlineDay: clamp(meetingDay - 2, 1, daysInMonth),
            reviewer: null,
            location: 'secretariat',
            deferred: false,
        },
        {
            appId: 'ERC-2026-005',
            title: 'Informed Consent Review for Genetic Studies',
            applicant: 'Liza Navarro',
            college: 'College of Science',
            submitDay:   clamp(cutoffDay - 8,  1, daysInMonth),
            secDay:      clamp(cutoffDay - 5,  1, daysInMonth),
            revDay:      null,
            deadlineDay: clamp(meetingDay - 1, 1, daysInMonth),
            reviewer: null,
            location: 'secretariat',
            deferred: false,
        },
        {
            appId: 'ERC-2026-006',
            title: 'Clinical Research Application on Hypertension',
            applicant: 'Roberto Cruz',
            college: 'College of Medicine',
            submitDay:   clamp(cutoffDay - 6,  1, daysInMonth),
            secDay:      clamp(cutoffDay - 3,  1, daysInMonth),
            revDay:      clamp(cutoffDay,      1, daysInMonth),
            deadlineDay: clamp(meetingDay - 4, 1, daysInMonth),
            reviewer: 'Dr. Torres',
            location: 'assigned',
            deferred: false,
        },

        // ─── AFTER CUT-OFF (deferred to next meeting) ───
        {
            appId: 'ERC-2026-007',
            title: 'Community Health Survey – Brgy. Kumintang',
            applicant: 'Carla Vidal',
            college: 'College of Public Health',
            submitDay:   clamp(cutoffDay + 2,  1, daysInMonth),
            secDay:      clamp(cutoffDay + 5,  1, daysInMonth),
            revDay:      null,
            deadlineDay: null,
            reviewer: null,
            location: 'secretariat',
            deferred: true,
        },
        {
            appId: 'ERC-2026-008',
            title: 'Orientation on Ethics Clearance for Researchers',
            applicant: 'Miguel Torres',
            college: 'Research & Development',
            submitDay:   clamp(cutoffDay + 4,  1, daysInMonth),
            secDay:      clamp(cutoffDay + 7,  1, daysInMonth),
            revDay:      null,
            deadlineDay: null,
            reviewer: null,
            location: 'secretariat',
            deferred: true,
        },
        {
            appId: 'ERC-2026-009',
            title: 'Ethics Clearance for Dental Implant Study',
            applicant: 'Grace Lim',
            college: 'College of Dentistry',
            submitDay:   clamp(cutoffDay + 7,  1, daysInMonth),
            secDay:      clamp(cutoffDay + 10, 1, daysInMonth),
            revDay:      null,
            deadlineDay: null,
            reviewer: null,
            location: 'secretariat',
            deferred: true,
        },
    ];

    apps.forEach(app => {
        const deferredNote = app.deferred
            ? `⚠️ Submitted AFTER cut-off (${MONTH_NAMES[month].substring(0,3)} ${cutoffDay}). Will be presented at next meeting: ${nextMtgStr}.`
            : null;

        // Submit / received event
        if (app.submitDay) {
            addEv(app.submitDay, {
                type: app.deferred ? 'deferred' : 'assigned',
                appId: app.appId,
                label: app.title,
                applicant: app.applicant,
                college: app.college,
                reviewer: app.reviewer,
                location: app.location,
                deferred: app.deferred,
                notes: app.deferred
                    ? deferredNote
                    : `Application received & logged. Currently at: ${locationLabel(app.location)}`
            });
        }

        // Secretariat processing
        if (app.secDay) {
            addEv(app.secDay, {
                type: 'secretariat',
                appId: app.appId,
                label: app.title,
                applicant: app.applicant,
                college: app.college,
                reviewer: app.reviewer,
                location: app.location,
                deferred: app.deferred,
                notes: app.deferred
                    ? deferredNote
                    : (app.location === 'secretariat'
                        ? 'File is with the Secretariat — awaiting assignment to reviewer.'
                        : 'File passed initial secretarial check.')
            });
        }

        // Reviewer assigned
        if (app.revDay) {
            addEv(app.revDay, {
                type: 'reviewer',
                appId: app.appId,
                label: app.title,
                applicant: app.applicant,
                college: app.college,
                reviewer: app.reviewer,
                location: app.location,
                deferred: app.deferred,
                notes: `File forwarded to reviewer: ${app.reviewer || 'TBA'}`
            });
        }

        // Deadline
        if (app.deadlineDay) {
            addEv(app.deadlineDay, {
                type: 'deadline',
                appId: app.appId,
                label: app.title,
                applicant: app.applicant,
                college: app.college,
                reviewer: app.reviewer,
                location: app.location,
                deferred: false,
                notes: `Review deadline. Reviewer: ${app.reviewer || 'Not yet assigned'}`
            });
        }
    });

    return events;
}

function locationLabel(loc) {
    if (loc === 'secretariat') return 'Secretariat';
    if (loc === 'reviewer')    return 'Reviewer';
    if (loc === 'assigned')    return 'Assigned to Reviewer';
    return '—';
}

function clamp(v, min, max) { return Math.max(min, Math.min(v, max)); }

// ── State ──
let viewYear  = new Date().getFullYear();
let viewMonth = new Date().getMonth();
const todayD  = new Date().getDate();
const todayM  = new Date().getMonth();
const todayY  = new Date().getFullYear();
let selectedDay = null;
let currentEvents = {};

// ── Build dropdowns ──
function buildDropdowns() {
    const monthSel = document.getElementById('monthSelect');
    const yearSel  = document.getElementById('yearSelect');

    MONTH_NAMES.forEach((m, i) => {
        const opt = document.createElement('option');
        opt.value = i; opt.textContent = m;
        monthSel.appendChild(opt);
    });
    for (let y = todayY - 3; y <= todayY + 5; y++) {
        const opt = document.createElement('option');
        opt.value = y; opt.textContent = y;
        yearSel.appendChild(opt);
    }

    monthSel.addEventListener('change', () => { viewMonth = parseInt(monthSel.value); selectedDay = null; renderCalendar(); });
    yearSel.addEventListener('change',  () => { viewYear  = parseInt(yearSel.value);  selectedDay = null; renderCalendar(); });
}

// ── Type helpers ──
function typeColor(type) {
    return { meeting:'#f97316', deadline:'#dc2626', secretariat:'#1e40af', reviewer:'#92400e', assigned:'#166534', cutoff:'#7c3aed', deferred:'#5b21b6' }[type] || '#6b7280';
}
function typeLabel(type) {
    return { meeting:'Meeting Day', deadline:'Deadline', secretariat:'At Secretariat', reviewer:'At Reviewer', assigned:'Assigned', cutoff:'Cut-off Date', deferred:'Deferred to Next Mtg.' }[type] || type;
}
function typePillClass(type) {
    return { meeting:'loc-pill-meet', deadline:'loc-pill-dead', secretariat:'loc-pill-sec', reviewer:'loc-pill-rev', assigned:'loc-pill-asgn', cutoff:'loc-pill-cut', deferred:'loc-pill-def' }[type] || 'loc-pill-sec';
}
function typeDateBoxClass(type) {
    return { meeting:'meeting', deadline:'deadline', secretariat:'', reviewer:'reviewer', assigned:'assigned', cutoff:'cutoff', deferred:'deferred' }[type] || '';
}
function typeChipClass(type) {
    return { meeting:'ev-chip-meeting', deadline:'ev-chip-deadline', secretariat:'ev-chip-secretariat', reviewer:'ev-chip-reviewer', assigned:'ev-chip-assigned', cutoff:'ev-chip-cutoff', deferred:'ev-chip-deferred' }[type] || 'ev-chip-secretariat';
}

function shouldShow(ev) {
    if (activeFilter === 'all') return true;
    return ev.type === activeFilter;
}

// ── Sidebar helpers ──
function makeSideItem(day, ev) {
    const monthShort = MONTH_NAMES[viewMonth].substring(0, 3).toUpperCase();
    const dboxClass  = typeDateBoxClass(ev.type);
    const item = document.createElement('div');
    item.className = 'side-event-item';

    const locCurrentLabel = ev.location ? locationLabel(ev.location) : typeLabel(ev.type);
    const locCurrentPill  = ev.location ? typePillClass(ev.location) : typePillClass(ev.type);

    item.innerHTML = `
        <div class="side-event-date-box ${dboxClass}">
            <div class="month-lbl">${monthShort}</div>
            <div class="day-lbl">${day}</div>
        </div>
        <div class="flex-1 min-w-0">
            ${ev.appId ? `<p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">${ev.appId}</p>` : ''}
            <p class="text-[11px] font-bold text-bsu-dark leading-tight" style="word-break:break-word;white-space:normal;">
                ${ev.label}
            </p>
            ${ev.applicant ? `<p class="text-[9px] text-gray-500 mt-0.5">${ev.applicant} · ${ev.college || ''}</p>` : ''}
            <div class="flex flex-wrap gap-1 mt-1.5">
                <span class="loc-pill ${typePillClass(ev.type)}">
                    <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                    ${typeLabel(ev.type)}
                </span>
                ${ev.location && ev.type !== ev.location ? `
                <span class="loc-pill ${locCurrentPill}">
                    📍 ${locCurrentLabel}
                </span>` : ''}
                ${ev.reviewer ? `<span class="loc-pill" style="background:#f0fdf4;color:#166534;">👤 ${ev.reviewer}</span>` : ''}
            </div>
            ${ev.notes ? `<p class="text-[9px] text-gray-400 mt-1.5 italic leading-tight">${ev.notes}</p>` : ''}
        </div>`;
    return item;
}

function renderDaySidebar(day, evList) {
    const sidebar  = document.getElementById('sidebarEvents');
    const titleEl  = document.getElementById('sidebarTitle');
    const clearBtn = document.getElementById('clearSelection');
    const dayName  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
                        [new Date(viewYear, viewMonth, day).getDay()];
    const monthShort = MONTH_NAMES[viewMonth].substring(0, 3).toUpperCase();

    titleEl.textContent = dayName + ', ' + monthShort + ' ' + day;
    clearBtn.classList.remove('hidden');
    sidebar.innerHTML = '';

    const filtered = (evList || []).filter(shouldShow);
    if (filtered.length === 0) {
        sidebar.innerHTML = `<p class="text-[11px] text-gray-400 text-center py-6 font-semibold">No matching events on this day.</p>`;
        return;
    }
    filtered.forEach(ev => sidebar.appendChild(makeSideItem(day, ev)));
}

function renderMonthSidebar(events) {
    const sidebar  = document.getElementById('sidebarEvents');
    const titleEl  = document.getElementById('sidebarTitle');
    const clearBtn = document.getElementById('clearSelection');

    titleEl.textContent = 'Upcoming This Month';
    clearBtn.classList.add('hidden');
    sidebar.innerHTML = '';

    const today = new Date(); today.setHours(0, 0, 0, 0);
    const sortedDays = Object.keys(events).map(Number).sort((a, b) => a - b);

    let counts = { deadline: 0, secretariat: 0, reviewer: 0, assigned: 0, meeting: 0, cutoff: 0, deferred: 0 };

    sortedDays.forEach(day => {
        const isPast = new Date(viewYear, viewMonth, day) < today
                    && !(viewYear === todayY && viewMonth === todayM && day === todayD);

        events[day].forEach(ev => {
            if (counts[ev.type] !== undefined) counts[ev.type]++;
            if (!shouldShow(ev)) return;
            const item = makeSideItem(day, ev);
            if (isPast) item.classList.add('opacity-50');
            sidebar.appendChild(item);
        });
    });

    const total = Object.values(counts).reduce((a, b) => a + b, 0);
    if (total === 0) {
        sidebar.innerHTML = `<p class="text-[11px] text-gray-400 text-center py-6 font-semibold">No events this month.</p>`;
    }

    document.getElementById('statDeadline').textContent   = counts.deadline;
    document.getElementById('statSecretariat').textContent = counts.secretariat;
    document.getElementById('statReviewer').textContent   = counts.reviewer;
    document.getElementById('statAssigned').textContent   = counts.assigned;
    document.getElementById('statMeeting').textContent    = counts.meeting;
    document.getElementById('statCutoff').textContent     = counts.cutoff;
    document.getElementById('statDeferred').textContent   = counts.deferred;
}

// ── Main render ──
function renderCalendar() {
    currentEvents = getMockEvents(viewYear, viewMonth);

    document.getElementById('calMonthLabel').textContent = MONTH_NAMES[viewMonth] + ' ' + viewYear;
    document.getElementById('monthSelect').value = viewMonth;
    document.getElementById('yearSelect').value  = viewYear;

    document.getElementById('calDayHeaders').innerHTML =
        DAY_HEADERS.map(d => `<div class="cal-day-hdr">${d}</div>`).join('');

    const firstDow    = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const prevDays    = new Date(viewYear, viewMonth, 0).getDate();
    const totalCells  = Math.ceil((firstDow + daysInMonth) / 7) * 7;
    const bodyEl      = document.getElementById('calBody');
    bodyEl.innerHTML  = '';

    for (let i = 0; i < totalCells; i++) {
        const cell = document.createElement('div');
        cell.className = 'cal-cell-new';

        let day, isCurrentMonth = true;
        if (i < firstDow) {
            day = prevDays - firstDow + i + 1;
            isCurrentMonth = false;
            cell.classList.add('other-month-new');
        } else if (i - firstDow >= daysInMonth) {
            day = i - firstDow - daysInMonth + 1;
            isCurrentMonth = false;
            cell.classList.add('other-month-new');
        } else {
            day = i - firstDow + 1;
        }

        const isToday = isCurrentMonth && day === todayD && viewMonth === todayM && viewYear === todayY;
        const dayEvs  = isCurrentMonth ? (currentEvents[day] || []) : [];
        const visEvs  = dayEvs.filter(shouldShow);

        if (isCurrentMonth && dayEvs.some(e => e.type === 'meeting'))    cell.classList.add('meeting-day-cell');
        if (isCurrentMonth && dayEvs.some(e => e.type === 'cutoff'))     cell.classList.add('cutoff-day');
        if (isCurrentMonth && dayEvs.some(e => e.type === 'deadline') && !dayEvs.some(e => e.type === 'meeting') && !dayEvs.some(e => e.type === 'cutoff')) cell.classList.add('has-deadline');
        if (isCurrentMonth && selectedDay === day) cell.classList.add('selected-cell');

        // Day number
        const numEl = document.createElement('div');
        if (isToday) {
            numEl.innerHTML = `<div class="day-num-today">${day}</div>`;
        } else {
            numEl.className = 'day-num-new' + (!isCurrentMonth ? ' other-month-num' : '');
            numEl.textContent = day;
        }
        cell.appendChild(numEl);

        // Event chips (visible events only)
        if (isCurrentMonth && visEvs.length > 0) {
            visEvs.forEach((ev, idx) => {
                if (idx >= 3) {
                    // "+N more" hint
                    if (idx === 3) {
                        const more = document.createElement('div');
                        more.className = 'ev-chip-new ev-chip-approved';
                        more.textContent = `+${visEvs.length - 3} more`;
                        cell.appendChild(more);
                    }
                    return;
                }

                if (ev.type === 'meeting') {
                    const badge = document.createElement('div');
                    badge.className = 'meeting-badge';
                    badge.textContent = 'MEETING';
                    cell.appendChild(badge);
                }
                if (ev.type === 'deadline') {
                    const badge = document.createElement('div');
                    badge.className = 'deadline-badge';
                    badge.textContent = 'DEADLINE';
                    cell.appendChild(badge);
                }
                if (ev.type === 'cutoff') {
                    const badge = document.createElement('div');
                    badge.className = 'cutoff-badge';
                    badge.textContent = '✂ CUT-OFF';
                    cell.appendChild(badge);
                }
                if (ev.type === 'deferred') {
                    const badge = document.createElement('div');
                    badge.className = 'deferred-badge';
                    badge.textContent = '↪ DEFERRED';
                    cell.appendChild(badge);
                }

                const chip = document.createElement('div');
                chip.className = 'ev-chip-new ' + typeChipClass(ev.type);

                // Prefix dot + truncated title
                const shortTitle = ev.appId
                    ? ev.appId + ': ' + ev.label.substring(0, 22) + (ev.label.length > 22 ? '…' : '')
                    : ev.label.substring(0, 28) + (ev.label.length > 28 ? '…' : '');
                chip.textContent = shortTitle;
                cell.appendChild(chip);

                // Mobile dot
                const dot = document.createElement('span');
                dot.className = 'event-dot-mobile hidden max-md:inline-block';
                dot.style.background = typeColor(ev.type);
                cell.appendChild(dot);
            });
        }

        if (isCurrentMonth) {
            cell.addEventListener('click', () => {
                if (selectedDay === day) {
                    selectedDay = null;
                    renderCalendar();
                    renderMonthSidebar(currentEvents);
                } else {
                    selectedDay = day;
                    renderCalendar();
                    renderDaySidebar(day, currentEvents[day] || []);
                }
            });
        }

        bodyEl.appendChild(cell);
    }

    // ── After building all cells, inject cut-off divider banner ──
    // Find the row that contains the cut-off day and insert a banner after it
    const meetingDayCalc = getMeetingDay(viewYear, viewMonth);
    const cutoffDayCalc  = getCutoffDay(meetingDayCalc);
    const daysInMonthCalc = new Date(viewYear, viewMonth + 1, 0).getDate();

    if (cutoffDayCalc >= 1 && cutoffDayCalc <= daysInMonthCalc) {
        // Which row index (0-based) contains the cut-off day?
        const firstDowCalc = new Date(viewYear, viewMonth, 1).getDay();
        const cellIndexOfCutoff = firstDowCalc + cutoffDayCalc - 1;
        const rowOfCutoff = Math.floor(cellIndexOfCutoff / 7);
        // Insert banner after that row's last cell (index rowOfCutoff*7 + 6)
        const cellsArr = Array.from(bodyEl.children);
        const insertAfterIndex = (rowOfCutoff + 1) * 7 - 1; // last cell of that row
        const refCell = cellsArr[insertAfterIndex];

        const banner = document.createElement('div');
        banner.className = 'cutoff-banner';
        banner.innerHTML = `
            <svg width="12" height="12" fill="none" stroke="#7c3aed" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M6 9l6 6 6-6"/>
            </svg>
            ✂&nbsp; Application Cut-off: ${MONTH_NAMES[viewMonth].substring(0,3)} ${cutoffDayCalc}
            &nbsp;—&nbsp; Applications received after this date are deferred to the next meeting
            <svg width="12" height="12" fill="none" stroke="#7c3aed" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M6 9l6 6 6-6"/>
            </svg>`;

        if (refCell && refCell.nextSibling) {
            bodyEl.insertBefore(banner, refCell.nextSibling);
        } else {
            bodyEl.appendChild(banner);
        }
    }

    selectedDay !== null
        ? renderDaySidebar(selectedDay, currentEvents[selectedDay] || [])
        : renderMonthSidebar(currentEvents);
}

// ── Filter buttons ──
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        activeFilter = btn.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderCalendar();
    });
});

// ── Clear selection ──
document.getElementById('clearSelection').addEventListener('click', () => {
    selectedDay = null;
    renderCalendar();
});

buildDropdowns();
renderCalendar();
</script>

</body>
</html>
