@extends('secretariat.layouts.app')

@section('content')
<style>
    /* Force vertical scrollbar to prevent page layout shift */
    html { overflow-y: scroll; }
    [x-cloak] { display: none !important; }

    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    /* Layout Components */
    .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); position: relative; }
    .card-header { display:flex; align-items:center; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .card-tab { font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:var(--bsu-dark); border-bottom:3px solid var(--brand-red); background:#fff; padding:14px 20px; display: flex; gap: 8px; align-items: center;}

    /* List Grid */
    .assessment-grid-header, .assessment-row {
        display: grid;
        grid-template-columns: minmax(140px, 1fr) minmax(300px, 2.5fr) minmax(180px, 1.5fr) minmax(140px, 1fr);
        padding: 14px 20px;
        align-items: center;
        gap: 12px;
    }
    .assessment-grid-header { background: #f3f4f6; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; padding-top: 10px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb; }
    .assessment-row { border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background .15s; }
    .assessment-row:hover { background: #f9fafb; }
    .assessment-grid-header > div:last-child, .assessment-row > div:last-child { text-align: center; }

    .app-id-badge { display: inline-flex; align-items: center; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; font-weight: 800; font-family: monospace; letter-spacing: 0.03em; padding: 4px 9px; border-radius: 6px; white-space: nowrap; }
    .workflow-action-link { font-size:11px; font-weight:800; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; transition:color 0.15s; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }

    /* Modal Styling */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(4px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:16px; width:100%; max-width:1500px; height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); animation: lbIn .2s ease; position:relative; }
    @keyframes lbIn { from { opacity:0; transform: scale(.97); } to { opacity:1; transform: scale(1); } }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .modal-header h2 { font-size:16px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
    .close-btn { font-size:24px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; border-radius:6px; transition:color .15s; }
    .close-btn:hover { color:#111; }

    .modal-content { display:flex; flex:1; min-height:0; overflow:hidden; }

    /* Left Panel */
    .protocol-info-panel { width:280px; min-width:280px; border-right:1px solid #e5e7eb; padding:24px; background:#fafafa; overflow-y:auto; flex-shrink:0; }
    .info-group { margin-bottom:16px; }
    .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
    .info-value { font-size:13px; font-weight:700; color:#111827; }

    .sidebar-nav-item { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 16px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; transition: all 0.2s; color: #64748b; margin-bottom: 4px; cursor: pointer; border:none; background: transparent; text-align: left; }
    .sidebar-nav-item.active { background: #213C71; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .sidebar-nav-item:hover:not(.active) { background: #f1f5f9; color: #1e293b; }

    /* Right Panel & Table */
    .form-preview-panel { flex:1; overflow:hidden; background:#f8fafc; position: relative; display: flex; flex-direction: column; }
    .form-preview-panel.viewer-active { padding: 0 !important; }
    .sticky-header th { position: sticky; top: 0; background: #f8fafc; z-index: 10; border-bottom: 2px solid #e2e8f0; }

    /* Footer Buttons */
    .modal-footer { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:14px 24px; border-top:1px solid #e5e7eb; background:#fafafa; z-index: 10; }
    .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
    .btn:active { transform:scale(.97); }
    .btn-primary { background:#c21c2c; color:#fff; }
    .btn-primary:hover:not(:disabled) { opacity:.9; }
    .btn-primary:disabled { background:#9ca3af; cursor:not-allowed; }
</style>

<div id="revision-forms-root" x-data="resubmissionData(@js($protocolsData ?? []))" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Resubmission Form Validation</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Secretariat Synthesis for completed revision reviews</p>
        </div>
        <div class="w-full max-w-sm relative">
            <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <div id="tour-revision-forms-list" class="app-card">
        <div class="card-header">
            <div class="card-tab">
                Ready for Synthesis
                <span x-show="filteredData.length > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="filteredData.length"></span>
            </div>
        </div>

        <div>
            <div class="assessment-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Review Status</div>
                <div>Action</div>
            </div>

            <template x-for="(protocol, index) in filteredData" :key="protocol.id + protocol.version">
                <div :id="index === 0 ? 'tour-first-revision-forms-row' : null" class="assessment-row" @click="openValidate(protocol)">
                    <div>
                        <span class="app-id-badge" x-text="protocol.id"></span>
                        <div class="mt-1 bg-blue-50 text-bsu-dark px-2 py-0.5 rounded text-[10px] font-black w-max border border-blue-200" x-text="protocol.version"></div>
                    </div>
                    <div>
                        <div class="font-bold text-[13px] text-gray-900 leading-snug" x-text="protocol.title"></div>
                        <div class="text-[11px] text-gray-500 font-bold mt-1 uppercase" x-text="protocol.proponent"></div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded border bg-green-50 text-green-700 border-green-200">Reviewers Finished</span>
                        <div class="text-[10px] text-gray-500 font-bold mt-1" x-text="'Updated: ' + protocol.dateSubmitted"></div>
                    </div>
                    <div>
                        <span class="workflow-action-link"><u>Synthesize</u></span>
                    </div>
                </div>
            </template>

            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No completed resubmissions pending synthesis.
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="selectedProtocol ? 'open' : ''">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <h2>Synthesis Dashboard</h2>
                    <span class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-700 text-[11px] font-bold font-mono tracking-[0.03em] px-2.5 py-1 rounded-md" x-text="selectedProtocol?.id + ' • ' + selectedProtocol?.version"></span>
                </div>
                <button class="close-btn" @click="closeModal()">&times;</button>
            </div>

            <div class="modal-content">

                <div id="tour-revision-forms-sidebar" class="protocol-info-panel">
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

                    <div style="margin-top:30px;">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Review Components</h3>
                        <nav class="space-y-1">
                            <button id="tour-nav-resubmission-form" @click="activeView = 'resubmission_form'" :class="activeView === 'resubmission_form' ? 'active' : ''" class="sidebar-nav-item">
                                <span>📋</span> Resubmission Form
                            </button>
                        </nav>
                    </div>

                    <div id="tour-revision-forms-documents" style="margin-top:30px;">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Protocol Documents</h3>
                        <div class="space-y-2">
                            <template x-if="isLoadingDocs">
                                <div class="text-[10px] text-gray-400 italic py-2">Loading documents...</div>
                            </template>

                            <template x-if="!isLoadingDocs">
                                <div class="space-y-4">
                                    <div x-show="loadedDocs.activeBasic.length > 0">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Basic Requirements</div>
                                        <template x-for="doc in loadedDocs.activeBasic" :key="doc.id">
                                            <div @click="viewDocument(doc)"
                                                :class="activeDoc?.id === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
                                                class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-start gap-3 shadow-sm mb-2">
                                                <div class="text-xl leading-none text-[#D32F2F]">📄</div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-[11px] font-bold text-slate-700 leading-tight flex items-center flex-wrap gap-1">
                                                        <span x-text="doc.label"></span>
                                                        <template x-if="doc.isRevised">
                                                            <span class="bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded text-[8px] font-black border border-yellow-300">REVISED</span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[9px] text-blue-600 font-bold mt-1 break-all leading-snug" x-text="doc.desc"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="loadedDocs.activeSupp.length > 0">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Supplementary Docs</div>
                                        <template x-for="doc in loadedDocs.activeSupp" :key="doc.id">
                                            <div @click="viewDocument(doc)"
                                                :class="activeDoc?.id === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
                                                class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-start gap-3 shadow-sm mb-2">
                                                <div class="text-xl leading-none text-[#D32F2F]">📄</div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-[11px] font-bold text-slate-700 leading-tight flex items-center flex-wrap gap-1">
                                                        <span x-text="doc.label"></span>
                                                        <template x-if="doc.isRevised">
                                                            <span class="bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded text-[8px] font-black border border-yellow-300">REVISED</span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[9px] text-blue-600 font-bold mt-1 break-all leading-snug" x-text="doc.desc"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="loadedDocs.activeBasic.length === 0 && loadedDocs.activeSupp.length === 0" class="text-[10px] text-gray-400 italic">
                                        No revised documents found for this version.
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="tour-revision-forms-preview" class="form-preview-panel" :class="activeView === 'doc_viewer' ? 'viewer-active' : ''">

                    <div x-show="activeView === 'resubmission_form'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col m-4">
                        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                            <h2 class="text-sm font-black text-bsu-dark uppercase tracking-wide">Synthesize Reviewer Feedback</h2>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            <table id="tour-revision-forms-table" class="w-full text-left border-collapse table-fixed">
                                <thead class="sticky-header shadow-sm">
                                    <tr>
                                        <th class="w-[18%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 bg-gray-50">Recommendation & Response</th>
                                        <th class="w-[25%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 bg-gray-50 border-l border-gray-200">Reviewers' Remarks</th>
                                        <th class="w-[35%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 bg-gray-50 border-l border-gray-200">Secretariat Synthesis</th>
                                        <th class="w-[15%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-center bg-gray-50 border-l border-gray-200">Final Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="(row, index) in selectedProtocol?.revisionRows" :key="row.id">
                                        <tr class="transition-colors duration-300 align-top"
                                            :class="{
                                                'bg-white hover:bg-gray-50': !row.action,
                                                'bg-green-50/50': row.action === 'resolved',
                                                'bg-red-50/50': row.action === 'action_required'
                                            }">

                                            <td class="px-4 py-5 align-top whitespace-normal">
                                                <div class="mb-2 flex flex-wrap gap-1">
                                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded border inline-block bg-gray-100 text-gray-600 border-gray-200 break-words whitespace-normal max-w-full">
                                                        <span x-text="row.item_display"></span>
                                                    </span>
                                                    <span class="text-[10px] font-black px-2 py-0.5 rounded border inline-block bg-blue-50 text-bsu-dark border-blue-200 break-words whitespace-normal max-w-full"
                                                        x-text="row.section_and_page"></span>
                                                </div>

                                                <div class="text-[11px] text-gray-500 italic mb-2 break-words whitespace-normal">
                                                    <b class="text-gray-700">BERC:</b>
                                                    <span class="break-words whitespace-normal" x-text="row.berc_recommendation"></span>
                                                </div>

                                                <div class="text-[11px] font-bold text-bsu-dark break-words whitespace-normal">
                                                    <b class="text-gray-900">Researcher:</b>
                                                    <span class="break-words whitespace-normal" x-text="row.researcher_response"></span>
                                                </div>
                                            </td>

                                            <td class="px-4 py-5 border-l border-gray-200">
                                                <div class="text-[10px] text-gray-700 leading-relaxed" x-html="row.reviewers_remarks"></div>
                                            </td>

                                            <td class="px-4 py-5 border-l border-gray-200">
                                                <textarea x-model="row.synthesizedComments"
                                                          @input="triggerAutosave(); autoGrow($event.target)"
                                                          x-init="$nextTick(() => autoGrow($el))"
                                                          class="w-full border rounded-lg p-2.5 text-[11px] focus:outline-none focus:ring-2 resize-y min-h-[60px] transition-all shadow-inner"
                                                          :class="{
                                                              'bg-white border-gray-200 focus:ring-bsu-dark/20 focus:border-bsu-dark text-gray-700': !row.action,
                                                              'bg-green-50 border-green-300 focus:ring-green-500/20 focus:border-green-500 text-green-800 placeholder-green-600/50': row.action === 'resolved',
                                                              'bg-red-50 border-red-300 focus:ring-red-500/20 focus:border-red-500 text-red-800 placeholder-red-600/50': row.action === 'action_required'
                                                          }"
                                                          placeholder="Type the finalized official comments here..."></textarea>
                                            </td>

                                            <td class="px-4 py-5 border-l border-gray-200 text-center align-middle">
                                                <div class="relative w-full group">
                                                    <select x-model="row.action"
                                                            @change="triggerAutosave()"
                                                            class="appearance-none w-full border-2 rounded-lg pl-2 pr-7 py-2.5 text-[10px] font-black uppercase outline-none transition-all shadow-sm cursor-pointer truncate"
                                                            :class="{
                                                                'border-gray-200 bg-white text-gray-500 hover:border-gray-300 focus:border-bsu-dark focus:ring-4 focus:ring-bsu-dark/10': !row.action,
                                                                'border-green-500 bg-green-100 text-green-700 focus:ring-4 focus:ring-green-500/20': row.action === 'resolved',
                                                                'border-red-500 bg-red-100 text-brand-red focus:ring-4 focus:ring-red-500/20': row.action === 'action_required'
                                                            }">
                                                        <option value="">Select Action</option>
                                                        <option value="resolved" class="text-green-700 font-bold bg-white">✓ Resolved</option>
                                                        <option value="action_required" class="text-brand-red font-bold bg-white">⚠ Action Required</option>
                                                    </select>

                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5"
                                                         :class="{
                                                             'text-gray-400 group-hover:text-gray-600': !row.action,
                                                             'text-green-600': row.action === 'resolved',
                                                             'text-brand-red': row.action === 'action_required'
                                                         }">
                                                        <svg class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="activeView === 'doc_viewer'" x-cloak class="absolute inset-0 bg-slate-800 flex flex-col z-50">
                        <div class="bg-white px-5 py-3 border-b border-gray-200 flex justify-between items-center shadow-md">
                            <div class="flex flex-col">
                                <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDoc?.label"></h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Digital Document Preview</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a :href="activeDoc?.url" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                <button @click="activeView = 'resubmission_form'; activeDoc = null" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
                                    Close Preview
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 relative w-full h-full">
                            <template x-if="activeDoc?.url">
                                <iframe :src="activeDoc.url" class="w-full h-full border-none bg-white"></iframe>
                            </template>
                            <div x-show="!activeDoc?.url" class="absolute inset-0 flex flex-col items-center justify-center text-white opacity-60">
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

            <div id="tour-revision-forms-footer" class="modal-footer">
                <div class="flex items-center gap-4">
                    <button class="btn btn-outline" @click="closeModal()" style="border:1px solid #d1d5db; color:#374151;">Cancel</button>
                    <span x-text="draftStatusMsg" class="text-[10px] font-bold text-green-600 transition-opacity duration-300"></span>
                </div>
                <button class="btn btn-primary shadow-sm flex items-center gap-2" @click="submitSynthesis" :disabled="isLoading">
                    <svg x-show="isLoading" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isLoading ? 'Saving...' : 'Save Final Synthesis'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="notificationOpen" style="display:none;" class="fixed bottom-6 right-6 z-[2000] bg-bsu-dark text-white p-4 rounded-xl shadow-2xl flex items-center gap-4 border border-blue-400">
        <div class="bg-green-500 text-white rounded-full p-1.5 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-200" x-text="notificationTitle"></p>
            <p class="text-xs font-bold mt-0.5" x-text="notificationMessage"></p>
        </div>
        <button @click="notificationOpen = false" class="ml-4 text-white/50 hover:text-white border-none bg-transparent cursor-pointer text-xl leading-none">&times;</button>
    </div>

    <div x-show="confirmSubmitOpen" x-cloak class="fixed inset-0 z-[2100] flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
                <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Confirm Final Synthesis Submission</h3>
                <p class="text-[11px] text-gray-500 font-semibold mt-1" x-show="!hasMissingFinalAction">
                    Some rows still have blank synthesis comments. You may still continue if you confirm.
                </p>

                <p class="text-[11px] text-red-600 font-semibold mt-1" x-show="hasMissingFinalAction">
                    Some rows are still missing a Final Action. You cannot submit until all Final Action fields are completed.
                </p>
            </div>

            <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                <template x-if="missingInputItems.length > 0">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-red-600 mb-3">
                            Missing inputs found in these rows:
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full border-collapse">
                                <thead class="bg-gray-50">
                                    <tr class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                        <th class="px-4 py-3 text-left w-32">Item</th>
                                        <th class="px-4 py-3 text-left">Section/Page</th>
                                        <th class="px-4 py-3 text-left w-48">Missing Fields</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in missingInputItems" :key="item.id">
                                        <tr>
                                            <td class="px-4 py-3 text-[11px] font-black text-bsu-dark whitespace-normal break-words"
                                                x-text="item.item_display"></td>
                                            <td class="px-4 py-3 text-[11px] text-gray-700"
                                                x-text="item.section_and_page || '—'"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="part in item.missingParts" :key="part">
                                                        <span class="px-2 py-1 rounded-md text-[10px] font-black border"
                                                            :class="part === 'Final Action'
                                                                ? 'bg-red-100 text-red-800 border-red-300'
                                                                : 'bg-yellow-50 text-yellow-800 border-yellow-300'"
                                                            x-text="part"></span>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template x-if="missingInputItems.length === 0">
                    <div class="text-sm font-bold text-green-700">
                        No missing inputs detected.
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-slate-50 flex justify-end gap-3">
                <button @click="closeSubmitConfirmation()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors uppercase border-none bg-transparent cursor-pointer">
                    Go Back
                </button>

                <button @click="confirmAndSubmitSynthesis()"
                        :disabled="isLoading || hasMissingFinalAction"
                        class="bg-[#D32F2F] text-white px-6 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirm Submit
                </button>
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
    Alpine.data('resubmissionData', (initialData = []) => ({
        searchQuery: '',
        forValidation: initialData,

        selectedProtocol: null,
        activeView: 'resubmission_form',
        activeDocKey: null,
        activeDoc: null,

        // Document State
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,

        isLoading: false,
        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',
        confirmSubmitOpen: false,
        missingInputItems: [],

        // AutoSave Draft State
        autosaveTimer: null,
        draftStatusMsg: '',

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
            'manuscript', 'questionnaire', 'data_collection', 'product_brochure',
            'philippine_fda'
        ],

        init() {
            window.revisionFormsAlpine = this;
        },

        get filteredData() {
            return this.forValidation.filter(p => {
                const s = this.searchQuery.toLowerCase();
                return (p.id || '').toLowerCase().includes(s) ||
                       (p.title || '').toLowerCase().includes(s) ||
                       (p.proponent || '').toLowerCase().includes(s);
            });
        },

        async openValidate(protocol) {
            if (protocol.is_mock) {
                protocol.revisionRows.forEach(r => {
                    if (typeof r.synthesizedComments === 'undefined') r.synthesizedComments = '';
                    if (typeof r.action === 'undefined') r.action = '';
                });

                this.selectedProtocol = protocol;
                this.activeView = 'resubmission_form';
                this.activeDoc = null;
                this.activeDocKey = null;
                this.draftStatusMsg = '';
                document.body.style.overflow = 'hidden';

                this.loadedDocs = {
                    activeBasic: [
                        { id: 'mock_doc_1', label: 'Study Protocol', url: '#', isRevised: true, desc: 'FULL PROPOSAL REVISED' },
                        { id: 'mock_doc_2', label: 'Informed Consent Form', url: '#', isRevised: true, desc: 'INFORMED CONSENT FORM REVISED' }
                    ],
                    activeSupp: [
                        { id: 'mock_doc_3', label: 'Questionnaire', url: '#', isRevised: true, desc: 'SURVEY QUESTIONNAIRE V2' }
                    ],
                    legacy: []
                };
                this.isLoadingDocs = false;
                return;
            }

            protocol.revisionRows.forEach(r => {
                if(typeof r.synthesizedComments === 'undefined') r.synthesizedComments = '';
                if(typeof r.action === 'undefined') r.action = '';
            });

            this.selectedProtocol = protocol;
            this.activeView = 'resubmission_form';
            this.activeDoc = null;
            this.activeDocKey = null;
            this.draftStatusMsg = '';
            document.body.style.overflow = 'hidden';

            const revNum = protocol.version.replace('V', '');

            try {
                const draftRes = await fetch(`/api/secretariat/resubmission/${protocol.id}/v${revNum}/draft`);
                if (draftRes.ok) {
                    const draftData = await draftRes.json();

                    if (draftData && draftData.rows) {
                        this.selectedProtocol.revisionRows.forEach(r => {
                            const dRow = draftData.rows.find(dr => dr.id === r.id);
                            if (dRow) {
                                r.synthesizedComments = dRow.synthesized_comments;
                                r.action = dRow.synthesized_comments_action;
                            }
                        });
                        this.draftStatusMsg = 'Restored from saved draft.';
                        setTimeout(() => this.draftStatusMsg = '', 4000);
                    }
                }
            } catch (e) {
                console.warn('No draft found or error loading draft', e);
            }

            const parseFileName = (path) => {
                if (!path) return 'Document';
                let name = path.split('/').pop();
                name = name.replace(/_\d{10}_\d+\.\w+$/, '');
                if (protocol.id) name = name.replace(`_${protocol.id}`, '');
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            try {
                const docResponse = await fetch(`/documents/api/revision/${protocol.id}/${revNum}`);
                if (docResponse.ok) {
                    const data = await docResponse.json();
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const files = data.documents[type];
                            if (!files || !Array.isArray(files) || files.length === 0) return;

                            const isBasic = this.basicTypes.includes(type);
                            const titleLabel = this.docLabels[type] || type.replace(/_/g, ' ').toUpperCase();

                            files.forEach(file => {
                                const pathText = file.url || '';
                                const hasRealDesc = file.description && file.description !== 'View File' && file.description !== '';
                                const displayName = hasRealDesc ? file.description : parseFileName(pathText);
                                const uniqueId = `${type}_${file.id}_${Math.random().toString(36).substr(2, 9)}`;

                                const obj = {
                                    id: uniqueId,
                                    label: titleLabel,
                                    url: file.url,
                                    isRevised: true,
                                    desc: displayName
                                };

                                if (isBasic) tempDocs.activeBasic.push(obj);
                                else tempDocs.activeSupp.push(obj);
                            });
                        });
                    }
                    this.loadedDocs = tempDocs;
                }
            } catch (e) {
                console.error("API Doc Load Error:", e);
            } finally {
                this.isLoadingDocs = false;
            }
        },

        viewDocument(doc) {
            this.activeDoc = doc;
            this.activeDocKey = doc.id;
            this.activeView = 'doc_viewer';
        },

        autoGrow(el) {
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = `${el.scrollHeight}px`;
        },

        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
            setTimeout(() => this.notificationOpen = false, 4000);
        },

        closeModal() {
            this.selectedProtocol = null;
            this.activeDoc = null;
            this.activeDocKey = null;
            document.body.style.overflow = '';
        },

        triggerAutosave() {
            this.draftStatusMsg = 'Saving...';
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.saveDraft(), 1500);
        },

        async saveDraft() {
            if (!this.selectedProtocol || this.selectedProtocol.is_mock) {
                const now = new Date();
                let hours = now.getHours();
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                this.draftStatusMsg = `Draft saved at ${hours}:${minutes} ${ampm}`;
                return;
            }

            const protocolId = this.selectedProtocol.id;
            const revNum = this.selectedProtocol.version.replace('V', '');

            const payload = {
                protocol_code: protocolId,
                revision_number: revNum,
                rows: this.selectedProtocol.revisionRows.map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    synthesized_comments_action: row.action
                }))
            };

            try {
                const response = await fetch(`/api/secretariat/resubmission/${protocolId}/v${revNum}/draft`, {
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
                console.error("Draft Save Error:", e);
                this.draftStatusMsg = 'Offline - Could not save draft.';
            }
        },

        getMissingInputItems() {
            const missing = [];

            (this.selectedProtocol?.revisionRows || []).forEach(row => {
                const missingParts = [];

                if (!(row.synthesizedComments || '').trim()) {
                    missingParts.push('Synthesis');
                }

                if (!row.action) {
                    missingParts.push('Final Action');
                }

                if (missingParts.length > 0) {
                    missing.push({
                        id: row.id,
                        item_display: row.item_display || 'Requirement',
                        section_and_page: row.section_and_page || '',
                        missingParts: missingParts
                    });
                }
            });

            return missing;
        },

        openSubmitConfirmation() {
            this.missingInputItems = this.getMissingInputItems();
            this.confirmSubmitOpen = true;
        },

        closeSubmitConfirmation() {
            this.confirmSubmitOpen = false;
        },

        async confirmAndSubmitSynthesis() {
            if (this.hasMissingFinalAction) {
                return;
            }

            this.confirmSubmitOpen = false;
            await this.finalSubmitSynthesis();
        },

        get hasMissingFinalAction() {
            return (this.missingInputItems || []).some(item =>
                (item.missingParts || []).includes('Final Action')
            );
        },

        submitSynthesis() {
            if (!this.selectedProtocol || this.isLoading) return;
            this.openSubmitConfirmation();
        },

        async finalSubmitSynthesis() {
            const incompleteRows = this.selectedProtocol.revisionRows.filter(r => !r.action);

            // Block submission and push an error notification if items are missing
            if (incompleteRows.length > 0) {
                this.showNotification(
                    'Validation Error',
                    `Please select a final action for all ${incompleteRows.length} remaining item(s).`,
                    'error'
                );
                return;
            }

            if (this.selectedProtocol.is_mock) {
                this.showNotification('Success', 'Resubmission synthesis saved successfully.', 'success');
                this.closeModal();
                return;
            }

            this.isLoading = true;

            const payload = {
                protocol_code: this.selectedProtocol.id,
                revision_number: this.selectedProtocol.version.replace('V', ''),
                rows: this.selectedProtocol.revisionRows.map(row => ({
                    id: row.id,
                    synthesized_comments: row.synthesizedComments,
                    synthesized_comments_action: row.action
                }))
            };

            try {
                const response = await fetch('/api/secretariat/resubmission/synthesis/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    this.showNotification('Success', 'Resubmission synthesis saved successfully.', 'success');
                    this.closeModal();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    const errorData = await response.json();
                    this.showNotification('Server Error', errorData.message || 'Failed to save synthesis.', 'error');
                }
            } catch (error) {
                console.error("Submission error:", error);
                this.showNotification('Network Error', 'An unexpected network error occurred.', 'error');
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

    function runRevisionFormsTutorial(manual = false, retries = 0) {
        const isFirstLogin = @json(auth()->check() ? auth()->user()->is_first_login : true);
        const userId = @json(auth()->id() ?? 1);
        const storageKey = 'berc_tutorial_step_' + userId;

        const urlParams = new URLSearchParams(window.location.search);
        const forceTour = urlParams.get('tour') === '1';
        let tourState = localStorage.getItem(storageKey);

        if (tourState === 'secretariat_revision_forms_manual_skip') {
            localStorage.removeItem(storageKey);
            return;
        }

        if (manual || forceTour) {
            tourState = 'secretariat_revision_forms';
            localStorage.setItem(storageKey, tourState);
        }

        if (!manual && !forceTour && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!manual && !forceTour && tourState !== 'secretariat_revision_forms') {
            return;
        }

        if (window.__revisionFormsTourStarted) return;

        const rootEl = document.getElementById('revision-forms-root');
        let alpine = null;

        try {
            alpine = window.revisionFormsAlpine ||
                rootEl?.__x?.$data ||
                rootEl?._x_dataStack?.[0] ||
                (window.Alpine ? window.Alpine.$data(rootEl) : null);
        } catch (e) {}

        if (!rootEl || !alpine || typeof window.driver === 'undefined') {
            if (retries < 40) {
                setTimeout(() => runRevisionFormsTutorial(manual, retries + 1), 250);
            } else {
                console.error('Tutorial aborted: Could not hook into Alpine or Driver.js.');
            }
            return;
        }

        window.__revisionFormsTourStarted = true;

        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));
        const driver = window.driver.js.driver;

        const mockProtocol = {
            is_mock: true,
            id: '2026-MOCK-REVFORM-001',
            version: 'V2',
            title: 'Effects of AI on System Architecture',
            proponent: 'Dr. Jane Doe',
            dateSubmitted: '2026-04-19',
            revisionRows: [
                {
                    id: 'mock_row_1',
                    section_and_page: 'Methodology, p. 12',
                    berc_recommendation: 'Clarify the participant sampling strategy and provide a stronger justification for the selected sampling method.',
                    researcher_response: 'We revised the methodology to specify purposive sampling and added a justification that directly aligns participant selection with the study objectives.',
                    reviewers_remarks: '<b>Reviewer 1:</b> Revision is mostly acceptable, but the explanation of inclusion criteria can still be simplified.<br><br><b>Reviewer 2:</b> Sampling justification is clearer now.',
                    synthesizedComments: '',
                    action: ''
                },
                {
                    id: 'mock_row_2',
                    section_and_page: 'Data Privacy Plan, p. 20',
                    berc_recommendation: 'Provide stronger confidentiality safeguards, including storage restrictions and retention period.',
                    researcher_response: 'An encrypted storage plan was added, with access limited to the research team. Raw participant data will be deleted after the stated retention period.',
                    reviewers_remarks: '<b>Reviewer 1:</b> Privacy safeguards are improved and acceptable.<br><br><b>Reviewer 2:</b> Retention period is now clear.',
                    synthesizedComments: '',
                    action: ''
                },
                {
                    id: 'mock_row_3',
                    section_and_page: 'Informed Consent Form, p. 3',
                    berc_recommendation: 'Rewrite the risk section using clearer, participant-friendly language.',
                    researcher_response: 'The consent form was revised to simplify the language and explicitly state the possible discomforts and privacy risks.',
                    reviewers_remarks: '<b>Reviewer 1:</b> Risk language is improved, but one sentence is still too technical.<br><br><b>Reviewer 2:</b> Mostly resolved.',
                    synthesizedComments: '',
                    action: ''
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
                        localStorage.setItem(storageKey, 'secretariat_revision_decision_manual_skip');
                        tour.destroy();
                        window.location.href = "{{ route('secretariat.revision.decision') }}";
                        return;
                    }

                    localStorage.setItem(storageKey, 'secretariat_revision_decision');
                    tour.destroy();
                    window.location.href = "{{ route('secretariat.revision.decision') }}";

                } else {
                    tour.destroy();
                }
            },

            onDestroyed: () => {
                if (alpine.closeModal) {
                    alpine.closeModal();
                }

                window.__revisionFormsTourStarted = false;
            },

            steps: [
                {
                    element: '#tour-revision-forms-list',
                    onHighlightStarted: async () => {
                        alpine.closeModal();
                        await wait(200);
                    },
                    popover: {
                        title: 'Revision Synthesis Queue',
                        description: 'This page lists revised protocols whose reviewer re-checks are already complete. The Secretariat now prepares the final synthesis that summarizes whether each revision point is resolved or still requires action.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-revision-forms-row',
                    onHighlightStarted: async () => {
                        alpine.closeModal();
                        await wait(150);
                    },
                    popover: {
                        title: 'Completed Revision Entry',
                        description: 'Each row represents one completed revised submission. It shows the protocol code, revision version, updated study information, and confirms that reviewer feedback is finished and ready for Secretariat synthesis.',
                        side: 'bottom',
                        align: 'start',
                        onNextClick: async () => {
                            await alpine.openValidate(mockProtocol);
                            setTimeout(() => tour.moveNext(), 350);
                        }
                    }
                },
                {
                    element: '#tour-revision-forms-sidebar',
                    popover: {
                        title: 'Synthesis Sidebar',
                        description: 'The sidebar keeps the protocol identity visible and provides access to the resubmission form plus the revised protocol documents. This helps the Secretariat cross-check reviewer feedback against the actual revised files.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nav-resubmission-form',
                    popover: {
                        title: 'Resubmission Form View',
                        description: 'This is the main working view for the Secretariat. It displays the recommendation, the researcher response, the reviewer remarks after re-check, and the field where the Secretariat writes the final synthesis.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-revision-forms-documents',
                    popover: {
                        title: 'Revised Documents Panel',
                        description: 'These are the current documents submitted for this revised version. The Secretariat can inspect the updated basic and supplementary files to confirm that the researcher’s written response matches the actual document changes.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-revision-forms-preview',
                    popover: {
                        title: 'Synthesis Workspace',
                        description: 'This workspace is where the Secretariat consolidates the completed reviewer remarks into one final official synthesis. Each row is assessed and given a final action, such as Resolved or Action Required.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-revision-forms-table',
                    popover: {
                        title: 'Per-Item Review Table',
                        description: 'Each row brings together four pieces of information: the original committee recommendation, the researcher’s response, the reviewer remarks after re-check, and the Secretariat’s final synthesis with a decision action. This is the core validation table for revised submissions.',
                        side: 'left',
                        align: 'center',
                        onNextClick: async () => {
                            if (alpine.selectedProtocol?.revisionRows?.length) {
                                alpine.selectedProtocol.revisionRows[0].synthesizedComments = 'The revised methodology is substantially improved. Inclusion criteria wording may still be simplified, but the sampling justification is acceptable.';
                                alpine.selectedProtocol.revisionRows[0].action = 'resolved';
                                alpine.triggerAutosave();

                                alpine.selectedProtocol.revisionRows[1].synthesizedComments = 'The confidentiality safeguards are now clearly stated and are acceptable for reviewer endorsement.';
                                alpine.selectedProtocol.revisionRows[1].action = 'resolved';
                                alpine.triggerAutosave();

                                alpine.selectedProtocol.revisionRows[2].synthesizedComments = 'The consent form language is improved, but one sentence remains too technical. Minor revision is still required for complete participant clarity.';
                                alpine.selectedProtocol.revisionRows[2].action = 'action_required';
                                alpine.triggerAutosave();
                            }

                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    element: '#tour-revision-forms-footer',
                    popover: {
                        title: 'Save Final Synthesis',
                        description: 'Once all items are reviewed, this action saves the final Secretariat synthesis for the revised submission. After this step, the workflow proceeds to the revision decision page, where the final outcome is prepared.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    popover: {
                        title: 'Next Step: Revision Decision',
                        description: 'You have now seen the full synthesis workflow for completed revised submissions. The next page is Revision Decision, where the final decision letter for the revised protocol is prepared.',
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
        loadDriverThenRun(() => runRevisionFormsTutorial(true));
    };

    loadDriverThenRun(() => runRevisionFormsTutorial(false));
});
</script>
@endsection
