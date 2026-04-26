<x-client.layouts.app pageTitle="Audit Log">

    <div class="page-head">
        <div>
            <h1>Audit <em>Log</em></h1>
            <div class="sub">All activity by users in your team</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('client.settings.audit-log') }}" class="card" style="padding:16px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <div class="field" style="flex:1;min-width:160px;margin:0;">
            <label for="filter-user">User</label>
            <select id="filter-user" name="user">
                <option value="">All users</option>
                @foreach ($teamUsers as $u)
                    <option value="{{ $u->id }}" @selected((int) request('user') === $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="flex:1;min-width:160px;margin:0;">
            <label for="filter-event">Event</label>
            <select id="filter-event" name="event">
                <option value="">All events</option>
                @foreach ($events as $e)
                    <option value="{{ $e }}" @selected(request('event') === $e)>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="flex:0 0 160px;margin:0;">
            <label for="filter-from">From</label>
            <input id="filter-from" name="from" type="date" value="{{ request('from') }}"/>
        </div>
        <div class="field" style="flex:0 0 160px;margin:0;">
            <label for="filter-to">To</label>
            <input id="filter-to" name="to" type="date" value="{{ request('to') }}"/>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('client.settings.audit-log') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="card" style="margin-top:16px;" x-data="{ openId: null }">
        <div class="table-scroll" style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:160px;">When</th>
                        <th style="width:200px;">User</th>
                        <th style="width:140px;">Event</th>
                        <th>Description</th>
                        <th style="width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr style="cursor:pointer;" @click="openId = (openId === {{ $activity->id }} ? null : {{ $activity->id }})">
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);">{{ $activity->created_at->format('d M Y · H:i') }}</td>
                            <td>
                                @if ($activity->causer)
                                    <div style="font-size:13px;color:var(--ink-900);">{{ $activity->causer->name }}</div>
                                    <div style="font-size:11px;color:var(--ink-400);">{{ $activity->causer->email }}</div>
                                @else
                                    <span style="font-size:12px;color:var(--ink-400);">System</span>
                                @endif
                            </td>
                            <td>
                                <span class="pill pill-pending" style="font-family:var(--font-mono);"><span class="dot"></span>{{ $activity->event ?? $activity->log_name }}</span>
                            </td>
                            <td style="font-size:13px;color:var(--ink-700);">{{ $activity->description }}</td>
                            <td style="text-align:right;">
                                <svg style="width:14px;height:14px;color:var(--ink-400);transition:transform 150ms;" :style="openId === {{ $activity->id }} ? 'transform:rotate(180deg)' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </td>
                        </tr>
                        <tr x-show="openId === {{ $activity->id }}" x-cloak>
                            <td colspan="5" style="background:var(--paper);padding:14px 18px;border-top:1px solid var(--line);">
                                <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);margin-bottom:8px;">Properties</div>
                                <pre style="font-family:var(--font-mono);font-size:11px;color:var(--ink-700);white-space:pre-wrap;word-break:break-all;margin:0;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @if ($activity->subject_type)
                                    <div style="margin-top:10px;font-size:11px;color:var(--ink-500);">
                                        Subject: <span style="font-family:var(--font-mono);">{{ class_basename($activity->subject_type) }}#{{ $activity->subject_id }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:60px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No audit entries match the current filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($activities->hasPages())
            <div style="padding:14px 18px;border-top:1px solid var(--line);">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</x-client.layouts.app>
