<?php

namespace App\Services;

use App\Models\CurrencyExchanges\Exchanges;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getRate(int $fromCurrencyId, int $toCurrencyId): float
    {
        if ($fromCurrencyId === $toCurrencyId) {
            return 1.0;
        }

        $cacheKey = "rate_{$fromCurrencyId}_to_{$toCurrencyId}";

        $rate = $this->cacheService->getOrSet($cacheKey, function () use ($fromCurrencyId, $toCurrencyId) {
            return Exchanges::where('from_currency_id', $fromCurrencyId)
                ->where('to_currency_id', $toCurrencyId)
                ->value('rate');
        });

        if (is_null($rate)) {
            $responseText = "Valyuta məzənnəsi tapılmadı.";
            throw new \Exception($responseText);
        }

        return (float) $rate;
    }
}
