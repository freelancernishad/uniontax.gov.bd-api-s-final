<?php
use Mpdf\Mpdf;
use Mccarlosen\LaravelMpdf\LaravelMpdf;
 function generatePdf($html, $header = null, $footer = null, $filename = 'document.pdf',$page_format='A4',$font_familly='bangla')
{
    $margin_header = 0;
    $margin_footer = 0;
    if($header){
        $margin_header = 10;
    }
    if($footer){
        $margin_footer = 10;
    }

    $mpdf = new Mpdf([
        'default_font_size' => 12,
        'default_font' => "$font_familly",
        'mode' => 'utf-8',
        'format' => $page_format,
        'setAutoTopMargin' => 'stretch',
        'setAutoBottomMargin' => 'stretch',
        'margin_left'               => 8,
        'margin_right'               => 8,
        'margin_top'                 => 8,
        'margin_bottom'              => 8,
        'margin_header'              => $margin_header,
        'margin_footer'              => $margin_footer,
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

// Enable remote file access (to load images from external URLs)
// $mpdf->useActiveForms = true;
// $mpdf->options['isHtml5ParserEnabled'] = true;
// $mpdf->SetOption('isRemoteEnabled', true);


    $mpdf->showImageErrors = true;


    // Stream the PDF to the browser
    $mpdf->Output($filename, 'I');
}
