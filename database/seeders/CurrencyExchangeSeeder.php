<?php

namespace Database\Seeders;

use App\Models\CurrencyExchanges\Currency;
use App\Models\CurrencyExchanges\Exchanges;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencyExchangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => "Azərbaycan manatı",
                'symbol' => "₼",
                'code' => "AZN",
            ],
            [
                'name' => "Amerikan dolları",
                'symbol' => "$",
                'code' => "USD",
            ],
        ];
        $createdCurrencies = [];
        foreach ($currencies as $currency) {
            $currency_db = Currency::firstOrCreate([
                'code' => $currency['code'],
            ], [
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
            ]);
            $createdCurrencies[$currency_db->code] = $currency_db;
        }

        $azn = $createdCurrencies['AZN'];
        $usd = $createdCurrencies['USD'];

        $usdToAznRate = 1.70;

        if ($azn && $usd) {
            Exchanges::firstOrCreate(
                [
                    'from_currency_id' => $usd->id,
                    'to_currency_id' => $azn->id
                ],
                [
                    'rate' => $usdToAznRate,
                ]
            );

            Exchanges::firstOrCreate(
                [
                    'from_currency_id' => $azn->id,
                    'to_currency_id' => $usd->id
                ],
                [
                    'rate' => 1 / $usdToAznRate, // 1 / 170= azn 
                ]
            );
        }
    }
}
