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
    </style>


</head>

<body style="font-family: 'bangla', sans-serif;">



    <table width="100%" style="border-collapse: collapse;" border="0">
        <tr>
            <td style="text-align: center;" width="20%">
                {{-- <span style="color:#b400ff;"><b>
                        উন্নয়নের গণতন্ত্র, <br /> শেখ হাসিনার মূলমন্ত্র </b>
                </span> --}}
            </td>
            <td style="text-align: center;" width="20%">
                <img width="70px" src="{{ base64($uniouninfo->sonod_logo) }}">
            </td>
            <td style="text-align: center;" width="20%">
            </td>
        </tr>
        <tr style="margin-top:2px;margin-bottom:2px;">
            <td>
            </td>
            <td style="text-align: center;" width="50%">
                <p style="font-size:20px">গণপ্রজাতন্ত্রী বাংলাদেশ</p>
                <p style="font-size:25px">চেয়ারম্যানের কার্যালয়</p>
            </td>
            <td>
            </td>
        </tr>
        <tr style="margin-top:0px;margin-bottom:0px;">
            <td>
            </td>
            <td style="margin-top:0px; margin-bottom:0px; text-align: center;">
                <h1 style="color: #7230A0; margin: 0px; font-size: 28px">{{ $uniouninfo->full_name }}</h3>
            </td>
            <td>
            </td>
        </tr>
        <tr style="margin-top:2px;margin-bottom:2px;">
            <td>
            </td>
            <td style="text-align: center; ">
                <p style="font-size:20px">উপজেলা: {{ $uniouninfo->thana }}, জেলা: {{ $uniouninfo->district }} ।</p>
            </td>
            <td>
            </td>
        </tr>
    </table>
    <h2 style="width:350px;background:green;padding:10px 10px;margin:10px auto;text-align:center;color:white;border-radius: 20px;font-size:20px">বকেয়া হোল্ডিং ট্যাক্স - ওয়ার্ড- {{ int_en_to_bn($word) }}</h2>




    <table width="100%" style="border-collapse: collapse;" border="0">
        <thead>
            <tr>
                <th class="td" style="text-align:center" width="5%">ক্রমিক নং</th>
                <th class="td" style="text-align:center" width="8%">হোল্ডিং নং</th>
                <th class="td" style="text-align:center" width="15%">সেবা গ্রহনকারীর নাম</th>
                <th class="td" style="text-align:center" width="15%">ঠিকানা (গ্রাম)</th>
                <th class="td" style="text-align:center" width="15%">মোবাইল নম্বর</th>
                <th class="td" style="text-align:center" width="17%">জাতীয় পরিচয়পত্র নম্বর</th>
                <th class="td" style="text-align:center" width="25%"> অর্থ-বছর এবং ফি এর পরিমান</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1;
            @endphp
            @foreach ($holdingtaxs as $holdingtax)
            <tr>
                <td class="td" style="text-align:center">{{ int_en_to_bn($i) }}</td>

                <td class="td" style="text-align:center">{{ $holdingtax->holding_no }}</td>
                <td class="td" style="text-align:center">{{ $holdingtax->maliker_name }}</td>
                <td class="td" style="text-align:center">{{ $holdingtax->gramer_name }}</td>
                <td class="td" style="text-align:center">{{ int_en_to_bn($holdingtax->mobile_no) }}</td>
                <td class="td" style="text-align:center">{{ int_en_to_bn($holdingtax->nid_no) }}</td>

                <td class="td" style="text-align:center">

                    <table  width="100%" style="border-collapse: collapse;" border="0">
                        <thead>
                            <tr>
                                <th class="td">অর্থ-বছর</th>
                                <th class="td">টাকার পরিমাণ</th>
                                <th class="td">স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($holdingtax->holdingBokeyas as $item)
                            <tr>
                                <td class="td">{{ $item->year }}</td>
                                <td class="td">{{ $item->price }}</td>
                                <td class="td">{{ $item->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>




                </td>



            </tr>
            @php
            $i++;

            @endphp
            @endforeach
            {{-- <tr>
                @if($sonod_type=='all')
                <td colspan="6" class="td" style="text-align: right">মোট</td>
                @else
                <td colspan="5" class="td" style="text-align: right">মোট</td>
                @endif
                <td class="td" style="text-align:center">{{ int_en_to_bn(round($total,2)) }}</td>
            </tr> --}}
        </tbody>
    </table>



</body>

</html>
