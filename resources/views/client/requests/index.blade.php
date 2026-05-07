<x-client.layouts.app pageTitle="Active Requests">

    {{-- Page header --}}
    <div class="page-head">
        <div>
            <h1>Active <em>Requests</em></h1>
            <div class="sub">Requests currently being processed</div>
        </div>
        <a href="{{ route('client.request.new') }}" class="btn btn-primary">
            <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New request
        </a>
    </div>

    @if (($awaitingPaymentCount ?? 0) > 0)
        <div style="margin-bottom:16px;padding:12px 16px;background:rgba(184,147,31,0.06);border:1px solid rgba(184,147,31,0.25);border-left:3px solid var(--gold-500);border-radius:var(--radius);display:flex;align-items:center;gap:10px;font-size:13px;color:var(--gold-700);">
            <svg style="width:16px;height:16px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v4M12 16h.01"/><circle cx="12" cy="12" r="9"/></svg>
            <span>
                <b>{{ $awaitingPaymentCount }}</b> {{ Str::plural('request', $awaitingPaymentCount) }} awaiting payment.
                Use the <b>Payment</b> filter below to upload your bank-transfer slips.
            </span>
        </div>
    @endif

    {{-- Table card --}}
    <div class="card" x-data="{
        filter: 'all',
        search: '',
        matches(status, ref, payment) {
            const filterOk = this.filter === 'all'
                || (this.filter === 'payment' ? payment === 'awaiting' : this.filter === status);
            const q = this.search.trim().toLowerCase();
            const searchOk = q === '' || ref.toLowerCase().includes(q);
            return filterOk && searchOk;
        },
        get visibleCount() {
            return Array.from(this.$root.querySelectorAll('tr[data-row]'))
                .filter(r => r.style.display !== 'none').length;
        },
    }">
        @php
            $counts = [
                'all' => $requests->count(),
                'new' => $requests->where('status', 'new')->count(),
                'in_progress' => $requests->where('status', 'in_progress')->count(),
                'flagged' => $requests->where('status', 'flagged')->count(),
                'complete' => $requests->where('status', 'complete')->count(),
                'payment' => $awaitingPaymentCount ?? 0,
            ];

            $tabs = ['all' => 'All', 'new' => 'New', 'in_progress' => 'In Progress', 'flagged' => 'Flagged', 'complete' => 'Completed'];
            if (($isCashBilled ?? false)) {
                $tabs = ['all' => 'All', 'payment' => 'Payment', 'new' => 'New', 'in_progress' => 'In Progress', 'flagged' => 'Flagged', 'complete' => 'Completed'];
            }
        @endphp

        <div class="tab-bar">
            {{-- Tabs --}}
            <div class="tab-list" role="tablist">
                @foreach ($tabs as $val => $label)
                    <button type="button" role="tab" class="tab-item"
                        @click="filter = '{{ $val }}'"
                        :class="{ 'is-active': filter === '{{ $val }}' }"
                        :aria-selected="filter === '{{ $val }}' ? 'true' : 'false'">
                        {{ $label }}
                        <span class="tab-count">{{ $counts[$val] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Search --}}
            <div style="position:relative;width:240px;padding:10px 0;">
                <label for="requests-search" class="sr-only">Search requests</label>
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--ink-400);pointer-events:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                <input id="requests-search" x-model="search" type="text" placeholder="Search by reference…" aria-label="Search requests"
                    style="width:100%;padding:8px 10px 8px 32px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);transition:border-color 120ms,box-shadow 120ms;"
                />
            </div>
        </div>

        <div style="overflow-x:auto;">
            <div class="table-scroll"><table class="table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Candidates</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:160px;">Submitted</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        @php
                            $paymentState = (($isCashBilled ?? false) && $req->status === 'new' && ! $req->hasPaymentSlip()) ? 'awaiting' : 'none';
                        @endphp
                        <tr data-row data-status="{{ $req->status }}" data-ref="{{ $req->reference }}" data-payment="{{ $paymentState }}"
                            x-show="matches('{{ $req->status }}', @js($req->reference), '{{ $paymentState }}')"
                            onclick="location.href='{{ route('client.requests.details', $req->id) }}'"
                            style="cursor:pointer;">
                            <td>
                                <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $req->reference }}</span>
                            </td>
                            <td style="color:var(--ink-700);">{{ $req->candidates_count }}</td>
                            <td>
                                @include('client.partials._status-badge', ['status' => $req->status, 'request' => $req, 'isCashBilled' => $isCashBilled ?? false])
                            </td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $req->created_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('client.requests.details', $req->id) }}"
                                   class="btn btn-ghost" style="padding:5px 12px;font-size:12px;"
                                   onclick="event.stopPropagation()">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:60px 20px;text-align:center;">
                                <svg style="width:40px;height:40px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                </svg>
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No active requests.</p>
                                <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);text-decoration:none;display:inline-block;margin-top:8px;">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse

                    @if ($requests->isNotEmpty())
                        <tr x-show="visibleCount === 0" x-cloak>
                            <td colspan="5" style="padding:48px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No requests match the current filter.</p>
                                <button type="button" @click="filter = 'all'; search = ''"
                                    style="font-size:13px;font-weight:600;color:var(--emerald-700);background:none;border:none;cursor:pointer;margin-top:8px;font-family:var(--font-ui);">Clear filters</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table></div>
        </div>
    </div>

</x-client.layouts.app>
