@extends('chair.layouts.app')

@section('content')
<style>
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; width:100%; max-width:760px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:#fafafa; }
    .modal-header h2 { font-size:14px; font-weight:800; color:#213C71; text-transform:uppercase; letter-spacing:.04em; }
    .close-btn { font-size:20px; line-height:1; color:#6b7280; background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; }
    .close-btn:hover { background:#f3f4f6; color:#111; }
    .modal-body { padding:16px 20px; overflow-y:auto; }
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:14px 20px; border-top:1px solid #e5e7eb; background:#fafafa; }
    .field-label { display:block; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:#6b7280; margin-bottom:6px; }
    .field-input { width:100%; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#111827; font-size:12px; padding:9px 10px; }
    .field-input:focus { outline:none; border-color:#213C71; box-shadow:0 0 0 2px rgba(33,60,113,.1); }
    .action-btn { border:1px solid transparent; border-radius:8px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; padding:7px 12px; line-height:1.3; cursor:pointer; transition:all .15s; }
    .action-btn-primary { background:#213C71; border-color:#213C71; color:#fff; }
    .action-btn-primary:hover { opacity:.9; }
    .action-btn-outline { background:#fff; border-color:#cbd5e1; color:#1f2937; }
    .action-btn-outline:hover { border-color:#213C71; color:#213C71; }
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-6px); }
        50% { transform: translateX(6px); }
        75% { transform: translateX(-4px); }
        100% { transform: translateX(0); }
    }
    .animate-shake {
        animation: shake 0.3s ease;
    }

</style>
<div x-data="staffLogsData()" x-effect="toggleBodyScrollLock(anyModalOpen)" class="max-w-7xl mx-auto pb-6 animate-in fade-in duration-500">

    <div id="tour-staff-header" class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-bsu-dark uppercase tracking-tight">Staff</h1>
            <p class="text-gray-500 text-xs font-medium mt-1">Employee directory and time in/out records</p>
        </div>
        <button type="button"
           id="tour-create-btn"
           @click="openCreateStaffModal()"
           class="inline-flex items-center px-4 py-2.5 rounded-lg bg-bsu-dark text-white text-[11px] font-black uppercase tracking-wider hover:opacity-90 transition-opacity">
            Create Staff Account
        </button>
    </div>

    <section id="tour-staff-directory" class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-[13px] font-black text-bsu-dark uppercase tracking-wider">Employee Directory</h2>
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500" x-text="filteredDirectoryMembers.length + ' total'"></span>
        </div>

        <div class="px-5 py-3 border-b border-gray-100 bg-white grid grid-cols-1 lg:grid-cols-[1fr_auto_auto] gap-3">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Search Employee</label>
                <input type="text"
                       x-model.trim="employeeSearch"
                       placeholder="Search by name or email"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Role</label>
                <select x-model="directoryRoleFilter"
                        class="w-full lg:w-52 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark">
                    <template x-for="role in filterRolesList" :key="'directory-role-' + role">
                        <option :value="role" x-text="role === 'ALL' ? 'All Roles' : role"></option>
                    </template>
                </select>
            </div>
            <div class="lg:self-end">
                <button type="button"
                        x-show="employeeSearch || directoryRoleFilter !== 'ALL'"
                        @click="clearDirectoryFilters()"
                        class="w-full lg:w-auto px-3 py-2 rounded-lg border border-gray-300 bg-white text-[10px] font-black uppercase tracking-wider text-gray-600 hover:bg-gray-50 transition-colors">
                    Clear
                </button>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            <template x-for="group in groupedDirectory" :key="'group-' + group.role">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700" x-text="group.role"></h3>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="group.members.length + (group.members.length === 1 ? ' employee' : ' employees')"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2">
                        <template x-for="staff in group.members" :key="'directory-member-' + staff.id">
                            <button type="button"
                                    @click="openProfile(staff)"
                                    class="w-full h-full text-left px-3 py-2.5 rounded-lg border border-gray-200 bg-slate-50 hover:bg-slate-100 hover:border-gray-300 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <div class="relative shrink-0 w-10 h-10">
                                            <img x-show="staff.profile_image"
                                                :src="staff.profile_image ? '{{ asset('/') }}' + staff.profile_image.replace('public/', '') : ''"
                                                alt="Profile Image"
                                                class="w-full h-full rounded-full object-cover border border-gray-200 shadow-sm">
                                            <div x-show="!staff.profile_image"
                                                class="w-full h-full rounded-full flex items-center justify-center text-[11px] font-black uppercase tracking-wider shadow-sm"
                                                :class="getAvatarBgClass(staff.id)"
                                                x-text="getInitials(staff.name)"></div>

                                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white z-10"
                                                :class="getStatusDotClass(staff.status)"></span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800 truncate" x-text="staff.name"></p>
                                        <p class="text-[11px] font-semibold text-gray-500 truncate" x-text="staff.email"></p>
                                    </div>
                                    <div class="ml-auto text-right max-w-[40%]">
                                        <p class="text-[10px] font-black uppercase tracking-widest" :class="getStatusClass(staff.status)" x-text="getPresenceLabel(staff.status)"></p>
                                        <p class="text-[9px] font-semibold text-gray-500 mt-0.5 truncate" x-text="staff.expertise"></p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="groupedDirectory.length === 0" class="px-5 py-10 text-center text-sm font-bold text-gray-400">
                No employees found for the selected filters.
            </div>
        </div>
    </section>

    <section id="tour-staff-logs" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-[13px] font-black text-bsu-dark uppercase tracking-wider">Time In/Out</h2>
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500" x-text="filteredStaffMembers.length + ' records'"></span>
        </div>

        <div class="px-5 py-3 border-b border-gray-100 bg-white grid grid-cols-1 lg:grid-cols-[1fr_auto_auto_auto] gap-3">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Search</label>
                <input type="text"
                    x-model.trim="logSearch"
                    @input="currentPage = 1"
                    placeholder="Search employee"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Role</label>
                <select x-model="logRoleFilter"
                        @change="currentPage = 1"
                        class="w-full lg:w-48 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark">
                    <template x-for="role in filterRolesList" :key="'log-role-' + role">
                        <option :value="role" x-text="role === 'ALL' ? 'All Roles' : role"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Day</label>
                <input type="date"
                    x-model="logDateFilter"
                    @change="currentPage = 1"
                    class="w-full lg:w-44 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-bsu-dark focus:border-bsu-dark">
            </div>
            <div class="lg:self-end">
                <button type="button"
                        x-show="logRoleFilter !== 'ALL' || logDateFilter || logSearch"
                        @click="clearLogFilters(); currentPage = 1"
                        class="w-full lg:w-auto px-3 py-2 rounded-lg border border-gray-300 bg-white text-[10px] font-black uppercase tracking-wider text-gray-600 hover:bg-gray-50 transition-colors">
                    Clear
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500">Employee</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500">Role</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500">Activity</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="staff in paginatedStaffMembers" :key="'log-entry-' + staff.id">
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-4 py-3 text-sm font-bold text-gray-800">
                                <button type="button"
                                        @click="openProfile(staff)"
                                        class="text-left hover:underline underline-offset-2">
                                    <span x-text="staff.name"></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-600" x-text="staff.role"></td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded text-[10px] font-black uppercase tracking-widest border"
                                    :class="String(staff.logType).toLowerCase().includes('out')
                                        ? 'bg-rose-50 text-rose-700 border-rose-200'
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                    <svg x-show="!String(staff.logType).toLowerCase().includes('out')" class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <svg x-show="String(staff.logType).toLowerCase().includes('out')" class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span x-text="staff.logType"></span>
                                </span>
                            </td>

                            <td class="px-4 py-3 text-xs font-semibold text-gray-600" x-text="staff.logTimestamp"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredStaffMembers.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-sm font-bold text-gray-400">No time logs found for selected filters.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-3" x-show="filteredStaffMembers.length > 0">
            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                Showing <span class="text-gray-800" x-text="(currentPage - 1) * itemsPerPage + 1"></span> -
                <span class="text-gray-800" x-text="Math.min(currentPage * itemsPerPage, filteredStaffMembers.length)"></span>
                of <span class="text-gray-800" x-text="filteredStaffMembers.length"></span>
            </span>

            <div class="flex items-center gap-2">
                <button type="button"
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 rounded border border-gray-200 bg-white text-[10px] font-black uppercase tracking-widest text-gray-600 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors">
                    Prev
                </button>
                <span class="text-[10px] font-bold text-gray-500 mx-1">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button type="button"
                        @click="currentPage++"
                        :disabled="currentPage >= totalPages"
                        class="px-3 py-1.5 rounded border border-gray-200 bg-white text-[10px] font-black uppercase tracking-widest text-gray-600 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors">
                    Next
                </button>
            </div>
        </div>
    </section>

    <div class="modal-overlay"
         :class="{ 'open': showCreateStaffModal }"
         x-cloak
         @keydown.escape.window="closeCreateStaffModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Create Staff Account</h2>
                <button class="close-btn" @click="closeCreateStaffModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
                    <div class="relative z-10 p-4 sm:p-6 flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6">
                        <div class="shrink-0">
                            <div class="bg-gray-50 p-1 rounded-2xl border border-gray-200 shadow-sm">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 object-cover bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 10a6 6 0 0112 0"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 text-gray-900 text-center sm:text-left">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest"
                               x-text="createStaffForm.role === 'Reviewer' ? (createStaffForm.reviewerType || 'Reviewer Not Set') : (createStaffForm.role || 'Role Not Set')"></p>
                            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight" x-text="createStaffForm.name || 'New Account'"></h2>
                            <div class="flex flex-col sm:flex-row sm:space-x-12 gap-2 sm:gap-0 mt-2 sm:mt-3 items-center sm:items-start">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Account ID</p>
                                    <p class="text-xs font-bold tracking-wide text-gray-800">Auto-generated</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Email Address</p>
                                    <p class="text-xs font-bold tracking-wide text-gray-800" x-text="createStaffForm.email || '---'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Name</label>
                        <input type="text" class="field-input" x-model.trim="createStaffForm.name" placeholder="Enter full name">
                    </div>
                    <div>
                        <label class="field-label">Email</label>
                        <input type="email"
                            x-model.trim="createStaffForm.email"
                            class="field-input transition-all"
                            :class="createStaffFormError && !isValidEmail(createStaffForm.email)
                                    ? 'border-red-500 bg-red-50 ring-2 ring-red-300 animate-pulse'
                                    : ''"
                            placeholder="Enter email address">
                    </div>
                    <div>
                        <label class="field-label">Define Role</label>
                        <select class="field-input" x-model="createStaffForm.role" @change="onCreateRoleChange()">
                            <option value="">Select role</option>
                            <template x-for="role in formRoleOptions" :key="'create-role-' + role">
                                <option :value="role" x-text="role"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="mt-4" x-show="createStaffForm.role === 'Reviewer'" x-cloak>
                    <label class="field-label text-bsu-dark">Reviewer Type</label>
                    <select class="field-input" x-model="createStaffForm.reviewerType" @change="onCreateReviewerTypeChange()">
                        <option value="">Select reviewer type</option>
                        <option value="Panel Expert">Panel Expert</option>
                        <option value="Layperson">Layperson</option>
                        <option value="External Consultant">External Consultant</option>
                    </select>
                </div>

                <div class="mt-4" x-show="createStaffForm.role === 'Reviewer' && ['Panel Expert', 'Layperson'].includes(createStaffForm.reviewerType)" x-cloak>
                    <label class="field-label text-bsu-dark">Assign Panel</label>
                    <select class="field-input" x-model="createStaffForm.panel">
                        <option value="">Select a panel</option>
                        <option value="Panel I - Engineering, architecture, information, and computing sciences">Panel I - Engineering, architecture, information, and computing sciences</option>
                        <option value="Panel II - Law, education, and social sciences">Panel II - Law, education, and social sciences</option>
                        <option value="Panel III - Medicine and health sciences">Panel III - Medicine and health sciences</option>
                        <option value="Panel IV - Agriculture and natural sciences">Panel IV - Agriculture and natural sciences</option>
                        <option value="Panel V - Physical and biological sciences and mathematics">Panel V - Physical and biological sciences and mathematics</option>
                    </select>
                </div>

                <div class="mt-4" x-show="createStaffForm.role === 'Reviewer'" x-cloak>
                    <label class="field-label text-bsu-dark">Specialization / Expertise</label>
                    <input type="text" class="field-input" x-model.trim="createStaffForm.specialization" placeholder="e.g., General Research, Testing, Pharmacology">
                </div>
            </div>

            <div class="modal-footer items-center">
                <div x-show="createStaffFormError"
                    x-cloak
                    class="flex items-start gap-2 rounded-lg border border-red-300 bg-red-100 px-3 py-3 text-[11px] font-black text-red-800 shadow-sm animate-pulse">

                    <svg class="w-4 h-4 mt-[1px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.67 19h16.66a1 1 0 00.88-1.14l-7.5-13a1 1 0 00-1.76 0z"/>
                    </svg>

                    <span x-text="createStaffFormError"></span>
                </div>
                <div class="ml-auto flex gap-2">
                    <button type="button" class="action-btn action-btn-outline" @click="closeCreateStaffModal()">Cancel</button>
                    <button type="button" class="action-btn action-btn-primary" @click="submitCreateStaffForm()">Create Account</button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showProfileModal"
         x-cloak
         x-transition.opacity.duration.150ms
         class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4"
         @keydown.escape.window="closeProfileModal()">
        <div class="w-full max-w-2xl bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden"
             @click.outside="closeProfileModal()">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Employee Profile</h3>
                    <p class="text-[11px] font-semibold text-gray-500 mt-0.5" x-text="selectedStaff ? selectedStaff.name : ''"></p>
                </div>
                <button type="button"
                        @click="closeProfileModal()"
                        class="px-2.5 py-1.5 rounded-md border border-gray-300 text-[10px] font-black uppercase tracking-wider text-gray-600 hover:bg-gray-100 transition-colors">
                    Close
                </button>
            </div>

            <div class="p-5" x-show="selectedStaff">
                <div class="mb-5 p-4 rounded-xl border border-gray-200 bg-slate-50 flex items-center gap-4">

                    <div class="relative shrink-0 w-16 h-16">
                        <img x-show="selectedStaff && selectedStaff.profile_image"
                            :src="selectedStaff && selectedStaff.profile_image ? '{{ asset('/') }}' + selectedStaff.profile_image.replace(/^public\//, '').replace(/^\//, '') : ''"
                            alt="Profile Image"
                            class="w-full h-full rounded-full object-cover border border-gray-200 shadow-sm">

                        <div x-show="!selectedStaff || !selectedStaff.profile_image"
                             class="w-full h-full rounded-full flex items-center justify-center text-base font-black uppercase tracking-wider shadow-sm"
                             :class="getAvatarBgClass(selectedStaff ? selectedStaff.id : 0)"
                             x-text="getInitials(selectedStaff ? selectedStaff.name : '')"></div>

                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white z-10"
                              :class="getStatusDotClass(selectedStaff ? selectedStaff.status : '')"></span>
                    </div>

                    <div class="min-w-0">
                        <div class="text-base font-black text-bsu-dark uppercase tracking-tight truncate" x-text="selectedStaff ? selectedStaff.name : ''"></div>
                        <div class="text-xs font-semibold text-gray-500 mt-0.5" x-text="selectedStaff ? selectedStaff.role : ''"></div>
                        <div class="text-[10px] font-black uppercase tracking-widest mt-2"
                             :class="getStatusClass(selectedStaff ? selectedStaff.status : '')"
                             x-text="getPresenceLabel(selectedStaff ? selectedStaff.status : '')"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Employee ID</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.employeeId : 'N/A'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Role</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.role : 'N/A'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Email</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.email : 'N/A'"></div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Panel/Expertise</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.expertise : 'N/A'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Account Status</div>
                        <div class="mt-1 text-sm font-bold" :class="getStatusClass(selectedStaff ? selectedStaff.status : '')" x-text="selectedStaff ? selectedStaff.status : 'N/A'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Recent Activity</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.logType : 'N/A'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500">Last Log</div>
                        <div class="mt-1 text-sm font-bold text-gray-800" x-text="selectedStaff ? selectedStaff.logTimestamp : 'N/A'"></div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                    <span class="text-[11px] font-semibold text-gray-500">Deleting removes this account permanently.</span>
                    <button type="button"
                            @click="showDeleteConfirm = true"
                            class="px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-[11px] font-black uppercase tracking-wider text-red-700 hover:bg-red-100 transition-colors">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showDeleteConfirm"
         x-cloak
         x-transition.opacity.duration.150ms
         class="fixed inset-0 z-[60] bg-black/50 flex items-center justify-center p-4"
         @keydown.escape.window="showDeleteConfirm = false">
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-xl shadow-2xl p-5"
             @click.outside="showDeleteConfirm = false">
            <h3 class="text-sm font-black text-bsu-dark uppercase tracking-wider">Delete Staff Account</h3>
            <p class="mt-2 text-sm font-semibold text-gray-600">
                Delete
                <span class="text-gray-800" x-text="selectedStaff ? selectedStaff.name : 'this account'"></span>
                from the system? This action cannot be undone.
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button"
                        @click="showDeleteConfirm = false"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-[11px] font-black uppercase tracking-wider text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button"
                        @click="deleteSelectedAccount()"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white text-[11px] font-black uppercase tracking-wider hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div x-show="notification.open"
         x-cloak
         x-transition.opacity.duration.150ms
         class="fixed top-20 right-6 z-[70] bg-white border rounded-lg shadow-lg p-4 w-80"
         :class="notification.type === 'danger' ? 'border-red-200' : 'border-green-200'">
        <div class="text-[11px] font-black uppercase tracking-wider"
             :class="notification.type === 'danger' ? 'text-red-700' : 'text-green-700'"
             x-text="notification.title"></div>
        <div class="text-xs font-semibold text-gray-700 mt-1" x-text="notification.message"></div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('staffLogsData', () => ({
        // Filters
        employeeSearch: '',
        directoryRoleFilter: 'ALL',
        logSearch: '',
        logRoleFilter: 'ALL',
        logDateFilter: '',

        // Modal States
        selectedStaff: null,
        showCreateStaffModal: false,
        showProfileModal: false,
        showDeleteConfirm: false,

        // Form State
        createStaffForm: {
            name: '',
            email: '',
            phone: '',
            role: '',
            reviewerType: '', // Specific to reviewers
            panel: '',        // Specific to Panel Expert / Layperson
            specialization: '',
        },
        createStaffFormError: '',
        notificationTimer: null,
        notification: { open: false, title: '', message: '', type: 'success' },

        get anyModalOpen() {
            return this.showCreateStaffModal || this.showProfileModal || this.showDeleteConfirm;
        },

        // Role selections for the CREATE form dropdown
        formRoleOptions: [
            'Chair',
            'Co-Chair',
            'Secretariat',
            'Secretarial Staff',
            'Reviewer' // Trigger for reviewer sub-fields
        ],

        // Role selections for the FILTER dropdowns (expands Reviewer into specific types)
        filterRolesList: [
            'ALL',
            'Chair',
            'Co-Chair',
            'Secretariat',
            'Secretarial Staff',
            'Panel Expert',
            'Layperson',
            'External Consultant'
        ],

        // INJECT DATA FROM BACKEND HERE
        staffMembers: @json($staffData ?? []),

        // --- DIRECTORY LOGIC ---
        get filteredDirectoryMembers() {
            const search = this.employeeSearch.toLowerCase();
            return this.staffMembers
                .filter((item) => this.directoryRoleFilter === 'ALL' ? true : item.role === this.directoryRoleFilter)
                .filter((item) => {
                    if (!search) return true;
                    return String(item.name).toLowerCase().includes(search)
                        || String(item.email).toLowerCase().includes(search);
                })
                .sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
        },
        get groupedDirectory() {
            const groups = this.filteredDirectoryMembers.reduce((result, member) => {
                if (!result[member.role]) result[member.role] = [];
                result[member.role].push(member);
                return result;
            }, {});

            return Object.keys(groups)
                .sort((a, b) => this.getRoleOrderIndex(a) - this.getRoleOrderIndex(b))
                .map((role) => ({ role, members: groups[role] }));
        },

        // --- TIME IN/OUT LOGIC ---
        get filteredStaffMembers() {
            const search = this.logSearch.toLowerCase();
            return this.staffMembers
                .filter((item) => this.logRoleFilter === 'ALL' ? true : item.role === this.logRoleFilter)
                .filter((item) => this.logDateFilter ? item.logDateKey === this.logDateFilter : true)
                .filter((item) => {
                    if (!search) return true;
                    return String(item.name).toLowerCase().includes(search);
                })
                .sort((a, b) => this.getLogEpoch(b) - this.getLogEpoch(a)); // Sort latest first
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        // --- UI HELPERS ---
        getStatusClass(status) {
            const normalized = String(status || '').toLowerCase();
            return normalized === 'active' || normalized === '1' || normalized === 'true' ? 'text-green-700' : 'text-gray-500';
        },
        getStatusDotClass(status) {
            return this.isOnline(status) ? 'bg-green-500' : 'bg-gray-400';
        },
        getPresenceLabel(status) {
            return this.isOnline(status) ? 'Online' : 'Offline';
        },
        isOnline(status) {
            const normalized = String(status || '').toLowerCase();
            return normalized === 'active' || normalized === '1' || normalized === 'true';
        },
        getInitials(name) {
            const cleaned = String(name || '').trim();
            if (!cleaned) return '--';
            const parts = cleaned.split(/\s+/).filter(Boolean);
            if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
            return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        },
        getAvatarBgClass(id) {
            const tones = [
                'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700',
                'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700',
                'bg-indigo-100 text-indigo-700', 'bg-cyan-100 text-cyan-700',
            ];
            const idx = Math.abs(Number(id) || 0) % tones.length;
            return tones[idx];
        },
        toggleBodyScrollLock(shouldLock) {
            document.body.style.overflow = shouldLock ? 'hidden' : '';
        },

        // --- MODAL & FORM CONTROLS ---
        openCreateStaffModal() {
            this.resetCreateStaffForm();
            this.showCreateStaffModal = true;
        },
        closeCreateStaffModal() {
            this.showCreateStaffModal = false;
            this.createStaffFormError = '';
        },
        resetCreateStaffForm() {
            this.createStaffForm.name = '';
            this.createStaffForm.email = '';
            this.createStaffForm.phone = '';
            this.createStaffForm.role = '';
            this.createStaffForm.reviewerType = '';
            this.createStaffForm.panel = '';
            this.createStaffForm.specialization = '';
            this.createStaffFormError = '';
        },
        onCreateRoleChange() {
            if (this.createStaffForm.role !== 'Reviewer') {
                this.createStaffForm.reviewerType = '';
                this.createStaffForm.panel = '';
                this.createStaffForm.specialization = '';
            }
        },
        onCreateReviewerTypeChange() {
            if (!['Panel Expert', 'Layperson'].includes(this.createStaffForm.reviewerType)) {
                this.createStaffForm.panel = '';
            }
        },
        buildCreateStaffExpertiseLabel() {
            if (this.createStaffForm.role !== 'Reviewer') return 'General';

            if (['Panel Expert', 'Layperson'].includes(this.createStaffForm.reviewerType)) {
                if (this.createStaffForm.panel && this.createStaffForm.specialization) {
                    return this.createStaffForm.panel + ' | ' + this.createStaffForm.specialization;
                }
            }
            return this.createStaffForm.specialization || 'General';
        },

        // --- DATE PARSING HELPERS ---
        formatDatePart(value) {
            return String(value).padStart(2, '0');
        },
        formatLogDateKey(date) {
            const year = date.getFullYear();
            const month = this.formatDatePart(date.getMonth() + 1);
            const day = this.formatDatePart(date.getDate());
            return year + '-' + month + '-' + day;
        },
        formatLogTimestamp(date) {
            let hours = date.getHours();
            const minutes = this.formatDatePart(date.getMinutes());
            const period = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;

            const day = this.formatDatePart(date.getDate());
            const month = this.formatDatePart(date.getMonth() + 1);
            const year = String(date.getFullYear()).slice(-2);

            return hours + ':' + minutes + ' ' + period + ' | ' + month + '/' + day + '/' + year;
        },
        getLogEpoch(log) {
            const dateKey = String(log && log.logDateKey ? log.logDateKey : '').trim();
            if (!dateKey) return 0;

            const [year, month, day] = dateKey.split('-').map(Number);
            if (!year || !month || !day) return 0;

            const timePart = String(log && log.logTimestamp ? log.logTimestamp : '').split('|')[0].trim();
            const timeMatch = timePart.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
            let hours = 0; let minutes = 0;
            if (timeMatch) {
                hours = Number(timeMatch[1]);
                minutes = Number(timeMatch[2]);
                const period = timeMatch[3].toUpperCase();
                if (period === 'PM' && hours !== 12) hours += 12;
                if (period === 'AM' && hours === 12) hours = 0;
            }

            return new Date(year, month - 1, day, hours, minutes, 0, 0).getTime();
        },

        // --- ACTIONS (API CALLS) ---
        async submitCreateStaffForm() {
            this.createStaffFormError = '';

            if (!this.createStaffForm.name || !this.createStaffForm.email || !this.createStaffForm.role) {
                this.createStaffFormError = 'Please complete all required fields.';
                this.triggerFormShake();
                return;
            }

            if (!this.isValidEmail(this.createStaffForm.email)) {
                this.createStaffFormError = 'Invalid email format. Please enter a valid email address.';
                this.triggerFormShake();
                return;
            }

            if (this.createStaffForm.role === 'Reviewer') {
                if (!this.createStaffForm.reviewerType) {
                    this.createStaffFormError = 'Please select a Reviewer Type.';
                    return;
                }
                if (['Panel Expert', 'Layperson'].includes(this.createStaffForm.reviewerType) && !this.createStaffForm.panel) {
                    this.createStaffFormError = 'Please select a Panel assignment.';
                    return;
                }
                if (!this.createStaffForm.specialization) {
                    this.createStaffFormError = 'Please provide a specialization/expertise.';
                    return;
                }
            }

            const payload = {
                name: this.createStaffForm.name,
                email: this.createStaffForm.email,
                phone: this.createStaffForm.phone,
                role: this.createStaffForm.role === 'Reviewer' ? this.createStaffForm.reviewerType : this.createStaffForm.role,
                expertise: this.buildCreateStaffExpertiseLabel()
            };

            try {
                const response = await fetch('/staff/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    this.createStaffFormError = result.message || 'Email already exists or invalid data.';
                    return;
                }

                const now = new Date();
                const newStaff = {
                    id: result.new_id,
                    employeeId: 'STF-' + String(result.new_id).padStart(3, '0'),
                    role: payload.role,
                    name: payload.name,
                    email: payload.email,
                    phone: payload.phone || 'N/A',
                    expertise: payload.expertise,
                    status: 'Active',
                    logType: 'No Activity',
                    logTimestamp: 'N/A',
                    logDateKey: this.formatLogDateKey(now),
                };

                this.staffMembers = [newStaff, ...this.staffMembers];
                this.showCreateStaffModal = false;
                this.showNotification('Account Created', 'Staff account created successfully.', 'success');
                this.resetCreateStaffForm();

            } catch (error) {
                this.createStaffFormError = 'Server error. Please check your connection.';
            }
        },

        async deleteSelectedAccount() {
            if (!this.selectedStaff) return;
            const deleteId = this.selectedStaff.id;

            try {
                const response = await fetch(`/staff/${deleteId}`, {
                   method: 'DELETE',
                   headers: {
                       'Accept': 'application/json',
                       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                   }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    this.showNotification('Error', result.message || 'Failed to delete account.', 'danger');
                    this.showDeleteConfirm = false;
                    return;
                }

                this.staffMembers = this.staffMembers.filter((member) => member.id !== deleteId);
                this.showDeleteConfirm = false;
                this.showProfileModal = false;
                this.selectedStaff = null;
                this.showNotification('Account Deleted', 'Staff account permanently removed.', 'danger');

            } catch (error) {
                this.showNotification('Error', 'Server connection failed.', 'danger');
                this.showDeleteConfirm = false;
            }
        },

        triggerFormShake() {
            const modal = document.querySelector('.modal-box');
            if (!modal) return;

            modal.classList.add('animate-shake');
            setTimeout(() => modal.classList.remove('animate-shake'), 400);
        },

        // --- NOTIFICATIONS & CLEAR FILTERS ---
        showNotification(title, message, type = 'success') {
            this.notification.title = title;
            this.notification.message = message;
            this.notification.type = type;
            this.notification.open = true;

            if (this.notificationTimer) clearTimeout(this.notificationTimer);
            this.notificationTimer = setTimeout(() => { this.notification.open = false; }, 2200);
        },
        openProfile(staff) {
            // ── TUTORIAL MOCK BYPASS ──
            if(staff.is_mock) {
                this.selectedStaff = staff;
                this.showProfileModal = true;
                return;
            }

            this.selectedStaff = { ...staff };
            this.showProfileModal = true;
            this.showDeleteConfirm = false;
        },
        closeProfileModal() {
            this.showProfileModal = false;
            this.showDeleteConfirm = false;
            this.selectedStaff = null;
        },
        clearDirectoryFilters() {
            this.employeeSearch = '';
            this.directoryRoleFilter = 'ALL';
        },
        clearLogFilters() {
            this.logSearch = '';
            this.logRoleFilter = 'ALL';
            this.logDateFilter = '';
        },
        getRoleOrderIndex(role) {
            const idx = this.filterRolesList.indexOf(role);
            return idx === -1 ? 999 : idx;
        },

        // Add these to your x-data object
        currentPage: 1,
        itemsPerPage: 10, // Change this number to adjust how many rows show per page

        // Add these to your Alpine JS computed/getters
        get totalPages() {
            return Math.ceil(this.filteredStaffMembers.length / this.itemsPerPage) || 1;
        },

        get paginatedStaffMembers() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredStaffMembers.slice(start, end);
        },
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
            .driver-popover { font-family:'Inter',sans-serif!important;border-radius:12px!important;border:1px solid #E5E7EB!important;padding:20px!important; }
            .driver-popover-title { color:#213C71!important;font-weight:900!important;text-transform:uppercase!important;letter-spacing:.05em!important;font-size:14px!important; }
            .driver-popover-description { color:#6B7280!important;font-weight:500!important;font-size:12px!important;line-height:1.5!important; }
            .driver-popover-footer button { border-radius:8px!important;font-weight:700!important;font-size:11px!important;padding:8px 12px!important; }
            .driver-popover-next-btn { background:#D32F2F!important;color:#fff!important;border:none!important; }
            .driver-popover-prev-btn { background:#F3F4F6!important;color:#4B5563!important;border:none!important; }
        `;
        document.head.appendChild(styleOverride);

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function runChairStaffTutorial(manual = false) {
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;

        if (manual) {
            localStorage.removeItem(storageKey);
            localStorage.setItem(storageKey, 'chair_staff');
        }

        const alpineRoot = document.querySelector('[x-data="staffLogsData()"]');
        if (!alpineRoot) return;

        const alpineComponent = Alpine.$data(alpineRoot);
        const driver = window.driver.js.driver;

        const tour = driver({
            showProgress: true,
            allowClose: manual ? true : false,
            overlayColor: 'rgba(33, 60, 113, 0.75)',
            nextBtnText: 'Next →',
            prevBtnText: '← Back',

            onDestroyStarted: () => {
                if (alpineComponent.closeProfileModal) {
                    alpineComponent.closeProfileModal();
                }

                if (!tour.hasNextStep()) {
                    localStorage.setItem(storageKey, 'chair_history');
                    tour.destroy();
                    window.location.href = "{{ route('chair.history') ?? '/chair/history' }}";
                } else {
                    tour.destroy();
                }
            },

            steps: [
                {
                    element: '#tour-staff-header',
                    popover: {
                        title: 'Staff Management',
                        description: 'Manage committee accounts, roles, and access from this page.',
                        side: "bottom",
                        align: "start"
                    }
                },
                {
                    element: '#tour-staff-directory',
                    popover: {
                        title: 'Employee Directory',
                        description: 'View all committee members and open profiles for management.',
                        side: "top",
                        align: "start",
                        onNextClick: () => {
                            alpineComponent.openProfile({
                                is_mock: true,
                                id: 999,
                                employeeId: 'STF-999',
                                name: 'Dr. Emmet Brown',
                                email: 'emmet.brown@g.batstate-u.edu.ph',
                                role: 'Panel Expert',
                                expertise: 'Panel I - Engineering | Software Architecture',
                                status: 'Active',
                                logType: 'Time In',
                                logTimestamp: '08:00 AM | 04/15/26'
                            });

                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    element: '#tour-staff-logs',
                    popover: {
                        title: 'Account Activity',
                        description: 'Login activity and engagement logs are tracked here.',
                        side: "top",
                        align: "start",
                        onNextClick: () => {
                            alpineComponent.closeProfileModal();
                            setTimeout(() => tour.moveNext(), 300);
                        }
                    }
                },
                {
                    popover: {
                        title: 'Next Stop: History & Archives',
                        description: 'The final page is the History archive where finalized protocols are permanently stored.',
                        side: "bottom",
                        align: "center",
                        doneBtnText: 'Next Page →'
                    }
                }
            ]
        });

        tour.drive();
    }

    // manual button support from layout
    window.startPageTutorial = function () {
        loadDriverThenRun(() => runChairStaffTutorial(true));
    };

    // automatic first-login flow
    loadDriverThenRun(() => {
        const isFirstLogin = @json(auth()->user()->is_first_login);
        const userId = @json(auth()->id());
        const storageKey = 'berc_tutorial_step_' + userId;
        const tourState = localStorage.getItem(storageKey);

        if (!isFirstLogin) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (tourState === 'chair_staff') {
            runChairStaffTutorial(false);
        }
    });

});
</script>
@endsection
