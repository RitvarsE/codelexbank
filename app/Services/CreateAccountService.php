<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class CreateAccountService
{
    public function bankAccount(): string
    {
        do {
            $newBankAccount = mt_rand(100000000, 999999999);
        } while (BankAccount::where('number', $newBankAccount)->exists());
        return 'RIT69AG0123' . $newBankAccount;
    }

    public function create($user = null, Request $request = null)
    {
        BankAccount::create([
            'user_id' => isset($user) ? $user->id : auth()->id(),
            'number' => $this->bankAccount(),
            'type' => isset($request) ? (int) $request->get('accountType') : 0,
            'currency' => isset($request) ? $request->get('currency') : 'EUR'
        ]);
    }
}
