<?php

namespace App\Traits;

use Ipe\Sdk\Facades\SmsIr;

trait SmsSender
{
    public function sendTextSms($messageText, $mobile, $sendDateTime = null)
    {
        $lineNumber = "30007732010229";
        $mobiles[0] = $mobile;

        SmsIr::bulkSend($lineNumber, $messageText, $mobiles, $sendDateTime);

    }

    public function sendOtp($otp, $mobile)
    {
        $templateId = 647871;
        $parameters = [
            [
                "name" => "Code",
                "value" => $otp
            ]
        ];

        SmsIr::verifySend($mobile, $templateId, $parameters);
    }
}
