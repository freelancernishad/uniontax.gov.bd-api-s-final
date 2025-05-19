<?php

use App\Models\SiteSetting;
use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Rakibhstu\Banglanumber\NumberToBangla;

   function sonodId($union, $sonodname, $orthoBchor)
   {
       $sonodFinalId = '';

       $date = date('m');
       if ($date < 7) {
           $sortYear = date('y') - 1;  // For fiscal year, use the previous year if before July
       } else {
           $sortYear = date('y');  // Current year
       }

       // Get the union info count
       $UniouninfoCount = Uniouninfo::where('short_name_e', $union)->latest()->count();
       // Get the sonod count
       $SonodCount = Sonod::where(['unioun_name' => $union, 'sonod_name' => $sonodname, 'orthoBchor' => $orthoBchor])->latest()->count();

       // Check if the union info exists
       if ($UniouninfoCount > 0) {
           // Retrieve union info
           $Uniouninfo = Uniouninfo::where('short_name_e', $union)->latest()->first();

           if ($SonodCount > 0) {
               // Retrieve latest Sonod based on union, sonod name, and orthoBchor
               $Sonod = Sonod::where(['unioun_name' => $union, 'sonod_name' => $sonodname, 'orthoBchor' => $orthoBchor])->latest()->first();
               // Increment the sonod id
               $sonodFinalId = $Sonod->sonod_Id + 1;
           } else {
               // Default sonod id if no previous sonods are found
               $sonod_Id = str_pad(00001, 5, '0', STR_PAD_LEFT);
               $sonodFinalId = $Uniouninfo->u_code . $sortYear . $sonod_Id;
           }
       }

       return $sonodFinalId;
   }


    function enBnName($data)
   {
       $data =  str_replace("_", " ", $data);
       return Sonodnamelist::where('enname', $data)->first();
   }

    function UnionenBnName($data)
   {

       return Uniouninfo::where('short_name_e', $data)->select('short_name_b')->first()->short_name_b;
   }
    function SonodEnName($data)
   {
       return Sonodnamelist::where('bnname', $data)->select('enname')->first()->enname;
   }

    function convertAnnualIncomeToText($annualIncome)
   {
       $numTo = new NumberToBangla();
       return $numTo->bnMoney(int_bn_to_en($annualIncome)) . ' মাত্র';
   }


function age($dateOf = '2001-08-25', $en = false) {
    $dateOfBirth = explode("-", $dateOf);
    $y1 = (int)$dateOfBirth[0];
    $m1 = (int)$dateOfBirth[1];
    $d1 = (int)$dateOfBirth[2];

    $currentDate = getdate();
    $d2 = $currentDate['mday'];
    $m2 = $currentDate['mon'];
    $y2 = $currentDate['year'];

    // Check for leap year and adjust February days
    $monthDays = [31, ($y2 % 4 == 0 && ($y2 % 100 != 0 || $y2 % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    if ($d1 > $d2) {
        $d2 += $monthDays[$m2 - 1];
        $m2 -= 1;
    }

    if ($m1 > $m2) {
        $m2 += 12;
        $y2 -= 1;
    }

    $d = $d2 - $d1;
    $m = $m2 - $m1;
    $y = $y2 - $y1;

    if ($en) {
        return $y . ' years ' . $m . ' months ' . $d . ' days';
    }

    return $y . ' বছর ' . $m . ' মাস ' . $d . ' দিন ';
}



   function generateSecProttoyon($details, $english = false) {
    // Initialize readonly flag and sec_prottoyon content
    $readonly = false;
    $sec_prottoyon = '';


    $union = SiteSetting::where('key', 'union')->first()->value;
    $union = $union === "false" ? false : (bool)$union;
    if($union==true){


        $union = "ইউনিয়ন";
        $unioner = "ইউনিয়নের";
        $union_oarisader = "ইউনিয়ন পরিষদের";

        $union_en = "ইউনিয়ন";
        $unioner_en = "ইউনিয়নের";
        $union_oarisader_en = "Union Parishad";

    }else{
        $unioner = "পৌরসভার";
        $union = "পৌরসভা";
        $union_oarisader = "পৌরসভার";

        $unioner_en = "পৌরসভার";
        $union_en = "পৌরসভা";
        $union_oarisader_en = "Municipality";

    }

    // Get the English name of the sonod if English is true
    $sonod_name = $english ? SonodEnName($details['sonod_name']) : $details['sonod_name'];

    // Define sonod name cases
    switch ($details['sonod_name']) {
        case 'নাগরিকত্ব সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the {$sonod_name} certificate." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
            break;

        case 'ট্রেড লাইসেন্স':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Trade License details for {$details['applicant_name']}." :
                "{$details['applicant_name']} এর ট্রেড লাইসেন্স বিবরণ।";
            break;

        case 'ওয়ারিশান সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the {$sonod_name}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে ওয়ারিশান সনদ প্রদান করা হলো ।";
            break;

        case 'উত্তরাধিকারী সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the {$sonod_name}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে উত্তরাধিকারী সনদ প্রদান করা হলো ।";
            break;

        case 'বিবিধ প্রত্যয়নপত্র':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Mr. {$details['utname']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. {$details['sonodlist']['prottoyon']}" :
                "জনাব {$details['utname']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। {$details['sonodlist']['prottoyon']}";
            break;

        case 'চারিত্রিক সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. His/Her character is good and he/she is of high moral character. This is true to the best of my knowledge." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তার স্বভাব চরিত্র ভালো এবং উন্নত চরিত্রের অধিকারী।ইহা আমার জানামতে সত্য ।";
            break;

        case 'ভূমিহীন সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. He/She does not own any land for living or cultivation. Therefore, he/she is granted the {$sonod_name}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। আমার জানা মতে তার থাকার মতো এবং চাষাবাদ করার মত নিজস্ব কোনো জমি নেই । তিনি একজন ভূমিহীন মানুষ তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
            break;

        case 'পারবারিক সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a descendant of the {$details['family_name']} family. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানা মতে সে {$details['family_name']} বংশের একজন উত্তরাধিকারী । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'অবিবাহিত সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To the best of my knowledge, he/she is an unmarried {$details['applicant_gender']}. He/She has not been married in the past. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন অবিবাহিত {$details['applicant_gender']} । বিগত সময়ে তার কোন বিবাহ ছিলনা বা বিবাহ করেনি । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই ।";
            break;

        case 'পুনঃ বিবাহ না হওয়া সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a married {$details['applicant_gender']} and has not remarried. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে একজন বিবাহিত {$details['applicant_gender']} এবং তাহার কোনো পুনঃ বিবাহ হয়নি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'বার্ষিক আয়ের প্রত্যয়ন':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. His/Her annual income is {$details['Annual_income']}/{$details['Annual_income_text']}. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার বার্ষিক আয় {$details['Annual_income']}/{$details['Annual_income_text']} । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'একই নামের প্রত্যয়ন':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['utname']} is personally known to me. It is disclosed that \"{$details['utname']}\" and \"{$details['applicant_second_name']}\" are the same person. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['utname']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। প্রকাশ থাকে যে \"{$details['utname']}\" ও \"{$details['applicant_second_name']}\" একই ব্যক্তি । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'অনুমতি পত্র':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. He/She is granted permission for {$details['Subject_to_permission']}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাকে {$details['Subject_to_permission']} অনুমতি দেওয়া হল ।";
            break;

        case 'প্রতিবন্ধী সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To the best of my knowledge, he/she is a {$details['disabled']} disabled person. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন {$details['disabled']} প্রতিবন্ধী । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'অনাপত্তি সনদপত্র':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me." :
                "জনাব {$details['applicant_name']} ";
            break;

        case 'অগভীর নলকূপ স্থাপন':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. There is currently no tube well in his/her homestead. Therefore, he/she is granted permission to install a shallow tube well." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তার বসতবাড়িতে বর্তমানে কোন টিউবওয়েল নেই । তাই তাকে অগভীর নলকূপ বসানোর অনুমতি দেওয়া হল ।";
            break;

        case 'অবকাঠামো নির্মাণের অনুমতি পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. Currently, he/she does not have any infrastructure or homestead for living. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted permission to construct infrastructure." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। বর্তমানে তার থাকার জন্য কোনো অবকাঠামো ও বসতবাড়ি নেই । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।  তাই তাকে অবকাঠামো নির্মাণের অনুমতি প্রদান করা হল ।";
            break;

        case 'অভিভাবকের আয়ের সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. His/Her father's annual income is {$details['Annual_income']}/= taka. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার বাবার বাৎসরিক আয় {$details['Annual_income']}/= হাজার টাকা । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'আনুমানিক বয়স প্রত্যয়ন পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. His/Her approximate age is ".age($details['applicant_date_of_birth'],true).". He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার আনুমানিক বয়স ".age($details['applicant_date_of_birth'])." । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'আর্থিক অস্বচ্ছলতার সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He is a resident of my {$union_oarisader_en}, with a status of {$details['applicant_resident_status']}, and is financially very poor. He is a natural-born citizen of Bangladesh and a resident of this {$union_oarisader_en} council, with a status of {$details['applicant_resident_status']}. To my knowledge, there are no charges of treason against him." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে আমার {$unioner} {$details['applicant_resident_status']} বাসিন্দা এবং সে আর্থিকভাবে খুবি অসচ্ছল । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'প্রত্যয়নপত্র':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'অস্থায়ীভাবে বসবাসের প্রত্যয়ন পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, he/she is temporarily residing as a tenant at {$details['utname']}, father- {$details['ut_father_name']}, village- {$details['ut_grame']}, post office- {$details['ut_post']}, upazila- {$details['ut_thana']}, district- {$details['ut_district']}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে, তিনি {$details['utname']}, পিতা- {$details['ut_father_name']}, গ্রাম- {$details['ut_grame']}, ডাকঘর- {$details['ut_post']}, উপজেলা- {$details['ut_thana']}, জেলা- {$details['ut_district']}-এর বাসায় ভাড়াটিয়া হিসেবে অস্থায়ীভাবে বসবাস করছেন।";
            break;

        case 'বিবাহিত সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, he/she is a married {$details['applicant_gender']}. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন বিবাহিত {$details['applicant_gender']} । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই ।";
            break;

        case 'দ্বিতীয় বিবাহের অনুমতি পত্র':
            $customtext1 = $details['applicant_gender'] == 'পুরুষ' ? 'স্ত্রী' : 'স্বামী';
            $customtext2 = $details['applicant_gender'] == 'পুরুষ' ? 'স্বামী' : 'স্ত্রী';

            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, he/she is a married {$details['applicant_gender']}. According to the application description, he/she voluntarily gives permission to his/her {$customtext2} for a second marriage and the relevant ward councilor recommends it." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন বিবাহিত {$details['applicant_gender']} । তাঁর {$customtext1} এর আবেদনের বর্ণনা মোতাবেক সে স্বেচ্ছায় তাঁর {$customtext2}কে দ্বিতীয় বিবাহ করার জন্য অনুমতি প্রদান করেন এবং সংশ্লিষ্ট ওয়ার্ড সহকারি সুপারিশ করেন।";
            break;

        case 'ভোটার স্থানান্তরের প্রত্যয়ন পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To my knowledge, he/she was a voter at {$details['applicant_present_village']}, {$details['applicant_present_post_office']}, {$details['applicant_present_Upazila']}, {$details['applicant_present_district']}. Currently he/she is permanently residing at {$details['Name_of_the_transferred_area']}, so I recommend for his/her voter transfer." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে, তিনি {$details['applicant_present_village']}, {$details['applicant_present_post_office']}, {$details['applicant_present_Upazila']}, {$details['applicant_present_district']}-এ ভোটার হয়েছেন। বর্তমানে তিনি {$details['Name_of_the_transferred_area']}-এ স্থায়ীভাবে বসবাস করায় তাঁর ভোটার স্তানান্তর করার সুপারিশ করছি।";
            break;

        case 'জাতীয় পরিচয়পত্র সংশোধন প্রত্যয়ন':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. There are some unintended errors in his/her national ID card which need urgent correction. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার জাতীয় পরিচয় পত্র কিছু তথ্য অনাকাঙ্ক্ষিত ভুল হয়েছে এটি সংশোধন করা অতীব জরুরী । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'নিঃসন্তান প্রত্যয়ন':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, he/she is married but has no children. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে বিবাহিত কিন্তু তার কোন সন্তান নেই । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'ভোটার তালিকায় নাম অন্তর্ভুক্ত না হওয়ার প্রত্যয়ন':
            $readonly = false;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. Due to illness, his/her name was not included in the voter list. Request to include his/her name in the voter list. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে অসুস্থ থাকার কারণে ভোটার তালিকায় তার নাম অন্তর্ভুক্তি করন করা হয়নি । তার নামটি ভোটার তালিকায় অন্তর্ভুক্ত করার জন্য অনুরোধ করা হলো । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'সম্প্রদায় সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. Community related text will be here. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সম্প্রদায় নিয়ে লেখাগুলো এখানে হবে । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'জীবিত ব্যক্তির ওয়ারিশ সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the {$sonod_name}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
            break;

        case 'এতিম সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, his/her parents are not alive, he/she is an orphan. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানা মতে তার বাবা-মা জীবিত নেই, সে একজন এতিম । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'জীবিত সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He is alive and well. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তিনি জীবিত এবং সুস্থ আছেন । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'রিনিউ সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. He/She is approved for certificate renewal." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাকে সনদ রিনিউ করার জন্য অনুমোদন দেওয়া হল ।";
            break;

        case 'বসতবাড়ি হোল্ডিং নিবন্ধন সনদ':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She has taken holding number for his/her homestead. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে তার বসত বাড়ির জন্য হোল্ডিং নাম্বার নিয়েছে । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case 'রোহিঙ্গা নয় মর্মে প্রত্যয়ন পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is not a Rohingya, but a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে রোহিঙ্গা নয়, সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
            break;

        case "{$union} পরিষদ নাগরিক লিস্ট":
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the {$sonod_name}." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
            break;

        case 'নতুন ভোটারের প্রত্যয়ন পত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. He/She couldn't become a voter during voter list update due to absence/being outside. Therefore, I recommend to include him/her in the new voter list." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তিনি ভোটার তালিকা হালনাগাদ করার সময় অনুপস্থিত/বাহিরে থাকার কারণে ভোটার হতে পারে নি। তাই তাকে নতুন ভোটার তালিকায় অন্তর্ভুক্ত করার জন্য সুপারিশ করছি ।";
            break;

        case 'বেকারত্বের সনদপত্র':
            $readonly = true;
            $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. To my knowledge, he/she is currently unemployed and jobless." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই।<br/><br/>&nbsp; &nbsp; &nbsp; আমার জানামতে বর্তমানে সে বেকার ও কর্মহীন ।";
            break;





        case 'জাতীয়তা সনদ':
                $readonly = true;
                $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a Bangladeshi national by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the Nationality Certificate." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তিনি জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে জাতীয়তা সনদ প্রদান করা হলো।";
                break;

            case 'স্থায়ী বাসিন্দা সনদ':
                $readonly = true;
                $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a permanent resident of this {$union_oarisader_en} and a citizen of Bangladesh by birth. To the best of my knowledge, there are no charges of sedition against him/her. Therefore, he/she is granted the Permanent Resident Certificate." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তিনি অত্র {$union_oarisader} স্থায়ী বাসিন্দা এবং জন্মসূত্রে বাংলাদেশের নাগরিক। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে স্থায়ী বাসিন্দা সনদ প্রদান করা হলো।";
                break;

            case 'এতিম সনদ':
                $readonly = true;
                $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. To my knowledge, his/her parents are not alive, and he/she is an orphan. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানা মতে তার বাবা-মা জীবিত নেই, তিনি একজন এতিম। তিনি জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'মাসিক আয়ের সনদ':
                $readonly = true;
                $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. His/Her monthly income is {$details['Annual_income']}/{$details['Annual_income_text']}. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her." :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার মাসিক আয় {$details['Annual_income']}/{$details['Annual_income_text']}। তিনি জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;


            case 'পারিবারিক সনদ':
                $readonly = true;
                $sec_prottoyon = $english ?
                "Mr. {$details['applicant_name']} is personally known to me. He/She is a citizen of Bangladesh by birth and a resident of this {$union_oarisader_en}. To the best of my knowledge, there are no charges of sedition against him/her. The names and information of the family members are as follows:" :
                "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তিনি জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র {$union_oarisader} {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার কোনো অভিযোগ নেই। পরিবারের সদস্যদের নাম ও তথ্য নিম্নরূপঃ";
                break;





        default:
            $readonly = false;
            $sec_prottoyon = $english ?
                "No matching certificate found." :
                "কোন মিলে যাওয়া সনদ পাওয়া যায়নি।";
            break;
    }

    return $sec_prottoyon;
}


    function getOrthoBchorYear()
    {
        $year = date('Y');
        $month = date('m');
        return $month < 7 ? ($year - 1) . "-" . date('y') : $year . "-" . (date('y') + 1);
    }

    function CurrentOrthoBochor($full = 0, $month = '', $year = '')
    {
        $year = $year ?: date('Y');
        $month = $month ?: date('m'); // Use the current month if none is provided

        // Determine the year range based on the month
        $startYear = $month < 7 ? $year - 1 : $year;
        $endYear = $month < 7 ? $year : $year + 1;

        // Return the formatted year range based on the $full flag
        return $full ? "{$startYear}-{$endYear}" : "{$startYear}-" . substr($endYear, -2);
    }

    function changeSonodName($name){
        if($name=='ওয়ারিশান সনদ'){
            return 'ওয়ারিশ সনদ';
        }elseif($name=='বিবিধ প্রত্যয়নপত্র'){
            return 'প্রত্যয়নপত্র';
        }else{
            return $name;
        }
    }



    function addressEnToBn($name,$which=''){
        if($which=='division'){
            return Division::where('name',$name)->select('bn_name')->firstOrFail()->bn_name;
        }elseif($which=='district'){
            return District::where('name',$name)->select('bn_name')->firstOrFail()->bn_name;
        }elseif($which=='upazila'){
            return Upazila::where('name',$name)->select('bn_name')->firstOrFail()->bn_name;
        }else{
            return '';
        }
    }

     function translateToBangla($text)
    {
        // Define translations manually
        $translations = [
            'holdingtax' => 'হোল্ডিং ট্যাক্স',
            'tender-deposit' => 'টেন্ডার জমা',
            'Tenders_form' => 'টেন্ডার ফর্ম',
        ];

        // Check if the term exists in the array
        if (array_key_exists($text, $translations)) {
            $translatedText = $translations[$text];
        } else {
            $translatedText = $text;
        }

        return $translatedText;
    }
