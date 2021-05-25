<?php


namespace App\Services;


use App\Models\VerificationCode;
use Illuminate\Http\Request;

class VerificationCodeService
{
    public function deleteCode(Request $request)
    {
        return VerificationCode::where(['code' => $request->get('code')])->delete();
    }
}
