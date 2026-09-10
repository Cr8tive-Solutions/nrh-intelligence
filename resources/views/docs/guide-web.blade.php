{{--
    On-screen customer portal guide. Content comes from App\Support\PortalGuide
    and renders through docs._blocks, the same partial the PDF uses.
--}}
<x-client.layouts.app pageTitle="User Guide">

<div class="page-head">
    <div>
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:6px;">Help</div>
        <h1>User <em>Guide</em></h1>
        <div class="sub">Every page in the portal, the permission it needs, and what each action does.</div>
    </div>
    <a href="{{ route('client.guide.download') }}" class="gw-dl">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="15" height="15"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
        Download PDF
    </a>
</div>

<style>
.gw-dl { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:var(--radius);
         background:var(--emerald-700); color:#fff; font-size:12.5px; font-weight:600;
         text-decoration:none; border:1px solid var(--emerald-700); flex-shrink:0; }
.gw-dl:hover { background:var(--emerald-800); }

.gw-wrap { display:grid; grid-template-columns:242px minmax(0,1fr); gap:26px; align-items:start; }
@media (max-width: 1100px) { .gw-wrap { grid-template-columns:1fr; } .gw-toc { position:static !important; max-height:none !important; } }

/* ── Contents rail ── */
.gw-toc { position:sticky; top:16px; background:var(--card); border:1px solid var(--line);
          border-radius:var(--radius-lg); padding:14px 12px; max-height:calc(100vh - 120px); overflow-y:auto; }
.gw-toc-h { font-size:10px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase;
            color:var(--ink-500); padding:0 6px 9px; }
.gw-toc a { display:flex; gap:9px; padding:6px 7px; border-radius:var(--radius); text-decoration:none;
            font-size:12.5px; line-height:1.35; color:var(--ink-700); }
.gw-toc a:hover { background:var(--ink-50); color:var(--ink-900); }
.gw-toc a.on { background:var(--emerald-50); color:var(--emerald-700); font-weight:600; }
[data-theme="dark"] .gw-toc a.on { background:var(--ink-100); color:var(--emerald-500); }
.gw-toc-n { font-family:var(--font-mono); color:var(--gold-600); font-weight:700; min-width:16px; }

/* ── Sections ── */
.gw-sec { background:var(--card); border:1px solid var(--line); border-radius:var(--radius-lg);
          padding:22px 24px 18px; margin-bottom:18px; scroll-margin-top:80px; }
.gw-sec-h { display:flex; align-items:baseline; gap:11px; margin:0 0 4px; }
.gw-sec-n { font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--gold-600); }
.gw-sec-t { font-family:var(--font-display); font-size:20px; color:var(--ink-900); margin:0; font-weight:600; }
.gw-sec-s { font-size:12.5px; color:var(--ink-500); margin:0 0 12px; }
.gw-perm { display:inline-block; font-size:11px; font-family:var(--font-mono);
           background:var(--gold-100); color:var(--gold-700); border:1px solid var(--gold-400);
           border-radius:999px; padding:2px 11px; margin:0 0 14px; }
[data-theme="dark"] .gw-perm { background:transparent; }

/* ── Shared block styles (mirror docs/_blocks.blade.php) ── */
.gw-sec .g-p { font-size:13px; line-height:1.7; color:var(--ink-700); margin:0 0 11px; }
.gw-sec .g-sub { font-size:12.5px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase;
                 color:var(--emerald-700); margin:22px 0 9px; padding-bottom:5px;
                 border-bottom:1px solid var(--line); }
[data-theme="dark"] .gw-sec .g-sub { color:var(--emerald-500); }

.gw-sec table.g-steps, .gw-sec table.g-list { width:100%; border-collapse:collapse; margin:0 0 12px; }
.gw-sec table.g-steps td, .gw-sec table.g-list td { padding:4px 0 7px; vertical-align:top;
                 font-size:13px; line-height:1.65; color:var(--ink-700); }
.gw-sec td.g-step-n { width:28px; font-family:var(--font-mono); font-size:11.5px; font-weight:700;
                 color:var(--emerald-700); }
[data-theme="dark"] .gw-sec td.g-step-n { color:var(--emerald-500); }
.gw-sec td.g-bullet { width:18px; color:var(--gold-600); font-weight:700; }

.gw-sec table.g-table { width:100%; border-collapse:collapse; margin:0 0 14px; font-size:12.5px; }
.gw-sec table.g-table th { background:var(--ink-50); color:var(--ink-900); text-align:left;
                 font-size:10.5px; letter-spacing:0.06em; text-transform:uppercase; font-weight:700;
                 padding:8px 11px; border:1px solid var(--line); }
.gw-sec table.g-table td { padding:8px 11px; border:1px solid var(--line); vertical-align:top;
                 color:var(--ink-700); line-height:1.55; }

.gw-sec table.g-call { width:100%; border-collapse:collapse; margin:14px 0; }
.gw-sec table.g-call td { padding:12px 14px; font-size:12.5px; line-height:1.6; border-radius:var(--radius); }
.gw-sec table.g-note td { background:var(--emerald-50); border:1px solid rgba(5,150,105,0.22); color:var(--ink-700); }
[data-theme="dark"] .gw-sec table.g-note td { background:var(--ink-100); border-color:var(--ink-300); }
.gw-sec table.g-warn td { background:#fdf6e3; border:1px solid rgba(212,175,55,0.4); color:var(--ink-700); }
[data-theme="dark"] .gw-sec table.g-warn td { background:rgba(212,175,55,0.08); border-color:rgba(212,175,55,0.3); }
.gw-sec .g-call-t { font-size:10.5px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; }
.gw-sec table.g-note .g-call-t { color:var(--emerald-700); }
[data-theme="dark"] .gw-sec table.g-note .g-call-t { color:var(--emerald-500); }
.gw-sec table.g-warn .g-call-t { color:var(--gold-700); }
[data-theme="dark"] .gw-sec table.g-warn .g-call-t { color:var(--gold-500); }
.gw-sec .g-call-b { display:block; margin-top:3px; }

.gw-sec table.g-links { width:100%; border-collapse:collapse; margin:15px 0 2px;
                 border-top:1px dashed var(--line); }
.gw-sec table.g-links td { padding:11px 0 2px; font-size:12.5px; vertical-align:middle; }
.gw-sec td.g-links-lbl { width:92px; font-size:10px; letter-spacing:0.12em; text-transform:uppercase;
                 color:var(--ink-400); }
.gw-sec a.g-ext { color:var(--emerald-700); font-weight:600; text-decoration:none; }
.gw-sec a.g-ext:hover { text-decoration:underline; }
[data-theme="dark"] .gw-sec a.g-ext { color:var(--emerald-500); }
.gw-sec .g-sep { color:var(--ink-300); }

.gw-sec code { font-family:var(--font-mono); font-size:11.5px; background:var(--ink-50);
               border:1px solid var(--line); border-radius:4px; padding:1px 5px; color:var(--ink-900); }
</style>

<div class="gw-wrap" x-data="guideToc()" x-init="init()">

    {{-- Contents rail --}}
    <nav class="gw-toc">
        <div class="gw-toc-h">Contents</div>
        @foreach($sections as $i => $s)
        <a href="#{{ $s['id'] }}" :class="active === '{{ $s['id'] }}' ? 'on' : ''">
            <span class="gw-toc-n">{{ $i + 1 }}</span>
            <span>{!! $s['title'] !!}</span>
        </a>
        @endforeach
    </nav>

    <div>
        @foreach($sections as $i => $s)
        <section class="gw-sec" id="{{ $s['id'] }}">
            <div class="gw-sec-h">
                <span class="gw-sec-n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="gw-sec-t">{!! $s['title'] !!}</h3>
            </div>
            <p class="gw-sec-s">{{ $s['summary'] }}</p>
            @if($s['permission'])
            <div class="gw-perm">Requires: {{ $s['permission'] }}</div>
            @endif

            @include('docs._blocks', ['blocks' => $s['blocks']])
        </section>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
// Highlights the contents entry for whichever section is currently in view.
function guideToc() {
    return {
        active: @js($sections[0]['id'] ?? ''),
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) this.active = e.target.id;
                });
            }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });

            document.querySelectorAll('.gw-sec[id]').forEach((el) => observer.observe(el));
        },
    };
}
</script>
@endpush

</x-client.layouts.app>
