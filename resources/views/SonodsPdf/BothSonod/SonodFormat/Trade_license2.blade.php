@php
$orthoBchor = explode('-',$rowbn->orthoBchor);
Log::info('orthoBchor', ['orthoBchor' => $orthoBchor]);
@endphp
{{-- @php
$orthoBchor = explode('-',$row->orthoBchor);
@endphp

<p style="margin-bottom: 10px;">৩০ জুন, {{ int_en_to_bn('20'.$orthoBchor[1]) }} তারিখ পর্যন্ত বৈধ ফি প্রদানের পরিমাণ  {{ int_en_to_bn($row->total_amount) }}  টাকা  কথায়: {{ $row->the_amount_of_money_in_words }}   প্রাপ্ত হয়ে তার ব্যবসা/বৃত্তি/পেশা চালিয়ে যাওয়ার জন্য এই লাইসেন্স প্রদান করা হলো।<br>
</p> --}}

{{-- <table width="100%" style="margin-bottom: 10px;font-size:12px;text-align:justify">
    <tr>
         <td width="50%" style="vertical-align: top;">
            <p style="font-size: 14px;">স্থানীয় সরকার (ইউনিয়ন পরিষদ) আইন, ২০০৯ (২০০৯ সনের ৬১ নং আইন) এর ধারা ৬৬ তে প্রদত্ত ক্ষমতাবলে সরকার প্রণীত আদর্শ কর তফসিল, ২০১৩ এর ৬ ও ১৭ নং অনুচ্ছেদ অনুযায়ী ব্যবসা, বৃত্তি, পেশা, বা শিল্প প্রতিষ্ঠানের উপর আরোপিত কর আদায়ের লক্ষ্যে নির্ধারিত শর্তে নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের অনুকূলে এই ট্রেড লাইসেন্সটি ইস্যু করা হলো: <br></p>
        </td>
        <td width="100%" style="vertical-align: top;">
            <p style="font-size: 10px;">Under the authority granted by Section 66 of the Local Government (Union Parishad) Act, 2009 (Act No. 61 of 2009), and in accordance with Sections 6 and 17 of the Standard Tax Schedule, 2013, issued by the government for the purpose of collecting taxes imposed on businesses, professions, trades, or industrial establishments, this Trade License is issued to the following individual/establishment under the specified conditions: <br></p>
        </td>

        <td width="100%" style="vertical-align: top;">
            <p style="font-size: 14px;">স্থানীয় সরকার (ইউনিয়ন পরিষদ) আইন, ২০০৯ (২০০৯ সনের ৬১ নং আইন) এর ধারা ৬৬ তে প্রদত্ত ক্ষমতাবলে সরকার প্রণীত আদর্শ কর তফসিল, ২০১৩ এর ৬ ও ১৭ নং অনুচ্ছেদ অনুযায়ী ব্যবসা, বৃত্তি, পেশা, বা শিল্প প্রতিষ্ঠানের উপর আরোপিত কর আদায়ের লক্ষ্যে নির্ধারিত শর্তে নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের অনুকূলে এই ট্রেড লাইসেন্সটি ইস্যু করা হলো: <br></p>
        </td>

    </tr>
</table> --}}





@if(isUnion())

<p style="font-size: 14px;text-align:justify">স্থানীয় সরকার (ইউনিয়ন পরিষদ) আইন, ২০০৯ (২০০৯ সনের ৬১ নং আইন) এর ধারা ৬৬ তে প্রদত্ত ক্ষমতাবলে সরকার প্রণীত আদর্শ কর তফসিল, ২০১৩ এর ৬ ও ১৭ নং অনুচ্ছেদ অনুযায়ী ব্যবসা, বৃত্তি, পেশা, বা শিল্প প্রতিষ্ঠানের উপর আরোপিত কর আদায়ের লক্ষ্যে নির্ধারিত শর্তে নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের অনুকূলে এই ট্রেড লাইসেন্সটি ইস্যু করা হলো: </p>

<p style="font-size: 10px;text-align:justify">
    Under the authority granted by Section 66 of the Local Government (Union Parishad) Act, 2009 (Act No. 61 of 2009), and in accordance with Sections 6 and 17 of the Standard Tax Schedule, 2013, issued by the government for the purpose of collecting taxes imposed on businesses, professions, trades, or industrial establishments, this Trade License is issued to the following individual/establishment under the specified conditions:
</p>

@else
<p style="font-size: 14px;text-align:justify">এই লাইসেন্সটি নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের জন্য ইস্যু করা হলো, যা স্থানীয় সরকার (পৌরসভা) আইন, ২০০৯ এর ধারা ৯৮ অনুসারে সরকার কর্তৃক পৌরসভাগুলির জন্য প্রণীত আদর্শ কর তফসিল ২০১৪ এর ৬ নম্বর সূচি অনুযায়ী আরোপিত কর সংগ্রহের উদ্দেশ্যে। </p>

<p style="font-size: 10px;text-align:justify">
    This license is issued to the undermentioned person/institution for the collection of imposed tax according to Schedule 6 of the Model Tax Schedule 2014, enacted for municipalities by the government under Section 98 of the Local Government (Municipality) Act 2009.
</p>
@endif





<table width="100%" style="margin-top: 10px; font-size: 13px; border-collapse: collapse;">
    <thead style="border-bottom: 2px solid black;">
        <tr style="border-bottom:1px solid black;">
            <td style="width: 40%; border-bottom: 1px solid black;">বিষয় / <span style="font-size: 9px;">Subject</span></td>
            <td style="border-bottom: 1px solid black;">বাংলা</td>
            <td style="font-size: 9px; border-bottom: 1px solid black;">English</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>প্রতিষ্ঠানের নাম / <span style="font-size: 9px;">Name of Organization</span></td>
            <td>{{ $rowbn->applicant_name_of_the_organization }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_name_of_the_organization }}</span></td>
        </tr>
        <tr>
            <td>লাইসেন্সধারীর নাম / <span style="font-size: 9px;">Licensee Name</span></td>
            <td>{{ $rowbn->applicant_name }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_name }}</span></td>
        </tr>
        <tr>
            <td>পিতা/স্বামীর নাম / <span style="font-size: 9px;">Father’s or Husband’s Name</span></td>
            <td>{{ $rowbn->applicant_father_name }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_father_name }}</span></td>
        </tr>
        <tr>
            <td>মাতার নাম / <span style="font-size: 9px;">Mother’s Name</span></td>
            <td>{{ $rowbn->applicant_mother_name }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_mother_name }}</span></td>
        </tr>
        <tr>
            <td>ব্যবসার প্রকৃতি / <span style="font-size: 9px;">Nature of Business</span></td>
            <td>{{ $rowbn->applicant_owner_type }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_owner_type }}</span></td>
        </tr>
        <tr>
            <td>ব্যবসার ধরন / <span style="font-size: 9px;">Type of Business</span></td>
            <td>{{ $rowbn->applicant_type_of_business }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_type_of_business }}</span></td>
        </tr>
        <tr>
            <td>প্রতিষ্ঠানের ঠিকানা / <span style="font-size: 9px;">Organization Address</span></td>
            <td>{{ $rowbn->organization_address }}</td>
            <td><span style="font-size: 9px;">{{ $row->organization_address }}</span></td>
        </tr>

        <tr>
            <td>ওয়ার্ড নং / <span style="font-size: 9px;">Word No.</span></td>
            <td>{{ int_en_to_bn($row->organization_word_no ?? $row->applicant_present_word_number) }}</td>
            <td><span style="font-size: 9px;">{{ ($row->organization_word_no ?? $row->applicant_present_word_number) }}</span></td>
        </tr>

        <tr>
            <td>মোবাইল / <span style="font-size: 9px;">Mobile Number</span></td>
            <td>{{ int_en_to_bn($row->applicant_mobile) }}</td>
            <td><span style="font-size: 9px;">{{ ($row->applicant_mobile) }}</span></td>
        </tr>

        <tr>
            <td>জাতীয় পরিচয়পত্র নং / <span style="font-size: 9px;">NID No.</span></td>
            <td>{{ int_en_to_bn($rowbn->applicant_national_id_number) }}</td>
            <td><span style="font-size: 9px;">{{ $row->applicant_national_id_number }}</span></td>
        </tr>
        <tr>
            <td>অর্থবছর / <span style="font-size: 9px;">Fiscal Year</span></td>
            <td>{{ int_en_to_bn($rowbn->orthoBchor) }}</td>
            <td><span style="font-size: 9px;">{{ $row->orthoBchor }}</span></td>
        </tr>
    </tbody>
</table>

<table width="100%" style="font-size: 11px; border-collapse: collapse; margin-top: 10px;">
    <tr>
        <!-- Present Address -->
        <td width="50%" style="vertical-align: top; padding-right: 5px;">
            <p style="border-bottom:1px solid black; font-size: 12px;">
                <span style="font-size: 14px;">বর্তমান ঠিকানা</span> / <span style="font-size: 9px;">Present Address</span>
            </p>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td><span style="font-size: 14px;">হোল্ডিং নং</span> / Holding No.</td>
                    <td>: {{ $rowbn->applicant_holding_tax_number }} / {{ $row->applicant_holding_tax_number }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">রোড নং</span> / Road No.</td>
                    <td>: {{ $rowbn->applicant_present_road_no }} / {{ $row->applicant_present_road_no }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">গ্রাম/মহল্লা</span> / Village/Moholla</td>
                    <td>: {{ $rowbn->applicant_present_village }} / {{ $row->applicant_present_village }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">ডাকঘর</span> / Post Office</td>
                    <td>: {{ $rowbn->applicant_present_post_office }} / {{ $row->applicant_present_post_office }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">উপজেলা</span> / Upazila</td>
                    <td>: {{ $rowbn->applicant_present_Upazila }} / {{ $row->applicant_present_Upazila }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">জেলা</span> / District</td>
                    <td>: {{ $rowbn->applicant_present_district }} / {{ $row->applicant_present_district }}</td>
                </tr>
            </table>
        </td>

        <!-- Permanent Address -->
        <td width="50%" style="vertical-align: top; padding-left: 5px;">
            <p style="border-bottom:1px solid black; font-size: 12px;">
                <span style="font-size: 14px;">স্থায়ী ঠিকানা</span> / <span style="font-size: 9px;">Permanent Address</span>
            </p>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td><span style="font-size: 14px;">হোল্ডিং নং</span> / Holding No.</td>
                    <td>: {{ $rowbn->applicant_holding_tax_number }} / {{ $row->applicant_holding_tax_number }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">রোড নং</span> / Road No.</td>
                    <td>: {{ $rowbn->applicant_permanent_road_no }} / {{ $row->applicant_permanent_road_no }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">গ্রাম/মহল্লা</span> / Village/Moholla</td>
                    <td>: {{ $rowbn->applicant_permanent_village }} / {{ $row->applicant_permanent_village }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">ডাকঘর</span> / Post Office</td>
                    <td>: {{ $rowbn->applicant_permanent_post_office }} / {{ $row->applicant_permanent_post_office }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">উপজেলা</span> / Upazila</td>
                    <td>: {{ $rowbn->applicant_permanent_Upazila }} / {{ $row->applicant_permanent_Upazila }}</td>
                </tr>
                <tr>
                    <td><span style="font-size: 14px;">জেলা</span> / District</td>
                    <td>: {{ $rowbn->applicant_permanent_district }} / {{ $row->applicant_permanent_district }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>







    @php
    // Assuming the field name is correctly spelled as 'amount_details'
    $amount_deails = $rowbn->amount_deails;

    // Decode the JSON data and ensure it returns an object
    $amount_deails = json_decode($amount_deails);

    // Check if the data was successfully decoded
    $tredeLisenceFee = $amount_deails->tredeLisenceFee ?? 0;
    $vatAykor = isset($amount_deails->vatAykor) ? ($tredeLisenceFee * $amount_deails->vatAykor) / 100 : 0;
@endphp


<table width='100%' style="font-size: 10px;margin-top:10px">
    <tr>
        <td width='50%'>

            <ul style='list-style:none'>
                <li>Trade License Fee (Renewal) :-</li>
                <li>Permit Fee  : {{ $tredeLisenceFee }} Taka</li>
                <li>Service Charge : 0.00 Taka</li>
                <li>Due Amount : {{ $amount_deails->last_years_money ?? 0 }} Taka</li>
                <li>Subcharge  : 0.00 Taka</li>
            </ul>
        </td>

        <td width='50%' align="right">

            <ul style='list-style:none'>
                <li>Professional Tax :- {{ $amount_deails->pesaKor ?? 0 }} Taka</li>
                <li>Signboard (Identification)  : 0.00 Taka</li>
                <li>Income Tax/Source Tax : 0.00 Taka</li>
                <li>VAT : {{ $vatAykor }} Taka</li>
                <li>Correction Fee : 0.00 Taka</li>
            </ul>
        </td>
    </tr>
</table>



    <hr>

    <table width='100%' style="font-size: 10px;">
        <tr>
            <td width='50%'>

                <b style="color:#159513; font-size: 10px;">The validity of this Trade License is until: {{ ("30-06-20".$orthoBchor[1]) }} </b>
            </td>

            <td width='50%' align="right">

                @if($row->hasEnData==1)
                <b style="color:black; font-size: 10px;">Total Amount: {{ (($rowbn->currently_paid_money)+$amount_deails->last_years_money ?? 0) }} Taka Only </b>
                @else
                <b style="color:black; font-size: 10px;">Total Amount: {{ ($rowbn->currently_paid_money+$amount_deails->last_years_money ?? 0) }} Taka Only </b>
                @endif
            </td>
        </tr>
    </table>






    {{-- <tr>
        <td width="30%">ঠিকানা</td><td>: {{ $row->applicant_present_village }}, {{ $row->applicant_present_post_office }}, {{ $row->applicant_present_Upazila }}, {{ $row->applicant_present_district }}</td>
    </tr> --}}

{{--
    <p style="margin-bottom: 10px;"> {!! int_en_to_bn($row->sec_prottoyon) !!}<br>
    </p> --}}

    {{-- <p style="margin-bottom: 10px;"
    > {!! $sonod->template  !!}<br>
    </p> --}}

    {{-- <p style="margin-bottom: 10px;"
    >৩০ জুন, ২০২৩ তারিখ পর্যন্ত বৈধ ফি প্রদানের পরিমাণ  {{ int_en_to_bn($row->total_amount) }} টাকা  কথায়: {{ $row->the_amount_of_money_in_words }}  প্রাপ্ত হয়ে তার ব্যবসা/বৃত্তি/পেশা চালিয়ে যাওয়ার জন্য এই লাইসেন্স প্রদান করা হলো।<br>
    </p> --}}
