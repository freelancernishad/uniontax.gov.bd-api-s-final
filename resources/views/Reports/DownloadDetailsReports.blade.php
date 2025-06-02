<div class="container mt-5">
    <!-- Centered Report Title and Date with Government Logo -->
    <div class="text-center mb-4">
        <img src="{{ base64('backend/bd-logo.png') }}" alt="Government Logo" class="gov-logo">
        <h1 style="font-size: 25px" class="mt-3">{{ $reportTitle }}</h1>

        <h2 class="footer mt-2">
            রিপোর্ট জেনারেট তারিখ: {{ int_en_to_bn(now()->format('d-m-Y H:i')) }}
        </h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ $is_union ? 'ইউনিয়ন নাম' : 'পৌরসভা নাম' }}</th>
                        <th class="text-center">পেন্ডিং</th>
                        <th class="text-center">অনুমোদিত</th>
                        <th class="text-center">বাতিল</th>
                        <th class="text-center">মোট</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['total_report']['sonod_reports'] as $index => $report)

                    <tr>
                        <td>{{ int_en_to_bn($index + 1) }}</td>
                        <td>{{ UnionenBnName($report['unioun_name']) }}</td>
                        <td class="text-center">{{ int_en_to_bn($report['pending_count']) }}</td>
                        <td class="text-center">{{ int_en_to_bn($report['approved_count']) }}</td>
                        <td class="text-center">{{ int_en_to_bn($report['cancel_count']) }}</td>
                        <td class="text-center">
                            {{ int_en_to_bn($report['pending_count'] + $report['approved_count'] + $report['cancel_count']) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                                                 @php
\Log::info($data['total_report']);
                    @endphp
                        <th colspan="2" class="text-end">মোট:</th>
                        <th class="text-center">{{ int_en_to_bn($data['total_report']['totals']['total_pending']) }}</th>
                        <th class="text-center">{{ int_en_to_bn($data['total_report']['totals']['total_approved']) }}</th>
                        <th class="text-center">{{ int_en_to_bn($data['total_report']['totals']['total_cancel']) }}</th>
                        <th class="text-center">
   
                            {{ int_en_to_bn($data['total_report']['totals']['total_pending'] + $data['total_report']['totals']['total_approved'] + $data['total_report']['totals']['total_cancel']) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .gov-logo {
        width: 80px;
        height: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th, .custom-table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .custom-table th {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #495057;
    }

    .custom-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .custom-table tr:hover {
        background-color: #e9ecef;
    }

    .custom-table tfoot {
        font-weight: bold;
        background-color: #dfe4ea;
    }

    .footer {
        color: #6c757d;
        font-size: 16px;
    }

    .container {
        max-width: 100%;
        padding-left: 20px;
        padding-right: 20px;
    }

    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: end;
    }

    h1, h2 {
        margin: 0;
    }

    .mt-2 {
        margin-top: 8px;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mb-4 {
        margin-bottom: 16px;
    }
</style>
