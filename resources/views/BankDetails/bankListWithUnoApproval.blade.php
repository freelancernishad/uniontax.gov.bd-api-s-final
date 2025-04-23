<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ইকপে কালেকশন স্যাটেলমেন্টের জন্য UNO অনুমোদন</title>
    <style>
        body {
            padding: 40px;
            background-color: #fff;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
        }
        .header h2, .header h4 {
            margin: 0;
        }
        .header h2 {
            font-size: 24px;
        }
        .header h4 {
            margin-top: 5px;
            font-size: 18px;
            font-weight: normal;
            color: #666;
        }
        .content {
            margin-top: 20px;
            font-size: 16px;
            text-align: justify;
        }
        .bank-list {
            margin-top: 30px;
            font-size: 14px;
        }
        .bank-list table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Column size fixed */
        }
        .bank-list th, .bank-list td {
            border: 1px solid #444;
            padding: 4px; /* কমানো হয়েছে */
            text-align: center;
            font-size: 12px; /* আগে 13px ছিল, এখন 12px */
            overflow-wrap: break-word;
        }
        /* Column width specific */
        .bank-list th.union-name, .bank-list td.union-name {
            width: 12%;
        }
        .bank-list th.bank-name, .bank-list td.bank-name {
            width: 18%;
        }
        .bank-list th.account-name, .bank-list td.account-name {
            width: 22%;
        }
        .bank-list th {
            background-color: #0077cc;
            color: white;
        }
        .bank-list td.english {
            font-size: 11px; /* ইংরেজির জন্য 11px */
            font-family: Arial, sans-serif;
        }
        .footer {
            margin-top: 60px;
            text-align: right;
        }
        .signature {
            margin-top: 100px;
            text-align: right;
        }
        .signature p {
            margin: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>উপজেলা নির্বাহী অফিসারের অফিস</h2>
    <h4>উপজেলা: {{ $upazilaName_bn }}, জেলা: {{ $districtName_bn }}</h4>
</div>

<div class="content">
    <p>
        জানানো যাচ্ছে যে, {{ $upazilaName_bn }} উপজেলার ইউনিয়নগুলোর মধ্যে আধুনিক ও সুরক্ষিত ক্যাশলেস পেমেন্ট সিস্টেম চালু করা হয়েছে। নাগরিকরা তাদের ফি, কর এবং অন্যান্য পেমেন্ট Ekpay গেটওয়ে ব্যবহার করে জমা দিচ্ছেন।
    </p>
    <p>
        Ekpay কে অনুরোধ করা হচ্ছে, নাগরিকদের এই পেমেন্টগুলি সরাসরি ইউনিয়ন ব্যাংক একাউন্টে সেটেল করতে অনুমতি দেওয়া হোক।
    </p>
</div>

<div class="bank-list">
    <h4>ইউনিয়ন ব্যাংক এর বিস্তারিত:</h4>
    <table>
        <thead>
            <tr>
                <th class="union-name">ইউনিয়ন নাম</th>
                <th class="bank-name">ব্যাংক নাম</th>
                <th>শাখার নাম</th>
                <th class="account-name">অ্যাকাউন্ট শিরোনাম</th>
                <th>অ্যাকাউন্ট নং</th>
                <th>রাউটিং নং</th>
                <th>একপে ইউজার</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatted as $item)
                <tr>
                    <td class="union-name {{ preg_match('/[A-Za-z]/', $item['union_name']) ? 'english' : '' }}">
                        {{ $item['union_name'] }}
                    </td>
                    <td class="bank-name {{ preg_match('/[A-Za-z]/', $item['bank_name']) ? 'english' : '' }}">
                        {{ $item['bank_name'] }}
                    </td>
                    <td class="{{ preg_match('/[A-Za-z]/', $item['branch_name']) ? 'english' : '' }}">
                        {{ $item['branch_name'] }}
                    </td>
                    <td class="account-name {{ preg_match('/[A-Za-z]/', $item['account_name']) ? 'english' : '' }}">
                        {{ $item['account_name'] }}
                    </td>
                    <td class="account-name {{ preg_match('/[A-Za-z]/', $item['account_no']) ? 'english' : '' }}">{{ $item['account_no'] }}</td>
                    <td class="account-name {{ preg_match('/[A-Za-z]/', $item['routing_no']) ? 'english' : '' }}">{{ $item['routing_no'] }}</td>
                    <td class="account-name {{ preg_match('/[A-Za-z]/', $item['ekpay_user_id']) ? 'english' : '' }}">{{ $item['ekpay_user_id'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="signature">
    <p>উপজেলা নির্বাহী অফিসার</p>
    <p>{{ $upazilaName_bn }}, {{ $districtName_bn }}</p>
</div>

</body>
</html>
