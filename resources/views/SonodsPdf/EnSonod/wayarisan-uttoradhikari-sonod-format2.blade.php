<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body style="font-family: 'bangla', sans-serif;">

    <div style="width:800px; padding:12px; border: 10px solid #787878">
        <div style="width:750px; padding:12px; border: 5px solid #11083a; position:relative; overflow: hidden; ">
            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;" width="20%">
                        @php
                            $qrurl = url("/verification/sonod/$row->id?sonod_name=$sonodnames->enname&sonod_Id=$sonod_Id");
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ $qrurl }}&size=80x80"/>
                        <br />
                        <div class="signature text-center position-relative">
                            Issue Date: {{ int_en_to_bn(date('d/m/Y', strtotime($row->created_at))) }}
                        </div>
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="50px" src="{{ base64('backend/bd-logo.png') }}">
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="50px" src="{{ base64($row->image) }}">
                    </td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:14px">Government of the People's Republic of Bangladesh</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:0px;margin-bottom:0px;">
                    <td></td>
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;" width="50%">
                        <h1 style="color: #7230A0; margin: 0px; font-size: 18px">{{ $uniouninfo->full_name_en }}</h1>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:13px">Upazila: {{ $uniouninfo->thana_en }}, District: {{ $uniouninfo->district_en }}.</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:11px">Website:</p>
                    </td>
                    <td></td>
                </tr>
            </table>

            <div class="nagorik_sonod" style="margin-bottom:10px;">
                @php
                    $width = '200px';
                    $fontsize = '15px';
                @endphp
                <div style="background-color: #159513; color: #fff; font-size: {{ $fontsize }}; border-radius: 30em; width:{{ $width }}; margin:14px auto; text-align:center; padding:3px 0;">
                    {{ changeSonodName($row->sonod_name) }}
                </div>
            </div>

            {!! sonodView_Inheritance_certificate($row->id) !!}

            @php
                $C_color = '#7230A0';
                $C_size = '14px';
                $color = 'black';
                $style = '';
                $w_list = json_decode($row->successor_list);
                $margin_top = 300 - (count($w_list) * 15);
                $marginTop = "margin-top:$margin_top";
            @endphp

            <table width="100%" style="border-collapse: collapse;{{ $marginTop }}" border="0">
                <tr>
                    <td style="text-align: center; vertical-align: bottom;" width="40%">
                        <div class="signature text-center position-relative" style="color:black;font-size:12px;">
                            <span style="font-size:12px;">Union Member</span><br />
                            {{ $uniouninfo->full_name }}<br> {{ $uniouninfo->thana }}, {{ $uniouninfo->district }}.<br>
                        </div>
                    </td>
                    <td style="text-align: center; width: 200px;" width="30%">
                        <img width="100px" src="{{ base64($uniouninfo->sonod_logo) }}">
                    </td>
                    <td style="text-align: center;" width="40%">
                        <div class="signature text-center position-relative" style="color:{{ $color }}; font-size:12px;">
                            <img width="170px" style="{{ $style }}" src="{{ base64($row->chaireman_sign) }}"><br/>
                            <b><span style="color:{{ $C_color }}; font-size:{{ $C_size }};">{{ $row->chaireman_name }}</span></b><br />
                            <span style="font-size:12px;">{{ $row->chaireman_type }}</span><br />
                            {{ $uniouninfo->full_name_en }}<br> {{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}.<br>
                            {{ $row->c_email }}
                        </div>
                    </td>
                </tr>
            </table>

            <p style="background: #787878; color: white; text-align: center; padding: 2px 2px; font-size: 12px; margin-top: 0px; margin:0" class="m-0">
                "Pay union taxes on time. Support the development activities of the union."
            </p>
            <p class="m-0" style="font-size:12px;text-align:center;margin:0;position: fixed; bottom: 0;">
                To verify this certificate, scan the QR code or visit {{ $uniouninfo->domain }}
            </p>
        </div>
    </div>

</body>

</html>
