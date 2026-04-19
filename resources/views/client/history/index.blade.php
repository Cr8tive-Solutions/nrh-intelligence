<x-client.layouts.app pageTitle="Reports">

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                <em style="font-style:italic;color:var(--emerald-700);">Completed</em> Reports
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Finalized background check requests</p>
        </div>
    </div>

    <div class="nrh-card">
        <div class="card-head">
            <h3>Report History</h3>
            <div style="position:relative;width:200px;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--ink-400);pointer-events:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                <input type="text" placeholder="Search…"
                    style="width:100%;padding:7px 10px 7px 30px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);"
                    onfocus="this.style.borderColor='var(--emerald-600)';this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.1)'"
                    onblur="this.style.borderColor='var(--line)';this.style.boxShadow=''"
                />
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="nrh-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Candidates</th>
                        <th style="width:160px;">Submitted</th>
                        <th style="width:160px;">Completed</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr onclick="location.href='{{ route('client.history.details', $req->id) }}'">
                            <td>
                                <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $req->reference }}</span>
                            </td>
                            <td style="color:var(--ink-700);">{{ $req->candidates_count }}</td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $req->created_at->format('d M Y') }}</td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:var(--emerald-700);font-family:var(--font-mono);">
                                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    {{ $req->updated_at->format('d M Y') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('client.history.details', $req->id) }}"
                                   class="btn-ghost" style="padding:5px 12px;font-size:12px;"
                                   onclick="event.stopPropagation()">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:60px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No completed requests yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client.layouts.app>
