@php
    $map = [
        'new'         => ['label' => 'New',         'pill' => 'pill-progress'],
        'in_progress' => ['label' => 'In Progress', 'pill' => 'pill-review'],
        'complete'    => ['label' => 'Complete',     'pill' => 'pill-clear'],
        'flagged'     => ['label' => 'Flagged',      'pill' => 'pill-flagged'],
    ];
    $badge = $map[$status] ?? ['label' => ucwords(str_replace('_', ' ', $status)), 'pill' => 'pill-pending'];
@endphp
<span class="pill {{ $badge['pill'] }}">
    <span class="dot"></span>
    {{ $badge['label'] }}
</span>
