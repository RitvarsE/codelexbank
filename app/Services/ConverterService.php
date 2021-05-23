<?php


namespace App\Services;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ConverterService
{
    public function upload(): void
    {
        $xmlString = file_get_contents('https://www.bank.lv/vk/ecb.xml');
        $xmlObject = simplexml_load_string($xmlString);
        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true);

        $currencies = $phpArray['Currencies']['Currency'];

        foreach ($currencies as $currency) {

            Currency::updateOrCreate(
                ['name' => $currency['ID']],
                ['rate' => $currency['Rate'] * 10000]);
        }
    }

    public function getAllCurrencies(): Collection
    {
        return Currency::all();
    }

    public function getCurrency(Request $request): Currency
    {
        return Currency::where('name', $request->get('currency'))->first();
    }
}
