{{--
    PDF rendering of the customer portal guide. Same content and same block
    partial as docs.guide-web — only the styling differs.

    dompdf notes:
      · No flexbox/grid and no box-shadow. Tables do the columnar work.
      · Fonts are dompdf's bundled DejaVu family, so ✓ · → § render without
        registering anything on the server.
      · Page numbers are drawn by the controller with page_text(), because
        dompdf's counter(pages) resolves to 0 during layout.
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>NRH Intelligence — Customer Portal Guide</title>
<style>
@page { margin: 76px 46px 56px 46px; }
* { box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5pt; color: #3b4b44; line-height: 1.55; }

/* ── Persistent header / footer ── */
.ph { position: fixed; top: -62px; left: 0; right: 0; height: 52px; border-bottom: 1.6pt solid #046c4e; }
.ph table { width: 100%; height: 49px; border-collapse: collapse; }
.ph td { border: none; padding: 0; vertical-align: top; }
.ph .ph-t { font-family: 'DejaVu Serif', serif; font-size: 9.5pt; color: #023527; letter-spacing: 0.04em; }
.ph .ph-t em { color: #b8931f; font-style: italic; }
.ph .ph-s { font-size: 7pt; color: #8a988f; letter-spacing: 0.16em; text-transform: uppercase; margin-top: 2px; }
.ph .ph-r { text-align: right; font-size: 7pt; color: #8a988f; letter-spacing: 0.16em; text-transform: uppercase; }

.pf { position: fixed; bottom: -40px; left: 0; right: 0; height: 24px;
      border-top: 0.6pt solid #e6e4d8; padding-top: 4px;
      font-size: 7pt; color: #8a988f; letter-spacing: 0.04em; }

/* ── Cover ── */
.cov { text-align: center; padding-top: 118px; }
.cov-mark { width: 52pt; margin: 0 auto; background-color: #023527; color: #d4af37;
            font-family: 'DejaVu Serif', serif; font-size: 17pt; line-height: 1;
            text-align: center; padding: 15pt 0 17pt 0; }
.cov-title { font-family: 'DejaVu Serif', serif; font-size: 21pt; color: #14201b;
             margin: 26px 0 5px; }
.cov-title em { color: #046c4e; font-style: italic; }
.cov-sub { font-size: 8pt; color: #8a988f; letter-spacing: 0.24em; text-transform: uppercase; margin-bottom: 26px; }
.cov-rule { border-top: 2.5pt solid #d4af37; width: 140px; margin: 0 auto 26px; }
.cov-meta { font-size: 8pt; color: #6b7a74; letter-spacing: 0.05em; line-height: 2; }
.cov-note { margin: 42px auto 0; width: 80%; border: 0.8pt solid #046c4e; background: #ecfdf5;
            padding: 10px 15px; font-size: 7.5pt; line-height: 1.6; text-align: left; color: #14201b; }
.cov-note b { color: #046c4e; }

/* ── Contents ── */
.toc-h { background: #023527; color: #ffffff; padding: 5px 12px 8px; font-weight: bold;
         font-size: 9.5pt; letter-spacing: 0.09em; margin: 0 0 10px; }
table.toc { width: 100%; border-collapse: collapse; }
table.toc td { border: none; padding: 5px 0; vertical-align: top; border-bottom: 0.5pt dotted #d9dfd9; }
table.toc td.toc-n { width: 30px; font-family: 'DejaVu Sans Mono', monospace; font-weight: bold;
                     color: #b8931f; font-size: 8.5pt; }
table.toc td.toc-t a { color: #14201b; text-decoration: none; font-weight: bold; font-size: 9pt; }
table.toc .toc-s { display: block; font-size: 7.5pt; color: #8a988f; font-weight: normal; margin-top: 1px; }

/* ── Section headers ── */
table.sec-bar { width: 100%; border-collapse: collapse; margin: 0 0 7px;
                page-break-inside: avoid; page-break-after: avoid; }
table.sec-bar td { background: #023527; color: #ffffff; padding: 6px 12px 9px; font-weight: bold;
                   font-size: 9.5pt; letter-spacing: 0.07em; vertical-align: middle; border: none; }
table.sec-bar td.sec-n { width: 34px; background: #d4af37; color: #14201b; text-align: center;
                         font-family: 'DejaVu Sans Mono', monospace; }
.sec-perm { background: #f5ecd1; color: #14201b; padding: 4px 11px 6px; font-size: 7.5pt;
            font-family: 'DejaVu Sans Mono', monospace; letter-spacing: 0.03em; margin: 0 0 9px; }
.sec-perm b { color: #9c7a1e; }
.sec-sum { font-size: 8pt; color: #8a988f; margin: 0 0 10px; }

/* ── Body blocks (class names shared with docs._blocks) ── */
.g-p { margin: 0 0 8px; text-align: justify; }
.g-sub { font-weight: bold; font-size: 8.5pt; color: #046c4e; letter-spacing: 0.05em;
         text-transform: uppercase; margin: 14px 0 6px; border-bottom: 0.8pt solid #d4af37;
         padding-bottom: 3px; page-break-after: avoid; }

table.g-steps, table.g-list { width: 100%; border-collapse: collapse; margin: 0 0 9px; }
table.g-steps td, table.g-list td { border: none; padding: 2px 0 5px; vertical-align: top; }
td.g-step-n { width: 20px; font-family: 'DejaVu Sans Mono', monospace; font-weight: bold;
              color: #046c4e; font-size: 8.5pt; }
td.g-bullet { width: 14px; color: #d4af37; font-weight: bold; }
td.g-step-t, td.g-list-t { text-align: justify; }

/* Break avoidance is per row, not per table: the reference and troubleshooting
   tables are taller than a page, and avoiding a break on the whole table would
   push it off the bottom and clip it. */
table.g-table { width: 100%; border-collapse: collapse; margin: 0 0 11px; }
table.g-table tr { page-break-inside: avoid; }
table.g-table th { background: #046c4e; color: #ffffff; font-weight: bold; letter-spacing: 0.04em;
                   text-align: left; border: 0.5pt solid #14201b; padding: 5px 8px 7px; font-size: 8pt; }
table.g-table td { border: 0.5pt solid #b8c2bb; padding: 5px 8px 7px; vertical-align: top; line-height: 1.4; }

table.g-call { width: 100%; border-collapse: collapse; margin: 9px 0 11px; page-break-inside: avoid; }
table.g-call td { padding: 8px 11px 10px; font-size: 8pt; line-height: 1.55; }
table.g-note td { border: 0.8pt solid #046c4e; background: #ecfdf5; }
table.g-warn td { border: 0.8pt solid #d4af37; background: #fdf6e3; }
.g-call-t { font-weight: bold; font-size: 7.5pt; letter-spacing: 0.06em; text-transform: uppercase; }
table.g-note .g-call-t { color: #046c4e; }
table.g-warn .g-call-t { color: #9c7a1e; }
.g-call-b { display: block; margin-top: 3px; text-align: justify; }

table.g-links { width: 100%; border-collapse: collapse; margin: 9px 0 12px; page-break-inside: avoid;
                border-top: 0.5pt dashed #d9dfd9; }
table.g-links td { border: none; padding: 6px 0 2px; vertical-align: middle; font-size: 8pt; }
td.g-links-lbl { width: 80px; font-size: 7pt; color: #8a988f; letter-spacing: 0.12em; text-transform: uppercase; }
a.g-ext { color: #046c4e; text-decoration: none; font-weight: bold; }
.g-sep { color: #d9dfd9; }

code { font-family: 'DejaVu Sans Mono', monospace; background: #f3f4ee;
       border: 0.5pt solid #e6e4d8; padding: 0 3px; font-size: 7.5pt; color: #14201b; }
.sec { page-break-inside: auto; }
</style>
</head>
<body>

{{-- Running header / footer, repeated on every page --}}
<div class="ph">
    <table>
        <tr>
            <td>
                <div class="ph-t">NRH <em>Intelligence</em></div>
                <div class="ph-s">Customer Portal Guide</div>
            </td>
            <td class="ph-r">{{ $portalHost }}</td>
        </tr>
    </table>
</div>
<div class="pf">Generated {{ $generatedAt }} &nbsp;·&nbsp; The portal is authoritative where this guide and it disagree.</div>

{{-- ─────────────── Cover ─────────────── --}}
<div class="cov">
    <div class="cov-mark">NRH</div>
    <div class="cov-title">Customer Portal <em>Guide</em></div>
    <div class="cov-sub">NRH Intelligence &mdash; Screening &middot; Est. 2019</div>
    <div class="cov-rule"></div>
    <div class="cov-meta">
        {{ $sectionCount }} SECTIONS<br>
        GENERATED {{ strtoupper($generatedAt) }}<br>
        {{ strtoupper($portalHost) }}
    </div>
    <div class="cov-note">
        <b>This document is interactive.</b> Every entry in the contents is a live link, and so is
        every “In the portal” link at the end of a section — those open the matching page in your
        browser, so you will need to be signed in for them to land. The same guide is available on
        screen at <b>System &rarr; User Guide</b>, where it is always the current edition.
    </div>
</div>

{{-- ─────────────── Contents ─────────────── --}}
<div style="page-break-before:always;"></div>
<div class="toc-h">CONTENTS</div>
<table class="toc">
    @foreach($sections as $i => $s)
    <tr>
        <td class="toc-n">{{ $i + 1 }}</td>
        <td class="toc-t">
            <a href="#{{ $s['id'] }}">{!! $s['title'] !!}</a>
            <span class="toc-s">{{ $s['summary'] }}</span>
        </td>
    </tr>
    @endforeach
</table>

{{-- ─────────────── Sections ─────────────── --}}
@foreach($sections as $i => $s)
{{-- The id must sit on a block that actually renders. dompdf registers a named
     destination from AbstractRenderer::addNamedDest(), which only runs for frames
     that reach a renderer — an empty <a name> anchor produces no frame, and the
     contents-page links then emit an empty action (a dead link). --}}
<div class="sec" id="{{ $s['id'] }}" style="page-break-before:always;">
    <table class="sec-bar">
        <tr>
            <td class="sec-n">{{ $i + 1 }}</td>
            <td>{{ strtoupper(html_entity_decode(strip_tags($s['title']))) }}</td>
        </tr>
    </table>
    @if($s['permission'])
    <div class="sec-perm"><b>REQUIRES:</b> {{ $s['permission'] }}</div>
    @endif
    <div class="sec-sum">{{ $s['summary'] }}</div>

    @include('docs._blocks', ['blocks' => $s['blocks']])
</div>
@endforeach

</body>
</html>
