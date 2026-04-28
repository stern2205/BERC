@extends('secretariat.layouts.app')

@section('content')
<style>
    /* ── KPI cards ── */
    .kpi-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem 1.25rem; }
    .kpi-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; margin-bottom: 6px; }
    .kpi-value { font-size: 30px; font-weight: 900; color: #111827; line-height: 1; }
    .kpi-value.red { color: #D32F2F; }

    /* ── Notification banner ── */
    .notif-banner { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem; }
    .notif-title { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #1e3a5f; }
    .notif-body { font-size: 11px; font-weight: 600; color: #D32F2F; margin-top: 2px; }

    /* ── Chart panel title ── */
    .chart-panel-title { font-size: 11px; font-weight: 900; letter-spacing: 0.10em; text-transform: uppercase; color: #111827; }

    /* ── Pill dropdowns ── */
    .pill-select {
        appearance: none; -webkit-appearance: none; font-size: 11px; font-weight: 700;
        border: 1px solid #d1d5db; border-radius: 6px; padding: 3px 22px 3px 10px; color: #374151;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 7px center;
        cursor: pointer; outline: none;
    }
    .pill-select:focus { border-color: #213C71; }

    /* ── Legend & Buttons ── */
    .leg { display:inline-flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#374151; }
    .leg-dot { width:12px; height:10px; border-radius:2px; flex-shrink:0; }
    .btn-print {
        font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
        color: #374151; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 16px;
        background: white; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-print:hover { background: #f9fafb; border-color: #213C71; color: #213C71; }

    /* ── About cards ── */
    .about-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem 1.25rem; cursor: pointer; transition: border-color 0.15s, box-shadow 0.15s; }
    .about-card:hover { border-color: #213C71; box-shadow: 0 2px 8px rgba(33,60,113,0.08); }

    /* ── Analytics chart card ── */
    .analytics-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .analytics-card-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #f3f4f6; background: linear-gradient(to right, #f8faff, #f9fafb); }
    .analytics-card-header-left { display: flex; align-items: center; gap: 8px; }
    .analytics-card-body { padding: 16px; }
    .accent-dot-blue { width: 8px; height: 8px; border-radius: 50%; background: #213C71; flex-shrink: 0; }
    .accent-dot-red { width: 8px; height: 8px; border-radius: 50%; background: #D32F2F; flex-shrink: 0; }
</style>

@php
    // Calculate total action required items
    $actionRequired = $kpi['reviewClassification'] + $kpi['assessmentForms'] + $kpi['decisionLetter'] +
                      $kpi['resubmissionValidation'] + $kpi['resubmissionForms'] + $kpi['revisionDecisionLetter'];
@endphp

<div class="flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-500">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mb-4">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/background.jpg') }}" alt="Campus Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-bsu-dark/70"></div>
        </div>

        <div id="tour-profile" class="relative z-10 p-4 sm:p-8 flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6">
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

    @if($actionRequired > 0 || $kpi['newToday'] > 0)
    <div class="mb-4 notif-banner">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="notif-title">Action Required!</p>
            <p class="notif-body">
                There are <span>{{ number_format($kpi['newToday']) }}</span> new applications today and
                <span>{{ number_format($actionRequired) }}</span> protocol tasks requiring your attention.
            </p>
        </div>
    </div>
    @endif

    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="kpi-card shadow-sm">
            <p class="kpi-label">New Applications Today</p>
            <p class="kpi-value">{{ number_format($kpi['newToday']) }}</p>
        </div>
        <div class="kpi-card shadow-sm">
            <p class="kpi-label">Total — {{ date('F Y') }}</p>
            <p class="kpi-value">{{ number_format($kpi['totalMonth']) }}</p>
        </div>
        <div class="kpi-card shadow-sm">
            <p class="kpi-label">Total Applications All Time</p>
            <p class="kpi-value">{{ number_format($kpi['totalApps']) }}</p>
        </div>
    </div>

    <div class="mb-3 flex items-center space-x-4">
        <h2 class="text-[11px] font-black text-bsu-dark uppercase tracking-widest whitespace-nowrap">Pending Original Applications</h2>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>
    <div id="tour-kpis" class="mb-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div class="kpi-card shadow-sm border-l-4 border-l-blue-600">
            <p class="kpi-label">Review Classification</p>
            <p class="kpi-value {{ $kpi['reviewClassification'] > 0 ? 'text-blue-600' : '' }}">{{ number_format($kpi['reviewClassification']) }}</p>
        </div>
        <div class="kpi-card shadow-sm border-l-4 border-l-amber-500">
            <p class="kpi-label">Assessment Forms</p>
            <p class="kpi-value {{ $kpi['assessmentForms'] > 0 ? 'text-amber-600' : '' }}">{{ number_format($kpi['assessmentForms']) }}</p>
        </div>
        <div class="kpi-card shadow-sm border-l-4 border-l-green-600">
            <p class="kpi-label">Decision Letter</p>
            <p class="kpi-value {{ $kpi['decisionLetter'] > 0 ? 'text-green-600' : '' }}">{{ number_format($kpi['decisionLetter']) }}</p>
        </div>
    </div>

    <div class="mb-3 flex items-center space-x-4">
        <h2 class="text-[11px] font-black text-brand-red uppercase tracking-widest whitespace-nowrap">Pending Revisions</h2>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>
    <div class="mb-8 grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div class="kpi-card shadow-sm border-l-4 border-l-brand-red">
            <p class="kpi-label">Resubmission Validation</p>
            <p class="kpi-value {{ $kpi['resubmissionValidation'] > 0 ? 'red' : '' }}">{{ number_format($kpi['resubmissionValidation']) }}</p>
        </div>
        <div class="kpi-card shadow-sm border-l-4 border-l-amber-500">
            <p class="kpi-label">Resubmission Forms</p>
            <p class="kpi-value {{ $kpi['resubmissionForms'] > 0 ? 'text-amber-600' : '' }}">{{ number_format($kpi['resubmissionForms']) }}</p>
        </div>
        <div class="kpi-card shadow-sm border-l-4 border-l-green-600">
            <p class="kpi-label">Revision Decision Letter</p>
            <p class="kpi-value {{ $kpi['revisionDecisionLetter'] > 0 ? 'text-green-600' : '' }}">{{ number_format($kpi['revisionDecisionLetter']) }}</p>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <h2 class="text-[12px] font-black text-brand-red uppercase tracking-widest whitespace-nowrap">Protocol Management</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <div id="tour-protocol-mgmt" class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

                <a href="{{ route('secretariat.evaluation') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['reviewClassification'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['reviewClassification'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Protocol Evaluation</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Classify and Assign Reviewers</p>
                    </div>
                </a>

                <a href="{{ route('secretariat.assessment') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['assessmentForms'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['assessmentForms'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Assessment Validation</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Review Assessment</p>
                    </div>
                </a>

                <a href="{{ route('secretariat.decision') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['decisionLetter'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['decisionLetter'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Decision Letter</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Create Letter</p>
                    </div>
                </a>
                <a href="{{ route('secretariat.reports') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">History & Reports</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">View History</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <h2 class="text-[12px] font-black text-brand-red uppercase tracking-widest whitespace-nowrap">Resubmitted Protocol Management</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <div id="tour-resub-mgmt" class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <a href="{{ route('secretariat.revision_validation') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['resubmissionValidation'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['resubmissionValidation'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Resubmission Validation</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Validate Submissions</p>
                    </div>
                </a>

                <a href="{{ route('secretariat.revision_forms') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['resubmissionForms'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['resubmissionForms'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Resubmission Forms</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Validate Reviewer Comments</p>
                    </div>
                </a>

                <a href="{{ route('secretariat.revision.decision') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['revisionDecisionLetter'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['revisionDecisionLetter'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Revision Decision</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Create Decision Letter</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div id="tour-analytics" class="mb-6">
        <div class="flex items-center justify-between mb-4">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <div class="analytics-card-header-left">
                        <div class="accent-dot-blue"></div>
                        <p class="chart-panel-title">Application Status Analytics</p>
                    </div>
                    <select class="pill-select" id="statusTimeFilter" onchange="updateStatusChart()">
                        <option value="all">All Time</option>
                        @foreach($chartData['appYear']['years'] as $yr)
                            <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
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
                            @foreach($chartData['appYear']['years'] as $yr)
                                <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
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
                    @foreach($chartData['appYear']['years'] as $yr)
                        <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
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
                        <p class="chart-panel-title">Avg Revisions per Protocol</p>
                    </div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider border border-gray-200 rounded px-2 py-0.5">All Years</span>
                </div>
                <div class="analytics-card-body">
                    <div style="height:180px;"><canvas id="avgRevChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-base sm:text-xl font-bold text-gray-800 mb-3 mt-6 uppercase tracking-tight">About BERC</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-4">
        <div class="about-card group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform inline-block">📘</div>
            <h3 class="font-bold text-gray-800 text-xs sm:text-sm">Staff Handbook</h3>
            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold mt-1 tracking-wider group-hover:text-bsu-dark">View Guide →</p>
        </div>
        <div class="about-card group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform inline-block">📅</div>
            <h3 class="font-bold text-gray-800 text-xs sm:text-sm">Review Schedule</h3>
            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold mt-1 tracking-wider group-hover:text-bsu-dark">Access Tool →</p>
        </div>
        <div class="about-card group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform inline-block">🎓</div>
            <h3 class="font-bold text-gray-800 text-xs sm:text-sm">Ethics Guidelines</h3>
            <span class="text-[8px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-black uppercase mt-1 inline-block">URGENT</span>
        </div>
        <div class="about-card group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform inline-block">📝</div>
            <h3 class="font-bold text-gray-800 text-xs sm:text-sm">Special Cases</h3>
            <span class="text-[8px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-black uppercase mt-1 inline-block">NEW</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

    // Dynamically inject backend chart data
    const CHART_DATA = {!! json_encode($chartData) !!};
    const currentYear = '{{ date('Y') }}';

    // UI Configuration mappings
    const UI_CONFIG = {
        status: {
            labels: ['Pending', 'For Review', 'Approved', 'For Revision', 'Rejected'],
            colors: [BSU, BLUE2, GREEN, AMBER, RED]
        },
        typeStudy: {
            labels: [
                'Clinical Trial (Sponsored)', 'Clinical Trials (Researcher-initiated)',
                'Health Operations Research', 'Social-Behavioral Research',
                'Public Health-Epidemiologic', 'Biomedical Research (Retrospective/Prospective)',
                'Stem Cell Research', 'Others'
            ],
            colors: [BSU, BLUE2, STEEL, TEAL, AMBER, GREEN, PINK, RED]
        }
    };

    // 1. Application Status (horizontal bar)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: UI_CONFIG.status.labels,
            datasets: [{ label: 'Applications', data: CHART_DATA.status.byYear[currentYear] || CHART_DATA.status.byYear.all, backgroundColor: UI_CONFIG.status.colors, borderRadius: 5, borderSkipped: false }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.x} application(s)` } } },
            scales: {
                x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } }, border: { display: false } },
                y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' } }, border: { display: false } }
            }
        }
    });
    function updateStatusChart() {
        const yr = document.getElementById('statusTimeFilter').value;
        statusChart.data.datasets[0].data = CHART_DATA.status.byYear[yr];
        statusChart.update('active');
    }

    // 2. Monthly Application Trend (line)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    const gradTrend = ctxTrend.createLinearGradient(0, 0, 0, 195);
    gradTrend.addColorStop(0, 'rgba(33,60,113,0.15)'); gradTrend.addColorStop(1, 'rgba(33,60,113,0)');
    const trendChart = new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{ label: 'Applications', data: CHART_DATA.appMonth.all, borderColor: BSU, backgroundColor: gradTrend, fill: true, tension: 0.4, pointBackgroundColor: '#fff', pointBorderColor: BSU, pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, borderWidth: 2.5 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } } },
            scales: {
                x: { grid: { color: '#f9fafb' }, ticks: { font: { size: 10 } }, border: { display: false } },
                y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } }
            }
        }
    });
    function updateTrendChart() {
        const yr = document.getElementById('trendYearFilter').value;
        trendChart.data.datasets[0].data = CHART_DATA.appMonth[yr] || CHART_DATA.appMonth.all;
        trendChart.update('active');
    }

    // 3. Type of Study (horizontal bar)
    const ctxType = document.getElementById('typeStudyChart').getContext('2d');
    const typeStudyChart = new Chart(ctxType, {
        type: 'bar',
        data: {
            labels: UI_CONFIG.typeStudy.labels,
            datasets: [{ label: 'Applications', data: CHART_DATA.typeStudy.byYear[currentYear] || CHART_DATA.typeStudy.byYear.all, backgroundColor: UI_CONFIG.typeStudy.colors, borderRadius: 5, borderSkipped: false }]
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
        typeStudyChart.data.datasets[0].data = CHART_DATA.typeStudy.byYear[yr];
        typeStudyChart.update('active');
    }

    // 4. Applications per Year (bar)
    new Chart(document.getElementById('appYearChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: CHART_DATA.appYear.years,
            datasets: [{
                label: 'Applications',
                data: CHART_DATA.appYear.totals,
                backgroundColor: CHART_DATA.appYear.years.map((y, i) => i === CHART_DATA.appYear.years.length - 1 ? 'rgba(33,60,113,0.35)' : BSU),
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} application(s)` } } },
            scales: { x: { grid: { display: false }, border: { display: false } }, y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false }, ticks: { precision:0 } } }
        }
    });

    // 5. Average Revisions per Protocol (bar)
    new Chart(document.getElementById('avgRevChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: CHART_DATA.avgRevYear.years,
            datasets: [{
                label: 'Avg Revisions',
                data: CHART_DATA.avgRevYear.averages,
                backgroundColor: RED,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...sharedTooltip, callbacks: { label: ctx => `  ${ctx.parsed.y} avg revision(s)` } } },
            scales: { x: { grid: { display: false }, border: { display: false } }, y: { grid: { color: '#f3f4f6' }, beginAtZero: true, border: { display: false } } }
        }
    });

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    function loadDriverThenRun(callback) {
        if (typeof window.driver !== 'undefined') {
            callback();
            return;
        }

        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css';
        document.head.appendChild(css);

        const styleOverride = document.createElement('style');
        styleOverride.innerHTML = `
            .driver-popover { font-family:'Inter',sans-serif!important;border-radius:12px!important;border:1px solid #E5E7EB!important;padding:20px!important; }
            .driver-popover-title { color:#213C71!important;font-weight:900!important;text-transform:uppercase!important;letter-spacing:.05em!important;font-size:14px!important; }
            .driver-popover-description { color:#6B7280!important;font-weight:500!important;font-size:12px!important;margin-top:8px!important;line-height:1.5!important; }
            .driver-popover-footer button { border-radius:8px!important;font-weight:700!important;font-size:11px!important;text-transform:uppercase!important;letter-spacing:.05em!important;padding:8px 12px!important; }
            .driver-popover-next-btn { background:#D32F2F!important;color:#fff!important;border:none!important; }
            .driver-popover-next-btn:hover { background:#b91c1c!important; }
            .driver-popover-prev-btn { background:#F3F4F6!important;color:#4B5563!important;border:none!important; }
            .driver-popover-prev-btn:hover { background:#E5E7EB!important; }
        `;
        document.head.appendChild(styleOverride);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runSecretariatDashboardTutorial(manual = false) {
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (manual) {
            localStorage.removeItem(storageKey);
            localStorage.setItem(storageKey, 'secretariat_dashboard');
        }

        const driver = window.driver.js.driver;

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    localStorage.setItem(storageKey, 'secretariat_calendar');
                    tour.destroy();
                    window.location.href = "{{ route('secretariat.calendar') ?? '/secretariat/calendar' }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-profile',
                    popover: {
                        title: 'Welcome Secretariat!',
                        description: 'This is your central command dashboard. You serve as the bridge between Researchers, Reviewers, and the Committee Chair.',
                        side: "bottom",
                        align: "start"
                    }
                },
                {
                    element: '#tour-kpis',
                    popover: {
                        title: 'Original Applications Hub',
                        description: 'These indicators track newly submitted protocols and their progress.',
                        side: "bottom",
                        align: "start"
                    }
                },
                {
                    element: '#tour-protocol-mgmt',
                    popover: {
                        title: 'Primary Workflows',
                        description: 'These modules manage initial submissions, assessments, and decisions.',
                        side: "top",
                        align: "start"
                    }
                },
                {
                    element: '#tour-resub-mgmt',
                    popover: {
                        title: 'Resubmissions Handling',
                        description: 'Updated researcher submissions are validated here.',
                        side: "top",
                        align: "start"
                    }
                },
                {
                    element: '#tour-analytics',
                    popover: {
                        title: 'Data & Reporting',
                        description: 'View analytics, workload statistics, and printable reports.',
                        side: "top",
                        align: "start"
                    }
                },
                {
                    popover: {
                        title: 'Next Stop: Calendar',
                        description: 'Let’s continue to the Calendar page.',
                        side: "bottom",
                        align: "center",
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        tour.drive();
    }

    // MANUAL BUTTON FROM LAYOUT
    window.startPageTutorial = function () {
        loadDriverThenRun(() => runSecretariatDashboardTutorial(true));
    };

    // AUTO FIRST LOGIN
    loadDriverThenRun(() => {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;
        const tourState = localStorage.getItem(storageKey);

        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!tourState || tourState === 'secretariat_dashboard') {
            runSecretariatDashboardTutorial(false);
        }
    });

});
</script>
@endsection
