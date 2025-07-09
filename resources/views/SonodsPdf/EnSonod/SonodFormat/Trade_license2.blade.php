@php
$orthoBchor = explode('-', $row->orthoBchor);
@endphp




@if(isUnion())

<p style="margin-bottom: 10px;font-size:12px;text-align:justify">
    Under the authority granted by Section 66 of the Local Government (Union Parishad) Act, 2009 (Act No. 61 of 2009), and in accordance with Sections 6 and 17 of the Standard Tax Schedule, 2013, issued by the government for the purpose of collecting taxes imposed on businesses, professions, trades, or industrial establishments, this Trade License is issued to the following individual/establishment under the specified conditions: <br>
</p>

@else

<p style="margin-bottom: 10px;font-size:12px;text-align:justify">
    This license is issued to the undermentioned person/institution for the collection of imposed tax according to Schedule 6 of the Model Tax Schedule 2014, enacted for municipalities by the government under Section 98 of the Local Government (Municipality) Act 2009.
</p>
@endif






<table width="100%" style="margin-top:0px;font-size:12px">
    <tr>
        <td width="30%">Name of the Establishment</td><td>: {{ $row->applicant_name_of_the_organization }}</td>
    </tr>
    <tr>
        <td width="30%">Name of the License Holder</td><td>: {{ $row->applicant_name }}</td>
    </tr>
    <tr>
        <td width="30%">Father's/Husband's Name</td><td>: {{ $row->applicant_father_name }}</td>
    </tr>
    <tr>
        <td width="30%">Mother's Name</td><td>: {{ $row->applicant_mother_name }}</td>
    </tr>
    <tr>
        <td width="30%">Nature of Business</td><td>: {{ $row->applicant_owner_type }}</td>
    </tr>
    <tr>
        <td width="30%">Type of Business</td><td>: {{ $row->applicant_type_of_business }}</td>
    </tr>
    <tr>
        <td width="30%">Address of the Establishment</td><td>: {{ $row->organization_address }}</td>
    </tr>
    <tr>
        <td width="30%">Ward No.</td><td>: {{ ($row->applicant_present_word_number) }}</td>
    </tr>
    <tr>
        <td width="30%">National ID No.</td><td>: {{ ($row->applicant_national_id_number) }}</td>
    </tr>
    <tr>
        <td width="30%">Fiscal Year</td><td>: {{ ($row->orthoBchor) }}</td>
    </tr>
</table>

<table width='100%' style="font-size: 12px">
    <tr>
        <td width='50%'>
            <p style='border-bottom:2px solid black;'>Current Address of the Owner/Proprietor</p>
            <ul style='list-style:none'>
                <li>Holding No.: {{ $row->applicant_holding_tax_number }}</li>
                <li>Road No.: </li>
                <li>Village/Mohalla: {{ $row->applicant_present_village }}</li>
                <li>Post Office: {{ $row->applicant_present_post_office }}</li>
                <li>Upazila/Thana: {{ $row->applicant_present_Upazila }}</li>
                <li>District: {{ $row->applicant_present_district }}</li>
            </ul>
        </td>
        <td width='50%' align="right">
            <p style='border-bottom:2px solid black;'>Permanent Address of the Owner/Proprietor</p>
            <ul style='list-style:none'>
                <li>Holding No.: {{ $row->applicant_holding_tax_number }}</li>
                <li>Road No.: </li>
                <li>Village/Mohalla: {{ $row->applicant_permanent_village }}</li>
                <li>Post Office: {{ $row->applicant_permanent_post_office }}</li>
                <li>Upazila/Thana: {{ $row->applicant_permanent_Upazila }}</li>
                <li>District: {{ $row->applicant_permanent_district }}</li>
            </ul>
        </td>
    </tr>
</table>
@php
    $amount_details = json_decode($amount_details);
    $tredeLisenceFee = $amount_details->tredeLisenceFee ?? 0;
    $pesaKor = $amount_details->pesaKor ?? 0;
    $signboard_fee = property_exists($amount_details, 'signboard_fee') ? $amount_details->signboard_fee : 0;

    if (isUnion()) {
        $vatAykor = isset($amount_details->vatAykor) ? ($tredeLisenceFee * $amount_details->vatAykor) / 100 : 0;
        $aykorAndUtssoKor = 0;
    } else {

        $vatAykor = isset($amount_details->vatAykor) ? ($pesaKor * $amount_details->vatAykor) / 100 : 0;
        $aykorAndUtssoKor = $amount_details->aykorAndUtssoKor ?? 1000;


        $signboard_feeVatAykor = isset($amount_deails->vatAykor) ? ($signboard_fee * $amount_deails->vatAykor) / 100 : 0;
        // $aykorAndUtssoKorVatAykor = isset($amount_deails->vatAykor) ? ($aykorAndUtssoKor * $amount_deails->vatAykor) / 100 : 0;
        $vatAykor = $vatAykor + $signboard_feeVatAykor;
    }

    $currentlyPaid = $amount_details->currently_paid_money ?? 0;
    $lastYearsMoney = $amount_details->last_years_money ?? 0;

    $totalAmount = $currentlyPaid + $lastYearsMoney + $vatAykor + $aykorAndUtssoKor;



       if($row->hasEnData==1){

        if(isUnion()){
            $totalAmount =  (int)$totalAmount - (int)$vatAykor;
        }
    }else{
        if(isUnion()){
            $totalAmount =  (int)$totalAmount - (int)$vatAykor;
        }
    }

        // if (isUnion()) {
        //     $totalAmount -= $vatAykor;
        // }

@endphp

<table width='100%' style="font-size: 12px;margin-top:10px">
    <tr>
        <td width='50%'>
            <ul style='list-style:none'>
                <li>Trade License Fee (Renewal):</li>
                <li>Permit Fee: {{ $tredeLisenceFee }} Taka</li>
                <li>Service Charge: 0.00 Taka</li>
                <li>Arrears: {{ $lastYearsMoney }} Taka</li>
                <li>Subcharge: 0.00 Taka</li>
            </ul>
        </td>
        <td width='50%' align="right">
            <ul style='list-style:none'>
                <li>Tax on Profession, Business, and Trade: {{ $pesaKor }} Taka</li>
                <li>Signboard (Identification): {{ $signboard_fee }} Taka</li>
                <li>Income Tax/Source Tax: {{ $aykorAndUtssoKor }} Taka</li>
                <li>VAT: {{ $vatAykor }} Taka</li>
                <li>Amendment Fee: 0.00 Taka</li>
            </ul>
        </td>
    </tr>
</table>

<hr>

<table width='100%' style="font-size: 12px">
    <tr>
        <td width='50%'>
            <b style="color:#159513">Validity of this Trade License: Until {{ "30-06-20" . $orthoBchor[1] }}</b>
        </td>
        <td width='50%' align="right">
            <b style="color:black">Total Amount: {{ $totalAmount }} Taka Only</b>
        </td>
    </tr>
</table>

