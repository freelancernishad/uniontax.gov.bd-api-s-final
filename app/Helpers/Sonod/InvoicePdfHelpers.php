<?php

use App\Models\Sonod;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;

function invoiceView($id){
        $row = Sonod::find($id);
        $sonod = Sonodnamelist::where('bnname',$row->sonod_name)->first();
        $uniouninfo = Uniouninfo::where('short_name_e',$row->unioun_name)->first();
        $blade = 'other';
        $slug =  str_replace(' ', '_', $sonod->enname);

        if($slug=='Trade_license'){
        $blade = $slug;
        }
        return view('Invoice.Sonod.'.$blade,compact('row','sonod','uniouninfo'));

    };
