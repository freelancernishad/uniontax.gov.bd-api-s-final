<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8" />
    <title>অটো বাইক রেজিস্ট্রেশন</title>
    <style>
        body {
            font-family: 'bangla', sans-serif;
            margin: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table.main-layout {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-layout td {
            vertical-align: top;
            padding: 4px 10px;
        }

        table.inner-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.inner-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .section-title {
            margin-top: 30px;
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            background: #787878;
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 14px;
            margin-top: 40px;
        }

        .application-id {
            text-align: center;
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .notice {
            margin-top: 30px;
            font-size: 14px;
            background: #f9f9f9;
            padding: 10px;

        }
    </style>
</head>
<body>

    <!-- আবেদন নম্বর -->
    <div class="application-id">
        আবেদন নম্বর: {{ int_en_to_bn($row->application_id) }}
    </div>

    <!-- আবেদনকারীর তথ্য -->
    <table class="main-layout">
        <tr>
            <td>
                <table class="inner-table">
      
                    <tr>
                        <td>আবেদনকারীর নাম</td>
                        <td>:</td>
                        <td>{{ $row->applicant_name_bn }} ({{ $row->applicant_name_en }})</td>
                    </tr>
                    <tr>
                        <td>পিতা/স্বামী নাম</td>
                        <td>:</td>
                        <td>{{ $row->applicant_father_name }}</td>
                    </tr>
                    <tr>
                        <td>ঠিকানা</td>
                        <td>:</td>
                        <td>
                            {{ $row->applicant_address }},
                            উপজেলা: {{ $row->applicant_present_Upazila }},
                            জেলা: {{ $row->applicant_present_district }},
                            বিভাগ: {{ $row->current_division }}
                        </td>
                    </tr>
                    <tr>
                        <td>হোল্ডিং নম্বর</td>
                        <td>:</td>
                        <td>{{ $row->holding_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>যোগাযোগ নম্বর</td>
                        <td>:</td>
                        <td>{{ int_en_to_bn($row->applicant_mobile) }}</td>
                    </tr>
                    <tr>
                        <td>আবেদন প্রকার</td>
                        <td>:</td>
                        <td>{{ $row->application_type == 'new' ? 'নতুন নিবন্ধন' : 'নিবন্ধন নবায়ন' }}</td>
                    </tr>
                    <tr>
                        <td>ফি জমার তারিখ</td>
                        <td>:</td>
                        <td>
                            {{ isset($row->created_at) ? int_en_to_bn(date('d/m/Y', strtotime($row->created_at))) : '--/--/----' }},

                        </td>
                    </tr>
                    {{-- <tr>
                        <td>জমা দেওয়া নথি</td>
                        <td>:</td>
                        <td>
                            @php
                                $documents = explode(',', $row->submitted_documents ?? 'জাতীয় পরিচয়পত্র, ফর্ম-৬, ছবি');
                            @endphp
                            <ul>
                                @foreach ($documents as $doc)
                                    <li>{{ trim($doc) }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr> --}}
                </table>

                <!-- বিঃদ্রঃ -->
                <div class="notice">
                    <strong>বি.দ্রঃ</strong>
                    <ul style="margin: 10px 0 0 20px; padding: 0; font-size: 14px;">
                        <li>এই স্বীকার পত্রটি গ্রহণযোগ্য পদচিহ্ন হিসেবে থাকবে আপনার আবেদন প্রক্রিয়া সম্পন্ন না হওয়া পর্যন্ত।</li>
                        <li>প্রয়োজনে এই কপি অন্য কাউকে জমা বা উপস্থাপন করতে পারবেন।</li>
                        <li>অনুগ্রহ করে এটি সংরক্ষণ করুন।</li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
