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
</style>

<div x-data="revisionData()" x-cloak class="flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-500 max-w-7xl mx-auto pb-6">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Revision Validation</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Validate protocol revisions assigned to you</p>
        </div>
        <div class="flex items-center gap-2 w-full max-w-md">
            <input type="text" placeholder="Search Protocol Code or Title..." x-model="searchQuery" class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-bsu-dark/10" />
        </div>
    </div>

    <div id="tour-resub-tabs" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'validate'" :class="activeTab === 'validate' ? 'border-brand-red text-bsu-dark bg-blue-50/30' : 'border-transparent text-gray-500 hover:text-bsu-dark hover:bg-gray-50'" class="flex items-center gap-3 px-8 py-4 font-black text-[15px] uppercase tracking-[0.08em] transition-all border-b-4 relative">
                <svg class="w-4 h-4" :class="activeTab === 'validate' ? 'text-brand-red' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>To Validate</span>
            </button>
            <button @click="activeTab = 'validated'" :class="activeTab === 'validated' ? 'border-brand-red text-bsu-dark bg-blue-50/30' : 'border-transparent text-gray-500 hover:text-bsu-dark hover:bg-gray-50'" class="flex items-center gap-3 px-8 py-4 font-black text-[15px] uppercase tracking-[0.08em] transition-all border-b-4 relative">
                <svg class="w-4 h-4" :class="activeTab === 'validated' ? 'text-brand-red' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>History</span>
            </button>
        </div>
    </div>

    <div class="space-y-6">
        <div id="tour-resub-list" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3 flex justify-end">
                <div class="flex-[0.3] relative">
                    <select x-model="sortOrder" class="w-full appearance-none pl-4 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-bsu-dark focus:outline-none">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="w-[40%] px-4 py-4 text-[10px] font-bold uppercase tracking-[0.06em] text-gray-500">Protocol Details</th>
                            <th class="w-[25%] px-4 py-4 text-[10px] font-bold uppercase tracking-[0.06em] text-gray-500">Proponent</th>
                            <th class="w-[15%] px-4 py-4 text-[10px] font-bold uppercase tracking-[0.06em] text-gray-500">Date</th>
                            <th class="w-[20%] px-4 py-4 text-[10px] font-bold uppercase tracking-[0.06em] text-gray-500 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="protocol in filteredData" :key="protocol.id + protocol.version">
                            <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openValidate(protocol)">
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[11px] font-mono font-bold text-blue-700 uppercase" x-text="protocol.id"></span>
                                            <span class="bg-blue-50 text-bsu-dark px-1.5 py-0.5 rounded text-[9px] font-black" x-text="protocol.version"></span>
                                        </div>
                                        <span class="text-[13px] font-bold text-gray-800 truncate leading-tight" x-text="protocol.title"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4"><span class="text-[10px] uppercase font-bold text-gray-400" x-text="protocol.proponent"></span></td>
                                <td class="px-4 py-4">
                                    <span class="text-[11px] font-bold text-[#1f377d] uppercase" x-text="activeTab === 'validate' ? protocol.dateSubmitted : protocol.dateValidated"></span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-[0.06em] transition-all shadow-sm bg-bsu-dark text-white hover:bg-opacity-90">
                                        <span x-text="activeTab === 'validate' ? 'Review' : 'View Details'"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredData.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 font-bold italic text-sm">
                                    No revision tasks found.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <template x-if="selectedProtocol">
        <div class="modal-overlay" @click.self="closeModal()">
            <div class="modal-box shadow-2xl animate-in zoom-in-95 duration-200">

                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white shrink-0">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-black text-bsu-dark uppercase tracking-tight">Revision Assessment Dashboard</h2>
                        <span class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-700 text-[11px] font-bold font-mono tracking-[0.03em] px-2.5 py-1 rounded-md" x-text="selectedProtocol.id + ' • ' + selectedProtocol.version"></span>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl font-light border-none bg-transparent cursor-pointer">&times;</button>
                </div>

                <div class="flex-1 flex overflow-hidden">

                    <div id="tour-resub-sidebar" class="w-80 shrink-0 border-r border-gray-200 bg-slate-50 overflow-y-auto p-5">
                        <div class="mb-8">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Review Components</h3>
                            <nav class="space-y-1">
                                <button @click="activeView = 'resubmission_form'; activeDocKey = null; activeDocUrl = null;" :class="activeView === 'resubmission_form' ? 'active' : ''" class="sidebar-nav-item">
                                    <span>📋</span> Resubmission Form
                                </button>
                            </nav>
                        </div>

                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Protocol Documents</h3>

                            <div x-show="isLoadingDocs" class="text-[10px] text-gray-400 italic py-2">Loading documents...</div>

                            <div x-show="!isLoadingDocs" class="space-y-4">
                                <div>
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
                        </div>
                    </div>

                    <div class="flex-1 bg-white overflow-hidden flex flex-col relative z-0">

                        <div id="tour-resub-form" x-show="activeView === 'resubmission_form'" class="h-full overflow-y-auto bg-slate-100/50 flex flex-col">
                            <div class="p-6 pb-2">
                                <h2 class="text-sm font-black text-bsu-dark uppercase mb-4 flex items-center gap-2">
                                    <span>Recommendations & Responses</span>
                                    <span class="h-px bg-gray-300 flex-1"></span>
                                </h2>
                            </div>

                            <div class="flex-1 px-6 pb-6 overflow-y-auto">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    <table class="w-full border-collapse table-fixed">
                                        <thead class="sticky-header shadow-sm">
                                            <tr>
                                                <th class="w-[20%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-left bg-gray-50">BERC Recommendation</th>
                                                <th class="w-[22%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-left bg-gray-50">Researcher Response</th>
                                                <th class="w-[14%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-left bg-gray-50">Page / Section</th>
                                                <th class="w-[26%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-left bg-gray-50">Reviewer Remarks</th>
                                                <th class="w-[18%] px-4 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-center bg-gray-50">Status Action</th>
                                            </tr>
                                        </thead>

                                        <template x-for="group in groupedRows" :key="group.sectionName">
                                            <tbody class="divide-y divide-gray-200 border-b border-gray-200">
                                                <tr class="bg-blue-50/50">
                                                    <td colspan="5" class="px-4 py-2 border-b border-gray-200">
                                                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-widest flex items-center gap-2">
                                                            <span class="w-2 h-2 rounded-full bg-blue-900"></span>
                                                            <span x-text="group.sectionName"></span>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <template x-for="(row, index) in group.rows" :key="row.id">
                                                    <tr class="transition-colors duration-300 align-top"
                                                        :class="{
                                                            'bg-white hover:bg-gray-50': !row.action,
                                                            'bg-green-50': row.action === 'resolved',
                                                            'bg-red-50': row.action === 'action_required'
                                                        }">

                                                        <td class="px-4 py-5">
                                                            <div class="flex flex-col gap-1.5">
                                                                <template x-if="row.item">
                                                                    <span class="font-black text-blue-900 uppercase text-[10px] tracking-widest"
                                                                          x-text="'Item ' + row.item + (questionLabels[row.item] ? ' (' + questionLabels[row.item] + ')' : '')">
                                                                    </span>
                                                                </template>

                                                                <p class="text-[11px] text-gray-700 italic leading-relaxed"
                                                                   x-text="row.berc_recommendation">
                                                                </p>
                                                            </div>
                                                        </td>

                                                        <td class="px-4 py-5 border-l border-white/50">
                                                            <p class="text-[11px] font-bold text-bsu-dark leading-relaxed" x-text="row.researcher_response"></p>
                                                        </td>

                                                        <td class="px-4 py-5 border-l border-white/50">
                                                            <span class="text-[10px] font-black uppercase px-2 py-1 rounded border inline-block"
                                                                  :class="{
                                                                      'bg-gray-100 text-gray-600 border-gray-200': !row.action,
                                                                      'bg-green-100 text-green-700 border-green-200': row.action === 'resolved',
                                                                      'bg-red-100 text-red-700 border-red-200': row.action === 'action_required'
                                                                  }"
                                                                  x-text="row.section_and_page"></span>
                                                        </td>

                                                        <td class="px-4 py-5 border-l border-white/50">
                                                            <textarea x-model="row.remarks"
                                                                      @input="triggerAutosave()"
                                                                      :disabled="activeTab === 'validated'"
                                                                      class="w-full border rounded-lg p-2.5 text-[11px] focus:outline-none focus:ring-2 resize-y min-h-[60px] transition-all shadow-inner disabled:opacity-70 disabled:cursor-not-allowed"
                                                                      :class="{
                                                                          'bg-white border-gray-200 focus:ring-bsu-dark/20 focus:border-bsu-dark text-gray-700': !row.action,
                                                                          'bg-green-100 border-green-300 focus:ring-green-500/20 focus:border-green-500 text-green-800 placeholder-green-600/50': row.action === 'resolved',
                                                                          'bg-red-100 border-red-300 focus:ring-red-500/20 focus:border-red-500 text-red-800 placeholder-red-600/50': row.action === 'action_required'
                                                                      }"
                                                                      placeholder="Add specific remarks or instructions..."></textarea>
                                                        </td>

                                                        <td class="px-3 py-5 border-l border-white/50 text-center align-middle">
                                                            <div class="relative w-full group">
                                                                <select x-model="row.action"
                                                                        @change="triggerAutosave()"
                                                                        :disabled="activeTab === 'validated'"
                                                                        class="appearance-none w-full border-2 rounded-lg pl-2 pr-7 py-2.5 text-[10px] font-black uppercase outline-none transition-all shadow-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed truncate"
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
                                        </template>
                                    </table>
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
                                    <button @click="activeView = 'resubmission_form'; activeDocKey = null; activeDocUrl = null" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors border-none cursor-pointer shadow-sm">
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

                <div id="tour-resub-footer" class="px-6 py-4 bg-slate-50 border-t flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        <p class="text-[10px] font-bold text-slate-400 italic uppercase" x-text="activeTab === 'validate' ? 'Set action status and add remarks for all items to proceed.' : 'Viewing past validation.'"></p>
                        <span x-text="draftStatusMsg" class="text-[10px] font-bold text-green-600 transition-opacity duration-300"></span>
                    </div>

                    <div class="flex gap-3">
                        <button @click="closeModal()" class="px-6 py-2.5 rounded-lg text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:text-gray-800 transition-colors border border-gray-200 hover:bg-gray-100 bg-white cursor-pointer">Close</button>
                        <button x-show="activeTab === 'validate'" @click="submitValidation()" class="bg-brand-red text-white px-8 py-2.5 rounded-lg text-[11px] font-black shadow-md hover:shadow-lg hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Confirm Validation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

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
        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
                <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Confirm Revision Validation</h3>
                <p class="text-[11px] text-gray-500 font-semibold mt-1">
                    Some rows still have missing inputs. You may still continue if you confirm.
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
                                        <th class="px-4 py-3 text-left w-[38%]">Item</th>
                                        <th class="px-4 py-3 text-left w-[22%]">Section / Page</th>
                                        <th class="px-4 py-3 text-left">Missing Fields</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in missingInputItems" :key="item.id">
                                        <tr>
                                            <td class="px-4 py-3 text-[11px] font-bold text-bsu-dark whitespace-normal break-words"
                                                x-text="item.itemDisplay"></td>
                                            <td class="px-4 py-3 text-[11px] text-gray-700"
                                                x-text="item.sectionAndPage || '—'"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="part in item.missingParts" :key="part">
                                                        <span class="px-2 py-1 rounded-md bg-red-50 text-red-700 border border-red-200 text-[10px] font-black"
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

                <button @click="confirmAndSubmitValidation()"
                        class="bg-[#D32F2F] text-white px-6 py-2 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:bg-red-700 transition-all uppercase tracking-widest border-none cursor-pointer">
                    Confirm Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('revisionData', () => ({
        activeTab: 'validate',
        searchQuery: '',
        sortOrder: 'newest',
        revisions: @json($revisions),
        validatedRevisions: @json($validatedRevisions),

        selectedProtocol: null,
        protocolRows: [],

        activeView: 'resubmission_form',
        activeDocKey: null,
        activeDocUrl: null,
        activeDocTitle: '',

        loadedDocs: { activeBasic: [], activeSupp: [], legacy: [] },
        isLoadingDocs: false,

        notificationOpen: false,
        notificationTitle: '',
        notificationMessage: '',

        confirmSubmitOpen: false,
        missingInputItems: [],

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

        questionLabels: {
            '1.1': 'Objectives – Review of viability of expected output',
            '1.2': 'Literature review – Review of results of previous animal/human studies showing known risks and benefits of intervention, including known adverse drug effects, in case of drug trials',
            '1.3': 'Research design – Review of appropriateness of design in view of objectives',
            '1.4': 'Sampling design – Review of appropriateness of sampling methods and techniques',
            '1.5': 'Sample size – Review of justification of sample size',
            '1.6': 'Statistical analysis plan (SAP) – Review of appropriateness of statistical methods to be used and how participant data will be summarized',
            '1.7': 'Data analysis plan – Review of appropriateness of statistical and non-statistical methods of data analysis',
            '1.8': 'Inclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of equitable selection',
            '1.9': 'Exclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of justified selection',
            '1.10': 'Exclusion criteria – Review of criteria precision both for scientific merit and safety concerns',
            '1.11': 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',
            '1.12': 'Statement that the study involves research',
            '1.13': 'Approximate number of participants in the study',
            '1.14': 'Expected benefits to the community or to society, or contributions to scientific knowledge',
            '1.15': 'Description of post-study access to the study product or intervention that have been proven safe and effective',
            '1.16': 'Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount',
            '1.17': 'Anticipated expenses, if any, to the participant in the course of the study',
            '1.18': 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data',

            '2.1': 'Specimen handling – Review of specimen storage, access, disposal, and terms of use',
            '2.2': 'Principal Investigator qualifications – Review of CV and relevant certifications to ascertain capability to manage study related risks',
            '2.3': 'Suitability of site – Review of adequacy of qualified staff and infrastructures',
            '2.4': 'Duration – Review of length/extent of human participant involvement in the study',

            '3.1': 'Conflict of interest – Review of management of conflict arising from financial, familial, or proprietary considerations of the Principal Investigator, sponsor, or the study site',
            '3.2': 'Privacy and confidentiality – Review of measures or guarantees to protect privacy and confidentiality of participant information as indicated by data collection methods including data protection plans',
            '3.3': 'Informed consent process – Review of application of the principle of respect for persons, who may solicit consent, how and when it will be done; who may give consent especially in case of special populations like minors and those who are not legally competent to give consent, or indigenous people which require additional clearances',
            '3.4': 'Vulnerable study populations – Review of involvement of vulnerable study populations and impact on informed consent. Vulnerable groups include children, the elderly, ethnic and racial minority groups, the homeless, prisoners, people with incurable disease, people who are politically powerless, or junior members of a hierarchical group',
            '3.5': 'Recruitment methods – Review of manner of recruitment including appropriateness of identified recruiting parties',
            '3.6': 'Assent requirements – Review of feasibility of obtaining assent vis à vis incompetence to consent; Review of applicability of the assent age brackets in children (0-under 7: No assent; 7-under 12: Verbal Assent; 12-under 15: Simplified Assent Form; 15-under 18: Co-sign informed consent form)',
            '3.7': 'Risks and mitigation – Review of level of risk and measures to mitigate these risks (including physical, psychological, social, economic), including plans for adverse event management; Review of justification for allowable use of placebo as detailed in the Declaration of Helsinki',
            '3.8': 'Benefits – Review of potential direct benefit to participants; the potential to yield generalizable knowledge about the participant’s condition/problem; non-material compensation to participant (health education or other creative benefits), where no clear, direct benefit from the project will be received by the participant',
            '3.9': 'Financial compensation – Review of amount and method of compensations, financial incentives, or reimbursement of study-related expenses',
            '3.10': 'Community impact – Review of impact of the research on the community where the research occurs and/or to whom findings can be linked; including issues like stigma or draining of local capacity; sensitivity to cultural traditions, and involvement of the community in decisions about the conduct of study',
            '3.11': 'Collaborative studies – Review in terms of collaborative study especially in case of multi-country/multi-institutional studies, including intellectual property rights, publication rights, information and responsibility sharing, transparency, and capacity building',

            '4.1': 'Purpose of the study',
            '4.2': 'Expected duration of participation',
            '4.3': 'Procedures to be carried out',
            '4.4': 'Discomforts and inconveniences',
            '4.5': 'Risks (including possible discrimination)',
            '4.6': 'Random assignment to the trial treatments',
            '4.7': 'Benefits to the participants',
            '4.8': 'Alternative treatments procedures',
            '4.9': 'Compensation and/or medical treatments in case of injury',
            '4.10': 'Who to contact for pertinent questions and/or for assistance in a research-related injury',
            '4.11': 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',
            '4.12': 'Statement that the study involves research',
            '4.13': 'Approximate number of participants in the study',
            '4.14': 'Expected benefits to the community or to society, or contributions to scientific knowledge',
            '4.15': 'Description of post-study access to the study product or intervention that have been proven safe and effective',
            '4.16': 'Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount',
            '4.17': 'Anticipated expenses, if any, to the participant in the course of the study',
            '4.18': 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data',
            '4.19': 'Statement describing extent of participant’s right to access his/her records (or lack thereof vis à vis pending request for approval of non or partial disclosure)',
            '4.20': 'Description of policy regarding the use of genetic tests and familial genetic information, and the precautions in place to prevent disclosure of results to immediate family relative or to others without consent of the participant',
            '4.21': 'Possible direct or secondary use of participant’s medical records and biological specimens taken in the course of clinical care or in the course of this study',
            '4.22': 'Plans to destroy collected biological specimen at the end of the study; if not, details about storage (duration, type of storage facility, location, access information) and possible future use; affirming participant’s right to refuse future use, refuse storage, or have the materials destroyed',
            '4.23': 'Plans to develop commercial products from biological specimens and whether the participant will receive monetary or other benefit from such development',
            '4.24': 'Statement that the BERC has approved the study and may be reached for information regarding participant rights, grievances, and complaints'
        },

        sectionTitles: {
            1: 'Scientific Design', 2: 'Conduct of Study', 3: 'Ethical Consideration', 4: 'Informed Consent'
        },

        get filteredData() {
            let list = this.activeTab === 'validate' ? this.revisions : this.validatedRevisions;
            return list.filter(p => {
                const search = this.searchQuery.toLowerCase();
                return p.id.toLowerCase().includes(search) || p.title.toLowerCase().includes(search);
            }).sort((a, b) => {
                const dateA = new Date(this.activeTab === 'validate' ? a.dateSubmitted : a.dateValidated).getTime();
                const dateB = new Date(this.activeTab === 'validate' ? b.dateSubmitted : b.dateValidated).getTime();
                return this.sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
            });
        },

        get groupedRows() {
            let groups = {};
            let generalGroup = [];

            this.protocolRows.forEach(row => {
                let sectionId = row.item ? parseInt(row.item.split('.')[0]) : null;

                if (sectionId && this.sectionTitles[sectionId]) {
                    if (!groups[sectionId]) {
                        groups[sectionId] = {
                            sectionName: this.sectionTitles[sectionId],
                            rows: []
                        };
                    }
                    groups[sectionId].rows.push(row);
                } else {
                    generalGroup.push(row);
                }
            });

            let result = [];
            [1, 2, 3, 4].forEach(id => {
                if (groups[id]) result.push(groups[id]);
            });

            if (generalGroup.length > 0) {
                result.push({ sectionName: 'General Revisions', rows: generalGroup });
            }

            return result;
        },

        async openValidate(protocol) {
            this.selectedProtocol = protocol;
            this.activeView = 'resubmission_form';
            this.activeDocKey = null;
            this.activeDocUrl = null;
            this.draftStatusMsg = '';
            document.body.style.overflow = 'hidden';

            this.protocolRows = protocol.rows.map(row => ({
                ...row,
                remarks: row.remarks || '',
                action: row.action || ''
            }));

            // ── TUTORIAL MOCK BYPASS ──
            if (protocol.is_mock) {
                this.loadedDocs = {
                    activeBasic: [{ id: 'doc-mock-1', label: 'Study Protocol', desc: 'Revised Version', url: '', isRevised: true }],
                    activeSupp: [],
                    legacy: []
                };
                this.isLoadingDocs = false;
                return; // End early to prevent fetch errors
            }

            const revNum = protocol.version.replace('V', '');

            if (this.activeTab === 'validate') {
                try {
                    const draftRes = await fetch(`/reviewer/assessment/${protocol.id}/v${revNum}/draft`);
                    if (draftRes.ok) {
                        const draftData = await draftRes.json();

                        if (draftData && draftData.rows) {
                            this.protocolRows.forEach(r => {
                                const dRow = draftData.rows.find(dr => dr.id === r.id);
                                if (dRow) {
                                    r.remarks = dRow.remarks;
                                    r.action = dRow.action;
                                }
                            });
                            this.draftStatusMsg = 'Restored from saved draft.';
                            setTimeout(() => this.draftStatusMsg = '', 4000);
                        }
                    }
                } catch (e) {
                    console.warn('No draft found or error loading draft', e);
                }
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

        viewDocument(id, url, label) {
            this.activeDocKey = id;
            this.activeDocUrl = url;
            this.activeDocTitle = label;
            this.activeView = 'doc_viewer';
        },

        closeModal() {
            this.selectedProtocol = null;
            this.activeDocKey = null;
            this.activeDocUrl = null;
            document.body.style.overflow = '';
        },

        triggerAutosave() {
            if (this.activeTab === 'validated') return;
            this.draftStatusMsg = 'Saving...';
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.saveDraft(), 1500);
        },

        async saveDraft() {
            if (!this.selectedProtocol) return;
            const protocolId = this.selectedProtocol.id;
            const revNum = this.selectedProtocol.version.replace('V', '');

            const payload = {
                protocol_code: protocolId,
                revision_number: revNum,
                rows: this.protocolRows.map(r => ({
                    id: r.id,
                    remarks: r.remarks,
                    action: r.action
                }))
            };

            try {
                const response = await fetch(`/reviewer/assessment/${protocolId}/v${revNum}/draft`, {
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

            (this.protocolRows || []).forEach(row => {
                const missingParts = [];

                if (!(row.remarks || '').trim()) {
                    missingParts.push('Reviewer Remarks');
                }

                if (!row.action) {
                    missingParts.push('Status Action');
                }

                if (missingParts.length > 0) {
                    missing.push({
                        id: row.id,
                        item: row.item || '',
                        itemDisplay: row.item
                            ? `Item ${row.item}${this.questionLabels[row.item] ? ' - ' + this.questionLabels[row.item] : ''}`
                            : 'General Revision',
                        sectionAndPage: row.section_and_page || '',
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

        async confirmAndSubmitValidation() {
            this.confirmSubmitOpen = false;
            await this.finalSubmitValidation();
        },

        submitValidation() {
            this.openSubmitConfirmation();
        },

        async finalSubmitValidation() {
            const incompleteRows = this.protocolRows.filter(r => !r.action);
            if (incompleteRows.length > 0) {
                if(!confirm(`You left ${incompleteRows.length} item(s) without an action status. Continue anyway?`)) {
                    return;
                }
            }

            try {
                const response = await fetch('/reviewer/validate-revisions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        protocol_code: this.selectedProtocol.id,
                        revision_number: this.selectedProtocol.version.replace('V', ''),
                        rows: this.protocolRows.map(r => ({
                            id: r.id,
                            remarks: r.remarks,
                            action: r.action
                        }))
                    })
                });

                if (response.ok) {
                    const sourceIdx = this.revisions.findIndex(p => p.id === this.selectedProtocol.id);
                    if (sourceIdx !== -1) {
                        const item = this.revisions.splice(sourceIdx, 1)[0];
                        item.dateValidated = new Date().toISOString().split('T')[0];
                        this.validatedRevisions.unshift(item);
                    }

                    this.showNotification('Validation Saved', 'Successfully updated revision status.');
                    this.closeModal();
                } else {
                    alert('Failed to save validation.');
                }
            } catch (error) {
                alert('An error occurred during submission.');
            }
        },

        showNotification(title, message) {
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.notificationOpen = true;
            setTimeout(() => { this.notificationOpen = false; }, 4000);
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

        if (tourState === 'rev_resubmissions') {
            const driver = window.driver.js.driver;
            const alpineComponent = Alpine.$data(document.querySelector('[x-data="revisionData()"]'));

            const tour = driver({
                showProgress: true,
                allowClose: false,
                overlayColor: 'rgba(33, 60, 113, 0.75)',
                nextBtnText: 'Next &rarr;',
                prevBtnText: '&larr; Back',

                onDestroyStarted: () => {
                    if (!tour.hasNextStep()) {
                        alpineComponent.closeModal();

                        // Final step: Move them to the Settings page to force password change!
                        localStorage.setItem(storageKey, 'rev_settings');
                        tour.destroy();
                        window.location.href = "{{ route('settings') }}";
                    } else {
                        tour.destroy();
                    }
                },

                steps: [
                    {
                        element: '#tour-resub-tabs',
                        popover: {
                            title: 'Validating Revisions',
                            description: 'When an applicant resubmits a protocol that you previously returned for modifications, it will appear in the "To Validate" tab.',
                            side: "bottom",
                            align: 'start',
                            onNextClick: () => {
                                // ── MOCK MODAL OPEN ──
                                alpineComponent.openValidate({
                                    is_mock: true,
                                    id: '2026-MOCK-003',
                                    version: 'V2',
                                    title: 'AI System Architecture',
                                    proponent: 'Dr. Jane Doe',
                                    rows: [
                                        {
                                            id: 999,
                                            item: '1.4',
                                            berc_recommendation: 'Please clarify your sampling method.',
                                            researcher_response: 'Updated the methodology in section 2.',
                                            section_and_page: 'Page 4',
                                            action: '',
                                            remarks: ''
                                        }
                                    ]
                                });

                                setTimeout(() => { tour.moveNext(); }, 300);
                            }
                        }
                    },
                    {
                        element: '#tour-resub-sidebar',
                        popover: {
                            title: '1. Review the New Files',
                            description: 'Just like the main assessment, you can use this sidebar to open and read the researcher\'s newly uploaded documents.',
                            side: "right",
                            align: 'start'
                        }
                    },
                    {
                        element: '#tour-resub-form',
                        popover: {
                            title: '2. Check Their Responses',
                            description: 'The validation table shows exactly what you asked them to change, and what their response was. You do not need to re-evaluate the entire protocol—only the flagged items.',
                            side: "left",
                            align: 'center'
                        }
                    },
                    {
                        element: '#tour-resub-footer',
                        popover: {
                            title: '3. Final Verdict',
                            description: 'For each row, use the dropdown to mark it as "Resolved" or flag it as still needing "Action Required", then submit your validation.',
                            side: "top",
                            align: 'center',
                            onNextClick: () => {
                                alpineComponent.closeModal();
                                setTimeout(() => { tour.moveNext(); }, 300);
                            }
                        }
                    },
                    {
                        // Floating popover for the grand finale
                        popover: {
                            title: 'Final Step: Account Security 🔒',
                            description: 'You have completed the system tour! Because you are using a default, auto-generated password, your final requirement is to update it. Click below to proceed to your Account Settings.',
                            side: "bottom",
                            align: 'center',
                            doneBtnText: 'Update Password →'
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
