<x-admin.layouts.app pageTitle="Requests Queue">

    <div class="page-head">
        <div>
            <h1>Requests <em>Queue</em></h1>
            <div class="sub">All screening requests across all customers</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card" style="padding:16px 20px;margin-bottom:16px;">
        <form method="GET" action="{{ route('admin.requests.index') }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            {{-- Status chips --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                @foreach ([''=>'All', 'new'=>'New', 'in_progress'=>'In Progress', 'flagged'=>'Flagged', 'complete'=>'Complete'] as $val => $label)
                    <a href="{{ route('admin.requests.index', array_merge(request()->except('status'), $val ? ['status'=>$val] : [])) }}"
                       style="padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;border:1px solid;cursor:pointer;text-decoration:none;transition:all 120ms;
                              {{ ($status ?? '') === $val ? 'background:var(--emerald-50);color:var(--emerald-800);border-color:rgba(4,108,78,0.2);' : 'background:transparent;color:var(--ink-500);border-color:var(--line);' }}">
                        {{ $label }}
                        <span style="font-family:var(--font-mono);font-size:10px;opacity:0.7;">{{ $counts[$val === '' ? 'all' : $val] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <div style="display:flex;gap:8px;margin-left:auto;">
                <div style="position:relative;">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--ink-400);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Reference or customer…"
                           style="padding:7px 10px 7px 30px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);outline:none;width:220px;font-family:var(--font-ui);"/>
                </div>
                <button type="submit" class="btn btn-ghost" style="font-size:13px;">Search</button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <div style="overflow-x:auto;">
            <div class="table-scroll"><table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th style="width:90px;">Type</th>
                        <th style="width:90px;">Candidates</th>
                        <th style="width:130px;">Status</th>
                        <th style="width:110px;">Submitted</th>
                        <th style="width:70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr onclick="location.href='{{ route('admin.requests.show', $req->id) }}'">
                            <td><span style="font-family:var(--font-mono);font-size:12px;color:var(--emerald-700);font-weight:500;">{{ $req->reference }}</span></td>
                            <td style="font-weight:600;color:var(--ink-900);">{{ $req->customer->name }}</td>
                            <td style="font-size:12px;color:var(--ink-600);">{{ strtoupper($req->type ?? 'EMP') }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;">{{ $req->candidates_count }}</td>
                            <td>@include('admin.partials._status-badge', ['status' => $req->status])</td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $req->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.requests.show', $req->id) }}" class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" onclick="event.stopPropagation()">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:60px;text-align:center;color:var(--ink-400);font-size:13px;">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

</x-admin.layouts.app>
