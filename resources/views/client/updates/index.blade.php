<x-client.layouts.app pageTitle="System Updates">

    <div class="page-head">
        <div>
            <h1>System <em>Updates</em></h1>
            <div class="sub">Release notes and platform improvements</div>
        </div>
    </div>

    <div style="max-width:720px;">

        @if ($updates->isEmpty())
            <div class="card" style="padding:60px;text-align:center;">
                <p style="font-size:13px;color:var(--ink-400);margin:0;">No updates published yet.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:32px;">
                @foreach ($updates as $date => $group)
                    @php
                        $releaseDate = \Carbon\Carbon::parse($date);
                        $version = $group->first()->version;
                    @endphp

                    <div style="display:flex;gap:24px;">

                        {{-- Timeline spine --}}
                        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:40px;">
                            <div style="width:10px;height:10px;border-radius:50%;background:var(--emerald-700);border:2px solid var(--emerald-700);flex-shrink:0;margin-top:4px;"></div>
                            @if (! $loop->last)
                                <div style="width:1px;flex:1;background:var(--line);margin-top:6px;"></div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div style="flex:1;padding-bottom:8px;">

                            {{-- Date + version --}}
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                                <span style="font-size:13px;font-weight:700;color:var(--ink-900);">{{ $releaseDate->format('d M Y') }}</span>
                                @if ($version)
                                    <span style="font-family:var(--font-mono);font-size:11px;font-weight:600;padding:2px 8px;background:var(--emerald-50);color:var(--emerald-700);border-radius:999px;border:1px solid rgba(4,108,78,0.15);">{{ $version }}</span>
                                @endif
                                @if ($loop->first)
                                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;padding:2px 8px;background:var(--gold-100);color:var(--gold-700);border-radius:999px;">Latest</span>
                                @endif
                            </div>

                            {{-- Update items --}}
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                @foreach ($group as $update)
                                    @php
                                        $typeConfig = match($update->type) {
                                            'feature'     => ['bg' => 'var(--emerald-50)',              'color' => 'var(--emerald-700)', 'border' => 'rgba(4,108,78,0.15)',   'label' => 'New',         'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                                            'improvement' => ['bg' => 'rgba(58,107,143,0.06)',          'color' => 'var(--info)',        'border' => 'rgba(58,107,143,0.15)', 'label' => 'Improved',    'icon' => 'M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18'],
                                            'fix'         => ['bg' => 'rgba(184,147,31,0.06)',          'color' => 'var(--gold-700)',    'border' => 'rgba(184,147,31,0.15)', 'label' => 'Fixed',       'icon' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z'],
                                            'security'    => ['bg' => 'rgba(196,69,58,0.05)',           'color' => 'var(--danger)',      'border' => 'rgba(196,69,58,0.15)',  'label' => 'Security',    'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
                                            default       => ['bg' => 'var(--paper)',                   'color' => 'var(--ink-500)',     'border' => 'var(--line)',           'label' => 'Update',      'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z'],
                                        };
                                    @endphp

                                    <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:{{ $typeConfig['bg'] }};border:1px solid {{ $typeConfig['border'] }};border-radius:var(--radius);">
                                        {{-- Type icon --}}
                                        <div style="width:28px;height:28px;border-radius:var(--radius);background:{{ $typeConfig['bg'] }};border:1px solid {{ $typeConfig['border'] }};display:grid;place-items:center;flex-shrink:0;">
                                            <svg style="width:13px;height:13px;color:{{ $typeConfig['color'] }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeConfig['icon'] }}"/>
                                            </svg>
                                        </div>

                                        <div style="flex:1;min-width:0;">
                                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $typeConfig['color'] }};">{{ $typeConfig['label'] }}</span>
                                                <span style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ $update->title }}</span>
                                            </div>
                                            @if ($update->body)
                                                <p style="font-size:12px;color:var(--ink-600);margin:0;line-height:1.55;">{{ $update->body }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-client.layouts.app>
