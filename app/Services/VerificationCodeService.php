<?php


namespace App\Services;


use App\Models\VerificationCode;
use Illuminate\Http\Request;

class VerificationCodeService
{
    public function deleteCode(Request $request): int
    {
        return VerificationCode::where(['code' => $request->get('code')])->delete();
    }

    public function findCode(Request $request)
    {
        try{
            return VerificationCode::where(['user_id' => $request->user()->id])->first()->code;
        }catch( \Throwable $e){
            report($e);
            return false;
        }

    }
}
