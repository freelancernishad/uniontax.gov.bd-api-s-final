
    @php
        $sonod_name = $row->sonod_name;
        if ($sonod_name == 'ওয়ারিশান সনদ') {
            $text = 'ওয়ারিশ/ওয়ারিশগণের নাম ও সম্পর্ক';
        } else {
            $text = 'উত্তরাধিকারীগণের নাম ও সম্পর্ক';
        }

        $EnsonodName = str_replace(" ", "_", $sonodnames->enname);

    @endphp

    <div>
        <h4 style="text-align:center;">{{ $text }}</h4>

        @if ($sonod_name == 'ওয়ারিশান সনদ')
            @if($row->ut_religion == 'ইসলাম')
                @php
                    $deathStatus = 'মরহুম';
                    $deathStatus2 = 'মরহুমের';
                @endphp
            @else
                @php
                    $deathStatus = 'স্বর্গীয়';
                    $deathStatus2 = 'স্বর্গীয় ব্যক্তির';
                @endphp
            @endif

            <p style="font-size:15px;text-align:justify">
                &nbsp;&nbsp;&nbsp; এই মর্মে প্রত্যয়ন করা যাচ্ছে যে,
                {{ $deathStatus }} {{ $row->utname }},
                পিতা/স্বামী- {{ $row->ut_father_name }},
                মাতা- {{ $row->ut_mother_name }},
                গ্রাম- {{ $row->ut_grame }},
                ডাকঘর- {{ $row->ut_post }},
                উপজেলা: {{ $row->ut_thana }},
                জেলা- {{ $row->ut_district }}।
                তিনি অত্র ইউনিয়নের {{ int_en_to_bn($row->ut_word) }} নং ওয়ার্ডের
                {{ $row->applicant_resident_status }} বাসিন্দা ছিলেন। মৃত্যুকালে তিনি নিম্নোক্ত ওয়ারিশগণ রেখে যান। নিম্নে তাঁর ওয়ারিশ/ওয়ারিশগণের নাম ও সম্পর্ক উল্লেখ করা হলো।
                <br><br>
                &nbsp;&nbsp;&nbsp; আমি {{ $deathStatus2 }} বিদেহী আত্মার মাগফেরাত কামনা করি।
            </p>

            @if($row->unioun_name == 'balarampur')
                <p style="font-size:14px;">বিঃদ্রঃ উক্ত ওয়ারিশান সনদের সকল দায়ভার সংশ্লিষ্ট ইউপি সদস্য/সদস্যার যাচাইকারীর উপর বর্তাইবে ।</p>
            @endif

        @else
            <p style="font-size:15px;text-align:justify">
                &nbsp;&nbsp;&nbsp; এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, জনাব
                {{ $row->utname }},
                পিতা/স্বামী- {{ $row->ut_father_name }},
                মাতা- {{ $row->ut_mother_name }},
                গ্রাম- {{ $row->ut_grame }},
                ডাকঘর- {{ $row->ut_post }},
                উপজেলা: {{ $row->ut_thana }},
                জেলা- {{ $row->ut_district }}।
                তিনি অত্র ইউনিয়নের {{ int_en_to_bn($row->ut_word) }} নং ওয়ার্ডের
                {{ $row->applicant_resident_status }} বাসিন্দা। নিম্নে তাঁর উত্তরাধিকারী/উত্তরাধিকারীগণের নাম ও সম্পর্ক উল্লেখ করা হলো।
                <br><br>
            </p>

            @if($row->unioun_name == 'balarampur')
                <p style="font-size:14px;">বিঃদ্রঃ উক্ত উত্তরাধিকারী সনদের সকল দায়ভার সংশ্লিষ্ট ইউপি সদস্য/সদস্যার যাচাইকারীর উপর বর্তাইবে ।</p>
            @endif
        @endif

        <h4 style="text-align:center;margin-bottom:0px">{{ $text }}</h4>

        <table class="table" style="width:100%;border-collapse: collapse;" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th style="border: 1px dotted black; padding: 4px 10px; font-size: 12px;" width="10%">ক্রমিক নং</th>
                    <th style="border: 1px dotted black; padding: 4px 10px; font-size: 12px;" width="30%">নাম</th>
                    <th style="border: 1px dotted black; padding: 4px 10px; font-size: 12px;" width="10%">সম্পর্ক</th>
                    <th style="border: 1px dotted black; padding: 4px 10px; font-size: 12px;" width="10%">বয়স</th>
                    <th style="border: 1px dotted black; padding: 4px 10px; font-size: 12px;" width="20%">জাতীয় পরিচয়পত্র নাম্বার/জন্মনিবন্ধন নাম্বার</th>
                </tr>
            </thead>
            <tbody>
                @foreach (json_decode($row->successor_list) as $index => $rowList)
                    <tr>
                        <td style="text-align:center; border: 1px dotted black; padding:4px 10px; font-size: 12px;">{{ int_en_to_bn($index + 1) }}</td>
                        <td style="text-align:center; border: 1px dotted black; padding:4px 10px; font-size: 12px;">{{ $rowList->w_name }}</td>
                        <td style="text-align:center; border: 1px dotted black; padding:4px 10px; font-size: 12px;">{{ $rowList->w_relation }}</td>
                        <td style="text-align:center; border: 1px dotted black; padding:4px 10px; font-size: 12px;">{{ int_en_to_bn($rowList->w_age) }}</td>
                        <td style="text-align:center; border: 1px dotted black; padding:4px 10px; font-size: 12px;">{{ int_en_to_bn($rowList->w_nid) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <p style="margin-top:-10px;margin-bottom:5px">
            আবেদনকারীর নামঃ {{ $row->applicant_name }}।  পিতা/স্বামীর নামঃ {{ $row->applicant_father_name }}।  মাতার নামঃ {{ $row->applicant_mother_name }}
        </p><br>

        <p style="margin-top:-10px;margin-bottom:5px">
            সংশ্লিষ্ট ওয়ার্ডের ইউপি সদস্য কর্তৃক আবেদনকারীর দাখিলকৃত তথ্য যাচাই/সত্যায়নের পরিপ্রেক্ষিতে অত্র সনদপত্র প্রদান করা হলো।
        </p> <br/>

        <p style="margin-top:-10px; margin-bottom:0px">
            &nbsp;&nbsp;&nbsp; আমি তাঁর/তাঁদের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করছি।
        </p>
    </div>

