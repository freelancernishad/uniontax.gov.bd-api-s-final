
@if ($row->sonod_name=='পারিবারিক সনদ')
<style>
.applicantDetailsTable tr td ,.detailsfontSize{

    font-size: 12px;
}

    .no-border td {
        border: none;
        padding: 3px 5px;
    }




</style>
@else
<style>
.applicantDetailsTable tr td ,.detailsfontSize{

    font-size: 13px;
}
</style>
@endif

<table width="100%" style="margin-top:-40px" class="applicantDetailsTable">
    <tr>
        <td width="30%">Certificate No</td><td>: {{ $sonod_id }}</td>
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



<p class="detailsfontSize">{{ $row->sec_prottoyon }}</p>


@php
    $successors = json_decode($row->successor_list, true);
@endphp

@if($row->sonod_name=='পারিবারিক সনদ')


    @if(!empty($successors))
        <table border="0" width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; margin-top: 10px; font-size: 12px;border: none">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Age</th>
                    <th>Relation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($successors as $successor)
                    @php
                        $dob = isset($successor['w_age']) ? $successor['w_age'] : null;
                        $age = '';

                        if ($dob && \Carbon\Carbon::hasFormat($dob, 'Y-m-d')) {
                            $birthDate = \Carbon\Carbon::parse($dob);
                            $now = \Carbon\Carbon::now();
                            $diff = $birthDate->diff($now);

                            // if ($diff->y > 0) {
                            //     $age = "{$diff->y} years {$diff->m} months {$diff->d} days";
                            // } elseif ($diff->m > 0) {
                            //     $age = "{$diff->m} months {$diff->d} days";
                            // } else {
                            //     $age = "{$diff->d} days";
                            // }

                                $age = "{$diff->y}";

                        }
                    @endphp
                                    <tr>
                        <td>{{ $successor['w_name'] ?? '' }}</td>
                        <td>{{ $dob ?? 'N/A' }}</td>
                        <td>{{ $age }}</td>
                        <td>{{ $successor['w_relation'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style=" font-size: 13px;">The certificate is provided based on the information provided by claimant. Thus, the claimant shall be solely responsible any misinformation or misrepresentation of any family members and the approver shall not be liable for any misinformation and the certificate shall be considered as abandoned.</p>



    @endif


@endif




<p class="detailsfontSize" style="margin-bottom: 6px;">&nbsp; &nbsp; &nbsp; I wish for his/her overall progress and well-being in future life.<br></p>
{{-- <p style="margin-bottom: 6px;">{!! $Sonodnamelist->template !!}<br></p> --}}
