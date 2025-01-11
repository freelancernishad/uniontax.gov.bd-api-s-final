<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\EnglishSonod;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

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
    public function sonodDownload(Request $request, $id)
    {


        ini_set('max_execution_time', '60000');
        ini_set("pcre.backtrack_limit", "50000000000000000");
        ini_set('memory_limit', '12008M');

        $en = $request->en;





        if($en){
            $sonod = Sonod::select('id','sonod_Id','stutus','sonod_name','unioun_name')->with('english_sonod')->findOrFail($id);
            $row = $sonod->english_sonod;
            $sonod_Id = $sonod->sonod_Id;
            $stutus = $sonod->stutus;
            $sonod_name = $sonod->sonod_name;
            $unioun_name = $sonod->unioun_name;
        }else{
            $row = Sonod::find($id);
            $sonod_Id = $row->sonod_Id;
            $stutus = $row->stutus;
            $sonod_name = $row->sonod_name;
            $unioun_name = $row->unioun_name;
        }







        if ($stutus == 'cancel') {
            return response("<h1 style='color:red;text-align:center'>সনদটি বাতিল করা হয়েছে!<h1>", 403);
        }

        if ($stutus != 'approved') {
            return response("<h1 style='color:red;text-align:center'>সনদটি এখনো অনুমোদন করা হয়নি !<h1>", 403);
        }


        $signatureFields = ['socib_signture', 'chaireman_sign','image'];
        // Handle signature fields
        foreach ($signatureFields as $field) {
            $row->$field = handleFileUrl($row->$field);
        }





        $uniouninfo = Uniouninfo::where('short_name_e', $unioun_name)->first();
        $sonodnames = Sonodnamelist::where('bnname', $sonod_name)->first();
        $filename = str_replace(" ", "_", $sonodnames->enname) . "-$row->sonod_Id.pdf";
        // Handle sonod_logo in $uniouninfo
        $uniouninfo->sonod_logo = handleFileUrl($uniouninfo->sonod_logo);

        // return $row;
        $htmlContent = $this->getHtmlContent($row, $sonod_name, $uniouninfo, $sonodnames,$sonod_Id,$en);

        if ($sonod_name == 'ওয়ারিশান সনদ' || $sonod_name == 'উত্তরাধিকারী সনদ') {
            $header = null;
            $footer = null;
            if ($row->format == 1) {
                $header = $this->pdfHeader($id, $filename);
                $footer = $this->pdfFooter($id, $filename);
            }
            generatePdf($htmlContent, $header, $footer, $filename);
        }else{
            generatePdf($htmlContent, $header=null, $footer=null, $filename);

        }




    }


    function sonodVerify(Request $request, $id) {
        $en = $request->query('en'); // Use query() to get the query parameter
    
        // Construct the base URL
        $url = url("/sonod/download/$id");
    
        // Append query parameter if 'en' exists
        if ($en) {
            $url .= "?en=$en";
        }
    
        // Redirect to the constructed URL
        return redirect($url);
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
    private function getHtmlContent($row, $sonod_name, $uniouninfo, $sonodnames,$sonod_Id,$en=false)
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


        $sonodFolder = 'BnSonod';
        if($en){
            $sonodFolder = 'EnSonod';

        }


        if ($sonod_name == 'ওয়ারিশান সনদ' || $sonod_name == 'উত্তরাধিকারী সনদ') {
            if ($row->format == 2) {
                if($en){
                    $main_sonod_id = $row->sonod->id;
                }else{
                    $main_sonod_id = $row->id;

                }
                return view("SonodsPdf.$sonodFolder.wayarisan-uttoradhikari-sonod-format2", compact('row', 'uniouninfo', 'sonodnames','sonod_name_size','sonod_Id','main_sonod_id'))->render();


            }
            return view("SonodsPdf.$sonodFolder.wayarisan-uttoradhikari-sonod", compact('row', 'uniouninfo', 'sonodnames','sonod_name_size','sonod_Id'))->render();
            // return $this->pdfHTMLut($row->id, "$sonod_name.pdf");
        }

        if ($sonod_name == 'ট্রেড লাইসেন্স' && $row->format == 2) {
            return view("SonodsPdf.$sonodFolder.sonod-tradelicense-format2", compact('row', 'uniouninfo', 'sonodnames','sonod_name_size','sonod_Id'))->render();
        }

        return view("SonodsPdf.$sonodFolder.sonod", compact('row', 'uniouninfo', 'sonodnames','sonod_name_size','sonod_Id'))->render();


    }

    /**
     * Generate PDF header.
     *
     * @param int $id
     * @param string $filename
     * @return string
     */
    public function pdfHeader($id, $filename)
    {
        // Fetch required data
        $row = Sonod::find($id);
        $sonod = Sonodnamelist::where('bnname', $row->sonod_name)->first();
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        // Generate QR code URL
        $qrurl = url("/verification/sonod/{$row->id}?sonod_name={$sonod->enname}&sonod_Id={$row->sonod_Id}");

        // Prepare the HTML output
        $output = '
            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;" width="20%">
                        <div class="signature text-center position-relative">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data='.$qrurl.'&size=70x70" /><br/>
                            <div class="signature text-center position-relative">
                                সনদ নং: ' . int_en_to_bn($row->sonod_Id) . ' <br />
                                ইস্যুর তারিখ: '. int_en_to_bn(date("d/m/Y", strtotime($row->created_at))) .'
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="70px" src="' . base64('backend/bd-logo.png') . '">
                    </td>
                    <td style="text-align: center;" width="20%"></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:20px">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:0px;margin-bottom:0px;">
                    <td></td>
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;" width="50%">
                        <h1 style="color: #7230A0; margin: 0px; font-size: 28px">' . $uniouninfo->full_name . '</h1>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:20px">উপজেলা: ' . $uniouninfo->thana . ', জেলা: ' . $uniouninfo->district . ' ।</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:12px">ওয়েবসাইটঃ </p>
                        <p style="font-size:12px">ইমেলঃ ' . $row->c_email . '</p>
                    </td>
                    <td></td>
                </tr>
            </table>

            <div class="nagorik_sonod" style="margin-bottom:10px;">
                <div style="
                    background-color: #159513;
                    color: #fff;
                    font-size: 30px;
                    border-radius: 30em;
                    width:320px;
                    margin:10px auto;
                    margin-bottom:0px;
                    text-align:center
                ">
                    ' . changeSonodName($row->sonod_name) . '
                </div>
            </div>';

        return $output;
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
        // Fetch data for the row and union info
        $row = Sonod::find($id);



        $signatureFields = ['socib_signture', 'chaireman_sign','image'];
        // Handle signature fields
        foreach ($signatureFields as $field) {
            $row->$field = handleFileUrl($row->$field);
        }

        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        $uniouninfo->sonod_logo = handleFileUrl($uniouninfo->sonod_logo);


        // Prepare the HTML output
        $output = '
            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center; vertical-align: bottom;" width="40%">
                        <div class="signature text-center position-relative" style="color:black">
                            <br/>
                            <b><span style="color:#7230A0; font-size:18px;"></span></b>
                            <span style="font-size:16px;">ইউপি সদস্য/সদস্যা</span><br />
                            ' . $uniouninfo->full_name . '<br>
                            ' . $uniouninfo->thana . ' , ' . $uniouninfo->district . ' ।
                        </div>
                    </td>
                    <td style="text-align: center; width: 200px;" width="30%">
                        <img width="100px" src="' . $uniouninfo->sonod_logo . '">
                    </td>
                    <td style="text-align: center;" width="40%">
                        <div class="signature text-center position-relative" style="color:black">
                            <img width="170px" src="' . $row->chaireman_sign . '"><br/>
                            <b><span style="color:#7230A0; font-size:18px;">' . $row->chaireman_name . '</span></b><br />
                            <span style="font-size:16px;">' . $row->chaireman_type . '</span><br />
                            ' . $uniouninfo->full_name . '<br>
                            ' . $uniouninfo->thana . ', ' . $uniouninfo->district . ' ।
                        </div>
                    </td>
                </tr>
            </table>

            <p style="background: #787878; color: white; text-align: center; padding: 2px; font-size: 16px; margin-top: 0;" class="m-0">
                "সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূক কাজে সহায়তা করুন"
            </p>
            <p class="m-0" style="font-size:14px; text-align:center">
                ইস্যুকৃত সনদটি যাচাই করতে QR কোড স্ক্যান করুন অথবা ভিজিট করুন ' . $uniouninfo->domain . '
            </p>
        ';

        return $output;
    }

}
