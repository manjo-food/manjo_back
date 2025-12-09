<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\OtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('send-otp', [OtpController::class,'store']);

Route::post('login',[AuthenticationController::class,'login']);
