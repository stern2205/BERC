@extends('chair.layouts.app')

@section('content')
<style>
    /* Force vertical scrollbar to prevent page layout shift when switching tabs */
    html { overflow-y: scroll; }

    /* Base Colors */
    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); position: relative; }
    .card-header { display:flex; align-items:center; justify-content:flex-start; border-bottom:1px solid #e5e7eb; background:#fafafa; padding:0; overflow-x: auto; }
    .card-tab { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; border-bottom:3px solid transparent; padding:14px 20px; white-space: nowrap; }
    .card-tab.active { color:var(--bsu-dark); border-bottom-color:var(--brand-red); background:#fff; }

    /* Copied Awaiting Response table format from evaluation.blade.php */
    .awaiting-grid-header,
    .awaiting-row {
        display: grid;
        grid-template-columns: minmax(120px, 0.95fr) minmax(250px, 2.2fr) minmax(210px, 1.6fr) minmax(280px, 2.2fr) minmax(220px, 1.8fr);
        padding: 8px 20px;
        align-items: center;
        gap: 12px;
    }
    .awaiting-grid-header {
        background: #f3f4f6;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }
    .awaiting-grid-header > div { text-align: left; }
    .awaiting-grid-header > div.header-center { text-align: center; }

    .awaiting-row {
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        cursor: default;
        transition: background .15s;
        align-items: center;
    }
    .awaiting-row > div {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        text-align: left;
    }
    .awaiting-row > div:nth-child(4),
    .awaiting-row > div:nth-child(5) {
        align-items: center;
        text-align: center;
    }
    .awaiting-row > div.reviewers-cell {
        align-items: flex-start !important;
        justify-content: center !important;
        text-align: left !important;
    }
    .awaiting-row > div.reviewers-cell .reviewers-inner {
        width: 100%;
        text-align: left;
    }
    .awaiting-row > div.reviewers-cell.reviewers-empty {
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
    }
    .awaiting-row:last-child { border-bottom: none; }
    .awaiting-row:hover { background: #f9fafb; }

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
    .app-row-sub { font-size:11px; color:#6b7280; margin-top:2px; }
    .workflow-action-link { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; transition:color 0.15s; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }
    .workflow-action-link.disabled { color:#9ca3af; cursor:default; text-decoration:none; }

    .action-btn {
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 7px 12px;
        line-height: 1.3;
        cursor: pointer;
        transition: all .15s;
    }
    .action-btn:disabled { opacity:.55; cursor:not-allowed; }
    .action-btn-primary { background: var(--bsu-dark); border-color: var(--bsu-dark); color: #fff; }
    .action-btn-primary:hover:not(:disabled) { opacity: .9; }
    .action-btn-outline { background: #fff; border-color: #cbd5e1; color: #1f2937; }
    .action-btn-outline:hover:not(:disabled) { border-color: var(--bsu-dark); color: var(--bsu-dark); }

    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; width:100%; max-width:1200px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    .certificate-modal-box { max-width:1200px; }
    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .modal-header h2 { font-size:14px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
    .close-btn { font-size:20px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; }
    .close-btn:hover { background:#f3f4f6; color:#111; }
    .modal-content { display:flex; gap:0; overflow:hidden; flex:1; min-height:0; }
    .modal-body { padding:16px 20px; overflow-y:auto; }
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 20px; border-top:1px solid #e5e7eb; background:#fafafa; }
    .field-label { display:block; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:#6b7280; margin-bottom:6px; }
    .field-input { width:100%; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#111827; font-size:12px; padding:9px 10px; }
    .field-input:focus { outline:none; border-color:var(--bsu-dark); box-shadow:0 0 0 2px rgba(33,60,113,.1); }
    .protocol-info-panel { width:260px; min-width:260px; border-right:1px solid #e5e7eb; padding:20px; background:#fafafa; overflow-y:auto; flex-shrink:0; }
    .info-group { margin-bottom:16px; }
    .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
    .info-value { font-size:13px; font-weight:700; color:#111827; }
    .doc-card { background:#fff; border:1.5px solid #d1d5db; padding:10px; border-radius:6px; display:flex; align-items:center; gap:10px; margin-top:6px; cursor:pointer; transition:all .2s; user-select:none; }
    .doc-card:hover { border-color:var(--brand-red); box-shadow:0 0 0 3px rgba(211,47,47,.12); }
    .doc-card.active { border-color:var(--bsu-dark); box-shadow:0 0 0 3px rgba(33,60,113,.12); background:#f0f4ff; }
    .doc-chevron { width:14px; height:14px; color:#9ca3af; transition:transform .3s ease; flex-shrink:0; }
    .doc-card.active .doc-chevron { transform:rotate(90deg); color:var(--bsu-dark); }
    .form-preview-panel { flex:1 1 0; width:auto; padding:24px; overflow-y:auto; background:#fff; position:relative; }
    .application-form-mock { border:1px solid #d1d5db; border-radius:8px; padding:24px; font-size:11px; color:#374151; background:#fff; max-width:700px; margin:0 auto; box-shadow:0 4px 6px rgba(0,0,0,.02); }
    .form-header { text-align:center; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid var(--bsu-dark); }
    .form-header h3 { font-size:13px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
    .cert-exemption { padding:40px; border:1px solid #e5e7eb; border-radius:8px; background:white; font-family:'Times New Roman', Times, serif; color:black; box-shadow:0 4px 10px rgba(0,0,0,.05); min-height:360px; }
    .cert-exemption:focus { outline:2px solid var(--bsu-dark); }

    .clean-input { border: 1px solid #f3f4f6; border-radius: 6px; padding: 6px 10px; font-size: 11px; color: #374151; background: #f9fafb; transition: all 0.2s; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-input:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }
    .clean-textarea { width: 100%; border: 1px solid #f3f4f6; border-radius: 6px; padding: 10px; font-size: 12px; color: #374151; background: #f9fafb; transition: all 0.2s; resize: vertical; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-textarea:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }

    .confirm-box { width:100%; max-width:400px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; box-shadow:0 20px 50px rgba(0,0,0,.2); }
    .confirm-box h3 { margin:0; font-size:18px; font-weight:900; color:#111827; }
    .confirm-box p { margin:8px 0 0; font-size:13px; color:#6b7280; line-height:1.35; }
    .confirm-actions { margin-top:16px; display:flex; justify-content:flex-end; gap:8px; }

    @media (max-width: 900px) {
        .awaiting-grid-header { display: none; }
        .awaiting-row { grid-template-columns: 1fr; gap: 8px; padding-bottom: 16px; }
        .awaiting-row > div:nth-child(4),
        .awaiting-row > div:nth-child(5) { align-items: flex-start; text-align: left; }
        .protocol-info-panel { width:100%; min-width:unset; border-right:none; border-bottom:1px solid #e5e7eb; height:250px; }
        .modal-content { flex-direction:column; }
    }
</style>

<div x-data="chairApprovalData()" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">For Approval</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Chairman queue for consultant requests and decision letters</p>
        </div>
        <div class="w-full max-w-xl flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <div class="app-card">
        <div id="tour-approval-tabs" class="card-header flex gap-6 px-4 pt-4 border-b border-gray-100">
            <button type="button"
                    class="card-tab cursor-pointer pb-3 text-sm font-bold transition-colors relative outline-none"
                    :class="activeTab === 'decision_letter' ? 'active text-bsu-dark border-b-2 border-brand-red' : 'text-gray-500 hover:text-bsu-dark'"
                    @click="activeTab = 'decision_letter'">
                Decision Letter Approval
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] ml-1" x-text="decisionLetterProposals.length"></span>
            </button>
            <button type="button"
                    class="card-tab cursor-pointer pb-3 text-sm font-bold transition-colors relative outline-none"
                    :class="activeTab === 'external_consultant' ? 'active text-bsu-dark border-b-2 border-brand-red' : 'text-gray-500 hover:text-bsu-dark'"
                    @click="activeTab = 'external_consultant'">
                External Consultant Approval
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] ml-1" x-text="consultantProposals.length"></span>
            </button>
        </div>

        <div id="tour-approval-list">
            <div class="awaiting-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Assigned Reviewers</div>
                <div class="header-center">Action</div>
            </div>

            <template x-for="protocol in filteredProposals" :key="protocol.id">
                <div class="awaiting-row hover:bg-white">
                    <div class="self-center"><span class="app-id-badge" x-text="protocol.id"></span></div>
                    <div class="self-center min-w-0">
                        <div class="app-row-title whitespace-normal break-words" x-text="protocol.title"></div>
                        <div class="app-row-sub whitespace-normal break-words mt-1" x-text="protocol.proponent"></div>
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

                    <div class="reviewers-cell w-full">
                        <template x-if="activeTab === 'external_consultant'">
                            <div class="w-full mt-1 reviewers-inner">
                                <div class="border border-gray-100 rounded-md bg-gray-50 px-2 py-1.5">
                                    <div class="text-[10px] font-bold text-gray-700">Request For Ext. Consultant</div>
                                    <div class="text-[10px] text-gray-500 font-semibold mt-0.5">
                                        Date Assigned: <span x-text="getExtConsultantRequestDate(protocol)"></span>
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-semibold" x-text="getRelativeTime(getExtConsultantRequestDateRaw(protocol))"></div>
                                    <div class="text-[10px] text-gray-600 italic mt-1.5" x-text="'(' + (protocol.external_consultant || 'Remarks of secretariat for making the request, responsibilities and reason for adding external consultant.') + ')'"></div>
                                </div>
                            </div>
                        </template>

                        <template x-if="activeTab === 'decision_letter'">
                            <div class="w-full">

                                <template x-if="protocol.classification === 'Exempted' || !protocol.classification">
                                    <div class="text-[10px] text-gray-500 italic text-left">No reviewers required for exempted protocols.</div>
                                </template>

                                <template x-if="protocol.classification === 'Expedited' || protocol.classification === 'Full Board'">
                                    <div class="flex flex-col gap-1.5 w-full">

                                        <template x-if="protocol.reviewers && protocol.reviewers.length > 0">
                                            <div class="flex flex-col gap-1">
                                                <template x-for="(reviewer, index) in protocol.reviewers" :key="index">
                                                    <div class="flex items-center gap-2 border border-gray-100 bg-gray-50 px-2 py-1.5 rounded-md w-full">
                                                        <div class="w-4 h-4 rounded bg-bsu-dark/10 text-bsu-dark flex items-center justify-center text-[9px] font-black shrink-0" x-text="index + 1"></div>
                                                        <div class="text-[10px] font-bold text-gray-700 truncate" x-text="reviewer.name || reviewer"></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!protocol.reviewers || protocol.reviewers.length === 0">
                                            <div class="text-[10px] text-gray-400 italic text-left">Reviewers pending/not mapped.</div>
                                        </template>

                                    </div>
                                </template>

                            </div>
                        </template>
                    </div>

                    <div class="h-full text-center flex flex-col items-center justify-center relative">
                        <template x-if="isSecretariatConsultantRequest(protocol)">
                            <div class="flex flex-col items-center justify-center">
                                <span @click.stop="openConsultantModal(protocol)" class="workflow-action-link cursor-pointer text-brand-red font-bold text-sm hover:underline">
                                    Add Consultant
                                </span>
                                <span class="text-[9px] font-bold text-gray-500 mt-1 text-center">Create Account</span>
                            </div>
                        </template>

                        <template x-if="isChairApproval(protocol)">
                            <div class="flex flex-col items-center justify-center w-full">
                                <template x-if="protocol.status === 'exempted_awaiting_chair_approval'">
                                    <div class="flex flex-col items-center justify-center">
                                        <span @click.stop="openReviewModal(protocol)" class="workflow-action-link cursor-pointer text-brand-red font-bold text-sm hover:underline">
                                            Review Certificate
                                        </span>
                                        <span class="text-[9px] font-bold text-gray-500 mt-1 text-center" x-text="protocol.documentReviewed ? 'Reviewed' : 'Check document'"></span>
                                    </div>
                                </template>

                                <template x-if="protocol.status !== 'exempted_awaiting_chair_approval'">
                                    <div class="flex flex-col items-center justify-center">
                                        <span @click.stop="openReviewModal(protocol)" class="workflow-action-link cursor-pointer text-brand-red font-bold text-sm hover:underline">
                                            Review Decision Letter
                                        </span>
                                        <span class="text-[9px] font-bold text-gray-500 mt-1 text-center" x-text="protocol.decisionSaved ? 'Draft Saved' : 'Pending Action'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="filteredProposals.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                <span x-show="activeTab === 'decision_letter'">No decision letters waiting for approval.</span>
                <span x-show="activeTab === 'external_consultant'">No external consultant requests pending.</span>
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="{ 'open': showConsultantModal }" x-cloak @keydown.escape.window="closeConsultantModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Create External Consultant Account</h2>
                <button class="close-btn" @click="closeConsultantModal()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 24px; overflow-y:auto; flex: 1;">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
                    <div class="relative z-10 p-4 sm:p-6 flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6">
                        <div class="shrink-0">
                            <div class="bg-gray-50 p-1 rounded-2xl border border-gray-200 shadow-sm">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 object-cover bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 10a6 6 0 0112 0"></path></svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 text-gray-900 text-center sm:text-left">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">External Consultant</p>
                            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight" x-text="consultantForm.name || 'New Account'"></h2>
                            <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Account ID</p>
                                    <p class="text-xs font-bold tracking-wide text-gray-800">---</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Email Address</p>
                                    <p class="text-xs font-bold tracking-wide text-gray-800" x-text="consultantForm.email || '---'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-blue-700">Requested From For Approval</div>
                    <div class="text-xs font-bold text-gray-800 mt-1" x-text="selectedConsultantProtocol?.title || 'N/A'"></div>

                    <div class="text-[11px] font-semibold text-gray-600 mt-1">
                        Protocol ID: <span x-text="selectedConsultantProtocol?.id || 'N/A'"></span> | Proponent: <span x-text="selectedConsultantProtocol?.proponent || 'N/A'"></span>
                    </div>

                    <div class="mt-2 pt-2 border-t border-blue-200/60">
                        <div class="text-[10px] font-black uppercase tracking-widest text-blue-700">Consultant Requirement Reason</div>
                        <div class="text-[11px] italic text-blue-900 leading-relaxed mt-1" x-text="selectedConsultantProtocol?.external_consultant || 'No specific reason provided.'"></div>
                    </div>
                </div>

                <div id="tour-consultant-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label style="font-size:10px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px;">Name</label>
                        <input type="text" class="clean-input w-full" x-model.trim="consultantForm.name" placeholder="Enter full name">
                    </div>
                    <div>
                        <label style="font-size:10px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px;">Email</label>
                        <input type="email" class="clean-input w-full" x-model.trim="consultantForm.email" placeholder="Enter email address">
                    </div>
                    <div>
                        <label style="font-size:10px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px;">Phone No.</label>
                        <input type="text" class="clean-input w-full" x-model.trim="consultantForm.phone" placeholder="Enter contact number">
                    </div>
                    <div>
                        <label style="font-size:10px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px;">Role</label>
                        <div class="clean-input w-full bg-gray-100 text-gray-500 font-bold">External Consultant</div>
                    </div>
                </div>

                <div class="mt-4">
                    <label style="font-size:10px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px;">Expertise</label>
                    <input type="text" class="clean-input w-full" x-model.trim="consultantForm.expertise" placeholder="Enter consultant expertise">
                </div>
            </div>

            <div class="modal-footer items-center">
                <div x-show="consultantFormError" x-cloak class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-[11px] font-bold text-red-700" x-text="consultantFormError"></div>
                <div class="ml-auto flex gap-2">
                    <button type="button" class="btn btn-outline" @click="closeConsultantModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="submitConsultantForm()">Create Account</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="{ 'open': showReviewModal }" x-cloak @keydown.escape.window="closeReviewModal()">
        <div class="modal-box certificate-modal-box">

            <div class="modal-header">
                <h2 x-text="selectedProtocol?.status === 'exempted_awaiting_chair_approval' ? 'Review Certificate' : 'Review Decision Letter'"></h2>
                <button class="close-btn" @click="closeReviewModal()">&times;</button>
            </div>

            <div class="modal-content relative">

                <div class="protocol-info-panel" style="overflow-y: auto;">

                    <div class="info-group">
                        <div class="info-label">Application ID</div>
                        <div class="font-bold text-lg text-bsu-dark" x-text="selectedProtocol?.id"></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Study Title</div>
                        <div class="info-value text-[11px] leading-tight" x-text="selectedProtocol?.title"></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Researcher Name</div>
                        <div class="info-value" x-text="selectedProtocol?.proponent"></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Assigned Review Type</div>
                        <div class="info-value text-blue-700" x-text="(selectedProtocol?.classification || 'Exempted') + ' Review'"></div>
                    </div>

                    <div style="margin-top:24px; padding-top:16px; border-top: 1px solid #e5e7eb;">
                        <div class="info-label" style="margin-bottom:8px;">Chair Action</div>

                        <template x-if="selectedProtocol?.status === 'exempted_awaiting_chair_approval'">
                            <div class="doc-card" :class="rightPanelMode === 'exemption' ? 'active' : ''" @click="setRightPanel('exemption')">
                                <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📜</span>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:11px; font-weight:700; color:#111827;">Exemption Certificate</div>
                                    <div style="font-size:9px; font-weight:600; color:#6b7280;">Review and approve</div>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedProtocol?.status !== 'exempted_awaiting_chair_approval'">
                            <div>
                                <div class="doc-card" :class="rightPanelMode === 'decision_form' ? 'border-brand-red bg-red-50' : ''" @click="decisionMenuOpen = !decisionMenuOpen" style="cursor:pointer; margin-bottom: 0; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">⚖️</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827;">Decision Letter</div>
                                        <div style="font-size:9px; font-weight:600; color:#6b7280;">Click to toggle options</div>
                                    </div>
                                    <svg class="doc-chevron transition-transform duration-200" :class="decisionMenuOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                                <div x-show="decisionMenuOpen" x-transition.opacity class="flex flex-col border border-t-0 border-gray-200 bg-white rounded-b-xl overflow-hidden mb-2">
                                    <button @click="setRightPanel('decision_form')" class="text-left px-4 py-3 text-[11px] font-bold hover:bg-gray-50 transition-colors border-b border-gray-100" :class="rightPanelMode === 'decision_form' ? 'text-brand-red bg-red-50/50' : 'text-gray-700'">
                                        ✏️ Edit Letter Details
                                    </button>
                                    <button @click="openIsoInNewTab()" class="text-left px-4 py-3 text-[11px] font-bold hover:bg-gray-50 transition-colors text-gray-700 flex items-center justify-between">
                                        <span>📄 Open ISO Form</span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div style="margin-top:20px; padding-top:20px; border-top: 1px dashed #e5e7eb;">
                        <div class="info-label" style="margin-bottom:8px;">Protocol Documents</div>
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
                                                <div style="font-size:10px; font-weight:700; color:#64748b;" x-text="doc.label"></div>
                                                <div style="font-size:8px; bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-black w-fit mt-1 mb-1">LEGACY</div>
                                                <div style="font-size:8px; font-weight:700; color:#94a3b8; word-break:break-all;" x-text="doc.desc"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="form-preview-panel" :class="rightPanelMode === 'document' ? 'viewer-active' : ''">

                    <div x-show="rightPanelMode === 'exemption'" x-cloak class="animate-in fade-in h-full flex flex-col max-w-3xl mx-auto w-full p-8 bg-white border border-gray-200 rounded-lg my-6 shadow-sm">
                        <div style="margin-bottom:16px;">
                            <h3 style="font-size:16px; font-weight:900; color:#213C71; text-transform:uppercase;">Exempted Review</h3>
                            <p style="font-size:11px; color:#6b7280;">Please review and edit the certificate below before final validation.</p>
                        </div>

                        <div class="cert-exemption flex-1 border border-gray-300 p-8 text-[11pt] leading-relaxed text-black bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 rounded-md overflow-y-auto font-serif shadow-inner"
                            x-ref="certificateEditor"
                            contenteditable="true"
                            @input="certificateDraft = $event.target.innerHTML">

                            <div style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #000; padding: 6px 10px; margin-bottom: 24px; font-family: sans-serif; font-size: 9pt;">
                                <div><strong>Reference No.:</strong> BatStateU-FO-BERC-023</div>
                                <div><strong>Effectivity Date:</strong> </div>
                                <div><strong>Revision No.:</strong> 00</div>
                            </div>

                            <h2 style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 24px; text-transform: uppercase;">Certificate of Exemption from Ethics Review</h2>

                            <table style="width: 100%; font-size: 11pt; margin-bottom: 24px; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 6px 0; width: 35%;"><strong>Date:</strong></td>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #000;" x-text="new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0 6px 0;"><strong>Name of Principal Investigator:</strong></td>

                                        <td x-ref="piNameCell" style="padding: 10px 0 6px 0; border-bottom: 1px solid #000; font-weight: bold;"
                                            x-text="selectedProtocol?.proponent || selectedProtocol?.primary_researcher || 'N/A'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0 6px 0;"><strong>Title of Study/Protocol:</strong></td>
                                        <td style="padding: 10px 0 6px 0; border-bottom: 1px solid #000; font-weight: bold;" x-text="selectedProtocol?.title || 'N/A'"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0 6px 0;"><strong>BERC Code:</strong></td>
                                        <td style="padding: 10px 0 6px 0; border-bottom: 1px solid #000; font-weight: bold;" x-text="selectedProtocol?.id || selectedProtocol?.protocol_code || 'N/A'"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="font-size: 11pt; text-align: justify; margin-bottom: 40px;">
                                <p style="margin-bottom: 14px;">After a preliminary review, the BatStateU TNEU Ethics Review Committee deemed it appropriate that the above protocol be EXEMPTED FROM REVIEW.</p>

                                <p style="margin-bottom: 14px;">This means that the study may be implemented without undergoing an expedited or full review. Neither will the proponents be required to submit further documents to the committee as long as there is no amendment nor alteration in the protocol that will change the nature of the study nor the level of risk involved. Please note also, that the following responsibilities of the investigator/s are maintained while the study is in progress:</p>

                                <ol style="margin: 0 0 0 24px; padding: 0; list-style-type: decimal;">
                                    <li style="margin-bottom: 10px;">Continuing compliance with the exemption criteria of the National Ethical Guidelines for Research Involving Human Participants 2022 in the duration of the study;</li>
                                    <li style="margin-bottom: 10px;">Nonetheless, such human participants in case reports/case series/non-health research are entitled to compliance of researchers with universal ethical principles of respect for persons, beneficence, and justice, as well as applicable local regulations, including the Data Privacy Act of 2012 (RA 10173). Thus, it is the responsibility of the author/investigator(s) to ensure satisfactory compliance with the aforementioned principles and all applicable regulations, and to obtain informed consent from the human subjects involved, if personally identifiable information will be used in any way.</li>
                                    <li style="margin-bottom: 10px;">No substantial changes in research design, methodology, and subject population from the protocol submitted for exemption. Modifications that significantly affect previous risk-benefit assessments or qualification for exemption may be submitted as a new protocol for initial review.</li>
                                </ol>
                            </div>

                            <div style="margin-top: 50px; text-align: right; font-style: italic; font-size: 10pt;">
                                Tracking No. <span x-text="selectedProtocol?.tracking_number || '___________________'"></span>
                            </div>
                        </div>
                    </div>

                    <div id="tour-decision-panel" x-show="rightPanelMode === 'decision_form'" x-cloak class="animate-in fade-in h-full flex flex-col w-full bg-gray-50/50">

                        <div class="bg-white border-b border-gray-200 px-5 py-4 flex justify-between items-center shrink-0 shadow-sm z-10 w-full">
                            <div class="flex flex-col">
                                <h2 class="m-0 text-sm font-black text-bsu-dark uppercase tracking-wide">Final Decision Letter Template</h2>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6 md:p-8">
                            <div class="max-w-4xl mx-auto w-full bg-white border border-gray-200 shadow-sm rounded-xl p-8">

                                <div class="mb-6 p-4 rounded-xl border-2 transition-colors"
                                    :class="{
                                        'border-green-400 bg-green-50': decisionForm.decision_status === 'approved',
                                        'border-yellow-400 bg-yellow-50': decisionForm.decision_status === 'minor_revision' || decisionForm.decision_status === 'major_revision',
                                        'border-red-400 bg-red-50': decisionForm.decision_status === 'rejected',
                                        'border-gray-200 bg-gray-50': !decisionForm.decision_status
                                    }">
                                    <label class="block text-[11px] font-black uppercase tracking-widest mb-2"
                                        :class="{
                                            'text-green-800': decisionForm.decision_status === 'approved',
                                            'text-yellow-800': decisionForm.decision_status === 'minor_revision' || decisionForm.decision_status === 'major_revision',
                                            'text-red-800': decisionForm.decision_status === 'rejected',
                                            'text-gray-600': !decisionForm.decision_status
                                        }">
                                        Committee Decision Status
                                    </label>
                                    <select x-model="decisionForm.decision_status" class="w-full p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-bsu-dark/20 transition-all">
                                        <option value="" disabled selected>-- Select a Decision Status --</option>
                                        <option value="approved">✅ APPROVED</option>
                                        <option value="minor_revision">⚠️ RESUBMIT (Minor Revision)</option>
                                        <option value="major_revision">⚠️ RESUBMIT (Major Revision)</option>
                                        <option value="rejected">❌ REJECTED/DISAPPROVED</option>
                                    </select>
                                </div>

                                <div style="display:flex; flex-direction:column; gap:8px; font-size: 12px; color: #374151;">
                                    <div>
                                        <input type="date" x-model="decisionForm.letter_date" class="clean-input border border-gray-300 rounded p-1" style="width: 160px;">
                                    </div>
                                    <div class="mt-4 flex flex-col gap-1 w-1/2">
                                        <input type="text" x-model="decisionForm.proponent" class="clean-input w-full font-bold border border-gray-300 rounded p-1" placeholder="(NAME OF PROPONENT)">
                                        <input type="text" x-model="decisionForm.designation" class="clean-input w-full border border-gray-300 rounded p-1" placeholder="(Designation)">
                                        <input type="text" x-model="decisionForm.institution" class="clean-input w-full border border-gray-300 rounded p-1" placeholder="(Institution)">
                                        <input type="text" x-model="decisionForm.address" class="clean-input w-full border border-gray-300 rounded p-1" placeholder="(Address)">
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <strong>RE:</strong>
                                        <input type="text" x-model="decisionForm.title" class="clean-input flex-1 font-bold border border-gray-300 rounded p-1" placeholder="(Title of project)">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <strong>REC Code:</strong>
                                        <input type="text" :value="selectedProtocol?.id" class="clean-input w-64 text-gray-500 bg-gray-100 border border-gray-300 rounded p-1" readonly>
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <strong>Subject:</strong>
                                        <input type="text" x-model="decisionForm.subject" class="clean-input flex-1 font-bold text-bsu-dark border border-gray-300 rounded p-1" placeholder="(Nature of action requested)">
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        Dear <input type="text" x-model="decisionForm.dear_name" class="clean-input font-bold border border-gray-300 rounded p-1" style="width: 300px;" placeholder="(Name of the Proponent)">:
                                    </div>
                                    <div class="mt-2 leading-relaxed flex items-center gap-1 flex-wrap">
                                        This is to acknowledge receipt of your request and following support documents dated <input type="date" x-model="decisionForm.support_date" class="clean-input w-32 border border-gray-300 rounded p-1 inline-block">.
                                    </div>

                                    <div class="mt-2 pl-4">
                                        <template x-for="(doc, index) in decisionForm.documents" :key="index">
                                            <div class="flex items-center gap-2 mb-2 w-full">
                                                <span>●</span>
                                                <input type="text" x-model="decisionForm.documents[index]" class="clean-input flex-1 border border-gray-300 rounded p-1" placeholder="Document Name/Version">
                                                <button @click="decisionForm.documents.splice(index, 1)" class="text-red-500 font-bold px-2 py-1 hover:bg-red-50 rounded">&times;</button>
                                            </div>
                                        </template>
                                        <button @click="decisionForm.documents.push('')" class="text-[10px] text-blue-600 font-bold underline mt-1">+ Add New Line</button>
                                    </div>

                                    <div class="mt-4 leading-relaxed">
                                        <textarea x-model="decisionForm.paragraph1" rows="3" class="clean-textarea w-full text-[12px] leading-relaxed font-medium text-gray-800 border border-gray-300 rounded p-2"></textarea>
                                    </div>
                                    <div class="mt-2 leading-relaxed">
                                        <textarea x-model="decisionForm.paragraph2" rows="2" class="clean-textarea w-full text-[12px] leading-relaxed font-medium text-gray-800 border border-gray-300 rounded p-2"></textarea>
                                    </div>

                                    <div class="mt-8 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                        <div class="bg-bsu-dark px-4 py-2.5 text-xs font-bold text-white tracking-wide border-b border-gray-300">
                                            Reviewers' Comments & Required Actions Summary
                                        </div>
                                        <table class="w-full text-left text-[11px]">
                                            <thead class="bg-gray-100 border-b border-gray-200">
                                                <tr>
                                                    <th class="px-4 py-3 font-bold text-gray-700 w-[35%]">Points for Revision (the item)</th>
                                                    <th class="px-4 py-3 font-bold text-gray-700 w-[65%] border-l border-gray-200">Recommendations</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                <template x-for="row in getActionRequiredRows()" :key="row.id || row.points">
                                                    <tr class="hover:bg-red-50/20">
                                                        <td class="px-4 py-4 align-top text-gray-800" x-text="row.points || 'Item'"></td>
                                                        <td class="px-4 py-4 align-top border-l border-gray-200 text-gray-700 leading-relaxed" x-html="row.synthesizedComments || row.synthesized_comments"></td>
                                                    </tr>
                                                </template>
                                                <tr x-show="getActionRequiredRows().length === 0">
                                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 italic">No specific points marked for action required.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        Very truly yours,
                                        <div class="mt-4 flex flex-col gap-2 w-64">
                                            <span class="text-[10px] text-gray-500 italic">(Signature is automatically attached on output)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div x-show="rightPanelMode === 'document'" x-cloak class="h-full flex flex-col bg-[#525659] w-full">

                        <div x-show="!activeDocUrl" class="absolute inset-0 flex items-center justify-center flex-col text-center opacity-50 bg-white z-0">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-bold text-gray-600 px-10">Select a protocol document from the left panel to begin reviewing.</p>
                        </div>

                        <div x-show="activeDocUrl" class="bg-white border-b border-gray-200 px-5 py-3 flex justify-between items-center shrink-0 shadow-sm z-10 w-full relative">
                            <div class="flex flex-col">
                                <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDocTitle"></h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Researcher Attachment Preview</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a :href="activeDocUrl" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                <button @click="setRightPanel(selectedProtocol?.status === 'exempted_awaiting_chair_approval' ? 'exemption' : 'decision_form')" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
                                    Close Preview
                                </button>
                            </div>
                        </div>

                        <div x-show="activeDocUrl" class="flex-1 relative w-full h-full z-10">
                            <iframe :src="activeDocUrl" class="w-full h-full border-none bg-white"></iframe>
                        </div>

                    </div>

                </div> </div> <div class="modal-footer">
                <button type="button" class="btn btn-outline" @click="closeReviewModal()">Close</button>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-primary" @click="submitFinalReview()">Validate & Route Approval</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="{ 'open': showConfirmModal }" x-cloak @keydown.escape.window="closeConfirmModal()">
        <div class="modal-box !h-auto !max-w-md mx-auto my-auto shadow-2xl overflow-visible">
            <div class="p-6">
                <h3 class="text-lg font-black text-bsu-dark uppercase tracking-tight mb-2">Continue?</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-6" x-text="confirmMessage"></p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" @click="closeConfirmModal()">Cancel</button>
                    <button type="button" class="btn btn-primary bg-[#c21c2c] hover:bg-[#a01724]" @click="runConfirm()">Yes, Continue</button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="notification.open"
         x-transition.opacity.duration.150ms
         class="fixed top-20 right-6 z-[9999] bg-white border rounded-lg shadow-lg p-4 w-80"
         :class="notification.type === 'success' ? 'border-green-200' : 'border-red-200'" x-cloak>
        <div class="text-[11px] font-black uppercase tracking-wider"
             :class="notification.type === 'success' ? 'text-green-700' : 'text-red-700'"
             x-text="notification.title"></div>
        <div class="text-xs font-semibold text-gray-700 mt-1" x-text="notification.message"></div>
    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chairApprovalData', () => ({
            activeTab: 'decision_letter',
            searchQuery: '',
            timeTicker: Date.now(),

            proposals: @json($proposals ?? []),
            systemExternalConsultants: @json($systemExternalConsultants ?? []),

            selectedProtocol: null,
            selectedConsultantProtocol: null,

            showConsultantModal: false,
            showReviewModal: false,
            showConfirmModal: false,
            confirmMessage: '',
            confirmAction: null,

            // Panel & Viewer State
            rightPanelMode: 'document', // 'document', 'decision_form', 'exemption'
            decisionMenuOpen: true,

            // Document Tracking State
            activeDocument: null,
            activeDocKey: null,
            activeDocUrl: null,
            activeDocTitle: '',

            // Dynamic Documents
            loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
            isLoadingDocs: false,

            certificateDraft: '',

            isSavingDecision: false,
            isIsoLoading: true,
            isoUrl: '',
            decisionForm: {
                decision_status: '', letter_date: '', proponent: '', designation: '', institution: '',
                address: '', title: '', subject: '', dear_name: '', support_date: '', documents: [],
                paragraph1: '', paragraph2: '', findings: '', recommendations: '', instructions: ''
            },

            consultantForm: { name: '', email: '', phone: '', expertise: '' },
            consultantFormError: '',

            notification: { open: false, title: '', message: '', type: 'success' },
            notificationTimer: null,

            // Document Mappers
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
                setInterval(() => { this.timeTicker = Date.now(); }, 1000);

                // Watcher to dynamically update Paragraph 2 based on Decision
                this.$watch('decisionForm.decision_status', (val) => {
                    if (!this.selectedProtocol) return;

                    if (val === 'approved') {
                        this.decisionForm.paragraph2 = `As a result of the review, we are pleased to inform you that your study protocol has been APPROVED. You may proceed with your research; however, please note the recommended improvements and/or clarifications summarized below for your guidance.`;
                    } else if (val === 'minor_revision') {
                        this.decisionForm.paragraph2 = `As a result of the review, your study protocol requires MINOR REVISIONS. Please address the recommended revisions and clarifications summarized below before resubmitting your application.`;
                    } else if (val === 'major_revision') {
                        this.decisionForm.paragraph2 = `As a result of the review, your study protocol requires MAJOR REVISIONS. Please thoroughly address the recommended revisions and clarifications summarized below before resubmitting your application.`;
                    } else if (val === 'rejected') {
                        this.decisionForm.paragraph2 = `As a result of the review, we regret to inform you that your study protocol has been DISAPPROVED. The specific findings and reasons leading to this decision are summarized below.`;
                    } else {
                        this.decisionForm.paragraph2 = `As a result of the review, the final decision for your study is currently pending.`;
                    }
                });
            },

            // --- COMPUTED PROPERTIES ---
            get decisionLetterProposals() { return this.proposals.filter((p) => this.isChairApproval(p)); },
            get consultantProposals() { return this.proposals.filter((p) => this.isSecretariatConsultantRequest(p)); },
            get filteredProposals() {
                const query = this.searchQuery.trim().toLowerCase();
                const eligible = this.activeTab === 'decision_letter' ? this.decisionLetterProposals : this.consultantProposals;
                if (!query) return eligible;
                return eligible.filter((p) => p.id.toLowerCase().includes(query) || p.title.toLowerCase().includes(query) || p.proponent.toLowerCase().includes(query) || p.classification.toLowerCase().includes(query));
            },

            getActionRequiredRows() {
                if (!this.selectedProtocol) return [];
                let rows = [];
                if (this.selectedProtocol.assessmentRows) {
                    rows = [...rows, ...this.selectedProtocol.assessmentRows.filter(r => r.synthesizedCommentsActionRequired)];
                }
                if (this.selectedProtocol.consentRows) {
                    rows = [...rows, ...this.selectedProtocol.consentRows.filter(r => r.synthesizedCommentsActionRequired)];
                }
                if (this.selectedProtocol.revisionRows) {
                    rows = [...rows, ...this.selectedProtocol.revisionRows.filter(r => r.action === 'action_required')];
                }
                return rows;
            },

            getSecondWednesday() {
                const d = new Date(); d.setDate(1);
                let day = d.getDay(); let offset = (3 - day + 7) % 7;
                d.setDate(1 + offset + 7);
                return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            },

            isSecretariatConsultantRequest(protocol) {
                return protocol?.external_consultant !== null && protocol?.external_consultant !== undefined && protocol?.external_consultant !== '';
            },
            isChairApproval(protocol) {
                return protocol?.status === 'exempted_awaiting_chair_approval' || protocol?.status === 'awaiting_approval';
            },
            getClassificationTagClass(classification) {
                if (classification === 'Exempted') return 'text-blue-700 border-blue-200 bg-blue-50';
                if (classification === 'Expedited' || classification === 'Full Board') return 'text-red-700 border-red-200 bg-red-50';
                return 'text-bsu-dark border-bsu-dark/20 bg-gray-50';
            },

            // Time Formatting
            formatElapsed(dateInput) {
                if (!dateInput) return 'N/A';
                const diffMs = Math.max(0, new Date() - new Date(dateInput));
                const mins = Math.floor(diffMs / 60000);
                if (mins < 60) return `${Math.max(1, mins)} minute${mins === 1 ? '' : 's'} ago`;
                const hours = Math.floor(mins / 60);
                if (hours < 24) return `${hours} hour${hours === 1 ? '' : 's'} ago`;
                const days = Math.floor(hours / 24);
                return `${days} day${days === 1 ? '' : 's'} ago`;
            },
            formatDateOnly(dateInput) {
                if (!dateInput) return 'N/A';
                if (typeof dateInput === 'string' && dateInput.includes('T')) return dateInput.split('T')[0];
                const parsed = new Date(dateInput);
                if (!Number.isNaN(parsed.getTime())) return parsed.toISOString().slice(0, 10);
                return dateInput;
            },

            // --- UI ACTIONS ---
            showNotification(title, message, type = 'success') {
                this.notification = { open: true, title, message, type };
                if (this.notificationTimer) clearTimeout(this.notificationTimer);
                this.notificationTimer = setTimeout(() => { this.notification.open = false; }, 2500);
            },

            setRightPanel(mode) {
                this.rightPanelMode = mode;
                this.activeDocKey = null;
                this.activeDocUrl = null;
            },

            viewDocument(id, url, label) {
                this.activeDocKey = id;
                this.activeDocUrl = url;
                this.activeDocTitle = label;
                this.rightPanelMode = 'document';
            },

            openIsoInNewTab() {
                if (!this.selectedProtocol) return;
                if (!this.selectedProtocol.decisionSaved && !this.decisionForm.decision_status) {
                    this.showNotification('Warning', 'Please save a draft of the letter first.', 'error');
                    return;
                }
                const url = `/decision-letter/pdf/${this.selectedProtocol.id}?t=${Date.now()}`;
                window.open(url, '_blank');
            },

            // --- MODAL & FETCH LOGIC ---
            openReviewModal(protocol) {
                // ── TUTORIAL BYPASS LOGIC ──
                if (protocol.is_mock) {
                    this.selectedProtocol = protocol;
                    this.rightPanelMode = 'decision_form';
                    this.decisionMenuOpen = true;
                    this.decisionForm = {
                        decision_status: 'approved',
                        letter_date: new Date().toISOString().split('T')[0],
                        proponent: protocol.proponent,
                        designation: 'Lead Researcher',
                        institution: 'Batangas State University',
                        address: 'Batangas City',
                        title: protocol.title,
                        subject: 'Research Protocol Decision',
                        dear_name: protocol.proponent,
                        support_date: new Date().toISOString().split('T')[0],
                        documents: ['Study Protocol: V1', 'Informed Consent: V1'],
                        paragraph1: 'We wish to inform you...',
                        paragraph2: 'As a result of the review, we are pleased to inform you that your study protocol has been APPROVED.'
                    };
                    this.loadedDocs = { activeBasic: [{ id: 'mock1', label: 'Study Protocol', desc: 'Mock PDF', url: '', isRevised: false }], activeSupp: [], legacy: [] };
                    this.isLoadingDocs = false;
                    this.showReviewModal = true;
                    return;
                }

                this.selectedProtocol = protocol;
                this.activeDocument = null;
                this.activeDocKey = null;
                this.activeDocUrl = null;
                this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };
                this.decisionForm.documents = [];

                if (protocol.status === 'exempted_awaiting_chair_approval') {
                    this.rightPanelMode = 'exemption';
                    this.certificateDraft = protocol.certificateText || '';
                } else {
                    this.decisionMenuOpen = true;
                    this.rightPanelMode = 'decision_form';

                    const dl = protocol.decision_letter || {};
                    let initialDocs = [];

                    try {
                        if (Array.isArray(dl.documents)) {
                            initialDocs = [...dl.documents];
                        } else if (typeof dl.documents === 'string' && dl.documents.trim() !== '') {
                            initialDocs = JSON.parse(dl.documents);
                        }
                    } catch (e) {
                        console.error("JSON Parsing Error for documents:", e);
                        initialDocs = [];
                    }

                    const today = new Date().toISOString().split('T')[0];
                    const meetingDate = this.getSecondWednesday();

                    this.decisionForm = {
                        decision_status: dl.decision_status || '',
                        letter_date: dl.letter_date || today,
                        proponent: dl.proponent || protocol.proponent,
                        designation: dl.designation || 'Researcher',
                        institution: dl.institution || protocol.institution || 'Batangas State University',
                        address: dl.address || protocol.institution_address || 'Batangas City',
                        title: dl.title || protocol.title,
                        subject: dl.subject || 'Research Protocol Decision',
                        dear_name: dl.dear_name || protocol.proponent,
                        support_date: dl.support_date || today,
                        documents: Array.isArray(initialDocs) ? initialDocs.filter(d => d !== null) : [],
                        paragraph1: dl.paragraph1 || `We wish to inform you that the Batangas State University Research Ethics Committee (BERC) reviewed your study protocol during its regular meeting on ${meetingDate}. Your study has been assigned the protocol code ${protocol.id}, which should be used in all future communications related to this study.`,
                        paragraph2: dl.paragraph2 || `As a result of the review, the action requested for your study is pending. Recommended revisions and/or clarifications are summarized below:`
                    };
                }

                // FETCH DYNAMIC DOCS
                this.fetchDocuments(protocol.id || protocol.protocol_code);

                this.showReviewModal = true;
            },

            async fetchDocuments(protocolId) {
                this.isLoadingDocs = true;

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
                        let letterDocLines = [];
                        let groups = {};

                        if (data.documents) {
                            Object.keys(data.documents).forEach(type => {
                                const docs = data.documents[type];
                                if (!docs || docs.length === 0) return;

                                // 1. Find the highest timestamp for version sorting
                                let maxTs = 0;
                                docs.forEach(doc => {
                                    const match = (doc.url || '').match(/_(\d{10})_/);
                                    const ts = match ? parseInt(match[1]) : 0;
                                    if (ts > maxTs) maxTs = ts;
                                });

                                const isBasic = this.basicTypes.includes(type);
                                const titleLabel = this.docLabels[type] || type.replace(/_/g, ' ').toUpperCase();
                                groups[type] = { items: [] };

                                // 2. Map and partition
                                docs.forEach(doc => {
                                    const path = doc.url || '';
                                    const match = path.match(/_(\d{10})_/);
                                    const ts = match ? parseInt(match[1]) : 0;

                                    const hasRealDesc = doc.description && doc.description !== 'View File' && doc.description !== '';
                                    const displayName = hasRealDesc ? doc.description : parseFileName(path);
                                    const isRevised = path.includes('resubmit_');

                                    const obj = { id: doc.id || Math.random(), label: titleLabel, url: doc.url, isRevised: isRevised, desc: displayName };

                                    if (ts === maxTs || maxTs === 0) {
                                        if (isBasic) tempDocs.activeBasic.push(obj);
                                        else tempDocs.activeSupp.push(obj);

                                        groups[type].items.push(displayName);
                                    } else {
                                        obj.desc = displayName;
                                        obj.isRevised = false;
                                        tempDocs.legacy.push(obj);
                                    }
                                });
                            });

                            // 3. Build Draft Letter Lines
                            Object.keys(groups).forEach(type => {
                                if (groups[type].items.length > 0) {
                                    const label = this.docLabels[type] || type.toUpperCase().replace(/_/g, ' ');
                                    const combinedDesc = [...new Set(groups[type].items)].join(', ');
                                    letterDocLines.push(`${label}: ${combinedDesc}`);
                                }
                            });
                        }
                        this.loadedDocs = tempDocs;

                        // 4. One-Shot Padding: Only overwrite the letter lines if the current draft is empty
                        const isEmptyDraft = this.decisionForm.documents.length === 0 || this.decisionForm.documents.every(d => d === '');
                        if (isEmptyDraft) {
                            this.decisionForm.documents = letterDocLines;
                            while (this.decisionForm.documents.length < 5) {
                                this.decisionForm.documents.push('');
                            }
                        }
                    }
                } catch (e) {
                    console.error("Doc Load Error:", e);
                } finally {
                    this.isLoadingDocs = false;
                }
            },

            closeReviewModal() {
                this.showReviewModal = false;
                this.selectedProtocol = null;
                this.activeDocKey = null;
                this.activeDocUrl = null;
                this.isoUrl = '';
            },

            // --- SAVING & SUBMISSIONS ---
            async saveDecisionForm() {
                if (!this.selectedProtocol) return;

                if (!this.decisionForm.decision_status) {
                    this.showNotification('Error', 'Please select a Committee Decision Status.', 'error');
                    return;
                }

                this.isSavingDecision = true;

                try {
                    const response = await fetch('/chair/decision-letter/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            protocol_code: this.selectedProtocol.id,
                            ...this.decisionForm
                        })
                    });

                    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                    const result = await response.json();

                    if (result.success || response.status === 200) {
                        this.selectedProtocol.decision_letter = this.decisionForm;
                        this.selectedProtocol.decisionSaved = true;
                        this.showNotification('Saved', 'Decision Letter drafted successfully.', 'success');
                    } else {
                        this.showNotification('Error', result.message || 'Failed to save letter.', 'error');
                    }
                } catch (error) {
                    console.error("Save Draft Error:", error);
                    this.showNotification('Error', 'Network error or invalid server response.', 'error');
                } finally {
                    this.isSavingDecision = false;
                }
            },

            submitFinalReview() {
                const isExempt = this.selectedProtocol.status === 'exempted_awaiting_chair_approval';

                if (isExempt) {
                    if (this.$refs.certificateEditor) {
                        this.certificateDraft = this.$refs.certificateEditor.innerText;
                        if (!this.certificateDraft.trim()) {
                            this.showNotification('Validation Error', 'The document cannot be empty.', 'error');
                            return;
                        }
                    }
                    this.confirmMessage = 'Approve and release this exemption certificate?';
                } else {
                    if (!this.decisionForm.decision_status) {
                        this.showNotification('Validation Error', 'Please select a Committee Decision Status.', 'error');
                        return;
                    }
                    this.confirmMessage = `Officially mark this protocol as ${this.decisionForm.decision_status.toUpperCase().replace('_', ' ')}?`;
                }

                this.confirmAction = 'finalize';
                this.showConfirmModal = true;
            },

            async executeFinalizeProtocol() {
                if (!this.selectedProtocol) return;

                this.isSavingDecision = true;
                const isExempt = this.selectedProtocol.status === 'exempted_awaiting_chair_approval';
                let editedPiName = null;
                if (isExempt && this.$refs.piNameCell) {
                    editedPiName = this.$refs.piNameCell.innerText.trim();
                }

                try {
                    const response = await fetch('/chair/protocol/finalize', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            protocol_code: this.selectedProtocol.protocol_code || this.selectedProtocol.id,
                            type: isExempt ? 'exempted' : 'full_board',
                            decision_status: isExempt ? 'approved' : this.decisionForm.decision_status,
                            certificate_text: isExempt ? this.certificateDraft : null,
                            principal_investigator: editedPiName
                        })
                    });

                    // We can safely go back to standard JSON parsing now
                    const result = await response.json();

                    if (response.ok && result.success) {
                        // 1. Mark as reviewed while the protocol object still exists
                        this.selectedProtocol.documentReviewed = true;

                        // 2. Filter the array FIRST, before closing the modal
                        if (this.proposals) {
                            const targetId = this.selectedProtocol.protocol_code || this.selectedProtocol.id;
                            this.proposals = this.proposals.filter(p => (p.protocol_code || p.id) !== targetId);
                        }

                        // 3. NOW safely close the modal (which wipes this.selectedProtocol)
                        this.closeReviewModal();

                        // 4. Show the success notification
                        this.showNotification('Success', result.message || 'Protocol finalized.', 'success');

                    } else {
                        const errorMessage = result.message || (result.errors ? Object.values(result.errors).flat()[0] : 'Failed to finalize.');
                        this.showNotification('Error', errorMessage, 'error');
                    }

                } catch (error) {
                    console.error("Finalize Error:", error);
                    this.showNotification('Error', 'Network error or invalid server response.', 'error');
                } finally {
                    this.isSavingDecision = false;
                }
            },

            // --- CONSULTANT ASSIGNMENT ---
            resetConsultantForm() {
                this.consultantForm = { name: '', email: '', phone: '', expertise: '' };
                this.consultantFormError = '';
            },

            openConsultantModal(protocol) {
                // ── TUTORIAL BYPASS LOGIC ──
                if (protocol.is_mock) {
                    this.selectedConsultantProtocol = protocol;
                    this.resetConsultantForm();
                    this.showConsultantModal = true;
                    return;
                }

                if (!this.isSecretariatConsultantRequest(protocol)) return;
                this.selectedConsultantProtocol = protocol;
                this.resetConsultantForm();
                this.showConsultantModal = true;
            },
            closeConsultantModal() {
                this.showConsultantModal = false;
                this.selectedConsultantProtocol = null;
            },
            submitConsultantForm() {
                if (!this.consultantForm.name || !this.consultantForm.email || !this.consultantForm.expertise) {
                    this.consultantFormError = 'Please complete Name, Email, and Expertise.';
                    return;
                }
                this.consultantFormError = '';
                this.confirmMessage = `Create account and assign to ${this.selectedConsultantProtocol.id}?`;
                this.confirmAction = 'consultant';
                this.showConfirmModal = true;
            },
            async executeAssignConsultant() {
                try {
                    const response = await fetch('{{ route('chair.consultant.assign') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            protocol_code: this.selectedConsultantProtocol.id,
                            ...this.consultantForm
                        })
                    });

                    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                    const result = await response.json();

                    if (result.success) {
                        this.proposals = this.proposals.filter(p => p.id !== this.selectedConsultantProtocol.id);
                        this.closeConsultantModal();
                        this.showNotification('Consultant Assigned', 'Account created and assigned.', 'success');
                    } else {
                        this.consultantFormError = result.message || 'Failed to create consultant.';
                    }
                } catch (error) {
                    console.error("Consultant Error:", error);
                    this.consultantFormError = 'Network error occurred.';
                }
            },

            // --- CONFIRMATION MODAL ---
            closeConfirmModal() {
                this.showConfirmModal = false;
                this.confirmMessage = '';
                this.confirmAction = null;
            },
            async runConfirm() {
                const actionToRun = this.confirmAction;
                this.closeConfirmModal();

                if (actionToRun === 'finalize') {
                    await this.executeFinalizeProtocol();
                } else if (actionToRun === 'consultant') {
                    await this.executeAssignConsultant();
                }
            }
        }));
    });
</script>

<script>
document.addEventListener('alpine:initialized', () => {
    // 1. Dynamic CSS/JS Injection
    if (typeof window.driver === 'undefined') {
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css';
        document.head.appendChild(css);

        // Inject Driver Overrides dynamically
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

        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        if (tourState === 'chair_approval') {
            const driver = window.driver.js.driver;

            // Gain access to Alpine's state variables so we can force open the modals
            const alpineComponent = Alpine.$data(document.querySelector('[x-data="chairApprovalData()"]'));

            const tour = driver({
                showProgress: true,
                allowClose: false,
                overlayColor: 'rgba(33, 60, 113, 0.75)',
                nextBtnText: 'Next &rarr;',
                prevBtnText: '&larr; Back',

                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        // Clean up mock modals before leaving
                        alpineComponent.closeReviewModal();
                        alpineComponent.closeConsultantModal();

                        localStorage.setItem(storageKey, 'chair_revisions');
                        tour.destroy();
                        window.location.href = "{{ route('chair.revision.decision') ?? '/chair/revisions' }}";
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-approval-tabs',
                        popover: {
                            title: 'Approval Queues',
                            description: 'You have two queues here. "Decision Letters" are protocols that have been reviewed and need your final approval. "External Consultant" are requests from the Secretariat to add a specialist to a review panel.',
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-approval-list',
                        popover: {
                            title: 'Decision Letters',
                            description: 'Clicking on an item in this list will open the Decision Letter template for you to finalize the status.',
                            side: "top",
                            align: 'start',
                            onNextClick: () => {
                                // ── MOCK REVIEW MODAL OPEN ──
                                alpineComponent.activeTab = 'decision_letter';
                                alpineComponent.openReviewModal({
                                    is_mock: true,
                                    id: '2026-MOCK-DL',
                                    title: 'AI Ethics in Academia',
                                    proponent: 'Dr. John Smith',
                                    status: 'awaiting_approval'
                                });

                                setTimeout(() => { tour.moveNext(); }, 300);
                            }
                        }
                    },
                    {
                        element: '#tour-decision-panel',
                        popover: {
                            title: 'Drafting the Letter',
                            description: 'This template is automatically generated based on the reviewers\' feedback. You can edit the text, change the decision status (Approved/Rejected), and then validate it to send it to the researcher.',
                            side: "left",
                            align: 'center',
                            onNextClick: () => {
                                // Close the review modal, switch tabs, and open the consultant modal
                                alpineComponent.closeReviewModal();
                                alpineComponent.activeTab = 'external_consultant';

                                // ── MOCK CONSULTANT MODAL OPEN ──
                                alpineComponent.openConsultantModal({
                                    is_mock: true,
                                    id: '2026-MOCK-EC',
                                    title: 'Advanced Robotics and Human Interaction',
                                    proponent: 'Dr. Emmett Brown',
                                    external_consultant: 'Needs expert in mechanical engineering to verify hardware safety.'
                                });

                                setTimeout(() => { tour.moveNext(); }, 400);
                            }
                        }
                    },
                    {
                        element: '#tour-consultant-form',
                        popover: {
                            title: 'External Consultants',
                            description: 'When the Secretariat requests a consultant, you will see their reasoning. If you approve, fill out this form to generate a temporary account and assign them to the protocol.',
                            side: "top",
                            align: 'center',
                            onNextClick: () => {
                                alpineComponent.closeConsultantModal();
                                setTimeout(() => { tour.moveNext(); }, 300);
                            }
                        }
                    },
                    {
                        // Floating popover
                        popover: {
                            title: 'Next Stop: Revision Approvals',
                            description: 'What happens when a researcher resubmits their work and the reviewers accept it? It comes back to you. Let\'s look at Revision Approvals next.',
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
@endsection
