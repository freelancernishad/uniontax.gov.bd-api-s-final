<?php

use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;
use Illuminate\Support\Facades\Log;


function sonodView($id){
    $row = Sonod::find($id);
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


    return view('SonodsPdf.SonodFormat.'.$blade,compact('row','Sonodnamelist','uniouninfo'));

};



function sonodView_trade2($id){
    $row = Sonod::find($id);

    $sonod = Sonodnamelist::where('bnname',$row->sonod_name)->first();
    $uniouninfo = Uniouninfo::where('short_name_e',$row->unioun_name)->first();
    $blade = 'Trade_license2';
    return view('SonodsPdf.SonodFormat.'.$blade,compact('row','sonod','uniouninfo'));

};
