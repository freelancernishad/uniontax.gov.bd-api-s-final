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

        /* Left and right columns */
        table.main-layout td.left-col,
        table.main-layout td.right-col {
            width: 49%;
        }

        /* Middle vertical line */
        table.main-layout td.divider {
            width: 2%;
            border-left: 2px solid #333;
        }

        /* Inner tables for data */
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

        /* Footer */
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
    </style>
</head>
<body>


        <div class="application-id">
        আবেদন নম্বর: {{ int_en_to_bn($row->application_id) }}
    </div>


    <table class="main-layout">
        <tr>
            <!-- Left Column -->
            <td class="left-col">
                <table class="inner-table">

                    <tr><td>আবেদনের তারিখ</td><td>:</td><td>{{ int_en_to_bn(date('d/m/Y', strtotime($row->created_at))) }}</td></tr>
                    <tr><td>আবেদনকারীর নাম</td><td>:</td><td>{{ $row->applicant_name_bn }} ({{ $row->applicant_name_en }})</td></tr>
                    <tr><td>পিতার নাম</td><td>:</td><td>{{ $row->applicant_father_name }}</td></tr>
                    <tr><td>মাতার নাম</td><td>:</td><td>{{ $row->applicant_mother_name }}</td></tr>
                    <tr><td>লিঙ্গ</td><td>:</td><td>{{ $row->applicant_gender }}</td></tr>
                    <tr><td>জন্ম তারিখ</td><td>:</td><td>{{ int_en_to_bn($row->applicant_date_of_birth) }}</td></tr>
                    <tr><td>ধর্ম</td><td>:</td><td>{{ $row->applicant_religion }}</td></tr>
                    <tr><td>জাতীয়তা</td><td>:</td><td>{{ $row->nationality }}</td></tr>
                    <tr><td>রক্তের গ্রুপ</td><td>:</td><td>{{ $row->blood_group }}</td></tr>
                    <tr><td>পেশা</td><td>:</td><td>{{ $row->profession }}</td></tr>
                    <tr><td>বৈবাহিক অবস্থা</td><td>:</td><td>{{ $row->marital_status }}</td></tr>
                    <tr><td>জাতীয় পরিচয়পত্র</td><td>:</td><td>{{ int_en_to_bn($row->applicant_nid_no) }}</td></tr>
                    <tr><td>মোবাইল</td><td>:</td><td>{{ int_en_to_bn($row->applicant_mobile) }}</td></tr>
                </table>
            </td>

            <!-- Middle Divider (Vertical Line) -->
            <td class="divider"></td>

            <!-- Right Column -->
            <td class="right-col">
                <table class="inner-table">
                    <tr>
                        <td>বর্তমান ঠিকানা</td>
                        <td>:</td>
                        <td>
                            {{ $row->applicant_address }},
                            উপজেলা: {{ $row->applicant_present_Upazila }},
                            জেলা: {{ $row->applicant_present_district }},
                            বিভাগ: {{ $row->current_division }}
                        </td>
                    </tr>
                    <tr>
                        <td>স্থায়ী ঠিকানা</td>
                        <td>:</td>
                        <td>
                            {{ $row->permanent_address }},
                            উপজেলা: {{ $row->applicant_permanent_Upazila }},
                            জেলা: {{ $row->applicant_permanent_district }},
                            বিভাগ: {{ $row->applicant_permanent_division }}
                        </td>
                    </tr>
                    <tr><td>বাইক ক্রয়ের তারিখ</td><td>:</td><td>{{ int_en_to_bn($row->auto_bike_purchase_date) }}</td></tr>
                    <tr><td>শেষ নবায়ন</td><td>:</td><td>{{ int_en_to_bn($row->auto_bike_last_renew_date) }}</td></tr>
                    <tr><td>সরবরাহকারীর নাম</td><td>:</td><td>{{ $row->auto_bike_supplier_name }}</td></tr>
                    <tr><td>সরবরাহকারীর মোবাইল</td><td>:</td><td>{{ int_en_to_bn($row->auto_bike_supplier_mobile) }}</td></tr>
                    <tr><td>সরবরাহকারীর ঠিকানা</td><td>:</td><td>{{ $row->auto_bike_supplier_address }}</td></tr>
                    <tr><td>রেজিস্ট্রেশন গ্রহণ স্থান</td><td>:</td><td>{{ $row->registration_place }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">জরুরি যোগাযোগ</div>
    <table class="inner-table" style="width: 100%; margin-top: 10px;">
        <tr><td>নাম</td><td>:</td><td>{{ $row->emergency_contact_name }}</td></tr>
        <tr><td>মোবাইল</td><td>:</td><td>{{ int_en_to_bn($row->emergency_contact_phone) }}</td></tr>
        <tr><td>সম্পর্ক</td><td>:</td><td>{{ $row->emergency_contact_relation }}</td></tr>
        <tr><td>জাতীয় পরিচয়পত্র</td><td>:</td><td>{{ int_en_to_bn($row->emergency_contact_national_id_number) }}</td></tr>
    </table>


</body>
</html>
