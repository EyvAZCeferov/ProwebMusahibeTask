<?php

namespace Database\Seeders;

use App\Models\AtmBanknote;
use App\Models\CurrencyExchanges\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('atm_banknotes')->truncate();

        $banknotesByCurrency = [
            'AZN' => [
                ['name' => 200, 'quantity' => 50],
                ['name' => 100, 'quantity' => 100],
                ['name' => 50,  'quantity' => 150],
                ['name' => 20,  'quantity' => 200],
                ['name' => 10,  'quantity' => 250],
                ['name' => 5,   'quantity' => 300],
                ['name' => 1,   'quantity' => 500],
            ],
            'USD' => [
                ['name' => 100, 'quantity' => 100],
                ['name' => 50,  'quantity' => 200],
                ['name' => 20,  'quantity' => 300],
                ['name' => 10,  'quantity' => 400],
                ['name' => 5,   'quantity' => 500],
                ['name' => 1,   'quantity' => 1000],
            ],
        ];

        $currencies = Currency::whereIn('code', array_keys($banknotesByCurrency))->get()->keyBy('code');

        foreach ($banknotesByCurrency as $currencyCode => $banknotes) {
            if (!isset($currencies[$currencyCode])) {
                continue;
            }

            $currency = $currencies[$currencyCode];

            foreach ($banknotes as $banknoteData) {
                AtmBanknote::create([
                    'currency_id' => $currency->id,
                    'name' => $banknoteData['name'],
                    'quantity' => $banknoteData['quantity'],
                ]);
            }
        }
    }
}
