@php
$orthoBchor = explode('-', $row->orthoBchor);
@endphp

<p style="margin-bottom: 10px;font-size:12px;text-align:justify">
    Under the authority granted by Section 66 of the Local Government (Union Parishad) Act, 2009 (Act No. 61 of 2009), and in accordance with Sections 6 and 17 of the Standard Tax Schedule, 2013, issued by the government for the purpose of collecting taxes imposed on businesses, professions, trades, or industrial establishments, this Trade License is issued to the following individual/establishment under the specified conditions: <br>
</p>

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
        <td width="30%">Ward No.</td><td>: {{ int_en_to_bn($row->applicant_present_word_number) }}</td>
    </tr>
    <tr>
        <td width="30%">National ID No.</td><td>: {{ int_en_to_bn($row->applicant_national_id_number) }}</td>
    </tr>
    <tr>
        <td width="30%">Fiscal Year</td><td>: {{ int_en_to_bn($row->orthoBchor) }}</td>
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
$amount_details = $amount_details;
$amount_details = json_decode($amount_details);
$tradeLicenseFee = $amount_details->tradeLicenseFee;
$vatAykor = ($tradeLicenseFee * $amount_details->vatAykor) / 100;
@endphp

<table width='100%' style="font-size: 12px;margin-top:10px">
    <tr>
        <td width='50%'>
            <ul style='list-style:none'>
                <li>Trade License Fee (Renewal):</li>
                <li>Permit Fee: {{ int_en_to_bn($tradeLicenseFee) }} Taka</li>
                <li>Service Charge: 0.00 Taka</li>
                <li>Arrears: {{ int_en_to_bn($amount_details->last_years_money) }} Taka</li>
                <li>Subcharge: 0.00 Taka</li>
            </ul>
        </td>
        <td width='50%' align="right">
            <ul style='list-style:none'>
                <li>Tax on Profession, Business, and Trade: {{ int_en_to_bn($amount_details->pesaKor) }} Taka</li>
                <li>Signboard (Identification): 0.00 Taka</li>
                <li>Income Tax/Source Tax: 0.00 Taka</li>
                <li>VAT: {{ int_en_to_bn($vatAykor) }} Taka</li>
                <li>Amendment Fee: 0.00 Taka</li>
            </ul>
        </td>
    </tr>
</table>

<hr>

<table width='100%' style="font-size: 12px">
    <tr>
        <td width='50%'>
            <b style="color:#159513">Validity of this Trade License: Until {{ int_en_to_bn("30-06-20" . $orthoBchor[1]) }}</b>
        </td>
        <td width='50%' align="right">
            <b style="color:black">Total Amount: {{ int_en_to_bn($row->total_amount) }} Taka Only</b>
        </td>
    </tr>
</table>
