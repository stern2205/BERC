@extends('secretariat.layouts.app')

@section('content')
<style>
    /* Force vertical scrollbar to prevent page layout shift when switching tabs */
    html { overflow-y: scroll; }

    /* Base Colors */
    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    /* Main Container / Grid */
    .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); position: relative; }
    .card-header { display:flex; align-items:center; justify-content: flex-start; border-bottom:1px solid #e5e7eb; background:#fafafa; padding:0; overflow-x: auto; }

    /* Tabs Left Aligned */
    .card-tab { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; border-bottom:3px solid transparent; padding:14px 20px; cursor:pointer; transition:all 0.2s; white-space: nowrap; }
    .card-tab.active { color:var(--bsu-dark); border-bottom-color:var(--brand-red); background:#fff; }

    /* Standard Grid */
    .list-grid-header,
    .app-row {
        display: grid;
        grid-template-columns: minmax(120px, 0.95fr) minmax(260px, 2.3fr) minmax(180px, 1.4fr) minmax(150px, 1.2fr) minmax(140px, 1fr);
        padding: 8px 20px;
        align-items: center;
        gap: 12px;
    }
    .list-grid-header.reassign-grid,
    .app-row.reassign-row {
        grid-template-columns: minmax(120px, 0.95fr) minmax(250px, 2.2fr) minmax(210px, 1.6fr) minmax(280px, 2.2fr) minmax(220px, 1.8fr);
    }

    /* Grid Specifically for Awaiting Response Tab */
    .awaiting-grid-header,
    .awaiting-row {
        display: grid;
        grid-template-columns: minmax(120px, 0.95fr) minmax(250px, 2.2fr) minmax(210px, 1.6fr) minmax(280px, 2.2fr) minmax(220px, 1.8fr);
        padding: 8px 20px;
        align-items: center;
        gap: 12px;
    }

    .list-grid-header, .awaiting-grid-header {
        background: #f3f4f6;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }
    .list-grid-header > div,
    .awaiting-grid-header > div { text-align: left; }
    .list-grid-header > div.header-center,
    .awaiting-grid-header > div.header-center { text-align: center; }

    .app-row, .awaiting-row {
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background .15s;
    }
    .awaiting-row { cursor: default; align-items: center; } /* No click action for awaiting row currently */
    .app-row > div,
    .awaiting-row > div {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        text-align: left;
    }
    .app-row > div:nth-child(5),
    .awaiting-row > div:nth-child(4),
    .awaiting-row > div:nth-child(5) {
        align-items: center;
        text-align: center;
    }
    .app-row.reassign-row > div:nth-child(4) {
        align-items: stretch;
        text-align: left;
    }
    .app-row.reassign-row > div:nth-child(3) {
        align-items: flex-start;
        text-align: left;
    }
    .app-row.reassign-row > div.reassign-classification-cell {
        align-items: flex-start !important;
        justify-content: center !important;
        text-align: left !important;
    }
    .app-row.reassign-row > div.reassign-classification-cell > div {
        width: 100%;
        text-align: left;
    }
    .app-row.reassign-row > div.reassign-classification-cell .classification-pill {
        display: inline-flex;
        align-self: flex-start;
    }
    .action-cell { flex-direction: row !important; }
    .app-row.reassign-row > div.reviewers-cell,
    .awaiting-row > div.reviewers-cell {
        align-items: flex-start !important;
        justify-content: center !important;
        text-align: left !important;
    }
    .app-row.reassign-row > div.reviewers-cell .reviewers-inner,
    .awaiting-row > div.reviewers-cell .reviewers-inner {
        width: 100%;
        text-align: left;
    }
    .awaiting-row > div.reviewers-cell.reviewers-empty {
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
    }
    .app-row:last-child, .awaiting-row:last-child { border-bottom: none; }
    .app-row:hover, .awaiting-row:hover { background: #f9fafb; }

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
    .receiver-name { font-size:12px; font-weight:600; color:#374151; }
    .protocol-date-ref { font-size:11px; font-weight:700; color:#2563eb; font-family:monospace; }
    .workflow-action-link { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; transition:color 0.15s; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }


    /* Modal Styling */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; width:100%; max-width:1200px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); animation: lbIn .2s ease; position:relative; }

    @keyframes lbIn { from { opacity:0; transform: scale(.94); } to { opacity:1; transform: scale(1); } }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:#fafafa; gap:14px; }
    .modal-header h2 { font-size:14px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
    .modal-header-left { display:flex; align-items:center; min-width:0; flex:1; }
    .modal-header-right { display:flex; align-items:center; gap:10px; margin-left:auto; }
    .close-btn { font-size:20px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; transition:background .15s; }
    .close-btn:hover { background:#f3f4f6; color:#111; }

    .modal-content { display:flex; gap:0; overflow:hidden; flex:1; min-height:0; }

    /* Left Panel */
    .protocol-info-panel { width:260px; min-width:260px; border-right:1px solid #e5e7eb; padding:20px; background:#fafafa; overflow-y:auto; flex-shrink:0; }
    .info-group { margin-bottom:16px; }
    .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
    .info-value { font-size:13px; font-weight:700; color:#111827; }

    /* Documents Cards */
    .doc-card { background:#fff; border:1.5px solid #d1d5db; padding:10px; border-radius:6px; display:flex; align-items:center; gap:10px; margin-top:6px; cursor:pointer; transition:all .2s; user-select:none; }
    .doc-card:hover { border-color:var(--brand-red); box-shadow:0 0 0 3px rgba(211,47,47,.12); }
    .doc-card.active { border-color:var(--bsu-dark); box-shadow:0 0 0 3px rgba(33,60,113,.12); background:#f0f4ff; }
    .doc-chevron { width:14px; height:14px; color:#9ca3af; transition:transform .3s ease; flex-shrink:0; }
    .doc-card.active .doc-chevron { transform:rotate(90deg); color:var(--bsu-dark); }

    /* Right Panel */
    .form-preview-panel { flex:1 1 0; width:auto; padding:24px; overflow-y:auto; background:#fff; position:relative; }

    /* Document/Mock Form Styling */
    .application-form-mock { border:1px solid #d1d5db; border-radius:8px; padding:24px; font-size:11px; color:#374151; background:#fff; max-width:700px; margin:0 auto; box-shadow:0 4px 6px rgba(0,0,0,.02); }
    .form-header { text-align:center; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid var(--bsu-dark); }
    .form-header h3 { font-size:13px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }

    /* Editable Certificate */
    .cert-exemption { padding: 40px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; font-family: 'Times New Roman', Times, serif; color: black; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .cert-exemption:focus { outline: 2px solid var(--bsu-dark); }
    .cert-title { text-align: center; font-size: 20px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
    .cert-body { font-size: 14px; line-height: 1.8; text-align: justify; }

    /* Footer Buttons */
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 20px; border-top:1px solid #e5e7eb; background:#fafafa; z-index: 10; position: relative;}
    .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
    .btn:active { transform:scale(.97); }
    .btn-primary { background:var(--bsu-dark); color:#fff; }
    .btn-primary:hover:not(:disabled) { opacity:.88; }
    .btn-danger { background:var(--brand-red); color:#fff; }
    .btn-danger:hover:not(:disabled) { opacity:.88; }
    .btn-outline { background:transparent; color:var(--bsu-dark); border:1.5px solid var(--bsu-dark); }
    .btn-outline:hover { background:#f0f4ff; }
    .btn:disabled { opacity:0.5; cursor:not-allowed; }

    /* Clean Reviewer Button Styling */
    .reviewer-btn { width:100%; text-align:left; padding:12px 16px; border:1px solid #e5e7eb; border-radius:8px; transition:all .15s; background:#fff; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom: 8px;}
    .reviewer-btn:hover:not(.disabled) { border-color:var(--bsu-dark); background:#f8fafc; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
    .reviewer-btn.disabled { opacity:0.5; cursor:not-allowed; background:#f9fafb; border-color:#e5e7eb; }

    /* Sub-Modal for Reviewer Details */
    .sub-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1100; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(1px); }
    .sub-modal-box { background: #fff; border-radius: 12px; width: 450px; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: lbIn 0.2s ease; }
    .workflow-timeline { display:flex; align-items:center; gap:12px; margin-bottom:16px; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; background:#fafafa; }
    .workflow-timeline.header-timeline { margin-bottom:0; padding:8px 10px; background:#fff; }
    .timeline-step { display:flex; align-items:center; gap:8px; min-width:0; }
    .timeline-dot { width:20px; height:20px; border-radius:999px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; border:1px solid #cbd5e1; color:#64748b; background:#fff; }
    .timeline-step.active .timeline-dot { border-color:#1d4ed8; color:#1d4ed8; background:#dbeafe; }
    .timeline-step.done .timeline-dot { border-color:#166534; color:#166534; background:#dcfce7; }
    .timeline-step-label { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; white-space:nowrap; }
    .timeline-step.active .timeline-step-label,
    .timeline-step.done .timeline-step-label { color:#1f2937; }
    .timeline-link { height:2px; width:32px; background:#d1d5db; border-radius:999px; }
    .timeline-link.done { background:#86efac; }

    @media (max-width: 900px) {
        .list-grid-header, .awaiting-grid-header { display: none; }
        .app-row, .awaiting-row { grid-template-columns: 1fr; gap: 8px; padding-bottom: 16px; }
        .app-row.reassign-row { grid-template-columns: 1fr; }
        .protocol-info-panel { width:100%; min-width:unset; border-right:none; border-bottom:1px solid #e5e7eb; height:250px; }
        .modal-content { flex-direction:column; }
        .modal-header { flex-direction:column; align-items:flex-start; }
        .modal-header-right { width:100%; justify-content:space-between; }
    }
</style>

<div x-data="protocolEvaluation" id="protocol-evaluation-root" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">

    <div id="tour-protocol-evaluation-header" class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Protocol Evaluation</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Classify and manage protocol reviews</p>
        </div>
        <div class="w-full max-w-xl flex items-center gap-2">
            <div id="tour-search-box" class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select id="tour-sort-order" x-model="sortOrder" class="w-44 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-bsu-dark focus:outline-none focus:ring-1 focus:ring-bsu-dark/20">
                <option value="newest">Newest -> Oldest</option>
                <option value="oldest">Oldest -> Newest</option>
            </select>
        </div>
    </div>

    <div class="app-card relative">
        <div id="tour-main-tabs" class="card-header">
            <div id="tour-tab-evaluation" class="card-tab" :class="activeTab === 'evaluation' ? 'active' : ''" @click="setActiveTab('evaluation')">
                Evaluate Protocol
                <span x-show="evalCount > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="evalCount" x-cloak></span>
            </div>
            <div id="tour-tab-reassignment" class="card-tab" :class="activeTab === 'reassignment' ? 'active' : ''" @click="setActiveTab('reassignment')">
                For Reassignment
                <span x-show="reassignCount > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="reassignCount" x-cloak></span>
            </div>
            <div id="tour-tab-awaiting" class="card-tab" :class="activeTab === 'awaiting' ? 'active' : ''" @click="setActiveTab('awaiting')">
                Awaiting Response
                <span x-show="awaitingCount > 0" class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px]" x-text="awaitingCount" x-cloak></span>
            </div>
        </div>

        <div x-show="activeTab === 'evaluation' || activeTab === 'reassignment'">
            <div class="list-grid-header" :class="activeTab === 'reassignment' ? 'reassign-grid' : ''">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <template x-if="activeTab === 'evaluation'">
                    <div>Receiver</div>
                </template>
                <template x-if="activeTab === 'reassignment'">
                    <div>Classification</div>
                </template>
                <template x-if="activeTab === 'evaluation'">
                    <div>Date Processed</div>
                </template>
                <template x-if="activeTab === 'reassignment'">
                    <div>Assigned Reviewers</div>
                </template>
                <div class="header-center">Action</div>
            </div>

            <template x-for="(protocol, index) in filteredProposals" :key="protocol.id">
                <div class="app-row"
                     :id="index === 0 && activeTab === 'evaluation' ? 'tour-first-evaluation-row' : (index === 0 && activeTab === 'reassignment' ? 'tour-first-reassignment-row' : null)"
                     :class="activeTab === 'reassignment' ? 'reassign-row' : ''"
                     @click="openPreviewModal(protocol)">
                    <div><span class="app-id-badge" x-text="protocol.id"></span></div>
                    <div class="min-w-0 flex-1 pr-4">
                        <div class="app-row-title whitespace-normal break-words leading-tight mb-1"
                            style="display: block; overflow: visible;"
                            x-text="protocol.title">
                        </div>

                        <div class="app-row-sub text-gray-500 font-medium italic"
                            x-text="protocol.proponent">
                        </div>
                    </div>
                    <template x-if="activeTab === 'evaluation'">
                        <div class="flex flex-col justify-center">
                            <div style="font-size:9px; font-weight:800; text-transform:uppercase; color:#9ca3af; margin-bottom:2px; letter-spacing: 0.05em;">
                                Secretarial Staff
                            </div>
                            <div class="receiver-name font-bold text-gray-700 text-xs"
                                x-text="protocol.receiver"
                                :class="protocol.receiver === 'Unassigned' ? 'italic text-gray-400' : ''">
                            </div>
                        </div>
                    </template>
                    <template x-if="activeTab === 'reassignment'">
                        <div class="reassign-classification-cell h-full w-full flex flex-col justify-center items-start text-left gap-1.5">
                            <div>
                                <span class="classification-pill font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border" :class="getClassificationTagClass(protocol.classification)" x-text="protocol.classification"></span>
                            </div>
                            <template x-if="protocol.classification">
                                <div class="mt-1 w-full text-left">
                                    <div class="text-[10px] font-bold text-gray-600" x-text="'Classified: ' + getProtocolClassifiedDate(protocol)"></div>
                                    <div class="text-[10px] text-gray-500 font-bold mt-0.5" x-text="getRelativeTime(getProtocolClassifiedDate(protocol))"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="activeTab === 'evaluation'">
                        <div :class="protocol.assignedReviewers?.length ? '' : 'self-stretch flex items-center justify-center'">

                            <div class="protocol-date-ref" style="color:#4b5563;" x-text="formatDateOnly(protocol.date)"></div>

                            <div style="font-size:10px; color:#9ca3af; font-weight:700; margin-top:2px;"
                                x-text="getRelativeTime(protocol.date)">
                            </div>
                        </div>
                    </template>
                    <template x-if="activeTab === 'reassignment'">
                        <div class="w-full reviewers-cell">
                            <div class="reviewers-inner">
                                <div class="space-y-1" x-show="getProtocolReviewersForDisplay(protocol).length">
                                    <template x-for="(rev, revIdx) in getProtocolReviewersForDisplay(protocol)" :key="(rev.name || 'reviewer') + '_' + revIdx + '_' + getReviewerDisplayStatus(protocol, rev, 'reassignment')">
                                        <div class="flex justify-between items-start gap-3 border border-gray-100 rounded-md bg-gray-50 px-2 py-1.5">
                                            <div class="min-w-0 text-left pr-2 flex-1">
                                                <div class="text-[10px] font-bold text-gray-700" x-text="rev.name"></div>
                                                <div class="text-[10px] text-gray-500 font-semibold mt-0.5">
                                                    <span x-text="getReviewerStatusDateLabel(rev.status, protocol, rev, 'reassignment') + ': '"></span><span x-text="getReviewerStatusDate(protocol, rev, 'reassignment')"></span>
                                                </div>
                                                    <div class="text-[10px] font-semibold"
                                                        :class="['Expired', 'Declined'].includes(getReviewerDisplayStatus(protocol, rev, 'reassignment')) ? 'text-red-600' : 'text-gray-400'"
                                                        x-text="getReviewerStatusSubtext(protocol, rev, 'reassignment')"></div>                                            </div>
                                            <div class="flex flex-col items-end gap-1.5 shrink-0 ml-auto pl-5">
                                                <span class="text-[10px] font-black tracking-wide px-2 py-0.5 rounded border min-w-[92px] text-center"
                                                      :class="getReviewerStatusBadgeClass(rev.status, protocol, rev, 'reassignment')"
                                                      x-text="getReviewerDisplayStatus(protocol, rev, 'reassignment')"></span>
                                                <div x-show="isReviewerNoResponse(protocol, rev)"
                                                     class="text-[9px] font-bold text-red-600 text-right leading-tight">
                                                    No response in 24 hours
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div x-show="!getProtocolReviewersForDisplay(protocol).length" class="text-[10px] text-gray-500 italic text-left py-1">No assigned reviewers yet.</div>
                        </div>
                    </template>
                    <div class="action-cell flex justify-center items-center gap-2 text-center">
                        <span
                            x-show="protocol.status === 'pending' || protocol.status === 'documents_checking'"
                            @click.stop="openPreviewModal(protocol)"
                            class="workflow-action-link">
                            Classify
                        </span>

                        <span
                            x-show="activeTab === 'reassignment' || protocol.status === 'reassign' || hasDeclinedReviewer(protocol)"
                            @click.stop="openPreviewModal(protocol)"
                            class="workflow-action-link text-red-600">
                            Reassign
                        </span>
                    </div>
                </div>
            </template>

            <div x-show="filteredProposals.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No protocols found in this section.
            </div>
        </div>

        <div x-show="activeTab === 'awaiting'" x-cloak>
            <div class="awaiting-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Assigned Reviewers</div>
                <div class="header-center">Status</div>
            </div>

            <template x-for="(protocol, index) in filteredProposals" :key="protocol.id">
                <div class="awaiting-row hover:bg-white" :id="index === 0 ? 'tour-first-awaiting-row' : null">
                    <div class="self-center"><span class="app-id-badge" x-text="protocol.id"></span></div>
                    <div class="min-w-0 flex-1 pr-4">
                        <div class="app-row-title whitespace-normal break-words leading-tight mb-1"
                            style="display: block; overflow: visible;"
                            x-text="protocol.title">
                        </div>

                        <div class="app-row-sub text-gray-500 font-medium italic"
                            x-text="protocol.proponent">
                        </div>
                    </div>

                    <div class="h-full flex flex-col justify-center gap-1.5">
                        <div>
                            <span class="font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border" :class="getClassificationTagClass(protocol.classification)" x-text="protocol.classification"></span>
                        </div>
                        <template x-if="protocol.classification">
                            <div class="mt-1">
                                <div class="text-[10px] font-bold text-gray-600" x-text="'Classified: ' + getProtocolClassifiedDate(protocol)"></div>
                                <div class="text-[10px] text-gray-500 font-bold mt-0.5" x-text="getRelativeTime(getProtocolClassifiedDate(protocol))"></div>
                            </div>
                        </template>
                    </div>

                    <div :class="getProtocolReviewersForDisplay(protocol).length ? 'reviewers-cell' : (protocol.classification === 'Exempted' ? 'reviewers-cell reviewers-empty self-stretch h-full' : 'reviewers-cell')">
                        <template x-if="getProtocolReviewersForDisplay(protocol).length > 0">
                            <div class="mt-1 reviewers-inner">
                                <div>
                                    <div class="space-y-1">
                                        <template x-for="(rev, revIdx) in getProtocolReviewersForDisplay(protocol)" :key="(rev.name || 'reviewer') + '_' + revIdx + '_' + getReviewerDisplayStatus(protocol, rev, 'awaiting')">
                                            <div class="flex justify-between items-start gap-3 border border-gray-100 rounded-md bg-gray-50 px-2 py-1.5">
                                                <div class="min-w-0 text-left pr-2 flex-1">
                                                    <div class="text-[10px] font-bold text-gray-700" x-text="rev.name"></div>
                                                    <div class="text-[10px] text-gray-500 font-semibold mt-0.5">
                                                        <span x-text="getReviewerStatusDateLabel(rev.status, protocol, rev, 'awaiting') + ': '"></span><span x-text="getReviewerStatusDate(protocol, rev, 'awaiting')"></span>
                                                    </div>
                                                    <div class="text-[10px] font-semibold"
                                                        :class="['Expired', 'Declined'].includes(getReviewerDisplayStatus(protocol, rev, 'awaiting')) ? 'text-red-600' : 'text-gray-400'"
                                                        x-text="getReviewerStatusSubtext(protocol, rev, 'awaiting')"></div>                                                </div>
                                                <div class="flex flex-col items-center gap-1.5 shrink-0 ml-auto pl-5">
                                                    <span class="text-[10px] font-black tracking-wide px-2 py-0.5 rounded border min-w-[92px] text-center"
                                                        :class="getReviewerStatusBadgeClass(rev.status, protocol, rev, 'awaiting')"
                                                        x-text="getReviewerDisplayStatus(protocol, rev, 'awaiting')"></span>

                                                    <div x-show="getReviewerDisplayStatus(protocol, rev, 'awaiting') === 'Pending'"
                                                        class="text-[9px] font-bold text-orange-600 text-center leading-tight"
                                                        x-text="getReviewerPendingCountdown(protocol, rev, 'awaiting')"></div>

                                                    <div x-show="getReviewerDisplayStatus(protocol, rev, 'awaiting') === 'Accepted'"
                                                        class="text-[9px] font-bold text-center leading-tight mt-0.5"
                                                        :class="isReviewOverdue(protocol, rev) ? 'text-red-600' : 'text-blue-600'"
                                                        x-text="getReviewCountdown(protocol, rev)"></div>
                                                    <div x-show="getReviewerDisplayStatus(protocol, rev, 'awaiting') === 'Expired'"
                                                        class="text-[9px] font-bold text-red-600 text-center leading-tight">
                                                        Deadline passed
                                                    </div>

                                                    <div x-show="getReviewerDisplayStatus(protocol, rev, 'awaiting') === 'Declined'"
                                                        class="text-[9px] font-bold text-red-600 text-center leading-tight">
                                                        Rejected
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="!getProtocolReviewersForDisplay(protocol).length">
                            <div class="w-full text-[10px] text-gray-500 italic text-left">No reviewers required for exempted protocols.</div>
                        </template>
                    </div>

                    <div class="h-full text-center flex flex-col items-center justify-center">
                        <div class="flex flex-col items-center justify-center">
                            <span class="inline-flex items-center justify-center gap-1.5 text-center text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded border bg-orange-50 text-orange-700 border-orange-200">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <span class="text-[9px] font-bold text-gray-500 mt-1 text-center" x-text="getAwaitingStatusText(protocol)"></span>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="filteredProposals.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                Protocols waiting for reviewer response or chair approval will appear here.
            </div>
        </div>
    </div>

    <div id="tour-preview-modal" class="modal-overlay" :class="isPreviewModalOpen ? 'open' : ''" @click.self="closePreviewModal()">
        <div class="modal-box w-full max-w-[1200px] h-[90vh] flex flex-col">
            <div id="tour-modal-header" class="modal-header shrink-0">
                <div class="modal-header-left">
                    <h2 x-text="isReassigning ? 'Reassign Reviewers' : 'Protocol Classification & Assignment'"></h2>
                </div>
                <div class="modal-header-right">
                    <div class="workflow-timeline header-timeline" x-cloak>
                        <div class="timeline-step" :class="currentWorkflowStep === 1 ? 'active' : 'done'">
                            <span class="timeline-dot" x-text="currentWorkflowStep === 1 ? '1' : '\u2713'"></span>
                            <span class="timeline-step-label" x-text="timelineStepOneLabel"></span>
                        </div>
                        <div class="timeline-link" :class="currentWorkflowStep > 1 ? 'done' : ''"></div>
                        <div class="timeline-step" :class="currentWorkflowStep > 1 ? 'active' : ''">
                            <span class="timeline-dot">2</span>
                            <span class="timeline-step-label" x-text="timelineStepTwoLabel"></span>
                        </div>
                    </div>
                    <button class="close-btn" @click="closePreviewModal()">&times;</button>
                </div>
            </div>

            <div class="modal-content flex-1 flex overflow-hidden">

                <div id="tour-modal-left-panel" class="protocol-info-panel w-80 shrink-0 overflow-y-auto border-r border-gray-200 p-6 bg-white">
                    <div class="info-group mb-4">
                        <div class="info-label">Application ID</div>
                        <div class="modal-app-id font-bold text-lg text-bsu-dark" x-text="selectedProtocol?.id"></div>
                    </div>
                    <div class="info-group mb-4">
                        <div class="info-label">Study Title</div>
                        <div class="info-value text-[11px] leading-tight" x-text="selectedProtocol?.title"></div>
                    </div>
                    <div class="info-group mb-4">
                        <div class="info-label">Researcher Name</div>
                        <div class="info-value" x-text="selectedProtocol?.proponent"></div>
                    </div>

                    <template x-if="isReassigning && (selectedDeclinedReviewers.length || selectedExpiredReviewers.length || assignments.some(a => a.locked))">
                        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px; margin-top:16px;">
                            <template x-if="selectedDeclinedReviewers.length">
                                <div>
                                    <template x-if="selectedDeclinedReviewerDetails.length">
                                        <div>
                                            <div style="font-size:9px; font-weight:800; color:#dc2626; text-transform:uppercase; margin-bottom:4px;">
                                                Declined
                                            </div>

                                            <template x-for="reviewer in selectedDeclinedReviewerDetails" :key="'declined_' + reviewer.name">
                                                <div style="margin-bottom:8px;">
                                                    <div style="font-size:12px; font-weight:800; color:#b91c1c;"
                                                        x-text="capitalizeName(reviewer.name)"></div>

                                                    <template x-if="reviewer.declinedReason">
                                                        <div style="font-size:10px; color:#7f1d1d; margin-top:4px; font-style:italic;"
                                                            x-text="'Reason for Declining: ' + reviewer.declinedReason"></div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="selectedExpiredReviewers.length">
                                <div :style="'margin-top:' + (selectedDeclinedReviewers.length ? '10px' : '0') + ';'">
                                    <div style="font-size:9px; font-weight:800; color:#b45309; text-transform:uppercase; margin-bottom:4px;">Expired (No Response in 24 Hours)</div>
                                    <template x-for="name in selectedExpiredReviewers" :key="'expired_' + name">
                                        <div style="font-size:12px; font-weight:800; color:#92400e;" x-text="capitalizeName(name)"></div>
                                    </template>
                                    <div style="font-size:10px; color:#92400e; margin-top:4px; font-style:italic;">These reviewers can still be reassigned.</div>
                                </div>
                            </template>

                            <template x-if="assignments.some(a => a.locked)">
                                <div :style="'margin-top:' + ((selectedDeclinedReviewers.length || selectedExpiredReviewers.length) ? '10px' : '0') + ';'">
                                    <div style="font-size:9px; font-weight:800; color:#047857; text-transform:uppercase; margin-bottom:4px;">Existing Reviewers (Locked)</div>
                                    <template x-for="rev in assignments.filter(a => a.locked)" :key="rev.id">
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:4px;">
                                            <span style="font-size:11px; font-weight:700; color:#047857;" x-text="rev.name"></span>
                                            <span class="text-[9px] font-black tracking-wide px-2 py-0.5 rounded border"
                                                  :class="getReviewerStatusBadgeClass(rev.lockedStatus || 'Accepted')"
                                                  x-text="rev.lockedStatus || 'Accepted'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div id="tour-document-list" style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">

                        <div class="info-label" style="margin-bottom:8px;">Basic Requirements</div>

                        <div x-show="isLoadingDocs" class="text-xs text-gray-500 italic py-2">Loading documents...</div>

                        <div x-show="!isLoadingDocs">

                            <div class="doc-card" :class="activeDocKey === 'appform' ? 'active' : ''" @click="viewDocument('appform')">
                                <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Application Form</div>
                                    <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;" x-text="activeDocKey === 'appform' ? 'Active' : 'System Generated'"></div>
                                </div>
                                <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </div>

                            <template x-for="doc in loadedDocs.activeBasic" :key="doc.id">
                                <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label, false)">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
                                            <span x-text="doc.label"></span>
                                            <template x-if="doc.isRevised">
                                                <span style="margin-left:6px; background:#fef9c3; color:#854d0e; padding:2px 6px; border-radius:4px; font-size:8px; font-weight:bold; flex-shrink:0; border:1px solid #fde047;">Revised</span>
                                            </template>
                                        </div>
                                        <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;" x-text="activeDocKey === doc.id ? 'Active' : doc.desc"></div>
                                    </div>
                                    <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </template>
                        </div>

                        <div x-show="!isLoadingDocs && loadedDocs.activeSupp.length > 0">
                            <div class="info-label" style="margin-top:20px; margin-bottom:8px;">Supplementary Docs</div>
                            <template x-for="doc in loadedDocs.activeSupp" :key="doc.id">
                                <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label, true)">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
                                            <span x-text="doc.label"></span>
                                            <template x-if="doc.isRevised">
                                                <span style="margin-left:6px; background:#fef9c3; color:#854d0e; padding:2px 6px; border-radius:4px; font-size:8px; font-weight:bold; flex-shrink:0; border:1px solid #fde047;">Revised</span>
                                            </template>
                                        </div>
                                        <div style="font-size:9px; font-weight:600; color:#6b7280; margin-top:1px;" x-text="activeDocKey === doc.id ? 'Active' : doc.desc"></div>
                                    </div>
                                    <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </template>
                        </div>

                        <div x-show="!isLoadingDocs && loadedDocs.legacy.length > 0" style="margin-top:24px; border-top:2px dashed #cbd5e1; padding-top:16px;">
                            <div class="info-label" style="margin-bottom:8px; color:#64748b;">Version History (Archived)</div>
                            <template x-for="doc in loadedDocs.legacy" :key="doc.id">
                                <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label, false)" style="opacity:0.75; background:#f8fafc; border:1px dashed #cbd5e1;">
                                    <span style="color:#94a3b8; font-size:16px; flex-shrink:0; margin-right:4px;">🗄️</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
                                            <span x-text="doc.label"></span>
                                            <span style="margin-left:6px; background:#e2e8f0; color:#475569; padding:2px 6px; border-radius:4px; font-size:8px; font-weight:bold; flex-shrink:0;">Legacy</span>
                                        </div>
                                        <div style="font-size:9px; font-weight:600; color:#94a3b8; margin-top:1px;" x-text="doc.desc"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="tour-modal-right-panel" class="form-preview-panel flex-1 bg-gray-50/50 flex flex-col relative overflow-hidden">

                    <div x-show="activeDocKey" x-cloak class="h-full flex flex-col w-full">
                        <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center shrink-0">
                            <span class="text-[11px] font-bold text-gray-700 uppercase" x-text="activeDocTitle"></span>
                            <div class="flex items-center gap-4">
                                <a x-show="activeDocUrl" :href="activeDocUrl" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">Fullscreen ↗</a>
                                <button @click="activeDocKey = null" class="text-[10px] font-bold text-gray-500 hover:text-gray-800 bg-gray-100 px-3 py-1 rounded">Close Document</button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-auto w-full relative">
                            <div x-show="activeDocKey === 'appform'" class="p-4 sm:p-8 bg-gray-50 min-h-full">
                                <div class="max-w-2xl mx-auto bg-white border border-gray-300 shadow-sm p-6 sm:p-10 rounded">
                                    <div class="border-b border-gray-200 pb-5 mb-6 text-center">
                                        <img src="{{ asset('logo/BERC.png') }}" style="height:45px; margin:0 auto 12px; display:block;" alt="BERC Logo">
                                        <h3 class="text-lg font-black text-bsu-dark tracking-tight">Ethics Review Committee Application Form</h3>
                                        <p class="text-[10px] font-bold text-gray-500 mt-1 uppercase tracking-widest">BSU-ERC-FORM-01 (2026)</p>
                                    </div>

                                    <div class="mb-8">
                                        <div class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3 bg-gray-100 px-3 py-1.5 rounded">I. General Information</div>
                                        <div class="grid gap-3 text-sm px-2">
                                            <div class="flex"><span class="w-40 font-bold text-gray-600">Application ID:</span> <span class="font-mono font-bold text-blue-700" x-text="appDetails.id"></span></div>
                                            <div class="flex"><span class="w-40 font-bold text-gray-600">Research Title:</span> <span class="font-medium" x-text="appDetails.title"></span></div>
                                            <div class="flex"><span class="w-40 font-bold text-gray-600">Primary Researcher:</span> <span class="font-medium" x-text="appDetails.researcher"></span></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3 bg-gray-100 px-3 py-1.5 rounded">II. Checklist of Documents</div>
                                        <div class="flex gap-6 px-2">

                                            <div class="flex-1 border-r border-gray-100 pr-4 space-y-2.5">
                                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Basic Requirements</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('letter') ? '✔' : '○'"></span> Letter request for review</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('endorsement') ? '✔' : '○'"></span> Endorsement Letter</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('proposal') ? '✔' : '○'"></span> Study Protocol</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('technicalreviewapproval') ? '✔' : '○'"></span> Technical Review Approval</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('cv') ? '✔' : '○'"></span> Curriculum Vitae</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="(hasDoc('consent_en') || hasDoc('consent_ph')) ? '✔' : '○'"></span> Informed Consent Form</div>
                                                <div class="flex gap-3 pl-6 mt-1 text-[9px] font-medium text-gray-500">
                                                    <span class="flex items-center gap-1"><span class="text-blue-500" x-text="hasDoc('consent_en') ? '☑' : '☐'"></span> English</span>
                                                    <span class="flex items-center gap-1"><span class="text-blue-500" x-text="hasDoc('consent_ph') ? '☑' : '☐'"></span> Filipino</span>
                                                </div>
                                            </div>

                                            <div class="flex-1 space-y-2.5">
                                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Supplementary Documents</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('questionnaire') ? '✔' : '○'"></span> Questionnaire</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('datacollection') ? '✔' : '○'"></span> Data Collection Forms</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('productbrochure') ? '✔' : '○'"></span> Product Brochure</div>
                                                <div class="text-[11px] font-medium flex items-center gap-2"><span class="w-4 text-blue-600 font-black text-sm" x-text="hasDoc('fda') ? '✔' : '○'"></span> FDA Authorization</div>

                                                <div class="mt-4 pt-2 border-t border-dashed border-gray-200 space-y-3">
                                                    <div>
                                                        <div class="text-[10px] font-bold flex items-center gap-2">
                                                            <span class="w-3 text-blue-600" x-text="getSuppData('special').length > 0 ? '✔' : '○'"></span> Special Populations:
                                                        </div>
                                                        <div class="text-[9px] text-blue-600 font-medium italic mt-1 pl-5 border-b border-gray-200 min-h-[14px]">
                                                            <template x-if="getSuppData('special').length > 0">
                                                                <span x-text="getSuppData('special').map(d => d.description).join(', ')"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[10px] font-bold flex items-center gap-2">
                                                            <span class="w-3 text-blue-600" x-text="getSuppData('other').length > 0 ? '✔' : '○'"></span> Others:
                                                        </div>
                                                        <div class="text-[9px] text-blue-600 font-medium italic mt-1 pl-5 border-b border-gray-200 min-h-[14px]">
                                                            <template x-if="getSuppData('other').length > 0">
                                                                <span x-text="getSuppData('other').map(d => d.description).join(', ')"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-12 border-t border-dashed border-gray-200 pt-3 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                        * Automated preview of the digital application form *
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeDocKey !== 'appform' && isImageView()" class="w-full h-full bg-gray-200 flex items-center justify-center p-6 absolute inset-0">
                                <img :src="activeDocUrl" class="max-w-full max-h-full object-contain shadow-lg rounded">
                            </div>

                            <div x-show="activeDocKey !== 'appform' && !isImageView() && activeDocUrl" class="w-full h-full absolute inset-0">
                                <iframe :src="activeDocUrl" class="w-full h-full border-none"></iframe>
                            </div>
                        </div>
                    </div>

                    <div x-show="!activeDocKey" class="animate-in fade-in h-full flex flex-col max-w-2xl mx-auto w-full p-8 overflow-y-auto">
                        <div>
                            <div x-show="!classification" class="flex flex-col gap-3">
                                <div style="margin-bottom:12px;">
                                    <h3 style="font-size:16px; font-weight:900; color:var(--bsu-dark); text-transform:uppercase;">Classification</h3>
                                    <p style="font-size:11px; color:#6b7280;">Select the appropriate review pathway.</p>
                                </div>

                                <div id="tour-classification-exempted" class="doc-card" @click="selectClassification('Exempted')" style="padding:16px; justify-content:flex-start;">
                                    <div style="margin-left:10px;">
                                        <div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Exempted Review</div>
                                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">Generates a certificate of exemption directly.</div>
                                    </div>
                                </div>

                                <div id="tour-classification-expedited" class="doc-card" @click="selectClassification('Expedited')" style="padding:16px; justify-content:flex-start;">
                                    <div style="margin-left:10px;">
                                        <div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Expedited Review</div>
                                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">Proceeds to reviewer assignment.</div>
                                    </div>
                                </div>

                                <div id="tour-classification-fullboard" class="doc-card" @click="selectClassification('Full Board')" style="padding:16px; justify-content:flex-start;">
                                    <div style="margin-left:10px;">
                                        <div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Full Board Review</div>
                                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">Proceeds to extensive reviewer assignment.</div>
                                    </div>
                                </div>
                            </div>

                            <div id="tour-selected-classification" x-show="classification" x-cloak style="margin-bottom:24px;">
                                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                                    <div style="font-size:16px; font-weight:900; color:var(--bsu-dark);" x-text="classification"></div>
                                    <button @click="requestClassificationChange()" style="background:none; border:none; color:var(--brand-red); font-size:11px; font-weight:800; cursor:pointer;">
                                        Change Option
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="tour-exemption-preview" x-show="classification === 'Exempted'" x-cloak>
                            <div class="cert-exemption font-serif" contenteditable="true" style="padding:40px; border:1px solid #e5e7eb; background:#fff; border-radius:8px; line-height: 1.6; color: #000;">

                                <div class="flex justify-between items-center border border-gray-400 px-4 py-2 mb-6 text-xs font-sans">
                                    <div><strong>Reference No.:</strong> BatStateU-FO-BERC-023</div>
                                    <div><strong>Effectivity Date:</strong> </div>
                                    <div><strong>Revision No.:</strong> 00</div>
                                </div>

                                <h2 class="text-center font-bold text-lg mb-8 uppercase tracking-wide">Certificate of Exemption from Ethics Review</h2>

                                <table class="w-full text-sm mb-6 border-collapse">
                                    <tbody>
                                        <tr>
                                            <td class="py-1 w-1/3"><strong>Date:</strong></td>
                                            <td class="py-1 border-b border-gray-300" x-text="new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })"></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 10px 0 6px 0;"><strong>Name of Principal Investigator:</strong></td>

                                            <td x-ref="piNameCell" style="padding: 10px 0 6px 0; border-bottom: 1px solid #000; font-weight: bold;"
                                                x-text="selectedProtocol?.proponent || selectedProtocol?.primary_researcher || 'N/A'">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 pt-3"><strong>Title of Study/Protocol:</strong></td>
                                            <td class="py-1 pt-3 border-b border-gray-300 font-bold" x-text="selectedProtocol?.title || 'N/A'"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 pt-3"><strong>BERC Code:</strong></td>
                                            <td class="py-1 pt-3 border-b border-gray-300 font-bold" x-text="selectedProtocol?.protocol_code || selectedProtocol?.id || 'N/A'"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="text-sm space-y-4 text-justify">
                                    <p>After a preliminary review, the BatStateU TNEU Ethics Review Committee deemed it appropriate that the above protocol be EXEMPTED FROM REVIEW.</p>

                                    <p>This means that the study may be implemented without undergoing an expedited or full review. Neither will the proponents be required to submit further documents to the committee as long as there is no amendment nor alteration in the protocol that will change the nature of the study nor the level of risk involved. Please note also, that the following responsibilities of the investigator/s are maintained while the study is in progress:</p>

                                    <ol class="list-decimal pl-8 space-y-3 mt-4">
                                        <li>Continuing compliance with the exemption criteria of the National Ethical Guidelines for Research Involving Human Participants 2022 in the duration of the study;</li>
                                        <li>Nonetheless, such human participants in case reports/case series/non-health research are entitled to compliance of researchers with universal ethical principles of respect for persons, beneficence, and justice, as well as applicable local regulations, including the Data Privacy Act of 2012 (RA 10173). Thus, it is the responsibility of the author/investigator(s) to ensure satisfactory compliance with the aforementioned principles and all applicable regulations, and to obtain informed consent from the human subjects involved, if personally identifiable information will be used in any way.</li>
                                        <li>No substantial changes in research design, methodology, and subject population from the protocol submitted for exemption. Modifications that significantly affect previous risk-benefit assessments or qualification for exemption may be submitted as a new protocol for initial review.</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button type="button"
                                        class="bg-bsu-dark hover:bg-blue-900 text-white font-bold px-6 py-2.5 rounded-lg shadow-md transition-colors"
                                        @click="confirmExemption()">
                                    Confirm & Send to Chair
                                </button>
                            </div>
                        </div>

                        <div x-show="classification === 'Expedited' || classification === 'Full Board'" x-cloak>
                            <div id="tour-consultant-form" x-show="showConsultantForm" class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                <h4 class="text-xs font-bold text-purple-900 mb-2">Define Consultant Role & Responsibilities</h4>
                                <input id="tour-consultant-input" type="text" x-model="consultantRole" placeholder="e.g. Needs statistical expertise for data sets..." class="w-full text-sm p-2 border border-purple-300 rounded mb-3">
                                <div class="flex gap-2">
                                    <button id="tour-consultant-submit" class="btn bg-purple-600 text-white hover:bg-purple-700 px-3 py-1 rounded text-[10px]" @click="submitConsultant()">Submit Request</button>
                                    <button class="btn border border-gray-300 bg-white hover:bg-gray-50 px-3 py-1 rounded text-[10px]" @click="showConsultantForm = false">Cancel</button>
                                </div>
                            </div>

                            <div id="tour-assignment-checklist" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                                <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
                                    <h4 style="font-size:12px; font-weight:900; color:var(--bsu-dark);">Reviewer Assignment Checklist</h4>
                                    <button id="tour-add-consultant-btn" x-show="!hasConsultant" @click="showConsultantForm = true" class="text-[10px] font-bold text-purple-600 hover:text-purple-800 underline">Add External Consultant</button>
                                </div>
                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                    <div>
                                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700;" :class="(primaryCount + (hasConsultant ? 1 : 0)) >= 2 && (primaryCount + (hasConsultant ? 1 : 0)) <= 3 ? 'text-green-700' : 'text-gray-500'">
                                            <span x-text="(primaryCount + (hasConsultant ? 1 : 0)) >= 2 && (primaryCount + (hasConsultant ? 1 : 0)) <= 3 ? '✓' : '○'"></span>
                                            <span x-text="'Assign 2-3 primary reviewers (' + (primaryCount + (hasConsultant ? 1 : 0)) + '/3)'"></span>
                                        </div>

                                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700;" :class="(hasPanelExpert || hasConsultant) ? 'text-green-700' : 'text-gray-500'">
                                            <span x-text="(hasPanelExpert || hasConsultant) ? '✓' : '○'"></span> At least 1 Scientist/Expert
                                        </div>

                                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700;" :class="hasLayperson ? 'text-green-700' : 'text-gray-500'">
                                            <span x-text="hasLayperson ? '✓' : '○'"></span> At least 1 Layperson
                                        </div>

                                        <div x-show="hasConsultant" style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#6b21a8;">
                                            ✓ External Consultant Added
                                        </div>
                                    </div>

                                    <div id="tour-assignment-summary" class="flex flex-col gap-1 border-l pl-3 border-gray-100">
                                        <template x-for="a in assignments" :key="a.id">
                                            <div class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                                <span class="text-[10px] font-bold text-gray-700" x-text="a.name"></span>
                                                <button x-show="!a.locked" @click="removeAssignment(a.id)" class="text-red-500 hover:text-red-700 leading-none">&times;</button>
                                                <span x-show="a.locked"
                                                    class="text-[9px] text-green-600 font-bold uppercase tracking-wider"
                                                    x-text="'Locked (' + (a.lockedStatus || 'Accepted') + ')'"></span>
                                            </div>
                                        </template>
                                        <div x-show="assignments.length === 0" class="text-[10px] text-gray-400 italic mt-1">No reviewers assigned yet.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pb-16">
                                <h4 style="font-size:11px; font-weight:800; color:#6b7280; text-transform:uppercase; margin-bottom:8px;">Available Reviewers</h4>

                                <div id="tour-reviewer-tabs" class="flex border-b border-gray-200 mb-4 gap-4">
                                    <button id="tour-reviewer-tab-panel" @click="reviewerTab = 'panel'" :class="reviewerTab === 'panel' ? 'border-bsu-dark text-bsu-dark' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 text-xs font-bold border-b-2 transition-colors">Scientist/Experts</button>
                                    <button id="tour-reviewer-tab-layperson" @click="reviewerTab = 'layperson'" :class="reviewerTab === 'layperson' ? 'border-bsu-dark text-bsu-dark' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 text-xs font-bold border-b-2 transition-colors">Laypersons</button>
                                    <button id="tour-reviewer-tab-consultant" @click="reviewerTab = 'consultant'" :class="reviewerTab === 'consultant' ? 'border-bsu-dark text-bsu-dark' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 text-xs font-bold border-b-2 transition-colors">External Consultants</button>
                                </div>

                                <div id="tour-reviewer-list" x-show="reviewerTab === 'panel'" x-cloak class="flex flex-col gap-2">
                                    <template x-for="(r, idx) in getReviewersByType('Panel Expert', true)" :key="r.id">
                                        <button :id="idx === 0 ? 'tour-first-panel-reviewer' : null"
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded hover:bg-gray-50 bg-white transition-all w-full"
                                            :class="r.isDisabled ? 'opacity-60 grayscale bg-gray-50' : ''"
                                            @click="openReviewerDetails(r)"> <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-bsu-dark" x-text="r.name"></span>
                                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded" x-text="r.panel"></span>

                                                <template x-if="r.isDisabled && !isDeclined(r) && !isNoResponse(r)">
                                                    <span class="text-[9px] text-red-600 font-bold uppercase bg-red-50 border border-red-200 px-2 py-0.5 rounded ml-2">Quota Full</span>
                                                </template>

                                                <span x-show="isDeclined(r)" class="text-[9px] text-red-600 font-bold uppercase bg-red-100 px-2 py-0.5 rounded ml-2">Declined</span>
                                                <span x-show="isNoResponse(r)" class="text-[9px] text-amber-700 font-bold uppercase bg-amber-100 border border-amber-200 px-2 py-0.5 rounded ml-2">Expired</span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </template>
                                    <div x-show="getReviewersByType('Panel Expert', true).length === 0" class="text-xs text-gray-400 italic">No Scientist/Experts available.</div>
                                </div>

                                <div x-show="reviewerTab === 'layperson'" x-cloak class="flex flex-col gap-2">
                                    <template x-for="(r, idx) in getReviewersByType('Layperson', true)" :key="r.id">
                                        <button :id="idx === 0 ? 'tour-first-layperson-reviewer' : null"
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded hover:bg-gray-50 bg-white w-full"
                                            :class="r.isDisabled ? 'opacity-60 grayscale bg-gray-50' : ''"
                                            @click="openReviewerDetails(r)">

                                            <div class="flex flex-col items-start relative w-full">
                                                <div class="flex items-center justify-between w-full">
                                                    <span class="text-sm font-bold text-bsu-dark" x-text="r.name"></span>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] text-gray-500">Layperson</span>

                                                    <template x-if="r.isDisabled && !isDeclined(r) && !isNoResponse(r)">
                                                        <span class="text-[9px] text-red-600 font-bold uppercase bg-red-50 border border-red-200 px-2 py-0.5 rounded">Quota Full</span>
                                                    </template>

                                                    <span x-show="isDeclined(r)" class="text-[9px] text-red-600 font-bold uppercase bg-red-100 px-2 py-0.5 rounded">Declined</span>
                                                    <span x-show="isNoResponse(r)" class="text-[9px] text-amber-700 font-bold uppercase bg-amber-100 border border-amber-200 px-2 py-0.5 rounded">Expired</span>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="getReviewersByType('Layperson', true).length === 0" class="text-xs text-gray-400 italic">No Laypersons available.</div>
                                </div>

                                <div x-show="reviewerTab === 'consultant'" x-cloak class="flex flex-col gap-2">
                                    <template x-for="(c, idx) in availableConsultants" :key="c.id">
                                        <button :id="idx === 0 ? 'tour-first-consultant-reviewer' : null"
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded hover:bg-gray-50 bg-white w-full"
                                            :class="c.isDisabled ? 'opacity-60 grayscale bg-gray-50' : ''"
                                            @click="openReviewerDetails(c)">

                                            <div class="flex flex-col items-start w-full">
                                                <div class="flex items-center justify-between w-full">
                                                    <span class="text-sm font-bold text-purple-900" x-text="c.name"></span>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] text-purple-600 font-semibold" x-text="c.type"></span>

                                                    <template x-if="c.isDisabled && !isDeclined(c) && !isNoResponse(c)">
                                                        <span class="text-[9px] text-red-600 font-bold uppercase bg-red-50 border border-red-200 px-2 py-0.5 rounded">Quota Full</span>
                                                    </template>

                                                    <span x-show="isDeclined(c)" class="text-[9px] text-red-600 font-bold uppercase bg-red-100 px-2 py-0.5 rounded">Declined</span>
                                                    <span x-show="isNoResponse(c)" class="text-[9px] text-amber-700 font-bold uppercase bg-amber-100 border border-amber-200 px-2 py-0.5 rounded">Expired</span>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="availableConsultants.length === 0" class="text-xs text-gray-400 italic">No external consultants available. Request one above if needed.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-0 w-full bg-white border-t border-gray-200 p-4 flex justify-end" x-show="!activeDocKey && (classification === 'Expedited' || classification === 'Full Board')">
                        <button id="tour-confirm-evaluation-btn" class="bg-red-600 text-white font-bold px-5 py-2 rounded uppercase text-xs disabled:opacity-50 disabled:cursor-not-allowed"
                                @click="handleConfirmEvaluation()"
                                :disabled="!canConfirmEvaluation">
                            Confirm Assignment
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="tour-reviewer-modal" x-show="showReviewerModal" class="sub-modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-[200] flex items-center justify-center" style="display:none;" x-transition>
        <div class="sub-modal-box bg-white rounded-xl shadow-2xl p-6 w-full max-w-md m-4" @click.stop>
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-black text-bsu-dark" x-text="selectedReviewer?.name"></h3>
                <button @click="showReviewerModal = false" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
            </div>

            <template x-if="selectedReviewer?.type === 'Panel Expert'">
                <div class="mb-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">1. Expertise</div>
                    <div class="text-sm font-semibold text-blue-800 bg-blue-50 p-2 rounded border border-blue-100" x-text="selectedReviewer?.panel"></div>
                </div>
            </template>

            <template x-if="selectedReviewer?.type === 'Layperson'">
                <div class="mb-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">1. Expertise</div>
                    <div class="text-sm font-semibold text-green-800 bg-green-50 p-2 rounded border border-green-100">Layperson</div>
                </div>
            </template>

            <template x-if="selectedReviewer?.type === 'External Consultant'">
                <div class="mb-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">1. Expertise</div>
                    <div class="text-sm font-semibold text-purple-800 bg-purple-50 p-2 rounded border border-purple-100">External Consultant</div>
                </div>
            </template>

            <div class="mt-4">
                <h5 class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Current Active Evaluations
                    <span class="text-blue-600" x-text="'(' + (selectedReviewer?.evaluations?.length || 0) + '/3)'"></span>
                </h5>

                <template x-if="selectedReviewer?.evaluations?.length > 0">
                    <ul class="space-y-2">
                        <template x-for="eval in selectedReviewer.evaluations" :key="eval.protocol_code">
                            <li class="bg-gray-50 p-2.5 rounded border border-gray-200">
                                <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 mb-1 inline-block"
                                    x-text="eval.protocol_code">
                                </span>
                                <span class="text-xs text-gray-700 block leading-snug font-medium"
                                    x-text="eval.title">
                                </span>
                            </li>
                        </template>
                    </ul>
                </template>

                <template x-if="!selectedReviewer?.evaluations || selectedReviewer.evaluations.length === 0">
                    <div class="text-xs text-gray-500 italic p-3 bg-gray-50 rounded border border-gray-100 text-center">
                        No active evaluations for this cycle yet.
                    </div>
                </template>
            </div>

            <div class="mb-6">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">3. Previous Performance</div>
                <div class="text-sm font-semibold text-gray-800">
                    Average Review Time: <span class="text-[#c21c2c]" x-text="formatAvgTime(selectedReviewer?.avg_review_time_days)"></span>
                </div>
            </div>

            <button id="tour-assign-reviewer-btn" class="btn btn-primary bg-bsu-dark text-white font-bold px-4 py-2 rounded w-full uppercase text-xs" @click="assignSelectedReviewer()">Assign Reviewer</button>
        </div>
    </div>

    <div x-show="notificationOpen" class="sub-modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center" style="display:none;" x-transition>
        <div class="sub-modal-box bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm m-4" @click.stop>
            <h3 class="text-base font-black text-bsu-dark uppercase tracking-wide mb-2" x-text="notificationTitle"></h3>
            <p class="text-sm text-gray-600 leading-relaxed mb-5" x-text="notificationMessage"></p>
            <div class="flex justify-end">
                <button class="btn btn-primary bg-bsu-dark text-white px-4 py-2 rounded text-xs font-bold" @click="closeNotification()">OK</button>
            </div>
        </div>
    </div>

    <div x-show="confirmOpen" class="sub-modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-[200] flex items-center justify-center" style="display:none;" x-transition>
        <div class="sub-modal-box bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm m-4" @click.stop>
            <h3 class="text-base font-black text-bsu-dark uppercase tracking-wide mb-2" x-text="confirmTitle"></h3>
            <p class="text-sm text-gray-600 leading-relaxed mb-5" x-text="confirmMessage"></p>
            <div class="flex justify-end gap-2">
                <button class="btn btn-outline border border-gray-300 text-gray-700 px-4 py-2 rounded text-xs font-bold" @click="cancelConfirm()">Cancel</button>
                <button class="btn btn-primary bg-brand-red text-white px-4 py-2 rounded text-xs font-bold" @click="runConfirm()">Continue</button>
            </div>
        </div>
    </div>

    <!-- Mock tutorial modal for smooth guided walkthrough -->
    <div id="tutorial-mock-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1400; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);">
        <div id="tutorial-mock-modal" style="background:#fff; border-radius:14px; width:100%; max-width:1180px; height:88vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <div id="tutorial-mock-header" class="modal-header shrink-0">
                <div class="modal-header-left">
                    <h2>Protocol Classification &amp; Assignment</h2>
                </div>
                <div class="modal-header-right">
                    <div class="workflow-timeline header-timeline">
                        <div class="timeline-step active">
                            <span class="timeline-dot">1</span>
                            <span class="timeline-step-label">Classification</span>
                        </div>
                        <div class="timeline-link"></div>
                        <div class="timeline-step">
                            <span class="timeline-dot">2</span>
                            <span class="timeline-step-label">Assignment</span>
                        </div>
                    </div>
                    <button type="button" class="close-btn" onclick="window.closeProtocolTutorialMock?.()">&times;</button>
                </div>
            </div>
            <div class="modal-content flex-1 flex overflow-hidden">
                <div id="tutorial-mock-left" class="protocol-info-panel w-80 shrink-0 overflow-y-auto border-r border-gray-200 p-6 bg-white">
                    <div class="info-group mb-4">
                        <div class="info-label">Application ID</div>
                        <div class="modal-app-id font-bold text-lg text-bsu-dark">ERC-2026-001</div>
                    </div>
                    <div class="info-group mb-4">
                        <div class="info-label">Study Title</div>
                        <div class="info-value text-[11px] leading-tight">Assessing the Ethical Readiness of a Community Health Intervention</div>
                    </div>
                    <div class="info-group mb-4">
                        <div class="info-label">Researcher Name</div>
                        <div class="info-value">Juan Dela Cruz</div>
                    </div>
                    <div id="tutorial-mock-docs" style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
                        <div class="info-label" style="margin-bottom:8px;">Basic Requirements</div>
                        <div class="doc-card active"><span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span><div style="flex:1;"><div style="font-size:11px; font-weight:700; color:#111827;">Application Form</div><div style="font-size:9px; color:#6b7280;">System Generated</div></div></div>
                        <div class="doc-card"><span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span><div style="flex:1;"><div style="font-size:11px; font-weight:700; color:#111827;">Study Protocol</div><div style="font-size:9px; color:#6b7280;">Primary attachment</div></div></div>
                        <div class="doc-card"><span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span><div style="flex:1;"><div style="font-size:11px; font-weight:700; color:#111827;">Informed Consent Form</div><div style="font-size:9px; color:#6b7280;">English / Filipino</div></div></div>
                        <div class="doc-card"><span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span><div style="flex:1;"><div style="font-size:11px; font-weight:700; color:#111827;">Technical Review Approval</div><div style="font-size:9px; color:#6b7280;">Validated document</div></div></div>
                    </div>
                </div>
                <div id="tutorial-mock-right" class="form-preview-panel flex-1 bg-gray-50/50 flex flex-col relative overflow-hidden">
                    <div id="tutorial-mock-classification-view" class="h-full p-8 overflow-y-auto">
                        <div style="margin-bottom:12px;">
                            <h3 style="font-size:16px; font-weight:900; color:var(--bsu-dark); text-transform:uppercase;">Classification</h3>
                            <p style="font-size:11px; color:#6b7280;">Select the appropriate review pathway.</p>
                        </div>
                        <div id="tutorial-mock-classification-exempted" class="doc-card" style="padding:16px; justify-content:flex-start; margin-bottom:12px;"><div style="margin-left:10px;"><div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Exempted Review</div><div style="font-size:11px; color:#6b7280; margin-top:2px;">Generates a certificate of exemption directly.</div></div></div>
                        <div id="tutorial-mock-classification-expedited" class="doc-card active" style="padding:16px; justify-content:flex-start; margin-bottom:12px;"><div style="margin-left:10px;"><div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Expedited Review</div><div style="font-size:11px; color:#6b7280; margin-top:2px;">Proceeds to reviewer assignment.</div></div></div>
                        <div id="tutorial-mock-classification-fullboard" class="doc-card" style="padding:16px; justify-content:flex-start;"><div style="margin-left:10px;"><div style="font-size:15px; font-weight:800; color:var(--bsu-dark);">Full Board Review</div><div style="font-size:11px; color:#6b7280; margin-top:2px;">Proceeds to extensive reviewer assignment.</div></div></div>
                    </div>
                    <div id="tutorial-mock-assignment-view" class="h-full p-8 overflow-y-auto" style="display:none;">
                        <div id="tutorial-mock-selected-classification" style="margin-bottom:24px;">
                            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-size:16px; font-weight:900; color:var(--bsu-dark);">Expedited</div>
                                <button type="button" style="background:none; border:none; color:var(--brand-red); font-size:11px; font-weight:800;">Change Option</button>
                            </div>
                        </div>
                        <div id="tutorial-mock-consultant-form" class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg" style="display:none;">
                            <h4 class="text-xs font-bold text-purple-900 mb-2">Define Consultant Role &amp; Responsibilities</h4>
                            <input id="tutorial-mock-consultant-input" type="text" value="Needs statistical expertise for randomized sampling review." class="w-full text-sm p-2 border border-purple-300 rounded mb-3">
                            <div class="flex gap-2">
                                <button id="tutorial-mock-consultant-submit" class="btn bg-purple-600 text-white hover:bg-purple-700 px-3 py-1 rounded text-[10px]">Submit Request</button>
                                <button class="btn border border-gray-300 bg-white hover:bg-gray-50 px-3 py-1 rounded text-[10px]">Cancel</button>
                            </div>
                        </div>
                        <div id="tutorial-mock-checklist" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                            <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
                                <h4 style="font-size:12px; font-weight:900; color:var(--bsu-dark);">Reviewer Assignment Checklist</h4>
                                <button id="tutorial-mock-add-consultant-btn" class="text-[10px] font-bold text-purple-600 hover:text-purple-800 underline">Add External Consultant</button>
                            </div>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                <div>
                                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#166534;"><span>✓</span><span>Assign 2-3 primary reviewers (2/3)</span></div>
                                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#166534;"><span>✓</span><span>At least 1 Scientist/Expert</span></div>
                                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#166534;"><span>✓</span><span>At least 1 Layperson</span></div>
                                </div>
                                <div id="tutorial-mock-assignment-summary" class="flex flex-col gap-1 border-l pl-3 border-gray-100">
                                    <div class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded border border-gray-200"><span class="text-[10px] font-bold text-gray-700">Dr. Maria Santos</span><span class="text-[9px] text-green-600 font-bold uppercase tracking-wider">Assigned</span></div>
                                    <div class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded border border-gray-200"><span class="text-[10px] font-bold text-gray-700">Mr. Allan Reyes</span><span class="text-[9px] text-green-600 font-bold uppercase tracking-wider">Assigned</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pb-16">
                            <h4 style="font-size:11px; font-weight:800; color:#6b7280; text-transform:uppercase; margin-bottom:8px;">Available Reviewers</h4>
                            <div id="tutorial-mock-reviewer-tabs" class="flex border-b border-gray-200 mb-4 gap-4">
                                <button id="tutorial-mock-reviewer-tab-panel" class="pb-2 text-xs font-bold border-b-2 border-bsu-dark text-bsu-dark transition-colors">Scientist/Experts</button>
                                <button id="tutorial-mock-reviewer-tab-layperson" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 transition-colors">Laypersons</button>
                                <button id="tutorial-mock-reviewer-tab-consultant" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 transition-colors">External Consultant</button>
                            </div>
                            <div id="tutorial-mock-reviewer-list">
                                <button id="tutorial-mock-reviewer-card" class="reviewer-btn" type="button">
                                    <div>
                                        <div class="text-[11px] font-bold text-gray-800">Dr. Maria Santos</div>
                                        <div class="text-[10px] text-gray-500 mt-1">Bioethics / Public Health</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700">View Details</span>
                                </button>
                                <button class="reviewer-btn" type="button">
                                    <div>
                                        <div class="text-[11px] font-bold text-gray-800">Mr. Allan Reyes</div>
                                        <div class="text-[10px] text-gray-500 mt-1">Layperson / Community Representative</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700">View Details</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tutorial-mock-footer" class="modal-footer">
                <button type="button" class="btn btn-outline">Cancel</button>
                <button id="tutorial-mock-confirm-btn" type="button" class="btn btn-primary">Confirm Evaluation</button>
            </div>
        </div>
    </div>

</div>

<div x-show="notification.open"
        x-transition.opacity.duration.150ms
        class="fixed top-20 right-6 z-[9999] bg-white border rounded-lg shadow-lg p-4 w-80"
        :class="notification.type === 'success' ? 'border-green-200' : 'border-red-200'"
        x-cloak>
        <div class="text-[11px] font-black uppercase tracking-wider"
            :class="notification.type === 'success' ? 'text-green-700' : 'text-red-700'"
            x-text="notification.title"></div>
        <div class="text-xs font-semibold text-gray-700 mt-1" x-text="notification.message"></div>
    </div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('protocolEvaluation', () => ({
        // 1. CORE STATE
        activeTab: 'evaluation',
        searchQuery: '',
        sortOrder: 'newest',
        timeTicker: Date.now(),

        // 2. INJECT DYNAMIC DATA FROM BACKEND
        proposals: @json($proposals ?? []),
        allReviewers: @json($reviewers ?? []),
        externalConsultants: @json($consultants ?? []),

        // 3. DOCUMENTS STATE (Updated to support Active vs Legacy)
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,
        docUrls: {},
        docGroups: {},
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: 'Preview',
        appDetails: { id: '', researcher: '', title: '' },

        // Standardized document titles
        docLabels: {
            'letter_request': 'Letter Request.pdf',
            'endorsement_letter': 'Endorsement Letter.pdf',
            'full_proposal': 'Study Protocol.pdf',
            'technical_review_approval': 'Technical Review Approval.pdf',
            'curriculum_vitae': 'Curriculum Vitae.pdf',
            'informed_consent': 'Informed Consent Form.pdf',
            'questionnaire': 'Questionnaire.pdf',
            'data_collection': 'Data Collection Forms.pdf',
            'product_brochure': 'Product Brochure.pdf',
            'philippine_fda': 'Philippine FDA Approval.pdf',
            'manuscript': 'Manuscript.pdf',
            'special_populations': 'Special Populations.pdf',
            'others': 'Other Document.pdf'
        },

        // 4. MODAL AND WORKFLOW STATE
        isPreviewModalOpen: false,
        selectedProtocol: null,
        classification: '',
        isReassigning: false,
        initialReassignClassification: '',
        assignments: [],
        showConsultantForm: false,
        consultantRole: '',
        reviewerTab: 'panel',
        showReviewerModal: false,
        selectedReviewer: null,

        // 5. NOTIFICATION STATE (Unified)
        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',
        notificationType: 'success', // 'success', 'error', or 'warning'
        notificationTimer: null,

        confirmOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmAction: null,

        init() {
            const savedTab = localStorage.getItem('protocolEvaluation.activeTab');
            const allowedTabs = ['evaluation', 'reassignment', 'awaiting'];
            if (allowedTabs.includes(savedTab)) {
                this.activeTab = savedTab;
            }
            setInterval(() => {
                this.timeTicker = Date.now();
            }, 1000);
        },

        setActiveTab(tab) {
            this.activeTab = tab;
            localStorage.setItem('protocolEvaluation.activeTab', tab);
        },

        getFirstProtocolForTab(tab) {
            const current = this.activeTab;
            this.activeTab = tab;
            const result = this.filteredProposals.length ? this.filteredProposals[0] : null;
            this.activeTab = current;
            return result;
        },

        // ADD: Logic for 10 (Expedited) vs 21 (Full Board) days
        getDaysAllowed(classification) {
            if (classification === 'Expedited') return 10;
            if (classification === 'Full Board') return 21;
            return 0;
        },

        // ADD: Calculate when the review is actually due
        getReviewDeadlineMs(protocol, reviewer) {
            const startSource = reviewer?.acceptedAt || reviewer?.dateAccepted || reviewer?.dateAssigned || protocol?.date;
            if (!startSource) return 0;

            const startMs = new Date(startSource).getTime();
            const daysAllowed = this.getDaysAllowed(protocol.classification || this.classification);

            if (daysAllowed === 0) return 0;
            return startMs + (daysAllowed * 24 * 60 * 60 * 1000);
        },

        // ADD: Check if time has run out
        isReviewOverdue(protocol, reviewer) {
            if (this.normalizeReviewerStatus(reviewer?.status) !== 'Accepted') return false;
            const deadlineMs = this.getReviewDeadlineMs(protocol, reviewer);
            return deadlineMs > 0 && deadlineMs <= this.timeTicker;
        },

        // ADD: For the UI countdown display
        getReviewCountdown(protocol, reviewer) {
            if (this.normalizeReviewerStatus(reviewer?.status) !== 'Accepted') return '';
            const deadlineMs = this.getReviewDeadlineMs(protocol, reviewer);
            if (!deadlineMs) return '';

            const remainingMs = deadlineMs - this.timeTicker;
            if (remainingMs <= 0) return 'OVERDUE';

            const totalHours = Math.floor(remainingMs / (1000 * 60 * 60));
            const days = Math.floor(totalHours / 24);
            const remainingHours = totalHours % 24;

            return `${days}d ${remainingHours}h remaining`;
        },

        // --- FETCH & LOAD DOCUMENTS ---
        async openPreviewModal(protocol) {
            this.syncExpiredReviewerStatuses(protocol);
            this.selectedProtocol = protocol;
            this.assignments = [];
            this.showConsultantForm = false;
            this.reviewerTab = 'panel';

            this.isReassigning = this.activeTab === 'reassignment' || protocol.status === 'reassign' || this.hasDeclinedReviewer(protocol);

            if (this.isReassigning) {
                this.initialReassignClassification = protocol.classification || 'Expedited';
                this.classification = this.initialReassignClassification;
                if (protocol.assignedReviewers) {
                    this.assignments = protocol.assignedReviewers
                        .filter(r => ['Accepted', 'Pending'].includes(this.normalizeReviewerStatus(r?.status)))
                        .map(r => {
                            const status = this.normalizeReviewerStatus(r?.status);
                            let reviewerData = this.allReviewers.find(x => x.name === r.name) || this.externalConsultants.find(x => x.name === r.name);
                            if(reviewerData) {
                                return {
                                    ...reviewerData,
                                    isConsultant: reviewerData.type === 'External Consultant',
                                    locked: true,
                                    lockedStatus: status,
                                    dateAssigned: r.dateAssigned || protocol.dateAssigned || protocol.date
                                };
                            }
                            return {
                                id: Date.now() + Math.random(),
                                name: r.name,
                                type: 'Unknown',
                                isConsultant: false,
                                locked: true,
                                lockedStatus: status,
                                dateAssigned: r.dateAssigned || protocol.dateAssigned || protocol.date
                            };
                        });
                }
            } else {
                this.initialReassignClassification = '';
                this.classification = '';
            }

            this.isPreviewModalOpen = true;
            document.body.style.overflow = 'hidden';

            // --- ACTUAL DOCUMENT FETCHING ---
            this.isLoadingDocs = true;
            this.activeDocKey = null;
            this.activeDocUrl = null;
            this.docGroups = {}; // Used to power the green checkmarks

            try {
                const response = await fetch(`/secretariat/applications/${protocol.id}`);
                if (response.ok) {
                    const data = await response.json();
                    console.log("🚀 FRESH API DATA:", data);

                    this.appDetails = {
                        id: data.protocol_code,
                        researcher: data.primary_researcher || data.name_of_researcher || 'N/A',
                        title: data.research_title || 'Application Form Preview'
                    };

                    this.docGroups = data.documents || {}; // Store raw grouped data for hasDoc()
                    this.docUrls = {}; // Used for proof of payment

                    if (data.payment && data.payment.proof_url) {
                        this.docUrls['proof'] = data.payment.proof_url;
                    }

                    const basicTypes = ['letter_request', 'endorsement_letter', 'full_proposal', 'technical_review_approval', 'curriculum_vitae', 'informed_consent', 'manuscript'];

                    // REACTIVITY FIX: Build a temporary object first, then assign it to Alpine
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const docs = data.documents[type];
                            if (!docs || docs.length === 0) return;

                            let activeDocs = [];
                            let legacyDocs = [];

                            // Smart Sorting for Versions
                            const newestDoc = docs[0];
                            const match = newestDoc.url ? newestDoc.url.match(/resubmit_[a-zA-Z_]+_(\d+)_/) : null;

                            if (match) {
                                const newestTimestamp = match[1];
                                docs.forEach(d => {
                                    const dMatch = d.url ? d.url.match(/resubmit_[a-zA-Z_]+_(\d+)_/) : null;
                                    if (dMatch && dMatch[1] === newestTimestamp) activeDocs.push(d);
                                    else legacyDocs.push(d);
                                });
                            } else {
                                activeDocs = docs;
                                legacyDocs = [];
                            }

                            const title = this.docLabels[type] || type.replace(/_/g, ' ');
                            const isBasic = basicTypes.includes(type);

                            // Format Active Docs
                            activeDocs.forEach(doc => {
                                const isRevised = doc.url && doc.url.includes('resubmit_');
                                let cleanDesc = doc.description ? doc.description.replace('(Resubmitted)', '').trim() : 'View File';

                                const docObj = {
                                    id: `doc-${doc.id}`,
                                    label: title,
                                    desc: cleanDesc,
                                    url: doc.url,
                                    isRevised: isRevised,
                                    rawType: type // Kept for reference
                                };

                                if (isBasic) tempDocs.activeBasic.push(docObj);
                                else tempDocs.activeSupp.push(docObj);
                            });

                            // Format Legacy Docs
                            legacyDocs.forEach(doc => {
                                let cleanDesc = doc.description ? doc.description.replace('(Resubmitted)', '').trim() : 'Archived Version';
                                tempDocs.legacy.push({
                                    id: `doc-${doc.id}`,
                                    label: title + ' (Archived)',
                                    desc: cleanDesc,
                                    url: doc.url,
                                    isRevised: false
                                });
                            });
                        });
                    }

                    // TRIGGER ALPINE REACTIVITY ALL AT ONCE
                    this.loadedDocs = tempDocs;
                    console.log("✅ PROCESSED DOCS FOR UI:", this.loadedDocs);

                    this.viewDocument('appform');
                } else {
                    console.error("404 Error: Protocol not found.");
                }
            } catch (e) {
                console.error('Failed to load documents:', e);
            } finally {
                this.isLoadingDocs = false;
            }
        },

        closePreviewModal() {
            this.isPreviewModalOpen = false;
            this.showReviewerModal = false;
            this.initialReassignClassification = '';
            document.body.style.overflow = '';
        },

        // --- DOCUMENT VIEWER HELPERS ---
        viewDocument(key, url = null, title = null, isSupp = false) {
            this.activeDocKey = key;
            this.activeDocUrl = url || this.docUrls[key] || null;

            if (key === 'appform') {
                this.activeDocTitle = 'Application Form Preview';
            } else {
                this.activeDocTitle = title || key;
            }
        },

        // FIX 2: Added safety checks so it doesn't crash before the fetch finishes
        hasDoc(key) {
            if (!this.docGroups) return false;

            const keyMap = {
                'letter': 'letter_request',
                'endorsement': 'endorsement_letter',
                'proposal': 'full_proposal',
                'technicalreviewapproval': 'technical_review_approval',
                'cv': 'curriculum_vitae',
                'questionnaire': 'questionnaire',
                'datacollection': 'data_collection',
                'productbrochure': 'product_brochure',
                'fda': 'philippine_fda',
                'manuscript': 'manuscript'
            };

            if (key === 'consent_en') {
                return this.docGroups['informed_consent']?.some(d => (d.description || '').toLowerCase().includes('english')) || false;
            }
            if (key === 'consent_ph') {
                return this.docGroups['informed_consent']?.some(d => (d.description || '').toLowerCase().includes('filipino')) || false;
            }

            const dbKey = keyMap[key] || key;
            return (this.docGroups[dbKey] && this.docGroups[dbKey].length > 0) ? true : false;
        },

        // FIX 3: Re-added the missing getSuppData function!
        getSuppData(typeSubstring) {
            if (!this.docGroups) return [];

            if (typeSubstring === 'special') {
                return this.docGroups['special_populations'] || [];
            }
            if (typeSubstring === 'other') {
                return this.docGroups['others'] || [];
            }
            return [];
        },

        isImageView() {
            if (!this.activeDocUrl) return false;
            return this.activeDocKey === 'proof' || this.activeDocUrl.match(/\.(jpeg|jpg|gif|png|webp)$/i);
        },

        // --- ALL OTHER TIMELINE & WORKFLOW LOGIC REMAINS IDENTICAL ---
        getRelativeTime(dateStr) { return this.formatElapsed(dateStr); },
        formatElapsed(dateInput) {
            if (!dateInput) return 'N/A';
            const date = new Date(dateInput);
            const diffMs = Math.max(0, new Date() - date);
            const mins = Math.floor(diffMs / (1000 * 60));
            if (mins < 60) return `${Math.max(1, mins)} minute${mins === 1 ? '' : 's'} ago`;
            const hours = Math.floor(mins / 60);
            if (hours < 24) return `${hours} hour${hours === 1 ? '' : 's'} ago`;
            const days = Math.floor(hours / 24);
            return `${days} day${days === 1 ? '' : 's'} ago`;
        },
        showNotification(title, message, type = 'success') {
            // Set the individual variables
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationType = type;
            this.notificationOpen = true;

            // Clear any existing timer to prevent premature closing
            if (this.notificationTimer) clearTimeout(this.notificationTimer);

            // Auto-hide after 3.5 seconds
            this.notificationTimer = setTimeout(() => {
                this.notificationOpen = false;
            }, 3500);
        },
        closeNotification() { this.notificationOpen = false; },
        openConfirm(title, message, onConfirm) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmAction = onConfirm;
            this.confirmOpen = true;
        },
        cancelConfirm() {
            this.confirmOpen = false;
            this.confirmAction = null;
        },
        runConfirm() {
            if (typeof this.confirmAction === 'function') this.confirmAction();
            this.cancelConfirm();
        },
        getAcceptedCount(protocol) {
            if (!protocol?.assignedReviewers?.length) return 0;
            return protocol.assignedReviewers.filter(r => r.status === 'Accepted').length;
        },
        getClassificationTagClass(classification) {
            if (classification === 'Exempted') return 'text-blue-700 border-blue-200 bg-blue-50';
            if (classification === 'Expedited' || classification === 'Full Board') return 'text-red-700 border-red-200 bg-red-50';
            return 'text-bsu-dark border-bsu-dark/20 bg-gray-50';
        },
        getProtocolClassifiedDate(protocol) {
            return this.formatDateOnly(protocol?.classifiedDate || protocol?.date || 'N/A');
        },
        formatDateOnly(dateInput) {
            if (!dateInput || dateInput === 'N/A') return 'N/A';
            const parsed = new Date(dateInput);
            if (!Number.isNaN(parsed.getTime())) {
                const year = parsed.getFullYear();
                const month = String(parsed.getMonth() + 1).padStart(2, '0');
                const day = String(parsed.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            if (typeof dateInput === 'string' && dateInput.includes('T')) return dateInput.split('T')[0];
            return dateInput;
        },
        getReviewerAssignedDate(protocol, reviewer) { return this.formatDateOnly(reviewer?.dateAssigned || protocol?.dateAssigned || protocol?.date || 'N/A'); },
        normalizeReviewerStatus(status) {
            const raw = String(status || '').trim().toLowerCase();
            if (raw === 'accepted') return 'Accepted';
            if (raw === 'declined') return 'Declined';
            if (raw === 'expired') return 'Expired';
            if (raw === 'pending') return 'Pending';
            return 'Pending';
        },
        getReviewerPendingDeadlineMs(protocol, reviewer) {
            const source = reviewer?.dateAssigned || reviewer?.assignedAt || protocol?.dateAssigned || protocol?.date;
            if (!source) return 0;
            const assignedMs = new Date(source).getTime();
            if (Number.isNaN(assignedMs)) return 0;
            return assignedMs + (24 * 60 * 60 * 1000);
        },
        isReviewerNoResponse(protocol, reviewer) {
            const normalized = this.normalizeReviewerStatus(reviewer?.status);
            if (normalized === 'Expired') return true;
            if (normalized !== 'Pending') return false;
            const deadlineMs = this.getReviewerPendingDeadlineMs(protocol, reviewer);
            if (!deadlineMs) return false;
            return deadlineMs <= this.timeTicker;
        },
        getReviewerDisplayStatus(protocol, reviewer, context = 'default') {
            const normalized = this.normalizeReviewerStatus(reviewer?.status);

            if (normalized === 'Expired') return 'Expired';
            if (normalized === 'Declined') return 'Declined';

            if (normalized === 'Pending' && this.isReviewerNoResponse(protocol, reviewer)) {
                return 'Expired';
            }

            return normalized;
        },
        getReviewerStatusTextClass(status, protocol = null, reviewer = null, context = 'default') {
            const normalized = reviewer ? this.getReviewerDisplayStatus(protocol, reviewer, context) : this.normalizeReviewerStatus(status);
            if (normalized === 'Accepted') return 'text-green-700';
            if (normalized === 'Declined' || normalized === 'Expired') return 'text-red-600';
            return 'text-orange-600';
        },
        getReviewerStatusBadgeClass(status, protocol = null, reviewer = null, context = 'default') {
            const normalized = reviewer ? this.getReviewerDisplayStatus(protocol, reviewer, context) : this.normalizeReviewerStatus(status);
            if (normalized === 'Accepted') return 'bg-green-50 text-green-700 border-green-200';
            if (normalized === 'Declined' || normalized === 'Expired') return 'bg-red-50 text-red-600 border-red-200';
            return 'bg-orange-50 text-orange-600 border-orange-200';
        },
        getReviewerStatusDateLabel(status, protocol = null, reviewer = null, context = 'default') {
            const normalized = reviewer ? this.getReviewerDisplayStatus(protocol, reviewer, context) : this.normalizeReviewerStatus(status);
            if (normalized === 'Accepted') return 'Date Accepted';
            if (normalized === 'Declined') return 'Date Declined';
            if (normalized === 'Expired') return 'Date Expired';
            return 'Date Assigned';
        },
        getReviewerStatusDateSource(protocol, reviewer, context = 'default') {
            const normalized = this.getReviewerDisplayStatus(protocol, reviewer, context);
            if (normalized === 'Accepted') return reviewer?.dateAccepted || reviewer?.acceptedAt || reviewer?.dateAssigned || protocol?.dateAssigned || protocol?.date || '';
            if (normalized === 'Declined') {
                if (context === 'reassignment' && this.normalizeReviewerStatus(reviewer?.status) === 'Pending' && this.isReviewerNoResponse(protocol, reviewer)) {
                    const deadlineMs = this.getReviewerPendingDeadlineMs(protocol, reviewer);
                    if (deadlineMs) return new Date(deadlineMs).toISOString();
                }
                return reviewer?.dateDeclined || reviewer?.declinedAt || reviewer?.dateAssigned || protocol?.declinedAt || protocol?.dateAssigned || protocol?.date || '';
            }
            if (normalized === 'Expired') {
                const deadlineMs = this.getReviewerPendingDeadlineMs(protocol, reviewer);
                if (deadlineMs) return reviewer?.dateExpired || new Date(deadlineMs).toISOString();
                return reviewer?.dateExpired || reviewer?.dateAssigned || protocol?.dateAssigned || protocol?.date || '';
            }
            return reviewer?.dateAssigned || protocol?.dateAssigned || protocol?.date || '';
        },
        getReviewerStatusDate(protocol, reviewer, context = 'default') { return this.formatDateOnly(this.getReviewerStatusDateSource(protocol, reviewer, context)); },
        getProtocolReviewersForDisplay(protocol) {
            const base = Array.isArray(protocol?.assignedReviewers) ? protocol.assignedReviewers.map(r => ({ ...r })) : [];
            if (protocol?.declinedBy) {
                const alreadyInList = base.some(r => (r?.name || '').trim() === String(protocol.declinedBy).trim());
                if (!alreadyInList) base.push({ name: protocol.declinedBy, status: 'Declined', dateDeclined: protocol?.declinedAt || protocol?.dateAssigned || protocol?.date });
            }
            return base;
        },

        getAwaitingStatusText(protocol) {
            if (protocol?.classification === 'Exempted') return "Waiting for Chair's Approval";
            if (protocol?.external_consultant) return "Waiting for Chair to assign External Consultant";
            return 'Waiting for Reviewers to Approve';
        },
        getAwaitingStatusClass(protocol) {
            if (protocol?.classification === 'Exempted' || protocol?.external_consultant) return 'text-blue-700 border-blue-200 bg-blue-50';
            return 'text-orange-700 border-orange-200 bg-orange-50';
        },
        getApprovalCountdown(protocol) {
            const source = protocol?.assignedAt || protocol?.dateAssigned || protocol?.date;
            if (!source) return '24h 00m';
            const assignedMs = new Date(source).getTime();
            if (Number.isNaN(assignedMs)) return '24h 00m';
            const remainingMs = (assignedMs + (24 * 60 * 60 * 1000)) - this.timeTicker;
            if (remainingMs <= 0) return '0h 00m';
            const totalMinutes = Math.floor(remainingMs / (1000 * 60));
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            return `${hours}h ${String(minutes).padStart(2, '0')}m`;
        },
        getReviewerPendingCountdown(protocol, reviewer, context = 'default') {
            if (this.getReviewerDisplayStatus(protocol, reviewer, context) !== 'Pending') return '';
            const deadlineMs = this.getReviewerPendingDeadlineMs(protocol, reviewer);
            if (!deadlineMs) return '24h 00m';
            const remainingMs = deadlineMs - this.timeTicker;
            if (remainingMs <= 0) return '0h 00m';
            const totalMinutes = Math.floor(remainingMs / (1000 * 60));
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            return `${hours}h ${String(minutes).padStart(2, '0')}m`;
        },
        syncExpiredReviewerStatuses(protocol) {
            if (!protocol || !Array.isArray(protocol.assignedReviewers)) return;
            let needsReassignment = false;

            protocol.assignedReviewers.forEach(reviewer => {
                const normalized = this.normalizeReviewerStatus(reviewer?.status);

                // Rule 1: Acceptance Deadline (24 Hours)
                if (normalized === 'Pending' && this.isReviewerNoResponse(protocol, reviewer)) {
                    reviewer.status = 'Expired';
                    needsReassignment = true;
                }

                // Rule 2: Review Completion Deadline (10 or 21 Days)
                if (normalized === 'Accepted' && this.isReviewOverdue(protocol, reviewer)) {
                    reviewer.status = 'Expired';
                    reviewer.isOverdue = true;
                    needsReassignment = true;
                }
            });

            if (needsReassignment) {
                protocol.expired = true;
            }
        },

        capitalizeName(name) {
            if (!name) return '';
            return name
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },

        getReviewerStatusSubtext(protocol, reviewer, context = 'default') {
            const status = this.getReviewerDisplayStatus(protocol, reviewer, context);

            if (status === 'Expired') return 'Deadline passed';
            if (status === 'Declined') return 'Rejected';

            const source = this.getReviewerStatusDateSource(protocol, reviewer, context);
            return this.getRelativeTime(source);
        },
        normalizeReviewerName(name) { return String(name || '').trim().toLowerCase(); },
        // Add this helper function
        formatAvgTime(days) {
            if (days === undefined || days === null || days === 0) return 'No data yet';
            return `${days} day${days !== 1 ? 's' : ''}`;
        },
        getProtocolReviewerOutcomes(protocol) {
            const accepted = []; const declined = []; const expired = [];
            const pushUnique = (list, name) => {
                const normalized = this.normalizeReviewerName(name);
                if (!normalized) return;
                if (!list.includes(normalized)) list.push(normalized);
            };
            if (Array.isArray(protocol?.assignedReviewers)) {
                protocol.assignedReviewers.forEach(reviewer => {
                    const name = this.normalizeReviewerName(reviewer?.name);
                    if (!name) return;
                    const status = this.normalizeReviewerStatus(reviewer?.status);
                    if (status === 'Accepted') return pushUnique(accepted, name);
                    if (status === 'Declined') return pushUnique(declined, name);
                    if (status === 'Expired') return pushUnique(expired, name);
                    if (status === 'Pending' && this.isReviewerNoResponse(protocol, reviewer)) pushUnique(expired, name);
                });
            }
            const fallbackDeclinedBy = this.normalizeReviewerName(protocol?.declinedBy);
            const fallbackTracked = fallbackDeclinedBy && (accepted.includes(fallbackDeclinedBy) || declined.includes(fallbackDeclinedBy) || expired.includes(fallbackDeclinedBy));
            if (fallbackDeclinedBy && !fallbackTracked) {
                if (protocol?.expired) pushUnique(expired, fallbackDeclinedBy);
                else pushUnique(declined, fallbackDeclinedBy);
            }
            return { accepted, declined, expired };
        },
        get selectedReviewerOutcomes() { return this.getProtocolReviewerOutcomes(this.selectedProtocol); },
        get selectedDeclinedReviewers() { return this.selectedReviewerOutcomes.declined; },
        get selectedExpiredReviewers() { return this.selectedReviewerOutcomes.expired; },
        get currentWorkflowStep() { return this.classification ? 2 : 1; },
        get timelineStepOneLabel() { return this.isReassigning ? 'Classify Protocol' : 'Classify Protocol'; },
        get timelineStepTwoLabel() {
            if (this.isReassigning) return this.classification === 'Exempted' ? 'Re-route To Chair' : 'Reassign Reviewers';
            return this.classification === 'Exempted' ? 'Send To Chair For Approval' : 'Assign Reviewers';
        },

        get selectedDeclinedReviewerDetails() {
            if (!Array.isArray(this.selectedProtocol?.assignedReviewers)) return [];

            return this.selectedProtocol.assignedReviewers
                .filter(reviewer => this.normalizeReviewerStatus(reviewer?.status) === 'Declined')
                .map(reviewer => ({
                    name: this.normalizeReviewerName(reviewer?.name),
                    declinedReason: reviewer?.declinedReason || null,
                    dateDeclined: reviewer?.dateDeclined || null,
                }));
        },

        // UPDATED: Now also checks if the 10/21 day review period has passed
        hasDeclinedReviewer(protocol) {
            if (!protocol) return false;
            return this.getProtocolReviewersForDisplay(protocol).some(r => {
                const normalized = this.normalizeReviewerStatus(r?.status);
                return normalized === 'Declined' ||
                    normalized === 'Expired' ||
                    this.isReviewerNoResponse(protocol, r) ||
                    this.isReviewOverdue(protocol, r); // Added overdue check
            });
        },
        isDeclined(reviewer) {
            if (!reviewer?.name) return false;
            const reviewerName = this.normalizeReviewerName(reviewer.name);
            return this.selectedDeclinedReviewers.some(name => this.normalizeReviewerName(name) === reviewerName);
        },
        isNoResponse(reviewer) {
            if (!reviewer?.name) return false;
            const reviewerName = this.normalizeReviewerName(reviewer.name);
            return this.selectedExpiredReviewers.some(name => this.normalizeReviewerName(name) === reviewerName);
        },

        getReviewersByType(type) {
            return this.allReviewers
                .filter(r => r.type === type && !this.assignments.some(a => a.id === r.id))
                .map(r => {
                    const hasDeclined = this.isDeclined(r);
                    const hasExpired = this.isNoResponse(r);
                    const isOverQuota = (r.evaluations?.length || 0) >= 3;
                    const disabled = hasDeclined || hasExpired || isOverQuota;
                    let reason = '';
                    if (hasDeclined) reason = 'Declined';
                    else if (hasExpired) reason = 'Expired';
                    else if (isOverQuota) reason = 'Quota Full';
                    return { ...r, isDisabled: disabled, disabledReason: reason };
                });
        },
        get availableConsultants() {
            return this.externalConsultants
                .filter(c => !this.assignments.some(a => a.id === c.id))
                .map(c => {
                    const hasDeclined = this.isDeclined(c);
                    const hasExpired = this.isNoResponse(c);
                    const isOverQuota = (c.evaluations?.length || 0) >= 3;
                    const disabled = hasDeclined || hasExpired || isOverQuota;
                    let reason = '';
                    if (hasDeclined) reason = 'Declined';
                    else if (hasExpired) reason = 'Expired';
                    else if (isOverQuota) reason = 'Quota Full';
                    return { ...c, isDisabled: disabled, disabledReason: reason };
                });
        },

        openReviewerDetails(reviewer) {
            // If the reviewer is disabled (Quota full, Declined, or Expired)
            if (reviewer.isDisabled) {
                // Trigger the popup immediately instead of opening the details modal
                this.showNotification(
                    'Reviewer Unavailable',
                    `This reviewer cannot be assigned: ${reviewer.disabledReason}`,
                    'error'
                );
                return;
            }

            // If not disabled, proceed to open the assignment modal
            this.selectedReviewer = reviewer;
            this.showReviewerModal = true;
        },

        get evalCount() { return this.proposals.filter(p => p.status === 'pending' || p.status === 'documents_checking').length; },
        get reassignCount() { return this.proposals.filter(p => this.hasDeclinedReviewer(p)).length; },
        get awaitingCount() {
            return this.proposals.filter(p => p.status === 'awaiting_reviewer_approval' || p.status === 'exempted_awaiting_chair_approval').length;
        },

        parseDateMs(dateInput) {
            if (!dateInput) return 0;
            const value = new Date(dateInput).getTime();
            return Number.isNaN(value) ? 0 : value;
        },
        getProtocolSortTimestamp(protocol) {
            if (!protocol) return 0;
            const classifiedMs = Math.max(this.parseDateMs(protocol.classifiedAt), this.parseDateMs(protocol.classifiedDate));
            const assignedMs = Math.max(this.parseDateMs(protocol.assignedAt), this.parseDateMs(protocol.dateAssigned));
            const reviewerAssignedMs = Array.isArray(protocol.assignedReviewers) ? protocol.assignedReviewers.reduce((latest, reviewer) => {
                return Math.max(latest, this.parseDateMs(reviewer?.dateAssigned));
            }, 0) : 0;
            return Math.max(classifiedMs, assignedMs, reviewerAssignedMs, this.parseDateMs(protocol.date));
        },

        get filteredProposals() {
            let res = this.proposals.filter(p => {
                const search = this.searchQuery.toLowerCase();
                const matchSearch = p.id.toLowerCase().includes(search) || p.title.toLowerCase().includes(search);
                if(this.activeTab === 'evaluation') return matchSearch && (p.status === 'pending' || p.status === 'documents_checking');
                if(this.activeTab === 'reassignment') return matchSearch && this.hasDeclinedReviewer(p);
                if(this.activeTab === 'awaiting') return matchSearch && (p.status === 'awaiting_reviewer_approval' || p.status === 'exempted_awaiting_chair_approval' || p.status === 'under_review');
                return false;
            });
            return res.sort((a, b) => {
                const aTime = this.getProtocolSortTimestamp(a);
                const bTime = this.getProtocolSortTimestamp(b);
                return this.sortOrder === 'newest' ? bTime - aTime : aTime - bTime;
            });
        },

        resetClassification() {
            this.classification = '';
            this.assignments = [];
            this.showConsultantForm = false;
        },
        requestClassificationChange() {
            if (this.isReassigning) {
                const hasLockedReviewers = this.assignments.some(a => a.locked);
                this.openConfirm(
                    'Change Reassign Type',
                    hasLockedReviewers ? 'Changing the review type will remove the currently locked reviewers (accepted/pending) from this reassignment. Continue?' : 'Change the reassignment review type and proceed?',
                    () => { this.resetClassification(); this.reviewerTab = 'panel'; }
                );
                return;
            }
            this.resetClassification();
        },
        selectClassification(nextClassification) {
            if (!nextClassification || this.classification === nextClassification) return;
            this.classification = nextClassification;
        },

        async confirmExemption() {
            this.openConfirm('Send To Chair', 'Proceed with routing this exempted protocol to Chair approval?', async () => {
                const payload = {
                    status: 'exempted_awaiting_chair_approval',
                    classification: 'Exempted',
                    comment: 'Protocol classified as Exempted. Routing to Chair.',
                    reviewers: [],
                    principal_investigator: this.selectedProtocol?.proponent || this.selectedProtocol?.primary_researcher
                };

                try {
                    const response = await fetch(`/research/status/${this.selectedProtocol.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        let p = this.proposals.find(x => x.id === this.selectedProtocol.id);
                        if (p) {
                            const nowDate = new Date().toISOString().slice(0, 10);
                            const nowDateTime = new Date().toISOString();

                            p.status = 'exempted_awaiting_chair_approval';
                            p.classification = 'Exempted';
                            p.assignedReviewers = [];
                            p.classifiedDate = nowDate;
                            p.classifiedAt = nowDateTime;
                            p.dateAssigned = nowDate;
                            p.assignedAt = nowDateTime;
                            p.pendingReason = 'Chair Approval (Exempted)';
                        }

                        // Success Notification
                        this.showNotification('Protocol Routed', 'Exempted protocol was saved and sent to Chair approval.', 'success');
                        this.closePreviewModal();
                        this.activeTab = 'awaiting';
                        localStorage.setItem('protocolEvaluation.activeTab', 'awaiting');
                    } else {
                        // Error Notification (Replaced alert)
                        this.showNotification('Database Error', result.message || "Failed to save exemption.", 'error');
                    }
                } catch (error) {
                    // Error Notification (Replaced alert)
                    this.showNotification('Network Error', 'Could not reach the server.', 'error');
                }
            });
        },

        assignSelectedReviewer() {
            if (this.selectedReviewer) {
                this.assignments.push({
                    id: this.selectedReviewer.id,
                    name: this.selectedReviewer.name,
                    type: this.selectedReviewer.type,
                    isConsultant: this.selectedReviewer.type === 'External Consultant' || this.selectedReviewer.type === 'Consultant',
                    locked: false,
                    dateAssigned: new Date().toISOString()
                });
            }
            this.showReviewerModal = false;
            this.selectedReviewer = null;
        },

        // Add these to your return {} block inside Alpine.data()

        notification: { open: false, title: '', message: '', type: 'success' },
        notificationTimer: null,

        showNotification(title, message, type = 'success') {
            this.notification = { open: true, title, message, type };

            // Clear any existing timer so notifications don't overlap strangely
            if (this.notificationTimer) clearTimeout(this.notificationTimer);

            // Auto-hide after 3.5 seconds
            this.notificationTimer = setTimeout(() => {
                this.notification.open = false;
            }, 3500);
        },

        submitConsultant() {
            if (this.consultantRole.trim() === '') return;
            const newId = 'cons_' + Date.now();
            this.externalConsultants.push({ id: newId, name: 'External Consultant Request', type: 'External Consultant', avgTime: 'N/A', evaluations: [], role: this.consultantRole });
            this.assignments.push({ id: newId, name: 'External Consultant Request', type: 'Consultant', isConsultant: true, locked: false, dateAssigned: new Date().toISOString(), role: this.consultantRole });
            this.consultantRole = '';
            this.showConsultantForm = false;
            this.reviewerTab = 'consultant';
        },

        removeAssignment(id) { this.assignments = this.assignments.filter(a => a.id !== id || a.locked); },
        get primaryCount() { return this.assignments.filter(a => !a.isConsultant).length; },
        get hasPanelExpert() { return this.assignments.some(a => a.type === 'Panel Expert'); },
        get hasLayperson() { return this.assignments.some(a => a.type === 'Layperson'); },
        get hasConsultant() { return this.assignments.some(a => a.isConsultant); },
        get canConfirmEvaluation() {
            // Calculate effective primary count (Internal + External)
            const effectivePrimaryCount = this.primaryCount + (this.hasConsultant ? 1 : 0);

            // Expert requirement is satisfied by either an internal expert OR an external consultant
            const expertSatisfied = this.hasPanelExpert || this.hasConsultant;

            return effectivePrimaryCount >= 2 &&
                effectivePrimaryCount <= 3 &&
                expertSatisfied &&
                this.hasLayperson;
        },

        async handleConfirmEvaluation() {
            if (!this.selectedProtocol || !this.selectedProtocol.id) return;

            const customConsultant = this.assignments.find(a => String(a.id).startsWith('cons_'));
            const extConsultantReason = customConsultant ? customConsultant.role : null;
            const realReviewers = this.assignments
                .filter(a => !String(a.id).startsWith('cons_'))
                .map(a => ({
                    reviewer_id: a.id,
                    status: a.locked ? (a.lockedStatus || 'Pending') : 'Pending'
                }));

            const payload = {
                status: 'awaiting_reviewer_approval',
                classification: this.classification,
                comment: this.isReassigning ? 'Reviewers reassigned.' : 'Initial evaluation and reviewer assignment.',
                reviewers: realReviewers,
                external_consultant_reason: extConsultantReason
            };

            try {
                const response = await fetch(`/research/status/${this.selectedProtocol.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    let p = this.proposals.find(x => x.id === this.selectedProtocol.id);
                    if (p) {
                        p.status = 'awaiting_reviewer_approval';
                        p.classification = this.classification;
                        if (extConsultantReason) {
                            p.external_consultant = extConsultantReason;
                            p.pendingReason = 'Pending Reviewers (Ext. Consultant Req.)';
                        } else {
                            p.external_consultant = null;
                        }
                        if (result.reviewers) p.assignedReviewers = result.reviewers;
                    }

                    this.showNotification('Success', 'Evaluation confirmed and routed successfully.', 'success');
                    this.closePreviewModal();
                    this.activeTab = 'awaiting';
                    localStorage.setItem('protocolEvaluation.activeTab', 'awaiting');
                } else {
                    // Replaced alert with error notification
                    this.showNotification('Server Error', result.message || "Could not save.", 'error');
                }
            } catch (error) {
                // Replaced alert with error notification
                this.showNotification('Request Failed', "Could not reach the server.", 'error');
            }
        }
    }));
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.driver === 'undefined') {
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css';
        document.head.appendChild(css);

        const styleOverride = document.createElement('style');
        styleOverride.innerHTML = `
            .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
            .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
            .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
            .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
            .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; }
            .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
            .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
            .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
        `;
        document.head.appendChild(styleOverride);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = initProtocolEvaluationTour;
        document.head.appendChild(script);
    } else {
        initProtocolEvaluationTour();
    }

    function initProtocolEvaluationTour(retries = 0) {
        const userId = @json(auth()->id());
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const storageKey = 'berc_tutorial_step_' + userId;
        const urlParams = new URLSearchParams(window.location.search);
        const forceTour = urlParams.get('tour') === '1';
        const tourState = localStorage.getItem(storageKey);

        if (!isFirstLogin && !forceTour) {
            localStorage.removeItem(storageKey);
            return;
        }

        // FIXED STRING MISMATCH: Looking for 'secretariat_evaluation'
        if (tourState !== 'secretariat_evaluation' && !forceTour) {
            return;
        }

        if (window.__protocolEvaluationTourStarted) return;

        const rootEl = document.querySelector('#protocol-evaluation-root');
        const alpine = rootEl?.__x?.$data || rootEl?._x_dataStack?.[0] || null;

        if (!rootEl || !alpine) {
            if (retries < 40) {
                setTimeout(() => initProtocolEvaluationTour(retries + 1), 250);
            }
            return;
        }

        window.__protocolEvaluationTourStarted = true;

        const overlay = document.getElementById('tutorial-mock-overlay');
        const classView = document.getElementById('tutorial-mock-classification-view');
        const assignView = document.getElementById('tutorial-mock-assignment-view');
        const consultantForm = document.getElementById('tutorial-mock-consultant-form');

        const tabs = {
            panel: document.getElementById('tutorial-mock-reviewer-tab-panel'),
            layperson: document.getElementById('tutorial-mock-reviewer-tab-layperson'),
            consultant: document.getElementById('tutorial-mock-reviewer-tab-consultant')
        };

        if (!overlay || !classView || !assignView) {
            window.__protocolEvaluationTourStarted = false;
            return;
        }

        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        const waitFor = async (selector, timeout = 4000) => {
            const start = Date.now();
            while (Date.now() - start < timeout) {
                const el = document.querySelector(selector);
                if (el && el.offsetParent !== null) return el;
                await wait(120);
            }
            return null;
        };

        const setReviewerTab = (key) => {
            Object.entries(tabs).forEach(([k, el]) => {
                if (!el) return;

                const active = k === key;
                el.classList.toggle('border-bsu-dark', active);
                el.classList.toggle('text-bsu-dark', active);
                el.classList.toggle('border-transparent', !active);
                el.classList.toggle('text-gray-500', !active);
            });
        };

        const openMock = async (mode = 'classification') => {
            overlay.style.display = 'flex';
            classView.style.display = mode === 'classification' ? 'block' : 'none';
            assignView.style.display = mode === 'assignment' ? 'block' : 'none';
            if (consultantForm) consultantForm.style.display = 'none';
            setReviewerTab('panel');
            await wait(180);
        };

        const closeMock = async () => {
            overlay.style.display = 'none';
            if (consultantForm) consultantForm.style.display = 'none';
            await wait(180);
        };

        const showConsultant = async (show = true) => {
            if (consultantForm) {
                consultantForm.style.display = show ? 'block' : 'none';
            }
            await wait(150);
        };

        const setActualTab = async (tab, rowSelector = null) => {
            if (typeof alpine.setActiveTab === 'function') {
                alpine.setActiveTab(tab);
            } else {
                alpine.activeTab = tab;
            }

            await wait(300);

            if (rowSelector) {
                await waitFor(rowSelector, 5000);
            }
        };

        window.closeProtocolTutorialMock = closeMock;

        const driver = window.driver.js.driver;
        let shouldRedirectToAssessmentForms = false;

        const tour = driver({
            showProgress: true,
            allowClose: false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    shouldRedirectToAssessmentForms = true;
                    localStorage.setItem(storageKey, 'secretariat_assessment');
                    tour.destroy();

                    window.location.href = "{{ url('/secretariat/assessment') }}";
                } else {s
                    tour.destroy();
                }
            },

            onDestroyed: () => {
                overlay.style.display = 'none';
                if (consultantForm) consultantForm.style.display = 'none';
                window.__protocolEvaluationTourStarted = false;

                if (!shouldRedirectToAssessmentForms && localStorage.getItem(storageKey) === 'secretariat_evaluation') {
                    localStorage.setItem(storageKey, 'done');
                }
            },

            steps: [
                {
                    element: '#tour-protocol-evaluation-header',
                    onHighlightStarted: async () => {
                        shouldRedirectToAssessmentForms = false;
                        await closeMock();
                        await setActualTab('evaluation', '#tour-first-evaluation-row');
                    },
                    popover: {
                        title: 'Protocol Evaluation Page',
                        description: 'This page is where the Secretariat classifies protocols, assigns reviewers, monitors pending responses, and handles reassignment.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-main-tabs',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('evaluation', '#tour-first-evaluation-row');
                    },
                    popover: {
                        title: 'Workflow Tabs',
                        description: 'These tabs organize the work into Evaluate Protocol, Awaiting Response, and For Reassignment.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-evaluation-row',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('evaluation', '#tour-first-evaluation-row');
                    },
                    popover: {
                        title: 'Protocol Queue',
                        description: 'Each row shows the protocol summary and opens the classification workflow.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-modal',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Mock Workflow Modal',
                        description: 'For the tutorial, this mock modal demonstrates the full process smoothly without depending on live protocol state.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-left',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Protocol Information Panel',
                        description: 'The left panel contains the application ID, study title, researcher name, and submitted requirements.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-docs',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Document Layout',
                        description: 'This is where you review the application form and supporting documents before classifying the protocol.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-classification-exempted',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Exempted Review',
                        description: 'Use this when the study qualifies for exemption and can be routed with a certificate of exemption.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-classification-expedited',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Expedited Review',
                        description: 'Use this when the protocol needs reviewer assignment but does not require full board deliberation.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-classification-fullboard',
                    onHighlightStarted: async () => {
                        await openMock('classification');
                    },
                    popover: {
                        title: 'Full Board Review',
                        description: 'Use this when the study requires broader review and discussion by the board.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-selected-classification',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                    },
                    popover: {
                        title: 'Selected Classification',
                        description: 'After classification, the chosen pathway appears here and can still be changed before confirmation.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-checklist',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                    },
                    popover: {
                        title: 'Reviewer Assignment Checklist',
                        description: 'This checklist helps ensure the assigned reviewers meet the required composition.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-assignment-summary',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                    },
                    popover: {
                        title: 'Assigned Reviewers',
                        description: 'Assigned reviewers appear here so the Secretariat can verify the final lineup before confirming.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-add-consultant-btn',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                        await showConsultant(false);
                    },
                    popover: {
                        title: 'Request External Consultant',
                        description: 'Use this when the protocol needs expertise outside the regular review pool.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-consultant-form',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                        await showConsultant(true);
                    },
                    popover: {
                        title: 'Consultant Request Form',
                        description: 'State the needed specialty or reason for the request, then submit it for coordination.',
                        side: 'left',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-reviewer-tabs',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                        await showConsultant(false);
                        setReviewerTab('panel');
                    },
                    popover: {
                        title: 'Reviewer Classification',
                        description: 'Reviewers are grouped by Scientist/Experts, Laypersons, and External Consultant so assignment stays balanced.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-reviewer-card',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                        setReviewerTab('panel');
                    },
                    popover: {
                        title: 'Reviewer Card',
                        description: 'Each reviewer card can be inspected before assignment to check expertise and fit for the protocol.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tutorial-mock-confirm-btn',
                    onHighlightStarted: async () => {
                        await openMock('assignment');
                    },
                    popover: {
                        title: 'Confirm Evaluation',
                        description: 'After classification, assignment, and optional consultant request, confirm to move the protocol into the next stage.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-tab-awaiting',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('awaiting', '#tour-first-awaiting-row');
                    },
                    popover: {
                        title: 'Awaiting Response',
                        description: 'After confirmation, the protocol moves here while waiting for reviewer acceptance or other pending responses.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-awaiting-row',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('awaiting', '#tour-first-awaiting-row');
                    },
                    popover: {
                        title: 'Awaiting Response Details',
                        description: 'This view shows the classification, assigned reviewers, and each reviewer status such as Pending, Accepted, or countdown indicators.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-tab-reassignment',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('reassignment', '#tour-first-reassignment-row');
                    },
                    popover: {
                        title: 'For Reassignment',
                        description: 'Protocols appear here when reviewers decline or fail to respond, allowing the Secretariat to assign replacements.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-reassignment-row',
                    onHighlightStarted: async () => {
                        await closeMock();
                        await setActualTab('reassignment', '#tour-first-reassignment-row');
                    },
                    popover: {
                        title: 'Reassignment Details',
                        description: 'Here the Secretariat can review the previous classification, see which reviewers declined or failed to respond, retain locked accepted reviewers, and assign replacements.',
                        side: 'top',
                        align: 'start'
                    }
                },
                {
                    // Floating popover
                    popover: {
                        title: 'Next Step: Assessment Forms',
                        description: 'Now let’s proceed to Assessment Forms where reviewer evaluations are handled.',
                        side: 'bottom',
                        align: 'center',
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        setTimeout(() => {
            tour.drive();
        }, 300);
    }
});
</script>
@endsection


