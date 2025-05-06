
<table width="100%" style="margin-top:-40px">
    <tr>
        <td width="30%">সনদ নং</td><td>: {{ int_en_to_bn($row->sonod_Id) }}</td>
        </tr>


        @if ($row->sonod_name=='একই নামের প্রত্যয়ন' || $row->sonod_name=='বিবিধ প্রত্যয়নপত্র')

            @if($row->sameNameNew==1)
                <tr>
                    <td width="30%">সনদধারীর নাম</td><td>: {{ $row->utname }}</td>
                </tr>

            @else
                <tr>
                    <td width="30%">সনদধারীর নাম</td><td>: {{ $row->applicant_name }}</td>
                </tr>

            @endif

        @else
        <tr>
            <td width="30%">সনদধারীর নাম</td><td>: {{ $row->applicant_name }}</td>
        </tr>

        @endif


        @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
            @if($row->sameNameNew==1)
            <tr>
                <td width="30%">পিতা/স্বামীর নাম</td><td>: {{ $row->ut_father_name }}</td>
                </tr>
                <tr>
                <td width="30%">মাতার নাম</td><td>: {{ $row->ut_mother_name }}</td>
                </tr>
            @else
            <tr>
                <td width="30%">পিতা/স্বামীর নাম</td><td>: {{ $row->applicant_father_name }}</td>
                </tr>
                <tr>
                <td width="30%">মাতার নাম</td><td>: {{ $row->applicant_mother_name }}</td>
                </tr>
            @endif

        @else
        <tr>
            <td width="30%">পিতা/স্বামীর নাম</td><td>: {{ $row->applicant_father_name }}</td>
            </tr>
            <tr>
            <td width="30%">মাতার নাম</td><td>: {{ $row->applicant_mother_name }}</td>
            </tr>
        @endif

        @if($row->sonod_name=='বার্ষিক আয়ের প্রত্যয়ন')
        <tr>
            <td width="30%">পেশা</td><td>: {{ $row->applicant_occupation }}</td>
            </tr>
        @endif


        @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
            @if($row->sameNameNew==1)
            @else
                @if($row->applicant_national_id_number)
                <tr>
                    <td width="30%">জাতীয় পরিচয়পত্র নং</td>
                    <td>: {{ int_en_to_bn($row->applicant_national_id_number) }}</td>
                </tr>
                @else
                <tr>
                    <td>জন্ম নিবন্ধন নং</td>
                    <td>: {{ int_en_to_bn($row->applicant_birth_certificate_number) }}</td>
                </tr>
                @endif
            @endif
        @else

            @if($row->applicant_national_id_number)
            <tr>
                <td width="30%">জাতীয় পরিচয়পত্র নং</td>
                <td>: {{ int_en_to_bn($row->applicant_national_id_number) }}</td>
            </tr>
            @else
            <tr>
                <td>জন্ম নিবন্ধন নং</td>
                <td>: {{ int_en_to_bn($row->applicant_birth_certificate_number) }}</td>
            </tr>
            @endif
        @endif






        @if($row->sameNameNew==1)
            @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
                <tr>
                    <td width="30%">ঠিকানা</td>
                    <td>: গ্রাম: {{ $row->ut_grame }}, ডাকঘর: {{ $row->ut_post }}, উপজেলা: {{ $row->ut_thana }} , জেলা: {{ $row->ut_district }}</td>
                </tr>
            @else
                 <tr>
                    <td width="30%">ঠিকানা</td><td>:  গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }}, জেলা: {{ $row->applicant_present_district }}</td>
                </tr>
            @endif
        @else
        <tr>
        <td width="30%">ঠিকানা</td><td>:  গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }}, জেলা: {{ $row->applicant_present_district }}</td>
        </tr>
        @endif

        @if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র' || $row->sonod_name=='একই নামের প্রত্যয়ন')
            @if($row->sameNameNew==1)
                @if($row->ut_word)
                <tr>
                    <td width="30%">ওয়ার্ড নং</td><td>: {{ int_en_to_bn($row->ut_word) }}</td>
                </tr>
                @endif
            @else
                @if($row->applicant_present_word_number)
                <tr>
                    <td width="30%">ওয়ার্ড নং</td><td>: {{ int_en_to_bn($row->applicant_present_word_number) }}</td>
                </tr>
                @endif
            @endif
        @else
            @if($row->applicant_present_word_number)
            <tr>
                <td width="30%">ওয়ার্ড নং</td><td>: {{ int_en_to_bn($row->applicant_present_word_number) }}</td>
            </tr>
            @endif
            @if($row->applicant_holding_tax_number)
            <tr>
            <td width="30%">হোল্ডিং নং</td><td>: {{ int_en_to_bn($row->applicant_holding_tax_number) }}</td>
            </tr>
            @endif
        @endif






    </table>


    <p
    > {!! int_en_to_bn($row->sec_prottoyon) !!}<br>
    </p>

    <p style="margin-bottom: 6px;"
    > {!! $Sonodnamelist->template  !!}<br>
    </p>



{{--
<p style="margin-top:15px;margin-bottom:15px;font-size:15px;text-align:justify">&nbsp; &nbsp; &nbsp; এই মর্মে
    প্রত্যয়ন করা যাচ্ছে যে, {{ $row->applicant_name }}, পিতা/স্বামী: {{ $row->applicant_father_name }}, মাতা: {{ $row->applicant_mother_name }}, ওয়ার্ড নং- {{ $row->applicant_present_word_number }} হোল্ডিং নং- {{ $row->applicant_holding_tax_number }}, গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }} , জেলা: {{ $row->applicant_present_district }}।

     {{-- @if($row->sonod_name=='প্রত্যয়নপত্র' || $row->sonod_name=='বিবিধ প্রত্যয়নপত্র')  --}}

        {{-- {!! $row->sec_prottoyon !!} --}}

    {{-- <!-- @else --}}

        {{-- {!! $sonod->template  !!} --}}
    {{-- @endif --> --}}


{{-- </p> --}}
