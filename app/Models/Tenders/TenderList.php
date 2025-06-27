<?php

namespace App\Models\Tenders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderList extends Model
{
    use HasFactory;


    protected $fillable=[
       'union_name',
       'tender_id',
       'tender_type',
       'memorial_no',
       'tender_name',
       'description',
       'tender_word_no',
       'division',
       'district',
       'thana',
       'union',
       'govt_price',
       'form_price',
       'deposit_percent',
       'noticeDate',
       'form_buy_last_date',
       'tender_start',
       'tender_end',
       'tender_open',
       'tender_roles',
       'status',
       'committe1name',
       'committe1position',
       'commette1phone',
       'commette1pass',
       'committe2name',
       'committe2position',
       'commette2phone',
       'commette2pass',
       'committe3name',
       'committe3position',
       'commette3phone',
       'commette3pass',
       'bankName',
       'bankCheck',
       'daysOfDepositeAmount',
       'permitDetials',
    ];

    protected $hidden = [
        'commette1pass',
        'commette2pass',
        'commette3pass',
    ];

    protected $appends = ['is_committee_created','is_committee_created', 'permit_detials_modify'];

    public function getIsCommitteeCreatedAttribute()
    {
        return $this->committe1name && $this->committe1position && $this->commette1phone && $this->commette1pass &&
               $this->committe2name && $this->committe2position && $this->commette2phone && $this->commette2pass &&
               $this->committe3name && $this->committe3position && $this->commette3phone && $this->commette3pass
               ? true : false;
    }

    public function tanderInvoices()
    {
        return $this->hasMany(TanderInvoice::class);
    }


    public function getPermitDetialsModifyAttribute()
    {
        // যদি ডাটাবেজে permitDetials সেট থাকে তাহলে সেটা রিটার্ন করো
        $value = $this->permitDetials;
        if (!empty($value)) {
            return $value;
        }

        // যদি না থাকে, ডিফল্ট ডায়নামিক টেক্সট তৈরি করো
        return $this->generateNagoriInfoDefaultText();
    }


    // helper method for generating default text
    public function generateNagoriInfoDefaultText()
    {
        // এখানে তোমার ডিফল্ট ডাটা পাস করার লজিক বা ডামি ডাটা ব্যবহার করো
        // উদাহরণ স্বরূপ:
        $tenderSelected = (object)[
            'applicant_orgName' => 'নাম',
            'applicant_org_fatherName' => 'পিতার নাম',
            'vill' => 'গ্রাম',
            'postoffice' => 'ডাকঘর',
            'thana' => 'উপজেলা',
            'distric' => 'জেলা',
            'DorAmount' => 50000,
        ];

        $DorAmount = '৫০,০০০';
        $result15Percent = 7500;
        $result15PercentText = '৭,৫০০';
        $result5Percent = 2500;
        $result5PercentText = '২,৫০০';
        $meyadStart = '০১-০১-২০২৫';
        $meyadEnd = '৩১-১২-২০২৫';

        // তোমার int_en_to_bn helper ফাংশন ব্যবহার করো যদি থাকে, না থাকলে এখানে numeric text দিবে
return "এমতাবস্থায় " . ($tenderSelected->applicant_orgName ?? '[নাম প্রদান করুন]...') .
    ", পিতা: " . ($tenderSelected->applicant_org_fatherName ?? '[পিতার নাম প্রদান করুন]...') .
    ", গ্রামঃ " . ($tenderSelected->vill ?? '[গ্রাম প্রদান করুন]...') .
    ", ডাকঘরঃ " . ($tenderSelected->postoffice ?? '[ডাকঘর প্রদান করুন]...') .
    ", উপজেলাঃ " . ($tenderSelected->thana ?? '[উপজেলা প্রদান করুন]...') .
    ", জেলাঃ " . ($tenderSelected->distric ?? '[জেলা প্রদান করুন]...') .
    "-কে তার দাখিলকৃত দর " . ($DorAmount ?? '[দর লিখুন]...') . "/- (" . ($DorAmount ?? '[দর লিখুন]...') . ") " .
    ($this->bankName ?? '[ব্যাংকের নাম প্রদান করুন]...') . " " . ($this->bankCheck ?? '[চেক নম্বর লিখুন]...') .
    " হিসাব নম্বরে এবং তৎসঙ্গে নিদিষ্ট কোডে বিধি মোতাবেক দাখিলকৃত " . ($DorAmount ?? '[দর লিখুন]...') .
    "/- (" . ($DorAmount ?? '[দর লিখুন]...') . ") এর ১৫% ভ্যাট = " . ($result15PercentText ?? '[ভ্যাট লিখুন]...') .
    "/- (" . ($result15PercentText ?? '[ভ্যাট লিখুন]...') . ") এবং দাখিলকৃত " . ($DorAmount ?? '[দর লিখুন]...') .
    "/- (" . ($DorAmount ?? '[দর লিখুন]...') . ") এর ৫% আয়কর = " . ($result5PercentText ?? '[আয়কর লিখুন]...') .
    "/- (" . ($result5PercentText ?? '[আয়কর লিখুন]...') . ") সরকারি কোষাগারে আগামী " .
    ($this->daysOfDepositeAmount ?? '[দিন সংখ্যা প্রদান করুন]...') . " কর্মদিবসের মধ্যে সমুদয় অর্থ জমা প্রদান নিশ্চিত করা সাপেক্ষে " .
    ($meyadStart ?? '[শুরুর তারিখ লিখুন]...') . " ইং তারিখে হতে " .
    ($meyadEnd ?? '[শেষ তারিখ লিখুন]...') . " ইং তারিখ পর্যন্ত " .
    ($this->tender_name ?? '[ইজারার নাম লিখুন]...') .
    " প্রদানের কার্যাদেশ প্রদান করা হলো। অন্যথায়/ ব্যথতায় জামানত বাজেয়াপ্তসহ নিলাম বিজ্ঞপ্তিটি বাজেয়াপ্ত বলে গন্য হইবে এবং পুনরায় ডাক প্রদান করা হইবে।";



    }



}
