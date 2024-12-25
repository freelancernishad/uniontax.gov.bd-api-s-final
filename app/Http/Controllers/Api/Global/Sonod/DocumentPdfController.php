<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use App\Http\Controllers\Controller;
use App\Models\Sonod;
use App\Models\Sonodnamelist;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;

class DocumentPdfController extends Controller
{
    public function userDocument(Request $request, $id)
    {
        // Set memory and execution limits
        ini_set('max_execution_time', '60000');
        ini_set("pcre.backtrack_limit", "50000000000000000");
        ini_set('memory_limit', '12008M');

        // Fetch necessary data
        $row = Sonod::find($id);

        // Check if the Sonod record exists
        if (!$row) {
            return response()->json([
                'error' => 'সনদটি পাওয়া যায়নি!',
            ], 404);
        }

        // Define allowed statuses
        $allowedStatuses = ['Pending', 'sec_approved', 'approved'];

        // Check if the current status is allowed
        if (!in_array($row->status, $allowedStatuses)) {
            return response()->json([
                'error' => 'এই সনদটি প্রক্রিয়া করা যাবে না!',
            ], 403);
        }

        $sonod = Sonodnamelist::where('bnname', $row->sonod_name)->first();
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        $sonodnames = Sonodnamelist::where('bnname', $row->sonod_name)->first();

        if (!$sonodnames) {
            return response("<h1 style='color:red;text-align:center'>সনদটির তথ্য মেলে না!<h1>", 404);
        }

        $EnsonodName = str_replace(" ", "_", $sonodnames->enname);
        $filename = "$EnsonodName-$row->sonod_Id.pdf";

        // Determine the HTML content based on the certificate type
        $htmlView = '';
        if (in_array($EnsonodName, ['Certificate_of_Inheritance', 'Inheritance_certificate'])) {

            $header = $this->pdfHeader($id);
            $footer = $this->pdfFooter($id);



            $htmlView = view('ApplicationPdf.ApplicationCopyWayaris', compact('row', 'sonod', 'uniouninfo'))->render();
        // } elseif (in_array($EnsonodName, ['Miscellaneous_certificates', 'Certification_of_the_same_name'])) {
            // $htmlView = view('ApplicationPdf.ApplicationCopyFromat2', compact('row', 'sonod', 'uniouninfo'))->render();
        }
         else {

            $header = null;
            $footer = null;
            $htmlView = view('ApplicationPdf.ApplicationCopyFromat1', compact('row', 'sonod', 'uniouninfo'))->render();
        }

        // Generate the PDF with the appropriate header and footer
        generatePdf($htmlView, $header, $footer, $filename);
    }


    public function pdfHeader($id)
    {
        $row = Sonod::find($id);
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();

        $output = "










        <div class='pdfhead' style='text-align: center;'>
            <div style='text-align: center;'>
                <img width='70px' src='" . base64($uniouninfo->sonod_logo) . "' alt='Union Logo' />
            </div>


            <div style='width:300px;margin:0 auto;' ><p style='margin-bottom:0 !important;font-size:16px'>  গণপ্রজাতন্ত্রী বাংলাদেশ
                <h2 style='margin: 0;'> $uniouninfo->full_name </h2>
                উপজেলা:   $uniouninfo->thana , জেলা:   $uniouninfo->district  ।

            </p></div>





            <div style='margin-bottom: 0px !important;
                font-size: 35px;
                color: white;
                background: green;
                padding: 13px 39px;
                border-radius: 14px;
                width: 200px;
                margin: 0 auto;'>
                অভিনন্দন!
            </div>
            <br>
            <p style='font-size: 16px; color: blue; margin-bottom: 0px !important;'>
                ডিজিটাল ইউনিয়ন ট্যাক্স ও সেবা সিস্টেমে আপনার আবেদনটি যথাযথভাবে দাখিল হয়েছে।
            </p>
        </div>
        ";

        return $output;
    }




    /**
     * Generate PDF footer.
     *
     * @param int $id
     * @param string $filename
     * @return string
     */
    private function pdfFooter($id)
    {

        $row = Sonod::find($id);
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        $output = "




    <table width='100%' style='border-collapse: collapse;margin-top:60px' border='0'>
        <tr>
            <td  style='text-align: center;' width='40%'>
                <div class='signature text-center position-relative'>
                    <b><span style='color:#7230A0;font-size:18px;'>সংশ্লিষ্ট ইউপি সদস্যের স্বাক্ষর ও সীল</span> <br />




                </div>
            </td>
            <td style='text-align: center; width: 200px;' width='30%'>

            </td>
            <td style='text-align: center;' width='40%'>
                <div class='signature text-center position-relative'>

                    <b><span style='color:#7230A0;font-size:18px;'> $row->chaireman_name</span> <br />
                            </b><span style='font-size:16px;'> $row->chaireman_type </span><br />

                    $uniouninfo->full_name <br>  $uniouninfo->thana , $uniouninfo->district  । <br>


                </div>
            </td>
        </tr>


    </table>



<p style='background: #787878;
    color: white;
    text-align: center;
    padding: 2px 2px;font-size: 16px;     margin-top: 20px;margin-bottom:0px' class='m-0'>'সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন'</p>

<p class='m-0' style='font-size:14px;text-align:center;margin: 0;'>'ডিজিটাল ইউনিয়ন ট্যাক্স ও সেবা সিস্টেম'  $uniouninfo->domain  এর সাথে থাকার জন্য ধন্যবাদ</p>





    </div>

        ";


        return $output;


    }


}
