@php

$khatlist = $row->amount_deails;
        $khatlist = json_decode($khatlist);
        $total = (int)$khatlist->tredeLisenceFee;
        $amount = ($total*$khatlist->vatAykor)/100;

        $totalAmount = (int)$khatlist->pesaKor+(int)$total+(int)$amount+(int)$khatlist->last_years_money;

    $html = "
    <table class='table ' style='width:100%;' cellspacing='0' cellpadding='0' border='1' >
        <thead>
        <tr>

        <th scope='col' style='text-align:center'>খাত/আদায়ের বিবরণ</th>

        <th scope='col' style='text-align:center'>বর্তমানে পরিশোধকৃত টাকা </th>
        <th scope='col' style='text-align:center'>মোট টাকার পরিমাণ</th>
        <th scope='col' style='text-align:center'>কর নির্ধারণী তালিকার ক্রমিক নং</th>
        </tr>
        </thead>
        <tbody>

        <tr>

        <td style='text-align:center'>পেশা কর</td>

        <td style='text-align:center'> ".int_en_to_bn($khatlist->pesaKor)." </td>
        <td style='text-align:center'> ".int_en_to_bn($khatlist->pesaKor)." </td>
        <td style='text-align:center'></td>
        </tr>

        <tr>

        <td style='text-align:center'>
        ট্রেড লাইসেন্স ফি</td>

        <td style='text-align:center'> ".int_en_to_bn($khatlist->tredeLisenceFee)." </td>
        <td style='text-align:center'> ".int_en_to_bn($khatlist->tredeLisenceFee)." </td>
        <td style='text-align:center'></td>
        </tr>

        <tr>

        <td style='text-align:center'>ভ্যাট ও আয়কর</td>

        <td style='text-align:center'> ".int_en_to_bn($amount)." </td>
        <td style='text-align:center'> ".int_en_to_bn($amount)." </td>
        <td style='text-align:center'></td>
        </tr>
        <tr>

        <td style='text-align:center'>বকেয়া</td>

        <td style='text-align:center'> ".int_en_to_bn($khatlist->last_years_money)." </td>
        <td style='text-align:center'> ".int_en_to_bn($khatlist->last_years_money)." </td>
        <td style='text-align:center'></td>
        </tr>

        <tr>
        <td style='text-align:center' colspan='2'> মোট</td>

        <td style='text-align:center'> ".int_en_to_bn($totalAmount)."  </td>
        <td style='text-align:center'></td>
        </tr>


        </tbody>
        </table>
    ";
    echo $html;
@endphp
