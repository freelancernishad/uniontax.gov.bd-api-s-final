<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>টেন্ডার ইনভয়েস</title>
    <style>
        h1, h2, h3, h4, h5, h6, th { font-weight: normal; }
        tr th, tr td { padding: 5px 10px; border: 1px solid black; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        td { text-align: center; }
    </style>
</head>
<body style="font-family: 'bangla', sans-serif;'">

@php
    $amount = $TaxInvoice->amount ?? 0;
    $html = "
        <div style='text-align:center'>
            <h5 style='margin:0;font-size:16px;'>গণপ্রজাতন্ত্রী বাংলাদেশ</h5>
            <h4 style='margin:0;font-size:20px;'>$uniouninfo->full_name</h4>
            <h6 style='margin:0;font-size:16px;'>উপজেলা: $uniouninfo->thana, জেলা: $uniouninfo->district</h6>
            <h2 style='margin:0 auto;background:black;color:white;width:300px;'>টেন্ডার ফর্ম ইনভয়েস</h2>
        </div>

        <p style='text-align:right'>রশিদ নং: " . int_en_to_bn($TaxInvoice->trxId ?? 'N/A') . "</p>

        <p>
            আবেদনকারী: $row->applicant_orgName<br>
            পিতা: $row->applicant_org_fatherName<br>
            ঠিকানা: গ্রাম - $row->vill, ডাকঘর - $row->postoffice,
            থানা - $row->thana, জেলা - $row->distric<br>
            মোবাইল: ". int_en_to_bn($row->mobile) ." <br>
           <h2> দর আইডি: ". int_en_to_bn($row->dorId) ." </h2>
        </p>


        <table>
            <thead>
                <tr>
                    <th>ক্রমিক</th>
                    <th>বিবরণ</th>
                    <th>পরিমাণ (টাকা)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>১</td>
                    <td>সিডিউল ফর্ম ফি</td>
                    <td>" . int_en_to_bn($amount) . "</td>
                </tr>
                <tr>
                    <td colspan='2' style='text-align:right'><strong>মোট</strong></td>
                    <td><strong>" . int_en_to_bn($amount) . "</strong></td>
                </tr>
            </tbody>
        </table>
    ";

    $html .= "
        <p style='text-align:right; margin-top: 30px;'>আদায়কারীর স্বাক্ষর</p>
        <h5>.......................................................................................................................................</h5>
    ";

    echo $html;
@endphp

</body>
</html>
