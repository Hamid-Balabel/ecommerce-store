<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationRequest;
use App\Http\Services\VerificationServices;

class VerificationCodeController extends Controller
{

    public $verificationServices;

    public function __construct(VerificationServices $verificationServices){
        $this->verificationServices= $verificationServices;
    }

    public function verify(VerificationRequest $request)
    {

        $check= $this-> verificationServices-> checkOTPCode($request->code);
        if(!$check){
            //return 'you enter a wrong code';
            return redirect()->route('get.verification.form')->withErrors(['code'=>'this code is not correct ']);
        }else{
            $this->verificationServices->removeOTPCode($request->code);
            return redirect()->route('home');
        }
    }

    public function getVerifyPage(){
        return view('auth.verification');
    }
}
