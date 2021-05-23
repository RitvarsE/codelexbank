<?php

namespace App\Http\Controllers;

use App\Mail\RecoveryCodeSent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecoveryCodeController extends Controller
{
    public function recovery(Request $request)
    {
        $user = User::where('id', $request->session()->get('login.id'))->first();

        Mail::to($user->email)->send(new RecoveryCodeSent());

        return View('auth.two-factor-recovery-code');

    }
}
