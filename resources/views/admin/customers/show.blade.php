<x-admin.layouts.app pageTitle="{{ $customer->name }}">

    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('admin.customers.index') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Customer</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $customer->name }}</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;">

        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Recent Requests --}}
            <div class="card">
                <div class="card-head">
                    <h3>Screening Requests</h3>
                    <a href="{{ route('admin.requests.index', ['q' => $customer->name]) }}" style="font-size:12px;color:var(--emerald-700);font-weight:600;">View all</a>
                </div>
                <div class="table-scroll"><table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th style="width:90px;">Candidates</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:110px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->screeningRequests as $req)
                            <tr onclick="location.href='{{ route('admin.requests.show', $req->id) }}'">
                                <td><span style="font-family:var(--font-mono);font-size:12px;color:var(--emerald-700);">{{ $req->reference }}</span></td>
                                <td style="font-family:var(--font-mono);font-size:12px;">{{ $req->candidates_count }}</td>
                                <td>@include('admin.partials._status-badge', ['status' => $req->status])</td>
                                <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $req->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="padding:32px;text-align:center;color:var(--ink-400);font-size:13px;">No requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>

            {{-- Users --}}
            <div class="card">
                <div class="card-head">
                    <h3>Team Members</h3>
                    <span class="count-pill">{{ $customer->users->count() }} USERS</span>
                </div>
                <div class="table-scroll"><table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width:100px;">Role</th>
                            <th style="width:100px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->users as $user)
                            <tr>
                                <td style="font-weight:600;color:var(--ink-900);">{{ $user->name }}</td>
                                <td style="font-size:12px;color:var(--ink-500);">{{ $user->email }}</td>
                                <td><span class="pill pill-pending"><span class="dot"></span>{{ ucfirst($user->role) }}</span></td>
                                <td><span class="pill {{ $user->status === 'active' ? 'pill-clear' : 'pill-pending' }}"><span class="dot"></span>{{ ucfirst($user->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:12px;">

            {{-- Company info --}}
            <div class="card" style="padding:20px 24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Company Info</h3>
                <dl style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ([
                        ['Registration', $customer->registration_no ?? '—', true],
                        ['Industry',     $customer->industry ?? '—',        false],
                        ['Country',      $customer->country ?? '—',         false],
                        ['Contact',      $customer->contact_name ?? '—',    false],
                        ['Email',        $customer->contact_email ?? '—',   false],
                        ['Phone',        $customer->contact_phone ?? '—',   false],
                    ] as [$label, $value, $mono])
                        <div>
                            <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);">{{ $label }}</dt>
                            <dd style="font-size:13px;font-weight:500;color:var(--ink-900);margin:3px 0 0;word-break:break-all;{{ $mono ? 'font-family:var(--font-mono);font-size:12px;' : '' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Agreement --}}
            @if ($customer->agreement)
                <div class="card" style="padding:20px 24px;">
                    <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Service Agreement</h3>
                    <dl style="display:flex;flex-direction:column;gap:12px;">
                        @foreach ([
                            ['Type',    ucfirst($customer->agreement->type ?? '—')],
                            ['Start',   $customer->agreement->start_date?->format('d M Y') ?? '—'],
                            ['Expiry',  $customer->agreement->expiry_date?->format('d M Y') ?? '—'],
                            ['SLA TAT', ($customer->agreement->sla_tat ?? '—').' days'],
                            ['Billing', ucfirst($customer->agreement->billing ?? '—')],
                        ] as [$label, $value])
                            <div>
                                <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);">{{ $label }}</dt>
                                <dd style="font-size:13px;font-weight:500;color:var(--ink-900);margin:3px 0 0;">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    @php $daysLeft = $customer->agreement->days_left; @endphp
                    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--line);">
                        <span class="pill {{ $daysLeft <= 14 ? 'pill-review' : ($daysLeft <= 60 ? 'pill-pending' : 'pill-clear') }}">
                            <span class="dot"></span>
                            {{ $daysLeft > 0 ? $daysLeft.' days remaining' : 'Expired' }}
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-admin.layouts.app>
