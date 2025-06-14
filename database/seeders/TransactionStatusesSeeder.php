<?php

namespace Database\Seeders;

use App\Models\Transactions\TransactionStatuses;
use App\Services\AutoTranslationService;
use Illuminate\Database\Seeder;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TransactionStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $autoTranslator = resolve(AutoTranslationService::class);
        $statuses = [
            'complete' => "Tamamlandı",
            'pending' => "Gözləmədə",
            'failed' => "Uğursuz",
            'insufficient_balance' => "Qeyri-kafi balans",
            'insufficient_funds' => "Hesabda kifayət qədər məbləğ yoxdur",
            'self_transfer' => "Öz hesabları arasında mübadilə",
            'external_transfer' => "Başqa hesaba mübadilə",
            'banknote_combination_error' => "Məbləğ mövcud əskinazlarla ödənilə bilmir",
            'amount_too_low' => "Məbləğ 0-dan böyük olmalıdır",
            'daily_limit_exceeded' => "Gündəlik nağdlaşdırma limitini aşırsınız",
        ];

        foreach ($statuses as $key => $stat) {
            $sourceLocale = config('app.locale', 'az');

            $name = [
                $sourceLocale => $stat,
            ];

            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $targetLocale) {
                if ($targetLocale === $sourceLocale) {
                    continue;
                }

                $translatedText = $autoTranslator->translate($name[$sourceLocale], $targetLocale, $sourceLocale);

                if ($translatedText) {
                    $name[$targetLocale] = $translatedText;
                }
            }
            TransactionStatuses::firstOrCreate(['code' => $key], ['name' => $name]);
        }
    }
}
