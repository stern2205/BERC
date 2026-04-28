@extends('chair.layouts.app')

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

    /* Clean inputs for the Decision Letter */
    .clean-input { border: 1px solid #f3f4f6; border-radius: 6px; padding: 6px 10px; font-size: 11px; color: #374151; background: #f9fafb; transition: all 0.2s; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-input:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }
    .clean-textarea { width: 100%; border: 1px solid #f3f4f6; border-radius: 6px; padding: 10px; font-size: 12px; color: #374151; background: #f9fafb; transition: all 0.2s; resize: vertical; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .clean-textarea:focus { outline: none; background: #fff; border-color: var(--bsu-dark); box-shadow: 0 0 0 2px rgba(33,60,113,.1); }

    /* Footer Buttons */
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 24px; border-top:1px solid #e5e7eb; background:#fafafa; z-index: 10; }
    .btn { padding:9px 20px; border-radius:8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border:none; cursor:pointer; transition:opacity .15s, transform .1s; }
    .btn:active { transform:scale(.97); }
    .btn-primary { background:#16a34a; color:#fff; }
    .btn-primary:hover:not(:disabled) { opacity:.9; }
    .btn-primary:disabled { background:#9ca3af; cursor:not-allowed; }
</style>

<div x-data="decisionData(@js($protocolsData ?? []))" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Chair Decision Validation (REVISIONS)</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Review and finalize drafted decision letters from the Secretariat</p>
        </div>
        <div class="w-full max-w-sm relative">
            <input type="text" x-model="searchQuery" placeholder="Search ID, Title, or Proponent..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-bsu-dark focus:ring-1 focus:ring-bsu-dark transition-all">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <div id="tour-revisions-list" class="app-card">
        <div class="card-header">
            <div class="card-tab">
                Awaiting Chair Approval
                <span x-show="filteredData.length > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px]" x-text="filteredData.length"></span>
            </div>
        </div>

        <div>
            <div class="assessment-grid-header">
                <div>Application ID</div>
                <div>Study & Researcher</div>
                <div>Drafted Status</div>
                <div>Action</div>
            </div>

            <template x-for="protocol in filteredData" :key="protocol.id + protocol.version">
                <div class="assessment-row" @click="openValidate(protocol)">
                    <div>
                        <span class="app-id-badge" x-text="protocol.id"></span>
                        <div class="mt-1 bg-blue-50 text-bsu-dark px-2 py-0.5 rounded text-[10px] font-black w-max border border-blue-200" x-text="protocol.version"></div>
                    </div>
                    <div>
                        <div class="font-bold text-[13px] text-gray-900 leading-snug" x-text="protocol.title"></div>
                        <div class="text-[11px] text-gray-500 font-bold mt-1 uppercase" x-text="protocol.proponent"></div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded border"
                              :class="{'bg-green-50 text-green-700 border-green-200': protocol.letterData?.decision_status === 'approved', 'bg-red-50 text-red-700 border-red-200': protocol.letterData?.decision_status !== 'approved'}"
                              x-text="protocol.letterData?.decision_status ? protocol.letterData.decision_status.replace('_', ' ') : 'Pending Letter'"></span>
                        <div class="text-[10px] text-gray-500 font-bold mt-1" x-text="'Updated: ' + protocol.dateSubmitted"></div>
                    </div>
                    <div>
                        <span class="workflow-action-link"><u>Review & Finalize</u></span>
                    </div>
                </div>
            </template>

            <div x-show="filteredData.length === 0" class="p-12 text-center text-gray-500 font-bold text-sm">
                No decision letters awaiting approval.
            </div>
        </div>
    </div>

    <div class="modal-overlay" :class="selectedProtocol ? 'open' : ''">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <h2>Chair Letter Validation</h2>
                    <span class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-700 text-[11px] font-bold font-mono tracking-[0.03em] px-2.5 py-1 rounded-md" x-text="selectedProtocol?.id + ' • ' + selectedProtocol?.version"></span>
                </div>
                <button class="close-btn" @click="closeModal()">&times;</button>
            </div>

            <div class="modal-content">
                <div id="tour-revisions-sidebar" class="protocol-info-panel">
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
                            <button id="tour-letter-btn" @click="activeView = 'decision_letter'" :class="activeView === 'decision_letter' ? 'active' : ''" class="sidebar-nav-item bg-brand-red/10 text-brand-red hover:bg-brand-red/20">
                                <span>✉️</span> Review Decision Letter
                            </button>
                            <button id="tour-feedback-btn" @click="activeView = 'resubmission_form'" :class="activeView === 'resubmission_form' ? 'active' : ''" class="sidebar-nav-item">
                                <span>📋</span> View Synthesized Feedback
                            </button>
                        </nav>
                    </div>

                    <div style="margin-top:30px;">
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
                                            <div @click="viewDocument(doc.id, doc.url, doc.label)"
                                                :class="activeDocKey === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
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
                                                <div @click="viewDocument(doc.id, doc.url, doc.label)"
                                                    :class="activeDocKey === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
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
                                                <div @click="viewDocument(doc.id, doc.url, doc.label + ' (Archived)')"
                                                    :class="activeDocKey === doc.id ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-slate-300 bg-slate-50 hover:border-slate-400'"
                                                    class="p-3 border-2 border-dashed rounded-xl cursor-pointer transition-all flex items-start gap-3 mb-2 opacity-75 hover:opacity-100">
                                                    <div class="text-xl leading-none opacity-60">🗄️</div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-[10px] font-bold text-slate-500 leading-tight" x-text="doc.label"></div>
                                                        <div class="text-[8px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-black w-fit mt-1 mb-1">LEGACY</div>
                                                        <div class="text-[9px] text-slate-500 font-bold mt-1 break-all leading-snug" x-text="doc.desc"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="form-preview-panel" :class="activeView === 'doc_viewer' ? 'viewer-active' : ''">

                    <div id="tour-decision-panel" x-show="activeView === 'decision_letter'" class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full m-4 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                            <h2 class="text-sm font-black text-bsu-dark uppercase tracking-wide">Final Decision Letter Template</h2>
                        </div>

                        <div class="p-8 flex-1 overflow-y-auto bg-gray-50/50">
                            <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl p-8 shadow-sm">

                                <div class="mb-6 p-4 rounded-xl border-2 transition-colors"
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
                                    <div>
                                        <input type="date" x-model="letter.date" class="clean-input" style="width: 160px;">
                                    </div>

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
                                        <input type="text" x-model="letter.code" class="clean-input w-64 text-gray-500 bg-gray-100" readonly>
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

                                    <div class="mt-2 pl-4">
                                        <template x-for="(doc, index) in letter.documents" :key="index">
                                            <div class="flex items-center gap-2 mb-2 w-full">
                                                <span>●</span>
                                                <input type="text" x-model="letter.documents[index]" class="clean-input flex-1" placeholder="Document Name/Version">
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

                                    <div class="mt-8 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                        <div class="bg-bsu-dark px-4 py-2.5 text-xs font-bold text-white tracking-wide border-b border-gray-300">
                                            Pending Revisions & Recommendations
                                        </div>
                                        <table class="w-full text-left text-[11px]">
                                            <thead class="bg-gray-100 border-b border-gray-200">
                                                <tr>
                                                    <th class="px-4 py-3 font-bold text-gray-700 w-[40%]">Points for Revision</th>
                                                    <th class="px-4 py-3 font-bold text-gray-700 border-l border-gray-200">Recommendations</th>
                                                </tr>
                                            </thead>
                                            <template x-for="group in groupedActionRequiredRows" :key="group.sectionName">
                                                <tbody class="divide-y divide-gray-200 bg-white border-b border-gray-200">
                                                    <tr class="bg-gray-50/80">
                                                        <td colspan="2" class="px-4 py-2 border-b border-gray-200">
                                                            <span class="text-[10px] font-black text-bsu-dark uppercase tracking-widest flex items-center gap-2">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-red"></span>
                                                                <span x-text="group.sectionName"></span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <template x-for="row in group.rows" :key="row.id">
                                                        <tr class="hover:bg-red-50/20">
                                                            <td class="px-4 py-4 align-top text-gray-800 font-bold"
                                                                x-text="row.item ? 'Item ' + row.item + (questionLabels[row.item] ? ' - ' + questionLabels[row.item] : '') : 'Revision Point'"></td>

                                                            <td class="px-4 py-4 align-top border-l border-gray-200 text-gray-700 leading-relaxed"
                                                                x-html="row.synthesized_comments || row.reviewers_remarks || row.berc_recommendation"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </template>

                                            <tbody x-show="groupedActionRequiredRows.length === 0" class="bg-white">
                                                <tr>
                                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 italic">No specific points marked for action required.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-8 text-[12px]">
                                        Very truly yours,
                                        <div class="mt-4 flex flex-col gap-2 w-64">
                                            <span class="text-[10px] text-gray-500 italic">(Upload E-signature)</span>
                                            <input type="file" class="text-[10px] file:mr-4 file:py-1 file:px-3 file:rounded file:border border-gray-200 file:text-[10px] file:font-semibold file:bg-gray-50 file:text-bsu-dark hover:file:bg-gray-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tour-resubmission-panel" x-show="activeView === 'resubmission_form'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col m-4">
                        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                            <h2 class="text-sm font-black text-bsu-dark uppercase tracking-wide">Synthesized Resubmission Feedback</h2>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                            <div class="space-y-6">
                                <template x-for="group in groupedRows" :key="group.sectionName">
                                    <div>
                                        <h3 class="text-xs font-black text-bsu-dark uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-brand-red"></span>
                                            <span x-text="group.sectionName"></span>
                                        </h3>

                                        <div class="space-y-4">
                                            <template x-for="(row, index) in group.rows" :key="row.id">
                                                <div class="bg-white border rounded-xl shadow-sm overflow-hidden"
                                                     :class="{'border-green-200': row.action === 'resolved', 'border-red-200': row.action === 'action_required'}">

                                                    <div class="px-5 py-3 border-b flex justify-between items-center"
                                                         :class="{'bg-green-50': row.action === 'resolved', 'bg-red-50': row.action === 'action_required', 'bg-gray-50': !row.action}">
                                                        <div class="flex items-center gap-3">
                                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black text-white"
                                                                  :class="{'bg-green-600': row.action === 'resolved', 'bg-red-600': row.action === 'action_required', 'bg-gray-400': !row.action}"
                                                                  x-text="index + 1"></span>
                                                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-600"
                                                                  x-text="row.item ? 'Item ' + row.item + (questionLabels[row.item] ? ' (' + questionLabels[row.item] + ')' : '') : 'General Revision'"></span>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-[10px] font-bold text-gray-500 uppercase">Page: <span x-text="row.section_and_page"></span></span>
                                                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full border"
                                                                  :class="{'bg-green-100 text-green-700 border-green-200': row.action === 'resolved', 'bg-red-100 text-red-700 border-red-200': row.action === 'action_required'}"
                                                                  x-text="row.action === 'resolved' ? '✓ Resolved' : '⚠ Action Required'"></span>
                                                        </div>
                                                    </div>

                                                    <div class="p-5 grid grid-cols-2 gap-6">
                                                        <div>
                                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">BERC Recommendation</div>
                                                            <p class="text-xs text-gray-700 italic" x-text="row.berc_recommendation"></p>

                                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 mt-4">Researcher Response</div>
                                                            <p class="text-xs font-bold text-bsu-dark" x-text="row.researcher_response"></p>
                                                        </div>
                                                        <div class="border-l border-gray-100 pl-6">
                                                            <div class="text-[10px] font-black text-brand-red uppercase tracking-widest mb-1">Final Secretariat Synthesis</div>
                                                            <div class="text-xs text-gray-800 leading-relaxed bg-red-50/30 p-3 rounded-lg border border-red-100" x-html="row.synthesized_comments || row.reviewers_remarks || 'Pending'"></div>

                                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 mt-4">Reviewer Remarks Context</div>
                                                            <div class="text-[11px] text-gray-500 leading-relaxed" x-html="row.reviewers_remarks || 'N/A'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeView === 'doc_viewer'" x-cloak class="absolute inset-0 bg-slate-800 flex flex-col z-50">
                        <div class="bg-white px-5 py-3 border-b border-gray-200 flex justify-between items-center shadow-md">
                            <div class="flex flex-col">
                                <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDocTitle"></h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Digital Document Preview</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a :href="activeDocUrl" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                <button @click="activeView = 'decision_letter'; activeDocKey = null; activeDocUrl = null" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
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

            <div class="modal-footer">
                <button class="btn btn-outline" @click="closeModal()" style="border:1px solid #d1d5db; color:#374151;">Cancel</button>

                <template x-if="activeView === 'decision_letter'">
                    <div class="flex gap-3">
                        <button class="btn bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors border border-gray-300"
                                @click="submitDecision('draft')" :disabled="isLoading">
                            Save Letter Draft
                        </button>
                        <button class="btn btn-primary shadow-sm flex items-center gap-2"
                                @click="submitDecision('finalize')" :disabled="isLoading || !decisionStatus">
                            <svg x-show="isLoading" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isLoading ? 'Validating...' : 'Validate Decision'"></span>
                        </button>
                    </div>
                </template>
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('decisionData', (initialData = []) => ({
        searchQuery: '',
        forValidation: initialData,

        selectedProtocol: null,
        activeView: 'decision_letter',

        // Viewer State
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: '',

        // Document State
        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,
        isLoadingDraft: false,

        decisionStatus: '',
        letter: {
            date: '', proponent: '', designation: '', institution: '', address: '',
            title: '', code: '', subject: '', dearName: '', supportDate: '',
            documents: [],
            paragraph1: '', paragraph2: ''
        },

        isLoading: false,
        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',

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

        questionLabels: {
            '1.1': 'Viability of expected output', '1.2': 'Previous animal/human studies', '1.3': 'Appropriateness of design',
            '1.4': 'Sampling methods', '1.5': 'Sample size justification', '1.6': 'Statistical methods',
            '1.7': 'Data analysis methods', '1.8': 'Merit and safety criteria', '1.9': 'Justified exclusion',
            '1.10': 'Criteria precision', '1.11': 'Penalty/Benefit statement', '1.12': 'Research statement',
            '1.13': 'Number of participants', '1.14': 'Community benefits', '1.15': 'Post-study access',
            '1.16': 'Anticipated payment', '1.17': 'Anticipated expenses', '1.18': 'Medical record access',
            '2.1': 'Specimen storage & disposal', '2.2': 'Researcher CV/Capability', '2.3': 'Staff & Infrastructure', '2.4': 'Extent of involvement',
            '3.1': 'Conflict of Interest', '3.2': 'Privacy & Confidentiality', '3.3': 'Consent Principle',
            '3.4': 'Vulnerable Populations', '3.5': 'Recruitment Manner', '3.6': 'Assent for Minors',
            '3.7': 'Risk Mitigation', '3.8': 'Direct Benefits', '3.9': 'Reimbursements',
            '3.10': 'Community Impact', '3.11': 'Collaborative Terms',
            '4.1': 'Purpose', '4.2': 'Duration', '4.3': 'Procedures', '4.4': 'Discomforts',
            '4.5': 'Risks', '4.6': 'Randomization', '4.7': 'Benefits', '4.8': 'Alternatives',
            '4.9': 'Compensation', '4.10': 'Contact Persons', '4.11': 'Voluntary Nature',
            '4.12': 'Research Nature', '4.13': 'Participant Count', '4.14': 'Community Benefit',
            '4.15': 'Post-study Access', '4.16': 'Payments', '4.17': 'Expenses',
            '4.18': 'Medical Record Access', '4.19': 'Right to Access', '4.20': 'Genetic Policy',
            '4.21': 'Secondary Use', '4.22': 'Storage/Destruction', '4.23': 'Commercialization',
            '4.24': 'BERC Approval & Contact'
        },

        sectionTitles: {
            1: 'Scientific Design', 2: 'Conduct of Study', 3: 'Ethical Consideration', 4: 'Informed Consent'
        },

        getParagraph2Template(status) {
            if (status === 'approved') return `As a result of the review, we are pleased to inform you that your study protocol has been APPROVED. You may proceed with your research.`;
            if (status === 'minor_revision') return `As a result of the review, your study protocol requires MINOR REVISIONS. Please address the recommended revisions and clarifications summarized below before resubmitting your application.`;
            if (status === 'major_revision') return `As a result of the review, your study protocol requires MAJOR REVISIONS. Please thoroughly address the recommended revisions and clarifications summarized below before resubmitting your application.`;
            if (status === 'rejected') return `As a result of the review, we regret to inform you that your study protocol has been DISAPPROVED. The specific findings and reasons leading to this decision are summarized below.`;
            return `As a result of the review, the final decision for your study is currently pending.`;
        },

        init() {
            this.$watch('decisionStatus', (val) => {
                if (!this.selectedProtocol || this.isLoadingDraft) return;
                this.letter.paragraph2 = this.getParagraph2Template(val);
            });
        },

        get filteredData() {
            return this.forValidation.filter(p => {
                const s = this.searchQuery.toLowerCase();
                return (p.id || '').toLowerCase().includes(s) ||
                       (p.title || '').toLowerCase().includes(s) ||
                       (p.proponent || '').toLowerCase().includes(s);
            });
        },

        getSecondWednesday() {
            const d = new Date(); d.setDate(1);
            let day = d.getDay(); let offset = (3 - day + 7) % 7;
            d.setDate(1 + offset + 7);
            return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        },

        get groupedRows() {
            if (!this.selectedProtocol || !this.selectedProtocol.revisionRows) return [];
            let groups = {};
            let generalGroup = [];

            this.selectedProtocol.revisionRows.forEach(row => {
                let sectionId = row.item ? parseInt(row.item.split('.')[0]) : null;

                if (sectionId && this.sectionTitles[sectionId]) {
                    if (!groups[sectionId]) {
                        groups[sectionId] = {
                            sectionName: this.sectionTitles[sectionId],
                            rows: []
                        };
                    }
                    groups[sectionId].rows.push(row);
                } else { generalGroup.push(row); }
            });

            let result = [];
            [1, 2, 3, 4].forEach(id => { if (groups[id]) result.push(groups[id]); });
            if (generalGroup.length > 0) result.push({ sectionName: 'General Revisions', rows: generalGroup });
            return result;
        },

        get groupedActionRequiredRows() {
            if (!this.selectedProtocol || !this.selectedProtocol.revisionRows) return [];
            let groups = {};
            let generalGroup = [];
            const actionRows = this.selectedProtocol.revisionRows.filter(r => r.action === 'action_required');

            actionRows.forEach(row => {
                let sectionId = row.item ? parseInt(row.item.split('.')[0]) : null;
                if (sectionId && this.sectionTitles[sectionId]) {
                    if (!groups[sectionId]) {
                        groups[sectionId] = { sectionName: this.sectionTitles[sectionId], rows: [] };
                    }
                    groups[sectionId].rows.push(row);
                } else { generalGroup.push(row); }
            });

            let result = [];
            [1, 2, 3, 4].forEach(id => { if (groups[id]) result.push(groups[id]); });
            if (generalGroup.length > 0) result.push({ sectionName: 'General Revisions', rows: generalGroup });
            return result;
        },

        closeModal() {
            this.selectedProtocol = null;
            this.activeDocKey = null;
            this.activeDocUrl = null;
            document.body.style.overflow = '';
        },

        async openValidate(protocol) {
            // ── TUTORIAL MOCK BYPASS ──
            if (protocol.is_mock) {
                this.selectedProtocol = protocol;
                this.activeView = 'decision_letter';
                document.body.style.overflow = 'hidden';
                this.decisionStatus = 'approved';
                this.letter = {
                    date: new Date().toISOString().split('T')[0],
                    proponent: protocol.proponent,
                    designation: 'Researcher',
                    institution: 'Batangas State University',
                    address: 'Batangas City',
                    title: protocol.title,
                    code: protocol.id,
                    subject: 'Ethics Review Decision',
                    dearName: protocol.proponent,
                    supportDate: new Date().toISOString().split('T')[0],
                    documents: ['Revised Protocol V2'],
                    paragraph1: 'We wish to inform you...',
                    paragraph2: 'As a result of the review...'
                };
                this.loadedDocs = { activeBasic: [{id: 'm1', label: 'Revised Protocol', desc: 'PDF', isRevised: true}], activeSupp: [], legacy: [] };
                this.isLoadingDocs = false;
                return;
            }

            this.isLoadingDraft = true;
            this.selectedProtocol = protocol;
            this.activeView = 'decision_letter';
            this.activeDocKey = null;
            this.activeDocUrl = null;
            document.body.style.overflow = 'hidden';

            const revNum = protocol.version.replace('V', '');
            const protocolCode = protocol.id;

            const today = new Date().toISOString().split('T')[0];
            const meetingDate = this.getSecondWednesday();
            const defaultParagraph1 = `We wish to inform you that the Batangas State University Research Ethics Committee (BERC) reviewed your study protocol during its regular meeting. Your study has been assigned the protocol code ${protocolCode}, which should be used in all future communications related to this study.`;

            if (protocol.letterData) {
                const savedStatus = protocol.letterData.decision_status || '';
                this.decisionStatus = savedStatus;
                this.letter = {
                    date: protocol.letterData.date || '',
                    proponent: protocol.letterData.proponent || protocol.proponent,
                    designation: protocol.letterData.designation || '',
                    institution: protocol.letterData.institution || '',
                    address: protocol.letterData.address || '',
                    title: protocol.letterData.title || protocol.title,
                    code: protocolCode,
                    subject: protocol.letterData.subject || '',
                    dearName: protocol.letterData.dearName || '',
                    supportDate: protocol.letterData.supportDate || '',
                    documents: Array.isArray(protocol.letterData.documents) ? protocol.letterData.documents : [],
                    paragraph1: protocol.letterData.paragraph1 || defaultParagraph1,
                    paragraph2: protocol.letterData.paragraph2 || this.getParagraph2Template(savedStatus)
                };
            } else {
                this.decisionStatus = '';
                this.letter = {
                    date: today,
                    proponent: protocol.proponent || '',
                    designation: 'Researcher',
                    institution: protocol.institution || '',
                    address: protocol.institution_address || '',
                    title: protocol.title || '',
                    code: protocolCode,
                    subject: 'BSU Ethics Review Committee Decision Letter',
                    dearName: protocol.proponent || '',
                    supportDate: today,
                    documents: [],
                    paragraph1: defaultParagraph1,
                    paragraph2: this.getParagraph2Template('')
                };
            }

            const parseFileName = (path) => {
                if (!path) return 'Document';
                let name = path.split('/').pop();
                name = name.replace(/_\d{10}_\d+\.\w+$/, '');
                if (protocolCode) name = name.replace(`_${protocolCode}`, '');
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            try {
                const response = await fetch(`/documents/api/revision/${protocolCode}/${revNum}`);

                if (response.ok) {
                    const data = await response.json();
                    let tempDocs = { activeBasic: [], activeSupp: [], legacy: [] };
                    let letterDocLines = [];
                    let groups = {};

                    if (data.documents) {
                        Object.keys(data.documents).forEach(type => {
                            const files = data.documents[type];
                            if (!files || !Array.isArray(files) || files.length === 0) return;

                            const isBasic = this.basicTypes.includes(type);
                            const titleLabel = this.docLabels[type] || type.replace(/_/g, ' ').toUpperCase();
                            groups[type] = { items: [] };

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

                                groups[type].items.push(displayName);
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

                    const isEmptyDraft = this.letter.documents.length === 0 || this.letter.documents.every(d => d === '');
                    if (isEmptyDraft && letterDocLines.length > 0) {
                        this.letter.documents = letterDocLines;
                        while (this.letter.documents.length < 5) {
                            this.letter.documents.push('');
                        }
                    } else {
                        while (this.letter.documents.length < 5) {
                            this.letter.documents.push('');
                        }
                    }
                }
            } catch (e) {
                console.error("API Doc Load Error:", e);
            } finally {
                this.isLoadingDocs = false;
                setTimeout(() => { this.isLoadingDraft = false; }, 100);
            }
        },

        viewDocument(id, url, label) {
            this.activeDocKey = id;
            this.activeDocUrl = url;
            this.activeDocTitle = label;
            this.activeView = 'doc_viewer';
        },

        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
            setTimeout(() => this.notificationOpen = false, 4000);
        },

        async submitDecision(actionType) {
            if (actionType === 'finalize' && !this.decisionStatus) {
                alert('Please select a final decision status before validating.');
                return;
            }

            if (actionType === 'finalize' && !confirm('Are you sure you want to finalize this decision? This will officially update the application status.')) {
                return;
            }

            this.isLoading = true;

            const payload = {
                action_type: actionType,
                protocol_code: this.selectedProtocol.id,
                revision_number: this.selectedProtocol.version.replace('V', ''),
                decision_status: this.decisionStatus || 'pending',
                letter_data: {
                    date: this.letter.date,
                    proponent: this.letter.proponent,
                    designation: this.letter.designation,
                    institution: this.letter.institution,
                    address: this.letter.address,
                    title: this.letter.title,
                    subject: this.letter.subject,
                    dearName: this.letter.dearName,
                    supportDate: this.letter.supportDate,
                    documents: this.letter.documents,
                    paragraph1: this.letter.paragraph1,
                    paragraph2: this.letter.paragraph2
                }
            };

            try {
                const response = await fetch('/api/chair/resubmission/decision/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    this.showNotification('Success', actionType === 'finalize' ? 'Decision Finalized successfully.' : 'Draft saved successfully.');

                    if (actionType === 'finalize') {
                        this.selectedProtocol = null;
                        document.body.style.overflow = '';
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    const errorData = await response.json();
                    alert(errorData.message || 'Failed to save decision.');
                }
            } catch (error) {
                console.error("Submission error:", error);
                alert('An unexpected network error occurred.');
            } finally {
                this.isLoading = false;
            }
        }
    }));
});
</script>

<script>
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

        const styleOverride = document.createElement('style');
        styleOverride.innerHTML = `
            .driver-popover { font-family: 'Inter', sans-serif !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; padding: 20px !important; }
            .driver-popover-title { color: #213C71 !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 14px !important; }
            .driver-popover-description { color: #6B7280 !important; font-weight: 500 !important; font-size: 12px !important; margin-top: 8px !important; line-height: 1.5 !important; }
            .driver-popover-footer button { border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 8px 12px !important; }
            .driver-popover-next-btn { background-color: #D32F2F !important; color: white !important; border: none !important; text-shadow: none !important; }
            .driver-popover-prev-btn { background-color: #F3F4F6 !important; color: #4B5563 !important; border: none !important; }
        `;
        document.head.appendChild(styleOverride);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runChairRevisionsTutorial(manual = false) {
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (manual) {
            localStorage.removeItem(storageKey);
            localStorage.setItem(storageKey, 'chair_revisions');
        }

        const alpineRoot = document.querySelector('[x-data="decisionData(@js($protocolsData ?? []))"]');

        if (!alpineRoot) {
            console.error('decisionData Alpine component was not found.');
            return;
        }

        const alpineComponent = Alpine.$data(alpineRoot);
        const driver = window.driver.js.driver;

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (alpineComponent.closeModal) {
                    alpineComponent.closeModal();
                }

                if (!tour.hasNextStep()) {
                    localStorage.setItem(storageKey, 'chair_staff');
                    tour.destroy();
                    window.location.href = "{{ route('chair.add-staff') ?? '/chair/staff' }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-revisions-list',
                    popover: {
                        title: 'Revision Approvals',
                        description: 'Validated resubmitted protocols appear here for your final approval.',
                        side: "top",
                        align: 'start',
                        onNextClick: () => {
                            alpineComponent.openValidate({
                                is_mock: true,
                                id: '2026-MOCK-RESUB',
                                version: 'V2',
                                title: 'Effects of AI on System Architecture',
                                proponent: 'Dr. Jane Doe',
                                revisionRows: [
                                    {
                                        id: 1,
                                        item: '1.4',
                                        berc_recommendation: 'Update methodology.',
                                        researcher_response: 'Updated as requested.',
                                        section_and_page: 'Page 4',
                                        action: 'action_required',
                                        remarks: '',
                                        synthesized_comments: 'Researcher failed to update section 2.3.'
                                    }
                                ]
                            });

                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    element: '#tour-revisions-sidebar',
                    popover: {
                        title: 'Navigate Documents',
                        description: 'Use this sidebar to read revised documents or switch between the Decision Letter and feedback history.',
                        side: "right",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-feedback-btn',
                    popover: {
                        title: 'Check Feedback History',
                        description: 'This shows reviewer comments about the researcher’s revisions.',
                        side: "right",
                        align: 'center',
                        onNextClick: () => {
                            alpineComponent.activeView = 'resubmission_form';
                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    element: '#tour-resubmission-panel',
                    popover: {
                        title: 'Synthesized Feedback',
                        description: 'Reviewer comments are consolidated into this table for easier checking.',
                        side: "left",
                        align: 'center',
                        onNextClick: () => {
                            alpineComponent.activeView = 'decision_letter';
                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    element: '#tour-decision-panel',
                    popover: {
                        title: 'Finalize the Letter',
                        description: 'Verify the final Decision Letter and submit it to close the workflow.',
                        side: "left",
                        align: 'center'
                    }
                },
                {
                    popover: {
                        title: 'Next Stop: Staff Management',
                        description: 'Next, let’s look at managing committee members and system access.',
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
        loadDriverThenRun(() => runChairRevisionsTutorial(true));
    };

    loadDriverThenRun(() => {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;
        const tourState = localStorage.getItem(storageKey);

        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (tourState === 'chair_revisions') {
            runChairRevisionsTutorial(false);
        }
    });

});
</script>
@endsection
