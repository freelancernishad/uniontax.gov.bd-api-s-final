<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sonod;
use App\Models\Sonodnamelist;
use App\Models\Uniouninfo;
use App\Models\Payment;
use LaravelMpdf\Facades\LaravelMpdf;

class InvoicePdfController extends Controller
{
    public function invoice(Request $request, $id)
    {
        // Retrieve the Sonod record by ID
        $row = Sonod::find($id);

        if (!$row) {
            return response()->json(['error' => 'Sonod not found.'], 404);
        }

        // Retrieve related data
        $uniouninfo = Uniouninfo::where('short_name_e', $row->unioun_name)->first();
        $TaxInvoice = Payment::where('sonodId', $row->id)->latest()->first();

        if (!$uniouninfo) {
            return response()->json(['error' => 'Union information not found.'], 404);
        }

        // Prepare the HTML content
        $htmlView = view('Invoice.SonodInvoice', compact('row', 'uniouninfo', 'TaxInvoice'))->render();

        // Header and footer (if any, set here)
        $header = null;
        $footer = null;

        // File name
        $filename = "Invoice-$row->id.pdf";

        // Generate the PDF
        generatePdf($htmlView, $header, $footer, $filename);
    }


}
