<?php

namespace App\Helpers;

use App\Models\Uniouninfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class SmsNocHelper
{
    // Sends SMS via SMSNOC API
    public static function sendSms(string $description, string $applicantMobile = '01909756552', string $union = 'test'): string
    {
        // Fetch union info from database
        $unionInfo = Uniouninfo::where('short_name_e', $union)->first();

        if (!$unionInfo) {
            return 'Union not found';
        }

        // Check if union has enough SMS balance
        if ($unionInfo->smsBalance <= 0) {
            return 'You do not have enough balance';
        }

        // ✅ Detect if the message is Unicode (Bangla or other non-ASCII)
        $isUnicode = preg_match('/[^\x00-\x7F]/', $description);

        // ✅ Count characters accurately using multibyte support
        $charCount = mb_strlen($description, 'UTF-8');

        // ✅ Set character limit per message
        $limitPerMessage = $isUnicode ? 70 : 160;

        // ✅ Calculate how many SMS this message will consume
        $smsCount = (int) ceil($charCount / $limitPerMessage);

        // ✅ Check if balance is sufficient
        if ($unionInfo->smsBalance < $smsCount) {
            return "Insufficient SMS balance. You need {$smsCount} SMS credits.";
        }

        // Prepare API credentials and message details
        $smsnocApiKey = env('SMSNOC_API_KEY');
        $smsnocSenderId = env('SMSNOC_SENDER_ID');

        $payload = [
            'recipient' => '88' . $applicantMobile,
            'sender_id' => $smsnocSenderId,
            'type' => $isUnicode ? 'unicode' : 'plain',
            'message' => $description,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $smsnocApiKey,
            ])->post('https://app.smsnoc.com/api/v3/sms/send', $payload);

            if ($response->successful()) {
                // Decrement the actual used balance
                $unionInfo->decrement('smsBalance', $smsCount);

                Log::info("SMS sent to {$applicantMobile} for union {$union}. Char: {$charCount}, SMS used: {$smsCount}");

                return "SMS sent successfully using {$smsCount} credit(s).";
            } else {
                Log::error('SMS sending failed. Response: ' . $response->body());
                return 'SMS sending failed. Please try again later.';
            }
        } catch (Exception $e) {
            Log::error('Error sending SMS: ' . $e->getMessage());
            return 'An error occurred while sending SMS.';
        }
    }

}
