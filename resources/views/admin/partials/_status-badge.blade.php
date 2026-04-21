@php
    $map = [
        'new'         => ['pill-pending',  'New'],
        'in_progress' => ['pill-progress', 'In Progress'],
        'flagged'     => ['pill-review',   'Flagged'],
        'complete'    => ['pill-clear',    'Complete'],
    ];
    [$cls, $label] = $map[$status] ?? ['pill-pending', ucfirst($status)];
@endphp
<span class="pill {{ $cls }}"><span class="dot"></span>{{ $label }}</span>
