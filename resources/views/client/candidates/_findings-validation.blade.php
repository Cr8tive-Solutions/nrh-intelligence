{{-- Validation matrix shared by employment + academic structured findings.
     Expects: $rows = array of { aspect, provided?, verified?, match?, risk?, interpretation? }
     Shapes are written by the admin portal's findings editor (nrh-admin
     RequestQueueController::normaliseStructuredFindings). --}}
@php
    $rows = collect($rows ?? [])->filter(fn ($r) => is_array($r))->values();
    $hasProvided = $rows->contains(fn ($r) => array_key_exists('provided', $r) && $r['provided'] !== null && $r['provided'] !== '');

    $matchChip = function ($m) {
        return match (strtolower((string) $m)) {
            'match'       => ['Match', 'var(--emerald-700)', 'var(--emerald-50)'],
            'partial'     => ['Partial', 'var(--gold-700, #b8860b)', 'rgba(184,147,31,0.12)'],
            'no_record'   => ['No record', 'var(--danger)', 'rgba(196,69,58,0.08)'],
            'discrepancy' => ['Discrepancy', 'var(--danger)', 'rgba(196,69,58,0.08)'],
            default       => [$m !== '' && $m !== null ? ucfirst((string) $m) : '—', 'var(--ink-500)', 'var(--paper)'],
        };
    };
    $riskChip = function ($r) {
        return match (strtolower((string) $r)) {
            'low'                => ['Low', 'var(--emerald-700)', 'var(--emerald-50)'],
            'moderate', 'medium' => ['Moderate', 'var(--gold-700, #b8860b)', 'rgba(184,147,31,0.12)'],
            'high'               => ['High', 'var(--danger)', 'rgba(196,69,58,0.08)'],
            'critical'           => ['Critical', '#fff', 'var(--danger)'],
            default              => [$r !== '' && $r !== null ? ucfirst((string) $r) : '—', 'var(--ink-500)', 'var(--paper)'],
        };
    };
    $chipStyle = fn ($fg, $bg) => "display:inline-block;padding:1px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:{$fg};background:{$bg};";
@endphp
@if ($rows->isNotEmpty())
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:11px;margin-top:4px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 8px 4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Aspect</th>
                    @if ($hasProvided)
                        <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 8px 4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Provided</th>
                    @endif
                    <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 8px 4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Verified</th>
                    <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 8px 4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Result</th>
                    <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 8px 4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Risk</th>
                    <th style="text-align:left;font-weight:600;color:var(--ink-500);padding:4px 0;border-bottom:1px solid var(--line);text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">Interpretation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        [$mTxt, $mFg, $mBg] = $matchChip($row['match'] ?? null);
                        [$rTxt, $rFg, $rBg] = $riskChip($row['risk'] ?? null);
                    @endphp
                    <tr>
                        <td style="padding:5px 8px 5px 0;color:var(--ink-900);font-weight:600;border-bottom:1px solid var(--line);white-space:nowrap;">{{ $row['aspect'] ?? '—' }}</td>
                        @if ($hasProvided)
                            <td style="padding:5px 8px 5px 0;color:var(--ink-700);border-bottom:1px solid var(--line);">{{ is_scalar($row['provided'] ?? null) ? ($row['provided'] ?: '—') : '—' }}</td>
                        @endif
                        <td style="padding:5px 8px 5px 0;color:var(--ink-700);border-bottom:1px solid var(--line);">{{ is_scalar($row['verified'] ?? null) ? ($row['verified'] ?: '—') : '—' }}</td>
                        <td style="padding:5px 8px 5px 0;border-bottom:1px solid var(--line);"><span style="{{ $chipStyle($mFg, $mBg) }}">{{ $mTxt }}</span></td>
                        <td style="padding:5px 8px 5px 0;border-bottom:1px solid var(--line);"><span style="{{ $chipStyle($rFg, $rBg) }}">{{ $rTxt }}</span></td>
                        <td style="padding:5px 0;color:var(--ink-500);border-bottom:1px solid var(--line);">{{ is_scalar($row['interpretation'] ?? null) ? $row['interpretation'] : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
