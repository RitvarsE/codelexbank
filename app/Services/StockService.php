<?php


namespace App\Services;


use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Collection;

class StockService
{
    private ConverterService $converterService;
    private GetAccountService $accountService;

    public function __construct(ConverterService $converterService, GetAccountService $accountService)
    {
        $this->converterService = $converterService;
        $this->accountService = $accountService;
    }

    public function getQuote(Request $request): int
    {
        $request->validate([
            'stock' => 'required|alpha_num|',
            'currency' => 'required|alpha|exists:App\Models\Currency,name'
        ]);
        $stockName = $request->get('stock');
        $stock = 'stock_' . $stockName . '_currency_' . $request->get('currency');
        if (Cache::has($stock)) {
            return Cache::get($stock);
        }
        Cache::put($stock, $this->converterService->convert('USD', $request->get('currency'), json_decode(
            file_get_contents(
                'https://finnhub.io/api/v1/quote?symbol=' . $stockName . '&token=c1pj3v2ad3id1hoq2ap0')
            , true)['c']), 1);

        return $this->converterService->convert('USD', $request->get('currency'), json_decode(
            file_get_contents(
                'https://finnhub.io/api/v1/quote?symbol=' . $stockName . '&token=c1pj3v2ad3id1hoq2ap0')
            , true)['c']);
    }

    public function buyStock(Request $request)
    {
        $accountAmount = $request->get('bankAccount')['amount'];
        $stockPrice = $request->get('stockPrice');

        $request->validate([
            'bankAccount.number' => 'required',
            'bankAccount.amount' => 'required',
            'bankAccount.currency' => 'required',
            'stockName' => 'required',
            'stockAmount' => ['required', 'numeric', 'lte:' . $accountAmount / $stockPrice],
            'stockPrice' => 'required|numeric|'
        ]);
        return $this->transaction($request);
    }

    public function transaction(Request $request): RedirectResponse
    {
        $stockName = $request->get('stockName');
        $stockAmount = $request->get('stockAmount');
        $stockPrice = $request->get('stockPrice');
        $currency = $request->get('bankAccount')['currency'];

        DB::beginTransaction();
        try {
            $account = $this->accountService->getAccountByNumber($request->get('bankAccount')['number']);
            $account->update([
                'amount' => $account->amount - $stockAmount * $stockPrice
            ]);
            Stock::create([
                'name' => $stockName,
                'price_bought' => $stockPrice,
                'quantity' => $stockAmount,
                'user_id' => $request->user()->id,
                'status' => 1,
                'currency' => $currency
            ]);
            DB::commit();
            return Redirect::route('stocks.yourstocks');
        } catch (\Exception $exception) {
            DB::rollBack();
            return Redirect::route('error')->with(['code' => $exception->getMessage()]);
        }
    }

    public function getAll(Request $request): Collection
    {
        return Stock::where('user_id', $request->user()->id)->get();
    }

    public function sellStock(Stock $stock): ?bool
    {
        return $stock->delete();
    }
}
