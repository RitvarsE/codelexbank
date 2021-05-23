<?php


namespace App\Services;


use App\Models\BankAccount;

class GetAccountService
{
    public function getAccountByNumber(string $account)
    {
        return BankAccount::where('number', $account)->first();
    }
    public function getUserAccounts($id){
        return BankAccount::where('user_id', $id)->get();
    }
}
