<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;

trait PhoneValidator
{
    public function validate($phone, $otp)
    {
        $cachedOtp = json_decode(Redis::get('otp.' . $phone), true);

        if (!$cachedOtp) {
            return false;
        }
        if ($cachedOtp['used']) {
            return false;
        }
        if ($cachedOtp['code'] != $otp) {
            return false;
        }
        // Mark OTP as used
        $cachedOtp['used'] = true;
        Redis::set('otp.' . $phone, json_encode($cachedOtp), 'EX', 60 * 1); // 1 min to send new OTP
        return true;
    }
}
