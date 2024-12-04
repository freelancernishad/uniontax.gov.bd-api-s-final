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

        // Check if union info exists
        if (!$unionInfo) {
            return 'Union not found';
        }

        // Check if union has enough SMS balance
        if ($unionInfo->smsBalance <= 0) {
            return 'You do not have enough balance';
        }

        // Prepare API credentials and message details
        $smsnocApiKey = env('SMSNOC_API_KEY');  // Store in .env file
        $smsnocSenderId = env('SMSNOC_SENDER_ID');  // Store in .env file

        // Prepare the SMS payload
        $payload = [
            'recipient' => '88' . $applicantMobile,
            'sender_id' => $smsnocSenderId,
            'type' => 'plain',
            'message' => $description,
        ];

        try {
            // Make the API request using Laravel HTTP client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $smsnocApiKey,
            ])
            ->post('https://app.smsnoc.com/api/v3/sms/send', $payload);

            // Check if the response is successful
            if ($response->successful()) {
                // Decrement the SMS balance
                $unionInfo->decrement('smsBalance', 1);

                // Log the successful SMS
                Log::info("SMS sent successfully to {$applicantMobile} for union {$union}");

                return 'SMS sent successfully';
            } else {
                // Log the error response
                Log::error('SMS sending failed. Response: ' . $response->body());

                return 'SMS sending failed. Please try again later.';
            }

        } catch (Exception $e) {
            // Log the exception error
            Log::error('Error sending SMS: ' . $e->getMessage());

            return 'An error occurred while sending SMS.';
        }
    }
}
