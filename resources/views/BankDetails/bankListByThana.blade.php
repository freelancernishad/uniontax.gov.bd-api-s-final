<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ইউনিয়ন পরিষদ ব্যাংক তথ্য</title>
    <style>
        body {
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
        /* Adjust font size for English text */
        .english {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }
        /* Adjust width of columns for balance */
        th.union-name, td.union-name {
            width: 15%;
        }
        th.bank-name, td.bank-name {
            width: 18%;
        }
        th.branch-name, td.branch-name {
            width: 18%;
        }
        th.account-name, td.account-name {
            width: 18%;
        }
        th.account-no, td.account-no {
            width: 12%;
        }
        th.routing-no, td.routing-no {
            width: 12%;
        }
        th.ekpay-user, td.ekpay-user {
            width: 12%;
        }
    </style>
</head>
<body>

    <h2 class="title">uniontax.gov.bd ইউনিয়ন পরিষদ ব্যাংক তথ্য - একপে</h2>
    <h4 class="subtitle">উপজেলা: {{ $upazilaName_bn }}, জেলা: {{ $districtName_bn }}</h4>

    <table>
        <thead>
            <tr>
                <th class="union-name">ইউনিয়ন নাম</th>
                <th class="bank-name">ব্যাংক নাম</th>
                <th class="branch-name">শাখার নাম</th>
                <th class="account-name">অ্যাকাউন্ট শিরোনাম</th>
                <th class="account-no">অ্যাকাউন্ট নং</th>
                <th class="routing-no">রাউটিং নং</th>
                <th class="ekpay-user">একপে ইউজার আইডি</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatted as $item)
                <tr>
                    <td class="union-name {{ preg_match('/[A-Za-z]/', $item['union_name']) ? 'english' : '' }}">{{ $item['union_name'] }}</td>
                    <td class="bank-name {{ preg_match('/[A-Za-z]/', $item['bank_name']) ? 'english' : '' }}">{{ $item['bank_name'] }}</td>
                    <td class="branch-name {{ preg_match('/[A-Za-z]/', $item['branch_name']) ? 'english' : '' }}">{{ $item['branch_name'] }}</td>
                    <td class="account-name {{ preg_match('/[A-Za-z]/', $item['account_name']) ? 'english' : '' }}">{{ $item['account_name'] }}</td>
                    <td class="account-no {{ preg_match('/[A-Za-z]/', $item['account_no']) ? 'english' : '' }}">{{ $item['account_no'] }}</td>
                    <td class="routing-no {{ preg_match('/[A-Za-z]/', $item['routing_no']) ? 'english' : '' }}">{{ $item['routing_no'] }}</td>
                    <td class="ekpay-user {{ preg_match('/[A-Za-z]/', $item['ekpay_user_id']) ? 'english' : '' }}">{{ $item['ekpay_user_id'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        মুদ্রিত তারিখ: {{ \Carbon\Carbon::now()->format('d M, Y') }}
    </div>

</body>
</html>
