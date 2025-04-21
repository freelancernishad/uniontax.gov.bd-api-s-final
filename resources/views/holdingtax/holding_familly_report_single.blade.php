<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সমন্বিত সামাজিক নিরাপত্তা বেষ্টনী সিস্টেম</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {

            font-size: 14px;
            color: #1e293b;
            margin: 0;
            padding: 40px 0;
            background-color: #f1f5f9;
            line-height: 1.5;
        }

        .document {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(135deg, transparent 25%, rgba(255,255,255,0.2) 50%, transparent 75%);
            background-size: 20px 20px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 18px;
            font-weight: 500;
            margin: 0;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            position: relative;
        }

        .section-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 80px;
            height: 2px;
            background: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px 0;
            font-size: 13.5px;
        }

        th {


            font-weight: 600;
            text-align: left;
            padding: 10px 15px;
        }

        td {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .info-table {
            border-radius: 6px;
            overflow: hidden;
        }

        .info-table th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            width: 25%;
        }

        .member-table {
            margin-bottom: 30px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .member-header {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
        }

        .member-row {
            background-color: white;
        }

        .member-row:nth-child(even) {
            background-color: #f8fafc;
        }

        .support-header {
            background-color: #16a34a;
            color: white;
        }

        .support-row {
            background-color: #f0fdf4;
        }

        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .highlight {
            background-color: #fffbeb;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .no-support {
            text-align: center;
            padding: 15px;
            color: #64748b;
            font-style: italic;
            background-color: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <h1>সমন্বিত সামাজিক নিরাপত্তা বেষ্টনী সিস্টেম</h1>
            <h2>পরিবারভিত্তিক উপকারভোগী তথ্য</h2>
        </div>

        <div class="content">
            <div class="section">
                <div class="section-title">হোল্ডিং কর বিবরণী</div>
                <table class="info-table">
                    <tr>
                        <th>হোল্ডিং নম্বর</th>
                        <td>{{ $holding->holding_no }}</td>
                        <th>শ্রেণি</th>
                        <td>{{ $holding->category }}</td>
                    </tr>
                    <tr>
                        <th>ইউনিয়ন</th>
                        <td>{{ $holding->unioun }}</td>
                        <th>মালিকের নাম</th>
                        <td>{{ $holding->maliker_name }}</td>
                    </tr>
                    <tr>
                        <th>পিতা/স্বামীর নাম</th>
                        <td>{{ $holding->father_or_samir_name }}</td>
                        <th>গ্রাম</th>
                        <td>{{ $holding->gramer_name }}</td>
                    </tr>
                    <tr>
                        <th>ওয়ার্ড নং</th>
                        <td>{{ $holding->word_no }}</td>
                        <th>মোবাইল</th>
                        <td>{{ $holding->mobile_no }}</td>
                    </tr>
                    <tr>
                        <th>জাতীয় পরিচয়পত্র</th>
                        <td>{{ $holding->nid_no }}</td>
                        <th>ব্যবসার নাম</th>
                        <td>{{ $holding->busnessName }}</td>
                    </tr>
                    <tr class="highlight">
                        <th>গৃহের বার্ষিক মূল্য</th>
                        <td>{{ $holding->griher_barsikh_mullo }}</td>
                        <th>মূল্যের শতাংশ</th>
                        <td>{{ $holding->barsikh_muller_percent }}%</td>
                    </tr>
                    <tr>
                        <th>জমির ভাড়া</th>
                        <td>{{ $holding->jomir_vara }}</td>
                        <th>মোট মূল্য</th>
                        <td>{{ $holding->total_mullo }}</td>
                    </tr>
                    <tr>
                        <th>রক্ষণাবেক্ষণ খরচ</th>
                        <td>{{ $holding->rokhona_bekhon_khoroch }}</td>
                        <th>প্রক্কলিত মূল্য</th>
                        <td>{{ $holding->prakklito_mullo }}</td>
                    </tr>
                    <tr class="highlight">
                        <th>রেয়াত</th>
                        <td>{{ $holding->reyad }}</td>
                        <th>অংশীক প্রদেয় করযোগ্য বার্ষিক মূল্য</th>
                        <td>{{ $holding->angsikh_prodoy_korjoggo_barsikh_mullo }}</td>
                    </tr>
                    <tr>
                        <th>বার্ষিক ভাড়া</th>
                        <td>{{ $holding->barsikh_vara }}</td>
                        <th>রক্ষণাবেক্ষণ খরচের শতাংশ</th>
                        <td>{{ $holding->rokhona_bekhon_khoroch_percent }}%</td>
                    </tr>
                    <tr class="highlight">
                        <th>প্রদেয় করযোগ্য বার্ষিক মূল্য</th>
                        <td>{{ $holding->prodey_korjoggo_barsikh_mullo }}</td>
                        <th>প্রদেয় করযোগ্য বার্ষিক ভাড়ার মূল্য</th>
                        <td>{{ $holding->prodey_korjoggo_barsikh_varar_mullo }}</td>
                    </tr>
                    <tr>
                        <th>মোট প্রদেয় করযোগ্য বার্ষিক মূল্য</th>
                        <td>{{ $holding->total_prodey_korjoggo_barsikh_mullo }}</td>
                        <th>বর্তমান বছরের কর</th>
                        <td style="font-weight: 600; color: #dc2626;">{{ $holding->current_year_kor }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">পরিবারের সদস্যদের তথ্য</div>

                @foreach($holding->familyMembers as $member)
                <table class="member-table">
                    <tr class="member-header">
                        <th colspan="2">সদস্য তথ্য</th>
                        <th colspan="5">বিস্তারিত</th>
                    </tr>
                    <tr class="member-row">
                        <th>নাম</th>
                        <td colspan="6" style="font-size: 16px;">{{ $member->name }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>সম্পর্ক</th>
                        <td colspan="6">{{ $member->relation }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>বয়স</th>
                        <td colspan="6">{{ $member->age }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>লিঙ্গ</th>
                        <td colspan="6">{{ $member->gender }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>এনআইডি</th>
                        <td colspan="6">{{ $member->nid_no }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>জন্ম সনদ</th>
                        <td colspan="6">{{ $member->birth_certificate_no }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>মোবাইল</th>
                        <td colspan="6">{{ $member->mobile_no }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>পেশা</th>
                        <td colspan="6">{{ $member->occupation }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>শিক্ষা</th>
                        <td colspan="6">{{ $member->education }}</td>
                    </tr>
                    <tr class="member-row">
                        <th>প্রতিবন্ধিতা</th>
                        <td colspan="6">
                            @if($member->disability)
                                <span class="status status-active">হ্যাঁ</span>
                            @else
                                <span class="status">না</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="support-header">
                        <th>সহায়তার তথ্য</th>
                        <th>ধরণ</th>
                        <th>কার্ড নম্বর</th>
                        <th>শুরু</th>
                        <th>শেষ</th>
                        <th>স্ট্যাটাস</th>
                        <th>মন্তব্য</th>
                    </tr>

                    @if($member->sohayotas->count() > 0)
                        @foreach($member->sohayotas as $support)
                        <tr class="support-row">
                            <td></td>
                            <td>{{ $support->sohayota_type }}</td>
                            <td>{{ $support->card_number }}</td>
                            <td>{{ $support->start_date }}</td>
                            <td>{{ $support->end_date }}</td>
                            <td>
                                @if($support->status === 'active')
                                    <span class="status status-active">চলমান</span>
                                @else
                                    <span class="status status-inactive">বন্ধ</span>
                                @endif
                            </td>
                            <td>{{ $support->remarks }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="no-support">এ সদস্যের জন্য কোনো সহায়তা পাওয়া যায়নি</td>
                        </tr>
                    @endif
                </table>
                @endforeach
            </div>
        </div>

        <div class="footer">
            <div>প্রতিবেদন তারিখ: {{ date('d-m-Y') }}</div>
            <div style="margin-top: 5px;">সমন্বিত সামাজিক নিরাপত্তা বেষ্টনী সিস্টেম | ইউনিয়ন ডিজিটাল সেন্টার</div>
        </div>
    </div>
</body>
</html>
