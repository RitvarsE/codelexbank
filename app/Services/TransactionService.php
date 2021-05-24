<?php


namespace App\Services;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TransactionService
{
    private GetAccountService $accountService;
    private ConverterService $converterService;

    public function __construct(GetAccountService $accountService, ConverterService $converterService)
    {
        $this->accountService = $accountService;
        $this->converterService = $converterService;
    }

    public function transfer(Request $request): RedirectResponse
    {

        $senderAccount = $this->accountService->getAccountByNumber($request->get('senderAccount')['number']);
        $receiverAccount = $this->accountService->getAccountByNumber($request->get('receiverAccount'));

        $request->request->add(['receiver_name_validation' => $receiverAccount->user->name]);

        $amount = $request->get('senderAccount')['amount'];
        $currencyFrom = $senderAccount->currency;
        $currencyTo = $request->get('currency');

        $convertedAmount = $this->converterService->convert($currencyFrom, $currencyTo, $amount);

        $request->validate([
            'senderAccount.number' => 'required|size:20|alpha_num',
            'senderAccount.amount' => 'required|alpha_num|',
            'receiver' => 'required|exists:App\Models\User,name|same:receiver_name_validation',
            'sendingAmount' => 'required|lte:' . $amount
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

    public function receipt(int $id): Model
    {
        return Transaction::with(['sender', 'receiver'])->find($id);
    }

    public function transactionHistory(Request $request): Collection
    {
        $id = $request->user()->id;
        return Transaction::with(['sender', 'receiver'])
            ->where('sender_id', $id)
            ->orWhere('receiver_id', $id)
            ->get();
    }
}
