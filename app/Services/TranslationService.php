<?php

namespace App\Services;

use App\Models\BaseSettings\Translations;

class TranslationService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function get(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "translation.{$key}.{$locale}";

        return $this->cacheService->getOrSet($cacheKey, function () use ($key, $locale) {
            $translation = Translations::where('key', $key)->select("value")->first();

            return $translation->value[$locale] ?? $key;
        });
    }
}
