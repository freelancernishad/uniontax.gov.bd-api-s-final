<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Sonodnamelists</title>
    <style>
        body { font-family: 'bangla', sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header-table { margin-bottom: 20px; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table" width="100%" style="border-collapse: collapse;">
        <tr>
            <td width="20%"></td>
            <td width="20%" style="text-align: center;">
                <img width="70px" src="{{ base64('backend/bd-logo.png') }}" alt="Logo">
            </td>
            <td width="20%"></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <p style="font-size: 20px;font-weight: normal;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</p>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <h1 style="color: #7230A0; margin: 0; font-size: 35px;">{{ $uniouninfo->full_name }}</h1>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <p style="font-size: 20px">উপজেলা: {{ $uniouninfo->thana }}, জেলা: {{ $uniouninfo->district }} ।</p>
            </td>
            <td></td>
        </tr>
    </table>

    <!-- Table Content -->
    <h2 style="text-align: center;">সনদ নামের তালিকা</h2>
    <table>
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
