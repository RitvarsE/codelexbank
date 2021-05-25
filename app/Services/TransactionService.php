<?php


namespace App\Services;

use App\Mail\SendVerificationCode;
use App\Models\Transaction;
use App\Models\VerificationCode;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

    public function validation(Request $request): RedirectResponse
    {
        $senderAccount = $this->accountService->getAccountByNumber($request->get('senderAccount')['number']);
        $receiverAccount = $this->accountService->getAccountByNumber($request->get('receiverAccount'));


        $request->request->add(['receiver_name_validation' => $receiverAccount->user->name]);
        $request->validate([
            'senderAccount.number' => 'required|size:20|alpha_num',
            'senderAccount.amount' => 'required|alpha_num|',
            'receiver' => 'required|exists:App\Models\User,name|same:receiver_name_validation',
            'sendingAmount' => 'required|lte:' . $senderAccount->amount / 100,
            'purpose' => 'required|min:5',
            'receiverAccount' => 'required|size:20|Exists:App\Models\BankAccount,number|different:senderAccount.number'
        ]);

        $verificationCode = VerificationCode::create([
            'code' => bin2hex(openssl_random_pseudo_bytes(10))]);

        Mail::to($request->user())->send(new SendVerificationCode($verificationCode));

        return back()->with(['code' => $verificationCode]);
    }

    public function sendMoney(Request $request)
    {
        $senderAccount = $this->accountService->getAccountByNumber($request->get('senderAccount')['number']);
        $receiverAccount = $this->accountService->getAccountByNumber($request->get('receiverAccount'));

        $amount = $request->get('sendingAmount') * 100;

        $convertedAmount = $this->converterService->convert(
            $senderAccount->currency,
            $receiverAccount->currency,
            $amount);

        $amountForTaxes = $convertedAmount;

        DB::beginTransaction();

        try {
            if ($senderAccount->type === 1 && $convertedAmount + $senderAccount->withdraw > $senderAccount->deposit) {
                if ($senderAccount->deposit - $senderAccount->withdraw <= 0) {
                    $convertedAmount = $convertedAmount * 0.8;
                    $amountForTaxes -= $convertedAmount;

                } else {
                    $convertedAmount = ($convertedAmount + $senderAccount->withdraw - $senderAccount->deposit)
                        * 0.8 + $senderAccount->deposit - $senderAccount->withdraw;
                    $amountForTaxes -= $convertedAmount;
                }
            }
            $receiverAccount->update(
                ['amount' => $receiverAccount->amount + $convertedAmount,
                    'deposit' => $receiverAccount->deposit + $convertedAmount]);

            $senderAccount->update(
                ['amount' => $senderAccount->amount - $amount,
                    'withdraw' => $senderAccount->withdraw + $amount,
                    'tax' => $senderAccount->tax + ($amountForTaxes === $convertedAmount ? 0 : $amountForTaxes)]
            );
            $transaction = Transaction::create([
                'sender_id' => $senderAccount->user_id,
                'receiver_id' => $receiverAccount->user_id,
                'amount' => $convertedAmount,
                'currency' => $senderAccount->currency,
                'sender_account_number' => $senderAccount->number,
                'receiver_account_number' => $receiverAccount->number,
                'purpose' => $request->get('purpose'),
                'tax' => $amountForTaxes === $convertedAmount ? null : $amountForTaxes
            ]);

            DB::commit();
            return Redirect::route('receipt', $transaction->id);

        } catch (Exception $error) {
            DB::rollback();
            return Redirect::route('error');
        }
    }

    public function transaction(Request $request)
    {
        $request->validate([
            'code' => 'exists:App\Models\VerificationCode,code|required'
        ]);
        return back()->with(['code' => 'redirecting']);
    }

    public function receipt(Transaction $transaction): Model
    {
        return Transaction::with(['sender', 'receiver'])->find($transaction->id);
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
