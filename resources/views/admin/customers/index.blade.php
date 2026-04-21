<x-admin.layouts.app pageTitle="Customers">

    <div class="page-head">
        <div>
            <h1>Customer <em>Accounts</em></h1>
            <div class="sub">All registered corporate clients</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card" style="padding:14px 20px;margin-bottom:16px;">
        <form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex;gap:8px;">
            <div style="position:relative;flex:1;max-width:360px;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--ink-400);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Company name, reg. no., or email…"
                       style="width:100%;padding:7px 10px 7px 30px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);outline:none;font-family:var(--font-ui);"/>
            </div>
            <button type="submit" class="btn btn-ghost" style="font-size:13px;">Search</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-head">
            <h3>All Customers</h3>
            <span class="count-pill">{{ count($customers) }} ACCOUNTS</span>
        </div>
        <div style="overflow-x:auto;">
            <div class="table-scroll"><table class="table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Registration No.</th>
                        <th style="width:120px;">Industry</th>
                        <th style="width:80px;">Users</th>
                        <th style="width:90px;">Requests</th>
                        <th style="width:120px;">Agreement</th>
                        <th style="width:70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr onclick="location.href='{{ route('admin.customers.show', $customer->id) }}'">
                            <td>
                                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">{{ $customer->name }}</p>
                                <p style="font-size:11px;color:var(--ink-400);margin:2px 0 0;">{{ $customer->contact_email }}</p>
                            </td>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-600);">{{ $customer->registration_no ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--ink-600);">{{ $customer->industry ?? '—' }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;">{{ $customer->users_count }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;">{{ $customer->screening_requests_count }}</td>
                            <td>
                                @if ($customer->agreement)
                                    @php $daysLeft = $customer->agreement->days_left; @endphp
                                    <span class="pill {{ $daysLeft <= 14 ? 'pill-review' : ($daysLeft <= 60 ? 'pill-pending' : 'pill-clear') }}">
                                        <span class="dot"></span>
                                        {{ $daysLeft > 0 ? $daysLeft.'d left' : 'Expired' }}
                                    </span>
                                @else
                                    <span style="font-size:12px;color:var(--ink-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" onclick="event.stopPropagation()">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:60px;text-align:center;color:var(--ink-400);font-size:13px;">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

</x-admin.layouts.app>
