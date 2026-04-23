@props(['status'])
@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-700',
    'submitted' => 'bg-blue-100 text-blue-700',
    'screened' => 'bg-blue-200 text-blue-800',
    'classified' => 'bg-indigo-100 text-indigo-700',
    'assigned' => 'bg-indigo-200 text-indigo-800',
    'under_review' => 'bg-yellow-100 text-yellow-800',
    'feedback_submitted' => 'bg-yellow-200 text-yellow-900',
    'completed' => 'bg-yellow-200 text-yellow-900',
    'decision_released' => 'bg-teal-100 text-teal-800',
    'modifications_required' => 'bg-orange-100 text-orange-800',
    'resubmitted' => 'bg-orange-200 text-orange-900',
    'approved' => 'bg-green-100 text-green-800',
    'disapproved' => 'bg-red-100 text-red-800',
    'deferred' => 'bg-purple-100 text-purple-800',
    'pending' => 'bg-purple-200 text-purple-900',
    'continuing_review' => 'bg-cyan-100 text-cyan-800',
    'final_report' => 'bg-teal-100 text-teal-800',
    'archived' => 'bg-gray-200 text-gray-600',
];
$color = $colors[$status] ?? 'bg-gray-100 text-gray-700';
$label = ucwords(str_replace('_', ' ', $status));
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">{{ $label }}</span>
