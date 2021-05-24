<?php


namespace App\Services;


use Illuminate\Support\Facades\Cache;

class StockService
{
    public function getQuote(string $stock): string
    {
        $stockPrice = 'stock_' . $stock;
        if (Cache::has($stockPrice)) {
            return Cache::get($stockPrice);
        }
        Cache::put($stockPrice, json_decode(
            file_get_contents(
                'https://finnhub.io/api/v1/quote?symbol=' . $stock . '&token=c1pj3v2ad3id1hoq2ap0')
            , true)['c'], 300);

        return json_decode(
            file_get_contents(
                'https://finnhub.io/api/v1/quote?symbol=' . $stock . '&token=c1pj3v2ad3id1hoq2ap0')
            , true)['c'];
    }
}
