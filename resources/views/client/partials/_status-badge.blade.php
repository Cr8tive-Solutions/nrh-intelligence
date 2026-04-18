@php
    $map = [
        1 => ['label' => 'New',       'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        2 => ['label' => 'Pending',   'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        3 => ['label' => 'Complete',  'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        4 => ['label' => 'Rejected',  'class' => 'bg-red-50 text-red-700 border-red-200'],
    ];
    $statusId = is_object($status) ? $status->id : $status;
    $badge = $map[$statusId] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'];
@endphp
<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge['class'] }}">
    {{ $badge['label'] }}
</span>
