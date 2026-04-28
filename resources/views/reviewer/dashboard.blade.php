@extends('reviewer.layouts.app')
@section('title', 'Reviewer Dashboard')
@section('header', 'Reviewer Dashboard')

@section('content')
<style>
    .rv-dashboard {
        color: #132746;
        display: grid;
        gap: 14px;
    }
    .rv-card {
        background: #fff;
        border: 1px solid #d9dde4;
        border-radius: 14px;
        overflow: hidden;
    }
    .notice {
        border: 1px solid #c6d8f0;
        border-radius: 14px;
        background: linear-gradient(155deg, #e9f1ff, #dbe8fb);
        padding: 12px 14px;
        color: #1f3564;
        font-weight: 700;
    }
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }
    .stat {
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #fff;
        padding: 12px;
    }
    .stat .k {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .stat .v {
        margin-top: 4px;
        color: #1f3564;
        font-size: 26px;
        font-weight: 800;
    }
    .panel-head {
        padding: 12px 14px;
        border-bottom: 1px solid #e6ebf1;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .panel-title {
        font-size: 13px;
        font-weight: 800;
        color: #1f3564;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .apps-link {
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        border-radius: 999px;
        padding: 6px 10px;
        line-height: 1;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
    }
    .apps-link.view { color: #1f3564; border-color: #c7d6f2; background: #eef3ff; }
    .apps-link.info { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
    .apps-link.success { color: #16a34a; border-color: #bbf7d0; background: #f0fdf4; }
    .table-wrap {
        overflow-x: auto;
        background: #fff;
    }
    .apps-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }
    .apps-table thead th {
        text-align: left;
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #7b8593;
        padding: 10px 14px;
        border-bottom: 1px solid #e1e6ee;
        background: #f6f7f9;
        font-weight: 700;
    }
    .apps-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #eaedf2;
        vertical-align: middle;
        font-weight: 600;
    }
    .apps-title {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
    }
    .apps-sub {
        font-size: 11px;
        color: #4b5563;
        font-weight: 600;
    }
    @media (max-width: 980px) {
        .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    /* Driver.js Custom Overrides for BSU Theme */
    .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
    .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
    .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
    .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
    .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
    .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
    .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
    .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
</style>

@php
    $totalActionRequired = $kpi['invitations'] + $kpi['assessments'] + $kpi['resubmissions'];
@endphp

<div class="rv-dashboard">
    <div class="notice" id="rv-notice">
        @if($totalActionRequired > 0)
            Action Required: You have {{ $kpi['invitations'] }} pending invitation(s), {{ $kpi['assessments'] }} assessment(s), and {{ $kpi['resubmissions'] }} resubmission(s) to review.
        @else
            You currently have no pending tasks. Great job!
        @endif
    </div>

    <section id="tour-stats" class="stats">
        <article class="stat"><div class="k">Total Assignments</div><div class="v">{{ $assignments->count() }}</div></article>
        <article class="stat"><div class="k">Active Tasks</div><div class="v">{{ $activeReviews->count() }}</div></article>
        <article class="stat"><div class="k">Pending Invitations</div><div class="v">{{ $pendingInvitations->count() }}</div></article>
        <article class="stat"><div class="k">History (Done/Declined)</div><div class="v">{{ $historyReviews->count() }}</div></article>
    </section>

    <div class="mb-6 sm:mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <h2 class="text-[12px] font-black text-brand-red uppercase tracking-widest whitespace-nowrap">Protocol Management</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

                <a id="tour-invitations-btn" href="{{ route('reviewer.invitations') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['invitations'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['invitations'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Invitations</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Accept or Reject Invitations</p>
                    </div>
                </a>

                <a id="tour-assessment-btn" href="{{ route('reviewer.assessment') }}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['assessments'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['assessments'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Protocol Assessment</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">View and Add Comments</p>
                    </div>
                </a>

                <a id="tour-resubmission-btn" href="{{ route('reviewer.resubmissions')}}" class="relative flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:border-brand-red hover:bg-gray-50 transition group shadow-sm">
                    @if($kpi['resubmissions'] > 0)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand-red rounded-full flex items-center justify-center shadow-md z-10">
                        <span class="text-white text-[10px] font-black">{{ $kpi['resubmissions'] }}</span>
                    </div>
                    @endif
                    <div class="bg-red-50 p-3 rounded-lg group-hover:bg-red-100 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div class="text-left min-w-0">
                        <p class="font-bold text-[13px] text-bsu-dark uppercase leading-tight truncate">Resubmissions</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">Review Revised Protocols</p>
                    </div>
                </a>

            </div>
        </div>
    </div>

    <section id="tour-snapshot" class="rv-card">
        <div class="panel-head">
            <h3 class="panel-title">Active Tasks Snapshot</h3>
            <a href="{{ route('reviewer.invitations') }}" class="apps-link view">View All</a>
        </div>
        <div class="table-wrap">
            <table class="apps-table">
                <thead>
                    <tr>
                        <th>Proposal</th><th>Researcher</th><th>Type</th><th>Status</th><th>Assigned Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="dash-app-body">
                    {{-- Concatenate Pending Invitations and Active Reviews, sort by newest, take top 5 --}}
                    @forelse($pendingInvitations->concat($activeReviews)->sortByDesc('date_assigned')->take(5) as $app)
                        <tr>
                            <td>
                                <div class="apps-title">{{ $app->research_title }}</div>
                                <div class="apps-sub">{{ $app->protocol_code }}</div>
                            </td>
                            <td class="apps-sub">{{ $app->name_of_researcher }}</td>
                            <td class="apps-sub">{{ $app->review_classification ?? 'N/A' }}</td>
                            <td class="apps-sub">
                                <span style="text-transform: capitalize; font-weight: 700; color: {{ $app->status === 'Pending' ? '#D97706' : '#16A34A' }};">
                                    {{ $app->status === 'Accepted' ? 'In Progress' : 'Pending Invite' }}
                                </span>
                            </td>
                            <td class="apps-sub">{{ \Carbon\Carbon::parse($app->date_assigned)->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    @if($app->status === 'Pending')
                                        <a href="{{ route('reviewer.invitations') }}" class="apps-link view">Review Invite</a>
                                    @else
                                        <a href="{{ route('reviewer.assessment') }}" class="apps-link success">Go to Assessment</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="apps-sub" style="text-align: center; padding: 20px;">No pending or active tasks found in your snapshot.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

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

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runReviewerDashboardTutorial(manual = false) {
        const isFirstLogin = @json(auth()->user()->is_first_login ?? false);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (manual) {
            localStorage.removeItem(storageKey);
            localStorage.setItem(storageKey, 'dashboard');
        }

        if (!manual && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        if (!manual && tourState && tourState !== 'dashboard') {
            return;
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
                    if (manual) {
                        localStorage.setItem(storageKey, 'rev_calendar_manual_skip');
                    } else {
                        localStorage.setItem(storageKey, 'rev_calendar');
                    }

                    tour.destroy();
                    window.location.href = "{{ route('reviewer.calendar') ?? '/reviewer/calendar' }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#rv-notice',
                    popover: {
                        title: 'Welcome Reviewer!',
                        description: 'This is your central hub. Urgent notifications regarding pending or overdue assignments will always appear right here at the top.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-stats',
                    popover: {
                        title: 'Your Workload',
                        description: 'Keep track of how many active reviews you currently have and easily spot if you have missed any deadlines.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-invitations-btn',
                    popover: {
                        title: 'Manage Invitations',
                        description: 'When the Secretariat assigns you a new protocol to review, it lands here first. You must accept the invitation before you can read the full document.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-assessment-btn',
                    popover: {
                        title: 'Conduct Assessments',
                        description: 'Once you accept an invitation, the protocol moves here. This is where you will read the actual files, fill out your assessment form, and cast your vote.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-resubmission-btn',
                    popover: {
                        title: 'Review Resubmissions',
                        description: 'When a researcher resubmits an updated protocol based on previous feedback, you will review their changes here.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-snapshot',
                    popover: {
                        title: 'Quick Access',
                        description: 'This table gives you a rapid overview of your active tasks and upcoming due dates so nothing slips through the cracks.',
                        side: "top",
                        align: 'start'
                    }
                },
                {
                    popover: {
                        title: 'Next Stop: Calendar',
                        description: 'Managing your deadlines is critical as a reviewer. Let’s look at how you can visualize your schedule on the Calendar next.',
                        side: "bottom",
                        align: 'center',
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        tour.drive();
    }

    window.startPageTutorial = function () {
        loadDriverThenRun(() => runReviewerDashboardTutorial(true));
    };

    loadDriverThenRun(() => runReviewerDashboardTutorial(false));
});
</script>
@endsection
