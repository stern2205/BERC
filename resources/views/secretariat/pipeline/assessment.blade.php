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

    /* Awaiting-style Grid matched with Protocol Evaluation */
    .assessment-grid-header,
    .assessment-row {
        display: grid;
        grid-template-columns: minmax(120px, 0.95fr) minmax(250px, 2.2fr) minmax(210px, 1.6fr) minmax(280px, 2.2fr) minmax(140px, 1fr);
        padding: 8px 20px;
        align-items: center;
        gap: 12px;
    }

    .assessment-grid-header {
        background: #f3f4f6;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }
    .assessment-grid-header > div { text-align: left; }
    .assessment-grid-header > div:last-child { text-align: center; }

    .assessment-row {
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background .15s;
    }
    .assessment-row > div {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        text-align: left;
    }
    .assessment-row > div:last-child {
        align-items: center;
        text-align: center;
    }
    .assessment-row:last-child { border-bottom: none; }
    .assessment-row:hover { background: #f9fafb; }

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
    .protocol-date-ref { font-size:11px; font-weight:700; color:#2563eb; font-family:monospace; }
    .workflow-action-link { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; transition:color 0.15s; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }

    /* Modal Styling */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; width:100%; max-width:900px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); animation: lbIn .2s ease; position:relative; transition:max-width .28s ease; }
    .modal-box.expanded { max-width:1240px; }

    @keyframes lbIn { from { opacity:0; transform: scale(.97); } to { opacity:1; transform: scale(1); } }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .modal-header h2 { font-size:14px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
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
    .doc-card.pending-doc { border-color:#ef4444; background:#fff1f2; }
    .doc-card.pending-doc .doc-chevron { color:#ef4444; }
    .doc-card.pending-doc.active { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); background:#ffe4e6; }
    .doc-chevron { width:14px; height:14px; color:#9ca3af; transition:transform .3s ease; flex-shrink:0; }
    .doc-card.active .doc-chevron { transform:rotate(90deg); color:var(--bsu-dark); }

    /* Right Panel */
    .form-preview-panel { flex:1 1 0; width:auto; padding:24px; overflow-y:auto; background:#fff; position:relative; transition:padding .28s ease; }
    .form-preview-panel.expanded { padding:24px 28px; }

    /* Document/Mock Form Styling */
    .application-form-mock { border:1px solid #d1d5db; border-radius:8px; padding:24px; font-size:11px; color:#374151; background:#fff; max-width:850px; margin:0 auto; box-shadow:0 4px 10px rgba(0,0,0,.04); }
    .form-header { text-align:center; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid var(--bsu-dark); }
    .form-header h3 { font-size:13px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }

    /* Checkbox & Input Styles for the Table */
    .cb-readonly { width: 15px; height: 15px; accent-color: var(--bsu-dark); opacity: 1 !important; cursor: default; }
    .cb-approve { width: 18px; height: 18px; accent-color: var(--brand-red); cursor: pointer; }

    .table-input { width: 100%; border: 1px dashed #d1d5db; border-radius: 4px; padding: 6px; font-size: 11px; color: #374151; background: #fafafa; transition: all 0.2s; min-height: 52px; line-height: 1.35; overflow-y: hidden; }
    .table-input:focus { border-color: var(--bsu-dark); background: #fff; outline: none; box-shadow: 0 0 0 2px rgba(33,60,113,.1); }

    /* Footer Buttons */
    .modal-footer { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-top:1px solid #e5e7eb; background:#fafafa; z-index: 10; position: relative;}
    .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
    .btn:active { transform:scale(.97); }
    .btn-primary { background:var(--bsu-dark); color:#fff; }
    .btn-primary:hover:not(:disabled) { opacity:.88; }
    .btn-primary:disabled { background:#9ca3af; cursor:not-allowed; }
    .btn-outline { background:transparent; color:var(--bsu-dark); border:1.5px solid var(--bsu-dark); }
    .btn-outline:hover { background:#f0f4ff; }

    @media (max-width: 900px) {
        .assessment-grid-header { display: none; }
        .assessment-row { grid-template-columns: 1fr; gap: 8px; padding-bottom: 16px; }
        .protocol-info-panel { width:100%; min-width:unset; border-right:none; border-bottom:1px solid #e5e7eb; height:250px; }
        .modal-content { flex-direction:column; }
    }

    /* --- Synthesis Textarea Specific Styles --- */
    .synthesis-textarea {
        min-height: 80px !important;
        line-height: 1.5;
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .action-required-active {
        border-color: #ef4444 !important;
        background-color: #fffafb !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.05);
    }

    .action-label-container {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 4px 8px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        width: fit-content;
    }
</style>

<style>
    .form-preview-panel { flex: 1 1 0; width: auto; padding: 24px; overflow-y: auto; background: #fff; position: relative; display: flex; flex-direction: column; }
    .form-preview-panel.viewer-active { padding: 0 !important; }
    .protocol-info-panel { width: 280px; min-width: 280px; border-right: 1px solid #e5e7eb; padding: 20px; background: #fafafa; overflow-y: auto; flex-shrink: 0; }
</style>

<div id="assessment-root" x-data="assessmentData(@js($protocolsData ?? []))" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Assessment Validation</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Validate reviewer comments</p>
        </div>
        <div class="w-full max-w-xl flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select x-model="sortOrder" class="w-44 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-bsu-dark focus:outline-none focus:ring-1 focus:ring-bsu-dark/20">
                <option value="newest">Newest -> Oldest</option>
                <option value="oldest">Oldest -> Newest</option>
            </select>
        </div>
    </div>

    <div id="tour-assessment-list" class="app-card relative">
        <div class="card-header">
            <div class="card-tab active">
                Validate Assessment
                <span x-show="forValidation.length > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="forValidation.length" x-cloak></span>
            </div>
        </div>

        <div>
            <div class="assessment-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Reviewer Forms</div>
                <div>Action</div>
            </div>

            <template x-for="(protocol, index) in filteredData" :key="protocol.id">
                <div :id="index === 0 ? 'tour-first-assessment-row' : null" class="assessment-row" @click="openValidate(protocol)">
                    <div><span class="app-id-badge" x-text="protocol.id"></span></div>
                    <div>
                        <div class="app-row-title whitespace-normal break-words leading-snug" x-text="protocol.title"></div>
                        <div class="app-row-sub whitespace-normal break-words mt-1" x-text="protocol.proponent"></div>
                    </div>
                    <div class="h-full flex flex-col justify-center gap-1.5">
                        <div>
                            <span class="font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border" :class="getClassificationTagClass(protocol.classification)" x-text="protocol.classification"></span>
                        </div>
                        <div class="mt-1">
                            <div class="text-[10px] font-bold text-gray-600" x-text="'Classified: ' + protocol.dateSubmitted"></div>
                            <div class="text-[10px] text-gray-500 font-bold mt-0.5" x-text="formatElapsed(protocol.dateSubmitted)"></div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="space-y-1">
                            <template x-for="r in protocol.reviewers">
                                <div class="flex justify-between items-start gap-3 border border-gray-100 rounded-md bg-gray-50 px-2 py-1.5">
                                    <div class="min-w-0 text-left pr-2 flex-1">
                                        <div class="text-[10px] font-bold text-gray-700" x-text="r.name"></div>
                                        <div class="text-[10px] text-gray-500 font-semibold mt-0.5"
                                             x-text="(r.formSubmitted ? 'Date Submitted: ' : 'Date Assigned: ') + (r.formSubmitted ? getReviewerSubmittedDate(protocol, r) : getReviewerAssignedDate(protocol, r))"></div>
                                        <div class="text-[10px] text-gray-400 font-semibold"
                                             x-text="formatElapsed(r.formSubmitted ? getReviewerSubmittedDate(protocol, r) : getReviewerAssignedDate(protocol, r))"></div>
                                    </div>
                                    <div class="flex flex-col items-center gap-1.5 shrink-0 ml-auto pl-5">
                                        <span class="text-[10px] font-black tracking-wide px-2 py-0.5 rounded border min-w-[110px] text-center"
                                            :class="getReviewerStatusClass(r)"
                                            x-text="getReviewerStatusLabel(r)"></span>
                                        <div x-show="!r.formSubmitted" class="text-[10px] text-orange-600 font-bold text-center w-full" x-text="getReviewerDeadlineText(protocol, r)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <span @click.stop="openValidate(protocol)" class="workflow-action-link"><u>Validate</u></span>
                    </div>
                </div>
            </template>

            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No protocols pending validation.
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="selectedProtocol ? 'open' : ''">
        <div class="modal-box" :class="isReviewerFormActive ? 'expanded' : ''" @click.stop>

            <div class="modal-header">
                <h2>Assessment Validation</h2>
                <button class="close-btn" @click="selectedProtocol = null; activeDocument = null; closeRemindModal()">&times;</button>
            </div>

            <div class="modal-content flex flex-row w-full h-full">

                <div id="tour-modal-sidebar" class="protocol-info-panel">
                    <div class="info-group">
                        <div class="info-label">Application ID</div>
                        <div class="info-value"><span class="app-id-badge" x-text="selectedProtocol?.id"></span></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Research Title</div>
                        <div class="info-value leading-snug" x-text="selectedProtocol?.title"></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Principal Investigator</div>
                        <div class="info-value" x-text="selectedProtocol?.proponent"></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Classification</div>
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-[#1f377d] text-white" x-text="selectedProtocol?.classification"></span>
                    </div>

                    <div style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
                        <div class="info-label" style="margin-bottom:8px;">Assigned Reviewer Forms</div>

                        <template x-for="rev in validationDocs" :key="rev.key">
                            <div>
                                <div class="doc-card" :class="getValidationDocCardClass(rev.key)" @click="toggleDocument(rev.key)">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📋</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" x-text="rev.label"></div>
                                        <div style="font-size:9px; font-weight:700; color:#6b7280; margin-top:1px;" x-text="activeDocument === rev.key ? 'Viewing Form' : 'Click to validate'"></div>
                                    </div>
                                    <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>

                                <div x-show="selectedProtocol && activeDocument === rev.key" style="margin-top:10px; padding-top:4px;">
                                    <div class="info-label" style="margin-bottom:8px;">Reviewer Tracker</div>
                                    <div x-show="getReviewersForActiveForm(selectedProtocol).length === 0" class="text-[10px] text-gray-500 italic bg-white border border-gray-200 rounded-md px-2 py-2">
                                        No reviewers assigned to this form.
                                    </div>
                                    <div class="space-y-1.5">
                                        <template x-for="r in getReviewersForActiveForm(selectedProtocol)" :key="'tracker_' + rev.key + '_' + r.id">
                                            <div class="border border-gray-200 rounded-md bg-white px-2 py-2">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="text-[10px] font-bold text-gray-700" x-text="r.name"></div>
                                                    <span class="text-[9px] font-black uppercase tracking-wide px-2 py-0.5 rounded border shrink-0"
                                                        :class="getReviewerStatusClass(r)"
                                                        x-text="getReviewerStatusLabel(r)"></span>
                                                </div>
                                                <template x-if="r.formSubmitted">
                                                    <div class="text-[10px] text-gray-500 font-semibold mt-1">
                                                        Date Submitted: <span x-text="getReviewerSubmittedDate(selectedProtocol, r)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!r.formSubmitted">
                                                    <div class="mt-1">
                                                        <div class="text-[10px] font-bold text-orange-700" x-text="getReviewerDeadlineText(selectedProtocol, r)"></div>
                                                        <div class="text-[10px] text-gray-500 font-semibold mt-0.5" x-text="'Reminded: ' + (r.noticesSent || 0) + '/3'"></div>
                                                        <button type="button"
                                                                class="mt-1.5 text-[10px] font-black uppercase tracking-wide px-2.5 py-1 rounded border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                                :disabled="(r.noticesSent || 0) >= 3"
                                                                @click="openRemindModal(r)">
                                                            Remind
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Protocol Documents</h3>

                        <div x-show="isLoadingDocs" class="text-[10px] text-gray-400 italic py-2">Loading documents...</div>

                        <div x-show="!isLoadingDocs" class="space-y-4">
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Basic Requirements</div>
                                <template x-for="doc in loadedDocs.activeBasic" :key="doc.id">
                                    <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label)">
                                        <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                        <div style="flex:1; min-width:0;">
                                            <div style="font-size:11px; font-weight:700; color:#111827; line-height:1.2; display:flex; flex-wrap:wrap; align-items:center; gap:4px;">
                                                <span x-text="doc.label"></span>
                                                <template x-if="doc.isRevised">
                                                    <span style="background:#fef9c3; color:#854d0e; padding:1px 4px; border-radius:4px; font-size:7px; border:1px solid #fde047; font-weight: 900;">REVISED</span>
                                                </template>
                                            </div>
                                            <div style="font-size:9px; font-weight:700; color:#3b82f6; margin-top:2px; word-break:break-all;" x-text="doc.desc"></div>
                                        </div>
                                        <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </template>
                            </div>

                            <template x-if="loadedDocs.activeSupp.length > 0">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Supplementary Docs</div>
                                    <template x-for="doc in loadedDocs.activeSupp" :key="doc.id">
                                        <div class="doc-card" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label)">
                                            <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📄</span>
                                            <div style="flex:1; min-width:0;">
                                                <div style="font-size:11px; font-weight:700; color:#111827;" x-text="doc.label"></div>
                                                <div style="font-size:9px; font-weight:700; color:#3b82f6; margin-top:2px; word-break:break-all;" x-text="doc.desc"></div>
                                            </div>
                                            <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="loadedDocs.legacy.length > 0">
                                <div style="padding-top:15px; border-top: 2px dashed #e5e7eb;">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Version History (Archived)</div>
                                    <template x-for="doc in loadedDocs.legacy" :key="doc.id">
                                        <div class="doc-card" style="opacity:0.6; border-style:dashed;" :class="activeDocKey === doc.id ? 'active' : ''" @click="viewDocument(doc.id, doc.url, doc.label + ' (Archived)')">
                                            <span style="color:#94a3b8; font-size:16px; flex-shrink:0; margin-right:4px;">🗄️</span>
                                            <div style="flex:1; min-width:0;">
                                                <div style="font-size:10px; font-weight:800; color:#64748b;" x-text="doc.label"></div>
                                                <div style="font-size:8px; bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-black w-fit mt-1">LEGACY</div>
                                                <div style="font-size:8px; font-weight:700; color:#94a3b8; word-break:break-all; mt-1" x-text="doc.desc"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="tour-modal-synthesis" class="form-preview-panel" :class="activeDocument === 'doc_viewer' ? 'viewer-active' : ''">

                    <div x-show="!activeDocument" class="h-full flex items-center justify-center flex-col text-center opacity-50">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-bold text-gray-600 px-10">Select a reviewer form or protocol document from the left panel to begin validation.</p>
                    </div>

                    <div x-show="activeDocument === 'assessment_form'" x-cloak class="application-form-mock relative">
                        <div class="form-header">
                            <h3>Study Protocol Assessment</h3>
                            <p style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:.05em;">Assessment Form Template</p>
                        </div>
                        <div x-show="selectedProtocol">
                            <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm bg-white">
                                <table class="w-full text-left border-collapse table-fixed">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:25%;">Assessment Points</th>
                                            <th class="px-2 py-2.5 text-[9px] font-black uppercase text-[#1f377d] text-center" style="width:5%;">Yes</th>
                                            <th class="px-2 py-2.5 text-[9px] font-black uppercase text-[#1f377d] text-center" style="width:5%;">No</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:10%;">Line & Page</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:27.5%;">Reviewers' Comments</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:27.5%;">Secretariat Synthesis</th>
                                        </tr>
                                    </thead>
                                    <template x-for="(row, index) in selectedProtocol?.assessmentRows" :key="row.id">
                                        <tbody class="border-b border-gray-100 last:border-b-0">
                                            <template x-if="index === 0 || row.section !== selectedProtocol.assessmentRows[index - 1].section">
                                                <tr class="bg-[#1f377d]/5 border-b border-[#1f377d]/20">
                                                    <td colspan="6" class="px-3 py-2 text-[11px] font-black uppercase text-[#1f377d] tracking-wider" x-text="row.section"></td>
                                                </tr>
                                            </template>

                                            <tr class="hover:bg-gray-50/30 transition-colors">
                                                <td class="px-3 py-3 align-top">
                                                    <div class="text-[10px] font-bold text-gray-800 leading-snug">
                                                        <span x-text="row.points"></span> - <span x-text="row.label" class="font-semibold text-gray-600"></span>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-3 text-center align-top"><input type="checkbox" :checked="row.yes" onclick="return false;" class="cb-readonly"></td>
                                                <td class="px-2 py-3 text-center align-top"><input type="checkbox" :checked="row.no" onclick="return false;" class="cb-readonly"></td>
                                                <td class="px-3 py-3 align-top"><span class="text-[10px] font-semibold text-gray-600 whitespace-nowrap" x-text="row.linePage"></span></td>
                                                <td class="px-3 py-3 align-top bg-gray-50/50">
                                                    <div class="text-[10px]" x-html="row.conjoinedComments"></div>
                                                </td>
                                                <td class="px-3 py-3 align-top">
                                                    <textarea x-model="row.synthesizedComments" @input="triggerAutosave(); autoGrow($event.target)" x-init="$nextTick(() => autoGrow($el))" rows="3" class="w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark text-[10px] resize-none overflow-hidden" placeholder="Type synthesized final comments here..."></textarea>
                                                    <div class="mt-2 flex items-center gap-1.5">
                                                        <input type="checkbox" x-model="row.synthesizedCommentsActionRequired" @change="triggerAutosave()" class="w-3.5 h-3.5 rounded border-gray-300 text-red-600 focus:ring-red-600 cursor-pointer">
                                                        <label class="text-[9px] font-black text-red-600 uppercase tracking-wider cursor-pointer select-none" @click="row.synthesizedCommentsActionRequired = !row.synthesizedCommentsActionRequired; triggerAutosave();">
                                                            Action Required
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeDocument === 'informed_consent'" x-cloak class="application-form-mock mt-6">
                        <div class="form-header">
                            <h3>Informed Consent Review</h3>
                            <p style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:.05em;">Guide questions for informed consent process and form</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm bg-white">
                            <table class="w-full text-left border-collapse table-fixed">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:25%;">Guide Question</th>
                                        <th class="px-2 py-2.5 text-[9px] font-black uppercase text-[#1f377d] text-center" style="width:5%;">Yes</th>
                                        <th class="px-2 py-2.5 text-[9px] font-black uppercase text-[#1f377d] text-center" style="width:5%;">No</th>
                                        <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:10%;">Line & Page</th>
                                        <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:27.5%;">Reviewers' Comments</th>
                                        <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:27.5%;">Secretariat Synthesis</th>
                                    </tr>
                                </thead>
                                <template x-for="(row, index) in selectedProtocol?.consentRows" :key="'iconsent_' + row.id">
                                    <tbody class="border-b border-gray-100 last:border-b-0">
                                        <template x-if="index === 0 || row.section !== selectedProtocol.consentRows[index - 1].section">
                                            <tr class="bg-[#1f377d]/5 border-b border-[#1f377d]/20">
                                                <td colspan="6" class="px-3 py-2 text-[11px] font-black uppercase text-[#1f377d] tracking-wider" x-text="row.section"></td>
                                            </tr>
                                        </template>

                                        <tr class="hover:bg-gray-50/30 transition-colors">
                                            <td class="px-3 py-3 align-top">
                                                <div class="text-[10px] font-bold text-gray-800 leading-snug">
                                                    <span x-text="row.points"></span> - <span x-text="row.label" class="font-semibold text-gray-600"></span>
                                                </div>
                                            </td>
                                            <td class="px-2 py-3 text-center align-top"><input type="checkbox" :checked="row.yes" onclick="return false;" class="cb-readonly"></td>
                                            <td class="px-2 py-3 text-center align-top"><input type="checkbox" :checked="row.no" onclick="return false;" class="cb-readonly"></td>
                                            <td class="px-3 py-3 align-top"><span class="text-[10px] font-semibold text-gray-600 whitespace-nowrap" x-text="row.linePage"></span></td>
                                            <td class="px-3 py-3 align-top bg-gray-50/50">
                                                <div class="text-[10px] text-gray-700 font-medium leading-relaxed" x-html="row.conjoinedComments"></div>
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <textarea x-model="row.synthesizedComments" @input="triggerAutosave(); autoGrow($event.target)" x-init="$nextTick(() => { setTimeout(() => autoGrow($el), 100) })" style="min-height: 80px;" class="w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark text-[10px] resize-none overflow-hidden" placeholder="Type synthesized final comments here..."></textarea>
                                                <div class="mt-2 flex items-center gap-1.5">
                                                    <input type="checkbox" x-model="row.synthesizedCommentsActionRequired" @change="triggerAutosave()" class="w-3.5 h-3.5 rounded border-gray-300 text-red-600 focus:ring-red-600 cursor-pointer">
                                                    <label class="text-[9px] font-black text-red-600 uppercase tracking-wider cursor-pointer select-none" @click="row.synthesizedCommentsActionRequired = !row.synthesizedCommentsActionRequired; triggerAutosave();">
                                                        Action Required
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </table>
                        </div>
                    </div>

                    <div x-show="activeDocument === 'doc_viewer'" x-cloak class="h-full flex flex-col bg-[#525659]">
                        <div class="bg-white border-b border-gray-200 px-5 py-3 flex justify-between items-center shrink-0 shadow-sm z-10">
                            <div class="flex flex-col">
                                <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDocTitle"></h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Digital Document Preview</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a :href="activeDocUrl" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                <button @click="activeDocument = null; activeDocKey = null;" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
                                    Close Preview
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 relative w-full h-full">
                            <template x-if="activeDocUrl">
                                <iframe :src="activeDocUrl" class="w-full h-full border-none bg-white"></iframe>
                            </template>
                            <div x-show="!activeDocUrl" class="absolute inset-0 flex flex-col items-center justify-center text-white opacity-60">
                                <svg class="animate-spin h-8 w-8 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-xs font-black tracking-widest uppercase">Loading Document...</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div id="tour-modal-footer" class="modal-footer flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div x-show="!allReviewersConfirmed && selectedProtocol" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        All reviewer forms must be sent to complete validation
                    </div>
                    <span x-text="draftStatusMsg" class="text-[10px] font-bold text-green-600 transition-opacity duration-300"></span>
                </div>

                <button x-show="allReviewersConfirmed" class="btn btn-primary bg-[#c21c2c] hover:bg-[#a01724] shadow-sm flex items-center gap-2" @click="submitSynthesis" :disabled="isLoading">
                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isLoading ? 'Saving...' : 'Complete Validation'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="confirmSubmitOpen" x-cloak class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
                <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Confirm Validation Submission</h3>
                <p class="text-[11px] text-gray-500 font-semibold mt-1">
                    Some synthesis fields are still blank. You may still continue if you confirm.
                </p>
            </div>

            <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                <template x-if="missingInputItems.length > 0">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-red-600 mb-3">
                            Missing inputs found in these items:
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <template x-for="item in missingInputItems" :key="item.type + '-' + item.id">
                                <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200 text-[11px] font-black"
                                    x-text="item.item"></span>
                            </template>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full border-collapse">
                                <thead class="bg-gray-50">
                                    <tr class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                        <th class="px-4 py-3 text-left w-24">Item</th>
                                        <th class="px-4 py-3 text-left">Question</th>
                                        <th class="px-4 py-3 text-left w-40">Section</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in missingInputItems" :key="'row-' + item.type + '-' + item.id">
                                        <tr>
                                            <td class="px-4 py-3 text-[11px] font-black text-bsu-dark" x-text="item.item"></td>
                                            <td class="px-4 py-3 text-[11px] text-gray-700 whitespace-normal break-words" x-text="item.label"></td>
                                            <td class="px-4 py-3 text-[10px] font-bold uppercase text-gray-500" x-text="item.section"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template x-if="missingInputItems.length === 0">
                    <div class="text-sm font-bold text-green-700">
                        No missing synthesis inputs detected.
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-slate-50 flex justify-end gap-3">
                <button @click="closeSubmitConfirmation()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors uppercase border-none bg-transparent cursor-pointer">
                    Go Back
                </button>

                <button @click="confirmAndSubmitSynthesis()"
                        :disabled="isLoading"
                        class="bg-[#D32F2F] text-white px-6 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer disabled:opacity-50">
                    Confirm Submit
                </button>
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('assessmentData', (initialData = []) => ({
        searchQuery: '',
        sortOrder: 'newest',
        forValidation: initialData,

        selectedProtocol: null,

        activeView: 'assessment_form',
        activeDocument: null,
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: '',
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,

        remindModalOpen: false,
        remindTargetReviewerId: null,
        isLoading: false,

        autosaveTimer: null,
        draftStatusMsg: '',
        timeTicker: Date.now(),

        notification: {
            open: false,
            title: '',
            message: '',
            type: 'success'
        },

        confirmSubmitOpen: false,
        missingInputItems: [],

        docLabels: {
            'letter_request': 'Letter Request',
            'endorsement_letter': 'Endorsement Letter',
            'full_proposal': 'Study Protocol',
            'technical_review_approval': 'Technical Review Approval',
            'curriculum_vitae': 'Curriculum Vitae',
            'informed_consent': 'Informed Consent Form',
            'questionnaire': 'Questionnaire',
            'data_collection': 'Data Collection Forms',
            'product_brochure': 'Product Brochure',
            'philippine_fda': 'Philippine FDA Approval',
            'manuscript': 'Manuscript'
        },

        basicTypes: [
            'letter_request', 'endorsement_letter', 'full_proposal',
            'technical_review_approval', 'curriculum_vitae', 'informed_consent',
            'manuscript'
        ],

        init() {
            window.assessmentAlpine = this;
            this.timeTicker = Date.now();
            setInterval(() => {
                this.timeTicker = Date.now();
            }, 60000);
        },

        get validationDocs() {
            let docs = [
                { key: 'assessment_form', label: 'Assessment Form' }
            ];
            if (this.selectedProtocol && this.selectedProtocol.hasInformedConsent) {
                docs.push({ key: 'informed_consent', label: 'Informed Consent' });
            }
            return docs;
        },

        getReviewerStatusLabel(r) {
            if (r.formSubmitted) return 'Form Sent';

            if (r.invitationStatus === 'Pending') return 'Pending Invitation';
            if (r.invitationStatus === 'Accepted') return 'Pending Review';
            if (r.invitationStatus === 'Declined') return 'Declined';
            if (r.invitationStatus === 'Expired') return 'Expired';

            return 'Pending Invitation';
        },

        getReviewerStatusClass(r) {
            if (r.formSubmitted) return 'bg-green-50 text-green-700 border-green-200';

            if (r.invitationStatus === 'Pending') return 'bg-gray-50 text-gray-600 border-gray-200';
            if (r.invitationStatus === 'Accepted') return 'bg-orange-50 text-orange-700 border-orange-200';
            if (r.invitationStatus === 'Declined') return 'bg-red-50 text-red-700 border-red-200';
            if (r.invitationStatus === 'Expired') return 'bg-yellow-50 text-yellow-700 border-yellow-200';

            return 'bg-gray-50 text-gray-600 border-gray-200';
        },

        get filteredData() {
            return this.forValidation.filter(p => {
                const search = this.searchQuery.toLowerCase();
                const title = p.title ? p.title.toLowerCase() : '';
                const proponent = p.proponent ? p.proponent.toLowerCase() : '';
                const id = p.id ? p.id.toLowerCase() : '';

                return id.includes(search) || title.includes(search) || proponent.includes(search);
            }).sort((a, b) => {
                const aTime = new Date(a.dateSubmitted || 0).getTime();
                const bTime = new Date(b.dateSubmitted || 0).getTime();
                return this.sortOrder === 'newest' ? bTime - aTime : aTime - bTime;
            });
        },

        get allReviewersConfirmed() {
            if (!this.selectedProtocol || !this.selectedProtocol.reviewers) return false;

            const assigned = this.selectedProtocol.reviewers.filter(r => !!r.id);
            if (assigned.length === 0) return false;

            return assigned.every(r =>
                r.invitationStatus === 'Accepted' && r.formSubmitted === true
            );
        },

        get isReviewerFormActive() {
            return this.validationDocs.some(d => d.key === this.activeDocument) ||
                   this.activeView === 'assessment_form' ||
                   this.activeView === 'informed_consent';
        },

        get remindTargetReviewer() {
            if (!this.selectedProtocol || !this.remindTargetReviewerId) return null;
            return (this.selectedProtocol.reviewers || []).find(r => r.id === this.remindTargetReviewerId) || null;
        },

        async openValidate(protocol) {
            if (protocol.is_mock) {
                this.selectedProtocol = protocol;
                this.activeDocument = 'assessment_form';
                this.activeView = 'assessment_form';
                this.activeDocKey = null;
                this.activeDocUrl = null;
                this.activeDocTitle = '';
                this.draftStatusMsg = '';
                this.loadedDocs = {
                    activeBasic: [
                        { id: 'mock_doc_1', label: 'Study Protocol', url: '#', isRevised: true, desc: 'FULL PROPOSAL REVISED' },
                        { id: 'mock_doc_2', label: 'Informed Consent Form', url: '#', isRevised: false, desc: 'INFORMED CONSENT FORM' }
                    ],
                    activeSupp: [
                        { id: 'mock_doc_3', label: 'Questionnaire', url: '#', isRevised: false, desc: 'SURVEY QUESTIONNAIRE' }
                    ],
                    legacy: [
                        { id: 'mock_doc_4', label: 'Study Protocol', url: '#', isRevised: false, desc: 'FULL PROPOSAL OLD VERSION' }
                    ]
                };
                return;
            }

            this.selectedProtocol = protocol;
            this.activeDocument = 'assessment_form';
            this.activeView = 'assessment_form';
            this.activeDocKey = null;
            this.activeDocUrl = null;
            this.activeDocTitle = '';
            this.draftStatusMsg = '';
            this.closeRemindModal();
            this.ensureAssessmentRows(protocol);
            this.ensureConsentRows(protocol);

            try {
                const draftRes = await fetch(`/api/secretariat/synthesis/${protocol.id}/draft`);
                if (draftRes.ok) {
                    const draftData = await draftRes.json();

                    if (draftData && draftData.assessment_items) {
                        this.selectedProtocol.assessmentRows.forEach(r => {
                            const dRow = draftData.assessment_items.find(dr => dr.id === r.id);
                            if (dRow) {
                                r.synthesizedComments = dRow.synthesized_comments ?? '';
                                r.synthesizedCommentsActionRequired = !!dRow.action_required;
                            }
                        });
                    }

                    if (draftData && draftData.icf_items) {
                        this.selectedProtocol.consentRows.forEach(r => {
                            const dRow = draftData.icf_items.find(dr => dr.id === r.id);
                            if (dRow) {
                                r.synthesizedComments = dRow.synthesized_comments ?? '';
                                r.synthesizedCommentsActionRequired = !!dRow.action_required;
                            }
                        });
                    }

                    this.draftStatusMsg = 'Restored from saved draft.';
                    setTimeout(() => this.draftStatusMsg = '', 4000);
                }
            } catch (e) {
                console.warn('No draft found or could not load draft.', e);
            }

            this.fetchDocuments(protocol.id);
        },

        ensureAssessmentRows(protocol) {
            if (!protocol) return;
            if (!Array.isArray(protocol.assessmentRows)) {
                protocol.assessmentRows = [];
            } else {
                protocol.assessmentRows.forEach(row => {
                    if (typeof row.synthesizedComments === 'undefined') row.synthesizedComments = '';
                    if (typeof row.synthesizedCommentsActionRequired === 'undefined') row.synthesizedCommentsActionRequired = false;
                    if (typeof row.yes === 'undefined') row.yes = false;
                    if (typeof row.no === 'undefined') row.no = false;
                    if (typeof row.linePage === 'undefined') row.linePage = 'N/A';
                });
            }
            if (typeof protocol.fullBoardComments !== 'string') {
                protocol.fullBoardComments = '';
            }
        },

        ensureConsentRows(protocol) {
            if (!protocol) return;
            if (!Array.isArray(protocol.consentRows)) {
                protocol.consentRows = [];
            } else {
                protocol.consentRows.forEach(row => {
                    if (typeof row.synthesizedComments === 'undefined') row.synthesizedComments = '';
                    if (typeof row.synthesizedCommentsActionRequired === 'undefined') row.synthesizedCommentsActionRequired = false;
                    if (typeof row.yes === 'undefined') row.yes = false;
                    if (typeof row.no === 'undefined') row.no = false;
                    if (typeof row.linePage === 'undefined') row.linePage = 'N/A';
                });
            }
        },

        async fetchDocuments(protocolId) {
            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            const parseFileName = (path) => {
                if (!path) return 'Document';
                let name = path.split('/').pop();
                name = name.replace(/_\d{10}_\d+\.\w+$/, '');
                name = name.replace(`_${protocolId}`, '');
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            try {
                const response = await fetch(`/documents/api/${protocolId}`);
                if (response.ok) {
                    const data = await response.json();
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const docs = data.documents[type];
                            if (!docs || !Array.isArray(docs) || docs.length === 0) return;

                            let maxTs = 0;
                            docs.forEach(d => {
                                const path = d.url || '';
                                const match = path.match(/_(\d{10})_/);
                                const ts = match ? parseInt(match[1]) : 0;
                                if (ts > maxTs) maxTs = ts;
                            });

                            const title = this.docLabels[type] || type.replace(/_/g, ' ').toUpperCase();
                            const isBasic = this.basicTypes.includes(type);

                            docs.forEach(doc => {
                                const path = doc.url || '';
                                const match = path.match(/_(\d{10})_/);
                                const ts = match ? parseInt(match[1]) : 0;

                                const hasRealDesc = doc.description && doc.description !== 'View File';
                                const displayName = hasRealDesc ? doc.description : parseFileName(path);
                                const isRevised = path.includes('resubmit_');

                                const obj = {
                                    id: doc.id,
                                    label: title,
                                    url: doc.url,
                                    isRevised: isRevised,
                                    desc: displayName
                                };

                                if (ts === maxTs || maxTs === 0) {
                                    if (isBasic) tempDocs.activeBasic.push(obj);
                                    else tempDocs.activeSupp.push(obj);
                                } else {
                                    obj.desc = displayName;
                                    obj.isRevised = false;
                                    tempDocs.legacy.push(obj);
                                }
                            });
                        });
                    }

                    this.loadedDocs = tempDocs;
                }
            } catch (e) {
                console.error('Doc Load Error:', e);
            } finally {
                this.isLoadingDocs = false;
            }
        },

        viewDocument(id, url, label) {
            this.activeDocument = 'doc_viewer';
            this.activeView = 'doc_viewer';
            this.activeDocKey = id;
            this.activeDocUrl = url;
            this.activeDocTitle = label;
        },

        toggleDocument(key) {
            this.activeDocument = this.activeDocument === key ? null : key;
            this.activeView = this.activeDocument;
            this.activeDocKey = null;
        },

        getValidationDocCardClass(docKey) {
            const classes = [];
            if (this.activeDocument === docKey) classes.push('active');
            if (this.hasPendingReviewer(this.selectedProtocol)) classes.push('pending-doc');
            return classes.join(' ');
        },

        getClassificationTagClass(classification) {
            if (classification === 'Exempted') return 'text-blue-700 border-blue-200 bg-blue-50';
            if (classification === 'Expedited' || classification === 'Full Board') return 'text-red-700 border-red-200 bg-red-50';
            return 'text-bsu-dark border-bsu-dark/20 bg-gray-50';
        },

        getReviewersForActiveForm(protocol) {
            if (this.activeView === 'assessment_form' || this.activeView === 'informed_consent') {
                return protocol?.reviewers || [];
            }
            return [];
        },

        hasPendingReviewer(protocol) {
            return (protocol?.reviewers || []).some(r => {
                if (!r?.id) return false;
                return !(r.invitationStatus === 'Accepted' && r.formSubmitted === true);
            });
        },

        formatDateOnly(dateInput) {
            if (!dateInput) return 'N/A';
            if (typeof dateInput === 'string' && dateInput.includes('T')) return dateInput.split('T')[0];
            const parsed = new Date(dateInput);
            if (!Number.isNaN(parsed.getTime())) return parsed.toISOString().slice(0, 10);
            return dateInput;
        },

        formatElapsed(dateInput) {
            if (!dateInput) return 'Unknown';
            const time = new Date(dateInput).getTime();
            if (isNaN(time)) return 'Unknown';
            const now = this.timeTicker || Date.now();
            const diff = now - time;
            const mins = Math.floor(diff / 60000);
            const hrs = Math.floor(mins / 60);
            const days = Math.floor(hrs / 24);

            if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
            if (hrs > 0) return `${hrs} hour${hrs > 1 ? 's' : ''} ago`;
            if (mins > 0) return `${mins} minute${mins > 1 ? 's' : ''} ago`;
            return 'Just now';
        },

        getReviewerAssignedDate(protocol, reviewer) {
            return this.formatDateOnly(reviewer?.dateAssigned || protocol?.dateSubmitted || 'N/A');
        },

        getReviewerSubmittedDate(protocol, reviewer) {
            return this.formatDateOnly(reviewer?.dateSubmitted || reviewer?.dateAssigned || protocol?.dateSubmitted || 'N/A');
        },

        getDaysAllowed(classification) {
            const classf = classification || 'Full Board';
            if (classf === 'Expedited') return 10;
            if (classf === 'Full Board') return 20;
            return 0;
        },

        getReviewDeadlineMs(protocol, reviewer) {
            const source = reviewer?.pivot?.created_at
                        || reviewer?.dateAssigned
                        || protocol?.created_at
                        || protocol?.dateSubmitted;

            if (!source || source === 'N/A') return 0;

            const assignedMs = new Date(source).getTime();
            if (isNaN(assignedMs) || assignedMs === 0) return 0;

            const daysAllowed = this.getDaysAllowed(protocol?.classification || protocol?.review_classification);
            return assignedMs + (daysAllowed * 24 * 60 * 60 * 1000);
        },

        isReviewOverdue(protocol, reviewer) {
            if (reviewer?.formSubmitted) return false;
            const deadlineMs = this.getReviewDeadlineMs(protocol, reviewer);
            const now = this.timeTicker || Date.now();
            return deadlineMs > 0 && deadlineMs <= now;
        },

        getReviewerDeadlineText(protocol, reviewer) {
            if (reviewer?.formSubmitted) return 'Submitted';

            if (reviewer?.invitationStatus === 'Pending') {
                return 'Awaiting acceptance';
            }

            if (reviewer?.invitationStatus === 'Declined') {
                return 'Invitation declined';
            }

            if (reviewer?.invitationStatus === 'Expired') {
                return 'Invitation expired';
            }

            if (reviewer?.invitationStatus !== 'Accepted') {
                return 'Awaiting reviewer';
            }

            const deadlineMs = this.getReviewDeadlineMs(protocol, reviewer);

            if (!deadlineMs) {
                const days = this.getDaysAllowed(protocol?.classification || protocol?.review_classification);
                return `${days} days left`;
            }

            const now = this.timeTicker || Date.now();
            const remainingMs = deadlineMs - now;

            if (isNaN(remainingMs)) return 'Calculating...';
            if (remainingMs <= 0) return 'OVERDUE';

            const totalHours = Math.floor(remainingMs / (1000 * 60 * 60));
            const days = Math.floor(totalHours / 24);
            const remainingHours = totalHours % 24;

            if (days > 0) {
                return `${days}d ${remainingHours}h left`;
            }

            return `${remainingHours}h left`;
        },

        autoGrow(el) {
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = `${el.scrollHeight}px`;
        },

        openRemindModal(reviewer) {
            if (!reviewer || reviewer.formSubmitted) return;
            this.remindTargetReviewerId = reviewer.id;
            this.remindModalOpen = true;
        },

        closeRemindModal() {
            this.remindModalOpen = false;
            this.remindTargetReviewerId = null;
        },

        confirmRemind() {
            const reviewer = this.remindTargetReviewer;
            if (!reviewer || reviewer.formSubmitted) {
                this.closeRemindModal();
                return;
            }
            if ((reviewer.noticesSent || 0) >= 3) {
                this.closeRemindModal();
                this.showNotification('Reminder Limit Reached', `${reviewer.name} has already received 3 reminders.`, 'error');
                return;
            }
            reviewer.noticesSent = (reviewer.noticesSent || 0) + 1;
            this.closeRemindModal();
            this.showNotification('Reminder Sent', `Reminder sent to ${reviewer.name}. (${reviewer.noticesSent}/3)`, 'success');
        },

        showNotification(title, message, type = 'success') {
            this.notification.title = title;
            this.notification.message = message;
            this.notification.type = type;
            this.notification.open = true;

            setTimeout(() => {
                this.notification.open = false;
            }, 3000);
        },

        triggerAutosave() {
            this.draftStatusMsg = 'Saving...';
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.saveDraft(), 1500);
        },

        async saveDraft() {
            if (!this.selectedProtocol) return;

            const protocolId = this.selectedProtocol.id;
            const payload = {
                protocol_code: protocolId,
                assessment_items: this.selectedProtocol.assessmentRows.map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    action_required: row.synthesizedCommentsActionRequired
                })),
                icf_items: (this.selectedProtocol.consentRows || []).map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    action_required: row.synthesizedCommentsActionRequired
                }))
            };

            try {
                const response = await fetch(`/api/secretariat/synthesis/${protocolId}/draft`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const now = new Date();
                    let hours = now.getHours();
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12 || 12;
                    this.draftStatusMsg = `Draft saved at ${hours}:${minutes} ${ampm}`;
                } else {
                    this.draftStatusMsg = 'Failed to save draft.';
                }
            } catch (e) {
                console.error('Draft Save Error:', e);
                this.draftStatusMsg = 'Offline - Could not save draft.';
            }
        },

        getMissingSynthesisItems() {
            const missing = [];

            const assessmentRows = this.selectedProtocol?.assessmentRows || [];
            const consentRows = this.selectedProtocol?.consentRows || [];

            assessmentRows.forEach(row => {
                const value = (row.synthesizedComments || '').trim();
                if (value === '') {
                    missing.push({
                        id: row.id,
                        item: row.points || row.item_display || row.question_number || 'Assessment Item',
                        label: row.label || '',
                        section: row.section || 'Assessment Form',
                        type: 'assessment'
                    });
                }
            });

            consentRows.forEach(row => {
                const value = (row.synthesizedComments || '').trim();
                if (value === '') {
                    missing.push({
                        id: row.id,
                        item: row.points || row.item_display || row.question_number || 'ICF Item',
                        label: row.label || '',
                        section: row.section || 'Informed Consent',
                        type: 'icf'
                    });
                }
            });

            return missing;
        },

        openSubmitConfirmation() {
            this.missingInputItems = this.getMissingSynthesisItems();
            this.confirmSubmitOpen = true;
        },

        closeSubmitConfirmation() {
            this.confirmSubmitOpen = false;
        },

        async confirmAndSubmitSynthesis() {
            this.confirmSubmitOpen = false;
            await this.finalSubmitSynthesis();
        },

        submitSynthesis() {
            if (!this.selectedProtocol || this.isLoading) return;
            this.openSubmitConfirmation();
        },

        async finalSubmitSynthesis() {
            if (!this.selectedProtocol) return;
            this.isLoading = true;

            const payload = {
                protocol_code: this.selectedProtocol.id,
                full_board_comments: this.selectedProtocol.fullBoardComments,
                assessment_items: this.selectedProtocol.assessmentRows.map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    action_required: row.synthesizedCommentsActionRequired
                })),
                icf_items: (this.selectedProtocol.consentRows || []).map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    action_required: row.synthesizedCommentsActionRequired
                }))
            };

            try {
                const response = await fetch('/api/secretariat/synthesis/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    this.showNotification('Success', 'Synthesized comments saved successfully.', 'success');
                    this.selectedProtocol = null;
                    this.activeDocument = null;
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    const errorData = await response.json();
                    this.showNotification('Error', errorData.message || 'Failed to save synthesis.', 'error');
                }
            } catch (error) {
                console.error('Submission error:', error);
                this.showNotification('Error', 'An unexpected network error occurred.', 'error');
            } finally {
                this.isLoading = false;
            }
        }
    }));
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
            .driver-popover-next-btn { background:#D32F2F!important;color:white!important;border:none!important;text-shadow:none!important; }
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

    function runAssessmentTutorial(manual = false, retries = 0) {
        const isFirstLogin = @json(auth()->check() ? auth()->user()->is_first_login : true);
        const userId = @json(auth()->id() ?? 1);
        const storageKey = 'berc_tutorial_step_' + userId;

        const urlParams = new URLSearchParams(window.location.search);
        const forceTour = urlParams.get('tour') === '1';
        let tourState = localStorage.getItem(storageKey);

        if (tourState === 'secretariat_assessment_manual_skip') {
            localStorage.removeItem(storageKey);
            return;
        }

        if (manual || forceTour) {
            tourState = 'secretariat_assessment';
            localStorage.setItem(storageKey, tourState);
        }

        if (!manual && !forceTour && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!manual && !forceTour && tourState !== 'secretariat_assessment') {
            return;
        }

        if (window.__assessmentTourStarted) return;

        const rootEl = document.getElementById('assessment-root');
        let alpine = null;

        try {
            alpine = window.assessmentAlpine ||
                rootEl?.__x?.$data ||
                rootEl?._x_dataStack?.[0] ||
                (window.Alpine ? window.Alpine.$data(rootEl) : null);
        } catch (e) {}

        if (!rootEl || !alpine || typeof window.driver === 'undefined') {
            if (retries < 40) {
                setTimeout(() => runAssessmentTutorial(manual, retries + 1), 250);
            } else {
                console.error('Tutorial aborted: Could not hook into Alpine or Driver.js.');
            }
            return;
        }

        window.__assessmentTourStarted = true;

        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));
        const driver = window.driver.js.driver;

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {

                    localStorage.setItem(storageKey, 'secretariat_decision');
                    tour.destroy();
                    window.location.href = "{{ route('secretariat.decision') ?? url('/secretariat/decision') }}";

                } else {
                    tour.destroy();
                }
            },

            onDestroyed: () => {
                alpine.selectedProtocol = null;
                alpine.activeDocument = null;
                window.__assessmentTourStarted = false;
            },

            steps: [
                {
                    element: '#tour-assessment-list',
                    onHighlightStarted: async () => {
                        alpine.selectedProtocol = null;
                        alpine.activeDocument = null;
                        await wait(200);
                    },
                    popover: {
                        title: 'Assessment Validation Queue',
                        description: 'This is where protocols appear once reviewer feedback is ready for Secretariat validation.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-assessment-row',
                    onHighlightStarted: async () => {
                        alpine.selectedProtocol = null;
                        alpine.activeDocument = null;
                        await wait(150);
                    },
                    popover: {
                        title: 'Protocol Entry',
                        description: 'Each row shows the protocol details, reviewer progress, and the validation action link.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-modal-sidebar',
                    onHighlightStarted: async () => {
                        await alpine.openValidate({
                            is_mock: true,
                            id: '2026-MOCK-ASMT',
                            title: 'Effects of AI on System Architecture',
                            proponent: 'Dr. Jane Doe',
                            classification: 'Expedited',
                            dateSubmitted: '2026-04-18',
                            hasInformedConsent: true,
                            reviewers: [
                                { id: 1, name: 'Dr. Smith', formSubmitted: true, dateSubmitted: '2026-04-19', noticesSent: 0 },
                                { id: 2, name: 'Dr. Brown', formSubmitted: false, dateAssigned: '2026-04-18', noticesSent: 1 }
                            ],
                            assessmentRows: [
                                {
                                    id: 1,
                                    points: '1.4 Sampling methods',
                                    yes: false,
                                    no: true,
                                    linePage: 'Pg 4',
                                    conjoinedComments: '<b>Dr. Smith:</b> Please clarify sample size and participant selection.',
                                    synthesizedComments: '',
                                    synthesizedCommentsActionRequired: false
                                }
                            ],
                            consentRows: [
                                {
                                    id: 1,
                                    points: '4.1 Purpose',
                                    yes: true,
                                    no: false,
                                    linePage: 'Pg 2',
                                    conjoinedComments: '<b>Dr. Smith:</b> Purpose is clear but should be simplified.',
                                    synthesizedComments: '',
                                    synthesizedCommentsActionRequired: false
                                }
                            ]
                        });

                        await wait(350);
                    },
                    popover: {
                        title: 'Validation Modal',
                        description: 'The left panel shows the protocol details, reviewer forms, tracker, and all submitted documents.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-modal-synthesis',
                    popover: {
                        title: 'Synthesis Workspace',
                        description: 'This is where the Secretariat reads reviewer comments and writes the final synthesized feedback for each item.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-modal-footer',
                    popover: {
                        title: 'Complete Validation',
                        description: 'Once reviewer forms are in and the synthesis is complete, this action finalizes validation and moves the protocol forward.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    onHighlightStarted: async () => {
                        alpine.selectedProtocol = null;
                        alpine.activeDocument = null;
                        await wait(250);
                    },
                    popover: {
                        title: 'Assessment Tutorial Complete',
                        description: 'You have now seen the validation queue, the modal layout, reviewer tracker, documents panel, and synthesis workflow.',
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

    window.startPageTutorial = function () {
        loadDriverThenRun(() => runAssessmentTutorial(true));
    };

    loadDriverThenRun(() => runAssessmentTutorial(false));
});
</script>
@endsection
