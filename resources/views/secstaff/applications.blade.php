<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU Ethics Review Committee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        body {
            font-family: 'Inter', sans-serif;
            overflow-y: scroll;
        }
        #mobile-menu { transition: transform 0.3s ease, opacity 0.3s ease; }
        #mobile-menu.hidden  { transform: translateY(-10px); opacity: 0; pointer-events: none; }
        #mobile-menu.open    { transform: translateY(0);     opacity: 1; pointer-events: auto; }
        :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

        .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); }
        .card-header { display:flex; align-items:center; border-bottom:1px solid #e5e7eb; background:#fafafa; padding:0 16px; }
        .card-tab { font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:var(--bsu-dark); border-bottom:3px solid var(--brand-red); padding:14px 4px 11px; }

        .list-grid-header,
        .app-row {
            display: grid;
            grid-template-columns: 150px 1fr 160px 120px 140px 160px 140px;
            padding: 8px 20px;
            align-items: center;
        }

        .nav-tab-active { border-bottom: 3px solid #D32F2F; color: #213C71; }

        .list-grid-header {
            background: #f3f4f6;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .app-row {
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background .15s;
        }
        .list-grid-header > div,
        .app-row > div {
            min-width: 0; /* Forces the column to respect the grid width and wrap text */
            padding-right: 16px; /* Adds breathing room so text doesn't touch the next column */
            text-align: left !important;
        }

        /* Optional: Remove the padding from the very last column so it aligns nicely with the right edge */
        .list-grid-header > div:last-child,
        .app-row > div:last-child {
            padding-right: 0;
        }
        .app-row:last-child { border-bottom: none; }
        .app-row:hover { background: #f9fafb; }

        .app-id-badge {
            display: inline-flex;
            align-items: center;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 800;
            font-family: monospace;
            letter-spacing: 0.03em;
            padding: 4px 9px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .app-row-title { font-size:13px; font-weight:700; color:#111827; }
        .app-row-sub   { font-size:11px; color:#6b7280; margin-top:2px; }
        .pay-method    { font-size:12px; font-weight:600; color:#374151; }
        .pay-ref       { font-size:11px; font-weight:700; color:#2563eb; font-family:monospace; }
        .receipt-link  { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; }

        .proof-thumb-wrap {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }
        .proof-thumb {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 6px;
            border: 1.5px solid #d1d5db;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        .proof-thumb:hover {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 3px rgba(211,47,47,.15);
            transform: scale(1.06);
        }
        .proof-thumb-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--brand-red);
            text-decoration: underline;
            text-underline-offset: 2px;
            white-space: nowrap;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            margin-bottom: 4px;
            color: #4b5563;
        }
        .mock-cb {
            width: 12px;
            height: 12px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 9px;
            font-weight: bold;
            color: #1d4ed8;
            flex-shrink: 0;
        }

        /* Lightbox Overlay */
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.82);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .lightbox-overlay.open { display: flex; }

        /* Lightbox Box */
        .lightbox-box {
            position: relative;
            background: #fff;
            border-radius: 14px;

            /* CHANGE: Use flex column to stack children */
            display: flex;
            flex-direction: column;

            overflow: hidden; /* Prevent the box itself from scrolling */
            max-width: 680px;
            max-height: 90vh;
            width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,.5);
            animation: lbIn .2s ease;
        }

        @keyframes lbIn {
            from { opacity:0; transform: scale(.94); }
            to   { opacity:1; transform: scale(1); }
        }

        .lightbox-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;

            /* CHANGE: Ensure header never shrinks */
            flex-shrink: 0;
        }

        .lightbox-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--bsu-dark);
        }

        .lightbox-close {
            background: none;
            border: none;
            font-size: 22px;
            color: #6b7280;
            cursor: pointer;
            line-height: 1;
            padding: 2px 8px;
            border-radius: 6px;
            transition: background .15s;
        }
        .lightbox-close:hover { background: #f3f4f6; color: #111; }

        /* Image Wrap */
        .lightbox-img-wrap {
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 340px;
            padding: 16px;

            /* CHANGE: Allow this area to grow/scroll while header/footer stay put */
            flex: 1;
            overflow-y: auto;
        }

        /* Image */
        .lightbox-img {
            max-width: 100%;

            /* CHANGE: Removed max-height 100% to allow natural vertical scrolling */
            height: auto;

            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
        }

        .lightbox-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            border-top: 1px solid #e5e7eb;
            background: #fafafa;

            /* CHANGE: Ensure footer never shrinks */
            flex-shrink: 0;
        }

        .lightbox-meta { font-size: 11px; color: #6b7280; font-weight: 600; }

        .lightbox-download {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: var(--bsu-dark);
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background .15s;
        }
        .lightbox-download:hover { background: #dbeafe; }

        /* Modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:#fff; border-radius:14px; width:100%; max-width:1200px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
        .modal-header h2 { font-size:14px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
        .close-btn { font-size:20px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; transition:background .15s; }
        .close-btn:hover { background:#f3f4f6; color:#111; }
        .modal-content { display:flex; gap:0; overflow:hidden; flex:1; min-height:0; }
        .payment-info-panel { width:260px; min-width:260px; border-right:1px solid #e5e7eb; padding:20px; background:#fafafa; overflow-y:auto; flex-shrink:0; }
        .info-group { margin-bottom:16px; }
        .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
        .info-value { font-size:13px; font-weight:700; color:#111827; }
        .modal-app-id { font-size:12px; font-weight:800; font-family:monospace; color:#1d4ed8; background:#eff6ff; border:1px solid #bfdbfe; padding:4px 10px; border-radius:6px; display:inline-block; }

        /* ── Document card & animated preview panel ── */
        .doc-card {
            background: #fff;
            border: 1.5px solid #d1d5db;
            padding: 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s, background .2s;
            user-select: none;
        }
        .doc-card:hover {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 3px rgba(211,47,47,.12);
        }
        .doc-card.active {
            border-color: var(--bsu-dark);
            box-shadow: 0 0 0 3px rgba(33,60,113,.12);
            background: #f0f4ff;
        }
        .doc-chevron {
            width: 14px;
            height: 14px;
            color: #9ca3af;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            flex-shrink: 0;
        }
        .doc-card.active .doc-chevron {
            transform: rotate(90deg);
            color: var(--bsu-dark);
        }

        /* Animated slide-in form preview */
        .form-preview-panel {
            flex: 0 0 0;
            width: 0;
            padding: 0;
            overflow: hidden;
            opacity: 0;
            border-left: 0px solid #e5e7eb;
            background: #fff;
            transition:
                flex-basis .35s cubic-bezier(.4,0,.2,1),
                width .35s cubic-bezier(.4,0,.2,1),
                opacity .3s ease,
                padding .35s ease,
                border-left-width .35s ease;
        }
        .form-preview-panel.doc-open {
            flex: 1 1 0;
            width: auto;
            padding: 20px;
            opacity: 1;
            overflow-y: auto;
            border-left: 1px solid #e5e7eb;
        }

        .application-form-mock { border:1px solid #d1d5db; border-radius:8px; padding:24px; font-size:11px; color:#374151; background:#fff; }
        .form-header { text-align:center; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid var(--bsu-dark); }
        .form-header h3 { font-size:13px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
        .form-section { margin-bottom:16px; }
        .form-section-title { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--bsu-dark); border-bottom:1px solid #e5e7eb; padding-bottom:6px; margin-bottom:8px; }
        .form-row { display:flex; gap:6px; margin-bottom:5px; line-height:1.5; }
        .field-label { font-weight:700; white-space:nowrap; }

        .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 20px; border-top:1px solid #e5e7eb; background:#fafafa; }
        .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
        .btn:active { transform:scale(.97); }
        .btn-primary { background:var(--bsu-dark); color:#fff; }
        .btn-primary:hover { opacity:.88; }
        .btn-danger { background:var(--brand-red); color:#fff; }
        .btn-danger:hover { opacity:.88; }
        .btn-outline { background:transparent; color:var(--bsu-dark); border:1.5px solid var(--bsu-dark); }
        .btn-outline:hover { background:#f0f4ff; }

        /* Responsive */
        @media (max-width: 900px) {
            .list-grid-header { display: none; }
            .app-row {
                grid-template-columns: 1fr auto;
                gap: 8px;
            }
            .app-row > *:nth-child(3),
            .app-row > *:nth-child(4),
            .app-row > *:nth-child(5),
            .app-row > *:nth-child(6) { display: none; }
            .app-id-col { grid-column: 1 / -1; order: -1; }
            .payment-info-panel { width:100%; min-width:unset; border-right:none; border-bottom:1px solid #e5e7eb; }
            .modal-content { flex-direction:column; overflow-y:auto; }
            .form-preview-panel.doc-open { flex: unset; width: 100%; border-left: none; border-top: 1px solid #e5e7eb; }
        }

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

        <!-- Mobile nav drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-[#FCFCFC]">
            <div class="flex flex-col text-[11px] font-bold uppercase tracking-wider text-gray-500 divide-y divide-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('secstaff.applications') }}" class="flex items-center space-x-3 text-bsu-dark px-5 py-3.5">
                    <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Applications</span>
                </a>
                <a href="{{ route('secstaff.calendar') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('secstaff.payment_settings') }}" class="flex items-center space-x-3 px-5 py-3.5 hover:text-bsu-dark">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Payment Settings</span>
                </a>
                <a href="{{ route('secstaff.history') }}" class="flex items-center gap-2 px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:text-bsu-dark transition border-b-[3px] border-transparent hover:border-brand-red">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 11a9 9 0 1 0 .5-3M3 4v4h4"/>
                    </svg>
                    History
                </a>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <a href="{{ route('settings') }}" class="hover:text-bsu-dark transition-colors">Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase">
                            Sign Out
                        </button>
                    </form>
                </div>
                <!-- Date on mobile -->
                <div class="px-5 py-3 bg-white">
                    <p class="text-[11px] font-bold text-bsu-dark uppercase tracking-wide">12:00 AM | Monday</p>
                    <p class="text-[10px] font-bold text-brand-red">16/02/2026</p>
                </div>
            </div>
        </div>
    </header>

    <script>
        var secHamburger = document.getElementById('sec-hamburger');
        var secMenu = document.getElementById('sec-mobile-menu');
        var menuOpen = false;
        secHamburger.addEventListener('click', function() {
            menuOpen = !menuOpen;
            secMenu.classList.toggle('hidden', !menuOpen);
            document.getElementById('hb1').style.transform = menuOpen ? 'translateY(7px) rotate(45deg)' : '';
            document.getElementById('hb2').style.opacity   = menuOpen ? '0' : '1';
            document.getElementById('hb3').style.transform = menuOpen ? 'translateY(-7px) rotate(-45deg)' : '';
        });
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

    <main class="max-w-7xl mx-auto px-4 sm:px-4 py-4 sm:py-4">

        <!-- Profile Banner -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
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

        <div class="app-card">
        <div class="card-header">
            <div class="card-tab">Applications</div>
        </div>

        <div class="list-grid-header">
            <div>Application ID</div>
            <div>Researcher &amp; Study</div>
            <div>Proof of Payment</div>
            <div>Method</div>
            <div>Reference No.</div>
            <div>Remarks/Comments</div>
            <div>Action</div>
        </div>

        @forelse($applications as $app)
            @php
                // 1. Get the payment relationship
                $payment = $app->payment;
                $proofUrl = null;

                // 2. Build the Proof URL securely
                if ($payment && $payment->proof_of_payment_path) {
                    $pathParts = explode('/', trim($payment->proof_of_payment_path, '/'));

                    if (count($pathParts) >= 3) {
                        $protocol = $pathParts[1];
                        $file = implode('/', array_slice($pathParts, 2));

                        $proofUrl = route('view.document', [
                            'protocol_code' => $protocol,
                            'filename'      => $file
                        ]);
                    }
                }

                // 3. Clean up strings for JavaScript safety
                $researcherName = addslashes($app->name_of_researcher);
                $researchTitle = addslashes($app->research_title);
            @endphp

            <div class="app-row cursor-pointer"
                onclick="openModal(
                    '{{ $app->protocol_code }}',
                    '{{ $researcherName }}',
                    '{{ $researchTitle }}',
                    '{{ $payment->payment_method ?? 'N/A' }}',
                    '{{ $payment->reference_number ?? 'N/A' }}'
                )">

                <div>
                    <span class="app-id-badge">{{ $app->protocol_code }}</span>
                </div>

                <div>
                    <div class="app-row-title whitespace-normal break-words leading-snug" title="{{ $app->research_title }}">
                        {{ $app->research_title }}
                    </div>
                    <div class="app-row-sub">{{ $app->name_of_researcher }}</div>
                </div>

                <div>
                    @if($proofUrl)
                        <div class="proof-thumb-wrap"
                            onclick="event.stopPropagation(); openLightbox(
                                '{{ $proofUrl }}',
                                'Proof of Payment — {{ $app->protocol_code }}',
                                '{{ $researcherName }}'
                            )">
                            <img src="{{ $proofUrl }}"
                                alt="Proof"
                                class="proof-thumb"
                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'44\' height=\'44\' viewBox=\'0 0 44 44\'%3E%3Crect width=\'44\' height=\'44\' rx=\'6\' fill=\'%23f3f4f6\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-size=\'18\' fill=\'%239ca3af\'%3E🖼%3C/text%3E%3C/svg%3E'">
                            <span class="proof-thumb-label">View Image</span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">No Proof</span>
                    @endif
                </div>

                <div class="pay-method">
                    {{ $payment->payment_method ?? 'Pending' }}
                </div>

                <div class="pay-ref">
                    {{ $payment->reference_number ?? '--' }}
                </div>

                <div class="text-[11px] text-gray-500 font-medium break-words whitespace-normal leading-relaxed" title="{{ $app->latest_comment }}">
                    {{ $app->latest_comment ?? '--' }}
                </div>

                <div class="receipt-link">
                        View Documents
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                No applications found.
            </div>
        @endforelse
    </div>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modalTitleStudy">Application Form Preview</h2>
                <button class="close-btn" onclick="closeModal()" aria-label="Close">&times;</button>
            </div>

            <div class="modal-content">

                <div class="payment-info-panel">

                    <div class="info-group">
                        <div class="info-label">Application ID</div>
                        <div class="modal-app-id" id="modalAppId">--</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Researcher Name</div>
                        <div class="info-value" id="modalResearcher">--</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value" id="modalMethod">--</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Reference Number</div>
                        <div class="info-value pay-ref" id="modalRef">--</div>
                    </div>

                    <div class="info-label" id="paymentDocLabel" style="margin-top:14px; margin-bottom:8px; display: none;">Payment Documents</div>
                    <div class="doc-card" id="docCard-proof" onclick="renderContent('Proof of Payment', window.currentAppDocs?.proof || null, false)" style="display: none; margin-top:6px;">
                        <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">🖼️</span>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:11px; font-weight:700; color:#111827;">Proof of Payment.img</div>
                            <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;">Click to preview</div>
                        </div>
                    </div>

                    <div style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
                        <div class="info-label" style="margin-bottom:8px;">Basic Requirements</div>

                        <div class="doc-card" id="docCard-appform" onclick="renderContent('appform', null, false)">
                            <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:11px; font-weight:700; color:#111827;">Application Form.pdf</div>
                                <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;">System Generated</div>
                            </div>
                        </div>

                        <div id="basicRequirementsSection"></div>
                    </div>

                    <div class="info-label" id="suppLabel" style="margin-bottom:14px; margin-top:14px; display:none;">Supplementary Documents</div>
                    <div id="supplementarySection"></div>

                    <div id="legacySectionWrapper" style="display:none; margin-top:24px; border-top:2px dashed #cbd5e1; padding-top:16px;">
                        <div class="info-label" style="margin-bottom:8px; color:#64748b;">Version History (Archived)</div>
                        <div id="legacySection"></div>
                    </div>

                </div>

                <div class="form-preview-panel" id="formPreviewPanel">
                    </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" onclick="Reject()">Reject</button>
                <button class="btn btn-primary" onclick="verifyPayment()">Verify &amp; Proceed</button>
            </div>
        </div>
    </div>

    <div id="verifyConfirmModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 text-brand-red w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Verify Payment?</h3>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Are you sure you want to verify the payment and documents for <br>
                    <span id="confirmProtocolCode" class="font-bold text-brand-red"></span>?<br>
                    This will proceed to forward the application to the Secretariat.
                </p>

                <div class="mb-6 text-left">
                    <label for="verifyComment" class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Remarks / Comments</label>
                    <textarea id="verifyComment" rows="3"
                        class="w-full text-xs p-3 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-red focus:border-brand-red outline-none transition resize-none placeholder-gray-400"
                        placeholder="Write any comments/remarks (default: Documents are complete.)"></textarea>
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button" id="cancelVerifyBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="button" id="confirmVerifyBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-brand-red hover:bg-red-700 text-white rounded-lg transition shadow-md">
                        Yes, Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="rejectConfirmModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[10000] hidden flex items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 text-red-600 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide">Return Application?</h3>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Are you sure you want to mark <br>
                    <span id="rejectProtocolCode" class="font-bold text-red-600"></span> as incomplete?
                </p>

                <div class="mb-6 text-left">
                    <label for="rejectComments" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Reason / Comments (Required)</label>
                    <textarea id="rejectComments" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition resize-none placeholder-gray-400"
                        placeholder="Specify which important documents are missing or incorrect. Supplementary documents are to follow."></textarea>
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button" id="cancelRejectBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="button" id="confirmRejectBtn" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-md">
                        Yes, Return It
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    <div id="js-toast" class="fixed top-6 right-6 z-[10010] flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-xl shadow-xl border border-gray-100 transform transition-all duration-500 translate-x-full opacity-0 hidden" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
        </div>
        <div class="ml-3 text-sm font-bold text-gray-700 tracking-wide" id="js-toast-message">
            </div>
    </div>

    <div id="lightboxOverlay" class="lightbox-overlay" onclick="closeLightbox(event)">
        <div class="lightbox-box" onclick="event.stopPropagation()">

            <div class="lightbox-header">
                <span class="lightbox-title" id="lightboxTitle">Proof of Payment</span>
                <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
            </div>

            <div class="lightbox-img-wrap">
                <img id="lightboxImg" class="lightbox-img" src="" alt="Proof">
            </div>

            <div class="lightbox-footer">
                <span class="lightbox-meta" id="lightboxMeta"></span>
                <a id="lightboxDownload" href="#" download class="lightbox-download">
                    Download Image
                </a>
            </div>

        </div>
    </div>
    <script>
    // ==========================================
    // 1. GLOBAL VARIABLES & LIGHTBOX
    // ==========================================
    var currentActiveDoc = null;
    var currentDocumentData = {}; // Stores all document info globally for the modal
    window.currentAppDocs = {}; // Helper for Proof of Payment

    function openLightbox(imgSrc, title, meta) {
        document.getElementById('lightboxImg').src = imgSrc;
        document.getElementById('lightboxTitle').textContent = title;
        document.getElementById('lightboxMeta').textContent = 'Submitted by: ' + meta;
        document.getElementById('lightboxDownload').href = imgSrc;
        document.getElementById('lightboxOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(e) {
        if (!e || e.target === document.getElementById('lightboxOverlay')) {
            document.getElementById('lightboxOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
    }


    // ==========================================
    // 2. MAIN MODAL (FETCH & RENDER)
    // ==========================================
    async function openModal(protocolCode) {
        try {
            const response = await fetch(`/secstaff/applications/${protocolCode}`);
            if (!response.ok) throw new Error('Fetch failed');
            const data = await response.json();

            // Save data globally
            currentDocumentData = data.documents || {};
            window.currentStudyTitle = data.research_title;

            // Populate Header Info
            document.getElementById('modalAppId').textContent = data.protocol_code;
            document.getElementById('modalResearcher').innerText = data.name_of_researcher;
            document.getElementById('modalMethod').innerText = data.payment?.payment_method || 'N/A';
            document.getElementById('modalRef').innerText = data.payment?.reference_number || 'N/A';
            document.getElementById('modalTitleStudy').innerText = data.research_title || 'Application Form Preview';

            // --- CLEAR & POPULATE SIDEBAR CARDS ---
            const basicSection = document.getElementById('basicRequirementsSection');
            const suppSection = document.getElementById('supplementarySection');
            const legacySection = document.getElementById('legacySection');
            const legacyWrapper = document.getElementById('legacySectionWrapper');

            if (basicSection) basicSection.innerHTML = '';
            if (suppSection) suppSection.innerHTML = '';
            if (legacySection) legacySection.innerHTML = '';

            const titleMap = {
                'letter_request': 'Letter Request', 'endorsement_letter': 'Endorsement Letter',
                'full_proposal': 'Full Proposal', 'technical_review_approval': 'Technical Review',
                'informed_consent': 'Informed Consent', 'manuscript': 'Manuscript',
                'curriculum_vitae': 'Curriculum Vitae', 'questionnaire': 'Questionnaire',
                'data_collection': 'Data Collection', 'product_brochure': 'Product Brochure',
                'philippine_fda': 'FDA License', 'special_populations': 'Special Populations', 'others': 'Others'
            };

            const basicTypes = ['letter_request', 'endorsement_letter', 'full_proposal', 'technical_review_approval', 'curriculum_vitae', 'informed_consent', 'manuscript'];

            Object.keys(currentDocumentData).forEach(type => {
                const docs = currentDocumentData[type];
                if (!docs || docs.length === 0) return;

                let activeDocs = [];
                let legacyDocs = [];

                // -- SMART SORTING LOGIC --
                // Because the database sorts by newest first, docs[0] is always the latest version.
                const newestDoc = docs[0];
                const match = newestDoc.url ? newestDoc.url.match(/resubmit_[a-zA-Z_]+_(\d+)_/) : null;

                if (match) {
                    const newestTimestamp = match[1];
                    // If the newest file is a resubmit, only files from this EXACT timestamp batch are active
                    docs.forEach(d => {
                        const dMatch = d.url ? d.url.match(/resubmit_[a-zA-Z_]+_(\d+)_/) : null;
                        if (dMatch && dMatch[1] === newestTimestamp) {
                            activeDocs.push(d);
                        } else {
                            legacyDocs.push(d);
                        }
                    });
                } else {
                    // If the newest file is not a resubmit, there is no version history for this type
                    activeDocs = docs;
                    legacyDocs = [];
                }

                const title = titleMap[type] || type;
                const isBasic = basicTypes.includes(type);

                // -- RENDER ACTIVE DOCUMENTS --
                activeDocs.forEach(doc => {
                    const uniqueId = `doc-${doc.id}`;
                    const isRevised = doc.url && doc.url.includes('resubmit_');

                    // Clean up the description (remove the hardcoded text since we have a visual badge now)
                    let cleanDesc = doc.description ? doc.description.replace('(Resubmitted)', '').trim() : 'View File';

                    // Fixed Badge HTML: Flex-shrink prevents it from getting crushed by long titles
                    const badgeHtml = isRevised
                        ? `<div style="flex-shrink:0; margin-left:8px;"><span style="background:#fef9c3; color:#854d0e; padding:3px 6px; border-radius:4px; font-size:9px; font-weight:bold; border:1px solid #fde047;">Revised</span></div>`
                        : '';

                    const cardHtml = `
                        <div class="doc-card" id="docCard-${uniqueId}" onclick="renderContent('${title}', '${doc.url}', ${!isBasic})" style="display:flex; align-items:center; margin-top:6px;">
                            <span style="color:var(--brand-red); font-size:18px; flex-shrink:0; margin-right:8px;">📄</span>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${title}</div>
                                <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;">${cleanDesc}</div>
                            </div>
                            ${badgeHtml}
                        </div>`;

                    if (isBasic && basicSection) basicSection.innerHTML += cardHtml;
                    else if (!isBasic && suppSection) suppSection.innerHTML += cardHtml;
                });

                // -- RENDER LEGACY / ARCHIVED DOCUMENTS --
                legacyDocs.forEach(doc => {
                    const uniqueId = `doc-${doc.id}`;
                    let cleanDesc = doc.description ? doc.description.replace('(Resubmitted)', '').trim() : 'Archived Version';

                    // Styling old docs to look muted and inactive
                    const cardHtml = `
                        <div class="doc-card" id="docCard-${uniqueId}" onclick="renderContent('${title} (Archived)', '${doc.url}', ${!isBasic})" style="display:flex; align-items:center; margin-top:6px; opacity:0.75; background:#f8fafc; border:1px dashed #cbd5e1;">
                            <span style="color:#94a3b8; font-size:16px; flex-shrink:0; margin-right:8px;">🗄️</span>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:11px; font-weight:700; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${title}</div>
                                <div style="font-size:9px; font-weight:600; color:#94a3b8; margin-top:1px;">${cleanDesc}</div>
                            </div>
                            <div style="flex-shrink:0; margin-left:8px;"><span style="background:#e2e8f0; color:#475569; padding:2px 4px; border-radius:4px; font-size:8px; font-weight:bold;">Legacy</span></div>
                        </div>`;

                    if (legacySection) legacySection.innerHTML += cardHtml;
                });
            });

            // Toggle visibility of headers if there are documents inside them
            const suppLabel = document.getElementById('suppLabel');
            if (suppLabel) {
                suppLabel.style.display = suppSection?.innerHTML.trim() !== '' ? 'block' : 'none';
            }
            if (legacyWrapper) {
                legacyWrapper.style.display = legacySection?.innerHTML.trim() !== '' ? 'block' : 'none';
            }

            // Handle Proof of Payment
            const proofCard = document.getElementById('docCard-proof');
            const paymentLabel = document.getElementById('paymentDocLabel');
            if (data.payment?.proof_url) {
                window.currentAppDocs.proof = data.payment.proof_url; // Store for fallback
                if (proofCard) {
                    proofCard.style.display = 'flex';
                }
                if (paymentLabel) paymentLabel.style.display = 'block';
            } else {
                if (proofCard) proofCard.style.display = 'none';
                if (paymentLabel) paymentLabel.style.display = 'none';
            }

            // Open Modal
            document.getElementById('modalOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';

            // Auto-click App Form first
            const appFormCard = document.getElementById('docCard-appform');
            if (appFormCard) appFormCard.click();

        } catch (error) {
            console.error("Error loading modal:", error);
            alert("Could not load application details.");
        }
    }

    // ==========================================
    // 3. DOCUMENT RENDERER & INTERNAL MOCK
    // ==========================================
    function renderContent(key, url, isSupplementary = false) {
        const panel = document.getElementById('formPreviewPanel');
        const timestamp = new Date().getTime();

        // 1. Update Header Title
        const headerTitle = document.getElementById('modalTitleStudy');
        if (headerTitle) {
            if (key === 'appform') {
                headerTitle.textContent = "Application Form Preview";
            } else {
                let cleanName = key.replace(/_/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                headerTitle.textContent = isSupplementary ? `Supplementary: ${cleanName}` : `${cleanName} Preview`;
            }
        }

        // 2. Manage Active State visually
        document.querySelectorAll('.doc-card').forEach(c => c.classList.remove('active'));
        // If it's a dynamic doc, key is the Title, not the ID.
        // We only style the AppForm and Proof cards specifically here, dynamic docs handle themselves via `onclick`.
        const card = document.getElementById(`docCard-${key.toLowerCase()}`);
        if (card) card.classList.add('active');

        // 3. Render Content
        if (key === 'appform' && !url) {
            showInternalMock();
        } else if (url) {
            const isImage = key === 'Proof of Payment' || url.match(/\.(jpeg|jpg|gif|png|webp)$/i);
            let viewerHtml = '';

            if (isImage) {
                viewerHtml = `
                    <div style="flex:1; display:flex; justify-content:center; align-items:center; background:#e5e7eb; padding:20px; overflow:auto; min-height:600px;">
                        <img src="${url}?t=${timestamp}" alt="Document Preview" style="max-width:100%; max-height:100%; object-fit:contain; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    </div>`;
            } else {
                viewerHtml = `<iframe src="${url}?t=${timestamp}" width="100%" style="flex:1; border:none; min-height:600px;"></iframe>`;
            }

            panel.innerHTML = `
                <div style="height:100%; display:flex; flex-direction:column; background:#f3f4f6;">
                    <div style="background:#fff; padding:10px 15px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:11px; font-weight:700; color:#374151; text-transform:uppercase;">
                            ${isSupplementary ? 'Supplementary Document' : key} Preview
                        </span>
                        <a href="${url}" target="_blank" style="font-size:10px; color:#1d4ed8; font-weight:600; text-decoration:none;">Fullscreen ↗</a>
                    </div>
                    ${viewerHtml}
                </div>`;
        }

        panel.classList.add('doc-open');
        panel.scrollTop = 0;
        currentActiveDoc = key;
    }

    function showInternalMock() {
        const panel = document.getElementById('formPreviewPanel');
        const appId = document.getElementById('modalAppId')?.textContent || '---';
        const researcher = document.getElementById('modalResearcher')?.textContent || '---';
        const study = window.currentStudyTitle || 'Untitled Research Study';

        const docs = currentDocumentData || {};
        const check = (type) => (docs[type] && docs[type].length > 0) ? '<span style="color:#1d4ed8; font-weight:bold;">✔</span>' : '';

        panel.innerHTML = `
            <div class="doc-preview-content" id="preview-appform" style="display:block;">
                <div class="application-form-mock">
                    <div class="form-header">
                        <img src="/logo/BERC.png" style="height:40px; margin:0 auto 10px; display:block;" alt="BERC Logo" onerror="this.style.display='none'">
                        <h3>Ethics Review Committee Application Form</h3>
                        <p style="font-size:9px; font-weight:600; color:#6b7280;">BSU-ERC-FORM-01 (2026)</p>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">I. General Information</div>
                        <div class="form-row"><span class="field-label">Application ID:</span> <span style="font-family:monospace; font-weight:800; color:#1d4ed8;">${appId}</span></div>
                        <div class="form-row"><span class="field-label">Research Title:</span> <span>${study}</span></div>
                        <div class="form-row"><span class="field-label">Primary Researcher:</span> <span>${researcher}</span></div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">II. Checklist of Documents</div>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">

                            <div style="flex: 1; border-right: 1px solid #e5e7eb; padding-right: 15px;">
                                <div style="font-size: 10px; font-weight: 800; color: #374151; margin-bottom: 8px;">Basic Requirements</div>
                                <div class="checkbox-row"><div class="mock-cb">${check('letter_request')}</div><span>Letter request for review</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('endorsement_letter')}</div><span>Endorsement Letter</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('full_proposal')}</div><span>Study Protocol</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('technical_review_approval')}</div><span>Technical Review Approval</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('curriculum_vitae')}</div><span>Curriculum Vitae</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('manuscript')}</div><span>Manuscript</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('informed_consent')}</div><span>Informed Consent Form</span></div>
                                <div style="margin-left: 20px; font-size: 9px; color: #1d4ed8; font-style: italic;">
                                    ${docs['informed_consent'] ? docs['informed_consent'].map(d => d.description).join('<br>') : '--'}
                                </div>
                            </div>

                            <div style="flex: 1;">
                                <div style="font-size: 10px; font-weight: 800; color: #374151; margin-bottom: 8px;">Supplementary Documents</div>
                                <div class="checkbox-row"><div class="mock-cb">${check('questionnaire')}</div><span>Questionnaire</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('data_collection')}</div><span>Data Collection Forms</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('product_brochure')}</div><span>Product Brochure</span></div>
                                <div class="checkbox-row"><div class="mock-cb">${check('philippine_fda')}</div><span>FDA Authorization/License</span></div>

                                <div class="checkbox-row" style="margin-top:8px;">
                                    <div class="mock-cb">${check('special_populations')}</div><span>Special Populations:</span>
                                </div>
                                <div style="margin-left:20px; font-size:9px; color:#1d4ed8; font-style:italic;">
                                    ${docs['special_populations'] ? docs['special_populations'].map(d => d.description).join('<br>') : '--'}
                                </div>

                                <div class="checkbox-row" style="margin-top:8px;">
                                    <div class="mock-cb">${check('others')}</div><span>Others:</span>
                                </div>
                                <div style="margin-left:20px; font-size:9px; color:#1d4ed8; font-style:italic;">
                                    ${docs['others'] ? docs['others'].map(d => d.description).join('<br>') : '--'}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // ==========================================
    // 4. MODAL CONTROLS & EVENT LISTENERS
    // ==========================================
    function closeAllDocs() {
        const panel = document.getElementById('formPreviewPanel');
        if(panel) panel.classList.remove('doc-open');
        document.querySelectorAll('.doc-card').forEach(c => c.classList.remove('active'));
        currentActiveDoc = null;
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('open');
        document.body.style.overflow = '';
        closeAllDocs();
    }

    // Global Close listeners
    document.getElementById('modalOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });


    // ==========================================
    // 5. REJECT & VERIFY ACTIONS
    // ==========================================

    // -- REJECT LOGIC --
    function Reject() {
        const protocolCode = document.getElementById('modalAppId').textContent;
        document.getElementById('rejectProtocolCode').textContent = protocolCode;

        const rejectCommentsInput = document.getElementById('rejectComments');
        if (rejectCommentsInput) {
            rejectCommentsInput.value = '';
            rejectCommentsInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        }
        document.getElementById('rejectConfirmModal')?.classList.remove('hidden');
    }

    document.getElementById('cancelRejectBtn')?.addEventListener('click', () => {
        document.getElementById('rejectConfirmModal').classList.add('hidden');
    });

    document.getElementById('confirmRejectBtn')?.addEventListener('click', async function() {
        const btn = this;
        const rejectCommentsInput = document.getElementById('rejectComments');
        const commentsValue = rejectCommentsInput ? rejectCommentsInput.value.trim() : '';

        if (!commentsValue && rejectCommentsInput) {
            rejectCommentsInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
            rejectCommentsInput.focus();
            return;
        }

        const protocolCode = document.getElementById('modalAppId').textContent;
        const originalText = btn.textContent;
        btn.textContent = 'Processing...';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(`/research/status/${protocolCode}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'incomplete_documents',
                    comment: commentsValue
                })
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('rejectConfirmModal')?.classList.add('hidden');
                closeModal();
                showToast('Application returned: Incomplete Documents.');
            } else {
                alert('Error: ' + (result.message || 'Could not update status'));
                resetButton(btn, originalText);
            }
        } catch (error) {
            console.error("Status Update Error:", error);
            alert("A network error occurred.");
            resetButton(btn, originalText);
        }
    });

    // -- VERIFY LOGIC --
    function verifyPayment() {
        const protocolCode = document.getElementById('modalAppId').textContent;
        document.getElementById('confirmProtocolCode').textContent = protocolCode;

        const verifyComment = document.getElementById('verifyComment');
        if(verifyComment) verifyComment.value = '';

        document.getElementById('verifyConfirmModal')?.classList.remove('hidden');
    }

    document.getElementById('cancelVerifyBtn')?.addEventListener('click', () => {
        document.getElementById('verifyConfirmModal').classList.add('hidden');
    });

    document.getElementById('confirmVerifyBtn')?.addEventListener('click', async function() {
        const btn = this;
        const protocolCode = document.getElementById('modalAppId').textContent;
        const commentInput = document.getElementById('verifyComment')?.value.trim();
        const finalComment = commentInput !== '' ? commentInput : 'Documents are complete.';

        const originalText = btn.textContent;
        btn.textContent = 'Processing...';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(`/research/status/${protocolCode}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'documents_checking',
                    comment: finalComment
                })
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('verifyConfirmModal')?.classList.add('hidden');
                closeModal();
                showToast('Payment & Documents verified successfully.');
            } else {
                alert('Error: ' + (result.message || 'Could not update status'));
                resetButton(btn, originalText);
            }
        } catch (error) {
            console.error("Status Update Error:", error);
            alert("A network error occurred.");
            resetButton(btn, originalText);
        }
    });

    // -- HELPER FUNCTIONS --
    function resetButton(btn, originalText) {
        btn.textContent = originalText;
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
    }

    function showToast(message) {
        const jsToast = document.getElementById('js-toast');
        const jsToastMessage = document.getElementById('js-toast-message');

        if (jsToast && jsToastMessage) {
            jsToastMessage.textContent = message;
            jsToast.classList.remove('hidden');

            setTimeout(() => {
                jsToast.classList.remove('translate-x-full', 'opacity-0');
                jsToast.classList.add('translate-x-0', 'opacity-100');
            }, 10);

            setTimeout(() => { location.reload(); }, 1500);
        } else {
            // Fallback if toast HTML doesn't exist
            alert(message);
            location.reload();
        }
    }

    // ==========================================
    // 6. TUTORIAL LOGIC (SEC STAFF: APPLICATIONS)
    // ==========================================

    // Helper function to force open a mock modal for the tutorial
    function openMockTutorialModal() {
        // Inject fake tutorial data
        document.getElementById('modalAppId').textContent = '2026-MOCK-001';
        document.getElementById('modalResearcher').textContent = 'Dr. Jane Doe (Tutorial)';
        document.getElementById('modalMethod').textContent = 'Bank Transfer';
        document.getElementById('modalRef').textContent = 'REF-123456789';
        document.getElementById('modalTitleStudy').textContent = 'Effects of AI on System Architecture';
        window.currentStudyTitle = 'Effects of AI on System Architecture';

        // Clear out any real docs if they happen to exist
        const basicSection = document.getElementById('basicRequirementsSection');
        if(basicSection) basicSection.innerHTML = '';

        const suppSection = document.getElementById('supplementarySection');
        if(suppSection) suppSection.innerHTML = '';

        // Render the internal mockup preview
        showInternalMock();

        // Open the modal
        document.getElementById('modalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // Initialize the Tour
    const isFirstLogin = @json(auth()->user()->is_first_login);
    const userId = @json(auth()->id());
    const storageKey = 'berc_tutorial_step_' + userId;

    // If they are no longer on their first login, wipe memory and abort
    if (!isFirstLogin) {
        localStorage.removeItem(storageKey);
    } else {
        const tourState = localStorage.getItem(storageKey);

        // Trigger ONLY if they arrived from the Dashboard
        if (tourState === 'sec_applications') {

            if (typeof window.driver === 'undefined') {
                console.error("Driver.js is missing on the Applications page!");
            } else {
                const driver = window.driver.js.driver;
                const tour = driver({
                    showProgress: true,
                    allowClose: false,
                    overlayColor: 'rgba(33, 60, 113, 0.75)',
                    nextBtnText: 'Next &rarr;',
                    prevBtnText: '&larr; Back',

                    onDestroyStarted: () => {
                        if (!tour.hasNextStep()) {
                            // Ensure modal is closed before leaving
                            closeModal();

                            // Move to the Calendar page next
                            localStorage.setItem(storageKey, 'sec_calendar');
                            tour.destroy();
                            window.location.href = "{{ route('secstaff.calendar') }}";
                        } else {
                            tour.destroy();
                        }
                    },

                    steps: [
                        {
                            element: '.app-card',
                            popover: {
                                title: 'The Applications Queue',
                                description: 'All newly submitted applications and resubmissions land here. When you click on a row, it opens the Document Verification panel.',
                                side: "top",
                                align: 'start',
                                onNextClick: () => {
                                    // Open the mock modal BEFORE moving to the next step
                                    openMockTutorialModal();
                                    tour.moveNext();
                                }
                            }
                        },
                        {
                            element: '.payment-info-panel',
                            popover: {
                                title: '1. The Checklist',
                                description: 'Here you can verify the researcher\'s payment details and see a checklist of every document they uploaded.',
                                side: "right",
                                align: 'start'
                            }
                        },
                        {
                            element: '.form-preview-panel',
                            popover: {
                                title: '2. Document Preview',
                                description: 'Clicking any document on the left will instantly display it here. You can review PDFs and images without needing to download them.',
                                side: "left",
                                align: 'center'
                            }
                        },
                        {
                            element: '.modal-footer',
                            popover: {
                                title: '3. The Final Verdict',
                                description: 'If a document is missing or invalid, click Reject to return it. If everything looks good, click Verify to forward it to the Committee.',
                                side: "top",
                                align: 'center',
                                onNextClick: () => {
                                    // Close the modal to cleanly show the final popover
                                    closeModal();
                                    tour.moveNext();
                                }
                            }
                        },
                        {
                            // Floating popover with no element
                            popover: {
                                title: 'Next Stop: The Calendar',
                                description: 'Now that you know how to screen applications, let\'s see how you track deadlines. Click below to continue.',
                                side: "bottom",
                                align: 'center',
                                doneBtnText: 'Next Page →' // Sends them to the secstaff.calendar page
                            }
                        }
                    ]
                });

                tour.drive();
            }
        }
    }
    </script>
<script>
function startManualTutorial() {
    if (typeof window.driver === 'undefined') {
        console.error("Driver.js is missing on the Applications page!");
        return;
    }

    const driver = window.driver.js.driver;

    const tour = driver({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(33, 60, 113, 0.75)',
        nextBtnText: 'Next →',
        prevBtnText: '← Back',

        onDestroyStarted: () => {
            if (!tour.hasNextStep()) {
                closeModal();
                tour.destroy();
                window.location.href = "{{ route('secstaff.calendar') }}";
            } else {
                tour.destroy();
            }
        },

        steps: [
            {
                element: '.app-card',
                popover: {
                    title: 'The Applications Queue',
                    description: 'All newly submitted applications and resubmissions land here. When you click on a row, it opens the Document Verification panel.',
                    side: "top",
                    align: 'start',
                    onNextClick: () => {
                        openMockTutorialModal();
                        tour.moveNext();
                    }
                }
            },
            {
                element: '.payment-info-panel',
                popover: {
                    title: '1. The Checklist',
                    description: 'Here you can verify the researcher’s payment details and see a checklist of every document they uploaded.',
                    side: "right",
                    align: 'start'
                }
            },
            {
                element: '.form-preview-panel',
                popover: {
                    title: '2. Document Preview',
                    description: 'Clicking any document on the left will instantly display it here. You can review PDFs and images without downloading them.',
                    side: "left",
                    align: 'center'
                }
            },
            {
                element: '.modal-footer',
                popover: {
                    title: '3. The Final Verdict',
                    description: 'If a document is missing or invalid, click Reject to return it. If everything looks good, click Verify to forward it to the Committee.',
                    side: "top",
                    align: 'center',
                    onNextClick: () => {
                        closeModal();
                        tour.moveNext();
                    }
                }
            },
            {
                popover: {
                    title: 'Next Stop: The Calendar',
                    description: 'Now that you know how to screen applications, let’s continue to the Calendar page.',
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
