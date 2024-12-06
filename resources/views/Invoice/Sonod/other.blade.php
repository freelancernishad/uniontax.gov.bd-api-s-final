@php

$khatlist = $row->amount_deails;
        $khatlist = json_decode($khatlist);
        $total = $khatlist->tredeLisenceFee;
        $amount = ($total*$khatlist->vatAykor)/100;

        $totalAmount = $khatlist->pesaKor+$total+$amount;

    $html = "
    <style>
        .tdlist{
    height: 160px;
    vertical-align: top;
}
    </style>
    <table class='table ' style='width:100%;' cellspacing='0' cellpadding='0' border='1' >
  <thead>
    <tr>

      <th scope='col'>খাত/আদায়ের বিবরণ</th>
      <th scope='col'>বিগত বছরের বকেয়া (যদিথাকে) টাকা </th>
      <th scope='col'>বর্তমানে পরিশোধকৃত টাকা </th>
      <th scope='col'>মোট টাকার পরিমাণ</th>

    </tr>
  </thead>
  <tbody>

    <tr>

      <td class='tdlist'>".int_en_to_bn($row->khat)."</td>
      <td class='tdlist'>".int_en_to_bn($row->last_years_money)."</td>
      <td class='tdlist'>".int_en_to_bn($row->currently_paid_money)."</td>

      <td class='tdlist'>".int_en_to_bn($row->total_amount)."</td>

    </tr>
    <tr class='tr'>
            <td colspan='3' class='defalttext td defaltfont'style='text-align:right;    padding: 0 13px;'><p> মোট </p></td>
            <td class='td defaltfont'>".int_en_to_bn($row->total_amount)."</td>
    </tr>

  </tbody>
</table>



    ";



    echo $html;
@endphp
