@extends('reviewer.layouts.app')

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

    /* Driver.js Custom Overrides */
    .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
    .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
    .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
    .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
    .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
    .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
    .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
    .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
</style>

<div x-data="protocolEvaluation" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Review Invitations</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Pending protocols awaiting your approval to review.</p>
        </div>
        <div class="w-full max-w-xl flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Search ID or Title..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select x-model="sortOrder" class="w-44 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-bsu-dark focus:outline-none focus:ring-1 focus:ring-bsu-dark/20">
                <option value="newest">Newest -> Oldest</option>
                <option value="oldest">Oldest -> Newest</option>
            </select>
        </div>
    </div>

    <div id="tour-invitations-list" class="app-card relative bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="grid grid-cols-[150px_minmax(0,1fr)_150px_150px] gap-4 p-4 border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
            <div>Application ID</div>
            <div>Study & Researcher</div>
            <div>Date Assigned</div>
            <div class="text-center">Action</div>
        </div>

        <template x-for="protocol in filteredProposals" :key="protocol.protocol_code">
            <div class="grid grid-cols-[150px_minmax(0,1fr)_150px_150px] gap-4 p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer items-center"
                 @click="openPreviewModal(protocol)">

                <div>
                    <span class="inline-block bg-blue-50 text-blue-700 border border-blue-200 font-mono font-bold text-xs px-2 py-1 rounded" x-text="protocol.protocol_code"></span>
                </div>

                <div class="min-w-0 pr-4">
                    <div class="text-sm font-bold text-gray-800 whitespace-normal break-words leading-tight mb-1" x-text="protocol.research_title"></div>
                    <div class="text-xs text-gray-500 font-medium italic" x-text="protocol.primary_researcher"></div>
                </div>

                <div class="flex flex-col justify-center">
                    <div class="text-xs font-bold text-gray-700" x-text="formatDate(protocol.date_assigned)"></div>
                    <div class="text-[10px] text-gray-400 font-semibold mt-0.5" x-text="getTimeAgo(protocol.date_assigned)"></div>
                </div>

                <div class="flex justify-center items-center gap-2">
                    <span class="bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 border border-gray-200 text-[10px] font-bold px-3 py-1.5 rounded uppercase tracking-wide transition-colors">
                        View Protocol
                    </span>
                </div>
            </div>
        </template>

        <div x-show="filteredProposals.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm bg-white">
            No pending invitations found.
        </div>
    </div>

    <div class="modal-overlay" :class="isPreviewModalOpen ? 'open' : ''" @click.self="closePreviewModal()" x-cloak>
        <div class="modal-box w-full max-w-[1200px] h-[90vh] flex flex-col bg-white rounded-lg shadow-2xl m-auto overflow-hidden">

            <div class="modal-header shrink-0 flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-black text-bsu-dark uppercase tracking-tight">Review Protocol Documents</h2>
                <button class="close-btn text-gray-400 hover:text-gray-700 text-2xl leading-none px-2" @click="closePreviewModal()">&times;</button>
            </div>

            <div class="modal-content flex-1 flex overflow-hidden">

                <div id="tour-protocol-info" class="protocol-info-panel w-80 shrink-0 overflow-y-auto border-r border-gray-200 p-6 bg-white relative">

                    <div class="info-group mb-4">
                        <div class="info-label text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Application ID</div>
                        <div class="modal-app-id font-mono font-bold text-base text-bsu-dark" x-text="selectedProtocol?.protocol_code"></div>
                    </div>
                    <div class="info-group mb-4">
                        <div class="info-label text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Study Title</div>
                        <div class="info-value text-xs font-bold text-gray-800 leading-tight" x-text="selectedProtocol?.research_title"></div>
                    </div>
                    <div class="info-group mb-2">
                        <div class="info-label text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Researcher Name</div>
                        <div class="info-value text-xs text-gray-600" x-text="selectedProtocol?.primary_researcher"></div>
                    </div>

                    <div style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
                        <div class="info-label text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Basic Requirements</div>

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
                                        <div style="font-size:11px; font-weight:700; color:#111827; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
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
                            <div class="info-label text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-5 mb-2">Supplementary Docs</div>
                            <template x-for="doc in loadedDocs.activeSupp" :key="doc.id">
                                <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label, true)">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
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
                                        <div style="font-size:11px; font-weight:700; color:#64748b; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center;">
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

                <div id="tour-preview-panel" class="form-preview-panel flex-1 bg-gray-100 flex flex-col relative overflow-hidden">

                    <div x-show="activeDocKey" x-cloak class="h-full flex flex-col w-full">

                        <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center shrink-0 shadow-sm z-10">
                            <span class="text-[11px] font-bold text-gray-700 uppercase tracking-wide" x-text="activeDocTitle"></span>
                            <div class="flex items-center gap-4">
                                <a x-show="activeDocUrl" :href="activeDocUrl" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">Open in New Tab ↗</a>
                                <button @click="activeDocKey = null" class="text-[10px] font-bold text-gray-500 hover:text-gray-800 bg-gray-100 px-3 py-1 rounded">Close Document</button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-auto w-full relative flex justify-center">

                            <div x-show="activeDocKey === 'appform'" class="w-full h-full p-4 sm:p-8 bg-gray-50 flex justify-center overflow-auto">
                                <div class="w-full max-w-3xl bg-white border border-gray-300 shadow-sm p-8 rounded my-auto h-fit">
                                    <div class="text-center mb-8 pb-4 border-b border-gray-200">
                                        <img src="{{ asset('logo/BERC.png') }}" style="height:45px; margin:0 auto 12px; display:block;" alt="BERC Logo" onerror="this.style.display='none'">
                                        <h3 class="text-lg font-black text-bsu-dark tracking-tight">Application Details Overview</h3>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Generated by System</p>
                                    </div>
                                    <div class="grid gap-4 text-sm">
                                        <div class="flex"><span class="w-40 font-bold text-gray-600">Protocol Code:</span> <span class="font-mono text-blue-700 font-bold" x-text="appDetails.id"></span></div>
                                        <div class="flex"><span class="w-40 font-bold text-gray-600">Research Title:</span> <span class="font-medium text-gray-800" x-text="appDetails.title"></span></div>
                                        <div class="flex"><span class="w-40 font-bold text-gray-600">Lead Researcher:</span> <span class="font-medium text-gray-800" x-text="appDetails.researcher"></span></div>
                                    </div>
                                    <div class="mt-12 border-t border-dashed border-gray-200 pt-3 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                        * Select a document from the left sidebar to view the actual files *
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeDocKey !== 'appform' && isImageView()" class="w-full h-full flex items-center justify-center absolute inset-0">
                                <img :src="activeDocUrl" class="max-w-full max-h-[90%] object-contain shadow-md bg-white p-2">
                            </div>

                            <div x-show="activeDocKey !== 'appform' && !isImageView() && activeDocUrl" class="w-full h-full absolute inset-0">
                                <iframe :src="activeDocUrl" class="w-full h-full border-none"></iframe>
                            </div>
                        </div>
                    </div>

                    <div x-show="!activeDocKey" class="h-full flex items-center justify-center">
                        <div class="text-center text-gray-400">
                            <span class="text-4xl block mb-2">📄</span>
                            <span class="text-sm font-bold">Select a document to preview</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tour-modal-footer" class="modal-footer px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 shrink-0">
                <button class="btn btn-danger bg-red-600 text-white font-bold px-6 py-2.5 rounded uppercase text-[11px] tracking-wider hover:bg-red-700 transition-colors"
                        @click="promptDecline()">
                    Decline Assignment
                </button>
                <button class="btn btn-primary bg-green-600 text-white font-bold px-6 py-2.5 rounded shadow-sm uppercase text-[11px] tracking-wider hover:bg-green-700 transition-colors"
                        @click="promptAccept()">
                    Accept Assignment
                </button>
            </div>

        </div>
    </div>

    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[200]" :class="notificationOpen ? 'flex' : 'hidden'" @click.self="closeNotification()" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm m-auto text-center">
            <h3 class="text-base font-black text-bsu-dark uppercase tracking-wide mb-2" x-text="notificationTitle"></h3>
            <p class="text-sm text-gray-600 mb-6" x-text="notificationMessage"></p>
            <button class="bg-bsu-dark text-white px-6 py-2 rounded text-xs font-bold uppercase tracking-wider" @click="closeNotification()">OK</button>
        </div>
    </div>

    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9999]"
        :class="confirmOpen ? 'flex items-center justify-center' : 'hidden'"
        @click.self="cancelConfirm()"
        x-cloak>

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 w-12 h-12"
                    :class="isDeclining ? 'text-red-600' : 'text-green-600'"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>

                <h3 class="mb-2 text-[13px] font-bold text-bsu-dark uppercase tracking-wide"
                    x-text="confirmTitle"></h3>

                <p class="text-xs text-gray-500 mb-4 leading-relaxed"
                    x-text="confirmMessage"></p>
                <div x-show="isDeclining" x-cloak class="mt-4 text-left">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-2">
                        Reason for declining
                    </label>

                    <textarea
                        x-model="declineReason"
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 resize-none mb-4"
                        placeholder="Enter your reason for declining this review assignment..."></textarea>
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button"
                        class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-100 rounded-lg transition"
                        @click="cancelConfirm()">
                        Cancel
                    </button>

                    <button type="button"
                        class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-white rounded-lg transition shadow-md"
                        :class="isDeclining ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                        @click="runConfirm()"
                        x-text="isDeclining ? 'Confirm Decline' : 'Confirm Accept'">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('protocolEvaluation', () => ({
        // 1. CORE STATE
        searchQuery: '',
        sortOrder: 'newest',
        timeTicker: Date.now(),

        // 2. INJECT DYNAMIC DATA FROM BACKEND
        proposals: @json($research_applications ?? []),

        // 3. DOCUMENTS STATE
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,
        docUrls: {},
        docGroups: {},
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: 'Preview',
        appDetails: { id: '', researcher: '', title: '' },

        docLabels: {
            'letter_request': 'Letter Request for Review.pdf',
            'endorsement_letter': 'Endorsement/Referral Letter.pdf',
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

        // 4. MODAL STATE
        isPreviewModalOpen: false,
        selectedProtocol: null,

        // 5. NOTIFICATION & CONFIRMATION STATE
        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',
        confirmOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmAction: null,
        declineReason: '',
        isDeclining: false,

        init() {
            setInterval(() => {
                this.timeTicker = Date.now();
            }, 1000);
        },

        // --- FETCH & LOAD DOCUMENTS ---
        async openPreviewModal(protocol) {
            this.selectedProtocol = protocol;
            this.isPreviewModalOpen = true;
            document.body.style.overflow = 'hidden';

            this.isLoadingDocs = true;
            this.activeDocKey = null;
            this.activeDocUrl = null;
            this.docGroups = {};
            this.docUrls = {};

            // Tutorial Bypass Logic
            if(protocol.is_mock) {
                this.isLoadingDocs = false;
                return;
            }

            try {
                const fetchUrl = `/documents/api/${protocol.protocol_code}`;
                const response = await fetch(fetchUrl);
                if (response.ok) {
                    const data = await response.json();

                    this.appDetails = {
                        id: data.protocol_code,
                        researcher: data.primary_researcher || data.name_of_researcher || 'N/A',
                        title: data.research_title || 'Application Form Preview'
                    };

                    this.docGroups = data.documents || {};

                    if (data.payment && data.payment.proof_url) {
                        this.docUrls['proof'] = data.payment.proof_url;
                    }

                    const basicTypes = ['letter_request', 'endorsement_letter', 'full_proposal', 'technical_review_approval', 'informed_consent', 'manuscript'];
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const docs = data.documents[type];
                            if (!docs || docs.length === 0) return;

                            let activeDocs = [];
                            let legacyDocs = [];

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

                            activeDocs.forEach(doc => {
                                const isRevised = doc.url && doc.url.includes('resubmit_');
                                let cleanDesc = doc.description ? doc.description.replace('(Resubmitted)', '').trim() : 'View File';

                                const docObj = {
                                    id: `doc-${doc.id}`,
                                    label: title,
                                    desc: cleanDesc,
                                    url: doc.url,
                                    isRevised: isRevised,
                                    rawType: type
                                };

                                if (isBasic) tempDocs.activeBasic.push(docObj);
                                else tempDocs.activeSupp.push(docObj);
                            });

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

                    this.loadedDocs = tempDocs;
                    this.viewDocument('appform');
                } else {
                    console.error("404 Error: The protocol code was not found.");
                }
            } catch (e) {
                console.error('Failed to load documents:', e);
            } finally {
                this.isLoadingDocs = false;
            }
        },

        closePreviewModal() {
            this.isPreviewModalOpen = false;
            document.body.style.overflow = '';
        },

        // --- DOCUMENT VIEWER HELPERS ---
        viewDocument(key, url = null, title = null, isSupp = false) {
            this.activeDocKey = key;
            this.activeDocUrl = url || this.docUrls[key] || null;

            if (key === 'appform') {
                this.activeDocTitle = 'Application Form Preview';
            } else {
                let cleanName = title || key.replace(/_/g, ' ').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                this.activeDocTitle = isSupp ? `Supplementary: ${cleanName}` : `${cleanName} Preview`;
            }
        },

        hasDoc(key) {
            if (!this.docGroups) return false;

            const keyMap = {
                'letter': 'letter_request', 'endorsement': 'endorsement_letter',
                'proposal': 'full_proposal', 'technicalreviewapproval': 'technical_review_approval',
                'cv': 'curriculum_vitae', 'questionnaire': 'questionnaire',
                'datacollection': 'data_collection', 'productbrochure': 'product_brochure',
                'fda': 'philippine_fda', 'manuscript': 'manuscript'
            };

            if (key === 'consent_en') return this.docGroups['informed_consent']?.some(d => (d.description || '').toLowerCase().includes('english')) || false;
            if (key === 'consent_ph') return this.docGroups['informed_consent']?.some(d => (d.description || '').toLowerCase().includes('filipino')) || false;

            const dbKey = keyMap[key] || key;
            return (this.docGroups[dbKey] && this.docGroups[dbKey].length > 0) ? true : false;
        },

        isImageView() {
            if (!this.activeDocUrl) return false;
            return this.activeDocKey === 'proof' || this.activeDocUrl.match(/\.(jpeg|jpg|gif|png|webp)$/i);
        },

        getSuppData(typeSubstring) {
            if (!this.docGroups) return [];
            if (typeSubstring === 'special') return this.docGroups['special_populations'] || [];
            if (typeSubstring === 'other') return this.docGroups['others'] || [];
            return [];
        },

        // --- DATE & LOG HELPERS ---
        formatDate(dateInput) {
            if (!dateInput) return 'N/A';
            const d = new Date(dateInput);
            if (Number.isNaN(d.getTime())) return 'N/A';
            return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
        },

        getTimeAgo(dateInput) {
            if (!dateInput) return '';
            const date = new Date(dateInput);
            const diffMs = Math.max(0, new Date() - date);
            const mins = Math.floor(diffMs / (1000 * 60));
            if (mins < 60) return `${Math.max(1, mins)} min${mins === 1 ? '' : 's'} ago`;
            const hours = Math.floor(mins / 60);
            if (hours < 24) return `${hours} hour${hours === 1 ? '' : 's'} ago`;
            const days = Math.floor(hours / 24);
            return `${days} day${days === 1 ? '' : 's'} ago`;
        },

        // --- NOTIFICATION & CONFIRMATION LOGIC ---
        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
        },
        closeNotification() { this.notificationOpen = false; },

        openConfirm(title, message, onConfirm, isDeclining = false) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmAction = onConfirm;
            this.isDeclining = isDeclining;
            this.declineReason = '';
            this.confirmOpen = true;
        },
        cancelConfirm() {
            this.confirmOpen = false;
            this.confirmAction = null;
            this.isDeclining = false;
            this.declineReason = '';
        },
        runConfirm() {
            if (this.isDeclining && this.declineReason.trim() === '') {
                alert('Please provide a reason for declining.');
                return;
            }
            if (typeof this.confirmAction === 'function') this.confirmAction();
            this.cancelConfirm();
        },

        // --- FILTER & SORT ---
        parseDateMs(dateInput) {
            if (!dateInput) return 0;
            const value = new Date(dateInput).getTime();
            return Number.isNaN(value) ? 0 : value;
        },

        get filteredProposals() {
            let res = this.proposals.filter(p => {
                const search = this.searchQuery.toLowerCase();
                return (p.protocol_code && p.protocol_code.toLowerCase().includes(search)) ||
                       (p.research_title && p.research_title.toLowerCase().includes(search));
            });

            return res.sort((a, b) => {
                const aTime = this.parseDateMs(a.date_accepted || a.date_assigned || a.created_at);
                const bTime = this.parseDateMs(b.date_accepted || b.date_assigned || b.created_at);
                return this.sortOrder === 'newest' ? bTime - aTime : aTime - bTime;
            });
        },

        // --- REVIEWER ACTIONS: ACCEPT & DECLINE ---
        promptAccept() {
            this.openConfirm(
                'Accept Invitation',
                'Are you sure you want to accept the assignment to review this protocol?',
                () => this.submitResponse('accept')
            );
        },

        promptDecline() {
            this.openConfirm(
                'Decline Invitation',
                'Please provide a brief reason for declining this review assignment.',
                () => this.submitResponse('decline'),
                true
            );
        },

        async submitResponse(actionType) {
            const payload = { action: actionType };
            if (actionType === 'decline') payload.decline_reason = String(this.declineReason);

            try {
                const url = `/reviewer/protocol/${this.selectedProtocol.protocol_code}/respond`;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    this.showNotification('Success', `You have successfully ${actionType}ed the invitation.`);
                    this.closePreviewModal();
                    this.proposals = this.proposals.filter(p => p.protocol_code !== this.selectedProtocol.protocol_code);
                } else {
                    let errorMsg = result.message || "Could not save your response.";
                    if (result.errors && result.errors.decline_reason) errorMsg = result.errors.decline_reason[0];
                    alert("Error: " + errorMsg);
                }
            } catch (error) {
                alert("Network Error: Could not reach the server.");
            }
        }
    }));
});

    document.addEventListener('alpine:initialized', () => {

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

        function runReviewerInvitationsTutorial(manual = false) {
            const isFirstLogin = @json(auth()->user()->is_first_login);
            const userId = @json(auth()->id());
            const storageKey = 'berc_tutorial_step_' + userId;

            if (manual) {
                localStorage.removeItem(storageKey);
                localStorage.setItem(storageKey, 'rev_invitations');
            }

            if (!manual && !isFirstLogin) {
                localStorage.removeItem(storageKey);
                return;
            }

            const tourState = localStorage.getItem(storageKey);

            if (tourState === 'rev_invitations_manual_skip') {
                localStorage.removeItem(storageKey);
                return;
            }

            if (!manual && tourState !== 'rev_invitations') {
                return;
            }

            const driver = window.driver.js.driver;
            const alpineComponent = Alpine.$data(document.querySelector('[x-data="protocolEvaluation"]'));

            const tour = driver({
                showProgress: true,
                allowClose: manual ? true : false,
                overlayColor: 'rgba(33, 60, 113, 0.75)',
                nextBtnText: 'Next →',
                prevBtnText: '← Back',

                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        alpineComponent.closePreviewModal();

                        if (manual) {
                            localStorage.setItem(storageKey, 'rev_assessment_manual_skip');
                        } else {
                            localStorage.setItem(storageKey, 'rev_assessment');
                        }

                        tour.destroy();
                        window.location.href = "{{ route('reviewer.assessment') ?? '/reviewer/assessment' }}";
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-invitations-list',
                        popover: {
                            title: 'Your Invitations',
                            description: 'When the Secretariat assigns you to a protocol, it will appear here. Click on any row in the list to open it and review the details before making a decision.',
                            side: "top",
                            align: 'start',
                            onNextClick: () => {
                                alpineComponent.appDetails = {
                                    id: '2026-MOCK-001',
                                    title: 'Effects of AI on System Architecture (Tutorial)',
                                    researcher: 'Dr. Jane Doe'
                                };

                                alpineComponent.loadedDocs = {
                                    activeBasic: [
                                        {
                                            id: 'doc-mock-1',
                                            label: 'Study Protocol',
                                            desc: 'Review requested',
                                            isRevised: false
                                        }
                                    ],
                                    activeSupp: [],
                                    legacy: []
                                };

                                alpineComponent.activeDocKey = 'appform';
                                alpineComponent.activeDocTitle = 'Application Form Preview';
                                alpineComponent.isLoadingDocs = false;

                                alpineComponent.openPreviewModal({
                                    is_mock: true,
                                    protocol_code: 'MOCK-01'
                                });

                                tour.moveNext();
                            }
                        }
                    },
                    {
                        element: '#tour-protocol-info',
                        popover: {
                            title: '1. Protocol Details',
                            description: 'Here you can view the basic study information and select any of the uploaded documents, like the Study Protocol, to read them.',
                            side: "right",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-preview-panel',
                        popover: {
                            title: '2. Document Viewer',
                            description: 'The selected document will display right here in the browser so you can evaluate the scope of the study.',
                            side: "left",
                            align: 'center'
                        }
                    },
                    {
                        element: '#tour-modal-footer',
                        popover: {
                            title: '3. Accept or Decline',
                            description: 'Once you evaluate the request, you must click either Accept to begin your formal review, or Decline if you have a conflict of interest or lack bandwidth.',
                            side: "top",
                            align: 'center',
                            onNextClick: () => {
                                alpineComponent.closePreviewModal();
                                tour.moveNext();
                            }
                        }
                    },
                    {
                        popover: {
                            title: 'Next Stop: Protocol Assessment',
                            description: 'If you click Accept, the protocol moves to your Assessment page where you conduct your actual review. Let’s go there next.',
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
            loadDriverThenRun(() => runReviewerInvitationsTutorial(true));
        };

        loadDriverThenRun(() => runReviewerInvitationsTutorial(false));
    });
</script>
@endsection
