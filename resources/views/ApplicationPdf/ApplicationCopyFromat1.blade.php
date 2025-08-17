<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: 'bangla', sans-serif;
        }

        .pdfhead {
            text-align: center;
        }

        .congrats {
            margin-bottom: 0px !important;
            font-size: 35px;
            color: white;
            background: green;
            padding: 13px 39px;
            border-radius: 14px;
            width: 200px;
            margin: 0 auto;
        }

        .footer {
            background: #787878;
            color: white;
            text-align: center;
            padding: 2px;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 0;
        }

        .footer-small {
            font-size: 14px;
            text-align: center;
            margin: 0;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="pdfhead">
        <div>
        @if(isUnion())
            <img width="70px" src="{{ base64('backend/bd-logo.png') }}">
        @endif
        </div>

        <div style="width:300px;margin:0 auto;" ><p style="margin-bottom:0 !important;font-size:16px">  গণপ্রজাতন্ত্রী বাংলাদেশ
            <h2 style="margin: 0;">{{ $uniouninfo->full_name }}</h2>
            উপজেলা:  {{ $uniouninfo->thana }}, জেলা:  {{ $uniouninfo->district }} ।

        </p></div>

        <div class="congrats">অভিনন্দন !</div>



            <p style="font-size:16px; color:blue; margin-bottom:0px;">ক্যাশ লেস , পেপার লেস সেবা সিস্টেমে আপনার আবেদনটি যথাযথভাবে দাখিল হয়েছে।</p>



    </div>

    <!-- Information Table -->
    <table style="margin-top:20px;">
        <tr>
            <td>সেবার ধরণ</td>
            <td>:</td>
            <td>{{ $row->sonod_name }}</td>
        </tr>
        <tr>
            <td>আবেদনের ক্রমিক নং</td>
            <td>:</td>
            <td>{{ int_en_to_bn($row->sonod_Id) }}</td>
        </tr>
        <tr>
            <td>আবেদনের তারিখ</td>
            <td>:</td>
            <td>{{ int_en_to_bn(date("d/m/Y", strtotime($row->created_at))) }}</td>
        </tr>

        <!-- Conditional Sections -->
        @if ($row->sonod_name == 'একই নামের প্রত্যয়ন' || $row->sonod_name == 'বিবিধ প্রত্যয়নপত্র')
            @if ($row->sameNameNew == 1)
                <tr>
                    <td>আবেদনকারীর নাম</td>
                    <td>:</td>
                    <td>{{ $row->applicant_name }}</td>
                </tr>
                <tr>
                    <td>সনদ ধারীর নাম</td>
                    <td>:</td>
                    <td>{{ $row->utname }}</td>
                </tr>
                @if ($row->sonod_name == 'একই নামের প্রত্যয়ন')
                    <tr>
                        <td>সনদ ধারীর দ্বিতীয় নাম</td>
                        <td>:</td>
                        <td>{{ $row->applicant_second_name }}</td>
                    </tr>
                @endif
            @else
                <tr>
                    <td>সনদ ধারীর নাম</td>
                    <td>:</td>
                    <td>{{ $row->applicant_name }}</td>
                </tr>
                <tr>
                    <td>সনদ ধারীর দ্বিতীয় নাম</td>
                    <td>:</td>
                    <td>{{ $row->applicant_second_name }}</td>
                </tr>
            @endif
        @else
            <tr>
                <td>আবেদনকারীর নাম</td>
                <td>:</td>
                <td>{{ $row->applicant_name }}</td>
            </tr>
        @endif

        @if ($row->sonod_name == 'ওয়ারিশ সনদ')
            <tr>
                <td>মৃত ব্যাক্তির নাম</td>
                <td>:</td>
                <td>{{ $row->utname }}</td>
            </tr>
        @elseif ($row->sonod_name == 'উত্তরাধিকারী সনদ')
            <tr>
                <td>জীবিত ব্যক্তির নাম</td>
                <td>:</td>
                <td>{{ $row->utname }}</td>
            </tr>
        @endif

        @if ($row->applicant_national_id_number)
            <tr>
                <td>এনআইডি নং</td>
                <td>:</td>
                <td>{{ int_en_to_bn($row->applicant_national_id_number) }}</td>
            </tr>
        @else
            <tr>
                <td>জন্ম নিবন্ধন নং</td>
                <td>:</td>
                <td>{{ int_en_to_bn($row->applicant_birth_certificate_number) }}</td>
            </tr>
        @endif

        <tr>
            <td>পিতা/স্বামীর নাম</td>
            <td>:</td>
            <td>{{ $row->applicant_father_name }}</td>
        </tr>

        <tr>
            <td>বাসিন্দার ধরণ</td>
            <td>:</td>
            <td>{{ $row->applicant_resident_status }}</td>
        </tr>

        <tr>
            <td>বর্তমান ঠিকানা</td>
            <td>:</td>
            <td>হোল্ডিং নং- {{ $row->applicant_holding_tax_number }}, গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }}, জেলা: {{ $row->applicant_present_district }}</td>
        </tr>


        @if ($row->sonod_name == 'বিবিধ প্রত্যয়নপত্র')
            <tr>
                <td>আবেদনকৃত প্রত্যয়নের বিবরণ</td>
                <td>:</td>
                <td>{{ $row->prottoyon }}</td>
            </tr>
        @endif
    </table>

    <!-- Footer Section -->
    <table width="100%" style="border-collapse: collapse;" border="0">
        <tr>
            <td style="text-align: center;" width="40%"></td>
            <td style="text-align: center; width: 200px;" width="30%"></td>
            <td style="text-align: center;" width="40%">
                <div class="signature text-center">
                    <b><span style="color:#7230A0; font-size:18px;">{{ $row->chaireman_name }}</span></b><br />
                    <span style="font-size:16px;">{{ $row->chaireman_type }}</span><br />
                    {{ $uniouninfo->full_name }}<br />{{ $uniouninfo->thana }}, {{ $uniouninfo->district }} ।
                </div>
            </td>
        </tr>
    </table>

    @if (isUnion())
        <p class="footer">"সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন"</p>
    @else
        <p class="footer">"সময়মত পৌরসভা কর পরিশোধ করুন। পৌরসভার উন্নয়নমূলক কাজে সহায়তা করুন"</p>
    @endif
    <p class="footer-small">'ক্যাশ লেস , পেপার লেস সেবা সিস্টেম' {{ $uniouninfo->domain }} এর সাথে থাকার জন্য ধন্যবাদ</p>

</body>

</html>
