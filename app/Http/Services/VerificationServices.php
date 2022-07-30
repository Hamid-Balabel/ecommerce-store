<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\User_verification;
use Illuminate\Support\Facades\Auth;
use Request;
class VerificationServices
{
   public function setVerificationCode($data){
        $code=mt_rand(100000,999999);
        $data['code']=$code;
        User_verification::whereNotNull('user_id')->where(['user_id'=>$data['user_id']])->delete();

        return User_verification::create($data);
   }

    public function getSMSVerifyMessageByAppName( $code)
    {
        $message = " is your verification code for your account";
        return $code.$message;
    }

    public function checkOTPCode ($code){

        if (Auth::guard('web')->check()) {
            $verificationData= User_verification::where('user_id',Auth::id())->first();

            if( $verificationData->code == $code){
                User::whereId(Auth::id()) -> update(['email_verified_at' => now()]);
                return true;
            }else{
                return false;
            }
        }
        return false ;
    }


    public function removeOTPCode($code)
    {
        User_verification::where('code',$code) -> delete();
    }
}
