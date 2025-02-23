<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'bangla', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-bottom: 10px;
        }
        .header-table td {
            border: none;
            padding: 2px 0;
        }
        .header-logo img {
            width: 70px;
        }
        .govt-text {
            font-size: 20px;
            font-weight: normal;
            margin: 0;
        }
        .union-name {
            font-size: 35px;
            font-weight: bold;
            color: #7230A0;
            margin: 0;
        }
        .location {
            font-size: 20px;
            margin: 0;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th, .main-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .main-table th {
            background-color: #f2f2f2;
        }
        h2 { text-align: center; margin-bottom: 5px; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td width="20%"></td>
            <td width="20%" class="header-logo">
                <img width="70px" src="{{ base64('backend/bd-logo.png') }}" alt="Logo">
            </td>
            <td width="20%"></td>
        </tr>
        <tr>
            <td colspan="3" class="govt-text">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</td>
        </tr>
        <tr>
            <td colspan="3" class="union-name">{{ $uniouninfo->full_name }}</td>
        </tr>
        <tr>
            <td colspan="3" class="location">উপজেলা: {{ $uniouninfo->thana }}, জেলা: {{ $uniouninfo->district }}</td>
        </tr>
    </table>

    <!-- Table Content -->
    <h2>সনদ নামের তালিকা</h2>
    <table class="main-table">
        <thead>
            <tr>
                <th>ক্রমিক নং</th>
                <th>সনদ নাম</th>
                <th>ফী</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['bnname'] }}</td>
                    <td>{{ $item['fees'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
