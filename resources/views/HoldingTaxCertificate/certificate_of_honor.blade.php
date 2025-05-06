<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body style="font-family: 'bangla', sans-serif;">

    <div style="width:800px; padding:20px; border: 10px solid #787878">
        <div style="width:750px;  padding:20px; border: 5px solid #11083a;position:relative;overflow: hidden; ">

            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;" width="20%">
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="70px" src="{{ base64('backend/bd-logo.png') }}">
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="100px" src="{{ base64($holdingTax->image) }}">
                    </td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:20px">গণপ্রজাতন্ত্রী বাংলাদেশ</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:0px;margin-bottom:0px;">
                    <td></td>
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;">
                        <h1 style="color: #7230A0; margin: 0px; font-size: 28px">{{ $uniouninfo->full_name }}</h1>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;">
                        <p style="font-size:20px">
                            উপজেলা: {{ $uniouninfo->thana }}, জেলা: {{ $uniouninfo->district }} ।
                        </p>
                    </td>
                    <td></td>
                </tr>
            </table>

            @php
                $width = '200px';
                $fontsize = '22px';
                $C_color = 'black';
            @endphp

            <div
                style="
                background-color: #159513;
                color: #fff;
                font-size: {{ $fontsize }};
                border-radius: 30em;
                width:{{ $width }};
                margin:20px auto;
                text-align:center;
                padding:5px 0;
                ">
                সম্মাননাপত্র
            </div>

            <p style="text-align:center;width:500px;margin:0 auto">
                হোল্ডিং ট্যাক্স প্রদানকারীর নাম: <u>{{ $holdingTax->maliker_name }} </u> <br>
                হোল্ডিং নং- <u>{{ int_en_to_bn($holdingTax->holding_no) }}</u>, গ্রাম: <u>{{ $holdingTax->gramer_name }} </u>, ওয়ার্ড নং- <u>{{ int_en_to_bn($holdingTax->word_no) }}</u>, <br>
                ডাকঘর- <u>{{ $uniouninfo->short_name_b }} </u>
                @if ($is_union)
                    ইউনিয়ন- <u>{{ $uniouninfo->short_name_b }} </u>, <br>
                @else
                    পৌরসভা- <u>{{ $uniouninfo->short_name_b }} </u>, <br>
                @endif
                উপজেলা- <u>{{ $uniouninfo->thana }} </u>, জেলা- <u>{{ $uniouninfo->district }} </u>। <br>
            </p>

            <p style="text-align:center;width:600px;margin:0 auto;margin-top:20px;font-size:20px">
                এই মর্মে জানানো যাচ্ছে যে, {{ int_en_to_bn($holdingBokeya->year) }} অর্থবছরে অত্র
                @if ($is_union)
                    ইউনিয়ন পরিষদ
                @else
                    পৌরসভা
                @endif
                কর্তৃক বসত বাড়ীর উপর ধার্য্যকৃত বার্ষিক কর (হোল্ডিং ট্যাক্স) পরিশোধ করায়
                @if ($is_union)
                    ইউনিয়ন পরিষদের
                @else
                    পৌরসভার
                @endif
                পক্ষ থেকে সম্মাননা প্রদান করা হলো।
            </p>

            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: left;" width="40%">
                        তারিখঃ {{ int_en_to_bn(date('Y-m-d')) }}
                    </td>
                    <td style="text-align: center; width: 200px;" width="30%">
                        <img width="100px" src="{{ $uniouninfo->sonod_logo }}">
                    </td>
                    <td style="text-align: center;" width="40%">
                        <div class="signature text-center position-relative" style="color:{{ $color }}">
                            <img width="170px" src="{{ $uniouninfo->c_signture }}"><br/>
                            <b><span style="color:{{ $C_color }};font-size:{{ $C_size }};">{{ $uniouninfo->c_name }}</span> <br />
                            </b><span style="font-size:16px;">চেয়ারম্যান</span><br />
                            {{ $uniouninfo->full_name }}<br> {{ $uniouninfo->thana }}, {{ $uniouninfo->district }} ।
                        </div>
                    </td>
                </tr>
            </table>
            <p style="background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 16px; margin-top: 0px;" class="m-0">
                "সময়মত
                @if ($is_union)
                    ইউনিয়ন কর
                @else
                    পৌরসভা কর
                @endif
                পরিশোধ করুন।
                @if ($is_union)
                    ইউনিয়নের
                @else
                    পৌরসভার
                @endif
                উন্নয়নমূলক কাজে সহায়তা করুন"
            </p>
        </div>
    </div>
</body>

</html>
