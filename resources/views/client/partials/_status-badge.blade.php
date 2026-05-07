@php
    /**
     * Status colour mapping (per 2026-05-05 client meeting):
     *   Blue   — new        — pending NRH
     *   Red    — rejected   — insufficient documents, no TAT
     *   Yellow — in_progress — TAT begins
     *   Black  — prelim
     *   Green  — complete
     *   Green  — updated
     *
     * "rejected", "prelim", "updated" depend on admin-side schema support.
     * Until they ship, unknown statuses fall back to a muted "Pending" pill.
     *
     * For cash-billed customers, "new" is overridden to surface payment state
     * so the list answers "which request is awaiting payment?" without a click-in.
     * Pass $request and $isCashBilled to enable the override.
     */
    $map = [
        'new'         => ['label' => 'New',         'pill' => 'pill-progress'],
        'in_progress' => ['label' => 'In Progress', 'pill' => 'pill-review'],
        'rejected'    => ['label' => 'Rejected',    'pill' => 'pill-flagged'],
        'prelim'      => ['label' => 'Prelim',      'pill' => 'pill-prelim'],
        'complete'    => ['label' => 'Complete',    'pill' => 'pill-clear'],
        'updated'     => ['label' => 'Updated',     'pill' => 'pill-clear'],
        'flagged'     => ['label' => 'Flagged',     'pill' => 'pill-flagged'],
    ];

    $cashOverride = null;
    if (($isCashBilled ?? false) && $status === 'new' && isset($request)) {
        $cashOverride = match (true) {
            $request->isPaymentVerified() => ['label' => 'Payment received', 'pill' => 'pill-clear'],
            $request->hasPaymentSlip()    => ['label' => 'Verifying payment', 'pill' => 'pill-pending'],
            default                       => ['label' => 'Awaiting payment',  'pill' => 'pill-flagged'],
        };
    } elseif (($isCashBilled ?? false) && $status === 'new') {
        $cashOverride = ['label' => 'Awaiting payment', 'pill' => 'pill-flagged'];
    }

    $badge = $cashOverride ?? ($map[$status] ?? ['label' => ucwords(str_replace('_', ' ', $status)), 'pill' => 'pill-pending']);
@endphp
<span class="pill {{ $badge['pill'] }}">
    <span class="dot"></span>
    {{ $badge['label'] }}
</span>
