<table width="100%" style="margin-top:-40px">
    <tr>
        <td width="30%">লাইসেন্স নং</td><td>: {{ int_en_to_bn($row->sonod_Id) }}</td>
        </tr>

        <tr>
        <td width="30%">প্রতিষ্ঠানের নাম</td><td>: {{ $row->applicant_name_of_the_organization }}</td>
        </tr>

        <tr>
        <td width="30%">প্রতিষ্ঠানের ঠিকানা</td><td>: {{ $row->organization_address }}</td>
        </tr>

        <tr>
        <td width="30%">ব্যবসার ধরন</td><td>: {{ $row->applicant_type_of_business }}</td>
        </tr>

        <tr>
        <td width="30%">প্রতিষ্ঠানের মালিকানার ধরণ</td><td>: {{ $row->applicant_owner_type }}</td>
        </tr>

        <tr>
        <td width="30%">লাইসেন্সধারীর নাম</td><td>: {{ $row->applicant_name }}</td>
        </tr>
        <tr>
        <td width="30%">পিতা/স্বামীর নাম</td><td>: {{ $row->applicant_father_name }}</td>
        </tr>
        <tr>
        <td width="30%">মাতার নাম</td><td>: {{ $row->applicant_mother_name }}</td>
        </tr>
        <tr>
        <td width="30%">জাতীয় পরিচয়পত্র নং</td><td>: {{ int_en_to_bn($row->applicant_national_id_number) }}</td>
        </tr>
        <tr>
        <td width="30%">ঠিকানা</td><td>: {{ $row->applicant_present_village }}, {{ $row->applicant_present_post_office }}, {{ $row->applicant_present_Upazila }}, {{ $row->applicant_present_district }}</td>
        </tr>
        {{-- <tr>
        <td width="30%">পেশার ধরন</td><td>: {{ $row->applicant_occupation }}</td>
        </tr> --}}
    </table>

    @php
        $orthoBchor = explode('-',$row->orthoBchor);
    @endphp

    <p style="margin-bottom: 10px;">৩০ জুন, {{ int_en_to_bn('20'.$orthoBchor[1]) }} তারিখ পর্যন্ত বৈধ ফি প্রদানের পরিমাণ  {{ int_en_to_bn($row->total_amount) }}  টাকা  কথায়: {{ $row->the_amount_of_money_in_words }}   প্রাপ্ত হয়ে তার ব্যবসা/বৃত্তি/পেশা চালিয়ে যাওয়ার জন্য এই লাইসেন্স প্রদান করা হলো।<br>
    </p>

{{--
    <p style="margin-bottom: 10px;"> {!! int_en_to_bn($row->sec_prottoyon) !!}<br>
    </p> --}}

    {{-- <p style="margin-bottom: 10px;"
    > {!! $sonod->template  !!}<br>
    </p> --}}

    {{-- <p style="margin-bottom: 10px;"
    >৩০ জুন, ২০২৩ তারিখ পর্যন্ত বৈধ ফি প্রদানের পরিমাণ  {{ int_en_to_bn($row->total_amount) }} টাকা  কথায়: {{ $row->the_amount_of_money_in_words }}  প্রাপ্ত হয়ে তার ব্যবসা/বৃত্তি/পেশা চালিয়ে যাওয়ার জন্য এই লাইসেন্স প্রদান করা হলো।<br>
    </p> --}}
