<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERC | Resubmission Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-header { font-family: 'Montserrat', sans-serif; }

        #success-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s ease;
        }
        #success-overlay.visible {
            opacity: 1;
            pointer-events: all;
        }
    </style>
</head>
<body class="bg-gray-50 py-10 px-4">

    <div class="max-w-5xl mx-auto bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">

        <div class="bg-white border-b-4 border-blue-900 p-8 text-center">
            <h1 class="font-header font-black text-2xl uppercase tracking-tighter text-blue-900">Resubmission Form</h1>

            {{-- Optional Info Banner --}}
            <div class="mt-4 max-w-2xl mx-auto bg-blue-50 border border-blue-100 rounded-lg px-5 py-3 text-left flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-[12px] text-blue-700 font-semibold leading-relaxed">
                    Please address each BERC recommendation below. You may edit the recommendation text if you need to group related points together.
                </p>
            </div>
        </div>

        <form id="resubmission-form" action="{{ route('resubmission.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf

            {{-- Critical: Hidden Protocol Code for Controller lookup --}}
            <input type="hidden" name="protocol_code" value="{{ $application->protocol_code }}">

            <div class="mb-8">
                <h2 class="bg-gray-100 px-4 py-2 font-header font-bold text-xs uppercase text-gray-700 border-l-4 border-red-600 mb-4">General Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase text-blue-900 mb-1 tracking-widest">Title of the Study</label>
                        <input type="text" value="{{ $application->research_title }}"
                            class="w-full border-2 border-gray-100 rounded-lg p-3 text-sm bg-gray-50 font-semibold text-gray-800 cursor-default"
                            readonly>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-blue-900 mb-1 tracking-widest">
                            Version Number / Date
                        </label>
                        <input type="text"
                            name="version"
                            value="{{ $autoVersion }}"
                            required
                            class="w-full border-2 border-gray-100 rounded-lg p-3 text-sm focus:border-blue-900 outline-none font-bold text-blue-800 bg-blue-50/30 transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-blue-900 mb-1 tracking-widest">BERC Protocol Code</label>
                        <input type="text" value="{{ $application->protocol_code }}"
                            class="w-full border-2 border-gray-100 rounded-lg p-3 text-sm bg-gray-50 cursor-not-allowed font-bold text-gray-500"
                            readonly>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="bg-gray-100 px-4 py-2 font-header font-bold text-xs uppercase text-gray-700 border-l-4 border-red-600 mb-4 text-center">Revisions Log</h2>
                <div class="overflow-hidden border-2 border-gray-100 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-blue-900 text-white text-[10px] font-header font-black uppercase tracking-widest">
                                <th class="p-4 border-r border-blue-800 w-[35%]">BERC Recommendations</th>
                                <th class="p-4 border-r border-blue-800 w-[35%]">Response of Researcher</th>
                                <th class="p-4 border-r border-blue-800 w-[20%]">Page No.</th>
                            </tr>
                        </thead>
                        <tbody id="recommendation-rows">
                            @php $currentSection = null; @endphp

                            @forelse($assessmentItems as $index => $item)
                                {{-- Section Header - Triggered when section name changes --}}
                                @if($currentSection !== $item->section_name)
                                    @php $currentSection = $item->section_name; @endphp
                                    <tr class="bg-blue-50/50">
                                        <td colspan="4" class="px-4 py-2 border-b border-gray-200">
                                            <span class="text-[10px] font-black text-blue-900 uppercase tracking-widest flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-blue-900"></span>
                                                {{ $item->section_name }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif

                                <tr class="border-b group">
                                    <td class="p-4 border-r border-gray-100 bg-slate-50/80 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600"></div>

                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-blue-900 uppercase tracking-widest">
                                                    Item {{ $item->question_number }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-bold uppercase">
                                                    BERC Recommendation
                                                </span>
                                            </div>

                                            <div class="text-[11px] leading-relaxed text-slate-700 font-bold">
                                                {!! nl2br(e($item->full_recommendation)) !!}
                                            </div>

                                            <input type="hidden" name="items[{{ $index }}]" value="{{ $item->question_number }}">
                                            <input type="hidden" name="recommendations[{{ $index }}]" value="{{ $item->synthesized_comments }}">
                                        </div>
                                    </td>
                                    <td class="p-0 border-r border-gray-100">
                                        <textarea name="responses[{{ $index }}]" required
                                            class="w-full p-4 text-[11px] font-medium outline-none focus:bg-blue-50/30 min-h-[120px] resize-none"
                                            placeholder="Explain how you addressed this..."></textarea>
                                    </td>
                                    <td class="p-0 border-r border-gray-100">
                                        <textarea name="section_pages[{{ $index }}]" required
                                            class="w-full p-4 text-[11px] font-bold text-center outline-none focus:bg-blue-50/30 min-h-[120px] resize-none"
                                            placeholder="Page #"></textarea>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400 text-xs italic">No specific recommendations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="bg-gray-100 px-4 py-2 font-header font-bold text-xs uppercase text-gray-700 border-l-4 border-red-600 mb-4 text-center">Upload Revised Documents</h2>

                <div class="p-6 border-2 border-gray-100 rounded-xl space-y-6">

                    <div>
                        <label class="block text-[10px] font-black uppercase text-blue-900 mb-2 tracking-widest">
                            Revised Manuscript <span class="text-gray-400 font-medium normal-case tracking-normal">(PDF, DOC, DOCX - Optional)</span>
                        </label>
                        <input type="file" name="revised_manuscript" accept=".pdf,.doc,.docx"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition border border-gray-200 rounded-lg p-2 bg-gray-50 cursor-pointer">
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                            <label class="block text-[10px] font-black uppercase text-blue-900 tracking-widest">
                                Revised Informed Consent Forms <span class="text-gray-400 font-medium normal-case tracking-normal">(Optional)</span>
                            </label>
                            <button type="button" onclick="addIcfRow()"
                                class="text-[9px] font-black uppercase text-red-600 flex items-center hover:text-red-800 transition tracking-widest bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg border border-red-100 w-fit">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Language / Dialect
                            </button>
                        </div>

                        <div id="icf-upload-container" class="space-y-3">
                            <div class="flex flex-col md:flex-row items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200 icf-row">
                                <div class="w-full md:w-1/3">
                                    <input type="text" name="icf_languages[]" placeholder="Language (e.g. English, Tagalog)"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:border-blue-900 outline-none font-medium">
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file" name="icf_files[]" accept=".pdf,.doc,.docx"
                                        class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:font-bold file:bg-white file:border-gray-200 file:shadow-sm file:text-gray-700 border border-gray-300 rounded bg-white p-1.5 cursor-pointer">
                                </div>
                                <div class="w-full md:w-8 flex justify-end md:justify-center"></div>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-blue-900 tracking-widest">
                                        Other Documents <span class="text-gray-400 font-medium normal-case tracking-normal">(Optional)</span>
                                    </label>
                                    <p class="text-[9px] text-gray-400 font-medium">Add Other Documents as per Instruction</p>
                                </div>

                                <button type="button" onclick="addOtherRow()"
                                    class="text-[9px] font-black uppercase text-blue-600 flex items-center hover:text-blue-800 transition tracking-widest bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg border border-blue-100 w-fit">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Other Document
                                </button>
                            </div>

                            <div id="other-upload-container" class="space-y-4 pb-4">
                                {{-- Rows added via JS will appear here --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('application.status') }}"
                    class="text-[10px] font-header font-black text-gray-400 uppercase tracking-widest hover:text-red-600 transition">
                    Cancel resubmission
                </a>
                <button type="submit" id="submit-btn"
                    class="bg-blue-900 text-white px-10 py-3 rounded-lg font-header font-black text-xs uppercase tracking-widest shadow-lg hover:bg-blue-800 transition-all active:scale-95">
                    Submit Revision
                </button>
            </div>
        </form>
    </div>

    <div id="success-overlay" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="font-header font-black text-[18px] text-blue-900 uppercase tracking-tight mb-2">Submitting...</h3>
            <p class="text-[12px] text-gray-500 font-medium leading-relaxed mb-6">
                Please wait while we process your resubmission responses.
            </p>
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div id="redirect-bar"
                    class="bg-blue-900 h-full rounded-full transition-all duration-[2000ms] ease-linear"
                    style="width: 0%"></div>
            </div>
        </div>
    </div>

    <script>
    /* ══════════════════════════════════════════════════════════
        DYNAMIC TABLE ROWS
       ══════════════════════════════════════════════════════════ */
    // rowCount starts after the Blade-generated items
    let rowCount = {{ count($assessmentItems) > 0 ? count($assessmentItems) : 1 }};

    function removeRow(btn) {
        btn.closest('tr').remove();
    }

    /* ══════════════════════════════════════════════════════════
        DYNAMIC ICF UPLOAD ROWS
       ══════════════════════════════════════════════════════════ */
    function addIcfRow() {
        const container = document.getElementById('icf-upload-container');
        const newRow = document.createElement('div');
        newRow.className = 'flex flex-col md:flex-row items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200 icf-row';
        newRow.innerHTML = `
            <div class="w-full md:w-1/3">
                <input type="text" name="icf_languages[]" placeholder="Language (e.g. Cebuano, Ilokano)"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:border-blue-900 outline-none font-medium">
            </div>
            <div class="flex-1 w-full">
                <input type="file" name="icf_files[]" accept=".pdf,.doc,.docx"
                    class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:font-bold file:bg-white file:border-gray-200 file:shadow-sm file:text-gray-700 border border-gray-300 rounded bg-white p-1.5 cursor-pointer">
            </div>
            <div class="w-full md:w-8 flex justify-end md:justify-center">
                <button type="button" onclick="this.closest('.icf-row').remove()" class="text-gray-400 hover:text-red-600 transition-colors p-1 bg-white border border-gray-200 rounded md:border-none md:bg-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    }

    /* ══════════════════════════════════════════════════════════
        UI HANDLER
       ══════════════════════════════════════════════════════════ */
    document.getElementById('resubmission-form').addEventListener('submit', function(e) {
        // Form is handled by standard POST, but we show the overlay for UX
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        document.getElementById('success-overlay').classList.add('visible');

        requestAnimationFrame(() => {
            document.getElementById('redirect-bar').style.width = '100%';
        });
    });

    function addOtherRow() {
        const container = document.getElementById('other-upload-container');
        const newRow = document.createElement('div');
        newRow.className = 'flex flex-col md:flex-row items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200 other-row';
        newRow.innerHTML = `
            <div class="w-full md:w-1/3">
                <input type="text" name="other_descriptions[]" placeholder="Document Name (e.g. CV, Site Approval)"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:border-blue-900 outline-none font-medium">
            </div>
            <div class="flex-1 w-full">
                <input type="file" name="other_files[]" accept=".pdf,.doc,.docx,.jpg,.png"
                    class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:font-bold file:bg-white file:border-gray-200 file:shadow-sm file:text-gray-700 border border-gray-300 rounded bg-white p-1.5 cursor-pointer">
            </div>
            <div class="w-full md:w-8 flex justify-end md:justify-center">
                <button type="button" onclick="this.closest('.other-row').remove()" class="text-gray-400 hover:text-red-600 transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    }
    </script>
</body>
</html>
