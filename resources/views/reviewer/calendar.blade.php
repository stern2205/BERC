@extends('reviewer.layouts.app')

@push('head')
<style>
        body { background-color: #f4f6fb; font-family: 'Inter', sans-serif; }
        .nav-tab-active { border-bottom: 3px solid #c21c2c; color: #1f3771; }

        .legend-item {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 500; color: #374151; white-space: nowrap;
        }
        .legend-box {
            width: 13px; height: 13px; border-radius: 3px;
            border: 1.5px solid; flex-shrink: 0;
        }
        .lbox-deadline    { background: #fee2e2; border-color: #fca5a5; }
        .lbox-secretariat { background: #dbeafe; border-color: #93c5fd; }
        .lbox-reviewer    { background: #fef9c3; border-color: #fde047; }
        .lbox-assigned    { background: #dcfce7; border-color: #86efac; }
        .lbox-meeting     { background: #fef3c7; border-color: #fcd34d; }
        .lbox-cutoff      { background: #f3e8ff; border-color: #d8b4fe; }

        .cal-outer {
            background: white; border-radius: 14px; border: 1px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden; width: 100%;
        }
        .cal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px 10px; background: white; flex-wrap: wrap; gap: 8px;
        }
        .cal-title-row { display: flex; align-items: center; gap: 8px; }
        .cal-icon { color: #c21c2c; flex-shrink: 0; }
        .cal-title { font-size: 16px; font-weight: 900; color: #1f3771; letter-spacing: 0.04em; text-transform: uppercase; }
        .cal-selects { display: flex; gap: 8px; }
        .cal-select {
            appearance: none; -webkit-appearance: none;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 8px center;
            border: 1.5px solid #d1d5db; border-radius: 8px; padding: 5px 26px 5px 10px;
            font-size: 12px; font-weight: 600; color: #374151; cursor: pointer; min-width: 95px;
        }
        .cal-select:focus { outline: none; border-color: #1f3771; }

        .cal-grid-card {
            background: #e4eaf6; margin: 0 12px 12px;
            border-radius: 10px; overflow: hidden; border: 1px solid #c4cfe8;
        }
        .cal-day-headers { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
        .cal-day-hdr {
            text-align: center; font-size: 10px; font-weight: 800; letter-spacing: 0.08em;
            text-transform: uppercase; color: #374151; padding: 8px 0 6px; min-width: 0;
        }
        /* Highlight Wednesday header */
        .cal-day-hdr.wed-hdr { color: #f97316; }

        .cal-grid-new {
            display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px; padding: 0 4px 4px;
        }
        .cal-cell-new {
            background: white; border-radius: 6px; min-height: 96px; padding: 6px 6px 4px;
            cursor: pointer; transition: box-shadow 0.12s; border: 1.5px solid transparent;
            min-width: 0; overflow: hidden;
        }
        .cal-cell-new:hover { box-shadow: 0 2px 6px rgba(31,55,113,0.13); border-color: #b8c9f0; }
        .cal-cell-new.other-month-new { background: #edf0f8; opacity: 0.55; }
        .cal-cell-new.meeting-day-cell {
            background: #fde68a;
            border: 1.5px solid #f59e0b !important;
        }
        .cal-cell-new.meeting-day-cell .day-num-new { color: #78350f; }
        .cal-cell-new.meeting-day-cell .meeting-badge { background: #f97316; color: #fafBf9; }
        .cal-cell-new.meeting-day-cell .ev-chip-meeting { background: rgba(249,115,22,0.12); color: #78350f; border-color: transparent; }
        .cal-cell-new.has-deadline     { background: #fff5f5; }
        .cal-cell-new.cutoff-day {
            background: #a0b4e0;
            border: 1.5px solid #1f3771 !important;
        }
        .cal-cell-new.cutoff-day .day-num-new { color: #0e1f45; }
        .cal-cell-new.cutoff-day .cutoff-badge { background: #1f3771; color: #fafBf9; }
        .cal-cell-new.cutoff-day .ev-chip-cutoff { background: rgba(31,55,113,0.15); color: #0e1f45; border-color: transparent; }
        .cal-cell-new.selected-cell    { border-color: #1f3771 !important; box-shadow: 0 0 0 2px rgba(31,55,113,0.18); }

        .cutoff-badge {
            display: inline-flex; align-items: center; background: #1f3771; color: white;
            font-size: 7px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase;
            padding: 1px 6px; border-radius: 999px; margin-bottom: 3px;
        }



        .day-num-new { font-size: 12px; font-weight: 700; color: #1f3771; margin-bottom: 4px; line-height: 1; }
        .day-num-new.other-month-num { color: #9ca3af; }
        .day-num-today {
            background: #1f3771; color: white; width: 22px; height: 22px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 900; margin-bottom: 4px;
        }

        .meeting-badge {
            display: inline-flex; align-items: center; background: #f97316; color: white;
            font-size: 7px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase;
            padding: 1px 6px; border-radius: 999px; margin-bottom: 3px;
        }
        .deadline-badge {
            display: inline-flex; align-items: center; background: #c21c2c; color: white;
            font-size: 7px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase;
            padding: 1px 6px; border-radius: 999px; margin-bottom: 3px;
        }

        .ev-chip-new {
            display: block; padding: 1px 5px; border-radius: 4px; font-size: 9px; font-weight: 500;
            margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            border: 1px solid transparent; max-width: 100%;
        }
        .ev-chip-secretariat { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
        .ev-chip-reviewer    { background: #fefce8; color: #854d0e; border-color: #fde68a; }
        .ev-chip-assigned    { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .ev-chip-deadline    { background: #fde8ea; color: #c21c2c; border-color: #f5b4b9; }
        .ev-chip-meeting     { background: #fef9c3; color: #713f12; border-color: #fef08a; }
        .ev-chip-cutoff      { background: #eef2fb; color: #1f3771; border-color: #a0b4e0; }

        .side-event-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 10px 0; border-bottom: 1px solid #f3f4f6;
        }
        .side-event-date-box {
            min-width: 38px; text-align: center; background: #e8eef8;
            border-radius: 7px; padding: 5px 4px 4px;
        }
        .side-event-date-box.meeting  { background: #fef3c7; }
        .side-event-date-box.deadline { background: #fde8ea; }
        .side-event-date-box.reviewer { background: #fefce8; }
        .side-event-date-box.assigned { background: #dcfce7; }
        .side-event-date-box.cutoff   { background: #eef2fb; }
        .side-event-date-box .month-lbl { font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; }
        .side-event-date-box .day-lbl  { font-size: 18px; font-weight: 900; line-height: 1; color: #1f3771; }
        .side-event-date-box.meeting  .day-lbl { color: #92400e; }
        .side-event-date-box.deadline .day-lbl { color: #c21c2c; }
        .side-event-date-box.reviewer .day-lbl { color: #854d0e; }
        .side-event-date-box.assigned .day-lbl { color: #166534; }
        .side-event-date-box.cutoff   .day-lbl { color: #1f3771; }

        .loc-pill {
            display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px;
            border-radius: 999px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px;
        }
        .loc-pill-sec  { background: #dbeafe; color: #1e40af; }
        .loc-pill-rev  { background: #fef9c3; color: #92400e; }
        .loc-pill-asgn { background: #dcfce7; color: #166534; }
        .loc-pill-meet { background: #ffedd5; color: #9a3412; }
        .loc-pill-dead { background: #fde8ea; color: #c21c2c; }
        .loc-pill-cut  { background: #eef2fb; color: #1f3771; }

        .side-event-clickable {
            display: flex; text-decoration: none; cursor: pointer;
            border-radius: 8px; margin: -4px -6px; padding: 4px 6px; transition: background 0.15s;
        }
        .side-event-clickable:hover { background: #f0f4ff; }
        .side-event-clickable:hover .view-details-link { color: #c21c2c; }
        .side-event-clickable:hover .view-details-link svg { transform: translateX(3px); }

        .view-details-link {
            display: inline-flex; align-items: center; gap: 3px; font-size: 10px;
            font-weight: 700; color: #1f3771; text-decoration: none; margin-top: 5px;
            padding: 0; background: none; border: none; line-height: 1.4;
        }
        .view-details-link svg { transition: transform 0.15s; flex-shrink: 0; }

        .filter-btn {
            padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 700;
            border: 1.5px solid #e5e7eb; background: white; color: #6b7280;
            cursor: pointer; transition: all 0.15s; white-space: nowrap;
        }
        .filter-btn:hover { border-color: #1f3771; color: #1f3771; }
        .filter-btn.active { background: #1f3771; color: white; border-color: #1f3771; }

        /* Fixed meeting day indicator on Wednesday column header area */
        .meeting-col-indicator {
            font-size: 7px; font-weight: 900; color: #f97316;
            letter-spacing: 0.06em; text-transform: uppercase;
            display: block; margin-top: 1px;
        }

        @media (max-width: 768px) {
            .cal-cell-new  { min-height: 50px; }
            .ev-chip-new   { display: none; }
            .meeting-badge, .deadline-badge { display: none; }
            .event-dot-mobile { width: 5px; height: 5px; border-radius: 50%; display: inline-block; margin-right: 2px; }
        }
        @media print {
            header, footer, .no-print { display: none !important; }
            .cal-cell-new { min-height: 70px; }
        }
    </style>
@endpush

@section('content')
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4" id="tour-calendar-legend">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 rounded-full bg-brand-red"></div>
                <div>
                    <h2 class="text-[20px] font-black uppercase tracking-widest text-bsu-dark">Calendar</h2>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="legend-item"><span class="legend-box lbox-deadline"></span>Deadline</span>
                <span class="legend-item"><span class="legend-box lbox-secretariat"></span>At Secretariat</span>
                <span class="legend-item"><span class="legend-box lbox-reviewer"></span>At Reviewer</span>
                <span class="legend-item"><span class="legend-box lbox-assigned"></span>Assigned</span>
                <span class="legend-item"><span class="legend-box lbox-meeting"></span>Meeting Day</span>
                <span class="legend-item"><span class="legend-box lbox-cutoff"></span>Cut-off Date</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 mb-3 px-4 py-2.5 bg-white border border-[#dde5f5] rounded-xl shadow-sm text-[11px] font-semibold text-gray-600" id="tour-calendar-rules">
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-sm bg-[#f97316] inline-block"></span>
                <strong class="text-gray-800">Meeting Day:</strong> Every 2nd Wednesday of the month
            </span>
            <span class="text-gray-300">|</span>
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-sm bg-[#7c3aed] inline-block"></span>
                <strong class="text-gray-800">Application Cut-off:</strong> Every 15th of the month
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-3" id="tour-calendar-filters">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mr-1">Filter:</span>
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="deadline"><span style="color:#dc2626">●</span> Deadlines</button>
            <button class="filter-btn" data-filter="secretariat"><span style="color:#1e40af">●</span> At Secretariat</button>
            <button class="filter-btn" data-filter="reviewer"><span style="color:#92400e">●</span> At Reviewer</button>
            <button class="filter-btn" data-filter="assigned"><span style="color:#166534">●</span> Assigned</button>
            <button class="filter-btn" data-filter="meeting"><span style="color:#f97316">●</span> Meetings</button>
            <button class="filter-btn" data-filter="cutoff"><span style="color:#7c3aed">●</span> Cut-off Date</button>
        </div>

        <div class="flex flex-col lg:flex-row gap-4">

            <div class="flex-1 min-w-0 overflow-hidden" id="tour-calendar-grid">
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

            <div class="lg:w-72 xl:w-80 shrink-0" id="tour-calendar-sidebar">
                <div class="mb-3 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-bsu-dark mb-3">This Month Summary</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#dc2626] inline-block"></span>Deadlines
                            </span>
                            <span class="text-[13px] font-black text-red-700" id="statDeadline">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#1e40af] inline-block"></span>At Secretariat
                            </span>
                            <span class="text-[13px] font-black text-blue-800" id="statSecretariat">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#d97706] inline-block"></span>At Reviewer
                            </span>
                            <span class="text-[13px] font-black text-yellow-800" id="statReviewer">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#166534] inline-block"></span>Assigned
                            </span>
                            <span class="text-[13px] font-black text-green-800" id="statAssigned">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#D97706] inline-block"></span>Meeting Days
                            </span>
                            <span class="text-[13px] font-black text-[#92400e]" id="statMeeting">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[11px] font-semibold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#7c3aed] inline-block"></span>Cut-off Dates
                            </span>
                            <span class="text-[13px] font-black text-purple-800" id="statCutoff">—</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-28">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-black uppercase tracking-widest text-bsu-dark" id="sidebarTitle">Upcoming Today</p>
                    </div>
                    <div class="px-4 py-1 max-h-[420px] overflow-y-auto" id="sidebarEvents"></div>
                </div>
            </div>
        </div>

        <div id="dayModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-opacity">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 id="modalTitle" class="text-lg font-black text-bsu-dark uppercase tracking-widest">Date Details</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-red-600 bg-gray-200 hover:bg-red-100 rounded-full p-1.5 transition">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modalEvents" class="p-6 overflow-y-auto space-y-4 bg-gray-50/50">
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
@endsection

@push('scripts')
    <script>
        const MONTH_NAMES = ['January','February','March','April','May','June',
                            'July','August','September','October','November','December'];
        const DAY_HEADERS = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

        let activeFilter = 'all';

        // ══════════════════════════════════════════════════════
        // FIXED RULES & UTILS
        // ══════════════════════════════════════════════════════

        function getMeetingDay(year, month) {
            let count = 0;
            for (let d = 1; d <= 31; d++) {
                const date = new Date(year, month, d);
                if (date.getMonth() !== month) break;
                if (date.getDay() === 3) {
                    count++;
                    if (count === 2) return d;
                }
            }
            return 14;
        }

        function getCutoffDay() { return 15; }

        // ── Smart Label Parser ──
        function parseLabelData(rawLabel) {
            let cleanLabel = rawLabel || 'Untitled';
            let isResub = false;
            let revNum = '';
            let isDeadlineText = false;

            if (cleanLabel.includes('[RESUBMISSION]')) {
                isResub = true;
                cleanLabel = cleanLabel.replace(/\[RESUBMISSION\]/g, '').trim();
            }

            const revMatch = cleanLabel.match(/\(Rev (\d+)\)/);
            if (revMatch) {
                revNum = revMatch[1];
                cleanLabel = cleanLabel.replace(revMatch[0], '').trim();
            }

            if (cleanLabel.startsWith('DEADLINE:')) {
                isDeadlineText = true;
                cleanLabel = cleanLabel.replace('DEADLINE:', '').trim();
            }

            return { cleanLabel, isResub, revNum, isDeadlineText };
        }

        // ── Type & Location Helpers ──
        function typeColor(type) {
            return { meeting:'#f97316', deadline:'#dc2626', secretariat:'#1e40af', reviewer:'#92400e', assigned:'#166534', cutoff:'#7c3aed' }[type] || '#6b7280';
        }
        function typeLabel(type) {
            return { meeting:'Meeting', deadline:'Deadline', secretariat:'At Secretariat', reviewer:'At Reviewer', assigned:'Assigned', cutoff:'Cut-off' }[type] || 'Event';
        }
        function locationLabel(loc) {
            return { secretariat:'Secretariat', reviewer:'Reviewer', assigned:'Awaiting Acceptance', chair:'Chair', applicant:'Applicant (Revision)', completed:'Completed' }[loc] || '—';
        }
        function typePillClass(type) {
            return { meeting:'loc-pill-meet', deadline:'loc-pill-dead', secretariat:'loc-pill-sec', reviewer:'loc-pill-rev', assigned:'loc-pill-asgn', cutoff:'loc-pill-cut' }[type] || 'loc-pill-sec';
        }
        function typeDateBoxClass(type) {
            return { meeting:'meeting', deadline:'deadline', secretariat:'', reviewer:'reviewer', assigned:'assigned', cutoff:'cutoff' }[type] || '';
        }
        function typeChipClass(type) {
            return { meeting:'ev-chip-meeting', deadline:'ev-chip-deadline', secretariat:'ev-chip-secretariat', reviewer:'ev-chip-reviewer', assigned:'ev-chip-assigned', cutoff:'ev-chip-cutoff' }[type] || 'ev-chip-secretariat';
        }
        function shouldShow(ev) {
            if (activeFilter === 'all') return true;
            return ev.type === activeFilter;
        }

        // ── Event Sorter ──
        function sortEventsPriority(events) {
            const priority = { deadline: 1, meeting: 2, cutoff: 3, assigned: 4, secretariat: 5, reviewer: 6 };
            return [...events].sort((a, b) => (priority[a.type] || 99) - (priority[b.type] || 99));
        }

        // ── State ──
        let viewYear  = new Date().getFullYear();
        let viewMonth = new Date().getMonth();
        const todayD  = new Date().getDate();
        const todayM  = new Date().getMonth();
        const todayY  = new Date().getFullYear();
        let currentEvents = {};

        // ── Fetch Dynamic Data ──
        async function fetchDynamicEvents(year, month) {
            const apiMonth = month + 1;

            try {
                document.getElementById('calBody').innerHTML = `
                    <div class="col-span-7 py-12 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                        Loading schedule...
                    </div>`;

                const fetchUrl = "{{ url('/reviewer/calendar/events') }}?year=" + year + "&month=" + apiMonth;

                const response = await fetch(fetchUrl, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.status === 401) throw new Error("Session expired. Please refresh and log in again.");
                if (!response.ok) throw new Error(`Server Error: ${response.status}`);

                currentEvents = await response.json();
                buildCalendarGrid();

            } catch (error) {
                console.error('Fetch Error:', error);
                currentEvents = {};
                document.getElementById('calBody').innerHTML = `
                    <div class="col-span-7 py-12 text-center text-red-500 text-xs font-bold tracking-wide">
                        ${error.message}
                    </div>`;
            }
        }

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
            monthSel.addEventListener('change', () => { viewMonth = parseInt(monthSel.value); renderCalendar(); });
            yearSel.addEventListener('change',  () => { viewYear  = parseInt(yearSel.value);  renderCalendar(); });
        }

        // ── Build Event Card (Expandable Modal & Compact Sidebar) ──
        function makeEventCard(day, ev, isForModal = false) {
            const monthShort = MONTH_NAMES[viewMonth].substring(0, 3).toUpperCase();
            const dboxClass  = typeDateBoxClass(ev.type);

            const locCurrentLabel = ev.location ? locationLabel(ev.location) : typeLabel(ev.type);
            const parsedData = parseLabelData(ev.label);
            const parsedNotes = parseLabelData(ev.notes);

            // -- Common Elements --
            let resubBadge = parsedData.isResub
                ? `<span class="inline-block bg-purple-100 text-purple-800 border border-purple-200 px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider mr-1">↻ Resub (v${parsedData.revNum})</span>`
                : '';

            let statusPillColor = ev.location === 'chair' ? 'background:#ccfbf1;color:#0f766e;' :
                                ev.location === 'applicant' ? 'background:#ffedd5;color:#b45309;' : '';

            // --- Metadata HTML Block ---
            let metadataHtml = '';
            if (ev.details && ev.details.metadata) {
                const meta = ev.details.metadata;
                metadataHtml = `
                    <div class="mt-2 mb-4 bg-white border border-gray-200 shadow-sm rounded-lg p-3.5 text-[10px] text-gray-600">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <p><strong class="text-gray-800 uppercase tracking-widest text-[9px] block mb-0.5">Study Type:</strong> ${meta.study_type}</p>
                            <p><strong class="text-gray-800 uppercase tracking-widest text-[9px] block mb-0.5">Classification:</strong> ${meta.classification}</p>
                            <p><strong class="text-gray-800 uppercase tracking-widest text-[9px] block mb-0.5">Institution:</strong> ${meta.institution}</p>
                            <p><strong class="text-gray-800 uppercase tracking-widest text-[9px] block mb-0.5">Contact:</strong> ${ev.details.contact.email}<br>${ev.details.contact.phone}</p>
                        </div>
                    </div>
                `;
            }

            // --- Workflow HTML Block ---
            let detailsHtml = '';
            if (ev.details && ev.details.workflow && ev.details.workflow.length > 0) {
                detailsHtml = `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Current Workflow Status</p>
                        <div class="space-y-2">
                            ${ev.details.workflow.map(d => {
                                let badgeClass =
                                    d.color === 'green'  ? 'bg-green-100 text-green-700 border-green-200' :
                                    d.color === 'blue'   ? 'bg-blue-100 text-blue-700 border-blue-200' :
                                    d.color === 'red'    ? 'bg-red-100 text-red-700 border-red-200' :
                                    d.color === 'teal'   ? 'bg-teal-100 text-teal-700 border-teal-200' :
                                    d.color === 'orange' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-gray-100 text-gray-700 border-gray-200';

                                return `
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white border border-gray-100 rounded-lg p-2.5 shadow-sm gap-2">
                                    <span class="text-[11px] font-bold text-gray-700 truncate">👤 ${d.actor}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded border ${badgeClass} text-center sm:text-right whitespace-nowrap">
                                        ${d.action}
                                    </span>
                                </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            // ==========================================
            // 1. MODAL EXPANDABLE DESIGN
            // ==========================================
            if (isForModal) {
                const item = document.createElement('div');
                item.className = 'bg-white border border-gray-200 shadow-sm hover:shadow-md transition-shadow rounded-xl overflow-hidden';

                // --- 1. Determine Event Type ---
                const eventType = ev.type || '';
                const eventLabel = ev.label || '';
                const isAssessment = eventType === 'reviewer' || eventLabel.includes('ASSESSMENT');
                const isInvitation = eventType === 'assigned' || eventLabel.includes('INVITATION');

                // --- 2. Build Action Button HTML ---
                let actionBtnHtml = '';
                if (ev.appId) {
                    const assessmentUrl = "{{ route('reviewer.assessment') }}";
                    const invitationUrl = "{{ route('reviewer.invitations') }}";

                    if (isAssessment) {
                        actionBtnHtml = `<a href="${assessmentUrl}?open_assessment=${ev.appId}" class="mt-4 block text-center w-full bg-[#1f3771] text-white text-[10px] font-bold uppercase tracking-widest py-2.5 rounded-lg hover:bg-blue-900 transition">OPEN ASSESSMENT FORM</a>`;
                    } else if (isInvitation) {
                        actionBtnHtml = `<a href="${invitationUrl}?open_invitation=${ev.appId}" class="mt-4 block text-center w-full bg-[#1f3771] text-white text-[10px] font-bold uppercase tracking-widest py-2.5 rounded-lg hover:bg-blue-900 transition">VIEW INVITATION</a>`;
                    }
                }

                item.innerHTML = `
                    <button type="button" class="w-full flex items-center justify-between p-4 focus:outline-none hover:bg-gray-50 transition-colors toggle-btn text-left">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="side-event-date-box ${dboxClass} shrink-0">
                                <div class="month-lbl">${monthShort}</div>
                                <div class="day-lbl">${day}</div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    ${ev.appId ? `<span class="text-[10px] font-bold text-brand-red uppercase tracking-widest bg-red-50 px-2 py-0.5 rounded border border-red-100">${ev.appId}</span>` : ''}
                                    ${resubBadge}
                                    <span class="loc-pill ${typePillClass(ev.type)} text-[9px] px-2 py-0.5 shadow-sm">
                                        ${typeLabel(ev.type)}
                                    </span>
                                </div>
                                <h4 class="text-[13px] font-black text-bsu-dark leading-tight truncate pr-4">${parsedData.cleanLabel}</h4>
                            </div>
                        </div>
                        <div class="shrink-0 ml-2 bg-gray-100 rounded-full p-2 text-gray-500">
                            <svg class="w-4 h-4 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    <div class="hidden expandable-content border-t border-gray-100 bg-[#FAFAFA] p-5">
                        ${ev.applicant ? `<p class="text-[12px] text-gray-700 mb-4"><strong class="text-gray-400 uppercase text-[10px] tracking-widest mr-2">Principal Investigator:</strong> <br>${ev.applicant}</p>` : ''}

                        ${parsedNotes.cleanLabel ? `
                            <div class="mb-4 text-[11px] text-orange-800 bg-orange-50 border border-orange-100 p-3 rounded-lg flex gap-2.5 items-start">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="leading-relaxed font-medium">${parsedNotes.cleanLabel}</p>
                            </div>`
                        : ''}

                        ${metadataHtml}
                        ${detailsHtml}
                        ${actionBtnHtml}
                    </div>
                `;

                // Vanilla JS Event Listener to handle expanding/collapsing
                const toggleBtn = item.querySelector('.toggle-btn');
                const content = item.querySelector('.expandable-content');
                const chevron = item.querySelector('.chevron-icon');

                toggleBtn.addEventListener('click', () => {
                    content.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                });

                return item;
            }

            // ==========================================
            // 2. SIDEBAR CONDENSED DESIGN
            // ==========================================
            else {
                const eventType = ev.type || '';
                const eventLabel = ev.label || '';
                const isAssessment = eventType === 'reviewer' || eventLabel.includes('ASSESSMENT');
                const isInvitation = eventType === 'assigned' || eventLabel.includes('INVITATION');

                // ALL clickable events must now be anchor tags so we can leave the Calendar page
                const wrapper = ev.appId ? document.createElement('a') : document.createElement('div');
                wrapper.className = 'side-event-item block transition-colors ' +
                    (ev.appId ? 'side-event-clickable hover:bg-gray-50' : 'cursor-pointer hover:bg-red-50');

                if (ev.appId) {
                    // Grab the base URLs directly from Laravel's router
                    const assessmentUrl = "{{ route('reviewer.assessment') }}";
                    const invitationUrl = "{{ route('reviewer.invitations') }}";

                    if (isAssessment) {
                        // Redirect to the assessment page
                        wrapper.setAttribute('href', `${assessmentUrl}?open_assessment=${ev.appId}`);
                    } else if (isInvitation) {
                        // Redirect to the invitation page
                        wrapper.setAttribute('href', `${invitationUrl}?open_invitation=${ev.appId}`);
                    }
                } else {
                    wrapper.addEventListener('click', (e) => {
                        e.preventDefault();
                        alert('Error: This event does not have an associated protocol ID.');
                    });
                }

                wrapper.innerHTML = `
                    <div class="side-event-date-box ${dboxClass}">
                        <div class="month-lbl">${monthShort}</div>
                        <div class="day-lbl">${day}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        ${ev.appId ? `<p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">${ev.appId}</p>` : ''}
                        <div>${resubBadge}</div>
                        <p class="text-xs font-bold text-bsu-dark leading-tight" style="word-break:break-word;white-space:normal;">${parsedData.cleanLabel}</p>

                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span class="loc-pill ${typePillClass(ev.type)} shadow-sm">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                                ${typeLabel(ev.type)}
                            </span>
                            ${ev.location ? `<span class="loc-pill shadow-sm" style="${statusPillColor || ''}">📍 ${locCurrentLabel}</span>` : ''}
                        </div>
                    </div>
                `;

                return wrapper;
            }
        }

        // ── Modal Handling ──
        function openDayModal(day, evList) {
            const modal = document.getElementById('dayModal');
            const titleEl = document.getElementById('modalTitle');
            const bodyEl = document.getElementById('modalEvents');

            // Format the date for the title (e.g., "Wednesday, March 14, 2026")
            const dateObj = new Date(viewYear, viewMonth, day);
            const dayName = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][dateObj.getDay()];
            titleEl.textContent = `${dayName}, ${MONTH_NAMES[viewMonth]} ${day}, ${viewYear}`;

            bodyEl.innerHTML = '';

            const filtered = sortEventsPriority((evList || []).filter(shouldShow));

            if (filtered.length === 0) {
                bodyEl.innerHTML = `<p class="text-gray-400 text-center py-8 font-semibold">No scheduled events for this day.</p>`;
            } else {
                filtered.forEach(ev => bodyEl.appendChild(makeEventCard(day, ev, true)));
            }

            modal.classList.remove('hidden');
        }

        function closeDayModal() {
            document.getElementById('dayModal').classList.add('hidden');
        }

        document.getElementById('closeModalBtn').addEventListener('click', closeDayModal);
        document.getElementById('dayModal').addEventListener('click', (e) => {
            if (e.target.id === 'dayModal') closeDayModal();
        });


        // ── Month Sidebar ──
        function renderMonthSidebar(events) {
            const sidebar = document.getElementById('sidebarEvents');
            sidebar.innerHTML = '';

            // Get actual current date elements (using new variable names to prevent shadowing)
            const now = new Date();
            const currentDay = now.getDate();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();

            // Check if the UI is currently viewing the real-time month/year
            const isViewingCurrentMonth = (viewMonth === currentMonth && viewYear === currentYear);

            // We will now ONLY track counts for TODAY to ensure no "other" events show up
            let counts = { deadline: 0, secretariat: 0, reviewer: 0, assigned: 0, meeting: 0, cutoff: 0, chair: 0, applicant: 0 };
            let todayEventsOnly = [];

            // ONLY fetch and process if we are viewing the current month AND today has events
            if (isViewingCurrentMonth && events[currentDay]) {
                events[currentDay].forEach(ev => {
                    // Tally stats strictly for today
                    if (counts[ev.type] !== undefined) counts[ev.type]++;

                    // Add to display array if it passes the active filter
                    if (shouldShow(ev)) {
                        todayEventsOnly.push({ day: currentDay, ev: ev, isPast: false });
                    }
                });
            }

            // Sort today's events by priority
            todayEventsOnly.sort((a, b) => {
                const priority = { deadline: 1, meeting: 2, cutoff: 3, assigned: 4, secretariat: 5, reviewer: 6, chair: 7, applicant: 8 };
                return (priority[a.ev.type] || 99) - (priority[b.ev.type] || 99);
            });

            // Render the sidebar cards
            todayEventsOnly.forEach(item => {
                const sideItem = makeEventCard(item.day, item.ev, false);
                sidebar.appendChild(sideItem);
            });

            // Empty state display
            if (todayEventsOnly.length === 0) {
                sidebar.innerHTML = `<p class="text-[11px] text-gray-400 text-center py-6 font-semibold">No events scheduled for today.</p>`;
            }

            // Update Quick Stats (Now explicitly restricted to TODAY'S counts)
            const statsMap = {
                'statDeadline': counts.deadline,
                'statSecretariat': counts.secretariat,
                'statReviewer': counts.reviewer,
                'statChair': counts.chair || 0,
                'statApplicant': counts.applicant || 0,
                'statAssigned': counts.assigned,
                'statMeeting': counts.meeting,
                'statCutoff': counts.cutoff
            };

            for (const [id, value] of Object.entries(statsMap)) {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            }
        }

        // ── DOM Manipulation (Calendar Grid) ──
        function buildCalendarGrid() {
            const meetingDay = getMeetingDay(viewYear, viewMonth);
            const cutoffDay  = getCutoffDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

            document.getElementById('calMonthLabel').textContent = MONTH_NAMES[viewMonth] + ' ' + viewYear;
            document.getElementById('monthSelect').value = viewMonth;
            document.getElementById('yearSelect').value  = viewYear;

            const firstDow = new Date(viewYear, viewMonth, 1).getDay();
            const headerEl = document.getElementById('calDayHeaders');
            headerEl.innerHTML = DAY_HEADERS.map((d, i) => {
                const isWed = i === 3;
                return `<div class="cal-day-hdr${isWed ? ' wed-hdr' : ''}">
                    ${d}${isWed ? '<span class="meeting-col-indicator">MTG</span>' : ''}
                </div>`;
            }).join('');

            const totalCells = Math.ceil((firstDow + daysInMonth) / 7) * 7;
            const prevDays   = new Date(viewYear, viewMonth, 0).getDate();
            const bodyEl     = document.getElementById('calBody');
            bodyEl.innerHTML = '';

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

                const dayEvs  = isCurrentMonth ? sortEventsPriority(currentEvents[day] || []) : [];
                const visEvs  = dayEvs.filter(shouldShow);

                if (isCurrentMonth) {
                    const isMeeting = day === meetingDay;
                    const isCutoff  = day === cutoffDay;
                    const isDeadline = dayEvs.some(e => e.type === 'deadline');

                    if (isMeeting)  cell.classList.add('meeting-day-cell');
                    if (isCutoff)   cell.classList.add('cutoff-day');
                    if (isDeadline && !isMeeting && !isCutoff) cell.classList.add('has-deadline');

                    // Allow cell click to open the modal
                    cell.addEventListener('click', () => {
                        if (visEvs.length > 0) {
                            openDayModal(day, dayEvs);
                        } else if (isMeeting || isCutoff) {
                            // Even if empty, allow click to show the fixed meeting notice
                            openDayModal(day, dayEvs);
                        }
                    });
                    cell.style.cursor = (visEvs.length > 0 || isMeeting || isCutoff) ? 'pointer' : 'default';
                }

                const numEl = document.createElement('div');
                if (isToday) {
                    numEl.innerHTML = `<div class="day-num-today">${day}</div>`;
                } else {
                    numEl.className = 'day-num-new' + (!isCurrentMonth ? ' other-month-num' : '');
                    numEl.textContent = day;

                    if (isCurrentMonth) {
                        if (day === meetingDay) {
                            const lbl = document.createElement('div');
                            lbl.style.cssText = 'font-size:7px;font-weight:900;color:#f97316;letter-spacing:.06em;text-transform:uppercase;margin-bottom:2px;';
                            lbl.textContent = '2nd WED';
                            numEl.appendChild(lbl);
                        }
                        if (day === cutoffDay) {
                            const lbl = document.createElement('div');
                            lbl.style.cssText = 'font-size:7px;font-weight:900;color:#7c3aed;letter-spacing:.06em;text-transform:uppercase;margin-bottom:2px;';
                            lbl.textContent = 'CUT-OFF';
                            numEl.appendChild(lbl);
                        }
                    }
                }
                cell.appendChild(numEl);

                // Event chips on the calendar grid
                if (isCurrentMonth && visEvs.length > 0) {
                    visEvs.forEach((ev, idx) => {
                        if (idx >= 3) {
                            if (idx === 3) {
                                const more = document.createElement('div');
                                more.className = 'ev-chip-new ev-chip-secretariat';
                                more.style.color = '#6b7280';
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
                        } else if (ev.type === 'deadline') {
                            const parsed = parseLabelData(ev.label);
                            const badge = document.createElement('div');
                            badge.className = 'deadline-badge';
                            badge.textContent = parsed.cleanLabel || 'DEADLINE';
                            cell.appendChild(badge);
                        } else if (ev.type === 'cutoff') {
                            const badge = document.createElement('div');
                            badge.className = 'cutoff-badge';
                            badge.textContent = 'CUT-OFF';
                            cell.appendChild(badge);
                        } else {
                            const parsed = parseLabelData(ev.label);
                            const chip = document.createElement('div');
                            chip.className = 'ev-chip-new ' + typeChipClass(ev.type);

                            let resubIcon = parsed.isResub ? '↻ ' : '';
                            let titleStr = parsed.cleanLabel.substring(0, 22) + (parsed.cleanLabel.length > 22 ? '…' : '');

                            chip.textContent = ev.appId ? `${ev.appId}: ${resubIcon}${titleStr}` : `${resubIcon}${titleStr}`;
                            cell.appendChild(chip);
                        }

                        const dot = document.createElement('span');
                        dot.className = 'event-dot-mobile hidden max-md:inline-block';
                        dot.style.background = typeColor(ev.type);
                        cell.appendChild(dot);
                    });
                }

                bodyEl.appendChild(cell);
            }

            // Always render the month summary in the sidebar
            renderMonthSidebar(currentEvents);
        }

        // ── App Init ──
        function renderCalendar() { fetchDynamicEvents(viewYear, viewMonth); }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                activeFilter = btn.dataset.filter;
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                buildCalendarGrid();
            });
        });

        buildDropdowns();
        renderCalendar();

        document.addEventListener('DOMContentLoaded', () => {
            // 1. Dynamic Injection: Ensure Driver.js is loaded
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

                // If they already changed their password, wipe memory and abort
                if (!isFirstLogin) {
                    localStorage.removeItem(storageKey);
                    return;
                }

                const tourState = localStorage.getItem(storageKey);

                // Trigger ONLY if they arrived from the Dashboard
                if (tourState === 'rev_calendar') {
                    const driver = window.driver.js.driver;
                    const tour = driver({
                        showProgress: true,
                        allowClose: false,
                        overlayColor: 'rgba(33, 60, 113, 0.75)',
                        nextBtnText: 'Next &rarr;',
                        prevBtnText: '&larr; Back',

                        onDestroyStarted: () => {
                            if (!tour.hasNextStep()) {
                                // Move to the Invitations page next
                                localStorage.setItem(storageKey, 'rev_invitations');
                                tour.destroy();
                                window.location.href = "{{ route('reviewer.invitations') ?? '/reviewer/invitations' }}";
                            } else {
                                tour.destroy();
                            }
                        },

                        steps: [
                            {
                                element: '#tour-calendar-legend',
                                popover: {
                                    title: 'Visual Tracking',
                                    description: 'Every protocol is color-coded based on its current status. As a reviewer, you will primarily look for items flagged as "Assigned" or "At Reviewer".',
                                    side: "bottom",
                                    align: 'start'
                                }
                            },
                            {
                                element: '#tour-calendar-rules',
                                popover: {
                                    title: 'Key Dates',
                                    description: 'Committee meeting days and application cut-off dates are automatically generated and marked on the calendar for your reference.',
                                    side: "bottom",
                                    align: 'start'
                                }
                            },
                            {
                                element: '#tour-calendar-filters',
                                popover: {
                                    title: 'Filter Views',
                                    description: 'If the calendar looks cluttered, use these buttons to isolate specific statuses—like filtering to show ONLY deadlines or applications waiting for your assessment.',
                                    side: "bottom",
                                    align: 'start'
                                }
                            },
                            {
                                element: '#tour-calendar-grid',
                                popover: {
                                    title: 'Interactive Grid',
                                    description: 'This is the main calendar. Clicking on any day with an event will pop up a detailed list of all protocols and deadlines occurring on that date.',
                                    side: "top",
                                    align: 'start'
                                }
                            },
                            {
                                element: '#tour-calendar-sidebar',
                                popover: {
                                    title: 'Monthly Summary',
                                    description: 'A quick breakdown of the month\'s schedule. Scroll down here to see an organized list of all upcoming assignments for the current month.',
                                    side: "left",
                                    align: 'start'
                                }
                            },
                            {
                                // Floating popover
                                popover: {
                                    title: 'Next Stop: Invitations',
                                    description: 'Now that you know how to track your deadlines, let\'s see what happens when you get assigned a new protocol to review. Click below to continue.',
                                    side: "bottom",
                                    align: 'center',
                                    doneBtnText: 'Next Page →' // Sends them to rev_invitations
                                }
                            }
                        ]
                    });

                    tour.drive();
                }
            }
        });
        </script>
@endpush
