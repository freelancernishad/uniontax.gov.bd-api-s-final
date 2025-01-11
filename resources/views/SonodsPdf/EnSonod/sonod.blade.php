<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Default font for English */
        }

        .english_text {
            font-family: 'Roboto', sans-serif; /* Default font for English */
        }
    </style>
</head>

<body>
    <div style="width:800px; padding:10px; border: 10px solid #787878">
        <div style="width:750px;  padding:20px; border: 5px solid #11083a;position:relative;overflow: hidden;">
            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;" width="20%">
                        @php
                        $qrurl = url("/verification/sonod/$main_sonod_id?en=true&sonod_name=$sonodnames->enname&sonod_Id=$sonod_Id");
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ $qrurl }}&size=80x80" />
                        <br />
                        <div class="signature text-center position-relative" style="font-size:12px">
                            Issue Date: {{ date("d/m/Y", strtotime($row->created_at)) }}
                        </div>
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="70px" src="{{ base64('backend/bd-logo.png') }}">
                    </td>
                    <td style="text-align: center;" width="20%">
                        <img width="100px" src="{{ $row->image }}">
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
                    <td style="margin-top:0px; margin-bottom:0px; text-align: center;" width=50%>
                        <h1 style="color: #7230A0; margin: 0px; font-size: 20px">{{ $uniouninfo->full_name_en }}</h1>
                    </td>
                    <td></td>
                </tr>
                <tr style="margin-top:2px;margin-bottom:2px;">
                    <td></td>
                    <td style="text-align: center;" width="50%">
                        <p style="font-size:14px">Upazila: {{ $uniouninfo->thana_en }}, District: {{ $uniouninfo->district_en }}.</p>
                    </td>
                    <td></td>
                </tr>

                @php
                if ($uniouninfo->portal) {
                    echo $website = '<tr style="margin-top:2px;margin-bottom:2px;">
                        <td></td>
                        <td style="text-align: center;" width="50%">
                            <p style="font-size:12px">Website: ' . $uniouninfo->portal . '</p>
                            <p style="font-size:12px">Email: ' . $row->c_email . '</p>
                        </td>
                        <td></td>
                    </tr>';
                }
                @endphp
            </table>

            <div class="nagorik_sonod" style="margin-bottom:10px;">
                <?php
                $namelength = strlen($row->sonod_name);
                $width = '300px';
                $fontsize = '30px';
                if ($namelength >= 100) {
                    $width = '400px';
                    $fontsize = '20px';
                } elseif ($namelength >= 85) {
                    $width = '500px';
                    $fontsize = '22px';
                } elseif ($namelength >= 72) {
                    $width = '450px';
                    $fontsize = '25px';
                } elseif ($namelength >= 20) {
                    $width = '300px';
                    $fontsize = '18px';
                }
                ?>
                <div style="
                    background-color: #159513;
                    color: #fff;
                    font-size: {{ $fontsize }};
                    border-radius: 30em;
                    width:{{ $width }};
                    margin:20px auto;
                    text-align:center;
                    padding:5px 0;">
                    {{ SonodEnName($row->sonod_name) }}
                </div>
            </div>

            {{ sonodView($row->id,true) }}

            @php
            $C_color = '#7230A0';
            $C_size = '13px';
            $color = 'black';
            $style = '';
            @endphp

            <table width="100%" style="border-collapse: collapse;" border="0">
                <tr>
                    <td style="text-align: center;vertical-align: bottom;" width="40%">

                        @if ($row->sonod_name == 'Trade License')
                        <div class="signature text-center position-relative" style="color:black">
                            <br />
                            <b><span style="color:#7230A0;font-size:12px;">{{ $row->socib_name }}</span> <br /></b>
                            <span style="font-size:16px;">Secretary</span><br />
                            {{ $uniouninfo->full_name_en }}<br> {{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}.
                            <br>
                        </div>
                        @endif

                    </td>
                    <td style="text-align: center; width: 200px;" width="30%">
                        <img width="100px" src="{{ $uniouninfo->sonod_logo }}">
                    </td>
                    <td style="text-align: center;" width="40%">
                        <div class="signature text-center position-relative" style="color:{{ $color }}">
                            <img width="170px" style="{{ $style }}" src="{{ $row->chaireman_sign }}"><br />
                            <b><span style="color:{{ $C_color }};font-size:{{ $C_size }};">{{ $row->chaireman_name }}</span> <br /></b>
                            <span style="font-size:13px;">{{ $row->chaireman_type }}</span><br />
                            <span style="font-size:13px;">{{ $uniouninfo->full_name_en }}<br> {{ $uniouninfo->thana_en }}, {{ $uniouninfo->district_en }}. </span>
                            <br>
                            <span class="english_text" style="font-size:13px;">{{ $row->c_email }}</span>
                        </div>
                    </td>
                </tr>
            </table>
            <p style="background: #787878; color: white; text-align: center; padding: 2px 2px;font-size: 13px; margin-top: 0px; margin-bottom: 0px;" class="m-0">
                "Pay union tax on time. Support the development work of the union."
            </p>
            <p class="m-0" style="font-size:12px;text-align:center;margin:0 !important">To verify this certificate, scan the <span class="english_text">QR</span> code or visit <span class="english_text">{{ $uniouninfo->domain }}</span></p>
        </div>
    </div>
</body>

</html>
