@extends('secretariat.layouts.app')

@section('content')
<style>
    html { overflow-y: scroll; }
    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); position: relative; }
    .card-header { display:flex; align-items:center; justify-content: flex-start; border-bottom:1px solid #e5e7eb; background:#fafafa; padding:0; overflow-x: auto; }
    .card-tab { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; border-bottom:3px solid transparent; padding:14px 20px; cursor:pointer; transition:all 0.2s; white-space: nowrap; }
    .card-tab.active { color:var(--bsu-dark); border-bottom-color:var(--brand-red); background:#fff; }

    .assessment-grid-header, .assessment-row {
        display: grid;
        grid-template-columns: minmax(120px, 0.95fr) minmax(250px, 2.2fr) minmax(210px, 1.6fr) minmax(280px, 2.2fr) minmax(140px, 1fr);
        padding: 8px 20px;
        align-items: center;
        gap: 12px;
    }
    .assessment-grid-header { background: #f3f4f6; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
    .assessment-grid-header > div { text-align: left; }
    .assessment-grid-header > div:last-child { text-align: center; }

    .assessment-row { padding: 14px 20px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background .15s; }
    .assessment-row > div { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; text-align: left; }
    .assessment-row > div:last-child { align-items: center; text-align: center; }
    .assessment-row:last-child { border-bottom: none; }
    .assessment-row:hover { background: #f9fafb; }

    .app-id-badge { display: inline-flex; align-items: center; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; font-weight: 800; font-family: monospace; letter-spacing: 0.03em; padding: 4px 9px; border-radius: 6px; white-space: nowrap; }
    .app-row-title { font-size:13px; font-weight:700; color:#111827; }
    .app-row-sub   { font-size:11px; color:#6b7280; margin-top:2px; }
    .workflow-action-link { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; transition:color 0.15s; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }

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

    .protocol-info-panel { width: 280px; min-width: 280px; border-right: 1px solid #e5e7eb; padding: 20px; background: #fafafa; overflow-y: auto; flex-shrink: 0; }
    .info-group { margin-bottom:16px; }
    .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
    .info-value { font-size:13px; font-weight:700; color:#111827; }

    .doc-card { background:#fff; border:1.5px solid #d1d5db; padding:10px; border-radius:6px; display:flex; align-items:center; gap:10px; margin-top:6px; cursor:pointer; transition:all .2s; user-select:none; }
    .doc-card:hover { border-color:var(--brand-red); box-shadow:0 0 0 3px rgba(211,47,47,.12); }
    .doc-card.active { border-color:var(--bsu-dark); box-shadow:0 0 0 3px rgba(33,60,113,.12); background:#f0f4ff; }
    .doc-chevron { width:14px; height:14px; color:#9ca3af; transition:transform .3s ease; flex-shrink:0; }
    .doc-card.active .doc-chevron { transform:rotate(90deg); color:var(--bsu-dark); }

    .form-preview-panel { flex: 1 1 0; width: auto; padding: 24px; overflow-y: auto; background: #fff; position: relative; display: flex; flex-direction: column; }
    .form-preview-panel.viewer-active { padding: 0 !important; }

    .application-form-mock { border:1px solid #d1d5db; border-radius:8px; padding:24px; font-size:11px; color:#374151; background:#fff; max-width:850px; margin:0 auto; box-shadow:0 4px 10px rgba(0,0,0,.04); }
    .form-header { text-align:center; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid var(--bsu-dark); }
    .form-header h3 { font-size:13px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }

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
</style>

<div id="revision-root" x-data="assessmentData(@js($protocolsData ?? []))" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Resubmission Validation</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Review researcher's response to committee recommendations</p>
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

    <div id="tour-revision-list" class="app-card relative">
        <div class="card-header">
            <div class="card-tab active">
                Pending Validations
                <span x-show="forValidation.length > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="forValidation.length" x-cloak></span>
            </div>
        </div>

        <div>
            <div class="assessment-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Revision Version</div>
                <div>Action</div>
            </div>

            <template x-for="(protocol, index) in filteredData" :key="protocol.id">
                <div :id="index === 0 ? 'tour-first-revision-row' : null" class="assessment-row" @click="openValidate(protocol)">
                    <div><span class="app-id-badge" x-text="protocol.id"></span></div>
                    <div>
                        <div class="app-row-title whitespace-normal break-words leading-snug" x-text="protocol.title"></div>
                        <div class="app-row-sub whitespace-normal break-words mt-1" x-text="protocol.proponent"></div>
                    </div>
                    <div class="h-full flex flex-col justify-center gap-1.5">
                        <div>
                            <span class="font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border" :class="getClassificationTagClass(protocol.classification)" x-text="protocol.classification"></span>
                        </div>
                    </div>
                    <div class="w-full flex flex-col justify-center gap-1">
                        <div>
                            <span class="text-[12px] font-black text-bsu-dark bg-gray-100 border border-gray-200 px-3 py-1 rounded-md" x-text="protocol.version"></span>
                        </div>
                        <div class="text-[10px] text-gray-500 font-bold mt-1" x-text="'Submitted: ' + protocol.dateSubmitted"></div>
                    </div>
                    <div>
                        <span @click.stop="openValidate(protocol)" class="workflow-action-link"><u>Validate</u></span>
                    </div>
                </div>
            </template>

            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No resubmissions pending validation.
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="selectedProtocol ? 'open' : ''">
        <div class="modal-box" :class="isReviewerFormActive ? 'expanded' : ''" @click.stop>

            <div class="modal-header">
                <h2>Resubmission Validation</h2>
                <button class="close-btn" @click="selectedProtocol = null; activeDocument = null;">&times;</button>
            </div>

            <div class="modal-content flex flex-row w-full h-full">
                <div id="tour-revision-sidebar" class="protocol-info-panel">
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
                        <div class="info-label" style="margin-bottom:8px;">Resubmission Details</div>
                        <template x-for="rev in validationDocs" :key="rev.key">
                            <div>
                                <div class="doc-card" :class="getValidationDocCardClass(rev.key)" @click="toggleDocument(rev.key)">
                                    <span style="color:var(--brand-red); font-size:18px; flex-shrink:0;">📋</span>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:11px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" x-text="rev.label"></div>
                                        <div style="font-size:9px; font-weight:700; color:#6b7280; margin-top:1px;" x-text="activeDocument === rev.key ? 'Viewing Form' : 'Click to review'"></div>
                                    </div>
                                    <svg class="doc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div id="tour-revision-documents" style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:20px;">
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
                        </div>
                    </div>
                </div>

                <div id="tour-revision-preview" class="form-preview-panel" :class="activeDocument === 'doc_viewer' ? 'viewer-active' : ''">

                    <div x-show="!activeDocument" class="h-full flex items-center justify-center flex-col text-center opacity-50">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-bold text-gray-600 px-10">Select the Resubmission Form or a document from the left panel to begin validation.</p>
                    </div>

                    <div x-show="activeDocument === 'revision_form'" x-cloak class="application-form-mock relative">
                        <div class="form-header">
                            <h3>Researcher Response Form</h3>
                            <p style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:.05em;">Submitted Revisions for Version <span x-text="selectedProtocol?.version"></span></p>
                        </div>
                        <div x-show="selectedProtocol">
                            <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm bg-white">
                                <table class="w-full text-left border-collapse table-fixed">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d] text-center" style="width:8%;">Item</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:17%;">Section & Page</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:35%;">BERC Recommendation</th>
                                            <th class="px-3 py-2.5 text-[9px] font-black uppercase text-[#1f377d]" style="width:40%;">Researcher Response</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in selectedProtocol?.revisionRows" :key="row.id">
                                            <tr class="hover:bg-gray-50/30 transition-colors">
                                                <td
                                                    class="px-3 py-3 text-[10px] font-bold text-gray-800 align-top text-center whitespace-normal break-words"
                                                    x-text="row.item_display">
                                                </td>
                                                <td class="px-3 py-3 align-top"><span class="text-[10px] font-semibold text-gray-600" x-text="row.section_and_page"></span></td>
                                                <td class="px-3 py-3 align-top bg-gray-50/50">
                                                    <div class="text-[10px] leading-relaxed text-gray-700 whitespace-pre-wrap" x-text="row.berc_recommendation"></div>
                                                </td>
                                                <td class="px-3 py-3 align-top bg-blue-50/30">
                                                    <div class="text-[10px] leading-relaxed text-bsu-dark font-medium whitespace-pre-wrap" x-text="row.researcher_response"></div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="!selectedProtocol?.revisionRows || selectedProtocol.revisionRows.length === 0">
                                            <td colspan="4" class="px-3 py-8 text-center text-gray-400 text-[10px] font-bold">No specific revision items found for this submission.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                <svg class="animate-spin h-8 w-8 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-xs font-black tracking-widest uppercase">Loading Document...</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="tour-revision-footer" class="modal-footer flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span x-text="notificationMessage" class="text-[10px] font-bold text-green-600 transition-opacity duration-300"></span>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-outline border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200"
                            @click="openConfirmModal('reject')">
                        Reject Resubmission
                    </button>

                    <button class="btn btn-primary bg-[#c21c2c] hover:bg-[#a01724] shadow-sm flex items-center gap-2"
                            @click="openConfirmModal('approve')">
                        Complete Validation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="tour-confirm-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity z-[2000]"
         x-show="confirmModalOpen" x-transition x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.stop>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         :class="pendingAction === 'approve' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                        <template x-if="pendingAction === 'approve'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        <template x-if="pendingAction === 'reject'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </template>
                    </div>
                    <h3 class="text-lg font-black text-bsu-dark tracking-tight uppercase" x-text="pendingAction === 'approve' ? 'Confirm Validation' : 'Reject Resubmission'"></h3>
                </div>

                <p class="text-xs text-gray-500 font-medium mb-5 leading-relaxed"
                   x-text="pendingAction === 'approve' ? 'Are you sure you want to approve this resubmission and forward it to the reviewers?' : 'Are you sure you want to reject this resubmission and return it to the researcher?'"></p>

                <div class="mb-6">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-2">Secretariat Comment <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea x-model="secretariatComment" rows="3" class="w-full p-3 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark resize-none bg-gray-50 hover:bg-white transition-colors" placeholder="Add a note or instruction for the researcher regarding this action..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="closeConfirmModal()" class="px-5 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wider hover:bg-gray-100 rounded-lg transition disabled:opacity-50" :disabled="isLoading">
                        Cancel
                    </button>

                    <button type="button" class="px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider rounded-lg shadow-sm transition flex items-center gap-2"
                            :class="pendingAction === 'approve' ? 'bg-bsu-dark hover:bg-opacity-90' : 'bg-brand-red hover:bg-red-700'"
                            @click="executeValidation()" :disabled="isLoading">
                        <svg x-show="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Processing...' : 'Confirm'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div x-show="notificationOpen"
     x-transition
     style="display:none;"
     class="fixed bottom-6 right-6 z-[2000] text-white p-4 rounded-xl shadow-2xl flex items-center gap-4 border"
     :class="notificationType === 'error'
        ? 'bg-red-700 border-red-400'
        : 'bg-bsu-dark border-blue-400'">

    <div class="text-white rounded-full p-1.5 flex items-center justify-center"
         :class="notificationType === 'error' ? 'bg-red-500' : 'bg-green-500'">
        <svg x-show="notificationType !== 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>

        <svg x-show="notificationType === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <div>
        <p class="text-[10px] font-black uppercase tracking-widest"
           :class="notificationType === 'error' ? 'text-red-100' : 'text-blue-200'"
           x-text="notificationTitle"></p>

        <p class="text-xs font-bold mt-0.5" x-text="notificationMessage"></p>
    </div>

    <button @click="notificationOpen = false"
            class="ml-4 text-white/50 hover:text-white border-none bg-transparent cursor-pointer text-xl leading-none">
        &times;
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('assessmentData', (initialData = []) => ({
        searchQuery: '',
        sortOrder: 'newest',
        forValidation: initialData,

        selectedProtocol: null,

        activeView: 'revision_form',
        activeDocument: null,
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: '',
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,

        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',
        isLoading: false,
        confirmModalOpen: false,
        pendingAction: 'approve',
        secretariatComment: '',

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

        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',
        notificationType: 'success',
        notificationTimer: null,

        showNotification(title, message, type = 'success') {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationType = type;
            this.notificationOpen = true;

            if (this.notificationTimer) clearTimeout(this.notificationTimer);

            this.notificationTimer = setTimeout(() => {
                this.notificationOpen = false;
            }, 3500);
        },

        init() {
            window.revisionValidationAlpine = this;
        },

        get validationDocs() {
            return [{ key: 'revision_form', label: 'Resubmission Form' }];
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

        get isReviewerFormActive() {
            return this.validationDocs.some(d => d.key === this.activeDocument) ||
                   this.activeView === 'revision_form';
        },

        async openValidate(protocol) {
            this.selectedProtocol = protocol;
            this.activeDocument = 'revision_form';
            this.activeView = 'revision_form';
            this.activeDocKey = null;
            this.activeDocUrl = null;

            if (protocol.is_mock) {
                this.loadedDocs = {
                    activeBasic: [
                        { id: 'mock_doc_1', label: 'Study Protocol', url: '#', isRevised: true, desc: 'FULL PROPOSAL REVISED' },
                        { id: 'mock_doc_2', label: 'Informed Consent Form', url: '#', isRevised: false, desc: 'INFORMED CONSENT FORM' }
                    ],
                    activeSupp: [
                        { id: 'mock_doc_3', label: 'Questionnaire', url: '#', isRevised: false, desc: 'SURVEY QUESTIONNAIRE' }
                    ],
                    legacy: []
                };
                this.isLoadingDocs = false;
                return;
            }

            this.fetchDocuments(protocol);
        },

        async fetchDocuments(protocol) {
            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            const protocolCode = protocol.id;
            const revNum = protocol.version.replace('V', '');

            const parseFileName = (path) => {
                if (!path) return 'Document';
                let name = path.split('/').pop();
                name = name.split('?')[0];
                name = name.replace(/_\d{10}_\d+\.\w+$/, '');
                if (protocolCode) {
                    name = name.replace(`_${protocolCode}`, '');
                }
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            try {
                if (!protocolCode || !revNum) {
                    console.error('Missing protocolCode or revNum', { protocolCode, revNum, protocol });
                    return;
                }

                const response = await fetch(`/documents/api/revision/${protocolCode}/${revNum}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

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
                                isRevised,
                                desc: displayName
                            };

                            if (ts === maxTs || maxTs === 0) {
                                if (isBasic) tempDocs.activeBasic.push(obj);
                                else tempDocs.activeSupp.push(obj);
                            } else {
                                obj.isRevised = false;
                                tempDocs.legacy.push(obj);
                            }
                        });
                    });
                }

                this.loadedDocs = tempDocs;
            } catch (e) {
                console.error("Doc Load Error:", e);
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
            return classes.join(' ');
        },

        getClassificationTagClass(classification) {
            if (classification === 'Exempted') return 'text-blue-700 border-blue-200 bg-blue-50';
            if (classification === 'Expedited' || classification === 'Full Board') return 'text-red-700 border-red-200 bg-red-50';
            return 'text-bsu-dark border-bsu-dark/20 bg-gray-50';
        },

        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
            setTimeout(() => { this.notificationOpen = false; this.notificationMessage = ''; }, 3000);
        },

        openConfirmModal(action) {
            this.pendingAction = action;
            this.secretariatComment = '';
            this.confirmModalOpen = true;
        },

        closeConfirmModal() {
            this.confirmModalOpen = false;
        },

        async executeValidation() {
            if (!this.selectedProtocol || this.selectedProtocol.is_mock) {
                const successMsg = this.pendingAction === 'approve'
                    ? 'Resubmission validated. Sent to reviewers.'
                    : 'Resubmission rejected. Returned to researcher.';
                this.showNotification('Success', successMsg);
                this.closeConfirmModal();
                return;
            }

            this.isLoading = true;

            try {
                const response = await fetch('/api/secretariat/revision/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        protocol_code: this.selectedProtocol.id,
                        action: this.pendingAction,
                        secretariat_comment: this.secretariatComment
                    })
                });

                if (response.ok) {
                    const successMsg = this.pendingAction === 'approve'
                        ? 'Resubmission validated. Sent to reviewers.'
                        : 'Resubmission rejected. Returned to researcher.';

                    this.showNotification('Success', successMsg);
                    this.closeConfirmModal();
                    this.selectedProtocol = null;
                    this.activeDocument = null;
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    const errorData = await response.json();
                    this.showNotification('Error', errorData.message || 'Failed to process request.');
                    this.closeConfirmModal();
                }
            } catch (error) {
                console.error("Validation submission error:", error);
                this.showNotification('Error', 'An unexpected network error occurred.');
                this.closeConfirmModal();
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
            .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; max-width: 420px !important; }
            .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
            .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.6 !important; }
            .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
            .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; transition: all 0.2s ease !important; }
            .driver-popover-next-btn:hover { background-color: #b91c1c !important; }
            .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
            .driver-popover-prev-btn:hover { background-color: #E5E7EB !important; }
        `;
        document.head.appendChild(styleOverride);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runRevisionValidationTutorial(manual = false, retries = 0) {
        const isFirstLogin = @json(auth()->check() ? auth()->user()->is_first_login : true);
        const userId = @json(auth()->id() ?? 1);
        const storageKey = 'berc_tutorial_step_' + userId;

        const urlParams = new URLSearchParams(window.location.search);
        const forceTour = urlParams.get('tour') === '1';
        let tourState = localStorage.getItem(storageKey);

        if (tourState === 'secretariat_revision_validation_manual_skip') {
            localStorage.removeItem(storageKey);
            return;
        }

        if (manual || forceTour) {
            tourState = 'secretariat_revision_validation';
            localStorage.setItem(storageKey, tourState);
        }

        if (!manual && !forceTour && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!manual && !forceTour && tourState !== 'secretariat_revision_validation') {
            return;
        }

        if (window.__revisionValidationTourStarted) return;

        const rootEl = document.getElementById('revision-root');
        let alpine = null;

        try {
            alpine = window.revisionValidationAlpine ||
                rootEl?.__x?.$data ||
                rootEl?._x_dataStack?.[0] ||
                (window.Alpine ? window.Alpine.$data(rootEl) : null);
        } catch (e) {}

        if (!rootEl || !alpine || typeof window.driver === 'undefined') {
            if (retries < 40) {
                setTimeout(() => runRevisionValidationTutorial(manual, retries + 1), 250);
            } else {
                console.error('Tutorial aborted: Could not hook into Alpine or Driver.js.');
            }
            return;
        }

        window.__revisionValidationTourStarted = true;

        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));
        const driver = window.driver.js.driver;

        const mockProtocol = {
            is_mock: true,
            id: '2026-MOCK-REV-001',
            title: 'Effects of AI on System Architecture',
            proponent: 'Dr. Jane Doe',
            classification: 'Full Board',
            version: 'V2',
            dateSubmitted: '2026-04-18',
            revisionRows: [
                {
                    id: 'rev1',
                    item: '1.4',
                    item_display: '1.4 - Sampling methods',
                    section_and_page: 'Methodology, p. 12',
                    berc_recommendation: 'Clarify the sampling technique and justify why the selected participants are appropriate for the study objectives.',
                    researcher_response: 'We revised the methodology section to specify purposive sampling and added a justification explaining why the chosen participants are directly aligned with the study scope.'
                },
                {
                    id: 'rev2',
                    item: '3.2',
                    item_display: '3.2 - Privacy & Confidentiality',
                    section_and_page: 'Data Privacy Plan, p. 20',
                    berc_recommendation: 'Provide clearer safeguards for confidential data handling, storage, and limited access to participant information.',
                    researcher_response: 'We added an encrypted storage plan, restricted file access to the research team, and specified that raw participant data will be deleted after the retention period.'
                },
                {
                    id: 'rev3',
                    item: '4.5',
                    item_display: '4.5 - Risks',
                    section_and_page: 'Informed Consent Form, p. 3',
                    berc_recommendation: 'Rewrite the risks section in more participant-friendly language and state the possible discomforts more explicitly.',
                    researcher_response: 'The informed consent form was revised to use simpler language and now clearly explains possible discomforts and privacy-related risks.'
                }
            ]
        };

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {

                    if (manual) {
                        localStorage.setItem(storageKey, 'secretariat_revision_forms_manual_skip');
                        tour.destroy();
                        window.location.href = "{{ route('secretariat.revision_forms') }}";
                        return;
                    }

                    localStorage.setItem(storageKey, 'secretariat_revision_forms');
                    tour.destroy();
                    window.location.href = "{{ route('secretariat.revision_forms') }}";

                } else {
                    tour.destroy();
                }
            },

            onDestroyed: () => {
                alpine.selectedProtocol = null;
                alpine.activeDocument = null;
                alpine.activeDocKey = null;
                alpine.activeDocUrl = null;

                if (alpine.closeConfirmModal) {
                    alpine.closeConfirmModal();
                }

                window.__revisionValidationTourStarted = false;
            },

            steps: [
                {
                    element: '#tour-revision-list',
                    onHighlightStarted: async () => {
                        alpine.selectedProtocol = null;
                        alpine.activeDocument = null;
                        await wait(200);
                    },
                    popover: {
                        title: 'Resubmission Validation Queue',
                        description: 'This page lists protocols that were revised by the researcher after receiving committee recommendations. The Secretariat uses this queue to verify whether the submitted responses and revised documents are ready to move forward again.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-revision-row',
                    onHighlightStarted: async () => {
                        alpine.selectedProtocol = null;
                        alpine.activeDocument = null;
                        await wait(150);
                    },
                    popover: {
                        title: 'Revision Entry',
                        description: 'Each row shows the protocol code, study title, classification, revision version, and submission date. Clicking a row opens the validation dashboard for the selected resubmission.',
                        side: 'bottom',
                        align: 'start',
                        onNextClick: async () => {
                            await alpine.openValidate(mockProtocol);
                            setTimeout(() => tour.moveNext(), 350);
                        }
                    }
                },
                {
                    element: '#tour-revision-sidebar',
                    popover: {
                        title: 'Validation Sidebar',
                        description: 'The sidebar keeps the protocol details visible while you review the resubmission. From here, the Secretariat can switch between the researcher response form and the attached revised documents.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-revision-documents',
                    popover: {
                        title: 'Revised Documents',
                        description: 'This panel contains the updated protocol files. The Secretariat can inspect revised basic requirements, supplementary files, and compare the latest submission package against the committee’s requested changes.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-revision-preview',
                    popover: {
                        title: 'Researcher Response Form',
                        description: 'This main panel shows the item-by-item revision form. It pairs each BERC recommendation with the researcher’s written response and the referenced section/page where the changes were made.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-revision-footer',
                    popover: {
                        title: 'Validation Actions',
                        description: 'Once the Secretariat finishes checking the responses and revised documents, this footer provides the two final actions: approve the resubmission and return it to the reviewers, or reject it and send it back to the researcher.',
                        side: 'top',
                        align: 'center',
                        onNextClick: async () => {
                            alpine.openConfirmModal('approve');
                            setTimeout(() => tour.moveNext(), 250);
                        }
                    }
                },
                {
                    element: '#tour-confirm-modal',
                    popover: {
                        title: 'Confirmation Modal',
                        description: 'Before the action is finalized, this confirmation modal appears. It allows the Secretariat to confirm whether the resubmission should be approved or rejected, and optionally add a comment explaining the decision.',
                        side: 'left',
                        align: 'center',
                        onNextClick: async () => {
                            alpine.closeConfirmModal();
                            setTimeout(() => tour.moveNext(), 200);
                        }
                    }
                },
                {
                    popover: {
                        title: 'Next Step: Revision Forms',
                        description: 'After validating the resubmission, the next page is Revision Forms, where the updated protocol is routed back into the reviewer-side revision workflow for further evaluation and form completion.',
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
        loadDriverThenRun(() => runRevisionValidationTutorial(true));
    };

    loadDriverThenRun(() => runRevisionValidationTutorial(false));
});
</script>
@endsection
