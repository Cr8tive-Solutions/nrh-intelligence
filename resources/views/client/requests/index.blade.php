<x-client.layouts.app pageTitle="Active Screenings">

    {{-- Page header --}}
    <div class="page-head">
        <div>
            <h1>Active <em>Screenings</em></h1>
            <div class="sub">Screenings currently being processed</div>
        </div>
        <a href="{{ route('client.request.new') }}" class="btn btn-primary">
            <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New screening
        </a>
    </div>

    {{-- Table card --}}
    <div class="card" x-data="{ filter: 'all', search: '' }">
        <div class="card-head">
            {{-- Filter chips --}}
            <div style="display:flex;align-items:center;gap:6px;">
                @foreach (['all' => 'All', 'new' => 'New', 'in_progress' => 'In Progress', 'complete' => 'Completed'] as $val => $label)
                    <button
                        @click="filter = '{{ $val }}'"
                        :style="filter === '{{ $val }}' ? 'background:var(--emerald-50);color:var(--emerald-800);border-color:rgba(4,108,78,0.2);' : 'background:transparent;color:var(--ink-500);border-color:var(--line);'"
                        style="padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;border:1px solid;cursor:pointer;transition:all 120ms;font-family:var(--font-ui);"
                    >{{ $label }}</button>
                @endforeach
            </div>

            {{-- Search --}}
            <div style="position:relative;width:240px;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--ink-400);pointer-events:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                <input x-model="search" type="text" placeholder="Search requests…"
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
                        <tr onclick="location.href='{{ route('client.requests.details', $req->id) }}'">
                            <td>
                                <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $req->reference }}</span>
                            </td>
                            <td style="color:var(--ink-700);">{{ $req->candidates_count }}</td>
                            <td>
                                @include('client.partials._status-badge', ['status' => $req->status])
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
                                <svg style="width:40px;height:40px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                </svg>
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No active screenings.</p>
                                <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);text-decoration:none;display:inline-block;margin-top:8px;">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

</x-client.layouts.app>
