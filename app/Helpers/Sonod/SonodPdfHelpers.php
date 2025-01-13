<?php

use App\Models\EnglishSonod;
use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;
use Illuminate\Support\Facades\Log;


function sonodView($id,$en=false){


    if($en){
        $row = EnglishSonod::with(['sonod' => function ($query) {
            $query->select('id', 'sonod_id'); // Select only 'id' and 'sonod_id' from the sonod table
        }])->find($id);
        $sonod_id = $row->sonod->sonod_id;
    }else{

        $row = Sonod::find($id);
        $sonod_id = $row->sonod_id;
    }


    Log::info("row = ".$row);

    if($row->sonod_name=='বিবিধ প্রত্যয়নপত্র'){
        if($row->alive_status=='0'){
            $Sonodnamelist = Sonodnamelist::where('bnname',$row->sonod_name)->first();
            $Sonodnamelist->template = "&nbsp; &nbsp; &nbsp; আমি তার আত্মার মাকফিরাত কামনা করি।";
        }else{
            $Sonodnamelist = Sonodnamelist::where('bnname',$row->sonod_name)->first();
        }
    }else{
        $Sonodnamelist = Sonodnamelist::where('bnname',$row->sonod_name)->first();
    }



    $uniouninfo = Uniouninfo::where('short_name_e',$row->unioun_name)->first();
    $blade = 'OthersSonodFormat';
    $slug =  str_replace(' ', '_', $Sonodnamelist->enname);

    if($slug=='Trade_license'){
    $blade = $slug;
    }

    $sonodFolder = 'BnSonod';
    if($en){
        $sonodFolder = 'EnSonod';
    }

    Log::info($sonodFolder);
    return view("SonodsPdf.$sonodFolder.SonodFormat.$blade",compact('row','Sonodnamelist','uniouninfo','sonod_id'));

};



function sonodView_trade2($id,$en=false){


    if($en){
        $row = EnglishSonod::with(['sonod' => function ($query) {
            $query->select('id', 'sonod_id','amount_deails'); // Select only 'id' and 'sonod_id' from the sonod table
        }])->find($id);
        $sonod_id = $row->sonod->sonod_id;
        $amount_details = $row->sonod->amount_deails;
        $bladeType = 'EnSonod';


        // return $amount_details;
    }else{
        $bladeType = 'BnSonod';

        $row = Sonod::find($id);
        $sonod_id = $row->sonod_id;
        $amount_details = $row->amount_deails;
    }


    // $row = Sonod::find($id);




    $sonod = Sonodnamelist::where('bnname',$row->sonod_name)->first();
    $uniouninfo = Uniouninfo::where('short_name_e',$row->unioun_name)->first();
    $blade = 'Trade_license2';
    return view("SonodsPdf.$bladeType.SonodFormat.".$blade,compact('row','sonod','uniouninfo','sonod_id','amount_details'));

};






function sonodView_Inheritance_certificate($id){

    $row = Sonod::find($id);
    $sonod_name = $row->sonod_name;
    if ($sonod_name == 'ওয়ারিশান সনদ') {
        $text = 'ওয়ারিশ/ওয়ারিশগণের নাম ও সম্পর্ক';
    } else {
        $text = 'উত্তরাধিকারীগণের নাম ও সম্পর্ক';
    }

    $w_list = $row->successor_list;
    $w_list = json_decode($w_list);


$nagoriinfo = '';




if ($sonod_name == 'ওয়ারিশান সনদ') {

    if($row->ut_religion=='ইসলাম'){
        $deathStatus = 'মরহুম';
        $deathStatus2 = 'মরহুমের';
    }else{
        $deathStatus = 'স্বর্গীয়';
        $deathStatus2 = 'স্বর্গীয় ব্যক্তির';

    }



    $nagoriinfo .= '
        <p style="margin-top:0px;margin-bottom:5px;font-size:11px;text-align:justify">&nbsp; &nbsp; &nbsp; এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, '.$deathStatus.' ' . $row->utname . ', পিতা/স্বামী- ' . $row->ut_father_name . ', মাতা- ' . $row->ut_mother_name . ', গ্রাম- ' . $row->ut_grame . ', ডাকঘর- ' . $row->ut_post . ', উপজেলা: ' . $row->ut_thana . ', জেলা- ' . $row->ut_district . '। তিনি অত্র ইউনিয়নের '.int_en_to_bn($row->ut_word).' নং ওয়ার্ডের '.$row->applicant_resident_status.' বাসিন্দা ছিলেন। মৃত্যুকালে তিনি নিম্নোক্ত ওয়ারিশগণ রেখে যান। নিম্নে তাঁর ওয়ারিশ/ওয়ারিশগণের নাম ও সম্পর্ক উল্লেখ করা হলো।<br>
        <br>

        &nbsp; &nbsp; &nbsp; আমি '.$deathStatus2.' বিদেহী আত্মার মাগফেরাত কামনা করি।
            </p>




            ';


            $nagoriinfo .= '<p style="margin: 0;font-size:11px;">বিঃদ্রঃ উক্ত ওয়ারিশান সনদের সকল দায়ভার  সংশ্লিষ্ট ইউপি সদস্য/সদস্যার যাচাইকারীর ওপর বর্তাইবে ।</p>';



        } else {

        $nagoriinfo .= '
        <p style="margin-top:0px;margin-bottom:5px;font-size:11px;text-align:justify">&nbsp; &nbsp; &nbsp; এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, জনাব ' . $row->utname . ', পিতা/স্বামী- ' . $row->ut_father_name . ', মাতা- ' . $row->ut_mother_name . ', গ্রাম- ' . $row->ut_grame . ', ডাকঘর- ' . $row->ut_post . ', উপজেলা: ' . $row->ut_thana . ', জেলা- ' . $row->ut_district . '। তিনি অত্র ইউনিয়নের '.int_en_to_bn($row->ut_word).' নং ওয়ার্ডের '.$row->applicant_resident_status.' বাসিন্দা। নিম্নে তাঁর উত্তরাধিকারী/উত্তরাধিকারীগণের নাম ও সম্পর্ক উল্লেখ করা হলো।<br>
        <br>


            </p>';


            $nagoriinfo .= '<p style="margin: 0;font-size:11px;">বিঃদ্রঃ উক্ত উত্তরাধিকারী সনদের সকল দায়ভার  সংশ্লিষ্ট ইউপি সদস্য/সদস্যার যাচাইকারীর ওপর বর্তাইবে ।</p>';



        }









$nagoriinfo .= '<h4 style="text-align:center;margin-bottom:0px;font-size:11px">' . $text . '</h4>
<table class="table " style="width:100%;border-collapse: collapse;" cellspacing="0" cellpadding="0"  >
<tr>
<th style="        border: 1px dotted black;
    padding:1px 4px;
    font-size: 11px;" width="10%">ক্রমিক নং</th>
<th style="        border: 1px dotted black;
    padding:1px 4px;
    font-size: 11px;" width="30%">নাম</th>
<th style="        border: 1px dotted black;
    padding:1px 4px;
    font-size: 11px;" width="10%">সম্পর্ক</th>
<th style="        border: 1px dotted black;
    padding:1px 4px;
    font-size: 11px;" width="10%">বয়স</th>
<th style="        border: 1px dotted black;
    padding:1px 4px;
    font-size: 11px;" width="20%">এনআইডি/জন্ম নিবন্ধন নং</th>
</tr>';
    $i = 1;


    foreach ($w_list as $rowList) {
        $nagoriinfo .= '
        <tr>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . int_en_to_bn($i) . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_name) && !empty($rowList->w_name) ? $rowList->w_name : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_relation) && !empty($rowList->w_relation) ? $rowList->w_relation : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_age) && !empty($rowList->w_age) ? int_en_to_bn(date("d/m/Y", strtotime($rowList->w_age))) : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_nid) && !empty($rowList->w_nid) ? int_en_to_bn($rowList->w_nid) : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_note) && !empty($rowList->w_note) ? $rowList->w_note : '-') . '</td>
        </tr>';

        $i++;
    }








    $nagoriinfo .= '
</table>
<br>
<p style="margin-top:-18px;margin-bottom:1px;font-size:11px">
আবেদনকারীর নামঃ '.$row->applicant_name.'।  পিতা/স্বামীর নামঃ '.$row->applicant_father_name.'।  মাতার নামঃ '.$row->applicant_mother_name.'
</p><br>

<p style="margin-top:-18px;margin-bottom:1px;font-size:11px">
সংশ্লিষ্ট ওয়ার্ডের ইউপি সদস্য কর্তৃক আবেদনকারীর দাখিলকৃত তথ্য যাচাই/সত্যায়নের পরিপ্রেক্ষিতে অত্র সনদপত্র প্রদান করা হলো।
</p> <br/>
<p style="margin-top:-18px; margin-bottom:0px;font-size:11px">
&nbsp; &nbsp; &nbsp; আমি তাঁর/তাঁদের সর্বাঙ্গীন উন্নতি ও মঙ্গল কামনা করছি।
</p>
';

    $output = ' ';
    $output .= '' . $nagoriinfo . '';
    return $output;
}


function sonodView_Inheritance_certificate_english($id) {




        $row = EnglishSonod::with(['sonod' => function ($query) {
            $query->select('id', 'sonod_id'); // Select only 'id' and 'sonod_id' from the sonod table
        }])->find($id);
        $sonod_id = $row->sonod->sonod_id;





    $sonod_name = $row->sonod_name;
    if ($sonod_name == 'ওয়ারিশান সনদ') {
        $text = 'Heir/Heirs Name and Relationship';
    } else {
        $text = 'Successors Name and Relationship';
    }

    $w_list = $row->successor_list;
    $w_list = json_decode($w_list);

    $nagoriinfo = '';

    if ($sonod_name == 'ওয়ারিশান সনদ') {
        if ($row->ut_religion == 'ইসলাম') {
            $deathStatus = 'Late';
            $deathStatus2 = 'the late';
        } else {
            $deathStatus = 'Deceased';
            $deathStatus2 = 'the deceased';
        }

        $nagoriinfo .= '
            <p style="margin-top:0px;margin-bottom:5px;font-size:11px;text-align:justify">&nbsp; &nbsp; &nbsp; This is to certify that ' . $deathStatus . ' ' . $row->utname . ', Father/Husband- ' . $row->ut_father_name . ', Mother- ' . $row->ut_mother_name . ', Village- ' . $row->ut_grame . ', Post Office- ' . $row->ut_post . ', Upazila: ' . $row->ut_thana . ', District- ' . $row->ut_district . '. He was a resident of Ward No. ' . int_en_to_bn($row->ut_word) . ' of this union as a ' . $row->applicant_resident_status . '. At the time of death, he left behind the following heirs. Below are the names and relationships of his heir/heirs.<br>
            <br>
            &nbsp; &nbsp; &nbsp; I pray for the salvation of the departed soul of ' . $deathStatus2 . '.
            </p>
            <p style="margin: 0;font-size:11px;">Note: All responsibilities of this heir certificate lie with the concerned UP member/verifier.</p>
        ';
    } else {
        $nagoriinfo .= '
            <p style="margin-top:0px;margin-bottom:5px;font-size:11px;text-align:justify">&nbsp; &nbsp; &nbsp; This is to certify that Mr. ' . $row->utname . ', Father/Husband- ' . $row->ut_father_name . ', Mother- ' . $row->ut_mother_name . ', Village- ' . $row->ut_grame . ', Post Office- ' . $row->ut_post . ', Upazila: ' . $row->ut_thana . ', District- ' . $row->ut_district . '. He is a resident of Ward No. ' . int_en_to_bn($row->ut_word) . ' of this union as a ' . $row->applicant_resident_status . '. Below are the names and relationships of his successor/successors.<br>
            <br>
            </p>
            <p style="margin: 0;font-size:11px;">Note: All responsibilities of this successor certificate lie with the concerned UP member/verifier.</p>
        ';
    }

    $nagoriinfo .= '<h4 style="text-align:center;margin-bottom:0px;font-size:11px">' . $text . '</h4>
    <table class="table" style="width:100%;border-collapse: collapse;" cellspacing="0" cellpadding="0">
        <tr>
            <th style="border: 1px dotted black; padding:1px 4px; font-size: 11px;" width="10%">SL No.</th>
            <th style="border: 1px dotted black; padding:1px 4px; font-size: 11px;" width="30%">Name</th>
            <th style="border: 1px dotted black; padding:1px 4px; font-size: 11px;" width="10%">Relationship</th>
            <th style="border: 1px dotted black; padding:1px 4px; font-size: 11px;" width="10%">Age</th>
            <th style="border: 1px dotted black; padding:1px 4px; font-size: 11px;" width="20%">NID/Birth Registration No.</th>
        </tr>';

    $i = 1;
    foreach ($w_list as $rowList) {
        $nagoriinfo .= '
        <tr>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . $i . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_name) && !empty($rowList->w_name) ? $rowList->w_name : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_relation) && !empty($rowList->w_relation) ? $rowList->w_relation : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_age) && !empty($rowList->w_age) ? date("d/m/Y", strtotime($rowList->w_age)) : '-') . '</td>
            <td style="text-align:center; border: 1px dotted black; padding:1px 4px; font-size: 11px;">' . (isset($rowList->w_nid) && !empty($rowList->w_nid) ? $rowList->w_nid : '-') . '</td>
        </tr>';
        $i++;
    }
    $nagoriinfo .= '
    </table>
    <br>
    <p style="margin-top:-18px;margin-bottom:1px;font-size:11px">
    Applicant\'s Name: ' . $row->applicant_name . '. Father/Husband\'s Name: ' . $row->applicant_father_name . '. Mother\'s Name: ' . $row->applicant_mother_name . '
    </p><br>
    <p style="margin-top:-18px;margin-bottom:1px;font-size:11px">
    This certificate is issued after verification/authentication of the information submitted by the applicant by the concerned ward UP member.
    </p> <br/>
    <p style="margin-top:-18px; margin-bottom:0px;font-size:11px">
    &nbsp; &nbsp; &nbsp; I wish for his/their overall progress and well-being.
    </p>';

    $output = ' ';
    $output .= '' . $nagoriinfo . '';
    return $output;
}
