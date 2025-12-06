<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Models\User;
use App\Traits\SmsSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OtpController extends Controller
{
    use SmsSender;

    public function store(SendOtpRequest $request)
    {
        if (Redis::get('otp.' . $request->phone)) {
            return $this->error(Status::OPERATION_ERROR, __('messages.otp_already_sent'));
        }
        $otp = rand(1000, 9999);

        Redis::set('otp.' . $request->phone, json_encode(['code' => $otp, 'used' => false]), 'EX', 60 * 2); // 2 min

        if (env('SEND_SMS') == 1) {
            $this->SMS($this->sendOtpTemp($otp), $request->phone);
        }

        return $this->success(__('messages.otp_sent'));
    }
}
