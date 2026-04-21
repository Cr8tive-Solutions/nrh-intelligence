<x-admin.layouts.app pageTitle="Dashboard">

    <div class="page-head">
        <div>
            <h1>Operations <em>Dashboard</em></h1>
            <div class="sub">{{ now()->format('l, d M Y') }} · Welcome back, {{ session('admin_name') }}</div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats" style="margin-bottom:24px;">
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>Active Requests</div>
            <div class="stat-value">{{ $stats['active_requests'] }}</div>
            <div class="stat-delta">Awaiting processing</div>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark gold"></span>Flagged</div>
            <div class="stat-value" style="color:var(--gold-700);">{{ $stats['flagged_requests'] }}</div>
            <div class="stat-delta">Needs attention</div>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>Completed Today</div>
            <div class="stat-value">{{ $stats['completed_today'] }}</div>
            <div class="stat-delta">Finalised today</div>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark ink"></span>Customers</div>
            <div class="stat-value">{{ $stats['total_customers'] }}</div>
            <div class="stat-delta">Total accounts</div>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark red"></span>Unpaid Invoices</div>
            <div class="stat-value" style="{{ $stats['overdue_invoices'] > 0 ? 'color:var(--danger);' : '' }}">{{ $stats['unpaid_invoices'] }}</div>
            <div class="stat-delta">{{ $stats['overdue_invoices'] }} overdue</div>
        </div>
    </div>

    <div class="grid">
        {{-- Recent Requests --}}
        <div class="card">
            <div class="card-head">
                <h3>Recent Requests</h3>
                <a href="{{ route('admin.requests.index') }}" style="font-size:11px;color:var(--emerald-700);font-weight:600;">View all</a>
            </div>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th style="width:100px;">Candidates</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:110px;">Submitted</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRequests as $req)
                            <tr onclick="location.href='{{ route('admin.requests.show', $req->id) }}'">
                                <td><span style="font-family:var(--font-mono);font-size:12px;color:var(--emerald-700);font-weight:500;">{{ $req->reference }}</span></td>
                                <td style="font-weight:600;color:var(--ink-900);">{{ $req->customer->name }}</td>
                                <td style="font-family:var(--font-mono);font-size:12px;">{{ $req->candidates_count }}</td>
                                <td>@include('admin.partials._status-badge', ['status' => $req->status])</td>
                                <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $req->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.requests.show', $req->id) }}" class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" onclick="event.stopPropagation()">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="padding:40px;text-align:center;color:var(--ink-400);font-size:13px;">No requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Side column --}}
        <div class="side-col">

            {{-- Pending (New) --}}
            <div class="card">
                <div class="card-head">
                    <h3>Pending Action</h3>
                    <span class="count-pill">{{ $stats['active_requests'] }} NEW</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:2px;padding:0 4px 4px;">
                    @forelse ($pendingRequests as $req)
                        <a href="{{ route('admin.requests.show', $req->id) }}"
                           style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:var(--radius);text-decoration:none;transition:background 120ms;"
                           onmouseover="this.style.background='rgba(5,150,105,0.05)'"
                           onmouseout="this.style.background=''">
                            <div>
                                <p style="font-size:12px;font-weight:600;color:var(--emerald-700);font-family:var(--font-mono);margin:0;">{{ $req->reference }}</p>
                                <p style="font-size:11px;color:var(--ink-500);margin:2px 0 0;">{{ $req->customer->name }}</p>
                            </div>
                            <span style="font-size:10px;color:var(--ink-400);font-family:var(--font-mono);">{{ $req->created_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <p style="font-size:13px;color:var(--ink-400);padding:12px;margin:0;">No pending requests.</p>
                    @endforelse
                </div>
                @if ($stats['active_requests'] > 5)
                    <div style="padding:8px 16px;border-top:1px solid var(--line);">
                        <a href="{{ route('admin.requests.index', ['status' => 'new']) }}" style="font-size:12px;color:var(--emerald-700);font-weight:600;text-decoration:none;">View all {{ $stats['active_requests'] }} pending →</a>
                    </div>
                @endif
            </div>

        </div>
    </div>

</x-admin.layouts.app>
