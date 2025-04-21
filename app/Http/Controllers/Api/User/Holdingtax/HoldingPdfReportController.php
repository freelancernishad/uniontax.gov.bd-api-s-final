<?php

namespace App\Http\Controllers\Api\User\Holdingtax;

use App\Models\Holdingtax;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class HoldingPdfReportController extends Controller
{
    public function holdingFamillySingleReportPDF(Request $request, $id)
{
    // Find the holding record
    $holding = Holdingtax::with(['familyMembers.sohayotas'])->find($id);

    if (!$holding) {
        return response()->json(['error' => 'Holdingtax record not found.'], 404);
    }

    // Prepare the HTML view
    $htmlView = view('holdingtax.holding_familly_report_single', compact('holding'))->render();

    // Optional header/footer HTML
    $header = null;
    $footer = null;

    // File name
    $filename = "Holdingtax-Family-Report-$id.pdf";

    // Generate and output PDF
    generatePdf($htmlView, $header, $footer, $filename);
}
}
