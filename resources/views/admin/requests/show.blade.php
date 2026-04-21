<x-admin.layouts.app pageTitle="{{ $request->reference }}">

    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('admin.requests.index') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">{{ $request->customer->name }}</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $request->reference }}</div>
            </div>
        </div>

        {{-- Update request status --}}
        <form method="POST" action="{{ route('admin.requests.status', $request->id) }}" style="display:flex;align-items:center;gap:8px;">
            @csrf
            @method('PATCH')
            <select name="status" style="padding:7px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);outline:none;cursor:pointer;">
                @foreach (['new' => 'New', 'in_progress' => 'In Progress', 'flagged' => 'Flagged', 'complete' => 'Complete'] as $val => $label)
                    <option value="{{ $val }}" {{ $request->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="font-size:13px;">Update Status</button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;">

        {{-- Candidates --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                <div class="card-head">
                    <h3>Candidates</h3>
                    <span style="font-size:12px;color:var(--ink-400);">{{ $request->candidates->count() }} total</span>
                </div>
                <div style="overflow-x:auto;">
                    <div class="table-scroll"><table class="table">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Name</th>
                                <th>Identity No.</th>
                                <th style="width:160px;">Scopes</th>
                                <th style="width:160px;">Status</th>
                                <th style="width:130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($request->candidates as $i => $candidate)
                                <tr>
                                    <td style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);">{{ $i + 1 }}</td>
                                    <td style="font-weight:600;color:var(--ink-900);">{{ $candidate->name }}</td>
                                    <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);">{{ $candidate->identity_number }}</td>
                                    <td>
                                        <span style="font-size:11px;color:var(--ink-500);">{{ $candidate->scopeTypes->count() }} checks</span>
                                    </td>
                                    <td>@include('admin.partials._status-badge', ['status' => $candidate->status])</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.requests.candidate.status', $candidate->id) }}" style="display:flex;gap:6px;">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" style="padding:4px 6px;border:1px solid var(--line);border-radius:var(--radius);font-size:11px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);outline:none;">
                                                @foreach (['new' => 'New', 'in_progress' => 'In Progress', 'flagged' => 'Flagged', 'complete' => 'Complete'] as $val => $label)
                                                    <option value="{{ $val }}" {{ $candidate->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-ghost" style="padding:3px 8px;font-size:11px;">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>

        {{-- Sidebar info --}}
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div class="card" style="padding:20px 24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Request Info</h3>
                <dl style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ([
                        ['Reference',    $request->reference,                                  true],
                        ['Customer',     $request->customer->name,                             false],
                        ['Type',         strtoupper($request->type ?? 'Employment'),           false],
                        ['Status',       ucwords(str_replace('_', ' ', $request->status)),     false],
                        ['Submitted By', $request->submittedBy?->name ?? '—',                  false],
                        ['Date',         $request->created_at->format('d M Y, H:i'),           false],
                    ] as [$label, $value, $mono])
                        <div>
                            <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);">{{ $label }}</dt>
                            <dd style="font-size:13px;font-weight:600;color:var(--ink-900);margin:3px 0 0;{{ $mono ? 'font-family:var(--font-mono);' : '' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <a href="{{ route('admin.customers.show', $request->customer->id) }}" class="btn btn-ghost" style="justify-content:center;font-size:13px;">
                View Customer Profile →
            </a>
        </div>
    </div>

</x-admin.layouts.app>
