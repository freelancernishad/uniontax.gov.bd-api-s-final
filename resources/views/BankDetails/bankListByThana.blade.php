<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Union Parishad Bank Info</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .title {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #0077cc;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e0f7fa;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: right;
            color: #777;
        }
    </style>
</head>
<body>

    <h2 class="title">uniontax.gov.bd Union Parishad Bank Information for Ekpay</h2>
    <h4 class="subtitle">Upazila: {{ $upazilaName }}, District: {{ $districtName }}</h4>

    <table>
        <thead>
            <tr>
                <th>Union Name</th>
                <th>Bank Name</th>
                <th>Branch Name</th>
                <th>Account Title</th>
                <th>Account No</th>
                <th>Routing No</th>
                <th>Ekpay User ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatted as $item)
                <tr>
                    <td>{{ $item['union_name'] }}</td>
                    <td>{{ $item['bank_name'] }}</td>
                    <td>{{ $item['branch_name'] }}</td>
                    <td>{{ $item['account_name'] }}</td>
                    <td>{{ $item['account_no'] }}</td>
                    <td>{{ $item['routing_no'] }}</td>
                    <td>{{ $item['ekpay_user_id'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed on: {{ \Carbon\Carbon::now()->format('d M, Y') }}
    </div>

</body>
</html>
