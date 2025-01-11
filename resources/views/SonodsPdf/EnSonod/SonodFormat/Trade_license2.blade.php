@php
$orthoBchor = explode('-',$row->orthoBchor);
@endphp
{{-- @php
$orthoBchor = explode('-',$row->orthoBchor);
@endphp

<p style="margin-bottom: 10px;">Valid until June 30, {{ int_en_to_bn('20'.$orthoBchor[1]) }}, the total amount of fees paid is {{ int_en_to_bn($row->total_amount) }} Taka, in words: {{ $row->the_amount_of_money_in_words }}. This license is issued to continue their business/occupation/profession.<br>
</p> --}}


<p style="margin-bottom: 10px;font-size:12px;text-align:justify">Under the authority granted by Section 66 of the Local Government (Union Parishad) Act, 2009 (Act No. 61 of 2009), and in accordance with Sections 6 and 17 of the Standard Tax Schedule, 2013, issued by the government for the purpose of collecting taxes imposed on businesses, professions, occupations, or industrial establishments, this trade license is issued to the following individual/organization under the specified conditions: <br>
</p>

<table width="100%" style="margin-top:0px;font-size:12px">


        <tr>
            <td width="30%">Name of the Organization</td><td>: {{ $row->applicant_name_of_the_organization }}</td>
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
            <td width="30%">Address of the Organization</td><td>: {{ $row->organization_address }}</td>
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
