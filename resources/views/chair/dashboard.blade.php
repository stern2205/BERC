@extends('chair.layouts.app')

@php
    $currentMonthStr = date('F Y');
@endphp

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* ── KPI Cards ── */
        .system-kpi-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem 1.25rem; }
        .system-kpi-lbl { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; margin-bottom: 6px; }
        .system-kpi-val { font-size: 30px; font-weight: 900; color: #111827; line-height: 1; }
        .system-kpi-val.alert-red { color: #D32F2F; }

        /* ── Notification Banner ── */
        .alert-banner-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem; }
        .alert-banner-head { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #1e3a5f; }
        .alert-banner-text { font-size: 11px; font-weight: 600; color: #D32F2F; margin-top: 2px; }

        /* ── Typography & UI elements ── */
        .panel-heading { font-size: 11px; font-weight: 900; letter-spacing: 0.10em; text-transform: uppercase; color: #111827; }
        .nav-item-active { border-bottom: 3px solid #D32F2F; color: #213C71; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

        .action-btn-print {
            font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: #374151; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 16px;
            background: white; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        }
        .action-btn-print:hover { background: #f9fafb; border-color: #213C71; color: #213C71; }

        /* ── Analytics Elements ── */
        .chart-panel-title { font-size: 11px; font-weight: 900; letter-spacing: 0.10em; text-transform: uppercase; color: #111827; }
        .pill-select { appearance: none; -webkit-appearance: none; font-size: 11px; font-weight: 700; border: 1px solid #d1d5db; border-radius: 6px; padding: 3px 22px 3px 10px; color: #374151; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 7px center; cursor: pointer; outline: none; }
        .pill-select:focus { border-color: #213C71; }
        .leg { display:inline-flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#374151; }
        .leg-dot { width:12px; height:10px; border-radius:2px; flex-shrink:0; }
        .analytics-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .analytics-card-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #f3f4f6; background: linear-gradient(to right, #f8faff, #f9fafb); }
        .analytics-card-header-left { display: flex; align-items: center; gap: 8px; }
        .analytics-card-body { padding: 16px; }
        .accent-dot-blue { width: 8px; height: 8px; border-radius: 50%; background: #213C71; flex-shrink: 0; }
        .accent-dot-red { width: 8px; height: 8px; border-radius: 50%; background: #D32F2F; flex-shrink: 0; }

        /* ── About cards ── */
        .about-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem 1.25rem; cursor: pointer; transition: border-color 0.15s, box-shadow 0.15s; }
        .about-card:hover { border-color: #213C71; box-shadow: 0 2px 8px rgba(33,60,113,0.08); }
        .analytics-print-only { display: none; }

        /* ── Print button ── */
        .btn-print {
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: #374151; border: 1px solid #d1d5db;
            border-radius: 6px; padding: 6px 16px;
            background: white; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-print:hover { background: #f9fafb; border-color: #213C71; color: #213C71; }

        /* User's Print CSS */
        @media print {
            @page { margin: 12mm; }
            html, body { background: #fff !important; height: auto !important; }
            header, footer { display: none !important; }

            #dashboard-content > :not(.analytics-print-section) { display: none !important; }
            .analytics-print-section { display: block !important; margin: 0 !important; padding: 0 !important; }
            .analytics-print-exclude { display: none !important; }

            .analytics-print-only {
                display: block !important;
                font-size: 12px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 10px;
            }

            main, main > div { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }

            /* 🚨 THE FIX: Force ALL containers to unhide their overflow 🚨 */
            .analytics-card,
            .analytics-card-body,
            .print-expand-container,
            #performanceChartContainer {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
                max-height: none !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Force charts to print colors */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #printScrollWrapper {
                max-height: none !important;
                overflow: visible !important;
                height: auto !important;
            }
        }
</style>
@endpush

@section('content')
            <div id="dashboard-content" class="flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-500">

                <div id="tour-profile" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
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

                <div id="tour-notifications" class="mb-4 alert-banner-box">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="alert-banner-head">Important Notification!</p>
                        <p class="alert-banner-text">
                            There are <span>{{ $kpi['newToday'] ?? 0 }}</span> new applications today and
                            <span>{{ $kpi['notScreened'] ?? 0 }}</span> unclassified applications requiring attention.
                        </p>
                    </div>
                </div>

                <div id="tour-management" class="mb-6 sm:mb-8">
                    <div class="flex items-center space-x-4 mb-4">
                        <h2 class="text-[12px] font-black text-brand-red uppercase tracking-widest whitespace-nowrap">Chair Management</h2>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">

                            <a href="{{ route('chair.approval') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                                <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                                    <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">For Approval</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">Review Pending Protocols</p>
                                </div>
                            </a>

                            <a href="{{ route('chair.add-staff') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                                <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                                    <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Staff Management</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">Add or Manage Members</p>
                                </div>
                            </a>

                            <a href="{{ route('chair.history') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                                <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                                    <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">History & Reports</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">View Committee Records</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <div id="tour-analytics" class="mb-6 analytics-print-section">
                    <div class="analytics-print-only">Analytics Report</div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-5 rounded-full bg-brand-red"></div>
                            <h2 class="text-[12px] font-black uppercase tracking-widest text-bsu-dark">Analytics Overview</h2>
                            <span class="text-[9px] font-black bg-bsu-dark text-white px-2.5 py-0.5 rounded-full uppercase tracking-wide">{{ date('Y') }}</span>
                        </div>

                        <button class="btn-print analytics-print-exclude" onclick="window.print()">
                            <svg class="w-3.5 h-3.5 inline mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Analytics
                        </button>
                    </div>

                    <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 gap-3 analytics-print-exclude">
                        <div class="system-kpi-card shadow-sm">
                            <p class="system-kpi-lbl">New Applications Today</p>
                            <p class="system-kpi-val">{{ $kpi['newToday'] ?? 0 }}</p>
                        </div>
                        <div class="system-kpi-card shadow-sm">
                            <p class="system-kpi-lbl">Total - {{ $currentMonthStr }}</p>
                            <p class="system-kpi-val">{{ number_format($kpi['totalMonth'] ?? 0) }}</p>
                        </div>
                        <div class="system-kpi-card shadow-sm">
                            <p class="system-kpi-lbl">Not Yet Screened</p>
                            <p class="system-kpi-val alert-red">{{ $kpi['notScreened'] ?? 0 }}</p>
                        </div>
                        <div class="system-kpi-card shadow-sm">
                            <p class="system-kpi-lbl">Total Applications</p>
                            <p class="system-kpi-val">{{ number_format($kpi['totalApps'] ?? 0) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <div class="analytics-card-header-left">
                                    <div class="accent-dot-blue"></div>
                                    <p class="chart-panel-title">Application Status Analytics</p>
                                </div>
                                <select class="pill-select" id="statusTimeFilter" onchange="updateStatusChart()">
                                    <option value="all">All Time</option>
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
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
                                    <select class="pill-select" id="trendYearFilter" onchange="updateTrendChart()">
                                        <option value="all">All Years</option>
                                        <option value="2026" selected>2026</option>
                                        <option value="2025">2025</option>
                                        <option value="2024">2024</option>
                                    </select>
                                </div>
                            </div>
                            <div class="analytics-card-body">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="leg"><span class="leg-dot" style="background:#213C71; width:20px; border-radius:3px;"></span>Applications by Month</span>
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
                            <select class="pill-select" id="typeStudyYearFilter" onchange="updateTypeStudyChart()">
                                <option value="all">All Years</option>
                                <option value="2026" selected>2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                            </select>
                        </div>
                        <div class="analytics-card-body">
                            <div class="w-full overflow-x-auto">
                                <div style="min-width:400px; height:255px;"><canvas id="typeStudyChart"></canvas></div>
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
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">All Years</span>
                            </div>
                            <div class="analytics-card-body">
                                <div style="height:180px;"><canvas id="appYearChart"></canvas></div>
                            </div>
                        </div>

                        <div class="analytics-card">
                            <div class="analytics-card-header">
                                <div class="analytics-card-header-left">
                                    <div class="accent-dot-red"></div>
                                    <p class="chart-panel-title">Average Revisions per Protocol</p>
                                </div>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">All Years</span>
                            </div>
                            <div class="analytics-card-body">
                                <div style="height:180px;"><canvas id="avgRevYearChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div class="analytics-card mb-6">
                        <div class="analytics-card-header">
                            <div class="analytics-card-header-left">
                                <div class="accent-dot-blue"></div>
                                <p class="chart-panel-title">Reviewer Performance Overview</p>
                            </div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">All Active Staff</span>
                        </div>
                        <div class="p-5">
                            <div class="analytics-print-only hidden">Reviewer Performance Report - {{ date('F d, Y') }}</div>

                            <div id="printScrollWrapper" class="w-full overflow-y-auto overflow-x-hidden" style="max-height: 600px;">

                                <div id="performanceChartContainer" style="position: relative; width: 100%;">
                                    <canvas id="performanceChart"></canvas>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
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
            backgroundColor: '#1e293b', titleColor: '#f1f5f9', bodyColor: '#cbd5e1',
            borderColor: '#334155', borderWidth: 1, padding: 10, cornerRadius: 6,
            titleFont: { weight: '700', size: 11 }, bodyFont: { size: 11 },
        };

        // PULL DATA SECURELY FROM THE CONTROLLER
        const BACKEND_DATA = @json($chartData ?? []);

        const DATA = {
            status: {
                labels: ['Pending', 'For Review', 'Approved', 'For Revision', 'Rejected'],
                colors: [BSU, BLUE2, GREEN, AMBER, RED],
                byYear: BACKEND_DATA.status?.byYear || {}
            },
            appMonth: BACKEND_DATA.appMonth || {},
            appYear: BACKEND_DATA.appYear || { years: [], totals: [] },
            typeStudy: {
                labels: [
                    'Clinical Trial (Sponsored)', 'Clinical Trials (Researcher-initiated)',
                    'Health Operations Research', 'Social-Behavioral Research',
                    'Public Health-Epidemiologic', 'Biomedical Research (Retrospective/Prospective)',
                    'Stem Cell Research', 'Others'
                ],
                byYear: BACKEND_DATA.typeStudy?.byYear || {}
            },
            avgRevYear: BACKEND_DATA.avgRevYear || { years: [], averages: [] }
        };

        // 1. Application Status
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: DATA.status.labels,
                datasets: [{ label: 'Applications', data: DATA.status.byYear[2026] || [0,0,0,0,0], backgroundColor: DATA.status.colors, borderRadius: 5, borderSkipped: false }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.x} application(s)` } } },
                scales: {
                    x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, precision:0 }, border: { display: false } },
                    y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' } }, border: { display: false } }
                }
            }
        });
        function updateStatusChart() {
            const yr = document.getElementById('statusTimeFilter').value;
            statusChart.data.datasets[0].data = DATA.status.byYear[yr] || [0,0,0,0,0];
            statusChart.update('active');
        }

        // 2. Monthly Application Trend
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        const gradTrend = ctxTrend.createLinearGradient(0, 0, 0, 195);
        gradTrend.addColorStop(0, 'rgba(33,60,113,0.15)'); gradTrend.addColorStop(1, 'rgba(33,60,113,0)');
        const trendChart = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{ label: 'Applications', data: DATA.appMonth[2026] || DATA.appMonth.all || [], borderColor: BSU, backgroundColor: gradTrend, fill: true, tension: 0.4, pointBackgroundColor: '#fff', pointBorderColor: BSU, pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, borderWidth: 2.5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } } },
                scales: {
                    x: { grid: { color: '#f9fafb' }, ticks: { font: { size: 10 } }, border: { display: false } },
                    y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false }, ticks: { precision:0 } }
                }
            }
        });
        function updateTrendChart() {
            const yr = document.getElementById('trendYearFilter').value;
            trendChart.data.datasets[0].data = DATA.appMonth[yr] || DATA.appMonth.all || [];
            trendChart.update('active');
        }

        // 3. Type of Study
        const ctxType = document.getElementById('typeStudyChart').getContext('2d');
        const typeStudyChart = new Chart(ctxType, {
            type: 'bar',
            data: {
                labels: DATA.typeStudy.labels,
                datasets: [{ label: 'Applications', data: DATA.typeStudy.byYear[2026] || Array(8).fill(0), backgroundColor: [BSU, BLUE2, STEEL, TEAL, AMBER, GREEN, PINK, RED], borderRadius: 5, borderSkipped: false }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.x} application(s)` } } },
                scales: {
                    x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, precision:0 }, border: { display: false } },
                    y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' }, callback: function(val) { const lbl = this.getLabelForValue(val); return lbl.length > 34 ? lbl.substring(0, 32) + '…' : lbl; } }, border: { display: false } }
                }
            }
        });
        function updateTypeStudyChart() {
            const yr = document.getElementById('typeStudyYearFilter').value;
            typeStudyChart.data.datasets[0].data = DATA.typeStudy.byYear[yr] || Array(8).fill(0);
            typeStudyChart.update('active');
        }

        const perfData = @json($performanceData ?? []);

        if (perfData.length > 0 && document.getElementById('performanceChart')) {

            // 1. Calculate exact height: 80px per reviewer (to fit 3 bars) + 100px for legend/axes.
            // Math.max ensures the chart never shrinks below 250px so it always looks good.
            const requiredHeight = Math.max(250, (perfData.length * 80) + 100);
            document.getElementById('performanceChartContainer').style.height = requiredHeight + 'px';

            const labels = perfData.map(d => d.name + ' (' + d.type + ')');
            const avgAssessed = perfData.map(d => d.avg_assessed);
            const avgTime = perfData.map(d => d.avg_time);
            const declined = perfData.map(d => d.declined);

            const ctxPerf = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctxPerf, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Avg Protocols (Per Cut-off)',
                            data: avgAssessed,
                            backgroundColor: '#213C71',
                            borderRadius: 4
                        },
                        {
                            label: 'Avg Review Time (Days)',
                            data: avgTime,
                            backgroundColor: '#16A34A',
                            borderRadius: 4
                        },
                        {
                            label: 'Declined Invitations',
                            data: declined,
                            backgroundColor: '#D32F2F',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    indexAxis: 'y', // Horizontal bars
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: 'bold', family: 'Inter, sans-serif' } }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { family: 'Inter, sans-serif', size: 11, weight: 'bold' } },
                            padding: 20
                        }
                    }
                }
            });
        }

        function prepareAndPrint() {
            const container = document.getElementById('performanceChartContainer');
            const parentCard = container ? container.closest('.analytics-card') : null;

            if (container) {
                // Save original states
                const originalMaxHeight = container.style.maxHeight;
                const originalOverflow = container.style.overflowY;
                const originalCardOverflow = parentCard ? parentCard.style.overflow : '';

                // Strip constraints from the container AND the parent card
                container.style.maxHeight = 'none';
                container.style.overflowY = 'visible';
                if (parentCard) parentCard.style.overflow = 'visible';

                // Force the browser to recalculate the layout immediately
                void container.offsetHeight;

                // Give Chart.js 500ms to stretch out the canvas before taking the print snapshot
                setTimeout(() => {
                    window.print();

                    // Restore everything once the print dialog is closed
                    container.style.maxHeight = originalMaxHeight;
                    container.style.overflowY = originalOverflow;
                    if (parentCard) parentCard.style.overflow = originalCardOverflow;
                }, 500);
            } else {
                window.print();
            }
        }

        // 4. Applications per Year
        new Chart(document.getElementById('appYearChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: DATA.appYear.years,
                datasets: [{ label: 'Applications', data: DATA.appYear.totals, backgroundColor: DATA.appYear.years.map((y, i) => i === DATA.appYear.years.length - 1 ? 'rgba(33,60,113,0.35)' : BSU), borderRadius: 6, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } } },
                scales: { x: { grid: { display: false }, border: { display: false } }, y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false }, ticks: { precision:0 } } }
            }
        });

        // 5. Average Revisions per Protocol
        new Chart(document.getElementById('avgRevYearChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: DATA.avgRevYear.years,
                datasets: [{ label: 'Avg Revisions', data: DATA.avgRevYear.averages, backgroundColor: DATA.avgRevYear.years.map((y, i) => i === DATA.avgRevYear.years.length - 1 ? 'rgba(211,47,47,0.35)' : RED), borderRadius: 6, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} avg revisions` } } },
                scales: { x: { grid: { display: false }, border: { display: false } }, y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } } }
            }
        });

    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Dynamic CSS/JS Injection for Driver.js
        if (typeof window.driver === 'undefined') {
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css';
            document.head.appendChild(css);

            // Inject Custom BSU Styles dynamically so we don't break the original <style> block
            const styleOverride = document.createElement('style');
            styleOverride.innerHTML = `
                .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
                .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
                .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
                .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
                .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
                .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
                .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
                .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
            `;
            document.head.appendChild(styleOverride);

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

            // Start if no state exists or if they explicitly load the dashboard step
            if (!tourState || tourState === 'chair_dashboard') {
                const driver = window.driver.js.driver;
                const tour = driver({
                    showProgress: true,
                    allowClose: false,
                    overlayColor: 'rgba(33, 60, 113, 0.75)',
                    nextBtnText: 'Next &rarr;',
                    prevBtnText: '&larr; Back',

                    onDestroyStarted: () => {
                        if (!tour.hasNextStep()) {
                            // Move to Calendar page
                            localStorage.setItem(storageKey, 'chair_calendar');
                            tour.destroy();
                            window.location.href = "{{ route('chair.calendar') ?? '/chair/calendar' }}";
                        } else {
                            tour.destroy();
                        }
                    },

                    steps: [
                        {
                            element: '#tour-profile',
                            popover: {
                                title: 'Welcome Committee Chair!',
                                description: 'This is your executive dashboard. From here, you have oversight of the entire Ethics Review Committee operations.',
                                side: "bottom",
                                align: 'start'
                            }
                        },
                        {
                            element: '#tour-notifications',
                            popover: {
                                title: 'System Alerts',
                                description: 'Critical bottlenecks, such as unscreened applications or overdue reviews, will be flagged here to ensure timely processing.',
                                side: "bottom",
                                align: 'center'
                            }
                        },
                        {
                            element: '#tour-management',
                            popover: {
                                title: 'Executive Controls',
                                description: 'Use these tools to draft final decision letters, manage BERC staff accounts, and access the full committee archives.',
                                side: "bottom",
                                align: 'start'
                            }
                        },
                        {
                            element: '#tour-analytics',
                            popover: {
                                title: 'Data & Reporting',
                                description: 'Review high-level metrics, track submission trends over time, and print analytics reports directly from your dashboard.',
                                side: "top",
                                align: 'start'
                            }
                        },
                        {
                            // Floating Popover
                            popover: {
                                title: 'Next Stop: The Calendar',
                                description: 'Let\'s check the Calendar to see how you can manage committee meetings and application cut-off dates. Click below to continue.',
                                side: "bottom",
                                align: 'center',
                                doneBtnText: 'Next Page →'
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
