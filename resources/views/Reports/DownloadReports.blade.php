<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .summary-table {
            width: 100%;
            border: none;
            margin-bottom: 30px;
        }

        .summary-table tr {
            border: none;
        }

        .summary-table td {
            border: none;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .summary-box h3 {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }

        .summary-box p {
            font-size: 20px;
            margin: 0;
            color: #000;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 16px;
            color: #777;
            margin-bottom: 20px;
        }

        .gov-logo {
            width: 80px;
            height: auto;
        }

        .text-center {
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                background: none;
                color: #000;
            }
        }
    </style>
</head>
<body>

    <div class="text-center mb-4">
        <img src="{{ base64('backend/bd-logo.png') }}" alt="Government Logo" class="gov-logo">
        <h1 style="font-size: 25px" class="mt-3">{{ $reportTitle }}</h1>
        <h2 class="footer mt-2">
            রিপোর্ট জেনারেট তারিখ: {{ int_en_to_bn(now()->format('d-m-Y h:i A')) }}
        </h2>
    </div>

    <div class="text-center no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">প্রিন্ট করুন</button>
    </div>

    <!-- Summary Section -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-box">
                    <h3>মোট আবেদন</h3>
                    <p>{{ int_en_to_bn(($data['total_report']['totals']['total_pending'] ?? 0) + ($data['total_report']['totals']['total_approved'] ?? 0) + ($data['total_report']['totals']['total_cancel'] ?? 0)) }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>নতুন আবেদন</h3>
                    <p>{{ int_en_to_bn($data['total_report']['totals']['total_pending'] ?? 0) }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>ইস্যুকৃত সনদ</h3>
                    <p>{{ int_en_to_bn($data['total_report']['totals']['total_approved'] ?? 0) }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>বাতিলকৃত আবেদন</h3>
                    <p>{{ int_en_to_bn($data['total_report']['totals']['total_cancel'] ?? 0) }}</p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div class="summary-box">
                    <h3>মোট আদায়কৃত ফি এর পরিমাণ</h3>
                    <p>{{ int_en_to_bn($data['total_report']['totals']['total_amount'] ?? 0) }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Sonod Report Section -->
    <div>
        <h2>সনদ প্রতিবেদন</h2>
        <table>
            <thead>
                <tr>
                    <th>সনদ নাম</th>
                    <th>নতুন আবেদন</th>
                    <th>ইস্যুকৃত সনদ</th>
                    <th>বাতিলকৃত আবেদন</th>
                    <th>মোট আবেদন</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pendingTotal = 0;
                    $approvedTotal = 0;
                    $cancelTotal = 0;
                @endphp

                @foreach($data['total_report']['sonod_reports'] as $value)
                    @php
               
                        $pendingTotal += $value['pending_count'];
                        $approvedTotal += $value['approved_count'];
                        $cancelTotal += $value['cancel_count'];
                    @endphp
                    <tr>
                        <td>{{ $value['sonod_name'] }}</td>
                        <td>{{ int_en_to_bn($value['pending_count']) }}</td>
                        <td>{{ int_en_to_bn($value['approved_count']) }}</td>
                        <td>{{ int_en_to_bn($value['cancel_count']) }}</td>
                        <td>{{ int_en_to_bn($value['pending_count'] + $value['approved_count'] + $value['cancel_count']) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td><strong>মোট</strong></td>
                    <td><strong>{{ int_en_to_bn($pendingTotal) }}</strong></td>
                    <td><strong>{{ int_en_to_bn($approvedTotal) }}</strong></td>
                    <td><strong>{{ int_en_to_bn($cancelTotal) }}</strong></td>
                    <td><strong>{{ int_en_to_bn($pendingTotal + $approvedTotal + $cancelTotal) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Report Section -->
    <div>
        <h2>আদায় প্রতিবেদন</h2>
        <table>
            <thead>
                <tr>
                    <th>সনদ নাম</th>
                    <th>মোট লেনদেন</th>
                    <th>মোট টাকার পরিমাণ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['total_report']['payment_reports'] as $value)
                    <tr>
                        <td>{{ translateToBangla($value['sonod_type']) }}</td>
                        <td>{{ int_en_to_bn($value['total_payments']) }}</td>
                        <td>{{ int_en_to_bn($value['total_amount']) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td><strong>মোট</strong></td>
                    <td><strong>{{ int_en_to_bn($data['total_report']['totals']['total_payments'] ?? 0) }}</strong></td>
                    <td><strong>{{ int_en_to_bn($data['total_report']['totals']['total_amount'] ?? 0) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
