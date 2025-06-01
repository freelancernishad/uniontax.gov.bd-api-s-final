@php
$orthoBchor = explode('-',$row->orthoBchor);
@endphp
{{-- @php
$orthoBchor = explode('-',$row->orthoBchor);
@endphp

<p style="margin-bottom: 10px;">৩০ জুন, {{ int_en_to_bn('20'.$orthoBchor[1]) }} তারিখ পর্যন্ত বৈধ ফি প্রদানের পরিমাণ  {{ int_en_to_bn($row->total_amount) }}  টাকা  কথায়: {{ $row->the_amount_of_money_in_words }}   প্রাপ্ত হয়ে তার ব্যবসা/বৃত্তি/পেশা চালিয়ে যাওয়ার জন্য এই লাইসেন্স প্রদান করা হলো।<br>
</p> --}}




@if(isUnion())

<p style="margin-bottom: 10px;font-size:12px;text-align:justify">স্থানীয় সরকার (ইউনিয়ন পরিষদ) আইন, ২০০৯ (২০০৯ সনের ৬১ নং আইন) এর ধারা ৬৬ তে প্রদত্ত ক্ষমতাবলে সরকার প্রণীত আদর্শ কর তফসিল, ২০১৩ এর ৬ ও ১৭ নং অনুচ্ছেদ অনুযায়ী ব্যবসা, বৃত্তি, পেশা, বা শিল্প প্রতিষ্ঠানের উপর আরোপিত কর আদায়ের লক্ষ্যে নির্ধারিত শর্তে নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের অনুকূলে এই ট্রেড লাইসেন্সটি ইস্যু করা হলো: <br>
</p>

@else
<p style="margin-bottom: 10px;font-size:12px;text-align:justify">এই লাইসেন্সটি নিম্নবর্ণিত ব্যক্তি/প্রতিষ্ঠানের জন্য ইস্যু করা হলো, যা স্থানীয় সরকার (পৌরসভা) আইন, ২০০৯ এর ধারা ৯৮ অনুসারে সরকার কর্তৃক পৌরসভাগুলির জন্য প্রণীত আদর্শ কর তফসিল ২০১৪ এর ৬ নম্বর সূচি অনুযায়ী আরোপিত কর সংগ্রহের উদ্দেশ্যে। </p>

@endif




<table width="100%" style="margin-top:0px;font-size:12px">


        <tr>
            <td width="30%">প্রতিষ্ঠানের নাম</td><td>: {{ $row->applicant_name_of_the_organization }}</td>
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
            <td width="30%">ব্যবসার প্রকৃতি</td><td>: {{ $row->applicant_owner_type }}</td>
        </tr>

        <tr>
            <td width="30%">ব্যবসার ধরন</td><td>: {{ $row->applicant_type_of_business }}</td>
        </tr>

        <tr>
            <td width="30%">প্রতিষ্ঠানের ঠিকানা</td><td>: {{ $row->organization_address }}</td>
        </tr>

        <tr>
            <td width="30%">ওয়ার্ড নং</td><td>: {{ int_en_to_bn($row->applicant_present_word_number) }}</td>
        </tr>

        <tr>
            <td width="30%">জাতীয় পরিচয়পত্র নং</td><td>: {{ int_en_to_bn($row->applicant_national_id_number) }}</td>
        </tr>

        <tr>
            <td width="30%">অর্থবছর </td><td>: {{ int_en_to_bn($row->orthoBchor) }}</td>
        </tr>

    </table>



    <table width='100%' style="font-size: 12px">
        <tr>

            <td width='50%'>
                <p style='border-bottom:2px solid black;'>মালিক/স্বত্বাধিকারীর বর্তমান ঠিকানা</p>
                <ul style='list-style:none'>
                    <li>হোল্ডিং নং : {{ $row->applicant_holding_tax_number }}</li>
                    <li>রোড নং  : </li>
                    <li>গ্রাম/মহল্লা : {{ $row->applicant_present_village }}</li>
                    <li>ডাকঘর : {{ $row->applicant_present_post_office }}</li>
                    {{-- <li>পোস্ট কোড : {{ $row->applicant_present_village }}</li> --}}
                    <li>উপজেলা/থানা : {{ $row->applicant_present_Upazila }}</li>
                    <li>জেলা : {{ $row->applicant_present_district }}</li>
                </ul>
            </td>

        <td width='50%' align ="right">
                <p style='border-bottom:2px solid black;'>মালিক/স্বত্বাধিকারীর স্থায়ী ঠিকানা</p>
                <ul style='list-style:none'>
                    <li>হোল্ডিং নং : {{ $row->applicant_holding_tax_number }}</li>
                    <li>রোড নং  : </li>
                    <li>গ্রাম/মহল্লা : {{ $row->applicant_permanent_village }}</li>
                    <li>ডাকঘর : {{ $row->applicant_permanent_post_office }}</li>
                    {{-- <li>পোস্ট কোড : {{ $row->applicant_permanent_village }}</li> --}}
                    <li>উপজেলা/থানা : {{ $row->applicant_permanent_Upazila }}</li>
                    <li>জেলা : {{ $row->applicant_permanent_district }}</li>
                </ul>
            </td>


        </tr>
    </table>

    @php
    // Assuming the field name is correctly spelled as 'amount_details'
    $amount_deails = $row->amount_deails;

    // Decode the JSON data and ensure it returns an object
    $amount_deails = json_decode($amount_deails);

    // Check if the data was successfully decoded
    $tredeLisenceFee = $amount_deails->tredeLisenceFee ?? 0;
    $pesaKor = $amount_deails->pesaKor ?? 0;
    $signboard_fee = property_exists($amount_deails, 'signboard_fee') ? $amount_deails->signboard_fee : 0;

    if(isUnion()){

        $vatAykor = isset($amount_deails->vatAykor) ? ($tredeLisenceFee * $amount_deails->vatAykor) / 100 : 0;
        $aykorAndUtssoKor = 0;
    }else{

        $vatAykor = isset($amount_deails->vatAykor) ? ($pesaKor * $amount_deails->vatAykor) / 100 : 0;


        \Log::info("vatAykor: $vatAykor");
        $aykorAndUtssoKor = isset($amount_deails->aykorAndUtssoKor) ? $amount_deails->aykorAndUtssoKor : 1000;
        $aykorAndUtssoKorVatAykor = isset($amount_deails->vatAykor) ? ($aykorAndUtssoKor * $amount_deails->vatAykor) / 100 : 0;
        \Log::info("aykorAndUtssoKor: $aykorAndUtssoKor");

        $vatAykor = $vatAykor + $aykorAndUtssoKorVatAykor;

    }


    $totalAmount = ($row->currently_paid_money ?? 0) + ($amount_deails->last_years_money ?? 0) + ($vatAykor ?? 0) + ($aykorAndUtssoKor ?? 0);



    if($row->hasEnData){

        if(isUnion()){
            $totalAmount =  (int)$totalAmount - (int)$vatAykor;
        }
    }

    $totalAmount = int_en_to_bn($totalAmount ?? 0);

@endphp


    <table width='100%' style="font-size: 12px;margin-top:10px">
        <tr>
            <td width='50%'>

                <ul style='list-style:none'>
                    <li>ট্রেড লাইসেন্স ফি (নবায়ন) :-</li>
                    <li>পারমিট ফি  : {{ int_en_to_bn($tredeLisenceFee) }} টাকা</li>
                    <li>সার্ভিস চার্জ : ০.০০ টাকা</li>
                    <li>বকেয়া : {{ int_en_to_bn($amount_deails->last_years_money ?? 0) }} টাকা</li>
                    <li>সাবচার্জ  : ০.০০ টাকা</li>
                </ul>
            </td>

        <td width='50%' align ="right">

                <ul style='list-style:none'>
                    <li>পেশা ব্যবসা ও বৃত্তির উপর কর  :- {{ int_en_to_bn($pesaKor) }} টাকা</li>
                    <li>সাইনবোর্ড (পরিচিতিমূলক)  : {{ int_en_to_bn($signboard_fee) }} টাকা</li>
                    <li>আয়কর/উৎস কর : {{ int_en_to_bn($aykorAndUtssoKor) }} টাকা</li>
                    <li>ভ্যাট : {{ int_en_to_bn($vatAykor) }} টাকা</li>
                    <li>সংশোধন ফি : ০.০০ টাকা</li>
                </ul>
            </td>
        </tr>
    </table>

    <hr>

    <table width='100%' style="font-size: 12px">
        <tr>
            <td width='50%'>

                <b style="color:#159513">অত্র ট্রেড লাইসেন্স এর মেয়াদ : {{ int_en_to_bn("30-06-20".$orthoBchor[1]) }} পর্যন্ত </b>
            </td>

        <td width='50%' align ="right">

            @if($row->hasEnData==1)
            <b style="color:black">সর্বমোট : {{ int_en_to_bn($totalAmount ?? 0) }} টাকা মাত্র </b>
            @else
            <b style="color:black">সর্বমোট : {{ int_en_to_bn($totalAmount ?? 0) }} টাকা মাত্র </b>
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
