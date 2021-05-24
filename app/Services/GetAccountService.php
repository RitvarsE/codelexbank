<?php


namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetAccountService
{
    public function getAccountByNumber(string $account): BankAccount
    {
        Validator::make(['account' => $account], [
            'account' => 'exists:App\Models\BankAccount,number'
        ])->validate();
        return BankAccount::where('number', $account)->first();
    }

    public function getUserAccounts(Request $request): Collection
    {
        return BankAccount::where('user_id', $request->user()->id)->get();
    }
}
