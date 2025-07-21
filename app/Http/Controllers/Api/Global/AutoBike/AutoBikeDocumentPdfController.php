<?php

namespace App\Http\Controllers\Api\Global\AutoBike;

use App\Models\AutoBikeRegistration;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AutoBikeDocumentPdfController extends Controller
{
    public function userDocument(Request $request, $id)
    {
        // Increase limits
        ini_set('max_execution_time', '600');
        ini_set('memory_limit', '512M');

        $is_union = isUnion();

        // Get the registration
        $row = AutoBikeRegistration::find($id);

        if (!$row) {
            return response()->json([
                'error' => 'আবেদনটি পাওয়া যায়নি!',
            ], 404);
        }

        $uniouninfo = Uniouninfo::where('short_name_e', $row->union_name)->first();

        if (!$uniouninfo) {
            return response("<h1 style='color:red;text-align:center'>ইউনিয়নের তথ্য মেলে না!<h1>", 404);
        }

        $filename = "AutoBikeRegistration-$row->application_id.pdf";

        $header = $this->pdfHeader($row);
        $footer = $this->pdfFooter($row);

        // Render the HTML view (you can design this blade as needed)
        $htmlView = view('ApplicationPdf.AutoBikeApplicationCopy', compact('row', 'uniouninfo', 'is_union'))->render();

        // Generate PDF using helper
        generatePdf($htmlView, $header, $footer, $filename);
    }

    public function pdfHeader($row)
    {
        $uniouninfo = Uniouninfo::where('short_name_e', $row->union_name)->first();
        $is_union = isUnion();

        $output = "
        <div class='pdfhead' style='text-align: center;'>

            <div style='width:300px;margin:0 auto;'>
           
                <h2 style='margin: 0;'> $uniouninfo->full_name </h2>
                উপজেলা: $uniouninfo->thana , জেলা: $uniouninfo->district ।
            </div>
            <div style='margin-bottom: 0px !important; font-size: 35px; color: white; background: green; padding: 13px 39px; border-radius: 14px; width: 200px; margin: 0 auto;'>
                অভিনন্দন!
            </div>
            <br>
            <p style='font-size: 16px; color: blue; margin-bottom: 0px !important;  margin-top: 0px !important;'>
                ক্যাশ লেস , পেপার লেস সেবা সিস্টেমে আপনার আবেদনটি যথাযথভাবে দাখিল হয়েছে।
            </p>
        </div>
        ";

        return $output;
    }

    public function pdfFooter($row)
    {
        $is_union = isUnion();
        $uniouninfo = Uniouninfo::where('short_name_e', $row->union_name)->first();

        $membar_text = $is_union ? 'সংশ্লিষ্ট ইউপি সদস্যের স্বাক্ষর ও সীল' : 'ওয়ার্ড সহকারি';

        $output = "
        <table width='100%' style='border-collapse: collapse;margin-top:60px' border='0'>
            <tr>
                <td style='text-align: center;' width='40%'>
                    <div class='signature text-center position-relative'>
                        <b><span style='color:#7230A0;font-size:18px;'>$membar_text</span></b>
                    </div>
                </td>
                <td style='text-align: center; width: 200px;' width='30%'></td>
                <td style='text-align: center;' width='40%'>
                    <div class='signature text-center position-relative'>
                        <b><span style='color:#7230A0;font-size:18px;'> $row->applicant_name_bn </span><br /></b>
                        <span style='font-size:16px;'>আবেদনকারী</span><br />
                        $uniouninfo->full_name <br> $uniouninfo->thana , $uniouninfo->district  ।
                    </div>
                </td>
            </tr>
        </table>
        <p style='background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 16px; margin-top: 20px;margin-bottom:0px'>
            " . ($is_union ? 'সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন' : 'সময়মত পৌরসভা কর পরিশোধ করুন। পৌরসভার উন্নয়নমূলক কাজে সহায়তা করুন') . "
        </p>
        <p class='m-0' style='font-size:14px;text-align:center;margin: 0;'> 'ক্যাশ লেস , পেপার লেস সেবা সিস্টেম' $uniouninfo->domain এর সাথে থাকার জন্য ধন্যবাদ </p>
        ";

        return $output;
    }
}
