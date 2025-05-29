@if($row->sonod_name == 'পারিবারিক সনদ')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
    }

    table td, table th {
        padding: 5px;
        border: 1px solid #000;
        vertical-align: top;
        text-align: left;
    }

    .no-border td {
        border: none;
        padding: 3px 5px;
    }

    .successor-table th {
        background-color: #f2f2f2;
        text-align: center;
    }

    .successor-table td {
        text-align: center;
    }

    p {
        font-size: 14px;
        margin: 8px 0;
    }
</style>
@endif

<table class="no-border">
    <tr>
        <td width="30%">সনদ নং</td>
        <td>: {{ int_en_to_bn($row->sonod_Id) }}</td>
    </tr>

    {{-- সনদধারীর নাম --}}
    <tr>
        <td>সনদধারীর নাম</td>
        <td>: 
            @if ($row->sonod_name == 'একই নামের প্রত্যয়ন' || $row->sonod_name == 'বিবিধ প্রত্যয়নপত্র')
                {{ $row->sameNameNew == 1 ? $row->utname : $row->applicant_name }}
            @else
                {{ $row->applicant_name }}
            @endif
        </td>
    </tr>

    {{-- পিতা/স্বামী ও মাতার নাম --}}
    @if ($row->sonod_name == 'বিবিধ প্রত্যয়নপত্র' || $row->sonod_name == 'একই নামের প্রত্যয়ন')
        @if($row->sameNameNew == 1)
            <tr><td>পিতা/স্বামীর নাম</td><td>: {{ $row->ut_father_name }}</td></tr>
            <tr><td>মাতার নাম</td><td>: {{ $row->ut_mother_name }}</td></tr>
        @else
            <tr><td>পিতা/স্বামীর নাম</td><td>: {{ $row->applicant_father_name }}</td></tr>
            <tr><td>মাতার নাম</td><td>: {{ $row->applicant_mother_name }}</td></tr>
        @endif
    @else
        <tr><td>পিতা/স্বামীর নাম</td><td>: {{ $row->applicant_father_name }}</td></tr>
        <tr><td>মাতার নাম</td><td>: {{ $row->applicant_mother_name }}</td></tr>
    @endif

    {{-- পেশা --}}
    @if ($row->sonod_name == 'বার্ষিক আয়ের প্রত্যয়ন')
        <tr><td>পেশা</td><td>: {{ $row->applicant_occupation }}</td></tr>
    @endif

    {{-- জাতীয় পরিচয়পত্র বা জন্ম নিবন্ধন --}}
    @php
        $hasNID = $row->applicant_national_id_number;
        $hasBRC = $row->applicant_birth_certificate_number;
    @endphp
    @if ($row->sonod_name == 'বিবিধ প্রত্যয়নপত্র' || $row->sonod_name == 'একই নামের প্রত্যয়ন')
        @if($row->sameNameNew != 1)
            <tr>
                <td>
                    {{ $hasNID ? 'জাতীয় পরিচয়পত্র নং' : 'জন্ম নিবন্ধন নং' }}
                </td>
                <td>: {{ int_en_to_bn($hasNID ?: $hasBRC) }}</td>
            </tr>
        @endif
    @else
        <tr>
            <td>{{ $hasNID ? 'জাতীয় পরিচয়পত্র নং' : 'জন্ম নিবন্ধন নং' }}</td>
            <td>: {{ int_en_to_bn($hasNID ?: $hasBRC) }}</td>
        </tr>
    @endif

    {{-- ঠিকানা --}}
    <tr>
        <td>ঠিকানা</td>
        <td>:
            @if($row->sameNameNew == 1 && ($row->sonod_name == 'বিবিধ প্রত্যয়নপত্র' || $row->sonod_name == 'একই নামের প্রত্যয়ন'))
                গ্রাম: {{ $row->ut_grame }}, ডাকঘর: {{ $row->ut_post }}, উপজেলা: {{ $row->ut_thana }}, জেলা: {{ $row->ut_district }}
            @else
                গ্রাম: {{ $row->applicant_present_village }}, ডাকঘর: {{ $row->applicant_present_post_office }}, উপজেলা: {{ $row->applicant_present_Upazila }}, জেলা: {{ $row->applicant_present_district }}
            @endif
        </td>
    </tr>

    {{-- ওয়ার্ড ও হোল্ডিং নং --}}
    @php
        $word = $row->sameNameNew == 1 ? $row->ut_word : $row->applicant_present_word_number;
        $holding = $row->applicant_holding_tax_number;
    @endphp

    @if($word)
        <tr><td>ওয়ার্ড নং</td><td>: {{ int_en_to_bn($word) }}</td></tr>
    @endif
    @if($holding && $row->sonod_name != 'বিবিধ প্রত্যয়নপত্র' && $row->sonod_name != 'একই নামের প্রত্যয়ন')
        <tr><td>হোল্ডিং নং</td><td>: {{ int_en_to_bn($holding) }}</td></tr>
    @endif
</table>

{{-- সনদের বর্ণনা --}}
<p>{!! int_en_to_bn($row->sec_prottoyon) !!}</p>

{{-- উত্তরাধিকারী তালিকা --}}
@if($row->sonod_name == 'পারিবারিক সনদ' && !empty(json_decode($row->successor_list, true)))
    <table class="successor-table">
        <thead>
            <tr>
                <th>ক্রমিক নং</th>
                <th>নাম</th>
                <th>জন্ম তারিখ</th>
                <th>বয়স</th>
                <th>সম্পর্ক</th>
            </tr>
        </thead>
        <tbody>
            @foreach(json_decode($row->successor_list, true) as $index => $successor)
                @php
                    $dob = $successor['w_age'] ?? null;
                    $age = '';
                    if ($dob && \Carbon\Carbon::hasFormat($dob, 'Y-m-d')) {
                        $birthDate = \Carbon\Carbon::parse($dob);
                        $now = \Carbon\Carbon::now();
                        $age = $birthDate->diff($now)->y;
                    }
                @endphp
                <tr>
                    <td>{{ int_en_to_bn($index + 1) }}</td>
                    <td>{{ $successor['w_name'] ?? '' }}</td>
                    <td>{{ int_en_to_bn($dob ?? 'উল্লেখ নেই') }}</td>
                    <td>{{ int_en_to_bn($age) }}</td>
                    <td>{{ $successor['w_relation'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>
        এই সনদপত্র আবেদনকারীর প্রদত্ত তথ্যের ভিত্তিতে প্রদান করা হয়েছে। অতএব, পরিবারের কোনো সদস্য সম্পর্কে ভুল বা ভ্রান্ত তথ্য প্রদান করা হলে তার সম্পূর্ণ দায়ভার আবেদনকারীর উপর বর্তাবে। অনুমোদনকারী এ বিষয়ে কোনো দায়িত্ব নেবেন না এবং ভুল তথ্য প্রদান করা হলে উক্ত সনদপত্র বাতিল বলে গণ্য হবে।
    </p>
@endif

{{-- টেমপ্লেট --}}
<p>{!! $Sonodnamelist->template !!}</p>
