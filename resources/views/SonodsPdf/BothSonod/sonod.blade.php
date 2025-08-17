<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

<style>
    body{
        /*font-family: 'noto_sans_bengali', 'sans-serif';*/
        font-family: '{{ $font_family }}', "sans-serif";
    }

    .nikosh{
        font-family: 'bangla', 'sans-serif'
    }

    .english_text {
        font-family: 'Roboto', sans-serif; /* Default font for English */
    }



</style>


</head>

<body>


    <div style="width:800px; padding:10px; border: 10px solid #787878">
        <div style="width:750px;  padding:20px; border: 5px solid #11083a;position:relative;overflow: hidden; ">
            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;" width="20%">
                        @php
                        $qrurl = url("/verification/sonod/$row->id?sonod_name=$sonodnames->enname&sonod_Id=$row->sonod_Id");
                       @endphp
                       <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ $qrurl }}&size=80x80"/>
                              <br/>
                               <div class="signature text-center position-relative">
                                  ইস্যুর তারিখ: {{ int_en_to_bn(date("d/m/Y", strtotime($row->created_at))) }}
                                </div>

                    </td>
                    <td style="text-align: center;" width="20%">
                    @if(isUnion())
                        <img width="70px" src="{{ base64('backend/bd-logo.png') }}">
                    @endif
                    </td>
                    <td style="text-align: center;" width="20%">

                    <img width="100px" src="{{ $row->image }}">
               </td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td>
                    </td>
                    <td style="text-align: center;" width="50%">
                                                @if(isUnion())
                        <p style="font-size:20px">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার </p>
                        @endif
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="margin-top:0px;margin-bottom:0px;">
                    <td>
                    </td>
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;" width=50%>
                        <h1 class="nikosh" style="color: #7230A0; margin: 0px; font-size: 28px">{{ $uniouninfo->full_name }}</h3>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td>
                    </td>
                    <td style="text-align: center; " width="50%">

                        <p style="font-size:20px">উপজেলা:  {{ $uniouninfo->thana }}, জেলা:  {{ $uniouninfo->district }} ।</p>
                    </td>
                    <td>
                    </td>
                </tr>


                @php
                    if($uniouninfo->portal){
                        echo $website = '<tr style="margin-top:2px;margin-bottom:2px;">
                            <td>
                            </td>
                            <td style="text-align: center; " width="50%">
                                <p style="font-size:12px">ওয়েবসাইটঃ '. $uniouninfo->portal.'</p>
                                <p style="font-size:12px">ই-মেইলঃ '. $row->c_email.'</p>
                            </td>
                            <td>
                            </td>
                        </tr>';
                    }
                @endphp

</table>


<div class="nagorik_sonod" style="margin-bottom:10px;">
    <?php


     $namelength =  strlen($row->sonod_name);
    $width = '300px';
    $fontsize = '30px';
    if($namelength>=100){
        $width = '400px';
        $fontsize = '20px';

    }elseif($namelength>=85){
        $width = '500px';
        $fontsize = '22px';
    }elseif($namelength>=72){
        $width = '450px';
        $fontsize = '25px';
    }elseif($namelength>=62){
        $width = '400px';
        $fontsize = '27px';
    }
           ?>
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
{{ changeSonodName($row->sonod_name) }} </div>
                        </div>

{{ sonodView($row->id) }}

@php
$C_color = '#7230A0';
$C_size = '18px';
$color = 'black';
$style = '';
@endphp

<table width="100%" style="border-collapse: collapse;" border="0">
                    <tr>
                        <td style="text-align: center;vertical-align: bottom;"  width="40%">


                @if($row->unioun_name!='gognagar')
                   <div class="signature text-center position-relative">



                    @else
                        @if($row->sonod_name=='ট্রেড লাইসেন্স')
                        <div class="signature text-center position-relative" style="color:black">
                            <br/>
                             <b><span style="color:#7230A0;font-size:18px;">মহিউদ্দিন দেওয়ান</span> <br />
                                     </b><span style="font-size:16px;">সচিব</span><br />
                             {{ $uniouninfo->full_name }}<br> {{ $uniouninfo->thana }}, {{ $uniouninfo->district }} ।
                         <br>
                         </div>
                        @endif
                    @endif
                        </td>
                        <td style="text-align: center; width: 200px;" width="30%">
                            <img width="100px" src="{{ $uniouninfo->sonod_logo }}">
                        </td>
                        <td style="text-align: center;" width="40%">

                            <div class="signature text-center position-relative" style="color:{{ $color }}">
                                <img width="170px" style="{{ $style }}"  src="{{ $row->chaireman_sign }}"><br/>
                                <b><span style="color:{{ $C_color }};font-size:{{ $C_size }};">{{ $row->chaireman_name }}</span> <br />
                                        </b><span style="font-size:16px;">{{ $row->chaireman_type }}</span><br />

                                {{ $uniouninfo->full_name }}<br> {{ $uniouninfo->thana }}, {{ $uniouninfo->district }} ।
                            <br>
                            @if($row->unioun_name!='gognagar')
                            <span class="english_text">{{ $row->c_email }}</span>
                            @endif
                            </div>





                        </td>
                    </tr>

                </table>


                @if(isUnion())
                    <p style="background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 13px; margin-top: 0px; margin-bottom: 0px;" class="m-0">
                        "সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন"
                    </p>
                @else
                    <p style="background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 13px; margin-top: 0px; margin-bottom: 0px;" class="m-0">
                        "সময়মত পৌরসভা কর পরিশোধ করুন। পৌরসভার উন্নয়নমূলক কাজে সহায়তা করুন"
                    </p>
                @endif
                <p class="m-0" style="font-size:12px;text-align:center;margin:0 !important">ইস্যুকৃত সনদটি যাচাই করতে <span class="english_text">QR</span> কোড স্ক্যান করুন অথবা ভিজিট করুন <span class="english_text">{{ $uniouninfo->domain }}</span></p>



                  {{-- <p style="background: #787878;
  color: white;
  text-align: center;
  padding: 2px 2px;font-size: 16px;     margin-top: 0px;" class="m-0">"সময়মত ইউনিয়ন কর পরিশোধ করুন। ইউনিয়নের উন্নয়নমূলক কাজে সহায়তা করুন"</p>
                  <p class="m-0" style="font-size:14px;text-align:center">ইস্যুকৃত সনদটি যাচাই করতে <span class="english_text">QR</span> কোড স্ক্যান করুন অথবা ভিজিট করুন <span class="english_text">{{ $uniouninfo->domain }}</span></p> --}}
            </div>
        </div>
    </div>




</body>

</html>
