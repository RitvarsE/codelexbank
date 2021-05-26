<?php


namespace App\Services;

use App\Jobs\SendEmail;
use App\Models\Transaction;
use App\Models\VerificationCode;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TransactionService
{
    private GetAccountService $accountService;
    private ConverterService $converterService;
    private VerificationCodeService $codeService;

    public function __construct(
        GetAccountService $accountService,
        ConverterService $converterService,
        VerificationCodeService $codeService)
    {
        $this->accountService = $accountService;
        $this->converterService = $converterService;
        $this->codeService = $codeService;
    }

    public function validation(Request $request): RedirectResponse
    {
        $senderAccount = $this->accountService
            ->getAccountByNumber($request->get('senderAccount')['number']);
        $receiverName = $this->accountService
            ->getAccountByNumber($request->get('receiverAccount'))->user->name;

        $request->validate([
            'senderAccount.number' => 'required|size:20|alpha_num',
            'senderAccount.amount' => 'required|alpha_num|',
            'receiver' => ['required', 'exists:App\Models\User,name', Rule::in([$receiverName])],
            'sendingAmount' => 'required|lte:' . $senderAccount->amount / 100,
            'purpose' => 'required|min:5',
            'receiverAccount' => ['required', 'size:20', 'Exists:App\Models\BankAccount,number', 'different:senderAccount.number',
                Rule::notIn([$senderAccount->number])]
        ]);

        $verificationCode = VerificationCode::UpdateOrCreate(
            ['user_id' => $request->user()->id],
            ['code' => bin2hex(openssl_random_pseudo_bytes(10))]);

        $details = ['verificationCode' => $verificationCode, 'user' => $request->user()];
        SendEmail::dispatch($details);

        return back()->with(['code' => 'success']);
    }

    public function validateVerification(Request $request): RedirectResponse
    {
        $codeForValidation = $this->codeService->findCode($request);

        $request->validate([
            'code' => ['exists:App\Models\VerificationCode,code', 'required', Rule::in([$codeForValidation])]
        ]);
        $this->codeService->deleteCode($request);

        return back()->with(['code' => 'redirecting']);
    }

    public function sendMoney(Request $request): RedirectResponse
    {
        $senderAccount = $this->accountService->
        getAccountByNumber($request->get('senderAccount')['number']);

        $receiverAccount = $this->accountService
            ->getAccountByNumber($request->get('receiverAccount'));

        $amount = $request->get('sendingAmount') * 100;

        $convertedAmount = $this->converterService->convert(
            $senderAccount->currency,
            $receiverAccount->currency,
            $amount) / 100;

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

            Transaction::create([
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
            return Redirect::route('receipt')->with(['code' => 'receipt']);

        } catch (Exception $error) {
            DB::rollback();
            return Redirect::route('error');
        }
    }

    public function receipt(Request $request): Model
    {
        return Transaction::with(['sender', 'receiver'])
            ->where('sender_id', $request->user()->id)
            ->latest()
            ->first();
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
