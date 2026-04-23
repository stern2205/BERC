@extends('secretariat.layouts.app')

@section('content')
<div x-data="reportsData()" class="flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-500">
    <div class="flex flex-col gap-6">
        <div>
            <h2 class="text-2xl font-black text-[#1f377d] uppercase tracking-tight">History</h2>
            <p class="text-gray-500 text-sm font-medium">Completed protocols with final decisions</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-6 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-[#1f377d] group-hover:bg-[#1f377d] group-hover:text-white transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Total Completed</h4>
                    <span class="text-3xl font-black text-[#1f377d] mt-1 block tracking-tight" x-text="protocols.length"></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-6 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Approved Protocols</h4>
                    <span class="text-3xl font-black text-green-600 mt-1 block tracking-tight" x-text="protocols.filter(p => p.decision === 'Approved').length"></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-6 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-[#c21c2c] group-hover:bg-[#c21c2c] group-hover:text-white transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Disapproved Protocols</h4>
                    <span class="text-3xl font-black text-[#c21c2c] mt-1 block tracking-tight" x-text="protocols.filter(p => p.decision === 'Disapproved').length"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex items-center gap-3">
                <div class="flex-[0.4] relative">
                    <input
                        type="text"
                        placeholder="Search by Protocol ID, Title, or Proponent..."
                        x-model="searchQuery"
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#1f377d]/10 transition-all"
                    />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex-[0.2] relative">
                    <select x-model="filterDecision" class="w-full appearance-none pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-[#1f377d] focus:outline-none focus:ring-2 focus:ring-[#1f377d]/10 cursor-pointer">
                        <option value="ALL">All Decisions</option>
                        <option value="Approved">Approved</option>
                        <option value="Disapproved">Disapproved</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>

                <div class="flex-[0.2] relative">
                    <select x-model="filterClassification" class="w-full appearance-none pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-[#1f377d] focus:outline-none focus:ring-2 focus:ring-[#1f377d]/10 cursor-pointer">
                        <option value="ALL">All Classifications</option>
                        <option value="Exempted">Exempted</option>
                        <option value="Expedited">Expedited</option>
                        <option value="Full Board">Full Board</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <div class="flex-[0.2] relative">
                    <select x-model="sortOrder" class="w-full appearance-none pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-[#1f377d] focus:outline-none focus:ring-2 focus:ring-[#1f377d]/10 cursor-pointer">
                        <option value="newest">Newest -> Oldest</option>
                        <option value="oldest">Oldest -> Newest</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Protocol ID</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d] w-1/4">Title</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Proponent</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Classification</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Reviewers</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Date Completed</th>
                            <th class="px-6 py-4 text-[9px] font-black uppercase tracking-[0.2em] text-[#1f377d]">Decision</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="protocol in filteredProtocols" :key="protocol.id">
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-[#1f377d] font-black text-[11px] uppercase group-hover:text-[#c21c2c] transition-colors" x-text="protocol.id"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-bold text-gray-700 leading-relaxed" x-text="protocol.title"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-bold text-gray-900" x-text="protocol.proponent"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest text-white inline-block"
                                          :class="protocol.classification === 'Full Board' ? 'bg-[#c21c2c]' : 'bg-[#1f377d]'"
                                          x-text="protocol.classification">
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1">
                                        <template x-for="(reviewer, index) in protocol.reviewers" :key="index">
                                            <span class="text-[10px] font-bold text-gray-600" x-text="reviewer"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-[#1f377d] uppercase" x-text="protocol.dateCompleted"></span>
                                        <span class="text-[9px] font-bold text-gray-400" x-text="formatRelativeTime(protocol.dateCompleted)"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <template x-if="protocol.decision === 'Approved'">
                                            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 rounded-lg">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">Approved</span>
                                            </div>
                                        </template>
                                        <template x-if="protocol.decision === 'Disapproved'">
                                            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 rounded-lg">
                                                <svg class="w-3 h-3 text-[#c21c2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="text-[10px] font-black text-[#c21c2c] uppercase tracking-widest">Disapproved</span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="filteredProtocols.length === 0" style="display: none;" class="p-16 text-center">
                <svg class="mx-auto text-gray-300 mb-4 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">No completed protocols found</p>
                <p class="text-xs text-gray-400 mt-1">Protocols appear here after receiving a final decision</p>
            </div>
        </div>
    </div>
</div>

<script>
const MOCK_COMPLETED_PROTOCOLS = [
  { id: '2026-0088', title: 'Legal Compliance in Healthcare Systems', proponent: 'Atty. Mark Reyes', classification: 'Full Board', reviewers: ['Dr. Sarah Chen', 'Prof. Michael Torres', 'Dr. Rachel Gomez'], decision: 'Approved', dateCompleted: '2026-02-16' },
  { id: '2026-0089', title: 'Social Media Impact on Youth Mental Health', proponent: 'Dr. Jennifer Lopez', classification: 'Expedited', reviewers: ['Dr. Sarah Chen', 'Prof. Michael Torres'], decision: 'Disapproved', dateCompleted: '2026-02-15' },
  { id: '2026-0087', title: 'Student Survey on Campus Facilities', proponent: 'Ms. Patricia Reyes', classification: 'Exempted', reviewers: ['Dr. Sarah Chen'], decision: 'Approved', dateCompleted: '2026-02-13' },
  { id: '2026-0085', title: 'Renewable Energy Integration Study', proponent: 'Engr. Carlos Rivera', classification: 'Full Board', reviewers: ['Dr. Sarah Chen', 'Prof. Michael Torres', 'Dr. Rachel Gomez'], decision: 'Approved', dateCompleted: '2026-02-11' },
  { id: '2026-0083', title: 'Educational Technology Assessment', proponent: 'Prof. Maria Garcia', classification: 'Expedited', reviewers: ['Dr. Sarah Chen', 'Prof. Michael Torres'], decision: 'Approved', dateCompleted: '2026-02-09' },
  { id: '2026-0082', title: 'Anonymous Feedback Collection Study', proponent: 'Dr. Linda Santos', classification: 'Exempted', reviewers: ['Dr. Sarah Chen'], decision: 'Approved', dateCompleted: '2026-02-07' },
  { id: '2026-0080', title: 'Urban Planning and Sustainability', proponent: 'Arch. David Santos', classification: 'Full Board', reviewers: ['Dr. Sarah Chen', 'Prof. Michael Torres', 'Dr. Rachel Gomez'], decision: 'Disapproved', dateCompleted: '2026-02-06' },
];

document.addEventListener('alpine:init', () => {
    Alpine.data('reportsData', () => ({
        protocols: MOCK_COMPLETED_PROTOCOLS,
        searchQuery: '',
        filterDecision: 'ALL',
        filterClassification: 'ALL',
        sortOrder: 'newest',

        get filteredProtocols() {
            return this.protocols.filter(p => {
                const searchLower = this.searchQuery.toLowerCase();
                const matchesSearch = p.id.toLowerCase().includes(searchLower) ||
                                      p.title.toLowerCase().includes(searchLower) ||
                                      p.proponent.toLowerCase().includes(searchLower);

                const matchesDecision = this.filterDecision === 'ALL' || p.decision === this.filterDecision;
                const matchesClassification = this.filterClassification === 'ALL' || p.classification === this.filterClassification;

                return matchesSearch && matchesDecision && matchesClassification;
            }).sort((a, b) => {
                const aTime = new Date(a.dateCompleted || 0).getTime();
                const bTime = new Date(b.dateCompleted || 0).getTime();
                return this.sortOrder === 'newest' ? bTime - aTime : aTime - bTime;
            });
        },

        formatRelativeTime(dateStr) {
            const today = new Date(2026, 1, 24); // Hardcoded today as Feb 24, 2026 based on mock data
            const targetDate = new Date(dateStr);
            const diffTime = Math.abs(today - targetDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 0) return 'Today';
            if (diffDays === 1) return 'Yesterday';
            return `${diffDays} days ago`;
        }
    }));
});
</script>
@endsection
