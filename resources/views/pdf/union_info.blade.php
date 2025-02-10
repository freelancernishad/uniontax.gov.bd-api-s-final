<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Union Information</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: left;

        }

        th {
            background-color: #f2f2f2;
        }

        td a {
            color: #007bff;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body>
    <h2>{{ $district->bn_name }} জেলার {{ $thana }} উপজেলার সকল ইউনিয়ন এর ওয়েবসাইট এবং ইমেইল পাসওয়ার্ড</h2>

    <table>
        <thead>
            <tr>
                <th>ইউনিয়ন</th>
                <th>থানা</th>
                <th>জেলা</th>
                <th>ওয়েবসাইট</th>
                <th>প্রশাসনিক কর্মকর্তা</th>
                <th>চেয়ারম্যান</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uniouninfoList as $info)
                <tr>
                    <td>{{ $info['full_name'] }}</td>
                    <td>{{ $info['thana'] }}</td>
                    <td>{{ $info['district'] }}</td>
                    <td>
                        <a href="{{ $info['url1'] }}" target="_blank">{{ $info['url1'] }}</a><br>
                        <a href="{{ $info['url2'] }}" target="_blank">{{ $info['url2'] }}</a>
                    </td>
                    <td>
                        ইমেইলঃ {{ $info['Secretary_email'] }}<br>
                        পাসওয়ার্ডঃ {{ $info['Secretary_password'] }}
                    </td>
                    <td>
                        ইমেইলঃ {{ $info['Chairman_email'] }}<br>
                        পাসওয়ার্ডঃ {{ $info['Chairman_password'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
