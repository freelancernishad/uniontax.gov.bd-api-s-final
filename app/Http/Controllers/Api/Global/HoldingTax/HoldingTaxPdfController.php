<?php

namespace App\Http\Controllers\Api\Global\HoldingTax;

use App\Models\Holdingtax;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\HoldingBokeya;
use App\Http\Controllers\Controller;
use App\Models\Payment;

class HoldingTaxPdfController extends Controller
{

    public function holdingPaymentInvoice($id)
    {
        $holdingBokeya = HoldingBokeya::find($id);

        if ($holdingBokeya->status !== 'Paid') {
            return response()->json(['error'=>'Invoice generation is only available for paid entries.'],404);
        }
        $payment = Payment::where(['sonod_type'=>'holdingtax','sonodId'=>$id,'status'=>'Paid'])->first();

        $COB = getOrthoBchorYear(1);

        if ($holdingBokeya->payOB) {
            $COB = $holdingBokeya->payOB;
        }

        $payYear = $holdingBokeya->payYear;

        $holdingTax_id = $holdingBokeya->holdingTax_id;
        $holdingTax = Holdingtax::find($holdingTax_id);
        $union = $holdingTax->unioun;
        $unions = Uniouninfo::where(['short_name_e' => $union])->first();

        // Filter HoldingBokeyas by pay year and session year (COB)
        $holdingBokeyas = HoldingBokeya::where([
            'holdingTax_id' => $holdingTax_id,
            'payYear' => $payYear,
            'payOB' => $COB
        ])->get();


        $currentYear = $COB; // Current session year
        $previousYears = $this->getPreviousYears($currentYear,$holdingTax_id); // Helper function to get multiple previous years


        // Calculate the current and previous amounts
        $currentamount = HoldingBokeya::where([
            'holdingTax_id' => $holdingTax_id,
            'payYear' => $payYear,
            'year' => $currentYear,
            'payOB' => $COB
        ])->sum('price');

        // return response()->json($currentamount);



        $previousamount = HoldingBokeya::where([
            'holdingTax_id' => $holdingTax_id,
            'payYear' => $payYear,
            'payOB' => $COB
        ])->whereIn('year', $previousYears)->sum('price');

        // Format the amount
        $amounts = number_format((float)$holdingBokeyas->sum('price'), 2, '.', '');
        $totalAmount = $amounts;
        $amount_text = convertAnnualIncomeToText($amounts);




        // Generate the HTML for the invoice using a Blade file
        $htmlView = view('Invoice.HoldingTaxInvoice', [

            'customers' => $holdingTax,
            'payment' => $payment,
            'unions' => $unions,
            'amount_text' => $amount_text,
            'totalAmount' => $totalAmount,
            'holdingBokeyas' => $holdingBokeyas,
            'HoldingBokeya' => $holdingBokeya,
            'currentamount' => $currentamount,
            'previousamount' => $previousamount,
        ])->render();

        // Optional header and footer
        $header = null;
        $footer = null;

        // Generate the PDF using the generatePdf function
        $fileName = 'Invoice-' . date('Y-m-d H:i:s') . '.pdf';
        generatePdf($htmlView, $header, $footer, $fileName,'A4-L');
    }



    protected function getPreviousYears($currentYear, $holdingTaxId)
    {
        return HoldingBokeya::where('holdingTax_id', $holdingTaxId)
            ->where('year', '<>', $currentYear) // Exclude the current year
            ->distinct() // Ensure no duplicate years
            ->pluck('year') // Retrieve only the 'year' column
            ->toArray(); // Convert to an array
    }


    public function holdingCertificate_of_honor(Request $request, $id)
    {
        // Retrieve the HoldingBokeya, HoldingTax, and UniounInfo data
        $holdingBokeya = HoldingBokeya::find($id);
        $holdingTax = Holdingtax::find($holdingBokeya->holdingTax_id);
        $holdingTax->image = handleFileUrl($holdingTax->image);
        
        $uniouninfo = Uniouninfo::where('short_name_e', $holdingTax->unioun)->first();
        $uniouninfo->sonod_logo = handleFileUrl($uniouninfo->sonod_logo);
        $uniouninfo->c_signture = handleFileUrl($uniouninfo->c_signture);
        // Generate the file name
        $fileName = 'Certificate_of_Honor-' . date('Y-m-d_H:m:s');

        // Prepare the view data
        $htmlView = view('HoldingTaxCertificate.certificate_of_honor', compact('uniouninfo', 'holdingTax', 'holdingBokeya'))->render();

        // Optional header and footer (could be passed as null for simplicity)
        $header = null;
        $footer = null;

        // Generate the PDF using the generatePdf function
        generatePdf($htmlView, $header, $footer, "$fileName-$holdingBokeya->holdingTax_id.pdf", 'A4');

        // The PDF will be generated and saved or streamed automatically by the generatePdf function
        // Optionally, you can return a response after this process if needed.
    }





}
