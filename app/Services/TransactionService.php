<?php


namespace App\Services;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TransactionService
{
    private GetAccountService $accountService;

    public function __construct(GetAccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function transfer(Request $request): RedirectResponse
    {

        $senderAccount = $this->accountService->getAccountByNumber($request->get('senderAccount')['number']);
        $receiverAccount = $this->accountService->getAccountByNumber($request->get('receiverAccount'));
        $request->request->add(['receiver_name_validation' => $receiverAccount->user->name]);
        var_dump($request->all());
        $request->validate([
            'senderAccount.number' => 'required|size:20|alpha_num',
            'senderAccount.amount' => 'required|alpha_num|',
            'receiver' => 'required|exists:App\Models\User,name|same:receiver_name_validation',
            'sendingAmount' => 'required|lte:'.$senderAccount->amount
        ]);

        DB::beginTransaction();

        try {
            $receiverAccount->update(['amount' => $receiverAccount->amount + $request->get('sendingAmount')]);
            $senderAccount->update(['amount' => $senderAccount->amount - $request->get('sendingAmount')]);
            $transaction = Transaction::create([
                'sender_id' => $senderAccount->user_id,
                'receiver_id' => $receiverAccount->user_id,
                'amount' => $request->get('sendingAmount'),
                'currency' => $senderAccount->currency,
                'sender_account_number' => $senderAccount->number,
                'receiver_account_number' => $receiverAccount->number,
                'purpose' => $request->get('purpose')
            ]);

            DB::commit();
            return Redirect::route('receipt', $transaction->id);

        } catch (\Exception $error) {
            DB::rollback();
            return Redirect::route('error');
        }
    }

    public function receipt(int $id)
    {
        return Transaction::with(['sender', 'receiver'])->find($id);
    }
}
