@extends('secretariat.layouts.app')

@section('content')
<style>
    /* Layout & Scroll Fixes */
    html { overflow-y: scroll; }
    [x-cloak] { display: none !important; }
    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    /* Main Container & Tabs */
    .app-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); position: relative; }
    .card-header { display:flex; align-items:center; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .card-tab { font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; border-bottom:3px solid transparent; padding:14px 20px; display: flex; gap: 8px; align-items: center; cursor: pointer; transition: all 0.2s;}
    .card-tab.active { color:var(--bsu-dark); border-bottom-color:var(--brand-red); background:#fff; }

    /* Grid Layouts */
    .list-grid-header, .app-row { display: grid; grid-template-columns: minmax(120px, 0.9fr) minmax(220px, 1.8fr) minmax(180px, 1.2fr) minmax(250px, 2fr) minmax(170px, 1.1fr) minmax(120px, 0.8fr); padding: 8px 20px; align-items: center; gap: 12px; }
    .awaiting-grid-header, .awaiting-row { display: grid; grid-template-columns: minmax(120px, 0.9fr) minmax(220px, 1.8fr) minmax(180px, 1.2fr) minmax(250px, 2fr) minmax(180px, 1.15fr) minmax(170px, 1.05fr); padding: 8px 20px; align-items: center; gap: 12px; }

    .list-grid-header, .awaiting-grid-header { background: #f3f4f6; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
    .list-grid-header > div, .awaiting-grid-header > div { text-align: left; }
    .list-grid-header > div.header-center, .awaiting-grid-header > div.header-center { text-align: center; }

    .app-row, .awaiting-row { padding: 14px 20px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background .15s; }
    .app-row:hover, .awaiting-row:hover { background: #f9fafb; }
    .app-row:last-child, .awaiting-row:last-child { border-bottom: none; }

    .app-id-badge { display: inline-flex; align-items: center; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; font-weight: 800; font-family: monospace; letter-spacing: 0.03em; padding: 4px 9px; border-radius: 6px; white-space: nowrap; }
    .app-row-title { font-size:13px; font-weight:700; color:#111827; }
    .app-row-sub { font-size:11px; color:#6b7280; margin-top:2px; }
    .workflow-action-link { font-size:11px; font-weight:700; color:var(--brand-red); text-decoration:underline; text-underline-offset:2px; cursor:pointer; }
    .workflow-action-link:hover { color:var(--bsu-dark); }

    /* Modal Architecture */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(4px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:16px; width:100%; max-width:1500px; height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); animation: lbIn .2s ease; position:relative; }
    @keyframes lbIn { from { opacity:0; transform: scale(.97); } to { opacity:1; transform: scale(1); } }

    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .modal-header h2 { font-size:16px; font-weight:800; color:var(--bsu-dark); text-transform:uppercase; letter-spacing:.04em; }
    .close-btn { font-size:24px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; border-radius:6px; transition:color .15s; }
    .close-btn:hover { color:#111; }

    /* Sidebar Navigation */
    .sidebar-nav-item { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; text-transform: uppercase; transition: all 0.2s; color: #64748b; margin-bottom: 4px; border: none; cursor: pointer; background: transparent; text-align: left; }
    .sidebar-nav-item.active { background: #213C71; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .sidebar-nav-item:hover:not(.active) { background: #f1f5f9; color: #1e293b; }

    /* Clean inputs for the Decision Letter */
    .clean-input { border: 1px solid #f3f4f6; border-radius: 6px; padding: 6px 10px; font-size: 11px; color: #374151; background: #f9fafb; transition: all 0.2s; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-input:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }
    .clean-textarea { width: 100%; border: 1px solid #f3f4f6; border-radius: 6px; padding: 10px; font-size: 12px; color: #374151; background: #f9fafb; transition: all 0.2s; resize: vertical; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-textarea:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }

    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 24px; border-top:1px solid #e5e7eb; background:#fafafa; z-index: 10; }
    .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
    .btn:active { transform:scale(.97); }
    .btn-primary { background:#c21c2c; color:#fff; }
    .btn-outline { background:transparent; color:var(--bsu-dark); border:1px solid #d1d5db; }

    /* Right Panel Layout override for Viewer */
    .form-preview-panel { flex: 1 1 0; width: auto; overflow-y: auto; background: #f8fafc; position: relative; display: flex; flex-direction: column; }
    .form-preview-panel.viewer-active { padding: 0 !important; }
</style>

<div id="decision-root" x-data="decisionData(@js($initialData ?? ['drafting' => [], 'awaiting' => []]))" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Decision Letter</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Generate final decision letters for assessed protocols</p>
        </div>
        <div class="w-full max-w-xl flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
            </div>
            <select x-model="sortOrder" class="w-44 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-bsu-dark focus:outline-none">
                <option value="newest">Newest -> Oldest</option>
                <option value="oldest">Oldest -> Newest</option>
            </select>
        </div>
    </div>

    <div id="tour-decision-list" class="app-card relative">
        <div class="card-header">
            <div id="tour-tab-drafting" class="card-tab" :class="activeTab === 'drafting' ? 'active' : ''" @click="activeTab = 'drafting'">
                Pending Decisions
                <span x-show="forValidation.length > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="forValidation.length" x-cloak></span>
            </div>
            <div id="tour-tab-awaiting" class="card-tab" :class="activeTab === 'awaiting' ? 'active' : ''" @click="activeTab = 'awaiting'">
                Awaiting Approval
                <span x-show="awaitingApproval.length > 0" class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full text-[10px]" x-text="awaitingApproval.length" x-cloak></span>
            </div>
        </div>

        <div x-show="activeTab === 'drafting'">
            <div class="list-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Assigned Reviewers</div>
                <div>Date of Meeting</div>
                <div class="header-center">Action</div>
            </div>

            <template x-for="(protocol, index) in filteredData" :key="protocol.id">
                <div :id="index === 0 ? 'tour-first-decision-row' : null"
                    class="app-row"
                    :class="canOpenDecision(protocol) ? '' : 'opacity-80 cursor-not-allowed'"
                    @click="canOpenDecision(protocol) && openValidate(protocol)">
                    <div>
                        <span class="app-id-badge" x-text="protocol.id"></span>
                    </div>
                    <div>
                        <div class="app-row-title leading-snug" x-text="protocol.title"></div>
                        <div class="app-row-sub uppercase font-bold mt-1" x-text="protocol.proponent"></div>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border text-gray-700 bg-gray-50" x-text="protocol.classification || 'N/A'"></span>
                    </div>
                    <div>
                        <div class="space-y-1 inline-flex flex-col">
                            <template x-for="(r, idx) in protocol.reviewers" :key="idx">
                                <div class="text-[10px] font-bold text-gray-700 border border-gray-100 rounded-md bg-gray-50 px-2 py-1" x-text="r.name"></div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-700 font-bold" x-text="getMeetingDate(protocol) || 'TBD'"></div>
                    </div>
                    <div class="text-center">
                        <span
                            :class="canOpenDecision(protocol) ? 'workflow-action-link' : 'text-[11px] font-bold text-gray-500 uppercase tracking-wider'"
                            x-text="getDraftingActionText(protocol)">
                        </span>
                    </div>
                </div>
            </template>
            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No protocols pending decisions.
            </div>
        </div>

        <div x-show="activeTab === 'awaiting'" x-cloak>
            <div class="awaiting-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Classification</div>
                <div>Assigned Reviewers</div>
                <div>Draft Created</div>
                <div class="header-center">Status</div>
            </div>

            <template x-for="protocol in filteredData" :key="protocol.id">
                <div class="awaiting-row" @click="openValidate(protocol)">
                    <div>
                        <span class="app-id-badge" x-text="protocol.id"></span>
                    </div>
                    <div>
                        <div class="app-row-title" x-text="protocol.title"></div>
                        <div class="app-row-sub font-bold mt-1 uppercase" x-text="protocol.proponent"></div>
                    </div>
                    <div>
                        <span class="font-bold text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border text-gray-700 bg-gray-50" x-text="protocol.classification || 'N/A'"></span>
                    </div>
                    <div>
                        <div class="space-y-1 inline-flex flex-col">
                            <template x-for="(r, idx) in protocol.reviewers" :key="idx">
                                <div class="text-[10px] font-bold text-gray-700 border border-gray-100 rounded-md bg-gray-50 px-2 py-1" x-text="r.name"></div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-700 font-bold" x-text="new Date(protocol.dateSubmitted).toLocaleDateString()"></div>
                    </div>
                    <div class="h-full text-center flex flex-col items-center justify-center">
                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-1 rounded border bg-orange-50 text-orange-700 border-orange-200">Waiting for Chair</span>
                    </div>
                </div>
            </template>
            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No decisions awaiting chair approval.
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="selectedProtocol ? 'open' : ''">
        <div class="modal-box shadow-2xl animate-in zoom-in-95 duration-200" @click.stop>

            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-black text-bsu-dark uppercase tracking-tight">Decision Letter Dashboard</h2>
                    <span class="inline-flex items-center bg-[#eff6ff] border border-[#bfdbfe] text-[#1d4ed8] text-[11px] font-bold font-mono tracking-[0.03em] px-2.5 py-1 rounded-md" x-text="selectedProtocol?.id"></span>
                </div>
                <button class="text-gray-400 hover:text-gray-600 transition-colors text-2xl font-light border-none bg-transparent cursor-pointer" @click="closeModal()">&times;</button>
            </div>

            <div class="flex-1 flex overflow-hidden w-full h-full relative">

                <div id="tour-decision-sidebar" class="w-80 flex-shrink-0 border-r border-gray-200 bg-slate-50 overflow-y-auto p-5 z-10">

                    <div class="mb-6">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Research Title</div>
                        <div class="text-xs font-bold text-gray-800 leading-snug" x-text="selectedProtocol?.title"></div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Letter Components</h3>
                        <nav class="space-y-1">
                            <button id="tour-nav-compose" @click="activeView = 'decision_letter'" :class="activeView === 'decision_letter' ? 'active' : ''" class="sidebar-nav-item">
                                <span>✉️</span> Compose Letter
                            </button>
                            <button id="tour-nav-feedback" @click="activeView = 'resubmission_form'" :class="activeView === 'resubmission_form' ? 'active' : ''" class="sidebar-nav-item">
                                <span>📋</span> Synthesized Feedback
                            </button>
                        </nav>
                    </div>

                    <div id="tour-documents-panel">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Protocol Documents</h3>

                        <div x-show="isLoadingDocs" class="text-[10px] text-gray-400 italic py-2">Loading documents...</div>

                        <div x-show="!isLoadingDocs" class="space-y-4">
                            <div>
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

                            <template x-if="loadedDocs.activeSupp.length > 0">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Supplementary Docs</div>
                                    <template x-for="doc in loadedDocs.activeSupp" :key="doc.id">
                                        <div @click="viewDocument(doc)"
                                            :class="activeDoc?.id === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
                                            class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-start gap-3 shadow-sm mb-2">
                                            <div class="text-xl leading-none text-[#D32F2F]">📄</div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-[11px] font-bold text-slate-700 leading-tight" x-text="doc.label"></div>
                                                <div class="text-[9px] text-blue-600 font-bold mt-1 break-all leading-snug" x-text="doc.desc"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="loadedDocs.legacy.length > 0">
                                <div class="pt-4 border-t-2 border-dashed border-slate-200">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Version History (Archived)</div>
                                    <template x-for="doc in loadedDocs.legacy" :key="doc.id">
                                        <div @click="viewDocument(doc)"
                                            :class="activeDoc?.id === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-slate-300 bg-slate-50 hover:border-slate-400'"
                                            class="p-3 border-2 border-dashed rounded-xl cursor-pointer transition-all flex items-start gap-3 mb-2 opacity-75 hover:opacity-100">
                                            <div class="text-xl leading-none opacity-60">🗄️</div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-[10px] font-bold text-slate-500 leading-tight" x-text="doc.label"></div>
                                                <div class="text-[8px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-black w-fit mt-1">LEGACY</div>
                                                <div class="text-[9px] text-slate-500 font-bold mt-1 break-all leading-snug" x-text="doc.desc"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="tour-decision-preview" class="form-preview-panel flex-1 overflow-hidden relative z-0 flex flex-col" :class="activeView === 'doc_viewer' ? 'viewer-active' : ''">

                    <div x-show="activeView === 'decision_letter'" class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full m-4 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                            <h2 class="text-sm font-black text-bsu-dark uppercase tracking-wide">Final Decision Letter Template</h2>
                        </div>

                        <div class="p-8 flex-1 overflow-y-auto bg-gray-50/50">
                            <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl p-8 shadow-sm">

                                <div id="tour-decision-status" class="mb-6 p-4 rounded-xl border-2 transition-colors"
                                    :class="{
                                        'border-green-400 bg-green-50': decisionStatus === 'approved',
                                        'border-yellow-400 bg-yellow-50': decisionStatus === 'minor_revision' || decisionStatus === 'major_revision',
                                        'border-red-400 bg-red-50': decisionStatus === 'rejected',
                                        'border-gray-200 bg-gray-50': !decisionStatus
                                    }">
                                    <label class="block text-[11px] font-black uppercase tracking-widest mb-2"
                                        :class="{
                                            'text-green-800': decisionStatus === 'approved',
                                            'text-yellow-800': decisionStatus === 'minor_revision' || decisionStatus === 'major_revision',
                                            'text-red-800': decisionStatus === 'rejected',
                                            'text-gray-600': !decisionStatus
                                        }">
                                        Recommended Committee Decision
                                    </label>
                                    <select x-model="decisionStatus" class="w-full p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-bsu-dark/20 transition-all">
                                        <option value="" disabled selected>-- Select a Decision Status --</option>
                                        <option value="approved">✅ APPROVED</option>
                                        <option value="minor_revision">⚠️ RESUBMIT (Minor Revision)</option>
                                        <option value="major_revision">⚠️ RESUBMIT (Major Revision)</option>
                                        <option value="rejected">❌ REJECTED</option>
                                    </select>
                                </div>

                                <div style="display:flex; flex-direction:column; gap:12px; font-size: 12px; color: #374151;">
                                    <div><input type="date" x-model="letter.date" class="clean-input" style="width: 160px;"></div>
                                    <div class="mt-4 flex flex-col gap-2 w-1/2">
                                        <input type="text" x-model="letter.proponent" class="clean-input w-full font-bold" placeholder="(NAME OF PROPONENT)">
                                        <input type="text" x-model="letter.designation" class="clean-input w-full" placeholder="(Designation)">
                                        <input type="text" x-model="letter.institution" class="clean-input w-full" placeholder="(Institution)">
                                        <input type="text" x-model="letter.address" class="clean-input w-full" placeholder="(Address)">
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <strong>RE:</strong>
                                        <input type="text" x-model="letter.title" class="clean-input flex-1 font-bold" placeholder="(Title of project)">
                                    </div>
                                    <div class="flex items-center gap-2 pl-6">
                                        <strong>REC Code:</strong>
                                        <input type="text" x-model="letter.code" class="clean-input w-64" placeholder="(code)">
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <strong>Subject:</strong>
                                        <input type="text" x-model="letter.subject" class="clean-input flex-1 font-bold text-bsu-dark" placeholder="(Nature of action requested)">
                                    </div>
                                    <div class="mt-6 flex items-center gap-2">
                                        Dear <input type="text" x-model="letter.dearName" class="clean-input font-bold" style="width: 300px;" placeholder="(Name of the Proponent)">:
                                    </div>
                                    <div class="mt-2 leading-relaxed flex items-center gap-1 flex-wrap">
                                        This is to acknowledge receipt of your request and following support documents dated <input type="date" x-model="letter.supportDate" class="clean-input w-32 inline-block">.
                                    </div>

                                    <div id="tour-letter-doc-list" class="mt-2 pl-4">
                                        <template x-for="(doc, index) in letter.documents" :key="index">
                                            <div class="flex items-center gap-2 mb-2 w-full">
                                                <span>●</span>
                                                <input type="text" x-model="letter.documents[index]" class="clean-input flex-1" placeholder="Document Name">
                                                <button @click="letter.documents.splice(index, 1)" class="text-red-500 font-bold px-2 py-1 hover:bg-red-50 rounded">&times;</button>
                                            </div>
                                        </template>
                                        <button @click="letter.documents.push('')" class="text-[10px] text-blue-600 font-bold underline mt-1">+ Add New Document Line</button>
                                    </div>

                                    <div class="mt-4 leading-relaxed">
                                        <textarea x-model="letter.paragraph1" rows="3" class="clean-textarea text-[12px] leading-relaxed font-medium text-gray-800"></textarea>
                                    </div>
                                    <div class="mt-2 leading-relaxed">
                                        <textarea x-model="letter.paragraph2" rows="2" class="clean-textarea text-[12px] leading-relaxed font-medium text-gray-800"></textarea>
                                    </div>

                                    <div id="tour-action-required-table" class="mt-8 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                        <div class="bg-bsu-dark px-4 py-2.5 text-xs font-bold text-white tracking-wide border-b border-gray-300">
                                            Reviewers' Comments & Recommendations
                                        </div>
                                        <table class="w-full text-left text-[11px]">
                                            <thead class="bg-gray-100 border-b border-gray-200">
                                                <tr>
                                                    <th class="px-4 py-3 font-bold text-gray-700 w-1/3">Points for Revision (the item)</th>
                                                    <th class="px-4 py-3 font-bold text-gray-700 border-l border-gray-200">Recommendations</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                <template x-for="row in getActionRequiredRows()" :key="row.id">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeView === 'resubmission_form'" class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full m-4 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                            <h2 class="text-sm font-black text-bsu-dark uppercase tracking-wide">Synthesized Feedback Viewer</h2>
                        </div>
                        <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                            <div class="space-y-4">
                                <template x-for="(row, index) in selectedProtocol?.assessmentRows" :key="row.id">
                                    <div class="bg-white border rounded-xl shadow-sm overflow-hidden" :class="{'border-red-200': row.synthesizedCommentsActionRequired, 'border-green-200': !row.synthesizedCommentsActionRequired}">
                                        <div class="px-5 py-3 border-b flex justify-between items-center" :class="{'bg-red-50': row.synthesizedCommentsActionRequired, 'bg-green-50': !row.synthesizedCommentsActionRequired}">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-600" x-text="row.points"></span>
                                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full border bg-white" :class="row.synthesizedCommentsActionRequired ? 'text-red-700' : 'text-green-700'" x-text="row.synthesizedCommentsActionRequired ? '⚠ Action Required' : '✓ Resolved'"></span>
                                        </div>
                                        <div class="p-5">
                                            <div class="text-[10px] font-black text-brand-red uppercase tracking-widest mb-1">Secretariat Synthesis</div>
                                            <div class="text-xs text-gray-800 leading-relaxed" x-html="row.synthesizedComments"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-for="(row, index) in selectedProtocol?.consentRows" :key="row.id">
                                    <div class="bg-white border rounded-xl shadow-sm overflow-hidden" :class="{'border-red-200': row.synthesizedCommentsActionRequired, 'border-green-200': !row.synthesizedCommentsActionRequired}">
                                        <div class="px-5 py-3 border-b flex justify-between items-center" :class="{'bg-red-50': row.synthesizedCommentsActionRequired, 'bg-green-50': !row.synthesizedCommentsActionRequired}">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-600" x-text="row.points"></span>
                                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full border bg-white" :class="row.synthesizedCommentsActionRequired ? 'text-red-700' : 'text-green-700'" x-text="row.synthesizedCommentsActionRequired ? '⚠ Action Required' : '✓ Resolved'"></span>
                                        </div>
                                        <div class="p-5">
                                            <div class="text-[10px] font-black text-brand-red uppercase tracking-widest mb-1">Secretariat Synthesis</div>
                                            <div class="text-xs text-gray-800 leading-relaxed" x-html="row.synthesizedComments"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeView === 'doc_viewer'" x-cloak class="h-full flex flex-col bg-[#525659] w-full">
                        <div class="bg-white border-b border-gray-200 px-5 py-3 flex justify-between items-center shrink-0 shadow-sm z-10 w-full">
                            <div class="flex flex-col">
                                <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDoc?.label"></h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Researcher Attachment Preview</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a :href="activeDoc?.url" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                <button @click="activeView = 'decision_letter'; activeDoc = null" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
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

            <div id="tour-decision-footer" class="px-6 py-4 bg-slate-50 border-t flex justify-end items-center shrink-0">
                <div class="flex gap-3">
                    <button class="px-6 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-200 transition-colors uppercase border-none bg-transparent cursor-pointer" @click="closeModal()">Cancel</button>
                    <button x-show="activeView === 'decision_letter' && activeTab === 'drafting'" class="bg-[#D32F2F] text-white px-8 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer disabled:opacity-50" @click="submitDecision" :disabled="isLoading">
                        <span x-text="isLoading ? 'Sending...' : 'Save & Route to Chair'"></span>
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('decisionData', (initialData = { drafting: [], awaiting: [] }) => ({
        activeTab: 'drafting',
        searchQuery: '',
        sortOrder: 'newest',
        forValidation: initialData.drafting || [],
        awaitingApproval: initialData.awaiting || [],

        selectedProtocol: null,
        activeView: 'decision_letter',

        activeDoc: null,
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,

        decisionStatus: '',

        letter: {
            date: '', proponent: '', designation: '', institution: '', address: '',
            title: '', code: '', subject: '', dearName: '', supportDate: '',
            documents: ['', '', '', '', ''],
            paragraph1: '', paragraph2: ''
        },

        isLoading: false,

        notification: {
            open: false,
            title: '',
            message: '',
            type: 'success'
        },

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

        init() {
            window.decisionAlpine = this;

            this.$watch('decisionStatus', (val) => {
                if (!this.selectedProtocol) return;
                let msgs = {
                    'approved': 'As a result of the review, we are pleased to inform you that your study protocol has been APPROVED. You may proceed with your research; however, please note the recommended improvements and/or clarifications summarized below for your guidance.',
                    'minor_revision': 'As a result of the review, your study protocol requires MINOR REVISIONS. Please address the recommended revisions and clarifications summarized below before resubmitting your application.',
                    'major_revision': 'As a result of the review, your study protocol requires MAJOR REVISIONS. Please thoroughly address the recommended revisions and clarifications summarized below before resubmitting your application.',
                    'rejected': 'As a result of the review, we regret to inform you that your study protocol has been DISAPPROVED. The specific findings and reasons leading to this decision are summarized below.'
                };
                this.letter.paragraph2 = msgs[val] || 'As a result of the review, the final decision for your study is currently pending.';
            });
        },

        get filteredData() {
            let dataList = this.activeTab === 'drafting' ? this.forValidation : this.awaitingApproval;

            return dataList
                .filter(p => {
                    const s = this.searchQuery.toLowerCase();
                    return (p.id || '').toLowerCase().includes(s)
                        || (p.title || '').toLowerCase().includes(s)
                        || (p.proponent || '').toLowerCase().includes(s);
                })
                .sort((a, b) => {
                    const aTime = new Date(a.dateSubmitted || 0).getTime();
                    const bTime = new Date(b.dateSubmitted || 0).getTime();
                    return this.sortOrder === 'newest' ? bTime - aTime : aTime - bTime;
                });
        },

        parseDateSafe(value) {
            if (!value) return null;
            if (value instanceof Date) return Number.isNaN(value.getTime()) ? null : value;

            const d = new Date(value);
            if (!Number.isNaN(d.getTime())) return d;

            return null;
        },

        getSecondWednesdayDate(year, monthIndex) {
            const firstDay = new Date(year, monthIndex, 1);
            const firstDayWeekday = firstDay.getDay(); // 0=Sun ... 6=Sat
            const daysUntilWednesday = (3 - firstDayWeekday + 7) % 7;
            const firstWednesday = 1 + daysUntilWednesday;
            return new Date(year, monthIndex, firstWednesday + 7);
        },

        getCutoffDate(year, monthIndex) {
            return new Date(year, monthIndex, 15, 23, 59, 59, 999);
        },

        getDecisionBaseDate(protocol) {
            // Use the date when drafting_decision was issued.
            // Ask backend to pass this as protocol.draftingDecisionIssuedAt.
            // Fallbacks are here just in case.
            return this.parseDateSafe(
                protocol.draftingDecisionIssuedAt ||
                protocol.updated_at ||
                protocol.updatedAt ||
                protocol.dateSubmitted ||
                protocol.created_at ||
                protocol.createdAt
            );
        },

        getDecisionSchedule(protocol) {
            const now = new Date();
            const baseDate = this.getDecisionBaseDate(protocol);

            if (!baseDate) {
                return {
                    meetingDate: null,
                    meetingPassed: false,
                    qualifiesForCurrentMonth: false
                };
            }

            const status = String(protocol.status || '').toLowerCase();

            let meetingYear = baseDate.getFullYear();
            let meetingMonth = baseDate.getMonth();

            const cutoff = this.getCutoffDate(meetingYear, meetingMonth);

            const qualifiesForCurrentMonth =
                status === 'drafting_decision' &&
                baseDate.getTime() <= cutoff.getTime();

            if (!qualifiesForCurrentMonth) {
                meetingMonth += 1;
                if (meetingMonth > 11) {
                    meetingMonth = 0;
                    meetingYear += 1;
                }
            }

            const meetingDate = this.getSecondWednesdayDate(meetingYear, meetingMonth);

            return {
                meetingDate,
                meetingPassed: now.getTime() >= meetingDate.getTime(),
                qualifiesForCurrentMonth
            };
        },

        formatMeetingDate(dateObj) {
            if (!(dateObj instanceof Date) || Number.isNaN(dateObj.getTime())) return 'TBD';
            return dateObj.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
        },

        getMeetingDate(protocol) {
            return this.formatMeetingDate(this.getDecisionSchedule(protocol).meetingDate);
        },

        canOpenDecision(protocol) {
            return this.getDecisionSchedule(protocol).meetingPassed;
            //return true; // USE THIS FOR TESTING PURPOSES TO BYPASS MEETING SCHEDULE LOGIC
        },

        getDraftingActionText(protocol) {
            return this.canOpenDecision(protocol)
                ? 'Create Decision Letter'
                : 'Meeting Still Pending';
        },

        getAwaitingStatusText(protocol) {
            return String(protocol.status || '').toLowerCase() === 'drafting_decision'
                ? 'Meeting Still Pending'
                : 'Waiting for Chair';
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
            return rows;
        },

        closeModal() {
            this.selectedProtocol = null;
            this.activeDoc = null;
            this.activeView = 'decision_letter';
            document.body.style.overflow = '';
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

        async openValidate(protocol) {
            if (protocol.is_mock) {
                this.selectedProtocol = protocol;
                this.activeTab = protocol.activeTab || 'drafting';
                this.activeView = 'decision_letter';
                this.activeDoc = null;
                this.decisionStatus = protocol.mockDecisionStatus || 'minor_revision';
                document.body.style.overflow = 'hidden';

                const today = new Date().toISOString().split('T')[0];
                const meetingDate = this.getMeetingDate(protocol);

                this.letter = {
                    date: today,
                    proponent: protocol.proponent || '',
                    designation: 'Principal Investigator',
                    institution: protocol.institution || 'Batangas State University',
                    address: protocol.address || 'Batangas City',
                    title: protocol.title || '',
                    code: protocol.id || '',
                    subject: 'BSU Ethics Review Committee Decision Letter',
                    dearName: protocol.proponent || '',
                    supportDate: today,
                    documents: [
                        'Study Protocol: FULL PROPOSAL REVISED',
                        'Informed Consent Form: INFORMED CONSENT FORM',
                        'Questionnaire: SURVEY QUESTIONNAIRE',
                        'Technical Review Approval: TECHNICAL REVIEW APPROVAL',
                        'Curriculum Vitae: PRINCIPAL INVESTIGATOR CV'
                    ],
                    paragraph1: `We wish to inform you that the Batangas State University Ethics Committee reviewed your study protocol during its regular meeting on ${meetingDate}. Your study has been assigned the code ${protocol.id} which should be used for all communication to the BERC related to this study.`,
                    paragraph2: 'As a result of the review, your study protocol requires MINOR REVISIONS. Please address the recommended revisions and clarifications summarized below before resubmitting your application.'
                };

                this.loadedDocs = {
                    activeBasic: [
                        { id: 'mock_doc_1', label: 'Study Protocol', url: '#', isRevised: true, desc: 'FULL PROPOSAL REVISED' },
                        { id: 'mock_doc_2', label: 'Informed Consent Form', url: '#', isRevised: false, desc: 'INFORMED CONSENT FORM' },
                        { id: 'mock_doc_3', label: 'Technical Review Approval', url: '#', isRevised: false, desc: 'TECHNICAL REVIEW APPROVAL' }
                    ],
                    activeSupp: [
                        { id: 'mock_doc_4', label: 'Questionnaire', url: '#', isRevised: false, desc: 'SURVEY QUESTIONNAIRE' }
                    ],
                    legacy: [
                        { id: 'mock_doc_5', label: 'Study Protocol', url: '#', isRevised: false, desc: 'FULL PROPOSAL OLD VERSION' }
                    ]
                };
                this.isLoadingDocs = false;

                this.selectedProtocol.assessmentRows = [
                    {
                        id: 'mock_a1',
                        points: '1.4 Sampling methods',
                        synthesizedCommentsActionRequired: true,
                        synthesizedComments: 'Clarify the participant selection criteria and explain how the proposed sampling strategy reduces selection bias.'
                    },
                    {
                        id: 'mock_a2',
                        points: '3.2 Privacy & Confidentiality',
                        synthesizedCommentsActionRequired: true,
                        synthesizedComments: 'Provide a clearer data protection plan, including storage location, access restrictions, and retention period for collected records.'
                    }
                ];

                this.selectedProtocol.consentRows = [
                    {
                        id: 'mock_c1',
                        points: '4.5 Risks',
                        synthesizedCommentsActionRequired: true,
                        synthesizedComments: 'The risks section in the informed consent should explain discomforts and possible privacy risks in simpler, participant-friendly language.'
                    }
                ];

                return;
            }

            this.selectedProtocol = protocol;
            this.activeView = 'decision_letter';
            this.activeDoc = null;
            this.decisionStatus = '';
            document.body.style.overflow = 'hidden';

            const today = new Date().toISOString().split('T')[0];
            const meetingDate = this.getMeetingDate(protocol);

            this.letter = {
                date: today,
                proponent: protocol.proponent || '',
                designation: 'Researcher',
                institution: protocol.institution || '',
                address: protocol.address || '',
                title: protocol.title || '',
                code: protocol.id || '',
                subject: 'BSU Ethics Review Committee Decision Letter',
                dearName: protocol.proponent || '',
                supportDate: today,
                documents: ['', '', '', '', ''],
                paragraph1: `We wish to inform you that the Batangas State University Ethics Committee reviewed your study protocol during its regular meeting on ${meetingDate}. Your study has been assigned the code ${protocol.id} which should be used for all communication to the BERC related to this study.`,
                paragraph2: 'As a result of the review, the action requested for your study is pending. Recommended revisions and/or clarifications are summarized below:'
            };

            const parseFileName = (path) => {
                if (!path) return 'Document';
                let name = path.split('/').pop();
                name = name.replace(/_\d{10}_\d+\.\w+$/, '');
                if (this.selectedProtocol?.id) {
                    name = name.replace(`_${this.selectedProtocol.id}`, '');
                }
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            try {
                const response = await fetch(`/documents/api/${protocol.id}`);

                if (response.ok) {
                    const data = await response.json();

                    const basicTypes = ['letter_request', 'endorsement_letter', 'full_proposal', 'technical_review_approval', 'informed_consent', 'manuscript', 'curriculum_vitae'];
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };
                    let letterDocLines = [];
                    let groups = {};

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const files = data.documents[type];
                            if (!files || !Array.isArray(files) || files.length === 0) return;

                            let maxTs = 0;
                            files.forEach(file => {
                                const pathText = file.url || '';
                                const match = pathText.match(/_(\d{10})_/);
                                const ts = match ? parseInt(match[1]) : 0;
                                if (ts > maxTs) maxTs = ts;
                            });

                            const isBasic = basicTypes.includes(type);
                            const titleLabel = this.docLabels[type] || type.replace(/_/g, ' ').toUpperCase();

                            groups[type] = { items: [] };

                            files.forEach(file => {
                                const pathText = file.url || '';
                                const match = pathText.match(/_(\d{10})_/);
                                const ts = match ? parseInt(match[1]) : 0;

                                const hasRealDesc = file.description && file.description !== 'View File' && file.description !== '';
                                const displayName = hasRealDesc ? file.description : parseFileName(pathText);
                                const isRevised = pathText.includes('resubmit_');

                                const obj = {
                                    id: file.id || Math.random(),
                                    label: titleLabel,
                                    url: file.url,
                                    isRevised: isRevised,
                                    desc: displayName
                                };

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

                        Object.keys(groups).forEach(type => {
                            if (groups[type].items.length > 0) {
                                const label = this.docLabels[type] || type.toUpperCase().replace(/_/g, ' ');
                                const combinedDesc = [...new Set(groups[type].items)].join(', ');
                                letterDocLines.push(`${label}: ${combinedDesc}`);
                            }
                        });
                    }

                    this.loadedDocs = tempDocs;
                    this.letter.documents = letterDocLines;
                    while (this.letter.documents.length < 5) {
                        this.letter.documents.push('');
                    }
                }
            } catch (e) {
                console.error('API Doc Load Error:', e);
            } finally {
                this.isLoadingDocs = false;
            }
        },

        viewDocument(doc) {
            this.activeDoc = doc;
            this.activeView = 'doc_viewer';
        },

        getClassificationTagClass(c) {
            const s = (c || '').toLowerCase();
            if (s.includes('exempt')) return 'text-blue-700 border-blue-200 bg-blue-50';
            return 'text-red-700 border-red-200 bg-red-50';
        },

        async submitDecision() {
            if (!this.decisionStatus) {
                this.showNotification('Error', 'Please select a Recommended Decision Status', 'error');
                return;
            }

            if (!confirm('Finalize this decision letter and route it to the Chair?')) return;

            this.isLoading = true;

            const payload = {
                protocol_code: this.selectedProtocol.id,
                decision_status: this.decisionStatus,
                letter_data: { ...this.letter }
            };

            try {
                const response = await fetch('/api/secretariat/decision-letter/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    this.closeModal();
                    this.showNotification('Success', 'Decision letter finalized and routed successfully.', 'success');
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    const err = await response.json();
                    this.showNotification('Error', err.message || 'Failed to save decision.', 'error');
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

    function runDecisionTutorial(manual = false, retries = 0) {
        const isFirstLogin = @json(auth()->check() ? auth()->user()->is_first_login : true);
        const userId = @json(auth()->id() ?? 1);
        const storageKey = 'berc_tutorial_step_' + userId;

        const urlParams = new URLSearchParams(window.location.search);
        const forceTour = urlParams.get('tour') === '1';
        let tourState = localStorage.getItem(storageKey);

        if (tourState === 'secretariat_decision_manual_skip') {
            localStorage.removeItem(storageKey);
            return;
        }

        if (manual || forceTour) {
            tourState = 'secretariat_decision';
            localStorage.setItem(storageKey, tourState);
        }

        if (!manual && !forceTour && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!manual && !forceTour && tourState !== 'secretariat_decision') {
            return;
        }

        if (window.__decisionTourStarted) return;

        const rootEl = document.getElementById('decision-root');
        let alpine = null;

        try {
            alpine = window.decisionAlpine ||
                rootEl?.__x?.$data ||
                rootEl?._x_dataStack?.[0] ||
                (window.Alpine ? window.Alpine.$data(rootEl) : null);
        } catch (e) {}

        if (!rootEl || !alpine || typeof window.driver === 'undefined') {
            if (retries < 40) {
                setTimeout(() => runDecisionTutorial(manual, retries + 1), 250);
            } else {
                console.error('Tutorial aborted: Could not hook into Alpine or Driver.js.');
            }
            return;
        }

        window.__decisionTourStarted = true;

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

                    if (manual) {
                        localStorage.setItem(storageKey, 'secretariat_revision_validation_manual_skip');
                        tour.destroy();
                        window.location.href = "{{ route('secretariat.revision_validation') }}";
                        return;
                    }

                    localStorage.setItem(storageKey, 'secretariat_revision_validation');
                    tour.destroy();
                    window.location.href = "{{ route('secretariat.revision_validation') }}";

                } else {
                    tour.destroy();
                }
            },

            onDestroyed: () => {
                if (alpine.closeModal) {
                    alpine.closeModal();
                }

                alpine.activeTab = 'drafting';
                alpine.activeView = 'decision_letter';
                window.__decisionTourStarted = false;
            },

            steps: [
                {
                    element: '#tour-decision-list',
                    onHighlightStarted: async () => {
                        alpine.closeModal();
                        alpine.activeTab = 'drafting';
                        await wait(200);
                    },
                    popover: {
                        title: 'Decision Letter Workspace',
                        description: 'This page is where the Secretariat prepares the official ethics decision letter after assessment validation has already been completed. From here, pending protocols can be drafted and routed to the Chair, while completed drafts remain visible in the approval queue.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-tab-drafting',
                    onHighlightStarted: async () => {
                        alpine.activeTab = 'drafting';
                        alpine.closeModal();
                        await wait(150);
                    },
                    popover: {
                        title: 'Pending Decisions Tab',
                        description: 'This tab lists protocols that are ready for decision-letter drafting. Each row gives you the protocol code, study title, assigned reviewers, classification, and meeting date so you can prepare the correct final letter.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-tab-awaiting',
                    onHighlightStarted: async () => {
                        alpine.activeTab = 'awaiting';
                        alpine.closeModal();
                        await wait(150);
                    },
                    popover: {
                        title: 'Awaiting Approval Tab',
                        description: 'After drafting, letters move here while they wait for the Committee Chair’s review and approval. This lets the Secretariat track what has already been routed forward.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-first-decision-row',
                    onHighlightStarted: async () => {
                        alpine.activeTab = 'drafting';
                        alpine.closeModal();
                        await wait(200);
                    },
                    popover: {
                        title: 'Protocol Decision Entry',
                        description: 'Each row represents one protocol that is ready for a decision letter. Clicking it opens the drafting dashboard where the decision status, supporting documents, and synthesized comments are brought together.',
                        side: 'bottom',
                        align: 'start',
                        onNextClick: async () => {
                            await alpine.openValidate({
                                is_mock: true,
                                id: '2026-MOCK-DEC-001',
                                title: 'Effects of AI on System Architecture',
                                proponent: 'Dr. Jane Doe',
                                classification: 'Full Board',
                                institution: 'Batangas State University',
                                address: 'Batangas City',
                                activeTab: 'drafting',
                                mockDecisionStatus: 'minor_revision',
                                reviewers: [
                                    { name: 'Dr. Smith' },
                                    { name: 'Dr. Brown' },
                                    { name: 'Dr. Cruz' }
                                ]
                            });

                            setTimeout(() => tour.moveNext(), 350);
                        }
                    }
                },
                {
                    element: '#tour-decision-sidebar',
                    popover: {
                        title: 'Dashboard Sidebar',
                        description: 'The left panel acts as your dashboard navigation. It keeps the protocol identity visible, lets you switch between composing the final letter and reviewing synthesized feedback, and also shows all attached protocol documents that may need to be checked before routing the draft.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nav-compose',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Compose Letter View',
                        description: 'This is the main drafting view. The Secretariat finalizes the decision status, fills out the official letter content, and prepares the wording that will be sent forward for Chair approval.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nav-feedback',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'resubmission_form';
                        await wait(200);
                    },
                    popover: {
                        title: 'Synthesized Feedback View',
                        description: 'This section shows the consolidated comments that came from the earlier validation step. It helps the Secretariat copy only the relevant action-required revisions into the official decision letter.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-documents-panel',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Protocol Documents Panel',
                        description: 'These are the submitted documents tied to the protocol. The Secretariat can review the latest revised files, check supplementary materials, and even inspect archived versions before finalizing the decision wording.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-decision-preview',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Decision Letter Drafting Area',
                        description: 'The right panel is the actual decision-letter workspace. It contains the editable letter body, automatically prepared protocol details, and the revision summary that reflects the validated assessment results.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-decision-status',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        alpine.decisionStatus = 'minor_revision';
                        await wait(150);
                    },
                    popover: {
                        title: 'Decision Status Selector',
                        description: 'This is the most important control in the draft. The selected status changes the tone and content of the letter, such as Approved, Minor Revision, Major Revision, or Rejected. The paragraph below is updated automatically based on this choice.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-letter-doc-list',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Support Documents Section',
                        description: 'This list records the documents received with the protocol. It is automatically populated from the current files, but the Secretariat can still add, remove, or refine entries so the final letter accurately reflects the received submission package.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-action-required-table',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Revision Summary Table',
                        description: 'This table captures the action-required items that came from the synthesized assessment and informed consent review. These are the exact issues that need to be reflected in a revision-based decision letter.',
                        side: 'left',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-decision-footer',
                    onHighlightStarted: async () => {
                        alpine.activeView = 'decision_letter';
                        await wait(150);
                    },
                    popover: {
                        title: 'Save and Route to Chair',
                        description: 'Once the letter is complete, this action saves the decision draft and routes it to the Chair for approval. In the real workflow, this is the final Secretariat action on this page before the Chair reviews the document.',
                        side: 'top',
                        align: 'center',
                        onNextClick: async () => {
                            alpine.closeModal();
                            setTimeout(() => tour.moveNext(), 250);
                        }
                    }
                },
                {
                    popover: {
                        title: 'Next Step: Revision Validation',
                        description: 'After drafting and routing the decision letter, the next workflow page is Revision Validation, where submitted revisions and supporting responses are checked against the committee recommendations.',
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
        loadDriverThenRun(() => runDecisionTutorial(true));
    };

    loadDriverThenRun(() => runDecisionTutorial(false));
});
</script>
@endsection
