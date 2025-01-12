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
        <div style="width:750px; padding:20px; border: 5px solid #11083a;position:relative;overflow: hidden;">

            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr style="margin-top:0px;margin-bottom:0px;">
                    <td></td>
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;" width=50%>
                        <h1 style="color: #7230A0; margin: 0px; font-size: 18px">{{ $uniouninfo->full_name_en }}</h1>
                    </td>
                    <td></td>
                </tr>

                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:14px">{{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}.</p>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:12px">Email: {{ $row->c_email }}</p>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <td style="text-align: center;" width="20%">
                        @php
                        $qrurl = url("/verification/sonod/$main_sonod_id?en=true&sonod_name=$sonodnames->enname&sonod_Id=$sonod_Id");
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ $qrurl }}&size=80x80"/>
                        <br/>
                        <div class="signature text-center position-relative" style="font-size: 12px">
                            Issue Date: {{ (date("d/m/Y", strtotime($row->created_at))) }}
                        </div>
                    </td>
                    <td style="text-align: center; line-height:1em" width="20%">
                        <img width="70px" style='margin-bottom:7px' src="{{ base64('backend/bd-logo.png') }}">

                        <div class="nagorik_sonod" style="margin-top:2px;">
                            <div style="color: #159513;font-size: 17px;border-radius: 30em;width:200px;margin:5px auto;text-align:center;padding:3px 0;">
                                <b>{{ SonodEnName($row->sonod_name) }}</b>
                            </div>
                            <div style="font-size: 14px;width:300px;margin:1px auto;text-align:center;">
                                License No: {{ ($sonod_Id) }}
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="100px" src="{{ $row->image }}">
                    </td>
                </tr>
            </table>

            {{ sonodView_trade2($row->id,true) }}

            @php
            $C_color = '#7230A0';
            $C_size = '15px';
            $color = 'black';
            $style = '';
            if($row->unioun_name=='dhamor'){
                $C_color = '#5c1caa';
                $C_size = '16px';
                $color = '#5c1caa';
            }

            if($row->unioun_name=='toria'){
                $C_color = '#5c1caa';
                $style = "margin-bottom: -33px; margin-left: 83px;";
            }
            @endphp

            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;vertical-align: bottom;" width="40%">
                        <div class="signature text-center position-relative" style="color:black">
                            <img width="170px" style="{{ $style }}" src="{{ $row->socib_signture }}"><br/>
                            <b><span style="color:{{ $C_color }};font-size:{{ $C_size }};">{{ $row->socib_name }}</span><br/></b>
                            <span style="font-size:16px;">Administrative Officer</span><br/>
                            {{ $uniouninfo->full_name_en }}<br> {{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}.<br>
                        </div>
                    </td>
                    <td style="text-align: center; width: 200px;" width="30%">
                        <img width="100px" src="{{ $uniouninfo->sonod_logo }}">
                    </td>
                    <td style="text-align: center;" width="40%">
                        <div class="signature text-center position-relative" style="color:{{ $color }}">
                            <img width="170px" style="{{ $style }}" src="{{ $row->chaireman_sign }}"><br/>
                            <b><span style="color:{{ $C_color }};font-size:{{ $C_size }};">{{ $row->chaireman_name }}</span><br/></b>
                            <span style="font-size:16px;">{{ $row->chaireman_type }}</span><br/>
                            {{ $uniouninfo->full_name_en }}<br> {{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}.<br>
                            @if($row->unioun_name!='gognagar')
                            {{ $row->c_email }}
                            @endif
                        </div>
                    </td>
                </tr>
            </table>

            <p style="background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 12px; margin-top: 0px;" class="m-0">
                "Pay union taxes on time. Support the development work of the union."
            </p>
            <p style="font-size:12px;text-align:center;margin:0px">
                To verify this document, scan the QR code or visit {{ $uniouninfo->domain }}
            </p>
        </div>
    </div>

</body>

</html>
