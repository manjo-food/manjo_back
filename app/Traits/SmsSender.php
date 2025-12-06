<?php

namespace App\Traits;

use Ipe\Sdk\Facades\SmsIr;

trait SmsSender
{
    public function SMS($messageText, $mobile)
    {
        $lineNumber = "90003591";
        $sendDateTime = null;
        $mobiles[0] = $mobile;

        SmsIr::bulkSend($lineNumber, $messageText, $mobiles, $sendDateTime);

    }

    public function sendOtpTemp($otp)
    {
        $text = " کد تایید شما: $otp \n" .
            "مانجو سوخاری\n";

        return $text;
    }
}
