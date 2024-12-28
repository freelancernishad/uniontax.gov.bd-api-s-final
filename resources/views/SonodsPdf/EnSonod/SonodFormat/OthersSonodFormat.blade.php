<table width="100%" style="margin-top:-40px">
    <tr>
        <td width="30%">Certificate No</td><td>: {{ $row->sonod_Id }}</td>
    </tr>

    @if ($row->sonod_name=='একই নামের প্রত্যয়ন' || $row->sonod_name=='বিবিধ প্রত্যয়নপত্র')
        @if($row->sameNameNew==1)
            <tr>
                <td width="30%">Certificate Holder's Name</td><td>: {{ $row->utname }}</td>
            </tr>
        @else
            <tr>
                <td width="30%">Certificate Holder's Name</td><td>: {{ $row->applicant_name }}</td>
            </tr>
        @endif
    @else
        <tr>
            <td width="30%">Certificate Holder's Name</td><td>: {{ $row->applicant_name }}</td>
        </tr>
    @endif

    @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
        @if($row->sameNameNew==1)
            <tr>
                <td width="30%">Father/Husband's Name</td><td>: {{ $row->ut_father_name }}</td>
            </tr>
            <tr>
                <td width="30%">Mother's Name</td><td>: {{ $row->ut_mother_name }}</td>
            </tr>
        @else
            <tr>
                <td width="30%">Father/Husband's Name</td><td>: {{ $row->applicant_father_name }}</td>
            </tr>
            <tr>
                <td width="30%">Mother's Name</td><td>: {{ $row->applicant_mother_name }}</td>
            </tr>
        @endif
    @else
        <tr>
            <td width="30%">Father/Husband's Name</td><td>: {{ $row->applicant_father_name }}</td>
        </tr>
        <tr>
            <td width="30%">Mother's Name</td><td>: {{ $row->applicant_mother_name }}</td>
        </tr>
    @endif

    @if($row->sonod_name=='বার্ষিক আয়ের প্রত্যয়ন')
        <tr>
            <td width="30%">Occupation</td><td>: {{ $row->applicant_occupation }}</td>
        </tr>
    @endif

    @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
        @if($row->sameNameNew==1)
        @else
            @if($row->applicant_national_id_number)
                <tr>
                    <td width="30%">National ID No</td>
                    <td>: {{ $row->applicant_national_id_number }}</td>
                </tr>
            @else
                <tr>
                    <td>Birth Registration No</td>
                    <td>: {{ $row->applicant_birth_certificate_number }}</td>
                </tr>
            @endif
        @endif
    @else
        @if($row->applicant_national_id_number)
            <tr>
                <td width="30%">National ID No</td>
                <td>: {{ $row->applicant_national_id_number }}</td>
            </tr>
        @else
            <tr>
                <td>Birth Registration No</td>
                <td>: {{ $row->applicant_birth_certificate_number }}</td>
            </tr>
        @endif
    @endif

    @if($row->sameNameNew==1)
        @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
            <tr>
                <td width="30%">Address</td>
                <td>: Village: {{ $row->ut_grame }}, Post Office: {{ $row->ut_post }}, Upazila: {{ $row->ut_thana }}, District: {{ $row->ut_district }}</td>
            </tr>
        @else
            <tr>
                <td width="30%">Address</td><td>: Village: {{ $row->applicant_present_village }}, Post Office: {{ $row->applicant_present_post_office }}, Upazila: {{ $row->applicant_present_Upazila }}, District: {{ $row->applicant_present_district }}</td>
            </tr>
        @endif
    @else
        <tr>
            <td width="30%">Address</td><td>: Village: {{ $row->applicant_present_village }}, Post Office: {{ $row->applicant_present_post_office }}, Upazila: {{ $row->applicant_present_Upazila }}, District: {{ $row->applicant_present_district }}</td>
        </tr>
    @endif

    @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
        @if($row->sameNameNew==1)
            @if($row->ut_word)
                <tr>
                    <td width="30%">Ward No</td><td>: {{ $row->ut_word }}</td>
                </tr>
            @endif
        @else
            @if($row->applicant_present_word_number)
                <tr>
                    <td width="30%">Ward No</td><td>: {{ $row->applicant_present_word_number }}</td>
                </tr>
            @endif
        @endif
    @else
        @if($row->applicant_present_word_number)
            <tr>
                <td width="30%">Ward No</td><td>: {{ $row->applicant_present_word_number }}</td>
            </tr>
        @endif
        @if($row->applicant_holding_tax_number)
            <tr>
                <td width="30%">Holding No</td><td>: {{ $row->applicant_holding_tax_number }}</td>
            </tr>
        @endif
    @endif
</table>

<p>{!! $row->sec_prottoyon !!}<br></p>

<p style="margin-bottom: 6px;">{!! $Sonodnamelist->template !!}<br></p>
