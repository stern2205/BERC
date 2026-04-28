@extends('reviewer.layouts.app')

@section('content')
<style>
    /* Layout & Scroll Fixes */
    html { overflow-y: scroll; }
    [x-cloak] { display: none !important; }

    /* Base Colors */
    :root { --bsu-dark: #213C71; --brand-red: #D32F2F; }

    /* Modal Architecture */
    .modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 1550px; height: 92vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

    /* Sidebar Navigation */
    .sidebar-nav-item { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; text-transform: uppercase; transition: all 0.2s; color: #64748b; margin-bottom: 4px; border: none; cursor: pointer; background: transparent; text-align: left; }
    .sidebar-nav-item.active { background: #213C71; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .sidebar-nav-item:hover:not(.active) { background: #f1f5f9; color: #1e293b; }

    /* Table Specifics */
    .sticky-header th { position: sticky; top: 0; background: #f8fafc; z-index: 10; border-bottom: 2px solid #e2e8f0; }
    .table-input-field { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; font-size: 11px; transition: all 0.2s; }
    .table-input-field:focus { background: #fff; border-color: #213C71; box-shadow: 0 0 0 2px rgba(33, 60, 113, 0.1); outline: none; }
    .comment-area { min-height: 80px; resize: vertical; line-height: 1.4; }

    /* Custom Checkbox */
    .action-checkbox { accent-color: #D32F2F; width: 14px; height: 14px; cursor: pointer; }
    .row-has-action { background-color: #fff5f5 !important; }
</style>

<div x-data="assessmentData()" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Protocol Assessment</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Review assigned protocols and submit your evaluation</p>
        </div>
        <div class="flex items-center gap-2 w-full max-w-md">
            <input type="text" x-model="searchQuery" placeholder="Search Protocol Code or Title..." class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-bsu-dark/10">
        </div>
    </div>

    <div id="tour-assessment-list" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-[140px_1fr_180px_120px] gap-4 p-4 border-b border-gray-200 bg-gray-50 text-[10px] font-black uppercase text-gray-500">
            <div>Application ID</div>
            <div>Study Details</div>
            <div>Review Status</div>
            <div class="text-center">Action</div>
        </div>

        <div class="divide-y divide-gray-100">
            <template x-for="protocol in filteredData" :key="protocol.protocol_code">
                <div class="grid grid-cols-[140px_1fr_180px_120px] gap-4 p-4 items-center hover:bg-gray-50 cursor-pointer transition-colors" @click="openValidate(protocol)">
                    <div class="font-mono font-bold text-blue-700 text-xs" x-text="protocol.protocol_code"></div>
                    <div>
                        <div class="text-sm font-bold text-gray-800 leading-tight" x-text="protocol.research_title"></div>
                        <div class="text-[10px] text-gray-400 mt-1 uppercase font-bold" x-text="protocol.primary_researcher"></div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100" x-text="protocol.classification"></span>
                    </div>
                    <div class="text-center">
                        <button class="bg-bsu-dark text-white text-[10px] font-black uppercase px-4 py-2 rounded-lg hover:bg-opacity-90 transition-all">Evaluate</button>
                    </div>
                </div>
            </template>
        </div>

        <template x-if="filteredData.length === 0">
            <div class="p-12 text-center text-gray-400 font-bold italic text-sm">No protocols currently assigned for review.</div>
        </template>
    </div>

    <template x-if="selectedProtocol">
        <div class="modal-overlay" @click.self="closeModal()">
            <div class="modal-box shadow-2xl animate-in zoom-in-95 duration-200">

                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-black text-bsu-dark uppercase tracking-tight">Reviewer Assessment Dashboard</h2>
                        <span class="inline-flex items-center bg-[#eff6ff] border border-[#bfdbfe] text-[#1d4ed8] text-[11px] font-bold font-mono tracking-[0.03em] px-2.5 py-1 rounded-md" x-text="selectedProtocol.protocol_code"></span>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl font-light border-none bg-transparent cursor-pointer">&times;</button>
                </div>

                <div class="flex-1 flex overflow-hidden w-full h-full relative">

                    <div id="tour-assessment-sidebar" class="w-80 flex-shrink-0 border-r border-gray-200 bg-slate-50 overflow-y-auto p-5 z-10">

                        <div class="mb-8">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Review Components</h3>
                            <nav class="space-y-1">
                                <button @click="activeView = 'assessment_form'; activeDocKey = null;" :class="activeView === 'assessment_form' ? 'active' : ''" class="sidebar-nav-item">
                                    <span>📋</span> Assessment Form
                                </button>
                                <button x-show="hasInformedConsent()" @click="activeView = 'informed_consent'; activeDocKey = null;" :class="activeView === 'informed_consent' ? 'active' : ''" class="sidebar-nav-item">
                                    <span>✍️</span> Informed Consent
                                </button>
                            </nav>
                        </div>

                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Protocol Documents</h3>

                            <div x-show="isLoadingDocs" class="text-[10px] text-gray-400 italic py-2">Loading documents...</div>

                            <div x-show="!isLoadingDocs" class="space-y-4">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Basic Requirements</div>

                                    <div @click="activeView = 'appform'; activeDocKey = 'appform'"
                                        :class="activeDocKey === 'appform' ? 'border-bsu-dark bg-blue-50 ring-2 ring-bsu-dark/10' : 'border-gray-200 bg-white hover:border-brand-red'"
                                        class="p-3 border-2 rounded-xl cursor-pointer transition-all flex items-start gap-3 shadow-sm mb-2">
                                        <div class="text-xl leading-none text-[#D32F2F]">📄</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-[11px] font-bold text-slate-700 leading-tight">Application Form</div>
                                            <div class="text-[9px] text-blue-600 font-bold mt-1 break-all leading-snug">System Generated</div>
                                        </div>
                                    </div>

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

                    <div class="flex-1 bg-white overflow-hidden flex flex-col relative z-0">

                        <div x-show="activeView === 'appform'" x-cloak class="h-full overflow-y-auto p-8 bg-slate-50 flex justify-center">
                            <div class="w-full max-w-3xl bg-white border border-gray-300 shadow-sm p-10 rounded h-fit my-auto">
                                <div class="text-center mb-8 pb-5 border-b border-gray-200">
                                    <img src="{{ asset('logo/BERC.png') }}" style="height:45px; margin:0 auto 12px; display:block;" alt="BERC Logo" onerror="this.style.display='none'">
                                    <h3 class="text-xl font-black text-bsu-dark uppercase tracking-tight">Application Overview</h3>
                                    <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase tracking-widest">System Generated Summary</p>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">I. General Information</div>
                                        <div class="grid gap-4 text-sm px-2">
                                            <div class="flex"><span class="w-40 font-bold text-gray-500">Protocol Code:</span> <span class="font-mono font-bold text-blue-700" x-text="selectedProtocol?.protocol_code"></span></div>
                                            <div class="flex"><span class="w-40 font-bold text-gray-500">Research Title:</span> <span class="font-medium text-gray-800" x-text="selectedProtocol?.research_title"></span></div>
                                            <div class="flex"><span class="w-40 font-bold text-gray-500">Lead Researcher:</span> <span class="font-medium text-gray-800" x-text="selectedProtocol?.primary_researcher"></span></div>
                                            <div class="flex"><span class="w-40 font-bold text-gray-500">Classification:</span> <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black uppercase" x-text="selectedProtocol?.classification"></span></div>
                                        </div>
                                    </div>
                                    <div class="pt-6 border-t border-dashed border-gray-200 text-center">
                                        <p class="text-[11px] text-gray-400 italic">Please use the sidebar on the left to select specific documents for detailed review.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeView === 'doc_viewer'" x-cloak class="h-full flex flex-col bg-[#525659]">
                            <div class="bg-white border-b border-gray-200 px-5 py-3 flex justify-between items-center shrink-0 shadow-sm z-10">
                                <div class="flex flex-col">
                                    <h3 class="m-0 text-[13px] font-black text-bsu-dark uppercase tracking-tight" x-text="activeDocTitle"></h3>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Researcher Attachment Preview</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <a :href="activeDocUrl" target="_blank" class="text-[10px] font-bold text-brand-red hover:text-bsu-dark transition-colors underline underline-offset-2">Open Fullscreen ↗</a>
                                    <button @click="activeView = 'assessment_form'; activeDocKey = null" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
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

                        <div id="tour-assessment-form" x-show="activeView === 'assessment_form' || activeView === 'informed_consent'" x-cloak class="h-full overflow-y-auto p-6 bg-slate-100/50">
                            <h2 class="text-sm font-black text-bsu-dark uppercase mb-4 flex items-center gap-2">
                                <span x-text="activeView === 'assessment_form' ? 'Protocol Assessment Items (1.0 - 3.0)' : 'Informed Consent Assessment (4.0)'"></span>
                                <span class="h-px bg-gray-300 flex-1"></span>
                            </h2>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <table class="w-full border-collapse">
                                    <thead class="sticky-header">
                                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-wider text-left">
                                            <th class="px-4 py-4 w-16">Item #</th>
                                            <th class="px-4 py-4 min-w-[250px]">Criteria / Question</th>
                                            <th class="px-4 py-4 w-24 text-center">Remark</th>
                                            <th class="px-4 py-4 w-28 text-center">Line/Pg</th>

                                            <th id="tour-comment-column" class="px-4 py-4 border-l border-gray-200 min-w-[300px]">Your Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="row in (activeView === 'assessment_form' ? selectedProtocol.assessmentRows : selectedProtocol.consentRows)" :key="row.question_number">
                                            <tr class="transition-colors hover:bg-blue-50/30" :class="{'row-has-action': row.action_required}">
                                                <td class="px-4 py-4 font-bold text-bsu-dark text-xs align-top" x-text="row.question_number"></td>
                                                <td class="px-4 py-4 text-[11px] text-gray-700 font-medium leading-relaxed pr-6 align-top" x-text="row.question_text"></td>

                                                <td class="px-4 py-4 align-top">
                                                    <div class="text-[11px] font-black border border-gray-200 rounded bg-gray-100 text-gray-500 w-full p-2 text-center cursor-not-allowed" x-text="row.remark"></div>
                                                </td>

                                                <td class="px-4 py-4 align-top">
                                                    <div class="text-[11px] font-bold border border-gray-200 rounded bg-gray-100 text-gray-500 w-full p-2 text-center min-h-[34px] flex items-center justify-center cursor-not-allowed" x-text="row.line_page"></div>
                                                </td>

                                                <td class="px-4 py-4 border-l border-gray-200 align-top">
                                                    <textarea x-model="row.reviewer_comments" @input="triggerAutosave()" class="table-input-field comment-area" :class="{'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-100': row.action_required}" placeholder="Type your findings..."></textarea>
                                                    <label class="mt-2 flex items-center gap-2 cursor-pointer w-max select-none">
                                                        <input type="checkbox" x-model="row.action_required" @change="triggerAutosave()" class="action-checkbox">
                                                        <span class="text-[10px] font-black uppercase" :class="row.action_required ? 'text-brand-red' : 'text-gray-400 hover:text-gray-600'">
                                                            Flag as Action Required
                                                        </span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-lg flex items-start gap-3">
                                <span class="text-blue-500">ℹ️</span>
                                <p class="text-[11px] text-blue-700 leading-relaxed">
                                    <strong>Note:</strong> Check the "Flag as Action Required" box if the researcher needs to revise this specific item based on your comments.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tour-modal-footer" class="px-6 py-4 bg-slate-50 border-t flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <p class="text-[10px] font-bold text-slate-400 italic uppercase">Your comments will be saved securely to your assigned reviewer slot.</p>
                        <span x-text="draftStatusMsg" class="text-[10px] font-bold text-green-600 ml-4 transition-opacity duration-300"></span>
                    </div>

                    <div class="flex gap-3">
                        <button @click="closeModal()" class="px-6 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-200 transition-colors uppercase border-none bg-transparent cursor-pointer">Cancel</button>
                        <button @click="submitValidation()" :disabled="isSubmitting" class="bg-[#D32F2F] text-white px-8 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer disabled:opacity-50 flex items-center gap-2">
                            <svg x-show="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Saving...' : 'Save Assessment'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div x-show="confirmSubmitOpen" x-cloak class="fixed inset-0 z-[2100] flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
                <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Confirm Assessment Submission</h3>
                <p class="text-[11px] text-gray-500 font-semibold mt-1">
                    Some assessment items have no input. You may still continue if you confirm.
                </p>
            </div>

            <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                <template x-if="missingInputItems.length > 0">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-red-600 mb-3">
                            Missing Input on These Item Numbers:
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <template x-for="item in missingInputItems" :key="item.view + '-' + item.number">
                                <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200 text-[11px] font-black"
                                    x-text="item.number + (item.view === 'informed_consent' ? ' (ICF)' : '')"></span>
                            </template>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full border-collapse">
                                <thead class="bg-gray-50">
                                    <tr class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                        <th class="px-4 py-3 text-left w-24">Item #</th>
                                        <th class="px-4 py-3 text-left">Question</th>
                                        <th class="px-4 py-3 text-left w-40">Section</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in missingInputItems" :key="'row-' + item.view + '-' + item.number">
                                        <tr>
                                            <td class="px-4 py-3 text-[11px] font-black text-bsu-dark" x-text="item.number"></td>
                                            <td class="px-4 py-3 text-[11px] text-gray-700" x-text="item.text"></td>
                                            <td class="px-4 py-3 text-[10px] font-bold uppercase text-gray-500"
                                                x-text="item.view === 'informed_consent' ? 'Informed Consent' : 'Assessment Form'"></td>
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
                <button @click="confirmSubmitOpen = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors uppercase border-none bg-transparent cursor-pointer">
                    Go Back
                </button>

                <button @click="confirmAndSubmit()"
                        :disabled="isSubmitting"
                        class="bg-[#D32F2F] text-white px-6 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer disabled:opacity-50">
                    Confirm Submit
                </button>
            </div>
        </div>
    </div>

    <div x-show="notificationOpen" style="display:none;" class="fixed bottom-6 right-6 z-[2000] bg-[#213C71] text-white p-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-blue-400">
        <div class="bg-white/20 p-2 rounded-lg">✅</div>
        <div>
            <p class="text-[10px] font-black uppercase opacity-60" x-text="notificationTitle"></p>
            <p class="text-xs font-bold" x-text="notificationMessage"></p>
        </div>
        <button @click="notificationOpen = false" class="ml-4 text-white/50 hover:text-white border-none bg-transparent cursor-pointer text-lg">&times;</button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('assessmentData', () => ({
        searchQuery: '',
        isSubmitting: false,
        forValidation: @json($research_applications ?? []),

        selectedProtocol: null,
        activeView: 'assessment_form',
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: '',

        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,
        autosaveTimer: null,
        draftStatusMsg: '',
        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',

        // NEW
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

        assessmentQuestions: [
            { num: '1.1', text: 'Objectives – Review of viability of expected output' },
            { num: '1.2', text: 'Literature review – Review of results of previous animal/human studies showing known risks and benefits of intervention, including known adverse drug effects, in case of drug trials' },
            { num: '1.3', text: 'Research design – Review of appropriateness of design in view of objectives' },
            { num: '1.4', text: 'Sampling design – Review of appropriateness of sampling methods and techniques' },
            { num: '1.5', text: 'Sample size – Review of justification of sample size' },
            { num: '1.6', text: 'Statistical analysis plan (SAP) – Review of appropriateness of statistical methods to be used and how participant data will be summarized' },
            { num: '1.7', text: 'Data analysis plan – Review of appropriateness of statistical and non-statistical methods of data analysis' },
            { num: '1.8', text: 'Inclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of equitable selection' },
            { num: '1.9', text: 'Exclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of justified selection' },
            { num: '1.10', text: 'Exclusion criteria – Review of criteria precision both for scientific merit and safety concerns' },
            { num: '1.11', text: 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled' },
            { num: '1.12', text: 'Statement that the study involves research' },
            { num: '1.13', text: 'Approximate number of participants in the study' },
            { num: '1.14', text: 'Expected benefits to the community or to society, or contributions to scientific knowledge' },
            { num: '1.15', text: 'Description of post-study access to the study product or intervention that have been proven safe and effective' },
            { num: '1.16', text: 'Anticipated payment, if any, to the participant in the course of the study; whether money or other forms of material goods, and if so, the kind and amount' },
            { num: '1.17', text: 'Anticipated expenses, if any, to the participant in the course of the study' },
            { num: '1.18', text: 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data' },

            { num: '2.1', text: 'Review of specimen storage, access, disposal, and terms of use' },
            { num: '2.2', text: 'Review of CV and relevant certifications of the Principal Investigator to ascertain capability to manage study related risks' },
            { num: '2.3', text: 'Review of adequacy of qualified staff and infrastructures (site suitability)' },
            { num: '2.4', text: 'Review of length/extent of human participant involvement in the study' },

            { num: '3.1', text: 'Review of management of conflict arising from financial, familial, or proprietary considerations of the Principal Investigator, sponsor, or study site' },
            { num: '3.2', text: 'Review of measures to protect privacy and confidentiality of participant information, including data protection plans' },
            { num: '3.3', text: 'Review of informed consent process including who may solicit consent, how and when consent will be obtained, and from whom' },
            { num: '3.4', text: 'Review of involvement of vulnerable populations and its impact on the informed consent process' },
            { num: '3.5', text: 'Review of recruitment methods including appropriateness of identified recruiting parties' },
            { num: '3.6', text: 'Review of feasibility of obtaining assent and applicability of assent requirements for minors or legally incapable participants' },
            { num: '3.7', text: 'Review of level of risks and measures to mitigate these risks, including adverse event management and justification for use of placebo where applicable' },
            { num: '3.8', text: 'Review of potential direct benefits to participants and contribution to generalizable knowledge' },
            { num: '3.9', text: 'Review of amount and method of compensation, incentives, and reimbursement of study-related expenses' },
            { num: '3.10', text: 'Review of community impact including cultural sensitivity, stigma risks, and community involvement' },
            { num: '3.11', text: 'Review of collaborative study terms including intellectual property rights, publication rights, transparency, and capacity building' }
        ],

        consentQuestions: [
            { num: '4.1', text: 'Purpose of the study' },
            { num: '4.2', text: 'Expected duration of participation' },
            { num: '4.3', text: 'Procedures to be carried out' },
            { num: '4.4', text: 'Discomforts and inconveniences' },
            { num: '4.5', text: 'Risks (including possible discrimination)' },
            { num: '4.6', text: 'Random assignment to the trial treatments' },
            { num: '4.7', text: 'Benefits to the participants' },
            { num: '4.8', text: 'Alternative treatments procedures' },
            { num: '4.9', text: 'Compensation and/or medical treatments in case of injury' },
            { num: '4.10', text: 'Who to contact for pertinent questions and/or for assistance in a research-related injury' },
            { num: '4.11', text: 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled' },
            { num: '4.12', text: 'Statement that the study involves research' },
            { num: '4.13', text: 'Approximate number of participants in the study' },
            { num: '4.14', text: 'Expected benefits to the community or to society, or contributions to scientific knowledge' },
            { num: '4.15', text: 'Description of post-study access to the study product or intervention that have been proven safe and effective' },
            { num: '4.16', text: 'Anticipated payment, if any, to the participant in the course of the study; whether money or other forms of material goods, and if so, the kind and amount' },
            { num: '4.17', text: 'Anticipated expenses, if any, to the participant in the course of the study' },
            { num: '4.18', text: 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data' },
            { num: '4.19', text: 'Statement describing extent of participant’s right to access his/her records (or lack thereof)' },
            { num: '4.20', text: 'Description of policy regarding genetic testing and protection of familial genetic information' },
            { num: '4.21', text: 'Possible direct or secondary use of participant’s medical records and biological specimens' },
            { num: '4.22', text: 'Plans for storage or destruction of biological specimens and participant rights regarding their use' },
            { num: '4.23', text: 'Plans to develop commercial products from biological specimens and whether participant will receive benefits' },
            { num: '4.24', text: 'Statement that the BERC has approved the study and may be contacted regarding participant rights, grievances, and complaints' }
        ],

        init() {},

        async openValidate(protocol) {
            this.selectedProtocol = protocol;
            this.activeView = 'assessment_form';
            this.activeDocKey = null;
            this.draftStatusMsg = '';
            this.confirmSubmitOpen = false;
            this.missingInputItems = [];
            document.body.style.overflow = 'hidden';

            const slot = protocol.reviewer_slot;
            const actionSlot = slot ? slot.replace('comments', 'action_required') : null;

            const dbAssessmentRows = protocol.assessmentRows || [];
            const dbConsentRows = protocol.consentRows || [];

            this.selectedProtocol.assessmentRows = this.assessmentQuestions.map(q => {
                const dbRow = dbAssessmentRows.find(r => r.question_number === q.num) || {};
                return {
                    question_number: q.num,
                    question_text: q.text,
                    remark: dbRow.remark || 'N/A',
                    line_page: dbRow.line_page || '—',
                    reviewer_comments: slot && dbRow[slot] ? dbRow[slot] : '',
                    action_required: actionSlot && dbRow[actionSlot] ? !!dbRow[actionSlot] : false
                };
            });

            this.selectedProtocol.consentRows = this.consentQuestions.map(q => {
                const dbRow = dbConsentRows.find(r => r.question_number === q.num) || {};
                return {
                    question_number: q.num,
                    question_text: q.text,
                    remark: dbRow.remark || 'N/A',
                    line_page: dbRow.line_page || '—',
                    reviewer_comments: slot && dbRow[slot] ? dbRow[slot] : '',
                    action_required: actionSlot && dbRow[actionSlot] ? !!dbRow[actionSlot] : false
                };
            });

            if (protocol.is_mock) {
                this.loadedDocs = {
                    activeBasic: [{ id: 'doc-mock-1', label: 'Study Protocol', desc: 'PDF Document', url: '', isRevised: false }],
                    activeSupp: [],
                    legacy: []
                };
                this.isLoadingDocs = false;
                return;
            }

            try {
                const draftRes = await fetch(`/reviewer/assessment/${protocol.protocol_code}/draft`);
                if (draftRes.ok) {
                    const draftData = await draftRes.json();
                    if (draftData && draftData.assessment_rows) {
                        this.selectedProtocol.assessmentRows.forEach(r => {
                            const dRow = draftData.assessment_rows.find(dr => dr.question_number === r.question_number);
                            if (dRow) {
                                r.reviewer_comments = dRow.reviewer_comments;
                                r.action_required = dRow.action_required;
                            }
                        });
                    }
                    if (draftData && draftData.consent_rows && this.hasInformedConsent()) {
                        this.selectedProtocol.consentRows.forEach(r => {
                            const dRow = draftData.consent_rows.find(dr => dr.question_number === r.question_number);
                            if (dRow) {
                                r.reviewer_comments = dRow.reviewer_comments;
                                r.action_required = dRow.action_required;
                            }
                        });
                    }
                    if (draftData) {
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
                if (this.selectedProtocol?.protocol_code) name = name.replace(`_${this.selectedProtocol.protocol_code}`, '');
                name = name.replace('resubmit_', '');
                return name.replace(/[-_]/g, ' ').toUpperCase().trim();
            };

            this.isLoadingDocs = true;
            this.loadedDocs = { activeBasic: [], activeSupp: [], legacy: [] };

            try {
                const response = await fetch(`/documents/api/${protocol.protocol_code}`);
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
                                const displayName = doc.description || parseFileName(path);
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
                console.error("Doc Load Error:", e);
            }
            this.isLoadingDocs = false;
        },

        viewDocument(id, url, label) {
            this.activeDocKey = id;
            this.activeDocUrl = url;
            this.activeDocTitle = label;
            this.activeView = 'doc_viewer';
        },

        hasInformedConsent() {
            return this.selectedProtocol &&
                this.selectedProtocol.has_icf_assignment === true &&
                this.selectedProtocol.consentRows.length > 0;
        },

        closeModal() {
            this.selectedProtocol = null;
            this.activeDocKey = null;
            this.activeDocUrl = null;
            this.confirmSubmitOpen = false;
            this.missingInputItems = [];
            document.body.style.overflow = '';
        },

        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
            setTimeout(() => this.notificationOpen = false, 5000);
        },

        triggerAutosave() {
            this.draftStatusMsg = 'Saving...';
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.saveDraft(), 1500);
        },

        async saveDraft() {
            if (!this.selectedProtocol) return;
            const protocolId = this.selectedProtocol.protocol_code;
            const payload = {
                assessment_rows: this.selectedProtocol.assessmentRows,
                consent_rows: this.hasInformedConsent() ? this.selectedProtocol.consentRows : null
            };

            try {
                const response = await fetch(`/reviewer/assessment/${protocolId}/draft`, {
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

        // NEW
        getMissingInputItems() {
            const missing = [];

            const checkRows = (rows, viewName) => {
                (rows || []).forEach(row => {
                    const comment = (row.reviewer_comments || '').trim();

                    if (comment === '') {
                        missing.push({
                            number: row.question_number,
                            text: row.question_text,
                            view: viewName
                        });
                    }
                });
            };

            checkRows(this.selectedProtocol?.assessmentRows || [], 'assessment_form');

            if (this.hasInformedConsent()) {
                checkRows(this.selectedProtocol?.consentRows || [], 'informed_consent');
            }

            return missing;
        },

        // NEW
        submitValidation() {
            if (this.isSubmitting || !this.selectedProtocol) return;
            this.missingInputItems = this.getMissingInputItems();
            this.confirmSubmitOpen = true;
        },

        // NEW
        async confirmAndSubmit() {
            if (this.isSubmitting || !this.selectedProtocol) return;

            this.confirmSubmitOpen = false;
            this.isSubmitting = true;

            const protocolId = this.selectedProtocol.protocol_code;
            const payload = {
                assessment_rows: this.selectedProtocol.assessmentRows,
                consent_rows: this.hasInformedConsent() ? this.selectedProtocol.consentRows : null
            };

            try {
                const response = await fetch(`/reviewer/assessment/${protocolId}/validate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    this.showNotification('Success', 'Your assessment has been saved successfully.');
                    this.forValidation = this.forValidation.filter(p => p.protocol_code !== protocolId);
                    this.closeModal();
                } else {
                    alert(result.message || "Failed to submit. Please check your comments.");
                }
            } catch (e) {
                console.error("Submission Error:", e);
                alert("A network error occurred. Please try again.");
            } finally {
                this.isSubmitting = false;
            }
        },

        get filteredData() {
            const s = this.searchQuery.toLowerCase();
            return this.forValidation.filter(p =>
                p.protocol_code.toLowerCase().includes(s) ||
                (p.research_title || '').toLowerCase().includes(s)
            );
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

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runReviewerAssessmentTutorial(manual = false) {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (manual) {
            localStorage.removeItem(storageKey);
            localStorage.setItem(storageKey, 'rev_assessment');
        }

        if (!manual && !isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        const tourState = localStorage.getItem(storageKey);

        if (tourState === 'rev_assessment_manual_skip') {
            localStorage.removeItem(storageKey);
            return;
        }

        if (!manual && tourState !== 'rev_assessment') {
            return;
        }

        const driver = window.driver.js.driver;
        const alpineComponent = Alpine.$data(document.querySelector('[x-data="assessmentData()"]'));

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (!tour.hasNextStep()) {
                    alpineComponent.closeModal();

                    if (manual) {
                        localStorage.setItem(storageKey, 'rev_resubmissions_manual_skip');
                    } else {
                        localStorage.setItem(storageKey, 'rev_resubmissions');
                    }

                    tour.destroy();
                    window.location.href = "{{ route('reviewer.resubmissions') ?? '/reviewer/resubmissions' }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-assessment-list',
                    popover: {
                        title: 'Your Assessment Queue',
                        description: 'Once you accept an invitation, the protocol moves here for your official evaluation. Click any row to open the Assessment Dashboard.',
                        side: "top",
                        align: 'start',
                        onNextClick: () => {
                            alpineComponent.openValidate({
                                is_mock: true,
                                protocol_code: '2026-MOCK-002',
                                research_title: 'Effects of AI on System Architecture',
                                primary_researcher: 'Dr. Jane Doe',
                                classification: 'Full Board'
                            });

                            setTimeout(() => {
                                tour.moveNext();
                            }, 300);
                        }
                    }
                },
                {
                    element: '#tour-assessment-sidebar',
                    popover: {
                        title: '1. Navigation & Documents',
                        description: 'Use this sidebar to toggle back and forth between filling out your Assessment Form and reading the actual protocol documents provided by the researcher.',
                        side: "right",
                        align: 'start'
                    }
                },
                {
                    element: '#tour-assessment-form',
                    popover: {
                        title: '2. The Evaluation Matrix',
                        description: 'This is the standardized criteria for evaluating the protocol. You will see the researcher’s self-assessed remarks and page references here.',
                        side: "left",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-comment-column',
                    popover: {
                        title: '3. Adding Feedback',
                        description: 'Type your detailed findings in the comment box. If a specific issue must be fixed by the researcher before approval, check the Flag as Action Required box.',
                        side: "bottom",
                        align: 'center'
                    }
                },
                {
                    element: '#tour-modal-footer',
                    popover: {
                        title: '4. Autosave & Submission',
                        description: 'Your progress automatically saves as a draft while you type. When finished evaluating all rows, click Save Assessment to submit your final review.',
                        side: "top",
                        align: 'center',
                        onNextClick: () => {
                            alpineComponent.closeModal();

                            setTimeout(() => {
                                tour.moveNext();
                            }, 300);
                        }
                    }
                },
                {
                    popover: {
                        title: 'Next Stop: Resubmissions',
                        description: 'If a protocol is returned for changes, the researcher will resubmit it. Let’s see how to handle those revisions next.',
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
        loadDriverThenRun(() => runReviewerAssessmentTutorial(true));
    };

    loadDriverThenRun(() => runReviewerAssessmentTutorial(false));
});
</script>
@endsection
