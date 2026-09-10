<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\PortalGuide;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * The customer-facing user guide for this portal.
 *
 * Content lives in App\Support\PortalGuide; this controller only renders it,
 * on screen or as a PDF. Mirrors the admin portal's GuideController.
 *
 * Available to every authenticated user — the guide documents permission-gated
 * pages, but reading about them is not gated.
 */
class GuideController extends Controller
{
    public function show(): View
    {
        return view('docs.guide-web', [
            'sections' => PortalGuide::sections(),
        ]);
    }

    public function download(): Response
    {
        $sections = PortalGuide::sections();

        // dompdf's bundled DejaVu family is used rather than the portal's web
        // fonts: it covers ✓ · → § out of the box and needs nothing registered
        // on the server, so the PDF renders identically on every environment.
        $pdf = Pdf::loadView('docs.guide-pdf', [
            'sections' => $sections,
            'sectionCount' => count($sections),
            'generatedAt' => now()->format('d F Y'),
            'portalHost' => preg_replace('#^https?://#', '', rtrim((string) config('app.url'), '/')),
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Page numbers. dompdf's counter(pages) resolves to 0 during layout, so
        // the total is only knowable here, after render.
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            $canvas->get_width() - 120,
            $canvas->get_height() - 30,
            'Page {PAGE_NUM} of {PAGE_COUNT}',
            $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal'),
            7,
            [0.54, 0.6, 0.56],
        );

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="NRH-Customer-Portal-Guide.pdf"',
        ]);
    }
}
