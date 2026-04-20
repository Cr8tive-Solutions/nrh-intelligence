<x-client.layouts.app pageTitle="Notifications">

    <div class="page-head">
        <div>
            <h1>
                <em style="font-style:italic;color:var(--emerald-700);">Notifications</em>
            </h1>
            <div class="sub">
                @if ($unreadCount > 0)
                    <b style="color:var(--ink-900);">{{ $unreadCount }}</b> unread ·
                @endif
                {{ $notifications->count() }} total alerts
            </div>
        </div>
        @if ($unreadCount > 0)
            <button class="btn btn-ghost" style="font-size:13px;">Mark all as read</button>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <div class="card" style="padding:80px 20px;text-align:center;">
            <svg style="width:44px;height:44px;color:var(--ink-200);margin:0 auto 16px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
            <p style="font-size:13px;color:var(--ink-400);margin:0;">You're all caught up. No notifications.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:8px;max-width:760px;">

            @foreach ($notifications as $notif)
                @php
                    $borderColor = match($notif['type']) {
                        'danger'  => 'rgba(196,69,58,0.3)',
                        'warning' => 'rgba(184,147,31,0.3)',
                        'success' => 'rgba(5,150,105,0.3)',
                        default   => 'var(--line)',
                    };
                    $bgColor = match($notif['type']) {
                        'danger'  => 'rgba(196,69,58,0.04)',
                        'warning' => 'rgba(184,147,31,0.04)',
                        'success' => 'rgba(5,150,105,0.04)',
                        default   => 'var(--card)',
                    };
                    $iconColor = match($notif['type']) {
                        'danger'  => 'var(--danger)',
                        'warning' => 'var(--gold-600)',
                        'success' => 'var(--emerald-700)',
                        default   => 'var(--ink-400)',
                    };
                    $iconBg = match($notif['type']) {
                        'danger'  => 'rgba(196,69,58,0.1)',
                        'warning' => 'rgba(184,147,31,0.1)',
                        'success' => 'var(--emerald-50)',
                        default   => 'var(--paper)',
                    };
                @endphp
                <div style="display:flex;align-items:flex-start;gap:16px;padding:16px 20px;background:{{ $bgColor }};border:1px solid {{ $borderColor }};border-left:3px solid {{ $borderColor }};border-radius:var(--radius);{{ ! $notif['read'] ? 'box-shadow:0 0 0 3px rgba(5,150,105,0.08);' : '' }}">

                    {{-- Icon --}}
                    <div style="width:36px;height:36px;border-radius:var(--radius);background:{{ $iconBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        @if ($notif['icon'] === 'shield')
                            <svg style="width:16px;height:16px;color:{{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                        @elseif ($notif['icon'] === 'invoice')
                            <svg style="width:16px;height:16px;color:{{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        @elseif ($notif['icon'] === 'check')
                            <svg style="width:16px;height:16px;color:{{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        @elseif ($notif['icon'] === 'transaction')
                            <svg style="width:16px;height:16px;color:{{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4 4 4m6 0v12m0 0 4-4m-4 4-4-4"/></svg>
                        @else
                            <svg style="width:16px;height:16px;color:{{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">
                                {{ $notif['title'] }}
                                @if (! $notif['read'])
                                    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--emerald-600);margin-left:6px;vertical-align:middle;"></span>
                                @endif
                            </p>
                            @if ($notif['time'])
                                <span style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);white-space:nowrap;flex-shrink:0;">{{ $notif['time']->format('d M Y') }}</span>
                            @endif
                        </div>
                        <p style="font-size:12px;color:var(--ink-600);margin:4px 0 0;line-height:1.5;">{{ $notif['body'] }}</p>
                        @if (isset($notif['link']))
                            <a href="{{ $notif['link'] }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:var(--emerald-700);text-decoration:none;margin-top:8px;">
                                View details
                                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    @endif

</x-client.layouts.app>
