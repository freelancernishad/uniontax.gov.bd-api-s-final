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
    </style>
</head>
<body>



    <h1>
{{ $reportTitle }}
    </h1>
        <!-- Footer Section -->
        <h2 class="footer">
            রিপোর্ট জেনারেট তারিখ: {{ int_en_to_bn(now()->format('d-m-Y')) }}
        </h2>

    <!-- Summary Section -->
    <table class="summary-table">

        <tr>
            <td>
                <div class="summary-box">
                    <h3>মোট আবেদন</h3>
                    <p>{{ $data['totals']['total_pending']+$data['totals']['total_approved']+$data['totals']['total_cancel'] }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>নতুন আবেদন</h3>
                    <p>{{ $data['totals']['total_pending'] }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>ইস্যুকৃত সনদ</h3>
                    <p>{{ $data['totals']['total_approved'] }}</p>
                </div>
            </td>
            <td>
                <div class="summary-box">
                    <h3>বাতিলকৃত আবেদন</h3>
                    <p>{{ $data['totals']['total_cancel'] }}</p>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <div class="summary-box">
                    <h3>মোট আদায়কৃত ফি এর পরিমাণ</h3>
                    <p>{{ $data['totals']['total_amount'] }}</p>
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


                @foreach($data['sonod_reports'] as $value)

                <tr>
                    <td>{{ $value->sonod_name }}</td>
                    <td>{{ $value->pending_count }}</td>
                    <td>{{ $value->approved_count }}</td>
                    <td>{{ $value->cancel_count }}</td>
                    <td>{{ $value->pending_count+$value->approved_count+$value->cancel_count }}</td>
                </tr>
                @endforeach



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


                @foreach($data['payment_reports'] as $value)
                <tr>
                    <td>{{ $value->sonod_type }}</td>
                    <td>{{ $value->total_payments }}</td>
                    <td>{{ $value->total_amount }}</td>
                </tr>
                @endforeach


                <tr>
                    <td>মোট</td>
                    <td>{{ $data['totals']['total_payments'] }}</td>
                    <td>{{ $data['totals']['total_amount'] }}</td>
                </tr>

            </tbody>
        </table>
    </div>


</body>
</html>
