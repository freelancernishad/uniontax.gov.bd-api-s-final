<?php

namespace App\Http\Controllers\Api\User\Reports;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentReportsController extends Controller
{

    public function PaymentReports(Request $request)
    {
        ini_set('max_execution_time', '60000');
        ini_set("pcre.backtrack_limit", "500000000000000000");
        ini_set('memory_limit', '512M'); // Avoid excessively high memory limits

        // Extract request parameters
        $union = $request->union;
        $sonod_type = $request->sonod_type ?: 'all';
        // Get values from the request or set default to the last 7 days
        $from = $request->from ?: Carbon::now()->subDays(7)->toDateString();
        $to = $request->to ?: Carbon::now()->toDateString();
        $payment_type = $request->payment_type;

        // Build base query
        $query = Payment::with(['sonod', 'tax'])
            ->where('status', 'Paid');

        // Apply filters
        if ($union !== 'all') {
            $query->where('union', $union);
        }

        if ($payment_type === 'menual') {
            $query->whereNull('payment_type');
        } elseif ($payment_type === 'online') {
            $query->where('payment_type', 'online');
        }

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        if ($sonod_type && $sonod_type !== 'all') {
            $query->where('sonod_type', $sonod_type);
        }

        // Use chunking to reduce memory usage
        $rows = [];
        $query->orderBy('id', 'asc')->chunk(1000, function ($chunk) use (&$rows) {
            $rows = array_merge($rows, $chunk->toArray());
        });


        // Retrieve Union information
        $uniouninfo = Uniouninfo::where('short_name_e', $union)->first();

        // Generate HTML view for PDF
        $htmlView = view('Reports.PaymentReports', compact('rows', 'uniouninfo', 'sonod_type', 'from', 'to', 'union'))->render();

        // Define header and footer if needed
        $header = null; // Add HTML for header if required
        $footer = null; // Add HTML for footer if required


        if($union != 'all'){
            $footer = $this->pdfFooter($uniouninfo);
        }

        // File name
        $filename = "Payment_Report_" . now()->format('Ymd_His') . ".pdf";

        // Generate and stream the PDF
        return generatePdf($htmlView, $header, $footer, $filename);
    }

    public function pdfFooter($uniouninfo){


        $C_color = '#7230A0';
        $C_size = '18px';
        $color = 'black';
        if($uniouninfo->short_name_e=='dhamor'){
        $C_color = '#5c1caa';
        $C_size = '20px';
        $color = '#5c1caa';
        }

        $output ="
            <table width='100%' style='border-collapse: collapse;margin-top:50px' border='0'>
                <tr>
                    <td style='text-align: center;' width='40%'>
                    </td>
                    <td style='text-align: center; width: 200px;' width='30%'>
                    </td>
                    <td style='text-align: center;' width='40%'>

                        <div class='signature text-center position-relative' style='color: $color'>
                            <b><span style='color: $C_color;font-size: $C_size;'> $uniouninfo->c_name </span>
                                <br />
                            </b><span style='font-size:16px;'>চেয়ারম্যান</span><br />
                             $uniouninfo->full_name <br>  $uniouninfo->thana ,  $uniouninfo->district  ।
                        </div>
                    </td>
                </tr>
            </table>

        ";

        return $output;
    }


}