<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>



</head>

<body style="font-family: 'bangla', sans-serif;">

    <div class="pdfhead" style="text-align:center">
    <div style=":center;"  >
        @php
            // print_r($uniouninfo);
        @endphp
        <img width="70px" src="{{ base64($uniouninfo->sonod_logo) }}" />
    </div>

    <div style="width:300px;margin:0 auto;" ><p style="margin-bottom:0 !important;font-size:16px">  গণপ্রজাতন্ত্রী বাংলাদেশ
        <h2 style="margin: 0;">{{ $uniouninfo->full_name }}</h2>
        উপজেলা:  {{ $uniouninfo->thana }}, জেলা:  {{ $uniouninfo->district }} ।

    </p></div>

    <p style="text-align:center" >   <div style="margin-bottom: 0px !important;
        font-size: 35px;
        color: white;
        background: green;
        padding: 13px 39px;
        border-radius: 14px;width: 200px;
    margin: 0 auto;" >অভিনন্দন !</div>  <br> <p style="font:size:16px;color:blue;margin-bottom:0px !important;">ডিজিটাল ইউনিয়ন ট্যাক্স ও সেবা সিস্টেমে আপনার আবেদনটি যথাযথভাবে দাখিল হয়েছে।</p>

    </div>


                <table style="margin-top:20px">

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
                    @if ($row->sonod_name=='একই নামের প্রত্যয়ন' || $row->sonod_name=='বিবিধ প্রত্যয়নপত্র')

                        @if($row->sameNameNew==1)
                            <tr>
                                <td>আবেদনকারীর নাম</td>
                                <td>:</td>
                                <td>{{ $row->applicant_name }}</td>
                            </tr>

                            <tr>
                                <td>সনদ ধারীর নাম </td>
                                <td>:</td>
                                <td>{{ $row->utname }}</td>
                            </tr>

                            @if($row->sonod_name=='একই নামের প্রত্যয়ন')
                            <tr>
                                <td>সনদ ধারীর দ্বিতীয় নাম </td>
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
                            @if($row->sonod_name=='একই নামের প্রত্যয়ন')
                            <tr>
                                <td>সনদ ধারীর দ্বিতীয় নাম </td>
                                <td>:</td>
                                <td>{{ $row->applicant_second_name }}</td>
                            </tr>
                            @endif
                        @endif





                    @else
                        <tr>
                            <td>আবেদনকারীর নাম</td>
                            <td>:</td>
                            <td>{{ $row->applicant_name }}</td>
                        </tr>
                    @endif


                    @if ($row->sonod_name=='ওয়ারিশ সনদ')
                    <tr>
                        <td>মৃত ব্যাক্তির নাম</td>
                        <td>:</td>
                        <td>{{ $row->utname }}</td>
                    </tr>
                    @elseif ($row->sonod_name=='উত্তরাধিকারী সনদ')
                    <tr>
                        <td>জীবিত ব্যক্তির নাম</td>
                        <td>:</td>
                        <td>{{ $row->utname }}</td>
                    </tr>
                    @endif

                    @if($row->applicant_national_id_number)
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



                    @if($row->sameNameNew==1)

                    <tr>
                        <td>পিতা/স্বামীর নাম</td>
                        <td>:</td>
                        <td>{{ $row->ut_father_name }}</td>
                    </tr>
                    <tr>
                        <td>বর্তমান ঠিকানা</td>
                        <td>:</td>
                        <td> গ্রাম: {{ $row->ut_grame }}, ডাকঘর: {{ $row->ut_post }}, উপজেলা: {{ $row->ut_thana }} , জেলা: {{ $row->ut_district }}</td>
                    </tr>
                    @else

                    <tr>
                        <td>পিতা/স্বামীর নাম</td>
                        <td>:</td>
                        <td>{{ $row->applicant_father_name }}</td>
                    </tr>
                    <tr>
                        <td>বর্তমান ঠিকানা</td>
                        <td>:</td>
                        <td>হোল্ডিং নং- {{ $row->applicant_holding_tax_number }}, গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }} , জেলা: {{ $row->applicant_present_district }}</td>
                    </tr>

                    @endif


                    @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র')
                    <tr>
                        <td>আবেদনকৃত প্রত্যয়নের বিবরণ</td>
                        <td>:</td>
                        <td>{{ $row->prottoyon }}</td>
                    </tr>
                    @endif





                </table>




    <p style="text-align:center" >শীগ্রই আপনার আবেদনটি কর্তৃপক্ষ কর্তৃক যথাযথ প্রক্রিয়ায় অনুমোদন করা হবে। <br></p>

    @php
        // print_r($row)
    @endphp
        {{-- <table></table> --}}




    <table width="100%" style="border-collapse: collapse;" border="0">
        <tr>
            <td  style="text-align: center;" width="40%">

            </td>
            <td style="text-align: center; width: 200px;" width="30%">

            </td>
            <td style="text-align: center;" width="40%">
                <div class="signature text-center position-relative">
                    {{-- <img width="170px"  src="{{ base64($row->chaireman_sign) }}"><br/> --}}
                    <b><span style="color:#7230A0;font-size:18px;">{{ $row->chaireman_name }}</span> <br />
                            </b><span style="font-size:16px;">{{ $row->chaireman_type }}</span><br />

                    {{ $uniouninfo->full_name }}<br> {{ $uniouninfo->thana }}, {{ $uniouninfo->district }} । <br>


                </div>
            </td>
        </tr>


    </table>



<p style="background: #787878;
    color: white;
    text-align: center;
    padding: 2px 2px;font-size: 16px;     margin-top: 20px;margin-bottom:0px" class="m-0">"সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন"</p>

<p class="m-0" style="font-size:14px;text-align:center;margin: 0;">'ডিজিটাল ইউনিয়ন ট্যাক্স ও সেবা সিস্টেম' {{ $uniouninfo->domain }} এর সাথে থাকার জন্য ধন্যবাদ</p>

    {{-- <p style="text-align: center"> {{ $uniouninfo->domain }}</p> --}}


    </div>




</body>

</html>

