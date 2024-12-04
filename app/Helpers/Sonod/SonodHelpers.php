<?php

use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;
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

    function convertAnnualIncomeToText($annualIncome)
   {
       $numTo = new NumberToBangla();
       return $numTo->bnMoney(int_bn_to_en($annualIncome)) . ' মাত্র';
   }


   function generateSecProttoyon($details) {
        // Initialize readonly flag and sec_prottoyon content
        $readonly = false;
        $sec_prottoyon = '';

        // Define sonod name cases
        switch ($details['sonod_name']) {
            case 'নাগরিকত্ব সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে কোন রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
                break;

            case 'ট্রেড লাইসেন্স':
                $readonly = false;
                break;

            case 'ওয়ারিশান সনদ':
                $readonly = true;
                break;

            case 'উত্তরাধিকারী সনদ':
                $readonly = true;
                break;

            case 'বিবিধ প্রত্যয়নপত্র':
                $readonly = false;
                $sec_prottoyon = "জনাব {$details['utname']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। {$details['sonodlist']['prottoyon']}";
                break;

            case 'চারিত্রিক সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তার স্বভাব চরিত্র ভালো এবং উন্নত চরিত্রের অধিকারী।ইহা আমার জানামতে সত্য ।";
                break;

            case 'ভূমিহীন সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। আমার জানা মতে তার থাকার মতো এবং চাষাবাদ করার মত নিজস্ব কোনো জমি নেই । তিনি একজন ভূমিহীন মানুষ তাই তাকে {$details['sonod_name']} প্রদান করা হলো ।";
                break;

            case 'পারবারিক সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানা মতে সে {$details['family_name']} বংশের একজন উত্তরাধিকারী । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'অবিবাহিত সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন অবিবাহিত {$details['applicant_gender']} । বিগত সময়ে তার কোন বিবাহ ছিলনা বা বিবাহ করেনি । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই ।";
                break;

            case 'পুনঃ বিবাহ না হওয়া সনদ':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে একজন বিবাহিত {$details['applicant_gender']} এবং তাহার কোনো পুনঃ বিবাহ হয়নি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'বার্ষিক আয়ের প্রত্যয়ন':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার বার্ষিক আয় {$details['Annual_income']}/{$details['Annual_income_text']} । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'একই নামের প্রত্যয়ন':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['utname']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। প্রকাশ থাকে যে \"{$details['utname']}\" ও \"{$details['applicant_second_name']}\" একই ব্যক্তি । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'অনুমতি পত্র':
                $readonly = false;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তাকে {$details['Subject_to_permission']} অনুমতি দেওয়া হল ।";
                break;

            case 'প্রতিবন্ধী সনদপত্র':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। আমার জানামতে সে একজন {$details['disabled']} প্রতিবন্ধী । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'অনাপত্তি সনদপত্র':
                $readonly = false;
                $sec_prottoyon = "জনাব {$details['applicant_name']} ";
                break;

            case 'অগভীর নলকূপ স্থাপন':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই। তার বসতবাড়িতে বর্তমানে কোন টিউবওয়েল নেই । তাই তাকে অগভীর নলকূপ বসানোর অনুমতি দেওয়া হল ।";
                break;

            case 'অবকাঠামো নির্মাণের অনুমতি পত্র':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। বর্তমানে তার থাকার জন্য কোনো অবকাঠামো ও বসতবাড়ি নেই । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।  তাই তাকে অবকাঠামো নির্মাণের অনুমতি প্রদান করা হল ।";
                break;

            case 'অভিভাবকের আয়ের সনদপত্র':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার বাবার বাৎসরিক আয় {$details['Annual_income']}/= হাজার টাকা । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'আনুমানিক বয়স প্রত্যয়ন পত্র':
                $readonly = true;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। তার আনুমানিক বয়স {$this->age($details['applicant_date_of_birth'])} । সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            case 'প্রত্যয়নপত্র':
                $readonly = false;
                $sec_prottoyon = "জনাব {$details['applicant_name']} কে আমি ব্যক্তিগতভাবে চিনি ও জানি। সে জন্মসূত্রে বাংলাদেশের নাগরিক এবং অত্র ইউনিয়ন পরিষদের {$details['applicant_resident_status']} বাসিন্দা। আমার জানামতে তার বিরুদ্ধে রাষ্ট্রদ্রোহিতার অভিযোগ নেই।";
                break;

            default:
                $readonly = false;
                $sec_prottoyon = '';  // Fallback for unmatched sonod_name
                break;
        }

        return $sec_prottoyon;
    }
