
<style>
    td{
        font-size:14px;
    }



</style>



                <table style="margin-top:20px">

                    <tr>
                        <td>সেবার ধরণ</td>
                        <td>:</td>
                        <td>{{ changeSonodName($row->sonod_name) }}</td>
                    </tr>

                    <tr>
                        <td>আবেদনের ক্রমিক নং</td>
                        <td>:</td>
                        <td>{{ int_en_to_bn($row->sonod_Id) }}</td>
                    </tr>
                    <tr>
                        <td>আবেদনকারীর নাম</td>
                        <td>:</td>
                        <td>{{ int_en_to_bn($row->applicant_name) }}</td>
                    </tr>

                    <tr>
                        <td>আবেদনের তারিখ</td>
                        <td>:</td>
                        <td>{{ int_en_to_bn(date("d/m/Y", strtotime($row->created_at))) }}</td>
                    </tr>


                    <tr>
                        @if($row->sonod_name=='উত্তরাধিকারী সনদ')
                        <td>ব্যাক্তির নাম</td>
                        @else
                        <td>মৃত ব্যাক্তির নাম</td>
                        @endif
                        <td>:</td>
                        <td>{{ $row->utname }}</td>
                    </tr>

                    <tr>

                        @if($row->sonod_name=='উত্তরাধিকারী সনদ')

                        <td>ব্যাক্তির ঠিকানা</td>
                        @else
                        <td>মৃত ব্যাক্তির ঠিকানা</td>
                        @endif


                        <td>:</td>
                        <td>গ্রামঃ {{ $row->ut_grame }} , ডাকঘরঃ {{ $row->ut_post }}, উপজেলাঃ {{ $row->ut_thana }}, জেলাঃ {{ $row->ut_district }} </td>
                    </tr>


                </table>
<style>
    .w_table tr,.w_table th,.w_table td{
        border: 1px solid;
        text-align:center;
    }

</style>
            @if($row->sonod_name=='উত্তরাধিকারী সনদ')
            <h3 style="text-align:center">উত্তরাধিকারীগনের নাম ও সম্পর্ক</h3>
            @else
            <h3 style="text-align:center">ওয়ারিশগনের নাম ও সম্পর্ক</h3>

            @endif

                <table class="w_table" width="100%" border="1px" style="border-collapse: collapse; border: 1px solid;">
                    <tr >
                        <th width="5%">ক্রমিক নং</th>
                        <th width="20%">নাম</th>
                        <th width="15%">সম্পর্ক</th>
                        <th width="20%">জন্ম তারিখ</th>
                        <th width="20%">জাতীয় পরিচয়পত্র নাম্বার</th>
                        <th width="20%">মন্তব্য</th>
                    </tr>
                    @php
                        $i = 1;
                    @endphp
                        @if(!empty($row->successor_list) && is_array(json_decode($row->successor_list, true)))
                            @foreach(json_decode($row->successor_list) as $value)
                                <tr>
                                    <td>{{ int_en_to_bn($i) }}</td>
                                    <td>{{ isset($value->w_name) && !empty($value->w_name) ? $value->w_name : '' }}</td>
                                    <td>{{ isset($value->w_relation) && !empty($value->w_relation) ? $value->w_relation : '' }}</td>
                                    <td>
                                        @if(isset($value->w_age) && !empty($value->w_age))
                                            {{ int_en_to_bn(date("d/m/Y", strtotime($value->w_age))) }}
                                        @else
                                            {{ int_en_to_bn(0) }}
                                        @endif
                                    </td>
                                    <td>{{ isset($value->w_nid) && !empty($value->w_nid) ? int_en_to_bn($value->w_nid) : int_en_to_bn(0) }}</td>
                                    <td>{{ isset($value->w_note) && !empty($value->w_note) ? $value->w_note : '' }}</td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        @else
                        <tr>
                            @if($row->sonod_name=='উত্তরাধিকারী সনদ')
                            <td colspan="6" class="text-center">কোনও উত্তরাধিকারী যোগ করা হয় নাই</td>
                            @else
                            <td colspan="6" class="text-center">কোনও ওয়ারিশ যোগ করা হয় নাই</td>
                            @endif

                        </tr>
                        @endif
                </table>


<br>
	আবেদনকারীর স্বাক্ষর :-<br>
{{--
	<br>
	আবেদনকারীর নাম: {{ $row->applicant_name }}<br>
	বর্তমান ঠিকানা: হোল্ডিং নং- {{ int_en_to_bn($row->applicant_holding_tax_number) }} , গ্রাম:{{ $row->applicant_present_village }} , ডাকঘর: {{ $row->applicant_present_post_office }} , উপজেলা: {{ $row->applicant_present_Upazila }} , জেলা: {{ $row->applicant_present_district }} । <br>
		মোবাইল নম্বর	: {{ int_en_to_bn($row->applicant_mobile) }} <br> --}}




    <p style="text-align:center" >শীগ্রই আপনার আবেদনটি কর্তৃপক্ষ কর্তৃক যথাযথ প্রক্রিয়ায় অনুমোদন করা হবে।</p>


        <table></table>


        <p style="margin: 0;"><b>ইউপি সদস্যের মন্তব্য/সুপারিশ:</b></p>
        @if($row->sonod_name=='উত্তরাধিকারী সনদ')
        <p style="margin: 0;font-size:14px;">সরেজমিন তদন্ত পূর্বক বর্ণিত ব্যক্তির উল্লিখিত উত্তরাধিকারী/উত্তরাধিকারীগণ ছাড়া আর অন্য কোন উত্তরাধিকারী নেই। ইহা আমার জানা মতে সত্য।</p>

        <p style="margin: 0;font-size:14px;">** অন্য কোন উত্তরাধিকারী থাকলে তার বিবরণ উল্লেখ করুন (প্রযোজ্য ক্ষেত্রে):</p>
        @else


        <p style="margin: 0;font-size:14px;">সরেজমিন তদন্ত পূর্বক বর্ণিত মরহুম ব্যক্তির উল্লিখিত ওয়ারিশ/ওয়ারিশগণ ছাড়া আর অন্য কোন ওয়ারিশ নেই। ইহা আমার জানা মতে সত্য।</p>

        <p style="margin: 0;font-size:14px;">** অন্য কোন ওয়ারিশ থাকলে তার বিবরণ উল্লেখ করুন (প্রযোজ্য ক্ষেত্রে):</p>
        @endif






