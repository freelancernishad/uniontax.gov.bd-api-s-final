<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .td {
            border: 1px dotted black;
        }
        .header-table td, .content-table td {
            text-align: center;

        }
        .content-table  {
            border-collapse: collapse;
        }

        .content-table th, .content-table td {
            padding: 3px 5px;

            font-weight: normal;
        }

.td{
    font-size: 18px !important;
}

        .signature {
            font-size: 16px;
            text-align: center;
        }
        .signature b {
            font-size: 18px;
            color: #7230A0;
        }
    </style>
</head>
<body style="font-family: 'bangla', sans-serif;">

@if($union == 'all')
    <table class="header-table" width="100%" style="border-collapse: collapse;" >
        <tr>
            <td width="20%"></td>
            <td width="20%">
                <img width="70px" src="{{ base64('backend/bd-logo.png') }}" alt="Logo">
            </td>
            <td width="20%"></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <p style="font-size: 20px">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</p>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <h1 style="color: #7230A0; margin: 0; font-size: 28px">সকল ইউনিয়ন এর প্রতিবেদন</h1>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <!-- Uncomment if needed -->
                <!-- <p style="font-size: 20px">উপজেলা: {{ $uniouninfo->thana }}, জেলা: {{ $uniouninfo->district }} ।</p> -->
            </td>
            <td></td>
        </tr>
    </table>
@else
    <table class="header-table" width="100%" style="border-collapse: collapse;">
        <tr>
            <td width="20%"></td>
            <td width="20%">
                <img width="70px" src="{{ base64($uniouninfo->sonod_logo) }}" alt="Union Logo">
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
@endif

<h2 style="width:350px;background:green;padding:10px 10px;margin:10px auto;text-align:center;color:white;border-radius: 20px;font-size:20px">
    সেবা প্রদান ও ফি আদায় সংক্রান্ত প্রতিবেদন
</h2>

<h3 style="text-align: center;font-weight: normal;">
    @if($sonod_type == 'holdingtax')
        হোল্ডিং ট্যাক্স
    @elseif($sonod_type == 'all')
        সকল ফি এর প্রতিবেদন
    @else
        {{ $sonod_type }}
    @endif
</h3>

<table width="100%" class="content-table">
    <tr>
        <td colspan="2" style="text-align: left">
            <span>প্রতিবেদনের সময়কালঃ</span>  {{ int_en_to_bn(date("d/m/Y", strtotime($from))) }} থেকে {{ int_en_to_bn(date("d/m/Y", strtotime($to))) }} পর্যন্ত
        </td>
        <td style="text-align: right">
            অর্থ বছর: {{ int_en_to_bn(CurrentOrthoBochor(1, date("m", strtotime($from)))) }}
        </td>
    </tr>
</table>

<table width="100%" class="content-table">
    <thead>
        <tr>
            <th class="td" width="10%">ক্রমিক নং</th>
            <th class="td" width="20%">তারিখ</th>

            @if($sonod_type == 'all')
                <th class="td" width="20%">সেবার ধরণ</th>
            @endif

            <th class="td" width="20%">সেবা গ্রহনকারীর নাম</th>
            <th class="td" width="30%">ঠিকানা (হোল্ডিং ও গ্রাম)</th>
            <th class="td" width="20%">মোবাইল নম্বর</th>
            <th class="td" width="20%">আদায়কৃত ফি এর পরিমান</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
            $i = 1;
        @endphp
        @foreach ($rows as $row)

            <tr>
                <td class="td" style="text-align:center">{{ int_en_to_bn($i) }}</td>
                <td class="td" style="text-align:center">{{ int_en_to_bn(date("d-m-Y", strtotime($row['created_at']))) }}</td>

                @if($sonod_type == 'all')
                    <td class="td" style="text-align:center">
                        {{ $row['sonod_type'] == 'holdingtax' ? 'হোল্ডিং ট্যাক্স' : $row['sonod_type'] }}
                    </td>
                @endif

                @if($row['holding_tax'])
                    <td class="td">{{ $row['holding_tax']['maliker_name'] }}</td>
                    <td class="td">গ্রামঃ- {{ $row['holding_tax']['gramer_name'] }}, হোল্ডিং নং- {{ int_en_to_bn($row['holding_tax']['holding_no']) }}</td>
                    <td class="td">{{ int_en_to_bn($row['holding_tax']['mobile_no']) }}</td>
                @else
                    <td class="td">@if($row['sonods']){{ $row['sonods']['applicant_name'] }}@endif</td>
                    <td class="td">@if($row['sonods'])গ্রামঃ- {{ $row['sonods']['applicant_present_village'] }},
                        হোল্ডিং নং- {{ int_en_to_bn($row['sonods']['applicant_holding_tax_number']) }}@endif</td>
                    <td class="td">@if($row['sonods']){{ int_en_to_bn($row['sonods']['applicant_mobile']) }}@endif</td>
                @endif

                <td class="td" style="text-align:center">{{ int_en_to_bn(round($row['amount'], 2)) }}</td>
            </tr>
            @php
                $i++;
                $total += $row['amount'];
            @endphp
        @endforeach
        <tr>
            <td colspan="{{ $sonod_type == 'all' ? '6' : '5' }}" class="td" style="text-align: right">মোট</td>
            <td class="td" style="text-align:center">{{ int_en_to_bn(round($total, 2)) }}</td>
        </tr>
    </tbody>
</table>



</body>
</html>
