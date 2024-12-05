<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use App\Http\Controllers\Controller;
use App\Models\Sonod;
use App\Models\Sonodnamelist;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;

class SonodPdfController extends Controller
{


    /**
     * Generate PDF for Sonod.
     *
     * @param Request $request
     * @param string $name
     * @param int $id
     * @return \Illuminate\Http\Response|string
     */
    public function sonodDownload(Request $request, $name, $id)
    {
        ini_set('max_execution_time', '60000');
        ini_set("pcre.backtrack_limit", "50000000000000000");
        ini_set('memory_limit', '12008M');

        $row = Sonod::findOrFail($id);

        if ($row->stutus == 'cancel') {
            return response("<h1 style='color:red;text-align:center'>সনদটি বাতিল করা হয়েছে!<h1>", 403);
        }

        if ($row->stutus != 'approved') {
            return response("<h1 style='color:red;text-align:center'>সনদটি এখনো অনুমোদন করা হয়নি !<h1>", 403);
        }

        $sonod_name = $row->sonod_name;
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        $sonodnames = Sonodnamelist::where('bnname', $sonod_name)->first();
        $filename = str_replace(" ", "_", $sonodnames->enname) . "-$row->sonod_Id.pdf";

        $htmlContent = $this->getHtmlContent($row, $sonod_name, $uniouninfo, $sonodnames);


        // $header = $this->pdfHeader($id, $filename);
        // $footer = $this->pdfFooter($id, $filename);

        generatePdf($htmlContent, $header=null, $footer=null, $filename);
    }

    /**
     * Get HTML content for the PDF.
     *
     * @param $row
     * @param $sonod_name
     * @param $uniouninfo
     * @param $sonodnames
     * @return string
     */
    private function getHtmlContent($row, $sonod_name, $uniouninfo, $sonodnames)
    {



        $namelength = strlen($row->sonod_name);
        $width = '300px';  // Default width
        $fontsize = '30px';  // Default font size
        if ($namelength >= 100) {
            $width = '400px';
            $fontsize = '20px';
        } elseif ($namelength >= 85) {
            $width = '500px';
            $fontsize = '22px';
        } elseif ($namelength >= 72) {
            $width = '450px';
            $fontsize = '25px';
        } elseif ($namelength >= 62) {
            $width = '400px';
            $fontsize = '27px';
        }
        $sonod_name_size = ['width'=>$width,'fontsize'=>$fontsize];



  

    




        if ($sonod_name == 'ওয়ারিশান সনদ' || $sonod_name == 'উত্তরাধিকারী সনদ') {
            if ($row->format == 2) {
                return view('Inheritance-certificate.sonod', compact('row', 'uniouninfo', 'sonodnames','sonod_name_size'))->render();
            }
            return $this->pdfHTMLut($row->id, "$sonod_name.pdf");
        }

        if ($sonod_name == 'ট্রেড লাইসেন্স' && $row->format == 2) {
            return view('SonodsPdf.sonod-tradelicense-format2', compact('row', 'uniouninfo', 'sonodnames','sonod_name_size'))->render();
        }

        return view('SonodsPdf.sonod', compact('row', 'uniouninfo', 'sonodnames','sonod_name_size'))->render();
    }

    /**
     * Generate PDF header.
     *
     * @param int $id
     * @param string $filename
     * @return string
     */
    private function pdfHeader($id, $filename)
    {
        return "<div style='text-align: center;'>Header Content for Sonod #$id</div>";
    }

    /**
     * Generate PDF footer.
     *
     * @param int $id
     * @param string $filename
     * @return string
     */
    private function pdfFooter($id, $filename)
    {
        return "<div style='text-align: center;'>Footer Content for $filename</div>";
    }
}
