<?php


namespace App\Services;


use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Collection;

class GetAccountService
{
    public function getAccountByNumber(string $account): BankAccount
    {
        return BankAccount::where('number', $account)->first();
    }

    public function getUserAccounts($id): Collection
    {
        return BankAccount::where('user_id', $id)->get();
    }
}
