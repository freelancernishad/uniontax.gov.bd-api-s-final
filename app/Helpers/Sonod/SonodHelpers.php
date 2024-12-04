<?php

use App\Models\Sonod;
use App\Models\Uniouninfo;
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
