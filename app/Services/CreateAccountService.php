<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CreateAccountService
{
    public function bankAccount(): string
    {
        do {
            $newBankAccount = mt_rand(100000000, 999999999);
        } while (BankAccount::where('number', $newBankAccount)->exists());
        return 'RIT69AG0123' . $newBankAccount;
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            BankAccount::create([
                'user_id' => $request->user()->id,
                'number' => $this->bankAccount(),
                'type' => (int)$request->get('type'),
                'currency' => $request->get('currency')
            ]);
            DB::commit();
            return Redirect::route('dashboard');
        } catch (\Exception $error) {
            DB::rollback();
            return Redirect::route('error');
        }

    }
}
