<?php

function HoldingTaxInvoiceBody($unions, $HoldingBokeya, $customers, $previousamount, $currentamount, $payment, $amount,$totalAmount,$float='left') {

    $output = "
        <div class='memoborder' style='float: $float'>
            <div class='memobg memobg1'>
                <div class='memo'>
                    <div class='memoHead'>
                        <p class='defalttext'>ইউপি ফরম-১০</p>
                        <h2 style='font-weight: 500;' class='companiname'>{$unions->full_name}</h2>
                        <p class='defalttext'>উপজেলা: {$unions->thana}, জেলা: {$unions->district}</p>
                        <h2 class='companiname' style='color: #410fcc;'>ট্যাক্স, রেট ও বিবিধ প্রাপ্তি আদায় রশিদ</h2>
                        " . ($HoldingBokeya->status === 'Paid'
                            ? "<h2 class='companiname' style='width: 160px; margin: 0 auto; background: green; color: white; border-radius: 50px; font-size: 16px; padding: 6px 0px;'>পরিশোধিত</h2>"
                            : "<h2 class='companiname' style='width: 160px; margin: 0 auto; background: red; color: white; border-radius: 50px; font-size: 16px; padding: 6px 0px;'>অপরিশোধিত</h2>") . "
                    </div>
                    <table style='width: 100%'>
                        <tr>
                            <td colspan='2'>অর্থ বছর- " . int_en_to_bn($HoldingBokeya->payOB) . "</td>
                            <td style='text-align: right'>রশিদ নং- " . int_en_to_bn($HoldingBokeya->invoiceId) . "</td>
                        </tr>
                        <tr>
                            <td colspan='3'>এসেসমেন্ট/হোল্ডিং নং- " . int_en_to_bn($customers->holding_no) . "</td>
                        </tr>
                        <tr>
                            <td>নাম: {$customers->maliker_name}</td>
                            <td colspan='2'>পিতা/স্বামীর নাম- {$customers->father_or_samir_name}</td>
                        </tr>
                        <tr>
                            <td>ঠিকানা: গ্রাম- {$customers->gramer_name}</td>
                            <td>ওয়ার্ড- " . int_en_to_bn($customers->word_no) . "</td>
                            <td>ডাকঘর- {$unions->short_name_b}</td>
                        </tr>
                        <tr>
                            <td>উপজেলা: {$unions->thana}</td>
                            <td>জেলা: {$unions->district}</td>
                            <td>মোবাইল: " . int_en_to_bn($customers->mobile_no) . "</td>
                        </tr>
                    </table>
                    <p></p>
                    <div class='memobody' style='position: relative;'>
                        <div class='productDetails'>
                            <table class='table' style='border: 1px solid #444B8F; width: 100%' cellspacing='0'>
                                <thead class='thead'>
                                    <tr class='tr'>
                                        <td class='th defaltfont' colspan='5' width='10%'>আদায়ের বিবরণ</td>
                                    </tr>
                                    <tr class='tr'>
                                        <td class='td defaltfont' width='5%'>ক্র. নং</td>
                                        <td class='td defaltfont' width='25%'>খাত</td>
                                        <td class='td defaltfont' width='15%'>বিগত বছরের বকেয়া (যদি থাকে) টাকা</td>
                                        <td class='td defaltfont' width='15%'>চলতি অর্থ বছরে টাকার পরিমাণ</td>
                                        <td class='td defaltfont' width='15%'>মোট টাকার পরিমাণ</td>
                                    </tr>
                                </thead>
                                <tbody class='tbody'>
                                    <tr class='tr items'>
                                        <td class='td tdlist defaltfont'>" . int_en_to_bn(1) . "</td>
                                        <td class='td tdlist defaltfont'>
                                            " . ($customers->category === 'প্রতিষ্ঠান'
                                                ? "প্রতিষ্ঠানের বাৎসরিক মূল্যের উপর কর <br/> ({$customers->busnessName})"
                                                : 'বসত বাড়ীর বাৎসরিক মূল্যের উপর কর') . "
                                        </td>
                                        <td class='td tdlist defaltfont'>" . int_en_to_bn(number_format($previousamount, 2)) . "</td>
                                        <td class='td tdlist defaltfont'>" . int_en_to_bn(number_format($currentamount, 2)) . "</td>
                                        <td class='td tdlist defaltfont'>" . int_en_to_bn(number_format($totalAmount, 2)) . "</td>
                                    </tr>
                                </tbody>
                                <tfoot class='tfoot'>
                                    <tr class='tr'>
                                        <td colspan='4' class='defalttext td defaltfont' style='text-align: right; padding: 0 13px;'>মোট</td>
                                        <td class='td defaltfont'>" . int_en_to_bn(number_format($totalAmount, 2)) . "</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <p style='margin-top: 15px; padding: 0 15px;' class='defaltfont'>কথায় : {$amount}</p>
                        </div>
                    </div>
                    <div class='memofooter' style='margin-top: 25px;'>
                        <table width='100%'>
                            <tr>
                                <td>
                                    প্রশাসনিক কর্মকর্তা/আদায়কারীর স্বাক্ষর
                                    <br />
                                    তারিখ: " . int_en_to_bn(date('d/m/Y', strtotime($payment->date ?? now()))) . "
                                </td>
                                <td>
                                    <img src='https://api.qrserver.com/v1/create-qr-code/?data=" . url("/holding/tax/invoice/{$HoldingBokeya->id}") . "&size=80x80' />
                                </td>
                                <td style='text-align: right' width='130px'>
                                    " . (isUnion() ? 'ইউপি চেয়ারম্যানের স্বাক্ষর' : 'প্রশাসকের স্বাক্ষর') . "
                                </td>
                            </tr>
                        </table>
                        <p style='background-color: #7e7e7e; color: white; padding: 5px 10px; text-align: center;'>
                            এই রশিদ টি ইলেকট্রনিক ভাবে তৈরি হয়েছে,  কোন স্বাক্ষর প্রয়োজন নেই।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    ";

    return $output;
}
