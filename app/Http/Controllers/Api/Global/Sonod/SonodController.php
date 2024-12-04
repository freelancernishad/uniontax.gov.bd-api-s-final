<?php

namespace App\Http\Controllers\Api\Global\Sonod;

use Exception;
use App\Models\Sonod;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Models\Sonodnamelist;
use App\Http\Controllers\Controller;
use Rakibhstu\Banglanumber\NumberToBangla;

class SonodController extends Controller
{
    public function sonodSubmit(Request $request)
    {
        // Return the request data for debugging
        // return $request->all();

        // Extract necessary request data
        $sonodName = $request->sonod_name;
        $unionName = $request->unioun_name;
        $successors = json_encode($request->successors);
        $sonodEnName = Sonodnamelist::where('bnname', $sonodName)->first();
        $filePath = str_replace(' ', '_', $sonodEnName->enname);
        $dateFolder = date("Y/m/d");

        // Generate a unique key using a combination of timestamp, unionName, and sonodName
        do {
            $uniqueKey = md5(uniqid($unionName . $sonodName . microtime(), true));
            $existingSonod = Sonod::where('uniqeKey', $uniqueKey)->first();
        } while ($existingSonod);


        $sonodId = (string) sonodId($unionName, $sonodName, $this->getOrthoBchorYear());

        // Prepare data for insertion
        $insertData = $request->except([
            'sonod_Id', 'image', 'applicant_national_id_front_attachment',
            'applicant_national_id_back_attachment', 'applicant_birth_certificate_attachment',
            'successors', 'charages', 'Annual_income', 'applicant_type_of_businessKhat',
            'applicant_type_of_businessKhatAmount', 'orthoBchor'
        ]);

        $insertData['applicant_type_of_businessKhat'] = $request->applicant_type_of_businessKhat;

        if($request->applicant_type_of_businessKhatAmount){
            $insertData['applicant_type_of_businessKhatAmount'] = $request->applicant_type_of_businessKhatAmount;
        }else{
            $insertData['applicant_type_of_businessKhatAmount'] = 0;
        }



        $insertData['uniqeKey'] = $uniqueKey;
        $insertData['khat'] = "সনদ ফি";

        $insertData['stutus'] = "Pepaid";
        $insertData['payment_status'] = "Unpaid";

        $insertData = array_merge($insertData, $this->prepareSonodData($request, $sonodName, $successors, $unionName, $sonodId));

        // Handle file uploads securely
        $this->handleFileUploads($request, $insertData, $filePath, $dateFolder, $sonodId);

        // Check if annual income is provided and process accordingly
        if ($request->Annual_income) {
            $insertData['Annual_income'] = $request->Annual_income;
            $insertData['Annual_income_text'] = $this->convertAnnualIncomeToText($request->Annual_income);
        }

        // Handle the status and charges
        $this->handleCharges($request, $insertData);

        try {
            // Save the Sonod entry
            $sonod = Sonod::create($insertData);

          // Call sonodpayment to handle payment process
          $redirectUrl = sonodpayment($sonod->id);

          // Send notification if the status is Pending
          if ($request->stutus == 'Pending') {
              // $this->sendNotification($sonod);
          }

          // Return the created Sonod and the redirect URL
          return response()->json([
              'sonod' => $sonod,
              'redirect_url' => $redirectUrl
          ]);


        } catch (Exception $e) {
            // Handle errors and return a response
            return response()->json($e->getMessage(), 400);
        }
    }

    private function prepareSonodData($request, $sonodName, $successors, $unionName, $sonodId)
    {
        $insertData = [];

        // Specific adjustments based on sonod name
        if ($sonodName == 'একই নামের প্রত্যয়ন' || $sonodName == 'বিবিধ প্রত্যয়নপত্র') {
            $insertData['sameNameNew'] = 1;
        }

        // Set the orthoBchor based on current year/month
        $insertData['orthoBchor'] = $this->getOrthoBchorYear();
        // $insertData['orthoBchor'] = ($sonodName == 'ট্রেড লাইসেন্স') ? $request->orthoBchor : $this->getOrthoBchorYear();

        // Set additional fields from the union info
        $unionInfo = Uniouninfo::where('short_name_e', $unionName)->latest()->first();
        $insertData['chaireman_name'] = $unionInfo->c_name;
        $insertData['c_email'] = $unionInfo->c_email;
        $insertData['chaireman_sign'] = $unionInfo->c_signture;
        $insertData['chaireman_type'] = $unionInfo->c_type;

        // Add successor list
        $insertData['successor_list'] = $successors;

        // Set union chairman and secretary info
        $insertData['socib_name'] = $unionInfo->socib_name;
        $insertData['socib_email'] = $unionInfo->socib_email;
        $insertData['socib_signture'] = $unionInfo->socib_signture;
        $insertData['sonod_Id'] = $sonodId;

        return $insertData;
    }

    private function handleFileUploads($request, &$insertData, $filePath, $dateFolder, $sonodId)
    {
        // Handle file uploads with optimized code
        $this->uploadFile($request->image, $insertData, 'image', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_national_id_front_attachment, $insertData, 'applicant_national_id_front_attachment', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_national_id_back_attachment, $insertData, 'applicant_national_id_back_attachment', $filePath, $dateFolder, $sonodId);
        $this->uploadFile($request->applicant_birth_certificate_attachment, $insertData, 'applicant_birth_certificate_attachment', $filePath, $dateFolder, $sonodId);
    }

    private function uploadFile($fileData, &$insertData, $field, $filePath, $dateFolder, $sonodId)
    {
        if (count(explode(';', $fileData)) > 1) {
            // $insertData[$field] = uploadFileToS3($fileData, "sonod/$filePath/$dateFolder/$sonodId/");
        }
    }

    private function convertAnnualIncomeToText($annualIncome)
    {
        $numTo = new NumberToBangla();
        return $numTo->bnMoney(int_bn_to_en($annualIncome)) . ' মাত্র';
    }

    private function handleCharges($request, &$insertData)
    {
        if ($request->stutus == 'Prepaid') {
            $totalAmount = $request->charages['totalamount'];
            $sonodFee = $request->charages['sonod_fee'];
            $tradeVat = $request->charages['tradeVat'];
            $pesaKor = $request->charages['pesaKor'];
            $lastYearsMoney = $request->last_years_money;

            $currentlyPaidMoney = $totalAmount - $lastYearsMoney;

            $amountDetails = json_encode([
                'total_amount' => $totalAmount,
                'pesaKor' => $pesaKor,
                'tredeLisenceFee' => $sonodFee,
                'vatAykor' => $tradeVat,
                'khat' => '',
                'last_years_money' => $lastYearsMoney,
                'currently_paid_money' => $currentlyPaidMoney
            ]);

            $insertData['khat'] = '';
            $insertData['last_years_money'] = $lastYearsMoney;
            $insertData['currently_paid_money'] = $currentlyPaidMoney;
            $insertData['total_amount'] = $totalAmount;
            $insertData['the_amount_of_money_in_words'] = $this->convertAnnualIncomeToText($totalAmount);
            $insertData['amount_deails'] = $amountDetails;
        }
    }

    private function sendNotification($sonod)
    {
        // Send notification to the union's secretary
        $notificationData = [
            'union' => $sonod->unioun_name,
            'roles' => 'Secretary'
        ];

        $notificationCount = Notifications::where($notificationData)->count();
        if ($notificationCount > 0) {
            $actionUrl = makeshorturl(url('/secretary/approve/' . $sonod->id));
            $notification = Notifications::where($notificationData)->latest()->first();
            $data = json_encode([
                'to' => $notification->key,
                'notification' => [
                    'body' => $sonod->applicant_name . ' একটি ' . $sonod->sonod_name . ' এর নুতুন আবেদন করেছে',
                    'title' => 'সনদ নং ' . int_en_to_bn($sonod->sonod_Id),
                    'icon' => asset('assets/img/bangladesh-govt.png'),
                    'click_action' => $actionUrl
                ]
            ]);
            pushNotification($data);
        }
    }

    private function getOrthoBchorYear()
    {
        $year = date('Y');
        $month = date('m');
        return $month < 7 ? ($year - 1) . "-" . date('y') : $year . "-" . (date('y') + 1);
    }

}
