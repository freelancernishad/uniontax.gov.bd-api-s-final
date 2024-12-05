<?php
use Mpdf\Mpdf;
use Mccarlosen\LaravelMpdf\LaravelMpdf;
 function generatePdf($html, $header = null, $footer = null, $filename = 'document.pdf')
{
    $mpdf = new Mpdf([
        'default_font_size' => 12,
        'default_font' => 'bangla',
        'mode' => 'utf-8',
        'format' => 'A4',
        'setAutoTopMargin' => 'stretch',
        'setAutoBottomMargin' => 'stretch',
        'margin_left'               => 8,
        'margin_right'               => 8,
        'margin_top'                 => 8,
        'margin_bottom'              => 8,
        'margin_header'              => 0,
        'margin_footer'              => 0,
    ]);

    if ($header) {
        $mpdf->SetHTMLHeader($header);
    }

    if ($footer) {
        $mpdf->SetHTMLFooter($footer);
    }

    $mpdf->SetDisplayMode('fullpage');
    $mpdf->WriteHTML($html);
    $mpdf->useSubstitutions = false;
    $mpdf->simpleTables = true;

    // Stream the PDF to the browser
    $mpdf->Output($filename, 'I');
}
